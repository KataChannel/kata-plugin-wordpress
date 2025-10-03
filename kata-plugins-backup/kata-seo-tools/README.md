# Kata SEO Tools Plugin - Documentation

## 🎯 Tổng quan Plugin

**Kata SEO Tools** là plugin WordPress toàn diện với 7 tính năng chính hỗ trợ SEO và tương tác người dùng cho các bài post và page.

---

## 📋 Cấu trúc Plugin

```
kata-seo-tools/
├── kata-seo-tools.php              # Main plugin file
├── includes/
│   ├── class-schema-generator.php   # ✅ Tính năng 1: Dynamic Schema Markup
│   ├── class-social-share.php       # ✅ Tính năng 2: Custom Social Share
│   ├── class-quote-generator.php    # ✅ Tính năng 3: Quote & Hashtag
│   ├── class-quiz-handler.php       # ✅ Tính năng 4: Quiz (Gamification)
│   ├── class-wheel-handler.php      # ✅ Tính năng 4: Vòng quay may mắn
│   ├── class-poll-handler.php       # ✅ Tính năng 5: Poll
│   ├── class-rating-handler.php     # ✅ Tính năng 5: Rating & Review
│   ├── class-faq-handler.php        # ✅ Tính năng 6: Dynamic FAQ
│   ├── class-cta-handler.php        # ✅ Tính năng 7: Custom CTA
│   ├── class-form-handler.php       # ✅ Tính năng 7: Custom Form
│   └── class-admin.php              # Admin dashboard
├── templates/
│   ├── admin/                       # Admin templates
│   │   ├── dashboard.php
│   │   ├── settings.php
│   │   ├── analytics.php
│   │   ├── schema-meta-box.php
│   │   ├── social-meta-box.php
│   │   └── faq-meta-box.php
│   └── frontend/                    # Frontend templates
│       ├── quiz.php
│       ├── poll.php
│       ├── wheel.php
│       ├── rating.php
│       ├── faq.php
│       ├── quote.php
│       ├── cta.php
│       └── form.php
├── assets/
│   ├── css/
│   │   ├── frontend.css
│   │   └── admin.css
│   ├── js/
│   │   ├── frontend.js
│   │   └── admin.js
│   └── images/
└── languages/
```

---

## ✅ Tính năng 1: Dynamic Schema Markup

### **Mô tả:**
- Tự động tạo Schema Markup chuẩn Google cho mỗi bài viết
- Hỗ trợ nhiều loại schema: Article, BlogPosting, NewsArticle, Tutorial, etc.
- Tự động thêm Breadcrumb schema
- Organization schema cho homepage
- Aggregate Rating schema (tích hợp với rating system)

### **File liên quan:**
- `includes/class-schema-generator.php`
- `templates/admin/schema-meta-box.php`

### **Cách sử dụng:**
```php
// Tự động output trong <head>
// Hoặc tùy chỉnh trong meta box khi edit post

// Meta box options:
- Schema Type: Article, BlogPosting, NewsArticle, Tutorial
- Enable/Disable schema cho từng post
```

### **Schema Types được hỗ trợ:**
- ✅ Article Schema
- ✅ Breadcrumb Schema
- ✅ FAQ Schema (auto-generate từ FAQ meta box)
- ✅ Organization Schema
- ✅ AggregateRating Schema

---

## ✅ Tính năng 2: Custom Social Share

### **Platforms hỗ trợ:**
- Facebook
- Twitter (X)
- LinkedIn
- Pinterest
- Telegram
- WhatsApp
- TikTok

### **Shortcode:**
```
[kata_social_share platforms="facebook,twitter,linkedin,tiktok" style="circle" size="large" show_count="yes" title="Chia sẻ bài viết"]
```

### **Parameters:**
- `platforms`: Danh sách platforms (separated by comma)
- `style`: default, circle, square, minimal
- `size`: small, medium, large
- `show_count`: yes/no (hiển thị số lượt share)
- `title`: Tiêu đề phần share
- `layout`: horizontal, vertical

### **Custom Meta Tags:**
- Open Graph tags (og:title, og:description, og:image)
- Twitter Card tags
- Tùy chỉnh trong Social Meta Box khi edit post

### **Features:**
- ✅ Social share buttons với custom styling
- ✅ Track số lượt share cho mỗi platform
- ✅ Custom OG tags cho mỗi bài viết
- ✅ SVG icons cho mỗi platform
- ✅ Mobile-friendly

---

## ✅ Tính năng 3: Quote Generator & Hashtags

### **Shortcode:**
```
[kata_quote style="modern" author="John Doe" hashtags="#seo #marketing #timona"]
Nội dung quote của bạn ở đây
[/kata_quote]
```

