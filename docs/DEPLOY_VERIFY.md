# Deploy Verification Checklist

After deploying backend (Railway) and frontend (Netlify), verify with these steps.

## Backend (Railway)

### 1. Root / health
```bash
curl -s https://YOUR-RAILWAY-URL.up.railway.app/
```
**Expected:** `{"status":"ok","endpoints":["/health","/graphql"]}`

### 2. Health endpoint
```bash
curl -s https://YOUR-RAILWAY-URL.up.railway.app/health
```
**Expected:** `{"status":"ok",...}` (with `connected: true` if DB is set)

### 3. GET /graphql (should be 405)
```bash
curl -s -o /dev/null -w "%{http_code}" https://YOUR-RAILWAY-URL.up.railway.app/graphql
```
**Expected:** `405` (Method Not Allowed – GraphQL requires POST)

### 4. POST /graphql (should be 200)
```bash
curl -s -X POST https://YOUR-RAILWAY-URL.up.railway.app/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ __typename }"}'
```
**Expected:** `200` with body `{"data":{"__typename":"Query"}}` (or similar)

### 5. Sample query (categories – always valid)
```bash
curl -s -X POST https://YOUR-RAILWAY-URL.up.railway.app/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ categories { id name } }"}'
```
**Expected:** `200` with `{"data":{"categories":[...]}}`

---

## Frontend (Netlify)

### 1. Environment variable
- **Required:** `VITE_GRAPHQL_URI` = `https://YOUR-RAILWAY-URL.up.railway.app/graphql`
- No trailing slash. Must be set in Netlify → Site settings → Environment variables, then redeploy.

### 2. Network tab
- Open DevTools → Network.
- Load product list or product page.
- **Expected:** `POST` request to `https://YOUR-RAILWAY-URL.up.railway.app/graphql` with status `200`.

### 3. Missing config
- If `VITE_GRAPHQL_URI` is not set in production build, the app shows:
  > Missing VITE_GRAPHQL_URI. Set it in Netlify environment variables...
- And logs to console: `[GraphQL] Missing VITE_GRAPHQL_URI...`

---

## Common issues

| Symptom | Cause | Fix |
|--------|-------|-----|
| 404 on /graphql | Wrong base URL or path | Use `.../graphql` exactly; no `/graphql/graphql` |
| 405 on GET /graphql | Normal | GraphQL uses POST only |
| CORS errors | Backend CORS headers | Backend sends `Access-Control-Allow-Origin: *` |
| Blank / "Missing VITE_GRAPHQL_URI" | Env not set or not applied | Set `VITE_GRAPHQL_URI` in Netlify, redeploy |
