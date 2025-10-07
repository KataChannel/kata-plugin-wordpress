# KATA Schema Dual-Mode Customization Guide

## Tổng Quan

Plugin KATA SEO Manager hỗ trợ **2 MODES** tùy chỉnh độc lập:

### MODE 1: Schema Filtering 🔍
Tùy chỉnh **JSON-LD Schema Output** - Những trường nào xuất hiện trong `<script type="application/ld+json">`

### MODE 2: Content Display 👁️
Tùy chỉnh **HTML Content Display** - Những phần nào hiển thị cho người dùng trong bài viết

**Cả 2 modes hoạt động độc lập và có thể kết hợp!**

---

## MODE 1: Schema Filtering

### Mục Đích
Filter dữ liệu trong JSON-LD schema markup (cho Google/Search engines)

### Cú Pháp

#### 1. Sử dụng `schema_fields`
```
[kata_article schema_fields="headline,author,datePublished,-keywords"]
```

**Quy tắc:**
- Không có `-` = Hiển thị trong schema
- Có `-` = Ẩn khỏi schema
- `@context` và `@type` luôn có (không thể ẩn)

#### 2. Sử dụng `hide_*` / `show_*`
```
[kata_article hide_description="true" hide_keywords="true"]
[kata_article show_sku="true" show_gtin="true"]
```

**Thứ tự ưu tiên:**
```
show_* > hide_* > schema_fields > defaults
```

### Ví Dụ MODE 1

#### Article - Chỉ thông tin cơ bản trong schema
```
[kata_article 
    title="Bài viết SEO"
    description="Mô tả đầy đủ"
    author="John Doe"
    tags="wordpress, seo, tutorial"
    schema_fields="headline,author,datePublished"
]
```

**JSON-LD Output:**
```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Bài viết SEO",
  "author": {"@type": "Person", "name": "John Doe"},
  "datePublished": "2025-01-15T10:00:00+07:00"
}
```
☝️ Không có: description, keywords, image (bị filter)

#### Product - Hiển thị SKU/GTIN cho Google Shopping
```
[kata_product 
    name="iPhone 15 Pro"
    brand="Apple"
    sku="IP15P-256"
    gtin="0194253392552"
    show_sku="true"
    show_gtin="true"
]
```

