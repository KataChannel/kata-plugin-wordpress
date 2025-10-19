# WORDPRESS 404 NOT FOUND ERROR - FIXED

## 🐛 Issue Summary
**Problem**: WordPress pages showing "404 Not Found" error  
**Example URL**: `http://localhost/timona/ban-giam-hieu-timona/`  
**Error Message**: 
```
Not Found
The requested URL was not found on this server.
Apache/2.4.64 (Ubuntu) Server at localhost Port 80
```

## 🔍 Root Cause Analysis

### Issue Identified:
Apache configuration missing **`AllowOverride All`** directive

### Why This Causes 404 Errors:
1. **WordPress uses pretty permalinks** like `/page-name/` instead of `/?p=123`
2. **Pretty permalinks require `.htaccess` rewrite rules**
3. **Apache needs `AllowOverride All`** to read and apply `.htaccess` rules
4. **Without it**, Apache ignores `.htaccess` and all non-root pages return 404

### Technical Explanation:
```
User Request: /timona/ban-giam-hieu-timona/
         ↓
Apache checks .htaccess for rewrite rules
         ↓
Without AllowOverride: .htaccess IGNORED → 404 Error
With AllowOverride All: .htaccess APPLIED → WordPress loads page
```

## ✅ Solution Implemented

### 1. **Apache Configuration Update**
**File**: `/etc/apache2/sites-available/000-default.conf`

**Added**:
```apache
<Directory /var/www/html>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

### 2. **Apache Service Restart**
```bash
sudo systemctl restart apache2
```

### 3. **WordPress Permalinks Flushed**
```bash
php -r "
require_once('wp-load.php');
flush_rewrite_rules(true);
"
```

## 📊 Verification Results

### Before Fix:
- ❌ `http://localhost/timona/ban-giam-hieu-timona/` → 404 Not Found
- ❌ `.htaccess` rules being ignored
- ❌ AllowOverride not set in Apache config

### After Fix:
- ✅ `http://localhost/timona/ban-giam-hieu-timona/` → HTTP 200 OK
- ✅ `.htaccess` rules being applied
- ✅ AllowOverride All set correctly
- ✅ All WordPress pages accessible

## 🔧 Files Modified

### 1. Apache Configuration
**File**: `/etc/apache2/sites-available/000-default.conf`
**Backup**: `/etc/apache2/sites-available/000-default.conf.backup`

**Complete Configuration**:
```apache
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/html

    # Enable .htaccess overrides for WordPress
    <Directory /var/www/html>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

### 2. WordPress .htaccess
**File**: `/mnt/chikiet/webseo/timona/.htaccess`
**Status**: ✅ Existing rules preserved

**Content**:
```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /timona/
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /timona/index.php [L]
</IfModule>
# END WordPress
```

## 🎯 Fix Scripts Created

### 1. `fix-404-error.php`
- Web-based diagnostic tool
- Checks `.htaccess`, permalinks, Apache config
- Shows current rewrite rules
- Manual flush option

### 2. `fix-404.sh`
- Automated diagnostic and fix script
- Checks all common 404 causes
- Flushes WordPress permalinks
- Verifies URL accessibility

### 3. `fix-apache-allowoverride.sh`
- Backs up Apache config
- Adds Directory directive with AllowOverride All
- Tests config syntax
- Restarts Apache
- Verifies fix worked

## 🛡️ Prevention Measures

### For Future WordPress Installations:

1. **Always set AllowOverride in Apache config**:
```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

2. **Enable mod_rewrite**:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

3. **Ensure .htaccess is writable**:
```bash
chmod 644 .htaccess
```

4. **Flush permalinks after changes**:
- Via Admin: Settings → Permalinks → Save Changes
- Via CLI: `php -r "require 'wp-load.php'; flush_rewrite_rules();"`

## 📈 Common Causes of 404 Errors

### 1. **Missing AllowOverride** ← THIS WAS THE ISSUE
- Apache ignores `.htaccess`
- All non-root pages return 404

### 2. **mod_rewrite Not Enabled**
- Rewrite rules don't work
- Enable with: `sudo a2enmod rewrite`

### 3. **Incorrect .htaccess**
- Wrong RewriteBase
- Missing rewrite rules
- Fix by flushing permalinks

### 4. **Permalink Structure Not Set**
- WordPress using default `/?p=123`
- Set in Settings → Permalinks

### 5. **Page/Post Doesn't Exist**
- Slug is wrong
- Post is draft or trash
- Check in WordPress admin

## 🔍 Troubleshooting Guide

### If 404 Errors Persist:

#### Step 1: Check Apache Config
```bash
sudo cat /etc/apache2/sites-available/000-default.conf
# Look for: AllowOverride All
```

#### Step 2: Check mod_rewrite
```bash
apache2ctl -M | grep rewrite
# Should show: rewrite_module (shared)
```

#### Step 3: Check .htaccess
```bash
cat .htaccess
# Should have WordPress rewrite rules
```

#### Step 4: Check Permissions
```bash
ls -la .htaccess
# Should be readable: -rw-r--r--
```

#### Step 5: Flush Permalinks
```bash
# Via WordPress Admin
# Go to: Settings → Permalinks → Save Changes

# Or via command line
php -r "require 'wp-load.php'; flush_rewrite_rules();"
```

#### Step 6: Restart Apache
```bash
sudo systemctl restart apache2
```

## 🎨 User Experience

### Before Fix:
- ❌ All WordPress pages except homepage showed 404
- ❌ Frustrating user experience
- ❌ SEO issues (broken links)
- ❌ Cannot access any content pages

### After Fix:
- ✅ All WordPress pages accessible
- ✅ Pretty permalinks working
- ✅ Normal WordPress navigation
- ✅ SEO-friendly URLs functional

## 📊 Impact Assessment

### Pages Affected:
- ✅ `http://localhost/timona/ban-giam-hieu-timona/`
- ✅ All other WordPress pages
- ✅ All custom post types
- ✅ All category/tag archives

### Services Restored:
- ✅ WordPress permalink system
- ✅ `.htaccess` rewrite rules
- ✅ Apache URL rewriting
- ✅ Complete website navigation

## 🔐 Security Considerations

### AllowOverride All:
- ✅ **Safe for WordPress** - Required for proper functioning
- ✅ **Production Ready** - Standard WordPress configuration
- ⚠️ **Note**: Only allows `.htaccess` in specified directory

### Best Practices:
1. Keep `.htaccess` rules minimal
2. Don't allow overrides in sensitive directories
3. Regularly review `.htaccess` content
4. Monitor for unauthorized changes

---

## 🎯 SUMMARY:

**Issue**: WordPress 404 errors due to Apache missing AllowOverride  
**Root Cause**: Apache config lacked Directory directive with AllowOverride All  
**Solution**: Added Directory directive, restarted Apache, flushed permalinks  
**Result**: ✅ All WordPress pages now accessible  
**Status**: ✅ **FULLY RESOLVED**

---

**Date**: October 18, 2025  
**Issue**: 404 Not Found on WordPress pages  
**Root Cause**: Apache AllowOverride not set  
**Resolution**: Updated Apache config with AllowOverride All  
**Status**: ✅ **COMPLETE**