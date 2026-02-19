import { createContext, useContext, useState, useEffect } from 'react';

const CartContext = createContext();

export function CartProvider({ children }) {
  const [cartOverlayOpen, setCartOverlayOpen] = useState(false);
  const [cart, setCart] = useState(() => {
    try {
      const saved = localStorage.getItem('scandiweb-cart');
      return saved ? JSON.parse(saved) : [];
    } catch {
      return [];
    }
  });

  useEffect(() => {
    localStorage.setItem('scandiweb-cart', JSON.stringify(cart));
  }, [cart]);

  const addToCart = (product, selectedAttrs = {}, options = {}) => {
    const sortedAttrs = Object.keys(selectedAttrs)
      .sort()
      .reduce((acc, k) => {
        acc[k] = selectedAttrs[k];
        return acc;
      }, {});
    const key = `${product.id}-${JSON.stringify(sortedAttrs)}`;
    if (options.openOverlay) setCartOverlayOpen(true);
    setCart((prev) => {
      const existing = prev.find((item) => item.key === key);
      if (existing) {
        return prev.map((item) =>
          item.key === key ? { ...item, quantity: item.quantity + 1 } : item
        );
      }
      return [...prev, { ...product, selectedAttrs, quantity: 1, key }];
    });
  };

  const updateQuantity = (key, delta) => {
    setCart((prev) => {
      const item = prev.find((i) => i.key === key);
      if (!item) return prev;
      const newQty = item.quantity + delta;
      if (newQty <= 0) {
        return prev.filter((i) => i.key !== key);
      }
      return prev.map((i) =>
        i.key === key ? { ...i, quantity: newQty } : i
      );
    });
  };

  const removeFromCart = (key) => {
    setCart((prev) => prev.filter((i) => i.key !== key));
  };

  const clearCart = () => setCart([]);

  const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
  const totalPrice = cart.reduce((sum, item) => {
    const price = item.prices?.[0]?.amount ?? 0;
    return sum + price * item.quantity;
  }, 0);

  return (
    <CartContext.Provider
      value={{
        cart,
        cartOverlayOpen,
        setCartOverlayOpen,
        addToCart,
        updateQuantity,
        removeFromCart,
        clearCart,
        totalItems,
        totalPrice,
      }}
    >
      {children}
    </CartContext.Provider>
  );
}

export function useCart() {
  const ctx = useContext(CartContext);
  if (!ctx) throw new Error('useCart must be used within CartProvider');
  return ctx;
}