**JSON-LD Output:**
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "iPhone 15 Pro",
  "brand": {"@type": "Brand", "name": "Apple"},
  "sku": "IP15P-256",
  "gtin": "0194253392552"
}
```

---

## MODE 2: Content Display

### Mục Đích
Tùy chỉnh HTML hiển thị trong bài viết (cho người dùng)

### Cú Pháp

#### Sử dụng `hide_content_*` / `show_content_*`
```
[kata_article hide_content_description="true" hide_content_image="true"]
[kata_recipe show_content_nutrition="true"]
```

**Thứ tự ưu tiên:**
```
show_content_* > hide_content_* > defaults
```

### Ví Dụ MODE 2

#### Article - Ẩn description và image trong HTML
```
[kata_article 
    title="Bài viết của tôi"
    description="Mô tả đầy đủ cho SEO"
    image="https://example.com/image.jpg"
    author="John Doe"
    hide_content_description="true"
    hide_content_image="true"
]
```

**JSON-LD Schema:** ✅ Đầy đủ (có description, image, author)  
**HTML Display:** Chỉ hiển thị title và author (không có description, image)

#### Recipe - Ẩn nutrition trong HTML nhưng giữ trong schema
```
[kata_recipe 
    name="Salad giảm cân"
    nutrition_calories="150"
    nutrition_protein="5g"
    nutrition_fat="3g"
    hide_content_nutrition="true"
]
```

**JSON-LD Schema:** ✅ Có nutrition object đầy đủ  
**HTML Display:** Không hiển thị phần "Thông Tin Dinh Dưỡng"

#### Product - Chỉ hiển thị ảnh và tên
```
[kata_product 
    name="Giày Nike"
    brand="Nike"
    price="2990000"
    sku="NK-AIR-001"
    hide_content_price="true"
    hide_content_brand="true"
    hide_content_details="true"
    hide_content_buy_button="true"
]
```

**JSON-LD Schema:** ✅ Đầy đủ (brand, price, sku, offers)  
**HTML Display:** Chỉ có ảnh và tên sản phẩm

---

## KẾT HỢP CẢ 2 MODES

### Use Case 1: Schema đơn giản + HTML đầy đủ

```
[kata_article 
    title="Hướng dẫn WordPress"
    description="Mô tả chi tiết về WordPress"
    author="Nguyễn Văn A"
    image="..."
    
    schema_fields="headline,author,datePublished"
    (không có hide_content_*)
]
```

**Kết quả:**
- **Schema:** Chỉ headline, author, datePublished
- **HTML:** Hiển thị đầy đủ title, description, author, image

**Tại sao?** Schema nhẹ cho Google, nhưng người dùng thấy đầy đủ thông tin.

---

### Use Case 2: Schema đầy đủ + HTML tối giản

```
[kata_recipe 
    name="Phở bò"
    description="Công thức truyền thống"
    ingredients="Bánh phở|Thịt bò|Hành tây"
    nutrition_calories="350"
    nutrition_protein="25g"
    
    (không có schema_fields)
    hide_content_ingredients="true"
    hide_content_nutrition="true"
]
```

**Kết quả:**
- **Schema:** Đầy đủ (name, description, ingredients, nutrition)
- **HTML:** Chỉ hiển thị tên và mô tả

**Tại sao?** Google Rich Results có đầy đủ data, nhưng trang web gọn gàng.

---

### Use Case 3: Cả 2 đều filter

```
[kata_product 
    name="iPhone 15 Pro"
    brand="Apple"
    price="29990000"
    sku="IP15P-256"
    gtin="0194253392552"
    description="Smartphone cao cấp"
    
    schema_fields="name,brand,sku,gtin,offers"
    hide_content_description="true"
    hide_content_details="true"
]
```

**Kết quả:**
- **Schema:** name, brand, sku, gtin, offers (cho Google Shopping)
- **HTML:** Chỉ tên, ảnh, giá (không có description, SKU, GTIN)

**Tại sao?** Google Shopping cần SKU/GTIN, nhưng người dùng thường không quan tâm.

---

## Danh Sách Attributes

### Article Shortcode

#### MODE 1 - Schema Filtering:
```
schema_fields=""
hide_description, hide_author, hide_datePublished, hide_dateModified,
hide_image, hide_wordCount, hide_articleSection, hide_keywords
show_description, show_author, show_datePublished, show_dateModified,
show_image, show_wordCount, show_articleSection, show_keywords
```

#### MODE 2 - Content Display:
```
hide_content_title, hide_content_description, hide_content_author,
hide_content_date, hide_content_image, hide_content_reading_time, hide_content_meta
show_content_title, show_content_description, show_content_author,
show_content_date, show_content_image, show_content_reading_time, show_content_meta
```

---

### Recipe Shortcode

#### MODE 1 - Schema Filtering:
```
schema_fields=""
hide_description, hide_image, hide_author, hide_prepTime, hide_cookTime,
hide_totalTime, hide_recipeYield, hide_recipeCategory, hide_recipeCuisine,
hide_recipeIngredient, hide_recipeInstructions, hide_nutrition, hide_aggregateRating
show_description, show_image, show_author, show_prepTime, show_cookTime,
show_totalTime, show_recipeYield, show_recipeCategory, show_recipeCuisine,
show_recipeIngredient, show_recipeInstructions, show_nutrition, show_aggregateRating
```

#### MODE 2 - Content Display:
```
hide_content_title, hide_content_description, hide_content_image,
hide_content_author, hide_content_time, hide_content_meta,
hide_content_ingredients, hide_content_instructions,
hide_content_nutrition, hide_content_rating
show_content_title, show_content_description, show_content_image,
show_content_author, show_content_time, show_content_meta,
show_content_ingredients, show_content_instructions,
show_content_nutrition, show_content_rating
```

---

### Product Shortcode

#### MODE 1 - Schema Filtering:
```
schema_fields=""
hide_description, hide_image, hide_brand, hide_model, hide_sku, hide_gtin,
hide_mpn, hide_category, hide_color, hide_size, hide_material, hide_weight,
hide_offers, hide_aggregateRating
show_description, show_image, show_brand, show_model, show_sku, show_gtin,
show_mpn, show_category, show_color, show_size, show_material, show_weight,
show_offers, show_aggregateRating
```

#### MODE 2 - Content Display:
```
hide_content_title, hide_content_image, hide_content_description,
hide_content_brand, hide_content_price, hide_content_availability,
hide_content_details, hide_content_rating, hide_content_buy_button
show_content_title, show_content_image, show_content_description,
show_content_brand, show_content_price, show_content_availability,
show_content_details, show_content_rating, show_content_buy_button
```

---

## So Sánh 2 Modes

| Tiêu chí | MODE 1: Schema Filtering | MODE 2: Content Display |
|----------|--------------------------|-------------------------|
| **Mục đích** | Filter JSON-LD schema | Hide/show HTML elements |
| **Ảnh hưởng** | Google/Search engines | Người dùng cuối |
| **Attributes** | `schema_fields`, `hide_*`, `show_*` | `hide_content_*`, `show_content_*` |
| **Output** | `<script type="application/ld+json">` | `<article>`, `<div>` HTML tags |
| **SEO Impact** | ✅ Trực tiếp | ❌ Gián tiếp (UX) |
| **Performance** | Nhỏ (JSON size) | Vừa (HTML size) |

---

## Best Practices

### 1. Schema đầy đủ, HTML tùy chỉnh (Recommended)

Để schema luôn đầy đủ cho SEO, chỉ tùy chỉnh HTML:

```
[kata_article 
    title="..."
    description="..."
    author="..."
    
    // Không dùng MODE 1
    // Chỉ dùng MODE 2
    hide_content_description="true"
]
```

### 2. Schema tối giản, HTML đầy đủ

Khi muốn giảm schema size:

```
[kata_product 
    name="..."
    sku="..."
    
    // Dùng MODE 1
    schema_fields="name,offers"
    // Không dùng MODE 2
]
```

### 3. Cả 2 đều customize

Cho control tối đa:

```
[kata_recipe 
    name="..."
    nutrition_calories="..."
    
    // MODE 1: Schema có nutrition
    show_nutrition="true"
    
    // MODE 2: HTML không hiển thị nutrition
    hide_content_nutrition="true"
]
```

### 4. Tránh conflict

**KHÔNG NÊN:**
```
[kata_article 
    hide_description="true"           // MODE 1: Ẩn khỏi schema
    show_content_description="true"    // MODE 2: Hiển thị trong HTML
]
```
→ HTML hiển thị description nhưng schema không có (không tốt cho SEO)

**NÊN:**
```
[kata_article 
    show_description="true"            // MODE 1: Có trong schema
    hide_content_description="true"    // MODE 2: Ẩn trong HTML
]
```
→ Schema đầy đủ, HTML gọn (tốt cho cả SEO và UX)

---

## Testing

### 1. Test Schema Output (MODE 1)

```bash
# View page source
Ctrl + U

