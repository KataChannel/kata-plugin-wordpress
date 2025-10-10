# 🚀 QUICK START - Website Timona Restored

## ✅ Database đã được restore thành công!

### 📊 Thống kê nội dung

- ✅ **527 bài viết** đã published
- ✅ **57 trang** đã published  
- ✅ **5,147 hình ảnh** (attachments)
- ✅ **3 users** (quanly, linh.seo, thao.seo)
- ✅ **26 contact forms**
- ✅ **53 menu items**

### 🌐 Truy cập website

**Website:** http://localhost/timona  
**Admin:** http://localhost/timona/wp-admin

### 👤 Thông tin đăng nhập

**Users có sẵn:**
1. **quanly** - chikiet88@gmail.com
2. **linh.seo** - phuonglinh01051@gmail.com
3. **thao.seo** - buitruongbichthao0807@gmail.com

*(Mật khẩu: Cần liên hệ admin hoặc reset password)*

### 🔑 Reset password (nếu cần)

```bash
# Reset password cho user quanly
mysql -u timona_user -p'Timona@2025!Strong' -D tazaspac_wp_timona -e "UPDATE gt_users SET user_pass = MD5('newpassword123') WHERE user_login = 'quanly';"
```

**Hoặc dùng WP-CLI:**
```bash
wp user update quanly --user_pass=newpassword123 --allow-root
```

### 🔧 Các bước tiếp theo

#### 1. Flush permalinks
Vào: **Settings > Permalinks** → Click "Save Changes"

#### 2. Kiểm tra plugins
Vào: **Plugins** → Kiểm tra plugins active/inactive

#### 3. Clear cache
```bash
cd /mnt/chikiet/webseo/timona
rm -rf wp-content/cache/*
```

#### 4. Kiểm tra themes
Vào: **Appearance > Themes** → Activate theme cần dùng

#### 5. Test website
- ✅ Trang chủ
- ✅ Bài viết
- ✅ Hình ảnh hiển thị
- ✅ Menu navigation
- ✅ Contact forms

### 📝 Plugins quan trọng

Database chứa các plugins:
- KATA SEO Manager (kata-seo-manager)
- Contact Form 7 (wpcf7_contact_form)
- Really Simple SSL
- iThemes Security
- WP Cache

### ⚠️ Lưu ý

1. **URL đã được update:** `http://localhost/timona`
2. **Database:** tazaspac_wp_timona (65 tables)
3. **Charset:** utf8mb4_unicode_ci
4. **Backup file:** tazaspac_wp_timona_09102025.sql (83 MB)

### 🐛 Troubleshooting

#### Website không hiển thị?
```bash
# Kiểm tra Apache/Nginx đang chạy
sudo systemctl status apache2  # hoặc nginx

# Kiểm tra file .htaccess
cd /mnt/chikiet/webseo/timona
cat .htaccess
```

#### Hình ảnh không hiển thị?
```bash
# Kiểm tra quyền thư mục uploads
chmod -R 755 wp-content/uploads
chown -R www-data:www-data wp-content/uploads
```

#### Database connection error?
```bash
# Kiểm tra MySQL đang chạy
sudo systemctl status mysql

# Test connection
mysql -u timona_user -p'Timona@2025!Strong' -e "SHOW DATABASES;"
```

### 📞 Support

Nếu gặp vấn đề, kiểm tra:
1. **Database log:** `tail -f /var/log/mysql/error.log`
2. **WordPress debug:** Enable WP_DEBUG trong wp-config.php
3. **Apache/Nginx log:** `tail -f /var/log/apache2/error.log`

---

**Restore completed:** 2025-10-10  
**Status:** ✅ READY TO USE  
**Document:** DATABASE_RESTORE_SUMMARY.md
