# QUICK REFERENCE: 3 Schema Types Mới

## 📹 Video Schema

### Shortcode cơ bản:
```php
[kata_video title="Video Title" url="video.mp4"]
```

### Shortcode đầy đủ:
```php
[kata_video 
    title="How to Cook Pasta"
    description="Step by step pasta cooking guide"
    url="https://example.com/video.mp4"
    thumbnail="https://example.com/thumb.jpg"
    duration="PT10M30S"
    upload_date="2025-10-09"
    
    schema_fields="name,contentUrl,thumbnailUrl,duration"
    
    hide_content_thumbnail="true"
    show_content_video="true"
]
```

### Properties (17 total):
| Property | Required | Example |
|----------|----------|---------|
| name | ✅ | "How to Cook" |
| contentUrl | ✅ | "video.mp4" |
| description | ✅ | "Tutorial video" |
| thumbnailUrl | ✅ | "thumb.jpg" |
| duration | ✅ | "PT10M" (10 minutes) |
| uploadDate | ✅ | "2025-10-09" |
| embedUrl | ⚪ | "https://youtube.com/embed/xxx" |
| transcript | ⚪ | "Video transcript text" |
| videoQuality | ⚪ | "HD", "4K" |
| publisher | ⚪ | Organization object |
| contentSize | ⚪ | "524288" (bytes) |
| encodingFormat | ⚪ | "video/mp4" |
| interactionStatistic | ⚪ | Views, likes |
| regionsAllowed | ⚪ | "US,VN,JP" |

---

## 🏢 Organization Schema

### Shortcode cơ bản:
```php
[kata_organization name="Company Name" url="https://example.com"]
```

### Shortcode đầy đủ:
```php
[kata_organization 
    name="KATA SEO Company"
    url="https://kata.com"
    logo="https://kata.com/logo.png"
    description="Leading SEO Solutions Provider in Vietnam"
    address="123 Main Street, Hanoi, Vietnam"
    phone="+84-123-456-789"
    email="info@kata.com"
    
    schema_fields="name,url,logo,description,address,telephone,email"
    
    show_content="true"
    hide_content_contact="false"
]
```

### Properties (16 total):
| Property | Required | Example |
|----------|----------|---------|
| name | ✅ | "KATA Company" |
| url | ✅ | "https://kata.com" |
| logo | ✅ | "logo.png" |
| description | ✅ | "SEO solutions" |
| address | ✅ | "123 Main St" |
| telephone | ✅ | "+84-123-456" |
| email | ✅ | "info@kata.com" |
| foundingDate | ⚪ | "2020-01-01" |
| founder | ⚪ | Person object |
| numberOfEmployees | ⚪ | "50-100" |
| slogan | ⚪ | "Best SEO" |
| contactPoint | ⚪ | ContactPoint object |
| sameAs | ⚪ | ["facebook.com/kata"] |
| areaServed | ⚪ | "Vietnam" |
| award | ⚪ | "Best Company 2024" |

---

## ⭐ Rating Schema

### Shortcode cơ bản:
```php
[kata_rating title="Rating" max_stars="5" current_rating="4.5"]
```

### Shortcode đầy đủ:
```php
[kata_rating 
    title="Product Rating"
    max_stars="5"
    current_rating="4.5"
    allow_rating="true"
    show_average="true"
    
    hide_content_title="false"
    show_content_stars="true"
]
```

### Properties (10 total):
| Property | Required | Example |
|----------|----------|---------|
| ratingValue | ✅ | "4.5" |
| bestRating | ✅ | "5" |
| worstRating | ⚪ | "1" |
| ratingCount | ⚪ | "150" |
| reviewCount | ⚪ | "89" |
| itemReviewed | ⚪ | Product/Service object |
| author | ⚪ | Person/Organization |
| reviewBody | ⚪ | "Great product!" |
| datePublished | ⚪ | "2025-10-09" |

---

## 🎨 Dual-Mode Customization

