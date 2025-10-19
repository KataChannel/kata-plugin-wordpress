# 🧪 HƯỚNG DẪN KIỂM TRA CHECKBOX UPDATE

## 📋 Mục đích
Kiểm tra xem checkbox có cập nhật shortcode đúng hay không sau khi refactoring code.

## 🔍 Các vấn đề đã sửa

### 1. Xóa `data-type` không cần thiết
**Trước:**
```html
<input type="checkbox" class="schema-field-faq" data-attr="question" data-type="schema" />
<input type="checkbox" class="content-field-faq" data-attr="answer" data-type="content" />
```

**Sau:**
```html
<input type="checkbox" class="schema-field-faq" data-attr="question" />
<input type="checkbox" class="content-field-faq" data-attr="answer" />
```

**Lý do:** Hàm `updateShortcode` đã dùng class riêng biệt (`.schema-field-${key}` và `.content-field-${key}`), không cần `data-type` nữa.

### 2. Cập nhật hàm updateShortcode
**Trước:**
```javascript
const checkboxes = document.querySelectorAll('.schema-attr-${key}');
checkboxes.forEach(cb => {
    const type = cb.dataset.type;
    if (type === 'schema') {
        // ...
    } else if (type === 'content') {
        // ...
    }
});
```

**Sau:**
```javascript
const schemaCheckboxes = document.querySelectorAll('.schema-field-${key}');
const contentCheckboxes = document.querySelectorAll('.content-field-${key}');

schemaCheckboxes.forEach(cb => {
    // Process schema only
});

contentCheckboxes.forEach(cb => {
    // Process content only
});
```

### 3. Thêm console.log để debug
Thêm nhiều console.log để theo dõi quá trình:
- Số lượng checkbox tìm thấy
- Trạng thái của từng checkbox
- Hidden attributes
- Shortcode cuối cùng

## 🎯 Cách kiểm tra

### Option 1: Kiểm tra trên WordPress Admin
1. Đăng nhập WordPress admin
2. Vào bài viết bất kỳ
3. Click nút "Add Schema" trên TinyMCE editor
4. Chọn một schema (ví dụ: FAQ)
5. Mở Console (F12) để xem log
6. Thử các thao tác sau:
   - ✅ Bỏ check một checkbox → Xem shortcode có thêm `hide_*="true"` không
   - ✅ Check lại checkbox → Xem attribute `hide_*` có biến mất không
   - ✅ Click nút "All" ở Schema Fields → Tất cả schema checkbox được check
   - ✅ Click nút "None" ở Schema Fields → Tất cả schema checkbox bỏ check
   - ✅ Click nút "All" ở Content Display → Tất cả content checkbox được check
   - ✅ Click nút "None" ở Content Display → Tất cả content checkbox bỏ check

### Option 2: Kiểm tra với file test HTML
1. Mở file: `wp-content/plugins/kata-seo-manager/test-checkbox-update.html`
2. Mở Console (F12)
3. Thử các thao tác tương tự như trên
4. Xem log chi tiết trong Console

## 📊 Kết quả mong đợi

### Test Case 1: Bỏ check schema checkbox "question"
```
Console Log:
🔄 Updating shortcode for FAQ...
📊 Schema checkboxes found: 4
📊 Content checkboxes found: 3
✓ Schema checkbox: question checked: false  ← Bỏ check
✓ Schema checkbox: answer checked: true
✓ Schema checkbox: name checked: true
✓ Schema checkbox: url checked: true
🔵 Hidden schema attrs: hide_question="true"
🟠 Hidden content attrs: (none)
📦 All hide attributes: hide_question="true"
✅ Final shortcode: [kata_faq id="1" hide_question="true"]
✅ Textarea updated!
```

### Test Case 2: Bỏ check content checkbox "answer"
```
Console Log:
🔄 Updating shortcode for FAQ...
✓ Content checkbox: question checked: true
✓ Content checkbox: answer checked: false  ← Bỏ check
✓ Content checkbox: name checked: true
🔵 Hidden schema attrs: (none)
🟠 Hidden content attrs: hide_content_answer="true"
📦 All hide attributes: hide_content_answer="true"
✅ Final shortcode: [kata_faq id="1" hide_content_answer="true"]
✅ Textarea updated!
```

### Test Case 3: Bỏ check cả schema và content
```
Console Log:
🔄 Updating shortcode for FAQ...
✓ Schema checkbox: question checked: false
✓ Schema checkbox: answer checked: false
✓ Content checkbox: question checked: false
🔵 Hidden schema attrs: hide_question="true" hide_answer="true"
🟠 Hidden content attrs: hide_content_question="true"
📦 All hide attributes: hide_question="true" hide_answer="true" hide_content_question="true"
✅ Final shortcode: [kata_faq id="1" hide_question="true" hide_answer="true" hide_content_question="true"]
✅ Textarea updated!
```

### Test Case 4: Click nút "None" ở Schema Fields
```
Console Log:
🔄 Updating shortcode for FAQ...
📊 Schema checkboxes found: 4
✓ Schema checkbox: question checked: false
✓ Schema checkbox: answer checked: false
✓ Schema checkbox: name checked: false
✓ Schema checkbox: url checked: false
🔵 Hidden schema attrs: hide_question="true" hide_answer="true" hide_name="true" hide_url="true"
✅ Final shortcode: [kata_faq id="1" hide_question="true" hide_answer="true" hide_name="true" hide_url="true"]
```

## ❌ Lỗi thường gặp

### Lỗi 1: Checkbox không tìm thấy
```
📊 Schema checkboxes found: 0
📊 Content checkboxes found: 0
```
**Nguyên nhân:** Class name không đúng hoặc template chưa render
**Giải pháp:** Kiểm tra class name trong HTML và querySelector

### Lỗi 2: Textarea không cập nhật
```
❌ Textarea not found!
```
**Nguyên nhân:** ID của textarea không đúng
**Giải pháp:** Kiểm tra `id="shortcode-textarea-${key}"`

### Lỗi 3: data-attr undefined
```
✓ Schema checkbox: undefined checked: true
```
**Nguyên nhân:** Thiếu attribute `data-attr` trong HTML
**Giải pháp:** Thêm `data-attr="${attr}"` vào input checkbox

## ✅ Checklist hoàn thành

- [x] Xóa `data-type` khỏi checkbox
- [x] Cập nhật hàm `updateShortcode` để dùng class riêng
- [x] Thêm console.log để debug
- [x] Tạo file test HTML
- [x] Viết tài liệu hướng dẫn kiểm tra
- [ ] Test trên WordPress admin (cần người dùng thực hiện)
- [ ] Xác nhận checkbox hoạt động đúng
- [ ] Xác nhận nút All/None hoạt động đúng
- [ ] Xác nhận shortcode được cập nhật đúng

## 📝 Ghi chú

- File test: `wp-content/plugins/kata-seo-manager/test-checkbox-update.html`
- File chính: `wp-content/plugins/kata-seo-manager/assets/js/tinymce-plugin.js`
- Console log chỉ hiển thị khi debug, có thể xóa sau khi test xong

## 🚀 Bước tiếp theo

1. **Test trên WordPress:** Mở WordPress admin và kiểm tra
2. **Kiểm tra console:** Xem log có đúng không
3. **Test các trường hợp:** Checkbox, All/None buttons
4. **Báo cáo kết quả:** Nếu có lỗi, cung cấp log console
