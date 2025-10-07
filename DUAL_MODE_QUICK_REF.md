# KATA Schema Dual-Mode - Quick Reference

## 2 MODES

### MODE 1: Schema Filtering 🔍
**Target:** JSON-LD schema output  
**For:** Google/Search engines  
**Attributes:** `schema_fields`, `hide_*`, `show_*`

### MODE 2: Content Display 👁️
**Target:** HTML content display  
**For:** End users  
**Attributes:** `hide_content_*`, `show_content_*`

---

## Quick Syntax

### MODE 1 Examples

```
// Minimal schema
[kata_article schema_fields="headline,author"]

// Hide fields
[kata_article hide_description="true" hide_keywords="true"]

// Show optional fields
[kata_product show_sku="true" show_gtin="true"]
```

### MODE 2 Examples

```
// Hide HTML elements
[kata_article hide_content_description="true"]

// Hide nutrition display
[kata_recipe hide_content_nutrition="true"]

// Hide product details
[kata_product hide_content_details="true"]
```

### Combined

```
[kata_recipe 
    name="Phở bò"
    nutrition_calories="350"
    
    show_nutrition="true"              // MODE 1: Schema có nutrition
    hide_content_nutrition="true"      // MODE 2: HTML ẩn nutrition
]
```

---

## Common Attributes

### Article
```
MODE 1: hide_description, hide_author, hide_keywords
MODE 2: hide_content_description, hide_content_meta, hide_content_image
```

### Recipe
```
MODE 1: hide_nutrition, hide_recipeCategory, hide_recipeCuisine
MODE 2: hide_content_nutrition, hide_content_ingredients, hide_content_instructions
```

### Product
```
MODE 1: show_sku, show_gtin, show_mpn
MODE 2: hide_content_details, hide_content_buy_button, hide_content_price
```

### FAQ
```
MODE 1: hide_mainEntity
MODE 2: (không cần)
```

---

## Priority

### MODE 1
```
show_* > hide_* > schema_fields > defaults
```

### MODE 2
```
show_content_* > hide_content_* > defaults
```

---

## Best Practices

### ✅ RECOMMENDED
```
// Schema full, HTML minimal
[kata_article 
    description="Full description"
    hide_content_description="true"
]
```
→ SEO ✅ / UX ✅

### ⚠️ USE CAREFULLY
```
// Schema minimal, HTML full
[kata_article 
    description="Description"
    schema_fields="headline,author"
]
```
→ Có thể mất rich results

### ❌ AVOID
```
// Schema ẩn, HTML hiển thị
[kata_article 
    description="Description"
    hide_description="true"
    show_content_description="true"
]
```
→ Conflict: HTML có data nhưng schema không có

---

## Testing

### 1. View Source (Ctrl+U)
```
Find: <script type="application/ld+json">
Check: Schema fields
```

### 2. Inspect Element (F12)
```
Check: HTML element visibility
```

### 3. Google Rich Results
```
URL: https://search.google.com/test/rich-results
```

---

## Common Fields

### Article
- headline, description, author
- datePublished, dateModified
- image, wordCount
- articleSection, keywords

### Recipe
- name, description, image, author
- prepTime, cookTime, totalTime
- recipeYield, recipeIngredient, recipeInstructions
- nutrition, aggregateRating

### Product
- name, description, image, brand
- sku, gtin, mpn, model
- offers, category, color, size
- aggregateRating

---

## Files

📖 **Full Guide:** DUAL_MODE_CUSTOMIZATION_GUIDE.md  
🧪 **Test Examples:** DUAL_MODE_TEST_EXAMPLES.md  
📊 **Summary:** DUAL_MODE_IMPLEMENTATION_SUMMARY.md

---

## Support

**Documentation:** /chikiet/webseo/timona/DUAL_MODE_*.md  
**Plugin:** kata-seo-manager v1.1.0  
**Status:** ✅ Implemented (4/15 shortcodes)
