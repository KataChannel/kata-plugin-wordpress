# Hướng Dẫn Sử Dụng KATA Schema Builder Dialog

**Ngày cập nhật:** 08/10/2025  
**Phiên bản:** KATA SEO Manager v2.0.3

## 🎯 Tổng Quan

Đã cập nhật hệ thống chèn shortcode trong TinyMCE Editor với **Dialog Tùy Chỉnh** cho phép người dùng:
- ✅ Điền thông tin cơ bản
- ✅ Chọn/bỏ chọn Schema Properties (MODE 1)
- ✅ Chọn/bỏ chọn Content Display (MODE 2)
- ✅ Preview và insert shortcode tối ưu

## 📁 Files Đã Tạo/Cập Nhật

### 1. **schema-attributes-config.js**
**Đường dẫn:** `assets/js/schema-attributes-config.js`

**Chức năng:**
- Cấu hình chi tiết thuộc tính cho 11 loại schema
- Định nghĩa các field cơ bản (baseAttrs)
- Danh sách schema properties (schemaFields)
- Danh sách content display fields (contentFields)

**Ví dụ cấu trúc:**
```javascript
article: {
    name: 'Article',
    icon: '📝',
    baseAttrs: {
        title: { label: 'Tiêu đề', required: true, default: '' },
        author: { label: 'Tác giả', required: true, default: '' },
        ...
    },
    schemaFields: ['headline', 'author', 'datePublished', ...],
    contentFields: ['title', 'author', 'category', ...]
}
```

### 2. **schema-builder-dialog.js**
**Đường dẫn:** `assets/js/schema-builder-dialog.js`

**Chức năng:**
- Tạo TinyMCE dialog với 3 tabs:
  - **Tab 1 - Thông Tin Cơ Bản:** Input fields cho các thuộc tính
  - **Tab 2 - Schema Properties:** Checkboxes cho JSON-LD schema
  - **Tab 3 - Content Display:** Checkboxes cho hiển thị frontend
- Xử lý logic build shortcode từ user input
- Hỗ trợ "Select All" / "Deselect All"

**API:**
```javascript
KataSchemaBuilder.openDialog(editor, schemaType)
```

### 3. **tinymce-plugin.js** (Đã cập nhật)
**Đường dẫn:** `assets/js/tinymce-plugin.js`

**Thay đổi:**
- Thêm nút "Chèn với Dialog Tùy Chỉnh" (Primary button)
- Giữ nút "Chèn Nhanh" cho quick insert với mẫu đầy đủ
- Call `KataSchemaBuilder.openDialog()` thay vì insert trực tiếp

### 4. **kata-seo-manager.php** (Đã cập nhật)
**Đường dẫn:** `kata-seo-manager.php`

**Thay đổi trong `enqueue_admin_scripts()`:**
```php
// Enqueue TinyMCE helper scripts cho post editor
if (in_array($hook, array('post.php', 'post-new.php'))) {
    wp_enqueue_script('kata-schema-attributes', ...);
    wp_enqueue_script('kata-schema-builder', ...);
}
```

## 🎨 Giao Diện Dialog

### **Tab 1: 📝 Thông Tin Cơ Bản**
```
┌──────────────────────────────────────┐
│ 📝 Article Schema                    │
│ Điền thông tin và chọn thuộc tính    │
├──────────────────────────────────────┤
│                                      │
│ Tiêu đề *: [___________________]    │
│ Tác giả *: [___________________]    │
│ Danh mục:  [___________________]    │
│ Tags:      [___________________]    │
│ Tóm tắt:   [___________________]    │
│ Thời gian đọc: [__] phút            │
│ Số từ:     [_____]                  │
│                                      │
└──────────────────────────────────────┘
```

### **Tab 2: 🔍 Schema Properties**
```
┌──────────────────────────────────────┐
│ Chọn các thuộc tính Schema để        │
│ output trong <head>                  │
│                                      │
│ [✅ Chọn tất cả] [❌ Bỏ chọn tất cả]│
├──────────────────────────────────────┤
│ ☑ headline                          │
│ ☑ author                            │
│ ☑ datePublished                     │
│ ☑ dateModified                      │
│ ☑ image                             │
│ ☑ publisher                         │
│                                      │
└──────────────────────────────────────┘
```

### **Tab 3: 🎨 Content Display**
```
┌──────────────────────────────────────┐
│ Chọn các phần tử hiển thị trên       │
│ frontend                             │
│                                      │
│ [✅ Chọn tất cả] [❌ Bỏ chọn tất cả]│
├──────────────────────────────────────┤
│ ☑ title                             │
│ ☑ author                            │
│ ☑ category                          │
│ ☑ tags                              │
│ ☑ excerpt                           │
│ ☑ reading_time                      │
│ ☑ word_count                        │
│                                      │
└──────────────────────────────────────┘
```

## 🔧 Workflow Sử Dụng

### **Cách 1: Dialog Tùy Chỉnh (Khuyến nghị)**

1. **Click nút KATA SEO Manager** trong TinyMCE toolbar
2. **Chọn Schema type** từ danh sách (vd: Article)
3. **Click "Chèn với Dialog Tùy Chỉnh"**
4. **Dialog mở ra với 3 tabs:**
   - **Tab 1:** Điền thông tin cơ bản
   - **Tab 2:** Chọn Schema properties cần show (mặc định: tất cả checked)
   - **Tab 3:** Chọn Content fields cần show (mặc định: tất cả checked)
