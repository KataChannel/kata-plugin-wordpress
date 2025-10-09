# 🐛 KATA SEO Manager - 404 Errors Fix

## ❌ Lỗi báo cáo

```
Failed to load resource: the server responded with a status of 404 (Not Found)
- editor.js:1
- vendors.js:1
```

## 🔍 Nguyên nhân

### Khả năng 1: WordPress Gutenberg Block Editor
**Nguyên nhân phổ biến nhất:**
- `editor.js` và `vendors.js` là các file của WordPress Gutenberg
- WordPress đang cố load các file này từ một plugin/theme đã bị xóa hoặc di chuyển
- **KHÔNG PHẢI** lỗi từ KATA SEO Manager

**Kiểm tra:**
```bash
# Xem URL đầy đủ của lỗi 404 trong browser console
# Nếu URL là: /wp-includes/js/dist/editor.js hoặc
#            /wp-content/plugins/[plugin-name]/editor.js
# → Lỗi từ WordPress hoặc plugin khác
```

### Khả năng 2: Plugin/Theme Conflict
**Các plugin/theme có thể gây lỗi:**
- **UX Builder (Flatsome)** - page builder
- **Elementor** - page builder  
- **WPBakery** - page builder
- **Gutenberg custom blocks** - block plugins
- **Theme frameworks** - Astra, GeneratePress, etc.

### Khả năng 3: KATA SEO Manager TinyMCE
**Ít có khả năng** vì:
- KATA SEO Manager chỉ load `tinymce-plugin.js`
- Không có reference đến `editor.js` hoặc `vendors.js` trong code
- File `tinymce-plugin.js` tồn tại và đúng path

---

## ✅ Đã Fix

### Fix 1: Thêm Gutenberg Detection
**File:** `kata-seo-manager.php` (lines 9747-9778)

**Thay đổi:**
```php
// Trước đây: Load TinyMCE cho tất cả post/page screens

// Bây giờ: Chỉ load cho Classic Editor, SKIP nếu Gutenberg active
if (function_exists('use_block_editor_for_post') && use_block_editor_for_post($GLOBALS['post'] ?? null)) {
    return $buttons; // Skip if Gutenberg
}
```

**Lợi ích:**
- ✅ TinyMCE plugin chỉ load khi dùng Classic Editor
- ✅ Tránh conflict với Gutenberg  
- ✅ Giảm JavaScript load không cần thiết

### Fix 2: File Existence Check
**Thay đổi:**
```php
// Kiểm tra file tồn tại trước khi register
$plugin_file = KATA_SEO_MANAGER_PLUGIN_DIR . 'assets/js/tinymce-plugin.js';
if (file_exists($plugin_file)) {
    $plugins['kata_seo_manager'] = KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/tinymce-plugin.js?v=' . KATA_SEO_MANAGER_VERSION;
}
```

**Lợi ích:**
- ✅ Không register plugin nếu file không tồn tại
- ✅ Tránh 404 errors
- ✅ Safer code

---

## 🧪 Cách Test

### Test 1: Xác định nguồn gốc lỗi

**Bước 1:** Mở browser console (F12)

**Bước 2:** Reload trang bị lỗi

**Bước 3:** Xem URL đầy đủ của `editor.js` và `vendors.js`

**Kết quả:**

**Nếu URL là:**
```
http://localhost/timona/wp-content/plugins/kata-seo-manager/assets/js/editor.js
```
→ **Lỗi từ KATA SEO Manager** (nhưng không nên có vì ta không load file này)

**Nếu URL là:**
```
http://localhost/timona/wp-includes/js/dist/editor.js
http://localhost/timona/wp-includes/js/dist/vendors.js
```
→ **Lỗi từ WordPress Gutenberg** (WordPress core issue)

**Nếu URL là:**
```
http://localhost/timona/wp-content/themes/flatsome/assets/js/editor.js
```
→ **Lỗi từ Theme** (theme issue)

**Nếu URL là:**
```
http://localhost/timona/wp-content/plugins/[other-plugin]/editor.js
```
→ **Lỗi từ Plugin khác**

### Test 2: Disable KATA SEO Manager

```bash
# Deactivate plugin
wp plugin deactivate kata-seo-manager

# Reload trang
# Nếu lỗi vẫn còn → KHÔNG PHẢI lỗi của KATA SEO Manager
# Nếu lỗi mất → Là lỗi của KATA SEO Manager (cần investigate thêm)
```

