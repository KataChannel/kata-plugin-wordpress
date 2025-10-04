# KATA SEO Tools - Version 2.0.0

## 📋 Giới thiệu

**KATA SEO Tools** là plugin WordPress SEO toàn diện, hỗ trợ 16 loại Schema Markup cho mỗi bài viết với khả năng chọn nhiều schema cùng lúc.

## 🚀 Tính năng chính

### Schema Markup (16 loại)
- Article, BlogPosting, NewsArticle
- Product, Review, Recipe
- Event, VideoObject, Course, FAQPage
- HowTo, JobPosting, LocalBusiness
- Organization, Person, WebPage

### Gamification
- Quiz, Poll, Wheel of Fortune
- Ratings & Reviews

### Social Integration
- Facebook, Twitter, LinkedIn Share
- Open Graph, Twitter Cards

## 📦 Cài đặt

1. Upload ZIP qua WordPress Admin → Plugins → Add New → Upload
2. Hoặc giải nén và upload folder qua FTP vào `/wp-content/plugins/`
3. Kích hoạt plugin
4. Plugin tự động tạo 6 bảng database

## ⚙️ Database Tables (Tự động tạo)

Plugin tự động tạo các bảng sau khi kích hoạt:

1. `kata_seo_quiz_results` - Kết quả quiz
2. `kata_seo_poll_votes` - Phiếu bầu poll  
3. `kata_seo_wheel_spins` - Lượt quay
4. `kata_seo_ratings` - Đánh giá
5. `kata_seo_form_submissions` - Form data
6. `kata_seo_social_shares` - Social tracking

## 📝 Sử dụng Schema Markup

1. Edit bài viết
2. Tìm meta box "KATA Schema Markup"
3. Tích "Bật Schema Markup"
4. Chọn schema types (có thể chọn nhiều)
5. Điền thông tin
6. Save bài viết

## 🔧 Yêu cầu

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.6+

## 📄 License

GPL v2 or later

---
**Version 2.0.0** | Kata Team © 2025
