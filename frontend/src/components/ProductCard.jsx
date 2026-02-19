import { Link } from 'react-router-dom';
import { useState } from 'react';
import { useCart } from '../context/CartContext';
import { toKebab } from '../utils/testId';
import './ProductCard.css';

export default function ProductCard({ product, onQuickAdd }) {
  const [hovered, setHovered] = useState(false);
  const { addToCart } = useCart();
  const inStock = product.in_stock;
  const price = product.prices?.[0];
  const mainImage = product.gallery?.[0];

  const getDefaultAttrs = () => {
    const selected = {};
    product.attributes?.forEach((attr) => {
      if (attr.items?.[0]) {
        selected[attr.name] = attr.items[0].value;
      }
    });
    return selected;
  };

  const handleQuickShop = (e) => {
    e.preventDefault();
    e.stopPropagation();
    if (inStock) {
      addToCart(product, getDefaultAttrs(), { openOverlay: true });
      onQuickAdd?.();
    }
  };

  const productNameKebab = toKebab(product.name);

  return (
    <Link
      to={`/product/${product.id}`}
      className={`product-card ${!inStock ? 'out-of-stock' : ''}`}
      data-testid={`product-${productNameKebab}`}
      onMouseEnter={() => setHovered(true)}
      onMouseLeave={() => setHovered(false)}
    >
      <div className="product-card-inner">
      <div className="product-image-wrap">
        <img src={mainImage} alt={product.name} className="product-image" />
        {!inStock && (
          <div className="out-of-stock-badge">out of stock</div>
        )}
        {inStock && hovered && (
          <button
            className="quick-shop-btn"
            onClick={handleQuickShop}
            aria-label="Add to cart"
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
              <path d="M9 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="currentColor"/>
              <path d="M20 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="currentColor"/>
              <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" strokeLinecap="round" strokeLinejoin="round"/>
            </svg>
          </button>
        )}
      </div>
      <div className="product-info">
        <p className="product-name">{product.name}</p>
        <p className="product-price">
          {price?.currency?.symbol}
          {price?.amount?.toFixed(2)}
        </p>
      </div>
      </div>
    </Link>
  );
}
