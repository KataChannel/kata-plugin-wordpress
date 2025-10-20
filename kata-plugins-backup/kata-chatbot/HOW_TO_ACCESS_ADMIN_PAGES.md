# 📌 Hướng dẫn Truy cập Smart Chatbot Admin Pages

## ⚠️ Lỗi URL Phổ biến

### ❌ URL SAI (sẽ không hoạt động):
```
http://localhost/timona/wp-admin/kata-smart-chatbot
http://localhost/timona/wp-admin/kata-chatbot-logs
http://localhost/timona/wp-admin/kata-chatbot-leads
```

### ✅ URL ĐÚNG:
```
http://localhost/timona/wp-admin/admin.php?page=kata-smart-chatbot
http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-logs
http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-leads
```

---

## 🎯 Cách truy cập ĐÚNG

### Cách 1: Qua WordPress Admin Menu (KHUYẾN NGHỊ)

1. Đăng nhập WordPress Admin
2. Vào menu **KATA Chatbot** (sidebar trái)
3. Click vào submenu:
   - **Smart Chatbot** - Cài đặt Smart Chatbot
   - **Chat Logs** - Xem lịch sử chat
   - **Leads** - Quản lý leads

### Cách 2: Truy cập trực tiếp qua URL

#### Smart Chatbot Settings
```
http://localhost/timona/wp-admin/admin.php?page=kata-smart-chatbot
```

#### Chat Logs
```
http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-logs
```

#### Chatbot Leads
```
http://localhost/timona/wp-admin/admin.php?page=kata-chatbot-leads
```

---

## 🔧 Troubleshooting

### Nếu trang hiển thị trống hoặc lỗi:

#### 1. Kiểm tra Plugin đã kích hoạt
```
WordPress Admin → Plugins → KATA Chatbot → Ensure it's Activated
```

#### 2. Kiểm tra User Permissions
Bạn cần quyền **Administrator** hoặc **manage_options** capability.

#### 3. Kiểm tra Template Files
Chạy debug script:
```
http://localhost/timona/wp-content/plugins/kata-chatbot/test-smart-chatbot-debug.php
```

#### 4. Kiểm tra Error Logs
```bash
# Enable debug mode trong wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

# Xem error log
tail -f wp-content/debug.log
```

#### 5. Clear Cache
```bash
# Clear WordPress object cache
wp cache flush

# Clear browser cache
Ctrl + Shift + R (hoặc Cmd + Shift + R trên Mac)
```

---

## 📋 Menu Structure

```
KATA Chatbot (Main Menu)
├── Tổng quan (Dashboard)
├── Cuộc trò chuyện (Conversations)
├── Thống kê (Analytics)
├── Cơ sở tri thức (Knowledge Base)
├── Chi nhánh (Branches)
├── Cài đặt (Settings)
├── Smart Chatbot ← Legacy Smart Chatbot Settings
├── Chat Logs ← Legacy Chat Logs
└── Leads ← Legacy Leads Management
```

---

## 🔗 URL Reference

### Main Plugin URLs
- Dashboard: `admin.php?page=kata-chatbot`
- Conversations: `admin.php?page=kata-chatbot-conversations`
- Analytics: `admin.php?page=kata-chatbot-analytics`
- Knowledge: `admin.php?page=kata-chatbot-knowledge`
- Branches: `admin.php?page=kata-chatbot-branches`
- Settings: `admin.php?page=kata-chatbot-settings`

### Legacy Smart Chatbot URLs
- Smart Chatbot: `admin.php?page=kata-smart-chatbot`
- Chat Logs: `admin.php?page=kata-chatbot-logs`
- Leads: `admin.php?page=kata-chatbot-leads`

---

## 💡 Quick Links

### Production Site
Nếu site của bạn là `https://timona.vn`, URL sẽ là:
```
https://timona.vn/wp-admin/admin.php?page=kata-smart-chatbot
https://timona.vn/wp-admin/admin.php?page=kata-chatbot-logs
https://timona.vn/wp-admin/admin.php?page=kata-chatbot-leads
```

### Local Development
Localhost với port:
```
http://localhost:8080/timona/wp-admin/admin.php?page=kata-smart-chatbot
```

---

## 🐛 Common Errors

### Error 1: Page Not Found
**Nguyên nhân:** URL không đúng format WordPress admin menu

**Giải pháp:** Sử dụng `admin.php?page=` thay vì direct URL

### Error 2: Permission Denied
**Nguyên nhân:** User không có quyền `manage_options`

**Giải pháp:** Đăng nhập với tài khoản Administrator

### Error 3: Blank Page
**Nguyên nhân:** PHP error trong template file

**Giải pháp:** 
1. Enable WP_DEBUG
2. Check debug.log
3. Chạy test-smart-chatbot-debug.php

### Error 4: Menu Không Xuất hiện
**Nguyên nhân:** Class chưa được khởi tạo

**Giải pháp:**
1. Kiểm tra plugin activated
2. Check class-smart-chatbot-legacy.php loaded
3. Verify init_hooks() được gọi

---

## ✅ Checklist

Trước khi truy cập admin pages, đảm bảo:

- [ ] Plugin KATA Chatbot đã được activated
- [ ] Bạn đã đăng nhập với quyền Administrator
- [ ] URL có format: `admin.php?page=kata-smart-chatbot`
- [ ] Template files tồn tại trong thư mục admin/
- [ ] Database tables đã được tạo (run activation)
- [ ] Không có PHP errors trong debug.log

---

## 📞 Support

Nếu vẫn gặp vấn đề:

1. **Check Documentation:** Xem BUGFIX_SMART_CHATBOT_MENUS.md
2. **Run Debug Script:** test-smart-chatbot-debug.php
3. **Check Logs:** wp-content/debug.log
4. **Contact Support:** support@timona.edu.vn

---

## 🎓 Ví dụ Thực tế

### Scenario 1: Truy cập Settings từ Menu
```
1. Login → WordPress Admin
2. Sidebar → KATA Chatbot
3. Click → Smart Chatbot
4. ✅ URL tự động: admin.php?page=kata-smart-chatbot
```

### Scenario 2: Bookmark URL
```
Lưu bookmark với URL đầy đủ:
http://localhost/timona/wp-admin/admin.php?page=kata-smart-chatbot
```

### Scenario 3: Tạo Direct Link
```html
<a href="<?php echo admin_url('admin.php?page=kata-smart-chatbot'); ?>">
    Smart Chatbot Settings
</a>
```

### Scenario 4: Redirect trong PHP
```php
wp_redirect(admin_url('admin.php?page=kata-smart-chatbot'));
exit;
```

---

**Cập nhật:** October 16, 2025  
**Version:** 1.1.1  
**Status:** ✅ Working
