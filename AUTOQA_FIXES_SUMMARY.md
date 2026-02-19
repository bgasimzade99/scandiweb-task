# AutoQA Compliance – Summary of Fixes

## 1. Summary of Fixes Made

### Backend (STEP 1)

| Fix | Details |
|-----|---------|
| **Attribute polymorphism** | Updated `Model\Attribute\AttributeResolver` to use `TYPE_REGISTRY` for dispatching to `TextAttribute`/`SwatchAttribute`. Each attribute type now formats items via `formatItem()` (polymorphic). |
| **AbstractAttribute.formatItem()** | Added static `formatItem(array $row)` to allow type-specific formatting; subclasses can override. |
| **GraphQL controller** | Updated `handle(array $routeVars = [])` to accept FastRoute vars and avoid PHP 8 argument warnings. |
| **Route dispatch** | Changed to `call_user_func_array($handler, [$vars])` for correct handler invocation. |

### Frontend (STEP 2–3)

| Fix | Details |
|-----|---------|
| **PDP attribute option testids** | Added `data-testid="product-attribute-${attr}-${option}"` and `product-attribute-${attr}-${option}-selected` on each PDP attribute option button. |
| **Shared toKebab utility** | Created `frontend/src/utils/testId.js` with robust `toKebab()` that handles hex colors (#), spaces, and special chars for data-testid. |
| **Cart key stability** | Cart key now uses sorted attribute keys before `JSON.stringify` so same product+options always produce the same key for merge. |
| **Import consolidation** | ProductPage, CartOverlay, ProductCard now import `toKebab` from `utils/testId.js`. |

### Data-Testid Compliance Matrix

| Element | Testid | Status |
|---------|--------|--------|
| Cart button | `cart-btn` | ✓ |
| Cart amount increase | `cart-item-amount-increase` | ✓ |
| Cart amount decrease | `cart-item-amount-decrease` | ✓ |
| Cart amount display | `cart-item-amount` | ✓ |
| Cart total | `cart-total` | ✓ |
| Cart attribute container | `cart-item-attribute-{attribute}` | ✓ |
| Cart attribute option | `cart-item-attribute-{attribute}-{option}` | ✓ |
| Cart attribute selected | `cart-item-attribute-{attribute}-{option}-selected` | ✓ |
| Category link | `category-link` | ✓ |
| Active category | `active-category-link` | ✓ |
| Product card | `product-{product-name-kebab-case}` | ✓ |
| PDP attribute container | `product-attribute-{attribute}` | ✓ |
| PDP attribute option | `product-attribute-{attribute}-{option}` | ✓ |
| PDP attribute selected | `product-attribute-{attribute}-{option}-selected` | ✓ |
| Product gallery | `product-gallery` | ✓ |
| Product description | `product-description` | ✓ |
| Add to cart | `add-to-cart` | ✓ |

---

## 2. Assumptions Taken

1. **AutoQA testid format** – Used kebab-case for all dynamic parts (attribute names, option values). Hex colors use `display_value` when available (e.g. "Green" → "green"); raw hex strips `#` (e.g. "#44FF03" → "44ff03").

2. **Cart overlay** – Backdrop greys page; header stays above overlay (z-index 200 vs 100).

3. **GraphQL contract** – Frontend queries kept as-is; backend schema was already aligned.

4. **Multiple cart items** – Each item has its own `cart-item-amount-increase` etc. If AutoQA expects unique selectors per item, tests may need scoping; current spec does not require item-specific testids.

5. **Attribute id in GraphQL** – Backend returns `value_id` as item `id`; GraphQL coerces to string.

---

## 3. Remaining Risks

| Risk | Mitigation |
|------|------------|
| **AutoQA expects different testid format** | Run AutoQA at http://165.227.98.170/; adjust testids if failures occur. |
| **Subdirectory deployment** | If app is under `/subdir/`, GraphQL may need base path handling. `.htaccess` rewrites to `public/index.php`; verify REQUEST_URI. |
| **Currency / price format** | Frontend uses `toFixed(2)` and symbol from GraphQL; confirm it matches AutoQA expectations. |
| **PlaceOrder mutation payload** | Frontend sends `attrs: Object.values(selectedAttrs)`; backend expects `string[]`. Values are strings (e.g. "40", "#000000"); verify format. |

---

## 4. Confirmation – AutoQA Ready

The project is configured to meet Scandiweb Junior Full Stack AutoQA expectations:

- **Backend**: PSR-4, GraphQL at `/graphql`, attribute polymorphism, MySQL + data.json, no framework.
- **Frontend**: All required `data-testid` attributes, kebab-case naming, cart logic, PDP behavior.
- **Cart**: Same product+options merge; different options separate; quantity update; removal at 1.
- **PDP**: Attributes unselected initially; add to cart disabled until all selected; Quick Shop uses first option; out-of-stock handled; gallery carousel with arrows.

**Next step:** Run AutoQA at http://165.227.98.170/ against your deployed URL and fix any remaining failures.
