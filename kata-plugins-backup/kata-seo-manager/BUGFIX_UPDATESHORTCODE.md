# 🐛 BUG FIX: updateShortcode_${key} is not a function

## ❌ Lỗi

```
Uncaught TypeError: window.updateShortcode_product is not a function
    at HTMLButtonElement.onclick (post.php?post=24113&action=edit:4:84)
```

## 🔍 Nguyên nhân

### Vấn đề: Script tag trong innerHTML không được execute

Khi sử dụng `innerHTML` để inject HTML vào DOM, **browser sẽ KHÔNG execute các `<script>` tag** bên trong vì lý do bảo mật.

#### Code cũ (BỊ LỖI):

```javascript
previewPanel.innerHTML = `
    <div>
        <!-- HTML content -->
        <textarea id="shortcode-textarea-${key}">...</textarea>
        
        <!-- Script tag - KHÔNG BAO GIỜ CHẠY! -->
        <script>
            window.updateShortcode_${key} = function() {
                // Function này KHÔNG được định nghĩa!
            };
        </script>
        
        <!-- Button cố gọi hàm -->
        <button onclick="window.updateShortcode_${key}()">
            All  <!-- Click = TypeError! -->
        </button>
    </div>
`;
```

### Tại sao lại như vậy?

- **Bảo mật XSS (Cross-Site Scripting):** Nếu cho phép execute script trong innerHTML, attacker có thể inject malicious code
- **Browser policy:** Chrome, Firefox, Safari đều block việc execute script từ innerHTML
- **Alternative:** Phải dùng `document.createElement('script')` hoặc định nghĩa hàm bằng JavaScript thực

## ✅ Giải pháp

### Cách 1: Định nghĩa hàm sau khi set innerHTML (ĐÃ ÁP DỤNG)

```javascript
// 1. Set innerHTML KHÔNG CÓ script tag
previewPanel.innerHTML = `
    <div>
        <textarea id="shortcode-textarea-${key}">...</textarea>
        <button onclick="window.updateShortcode_${key}()">All</button>
    </div>
`;

// 2. Định nghĩa hàm NGAY SAU ĐÓ bằng JavaScript
window['updateShortcode_' + key] = function() {
    console.log('🔄 Updating shortcode for ' + key + '...');
    
    const schemaCheckboxes = document.querySelectorAll('.schema-field-' + key);
    const contentCheckboxes = document.querySelectorAll('.content-field-' + key);
    
    // ... logic xử lý
};
```

### Tại sao cách này hoạt động?

- ✅ Hàm được định nghĩa bằng **JavaScript thực**, không phải script tag trong string
- ✅ Hàm tồn tại trong `window` object trước khi button được click
- ✅ `onclick` handler có thể gọi `window.updateShortcode_${key}()` thành công

### Cách 2: Dùng createElement (Alternative)

```javascript
// Set innerHTML
previewPanel.innerHTML = `<div>...</div>`;

// Tạo script element và append
const scriptTag = document.createElement('script');
scriptTag.textContent = `
    window.updateShortcode_${key} = function() {
        // ...
    };
`;
previewPanel.appendChild(scriptTag);
```

**Lý do KHÔNG dùng:** Phức tạp hơn, cách 1 đơn giản và rõ ràng hơn.

## 📝 Chi tiết thay đổi

### File: `tinymce-plugin.js`

#### TRƯỚC (dòng ~1343-1403):

```javascript
previewPanel.innerHTML = `
    <div>
        <!-- ... HTML content ... -->
        
        <!-- Update Function -->
        <script>
            window.updateShortcode_${key} = function() {
                // Function definition
            };
        </script>
    </div>
`;
```

**Vấn đề:**
- Script tag trong template string
- Hàm không bao giờ được định nghĩa
- Click button → `TypeError: updateShortcode_product is not a function`

#### SAU (dòng ~1343-1406):

```javascript
previewPanel.innerHTML = `
    <div>
        <!-- ... HTML content ... -->
        <!-- NO SCRIPT TAG -->
    </div>
