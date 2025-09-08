<?php
/**
 * Chat Handler Class
 * 
 * Handles chat logic and message processing
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataChatbot_Chat_Handler {
    
    /**
     * AI handler instance
     */
    private $ai_handler;
    
    /**
     * Database handler instance
     */
    private $db_handler;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->ai_handler = new KataChatbot_AI_Handler();
        $this->db_handler = new KataChatbot_DB_Handler();
    }
    
    /**
     * Process incoming message
     */
    public function process_message($message, $session_id = null) {
        // Validate input
        if (empty($message)) {
            throw new Exception(__('Message cannot be empty', 'kata-chatbot'));
        }
        
        // Generate session ID if not provided
        if (empty($session_id)) {
            $session_id = $this->generate_session_id();
        }
        
        // Sanitize message
        $message = $this->sanitize_message($message);
        
        // Check message length
        $max_length = get_option('kata_chatbot_max_message_length', 1000);
        if (strlen($message) > $max_length) {
            throw new Exception(sprintf(__('Message is too long. Maximum %d characters allowed.', 'kata-chatbot'), $max_length));
        }
        
        // Get or create conversation
        $conversation = $this->db_handler->get_or_create_conversation(
            $session_id,
            is_user_logged_in() ? get_current_user_id() : null
        );
        
        // Save user message
        $user_message_id = $this->db_handler->save_message(
            $conversation->id,
            'user',
            $message
        );
        
        // Process message and generate AI response
        $ai_response = $this->generate_ai_response($message, $session_id);
        
        // Save bot response
        $bot_message_id = $this->db_handler->save_message(
            $conversation->id,
            'bot',
            $ai_response['message'],
            $ai_response
        );
        
        // Update conversation activity
        $this->update_conversation_activity($conversation->id);
        
        // Return response
        return array(
            'success' => true,
            'session_id' => $session_id,
            'conversation_id' => $conversation->id,
            'user_message_id' => $user_message_id,
            'bot_message_id' => $bot_message_id,
            'message' => $ai_response['message'],
            'response_time' => $ai_response['response_time'],
            'timestamp' => current_time('c'),
            'metadata' => array(
                'model' => $ai_response['model'],
                'success' => $ai_response['success']
            )
        );
    }
    
    /**
     * Generate AI response
     */
    private function generate_ai_response($message, $session_id) {
        // Check if chatbot is enabled
        if (!get_option('kata_chatbot_enabled', true)) {
            return array(
                'message' => get_option('kata_chatbot_offline_message', __('Chatbot is currently offline.', 'kata-chatbot')),
                'model' => 'offline',
                'response_time' => 0,
                'success' => false,
                'metadata' => array('offline' => true)
            );
        }
        
        // Get conversation context
        $context_limit = get_option('kata_chatbot_context_memory', 5);
        $context = $this->db_handler->get_conversation_context($session_id, $context_limit);
        
        // Check for special commands
        $special_response = $this->handle_special_commands($message);
        if ($special_response) {
            return $special_response;
        }
        
        // Generate AI response
        try {
            $ai_response = $this->ai_handler->generate_response($message, $context);
            
            // Post-process response
            $ai_response['message'] = $this->post_process_response($ai_response['message']);
            
            return $ai_response;
            
        } catch (Exception $e) {
            error_log('Kata Chatbot AI Error: ' . $e->getMessage());
            
            // Return fallback response
            return array(
                'message' => $this->get_fallback_response($message),
                'model' => 'fallback',
                'response_time' => 0,
                'success' => false,
                'error' => $e->getMessage(),
                'metadata' => array('fallback' => true)
            );
        }
    }
    
    /**
     * Handle special commands
     */
    private function handle_special_commands($message) {
        $message_lower = mb_strtolower(trim($message), 'UTF-8');
        
        // Help command
        if (in_array($message_lower, array('help', 'giúp đỡ', 'trợ giúp', '/help'))) {
            return array(
                'message' => $this->get_help_message(),
                'model' => 'command',
                'response_time' => 0,
                'success' => true,
                'metadata' => array('command' => 'help')
            );
        }
        
        // Contact command
        if (in_array($message_lower, array('liên hệ', 'contact', '/contact'))) {
            return array(
                'message' => $this->get_contact_message(),
                'model' => 'command',
                'response_time' => 0,
                'success' => true,
                'metadata' => array('command' => 'contact')
            );
        }
        
        // Services command
        if (preg_match('/dịch vụ|service|what.*do/i', $message)) {
            return array(
                'message' => $this->get_services_message(),
                'model' => 'command',
                'response_time' => 0,
                'success' => true,
                'metadata' => array('command' => 'services')
            );
        }
        
        // Reset conversation
        if (in_array($message_lower, array('reset', 'restart', 'bắt đầu lại'))) {
            return array(
                'message' => __('Cuộc hội thoại đã được khởi động lại. Tôi có thể giúp gì cho bạn? 😊', 'kata-chatbot'),
                'model' => 'command',
                'response_time' => 0,
                'success' => true,
                'metadata' => array('command' => 'reset')
            );
        }
        
        return null;
    }
    
    /**
     * Get help message
     */
    private function get_help_message() {
        return __('🤖 **Hướng dẫn sử dụng Chatbot**

Tôi có thể giúp bạn:
• Tư vấn các dịch vụ của Timona
• Giải đáp thắc mắc về công ty
• Cung cấp thông tin liên hệ
• Hỗ trợ báo giá dự án

**Các lệnh hữu ích:**
• "dịch vụ" - Xem danh sách dịch vụ
• "liên hệ" - Thông tin liên hệ
• "reset" - Bắt đầu lại cuộc hội thoại

Hãy đặt câu hỏi bất kỳ, tôi sẽ cố gắng hỗ trợ bạn tốt nhất! 😊', 'kata-chatbot');
    }
    
    /**
     * Get contact message
     */
    private function get_contact_message() {
        return __('📞 **Thông tin liên hệ Timona**

**Hotline:** 0123-456-789 (24/7)
**Email:** info@timona.vn
**Website:** https://timona.vn

**Văn phòng:** 
📍 Địa chỉ: Hà Nội, Việt Nam

**Giờ làm việc:**
🕒 Thứ 2 - Thứ 6: 8:00 - 18:00
🕒 Thứ 7: 8:00 - 12:00

**Mạng xã hội:**
🔗 Facebook: facebook.com/timona
🔗 LinkedIn: linkedin.com/company/timona

Bạn muốn tôi kết nối với team tư vấn để được hỗ trợ trực tiếp không? 😊', 'kata-chatbot');
    }
    
    /**
     * Get services message
     */
    private function get_services_message() {
        return __('🚀 **Dịch vụ chính của Timona**

**1. Thiết kế & Phát triển Website**
• Website doanh nghiệp chuyên nghiệp
• E-commerce / Cửa hàng online
• Landing page tối ưu conversion

**2. SEO & Digital Marketing**
• SEO tăng thứ hạng Google
• Google Ads & Facebook Ads
• Content Marketing

**3. Phát triển ứng dụng Mobile**
• App iOS / Android native
• React Native / Flutter
• App thương mại điện tử

**4. Chuyển đổi số doanh nghiệp**
• Tư vấn công nghệ
• Hệ thống quản lý (CRM, ERP)
• Tích hợp API & Automation

**5. Bảo mật & Hỗ trợ kỹ thuật**
• Bảo mật website
• Backup & Recovery
• Hỗ trợ 24/7

Bạn quan tâm dịch vụ nào? Tôi có thể tư vấn chi tiết hơn! 💼', 'kata-chatbot');
    }
    
    /**
     * Post-process AI response
     */
    private function post_process_response($response) {
        // Add emoji if not present
        if (!preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]/u', $response)) {
            if (rand(1, 3) === 1) {
                $emojis = array('😊', '👍', '💼', '🚀', '💡');
                $response .= ' ' . $emojis[array_rand($emojis)];
            }
        }
        
        // Add call-to-action if appropriate
        $cta_triggers = array('giá', 'báo giá', 'chi phí', 'cost', 'price');
        foreach ($cta_triggers as $trigger) {
            if (stripos($response, $trigger) !== false) {
                $response .= "\n\n" . __('Bạn có muốn để lại thông tin để nhận báo giá chi tiết không?', 'kata-chatbot');
                break;
            }
        }
        
        return $response;
    }
    
    /**
     * Get fallback response
     */
    private function get_fallback_response($message) {
        $fallback_responses = array(
            __('Xin lỗi, tôi đang gặp một chút vấn đề kỹ thuật. Bạn có thể thử lại sau ít phút không? 😅', 'kata-chatbot'),
            __('Hệ thống đang bận, nhưng bạn có thể liên hệ trực tiếp qua hotline 0123-456-789 để được hỗ trợ ngay! 📞', 'kata-chatbot'),
            __('Tôi không thể xử lý câu hỏi này lúc này. Vui lòng email cho chúng tôi tại info@timona.vn nhé! 📧', 'kata-chatbot'),
        );
        
        return $fallback_responses[array_rand($fallback_responses)];
    }
    
    /**
     * Sanitize user message
     */
    private function sanitize_message($message) {
        // Remove excessive whitespace
        $message = preg_replace('/\s+/', ' ', trim($message));
        
        // Remove potentially harmful content
        $message = wp_strip_all_tags($message);
        
        // Escape special characters but preserve Vietnamese
        $message = sanitize_textarea_field($message);
        
        return $message;
    }
    
    /**
     * Generate session ID
     */
    private function generate_session_id() {
        $prefix = 'chat_';
        $user_id = is_user_logged_in() ? get_current_user_id() : 'guest';
        $timestamp = time();
        $random = wp_generate_password(8, false);
        
        return $prefix . $user_id . '_' . $timestamp . '_' . $random;
    }
    
    /**
     * Update conversation activity
     */
    private function update_conversation_activity($conversation_id) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        $wpdb->update(
            $conversations_table,
            array('last_activity' => current_time('mysql')),
            array('id' => $conversation_id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Get conversation summary
     */
    public function get_conversation_summary($session_id) {
        $conversation = $this->db_handler->get_or_create_conversation($session_id);
        $messages = $this->db_handler->get_conversation_history($session_id, 50);
        
        return array(
            'conversation' => $conversation,
            'messages' => $messages,
            'total_messages' => count($messages),
            'duration' => $this->calculate_conversation_duration($conversation),
            'last_activity' => $conversation->last_activity
        );
    }
    
    /**
     * Calculate conversation duration
     */
    private function calculate_conversation_duration($conversation) {
        $start = strtotime($conversation->started_at);
        $end = strtotime($conversation->last_activity);
        
        return $end - $start;
    }
    
    /**
     * End conversation
     */
    public function end_conversation($session_id) {
        $this->db_handler->close_conversation($session_id);
        
        return array(
            'success' => true,
            'message' => __('Cảm ơn bạn đã sử dụng dịch vụ của Timona! Chúc bạn một ngày tốt lành! 👋', 'kata-chatbot')
        );
    }
    
    /**
     * Get suggested responses
     */
    public function get_suggested_responses($message) {
        $suggestions = array();
        
        // Based on message content, suggest relevant responses
        if (stripos($message, 'giá') !== false || stripos($message, 'chi phí') !== false) {
            $suggestions[] = __('Tôi muốn nhận báo giá chi tiết', 'kata-chatbot');
            $suggestions[] = __('Dự án của tôi có ngân sách khoảng bao nhiêu?', 'kata-chatbot');
        }
        
        if (stripos($message, 'website') !== false) {
            $suggestions[] = __('Thời gian làm website là bao lâu?', 'kata-chatbot');
            $suggestions[] = __('Tôi cần thiết kế website bán hàng', 'kata-chatbot');
        }
        
        if (stripos($message, 'seo') !== false) {
            $suggestions[] = __('SEO có hiệu quả sau bao lâu?', 'kata-chatbot');
            $suggestions[] = __('Dịch vụ SEO có gì khác biệt?', 'kata-chatbot');
        }
        
        // Default suggestions
        if (empty($suggestions)) {
            $suggestions = array(
                __('Tôi muốn tìm hiểu thêm về dịch vụ', 'kata-chatbot'),
                __('Làm sao để liên hệ tư vấn?', 'kata-chatbot'),
                __('Timona có văn phòng ở đâu?', 'kata-chatbot')
            );
        }
        
        return array_slice($suggestions, 0, 3);
    }
    
    /**
     * Check if conversation needs human handover
     */
    public function needs_human_handover($session_id) {
        $context = $this->db_handler->get_conversation_context($session_id, 10);
        
        // Check for handover triggers
        $handover_keywords = array(
            'nhân viên', 'tư vấn viên', 'gặp người', 'chuyên gia', 'human', 'staff',
            'không hiểu', 'không rõ', 'phức tạp', 'khó', 'confused'
        );
        
        $recent_messages = array_slice($context, -3); // Last 3 messages
        
        foreach ($recent_messages as $message) {
            if ($message->sender_type === 'user') {
                foreach ($handover_keywords as $keyword) {
                    if (stripos($message->message_text, $keyword) !== false) {
                        return true;
                    }
                }
            }
        }
        
        // Check for repeated similar questions
        if (count($context) >= 5) {
            $user_messages = array_filter($context, function($msg) {
                return $msg->sender_type === 'user';
            });
            
            if (count($user_messages) >= 3) {
                // Simple similarity check (more advanced NLP could be added)
                $last_messages = array_slice($user_messages, -3);
                $similarities = 0;
                
                for ($i = 0; $i < count($last_messages) - 1; $i++) {
                    $similarity = similar_text(
                        $last_messages[$i]->message_text,
                        $last_messages[$i + 1]->message_text,
                        $percent
                    );
                    
                    if ($percent > 70) {
                        $similarities++;
                    }
                }
                
                if ($similarities >= 2) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
