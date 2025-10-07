## Quick Test - KATA Schema Customization

### Test 1: Article - Minimal Schema
```
[kata_article 
    title="Test Article"
    author="John Doe"
    date_published="2025-01-15"
    schema_fields="headline,author,datePublished"
]
```

**Expected:** Chỉ có headline, author, datePublished trong schema

---

### Test 2: Recipe - With Nutrition
```
[kata_recipe 
    name="Healthy Salad"
    prep_time="10M"
    ingredients="Lettuce|Tomato|Cucumber"
    show_nutrition="true"
    nutrition_calories="120"
]
```

**Expected:** Schema có nutrition object

---

### Test 3: Product - Google Shopping
```
[kata_product 
    name="iPhone 15 Pro"
    brand="Apple"
    price="29990000"
    sku="IP15P-256"
    gtin="0194253392552"
    show_sku="true"
    show_gtin="true"
]
```

**Expected:** Schema có SKU và GTIN

---

### Test 4: FAQ - Simple
```
[kata_faq]
    [kata_faq_item question="What is KATA?" answer="It's a SEO plugin"]
    [kata_faq_item question="Is it free?" answer="Yes, completely free"]
[/kata_faq]
```

**Expected:** FAQPage với 2 questions

---

## How to Test:

1. Copy shortcode above
2. Create new WordPress post
3. Paste shortcode in editor
4. Publish and view post
5. View page source (Ctrl+U)
6. Search for `<script type="application/ld+json">`
7. Verify JSON output matches expected result

## Validation:

Copy JSON-LD from page source and paste into:
- https://search.google.com/test/rich-results
- https://validator.schema.org/

## Check @context and @type:

Every schema MUST have:
```json
{
  "@context": "https://schema.org",
  "@type": "Article|Recipe|Product|FAQPage"
}
```

These two fields CANNOT be hidden.
