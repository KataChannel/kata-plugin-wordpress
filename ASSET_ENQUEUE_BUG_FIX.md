# BUG FIX: Duplicate Asset Enqueue in Admin UI

## Ngày: 7 October 2025

## Bug Đã Fix

### 🐛 Vấn đề:
Trang Admin Schema Customization không load đúng CSS/JS do code enqueue assets bị **DUPLICATE**.

### 🔍 Chi tiết lỗi:

**File:** `/wp-content/plugins/kata-seo-manager/includes/class-schema-admin-ui.php`

**Method:** `enqueue_scripts()` (dòng 46-93)

**Vấn đề:**
- Assets (CSS & JavaScript) được enqueue **2 LẦN**
- Lần 1: Dùng `plugin_dir_url(dirname(__FILE__))` - **WRONG PATH**
- Lần 2: Dùng `KATA_SEO_MANAGER_PLUGIN_URL` - **CORRECT PATH**
- `wp_localize_script` cũng gọi 2 lần với data khác nhau

### ✅ Giải pháp:

**Đã xóa code duplicate:**

```php
// ❌ XÓA CODE NÀY (dòng 51-72):
wp_enqueue_style(
    'kata-schema-admin-ui',
    plugin_dir_url(dirname(__FILE__)) . 'assets/css/schema-admin-ui.css', // WRONG
    array(),
    '1.1.0'
);

wp_enqueue_script(
    'kata-schema-admin-ui',
    plugin_dir_url(dirname(__FILE__)) . 'assets/js/schema-admin-ui.js', // WRONG
    array('jquery'),
    '1.1.0',
    true
);

wp_localize_script('kata-schema-admin-ui', 'kataSchemaAdmin', array(
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('kata_schema_admin'), // WRONG nonce
    'defaultProperties' => self::get_default_properties() // INCOMPLETE data
));
```

**Giữ lại code đúng:**

```php
// ✅ GIỮ CODE NÀY:
wp_enqueue_style(
    'kata-schema-admin-ui',
    KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/schema-admin-ui.css', // CORRECT
    array(),
    KATA_SEO_MANAGER_VERSION
);

wp_enqueue_script(
    'kata-schema-admin-ui',
    KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/schema-admin-ui.js', // CORRECT
    array('jquery'),
    KATA_SEO_MANAGER_VERSION,
    true
);

wp_localize_script('kata-schema-admin-ui', 'kataSchemaAdmin', array(
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('kata_schema_admin_nonce'), // CORRECT nonce
    'schemaTypes' => self::get_schema_types(), // COMPLETE data
    'defaultProperties' => KATA_Schema_Customizer::get_all_default_properties()
));
```

### 📊 Thay đổi:

**File thay đổi:** `class-schema-admin-ui.php`

**Dòng:** 46-73 (đã rút gọn từ 57 dòng xuống 31 dòng)

**Trước fix:**
- 2 lần `wp_enqueue_style`
- 2 lần `wp_enqueue_script`
- 2 lần `wp_localize_script` với data khác nhau
- Tổng: **57 dòng code**

**Sau fix:**
- 1 lần `wp_enqueue_style` (đúng URL)
- 1 lần `wp_enqueue_script` (đúng URL)
- 1 lần `wp_localize_script` (đầy đủ data)
- Tổng: **31 dòng code**

### 🧪 Kiểm tra:

```bash
# 1. Check syntax
php -l includes/class-schema-admin-ui.php
# ✅ No syntax errors

# 2. Verify files exist
ls -la assets/css/schema-admin-ui.css assets/js/schema-admin-ui.js
# ✅ CSS: 8,700 bytes
# ✅ JS: 15,087 bytes

# 3. Check file permissions
# ✅ Both files: 777 (readable/executable)
```

### 🎯 Kết quả:

✅ **Code sạch hơn** (giảm 26 dòng duplicate)
✅ **Asset URLs đúng** (dùng constant thay vì relative path)
✅ **Data đầy đủ** (schemaTypes + defaultProperties)
✅ **Nonce name đúng** (kata_schema_admin_nonce)
✅ **Version control tốt** (dùng KATA_SEO_MANAGER_VERSION)

### 📝 Hướng dẫn test:

1. **Clear browser cache:** Ctrl+Shift+R
2. **Truy cập:** http://localhost/timona/wp-admin/admin.php?page=kata-schema-customization
3. **Mở Browser Console:** F12 → Console tab
4. **Kiểm tra:**
   - ✅ Không có lỗi 404 (assets not found)
   - ✅ Không có JavaScript errors
   - ✅ CSS được apply đúng
   - ✅ Admin UI hiển thị đầy đủ

### 🔗 Related Files:

- `class-schema-admin-ui.php` (602 lines) - Backend logic ✅ FIXED
- `schema-admin-ui.css` (8.7KB) - Styling ✅ OK
- `schema-admin-ui.js` (15KB) - Frontend ✅ OK

### 📌 Admin Page URL:

```
http://localhost/timona/wp-admin/admin.php?page=kata-schema-customization
```

### 🏆 Status:

**BUG:** ❌ Duplicate asset enqueue
**FIX:** ✅ COMPLETED
**TESTED:** ⏳ Pending user verification

---

**Tóm tắt ngắn gọn:**
Đã xóa code duplicate enqueue assets trong `class-schema-admin-ui.php`, giữ lại version đúng sử dụng constants và data đầy đủ. Admin page bây giờ sẽ load CSS/JS chính xác.
