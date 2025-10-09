# 🚀 KATA SEO Manager - Fullscreen UI Quick Guide

## 📖 Hướng dẫn sử dụng giao diện mới

---

## 🎯 Giao diện mới có gì?

### ✨ **Fullscreen Mode**
Dialog giờ mở **toàn màn hình** để bạn có nhiều không gian làm việc hơn!

### 📋 **Two-Panel Layout**
- **Bên trái:** Danh sách 26+ schemas với search box
- **Bên phải:** Preview chi tiết + shortcode

### 🔍 **Live Preview**
Xem trước schema trước khi chèn vào editor!

---

## 🏁 Bắt đầu nhanh (5 bước)

### **Bước 1: Mở Dialog**
Click nút **"KATA SEO Manager"** trên toolbar TinyMCE

![Button location](https://via.placeholder.com/600x100/667eea/ffffff?text=KATA+SEO+Manager+Button)

---

### **Bước 2: Tìm kiếm (optional)**
Gõ vào search box để lọc schema:

```
🔍 Tìm kiếm: "FAQ"     → Chỉ hiện FAQ schemas
🔍 Tìm kiếm: "Product" → Chỉ hiện Product schemas
🔍 Tìm kiếm: "Local"   → Chỉ hiện LocalBusiness schemas
```

**Tips:** Search theo tên tiếng Việt hoặc tiếng Anh đều được!

---

### **Bước 3: Click Schema**
Click vào bất kỳ schema nào trong danh sách bên trái

**Hiệu ứng:**
- Schema được chọn → Highlight màu xanh
- Preview hiển thị ngay bên phải

---

### **Bước 4: Xem Preview**
Ở panel bên phải, bạn sẽ thấy:

📌 **Schema Header** (gradient tím)
- Tên schema
- Mô tả ngắn

📄 **Preview Content**
- Thông tin schema sẽ tạo ra gì
- Các trường dữ liệu mẫu

💻 **Shortcode Box**
- Code mẫu sẵn sàng dùng
- Click vào để select tất cả

---

### **Bước 5: Chèn hoặc Copy**

**Option A: Chèn trực tiếp**
```
Click nút: ✨ Chèn vào Editor
→ Shortcode được insert
→ Dialog tự động đóng
```

**Option B: Copy rồi paste**
```
Click nút: 📋 Copy shortcode
→ Shortcode copy vào clipboard
→ Bạn có thể paste ở đâu tùy ý
```

---

## 💡 Tips & Tricks

### **1. Keyboard Shortcuts**
- `Esc` → Đóng dialog
- `Ctrl+F` → Focus vào search box (coming soon)
- `Tab` → Navigate giữa các elements

### **2. Search Smart**
```
❌ Không cần: Gõ đầy đủ "FAQ Schema"
✅ Chỉ cần: Gõ "faq" hoặc "FAQ"

❌ Không cần: "Local Business with Departments"
✅ Chỉ cần: "local" hoặc "business"
```

### **3. Active State**
Schema nào đang được chọn sẽ có:
- ✅ Border màu xanh
- ✅ Background màu xanh nhạt
- ✅ Nổi bật so với các schema khác

### **4. Scroll Independent**
- Left panel scroll riêng
- Right panel scroll riêng
- Không ảnh hưởng lẫn nhau!

---

## 📱 Screen Size Recommendations

### **Optimal**
- ✅ Desktop: 1920×1080 trở lên
- ✅ Laptop: 1366×768 trở lên

### **Minimum**
- ⚠️ 1024×768 (may need scroll)

### **Not recommended**
- ❌ Mobile/Tablet (use desktop for best experience)

---

## 🎨 Visual Guide

### **Layout Overview**
```
┌─────────────────────────────────────────────────┐
│ 🏷️ KATA SEO Manager Header (Gradient)         │
│ Chọn schema template để chèn vào nội dung      │
├──────────────────┬──────────────────────────────┤
│ LEFT PANEL       │ RIGHT PANEL                  │
│ (420px)          │ (Flex)                       │
│                  │                              │
│ ┌──────────────┐ │ ┌──────────────────────────┐ │
│ │ 🔍 Search    │ │ │ 📋 Empty State           │ │
│ └──────────────┘ │ │ or                       │ │
│                  │ │ ✨ Schema Preview        │ │
│ ┌──────────────┐ │ │                          │ │
│ │ Schema 1     │ │ │ • Header                 │ │
│ │ Schema 2     │ │ │ • Preview Box            │ │
│ │ Schema 3     │ │ │ • Shortcode              │ │
│ │ ...          │ │ │ • Buttons                │ │
│ │ (Scroll)     │ │ │                          │ │
│ └──────────────┘ │ └──────────────────────────┘ │
│                  │                              │
│ 💡 Tip message   │                              │
└──────────────────┴──────────────────────────────┘
```

### **Color Scheme**
```css
Primary: #667eea (Purple Blue)
Secondary: #764ba2 (Purple)
Accent: #0073aa (WordPress Blue)
Success: #059669 (Green)
Background: #f5f7fa (Light Gray)
```

---

## ❓ FAQs

### **Q: Dialog quá to, làm sao thu nhỏ?**
A: Dialog được thiết kế fullscreen để tối ưu workflow. Nhấn `Esc` để đóng.

### **Q: Tôi không thấy preview?**
A: Click vào một schema trong danh sách bên trái để preview hiển thị.

### **Q: Search không hoạt động?**
A: Clear browser cache (Ctrl+Shift+R) và thử lại.

### **Q: Làm sao copy shortcode?**
A: Click nút "📋 Copy shortcode" hoặc click vào textarea và Ctrl+C.

### **Q: Schema nào phổ biến nhất?**
A: FAQ, Article, Product, LocalBusiness, HowTo là top 5 schemas.

---

## 🔧 Troubleshooting

### **Issue: Dialog không mở**
```
✅ Check: TinyMCE đã load chưa?
✅ Check: Console có lỗi không? (F12)
✅ Try: Reload trang (Ctrl+R)
```

### **Issue: Preview không hiển thị**
```
✅ Check: Đã click vào schema chưa?
✅ Check: JavaScript có lỗi không?
✅ Try: Click schema khác rồi click lại
```

### **Issue: Copy button không hoạt động**
```
✅ Check: Browser có cho phép copy không?
✅ Try: Click vào textarea và Ctrl+C
✅ Workaround: Select text và right-click → Copy
```

---

## 📚 Resources

### **Documentation**
- Full changelog: `CHANGELOG_FULLSCREEN_UI.md`
- Update summary: `UPDATE_SUMMARY.md`

### **Demo**
- Static demo: `demo-fullscreen-ui.html`
- Open trong browser để test

### **Support**
- Plugin docs: `/wp-admin/admin.php?page=kata-seo-docs`
- GitHub: [kata-plugin-wordpress](https://github.com/KataChannel/kata-plugin-wordpress)

---

## 🎯 Best Practices

### **1. Workflow Optimization**
```
1. Biết rõ schema cần dùng → Search ngay
2. Không chắc → Scroll danh sách xem
3. Tìm được → Click xem preview
4. Hợp lý → Insert hoặc Copy
5. Cần chỉnh sửa → Copy rồi edit manually
```

### **2. Schema Selection**
```
✅ FAQ: Trang có nhiều Q&A
✅ Article: Blog posts, tin tức
✅ Product: Trang sản phẩm
✅ LocalBusiness: Về công ty, chi nhánh
✅ Recipe: Công thức nấu ăn
✅ HowTo: Hướng dẫn từng bước
```

### **3. Editing After Insert**
Sau khi insert shortcode:
1. ✅ Thay đổi nội dung mẫu
2. ✅ Xóa các attributes không dùng
3. ✅ Thêm attributes tùy chỉnh
4. ✅ Test trong preview mode

---

## 🎓 Advanced Usage

### **Custom Shortcodes**
Bạn có thể edit shortcode sau khi insert:

**Example:**
```
Original:
[kata_faq]...[/kata_faq]

Customized:
[kata_faq show_schema="true" show_content="true"]
[kata_faq_item question="Custom Q?" answer="Custom A"]
[/kata_faq]
```

### **Combining Schemas**
Có thể dùng nhiều schemas trong 1 page:

```html
[kata_article title="..."]

<p>Content here...</p>

[kata_faq]
[kata_faq_item ...]
[/kata_faq]

<p>More content...</p>

[kata_product name="..."]
```

---

## 📊 Performance Tips

### **1. Load Time**
- Dialog mở trong <100ms
- Preview render <100ms
- Search filter <50ms

### **2. Browser Optimization**
```
✅ Use latest browser version
✅ Enable JavaScript
✅ Clear cache periodically
✅ Disable conflicting extensions
```

---

## 🎉 Enjoy!

Giao diện mới được thiết kế để:
- ✨ Dễ sử dụng hơn
- 🚀 Nhanh hơn
- 👀 Trực quan hơn
- 💪 Mạnh mẽ hơn

**Happy coding with KATA SEO Manager! 🎊**

---

**Version:** 2.1.4+  
**Last updated:** 09/10/2025  
**Plugin:** KATA SEO Manager  
**Component:** TinyMCE Plugin Fullscreen UI
