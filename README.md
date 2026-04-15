# Ethnic RollOverImage
Magento 2 Hover Image Module for Hyvä Theme  
Product RollOver Image functionality for category, search, upsell, cross-sell, and homepage.

This module adds a hover (roll-over) image functionality for Magento 2 stores using the Hyvä theme.

When a customer hovers over a product image, a secondary image is displayed, improving product visibility and user experience.
A highly performant Magento 2 module that seamlessly changes the product image when a user hovers over it. Designed to work flawlessly across traditional Luma, Hyvä Themes, and Headless PWA deployments with true zero-config operation.

## Features
- **Auto Image Fetch**: Automatically use the **2nd Image** from the product's image gallery as the hover image. No manual role assignment needed!
- **Custom Image Option**: Automatically creates an administrative "Hover / RollOver Image" gallery role to deliberately define exactly what image triggers.
- **Hyvä Compatible**: Automatically detects Hyvä Themes and runs natively utilizing Alpine.js crossfade transitions avoiding any Knockout JS rendering latency.
- **Selective Rendering**: Admin configuration lets you determine exactly which site areas evaluate the hover effect (Category pages, Grid vs List, Search pages, Related Products, Upsells).
- **Animation Controls**: Customize fade transition opacity intervals seamlessly from 0.2 to 2.0 seconds utilizing CSS logic.
- **Fast & Lazy Load**: Native implementation of Alpine's `x-intersect` automatically guarantees hover images are *never* loaded until the user scrolls past them, defending top-tier Core Web Vitals.
- **Mobile Responsive**: Built-in Javascript hardware detection dynamically disables functionality on touch environments.

## GraphQL & PWA Studio Support
For modern Headless operations (PWA Studio, VueStorefront), backend logic is natively extended into the GraphQL schema! The PWA resolves URLs rapidly and cleanly bypassing typical logic extraction processes.

**GraphQL Example:**
```graphql
query GetCategoryProducts {
  products(
    filter: {
      category_id: { eq: "id" }
    }
    pageSize: 10
    currentPage: 1
    sort: { position: ASC }
  ) {
    total_count

    items {
      name
      sku
      ethnic_rollover_image
      small_image {
        url
        label
      }
    }
  }
  ethnicRolloverConfig {
    enabled
    animation
    animation_duration
    lazy_load
    image_role
    enabled_sections
  }
}
```

## Installation
✅ Via Composer (Recommended)
```

composer require niraligajera/module-rollover-image-all-places

```

### Luma & Hyvä Installation
1. Download the core module into `app/code/Ethnic/RollOverImage` or install via composer. 
2. Run Magento's CLI integration:
    ```bash
    bin/magento module:enable Ethnic_RollOverImage
    bin/magento setup:upgrade
    bin/magento setup:di:compile
    bin/magento cache:flush
    ```

## Usage Settings
1. Traverse to the Admin Panel: **Stores > Configuration > Ethnic > RollOver Image**.
2. Determine Global constraints via **Enable RollOver Image**.
3. also allow to set default product image position . Like if you want to set second image on hover you can set
  Also you can set ad image label Target an **Image Role**: 
   - `Use 2nd Gallery Image`: Engages Zero-Setup fallback mode.
   - `Hover / RollOver Image`: Targeted user uploads.
4. Dictate active sectors under **Enabled Sections**.
5. Save configuration and flush the block cache.

## Technical Requirements
- Magento Open Source / Commerce 2.4.X
- PHP 7.4 / 8.1 / 8.2 / 8.3 / 8.4 Compatible

## 🧠 Frontend Behavior
On hover → hover image is shown
On mouse leave → main image is restored
Fallback Logic

If no hover image is assigned:

Uses main image OR
Optionally can use second gallery image
📊 Bulk Upload (CSV)

You can import hover images using Magento import.

## Sample CSV Format:
  sku	hover_image
  ABC123	/h/o/hover1.jpg
  XYZ456	/h/o/hover2.jpg
  🎨 Styling

## Basic CSS example:

.product-image {
    position: relative;
}

.product-image .hover-img {
    position: absolute;
    top: 0;
    left: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-image:hover .hover-img {
    opacity: 1;
}

## ⚡ Hyvä Compatibility
Built using Hyvä best practices
No heavy JavaScript
Compatible with Alpine.js if needed


## ❓ FAQ. ###

1. What if no hover image is set?
Fallback will show the main product image (no break in UI).

2. Does it work on mobile?
Hover is not applicable on mobile. Can be extended to tap behavior if required.

## ⏱️ Estimated Performance Impact
Minimal – uses CSS-based hover, no extra API calls.

## 🧩 Future Improvements
Admin preview of hover image
Auto-pick second gallery image
Animation effects (zoom / slide)
PWA support
👨‍💻 Author

## Magento 2 Developer
Hyvä | Performance | Custom Modules

📄 License
OSL-3.0 / AFL-3.0