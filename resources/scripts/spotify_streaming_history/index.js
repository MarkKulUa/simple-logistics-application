const axios = require('axios');
const fs = require('fs');
const XLSX = require('xlsx');
const { stringify } = require('csv-stringify');
const Bottleneck = require('bottleneck');

const CHARTMETRIC_API = 'https://api.chartmetric.com/api';
const REFRESH_TOKEN = '<YOUR_REFRESH_TOKEN>';
const TRACKS_FILE = 'track_list.xlsx';
const OUTPUT_CSV = 'monthly_streams.csv';
const limiter = new Bottleneck({ minTime: 1100 });

async function authenticate() {
    const res = await axios.post(`${CHARTMETRIC_API}/token`, { refreshtoken: REFRESH_TOKEN });
    return res.data.token;
}

async function searchTrackId(token, isrc, trackName, artist) {
    const res = await limiter.schedule(() =>
        axios.get(`${CHARTMETRIC_API}/search`, {
            headers: { Authorization: `Bearer ${token}` },
            params: { q: isrc, type: 'tracks' },
        })
    );

    const match = res.data.obj.tracks.find(
        (t) =>
            t.isrc.toLowerCase() === isrc.toLowerCase() &&
            t.name.toLowerCase() === trackName.toLowerCase() &&
            t.artist_names.join(', ').toLowerCase().includes(artist.toLowerCase())
    );

    return match ? match.id : null;
}

async function getMonthlyStreams(token, trackId) {
    const streams = [];
    let nextPage = `${CHARTMETRIC_API}/track/${trackId}/streams?metric=spotify&interval=month`;

    while (nextPage) {
        const res = await limiter.schedule(() =>
            axios.get(nextPage, { headers: { Authorization: `Bearer ${token}` } })
        );
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

    for (const track of tracks) {
        const { ISRC, Track, Artists, 'Release Date': releaseDate } = track;
        const trackId = await searchTrackId(token, ISRC, Track, Artists);
        if (!trackId) continue;

        const streams = await getMonthlyStreams(token, trackId);
        for (const { timestp, value } of streams) {
            writer.write([ISRC, Track, Artists, releaseDate, timestp.substring(0, 7), value]);
        }
    }

    writer.end();
    console.log(`Finished writing to ${OUTPUT_CSV}`);
}

processTracks().catch(console.error);