### **Parameters:**
- `style`: classic, modern, minimal, boxed, gradient
- `author`: Tác giả của quote
- `hashtags`: Danh sách hashtags (space separated)
- `bg_color`: Màu nền custom
- `text_color`: Màu chữ custom
- `share`: yes/no (hiển thị nút share quote)

### **Styling Options:**
- Classic: Quote truyền thống với quotation marks
- Modern: Thiết kế hiện đại với border-left
- Minimal: Tối giản, chỉ text và author
- Boxed: Quote trong box có shadow
- Gradient: Background gradient đẹp mắt

### **Features:**
- ✅ Multiple quote styles
- ✅ Clickable hashtags
- ✅ Share quote as image
- ✅ Author attribution
- ✅ Responsive design

---

## ✅ Tính năng 4: Gamification (Quiz, Vòng Quay, Thử Thách)

### **4.1. Quiz**

**Shortcode:**
```
[kata_quiz id="quiz-seo-basic" title="Kiểm tra kiến thức SEO cơ bản"]
```

**JSON Configuration:**
```json
{
  "questions": [
    {
      "question": "SEO là viết tắt của gì?",
      "options": [
        "Search Engine Optimization",
        "Social Engine Optimization",
        "Server Engine Optimization"
      ],
      "correct": 0,
      "explanation": "SEO là Search Engine Optimization - Tối ưu hóa công cụ tìm kiếm"
    }
  ],
  "settings": {
    "show_explanation": true,
    "show_score": true,
    "allow_retry": true,
    "timer": 300
  }
}
```

**Features:**
- ✅ Multiple choice questions
- ✅ Tính điểm tự động
- ✅ Giải thích đáp án
- ✅ Timer (optional)
- ✅ Lưu kết quả vào database
- ✅ Leaderboard
- ✅ Certificate sau khi hoàn thành
- ✅ Schema markup cho quiz

### **4.2. Vòng Quay May Mắn**

**Shortcode:**
```
[kata_wheel id="wheel-timona" prizes="json_encoded_prizes"]
```

**JSON Configuration:**
```json
{
  "prizes": [
    {"id": 1, "name": "Giảm 50%", "probability": 10, "color": "#FF6B6B"},
    {"id": 2, "name": "Giảm 30%", "probability": 20, "color": "#4ECDC4"},
    {"id": 3, "name": "Giảm 10%", "probability": 30, "color": "#45B7D1"},
    {"id": 4, "name": "Thử lại", "probability": 40, "color": "#FFA07A"}
  ],
  "settings": {
    "spins_per_user": 1,
    "require_email": true,
    "redirect_after_spin": "/thank-you"
  }
}
```

**Features:**
- ✅ Animated wheel spin
- ✅ Configurable prizes với xác suất
- ✅ Giới hạn số lượt quay/user
- ✅ Email collection
- ✅ Prize redemption tracking
- ✅ Analytics dashboard

### **4.3. Thử Thách (Challenges)**

**Shortcode:**
```
[kata_challenge id="challenge-7days" type="daily" duration="7"]
```

**Features:**
- ✅ Daily challenges
- ✅ Progress tracking
- ✅ Rewards system
- ✅ Badges/achievements
- ✅ Social sharing của achievements

---

## ✅ Tính năng 5: Tương Tác Người Dùng

### **5.1. Rating & Review System**

**Shortcode:**
```
[kata_rating show_average="yes" allow_review="yes"]
```

**Features:**
- ✅ 5-star rating system
- ✅ Text reviews
- ✅ Average rating display
- ✅ Review moderation
- ✅ Schema markup (AggregateRating)
- ✅ Filter/sort reviews
- ✅ Helpful vote system

**Display:**
```php
// Average rating
★★★★☆ 4.5/5 (123 đánh giá)

// Individual review
★★★★★
"Bài viết rất hữu ích!"
- Nguyễn Văn A, 2 ngày trước
[👍 15] [👎 2]
```

### **5.2. Comments Enhancement**

**Features:**
- ✅ Threaded comments
- ✅ Comment reactions (like, love, wow)
- ✅ @ mention users
- ✅ Markdown support
- ✅ Comment moderation
- ✅ Spam filter

### **5.3. Poll System**

**Shortcode:**
```
[kata_poll id="poll-best-cms" question="CMS nào bạn thích nhất?"]
```

**JSON Configuration:**
```json
{
  "options": [
    {"id": 1, "text": "WordPress", "color": "#21759b"},
    {"id": 2, "text": "Drupal", "color": "#0678be"},
    {"id": 3, "text": "Joomla", "color": "#f44321"}
  ],
  "settings": {
    "allow_multiple": false,
    "show_results_before_vote": false,
    "votes_per_ip": 1
  }
}
```

