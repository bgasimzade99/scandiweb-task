import { useQuery } from '@apollo/client/react';
import { Link, useParams } from 'react-router-dom';
import { GET_PRODUCTS } from '../graphql/queries';
import ProductCard from './ProductCard';
import './ProductList.css';

export default function ProductList() {
  const { category } = useParams();
  const { data, loading, error } = useQuery(GET_PRODUCTS, {
    variables: { category: category ?? 'all' },
  });

  if (loading) return <div className="loading">Loading...</div>;
  if (error) {
    const err = error?.graphQLErrors?.[0];
    const msg = err?.message ?? error?.networkError?.message ?? error?.message ?? 'Error loading products';
    const code = err?.extensions?.code;
    return (
      <div className="error">
        Error loading products: {String(msg)}
        {code && <div style={{ fontSize: '0.85em', marginTop: 4 }}>[{code}]</div>}
      </div>
    );
  }

  const products = data?.products ?? [];

  const showBack = category && category !== 'all';

  return (
    <main className="product-list-page">
      <div className="category-header">
        {showBack && (
          <Link to="/category/all" className="back-link">
            <span className="back-arrow">←</span>
            All
          </Link>
        )}
        <h1 className="category-title">{category ?? 'all'}</h1>
      </div>
      <div className="product-grid">
        {products.map((product) => (
          <ProductCard key={product.id} product={product} />
        ))}
      </div>
    </main>
  );
}
