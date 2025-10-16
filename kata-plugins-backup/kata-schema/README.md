# KATA Schema Manager Plugin

**Version:** 1.0.0  
**Author:** KATA Team  
**License:** GPL v2 or later

## 📋 Mô Tả

KATA Schema Manager là plugin WordPress chuyên nghiệp giúp quản lý và chèn Schema Markup (JSON-LD) vào website một cách dễ dàng. Plugin được thiết kế với kiến trúc senior-level, dễ bảo trì và mở rộng.

### ✨ Tính Năng Chính

1. **Quản Lý Schema Templates**
   - 10 loại schema mặc định (Article, Product, LocalBusiness, FAQ, HowTo, v.v.)
   - Tạo và chỉnh sửa schema tùy chỉnh
   - Lưu trữ schema với dữ liệu mẫu sẵn có

2. **TinyMCE Integration**
   - Nút "KATA Schema" trên editor
   - Modal chọn schema theo từng loại
   - Chèn shortcode tự động

3. **Frontend Rendering**
   - Xuất JSON-LD schema vào `<head>`
   - Tùy chọn hiển thị visual trên frontend
   - Tối ưu hóa cho Google Rich Results

4. **Admin Dashboard**
   - Thống kê schema theo loại
   - Quản lý CRUD đầy đủ
   - Copy shortcode 1 click

---

## 🚀 Cài Đặt

### Cách 1: Upload thủ công

1. Tải folder `kata-schema` vào `/wp-content/plugins/`
2. Truy cập **Plugins** trong WordPress Admin
3. Kích hoạt **KATA Schema Manager**

### Cách 2: Từ WordPress Admin

1. Vào **Plugins → Add New**
2. Upload file `kata-schema.zip`
3. Kích hoạt plugin

---

## 📚 Hướng Dẫn Sử Dụng

### Bước 1: Tạo Schema

**Cách A: Sử dụng Template có sẵn**

1. Vào **KATA Schema → Templates**
2. Chọn loại schema phù hợp (Article, Product, FAQ, v.v.)
3. Click **"Sử Dụng Template"**
4. Chỉnh sửa dữ liệu JSON theo nội dung của bạn
5. Click **"Tạo Schema"**

**Cách B: Tạo Schema tùy chỉnh**

1. Vào **KATA Schema → Thêm Schema Mới**
2. Nhập tên schema
3. Chọn loại schema
4. Nhập dữ liệu JSON (có thể dựa vào template)
5. Click **"Kiểm Tra JSON"** để validate
6. Click **"Format JSON"** để làm đẹp code
7. Click **"Tạo Schema"**

### Bước 2: Chèn Schema Vào Bài Viết

**Cách A: Dùng TinyMCE Button (Khuyến nghị)**

1. Mở bài viết/trang trong Editor
2. Đặt con trỏ vào vị trí muốn chèn
3. Click nút **"KATA Schema"** trên toolbar
4. Chọn schema từ danh sách
5. Schema sẽ được chèn dưới dạng shortcode

**Cách B: Copy Shortcode**

1. Vào **KATA Schema → Dashboard**
2. Copy shortcode từ cột "Shortcode"
3. Paste vào bài viết

### Bước 3: Kiểm Tra Schema