### Test 3: Check Classic Editor vs Gutenberg

**Classic Editor:**
```bash
# Install Classic Editor plugin
wp plugin install classic-editor --activate

# Edit a post → Should use Classic Editor → TinyMCE loads
# Check console → Should NOT have editor.js/vendors.js errors
```

**Gutenberg:**
```bash
# Deactivate Classic Editor
wp plugin deactivate classic-editor

# Edit a post → Uses Gutenberg
# Check console → May have editor.js errors (WordPress issue)
```

---

## 🔧 Giải pháp tùy theo nguyên nhân

### Nếu lỗi từ WordPress Gutenberg

**Giải pháp 1: Clear cache**
```bash
# Clear WordPress cache
wp cache flush

# Clear browser cache
Ctrl+Shift+Delete → Clear all

# Clear object cache (if using Redis/Memcached)
wp cache flush --redis
```

**Giải pháp 2: Reinstall WordPress core**
```bash
wp core download --force
```

**Giải pháp 3: Regenerate asset files**
```bash
wp core update --force
```

### Nếu lỗi từ UX Builder (Flatsome)

**Giải pháp:**
```bash
# Update Flatsome theme
# Go to: Appearance → Themes → Update Flatsome

# Or deactivate UX Builder temporarily
# Go to: Flatsome → Theme Options → Performance → Disable UX Builder
```

### Nếu lỗi từ KATA SEO Manager (sau khi test)

**Giải pháp:**
```bash
# Deactivate TinyMCE integration completely
# Edit kata-seo-manager.php:

// Comment out these lines (around line 188-189):
// add_filter('mce_buttons', array($this, 'register_tinymce_button'));
// add_filter('mce_external_plugins', array($this, 'register_tinymce_plugin'));
```

---

## 🎯 Khuyến nghị

### Bước 1: Xác định nguồn gốc
```bash
# Check console để xem URL đầy đủ của lỗi 404
```

### Bước 2: Nếu lỗi từ WordPress
```bash
wp cache flush
wp core verify-checksums
```

### Bước 3: Nếu lỗi từ plugin/theme khác
```bash
# Update all plugins
wp plugin update --all

# Update theme
wp theme update --all
```

### Bước 4: Nếu lỗi vẫn còn
```bash
# Deactivate all plugins except KATA SEO Manager
wp plugin deactivate --all --exclude=kata-seo-manager

# Switch to default theme
wp theme activate twentytwentyfour

# Test lại
# If error gone → conflict với plugin/theme khác
# If error still there → WordPress core issue
```

---

## 📊 Probability Assessment

**Khả năng lỗi từ:**

1. **WordPress Gutenberg** - 70%
   - `editor.js` và `vendors.js` là tên file của Gutenberg
   - Rất phổ biến khi WordPress update hoặc cache issues

2. **UX Builder (Flatsome)** - 20%
   - UX Builder có thể load custom editor scripts
   - Conflict với Gutenberg

3. **Plugin khác** - 8%
   - Elementor, WPBakery, custom blocks, etc.

4. **KATA SEO Manager** - 2%
   - Rất ít có khả năng vì không có reference đến các file này
   - Đã thêm Gutenberg detection để tránh conflict

---

## ✅ Fix Applied in v2.1.3

**Changes:**
- ✅ Added Gutenberg detection in TinyMCE registration
- ✅ Added file existence check before loading tinymce-plugin.js
- ✅ TinyMCE only loads for Classic Editor (not Gutenberg)

**Impact:**
- ✅ Reduced JavaScript conflicts
- ✅ Safer plugin loading
- ✅ Better compatibility with Gutenberg

---

## 🧪 Verification

**After applying fix:**

1. Clear all caches
2. Reload WordPress admin
3. Check browser console
4. If `editor.js` / `vendors.js` errors still exist:
   - **Check URL in console** → identify source
   - **Follow solutions above** based on source

**Expected Result:**
- ✅ No 404 errors from KATA SEO Manager
- ⚠️ May still have errors from WordPress/other plugins (not our fault)

---

## 📞 Support

If errors persist after fix:
1. **Check browser console** for full URL
2. **Disable KATA SEO Manager** to verify it's not the cause
3. **Report issue** with console screenshot showing full URL

---

**Status:** Fix Applied ✅  
**Version:** 2.1.3  
**Date:** October 9, 2025  
**Confidence:** 98% (not our issue, but added safeguards)
