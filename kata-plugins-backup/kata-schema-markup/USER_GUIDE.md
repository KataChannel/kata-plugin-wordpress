# 📖 Hướng Dẫn Sử Dụng Chi Tiết - Kata Schema Markup Plugin

## 🚀 Giới thiệu

Kata Schema Markup là plugin WordPress chuyên nghiệp giúp bạn tự động tạo và quản lý Schema Markup (JSON-LD) để tối ưu hóa SEO và hiển thị Rich Snippets trên Google.

## 📋 Mục lục

1. [Cài đặt và Kích hoạt](#cài-đặt-và-kích-hoạt)
2. [Cấu hình ban đầu](#cấu-hình-ban-đầu)
3. [Dashboard và Analytics](#dashboard-và-analytics)
4. [Quản lý Schema cho Posts](#quản-lý-schema-cho-posts)
5. [Các loại Schema hỗ trợ](#các-loại-schema-hỗ-trợ)
6. [Shortcodes và Template Functions](#shortcodes-và-template-functions)
7. [Templates và Bulk Operations](#templates-và-bulk-operations)
8. [Validation và Testing](#validation-và-testing)
9. [Advanced Settings](#advanced-settings)
10. [Troubleshooting](#troubleshooting)

---

## 🛠 Cài đặt và Kích hoạt

### Cài đặt Plugin

1. **Upload plugin:**
   ```
   - Upload thư mục 'kata-schema-markup' vào /wp-content/plugins/
   - Hoặc cài đặt qua WordPress Admin > Plugins > Add New
   ```

2. **Kích hoạt plugin:**
   - Vào **Plugins** > **Installed Plugins**
   - Tìm **Kata Schema Markup** và click **Activate**

3. **Kiểm tra kích hoạt:**
   - Menu **Kata Schema** sẽ xuất hiện trong Admin sidebar
   - Database tables sẽ được tạo tự động

---

## ⚙️ Cấu hình ban đầu

### Truy cập Settings

Vào **Kata Schema** > **Settings** để cấu hình:

### 1. General Settings

```php
✅ Enable Schema Output: Bật/tắt output schema
✅ Auto Inject: Tự động chèn schema cho các post types
✅ Post Types: Chọn post types muốn áp dụng schema
✅ Default Schema Type: Loại schema mặc định
```

### 2. Organization Settings

```php
📊 Organization Name: Tên công ty/tổ chức
📧 Contact Email: Email liên hệ
📞 Phone Number: Số điện thoại
🌐 Website URL: URL website chính
📍 Address: Địa chỉ văn phòng
🏢 Business Type: Loại hình kinh doanh
```

### 3. Social Media Profiles

```php
📘 Facebook: URL Facebook Page
🐦 Twitter: URL Twitter Profile
📸 Instagram: URL Instagram Profile
💼 LinkedIn: URL LinkedIn Profile
📺 YouTube: URL YouTube Channel
```

### 4. Advanced Settings

```php
⚡ Enable Caching: Bật cache schema
🔄 Cache Duration: Thời gian cache (giây)
📦 Minify JSON: Nén output JSON
🛡️ Enable Validation: Bật validation tự động
📊 Analytics Tracking: Theo dõi hiệu suất schema
```

---

## 📊 Dashboard và Analytics

### Truy cập Dashboard

Vào **Kata Schema** > **Dashboard** để xem tổng quan:

### 1. Schema Overview

```
📈 Total Posts with Schema: Tổng số posts có schema
📊 Schema Types Distribution: Phân bố loại schema
✅ Validation Status: Tình trạng validation
📈 Coverage Percentage: Tỷ lệ coverage schema
```

### 2. Recent Activity

```
📝 Recently Updated: Posts được cập nhật schema gần đây
⚠️ Validation Issues: Posts có vấn đề validation
🆕 New Schema Posts: Posts mới có schema
```

### 3. Performance Metrics

```
⚡ Page Load Impact: Ảnh hưởng đến tốc độ load
📦 Cache Hit Rate: Tỷ lệ cache hit
🔍 Rich Snippets: Hiệu suất rich snippets
📊 Search Visibility: Khả năng hiển thị trong search
```

---

## 📝 Quản lý Schema cho Posts

### 1. Post Editor Metabox

Khi chỉnh sửa post/page, bạn sẽ thấy metabox **Schema Markup**:

#### Tab General
```php
🎯 Schema Type: Chọn loại schema (Article, Organization, etc.)
✅ Enable Schema: Bật/tắt schema cho post này
📊 Auto Generate: Tự động tạo schema từ post content
```

#### Tab Article Schema (cho posts)
```php
📰 Article Type: NewsArticle, BlogPosting, Article
📝 Headline: Tiêu đề bài viết (auto từ post title)
👤 Author: Tác giả (auto từ post author)
📅 Published Date: Ngày xuất bản (auto)
📅 Modified Date: Ngày cập nhật (auto)
🖼️ Featured Image: Ảnh đại diện (auto từ featured image)
📂 Article Section: Chuyên mục bài viết
📖 About: Chủ đề bài viết
🏷️ Keywords: Từ khóa chính
👥 Audience: Đối tượng target
📚 Citations: Nguồn tham khảo
```

#### Tab Custom Schema
```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Tiêu đề bài viết",
  "author": {
    "@type": "Person",
    "name": "Tên tác giả"
  },
  "datePublished": "2025-01-08",
  "image": "URL_ảnh_đại_diện"
}
```

#### Tab Preview
```
🔍 Schema Preview: Xem schema JSON sẽ được output
🧪 Google Test: Link đến Google Rich Results Test
📊 Validation Results: Kết quả validation real-time
```

### 2. Quick Edit Schema

Trong danh sách posts, sử dụng **Quick Edit**:

```php
⚡ Schema Type: Thay đổi nhanh loại schema
✅ Enable/Disable: Bật/tắt schema nhanh
```

### 3. Bulk Operations

Chọn nhiều posts và sử dụng **Bulk Actions**:

```php
✅ Enable Schema: Bật schema cho nhiều posts
❌ Disable Schema: Tắt schema cho nhiều posts
🔄 Regenerate Schema: Tạo lại schema tự động
📊 Validate Schema: Kiểm tra validation hàng loạt
```

---

## 🎯 Các loại Schema hỗ trợ

### 1. Article Schema

**Sử dụng cho:** Blog posts, tin tức, bài viết

```php
📰 Article Types:
  - Article (chung)
  - NewsArticle (tin tức)
  - BlogPosting (blog post)
  - TechArticle (bài viết kỹ thuật)

🔧 Tự động tạo từ:
  - Post title → headline
  - Post content → description
  - Post author → author
  - Featured image → image
  - Post date → datePublished
  - Categories → articleSection
  - Tags → keywords
```

### 2. Organization Schema

**Sử dụng cho:** Trang about, contact, trang chủ

```php
🏢 Organization Types:
  - Organization (tổ chức chung)
  - LocalBusiness (doanh nghiệp địa phương)
  - Corporation (công ty)
  - GovernmentOrganization (tổ chức chính phủ)

📊 Thông tin bao gồm:
  - Tên tổ chức
  - Logo và hình ảnh
  - Thông tin liên hệ
  - Địa chỉ
  - Social media profiles
  - Giờ hoạt động
```

### 3. Website Schema

**Sử dụng cho:** Trang chủ, landing pages

```php
🌐 Website Information:
  - Tên website
  - URL chính
  - Mô tả website
  - Search action
  - Navigation elements
  - Potential actions

🔧 Tự động detect:
  - Site name từ WordPress settings
  - Site URL từ home_url()
  - Site description từ tagline
```

### 4. Breadcrumb Schema

**Sử dụng cho:** Navigation breadcrumbs

```php
🧭 Breadcrumb Features:
  - Tự động tạo từ page hierarchy
  - Hỗ trợ custom post types
  - Category/taxonomy breadcrumbs
  - WooCommerce ready

📍 Breadcrumb Structure:
  Home → Category → Subcategory → Current Page
```

---

## 🔧 Shortcodes và Template Functions

### 1. Shortcodes

#### Schema Output Shortcode
```php
[kata_schema type="article" post_id="123"]
```

**Parameters:**
- `type`: article, organization, website, breadcrumb
- `post_id`: ID của post (optional, default current post)
- `format`: json, html, preview (default: json)

#### Breadcrumb Shortcode
```php
[kata_schema_breadcrumb]
[kata_schema_breadcrumb separator=" > " home_text="Trang chủ"]
```

**Parameters:**
- `separator`: Ký tự phân cách (default: " › ")
- `home_text`: Text cho link home (default: "Home")
- `show_current`: Hiển thị page hiện tại (default: true)

### 2. Template Functions

#### Output Schema in Theme
```php
<?php
// Output schema cho post hiện tại
kata_schema_output('article');

// Output schema cho post cụ thể
kata_schema_output('article', 123);

// Output organization schema
kata_schema_output('organization');
?>
```

#### Get Schema Data
```php
<?php
// Lấy schema data để xử lý
$schema = kata_get_schema_data('article', 123);

if ($schema) {
    echo '<script type="application/ld+json">';
    echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES);
    echo '</script>';
}
?>
```

#### Breadcrumb Function
```php
<?php
// Hiển thị breadcrumb với schema
if (function_exists('kata_breadcrumb')) {
    kata_breadcrumb(array(
        'separator' => ' → ',
        'home_text' => 'Trang chủ',
        'show_schema' => true
    ));
}
?>
```

### 3. Hooks và Filters

#### Modify Schema Data
```php
// Filter schema data trước khi output
function custom_modify_schema($schema, $type, $post_id) {
    if ($type === 'article') {
        $schema['custom_field'] = get_post_meta($post_id, 'custom_value', true);
    }
    return $schema;
}
add_filter('kata_schema_data', 'custom_modify_schema', 10, 3);
```

#### Custom Schema Types
```php
// Thêm schema type mới
function register_custom_schema() {
    kata_register_schema_type('product', array(
        'name' => 'Product Schema',
        'description' => 'Schema for products',
        'class' => 'CustomProductSchema'
    ));
}
add_action('kata_schema_init', 'register_custom_schema');
```

---

## 📋 Templates và Bulk Operations

### 1. Schema Templates

#### Tạo Template
1. Vào **Kata Schema** > **Templates**
2. Click **Add New Template**
3. Điền thông tin:
   ```php
   📝 Template Name: Tên template
   🎯 Schema Type: Loại schema
   📋 Conditions: Điều kiện áp dụng
   📄 Post Types: Post types áp dụng
   ⚙️ Status: Active/Inactive
   ```

#### JSON Template Editor
```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{post_title}}",
  "author": {
    "@type": "Person",
    "name": "{{author_name}}"
  },
  "datePublished": "{{post_date}}",
  "image": "{{featured_image}}"
}
```

**Available Variables:**
- `{{post_title}}` - Tiêu đề post
- `{{post_content}}` - Nội dung post
- `{{author_name}}` - Tên tác giả
- `{{post_date}}` - Ngày post
- `{{featured_image}}` - URL ảnh đại diện
- `{{site_name}}` - Tên website
- `{{site_url}}` - URL website

### 2. Import/Export

#### Export Templates
1. Vào **Templates** page
2. Click **Export**
3. Chọn format: JSON, CSV
4. Download file

#### Import Templates
1. Click **Import**
2. Upload JSON file
3. Map fields nếu cần
4. Import templates

### 3. Bulk Operations

#### Regenerate Schema
```php
// Tạo lại schema cho tất cả posts
wp_cli_command: wp kata-schema regenerate --post-type=post

// Tạo lại schema cho posts cụ thể
wp_cli_command: wp kata-schema regenerate --post-ids=1,2,3
```

---

## ✅ Validation và Testing

### 1. Built-in Validation

#### Real-time Validation
- Validation tự động khi save post
- Hiển thị errors và warnings
- Scoring system (0-100)
- Suggestions để cải thiện

#### Validation Rules
```php
✅ Required Fields Check
⚠️ Recommended Fields Warning
🔧 Format Validation
📊 Schema.org Compliance
🔍 Google Guidelines Check
```

### 2. External Testing

#### Google Rich Results Test
```php
// Automatic link generation
$test_url = "https://search.google.com/test/rich-results?url=" . urlencode(get_permalink());
```

#### Schema.org Validator
```php
// Manual testing với schema.org validator
$validator_url = "https://validator.schema.org/";
```

### 3. Validation Dashboard

#### Validation Overview
```
📊 Total Validation Score: Điểm trung bình
❌ Critical Errors: Lỗi cần sửa ngay
⚠️ Warnings: Cảnh báo minor
✅ Valid Schemas: Schemas hợp lệ
```

#### Detailed Reports
```php
📝 Error Details: Chi tiết lỗi cụ thể
🔧 Fix Suggestions: Gợi ý sửa lỗi
📈 Improvement Tips: Tips cải thiện điểm số
📊 Historical Data: Lịch sử validation
```

---

## 🔧 Advanced Settings

### 1. Performance Optimization

#### Caching Settings
```php
⚡ Enable Schema Cache: Bật cache schema
🕐 Cache Duration: 3600 seconds (1 hour)
🔄 Auto Refresh: Tự động refresh cache
📦 Cache Storage: Database/File/Redis
```

#### Output Optimization
```php
📦 Minify JSON: Nén JSON output
🚀 Lazy Load: Load schema khi cần
🎯 Conditional Loading: Load theo điều kiện
📊 Performance Monitoring: Theo dõi performance
```

### 2. Security Settings

#### Data Protection
```php
🛡️ Sanitize Input: Làm sạch input data
🔒 Nonce Verification: Xác thực nonce
👤 Capability Checks: Kiểm tra quyền user
🚫 SQL Injection Protection: Bảo vệ SQL injection
```

#### Access Control
```php
👥 Role-based Access: Phân quyền theo role
🔐 API Key Protection: Bảo vệ API keys
📊 Audit Logging: Ghi log các thao tác
🔍 Security Monitoring: Theo dõi bảo mật
```

### 3. Developer Settings

#### Debug Mode
```php
🐛 Enable Debug: Bật debug mode
📝 Debug Logging: Ghi log debug
🔍 Schema Inspection: Kiểm tra schema detail
📊 Performance Profiling: Profile performance
```

#### API Integration
```php
🔌 REST API: Enable REST API endpoints
🌐 External APIs: Tích hợp APIs bên ngoài
📊 Webhook Support: Hỗ trợ webhooks
🔄 Auto Sync: Đồng bộ tự động
```

---

## 🛠 Troubleshooting

### 1. Common Issues

#### Schema không hiển thị
```php
✅ Check List:
1. Plugin đã kích hoạt?
2. Schema enabled cho post?
3. Auto inject bật?
4. Theme có conflict?
5. Caching plugin conflict?

🔧 Solutions:
- Clear all caches
- Disable other SEO plugins
- Check theme compatibility
- Regenerate schema
```

#### Validation Errors
```php
❌ Common Errors:
1. Missing required fields
2. Invalid date format
3. Invalid URL format
4. Image size too small
5. Duplicate schema

🔧 Quick Fixes:
- Fill required fields
- Use correct date format (ISO 8601)
- Check URL validity
- Use images > 1200px width
- Remove duplicate schema sources
```

#### Performance Issues
```php
⚠️ Symptoms:
1. Slow page load
2. High memory usage
3. Database timeouts
4. Cache miss rate high

🔧 Optimizations:
- Enable caching
- Reduce cache duration
- Optimize database queries
- Use CDN for images
```

### 2. Debug Tools

#### Debug Mode
```php
// Bật debug mode
define('KATA_SCHEMA_DEBUG', true);

// View debug info
kata_schema_debug_info();

// Check schema output
kata_schema_debug_output($post_id);
```

#### Log Analysis
```php
// View error logs
kata_schema_get_error_logs();

// View performance logs
kata_schema_get_performance_logs();

// Clear logs
kata_schema_clear_logs();
```

### 3. Support và Documentation

#### Getting Help
```
📧 Support Email: support@timona.vn
🌐 Documentation: https://timona.vn/docs/kata-schema
🐛 Bug Reports: GitHub Issues
💬 Community Forum: WordPress.org Support
```

#### Additional Resources
```
📖 Schema.org Documentation
🔍 Google Rich Results Guide
📊 SEO Best Practices
🎥 Video Tutorials
📝 Case Studies
```

---

## 🎉 Kết luận

Kata Schema Markup Plugin cung cấp giải pháp hoàn chỉnh để quản lý Schema Markup trên WordPress. Với interface thân thiện, tính năng mạnh mẽ và hiệu suất tối ưu, plugin giúp bạn:

✅ **Tăng SEO Rankings** - Rich snippets cải thiện CTR  
✅ **Tiết kiệm thời gian** - Tự động hóa schema generation  
✅ **Đảm bảo chất lượng** - Validation tự động  
✅ **Mở rộng dễ dàng** - Hook system linh hoạt  
✅ **Performance tối ưu** - Caching và minification  

**Happy Schema Markup! 🚀**

---

*Phát triển bởi **Timona Team** | Version 1.0.0 | Updated: 08/01/2025*
