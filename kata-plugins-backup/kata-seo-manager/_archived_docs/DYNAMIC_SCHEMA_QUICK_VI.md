# ⚡ KATA DYNAMIC SCHEMA - HƯỚNG DẪN NHANH

## 🎯 Là Gì?

**Dynamic Schema** = Tự thêm code JSON-LD vào WordPress

✅ Không cần code PHP  
✅ Support 800+ schema types  
✅ Copy-paste từ Google Tool  
✅ Test schema mới nhanh chóng  

---

## 🚀 Cách Dùng Cơ Bản

### **Bước 1: Mở TinyMCE Editor**
Click nút **"KATA SEO Manager"** → Chọn **"⚡ Dynamic Schema"**

### **Bước 2: Thay JSON Code**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"TÊN WEBSITE CỦA BẠN","url":"https://website.com"}']
```

### **Bước 3: Publish!**
✅ Xong! Schema đã được thêm vào page.

---

## 📋 Ví Dụ Nhanh

### **WebSite Schema**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"example.com","url":"https://example.com"}']
```

### **Organization Schema**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Organization","name":"Công ty ABC","url":"https://congtyabc.com","logo":"https://congtyabc.com/logo.png"}']
```

### **Person Schema (Author)**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Person","name":"Nguyễn Văn A","jobTitle":"SEO Specialist","email":"contact@example.com"}']
```

---

## ⚙️ Attributes Hay Dùng

| Attribute | Mô Tả | Ví Dụ |
|-----------|-------|-------|
| `json_code` | Code JSON-LD (bắt buộc) | `json_code='{"@type":"WebSite",...}'` |
| `validate` | Kiểm tra lỗi syntax | `validate="true"` (mặc định) |
| `show_schema` | Hiển thị JSON-LD | `show_schema="true"` (mặc định) |
| `show_frontend` | Hiển thị visual UI | `show_frontend="true"` (test mode) |
| `show_info` | Hiển thị info box | `show_info="true"` |
| `show_raw` | Hiển thị raw JSON | `show_raw="true"` |
| `pretty_print` | Format JSON đẹp | `pretty_print="true"` |
| `minify` | Nén JSON nhỏ gọn | `minify="true"` (production) |
| `auto_context` | Tự thêm @context | `auto_context="true"` (mặc định) |

---

## 🎨 Display Modes

### **Mode 1: Chỉ Schema (Production)**
```php
[kata_dynamic json_code='...' show_schema="true" show_frontend="false"]
```
→ Chỉ có `<script type="application/ld+json">` (không có UI)

### **Mode 2: Test Mode (Development)**
```php
[kata_dynamic json_code='...' show_schema="true" show_frontend="true" show_info="true" show_raw="true"]
```
→ Có cả JSON-LD + Visual display để test

### **Mode 3: UI Only (Debug)**
```php
[kata_dynamic json_code='...' show_schema="false" show_frontend="true"]
```
→ Chỉ hiển thị UI (không output JSON-LD)

---

## 💡 Use Cases

### **1. Import từ Google Tool**
1. Vào https://search.google.com/test/rich-results
2. Copy JSON-LD output
3. Paste vào `json_code`
```php
[kata_dynamic json_code='... PASTE TỪ GOOGLE ...' auto_context="true"]
```

### **2. Test Schema Mới**
```php
[kata_dynamic json_code='{"@type":"SoftwareApplication","name":"App Name"}' show_frontend="true"]
```

### **3. Customize Schema Theo Ý**
```php
[kata_dynamic json_code='{"@type":"Product","name":"Sản phẩm","custom_field":"Giá trị tùy chỉnh"}']
```

---

## ⚠️ Lỗi Thường Gặp

### **Lỗi 1: Thiếu json_code**
```
❌ [kata_dynamic show_schema="true"]
```
**Fix:**
```php
✅ [kata_dynamic json_code='{"@type":"WebSite",...}']
```

### **Lỗi 2: JSON Sai Syntax**
```
❌ {"name": 'John'}  // Sai: dùng single quotes
```
**Fix:**
```json
✅ {"name": "John"}  // Đúng: dùng double quotes
```

### **Lỗi 3: Quên Dấu Đóng**
```
❌ {"@type":"WebSite","name":"Test"  // Thiếu }
```
**Fix:**
```json
✅ {"@type":"WebSite","name":"Test"}
```

---

## ✅ Best Practices

### **Development (Test):**
```php
[kata_dynamic 
    json_code='...' 
    validate="true" 
    pretty_print="true" 
    show_frontend="true" 
    show_info="true" 
    show_raw="true" 
    error_display="true"]
```

### **Production (Live Site):**
```php
[kata_dynamic 
    json_code='...' 
    validate="true" 
    minify="true" 
    show_schema="true" 
    show_frontend="false" 
    error_display="false"]
```

---

## 🔍 Validate Schema

