# 🔧 Database Connection Fix & Import Guide

## 🚨 **Vấn đề:**
Lỗi kết nối database: `Access denied for user 'tazaspac_wp_timona'@'localhost'`

## ✅ **Giải pháp:**

### 🎯 **QUICK FIX (Khuyến nghị - 2 phút)**

Chạy script tự động:

```bash
cd /mnt/chikiet/webseo/timona
bash quick_database_fix.sh
```

**Script sẽ tự động:**
1. ✅ Tạo database `tazaspac_wp_timona`
2. ✅ Tạo user `tazaspac_wp_timona` với password `timona123`
3. ✅ Import SQL backup (83MB)
4. ✅ Verify kết nối

**Yêu cầu:** MySQL root password

---

## 📋 **Các Script Có Sẵn:**

### 1. **quick_database_fix.sh** ⭐ (Khuyến nghị)
```bash
bash quick_database_fix.sh
```
- Nhanh nhất (2-5 phút)
- Tự động hoàn toàn
- Cần MySQL root password

### 2. **setup_database_from_backup.sh** (Full featured)
```bash
sudo bash setup_database_from_backup.sh
```
- Chi tiết hơn
- Progress bar (nếu có `pv`)
- Backup check
- Verification steps

### 3. **database_import_guide.sh** (Hướng dẫn)
```bash
bash database_import_guide.sh
```
- Hiển thị hướng dẫn manual
- Các option import khác nhau
- Troubleshooting tips

### 4. **test_database_connection.php** (Kiểm tra)
```bash
php test_database_connection.php
```
- Test kết nối database
- Kiểm tra tables
- Diagnose issues

---

## 🛠️ **Manual Fix (Nếu script không chạy)**

### Option A: MySQL Command Line

```bash
# 1. Login to MySQL
mysql -u root -p

# 2. Tạo database và user (copy/paste)
DROP DATABASE IF EXISTS tazaspac_wp_timona;
CREATE DATABASE tazaspac_wp_timona CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
DROP USER IF EXISTS 'tazaspac_wp_timona'@'localhost';
CREATE USER 'tazaspac_wp_timona'@'localhost' IDENTIFIED BY 'timona123';
GRANT ALL PRIVILEGES ON tazaspac_wp_timona.* TO 'tazaspac_wp_timona'@'localhost';
FLUSH PRIVILEGES;
exit;

# 3. Import SQL file
mysql -u root -p tazaspac_wp_timona < tazaspac_wp_timona_07102025.sql

# 4. Verify
php test_database_connection.php
```

### Option B: phpMyAdmin

1. Mở: `http://localhost/phpmyadmin`
2. Login với MySQL root
3. Tạo database: `tazaspac_wp_timona`
4. Create user: `tazaspac_wp_timona` / `timona123`
5. Grant privileges
6. Import file SQL: `tazaspac_wp_timona_07102025.sql`

---

## 🔍 **Troubleshooting**

### Problem 1: "Access denied for user 'root'"

**Giải pháp:**

```bash
# Check MySQL root có password không
sudo mysql -e "SELECT 1;"

# Nếu được → root không cần password
# Nếu lỗi → root cần password

# Reset MySQL root password
sudo mysql
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'newpassword';
FLUSH PRIVILEGES;
exit;
```

### Problem 2: "MySQL service not running"

```bash
# Start MySQL
sudo systemctl start mysql

# Enable on boot
sudo systemctl enable mysql

# Check status
sudo systemctl status mysql
```

### Problem 3: "SQL file too large for phpMyAdmin"

**Giải pháp 1:** Tăng upload limit

```bash
# Edit php.ini
sudo nano /etc/php/8.x/apache2/php.ini

# Change these values:
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300

# Restart Apache
sudo systemctl restart apache2
```

**Giải pháp 2:** Dùng command line

```bash
mysql -u root -p tazaspac_wp_timona < tazaspac_wp_timona_07102025.sql
```

### Problem 4: "Import timeout or freeze"

File SQL 83MB có thể mất 2-5 phút. **ĐỪNG ngắt kết nối!**

