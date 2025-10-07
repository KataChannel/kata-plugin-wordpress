# KATA Schema Customization Guide

## Tổng Quan

Plugin KATA SEO Manager giờ hỗ trợ tùy chỉnh những thuộc tính nào sẽ xuất hiện trong JSON-LD schema markup của các shortcode. Điều này cho phép bạn kiểm soát chính xác dữ liệu cấu trúc nào được hiển thị cho Google và các công cụ tìm kiếm khác.

## Cú Pháp Sử Dụng

### 1. Sử dụng `schema_fields`

Thuộc tính `schema_fields` cho phép bạn liệt kê các trường muốn hiển thị hoặc ẩn:

```
[kata_article schema_fields="headline,author,datePublished,-articleBody,-keywords"]
```

**Quy tắc:**
- Tên trường **không có dấu `-`** = **Hiển thị** trường đó
- Tên trường **có dấu `-`** ở đầu = **Ẩn** trường đó
- Các trường được ngăn cách bởi dấu phẩy

**Lưu ý:** `@context` và `@type` luôn được bao gồm và không thể ẩn.

### 2. Sử dụng `hide_*` và `show_*`

Bạn cũng có thể sử dụng các thuộc tính riêng lẻ:

```
[kata_recipe hide_nutrition="true" hide_suitableForDiet="true"]
```

```
[kata_product show_sku="true" show_gtin="true" show_mpn="true"]
```

**Quy tắc:**
- `hide_[fieldname]="true"` = Ẩn trường cụ thể
- `show_[fieldname]="true"` = Hiển thị trường cụ thể
- Nếu cả hai đều được đặt, `show_*` sẽ ưu tiên

---

## Schema Types và Các Trường Có Sẵn

### Article Schema

**Shortcode:** `[kata_article]`

**Các trường mặc định hiển thị:**
- `headline` - Tiêu đề bài viết
- `name` - Tên bài viết
- `author` - Tác giả
- `datePublished` - Ngày đăng
- `dateModified` - Ngày cập nhật
- `image` - Hình ảnh
- `url` - URL bài viết
- `publisher` - Nhà xuất bản

**Các trường mặc định ẩn:**
- `description` - Mô tả
- `articleBody` - Nội dung bài viết
- `wordCount` - Số từ
- `articleSection` - Phân loại
- `keywords` - Từ khóa

**Ví dụ:**

```
// Chỉ hiển thị tiêu đề, tác giả và ngày đăng
[kata_article 
    title="Bài viết của tôi"
    author="Nguyễn Văn A"
    schema_fields="headline,author,datePublished"
]

// Ẩn nội dung và từ khóa
[kata_article 
    title="Bài viết của tôi"
    hide_articleBody="true"
    hide_keywords="true"
]
```

---

### FAQ Schema

**Shortcode:** `[kata_faq]`

**Các trường mặc định hiển thị:**
- `mainEntity` - Danh sách câu hỏi FAQ

**Ví dụ:**

```
[kata_faq]
    [kata_faq_item question="Câu hỏi 1?" answer="Câu trả lời 1"]
    [kata_faq_item question="Câu hỏi 2?" answer="Câu trả lời 2"]
[/kata_faq]
```

---

### Recipe Schema

**Shortcode:** `[kata_recipe]`

**Các trường mặc định hiển thị:**
- `name` - Tên công thức
- `description` - Mô tả
- `image` - Hình ảnh
- `author` - Tác giả
- `prepTime` - Thời gian chuẩn bị
- `cookTime` - Thời gian nấu
- `totalTime` - Tổng thời gian
- `recipeYield` - Khẩu phần
- `recipeIngredient` - Nguyên liệu
- `recipeInstructions` - Hướng dẫn

**Các trường mặc định ẩn:**
- `recipeCategory` - Phân loại công thức
- `recipeCuisine` - Ẩm thực
- `nutrition` - Thông tin dinh dưỡng
- `aggregateRating` - Đánh giá
- `suitableForDiet` - Chế độ ăn phù hợp

**Ví dụ:**

```
// Hiển thị thông tin dinh dưỡng
[kata_recipe 
    name="Phở bò"
    show_nutrition="true"
    nutrition_calories="350"
    nutrition_protein="25g"
]

// Chỉ hiển thị tên và nguyên liệu
[kata_recipe 
    name="Phở bò"
    schema_fields="name,recipeIngredient"
]

// Ẩn thời gian và hướng dẫn
[kata_recipe 
    name="Phở bò"
    hide_prepTime="true"
    hide_cookTime="true"
    hide_recipeInstructions="true"
]
```

