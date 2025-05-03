````md
1. Place the `track_list.xlsx` file in the current directory.  
2. Replace the placeholder `<YOUR_REFRESH_TOKEN>` in the script with the valid refresh token you received from Chartmetric.  
3. In the terminal, navigate to the current directory and run:  
   ```bash
   chmod +x run_script.sh
````

4. Run:

   ```bash
   ./run_script.sh
   ```

---

### Task

List of 990 tracks provided (ISRC, Track Name, Artist, Release Date) and need to pull complete monthly Spotify streaming history from Chartmetric’s API, as far back as data is available. The final deliverable is a single CSV file with one row per track-month.

**1. Inputs**
• You’ll receive a track list with columns: ISRC | Track Name | Artist | Release Date.

**2. Authentication**
• We’ll provide a Chartmetric refresh token.
• Exchange it for an access token via the OAuth endpoint.
• Use the bearer token in all subsequent API requests.

**3. Track ID Resolution**
• For each ISRC, hit the Chartmetric search endpoint (type=track).
• Confirm the match by comparing track name/artist, then note its track ID.

**4. Data Retrieval (Monthly Only)**
• Call the “streams” endpoint for each track ID with metric=spotify and interval=month.
• Page through results until you’ve captured every month (up to \~60 points per track).

**5. Data Coverage & Granularity**
• Chartmetric provides \~5 years of history (back to August 2016; artist-level from November 25, 2016).
• Monthly granularity yields \~60 data points per track vs. \~1,800 if daily.

**6. Rate-Limit & Error Handling**
• We are on a trial plan which limits us to 1,000 requests per 24 hours.
• Implement polite rate-limiting (e.g. 1 req/sec) and retry on HTTP 429.

**7. Final CSV**
• Name: `monthly_streams.csv`
• Columns: ISRC | Track Name | Artist | Release Date | Month (YYYY-MM) | Streams
• One row per track-month (≈59,400 rows total).

```
```
