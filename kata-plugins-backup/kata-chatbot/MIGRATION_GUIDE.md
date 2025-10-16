# 🔄 KATA Chatbot Migration Guide

**Date:** October 16, 2025  
**Version:** 1.1.0  
**Status:** ✅ Migration Complete

---

## 📋 Tổng quan

Tài liệu này hướng dẫn quá trình di chuyển (migration) chức năng Smart Chatbot từ **KATA SEO Manager** plugin sang **KATA Chatbot** plugin độc lập.

### Lý do di chuyển

- ✅ **Tách biệt chức năng**: Chatbot là một tính năng độc lập, không nên tích hợp trong plugin SEO
- ✅ **Dễ bảo trì**: Mỗi plugin tập trung vào một nhiệm vụ cụ thể
- ✅ **Linh hoạt hơn**: Người dùng có thể chọn sử dụng chatbot mà không cần cài plugin SEO
- ✅ **Cập nhật độc lập**: Có thể cập nhật chatbot mà không ảnh hưởng đến SEO Manager

---

## 🎯 Những gì đã được di chuyển

### 1. Core Files

#### Class Files
- ✅ `class-smart-chatbot.php` → `class-smart-chatbot-legacy.php`
  - **Từ:** `/kata-seo-manager/includes/`
  - **Đến:** `/kata-chatbot/includes/`
  - **Thay đổi:** Đổi tên class thành `KATA_Smart_Chatbot_Legacy`

#### Admin Files
- ✅ `chatbot-settings.php` → `chatbot-settings-legacy.php`
- ✅ `chatbot-logs.php` → `chatbot-logs-legacy.php`
- ✅ `chatbot-leads.php` → `chatbot-leads-legacy.php`
  - **Từ:** `/kata-seo-manager/admin/`
  - **Đến:** `/kata-chatbot/admin/`

#### Template Files
- ✅ `chatbot-ui.php` → `chatbot-ui-legacy.php`
  - **Từ:** `/kata-seo-manager/templates/`
  - **Đến:** `/kata-chatbot/templates/`

### 2. Assets (CSS/JS)

- ✅ `smart-chatbot.css`
  - **Từ:** `/kata-seo-manager/assets/css/`
  - **Đến:** `/kata-chatbot/assets/css/`
  
- ✅ `smart-chatbot.js`
  - **Từ:** `/kata-seo-manager/assets/js/`
  - **Đến:** `/kata-chatbot/assets/js/`

### 3. Documentation

- ✅ `CHATBOT_INTEGRATION_COMPLETE.md`
- ✅ `CHATBOT_QUICK_START.md`
- ✅ `KATA_SMART_CHATBOT_GUIDE.md`
  - **Từ:** `/kata-seo-manager/`
  - **Đến:** `/kata-chatbot/`

### 4. Database Tables

Các bảng database vẫn giữ nguyên tên và cấu trúc:

- ✅ `wp_kata_chatbot_logs` - Chat conversation logs
- ✅ `wp_kata_chatbot_leads` - Lead information

---

## 🔧 Thay đổi kỹ thuật

### KATA SEO Manager Plugin

**File:** `kata-seo-manager.php`

**Các thay đổi:**

1. **Xóa require class:**
```php
// Trước:
require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-smart-chatbot.php';

// Sau:
// Chatbot functionality moved to kata-chatbot plugin
// require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-smart-chatbot.php';
```

2. **Xóa chatbot initialization trong activate():**
```php
// Trước:
$chatbot = KATA_Smart_Chatbot::get_instance();
$chatbot->create_tables();

// Sau:
// Chatbot functionality moved to kata-chatbot plugin
// $chatbot = KATA_Smart_Chatbot::get_instance();
// $chatbot->create_tables();
```

3. **Xóa default chatbot options:**
```php
// Trước:
add_option('kata_chatbot_enabled', true);
add_option('kata_chatbot_primary_color', '#042277');
// ...

// Sau:
// Chatbot settings moved to kata-chatbot plugin
// add_option('kata_chatbot_enabled', true);
// ...
```

### KATA Chatbot Plugin

**File:** `kata-chatbot.php`

**Các thay đổi:**

