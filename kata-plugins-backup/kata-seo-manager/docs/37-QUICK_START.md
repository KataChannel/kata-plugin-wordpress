# 🚀 KATA SEO Manager - Quick Start (5 phút)

## Bước 1: Kích hoạt Plugin ✅

```
WordPress Admin → Plugins → KATA SEO Manager → Activate
```

Sau khi activate, bạn sẽ thấy menu **KATA SEO** ở sidebar.

---

## Bước 2: Cấu hình cơ bản (2 phút)

```
KATA SEO → Settings

Điền thông tin:
✅ Organization Name: Tên công ty/website
✅ Organization URL: https://yoursite.com  
✅ Organization Logo: Upload logo (Click "Upload Image")

→ Click "Save Changes"
```

**Xong! Cấu hình cơ bản hoàn tất** 🎉

---

## Bước 3: Thêm Schema đầu tiên (3 phút)

### Option A: Dùng Meta Box (Khuyến nghị)

```
1. Posts → Edit bất kỳ bài viết nào
2. Cuộn xuống meta box "Schema Markup"
3. Click "Add Schema"
4. Chọn "Article"
5. Form tự động điền:
   - Headline: (đã có)
   - Author: (đã có)
   - Image: (đã có)
6. Click "Insert Schema"
7. Click "Update" post
```

### Option B: Dùng Shortcode

Thêm vào bài viết:

```
[kata_faq]
[kata_faq_item question="Sản phẩm có bảo hành không?" answer="Có, 12 tháng"]
[kata_faq_item question="Giao hàng mất bao lâu?" answer="2-3 ngày"]
[/kata_faq]
```

---

## Bước 4: Kiểm tra kết quả

### Cách 1: View Source

```
1. Mở bài viết vừa thêm schema
2. Chuột phải → View Page Source (Ctrl+U)
3. Tìm kiếm: "application/ld+json"
4. Thấy JSON-LD schema → Thành công! ✅
```

### Cách 2: Google Rich Results Test

```
1. Copy URL bài viết
2. Vào: https://search.google.com/test/rich-results
3. Paste URL
4. Click "Test URL"
5. Xem kết quả:
   - ✅ Valid: OK!
   - ⚠️  Warnings: Có thể bỏ qua
   - ❌ Errors: Cần sửa
```

---

## Các Schema Phổ Biến

### 1. Article (Bài viết/Blog)

**Tự động**: Thêm Article schema cho mọi post

```
Meta Box → Add Schema → Article → Insert
```

### 2. FAQ (Câu hỏi thường gặp)

**Shortcode**:

```
[kata_faq]
[kata_faq_item question="..." answer="..."]
[kata_faq_item question="..." answer="..."]
[/kata_faq]
```

### 3. Product (Sản phẩm)

**Manual**:

```
Meta Box → Add Schema → Product
Điền:
- Name: Tên sản phẩm
- Price: 299000
- Currency: VND
- SKU: PROD-123
```

### 4. Recipe (Công thức nấu ăn)

**Shortcode hoặc Meta Box**:

```
[kata_recipe 
    name="Bánh Chocolate" 
    prepTime="30" 
    cookTime="60"
    servings="8"]
```

---

## Dashboard - Xem thống kê

```
KATA SEO → Dashboard

Xem:
- Total Schemas: Tổng số schemas
- Active Schemas: Đang hoạt động
- Validation Pass: % hợp lệ
- Recent Schemas: Mới nhất
```

---

## Troubleshooting nhanh

### Schema không hiển thị?

```
1. Check "is_active" = true trong meta box
2. Clear cache (Ctrl+F5)
3. Kiểm tra Settings → Show in Source (phải ON)
```

### Có lỗi validation?

```
Dashboard → Validation Report → Xem chi tiết lỗi → Sửa
```

---

## 🎯 Bạn đã sẵn sàng!

✅ Plugin activated  
✅ Settings configured  
✅ First schema added  
✅ Verified in source code

**Bây giờ bạn có thể**:

- Thêm schemas cho tất cả posts
- Dùng 26 loại schemas khác nhau
- Xem statistics trong dashboard
- Optimize SEO với rich results

---

## 📚 Tài liệu đầy đủ

- **README.md** - Hướng dẫn chi tiết
- **INSTALLATION.md** - Setup từng bước
- **DEVELOPMENT_GUIDE.md** - Developer guide

---

**Chúc bạn SEO thành công với KATA SEO Manager!** 🚀

**Support**: support@katachannel.com
