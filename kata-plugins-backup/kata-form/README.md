# Kata Form Manager (Quản Lý Kata Form)

Plugin WordPress tích hợp với Contact Form 7 để lưu trữ và quản lý dữ liệu form submissions với giao diện tiếng Việt.

## 🚀 Tính năng chính

- **Tích hợp Contact Form 7**: Tự động lưu dữ liệu từ tất cả CF7 forms
- **Quản lý Dữ Liệu Gửi**: Xem, lọc, export dữ liệu submissions  
- **Thống kê chi tiết**: Dashboard với biểu đồ và analytics
- **Export/Import**: Hỗ trợ CSV, JSON formats
- **Hiển thị Frontend**: Shortcode để hiển thị thống kê
- **Responsive Design**: Giao diện tương thích mobile
- **🇻🇳 Tiếng Việt**: Giao diện hoàn toàn tiếng Việt

## 📦 Cài đặt

1. Upload plugin vào thư mục `/wp-content/plugins/kata-form/`
2. Kích hoạt plugin trong WordPress Admin
3. Đảm bảo Contact Form 7 đã được cài đặt và kích hoạt
4. Truy cập menu **"Kata Forms"** trong admin để cấu hình

## 🎯 Cách sử dụng

### Admin Dashboard (Tiếng Việt)
- **Kata Forms > Dữ Liệu Gửi**: Xem và quản lý form submissions
- **Kata Forms > Thống Kê**: Dashboard thống kê với biểu đồ
- **Kata Forms > Cài Đặt**: Cấu hình plugin

### Shortcode Frontend
```
[kata_form_stats]                    // Hiển thị tổng quan
[kata_form_stats form_id="123"]      // Thống kê form cụ thể
[kata_form_stats type="summary"]     // Bao gồm biểu đồ
```

## 🔧 Yêu cầu hệ thống

- WordPress 5.0+
- PHP 7.4+
- Contact Form 7 plugin
- MySQL 5.6+

## 📊 Database

Plugin sẽ tự động tạo bảng `{wp_prefix}kata_form_submissions` để lưu trữ:
- Thông tin form và submission data
- IP address, user agent, referrer
- Trạng thái và timestamps
- User ID (nếu đăng nhập)

## 🎨 Customization

### CSS Classes
- `.kata-stats-grid`: Grid layout cho thống kê
- `.kata-stat-card`: Card hiển thị số liệu
- `.kata-chart-wrapper`: Container cho biểu đồ
- `.status-badge`: Badge trạng thái submissions

### Hooks (cho developers)
- `kata_form_before_save_submission`: Before saving submission
- `kata_form_after_save_submission`: After saving submission
- `kata_form_submission_status_changed`: When status changes

## 🔍 Troubleshooting

### Menu "Kata Forms" không hiển thị
1. Kiểm tra Contact Form 7 đã active chưa
2. Đảm bảo user có quyền `manage_options`
3. Deactivate và reactive plugin
4. Kiểm tra WordPress debug log

### Dữ liệu không được lưu
1. Kiểm tra database permissions
2. Xem WordPress debug log
3. Test với form CF7 đơn giản
4. Kiểm tra hook `wpcf7_mail_sent`

### Export không hoạt động
1. Kiểm tra PHP memory limit
2. Đảm bảo có submissions để export
3. Test với dữ liệu ít hơn
4. Kiểm tra file permissions

## 📝 Changelog

### Version 1.0.0
- Initial release
- Basic CF7 integration
- Admin dashboard
- Statistics and charts
- Export/Import functionality
- Frontend shortcode

## 👥 Support

Để được hỗ trợ:
1. Kiểm tra documentation này
2. Xem WordPress debug log
3. Test với plugin khác bị disable
4. Liên hệ team phát triển

## 📄 License

GPL v2 or later

---

**Note**: Plugin này yêu cầu Contact Form 7 để hoạt động. Đảm bảo CF7 được cài đặt và kích hoạt trước khi sử dụng Kata Form Manager.
