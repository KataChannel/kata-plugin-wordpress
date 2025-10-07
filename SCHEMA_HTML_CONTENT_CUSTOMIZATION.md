# KATA Schema HTML Content Customization

## Yêu Cầu Mới

**Mục tiêu:** Tùy chỉnh những phần hiển thị trong **HTML content** của bài viết, trong khi **JSON-LD schema** vẫn giữ nguyên đầy đủ thông tin.

## Cách Hoạt Động

### Schema JSON-LD (Luôn đầy đủ)
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Bài viết của tôi",
  "description": "Mô tả đầy đủ",
  "author": {"@type": "Person", "name": "Tác giả"},
  "image": "...",
  "datePublished": "...",
  "keywords": "..."
}
</script>
```
☝️ **Schema này LUÔN chứa đầy đủ thông tin**, không bị ẩn.

### HTML Content (Có thể tùy chỉnh)
```html
<article class="kata-article-container">
  <h1>Bài viết của tôi</h1>
  <!-- description bị ẩn nếu hide_content_description="true" -->
  <!-- image bị ẩn nếu hide_content_image="true" -->
  <div class="kata-article-meta">
    <span>Tác giả: ...</span>
    <!-- author bị ẩn nếu hide_content_author="true" -->
  </div>
</article>
```

## Attributes Mới

### Article Shortcode

```
[kata_article 
    title="..."
    description="..."
    author="..."
    image="..."
    
    show_content="true"              // Có hiển thị HTML không?
    hide_content_title="true"        // Ẩn tiêu đề trong HTML
    hide_content_description="true"  // Ẩn mô tả trong HTML
    hide_content_author="true"       // Ẩn tác giả trong HTML
    hide_content_date="true"         // Ẩn ngày đăng trong HTML
    hide_content_image="true"        // Ẩn hình ảnh trong HTML
    hide_content_reading_time="true" // Ẩn thời gian đọc
]
```

### Recipe Shortcode

```
[kata_recipe 
    name="..."
    ingredients="..."
    instructions="..."
    nutrition_calories="..."
    
    show_content="true"
    hide_content_image="true"        // Ẩn hình ảnh món ăn
    hide_content_ingredients="true"  // Ẩn danh sách nguyên liệu
    hide_content_instructions="true" // Ẩn hướng dẫn
    hide_content_nutrition="true"    // Ẩn thông tin dinh dưỡng
    hide_content_rating="true"       // Ẩn đánh giá
]
```

### Product Shortcode

```
[kata_product 
    name="..."
    brand="..."
    price="..."
    sku="..."
    
    show_content="true"
    hide_content_image="true"        // Ẩn hình sản phẩm
    hide_content_price="true"        // Ẩn giá
    hide_content_brand="true"        // Ẩn thương hiệu
    hide_content_description="true"  // Ẩn mô tả
    hide_content_details="true"      // Ẩn chi tiết (SKU, model, etc.)
    hide_content_rating="true"       // Ẩn đánh giá
    hide_content_buy_button="true"   // Ẩn nút mua
]
```

## Use Cases

### 1. Hiển thị Schema nhưng ẩn HTML

Muốn Google đọc được schema nhưng không hiển thị content cho user:

```
[kata_article 
    title="Bài viết SEO"
    description="Full description for Google"
    author="John Doe"
    show_content="false"  // Không hiển thị HTML
    show_schema="true"    // Vẫn có schema
]
```

**Kết quả:**
- JSON-LD: ✅ Có đầy đủ
- HTML: ❌ Không hiển thị gì

### 2. Chỉ hiển thị tiêu đề và mô tả

```
[kata_article 
    title="Bài viết của tôi"
    description="Mô tả ngắn"
    author="Tác giả"
    image="..."
    hide_content_author="true"
    hide_content_image="true"
    hide_content_date="true"
]
```

**Kết quả:**
- JSON-LD: ✅ Có đầy đủ (title, description, author, image, date)
- HTML: Chỉ hiển thị title và description

### 3. Recipe không hiển thị nutrition

```
[kata_recipe 
    name="Phở bò"
    nutrition_calories="350"
    nutrition_protein="25g"
    hide_content_nutrition="true"
]
```

**Kết quả:**
- JSON-LD: ✅ Có nutrition object đầy đủ
- HTML: Không hiển thị phần nutrition info

### 4. Product chỉ hiển thị ảnh và tên

```
[kata_product 
    name="iPhone 15 Pro"
    brand="Apple"
    price="29990000"
    sku="..."
    hide_content_price="true"
    hide_content_brand="true"
    hide_content_details="true"
    hide_content_buy_button="true"
]
```

**Kết quả:**
- JSON-LD: ✅ Đầy đủ (name, brand, price, sku, offers)
- HTML: Chỉ hiển thị ảnh và tên sản phẩm

## Implementation Plan

### 1. Không cần Schema Customizer Class

Class `KATA_Schema_Customizer` đã tạo có thể **XÓA HOẶC GIỮ** nhưng không dùng cho mục đích này.

### 2. Update Render Functions

Mỗi render function cần check `hide_content_*` attributes trước khi output HTML:

```php
public function render_article($atts) {
    // ... parse attributes ...
    
    // Build FULL schema (không filter)
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $atts['title'],
        'description' => $atts['description'],
        'author' => array('@type' => 'Person', 'name' => $atts['author']),
        'image' => $atts['image'],
        // ... full schema ...
    );
    
    // Output schema (luôn đầy đủ)
    if ($atts['show_schema'] === 'true') {
        $this->store_shortcode_schema($schema, 'Article');
    }
    
    // Output HTML (có thể tùy chỉnh)
    $output = '';
    if ($atts['show_content'] === 'true') {
        $output .= '<article class="kata-article-container">';
        
        // Title (kiểm tra hide_content_title)
        if ($atts['hide_content_title'] !== 'true') {
            $output .= '<h1>' . esc_html($atts['title']) . '</h1>';
        }
        
        // Description (kiểm tra hide_content_description)
        if (!empty($atts['description']) && $atts['hide_content_description'] !== 'true') {
            $output .= '<p>' . esc_html($atts['description']) . '</p>';
        }
        
        // Author (kiểm tra hide_content_author)
        if (!empty($atts['author']) && $atts['hide_content_author'] !== 'true') {
            $output .= '<span>Tác giả: ' . esc_html($atts['author']) . '</span>';
        }
        
        // Image (kiểm tra hide_content_image)
        if (!empty($atts['image']) && $atts['hide_content_image'] !== 'true') {
            $output .= '<img src="' . esc_url($atts['image']) . '" />';
        }
        
        $output .= '</article>';
    }
    
    return $output;
}
```

## Attributes Summary

### Common Attributes (tất cả shortcodes):

- `show_content="true/false"` - Có hiển thị HTML không
- `show_schema="true/false"` - Có output JSON-LD không

### Article:

- `hide_content_title`
- `hide_content_description`
- `hide_content_author`
- `hide_content_date`
- `hide_content_image`
- `hide_content_reading_time`
- `hide_content_meta` (ẩn toàn bộ meta section)

### Recipe:

- `hide_content_title`
- `hide_content_description`
- `hide_content_image`
- `hide_content_author`
- `hide_content_time` (prep + cook time)
- `hide_content_ingredients`
- `hide_content_instructions`
- `hide_content_nutrition`
- `hide_content_rating`

### Product:

- `hide_content_title`
- `hide_content_image`
- `hide_content_description`
- `hide_content_brand`
- `hide_content_price`
- `hide_content_availability`
- `hide_content_details` (SKU, model, etc.)
- `hide_content_rating`
- `hide_content_buy_button`

### FAQ:

- FAQ không cần hide attributes vì nó đơn giản (chỉ questions/answers)
- Có thể thêm `hide_content_icons` để ẩn icon

## Benefits

1. ✅ **SEO tối ưu** - Schema luôn đầy đủ cho Google
2. ✅ **UI sạch đẹp** - Chỉ hiển thị thông tin cần thiết
3. ✅ **Linh hoạt** - Từng page có thể customize khác nhau
4. ✅ **Performance** - Giảm HTML size khi ẩn content không cần
5. ✅ **User experience** - Tránh information overload

## Example Real World

### Landing Page - Ẩn hầu hết content

```
[kata_product 
    name="Khóa học WordPress"
    price="1990000"
    description="Khóa học đầy đủ từ A-Z"
    hide_content_description="true"
    hide_content_price="true"
    hide_content_details="true"
]
```
→ Chỉ hiển thị tên và ảnh, nhưng schema có đầy đủ giá và mô tả

### Blog Post - Hiển thị đầy đủ

```
[kata_article 
    title="Hướng dẫn WordPress"
    description="..."
    author="..."
]
```
→ Hiển thị tất cả (default behavior)

### Recipe Page - Ẩn nutrition

```
[kata_recipe 
    name="Món ăn healthy"
    nutrition_calories="200"
    hide_content_nutrition="true"
]
```
→ User không thấy calories, nhưng Google Rich Results vẫn có

## Next Steps

1. ❌ Xóa logic filter schema trong Schema Customizer (hoặc giữ nhưng không dùng)
2. ✅ Cập nhật render functions với `hide_content_*` checks
3. ✅ Test HTML output với các combinations khác nhau
4. ✅ Update documentation với use cases mới

## Conclusion

Approach này **đơn giản hơn nhiều** vì:
- Không cần parse và filter schema
- Chỉ cần thêm conditional checks trong HTML rendering
- Schema luôn consistent và đầy đủ
- Dễ debug và maintain

**Schema = Data for Google (full)**  
**Content = Display for Users (customizable)**
