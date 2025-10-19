# 🚀 HƯỚNG DẪN CÀI ĐẶT & SỬ DỤNG KATA SEO MANAGER

## 📋 YÊU CẦU HỆ THỐNG

- **WordPress**: 5.0 trở lên
- **PHP**: 7.4 trở lên
- **MySQL**: 5.6 trở lên
- **RAM**: Tối thiểu 128MB
- **Disk Space**: 5MB

---

## 📦 BƯỚC 1: CÀI ĐẶT PLUGIN

### Cách 1: Qua WordPress Admin (Khuyến nghị)

1. Đăng nhập WordPress Admin
2. Vào **Plugins → Add New**
3. Click **Upload Plugin**
4. Chọn file `kata-seo-manager.zip`
5. Click **Install Now**
6. Click **Activate Plugin**

### Cách 2: Upload qua FTP/cPanel

```bash
# 1. Upload thư mục plugin
/wp-content/plugins/kata-seo-manager/

# 2. Kích hoạt trong WordPress Admin
Plugins → KATA SEO Manager → Activate
```

### Cách 3: Command Line

```bash
cd /path/to/wordpress/wp-content/plugins/
# Copy plugin folder here
chmod -R 755 kata-seo-manager/
# Activate via WordPress Admin
```

---

## ⚙️ BƯỚC 2: CẤU HÌNH BAN ĐẦU

### 2.1. Truy cập Settings

```
WordPress Admin → KATA SEO → Settings
```

### 2.2. Cấu hình Organization (Khuyến nghị)

```
Organization Details:
├─ Organization Name: Tên công ty/website của bạn
├─ Organization URL: https://yoursite.com
└─ Organization Logo: Upload logo (600x60px khuyến nghị)
```

**Tại sao quan trọng?**
- Google yêu cầu Publisher information cho Article schema
- Hiển thị logo trong Google Search Results
- Tăng độ tin cậy cho website

### 2.3. Bật Auto Schema Generation (Tùy chọn)

```
General Settings:
├─ ✅ Auto Schema Generation: Tự động tạo schema cho posts
├─ ✅ Schema Validation: Kiểm tra schema trước khi output
└─ ⬜ Strict Validation: Chỉ output schemas hợp lệ 100%
```

### 2.4. Performance Settings

```
Performance:
├─ ✅ Enable Caching: Bật cache (khuyến nghị)
└─ Cache Duration: 3600 seconds (1 hour)
```

---

## 🎯 BƯỚC 3: SỬ DỤNG CƠ BẢN

### 3.1. Thêm Schema cho Bài Viết

**Scenario: Thêm Article Schema**

```
1. Vào Posts → Edit bất kỳ bài viết nào
2. Cuộn xuống "Schema Markup" meta box
3. Click nút "Add Schema"
4. Chọn "Article" từ danh sách
5. Form tự động điền:
   - Headline: {post_title}
   - Author: {post_author}
   - Published Date: {post_published}
   - Image: {featured_image}
6. Click "Insert Schema"
7. Click "Update" để lưu bài viết
8. Xong! ✅
```

**Kết quả:**
- Schema JSON-LD được thêm vào `<head>`
- Google sẽ index trong 24-48 giờ
- Check tại: https://search.google.com/test/rich-results

### 3.2. Thêm FAQ Schema

**Scenario: Trang FAQ**

**Cách 1: Sử dụng Shortcode**

```html
[kata_faq]
[kata_faq_item question="Sản phẩm có bảo hành không?" answer="Có, bảo hành 12 tháng"]
[kata_faq_item question="Giao hàng mất bao lâu?" answer="2-3 ngày làm việc"]
[kata_faq_item question="Có hỗ trợ COD không?" answer="Có, thanh toán khi nhận hàng"]
[/kata_faq]
```

**Cách 2: Qua Meta Box**

```
1. Edit page
2. Schema Markup → Add Schema → FAQ
3. Click "Add Question"
4. Nhập câu hỏi và câu trả lời
5. Repeat cho tất cả FAQ
6. Insert Schema
```

### 3.3. Thêm Product Schema (WooCommerce/E-commerce)

**Scenario: Trang sản phẩm**