5. **Tùy chỉnh:**
   - Bỏ chọn field nào đó → Tự động thêm `hide_*="true"`
   - Giữ checked → Thêm `show_*="true"`
6. **Click "Chèn Shortcode"**
7. **Kết quả:** Shortcode được insert với:
   - Thuộc tính cơ bản đã điền
   - `show_*="true"` cho fields được chọn
   - `hide_*="true"` cho fields bị bỏ chọn

### **Cách 2: Chèn Nhanh (Quick Insert)**

1. **Click nút KATA SEO Manager**
2. **Chọn Schema type**
3. **Click "Chèn Nhanh (Mẫu Đầy Đủ)"**
4. **Kết quả:** Shortcode mẫu với TẤT CẢ thuộc tính `show_*="true"` được insert ngay lập tức

## 📊 Ví Dụ Output

### **Input trong Dialog:**
```
Tab 1 - Thông Tin Cơ Bản:
- Tiêu đề: "5 Bí Quyết SEO 2025"
- Tác giả: "Nguyễn Văn A"
- Danh mục: "SEO"
- Tags: "seo, marketing"

Tab 2 - Schema Properties:
☑ headline
☑ author
☐ datePublished (BỎ CHỌN)
☑ dateModified
☑ image

Tab 3 - Content Display:
☑ title
☐ author (BỎ CHỌN)
☑ category
☑ tags
```

### **Output Shortcode:**
```
[kata_article 
    title="5 Bí Quyết SEO 2025" 
    author="Nguyễn Văn A" 
    category="SEO" 
    tags="seo, marketing" 
    show_schema="true" 
    show_frontend="true" 
    show_headline="true" 
    show_author="true" 
    hide_datePublished="true" 
    show_dateModified="true" 
    show_image="true" 
    show_content_title="true" 
    hide_content_author="true" 
    show_content_category="true" 
    show_content_tags="true"
]
```

## 🎯 Logic Xử Lý

### **Schema Properties (MODE 1)**
```javascript
// Nếu CHECKED:
show_headline="true"

// Nếu UNCHECKED:
hide_headline="true"
```

### **Content Display (MODE 2)**
```javascript
// Nếu CHECKED:
show_content_title="true"

// Nếu UNCHECKED:
hide_content_title="true"
```

## ✅ Lợi Ích

1. **UX Tốt Hơn**
   - Không cần nhớ tên thuộc tính
   - Visual interface với checkboxes
   - Validate required fields

2. **Linh Hoạt Tối Đa**
   - Chọn chính xác fields cần thiết
   - Tránh bloat code với quá nhiều thuộc tính không dùng
   - Fine-grained control

3. **Professional**
   - Dialog chuyên nghiệp với tabs
   - Select All / Deselect All buttons
   - Clear labeling

4. **Backward Compatible**
   - Vẫn giữ chức năng "Chèn Nhanh" cũ
   - Fallback nếu dialog chưa load

## 🧪 Testing Checklist

- [ ] Dialog mở đúng khi click "Chèn với Dialog Tùy Chỉnh"
- [ ] Tab 1: Các input fields hoạt động
- [ ] Tab 2: Checkboxes schema properties hoạt động
- [ ] Tab 3: Checkboxes content display hoạt động
- [ ] Select All / Deselect All buttons hoạt động
- [ ] Shortcode output đúng format
- [ ] Required fields validate
- [ ] Fallback "Chèn Nhanh" vẫn hoạt động
- [ ] Notification success hiển thị
- [ ] Dialog close sau khi insert

## 🐛 Troubleshooting

### **Dialog không mở**
```javascript
// Check console for errors
console.log(window.KataSchemaBuilder); // Should exist
console.log(KATA_SCHEMA_ATTRIBUTES);  // Should exist
```

**Giải pháp:**
- Clear browser cache
- Kiểm tra scripts đã enqueue trong `post.php`/`post-new.php`
- Check version query string `?v=...`

### **Checkboxes không hoạt động**
```javascript
// Check TinyMCE dialog API
tinymce.activeEditor.windowManager.getWindows()[0].getData()
```

**Giải pháp:**
- Update TinyMCE lên version 4.x trở lên
- Check browser console for errors

## 📝 Next Steps

1. **Thêm Preview Real-time**
   - Show preview shortcode output khi thay đổi checkboxes

2. **Save Templates**
   - Cho phép lưu cấu hình checkbox thành template

3. **Import/Export**
   - Import từ existing shortcode
   - Export config as JSON

4. **Validation**
   - Validate required fields trước khi insert
   - Show error messages

## 🎉 Kết Luận

Hệ thống Dialog Builder mới cung cấp **trải nghiệm tốt nhất** cho việc tạo shortcode với:
- ✅ Visual interface thân thiện
- ✅ Full control over attributes
- ✅ Professional workflow
- ✅ Backward compatible

Người dùng giờ có **2 tùy chọn**:
1. **Dialog Builder:** Tùy chỉnh chi tiết
2. **Quick Insert:** Chèn nhanh với mẫu đầy đủ

Cả 2 đều output shortcode với cú pháp `show_*` / `hide_*` chuẩn! 🚀
