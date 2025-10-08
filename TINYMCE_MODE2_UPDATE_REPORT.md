# Báo Cáo Cập Nhật MODE 2 cho TinyMCE Shortcode Templates

**Ngày cập nhật:** 08/10/2025  
**File được cập nhật:** `wp-content/plugins/kata-seo-manager/assets/js/tinymce-plugin.js`

## Tổng Quan

Đã cập nhật **11 shortcode templates** trong TinyMCE Editor với hệ thống **MODE 2 (Content Display Control)** đầy đủ, đồng nhất với MODE 1 (Schema Filtering) đã có sẵn.

## Danh Sách Shortcode Đã Cập Nhật

### 1. ✅ **Article** (`kata_article`)
**Thuộc tính MODE 2 đã thêm:**
- `show_schema="true"`
- `show_frontend="true"`
- `show_content_title="true"`
- `show_content_author="true"`
- `show_content_category="true"`
- `show_content_tags="true"`
- `show_content_excerpt="true"`
- `show_content_reading_time="true"`
- `show_content_word_count="true"`

**Ví dụ sử dụng:**
```
hide_content_author="true" show_content_excerpt="false"
```

---

### 2. ✅ **FAQ** (`kata_faq`)
**Lưu ý đặc biệt:**  
FAQ là nested shortcode (kata_faq + kata_faq_item), MODE 2 áp dụng cho kata_faq_item

**Ví dụ sử dụng:**
```
[kata_faq_item question="..." answer="..." hide_content_question="true"]
```

---

### 3. ✅ **HowTo** (`kata_howto`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_description="true"`
- `show_content_steps="true"`
- `show_content_tools="true"`
- `show_content_time="true"`
- `show_content_difficulty="true"`

**Ví dụ sử dụng:**
```
hide_content_tools="true" show_content_steps="true"
```

---

### 4. ✅ **Event** (`kata_event`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_description="true"`
- `show_content_date="true"`
- `show_content_location="true"`
- `show_content_organizer="true"`
- `show_content_price="true"`

**Ví dụ sử dụng:**
```
hide_content_price="true" show_content_location="true"
```

---

### 5. ✅ **Product** (`kata_product`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_description="true"`
- `show_content_price="true"`
- `show_content_brand="true"`
- `show_content_availability="true"`
- `show_content_rating="true"`

**Ví dụ sử dụng:**
```
hide_content_price="true" show_content_rating="true"
```

---

### 6. ✅ **Recipe** (`kata_recipe`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_description="true"`
- `show_content_ingredients="true"`
- `show_content_instructions="true"`
- `show_content_time="true"`
- `show_content_nutrition="true"`

**Ví dụ sử dụng:**
```
hide_content_ingredients="true" show_content_instructions="true"
```

---

### 7. ✅ **Course** (`kata_course`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_description="true"`
- `show_content_provider="true"`
- `show_content_instructor="true"`
- `show_content_price="true"`
- `show_content_duration="true"`
- `show_content_level="true"`
- `show_content_skills="true"`

**Ví dụ sử dụng:**
```
hide_content_instructor="true" show_content_skills="true"
```

---

### 8. ✅ **Book** (`kata_book`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_author="true"`
- `show_content_description="true"`
- `show_content_publisher="true"`
- `show_content_date="true"`
- `show_content_pages="true"`
- `show_content_genre="true"`
- `show_content_isbn="true"`

**Ví dụ sử dụng:**
```
hide_content_isbn="true" show_content_publisher="true"
```

---

### 9. ✅ **Local Business** (`kata_local_business`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_name="true"`
- `show_content_address="true"`
- `show_content_contact="true"`
- `show_content_hours="true"`
- `show_content_description="true"`
- `show_content_services="true"`
- `show_content_rating="true"`

**Ví dụ sử dụng:**
```
hide_content_hours="true" show_content_contact="true"
```

---