Monitor progress:
```bash
# Terminal 1: Import
mysql -u root -p tazaspac_wp_timona < tazaspac_wp_timona_07102025.sql

# Terminal 2: Monitor
watch -n 1 'mysql -u root -p -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = \"tazaspac_wp_timona\";"'
```

### Problem 5: "Character encoding errors"

```bash
# Import with explicit encoding
mysql -u root -p --default-character-set=utf8mb4 tazaspac_wp_timona < tazaspac_wp_timona_07102025.sql
```

---

## ✅ **Verification Steps**

### 1. Test Database Connection

```bash
php test_database_connection.php
```

**Expected output:**
```
✓ MySQLi extension is loaded
✓ Successfully connected to MySQL server
✓ Database 'tazaspac_wp_timona' exists
✓ Successfully selected database
✓ Database contains 50+ tables
✓ All WordPress core tables exist
✓ All KATA plugin tables exist
✅ DATABASE IS READY TO USE!
```

### 2. Test WordPress Site

```bash
# Access site
curl -I http://localhost/timona

# Should return: HTTP/1.1 200 OK
```

### 3. Check Database Tables

```bash
mysql -u tazaspac_wp_timona -p'timona123' -e "USE tazaspac_wp_timona; SHOW TABLES;"
```

**Should show 50+ tables including:**
- wp_posts
- wp_users
- wp_options
- wp_kata_schemas
- wp_kata_polls
- etc.

---

## 📊 **Database Information**

| Setting | Value |
|---------|-------|
| **Database Name** | tazaspac_wp_timona |
| **Database User** | tazaspac_wp_timona |
| **Database Password** | timona123 |
| **Database Host** | localhost |
| **SQL Backup File** | tazaspac_wp_timona_07102025.sql |
| **File Size** | 83 MB |
| **Expected Tables** | 50+ tables |
| **Import Time** | 2-5 minutes |

---

## 🚀 **After Import Success**

### 1. Access WordPress Admin

```
URL: http://localhost/timona/wp-admin
```

### 2. Activate KATA SEO Manager

```
WordPress Admin → Plugins → Activate KATA SEO Manager
```

### 3. Verify Plugin Tables

```bash
mysql -u tazaspac_wp_timona -p'timona123' -e "USE tazaspac_wp_timona; SHOW TABLES LIKE 'wp_kata_%';"
```

### 4. Test Shortcodes

```
Create test post with:
[kata_article title="Test" show_schema="true"]

View source → Schema should be in <head>
```

---

## 📝 **Summary Commands**

```bash
# Quick fix (recommended)
bash quick_database_fix.sh

# Test connection
php test_database_connection.php

# Manual import
mysql -u root -p tazaspac_wp_timona < tazaspac_wp_timona_07102025.sql

# Verify tables
mysql -u tazaspac_wp_timona -p'timona123' -e "USE tazaspac_wp_timona; SHOW TABLES;"

# Check website
curl -I http://localhost/timona
```

---

## 📞 **Need Help?**

### Files Created:
- ✅ `quick_database_fix.sh` - Quick automated fix
- ✅ `setup_database_from_backup.sh` - Full featured setup
- ✅ `database_import_guide.sh` - Manual guide
- ✅ `test_database_connection.php` - Connection test

### Log Files:
- MySQL errors: `/var/log/mysql/error.log`
- WordPress errors: `wp-content/debug.log`
- Apache errors: `/var/log/apache2/error.log`

### Check Logs:
```bash
# MySQL
sudo tail -f /var/log/mysql/error.log

# WordPress
tail -f wp-content/debug.log

# Apache
sudo tail -f /var/log/apache2/error.log
```

---

## ✅ **Expected Result**

Sau khi fix thành công:

```
✅ Database: tazaspac_wp_timona - CREATED
✅ User: tazaspac_wp_timona - CREATED  
✅ SQL Import: 50+ tables - IMPORTED
✅ Connection Test: PASSED
✅ WordPress Site: WORKING
✅ KATA SEO Plugin: READY
```

---

**Last Updated:** October 8, 2025  
**Status:** Ready to deploy  
**Import Time:** 2-5 minutes for 83MB SQL file

🎉 **Database setup complete - Website ready to use!**
