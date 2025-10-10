# KATA SEO Manager - LocalBusiness Schema Update

## Thay đổi mới (October 9, 2025)

### 1. Thêm tính năng tìm kiếm Schema trong TinyMCE Editor

**File:** `assets/js/tinymce-plugin.js`

Đã thêm ô tìm kiếm (search box) vào dialog chọn schema template:

- **Vị trí:** Ngay bên dưới header trong dialog "🏷️ Chọn Schema Template để Chèn"
- **Tính năng:**
  - Tìm kiếm real-time khi gõ
  - Lọc theo tên schema và mô tả
  - Hiển thị thông báo "Không tìm thấy" khi không có kết quả
  - Placeholder: "🔍 Tìm kiếm schema (FAQ, Article, Product, LocalBusiness...)"

**Cách sử dụng:**
1. Mở TinyMCE Editor
2. Click nút "KATA SEO Manager"
3. Gõ từ khóa vào ô search (ví dụ: "FAQ", "Product", "LocalBusiness")
4. Chọn schema muốn chèn

---

### 2. Cập nhật LocalBusiness Schema với Department Array

**Files cập nhật:**
- `assets/js/tinymce-plugin.js` - Template mẫu
- `kata-seo-manager.php` - Render function

#### Cấu trúc Schema mới:

```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "https://example.com/#LocalBusiness",
  "name": "Công ty Chính",
  "url": "https://example.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "123 Main St"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 10.7769,
    "longitude": 106.7009
  },
  "telephone": "+84 28 1234 5678",
  "email": "hello@example.com",
  "openingHours": "Mon-Fri 9:00-18:00",
  "priceRange": "$$",
  "department": [
    {
      "@type": "LocalBusiness",
      "@id": "https://example.com/branch-1/#LocalBusiness",
      "name": "Chi nhánh 1",
      "url": "https://example.com/branch-1",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "456 Branch St"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": 10.7821,
        "longitude": 106.6919
      },
      "telephone": "+84 28 9876 5432"
    }
  ]
}
```

#### Thuộc tính mới cho shortcode:

**Main Business:**
- `latitude` - Vĩ độ
- `longitude` - Kinh độ
- `email` - Email liên hệ
- `website` - Website (thay thế cho `url`)
- `services` - Danh sách dịch vụ
- `rating` - Đánh giá trung bình
- `review_count` - Số lượng đánh giá

**Department (hỗ trợ tối đa 10 chi nhánh):**
- `department_1_name` - Tên chi nhánh 1
- `department_1_address` - Địa chỉ chi nhánh 1
- `department_1_phone` - Số điện thoại chi nhánh 1
- `department_1_url` - URL chi nhánh 1
- `department_1_latitude` - Vĩ độ chi nhánh 1
- `department_1_longitude` - Kinh độ chi nhánh 1
- ... (tương tự cho department_2 đến department_10)

**Content Display:**
- `show_content_departments` - Hiển thị danh sách chi nhánh
- `show_content_services` - Hiển thị dịch vụ
- `show_content_rating` - Hiển thị đánh giá
- `show_content_contact` - Hiển thị thông tin liên hệ

#### Ví dụ sử dụng:

```shortcode
[kata_local_business 
    name="KATA Digital Marketing Agency - Văn phòng Chính" 
    address="123 Nguyễn Văn Cừ, Quận 1, TP.HCM" 
    phone="+84 28 1234 5678" 
    email="hello@katadigital.com" 
    website="https://katadigital.com" 
    latitude="10.7769"
    longitude="106.7009"
    hours="Thứ 2-6: 9:00-18:00" 
    rating="4.8" 
    review_count="127" 
    price_range="$$"
    department_1_name="KATA Digital - Chi nhánh Quận 3"
    department_1_address="456 Võ Văn Tần, Quận 3, TP.HCM"
    department_1_phone="+84 28 9876 5432"
    department_1_latitude="10.7821"
    department_1_longitude="106.6919"
    department_2_name="KATA Digital - Chi nhánh Thủ Đức"
    department_2_address="789 Võ Văn Ngân, Thủ Đức, TP.HCM"
    department_2_phone="+84 28 5555 6666"
    department_2_latitude="10.8509"
    department_2_longitude="106.7718"
    show_schema="true" 
    show_frontend="true" 
    show_content_departments="true"]
```

#### Frontend Display:

Khi `show_content_departments="true"`, danh sách chi nhánh sẽ được hiển thị với:
- Icon 🏪 và header "Chi nhánh / Departments"
- Mỗi chi nhánh trong box riêng với border màu #667eea
- Hiển thị: Tên, Địa chỉ, Số điện thoại, Link website
- Style responsive và thân thiện với mobile

---

## Testing

### Test Search Function:
1. Mở TinyMCE Editor
2. Click "KATA SEO Manager"
3. Gõ "local" vào search box
4. Kiểm tra xem chỉ hiển thị LocalBusiness schema

### Test LocalBusiness with Departments:
1. Chèn shortcode LocalBusiness với department
2. Kiểm tra schema markup trong `<head>` (View Page Source)
3. Verify cấu trúc JSON-LD có department array
4. Test frontend display với `show_content_departments="true"`

---

## Browser Compatibility

- Chrome/Edge: ✅ Tested
- Firefox: ✅ Tested  
- Safari: ✅ Should work
- Mobile browsers: ✅ Responsive

---

## Notes

- Schema output được lưu trong `<head>` tag
- Frontend display có thể tùy chỉnh bằng CSS
- Department array giúp Google hiểu rõ hơn về cấu trúc doanh nghiệp
- Geo coordinates giúp cải thiện local SEO

---

## Version

- Plugin: KATA SEO Manager v2.1.3+
- Update: October 9, 2025
- Developer: KATA Team
