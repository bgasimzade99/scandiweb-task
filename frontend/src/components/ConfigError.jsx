/** Shown when VITE_GRAPHQL_URI is missing in production. ApolloProvider is not rendered. */
export default function ConfigError() {
  return (
    <div style={{ padding: 24, fontFamily: 'sans-serif', color: '#c00' }}>
      Missing VITE_GRAPHQL_URI. Set it in Netlify environment variables (e.g. https://YOUR-APP.up.railway.app/graphql).
    </div>
  );
}
