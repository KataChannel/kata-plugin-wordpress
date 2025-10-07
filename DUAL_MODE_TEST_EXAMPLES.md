# Dual-Mode Test Examples

## Test 1: MODE 1 - Schema Filtering Only

### Test 1A: Article - Minimal Schema
```
[kata_article 
    title="Test Schema Filtering"
    description="This is full description for testing"
    author="Test Author"
    tags="wordpress, test, schema"
    schema_fields="headline,author,datePublished"
]
```

**Expected:**
- ✅ JSON-LD: Chỉ có headline, author, datePublished
- ✅ HTML: Hiển thị đầy đủ (title, description, author, tags)

---

### Test 1B: Product - Show SKU/GTIN
```
[kata_product 
    name="Test Product"
    brand="Test Brand"
    price="1000000"
    sku="TEST-001"
    gtin="1234567890123"
    show_sku="true"
    show_gtin="true"
]
```

**Expected:**
- ✅ JSON-LD: Có name, brand, offers, sku, gtin
- ✅ HTML: Hiển thị đầy đủ

---

## Test 2: MODE 2 - Content Display Only

### Test 2A: Article - Hide Description in HTML
```
[kata_article 
    title="Test Content Display"
    description="This description will be hidden in HTML but present in schema"
    author="Test Author"
    hide_content_description="true"
    hide_content_meta="true"
]
```

**Expected:**
- ✅ JSON-LD: Đầy đủ (có description, author, date)
- ✅ HTML: Chỉ hiển thị title (không có description, meta)

---

### Test 2B: Recipe - Hide Nutrition in HTML
```
[kata_recipe 
    name="Test Recipe"
    nutrition_calories="300"
    nutrition_protein="15g"
    nutrition_fat="10g"
    hide_content_nutrition="true"
]
```

**Expected:**
- ✅ JSON-LD: Có nutrition object đầy đủ
- ✅ HTML: Không hiển thị phần nutrition

---

## Test 3: DUAL MODE - Combine Both

### Test 3A: Schema Minimal + HTML Full
```
[kata_article 
    title="Dual Mode Test 1"
    description="Full description"
    author="Author Name"
    tags="test, dual, mode"
    
    schema_fields="headline,author"
    (no hide_content_*)
]
```

**Expected:**
- ✅ JSON-LD: Chỉ headline, author
- ✅ HTML: Đầy đủ (title, description, author, tags)

---

### Test 3B: Schema Full + HTML Minimal
```
[kata_recipe 
    name="Dual Mode Recipe"
    description="Full description"
    ingredients="Ingredient 1|Ingredient 2"
    nutrition_calories="200"
    
    (no schema filters)
    hide_content_ingredients="true"
    hide_content_nutrition="true"
]
```

**Expected:**
- ✅ JSON-LD: Đầy đủ (name, description, ingredients, nutrition)
- ✅ HTML: Chỉ name và description

---

### Test 3C: Both Filtered (Advanced)
```
[kata_product 
    name="Advanced Product"
    brand="Brand Name"
    price="5000000"
    sku="ADV-001"
    gtin="9876543210987"
    description="Full product description"
    
    schema_fields="name,brand,sku,gtin,offers"
    hide_content_description="true"
    hide_content_details="true"
]
```

**Expected:**
- ✅ JSON-LD: name, brand, sku, gtin, offers (không có description)
- ✅ HTML: name, image, price (không có description, SKU details)

---

## Test 4: Priority Testing

### Test 4A: show_* overrides hide_*
```
[kata_article 
    description="Test priority"
    hide_description="true"
    show_description="true"
]
```

**Expected:**
- ✅ JSON-LD: Có description (show_description wins)

---

### Test 4B: show_content_* overrides hide_content_*
```
[kata_article 
    description="Test priority"
    hide_content_description="true"
    show_content_description="true"
]
```

**Expected:**
- ✅ HTML: Có description (show_content_description wins)

---

## Test 5: Edge Cases

### Test 5A: Empty schema_fields (show nothing except required)
```
[kata_article 
    title="Empty Schema Fields"
    description="Description"
    author="Author"
    schema_fields=""
]
```

