import { useState } from 'react';
import parse from 'html-react-parser';
import { toKebab } from '../utils/testId';

export default function ProductDetailsForm({ product, onAddToCart }) {
  const [selectedAttrs, setSelectedAttrs] = useState({});

  const allSelected = !product.attributes?.length ||
    product.attributes.every((attr) =>
      attr.items?.some((item) => selectedAttrs[attr.name] === item.value)
    );

  const handleAttrSelect = (attrName, value) => {
    setSelectedAttrs((prev) => ({ ...prev, [attrName]: value }));
  };

  const handleAddToCart = () => {
    if (!allSelected) return;
    onAddToCart(product, selectedAttrs);
  };

  const description = product?.description ? parse(product.description) : null;

  return (
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
              const displayVal = item.display_value ?? item.displayValue;
              const optPart = (displayVal ?? item.value ?? '').toString().trim();
              const optTestId = optPart
                ? optPart.replace(/\s+/g, '-')
                : toKebab(String(item.value ?? ''));
              const isSelected = selectedAttrs[attr.name] === item.value;
              const baseTestId = `product-attribute-${toKebab(attr.name)}-${optTestId}`;
              return (
                <button
                  key={item.id}
                  type="button"
                  className={`attr-option ${
                    isSelected ? 'selected' : ''
                  } ${attr.type === 'swatch' ? 'swatch' : ''}`}
                  onClick={() => handleAttrSelect(attr.name, item.value)}
                  style={
                    attr.type === 'swatch'
                      ? { backgroundColor: item.value }
                      : {}
                  }
                  data-testid={`${baseTestId}${isSelected ? '-selected' : ''}`}
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
      <button
        className="add-to-cart-btn"
        onClick={handleAddToCart}
        disabled={!product.in_stock || !allSelected}
        data-testid="add-to-cart"
      >
        {product.in_stock ? 'Add to cart' : 'Out of Stock'}
      </button>
      <div
        className="product-description"
        data-testid="product-description"
      >
        {description}
      </div>
    </>
  );
}
