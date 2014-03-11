API
===

REST API that helps you building ELO based rating systems.

Login
To use the API, firstly you need to have a session so you can include it in every request made. If you use the PHP RatingClient, it retrieves the session automatically and stores it in memcached. Every time RatingClient instantiated, it checks if the cached session is not outdated by performing a test request to `/api/checkSession` and if the response code is 401, the class retrieves a new session and rewrites the one in memcached. Otherwise, if you are making the requests manually, to get a session you need to make a request to `/api/login` with headers: `x-consumer-id and x-consumer-secret`.
Example: 
`GET /api/login/ HTTP/1.1`
`x-consumer-id: 296717969`
`x-consumer-secret: syAVVaCAHcwXNYWuuOmDORbDp403hORKnsGjCiTjuqhioeNT29xR1IvqNvt5kkPI`
`Cache-Control: no-cache`

`Response:`
`{`
`session: "jhji1gerqig0btl208uuap5a82"`
`}`
