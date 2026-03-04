import { ApolloClient, InMemoryCache, createHttpLink } from '@apollo/client';

/** GraphQL API – strictly env-driven in production. Dev: Vite proxy /graphql → localhost:8000. */
const envUri = (import.meta.env.VITE_GRAPHQL_URI ?? '').trim();
const isDev = !import.meta.env.PROD;

export const GRAPHQL_URI = isDev ? (envUri || '/graphql') : envUri;
export const isGraphQLConfigured = GRAPHQL_URI.length > 0;

if (!isGraphQLConfigured) {
  const msg = 'Missing VITE_GRAPHQL_URI. Set it in Netlify env (e.g. https://YOUR-APP.up.railway.app/graphql).';
  console.error('[GraphQL]', msg);
}

const httpLink = isGraphQLConfigured
  ? createHttpLink({
      uri: GRAPHQL_URI,
      fetchOptions: { mode: 'cors' },
      headers: { 'Content-Type': 'application/json' },
    })
  : null;

export const client = isGraphQLConfigured
  ? new ApolloClient({ link: httpLink, cache: new InMemoryCache() })
  : null;
