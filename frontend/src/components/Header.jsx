import { Link, useMatch } from 'react-router-dom';
import { useCart } from '../context/CartContext';
import BagIcon from './icons/BagIcon';
import CartIcon from './icons/CartIcon';
import CartOverlay from './CartOverlay';
import './Header.css';

const CATEGORIES = [
  { id: 'all', name: 'all' },
  { id: 'clothes', name: 'clothes' },
  { id: 'tech', name: 'tech' },
];

export default function Header() {
  const { totalItems, cartOverlayOpen, setCartOverlayOpen } = useCart();
  const match = useMatch('/:category');
  const activeCategory = match?.params?.category ?? 'all';

  return (
    <>
      <header className="header">
        <nav className="nav-categories" aria-label="Store categories">
          {CATEGORIES.map((cat) => {
            const slug = cat.name.toLowerCase();
            const to = `/${slug}`;
            const isActive = activeCategory?.toLowerCase() === slug;
            return (
              <Link
                key={cat.id}
                to={to}
                className={`nav-link ${isActive ? 'active' : ''}`}
                data-testid={isActive ? 'active-category-link' : 'category-link'}
              >
                {cat.name}
              </Link>
            );
          })}
        </nav>
        <Link to="/" className="header-logo">
          <BagIcon className="header-logo-icon" />
        </Link>
        <button
          className="cart-btn"
          onClick={() => setCartOverlayOpen((prev) => !prev)}
          data-testid="cart-btn"
          aria-label={cartOverlayOpen ? 'Close cart' : 'Open cart'}
        >
          <span className="cart-icon-wrap">
            <CartIcon className="cart-icon" />
            {totalItems > 0 && (
              <span className="cart-badge">{totalItems}</span>
            )}
          </span>
        </button>
      </header>
      {cartOverlayOpen && (
        <CartOverlay onClose={() => setCartOverlayOpen(false)} />
      )}
    </>
  );
}