1. **Thêm require class trong load_dependencies():**
```php
$files = array(
    'includes/class-ai-handler.php',
    'includes/class-db-handler.php',
    'includes/class-chat-handler.php',
    'includes/class-branch-handler.php',
    'includes/class-smart-chatbot-legacy.php', // New!
);
```

2. **Thêm initialization trong init_components():**
```php
// Initialize Smart Chatbot Legacy (from kata-seo-manager)
if (class_exists('KATA_Smart_Chatbot_Legacy')) {
    KATA_Smart_Chatbot_Legacy::get_instance();
}
```

3. **Thêm table creation trong activate():**
```php
// Create Smart Chatbot Legacy tables
if (class_exists('KATA_Smart_Chatbot_Legacy')) {
    $legacy_chatbot = KATA_Smart_Chatbot_Legacy::get_instance();
    $legacy_chatbot->create_tables();
}
```

---

## 📦 Hướng dẫn cài đặt sau Migration

### Bước 1: Cập nhật Plugin

1. **Cập nhật KATA SEO Manager:**
   - Đảm bảo plugin đã được cập nhật với code mới (đã xóa chatbot)
   - Version: 2.1.3+

2. **Cập nhật KATA Chatbot:**
   - Đảm bảo plugin đã có code Smart Chatbot Legacy
   - Version: 1.1.0+

### Bước 2: Kiểm tra Database

Các bảng database chatbot vẫn còn nguyên:

```sql
-- Kiểm tra bảng chat logs
SELECT * FROM wp_kata_chatbot_logs LIMIT 5;

-- Kiểm tra bảng leads
SELECT * FROM wp_kata_chatbot_leads LIMIT 5;
```

### Bước 3: Kiểm tra Settings

Các options WordPress vẫn được giữ nguyên:

- `kata_chatbot_enabled`
- `kata_chatbot_auto_open`
- `kata_chatbot_primary_color`
- `kata_chatbot_secondary_color`
- `kata_chatbot_welcome_message`
- `kata_chatbot_bot_name`
- v.v...

### Bước 4: Test Chatbot

1. Truy cập frontend website
2. Kiểm tra chatbot hiển thị ở góc màn hình
3. Test gửi tin nhắn
4. Kiểm tra lead capture form
5. Xem chat logs trong admin

---

## 🎨 Cấu trúc Admin Menu

### Trước Migration (KATA SEO Manager)

```
KATA SEO
├── Bảng điều khiển
├── Schema Types
├── Statistics
├── Smart Chatbot      ← Chatbot Settings
├── Chat Logs          ← Chat Logs
└── Leads              ← Chatbot Leads
```

### Sau Migration

**KATA SEO Manager:**
```
KATA SEO
├── Bảng điều khiển
├── Schema Types
└── Statistics
```

**KATA Chatbot:**
```
KATA Chatbot
├── Dashboard
├── Conversations
├── Branches
├── Knowledge Base
├── Analytics
├── Settings
├── Smart Chatbot      ← Legacy Smart Chatbot
├── Chat Logs          ← Legacy Chat Logs
└── Leads              ← Legacy Leads
```

---

## ⚙️ Tính năng Smart Chatbot Legacy

Tất cả tính năng từ KATA SEO Manager đều được giữ nguyên:

### 1. Content Detection
- ✅ Phát hiện keywords trong bài viết
- ✅ Nhận diện Schema Types (Course, FAQ, Article...)
- ✅ Theo dõi scroll depth
- ✅ Hiểu context từ categories & tags

### 2. Smart Triggers
- ✅ Time-based: Tự động mở sau X giây
- ✅ Scroll-based: Mở khi scroll đến Y%
- ✅ Exit-intent: Mở khi chuột ra ngoài
- ✅ Keyword-based: Kích hoạt theo từ khóa

### 3. Contextual Messages
- ✅ Course-related messages
- ✅ FAQ-related messages
- ✅ Article-related messages
- ✅ Exit intent messages

### 4. Lead Capture
- ✅ Form thu thập thông tin
- ✅ Email notification
- ✅ Lead status management
- ✅ Source tracking

### 5. Admin Features
- ✅ Settings page với 6 tabs
- ✅ Chat logs viewer
- ✅ Leads management
- ✅ Statistics dashboard

---

## 🔐 Backward Compatibility

