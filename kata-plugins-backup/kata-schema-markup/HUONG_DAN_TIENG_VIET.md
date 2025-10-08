# Hướng dẫn sử dụng Plugin Kata Schema Markup (Tiếng Việt)

## Cài đặt ngôn ngữ tiếng Việt

Plugin Kata Schema Markup đã được cập nhật với bản dịch tiếng Việt đầy đủ. Để kích hoạt:

### 1. Kiểm tra cấu hình WordPress
- File `wp-config.php` đã được cập nhật với `define('WPLANG', 'vi');`
- Đảm bảo WordPress đang sử dụng locale `vi_VN`

### 2. Files ngôn ngữ đã được tạo
- `languages/kata-schema-markup-vi.po` - File dịch thuật
- `languages/kata-schema-markup-vi.mo` - File nhị phân compiled
- `languages/kata-schema-markup.pot` - File template

### 3. Các thuật ngữ đã dịch chính

#### Menu và Navigation
- **Schema Markup** → Schema Markup (giữ nguyên)
- **Dashboard** → Bảng điều khiển
- **Templates** → Mẫu
- **Validation** → Kiểm tra
- **Settings** → Cài đặt
- **Tools** → Công cụ

#### Các loại Schema
- **Article** → Bài viết
- **Product** → Sản phẩm
- **Organization** → Tổ chức
- **Person** → Người
- **Event** → Sự kiện
- **Recipe** → Công thức
- **Review** → Đánh giá

#### Hành động
- **Generate Schema** → Tạo Schema
- **Validate Schema** → Kiểm tra Schema
- **Test with Google** → Kiểm tra với Google
- **Enable Schema** → Bật Schema
- **Save Changes** → Lưu thay đổi

#### Trạng thái
- **Active** → Hoạt động
- **Inactive** → Không hoạt động
- **Valid** → Hợp lệ
- **Error** → Lỗi
- **Warning** → Cảnh báo

### 4. Cách sử dụng

#### Bước 1: Truy cập Admin
Vào **WordPress Admin** → **Schema Markup**

#### Bước 2: Cấu hình cơ bản
1. Vào tab **Cài đặt**
2. Bật "Tự động tạo schema markup"
3. Chọn "Loại Schema mặc định"
4. Lưu thay đổi

#### Bước 3: Sử dụng cho bài viết
1. Chỉnh sửa bài viết/trang
2. Tìm meta box "Schema Markup"
3. Bật "Bật Schema"
4. Chọn "Loại Schema" phù hợp
5. Điền thông tin bổ sung nếu cần
6. Nhấn "Kiểm tra Schema" để validate
7. Lưu bài viết

#### Bước 4: Tạo Template
1. Vào tab **Mẫu**
2. Nhấn "Thêm Mẫu Mới"
3. Nhập "Tên Mẫu"
4. Chọn "Loại Schema"
5. Chọn "Loại Bài viết" áp dụng
6. Nhập JSON-LD template
7. Lưu mẫu

#### Bước 5: Kiểm tra và Validation
1. Vào tab **Kiểm tra**
2. Nhấn "Kiểm tra Tất cả Bài viết"
3. Xem kết quả validation
4. Sửa lỗi nếu có

### 5. Công cụ hữu ích

#### Tab Công cụ
- **Trình tạo Schema**: Tạo schema cho các loại nội dung
- **Kiểm tra Schema**: Validate JSON schema
- **Thao tác Hàng loạt**: Xử lý nhiều bài viết cùng lúc
- **Di chuyển Dữ liệu**: Import/Export và migrate từ plugin khác

### 6. Lưu ý quan trọng

#### Cache
- Bật "Cài đặt Cache" để tăng hiệu suất
- Xóa cache khi cần thiết

#### Debug
- Bật "Chế độ Gỡ lỗi" khi development
- Tắt trên site production

#### Validation
- Thường xuyên kiểm tra với Google Rich Results Test
- Theo dõi Google Search Console

### 7. Troubleshooting

#### Ngôn ngữ không hiển thị tiếng Việt
1. Kiểm tra `wp-config.php` có `define('WPLANG', 'vi');`
2. Xóa cache browser và WordPress
3. Kiểm tra file `.mo` có tồn tại trong thư mục `languages/`

#### Schema không hiển thị
1. Kiểm tra "Bật Schema" đã được chọn
2. Kiểm tra cache đã được xóa
3. View source code để xem JSON-LD

#### Lỗi validation
1. Sử dụng "Kiểm tra Schema" trong admin
2. Kiểm tra với Google Rich Results Test
3. Xem log lỗi WordPress

---

## Liên hệ hỗ trợ

Nếu gặp vấn đề với bản dịch tiếng Việt hoặc plugin, vui lòng:
1. Kiểm tra log lỗi WordPress
2. Sử dụng tab "Kiểm tra" để validate
3. Báo cáo lỗi qua GitHub hoặc support

---

*Plugin đã được dịch hoàn toàn sang tiếng Việt và sẵn sàng sử dụng!*
