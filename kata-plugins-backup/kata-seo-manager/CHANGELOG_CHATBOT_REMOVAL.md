# Changelog - KATA SEO Manager

## [2.1.4] - 2025-10-16

### Removed
- **Smart Chatbot functionality** - Di chuyển sang plugin KATA Chatbot
  - Removed `includes/class-smart-chatbot.php` (code commented, file kept for reference)
  - Removed chatbot initialization from `activate()` method
  - Removed chatbot default options setup
  - Removed chatbot admin menus
  
### Changed
- Updated `kata-seo-manager.php` - Commented out chatbot-related code
- Plugin now focuses solely on SEO Schema Management

### Notes
- **Migration:** Nếu bạn đang sử dụng Smart Chatbot, vui lòng cài đặt và kích hoạt plugin **KATA Chatbot v1.1.0+**
- **Backward Compatibility:** Tất cả dữ liệu chatbot (logs, leads, settings) được giữ nguyên
- **No Action Required:** Nếu bạn cập nhật cả KATA Chatbot lên v1.1.0+, chatbot sẽ tự động hoạt động bình thường

### Migration Guide
Xem file `MIGRATION_GUIDE.md` trong plugin KATA Chatbot để biết chi tiết về quá trình migration.

---

## [2.1.3] - 2025-10-13

### Added
- Schema Statistics feature
- User Interactions tracking
- Poll Management
- Wheel Management

### Fixed
- Database connection issues
- Schema output in wp_head
- TinyMCE integration

---

## [2.1.2] - Previous versions...

...
