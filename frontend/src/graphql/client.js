import { ApolloClient, InMemoryCache, createHttpLink } from '@apollo/client';

const graphqlUri = import.meta.env.VITE_GRAPHQL_URI || '/graphql';

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