```
1. Edit product page
2. Schema Markup → Add Schema → Product
3. Điền thông tin:
   ├─ Name: {post_title}
   ├─ Description: {post_excerpt}
   ├─ Image: {featured_image}
   ├─ SKU: PROD-12345
   ├─ Price: 299.000
   ├─ Currency: VND
   └─ Availability: In Stock
4. Thêm Rating (optional):
   ├─ Rating Value: 4.5
   └─ Review Count: 24
5. Insert Schema
```

### 3.4. Thêm Recipe Schema

**Scenario: Blog nấu ăn**

```
1. Edit recipe post
2. Schema Markup → Add Schema → Recipe
3. Điền:
   ├─ Name: Bánh Chocolate
   ├─ Prep Time: 30 (phút)
   ├─ Cook Time: 60 (phút)
   ├─ Servings: 8 người
   ├─ Ingredients: (mỗi dòng 1 nguyên liệu)
   │   2 cups bột mì
   │   1 cup đường
   │   3 quả trứng
   └─ Instructions: (mỗi dòng 1 bước)
       Trộn bột với đường
       Thêm trứng vào trộn đều
       Nướng 180°C trong 60 phút
```

---

## 📊 BƯỚC 4: KIỂM TRA & VALIDATION

### 4.1. Xem Schema trong Source Code

```
1. Mở bài viết đã thêm schema
2. View Page Source (Ctrl+U / Cmd+U)
3. Tìm kiếm: "application/ld+json"
4. Bạn sẽ thấy JSON-LD schema
```

**Ví dụ output:**

```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "Tiêu đề bài viết",
  "author": {
    "@type": "Person",
    "name": "Tác giả"
  },
  "datePublished": "2025-10-04T10:00:00+00:00"
}
</script>
```

### 4.2. Test với Google Rich Results

```
1. Copy schema JSON-LD từ source code
2. Vào: https://search.google.com/test/rich-results
3. Paste vào "Code Snippet"
4. Click "Test Code"
5. Xem kết quả:
   ✅ Valid: Schema đúng
   ⚠️  Warnings: Có cảnh báo
   ❌ Errors: Có lỗi cần sửa
```

### 4.3. Validation trong Plugin

```
KATA SEO → Dashboard → Validation Report

Xem:
- Total Valid: Số schema hợp lệ
- Total Invalid: Số schema lỗi
- Recent Errors: Lỗi gần đây
```

**Sửa lỗi:**

```
1. Click vào schema có lỗi
2. Xem chi tiết lỗi
3. Edit và sửa trường thiếu/sai
4. Re-validate
```

---

## 📈 BƯỚC 5: XEM THỐNG KÊ

### 5.1. Dashboard Overview

```
KATA SEO → Dashboard

Xem:
┌─────────────────────────────┐
│ Total Schemas: 156          │
│ Active: 142                 │
│ Validation Pass: 92%        │
└─────────────────────────────┘
```

### 5.2. Schema Types Usage

```
KATA SEO → Schema Types

Xem usage của từng loại:
- Article: 45 instances
- FAQ: 32 instances
- Product: 28 instances
- Recipe: 21 instances
```

### 5.3. Performance Tracking (Nếu bật GSC)

```
KATA SEO → Statistics

Xem:
- Impressions: Số lần hiển thị
- Clicks: Số lần click
- CTR: Click-through rate
- Position: Vị trí trung bình
```

---

## 🛠️ BƯỚC 6: TÙNG CHỈNH NÂNG CAO

### 6.1. Tạo Template Riêng

```
KATA SEO → Templates → Add New

Ví dụ: Template Article cho Blog
{
    "headline": "{post_title}",
    "description": "{post_excerpt}",
    "author": "Admin",
    "publisher": {
        "name": "My Blog",
        "logo": "https://myblog.com/logo.png"
    }
}

Lưu template để dùng lại
```

### 6.2. Sử dụng Placeholders

**Available placeholders:**

```
{post_title}        - Tiêu đề bài viết
{post_excerpt}      - Trích đoạn
{post_content}      - Nội dung đầy đủ
{post_author}       - Tên tác giả
{post_url}          - URL bài viết
{post_published}    - Ngày publish
{post_modified}     - Ngày chỉnh sửa
{featured_image}    - Ảnh đại diện
{site_name}         - Tên website
{site_url}          - URL website
{site_description}  - Mô tả website
```

### 6.3. Bulk Actions

**Thêm schema cho nhiều posts:**

