# Scandiweb Junior Full Stack Test – QA Review Checklist

**Review Date:** Strict analysis against stated requirements  
**Auto QA URL:** http://165.227.98.170/

---

## BACKEND

| Requirement | Status | Notes |
|-------------|--------|-------|
| PHP OOP structure (PSR compliant) | ✅ Implemented | PSR-4 autoload, namespaces under `App\`, `declare(strict_types=1)` |
| GraphQL server setup | ✅ Implemented | `webonyx/graphql-php`, POST `/graphql`, SchemaBuilder |
| Category, Product, Attribute polymorphism | ✅ Implemented | AbstractCategory, AbstractProduct, TextAttribute, SwatchAttribute |
| MySQL integration with provided data.json | ✅ Implemented | `SeedFromDataJson.php`, `scripts/seed-db.php`, data.json |
| Product queries | ✅ Implemented | `products(category)`, `product(id)` |
| Category queries | ✅ Implemented | `categories`, `category(input: { title })` |
| Attribute type resolvers | ✅ Implemented | AttributeResolver, ProductType resolves attributes |
| Order mutation | ✅ Implemented | `placeOrder(order: PlaceOrderInput!)` returns order ID |
| No framework usage | ✅ Implemented | Plain PHP, FastRoute, no Laravel/Symfony |

---

## FRONTEND

| Requirement | Status | Notes |
|-------------|--------|-------|
| Vite + React SPA setup | ✅ Implemented | Vite 7, React 19, build to `public/` |
| GraphQL queries integration | ✅ Implemented | Apollo Client 4, GET_CATEGORIES, GET_PRODUCTS, GET_PRODUCT |
| Product listing functionality | ✅ Implemented | ProductList + ProductCard, category routing |
| Product detail page logic | ✅ Implemented | ProductPage with attributes, gallery, add to cart |
| Cart overlay functionality | ✅ Implemented | CartOverlay with items, quantity, place order |
| Attribute selection system | ✅ Implemented | PDP: Size/Color swatches + text; Cart: read-only display |
| Quantity increase/decrease logic | ✅ Implemented | +/− in cart; − at 1 removes item |
| Disabled states behavior | ✅ Implemented | Add to cart disabled when out-of-stock or attrs missing; Place Order when empty |
| Local state persistence | ✅ Implemented | localStorage `scandiweb-cart`, CartContext |
| Order placement mutation | ✅ Implemented | PLACE_ORDER mutation, clears cart on success |

---

## TEST ATTRIBUTES (data-testid)

| Location | Required | Status | Current Implementation |
|----------|----------|--------|-------------------------|
| Cart button | cart-btn | ✅ Implemented | `data-testid="cart-btn"` |
| Category links | category-link | ✅ Implemented | `data-testid="category-link"` |
| Active category | active-category-link | ✅ Implemented | `data-testid="active-category-link"` |
| Product cards | product-{name-kebab} | ✅ Implemented | `data-testid="product-${productNameKebab}"` |
| Product gallery | product-gallery | ✅ Implemented | `data-testid="product-gallery"` |
| Product attributes (PDP) | product-attribute-{attr-kebab} | ✅ Implemented | On attribute container |
| PDP attribute options | product-attribute-{attr}-{option} | ⚠️ Partially implemented | Only container has testid; individual options may need `product-attribute-${attr}-${option}` for Auto QA |
| Add to cart button | add-to-cart | ✅ Implemented | `data-testid="add-to-cart"` |
| Product description | product-description | ✅ Implemented | `data-testid="product-description"` |
| Cart overlay – attribute | cart-item-attribute-{attr} | ✅ Implemented | Container + option with `-selected` |
| Cart overlay – amount | cart-item-amount | ✅ Implemented | `data-testid="cart-item-amount"` |
| Cart overlay – increase | cart-item-amount-increase | ✅ Implemented | ✓ |
| Cart overlay – decrease | cart-item-amount-decrease | ✅ Implemented | ✓ |
| Cart overlay – total | cart-total | ✅ Implemented | ✓ |

---

## UI & BEHAVIOR

| Requirement | Status | Notes |
|-------------|--------|-------|
| Out-of-stock handling | ✅ Implemented | Greyed image, "Out of Stock" badge, no Quick Shop, disabled add to cart |
| Hover quick shop behavior | ✅ Implemented | Quick Shop btn on hover (in-stock only) |
| Cart overlay grey background | ✅ Implemented | `rgba(57, 55, 72, 0.22)` backdrop |
| Gallery carousel functionality | ✅ Implemented | Thumbs + prev/next arrows, image switching |
| Proper price formatting | ✅ Implemented | `toFixed(2)` with currency symbol |

---

## POTENTIAL GAPS & RISKS

### ⚠️ Partially Implemented / Verify

1. **PDP attribute option testids**  
   Auto QA may expect `data-testid="product-attribute-{attr}-{option}"` on each option button, not just on the container. Add testids to option buttons if tests fail.

2. **GraphQL schema vs official endpoint**  
   Your PHP backend uses `products(category: String)` and `category(input: { title })`. The official Scandiweb React endpoint may use different argument names. If Auto QA targets their endpoint, query shapes must match.

3. **Currency / price source**  
   Prices come from `prices[0]`. Verify currency symbol and amount format match what Auto QA expects (e.g. `$50.00`).

### ❌ Missing / Unverified

1. **Explicit default attribute selection on PDP**  
   `selectedAttrs` starts as `{}`. Requirements say “Add to cart disabled until all attributes selected.” If products have attributes, the first option is not auto-selected. Some specs expect default selection; confirm vs. your REQUIREMENTS_CHECKLIST.

2. **Cart overlay close on backdrop click**  
   Implemented via `onClick={onClose}` on `.cart-overlay-backdrop`. Verify this is covered by Auto QA.

3. **VIEW BAG button navigation**  
   Links to `/category/all`. Ensure the route and behavior match expectations.

---

## OVERALL COMPLETION

| Area | Score | Notes |
|------|-------|-------|
| Backend | **95%** | Solid; verify order mutation `attrs` format vs. endpoint |
| Frontend | **90%** | Feature complete; PDP attribute testids may need expansion |
| Test Attributes | **85%** | Core testids present; option-level PDP testids uncertain |
| UI & Behavior | **95%** | Matches described behavior |

**Approximate overall completion: ~91%**

---

## CRITICAL BLOCKERS FOR AUTO QA

1. **PDP attribute testids on options**  
   Add `data-testid="product-attribute-${attrKebab}-${optionKebab}"` to each attribute option button. If Auto QA fails on attribute selection, this is the most likely cause.

2. **Exact testid strings**  
   Run Auto QA and fix any mismatches (e.g. `cart` vs `cart-btn`, `product-card` vs `product-{name}`).

3. **GraphQL compatibility**  
   If tests call the official Scandiweb endpoint, your app uses your own. You may need to ensure query/variable compatibility or use their endpoint for QA.

4. **Price format**  
   Ensure prices render as `$XX.XX` (2 decimals) everywhere. Already using `toFixed(2)`; confirm with Auto QA.

---

## PRIORITY ORDER FOR IMPLEMENTATION

1. **P0 – Run Auto QA**  
   Go to http://165.227.98.170/, run tests, and capture failing steps.

2. **P1 – PDP attribute option testids**  
   Add `data-testid` to attribute option buttons:
   ```jsx
   data-testid={`product-attribute-${toKebab(attr.name)}-${toKebab(item.display_value ?? item.value)}`}
   ```
   Append `-selected` when selected if Auto QA expects it.

3. **P2 – Fix failures from Auto QA**  
   Update testids, selectors, or behavior based on actual failures.

4. **P3 – Default attribute selection (if required)**  
   Initialize `selectedAttrs` with first option per attribute when product loads.

5. **P4 – End-to-end checks**  
   Test: add to cart → place order → cart clears; out-of-stock → no add; category switching; cart persistence across refresh.

---

## QUICK REFERENCE: data-testid Map

```
Header:
  cart-btn
  category-link (inactive)
  active-category-link (active)

ProductCard (each card):
  product-{product-name-kebab}

ProductPage:
  product-gallery
  product-attribute-{attr-kebab}           [container]
  product-attribute-{attr}-{option}        [each option - ADD IF NEEDED]
  add-to-cart
  product-description

CartOverlay:
  cart-item-attribute-{attr-kebab}         [container]
  cart-item-attribute-{attr}-{option}-selected  [when selected]
  cart-item-amount-increase
  cart-item-amount
  cart-item-amount-decrease
  cart-total
```