**Features:**
- ✅ Single/multiple choice
- ✅ Real-time results
- ✅ Beautiful progress bars
- ✅ IP/cookie-based vote limiting
- ✅ Export results to CSV
- ✅ Embed poll in widgets

---

## ✅ Tính năng 6: Dynamic FAQ với Schema

### **Shortcode:**
```
[kata_faq style="accordion" schema="yes"]
```

### **Meta Box Configuration:**
Trong post editor, sử dụng FAQ Meta Box để thêm Q&A:

```
Q: SEO là gì?
A: SEO (Search Engine Optimization) là quá trình tối ưu hóa website...

Q: Làm SEO mất bao lâu?
A: Thường mất từ 3-6 tháng để thấy kết quả...
```

### **Styles:**
- **Accordion**: Mở/đóng từng câu hỏi
- **Toggle**: Toggle individual items
- **Simple**: Hiển thị tất cả Q&A
- **Tabbed**: Q&A trong tabs

### **Features:**
- ✅ Auto-generate FAQ Schema
- ✅ Multiple display styles
- ✅ Search within FAQs
- ✅ Anchor links
- ✅ Print-friendly
- ✅ Export to PDF

### **Schema Output:**
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "SEO là gì?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "SEO là..."
    }
  }]
}
```

---

## ✅ Tính năng 7: Custom CTA & Forms

### **7.1. Call-to-Action (CTA)**

**Shortcode:**
```
[kata_cta style="box" button_text="Đăng ký ngay" button_url="/dang-ky" button_color="#FF6B6B"]
Nhận ưu đãi 50% cho khóa học SEO - Chỉ còn 3 ngày!
[/kata_cta]
```

**Styles:**
- **Box**: CTA trong box có background
- **Banner**: Full-width banner
- **Inline**: Inline với nội dung
- **Sticky**: Sticky bottom bar
- **Popup**: Popup modal (với timing trigger)
- **Slide-in**: Slide từ bên phải

**Parameters:**
- `style`: box, banner, inline, sticky, popup, slide-in
- `button_text`: Text trên button
- `button_url`: Link button
- `button_color`: Màu button
- `bg_color`: Màu nền CTA
- `icon`: Icon (SVG hoặc Font Awesome class)
- `trigger`: Timer cho popup (seconds)
- `position`: top, bottom (for sticky)

### **7.2. Custom Forms**

**Shortcode:**
```
[kata_form id="contact-form" title="Liên hệ với chúng tôi"]
```

**Form Builder trong Admin:**
```php
// Drag & drop form builder
- Text Input
- Email Input
- Textarea
- Select Dropdown
- Checkbox
- Radio
- File Upload
- reCAPTCHA
```

**Features:**
- ✅ Drag & drop form builder
- ✅ Field validation
- ✅ Conditional logic
- ✅ Email notifications
- ✅ Database storage
- ✅ Export submissions to CSV
- ✅ Spam protection (reCAPTCHA, Honeypot)
- ✅ Multi-step forms
- ✅ File uploads
- ✅ Integration với email marketing (Mailchimp, etc.)

**Form Templates:**
- Contact Form
- Newsletter Subscription
- Quote Request
- Survey Form
- Registration Form
- Feedback Form

---

## 📊 Analytics Dashboard

**Location:** WordPress Admin → Kata SEO → Analytics

### **Metrics Tracked:**

1. **Schema Performance**
   - Click-through rate (CTR) from search
   - Rich snippet impressions
   - Schema errors/warnings

2. **Social Sharing**
   - Shares by platform
   - Most shared content
   - Share trends over time

3. **Gamification**
   - Quiz completion rate
   - Average scores
   - Wheel spin redemption rate
   - Challenge participation

4. **User Engagement**
   - Average rating
   - Review submission rate
   - Poll participation
   - Comment activity

5. **Forms & CTAs**
   - Form submission rate
   - CTA click-through rate
   - Conversion tracking

### **Export Options:**
- CSV export
- PDF reports
- Google Analytics integration
- Email scheduled reports

---

## ⚙️ Settings & Configuration

**Location:** WordPress Admin → Kata SEO → Settings

### **General Settings:**
```php
- Enable/Disable Schema Markup
- Enable/Disable Social Sharing
- Enable/Disable Gamification
- Enable/Disable Ratings
- Default Schema Type
- Company Name
- Company Logo
- Social Media Profiles
```

### **Social Settings:**
```php
- Default OG Image
- Twitter Handle
- Facebook App ID
- Social Share Platforms (enable/disable individual)
```

### **Gamification Settings:**
```php
- Enable Prizes/Rewards
- Points System
- Badge Images
- Email Templates
```

### **Advanced Settings:**
```php
- Custom CSS
- Custom JS
- CDN URLs for assets
- Cache Settings
- Debug Mode
```

---

## 🎨 Styling & Customization

### **CSS Classes:**
```css
/* Schema */
.kata-schema-wrapper

