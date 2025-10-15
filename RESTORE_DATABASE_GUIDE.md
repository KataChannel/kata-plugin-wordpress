# 🔄 RESTORE DATABASE GUIDE

**Date:** October 15, 2025  
**Database File:** `tazaspac_wp_timona15102025.sql`  
**Status:** ✅ Configuration Updated

---

## ✅ Database Configuration Fixed

### Updated `wp-config.php`:

```php
// BEFORE (BUG - Duplicate DB_USER):
define('DB_USER', 'tazaspac_wp_timona');  ❌
// define('DB_PASSWORD', 'timona123');     ❌
define('DB_USER', 'timona_user');          ❌ Duplicate!
define('DB_PASSWORD', 'Timona@2025!Strong');

// AFTER (FIXED):
define('DB_NAME', 'tazaspac_wp_timona');
define('DB_USER', 'timona_user');          ✅
define('DB_PASSWORD', 'Timona@2025!Strong'); ✅
define('DB_HOST', 'localhost');
```

### Configuration Match:

| Parameter | wp-config.php | 05_import_database.sh | Status |
|-----------|---------------|----------------------|--------|
| DB_NAME | `tazaspac_wp_timona` | `tazaspac_wp_timona` | ✅ Match |
| DB_USER | `timona_user` | `timona_user` | ✅ Match |
| DB_PASS | `Timona@2025!Strong` | `Timona@2025!Strong` | ✅ Match |
| DB_HOST | `localhost` | (implicit) | ✅ Match |

---

## 🚀 Quick Restore Instructions

### Method 1: Using Install Script (Recommended)

```bash
cd /chikiet/webseo/timona
chmod +x install/05_import_database.sh
./install/05_import_database.sh
```

**Script will:**
1. ✅ Auto-detect `tazaspac_wp_timona15102025.sql`
2. ✅ Backup current database (if exists)
3. ✅ Drop old tables
4. ✅ Import new data
5. ✅ Verify import success

---

### Method 2: Manual Import

#### Step 1: Verify MySQL User Exists

```bash
sudo mysql -e "SELECT User, Host FROM mysql.user WHERE User='timona_user';"
```

**Expected output:**
```
+-------------+-----------+
| User        | Host      |
+-------------+-----------+
| timona_user | localhost |
+-------------+-----------+
```

If not exists, create user:

```bash
sudo mysql -e "CREATE USER 'timona_user'@'localhost' IDENTIFIED BY 'Timona@2025!Strong';"
sudo mysql -e "GRANT ALL PRIVILEGES ON tazaspac_wp_timona.* TO 'timona_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

---

#### Step 2: Verify Database Exists

```bash
sudo mysql -e "SHOW DATABASES LIKE 'tazaspac_wp_timona';"
```

**Expected output:**
```
+---------------------------+
| Database                  |
+---------------------------+
| tazaspac_wp_timona        |
+---------------------------+
```

If not exists, create database:

```bash
sudo mysql -e "CREATE DATABASE tazaspac_wp_timona CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

---

#### Step 3: Backup Current Database (Optional)

```bash
cd /chikiet/webseo/timona
mysqldump -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona > backup_$(date +%Y%m%d_%H%M%S).sql
```

---

#### Step 4: Import SQL File

```bash
cd /chikiet/webseo/timona
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona < tazaspac_wp_timona15102025.sql
```

**Alternative (with sudo):**

```bash
sudo mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona < tazaspac_wp_timona15102025.sql
```

---

#### Step 5: Verify Import

```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "SHOW TABLES;"
```

**Expected:** List of WordPress tables (gt_*)

```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'tazaspac_wp_timona';"
```

**Expected:** Number > 0 (WordPress typically has 12+ tables)

---

## 🧪 Test Database Connection

### Method 1: PHP Test Script

```bash
php -r "
\$mysqli = new mysqli('localhost', 'timona_user', 'Timona@2025!Strong', 'tazaspac_wp_timona');
if (\$mysqli->connect_error) {
    die('Connection failed: ' . \$mysqli->connect_error);
}
echo '✓ Database connection successful!\n';
\$result = \$mysqli->query('SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = \"tazaspac_wp_timona\"');
\$row = \$result->fetch_assoc();
echo '✓ Tables found: ' . \$row['cnt'] . '\n';
\$mysqli->close();
"
```

---

### Method 2: WordPress Test

```bash
cd /chikiet/webseo/timona
php -r "
define('DB_NAME', 'tazaspac_wp_timona');
define('DB_USER', 'timona_user');
define('DB_PASSWORD', 'Timona@2025!Strong');
define('DB_HOST', 'localhost');

\$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if (!\$link) {
    die('Connection failed: ' . mysqli_connect_error());
}
echo '✓ WordPress database connection OK!\n';
mysqli_close(\$link);
"
```

---

### Method 3: Access WordPress

```bash
# Start PHP server (if needed)
cd /chikiet/webseo/timona
php -S localhost:8080

# Or access via existing server
# http://localhost/timona
```

