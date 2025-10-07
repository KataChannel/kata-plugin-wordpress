# Hướng Dẫn Cài Đặt Font Exo 2 - Timona Website

## Tổng Quan

Font **Exo 2** đã được tích hợp vào toàn bộ website Timona, bao gồm:
- ✅ Frontend (giao diện người dùng)
- ✅ Backend (Admin dashboard)
- ✅ Tất cả Kata Plugins (SEO Manager, Chatbot, Form, Schema, Poll)
- ✅ Theme Flatsome và Flatsome Child

## Files Đã Thay Đổi

### 1. `/wp-content/themes/flatsome-child/functions.php`

**Chức năng đã thêm:**

```php
// Enqueue Exo 2 Google Font (Frontend)
add_action('wp_enqueue_scripts', 'timona_enqueue_exo2_font');
function timona_enqueue_exo2_font() {
    wp_enqueue_style('exo-2-font', 'https://fonts.googleapis.com/...');
    wp_enqueue_style('timona-exo2-custom', '...timona-exo2-custom.css');
}

// Inline CSS để đảm bảo load sớm
add_action('wp_head', 'timona_add_exo2_inline_css', 1);

// Enqueue cho Admin area
add_action('admin_enqueue_scripts', 'timona_enqueue_exo2_admin');
```

### 2. `/wp-content/themes/flatsome-child/style.css`

**CSS đã thêm:**

```css
/* Apply Exo 2 to all elements */
body, h1, h2, h3, h4, h5, h6, p, a, span, div, li, td, th,
input, textarea, select, button {
    font-family: 'Exo 2', sans-serif !important;
}

/* Heading weights */
h1 { font-weight: 700; }
h2 { font-weight: 600; }
h3 { font-weight: 600; }
```

### 3. `/wp-content/themes/flatsome-child/assets/timona-exo2-custom.css`

**File mới** - CSS custom cho tất cả Kata plugins:
- Kata Wheel
- Kata Form
- Kata Chatbot
- Kata Schema
- Kata Poll
- Kata SEO Quiz
- Modal styles
- Admin styles

## Font Weights Exo 2 Đã Load

Font Exo 2 với đầy đủ weights từ 100-900:
- Thin: 100
- Extra Light: 200
- Light: 300
- Regular: 400 ⭐ (mặc định)
- Medium: 500
- Semi Bold: 600 ⭐ (headings)
- Bold: 700 ⭐ (h1)
- Extra Bold: 800
- Black: 900

Cả phiên bản **normal** và **italic**.

## Cách Font Được Áp Dụng

### Frontend:
1. **Google Font** load qua `wp_enqueue_style()` với preconnect
2. **Inline CSS** trong `<head>` với priority cao (hook priority 1)
3. **Custom CSS file** load sau Google Font

### Backend (Admin):
1. **Google Font** load qua `admin_enqueue_scripts`
2. **Custom CSS** áp dụng cho tất cả Kata plugin admin pages

## Performance Optimization

✅ **Preconnect** đến fonts.googleapis.com và fonts.gstatic.com
✅ **display=swap** để tránh FOIT (Flash of Invisible Text)
✅ **Inline critical CSS** trong head
✅ **!important** để override theme mặc định

## Kiểm Tra Font Đã Load

### 1. Kiểm tra trong Browser DevTools:

```javascript
// Mở Console và chạy:
getComputedStyle(document.body).fontFamily
// Kết quả: "Exo 2", sans-serif
```

### 2. Kiểm tra Network Tab:

Tìm request đến:
```
fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,100;...
```

### 3. Kiểm tra Element:

```javascript
// Inspect bất kỳ element nào
// Trong tab "Computed" → tìm "font-family"
// Giá trị: Exo 2, sans-serif
```

## Xóa Cache

Sau khi cài đặt, cần xóa cache:

### LiteSpeed Cache:
```bash
# Admin Dashboard → LiteSpeed Cache → Toolbox → Purge All
```

### Browser Cache:
```
Ctrl + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)
```

### Theme Cache:
```bash
# WordPress Admin → Appearance → Flatsome → Advanced → Clear Cache
```

## Troubleshooting

### Font không hiển thị:

1. **Xóa cache:**
   - LiteSpeed Cache
   - Browser cache
   - Theme cache

2. **Kiểm tra file tồn tại:**
   ```bash
   ls -la /wp-content/themes/flatsome-child/assets/timona-exo2-custom.css
   ```

3. **Kiểm tra Google Fonts:**
   - Truy cập: https://fonts.google.com/specimen/Exo+2
   - Đảm bảo mạng có thể kết nối

4. **Force reload CSS:**
   - Thêm `?ver=1.0.1` vào URL trong functions.php
   - Tăng version number

### Override không hoạt động:

Nếu theme vẫn dùng font cũ:

```css
/* Thêm vào style.css */
* {
    font-family: 'Exo 2', sans-serif !important;
}
```

## Tích Hợp Với Kata Plugins

Font Exo 2 tự động áp dụng cho:

### Kata SEO Manager:
- ✅ Wheel segments
- ✅ Wheel title
- ✅ Result display
- ✅ Quiz questions
- ✅ Analytics charts

### Kata Chatbot:
- ✅ Chat messages
- ✅ Input field
- ✅ Buttons
- ✅ Header

### Kata Form:
- ✅ Form labels
- ✅ Input fields
- ✅ Buttons
- ✅ Validation messages

### Kata Schema:
- ✅ Schema preview
- ✅ Admin UI

## Maintenance

### Cập nhật font weights:

Nếu cần thêm/bớt weights, edit URL trong `functions.php`:

```php
'https://fonts.googleapis.com/css2?family=Exo+2:ital,wght@0,400;0,600;0,700&display=swap'
```

### Thay đổi font khác:

1. Thay URL Google Font trong `functions.php`
2. Thay `'Exo 2'` thành font mới trong:
   - `style.css`
   - `timona-exo2-custom.css`
   - Inline CSS trong `functions.php`

## Thông Tin Kỹ Thuật

- **Font Family:** Exo 2
- **Font Type:** Sans-serif
- **Weights:** 100-900 (Thin to Black)
- **Styles:** Normal, Italic
- **CDN:** Google Fonts
- **Loading Strategy:** Preload + Font Display Swap
- **Priority:** wp_head hook priority 1

## Tác Giả

- **Developer:** Kata Channel Team
- **Date:** 7 tháng 10, 2025
- **Version:** 1.0.0
- **Website:** timona.edu.vn

## Changelog

### Version 1.0.0 - 2025-10-07
- ✅ Initial implementation
- ✅ Frontend integration
- ✅ Admin integration
- ✅ Kata plugins support
- ✅ Performance optimization
- ✅ Documentation

---

**Lưu ý:** Font Exo 2 là font miễn phí từ Google Fonts, tuân theo Open Font License.
