import { BrowserRouter } from 'react-router-dom';
import { ApolloProvider } from '@apollo/client/react';
import { client } from './graphql/client';
import { CartProvider } from './context/CartContext';
import AppContent from './AppContent';
import './App.css';

function App() {
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