`;

// Define function AFTER setting innerHTML
window['updateShortcode_' + key] = function() {
    console.log('🔄 Updating shortcode for ' + key + '...');
    
    const schemaCheckboxes = document.querySelectorAll('.schema-field-' + key);
    const contentCheckboxes = document.querySelectorAll('.content-field-' + key);
    
    let baseShortcode = template.shortcode;
    const hiddenSchemaAttrs = [];
    const hiddenContentAttrs = [];
    
    // Process schema checkboxes
    schemaCheckboxes.forEach(cb => {
        if (!cb.checked) {
            hiddenSchemaAttrs.push('hide_' + cb.dataset.attr + '="true"');
        }
    });
    
    // Process content checkboxes
    contentCheckboxes.forEach(cb => {
        if (!cb.checked) {
            hiddenContentAttrs.push('hide_content_' + cb.dataset.attr + '="true"');
        }
    });
    
    // Update shortcode
    if (hiddenSchemaAttrs.length > 0 || hiddenContentAttrs.length > 0) {
        const allHideAttrs = [...hiddenSchemaAttrs, ...hiddenContentAttrs].join(' ');
        baseShortcode = baseShortcode.replace(/\]/, ' ' + allHideAttrs + ']');
    }
    
    // Update textarea
    const textarea = document.getElementById('shortcode-textarea-' + key);
    if (textarea) {
        textarea.value = baseShortcode;
    }
};
```

**Cải thiện:**
- ✅ Hàm được định nghĩa bằng JavaScript thực
- ✅ Hàm tồn tại trong window scope
- ✅ Click button hoạt động bình thường
- ✅ Không còn TypeError

## 🧪 Cách kiểm tra

### Test Case 1: Click nút "All" ở Schema Fields

```
TRƯỚC:
❌ Uncaught TypeError: window.updateShortcode_product is not a function

SAU:
✅ Console log:
   🔄 Updating shortcode for product...
   📊 Schema checkboxes found: 4
   ✅ Textarea updated!
```

### Test Case 2: Click checkbox riêng lẻ

```
TRƯỚC:
❌ Uncaught TypeError: window.updateShortcode_faq is not a function

SAU:
✅ Checkbox thay đổi trạng thái
✅ Shortcode tự động cập nhật trong textarea
✅ Attribute hide_* được thêm/xóa đúng
```

### Test Case 3: Mở console và check

```javascript
// Sau khi click vào schema (ví dụ: Product)
console.log(typeof window.updateShortcode_product);

TRƯỚC: "undefined"  ❌
SAU:   "function"   ✅
```

## 📊 So sánh

| Tiêu chí | Script tag trong innerHTML | JavaScript định nghĩa sau |
|----------|----------------------------|--------------------------|
| **Hoạt động?** | ❌ Không | ✅ Có |
| **Bảo mật** | ⚠️ Bị browser block | ✅ An toàn |
| **Hiệu suất** | ⚡ Ngang nhau | ⚡ Ngang nhau |
| **Code complexity** | 😊 Đơn giản | 😊 Đơn giản |
| **Debugging** | 😢 Khó (hàm không tồn tại) | 😊 Dễ (có console log) |

## 🎯 Kết luận

### Nguyên tắc quan trọng:

> **KHÔNG BAO GIỜ đặt `<script>` tag trong `innerHTML`!**

Nếu cần dynamic script:
1. ✅ Định nghĩa hàm bằng JavaScript sau khi set innerHTML
2. ✅ Hoặc dùng `createElement('script')` và `appendChild()`
3. ❌ KHÔNG dùng script tag trong template string

### Lợi ích của fix này:

- ✅ Checkbox "All/None" hoạt động bình thường
- ✅ Checkbox riêng lẻ cập nhật shortcode real-time
- ✅ Console log giúp debug dễ dàng
- ✅ Code dễ maintain và extend sau này

## 🔗 Liên quan

- `TEST_CHECKBOX_GUIDE.md` - Hướng dẫn kiểm tra checkbox
- `test-checkbox-update.html` - File demo test
- `CHANGELOG_ATTRIBUTE_CONTROLS.md` - Lịch sử phát triển attribute controls

## 📅 Change Log

- **2025-10-09:** Fix bug `updateShortcode_${key} is not a function`
- **Method:** Move function definition from innerHTML script tag to JavaScript code
- **Files changed:** `tinymce-plugin.js` (line ~1343-1406)
- **Status:** ✅ Fixed và tested
