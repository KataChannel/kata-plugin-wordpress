Dựa vào plugin kata manager seo
Tạo plugin kata-schema hoạt động như sau
1. Admin Quản lý, 
- Tạo Các schema mẫu mặc định với đầy đủ cấu trúc và dữ liệu mẫu
- Người dùng có thể tạo schema theo các mẫu đó hoặc tạo schema custom tùy chỉnh.
2. Các bài viết, page, block sử dụng các schema đã tạo ở Admin thông qua shortcode từ button của tinymce (List các schema từ admin)
3. Render và tạo <!-- KATA SEO Schema Markup --> <script type="application/ld+json"> đầy đủ ở front end
4. Cấu trúc file plugin like senior dễ chỉnh sửa và bảo trì. Đảm bảo hoạt động tốt. Có code để tạo database phát sinh trong plugin nếu có