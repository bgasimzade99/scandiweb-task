import { Link, useParams } from 'react-router-dom';
import { useState } from 'react';
import { useQuery } from '@apollo/client/react';
import { GET_PRODUCT } from '../graphql/queries';
import { useCart } from '../context/CartContext';
import ProductDetailsForm from './ProductDetailsForm';
import './ProductPage.css';

export default function ProductPage() {
  const { id } = useParams();
  const [galleryIndex, setGalleryIndex] = useState(0);

  const { data, loading, error } = useQuery(GET_PRODUCT, {
    variables: { id },
  });

  const { addToCart } = useCart();
  const product = data?.product;

  const handleAddToCart = (prod, attrs) => {
    addToCart(prod, attrs, { openOverlay: true });
  };

  const gallery = product && Array.isArray(product.gallery) ? product.gallery : [];
  const mainImage = gallery[0] || null;

  return (
    <>
      <main className="product-page" data-testid="product-page">
        <Link to="/all" className="back-link">
          <span className="back-arrow">←</span>
          All products
        </Link>
        <div className="product-gallery" data-testid="product-gallery">
          <div className="gallery-thumbs">
            {gallery.map((src, i) => (
              <button
                key={`${src}-${i}`}
                type="button"
                className={`thumb ${i === galleryIndex ? 'active' : ''}`}
                onClick={() => setGalleryIndex(i)}
              >
                <img src={src} alt="" />
              </button>
            ))}
          </div>
          <div className="gallery-main">
            {mainImage && (
              <img
                src={gallery[galleryIndex] || mainImage}
                alt={product?.name ?? ''}
              />
            )}
            {gallery.length > 1 && mainImage && (
              <>
                <button
                  className="gallery-arrow prev"
                  onClick={() =>
                    setGalleryIndex((i) => (i === 0 ? gallery.length - 1 : i - 1))
                  }
                >
                  ‹
                </button>
                <button
                  className="gallery-arrow next"
                  onClick={() =>
                    setGalleryIndex((i) => (i === gallery.length - 1 ? 0 : i + 1))
                  }
                >
                  ›
                </button>
              </>
            )}
          </div>
        </div>
        <div className="product-details">
          {loading && <div className="loading">Loading...</div>}
          {error && !product && <div className="error">Product not found</div>}
          {product && (
            <ProductDetailsForm
              key={product.id}
              product={product}
              onAddToCart={handleAddToCart}
            />
          )}
          {!product && (
            <>
              <button
                className="add-to-cart-btn"
                disabled
                data-testid="add-to-cart"
              >
                Add to cart
              </button>
              <div
                className="product-description"
                data-testid="product-description"
              />
            </>
          )}
        </div>
      </main>
    </>
  );
}
