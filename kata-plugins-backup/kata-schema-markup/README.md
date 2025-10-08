# Kata Schema Markup Plugin

Plugin WordPress chuyên nghiệp để quản lý Schema Markup (Dữ liệu có cấu trúc) nhằm tối ưu hóa SEO.

## 🚀 Tính năng chính

### Core Features
- **Schema Generator**: Tự động tạo JSON-LD schema cho các loại nội dung
- **Schema Validator**: Xác thực dữ liệu có cấu trúc theo chuẩn Schema.org
- **Schema Templates**: Mẫu có sẵn cho các loại schema phổ biến
- **Auto Injection**: Tự động chèn schema vào head của trang
- **Manual Control**: Thêm schema thủ công qua metabox và shortcode

### Supported Schema Types
- **Article Schema**: Bài viết, tin tức, blog post
- **Breadcrumb Schema**: Đường dẫn điều hướng
- **Organization Schema**: Thông tin tổ chức, doanh nghiệp
- **Website Schema**: Thông tin website tổng quan

### Advanced Features
- **Rich Snippets**: Hiển thị rich snippets trong kết quả tìm kiếm
- **Google Validation**: Tích hợp với Google Rich Results Test
- **Performance Optimized**: Cache và tối ưu hóa hiệu suất
- **SEO Analytics**: Theo dõi hiệu quả SEO của schema markup

## 📋 Yêu cầu hệ thống

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+

## 🛠 Cài đặt

1. Upload thư mục `kata-schema-markup` vào `/wp-content/plugins/`
2. Kích hoạt plugin qua WordPress Admin
3. Truy cập **Kata Schema** trong menu admin để cấu hình

## 📖 Hướng dẫn sử dụng

### Cấu hình cơ bản

1. **Settings**: Vào **Kata Schema > Settings** để cấu hình chung
2. **Auto Injection**: Bật tự động chèn schema cho các loại post
3. **Templates**: Thiết lập templates mặc định cho từng loại schema

### Sử dụng Metabox

Khi chỉnh sửa bài viết, sử dụng metabox **Schema Markup**:

```
- Article Tab: Cấu hình schema bài viết
- Organization Tab: Thông tin tổ chức
- Breadcrumb Tab: Thiết lập breadcrumb
- Website Tab: Schema website
```

### Shortcodes

**Hiển thị schema JSON-LD:**
```php
[kata_schema type="article" post_id="123"]
```

**Hiển thị breadcrumb:**
```php
[kata_schema_breadcrumb]
```

### Template Functions

**Trong theme template:**
```php
// Hiển thị schema cho post hiện tại
kata_schema_output('article');

// Hiển thị organization schema
kata_schema_output('organization');

// Lấy schema data
$schema = kata_get_schema_data('article', get_the_ID());
```

## 🎛 Cấu hình nâng cao

### Auto Injection Settings
```php
// Tự động chèn cho post types
'auto_inject_post_types' => ['post', 'page', 'product']

// Tự động chèn organization schema
'auto_inject_organization' => true

// Tự động chèn website schema
'auto_inject_website' => true
```

### Performance Settings
```php
// Bật cache schema
'enable_cache' => true

// Thời gian cache (giây)
'cache_duration' => 3600

// Minify JSON output
'minify_json' => true
```

## 📊 Analytics & Monitoring

### Dashboard Analytics
- **Schema Coverage**: % trang có schema markup
- **Schema Types**: Phân tích loại schema được sử dụng
- **Validation Status**: Tình trạng xác thực schema
- **Performance Metrics**: Metrics hiệu suất schema

### Google Integration
- **Rich Results Test**: Link trực tiếp đến Google Rich Results Test
- **Search Console**: Theo dõi rich snippets trong Search Console
- **Validation Reports**: Báo cáo validation tự động

## 🔧 API & Hooks

### Actions
```php
// Sau khi schema được tạo
do_action('kata_schema_generated', $schema_data, $post_id);

// Trước khi output schema
do_action('kata_schema_before_output', $schema_type, $post_id);

// Sau khi output schema
do_action('kata_schema_after_output', $schema_type, $post_id);
```

### Filters
```php
// Modify schema data
apply_filters('kata_schema_data', $schema_data, $schema_type, $post_id);

// Modify auto injection
apply_filters('kata_schema_auto_inject', $enabled, $post_type, $post_id);

// Modify validation rules
apply_filters('kata_schema_validation_rules', $rules, $schema_type);
```

## 🎨 Customization

### Custom Schema Types
```php
// Đăng ký schema type mới
function register_custom_schema() {
    kata_register_schema_type('product', [
        'name' => 'Product Schema',
        'class' => 'CustomProductSchema',
        'template' => 'product-schema-template'
    ]);
}
add_action('kata_schema_init', 'register_custom_schema');
```

### Custom Templates
```php
// Tạo template schema tùy chỉnh
function custom_article_schema($post_id) {
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => get_the_title($post_id),
        'customField' => get_post_meta($post_id, 'custom_field', true)
    ];
}
add_filter('kata_schema_article_template', 'custom_article_schema');
```

## 🔒 Security

- **Data Sanitization**: Tất cả input được sanitize
- **Nonce Verification**: Xác thực nonce cho forms
- **Capability Checks**: Kiểm tra quyền user
- **SQL Injection Protection**: Prepared statements

## 📈 Performance

- **Lazy Loading**: Load schema khi cần thiết
- **Caching**: Cache schema data và output
- **Minification**: Minify JSON output
- **Database Optimization**: Query optimization

## 🐛 Troubleshooting

### Common Issues

**Schema không hiển thị:**
1. Kiểm tra auto injection settings
2. Xác thực schema validation
3. Check browser console errors

**Validation errors:**
1. Dùng Google Rich Results Test
2. Check required fields
3. Validate JSON syntax

**Performance issues:**
1. Bật caching
2. Optimize database
3. Check conflicting plugins

### Debug Mode
```php
// Bật debug mode
define('KATA_SCHEMA_DEBUG', true);

// View debug logs
kata_schema_get_debug_logs();
```

## 📞 Hỗ trợ

- **Documentation**: [Plugin Documentation](https://kata-seo.com/docs/schema-markup)
- **Support Forum**: [WordPress.org Support](https://wordpress.org/support/plugin/kata-schema-markup)
- **GitHub Issues**: [GitHub Repository](https://github.com/kata-seo/schema-markup)

## 📝 Changelog

### Version 1.0.0
- Initial release
- Core schema types support
- Auto injection functionality
- Admin dashboard and metaboxes
- Performance optimization
- Security implementations

## 📄 License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

---

**Developed by Kata SEO Team** | [kata-seo.com](https://kata-seo.com)
