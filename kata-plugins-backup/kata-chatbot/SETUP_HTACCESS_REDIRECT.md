# Hướng dẫn cài đặt .htaccess Redirect

## Tổng quan
File `.htaccess-admin-redirect` chứa các rewrite rules để tự động chuyển hướng các URL admin sai sang định dạng đúng.

## Cách 1: Thêm vào file .htaccess chính (Khuyến nghị)

### Bước 1: Backup file .htaccess hiện tại
```bash
cp /mnt/chikiet/webseo/timona/.htaccess /mnt/chikiet/webseo/timona/.htaccess.backup
```

### Bước 2: Thêm rules vào cuối file .htaccess
```bash
cat wp-content/plugins/kata-chatbot/.htaccess-admin-redirect >> .htaccess
```

### Bước 3: Kiểm tra file .htaccess
```bash
tail -20 .htaccess
```

## Cách 2: Tạo .htaccess trong thư mục wp-admin

### Bước 1: Copy file vào wp-admin
```bash
cp wp-content/plugins/kata-chatbot/.htaccess-admin-redirect wp-admin/.htaccess
```

### Bước 2: Kiểm tra file đã được tạo
```bash
cat wp-admin/.htaccess
```

## Cách 3: Thêm thủ công (Tùy chỉnh)

Mở file `.htaccess` và thêm đoạn code sau:

```apache
# KATA Chatbot Admin URL Redirect Rules
<IfModule mod_rewrite.c>
RewriteEngine On

# Redirect incorrect Smart Chatbot admin URLs to correct format
RewriteCond %{REQUEST_URI} ^/wp-admin/kata-smart-chatbot/?$ [NC]
RewriteRule ^(.*)$ /wp-admin/admin.php?page=kata-smart-chatbot [R=301,L]

RewriteCond %{REQUEST_URI} ^/wp-admin/kata-chatbot-logs/?$ [NC]
RewriteRule ^(.*)$ /wp-admin/admin.php?page=kata-chatbot-logs [R=301,L]

RewriteCond %{REQUEST_URI} ^/wp-admin/kata-chatbot-leads/?$ [NC]
RewriteRule ^(.*)$ /wp-admin/admin.php?page=kata-chatbot-leads [R=301,L]
</IfModule>
```

## Kiểm tra hoạt động

### Test 1: URL sai tự động redirect
```
http://localhost/timona/wp-admin/kata-smart-chatbot
→ http://localhost/timona/wp-admin/admin.php?page=kata-smart-chatbot
```

### Test 2: URL sai với trailing slash
```
http://localhost/timona/wp-admin/kata-chatbot-logs/
→ http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-logs
```

### Test 3: Kiểm tra status code
```bash
curl -I http://localhost/timona/wp-admin/kata-smart-chatbot
# Phải trả về: HTTP/1.1 301 Moved Permanently
```

## Lưu ý quan trọng

### 1. Yêu cầu mod_rewrite
- Server phải bật module `mod_rewrite`
- Kiểm tra: `apache2ctl -M | grep rewrite`
- Nếu chưa bật: `sudo a2enmod rewrite && sudo systemctl restart apache2`

### 2. AllowOverride
- File `.htaccess` chỉ hoạt động khi `AllowOverride All` được cấu hình
- Kiểm tra trong `/etc/apache2/sites-available/000-default.conf`:
```apache
<Directory /var/www/html>
    AllowOverride All
</Directory>
```

### 3. Ưu tiên rules
- Rules được đọc từ trên xuống
- Đặt KATA Chatbot rules TRƯỚC các rules khác
- Rule đầu tiên match sẽ được thực thi

### 4. Cache
- Sau khi thêm rules, clear browser cache
- Test với Incognito/Private mode
- Hoặc dùng `Ctrl+Shift+R` để hard reload

## Troubleshooting

### Lỗi: 500 Internal Server Error
**Nguyên nhân**: Syntax error trong .htaccess
**Giải pháp**:
```bash
# Kiểm tra log
tail -f /var/log/apache2/error.log

# Restore backup
cp .htaccess.backup .htaccess
```

### Lỗi: Redirect không hoạt động
**Nguyên nhân**: mod_rewrite chưa được bật
**Giải pháp**:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Lỗi: Redirect loop
**Nguyên nhân**: Rules conflict
**Giải pháp**: Kiểm tra không có rules trùng lặp cho cùng một URL pattern

## Alternative: PHP Redirect (Không cần .htaccess)

Nếu không thể sử dụng `.htaccess`, dùng file `admin-url-redirect.php`:

1. File này tự động detect URL sai
2. Hiển thị thông báo friendly
3. Redirect sau 3 giây
4. Không cần cấu hình server

## URL chính xác (Không cần redirect)

### ✅ Cách tốt nhất: Dùng menu WordPress
- Vào **Kata Chatbot** trong admin sidebar
- Click vào submenu: **Smart Chatbot**, **Chat Logs**, **Leads**

### ✅ Cách 2: Dùng URL đúng format
```
http://localhost/timona/wp-admin/admin.php?page=kata-smart-chatbot
http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-logs
http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-leads
```

## Tham khảo

- [Apache mod_rewrite Documentation](https://httpd.apache.org/docs/current/mod/mod_rewrite.html)
- [WordPress .htaccess Guide](https://wordpress.org/support/article/htaccess/)
- [URL Rewriting Best Practices](https://httpd.apache.org/docs/current/rewrite/)

---

**Tạo ngày**: 2025-10-16  
**Phiên bản**: 1.0.0  
**Tác giả**: KATA Development Team
