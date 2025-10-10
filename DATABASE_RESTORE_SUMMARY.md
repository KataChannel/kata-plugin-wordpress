# ✅ DATABASE RESTORE COMPLETED

## 📋 Thông tin restore

**Ngày thực hiện:** 2025-10-10  
**File backup:** `tazaspac_wp_timona_09102025.sql`  
**Kích thước:** 83 MB  
**Database:** tazaspac_wp_timona  

## 🔧 Các bước đã thực hiện

### 1. Kiểm tra thông tin database từ wp-config.php
```php
DB_NAME: tazaspac_wp_timona
DB_USER: timona_user
DB_PASSWORD: Timona@2025!Strong
DB_HOST: localhost
```

### 2. Xóa database cũ và tạo mới
```bash
mysql -u timona_user -p'Timona@2025!Strong' \
  -e "DROP DATABASE IF EXISTS tazaspac_wp_timona; 
      CREATE DATABASE tazaspac_wp_timona 
      CHARACTER SET utf8mb4 
      COLLATE utf8mb4_unicode_ci;"
```

**Kết quả:** ✅ Database mới đã được tạo

### 3. Restore database từ file SQL
```bash
mysql -u timona_user -p'Timona@2025!Strong' \
  tazaspac_wp_timona < tazaspac_wp_timona_09102025.sql
```

**Kết quả:** ✅ Import thành công (83 MB)

### 4. Update URL cho localhost
```sql
UPDATE gt_options 
SET option_value = 'http://localhost/timona' 
WHERE option_name IN ('siteurl', 'home');
```

**URL trước:** `https://timona.alodigital.edu.vn`  
**URL sau:** `http://localhost/timona`  
**Kết quả:** ✅ Updated 2 rows

## 📊 Thông tin database sau restore

### Tổng số bảng
```
65 tables
```

### Bảng gt_posts
- **Engine:** InnoDB
- **Số bài viết:** 9,797 posts
- **Kích thước data:** ~95.9 MB
- **Auto increment:** 24,111
- **Collation:** utf8mb4_unicode_520_ci

### URL Configuration
| Option | Value |
|--------|-------|
| home | http://localhost/timona |
| siteurl | http://localhost/timona |

## ✅ Trạng thái

- ✅ Database restore thành công
- ✅ Tất cả bảng đã được import
- ✅ URL đã được update cho localhost
- ✅ Charset: utf8mb4_unicode_ci
- ✅ Sẵn sàng sử dụng

## 🌐 Truy cập website

**URL:** http://localhost/timona  
**Admin:** http://localhost/timona/wp-admin

## ⚠️ Lưu ý

1. **Cache:** Xóa cache nếu cần
   ```bash
   rm -rf wp-content/cache/*
   ```

2. **Permalinks:** Vào Settings > Permalinks và click "Save Changes" để flush rewrite rules

3. **File uploads:** Kiểm tra đường dẫn uploads nếu có hình ảnh bị lỗi

4. **Plugins:** Kiểm tra plugins có hoạt động đúng không

## 📝 Lệnh hữu ích

### Kiểm tra số lượng bảng
```bash
mysql -u timona_user -p'Timona@2025!Strong' -D tazaspac_wp_timona \
  -e "SELECT COUNT(*) as table_count 
      FROM information_schema.tables 
      WHERE table_schema = 'tazaspac_wp_timona';"
```

### Kiểm tra số bài viết
```bash
mysql -u timona_user -p'Timona@2025!Strong' -D tazaspac_wp_timona \
  -e "SELECT post_type, post_status, COUNT(*) as count 
      FROM gt_posts 
      GROUP BY post_type, post_status 
      ORDER BY count DESC 
      LIMIT 10;"
```

### Kiểm tra URL
```bash
mysql -u timona_user -p'Timona@2025!Strong' -D tazaspac_wp_timona \
  -e "SELECT option_name, option_value 
      FROM gt_options 
      WHERE option_name IN ('siteurl', 'home');"
```

### Backup database
```bash
mysqldump -u timona_user -p'Timona@2025!Strong' \
  tazaspac_wp_timona > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 🔄 Rollback (nếu cần)

Nếu cần quay lại trạng thái cũ, chỉ cần restore lại từ file backup:

```bash
# Drop database
mysql -u timona_user -p'Timona@2025!Strong' \
  -e "DROP DATABASE IF EXISTS tazaspac_wp_timona; 
      CREATE DATABASE tazaspac_wp_timona 
      CHARACTER SET utf8mb4 
      COLLATE utf8mb4_unicode_ci;"

# Restore
mysql -u timona_user -p'Timona@2025!Strong' \
  tazaspac_wp_timona < tazaspac_wp_timona_09102025.sql
```

## 📅 File backup

**Location:** `/mnt/chikiet/webseo/timona/tazaspac_wp_timona_09102025.sql`  
**Size:** 83 MB  
**Date:** 2025-10-09 13:02  
**Status:** ✅ Đã sử dụng để restore thành công

---

**Restore completed at:** 2025-10-10  
**Status:** ✅ SUCCESS  
**Ready to use:** YES
