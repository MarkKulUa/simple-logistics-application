const axios = require('axios');
const fs = require('fs');
const XLSX = require('xlsx');
const { stringify } = require('csv-stringify');
const Bottleneck = require('bottleneck');
const cliProgress = require('cli-progress');

const CHARTMETRIC_API = 'https://api.chartmetric.com/api';
const REFRESH_TOKEN = '<YOUR_REFRESH_TOKEN>';
const TRACKS_FILE = 'track_list.xlsx';
const OUTPUT_CSV = 'monthly_streams.csv';

const limiter = new Bottleneck({ minTime: 1100 });
let successCount = 0;
let failureCount = 0;

async function authenticate() {
    const res = await axios.post(`${CHARTMETRIC_API}/token`, { refreshtoken: REFRESH_TOKEN });
    return res.data.token;
}

async function makeRequestWithRetry(url, token, params = {}) {
    let retries = 3;
    while (retries > 0) {
        try {
            const res = await limiter.schedule(() =>
                axios.get(url, { headers: { Authorization: `Bearer ${token}` }, params })
            );
            successCount++;
            return res;
        } catch (error) {
            if (error.response && error.response.status === 429) {
                const retryAfter = error.response.headers['retry-after'] || 60;
                console.warn(`Rate limited. Retrying after ${retryAfter}s...`);
                await new Promise(res => setTimeout(res, retryAfter * 1000));
                retries--;
            } else {
                console.error(`Request failed: ${error.message}`);
                failureCount++;
                return null;
            }
        }
    }
    console.error('Failed after 3 retries.');
    failureCount++;
    return null;
}

async function searchTrackId(token, isrc, trackName, artist) {
    const res = await makeRequestWithRetry(`${CHARTMETRIC_API}/search`, token, { q: isrc, type: 'tracks' });
    if (!res) return null;

    const match = res.data.obj.tracks.find(
        (t) =>
            t.isrc?.toLowerCase() === isrc.toLowerCase() &&
            t.name?.toLowerCase() === trackName.toLowerCase() &&
            t.artist_names?.join(', ').toLowerCase().includes(artist.toLowerCase())
    );

    return match ? match.id : null;
}

async function getMonthlyStreams(token, trackId) {
    const streams = [];
    let nextPage = `${CHARTMETRIC_API}/track/${trackId}/streams?metric=spotify&interval=month`;

    while (nextPage) {
        const res = await makeRequestWithRetry(nextPage, token);
        if (!res) break;
        streams.push(...res.data.obj);
        nextPage = res.data.next;
    }

    return streams;
}

async function processTracks() {
    const token = await authenticate();
    const workbook = XLSX.readFile(TRACKS_FILE);
    const sheet = workbook.Sheets[workbook.SheetNames[0]];
    const tracks = XLSX.utils.sheet_to_json(sheet);

    const output = fs.createWriteStream(OUTPUT_CSV);
    const writer = stringify({ header: true, columns: ['ISRC', 'Track Name', 'Artist', 'Release Date', 'Month', 'Streams'] });
    writer.pipe(output);

    const bar = new cliProgress.SingleBar({}, cliProgress.Presets.shades_classic);
    bar.start(tracks.length, 0);

    for (const track of tracks) {
        const { ISRC, Track, Artists, 'Release Date': releaseDate } = track;

        try {
            const trackId = await searchTrackId(token, ISRC, Track, Artists);
            if (!trackId) {
                console.warn(`Track not found: ${ISRC} - ${Track} - ${Artists}`);
                bar.increment();
                continue;
            }

            const streams = await getMonthlyStreams(token, trackId);
            for (const { timestp, value } of streams) {
                writer.write([ISRC, Track, Artists, releaseDate, timestp.substring(0, 7), value]);
            }
        } catch (err) {
            console.error(`Error processing track ${ISRC}: ${err.message}`);
            failureCount++;
        }

        bar.increment();
    }

    bar.stop();
    writer.end();

    console.log(`\n✅ Successful requests: ${successCount}`);
    console.log(`❌ Failed requests: ${failureCount}`);
    console.log(`📄 Finished writing to ${OUTPUT_CSV}`);
}

processTracks().catch(console.error);
