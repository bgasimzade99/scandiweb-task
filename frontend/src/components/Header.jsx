import { Link, useParams } from 'react-router-dom';
import { useQuery } from '@apollo/client/react';
import { GET_CATEGORIES } from '../graphql/queries';
import { useCart } from '../context/CartContext';
import CartOverlay from './CartOverlay';
import './Header.css';

function BagIcon({ className }) {
  return (
    <svg className={className} width="32" height="30" viewBox="703.92 29.41 31.16 30.18" fill="none">
      <defs>
        <linearGradient id="bag-gradient" x1="728.873" y1="55.3337" x2="710.513" y2="33.9008" gradientUnits="userSpaceOnUse">
          <stop stopColor="#52D67A"/>
          <stop offset="1" stopColor="#5AEE87"/>
        </linearGradient>
      </defs>
      <path d="M733.022 52.6646C733.049 52.983 732.801 53.2566 732.485 53.2566H706.469C706.154 53.2566 705.906 52.9843 705.932 52.6665L707.796 29.9123C707.819 29.6296 708.053 29.4121 708.334 29.4121H730.543C730.823 29.4121 731.056 29.6285 731.08 29.9104L733.022 52.6646Z" fill="#1DCF65"/>
      <path d="M735.099 58.6014C735.131 58.9985 734.821 59.339 734.427 59.339H704.594C704.201 59.339 703.891 59.0002 703.922 58.6037L706.064 31.3472C706.092 30.9927 706.384 30.7197 706.736 30.7197H732.196C732.547 30.7197 732.839 30.9916 732.868 31.345L735.099 58.6014Z" fill="url(#bag-gradient)"/>
      <path d="M718.923 50.6953C715.04 50.6953 711.881 46.8631 711.881 42.1528C711.881 41.9075 712.078 41.7085 712.321 41.7085C712.564 41.7085 712.761 41.9073 712.761 42.1528C712.761 46.3732 715.525 49.8067 718.923 49.8067C722.321 49.8067 725.086 46.3732 725.086 42.1528C725.086 41.9075 725.283 41.7085 725.526 41.7085C725.769 41.7085 725.965 41.9073 725.965 42.1528C725.965 46.8631 722.806 50.6953 718.923 50.6953Z" fill="white"/>
      <path d="M723.258 42.0337C723.146 42.0337 723.033 41.9904 722.947 41.9036C722.775 41.7301 722.775 41.4488 722.947 41.2753L725.226 38.9729C725.308 38.8897 725.42 38.8428 725.537 38.8428C725.654 38.8428 725.765 38.8895 725.848 38.9729L728.104 41.2529C728.276 41.4264 728.276 41.7077 728.104 41.8812C727.933 42.0546 727.654 42.0547 727.483 41.8812L725.537 39.9155L723.569 41.9036C723.483 41.9904 723.371 42.0337 723.258 42.0337Z" fill="white"/>
    </svg>
  );
}

function CartIcon({ className }) {
  return (
    <svg className={className} width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
      <path d="M9 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="currentColor"/>
      <path d="M20 22a1 1 0 1 0 0-2 1 1 0 0 0 0 2z" fill="currentColor"/>
      <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" strokeLinecap="round" strokeLinejoin="round"/>
    </svg>
  );
}

export default function Header() {
  const { data } = useQuery(GET_CATEGORIES);
  const { totalItems, cartOverlayOpen, setCartOverlayOpen } = useCart();
  const { category } = useParams();

  const categories = data?.categories ?? [];
  const activeCategory = category ?? (categories[0]?.name ?? 'all');

  return (
    <>
      <header className="header">
        <nav className="nav-categories">
          {categories.map((cat) => (
            <Link
              key={cat.id}
              to={`/category/${cat.name}`}
              className={`nav-link ${activeCategory === cat.name ? 'active' : ''}`}
              data-testid={
                activeCategory === cat.name
                  ? 'active-category-link'
                  : 'category-link'
              }
            >
              {cat.name}
            </Link>
          ))}
        </nav>
        <Link to="/category/all" className="header-logo">
          <BagIcon className="header-logo-icon" />
        </Link>
        <button
          className="cart-btn"
          onClick={() => setCartOverlayOpen(true)}
          data-testid="cart-btn"
          aria-label="Open cart"
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
