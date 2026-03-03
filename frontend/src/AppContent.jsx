import { useEffect } from 'react';
import { Routes, Route, Navigate, useLocation } from 'react-router-dom';
import { useCart } from './context/CartContext';
import Header from './components/Header';
import ProductList from './components/ProductList';
import ProductPage from './components/ProductPage';

export default function AppContent() {
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
          <Route path="/" element={<Navigate to="/all" replace />} />
          <Route path="/product/:id" element={<ProductPage />} />
          <Route path="/:category" element={<ProductList />} />
          <Route path="*" element={<Navigate to="/all" replace />} />
        </Routes>
      </div>
    </div>
  );
}
