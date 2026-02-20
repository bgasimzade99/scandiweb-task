import { ApolloClient, InMemoryCache, createHttpLink } from '@apollo/client';

/** Production fallback when VITE_GRAPHQL_URI is not set on Netlify. Set VITE_GRAPHQL_URI in Netlify env, or update this to your Railway URL (e.g. https://YOUR-PROJECT.up.railway.app/graphql). */
const FALLBACK_GRAPHQL_URI = 'https://scandiweb-task-production.up.railway.app/graphql';
const graphqlUri = import.meta.env.VITE_GRAPHQL_URI || (import.meta.env.PROD ? FALLBACK_GRAPHQL_URI : '/graphql');

const httpLink = createHttpLink({
  uri: graphqlUri,
  fetchOptions: {
    mode: 'cors',
  },
  headers: {
    'Content-Type': 'application/json',
  },
});

export const client = new ApolloClient({
  link: httpLink,
  cache: new InMemoryCache(),
});