---

### Product Schema

**Shortcode:** `[kata_product]`

**Các trường mặc định hiển thị:**
- `name` - Tên sản phẩm
- `description` - Mô tả
- `image` - Hình ảnh
- `brand` - Thương hiệu
- `offers` - Giá bán

**Các trường mặc định ẩn:**
- `sku` - Mã SKU
- `gtin` - Mã GTIN
- `mpn` - Mã MPN
- `model` - Model
- `category` - Danh mục
- `color` - Màu sắc
- `size` - Kích thước
- `material` - Chất liệu
- `weight` - Trọng lượng
- `aggregateRating` - Đánh giá

**Ví dụ:**

```
// Hiển thị mã SKU và GTIN cho Google Shopping
[kata_product 
    name="iPhone 15 Pro"
    brand="Apple"
    price="29990000"
    sku="IP15P-256-TB"
    gtin="0194253392552"
    show_sku="true"
    show_gtin="true"
]

// Chỉ hiển thị thông tin cơ bản
[kata_product 
    name="iPhone 15 Pro"
    schema_fields="name,brand,image,offers"
]

// Ẩn các thông tin kỹ thuật
[kata_product 
    name="iPhone 15 Pro"
    hide_sku="true"
    hide_model="true"
    hide_weight="true"
]
```

---

### Event Schema

**Shortcode:** `[kata_event]`

**Các trường mặc định hiển thị:**
- `name` - Tên sự kiện
- `startDate` - Ngày bắt đầu
- `endDate` - Ngày kết thúc
- `location` - Địa điểm
- `description` - Mô tả

**Các trường mặc định ẩn:**
- `image` - Hình ảnh
- `organizer` - Tổ chức
- `performer` - Người biểu diễn
- `offers` - Vé
- `eventStatus` - Trạng thái
- `eventAttendanceMode` - Hình thức tham dự

---

### HowTo Schema

**Shortcode:** `[kata_howto]`

**Các trường mặc định hiển thị:**
- `name` - Tên hướng dẫn
- `description` - Mô tả
- `step` - Các bước

**Các trường mặc định ẩn:**
- `image` - Hình ảnh
- `totalTime` - Tổng thời gian
- `estimatedCost` - Chi phí ước tính
- `tool` - Công cụ cần thiết
- `supply` - Vật liệu cần thiết

---

### Quiz/Poll Schema

**Shortcode:** `[kata_quiz]` / `[kata_poll]`

**Các trường mặc định hiển thị:**
- `question` - Câu hỏi
- `acceptedAnswer` - Câu trả lời đúng
- `suggestedAnswer` - Các đáp án gợi ý

**Các trường mặc định ẩn:**
- `comment` - Bình luận
- `answerCount` - Số lượng câu trả lời

---

### LocalBusiness Schema

**Shortcode:** `[kata_localbusiness]`

**Các trường mặc định hiển thị:**
- `name` - Tên doanh nghiệp
- `address` - Địa chỉ
- `telephone` - Số điện thoại
- `openingHours` - Giờ mở cửa

**Các trường mặc định ẩn:**
- `image` - Hình ảnh
- `priceRange` - Mức giá
- `geo` - Tọa độ địa lý
- `url` - Website
- `aggregateRating` - Đánh giá

---

### JobPosting Schema

**Shortcode:** `[kata_jobposting]`

**Các trường mặc định hiển thị:**
- `title` - Tên công việc
- `description` - Mô tả
- `hiringOrganization` - Công ty tuyển dụng
- `jobLocation` - Địa điểm làm việc
- `datePosted` - Ngày đăng

**Các trường mặc định ẩn:**
- `baseSalary` - Lương cơ bản
- `employmentType` - Loại hợp đồng
- `validThrough` - Hết hạn
- `qualifications` - Yêu cầu
- `responsibilities` - Trách nhiệm
- `benefits` - Quyền lợi

---

## Use Cases Thực Tế

### 1. Tối ưu cho Google Shopping

Hiển thị đầy đủ thông tin sản phẩm cho Google Merchant Center:

```
[kata_product 
    name="Giày Nike Air Max 270"
    brand="Nike"
    price="3490000"
    currency="VND"
    sku="AIR-MAX-270-BK-42"
    gtin="0885176523924"
    mpn="AH8050-002"
    color="Đen"
    size="42"
    show_sku="true"
    show_gtin="true"
    show_mpn="true"
    show_color="true"
    show_size="true"
]
```

