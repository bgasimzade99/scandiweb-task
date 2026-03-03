# Scandiweb Test - Requirements Checklist

**Tüm task gereksinimleri uygulandı.** Göndermeden önce http://165.227.98.170/ ile test edin.

## Data & Backend
- [x] MySQL database with data from data.json
- [x] Run `php scripts/seed-db.php` after initial schema import
- [x] GraphQL schema: categories, products, attributes
- [x] GraphQL mutation: placeOrder
- [x] Attributes as separate type, resolved through own classes

## Header
- [x] `data-testid="cart-btn"` on cart button
- [x] `data-testid="category-link"` on category links
- [x] `data-testid="active-category-link"` on active category
- [x] Item count bubble only when cart has products

## Cart Overlay
- [x] `data-testid="cart-item-attribute-${attr-kebab}"` on attribute container
- [x] `data-testid="cart-item-attribute-${attr}-${option}-selected"` when selected
- [x] `data-testid="cart-item-amount-decrease"`
- [x] `data-testid="cart-item-amount-increase"`
- [x] `data-testid="cart-item-amount"`
- [x] `data-testid="cart-total"`
- [x] Product name + main image
- [x] Selected + available options (not clickable)
- [x] Same product + same options = quantity; different options = separate rows
- [x] +/- changes quantity; - at 1 removes item
- [x] Place Order disabled when empty
- [x] Page greyed when overlay open (header visible)

## Product Listing
- [x] `data-testid="product-${name-kebab}"` on each card
- [x] Main image, name, price (2 decimals)
- [x] In-stock: clickable to PDP, Quick Shop on hover
- [x] Out-of-stock: greyed image, "Out of Stock", no Quick Shop

## Product Details Page
- [x] `data-testid="product-attribute-${attr-kebab}"`
- [x] `data-testid="product-gallery"`
- [x] `data-testid="product-description"`
- [x] `data-testid="add-to-cart"`
- [x] Size = swatch buttons, Color = color squares
- [x] Add to cart disabled until all attributes selected

## Before Submit
1. Deploy to 24/7 hosting (e.g. 000webhost.com)
2. Run Auto QA at http://165.227.98.170/
3. Fix any failing tests
4. Share repo with tests@scandiweb.com
5. Email recruiter with: live URL, repo link, Passed screenshot
