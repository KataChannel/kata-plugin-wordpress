# 📚 Menu Khóa Học - White Background Section

## 🎯 Tổng Quan

CSS file mới được tạo để style cho section **Menu Khóa Học** với:
- ✅ **Nền trắng** (#ffffff) thay vì gradient
- ✅ **Chữ màu primary** (#042277) dễ đọc trên nền trắng
- ✅ **Hover màu secondary** (#040B1E)
- ✅ **Icons và buttons** vẫn giữ gradient đẹp mắt

---

## 📁 Files Đã Tạo/Cập Nhật

### 1. **menu-khoa-hoc.css**
```
Đường dẫn: /wp-content/themes/flatsome-child/assets/css/menu-khoa-hoc.css
Kích thước: ~8KB
Version: 1.0.2
```

**Nội dung:**
- Container styles (nền trắng)
- Menu item styles (chữ màu primary)
- Hover effects (màu secondary)
- Grid layout responsive
- Button styles (gradient)
- List styles
- Icon styles (gradient background)
- Mobile responsive
- Accessibility features

### 2. **functions.php** (Đã cập nhật)
```php
// Thêm enqueue cho menu-khoa-hoc.css
wp_enqueue_style( 
    'timona-menu-khoa-hoc', 
    get_stylesheet_directory_uri() . '/assets/css/menu-khoa-hoc.css',
    array('timona-custom-fonts'),
    '1.0.2'
);
```

### 3. **demo-menu-khoa-hoc.html** (Demo page)
```
Đường dẫn: /timona/demo-menu-khoa-hoc.html
URL: http://localhost/timona/demo-menu-khoa-hoc.html
```

---

## 🎨 Color Scheme

| Element | Color | Code |
|---------|-------|------|
| Background | White | `#ffffff` |
| Text (Normal) | Primary Blue | `#042277` |
| Text (Hover) | Secondary Dark | `#040B1E` |
| Active Border | Primary Blue | `#042277` |
| Icons BG | Gradient | `#042277 → #040B1E` |
| Buttons BG | Gradient | `#042277 → #040B1E` |

---

## 💻 Cách Sử Dụng

### Cách 1: Sử dụng Class

```html
<section class="menu-khoa-hoc">
    <h2 class="section-title">Danh Sách Khóa Học</h2>
    
    <div class="menu-grid">
        <div class="menu-item-card">
            <div class="menu-icon">📊</div>
            <div class="menu-item-title">Marketing Cơ Bản</div>
            <div class="menu-item-description">
                Học các kiến thức nền tảng về marketing digital
            </div>
            <a href="#" class="button">Xem Chi Tiết</a>
        </div>
        
        <!-- More cards... -->
    </div>
</section>
```

### Cách 2: Sử dụng ID

```html
<section id="menu-khoa-hoc">
    <h2>Khóa Học Phổ Biến</h2>
    <ul>
        <li><a href="#">Khóa học SEO Cơ Bản</a></li>
        <li><a href="#">Khóa học Content Writing</a></li>
        <li><a href="#">Khóa học Social Media</a></li>
    </ul>
</section>
```

### Cách 3: Trong WordPress Editor

Chế độ **Text/HTML**, paste:

```html
<section class="menu-khoa-hoc">
    <h2 style="text-align: center;">Khóa Học của Chúng Tôi</h2>
    <div class="menu-grid">
        <!-- Nội dung khóa học -->
    </div>
</section>
```

---

## 🎯 CSS Classes Có Sẵn

### Container Classes
```css
.menu-khoa-hoc          /* Container chính */
#menu-khoa-hoc          /* Hoặc dùng ID */
.section-khoa-hoc       /* Alternative */
.khoa-hoc-section       /* Alternative */
```

### Layout Classes
```css
.menu-grid              /* Grid responsive layout */
.menu-item-card         /* Card cho từng khóa học */
.menu-icon              /* Icon với gradient background */
.menu-item-title        /* Tiêu đề khóa học */
.menu-item-description  /* Mô tả khóa học */
```

### Button Classes
```css
.button                 /* Button gradient */
.btn                    /* Alternative button */
.button-outline         /* Outline button style */
```

---

## ✨ Features

### 🎨 **Styling**
- Nền trắng (#ffffff)
- Text màu primary (#042277)
- Hover effect smooth
- Border và shadow đẹp mắt
- Gradient icons và buttons

### 📐 **Layout**
- Grid responsive tự động
- Auto-fit: 1-4 columns
- Card style với border
- Proper spacing và padding

### 📱 **Responsive**
- **Mobile** (< 768px): 1 column
- **Tablet** (768px - 1024px): 2 columns  
- **Desktop** (> 1024px): 3-4 columns
- Auto-adjust based on content

### ♿ **Accessibility**
- Focus states rõ ràng
- High contrast mode support
- Keyboard navigation friendly
- Semantic HTML structure

### ⚡ **Performance**
- Pure CSS, no JavaScript
- GPU-accelerated transitions
- Optimized selectors
- Minimal file size (~8KB)

---

## 🧪 Testing

### 1. Clear Cache
```bash
# WordPress cache
rm -rf wp-content/cache/*

# Browser cache
Ctrl + Shift + R (Chrome)
Ctrl + F5 (Firefox)
```

### 2. View Demo
```
URL: http://localhost/timona/demo-menu-khoa-hoc.html
```

### 3. Test trong WordPress
```
1. Tạo Page/Post mới
2. Chuyển sang chế độ Text/HTML
3. Paste HTML code với class "menu-khoa-hoc"
4. Preview hoặc Publish
5. Kiểm tra:
   - Nền có trắng không
   - Chữ có màu #042277 không
   - Hover có đổi màu #040B1E không
   - Cards có hiển thị đúng grid không
```

### 4. DevTools Check
```
1. Right-click section → Inspect
2. Check computed styles:
   - background-color: rgb(255, 255, 255) ✓
   - color: rgb(4, 34, 119) ✓
3. Hover over link:
   - color: rgb(4, 11, 30) ✓
```

---

## 📋 Checklist

### Setup
- ✅ File `menu-khoa-hoc.css` đã tạo
- ✅ Enqueued trong `functions.php`
- ✅ Version 1.0.2 cho cache busting
- ✅ Dependencies: `timona-custom-fonts`

### Styling
- ✅ Background: White (#ffffff)
- ✅ Text: Primary (#042277)
- ✅ Hover: Secondary (#040B1E)
- ✅ Icons: Gradient background
- ✅ Buttons: Gradient style
- ✅ Active state: Bold + border

### Features
- ✅ Grid layout responsive
- ✅ Card style với hover effect
- ✅ List style với icons
- ✅ Button variants (normal, outline)
- ✅ Mobile responsive
- ✅ Accessibility support

### Documentation
- ✅ Demo page created
- ✅ Usage examples
- ✅ CSS reference
- ✅ Color scheme documented

---

## 🎓 Examples

### Example 1: Grid Cards
```html
<section class="menu-khoa-hoc">
    <h2>Khóa Học Marketing</h2>
    <div class="menu-grid">
        <div class="menu-item-card">
            <div class="menu-icon">📊</div>
            <div class="menu-item-title">SEO Cơ Bản</div>
            <div class="menu-item-description">
                Tối ưu hóa website cho công cụ tìm kiếm
            </div>
            <a href="/khoa-hoc/seo" class="button">Xem Chi Tiết</a>
        </div>
        <!-- More cards -->
    </div>
</section>
```

### Example 2: Simple List
```html
<div id="menu-khoa-hoc">
    <h2>Danh Sách Khóa Học</h2>
    <ul>
        <li><a href="/seo">SEO Cơ Bản</a></li>
        <li><a href="/content">Content Marketing</a></li>
        <li><a href="/ads">Google Ads</a></li>
    </ul>
</div>
```

### Example 3: With Outline Button
```html
<section class="menu-khoa-hoc">
    <h2>Khóa Học Miễn Phí</h2>
    <a href="/register" class="button">Đăng Ký Ngay</a>
    <a href="/info" class="button-outline">Tìm Hiểu Thêm</a>
</section>
```

---

## 🔧 Customization

### Thay đổi màu sắc
Sửa file `menu-khoa-hoc.css`:

```css
/* Thay đổi màu text */
.menu-khoa-hoc a {
    color: #YOUR_COLOR !important;
}

/* Thay đổi màu hover */
.menu-khoa-hoc a:hover {
    color: #YOUR_HOVER_COLOR !important;
}

/* Thay đổi gradient */
.menu-icon {
    background: linear-gradient(135deg, #COLOR1, #COLOR2);
}
```

### Thay đổi layout
```css
/* Số columns trong grid */
.menu-grid {
    grid-template-columns: repeat(4, 1fr); /* 4 columns */
}

/* Spacing giữa items */
.menu-grid {
    gap: 30px; /* Tăng khoảng cách */
}
```

---

## 🆘 Troubleshooting

### Section không hiển thị nền trắng?
```
1. Check class name: "menu-khoa-hoc" (có dấu gạch ngang)
2. Clear cache: Ctrl + Shift + R
3. Check DevTools: background-color computed value
4. Verify CSS file loaded: Network tab → menu-khoa-hoc.css
```

### Text không đổi màu khi hover?
```
1. Check CSS specificity
2. Verify !important đang hoạt động
3. Clear browser cache
4. Test trong Incognito mode
```

### Grid không responsive?
```
1. Check viewport meta tag
2. Test với DevTools responsive mode
3. Verify @media queries load
4. Check browser compatibility
```

---

## 📞 Support

### Files Location
```
CSS: /wp-content/themes/flatsome-child/assets/css/menu-khoa-hoc.css
PHP: /wp-content/themes/flatsome-child/functions.php
Demo: /timona/demo-menu-khoa-hoc.html
```

### Version Info
```
CSS Version: 1.0.2
Enqueue Priority: 999 (load after theme)
Dependencies: timona-custom-fonts
Browser Support: Modern browsers + IE11
```

---

## ✅ Summary

**Section "menu-khoa-hoc" đã sẵn sàng sử dụng với:**

1. ✅ **Nền trắng** thay vì gradient menu chính
2. ✅ **Chữ màu primary** (#042277) dễ đọc
3. ✅ **Hover màu secondary** (#040B1E)
4. ✅ **Icons và buttons** giữ gradient đẹp
5. ✅ **Responsive** cho mọi thiết bị
6. ✅ **Accessible** theo chuẩn WCAG
7. ✅ **Demo page** sẵn sàng tham khảo

**Chỉ cần thêm class hoặc ID vào HTML là xong!** 🎉
