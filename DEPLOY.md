# Netlify + Railway/Render Deployment

## Frontend (Netlify)

1. GitHub'a push edin
2. [Netlify](https://app.netlify.com) → Add new site → Import from Git → Repo seçin
3. Build ayarları (netlify.toml ile otomatik):
   - Base directory: `frontend`
   - Build command: `npm run build`
   - Publish directory: `frontend/dist`
4. **Environment variable ekleyin:**
   - `VITE_GRAPHQL_URI` = `https://YOUR-BACKEND-URL/graphql` (Railway/Render backend URL)

## Backend (Railway)

1. [Railway](https://railway.app) → New Project → Deploy from GitHub
2. Repo seçin
3. **ÖNEMLİ - Builder:** Settings → Build → **Builder: "Dockerfile"** seçin (Nixpacks değil!)
   - Cache sorunu varsa: Variables'a `NO_CACHE=1` ekleyip Redeploy
4. **Root Directory:** `/` veya boş bırakın
5. **MySQL ekleyin:** New → Database → MySQL
6. **Environment variables:** DB_HOST, DB_NAME, DB_USER, DB_PASS (MySQL connection string'den)
7. Deploy otomatik başlar
6. Settings → Generate Domain → URL'i kopyalayın
7. Netlify'a `VITE_GRAPHQL_URI` = `https://xxx.railway.app/graphql` ekleyin

## Backend (Render - alternatif)

1. [Render](https://render.com) → New → Web Service
2. Repo bağlayın
3. Environment: PHP
4. Build: `composer install --no-dev`
5. Start: `php -S 0.0.0.0:$PORT -t public`
6. MySQL: Render PostgreSQL yerine external MySQL (Railway, PlanetScale vb.) kullanın

## Veritabanı

Railway/Render'da MySQL eklendiğinde connection string alırsınız. `.env` değişkenlerini dashboard'dan ekleyin.

**İlk deploy sonrası:** Backend'de `php scripts/seed-db.php` çalıştırın (Railway/Render Console veya one-off job).
