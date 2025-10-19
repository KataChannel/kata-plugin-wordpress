# 🤖 KATA Smart Chatbot - Complete Guide

## 📋 Tổng quan

**KATA Smart Chatbot** là tính năng AI-powered chatbot được tích hợp vào plugin KATA SEO Manager. Chatbot tự động phát hiện nội dung trang và tương tác thông minh với người dùng để:

✅ Tư vấn khóa học
✅ Thu thập thông tin leads
✅ Hỗ trợ khách hàng
✅ Tăng conversion rate

---

## 🎯 Tính năng chính

### 1. **Content Detection** (Nhận diện nội dung)

Chatbot tự động phân tích:
- **Keywords** trong bài viết (khóa học, đào tạo, học phí...)
- **Schema Types** (Course, FAQ, Article, Product...)
- **Scroll Depth** - theo dõi người dùng đọc được bao nhiêu %
- **Categories & Tags** - hiểu ngữ cảnh nội dung

### 2. **Smart Triggers** (Kích hoạt thông minh)

- ⏱️ **Time-based**: Tự động mở sau X giây
- 📜 **Scroll-based**: Mở khi scroll đến Y% trang
- 🚪 **Exit-intent**: Mở khi chuột di chuyển ra ngoài (sắp rời trang)
- 🔍 **Keyword-based**: Kích hoạt khi phát hiện từ khóa quan trọng

### 3. **Contextual Messages** (Tin nhắn theo ngữ cảnh)

Chatbot gửi tin nhắn phù hợp dựa trên nội dung:

| Content Type | Message Example |
|-------------|----------------|
| Course Schema | "👋 Bạn đang quan tâm đến khóa học này? Tôi có thể tư vấn chi tiết!" |
| FAQ Schema | "💡 Bạn có câu hỏi nào khác? Tôi sẵn sàng hỗ trợ!" |
| Article | "📚 Bạn muốn tìm hiểu thêm? Chúng tôi có khóa học chuyên sâu!" |
| Exit Intent | "⏰ Đừng bỏ lỡ! Để lại thông tin nhận tư vấn miễn phí!" |

### 4. **AI-Powered Responses**

- 🤖 Tích hợp OpenAI (ChatGPT) hoặc Anthropic (Claude)
- 💬 Trả lời thông minh dựa trên context
- 📝 Lead capture form tự động
- 📊 Tracking và analytics

---

## ⚙️ Cài đặt

### Bước 1: Kích hoạt Plugin

Plugin đã được tích hợp sẵn trong **KATA SEO Manager**. Sau khi activate plugin:

```
WordPress Admin → Plugins → KATA SEO Manager → Activate
```

### Bước 2: Cấu hình Chatbot

Vào **KATA SEO Manager → Smart Chatbot**

#### Tab "Cài đặt chung"

- ✅ **Kích hoạt Chatbot**: Bật/tắt toàn bộ chatbot
- 📝 **Tên Bot**: Đặt tên hiển thị (mặc định: "KATA Assistant")
- 📍 **Vị trí**: Góc dưới phải / Góc dưới trái

#### Tab "Kích hoạt"

- ⏱️ **Tự động mở**: Bật để chatbot tự mở
- ⏲️ **Thời gian delay**: 5 giây (khuyến nghị)
- 📜 **Scroll trigger**: 50% (mở khi scroll nửa trang)
- 🚪 **Exit Intent**: Bật để giữ chân khách hàng

#### Tab "Giao diện"

- 🎨 **Màu chính**: #042277 (màu Timona)
- 🎨 **Màu phụ**: #040B1E (màu gradient)
- 🖼️ **Avatar**: Upload logo/ảnh bot

#### Tab "Tin nhắn"

- 💬 **Welcome message**: "Xin chào! Tôi có thể giúp gì cho bạn?"
- 📝 Contextual messages tự động phát hiện

#### Tab "Liên hệ"

- ☎️ **Hotline**: 1900 xxxx
- 📧 **Email**: support@timona.edu.vn
- 📍 **Địa chỉ**: Địa chỉ văn phòng

#### Tab "AI Integration" (Optional)

- 🤖 **AI Provider**: OpenAI / Anthropic
- 🔑 **API Key**: Nhập API key
- 🧠 **Model**: gpt-3.5-turbo / gpt-4

---

## 📊 Quản lý Leads

### Xem Chat Logs

**KATA SEO Manager → Chat Logs**

- 💬 Xem tất cả cuộc trò chuyện
- 🔍 Filter theo session, type, date
- 📈 Phân tích hành vi người dùng

### Quản lý Leads

**KATA SEO Manager → Leads**

- 👥 Danh sách leads thu thập được
- 📞 Thông tin liên hệ (tên, email, phone)
- 📊 Trạng thái: Mới → Đã liên hệ → Đủ điều kiện → Đã chuyển đổi
- 💼 Quản lý pipeline sales

#### Lead Statuses

| Status | Icon | Ý nghĩa |
|--------|------|---------|
| New | 🆕 | Lead mới, chưa liên hệ |
| Contacted | 📞 | Đã gọi điện/email |
| Qualified | ✅ | Lead tiềm năng cao |
| Converted | 💰 | Đã đăng ký/mua khóa học |
| Lost | ❌ | Không quan tâm nữa |

---

## 💡 Best Practices

### 1. Cấu hình Triggers hợp lý

