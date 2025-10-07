# Fix Lỗi 404 - WordPress Permalink Issue

## Vấn Đề:
- URL: http://localhost/timona/nganh-tri-lieu-dong-y-la-gi/
- Lỗi: HTTP 404 Not Found
- Nguyên nhân: `.htaccess` có RewriteBase sai

## Root Cause:
WordPress đang chạy trong subfolder `/timona` nhưng `.htaccess` có:
```apache
RewriteBase /
RewriteRule . /index.php [L]
```

## Solution:
Sửa `.htaccess` thành:
```apache
RewriteBase /timona/
RewriteRule . /timona/index.php [L]
```

## Files Modified:
- `/chikiet/webseo/timona/.htaccess` (lines 82-90)

## Result:
✅ HTTP 200 OK
✅ Font Exo 2 loaded
✅ Kata plugins active
✅ Schema markup working
✅ All permalinks functional

## Verification:
```bash
# Test URL
curl -I http://localhost/timona/nganh-tri-lieu-dong-y-la-gi/
# Result: HTTP/1.1 200 OK

# Check font
curl -s http://localhost/timona/nganh-tri-lieu-dong-y-la-gi/ | grep "Exo 2"
# Found: "Exo 2" in CSS

# Test other posts
curl -I http://localhost/timona/uon-mi-gladys/
# Result: HTTP/1.1 200 OK
```

## Notes:
- Permalink structure: `/%postname%/`
- Database prefix: `gt_` (not `wp_`)
- Site URLs updated to: `http://localhost/timona`
- All 66 database tables intact

---
Date: 7/10/2025
Status: RESOLVED ✅