**Expected:**
- ✅ JSON-LD: Chỉ @context, @type (required fields)

---

### Test 5B: hide_content_* all fields
```
[kata_article 
    title="Hide All Content"
    description="Description"
    hide_content_title="true"
    hide_content_description="true"
    hide_content_author="true"
    hide_content_meta="true"
    hide_content_image="true"
]
```

**Expected:**
- ✅ HTML: Empty article container (hoặc không hiển thị gì)

---

### Test 5C: show_schema="false" but HTML visible
```
[kata_article 
    title="No Schema but Show Content"
    show_schema="false"
    show_content="true"
]
```

**Expected:**
- ✅ JSON-LD: Không có script tag
- ✅ HTML: Hiển thị bình thường

---

## Validation Checklist

### For Each Test:

1. **View Page Source** (Ctrl+U)
   - [ ] Find `<script type="application/ld+json">`
   - [ ] Check JSON structure matches expected
   - [ ] Verify @context and @type always present

2. **Inspect HTML** (F12)
   - [ ] Check element visibility
   - [ ] Verify CSS classes
   - [ ] Test responsive display

3. **Google Rich Results Test**
   - [ ] Paste URL or HTML
   - [ ] Check for errors/warnings
   - [ ] Verify rich results preview

4. **Schema.org Validator**
   - [ ] Copy JSON-LD
   - [ ] Paste to validator
   - [ ] Check validation result

---

## Quick Validation Commands

### 1. Extract JSON-LD from page
```bash
curl http://localhost/timona/test-page/ | grep -A 50 "application/ld+json"
```

### 2. Check if element exists in HTML
```bash
curl http://localhost/timona/test-page/ | grep "kata-article-description"
```

### 3. Count schema scripts
```bash
curl http://localhost/timona/test-page/ | grep -c "application/ld+json"
```

---

## Expected Results Summary

| Test | Schema Output | HTML Output |
|------|---------------|-------------|
| 1A | headline, author, date | Full (all fields) |
| 1B | name, brand, sku, gtin, offers | Full (all fields) |
| 2A | Full (all fields) | title only |
| 2B | Full (with nutrition) | No nutrition section |
| 3A | headline, author | Full (all fields) |
| 3B | Full (all fields) | name, description only |
| 3C | name, brand, sku, gtin, offers | name, image, price |
| 4A | description visible | - |
| 4B | - | description visible |
| 5A | @context, @type only | Full (all fields) |
| 5B | Full (all fields) | Empty/minimal |
| 5C | No schema | Full (all fields) |

---

## Create WordPress Test Page

### Quick Setup:
1. Dashboard → Pages → Add New
2. Title: "Dual Mode Test"
3. Add test shortcodes from above
4. Publish
5. View page and validate

### Test Page Template:
```
<!-- Test 1A -->
[kata_article 
    title="Schema Filtering Test"
    description="Full description"
    author="Test Author"
    schema_fields="headline,author"
]

<hr>

<!-- Test 2A -->
[kata_article 
    title="Content Display Test"
    description="Hidden description"
    hide_content_description="true"
]

<hr>

<!-- Test 3B -->
[kata_recipe 
    name="Dual Mode Recipe"
    nutrition_calories="200"
    hide_content_nutrition="true"
]
```

---

## Debug Mode

Add to wp-config.php for debugging:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check logs:
```bash
tail -f /chikiet/webseo/timona/wp-content/debug.log
```

---

## Success Criteria

✅ **MODE 1 Working:**
- schema_fields filters JSON-LD correctly
- hide_* removes fields from schema
- show_* adds fields to schema
- Priority: show_* > hide_* > schema_fields

✅ **MODE 2 Working:**
- hide_content_* hides HTML elements
- show_content_* shows HTML elements
- Priority: show_content_* > hide_content_*
- Schema remains full

✅ **Both Modes Independent:**
- Can use MODE 1 alone
- Can use MODE 2 alone
- Can combine both modes
- No conflicts between modes