```
✅ Good:
- Auto-open delay: 5-10 giây (đủ thời gian đọc)
- Scroll trigger: 40-60% (đã quan tâm nội dung)
- Exit intent: BẬT (giữ chân khách hàng)

❌ Bad:
- Auto-open ngay lập tức (annoying)
- Scroll trigger quá thấp (10%) - chưa đọc gì
- Tắt exit intent (bỏ lỡ cơ hội)
```

### 2. Viết tin nhắn hiệu quả

```markdown
✅ Good Message:
"👋 Chào bạn! Tôi thấy bạn đang quan tâm khóa học Thẩm Mỹ Quốc Tế. 
Bạn muốn biết thêm về:
1️⃣ Nội dung khóa học
2️⃣ Học phí & ưu đãi
3️⃣ Lịch khai giảng"

❌ Bad Message:
"Xin chào" (quá chung chung, không context)
```

### 3. Tối ưu Lead Form

- ✅ Chỉ hỏi thông tin cần thiết (tên, email, phone)
- ✅ Có dropdown "Bạn quan tâm đến..."
- ✅ Message box để khách hàng ghi chú
- ❌ Không hỏi quá nhiều (form dài → bỏ cuộc)

### 4. Follow-up Leads nhanh

```
🆕 New Lead → Gọi điện trong 5 phút (conversion rate cao nhất!)
📞 Contacted → Gửi email follow-up sau 2 ngày
✅ Qualified → Tư vấn chi tiết, gửi brochure
💰 Converted → Chăm sóc sau bán, upsell
```

---

## 🔧 Customization

### Hook: Custom AI Response

```php
add_filter('kata_chatbot_ai_response', function($response, $message, $context) {
    // Custom logic để tạo response
    if (strpos($message, 'học phí') !== false) {
        return array(
            'message' => 'Học phí chi tiết: [link]',
            'type' => 'custom'
        );
    }
    return $response;
}, 10, 3);
```

### Hook: Custom Contextual Message

```php
add_filter('kata_chatbot_contextual_messages', function($messages, $context) {
    // Thêm message riêng cho category cụ thể
    if (in_array('Khóa học Nail', $context['categories'])) {
        $messages[] = array(
            'type' => 'nail_course',
            'message' => 'Bạn quan tâm khóa Nail Art? Giảm 20% trong tuần này!',
            'delay' => 5000
        );
    }
    return $messages;
}, 10, 2);
```

---

## 📈 Analytics & Tracking

### Metrics quan trọng

1. **Total Chats**: Tổng số cuộc trò chuyện
2. **Total Messages**: Tổng tin nhắn (user + bot)
3. **Total Leads**: Số leads thu thập được
4. **Conversion Rate**: % chats → leads
5. **Response Time**: Thời gian trả lời trung bình

### Xem Statistics

**Dashboard → KATA SEO Manager → Smart Chatbot**

- 📊 Stats 30 ngày gần nhất
- 📈 Biểu đồ conversion
- 🎯 Top keywords kích hoạt chatbot

---

## 🐛 Troubleshooting

### Chatbot không hiển thị?

```
✅ Kiểm tra:
1. Plugin KATA SEO Manager đã activate?
2. Settings → "Kích hoạt Chatbot" = BẬT
3. Cache đã clear chưa?
4. Console có lỗi JS không?
```

### Chatbot không tự động mở?

```
✅ Kiểm tra:
1. "Tự động mở" = BẬT
2. "Thời gian delay" > 0
3. Đã scroll đủ % chưa? (nếu dùng scroll trigger)
```

### Lead không lưu được?

```
✅ Kiểm tra:
1. Database tables đã tạo? (wp_kata_chatbot_leads)
2. Email format đúng chưa?
3. Xem error log: wp-content/debug.log
```

### AI không hoạt động?

```
✅ Kiểm tra:
1. "AI Enabled" = BẬT
2. API Key đã nhập đúng?
3. API Key còn credit không?
4. Model name đúng chưa? (gpt-3.5-turbo)
```

---

## 🚀 Advanced Features

### 1. Multi-language Support

```php
// Tự động phát hiện ngôn ngữ
if (function_exists('pll_current_language')) {
    $lang = pll_current_language();
    // Đổi welcome message theo ngôn ngữ
}
```

### 2. Integration với CRM

```php
add_action('kata_chatbot_lead_saved', function($lead_id, $lead_data) {
    // Push to HubSpot, Salesforce, etc.
    push_to_crm($lead_data);
}, 10, 2);
```

### 3. A/B Testing Messages

```php
// Test 2 versions của welcome message
$messages = array(
    'Xin chào! Tôi có thể giúp gì?',
    'Hi! Bạn đang tìm khóa học nào?'
);
$welcome = $messages[rand(0, 1)];
```

---

## 📞 Support

- 📧 Email: support@katachannel.com
- 📱 Hotline: 1900 xxxx
- 💬 Facebook: /KATAChannel
- 🌐 Website: katachannel.com

---

## 📝 Changelog

### Version 2.2.0 (2025-10-15)
- ✨ Initial release of KATA Smart Chatbot
- 🎯 Content detection & contextual messaging
- 🤖 AI integration (OpenAI & Claude)
- 📊 Lead management system
- 📈 Analytics dashboard

---

**© 2025 KATA Channel - All Rights Reserved**
