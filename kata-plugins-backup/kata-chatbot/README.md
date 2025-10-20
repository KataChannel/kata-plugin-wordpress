# 🤖 Kata Chatbot Plugin for WordPress

**Trí tuệ nhân tạo hỗ trợ khách hàng 24/7 với Google AI Studio**

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-green)
![PHP](https://img.shields.io/badge/PHP-7.4+-orange)
![License](https://img.shields.io/badge/license-GPL%202.0-red)

## 📋 Mô tả

Kata Chatbot là plugin WordPress mạnh mẽ tích hợp trí tuệ nhân tạo Google Gemini để cung cấp dịch vụ hỗ trợ khách hàng tự động. Plugin được thiết kế đặc biệt cho thị trường Việt Nam với giao diện thân thiện và khả năng hiểu tiếng Việt tự nhiên.

## ✨ Tính năng chính

### 🧠 AI-Powered
- **Google AI Studio Integration**: Sử dụng Gemini Pro model
- **Hiểu tiếng Việt tự nhiên**: Phản hồi chính xác và contextual
- **Knowledge Base**: Quản lý câu hỏi thường gặp
- **Learning System**: Cải thiện qua từng cuộc hội thoại

### 🎨 Giao diện đẹp
- **Responsive Design**: Tương thích mọi thiết bị
- **Customizable Widget**: Tùy chỉnh màu sắc, vị trí
- **Smooth Animations**: Hiệu ứng mượt mà
- **Emoji Support**: Picker emoji tích hợp

### 📊 Analytics & Quản lý
- **Dashboard tổng quan**: Thống kê chi tiết
- **Conversation History**: Lưu trữ toàn bộ hội thoại
- **Rating System**: Đánh giá chất lượng phản hồi
- **Performance Metrics**: Phân tích hiệu suất

### 🔒 Bảo mật
- **WordPress Nonce**: Chống CSRF attacks
- **SQL Injection Protection**: Sử dụng prepared statements
- **Data Sanitization**: Xử lý an toàn dữ liệu
- **Rate Limiting**: Giới hạn số lượng request

## 🚀 Cài đặt

### Yêu cầu hệ thống
- WordPress 5.0 trở lên
- PHP 7.4 trở lên
- MySQL 5.6 trở lên
- Google AI Studio API Key

### Cài đặt plugin

1. **Upload plugin**:
   ```bash
   cd /wp-content/plugins/
   # Upload thư mục kata-chatbot
   ```

2. **Kích hoạt plugin**:
   - Vào `WordPress Admin > Plugins`
   - Tìm "Kata Chatbot" và click "Activate"

3. **Cấu hình API Key**:
   - Vào `WordPress Admin > Kata Chatbot > Settings`
   - Nhập Google AI Studio API Key
   - Click "Save Settings"

### Lấy Google AI Studio API Key

1. Truy cập [Google AI Studio](https://aistudio.google.com/)
2. Đăng nhập với tài khoản Google
3. Tạo API Key mới
4. Copy và paste vào plugin settings

## 📁 Cấu trúc thư mục

```
kata-chatbot/
├── kata-chatbot.php           # Main plugin file
├── demo.php                   # Demo page
├── test-complete.php          # Complete test suite
├── includes/
│   ├── class-db-handler.php   # Database operations
│   ├── class-ai-handler.php   # Google AI integration
│   ├── class-chat-handler.php # Chat logic
│   └── class-admin.php        # Admin interface
├── assets/
│   └── js/
│       └── admin.js           # Admin dashboard JavaScript
├── templates/
│   └── chatbot-widget.php     # Frontend widget
└── README.md                  # Documentation
```

## ⚙️ Cấu hình

### Settings cơ bản

| Setting | Mô tả | Default |
|---------|-------|---------|
| `kata_chatbot_enabled` | Bật/tắt chatbot | `true` |
| `kata_chatbot_google_api_key` | Google AI API key | Empty |
| `kata_chatbot_welcome_message` | Tin nhắn chào mừng | "Xin chào! Tôi có thể giúp gì cho bạn?" |
| `kata_chatbot_position` | Vị trí widget | `bottom-right` |
| `kata_chatbot_primary_color` | Màu chủ đạo | `#2271b1` |

### Database Tables

Plugin tự động tạo 4 bảng:

- `wp_kata_chatbot_conversations`: Lưu thông tin cuộc hội thoại
- `wp_kata_chatbot_messages`: Lưu tin nhắn và phản hồi
- `wp_kata_chatbot_knowledge`: Knowledge base
- `wp_kata_chatbot_analytics`: Thống kê và phân tích

## 📋 Tổng quan

Kata Chatbot là một plugin WordPress mạnh mẽ tích hợp trí tuệ nhân tạo từ Google AI Studio (Gemini) để cung cấp dịch vụ hỗ trợ khách hàng tự động 24/7. Plugin lưu trữ toàn bộ cuộc hội thoại vào database và cung cấp dashboard quản lý chi tiết.

## ✨ Tính năng chính

### 🎯 Chatbot thông minh
- **AI Google Studio**: Tích hợp Gemini Pro model cho phản hồi thông minh
- **Xử lý ngôn ngữ tự nhiên**: Hiểu và phản hồi tiếng Việt tự nhiên
- **Gợi ý thông minh**: Đưa ra các gợi ý phù hợp dựa trên ngữ cảnh
- **Lệnh đặc biệt**: Hỗ trợ lệnh help, contact, services, reset

### 💾 Lưu trữ dữ liệu
- **Database hoàn chỉnh**: 4 bảng để lưu trữ conversations, messages, analytics, knowledge base
- **Lịch sử hội thoại**: Theo dõi toàn bộ cuộc trò chuyện của khách hàng
- **Phân tích chi tiết**: Thống kê về hiệu suất và độ hài lòng
- **Quản lý tri thức**: Hệ thống knowledge base để cải thiện phản hồi

### 🎨 Giao diện đẹp
- **Widget responsive**: Tương thích mọi thiết bị di động
- **3 chủ đề màu**: Blue, Green, Purple
- **4 vị trí hiển thị**: Bottom-right, bottom-left, bottom-center
- **Emoji picker**: Hỗ trợ biểu tượng cảm xúc
- **Âm thanh thông báo**: Tùy chọn bật/tắt âm thanh

### 📊 Dashboard quản lý
- **Thống kê realtime**: Tổng quan về hoạt động chatbot
- **Quản lý hội thoại**: Xem chi tiết từng cuộc trò chuyện
- **Đánh giá khách hàng**: Hệ thống feedback 5 sao
- **Cài đặt nhanh**: Bật/tắt các tính năng dễ dàng

## 🚀 Cài đặt

### Yêu cầu hệ thống
- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+
- Google AI Studio API Key

### Bước cài đặt

1. **Upload plugin**
   ```bash
   # Upload thư mục kata-chatbot vào wp-content/plugins/
   cp -r kata-chatbot /path/to/wordpress/wp-content/plugins/
   ```

2. **Kích hoạt plugin**
   - Vào WordPress Admin → Plugins
   - Tìm "Kata Chatbot" và click "Activate"

3. **Cấu hình API Key**
   - Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Tạo API key mới
   - Vào Settings → Kata Chatbot → API Settings
   - Nhập API key và lưu

4. **Tùy chỉnh widget**
   - Vào Settings → Kata Chatbot → Widget Settings
   - Chọn vị trí, màu sắc, tin nhắn chào
   - Lưu cài đặt

## ⚙️ Cấu hình

### API Settings
```php
// Cấu hình Google AI API
$api_key = 'YOUR_GOOGLE_AI_STUDIO_API_KEY';
$model = 'gemini-pro'; // Mặc định
$max_tokens = 1000;
$temperature = 0.7;
```

### Widget Settings
```php
// Tùy chỉnh giao diện
$position = 'bottom-right'; // bottom-left, bottom-center
$theme = 'blue'; // green, purple
$welcome_message = 'Xin chào! Tôi có thể giúp gì cho bạn? 😊';
$chatbot_name = 'Kata AI';
```

### Database Tables
Plugin tự động tạo 4 bảng:
- `wp_kata_chatbot_conversations` - Quản lý cuộc hội thoại
- `wp_kata_chatbot_messages` - Lưu trữ tin nhắn
- `wp_kata_chatbot_analytics` - Phân tích dữ liệu
- `wp_kata_chatbot_knowledge_base` - Cơ sở tri thức

## 🎯 Sử dụng

### Cho khách hàng
1. **Bắt đầu hội thoại**: Click vào icon chatbot ở góc trang
2. **Nhập tin nhắn**: Gõ câu hỏi hoặc yêu cầu hỗ trợ
3. **Nhận phản hồi**: AI sẽ trả lời ngay lập tức
4. **Sử dụng gợi ý**: Click vào các nút gợi ý nhanh
5. **Đánh giá**: Cho điểm và góp ý về trải nghiệm

### Cho admin
1. **Xem dashboard**: WordPress Admin → Kata Chatbot → Dashboard
2. **Quản lý hội thoại**: Xem chi tiết từng conversation
3. **Phân tích dữ liệu**: Theo dõi thống kê hiệu suất
4. **Cập nhật tri thức**: Thêm/sửa knowledge base
5. **Tùy chỉnh cài đặt**: Điều chỉnh các tham số chatbot

## 🛠️ API Reference

### AJAX Endpoints

#### Gửi tin nhắn
```javascript
// Frontend AJAX call
$.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'kata_chatbot_send_message',
        message: 'Tin nhắn của người dùng',
        session_id: 'session_id_unique',
        nonce: 'nonce_value'
    },
    success: function(response) {
        // Xử lý phản hồi
        console.log(response.data.message);
    }
});
```

#### Gửi feedback
```javascript
$.ajax({
    url: ajaxurl,
    type: 'POST', 
    data: {
        action: 'kata_chatbot_submit_feedback',
        session_id: 'session_id',
        rating: 5,
        feedback: 'Góp ý của khách hàng',
        nonce: 'nonce_value'
    }
});
```

### PHP Classes

#### Chat Handler
```php
$chat_handler = new KataChatbot_Chat_Handler();

// Xử lý tin nhắn
$response = $chat_handler->process_message(
    'Tin nhắn từ user',
    'session_id_optional'
);

// Kết thúc hội thoại
$chat_handler->end_conversation('session_id');
```

#### Database Handler
```php
$db_handler = new KataChatbot_DB_Handler();

// Lấy thống kê
$stats = $db_handler->get_dashboard_stats();

// Tạo cuộc hội thoại mới
$conversation = $db_handler->get_or_create_conversation(
    'session_id',
    $user_id
);
```

## 🎨 Tùy chỉnh giao diện

### CSS Classes
```css
/* Container chính */
.kata-chatbot-container {
    position: fixed;
    z-index: 999999;
}

/* Nút toggle */
.kata-chat-toggle {
    width: 60px;
    height: 60px;
    background: var(--kata-primary);
    border-radius: 50%;
}

/* Cửa sổ chat */
.kata-chat-window {
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
}

/* Tin nhắn */
.kata-message {
    display: flex;
    margin-bottom: 16px;
}

.kata-bot-message .kata-message-bubble {
    background: #f1f1f1;
    border-bottom-left-radius: 6px;
}

.kata-user-message .kata-message-bubble {
    background: var(--kata-primary);
    color: white;
    border-bottom-right-radius: 6px;
}
```

### JavaScript Events
```javascript
// Lắng nghe sự kiện chatbot
document.addEventListener('kata_chatbot_opened', function() {
    console.log('Chatbot đã mở');
});

document.addEventListener('kata_chatbot_message_sent', function(e) {
    console.log('Tin nhắn đã gửi:', e.detail.message);
});

document.addEventListener('kata_chatbot_response_received', function(e) {
    console.log('Nhận phản hồi:', e.detail.response);
});
```

## 📱 Responsive Design

Plugin được tối ưu cho mọi thiết bị:

### Desktop (>768px)
- Widget cố định ở góc màn hình
- Kích thước 350x500px
- Hiển thị đầy đủ tính năng

### Tablet (768px - 480px) 
- Widget điều chỉnh kích thước
- Layout linh hoạt
- Ẩn một số nút phụ

### Mobile (<480px)
- Widget full-width
- Height điều chỉnh theo viewport
- Touch-friendly interface

## 🔒 Bảo mật

### Input Sanitization
```php
// Tất cả input đều được sanitize
$message = sanitize_textarea_field($_POST['message']);
$session_id = sanitize_text_field($_POST['session_id']);
```

### Nonce Verification
```php
// Verify nonce cho mọi AJAX request
if (!wp_verify_nonce($_POST['nonce'], 'kata_chatbot_public')) {
    wp_die('Security check failed');
}
```

### Rate Limiting
```php
// Giới hạn số tin nhắn mỗi phiên
$message_count = $this->get_session_message_count($session_id);
if ($message_count > 50) {
    throw new Exception('Rate limit exceeded');
}
```

### SQL Injection Protection
```php
// Sử dụng prepared statements
$wpdb->prepare(
    "SELECT * FROM {$conversations_table} WHERE session_id = %s",
    $session_id
);
```

## 📊 Analytics & Tracking

### Metrics được theo dõi
- **Tổng cuộc hội thoại**: Số lượng conversations
- **Tổng tin nhắn**: Số messages đã xử lý  
- **Thời gian phản hồi**: Average response time
- **Tỷ lệ hài lòng**: Customer satisfaction rate
- **Người dùng hoạt động**: Active users
- **Đánh giá trung bình**: Average rating

### Dashboard widgets
- **Stats overview**: Thống kê tổng quan
- **Recent conversations**: Hội thoại gần đây
- **Performance metrics**: Chỉ số hiệu suất
- **System status**: Trạng thái hệ thống

## 🧪 Testing

### Chạy test plugin
```bash
# Truy cập test file
http://yoursite.com/wp-content/plugins/kata-chatbot/test-plugin.php
```

### Unit Tests
```php
// Test AI Handler
$ai_handler = new KataChatbot_AI_Handler();
$response = $ai_handler->generate_response('Test message');

// Test DB Handler  
$db_handler = new KataChatbot_DB_Handler();
$stats = $db_handler->get_dashboard_stats();

// Test Chat Handler
$chat_handler = new KataChatbot_Chat_Handler();
$result = $chat_handler->process_message('Hello', 'test_session');
```

## 🚨 Troubleshooting

### Lỗi thường gặp

#### 1. API Key không hoạt động
```php
// Kiểm tra API key
$api_key = get_option('kata_chatbot_google_api_key');
if (empty($api_key) || strpos($api_key, 'AIza') !== 0) {
    // API key không hợp lệ
}
```

#### 2. Database tables không tạo được
```sql
-- Tạo bảng thủ công
CREATE TABLE `wp_kata_chatbot_conversations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `session_id` varchar(255) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `status` enum('active','closed','archived') DEFAULT 'active',
    `started_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `last_activity` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `session_id` (`session_id`)
);
```

#### 3. Widget không hiển thị
```javascript
// Kiểm tra JavaScript console
console.log('Kata Chatbot container:', document.getElementById('kata-chatbot-container'));

// Kiểm tra CSS conflicts
.kata-chatbot-container {
    z-index: 999999 !important;
    position: fixed !important;
}
```

#### 4. AJAX requests lỗi
```php
// Enable WordPress debug
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Kiểm tra error logs
tail -f /path/to/wordpress/wp-content/debug.log
```

### Debug mode
```php
// Bật debug mode trong wp-config.php
define('KATA_CHATBOT_DEBUG', true);

// Logs sẽ được ghi vào
/wp-content/debug.log
```

## 🔄 Updates & Maintenance

### Cập nhật plugin
1. Backup database và files
2. Upload phiên bản mới
3. Chạy lại activation hooks
4. Test functionality

### Backup dữ liệu
```sql
-- Export chatbot data
mysqldump -u username -p database_name \
  wp_kata_chatbot_conversations \
  wp_kata_chatbot_messages \
  wp_kata_chatbot_analytics \
  wp_kata_chatbot_knowledge_base > kata_chatbot_backup.sql
```

### Performance optimization
```php
// Dọn dẹp dữ liệu cũ (chạy hàng tuần)
wp_schedule_event(time(), 'weekly', 'kata_chatbot_cleanup');

function kata_chatbot_cleanup() {
    global $wpdb;
    
    // Xóa conversations cũ hơn 90 ngày
    $wpdb->query($wpdb->prepare(
        "DELETE FROM {$wpdb->prefix}kata_chatbot_conversations 
         WHERE started_at < %s",
        date('Y-m-d H:i:s', strtotime('-90 days'))
    ));
}
```

## 📞 Hỗ trợ

### Liên hệ
- **Email**: support@timona.vn
- **Website**: https://timona.vn
- **Phone**: 0123-456-789

### Documentation
- **User Guide**: https://timona.vn/docs/kata-chatbot/user-guide
- **Developer API**: https://timona.vn/docs/kata-chatbot/api
- **Video Tutorials**: https://timona.vn/docs/kata-chatbot/videos

### Community
- **GitHub**: https://github.com/timona/kata-chatbot
- **Support Forum**: https://timona.vn/support/kata-chatbot

## 📄 License

MIT License - Xem file LICENSE để biết chi tiết.

## 🎯 Roadmap

### Version 1.1 (Coming Soon)
- [ ] Tích hợp WhatsApp Business API
- [ ] Multi-language support
- [ ] Voice message support
- [ ] Advanced analytics dashboard

### Version 1.2 (Q2 2024)
- [ ] AI model fine-tuning
- [ ] Live chat handover
- [ ] Integration với CRM systems
- [ ] Mobile app companion

### Version 2.0 (Q3 2024)
- [ ] Multi-agent conversations
- [ ] Advanced NLP capabilities
- [ ] Chatbot marketplace
- [ ] Enterprise features

---

**Made with ❤️ by Timona Team**

*Plugin này được phát triển để cung cấp trải nghiệm chatbot AI tốt nhất cho website WordPress của bạn.*
