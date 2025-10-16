# KATA Schema - Quick Start Guide

## 🚀 Khởi Động Nhanh 5 Phút

### Bước 1: Kích Hoạt Plugin (Đã hoàn thành ✅)
Plugin đã được kích hoạt tự động. Database table sẽ được tạo khi bạn truy cập admin lần đầu.

---

### Bước 2: Truy Cập Admin Dashboard

1. Đăng nhập WordPress Admin: http://localhost/timona/wp-admin
2. Tìm menu **KATA Schema** trên sidebar (icon: code standards)
3. Click vào để xem Dashboard

**Bạn sẽ thấy:**
- Thống kê schemas
- Danh sách schemas (ban đầu trống)
- Hướng dẫn sử dụng

---

### Bước 3: Tạo Schema Đầu Tiên

#### Cách A: Dùng Template (Khuyến nghị cho người mới)

1. Click **"Duyệt Mẫu Templates"** 
2. Chọn loại schema phù hợp (ví dụ: **Article**)
3. Click **"Sử Dụng Template"**
4. Chỉnh sửa dữ liệu JSON:
   ```json
   {
     "@context": "https://schema.org",
     "@type": "Article",
     "headline": "Tiêu đề bài viết của bạn",
     "author": {
       "@type": "Person",
       "name": "Tên tác giả"
     },
     "datePublished": "2025-01-15",
     "image": "URL_ảnh_của_bạn"
   }
   ```
5. Click **"Kiểm Tra JSON"** để validate
6. Click **"Tạo Schema"**

#### Cách B: Tạo Tùy Chỉnh

1. Click **"Thêm Schema Mới"**
2. Nhập tên schema
3. Chọn loại schema
4. Nhập JSON data
5. Click **"Tạo Schema"**

---

### Bước 4: Chèn Schema Vào Bài Viết

#### Cách 1: Dùng TinyMCE Button (Dễ nhất)

1. Mở bài viết/trang trong Editor
2. Tìm nút **"KATA Schema"** trên toolbar (icon: code)
3. Click nút → Modal hiện ra với danh sách schemas
4. Click vào schema muốn chèn
5. Shortcode tự động được insert: `[kata_schema id="1"]`
6. Publish bài

#### Cách 2: Copy Shortcode

1. Vào **KATA Schema → Dashboard**
2. Tìm schema cần dùng
3. Click vào shortcode ở cột "Shortcode" để copy
4. Paste vào bài viết
5. Publish

---

### Bước 5: Kiểm Tra Kết Quả

1. **View Page Source:**
   - Mở bài viết trên frontend
   - Chuột phải → View Page Source
   - Tìm `<script type="application/ld+json">`
   - Copy đoạn JSON schema

2. **Test với Google:**
   - Truy cập: https://search.google.com/test/rich-results
   - Paste URL bài viết HOẶC paste code JSON
   - Click **"Test URL"** hoặc **"Test Code"**
   - Kiểm tra kết quả: **"Valid rich results found"** ✅

---

## 📋 10 Schema Types Có Sẵn

| Schema Type | Dùng Cho | Google Rich Result |
|-------------|----------|-------------------|
| **Article** | Blog posts, tin tức | ✅ Snippets với ảnh, tác giả |
| **LocalBusiness** | Doanh nghiệp địa phương | ✅ Google Maps info |
| **Product** | Sản phẩm | ✅ Giá, rating |
| **FAQ** | Câu hỏi thường gặp | ✅ FAQ accordion |
| **HowTo** | Hướng dẫn | ✅ Step-by-step |
| **Organization** | Thông tin công ty | ✅ Knowledge Graph |
| **Person** | Hồ sơ cá nhân | ✅ People card |
| **Event** | Sự kiện | ✅ Event snippets |
| **Recipe** | Công thức nấu ăn | ✅ Recipe cards |
| **Course** | Khóa học | ✅ Course listings |

---

## 🎯 Shortcode Options

### Basic (chỉ schema markup, ẩn trên frontend)
```
[kata_schema id="1"]
```

### Hiển thị Visual + Schema
```
[kata_schema id="1" show_frontend="true"]
```