/* Social Share */
.kata-social-share
.kata-social-btn
.kata-social-facebook
.kata-social-twitter

/* Quote */
.kata-quote
.kata-quote-classic
.kata-quote-modern
.kata-quote-hashtags

/* Gamification */
.kata-quiz
.kata-wheel
.kata-challenge

/* Engagement */
.kata-rating
.kata-poll
.kata-comments-enhanced

/* FAQ */
.kata-faq
.kata-faq-accordion
.kata-faq-item

/* CTA & Forms */
.kata-cta
.kata-cta-box
.kata-form
.kata-form-field
```

### **Custom CSS:**
Thêm custom CSS trong:
- Admin → Kata SEO → Settings → Advanced → Custom CSS
- Hoặc trong theme's style.css

---

## 🔌 Hooks & Filters

### **Actions:**
```php
do_action('kata_seo_before_schema_output', $post_id);
do_action('kata_seo_after_quiz_submit', $quiz_id, $score, $user_id);
do_action('kata_seo_after_rating_submit', $post_id, $rating, $user_id);
do_action('kata_seo_after_form_submit', $form_id, $data);
```

### **Filters:**
```php
apply_filters('kata_seo_schema_data', $schema, $post_id);
apply_filters('kata_seo_social_platforms', $platforms);
apply_filters('kata_seo_quiz_score', $score, $quiz_id);
apply_filters('kata_seo_cta_button_text', $text, $atts);
```

---

## 📖 Usage Examples

### **Example 1: Blog Post với đầy đủ SEO**
```php
// Trong content editor:

[kata_quote style="modern" author="Bill Gates" hashtags="#seo #marketing"]
Content is King, but Distribution is Queen
[/kata_quote]

// Main content here...

[kata_faq style="accordion" schema="yes"]

[kata_quiz id="seo-quiz-basic" title="Test Your SEO Knowledge"]

[kata_rating show_average="yes" allow_review="yes"]

[kata_social_share platforms="facebook,twitter,linkedin" style="circle" size="large"]

[kata_cta style="box" button_text="Tìm hiểu thêm" button_url="/khoa-hoc-seo"]
Đăng ký khóa học SEO chuyên sâu - Giảm 50% trong tuần này!
[/kata_cta]
```

### **Example 2: Landing Page với Gamification**
```php
[kata_wheel id="lucky-wheel" prizes="..."]

[kata_poll id="survey" question="Bạn quan tâm đến khóa học nào?"]

[kata_form id="registration" title="Đăng ký nhận ưu đãi"]
```

---

## 🚀 Performance Optimization

### **Best Practices:**
1. ✅ Enable caching for schema markup
2. ✅ Lazy load quiz/poll data
3. ✅ Minify CSS/JS in production
4. ✅ Use CDN for assets
5. ✅ Database cleanup cho old submissions
6. ✅ Optimize images in wheel/prizes

### **Caching:**
```php
// Schema cache: 24 hours
// Social share counts: 1 hour  
// Poll results: 5 minutes
// Form submissions: No cache
```

---

## 🔒 Security Features

- ✅ Nonce verification for all AJAX
- ✅ Data sanitization & validation
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ CSRF protection
- ✅ reCAPTCHA for forms
- ✅ Rate limiting for gamification
- ✅ IP-based spam prevention

---

## 📱 Mobile Responsive

All features are fully responsive:
- ✅ Touch-friendly quiz/poll interfaces
- ✅ Mobile-optimized social share buttons
- ✅ Responsive FAQ accordion
- ✅ Mobile-friendly forms
- ✅ Adaptive CTA positioning

---

## 🌐 Multilingual Support

- ✅ Translation ready (.pot file included)
- ✅ WPML compatible
- ✅ Polylang compatible
- ✅ RTL support

---

## 📞 Support & Documentation

- Documentation: https://timona.vn/docs/kata-seo-tools
- Support Forum: https://timona.vn/support
- Video Tutorials: https://youtube.com/timona
- Email: support@timona.vn

---

## 🎉 Pro Version Features (Coming Soon)

- 🔥 Advanced Analytics with AI insights
- 🔥 A/B Testing for CTAs
- 🔥 Advanced Form Builder
- 🔥 Email Marketing Integration
- 🔥 WooCommerce Integration
- 🔥 Custom Quiz Templates
- 🔥 Advanced Wheel Customization
- 🔥 White Label Options

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-03  
**Author:** Kata Team  
**License:** GPL v2 or later
