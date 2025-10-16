# KATA Schema - Quick Test Guide (After Bugfix)

## 🧪 Testing Form Submit Fix

### Test 1: Create New Schema ✅

**Steps:**
1. Go to: **KATA Schema → Thêm Schema Mới**
2. Fill in:
   - **Tên Schema:** "Test Article Schema"
   - **Loại Schema:** Article
   - Click **"Kiểm Tra JSON"** (should auto-load template)
3. Click **"Tạo Schema"**

**Expected Results:**
- ✅ Button changes to: `🔄 Đang lưu...`
- ✅ Button is disabled (can't click again)
- ✅ Alert shows: "Đã lưu schema thành công!"
- ✅ Redirect to dashboard
- ✅ New schema appears in list
- ✅ **URL does NOT contain parameters** (should be `admin.php?page=kata-schema`)

**If it fails:**
- Open browser console (F12)
- Check for errors
- Look for `kataSchemaAdmin is not defined` error
- If error exists, clear cache and reload

---

### Test 2: Edit Existing Schema ✅

**Steps:**
1. Go to **KATA Schema → Dashboard**
2. Click **"Sửa"** on any schema
3. Modify JSON (change headline text)
4. Click **"Cập Nhật Schema"**

**Expected Results:**
- ✅ Button shows loading spinner
- ✅ Success alert
- ✅ Redirect to dashboard
- ✅ Changes are saved
- ✅ URL clean (no GET parameters)

---

### Test 3: Network Error Handling ✅

**Steps:**
1. Open DevTools (F12)
2. Go to **Network** tab
3. Click **"Offline"** (simulate no internet)
4. Try to save schema

**Expected Results:**
- ✅ Error alert with details
- ✅ Button re-enabled
- ✅ Can retry after going online

---

### Test 4: Invalid JSON ✅

**Steps:**
1. Enter invalid JSON in textarea:
   ```
   {invalid json}
   ```
2. Click save

**Expected Results:**
- ✅ Alert: "Dữ liệu JSON không hợp lệ. Vui lòng kiểm tra lại."
- ✅ Form NOT submitted
- ✅ Button NOT disabled

---

### Test 5: Template Auto-load ✅

**Steps:**
1. Go to **KATA Schema → Templates**
2. Click **"Sử dụng Template"** on any schema type (e.g., Product)
3. Should redirect to add page

**Expected Results:**
- ✅ URL contains `?template=Product`
- ✅ Dropdown auto-selects "Sản phẩm"
- ✅ Textarea auto-fills with Product JSON template
- ✅ Schema name auto-fills: "Sản phẩm - Mẫu mặc định"

---

### Test 6: Dropdown Change Auto-load ✅

**Steps:**
1. Go to add schema page (empty)
2. Select **"FAQ"** from Loại Schema dropdown

**Expected Results:**
- ✅ Textarea immediately fills with FAQ template
- ✅ No confirmation (field is empty)

**Now test with data:**
1. Leave FAQ data in textarea
2. Select **"HowTo"** from dropdown

**Expected Results:**
- ✅ Confirmation dialog: "Bạn có muốn tải template mẫu? Dữ liệu hiện tại sẽ bị thay thế."
- ✅ Click OK → HowTo template loads
- ✅ Click Cancel → FAQ data remains

---

### Test 7: Double Submission Prevention ✅

**Steps:**
1. Fill in form
2. **Rapidly click submit button 5-10 times**

**Expected Results:**
- ✅ Button disables after first click
- ✅ Only ONE AJAX request in Network tab
- ✅ Only ONE schema created in database
- ✅ No duplicate entries

---

## 🔍 Debug Checklist

### If schema doesn't save:

**1. Check JavaScript Console (F12):**
```javascript
// Type this in console:
console.log(kataSchemaAdmin);
```
**Should show:**
```javascript
{
    ajaxUrl: "http://localhost/timona/wp-admin/admin-ajax.php",
    nonce: "abc123...",
    pluginUrl: "http://localhost/timona/wp-content/plugins/kata-schema/",
    i18n: {...}
}
```

**If shows `undefined`:**
- Clear browser cache
- Hard reload (Ctrl+Shift+R)
- Check plugin is activated

**2. Check Network Tab:**
- Open DevTools → Network
- Try to save schema
- Look for request to `admin-ajax.php`
- Click on it to see Request/Response

**Request should contain:**
```
action: kata_save_schema
nonce: abc123...
schema_name: Test
schema_type: Article
schema_data: {...}
status: active
```

**Response should be:**
```json
{
    "success": true,
    "data": {
        "message": "Schema đã được tạo mới!",
        "schema_id": 123
    }
}
```

**3. Check PHP Errors:**
```bash
tail -f /var/log/apache2/error.log
# or
tail -f /var/www/html/timona/wp-content/debug.log
```

**4. Verify Database Table:**
```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "SELECT * FROM gt_kata_schemas ORDER BY id DESC LIMIT 5;"
```

Should show latest schemas.

---

## ✅ Success Indicators

### Visual Feedback:
1. **Button Loading State:**
   - Text changes to "Đang lưu..."
   - Spinner icon appears
   - Button is disabled (grayed out)

2. **Success Flow:**
   - Alert popup: "Đã lưu schema thành công!"
   - Page redirects to dashboard
   - Schema appears in list immediately

3. **Error Flow:**
   - Alert with error details
   - Button re-enables
   - Can retry

### Technical Indicators:
1. **Network Tab:**
   - Request to `admin-ajax.php`
   - Method: POST (not GET)
   - Status: 200 OK
   - Response: `{"success":true,...}`

2. **URL:**
   - Clean: `admin.php?page=kata-schema-add`
   - NO parameters like `?schema_name=...&schema_data=...`

3. **Database:**
   - New row in `gt_kata_schemas` table
   - `schema_data` contains JSON
   - `created_at` timestamp is current

---

## 🐛 Known Issues (Fixed)

### ~~Issue 1: GET Submission~~
**Status:** ✅ FIXED  
**Before:** Form submitted via GET with URL parameters  
**After:** AJAX POST submission

### ~~Issue 2: No Loading State~~
**Status:** ✅ FIXED  
**Before:** No visual feedback, could double-click  
**After:** Button disabled with spinner

### ~~Issue 3: Poor Error Messages~~
**Status:** ✅ FIXED  
**Before:** Generic "Có lỗi xảy ra"  
**After:** Detailed error with context

### ~~Issue 4: Variable Name Mismatch~~
**Status:** ✅ FIXED  
**Before:** `kataSchema` vs `kataSchemaAdmin`  
**After:** Consistent `kataSchemaAdmin`

---

## 📞 Support

**Still having issues?**

1. **Clear all cache:**
   ```bash
   # Browser cache: Ctrl+Shift+Delete
   # WordPress cache:
   rm -rf /mnt/chikiet/webseo/timona/wp-content/cache/*
   ```

2. **Verify plugin files:**
   ```bash
   cd /mnt/chikiet/webseo/timona/wp-content/plugins/kata-schema
   git status
   # Should show: "working tree clean"
   ```

3. **Check plugin version:**
   - Look at plugin list in WordPress
   - Should show: **KATA Schema Manager 1.0.2**

4. **Re-activate plugin:**
   - Deactivate → Activate
   - Check if default templates appear

5. **Contact support:**
   - Provide browser console errors
   - Provide Network tab screenshot
   - Provide PHP error log

---

## ✅ Final Checklist

Test all scenarios:
- [ ] Create new schema
- [ ] Edit existing schema
- [ ] Template auto-load from URL
- [ ] Template auto-load from dropdown
- [ ] Confirmation before overwrite
- [ ] Invalid JSON validation
- [ ] Network error handling
- [ ] Double submission prevention
- [ ] Loading state visible
- [ ] Success redirect works
- [ ] Error message shows details
- [ ] Button re-enables on error

**If all checked:** ✅ **Plugin working correctly!**

---

**Version:** 1.0.2  
**Last Updated:** 16/10/2025  
**Status:** Production Ready
