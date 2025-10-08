<?php
/**
 * Kata Chatbot Demo Page
 * 
 * Simple demo page to test chatbot functionality
 */

// Load WordPress
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php';

// Set demo API key if not configured
$api_key = get_option('kata_chatbot_google_api_key');
if (empty($api_key)) {
    // For demo purposes, you should replace this with your actual API key
    update_option('kata_chatbot_google_api_key', 'YOUR_GOOGLE_AI_API_KEY_HERE');
}

// Enable chatbot for demo
update_option('kata_chatbot_enabled', true);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kata Chatbot Demo - Timona</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .demo-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            max-width: 800px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .logo {
            font-size: 3em;
            margin-bottom: 20px;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.2em;
            margin-bottom: 40px;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }
        
        .feature {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 12px;
            border-left: 4px solid #2271b1;
        }
        
        .feature-icon {
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .feature h3 {
            margin: 0 0 10px;
            color: #333;
        }
        
        .feature p {
            color: #666;
            margin: 0;
            line-height: 1.5;
        }
        
        .cta {
            margin-top: 40px;
            padding: 30px;
            background: linear-gradient(135deg, #2271b1, #135e96);
            border-radius: 12px;
            color: white;
        }
        
        .cta h3 {
            margin: 0 0 15px;
            font-size: 1.5em;
        }
        
        .demo-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        .demo-btn {
            background: white;
            color: #2271b1;
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .demo-btn:hover {
            background: #f0f6fc;
            transform: translateY(-2px);
        }
        
        .chatbot-status {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #00a32a;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .instructions {
            margin-top: 30px;
            padding: 20px;
            background: #fff3cd;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
            text-align: left;
        }
        
        .instructions h4 {
            margin: 0 0 10px;
            color: #856404;
        }
        
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #856404;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        @media (max-width: 768px) {
            .demo-container {
                padding: 20px;
                margin: 20px;
            }
            
            h1 {
                font-size: 2em;
            }
            
            .features {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .demo-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="chatbot-status">
        🤖 Chatbot Active
    </div>

    <div class="demo-container">
        <div class="logo">🤖</div>
        <h1>Kata Chatbot Demo</h1>
        <p class="subtitle">Trí tuệ nhân tạo hỗ trợ khách hàng 24/7</p>
        
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🧠</div>
                <h3>AI Google Studio</h3>
                <p>Tích hợp Gemini Pro model để hiểu và phản hồi tiếng Việt tự nhiên</p>
            </div>
            
            <div class="feature">
                <div class="feature-icon">💾</div>
                <h3>Lưu trữ dữ liệu</h3>
                <p>Toàn bộ cuộc hội thoại được lưu vào database để phân tích</p>
            </div>
            
            <div class="feature">
                <div class="feature-icon">📱</div>
                <h3>Responsive Design</h3>
                <p>Giao diện đẹp, tương thích mọi thiết bị di động</p>
            </div>
            
            <div class="feature">
                <div class="feature-icon">📊</div>
                <h3>Dashboard Analytics</h3>
                <p>Thống kê chi tiết về hiệu suất và độ hài lòng khách hàng</p>
            </div>
        </div>
        
        <div class="cta">
            <h3>🚀 Thử ngay Chatbot!</h3>
            <p>Click vào nút chatbot ở góc phải màn hình để bắt đầu trò chuyện</p>
            
            <div class="demo-buttons">
                <button class="demo-btn" onclick="openChatbot()">Mở Chatbot</button>
                <a href="<?php echo admin_url('admin.php?page=kata-chatbot'); ?>" class="demo-btn">Xem Dashboard</a>
                <a href="<?php echo admin_url('plugins.php'); ?>" class="demo-btn">Quản lý Plugin</a>
            </div>
        </div>
        
        <div class="instructions">
            <h4>📋 Hướng dẫn test:</h4>
            <ol>
                <li>Click vào icon chatbot ở góc phải để mở cửa sổ chat</li>
                <li>Thử gửi tin nhắn: "Xin chào", "Dịch vụ", "Liên hệ", "Báo giá"</li>
                <li>Test emoji picker và gợi ý nhanh</li>
                <li>Đánh giá chatbot bằng nút ⭐ trong chat</li>
                <li>Xem thống kê trong Dashboard admin</li>
            </ol>
        </div>
    </div>

    <?php
    // Include chatbot widget
    include_once ABSPATH . 'wp-content/plugins/kata-chatbot/templates/chatbot-widget.php';
    ?>

    <script>
        // Helper functions for demo
        function openChatbot() {
            if (window.KataChatbot) {
                window.KataChatbot.openChat();
            } else if (window.kataChatbotInstance) {
                window.kataChatbotInstance.openChat();
            } else {
                // Fallback - click the toggle button
                var toggle = document.getElementById('kata-chat-toggle');
                if (toggle) {
                    toggle.click();
                } else {
                    alert('Chatbot chưa được khởi tạo. Vui lòng reload trang.');
                }
            }
        }
        
        // Auto-show chatbot after 3 seconds for demo
        setTimeout(function() {
            var notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                bottom: 100px;
                right: 20px;
                background: #2271b1;
                color: white;
                padding: 12px 20px;
                border-radius: 25px;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                cursor: pointer;
                animation: bounce 2s infinite;
                z-index: 999998;
            `;
            notification.textContent = '👋 Thử chat với tôi!';
            notification.onclick = openChatbot;
            document.body.appendChild(notification);
            
            // Add bounce animation
            var style = document.createElement('style');
            style.textContent = `
                @keyframes bounce {
                    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                    40% { transform: translateY(-10px); }
                    60% { transform: translateY(-5px); }
                }
            `;
            document.head.appendChild(style);
            
            // Remove notification after 10 seconds
            setTimeout(function() {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 10000);
        }, 3000);
        
        // Demo data seeding (add some knowledge base entries)
        document.addEventListener('DOMContentLoaded', function() {
            // You could make AJAX calls here to seed demo data
            console.log('Kata Chatbot Demo loaded successfully!');
            
            // Add some CSS for better demo experience
            var demoStyles = document.createElement('style');
            demoStyles.textContent = `
                /* Demo-specific chatbot adjustments */
                .kata-chatbot-container {
                    z-index: 999999 !important;
                }
                
                /* Hide chatbot on mobile if demo container is visible */
                @media (max-width: 768px) {
                    .kata-chatbot-container {
                        bottom: 10px !important;
                        right: 10px !important;
                    }
                }
            `;
            document.head.appendChild(demoStyles);
        });
    </script>
    
    <?php wp_footer(); ?>
</body>
</html>