1. Xuất bản bài viết
2. Truy cập bài viết trên frontend
3. View source và tìm `<script type="application/ld+json">`
4. Copy JSON schema
5. Paste vào [Google Rich Results Test](https://search.google.com/test/rich-results)
6. Kiểm tra kết quả

---

## 🎯 Shortcode

### Cú Pháp Cơ Bản

```
[kata_schema id="1"]
```

### Tùy Chọn

| Tham số | Giá trị | Mặc định | Mô tả |
|---------|---------|----------|-------|
| `id` | số nguyên | bắt buộc | ID của schema |
| `show_schema` | true/false | true | Có xuất schema markup không |
| `show_frontend` | true/false | false | Có hiển thị visual không |

### Ví Dụ

```
[kata_schema id="5"]
```
Chỉ xuất schema markup (ẩn trên frontend)

```
[kata_schema id="5" show_frontend="true"]
```
Xuất schema + hiển thị visual

```
[kata_schema id="5" show_schema="false" show_frontend="true"]
```
Chỉ hiển thị visual, không xuất schema

---

## 📂 Cấu Trúc Plugin

```
kata-schema/
├── kata-schema.php              # File chính
├── includes/
│   ├── class-database.php       # Quản lý database
│   ├── class-schema-templates.php # Templates mặc định
│   ├── class-schema-renderer.php  # Render frontend
│   └── class-tinymce-integration.php # TinyMCE button
├── admin/
│   ├── dashboard.php            # Trang danh sách
│   ├── add-schema.php           # Trang thêm/sửa
│   └── templates.php            # Trang templates
├── assets/
│   ├── css/
│   │   └── admin.css            # Admin styles
│   └── js/
│       ├── admin.js             # Admin scripts
│       └── tinymce-button.js    # TinyMCE plugin
└── README.md                     # Documentation
```

---

## 🗄️ Database Schema

### Bảng: `wp_kata_schemas`

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | bigint(20) | Primary key |
| schema_name | varchar(255) | Tên schema |
| schema_type | varchar(100) | Loại schema (Article, Product, v.v.) |
| schema_data | longtext | Dữ liệu JSON |
| status | varchar(20) | active/inactive |
| created_at | datetime | Ngày tạo |
| updated_at | datetime | Ngày cập nhật |

---

## 🎨 Schema Types

Plugin hỗ trợ 10 loại schema phổ biến:

1. **Article** - Bài viết, blog post
2. **LocalBusiness** - Doanh nghiệp địa phương
3. **Product** - Sản phẩm
4. **FAQ** - Câu hỏi thường gặp
5. **HowTo** - Hướng dẫn
6. **Organization** - Tổ chức
7. **Person** - Nhân vật
8. **Event** - Sự kiện
9. **Recipe** - Công thức nấu ăn
10. **Course** - Khóa học

---

## ⚙️ Developer Guide

### Hook Actions

```php
// Sau khi save schema
do_action('kata_schema_saved', $schema_id, $schema_data);

// Sau khi delete schema
do_action('kata_schema_deleted', $schema_id);

// Trước khi output schema
do_action('kata_schema_before_output', $schema_id);
```

### Hook Filters

```php
// Filter schema data trước khi output
$schema = apply_filters('kata_schema_output', $schema, $schema_id);

// Filter schema types
$types = apply_filters('kata_schema_types', $types);

// Filter shortcode output
$output = apply_filters('kata_schema_shortcode_output', $output, $atts, $schema);
```

### Lấy Schema Programmatically

```php
// Get database instance
$database = new KATA_Schema_Database();

// Get all schemas
$schemas = $database->get_all_schemas();

// Get specific schema
$schema = $database->get_schema(5);

// Get schemas by type
$products = $database->get_schemas_by_type('Product');
```

### Tạo Schema Bằng Code

```php
$database = new KATA_Schema_Database();

$schema_data = array(
    'schema_name' => 'My Custom Article',
    'schema_type' => 'Article',
    'schema_data' => json_encode(array(
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => 'Article Title',
        // ... other properties
    )),
    'status' => 'active'
);

$schema_id = $database->save_schema($schema_data);
```

---

## 🔧 Troubleshooting

### Schema không hiển thị trên Google

1. Kiểm tra schema có xuất ra trong `<head>` không (View Source)
2. Validate schema bằng [Google Rich Results Test](https://search.google.com/test/rich-results)
3. Đảm bảo tất cả thuộc tính bắt buộc đã có
4. Chờ Google re-index (có thể mất vài ngày)

### TinyMCE button không hiển thị

1. Kiểm tra user có quyền `edit_posts` không
2. Kiểm tra Rich Editing đã bật chưa (User Profile)
3. Clear cache browser và WordPress
4. Kiểm tra console log có lỗi JavaScript không

### JSON không hợp lệ

1. Click nút **"Kiểm Tra JSON"** để xem lỗi cụ thể
2. Đảm bảo tất cả chuỗi có dấu ngoặc kép `"`
3. Kiểm tra dấu phẩy ở cuối mỗi property
4. Sử dụng [JSONLint](https://jsonlint.com/) để validate

---

## 📝 Changelog

### Version 1.0.0 (2025-01-15)

- ✅ Initial release
- ✅ 10 schema templates mặc định
- ✅ TinyMCE integration
- ✅ Admin dashboard với thống kê
- ✅ CRUD operations đầy đủ
- ✅ Frontend rendering với visual options
- ✅ JSON validation và formatting
- ✅ Shortcode system
- ✅ Database optimization
- ✅ Responsive admin UI

---

## 👥 Credits

- **Developer:** KATA Team
- **Inspired by:** kata-seo-manager plugin architecture
- **Schema Documentation:** [Schema.org](https://schema.org/)
- **Testing Tools:** [Google Rich Results Test](https://search.google.com/test/rich-results)

---

## 📄 License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025 KATA Team

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

## 🌟 Support

Nếu bạn gặp vấn đề hoặc có góp ý, vui lòng:

1. Kiểm tra [Troubleshooting](#-troubleshooting) section
2. Xem lại [Hướng dẫn sử dụng](#-hướng-dẫn-sử-dụng)
3. Liên hệ KATA Team để được hỗ trợ

---

**Made with ❤️ by KATA Team**
