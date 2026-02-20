import { useEffect } from 'react';
import { BrowserRouter, Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { ApolloProvider } from '@apollo/client/react';
import { client } from './graphql/client';
import { CartProvider, useCart } from './context/CartContext';
import Header from './components/Header';
import ProductList from './components/ProductList';
import ProductPage from './components/ProductPage';
import './App.css';

function AppContent() {
  const { cartOverlayOpen, setCartOverlayOpen } = useCart();
  const location = useLocation();

  useEffect(() => {
    setCartOverlayOpen(false);
  }, [location.pathname, setCartOverlayOpen]);

  return (
    <div className={`app ${cartOverlayOpen ? 'cart-overlay-open' : ''}`} data-testid="app">
      <Header />
      <div className="main-content">
        <Routes>
          <Route path="/" element={<Navigate to="/category/all" replace />} />
          <Route path="/category/:category" element={<ProductList />} />
          <Route path="/product/:id" element={<ProductPage />} />
          <Route path="*" element={<Navigate to="/category/all" replace />} />
        </Routes>
      </div>
    </div>
  );
}

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