### 2. Bài viết tin tức đơn giản

Chỉ hiển thị thông tin cơ bản nhất:

```
[kata_article 
    title="Tin tức mới nhất"
    author="Biên tập viên"
    date_published="2025-01-15"
    schema_fields="headline,author,datePublished,publisher"
]
```

### 3. Công thức nấu ăn có thông tin dinh dưỡng

```
[kata_recipe 
    name="Salad giảm cân"
    prep_time="15M"
    show_nutrition="true"
    nutrition_calories="150"
    nutrition_protein="8g"
    nutrition_fat="5g"
    nutrition_carbs="20g"
]
```

### 4. Trang FAQ tối giản

```
[kata_faq schema_fields="mainEntity"]
    [kata_faq_item 
        question="Làm thế nào để đặt hàng?" 
        answer="Bạn có thể đặt hàng qua website hoặc hotline."
    ]
    [kata_faq_item 
        question="Thời gian giao hàng bao lâu?" 
        answer="Thường từ 2-3 ngày làm việc."
    ]
[/kata_faq]
```

### 5. Doanh nghiệp địa phương với đầy đủ thông tin

```
[kata_localbusiness 
    name="Nhà hàng ABC"
    address="123 Nguyễn Huệ, Q.1, TP.HCM"
    telephone="028-1234-5678"
    show_geo="true"
    show_priceRange="true"
    show_aggregateRating="true"
    priceRange="$$"
    rating_value="4.5"
    rating_count="120"
]
```

---

## Kiểm Tra Schema Output

### Cách kiểm tra trong WordPress:

1. Thêm shortcode vào bài viết/trang
2. Xem nguồn trang (View Page Source)
3. Tìm `<script type="application/ld+json">`
4. Kiểm tra JSON-LD schema markup

### Công cụ kiểm tra:

- **Google Rich Results Test:** https://search.google.com/test/rich-results
- **Schema.org Validator:** https://validator.schema.org/
- **JSON-LD Playground:** https://json-ld.org/playground/

---

## Lưu Ý Quan Trọng

1. **@context và @type luôn có mặt**: Hai thuộc tính này không thể bị ẩn vì chúng bắt buộc trong schema markup.

2. **Trường bắt buộc của Google**: Một số schema có các trường bắt buộc theo Google Search Guidelines. Hãy đảm bảo không ẩn các trường này:
   - **Product**: name, image, offers
   - **Recipe**: name, image, author, datePublished
   - **Article**: headline, image, author, datePublished
   - **Event**: name, startDate, location

3. **Thứ tự ưu tiên**:
   - `show_*` attributes > `hide_*` attributes > `schema_fields` > default settings

4. **Tên trường phân biệt hoa/thường**: Sử dụng đúng tên trường theo schema.org (camelCase).

5. **Validation**: Luôn kiểm tra schema với Google Rich Results Test sau khi tùy chỉnh.

---

## Troubleshooting

### Schema không xuất hiện

**Kiểm tra:**
- `show_schema="true"` đã được đặt?
- Class `KATA_Schema_Customizer` đã được load?
- Có lỗi PHP trong error log?

### Trường vẫn hiển thị dù đã ẩn

**Kiểm tra:**
- Tên trường có đúng chính tả không?
- Có thuộc tính `show_*` nào ghi đè không?
- Cache đã được xóa chưa?

### Google Rich Results Test báo lỗi

**Kiểm tra:**
- Các trường bắt buộc có đầy đủ không?
- Format dữ liệu có đúng không? (VD: datePublished phải dạng ISO 8601)
- Schema type có hợp lệ không?

---

## Changelog

### Version 1.0.0 (2025-01-15)
- ✅ Thêm class `KATA_Schema_Customizer`
- ✅ Hỗ trợ `schema_fields` attribute
- ✅ Hỗ trợ `hide_*` và `show_*` attributes
- ✅ Cập nhật 4 shortcodes: article, faq, recipe, product
- ⏳ Pending: event, howto, quiz, poll, wheel, localbusiness, jobposting, course

---

## Liên Hệ

Nếu có thắc mắc hoặc cần hỗ trợ, vui lòng liên hệ:
- **Website:** https://katachannel.com
- **Email:** support@katachannel.com