# Find schema
<script type="application/ld+json">

# Validate
https://search.google.com/test/rich-results
https://validator.schema.org/
```

### 2. Test HTML Display (MODE 2)

```bash
# View rendered page
# Inspect element (F12)
# Check if elements are hidden
```

### 3. Test Both Modes

```
[kata_article 
    title="Test Both Modes"
    description="Full description"
    
    schema_fields="headline,author"
    hide_content_description="true"
]
```

**Expected:**
- Schema: Chỉ có headline và author
- HTML: Có title, không có description

---

## Troubleshooting

### Schema vẫn có field dù đã hide

**Kiểm tra:**
- Có dùng đúng attribute? `hide_description` (MODE 1) không phải `hide_content_description` (MODE 2)
- Có attribute `show_*` ghi đè không?
- Cache đã clear chưa?

### HTML vẫn hiển thị dù đã hide_content

**Kiểm tra:**
- Có dùng đúng attribute? `hide_content_image` (MODE 2) không phải `hide_image` (MODE 1)
- Có attribute `show_content_*` ghi đè không?
- Browser cache đã clear chưa?

### Conflict giữa 2 modes

**Giải pháp:**
- Ưu tiên schema đầy đủ (không filter MODE 1)
- Chỉ customize HTML (MODE 2)
- Luôn có data trong schema hơn HTML

---

## Changelog

### Version 1.1.0 (2025-01-15)

**Added:**
- ✅ Dual-mode customization system
- ✅ MODE 1: Schema filtering với KATA_Schema_Customizer
- ✅ MODE 2: Content display với `hide_content_*` / `show_content_*`
- ✅ Helper function `should_show_content()` trong render functions
- ✅ Support cho cả 2 modes trong article, recipe, product shortcodes

**Updated:**
- ✅ render_article() - Dual-mode support
- ✅ render_recipe() - Dual-mode support  
- ✅ render_product() - Dual-mode support

---

## Summary

**2 MODES độc lập:**

1. **MODE 1** = Filter schema (`hide_*`, `show_*`, `schema_fields`)
2. **MODE 2** = Hide HTML content (`hide_content_*`, `show_content_*`)

**Recommendation:**
- Schema = Luôn đầy đủ (tốt cho SEO)
- HTML = Tùy chỉnh theo nhu cầu (tốt cho UX)

**Example:**
```
[kata_article 
    title="Perfect Article"
    description="Full description for Google"
    
    // Schema: Full (no MODE 1 filters)
    
    // HTML: Minimal (MODE 2 hide)
    hide_content_description="true"
    hide_content_meta="true"
]
```

→ **Best of both worlds!** 🎉
