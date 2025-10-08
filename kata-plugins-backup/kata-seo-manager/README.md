# KATA SEO Manager

🚀 **Plugin quản lý Schema Markup chuẩn Google cho WordPress**

Quản lý, cấu hình và thống kê 26 loại Schema Markup để tối ưu SEO cho website WordPress của bạn.

## 📋 Tính Năng Chính

### ✨ 26 Loại Schema Markup Được Hỗ Trợ

1. **Article** - Bài viết, tin tức, blog (NewsArticle, BlogPosting)
2. **Breadcrumb** - Đường dẫn điều hướng
3. **Carousel** - Băng chuyền nội dung
4. **Course** - Khóa học giáo dục
5. **Dataset** - Bộ dữ liệu lớn
6. **Forum** - Diễn đàn thảo luận
7. **EduQA** - Hỏi đáp giáo dục
8. **EmployerRating** - Đánh giá nhà tuyển dụng
9. **Event** - Sự kiện (Concert, Festival)
10. **FAQ** - Câu hỏi thường gặp
11. **HowTo** - Hướng dẫn từng bước
12. **ImageMetadata** - Siêu dữ liệu hình ảnh
13. **JobPosting** - Tin tuyển dụng
14. **LocalBusiness** - Doanh nghiệp địa phương (Restaurant, Store)
15. **MathSolver** - Giải toán
16. **Movie** - Phim ảnh
17. **Organization** - Tổ chức
18. **PracticeProblem** - Bài tập thực hành
19. **Product** - Sản phẩm e-commerce
20. **ProfilePage** - Trang hồ sơ
21. **Recipe** - Công thức nấu ăn
22. **Review** - Đánh giá
23. **Sitelinks** - Liên kết nội bộ
24. **Speakable** - Nội dung giọng nói
25. **Video** - Video
26. **WebPage** - Trang web

### 🎯 Tính Năng Nâng Cao

- **Dashboard Trực Quan**: Thống kê tổng quan về schemas
- **Editor Integration**: Button thêm schema ngay trong editor
- **Template System**: Thư viện templates có sẵn
- **Auto Validation**: Kiểm tra schema tự động
- **Statistics**: Theo dõi hiệu suất từ Google Search Console
- **Bulk Actions**: Thao tác hàng loạt
- **Import/Export**: Sao lưu và khôi phục
- **Placeholders**: Sử dụng {post_title}, {post_excerpt}, v.v.

## 📦 Cài Đặt

### Cách 1: Upload Plugin

1. Tải file ZIP của plugin
2. Vào **WordPress Admin → Plugins → Add New → Upload Plugin**
3. Chọn file ZIP và nhấn **Install Now**
4. Nhấn **Activate** để kích hoạt plugin

### Cách 2: FTP Upload

```bash
# Upload thư mục plugin vào:
/wp-content/plugins/kata-seo-manager/

# Kích hoạt plugin trong WordPress Admin
```

### Cách 3: Git Clone

```bash
cd /path/to/wordpress/wp-content/plugins/
git clone https://github.com/yourusername/kata-seo-manager.git
# Kích hoạt plugin trong WordPress Admin
```

## 🚀 Hướng Dẫn Sử Dụng Nhanh

### 1. Thêm Schema Vào Bài Viết

**Cách 1: Sử dụng Meta Box**

1. Chỉnh sửa bất kỳ post/page nào
2. Cuộn xuống **Schema Markup** meta box
3. Nhấn **Add Schema**
4. Chọn loại schema (Article, FAQ, Product, v.v.)
5. Điền thông tin hoặc chọn template
6. Nhấn **Insert Schema**

**Cách 2: Sử dụng Editor Button**

1. Trong editor, nhấn button **Insert Schema**
2. Modal popup hiện ra
3. Chọn loại schema và điền thông tin
4. Nhấn **Insert**

**Cách 3: Sử dụng Shortcode**

```php
// FAQ Schema
[kata_faq]
[kata_faq_item question="Câu hỏi 1?" answer="Câu trả lời 1"]
[kata_faq_item question="Câu hỏi 2?" answer="Câu trả lời 2"]
[/kata_faq]

// Recipe Schema
[kata_recipe name="Bánh Chocolate" prepTime="30" cookTime="60"]

// Product Schema
[kata_product name="Sản phẩm A" price="29.99" currency="USD"]
```

### 2. Sử Dụng Templates

Templates có sẵn với placeholders tự động:

- `{post_title}` - Tiêu đề bài viết
- `{post_excerpt}` - Trích đoạn
- `{post_content}` - Nội dung
- `{post_author}` - Tác giả
- `{post_url}` - URL bài viết
- `{featured_image}` - Ảnh đại diện
- `{site_name}` - Tên website
- `{site_url}` - URL website

### 3. Xem Thống Kê

1. Vào **KATA SEO → Dashboard**
2. Xem tổng quan: Total Schemas, Active Schemas, Validation Rate
3. Vào **Statistics** để xem chi tiết từng loại schema

### 4. Validate Schema

**Tự động:**
- Plugin tự động validate khi lưu schema

**Thủ công:**
- Trong meta box, nhấn **Validate All**
- Hoặc vào **Dashboard → Validation Report**

**Google Rich Results Test:**
- Copy JSON-LD schema
- Vào https://search.google.com/test/rich-results
- Paste và test

## ⚙️ Cấu Hình

Vào **KATA SEO → Settings** để cấu hình:

### General Settings

- **Auto Schema Generation**: Tự động tạo schema cho posts
- **Schema Validation**: Bật/tắt validation
- **Strict Validation**: Chỉ output schemas hợp lệ

### Organization Details

