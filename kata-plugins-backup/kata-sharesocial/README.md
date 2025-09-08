# Kata ShareSocial Plugin

Plugin WordPress hỗ trợ chia sẻ bài viết lên các nền tảng mạng xã hội một cách dễ dàng và hiệu quả.

## Tính năng chính

### 🚀 Nền tảng mạng xã hội hỗ trợ
- Facebook
- Twitter
- LinkedIn  
- Pinterest
- Telegram
- WhatsApp
- Reddit
- Tumblr
- Email
- Copy Link

### 📊 Thống kê và phân tích
- Theo dõi số lượng chia sẻ theo từng nền tảng
- Báo cáo chi tiết về hiệu suất chia sẻ
- Phân tích xu hướng theo thời gian
- Thống kê thiết bị và trình duyệt

### 🎨 Tùy chỉnh giao diện
- 3 kiểu nút: Rounded, Square, Circle
- 3 kích thước: Small, Medium, Large
- Hiển thị số lượng chia sẻ
- CSS tùy chỉnh

### ⚙️ Cài đặt linh hoạt
- Tự động thêm nút chia sẻ vào bài viết
- Chọn vị trí hiển thị (trước/sau/cả hai)
- Loại trừ post types không mong muốn
- Widget hỗ trợ sidebar

## Cài đặt

1. Upload thư mục `kata-sharesocial` vào `/wp-content/plugins/`
2. Kích hoạt plugin trong WordPress Admin
3. Vào **ShareSocial > Settings** để cấu hình

## Sử dụng

### Shortcode
```php
[kata_share_buttons]
```

Với tham số tùy chỉnh:
```php
[kata_share_buttons platforms="facebook,twitter,linkedin" style="rounded" size="medium" show_count="true"]
```

### PHP Code
```php
// Hiển thị nút chia sẻ cho bài viết hiện tại
$frontend = KataShareSocial_Frontend::get_instance();
echo $frontend->render_share_buttons();

// Hiển thị cho bài viết cụ thể
echo $frontend->render_share_buttons(array(
    'post_id' => 123,
    'platforms' => array('facebook', 'twitter'),
    'style' => 'circle',
    'size' => 'large',
    'show_count' => false
));
```

### Widget
- Vào **Appearance > Widgets**
- Thêm widget "Kata Share Buttons" vào sidebar
- Cấu hình các tùy chọn

## Cấu hình

### Nền tảng mạng xã hội
- Chọn các nền tảng muốn hiển thị
- Sắp xếp thứ tự hiển thị

### Giao diện
- **Button Style**: Rounded, Square, Circle
- **Button Size**: Small, Medium, Large  
- **Show Count**: Hiển thị số lượng chia sẻ

### Hiển thị
- **Auto Add Buttons**: Tự động thêm nút vào bài viết
- **Button Position**: Vị trí hiển thị (Before/After/Both)
- **Exclude Post Types**: Loại trừ post types

### Analytics
- **Enable Analytics**: Bật/tắt theo dõi thống kê
- Xem báo cáo chi tiết trong **ShareSocial > Analytics**

## Hooks và Filters

### Actions
```php
// Sau khi plugin được khởi tạo
do_action('kata_sharesocial_init');

// Sau khi chia sẻ được theo dõi
do_action('kata_sharesocial_share_tracked', $post_id, $platform);
```

### Filters
```php
// Tùy chỉnh danh sách nền tảng
add_filter('kata_sharesocial_platforms', function($platforms) {
    // Thêm nền tảng tùy chỉnh
    $platforms['custom'] = array(
        'name' => 'Custom Platform',
        'icon' => 'fas fa-share',
        'color' => '#000000',
        'url_template' => 'https://example.com/share?url={{url}}'
    );
    return $platforms;
});

// Tùy chỉnh URL chia sẻ
add_filter('kata_sharesocial_share_url', function($url, $platform, $post_id) {
    // Thêm tham số tracking
    return add_query_arg('utm_source', $platform, $url);
}, 10, 3);

// Tùy chỉnh HTML nút chia sẻ
add_filter('kata_sharesocial_button_html', function($html, $platform, $args) {
    // Tùy chỉnh HTML
    return $html;
}, 10, 3);
```

## CSS Classes

### Container
- `.kata-share-buttons` - Container chính
- `.kata-share-buttons-list` - Danh sách nút
- `.kata-share-total` - Hiển thị tổng số chia sẻ

### Styles
- `.kata-style-rounded` - Nút bo góc
- `.kata-style-square` - Nút vuông
- `.kata-style-circle` - Nút tròn

### Sizes  
- `.kata-size-small` - Kích thước nhỏ
- `.kata-size-medium` - Kích thước trung bình
- `.kata-size-large` - Kích thước lớn

### Platforms
- `.kata-platform-facebook` - Nút Facebook
- `.kata-platform-twitter` - Nút Twitter
- `.kata-platform-linkedin` - Nút LinkedIn
- v.v.

## Tùy chỉnh CSS

```css
/* Tùy chỉnh màu nút */
.kata-platform-facebook {
    background-color: #1877f2 !important;
}

/* Tùy chỉnh kích thước */
.kata-size-custom .kata-share-button {
    padding: 15px 20px;
    font-size: 16px;
}

/* Responsive */
@media (max-width: 768px) {
    .kata-share-buttons {
        flex-direction: column;
        gap: 10px;
    }
}
```

## Database Tables

Plugin tạo 2 bảng trong database:

### wp_kata_share_stats
Lưu trữ thống kê chia sẻ:
- `post_id` - ID bài viết
- `platform` - Nền tảng mạng xã hội
- `share_count` - Số lượng chia sẻ
- `last_shared` - Lần chia sẻ cuối
- `created_at` - Ngày tạo
- `updated_at` - Ngày cập nhật

### wp_kata_share_events (optional)
Lưu trữ sự kiện chia sẻ chi tiết:
- `post_id` - ID bài viết  
- `platform` - Nền tảng
- `user_agent` - Thông tin trình duyệt
- `ip_address` - Địa chỉ IP
- `shared_at` - Thời gian chia sẻ

## Performance

### Caching
- Plugin sử dụng WordPress Object Cache
- Cache thống kê trong 1 giờ
- Tự động xóa cache khi có chia sẻ mới

### Optimization
- CSS và JS được minify trong production
- Lazy load cho analytics scripts
- Database queries được optimize

## Bảo mật

- Xác thực nonce cho tất cả AJAX requests
- Sanitize và validate dữ liệu đầu vào
- Escape output để tránh XSS
- Capability checks cho admin functions

## Hỗ trợ

- Email: support@timona.vn
- Documentation: https://timona.vn/docs/kata-sharesocial
- GitHub: https://github.com/timona/kata-sharesocial

## Changelog

### Version 1.0.0
- Phiên bản đầu tiên
- Hỗ trợ 10+ nền tảng mạng xã hội
- Analytics và reporting
- Widget và shortcode support
- Admin dashboard

## License

GPL v2 or later