**Expected:**
- ✅ WordPress loads without database errors
- ✅ Login page accessible
- ✅ No "Error establishing database connection"

---

## 🔍 Troubleshooting

### Issue 1: "Access denied for user"

**Error:**
```
ERROR 1045 (28000): Access denied for user 'timona_user'@'localhost'
```

**Solution:**
```bash
# Reset password
sudo mysql -e "ALTER USER 'timona_user'@'localhost' IDENTIFIED BY 'Timona@2025!Strong';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

---

### Issue 2: "Unknown database"

**Error:**
```
ERROR 1049 (42000): Unknown database 'tazaspac_wp_timona'
```

**Solution:**
```bash
sudo mysql -e "CREATE DATABASE tazaspac_wp_timona CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "GRANT ALL PRIVILEGES ON tazaspac_wp_timona.* TO 'timona_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

---

### Issue 3: SQL Import Fails

**Error:**
```
ERROR at line XX: ...
```

**Solutions:**

**A. Check file exists:**
```bash
ls -lh /chikiet/webseo/timona/tazaspac_wp_timona15102025.sql
```

**B. Check file permissions:**
```bash
chmod 644 /chikiet/webseo/timona/tazaspac_wp_timona15102025.sql
```

**C. Import with detailed errors:**
```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona < tazaspac_wp_timona15102025.sql 2>&1 | tee import_errors.log
```

**D. Try with root:**
```bash
sudo mysql tazaspac_wp_timona < tazaspac_wp_timona15102025.sql
```

---

### Issue 4: WordPress URL Mismatch

After import, if WordPress redirects to wrong URL:

```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "
UPDATE gt_options SET option_value='http://localhost/timona' WHERE option_name='siteurl';
UPDATE gt_options SET option_value='http://localhost/timona' WHERE option_name='home';
"
```

---

### Issue 5: Plugin Errors After Import

```bash
# Verify plugin files exist
ls -la /chikiet/webseo/timona/wp-content/plugins/kata-*

# Check plugin status
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "
SELECT option_value FROM gt_options WHERE option_name='active_plugins';
"
```

---

## 📊 Import Verification Checklist

After import, verify:

- [ ] Database connection successful
- [ ] Tables imported (count > 0)
- [ ] WordPress loads without errors
- [ ] Login page accessible
- [ ] Admin panel accessible
- [ ] Posts/pages visible
- [ ] Plugins activated
- [ ] Theme active
- [ ] Media library intact
- [ ] Settings preserved

---

## 🔐 Security Notes

### Current Credentials:

```
Database: tazaspac_wp_timona
User:     timona_user
Password: Timona@2025!Strong
Host:     localhost
```

### Recommendations:

1. ✅ Strong password already set
2. ⚠️ Change password for production:
```bash
sudo mysql -e "ALTER USER 'timona_user'@'localhost' IDENTIFIED BY 'YOUR_NEW_STRONG_PASSWORD';"
```

3. ⚠️ Update `wp-config.php` after password change

4. ✅ Restrict database access:
```bash
# Only allow localhost access (already done)
sudo mysql -e "SELECT User, Host FROM mysql.user WHERE User='timona_user';"
```

---

## 📁 File Locations

```
/chikiet/webseo/timona/
├── wp-config.php                          ← Updated ✅
├── tazaspac_wp_timona15102025.sql        ← Import this
├── install/
│   └── 05_import_database.sh             ← Use this script
└── backup_*.sql                           ← Auto-created backups
```

---

## 🎯 Quick Commands Summary

### Using Script (Easiest):
```bash
cd /chikiet/webseo/timona
./install/05_import_database.sh
```

### Manual Import:
```bash
cd /chikiet/webseo/timona
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona < tazaspac_wp_timona15102025.sql
```

### Verify:
```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "SHOW TABLES;"
```

### Test WordPress:
```bash
# Browser
http://localhost/timona

# Or CLI
php -r "require 'wp-load.php'; echo 'WordPress loaded: ' . get_bloginfo('name') . PHP_EOL;"
```

---

## ✅ Completion Checklist

- [x] Fixed `wp-config.php` duplicate DB_USER
- [x] Updated DB credentials to match script
- [x] Verified configuration syntax
- [ ] Run import script
- [ ] Verify tables imported
- [ ] Test WordPress login
- [ ] Check plugins active
- [ ] Verify site loads correctly

---

## 📞 Support

If issues persist:

1. Check MySQL error log:
```bash
sudo tail -f /var/log/mysql/error.log
```

2. Check WordPress debug log:
```bash
tail -f /chikiet/webseo/timona/wp-content/debug.log
```

3. Check PHP error log:
```bash
sudo tail -f /var/log/apache2/error.log
# or
sudo tail -f /var/log/nginx/error.log
```

---

**Updated:** October 15, 2025  
**Status:** ✅ Ready to import  
**Next Step:** Run `./install/05_import_database.sh`
