import { useQuery } from '@apollo/client/react';
import { useParams } from 'react-router-dom';
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
    const msg = error?.graphQLErrors?.[0]?.message ?? error?.networkError?.message ?? error?.message ?? 'Error loading products';
    return <div className="error">Error loading products: {String(msg)}</div>;
  }

  const products = data?.products ?? [];

  return (
    <main className="product-list-page">
      <h1 className="category-title">{category ?? 'all'}</h1>
      <div className="product-grid">
        {products.map((product) => (
          <ProductCard key={product.id} product={product} />
        ))}
      </div>
    </main>
  );
}