### 10. ✅ **Job Posting** (`kata_job_posting`)
**Thuộc tính MODE 2 đã thêm:**
- `show_content_title="true"`
- `show_content_company="true"`
- `show_content_location="true"`
- `show_content_description="true"`
- `show_content_salary="true"`
- `show_content_requirements="true"`
- `show_content_benefits="true"`

**Ví dụ sử dụng:**
```
hide_content_salary="true" show_content_requirements="true"
```

---

### 11. ✅ **Image Metadata** (`kata_image_metadata`)
**Thuộc tính MODE 2 đã thêm (12 thuộc tính):**
- `show_content_preview="true"`
- `show_content_name="true"`
- `show_content_description="true"`
- `show_content_technical="true"`
- `show_content_dimensions="true"`
- `show_content_size="true"`
- `show_content_format="true"`
- `show_content_camera="true"`
- `show_content_creator="true"`
- `show_content_date="true"`
- `show_content_location="true"`
- `show_content_keywords="true"`

**Ví dụ sử dụng:**
```
hide_content_camera="true" hide_keywords="true"
```

---

## Cấu Trúc Preview Box Chuẩn

Tất cả 11 shortcode đều có preview box với cấu trúc:

```html
<div class="kata-preview-box">
    <h4>🎯 [Schema Type] sẽ tạo:</h4>
    <div class="[type]-preview">
        <!-- Nội dung preview -->
    </div>
    <small>✅ [Schema benefit message]</small>
    <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
    <small style="color:#666;">
        <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
        <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
        <strong>Ví dụ:</strong> [specific example]
    </small>
</div>
```

## Lợi Ích

### 1. **Tính Đồng Nhất API**
- Tất cả shortcode giờ đây có cùng pattern `show_content_*` và `hide_content_*`
- Dễ học, dễ nhớ cho người dùng

### 2. **Tài Liệu Trực Quan**
- Người dùng thấy ngay hướng dẫn trong preview khi chèn shortcode
- Không cần tìm kiếm documentation bên ngoài

### 3. **Tăng Tính Linh Hoạt**
- Kiểm soát từng phần tử hiển thị độc lập
- Kết hợp MODE 1 (schema filtering) + MODE 2 (content display)

### 4. **Professional UX**
- Preview box có phân cách rõ ràng
- Màu sắc phân biệt (color:#666 cho phần hướng dẫn)
- Icon emoji giúp dễ nhận biết

## Testing Checklist

- [ ] Kiểm tra preview hiển thị đúng khi click button trong TinyMCE
- [ ] Verify shortcode mẫu có đầy đủ thuộc tính MODE 2
- [ ] Test insert shortcode vào editor
- [ ] Kiểm tra frontend rendering với các thuộc tính filter
- [ ] Validate schema output trong `<head>` section

## Files Liên Quan

1. **TinyMCE Plugin:** `wp-content/plugins/kata-seo-manager/assets/js/tinymce-plugin.js`
2. **Shortcode Handlers:** `wp-content/plugins/kata-seo-manager/kata-seo-manager.php`
3. **Schema Customizer:** `wp-content/plugins/kata-seo-manager/includes/class-kata-schema-customizer.php`

## Commit Message Suggested

```
feat(tinymce): Add MODE 2 content display controls to 11 shortcode templates

- Added show_content_* and hide_content_* attributes to shortcode examples
- Unified preview box structure with MODE 1/MODE 2 documentation
- Updated: article, faq, howto, event, product, recipe, course, book, 
  local_business, job_posting, image_metadata
- Improved UX with visual examples and clear instructions

Benefits:
✅ Consistent API across all shortcodes
✅ Better user experience with inline documentation
✅ Complete control over schema + content display
```

## Kết Luận

Cập nhật này hoàn thiện hệ thống dual-mode filtering cho KATA SEO Manager, giúp người dùng có **toàn quyền kiểm soát** cả việc:
- **MODE 1:** Schema nào được output (SEO-focused)
- **MODE 2:** Nội dung nào được hiển thị (UX-focused)

Tất cả thông qua một API nhất quán, dễ học và có tài liệu tích hợp ngay trong editor! 🚀
