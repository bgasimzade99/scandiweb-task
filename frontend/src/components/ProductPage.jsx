import { Link, useParams } from 'react-router-dom';
import { useState, useRef } from 'react';
import { useQuery } from '@apollo/client/react';
import parse from 'html-react-parser';
import { GET_PRODUCT } from '../graphql/queries';
import { useCart } from '../context/CartContext';
import { toKebab } from '../utils/testId';
import './ProductPage.css';

export default function ProductPage() {
  const { id } = useParams();
  const [selectedAttrs, setSelectedAttrs] = useState({});
  const [galleryIndex, setGalleryIndex] = useState(0);

  const { data, loading, error } = useQuery(GET_PRODUCT, {
    variables: { id },
  });

  const { addToCart } = useCart();
  const product = data?.product;
  const prevProductIdRef = useRef(null);

  if (product?.id !== prevProductIdRef.current) {
    prevProductIdRef.current = product?.id ?? null;
    const defaults = product?.attributes
      ? product.attributes.reduce((acc, attr) => {
          if (attr.items?.[0]) acc[attr.name] = attr.items[0].value;
          return acc;
        }, {})
      : {};
    setSelectedAttrs(defaults);
  }

  const allSelected = !product
    ? false
    : !product.attributes?.length ||
      product.attributes.every((attr) =>
        attr.items?.some((item) => selectedAttrs[attr.name] === item.value)
      );

  const handleAttrSelect = (e, attrName, value) => {
    e.preventDefault();
    e.stopPropagation();
    setSelectedAttrs((prev) => ({ ...prev, [attrName]: value }));
  };

  const handleAddToCart = () => {
    if (!allSelected) return;
    addToCart(product, selectedAttrs, { openOverlay: true });
  };

  const gallery = product && Array.isArray(product.gallery) ? product.gallery : [];
  const mainImage = gallery[0] || null;
  const description = product?.description ? parse(product.description) : null;

  return (
    <>
      <main className="product-page" data-testid="product-page">
        <Link to="/category/all" className="back-link">
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
            <>
              <h1 className="product-name">{product.name}</h1>
              <p className="product-brand">{product.brand}</p>
              {product.attributes?.map((attr) => (
                <div
                  key={attr.id}
                  className="product-attr"
                  data-testid={`product-attribute-${toKebab(attr.name)}`}
                >
                  <span className="attr-label">{attr.name}:</span>
                  <div className="attr-options">
                    {attr.items?.map((item) => {
                      const optKebab = toKebab(item.display_value ?? item.value);
                      const isSelected = selectedAttrs[attr.name] === item.value;
                      return (
                        <button
                          key={item.id}
                          type="button"
                          className={`attr-option ${
                            isSelected ? 'selected' : ''
                          } ${attr.type === 'swatch' ? 'swatch' : ''}`}
                          onClick={(e) => handleAttrSelect(e, attr.name, item.value)}
                          style={
                            attr.type === 'swatch'
                              ? { backgroundColor: item.value }
                              : {}
                          }
                          data-testid={`product-attribute-${toKebab(attr.name)}-${optKebab}${isSelected ? '-selected' : ''}`}
                        >
                          {attr.type === 'text' ? item.display_value : ''}
                        </button>
                      );
                    })}
                  </div>
                </div>
              ))}
              <div className="product-price-section">
                <span className="product-price-label">Price:</span>
                <p className="product-price-value">
                  {product.prices?.[0]?.currency?.symbol}
                  {product.prices?.[0]?.amount?.toFixed(2)}
                </p>
              </div>
            </>
          )}
          <button
            className="add-to-cart-btn"
            onClick={handleAddToCart}
            disabled={!product?.in_stock || !allSelected}
            data-testid="add-to-cart"
          >
            {product?.in_stock ? 'Add to cart' : 'Out of Stock'}
          </button>
          <div
            className="product-description"
            data-testid="product-description"
          >
            {description}
          </div>
        </div>
      </main>
    </>
  );
}