### Chỉ Visual, không schema
```
[kata_schema id="1" show_schema="false" show_frontend="true"]
```

---

## 💡 Tips & Tricks

### 1. Copy Shortcode Nhanh
- Ở Dashboard, click vào code shortcode → Tự động copy!

### 2. Validate JSON Trước Khi Lưu
- Click nút **"Kiểm Tra JSON"** để tránh lỗi

### 3. Format JSON Cho Dễ Đọc
- Click nút **"Format JSON"** để tự động indent

### 4. Sử dụng Template
- Đừng tạo từ đầu, dùng template có sẵn rồi chỉnh sửa!

### 5. Test Thường Xuyên
- Sau mỗi thay đổi, test lại với Google Rich Results Test

---

## ⚠️ Troubleshooting

### Schema không xuất hiện?
✅ Kiểm tra shortcode đã đúng format: `[kata_schema id="X"]`  
✅ Schema status phải là "Hoạt động"  
✅ Clear cache nếu dùng caching plugin

### JSON không hợp lệ?
✅ Click **"Kiểm Tra JSON"** để xem lỗi  
✅ Đảm bảo tất cả string dùng dấu ngoặc kép `"`  
✅ Kiểm tra dấu phẩy ở cuối mỗi property

### TinyMCE button không thấy?
✅ User phải có quyền `edit_posts`  
✅ Rich Editing phải bật (User Profile)  
✅ Clear cache browser

### Google không hiển thị Rich Result?
✅ Validate với Google Rich Results Test  
✅ Đảm bảo có đủ required properties  
✅ Chờ Google re-index (vài ngày)

---

## 📚 Tài Liệu Tham Khảo

- **Plugin README:** `/wp-content/plugins/kata-schema/README.md`
- **Schema.org:** https://schema.org/
- **Google Rich Results Test:** https://search.google.com/test/rich-results
- **Schema Validator:** https://validator.schema.org/
- **JSON Validator:** https://jsonlint.com/

---

## 🎯 Workflow Khuyến Nghị

### Cho Blog/News Site:
1. Tạo **Article** schema template
2. Customize: headline, author, datePublished, image
3. Chèn vào mỗi bài viết mới
4. Google sẽ hiển thị snippets đẹp hơn

### Cho E-commerce:
1. Tạo **Product** schema cho từng sản phẩm
2. Include: name, price, image, rating
3. Chèn vào product pages
4. Google hiển thị giá + rating trên SERP

### Cho Local Business:
1. Tạo **LocalBusiness** schema
2. Điền đầy đủ: address, phone, hours, geo coordinates
3. Chèn vào trang Contact/About
4. Hiển thị trên Google Maps

### Cho Knowledge Base:
1. Tạo **FAQ** schemas cho các chủ đề
2. Format questions & answers rõ ràng
3. Chèn vào FAQ pages
4. Google hiển thị FAQ accordion ngay trên SERP

---

## ✅ Checklist Hoàn Thành

- [ ] Plugin đã kích hoạt
- [ ] Truy cập Dashboard thành công
- [ ] Tạo schema đầu tiên
- [ ] Chèn vào bài viết
- [ ] View source thấy JSON-LD
- [ ] Test với Google Rich Results (Valid ✅)
- [ ] Hiểu 10 schema types
- [ ] Biết cách dùng shortcode options
- [ ] Đọc troubleshooting guide

---

## 🏆 Next Steps

1. **Tạo schemas cho tất cả content types** của bạn
2. **Test từng schema** với Google
3. **Monitor Search Console** để xem rich results
4. **Optimize dữ liệu** dựa trên Google feedback
5. **Chia sẻ** với team về cách sử dụng

---

**🎉 Chúc mừng! Bạn đã sẵn sàng sử dụng KATA Schema Manager!**

**Nếu cần hỗ trợ:**
- Đọc README.md đầy đủ
- Xem KATA_SCHEMA_COMPLETION_SUMMARY.md
- Liên hệ KATA Team

---

**Version:** 1.0.0  
**Last Updated:** 15/01/2025  
**Made with ❤️ by KATA Team**
