# Phân Tích Schema Types Còn Thiếu

## Hiện có trong Customizer (15):
1. faq
2. article
3. recipe
4. product
5. event
6. howto
7. quiz
8. poll
9. wheel
10. course
11. localbusiness
12. jobposting
13. video
14. organization
15. rating

## Shortcodes Quan Trọng Còn Thiếu:

### 🔴 Priority HIGH (Google Rich Results):
1. **breadcrumb** - BreadcrumbList schema (rất quan trọng cho SEO)
2. **book** - Book schema
3. **movie** - Movie schema
4. **review** - Review schema (khác với rating)
5. **newsarticle** - NewsArticle (subtype của Article)
6. **blogposting** - BlogPosting (subtype của Article)

### 🟡 Priority MEDIUM:
7. **person** - Person schema
8. **software** - SoftwareApplication
9. **webpage** - WebPage
10. **website** - WebSite
11. **creativework** - CreativeWork (base type)
12. **service** - Service schema

### 🟢 Priority LOW (Specialized):
13. **dataset** - Dataset
14. **vehicle** - Vehicle
15. **realestate** - RealEstate
16. **restaurant** - Restaurant
17. **forum** - Forum/Discussion
18. **carousel** - Carousel/ItemList
19. **image_metadata** - ImageObject
20. **sitelinks** - SearchAction (sitelinks)

## Đề xuất:

Thêm **6 schema types HIGH priority** trước:
1. breadcrumb (BreadcrumbList)
2. book (Book)
3. movie (Movie)
4. review (Review)
5. newsarticle (NewsArticle)
6. blogposting (BlogPosting)

Sau đó thêm MEDIUM priority nếu cần.
