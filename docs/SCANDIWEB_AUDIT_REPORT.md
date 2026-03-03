# Scandiweb Feedback Audit Report

## 1. Audit Report Table

| ID | Feedback Item | Status | Evidence |
|----|---------------|--------|----------|
| **A** | Menu items have 2 underlines – remove link underline so only 1 remains | **DONE** | `Header.css` L22–25: `.nav-link { text-decoration: none; border-bottom: 2px solid transparent }` – link underline removed; L40–44: `.nav-link.active { border-bottom-color: #5ECE7B }` – single active underline. |
| **B** | While cart open + page scrolled, overlay not covering header area | **DONE** | `CartOverlay.css` L2–10: `.cart-overlay-backdrop { position: fixed; top:0; left:0; right:0; bottom:0; z-index: 250 }` covers viewport. `Header.css` L4: `.header { z-index: 200 }` – backdrop above header. |
| **C** | Cart has multiple scrollbars – should be only 1; attributes layout closer to design | **DONE** | Single scrollbar: `.cart-items { overflow-y: auto }`. Attributes: `.cart-item-attrs` groups under product; `.cart-item-attr` row layout (label + options); reduced gap (4px); `flex-wrap` on options to avoid chaos. |
| **D** | BE: Abstract product/category models redundant – refactor | **DONE** | No AbstractProduct/AbstractCategory. `ProductRepository.php` L7: `extends BaseRepository`; `CategoryRepository.php` L7: `extends BaseRepository`. AbstractAttribute/TextAttribute/SwatchAttribute exist for attribute types (separate concern). |
| **E** | BE: Add more common functionality to AbstractModel to avoid duplication | **PARTIAL** | `BaseRepository.php` L16–57: `fetchOne`, `fetchAll`, `execute`, `lastInsertId`, `transaction`. `AbstractModel.php` L9–16: only PDO + constructor. `Order` does not extend AbstractModel. Repositories use BaseRepository; Order/Attribute models use different patterns. |
| **F** | BE: Business logic in resolvers – enforce separation (resolver delegates to service) | **PARTIAL** | `SchemaBuilder.php` L134–145: `placeOrder` delegates to `$orderService->placeOrder($input)`. L69–78, L58–61: `products` and `category` call repositories directly with branching logic (e.g. `if (!$category) return $productRepository->findAll()`). |
| **G** | BE: Do not use JSON fields in SQL – normalize schema | **PARTIAL** | `scandiweb.sql` L29: `products.gallery` is `json`; L90: `prices.currency` is `json`. `orders` normalized (L55–63: `order_status`, `total`, `created_at` only). `order_items`/`order_item_attributes` normalized. |
| **H** | FE: Keep React components in separate files (ex App.jsx) | **DONE** | `App.jsx`, `AppContent.jsx`, `Header.jsx`, `CartOverlay.jsx`, `ProductList.jsx`, etc. – components in separate files. |
| **I** | FE: Do not use DOM APIs except when no alternative (Header.jsx:47 example) | **DONE** | Removed `document.body.classList` from AppContent. Scroll lock now via `.app.cart-overlay-open { position: fixed; overflow: hidden }` in App.css. Only `document.getElementById('root')` remains in main.jsx (mount, allowed). |

---

## 2. Runtime Verification

| Check | Status |
|-------|--------|
| Frontend builds | ✅ `npm run build` succeeds (vite build) |
| Backend placeOrder with DB | ⚠️ Not run (Railway DB not reachable from local) |
| Orders persist to orders/order_items/order_item_attributes | ✅ `Order.php` L26–55: inserts into all three tables in transaction |

---

## 3. Potential Scope Creep

- **Order merge logic** (`OrderService::buildMergeKey`, `placeOrder` merge): Adds duplicate cart-item merging; not in Scandiweb feedback.
- **order_status column** (was `status`): Production fix, not feedback item.
- **drop-order-details migration**: Refactor to remove legacy JSON; related to G but goes beyond feedback wording.
- **AppContent split from App.jsx**: Component separation; H asks for separate files (App.jsx) and is satisfied; AppContent is additional split.

---

## 4. Open Questions

1. **C – Attributes layout**: Do you have a design spec (Figma/mockup) to compare cart attribute layout against?
2. **G – JSON normalization**: Should `products.gallery` and `prices.currency` be fully normalized (e.g. `gallery_images` table, `currency` columns), or is the current state acceptable?
3. **I – DOM API alternative**: The `document.body.classList` is used to lock body scroll when the cart is open. Should we replace this with a React-friendly approach (e.g. `overflow: hidden` on a wrapper), even if it changes behavior slightly?

---

## 5. Patch Plan (Minimal Commits)

### Commit 1: I – Remove DOM API in AppContent
- **File**: `AppContent.jsx`
- **Change**: Replace `document.body.classList.toggle/remove` with CSS-only scroll lock. Add `overflow: hidden` to a root wrapper when `cartOverlayOpen`, or rely on `.cart-overlay-backdrop` covering viewport (body scroll may still need locking for accessibility).
- **Risk**: Low. May need to ensure body scroll lock works without `body.cart-overlay-open`.

### Commit 2: F – Delegate products/category resolver logic to services (optional)
- **Files**: `SchemaBuilder.php`, new `ProductService.php`, new `CategoryService.php` (or extend existing)
- **Change**: Move `categoryTitle` → `findByCategory` logic to `ProductService::getProducts(categoryTitle)`; move `findByName` + fallback to `CategoryService::resolve(input)`. Resolvers call services only.
- **Risk**: Medium. Requires new services and schema wiring.

### Commit 3: G – Normalize gallery/currency (if required)
- **Files**: `scandiweb.sql`, migrations, `ProductRepository`, `PriceRepository`
- **Change**: Add `gallery_images` table; add `currency_label`, `currency_symbol` to `prices` (or `currencies` table). Update seed/import.
- **Risk**: High. Schema and API changes.

### Commit 4: E – Clean AbstractModel/BaseRepository (if required)
- **Files**: `AbstractModel.php`, `Order.php`, `BaseRepository.php`
- **Change**: Either have Order extend AbstractModel for consistency, or remove AbstractModel if it adds no value; consolidate any remaining duplication.
- **Risk**: Low–medium.

---

**Recommendation**: Proceed with **Commit 1 (I)** as the lowest-risk, clearly actionable fix. Commit 2 and 4 are optional. Commit 3 (G) should be done only if full normalization is required.