### MODE 1: Schema Filtering (JSON-LD)
Kiểm soát properties nào xuất hiện trong schema output

**Cách 1: schema_fields**
```php
schema_fields="name,url,logo"
```

**Cách 2: hide_* attributes**
```php
hide_description="true" hide_address="true"
```

**Cách 3: show_* attributes**
```php
show_description="true" show_logo="true"
```

### MODE 2: Content Display (HTML)
Kiểm soát elements nào hiển thị trên page

**Hide content:**
```php
hide_content_title="true"
hide_content_thumbnail="true"
```

**Show content:**
```php
show_content_title="true"
show_content_video="true"
```

---

## 📝 Examples Thực Tế

### Example 1: Video Tutorial
```php
[kata_video 
    title="WordPress SEO Tutorial"
    url="/videos/seo-tutorial.mp4"
    thumbnail="/images/seo-thumb.jpg"
    duration="PT15M"
    description="Complete guide to WordPress SEO"
    schema_fields="name,contentUrl,thumbnailUrl,duration,description"
]
```

### Example 2: Company Info
```php
[kata_organization 
    name="Vietnam Tech Solutions"
    url="https://vts.com.vn"
    logo="https://vts.com.vn/logo.png"
    description="Software development company"
    address="456 Tech Street, HCMC, Vietnam"
    phone="+84-987-654-321"
    email="contact@vts.com.vn"
    schema_fields="name,url,logo,description,address,telephone,email,areaServed"
    show_areaServed="true"
]
```

### Example 3: Product Rating
```php
[kata_rating 
    title="iPhone 15 Pro Rating"
    max_stars="5"
    current_rating="4.8"
    allow_rating="false"
    show_average="true"
]
```

---

## 🧪 Testing in Admin UI

### Bước 1: Mở Admin UI
```
http://localhost/timona/wp-admin/admin.php?page=kata-schema-customization
```

### Bước 2: Chọn Schema Type
- Dropdown → "Video" hoặc "Organization" hoặc "Rating/Review"

### Bước 3: Chọn Properties
- ✅ Check properties muốn hiển thị
- ⚪ Uncheck properties muốn ẩn

### Bước 4: Generate Shortcode
- Click "Generate Shortcode"
- Copy code
- Paste vào page/post

---

## 🔍 Validation

### Google Rich Results Test:
```
https://search.google.com/test/rich-results
```

Paste URL trang có shortcode để kiểm tra schema output.

### Expected Output (Video):
```json
{
  "@context": "https://schema.org",
  "@type": "VideoObject",
  "name": "How to Cook Pasta",
  "contentUrl": "video.mp4",
  "thumbnailUrl": "thumb.jpg",
  "duration": "PT10M30S"
}
```

### Expected Output (Organization):
```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "KATA Company",
  "url": "https://kata.com",
  "logo": "logo.png",
  "description": "SEO solutions"
}
```

### Expected Output (Rating):
```json
{
  "@context": "https://schema.org",
  "@type": "Rating",
  "ratingValue": "4.5",
  "bestRating": "5"
}
```

---

## ⚡ Quick Tips

1. **Duration format:** Dùng ISO 8601
   - PT10M = 10 minutes
   - PT1H30M = 1 hour 30 minutes
   - PT45S = 45 seconds

2. **Date format:** YYYY-MM-DD
   - 2025-10-09

3. **Required vs Optional:**
   - Required (✅): Luôn hiển thị mặc định
   - Optional (⚪): Ẩn mặc định, cần enable

4. **Schema fields:**
   - Phân cách bằng dấu phẩy
   - Không có khoảng trắng
   - Example: `schema_fields="name,url,logo"`

---

## 🎯 Kết Luận

**Tổng cộng: 15 Schema Types**

- 12 schema cũ + 3 schema mới (Video, Organization, Rating)
- 198 properties tổng cộng
- Dual-mode customization cho tất cả
- Admin UI hỗ trợ đầy đủ

**Status:** ✅ ALL COMPLETE
