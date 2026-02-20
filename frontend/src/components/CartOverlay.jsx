import { useState } from 'react';
import { Link } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import { useMutation } from '@apollo/client/react';
import { PLACE_ORDER } from '../graphql/queries';
import { toKebab } from '../utils/testId';
import './CartOverlay.css';

export default function CartOverlay({ onClose }) {
  const { cart, updateQuantity, removeFromCart, totalPrice, totalItems, clearCart } = useCart();
  const [orderError, setOrderError] = useState(null);
  const [placeOrder, { loading }] = useMutation(PLACE_ORDER, {
    onCompleted: (data) => {
      if (data?.placeOrder?.success) {
        clearCart();
        localStorage.removeItem('scandiweb-cart');
        setOrderError(null);
        onClose();
      }
    },
    onError: (err) => {
      setOrderError(err.message || 'Failed to place order');
    },
  });

  const handlePlaceOrder = () => {
    if (cart.length === 0) return;
    setOrderError(null);
    placeOrder({
      variables: {
        order: {
          products: cart.map((item) => ({
            id: item.id,
            quantity: item.quantity,
            attrs: Object.values(item.selectedAttrs ?? {}),
          })),
        },
      },
    });
  };

  const symbol = cart[0]?.prices?.[0]?.currency?.symbol ?? '$';

  return (
    <>
      <div className="cart-overlay-backdrop" onClick={onClose} />
      <div className="cart-overlay" data-testid="cart-overlay">
        <div className="cart-content">
          <div className="cart-header">
            <h2 className="cart-title">
              My Bag{totalItems > 0 && (
                <span className="cart-title-count">, {totalItems} {totalItems === 1 ? 'item' : 'items'}</span>
              )}
            </h2>
            <button
              type="button"
              className="cart-close-btn"
              onClick={onClose}
              aria-label="Close cart"
            >
              <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                <path d="M15 5L5 15M5 5l10 10" />
              </svg>
            </button>
          </div>
          <div className="cart-items">
            {cart.map((item) => (
              <div key={item.key} className="cart-item">
                <div className="cart-item-info">
                  <div className="cart-item-info-inner">
                    <div className="cart-item-name-row">
                      <p className="cart-item-name">{item.name}</p>
                      <button
                        type="button"
                        className="cart-item-remove"
                        onClick={() => removeFromCart(item.key)}
                        aria-label={`Remove ${item.name} from cart`}
                      >
                        Remove
                      </button>
                    </div>
                    <p className="cart-item-price">
                      {symbol}{item.prices?.[0]?.amount?.toFixed(2)}
                    </p>
                    {item.attributes?.map((attr) => (
                      <div
                        key={attr.id}
                        className="cart-item-attr"
                        data-testid={`cart-item-attribute-${toKebab(attr.name)}`}
                      >
                        <span className="attr-name">{attr.name}:</span>
                        <span className="attr-options">
                          {attr.items?.map((opt) => {
                            const isSelected =
                              item.selectedAttrs?.[attr.name] === opt.value;
                            const optKebab = toKebab(opt.display_value ?? opt.value);
                            const attrKebab = toKebab(attr.name);
                            return (
                              <span
                                key={opt.id}
                                className={`attr-option ${isSelected ? 'selected' : ''} ${attr.type === 'swatch' ? 'swatch' : ''}`}
                                data-testid={`cart-item-attribute-${attrKebab}-${optKebab}${isSelected ? '-selected' : ''}`}
                                style={
                                  attr.type === 'swatch'
                                    ? { backgroundColor: opt.value }
                                    : {}
                                }
                              >
                                {attr.type === 'text' ? opt.display_value : ''}
                              </span>
                            );
                          })}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>
                <div className="cart-item-right">
                  <div className="cart-item-actions">
                    <button
                      className="qty-btn"
                      onClick={() => updateQuantity(item.key, 1)}
                      data-testid="cart-item-amount-increase"
                    >
                      +
                    </button>
                    <span className="cart-item-qty" data-testid="cart-item-amount">{item.quantity}</span>
                    <button
                      className="qty-btn"
                      onClick={() => updateQuantity(item.key, -1)}
                      data-testid="cart-item-amount-decrease"
                    >
                      −
                    </button>
                  </div>
                  <img
                    src={item.gallery?.[0]}
                    alt={item.name}
                    className="cart-item-image"
                  />
                </div>
              </div>
            ))}
          </div>
          <div className="cart-footer">
          <div className="cart-total-row" data-testid="cart-total">
            <span className="cart-total-label">Total</span>
            <span className="cart-total-amount">{symbol}{totalPrice.toFixed(2)}</span>
          </div>
          {orderError && (
            <p className="cart-order-error" role="alert">{orderError}</p>
          )}
          <div className="cart-footer-buttons">
            <Link to="/category/all" className="view-bag-btn" onClick={onClose}>
              VIEW BAG
            </Link>
            <button
              className="place-order-btn"
              onClick={handlePlaceOrder}
              disabled={cart.length === 0 || loading}
            >
              {loading ? 'Placing...' : 'PLACE ORDER'}
            </button>
          </div>
        </div>
        </div>
      </div>
    </>
  );
}