### **Bước 1: Thêm Schema**
```php
[kata_dynamic json_code='...' show_schema="true"]
```

### **Bước 2: Publish Page**
Publish hoặc Preview page

### **Bước 3: Google Test**
1. Copy URL page
2. Vào https://search.google.com/test/rich-results
3. Paste URL → Test
4. Check errors/warnings

---

## 📊 Ví Dụ Thực Tế

### **WebSite + Search**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"example.com","url":"https://example.com","potentialAction":{"@type":"SearchAction","target":"https://example.com/search?q={search_term_string}","query-input":"required name=search_term_string"}}']
```

### **Organization Full**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Organization","name":"KATA Digital","url":"https://katadigital.com","logo":"https://katadigital.com/logo.png","description":"Digital Marketing Agency","address":{"@type":"PostalAddress","streetAddress":"123 Main St","addressLocality":"Hanoi","addressCountry":"VN"},"contactPoint":{"@type":"ContactPoint","telephone":"+84-28-1234-5678","contactType":"Customer Service"}}' show_schema="true"]
```

### **Product với Price**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Product","name":"iPhone 15 Pro","brand":"Apple","offers":{"@type":"Offer","price":"29990000","priceCurrency":"VND","availability":"https://schema.org/InStock"}}']
```

### **Event**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Event","name":"WordPress Meetup Hanoi","startDate":"2025-10-15T18:00:00+07:00","location":{"@type":"Place","name":"KATA Office","address":"123 Main St, Hanoi"}}']
```

### **BreadcrumbList**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://example.com"},{"@type":"ListItem","position":2,"name":"Blog","item":"https://example.com/blog"}]}']
```

---

## 🎯 Tips & Tricks

### **Tip 1: Auto @context**
```php
// Không cần thêm @context
json_code='{"@type":"Person","name":"John"}' 
auto_context="true"

// Tự động thành:
{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "John"
}
```

### **Tip 2: Pretty Print cho Testing**
```php
pretty_print="true"
→ JSON được format đẹp, dễ đọc
```

### **Tip 3: Minify cho Production**
```php
minify="true"
→ JSON nhỏ gọn, giảm 30-40% size
```

### **Tip 4: Visual Debug**
```php
show_frontend="true" show_raw="true"
→ Xem visual + JSON code để debug
```

### **Tip 5: Multiple @type**
```php
json_code='{"@type":["LocalBusiness","Restaurant"],...}'
→ Combine nhiều types
```

---

## 📚 Schema Types Phổ Biến

| Type | Dùng Cho | Example |
|------|----------|---------|
| `WebSite` | Homepage | Website chính |
| `Organization` | Company | Công ty, doanh nghiệp |
| `Person` | Author | Tác giả, người |
| `Product` | E-commerce | Sản phẩm bán |
| `Event` | Events | Sự kiện |
| `Article` | Blog | Bài viết |
| `LocalBusiness` | Local SEO | Cửa hàng local |
| `BreadcrumbList` | Navigation | Breadcrumb |
| `Recipe` | Food | Công thức nấu ăn |
| `VideoObject` | Video | Video content |

**Xem thêm:** https://schema.org/docs/full.html (800+ types)

---

## 🆘 Trợ Giúp

### **Documentation:**
- `DYNAMIC_SCHEMA_GUIDE.md` - Hướng dẫn đầy đủ
- `test_dynamic_schema.html` - 12 test cases
- `DYNAMIC_SCHEMA_SUMMARY.md` - Tổng kết

### **Test File:**
```
/test_dynamic_schema.html
→ 12 scenarios với copy-paste shortcuts
```

### **Google Tools:**
- Rich Results Test: https://search.google.com/test/rich-results
- Schema.org: https://schema.org
- JSON-LD Playground: https://json-ld.org/playground

---

## ✅ Checklist Nhanh

- [ ] Mở TinyMCE editor
- [ ] Click "KATA SEO Manager" button
- [ ] Chọn "⚡ Dynamic Schema"
- [ ] Thay `json_code` bằng schema của bạn
- [ ] Test với `show_frontend="true"`
- [ ] Validate với Google Rich Results Tool
- [ ] Deploy với `show_frontend="false"`
- [ ] Check Google Search Console sau 1-2 tuần

---

## 🎉 Tóm Tắt

**Dynamic Schema = Universal JSON-LD Tool**

✅ 1 shortcode cho **tất cả schema types**  
✅ Copy-paste từ **bất kỳ source nào**  
✅ **Validate** tự động  
✅ **Test** với visual display  
✅ **Production-ready** với minify  
✅ **Error handling** thông minh  
✅ **800+ schema types** support  

**Shortcode:**
```php
[kata_dynamic json_code='YOUR_JSON_HERE']
```

**That's it! Simple & Powerful! 🚀**

---

*Tạo: 9/10/2025 | Plugin: KATA SEO Manager v1.0.0*