```php
// functions.php
add_action('save_post', 'auto_add_article_schema');
function auto_add_article_schema($post_id) {
    if (get_post_type($post_id) != 'post') return;
    
    $generator = new KATA_SEO_Schema_Generator();
    $schema = $generator->generate('Article', array(
        'post_id' => $post_id
    ));
    
    // Schema tự động được thêm
}
```

---

## 🐛 XỬ LÝ SỰ CỐ

### Issue 1: Schema không hiển thị

**Nguyên nhân:**
- Schema inactive
- Cache chưa clear
- Theme conflict

**Giải pháp:**

```
1. Check schema status: phải là "Active"
2. Clear cache:
   - WordPress cache
   - Browser cache
   - CDN cache
3. Deactivate other SEO plugins tạm thời
4. Check theme compatibility
```

### Issue 2: Validation errors

**Lỗi thường gặp:**

```
Error: "Missing required field: image"
→ Giải pháp: Thêm featured image cho post

Error: "Invalid date format"
→ Giải pháp: Dùng format ISO 8601 (YYYY-MM-DD)

Error: "Missing publisher"
→ Giải pháp: Cấu hình Organization trong Settings
```

### Issue 3: Performance slow

**Tối ưu:**

```
Settings → Performance:
✅ Enable Caching
✅ Cache Duration: 3600
✅ Optimize database
✅ Limit schemas per page: ≤ 3
```

---

## 📚 TUTORIALS CHO TỪNG USE CASE

### Use Case 1: Blog/News Website

```
Recommended schemas:
1. Article (tất cả posts)
2. Breadcrumb (navigation)
3. Organization (footer)
4. WebPage (pages)

Setup:
- Auto Article schema cho posts
- Manual Breadcrumb ở header
- Static Organization ở footer
```

### Use Case 2: E-commerce

```
Recommended schemas:
1. Product (product pages)
2. Breadcrumb (categories)
3. Organization (brand)
4. Review (customer reviews)

Setup:
- Integrate với WooCommerce
- Auto Product schema
- Import reviews → Review schema
```

### Use Case 3: Recipe/Food Blog

```
Recommended schemas:
1. Recipe (recipe posts)
2. Article (blog posts)
3. HowTo (cooking tutorials)
4. Video (cooking videos)

Setup:
- Template cho Recipe với ingredients
- Nutrition info (optional)
- Cooking time & servings
```

### Use Case 4: Local Business

```
Recommended schemas:
1. LocalBusiness (homepage)
2. Event (upcoming events)
3. FAQ (common questions)
4. Review (customer reviews)

Setup:
- Static LocalBusiness trên homepage
- Dynamic Events calendar
- FAQ page với FAQ schema
```

---

## 🎓 BEST PRACTICES

### 1. Schema Selection

```
✅ DO:
- Chỉ dùng schemas liên quan đến nội dung
- 1-3 schemas mỗi page
- Ưu tiên schemas có rich results

❌ DON'T:
- Spam nhiều schemas không liên quan
- Duplicate schemas
- Fake data trong schemas
```

### 2. Data Quality

```
✅ DO:
- Dùng real data
- Update thường xuyên
- Validate trước khi publish

❌ DON'T:
- Copy/paste từ website khác
- Hardcode old dates
- Ignore validation errors
```

### 3. Performance

```
✅ DO:
- Enable caching
- Optimize images
- Use CDN cho images

❌ DON'T:
- Add 10+ schemas per page
- Use large images (>2MB)
- Ignore warnings
```

---

## 📞 HỖ TRỢ

### Documentation

- **README.md** - Hướng dẫn tổng quan
- **DEVELOPMENT_GUIDE.md** - Developer guide
- **COMPLETION_SUMMARY.md** - Tổng kết dự án

### Online Resources

- Schema.org: https://schema.org
- Google Rich Results: https://search.google.com/test/rich-results
- WordPress Codex: https://codex.wordpress.org

### Contact

- Email: support@katachannel.com
- Website: https://katachannel.com

---

## ✅ CHECKLIST SAU KHI CÀI ĐẶT

```
□ Plugin đã activate
□ Settings đã cấu hình (Organization info)
□ Đã thêm schema cho ít nhất 1 post
□ Đã test với Google Rich Results Tool
□ Validation pass (ít nhất 80%)
□ Schema hiển thị trong page source
□ Dashboard có data
```

**Nếu tất cả ✅ → Bạn đã setup thành công!** 🎉

---

**Made with ❤️ by KATA Channel**  
**Version**: 1.0.0  
**Last Updated**: October 4, 2025
