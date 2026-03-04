import { BrowserRouter } from 'react-router-dom';
import { ApolloProvider } from '@apollo/client/react';
import { client, isGraphQLConfigured } from './graphql/client';
import { CartProvider } from './context/CartContext';
import AppContent from './AppContent';
import ConfigError from './components/ConfigError';
import './App.css';

function App() {
  if (!isGraphQLConfigured) {
    return <ConfigError />;
  }
  return (
    <ApolloProvider client={client}>
      <CartProvider>
        <BrowserRouter>
          <AppContent />
        </BrowserRouter>
      </CartProvider>
    </ApolloProvider>
  );
}

export default App;