- **Organization Name**: Tên tổ chức (mặc định: tên site)
- **Organization URL**: URL tổ chức
- **Organization Logo**: Logo (dùng cho Publisher)

### Performance

- **Enable Caching**: Cache schemas đã generate
- **Cache Duration**: Thời gian cache (giây)

### Google Search Console Integration

- **GSC Enabled**: Bật tích hợp GSC
- **Site URL**: URL website trong GSC
- **Client ID/Secret**: API credentials

## 🎨 Ví Dụ Sử Dụng

### Example 1: Article Schema

```php
// Trong editor, chọn Article schema và điền:
{
    "headline": "{post_title}",
    "description": "{post_excerpt}",
    "author": "{post_author}",
    "datePublished": "{post_published}",
    "image": "{featured_image}",
    "publisher": {
        "name": "{site_name}",
        "logo": "https://example.com/logo.png"
    }
}
```

**Output trong `<head>`:**

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Tiêu đề bài viết",
  "description": "Mô tả ngắn...",
  "author": {
    "@type": "Person",
    "name": "Tên tác giả"
  },
  "datePublished": "2025-10-04T10:00:00+00:00",
  "image": "https://example.com/image.jpg",
  "publisher": {
    "@type": "Organization",
    "name": "Tên website",
    "logo": {
      "@type": "ImageObject",
      "url": "https://example.com/logo.png"
    }
  }
}
</script>
```

### Example 2: FAQ Schema

```php
[kata_faq]
[kata_faq_item 
    question="Schema Markup là gì?" 
    answer="Schema Markup là mã giúp Google hiểu nội dung tốt hơn"]
[kata_faq_item 
    question="Tại sao nên dùng FAQ schema?" 
    answer="FAQ schema giúp hiển thị rich snippets trên Google"]
[/kata_faq]
```

### Example 3: Product Schema

```php
// Sử dụng trong WooCommerce
add_action('woocommerce_after_single_product', 'add_product_schema');
function add_product_schema() {
    global $product;
    
    $schema_data = array(
        'name' => $product->get_name(),
        'description' => $product->get_short_description(),
        'image' => wp_get_attachment_url($product->get_image_id()),
        'sku' => $product->get_sku(),
        'brand' => 'Your Brand',
        'offers' => array(
            'price' => $product->get_price(),
            'priceCurrency' => 'USD',
            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
        )
    );
    
    // Schema sẽ tự động generate
}
```

### Example 4: Recipe Schema

```php
[kata_recipe 
    name="Bánh Chocolate" 
    prepTime="30" 
    cookTime="60" 
    servings="8"
    ingredients="2 cups flour|1 cup sugar|3 eggs"
    instructions="Trộn bột|Thêm trứng|Nướng 60 phút"]
```

## 📊 Database Schema

Plugin tạo 6 bảng trong database:

1. **wp_kata_seo_schemas** - Lưu schema instances
2. **wp_kata_seo_schema_stats** - Thống kê usage
3. **wp_kata_seo_schema_validation** - Kết quả validation
4. **wp_kata_seo_schema_templates** - Templates
5. **wp_kata_seo_schema_tracking** - GSC tracking
6. **wp_kata_seo_activity_log** - Activity logs

## 🔧 Developer Guide

### Hooks & Filters

```php
// Filter schema before output
add_filter('kata_seo_schema_output', function($schema, $type, $post_id) {
    // Modify schema
    return $schema;
}, 10, 3);

// Action after schema insert
add_action('kata_seo_schema_inserted', function($schema_id, $post_id, $type) {
    // Do something
}, 10, 3);

// Custom schema type
add_filter('kata_seo_schema_types', function($types) {
    $types['CustomType'] = 'Custom Schema';
    return $types;
});
```

### Programmatically Add Schema

```php
$generator = new KATA_SEO_Schema_Generator();
$result = $generator->generate('Article', array(
    'headline' => 'My Article',
    'author' => 'John Doe',
    'datePublished' => '2025-10-04',
    'image' => 'https://example.com/image.jpg'
));

if (!is_wp_error($result)) {
    // Save to post
    $schemas = get_post_meta($post_id, '_kata_seo_schemas', true) ?: array();
    $schemas[] = array(
        'type' => 'Article',
        'schema' => $result['schema'],
        'is_active' => true
    );
    update_post_meta($post_id, '_kata_seo_schemas', $schemas);
}
```

## 🐛 Troubleshooting

### Schema không hiển thị trong source code

1. Kiểm tra schema đã được kích hoạt (is_active = true)
2. Xóa cache (nếu dùng caching plugin)
3. Kiểm tra **Settings → Show in Source** đã bật

### Validation errors

1. Vào **Dashboard → Validation Report**
2. Xem chi tiết lỗi
3. Sửa các trường bị thiếu/sai
4. Re-validate

### Performance issues

1. Bật **Caching** trong Settings
2. Giảm số lượng schemas trên 1 page
3. Optimize images trong schemas

## 📝 Changelog

### Version 1.0.0 (2025-10-04)

- ✅ Initial release
- ✅ 26 schema types supported
- ✅ Dashboard & statistics
- ✅ Editor integration
- ✅ Template system
- ✅ Auto validation
- ✅ Database optimization

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

GPL v2 or later

## 👨‍💻 Author

**KATA Channel**
- Website: https://katachannel.com
- Email: support@katachannel.com

## 💬 Support

- Documentation: `/wp-content/plugins/kata-seo-manager/DEVELOPMENT_GUIDE.md`
- Issues: Submit via GitHub Issues
- Email: support@katachannel.com

---

**Made with ❤️ by KATA Channel**