### Database
- ✅ Tất cả bảng database giữ nguyên tên
- ✅ Không cần migrate data
- ✅ Không mất dữ liệu chat logs hay leads

### Settings
- ✅ Tất cả WordPress options giữ nguyên
- ✅ Không cần cấu hình lại
- ✅ Settings sẽ tự động load

### Frontend
- ✅ Chatbot UI hiển thị giống y hệt
- ✅ JavaScript behavior không đổi
- ✅ CSS styling giữ nguyên

---

## 🚨 Troubleshooting

### Chatbot không hiển thị?

**Kiểm tra:**

1. Plugin KATA Chatbot đã được activate chưa?
2. Option `kata_chatbot_enabled` có giá trị `true`?
3. Có lỗi JavaScript trong console không?
4. File `smart-chatbot.js` đã được load chưa?

**Fix:**
```php
// Kích hoạt lại chatbot
update_option('kata_chatbot_enabled', true);

// Clear cache
wp_cache_flush();
```

### Menu Smart Chatbot không xuất hiện?

**Kiểm tra:**

1. File `class-smart-chatbot-legacy.php` có tồn tại?
2. Class `KATA_Smart_Chatbot_Legacy` đã được load?
3. Hook `admin_menu` có được gọi?

**Fix:**
```php
// Kiểm tra class
if (class_exists('KATA_Smart_Chatbot_Legacy')) {
    echo 'Class exists!';
} else {
    echo 'Class NOT loaded!';
}
```

### Chat logs không lưu?

**Kiểm tra:**

1. Bảng `wp_kata_chatbot_logs` có tồn tại?
2. AJAX endpoint có hoạt động?
3. Nonce verification có pass?

**Fix:**
```sql
-- Tạo lại bảng nếu cần
CREATE TABLE IF NOT EXISTS wp_kata_chatbot_logs (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    session_id varchar(100) NOT NULL,
    -- ... (xem cấu trúc trong class-smart-chatbot-legacy.php)
);
```

---

## 📝 Best Practices

### 1. Backup trước khi migrate
```bash
# Backup database
mysqldump -u user -p database > backup_before_migration.sql

# Backup files
tar -czf kata-plugins-backup.tar.gz wp-content/plugins/kata-*
```

### 2. Test trên staging trước
- Không test trực tiếp trên production
- Dùng environment giống production
- Test đầy đủ các tính năng

### 3. Monitor sau khi migrate
- Kiểm tra error logs
- Theo dõi chat logs
- Xem có leads mới không
- Test performance

### 4. Giữ lại backup
- Backup database ít nhất 30 ngày
- Backup code trước migration
- Document các thay đổi

---

## 🎯 Roadmap

### Phase 1: Migration (✅ Completed)
- ✅ Di chuyển code sang kata-chatbot
- ✅ Xóa code khỏi kata-seo-manager
- ✅ Test backward compatibility
- ✅ Tạo documentation

### Phase 2: Modernization (🔄 In Progress)
- 🔄 Refactor Smart Chatbot Legacy
- 🔄 Tích hợp với AI Handler mới
- 🔄 Unified admin interface
- 🔄 Improve performance

### Phase 3: Enhancement (📅 Planned)
- 📅 Add more AI models
- 📅 Multi-language support
- 📅 Advanced analytics
- 📅 Webhook integrations

---

## 📞 Support

Nếu bạn gặp vấn đề trong quá trình migration:

1. **Kiểm tra documentation này**
2. **Xem error logs:** `/wp-content/debug.log`
3. **Check browser console** cho JavaScript errors
4. **Contact support:** support@timona.edu.vn

---

## ✅ Checklist Sau Migration

- [ ] KATA SEO Manager đã được cập nhật
- [ ] KATA Chatbot đã được cập nhật và activated
- [ ] Database tables tồn tại và có dữ liệu
- [ ] Settings được giữ nguyên
- [ ] Chatbot hiển thị trên frontend
- [ ] Chat logs được lưu
- [ ] Lead capture hoạt động
- [ ] Admin menus hiển thị đúng
- [ ] No JavaScript errors
- [ ] No PHP errors
- [ ] Performance OK
- [ ] Backup completed

---

**Migration completed successfully! 🎉**

*Date: October 16, 2025*  
*Version: 1.1.0*  
*Status: Production Ready*
