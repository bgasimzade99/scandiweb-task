# Frontend – React SPA

Vite + React + Apollo Client + React Router.

## Setup

```bash
npm install
```

## Run

```bash
npm run dev
```

Dev server at http://localhost:5173. Proxies `/graphql` to backend (see vite.config.js).

## Scripts

- `npm run dev` – Vite dev server
- `npm run build` – Production build (output: `dist/`)
- `npm run preview` – Preview production build
- `npm run start` – Mock API + dev (no backend required)
- `npm run mock-api` – Node mock GraphQL server on :8000

## Environment

Create `.env` with `VITE_GRAPHQL_URI` for production (Netlify): backend GraphQL URL.
