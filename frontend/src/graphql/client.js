import { ApolloClient, InMemoryCache, createHttpLink } from '@apollo/client';

/** GraphQL API – POST /graphql only. Dev: Vite proxy → localhost:8000. Prod: Railway. */
export const API_URL = import.meta.env.VITE_GRAPHQL_URI
  || (import.meta.env.PROD ? 'https://scandiweb-task-production.up.railway.app/graphql' : '/graphql');

const httpLink = createHttpLink({
  uri: API_URL,
  fetchOptions: { mode: 'cors' },
  headers: { 'Content-Type': 'application/json' },
});

export const client = new ApolloClient({
  link: httpLink,
  cache: new InMemoryCache(),
});
