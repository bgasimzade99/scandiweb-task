import { useQuery } from '@apollo/client/react';
import { Link, useParams } from 'react-router-dom';
import { GET_PRODUCTS } from '../graphql/queries';
import ProductCard from './ProductCard';
import './ProductList.css';

const PLACEHOLDER_COUNT = 6;

export default function ProductList() {
  const { category } = useParams();
  const { data, loading, error } = useQuery(GET_PRODUCTS, {
    variables: { category: category ?? 'all' },
  });

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
      <div className="product-grid" data-testid="product-grid">
        {loading
          ? Array.from({ length: PLACEHOLDER_COUNT }, (_, i) => (
              <div key={`placeholder-${i}`} className="product-card product-card-placeholder" data-testid={`product-placeholder-${i}`}>
                <div className="product-card-inner">
                  <div className="product-image-wrap" />
                  <div className="product-info">
                    <p className="product-name" />
                    <p className="product-price" />
                  </div>
                </div>
              </div>
            ))
          : error
            ? (
              <div className="error">
                Error loading products: {String(error?.graphQLErrors?.[0]?.message ?? error?.networkError?.message ?? error?.message ?? 'Unknown error')}
              </div>
            )
            : products.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
      </div>
    </main>
  );
}
