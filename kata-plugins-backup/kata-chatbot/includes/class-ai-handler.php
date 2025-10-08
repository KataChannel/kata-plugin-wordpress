<?php
/**
 * AI Handler Class
 * 
 * Handles integration with Google AI Studio (Gemini)
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataChatbot_AI_Handler {
    
    /**
     * API endpoint for Google AI Studio
     */
    private $api_endpoint = 'https://generativelanguage.googleapis.com/v1beta/models/';
    
    /**
     * API key
     */
    private $api_key;
    
    /**
     * AI model
     */
    private $model;
    
    /**
     * Request timeout
     */
    private $timeout;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_key = get_option('kata_chatbot_ai_api_key', '');
        $this->model = get_option('kata_chatbot_ai_model', 'gemini-pro');
        $this->timeout = get_option('kata_chatbot_response_timeout', 30);
    }
    
    /**
     * Generate AI response
     */
    public function generate_response($message, $context = array()) {
        if (empty($this->api_key)) {
            throw new Exception(__('AI API key not configured', 'kata-chatbot'));
        }
        
        $start_time = microtime(true);
        
        try {
            // Prepare the prompt with context
            $prompt = $this->prepare_prompt($message, $context);
            
            // Make API request
            $response = $this->make_api_request($prompt);
            
            // Calculate response time
            $response_time = round((microtime(true) - $start_time) * 1000, 2);
            
            // Parse and validate response
            $ai_response = $this->parse_response($response);
            
            return array(
                'message' => $ai_response,
                'model' => $this->model,
                'response_time' => $response_time,
                'success' => true,
                'metadata' => array(
                    'prompt_length' => strlen($prompt),
                    'response_length' => strlen($ai_response),
                    'context_used' => !empty($context)
                )
            );
            
        } catch (Exception $e) {
            error_log('Kata Chatbot AI Error: ' . $e->getMessage());
            
            return array(
                'message' => $this->get_fallback_response($message),
                'model' => 'fallback',
                'response_time' => round((microtime(true) - $start_time) * 1000, 2),
                'success' => false,
                'error' => $e->getMessage(),
                'metadata' => array(
                    'fallback_used' => true
                )
            );
        }
    }
    
    /**
     * Prepare prompt with context and system instructions
     */
    private function prepare_prompt($message, $context = array()) {
        $system_prompt = $this->get_system_prompt();
        
        // Build conversation context
        $conversation_history = '';
        if (!empty($context)) {
            $conversation_history = "\n\nLịch sử cuộc hội thoại gần đây:\n";
            foreach ($context as $msg) {
                $role = $msg['sender_type'] === 'user' ? 'Khách hàng' : 'AI';
                $conversation_history .= "$role: " . $msg['message_text'] . "\n";
            }
        }
        
        // Combine system prompt, context, and current message
        $full_prompt = $system_prompt . $conversation_history . "\n\nKhách hàng: " . $message . "\nAI:";
        
        return $full_prompt;
    }
    
    /**
     * Get system prompt for the AI
     */
    private function get_system_prompt() {
        $company_info = $this->get_company_info();
        
        return "Bạn là trợ lý AI thông minh của Timona, một công ty công nghệ chuyên cung cấp các dịch vụ số.

THÔNG TIN CÔNG TY:
{$company_info}

VAI TRÒ CỦA BẠN:
- Tư vấn dịch vụ một cách chuyên nghiệp và thân thiện
- Trả lời câu hỏi về công ty, dịch vụ, giá cả
- Hỗ trợ khách hàng tìm hiểu và lựa chọn dịch vụ phù hợp
- Thu thập thông tin liên hệ nếu khách hàng quan tâm

QUY TẮC TRẢI LỜI:
1. Luôn lịch sự, thân thiện và chuyên nghiệp
2. Trả lời bằng tiếng Việt, ngắn gọn nhưng đầy đủ thông tin
3. Nếu không biết thông tin, hãy thành thật nói và đề xuất liên hệ trực tiếp
4. Đưa ra gợi ý dịch vụ phù hợp với nhu cầu khách hàng
5. Khuyến khích khách hàng để lại thông tin liên hệ để được tư vấn chi tiết

PHONG CÁCH GIAO TIẾP:
- Sử dụng emoji phù hợp để tạo không khí thân thiện 😊
- Gọi khách hàng bằng \"anh/chị\" hoặc \"bạn\"
- Tránh sử dụng thuật ngữ kỹ thuật phức tạp
- Luôn kết thúc bằng câu hỏi để duy trì cuộc hội thoại";
    }
    
    /**
     * Get company information
     */
    private function get_company_info() {
        return "
TIMONA - CÔNG TY CÔNG NGHỆ HÀNG ĐẦU

Dịch vụ chính:
• Thiết kế & Phát triển Website chuyên nghiệp
• SEO & Digital Marketing tăng doanh số
• Phát triển ứng dụng Mobile (iOS, Android)
• Tư vấn chuyển đổi số cho doanh nghiệp
• Phân tích dữ liệu & Business Intelligence
• Bảo mật thông tin & Hỗ trợ kỹ thuật 24/7

Ưu điểm nổi bật:
✓ Đội ngũ chuyên gia 5+ năm kinh nghiệm
✓ Giải pháp tùy chỉnh theo từng doanh nghiệp
✓ Bảo hành & Hỗ trợ dài hạn
✓ Giá cả cạnh tranh, thanh toán linh hoạt
✓ Cam kết tiến độ và chất lượng

Liên hệ:
📞 Hotline: 0123-456-789
📧 Email: info@timona.vn
🌐 Website: https://timona.vn
📍 Địa chỉ: Hà Nội, Việt Nam";
    }
    
    /**
     * Make API request to Google AI Studio
     */
    private function make_api_request($prompt) {
        $endpoint = $this->api_endpoint . $this->model . ':generateContent?key=' . $this->api_key;
        
        $body = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
                'stopSequences' => array()
            ),
            'safetySettings' => array(
                array(
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ),
                array(
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                )
            )
        );
        
        $args = array(
            'method' => 'POST',
            'timeout' => $this->timeout,
            'headers' => array(
                'Content-Type' => 'application/json',
                'User-Agent' => 'KataChatbot/1.0.0 WordPress/' . get_bloginfo('version')
            ),
            'body' => wp_json_encode($body)
        );
        
        $response = wp_remote_request($endpoint, $args);
        
        if (is_wp_error($response)) {
            throw new Exception('API Request Failed: ' . $response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            $error_body = wp_remote_retrieve_body($response);
            $error_data = json_decode($error_body, true);
            $error_message = $error_data['error']['message'] ?? 'Unknown API error';
            throw new Exception("API Error ($status_code): $error_message");
        }
        
        return wp_remote_retrieve_body($response);
    }
    
    /**
     * Parse AI response
     */
    private function parse_response($response_body) {
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from AI API');
        }
        
        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            throw new Exception('Unexpected response format from AI API');
        }
        
        $ai_message = $data['candidates'][0]['content']['parts'][0]['text'];
        
        // Clean up the response
        $ai_message = trim($ai_message);
        $ai_message = $this->clean_response($ai_message);
        
        if (empty($ai_message)) {
            throw new Exception('Empty response from AI API');
        }
        
        return $ai_message;
    }
    
    /**
     * Clean up AI response
     */
    private function clean_response($message) {
        // Remove any unwanted prefixes
        $message = preg_replace('/^(AI:|Bot:|Assistant:)\s*/i', '', $message);
        
        // Limit message length
        $max_length = get_option('kata_chatbot_max_message_length', 1000);
        if (strlen($message) > $max_length) {
            $message = substr($message, 0, $max_length - 3) . '...';
        }
        
        // Sanitize HTML but allow basic formatting
        $allowed_tags = '<br><b><strong><i><em><u><a>';
        $message = strip_tags($message, $allowed_tags);
        
        return $message;
    }
    
    /**
     * Get fallback response when AI fails
     */
    private function get_fallback_response($user_message) {
        // Try to match with knowledge base first
        $knowledge_response = $this->search_knowledge_base($user_message);
        if ($knowledge_response) {
            return $knowledge_response;
        }
        
        // Default fallback responses
        $fallback_responses = array(
            __('Xin lỗi, tôi không hiểu câu hỏi của bạn. Bạn có thể diễn đạt lại được không? 😊', 'kata-chatbot'),
            __('Tôi cần thêm thông tin để có thể hỗ trợ bạn tốt hơn. Bạn có thể nói rõ hơn về vấn đề cần tư vấn không?', 'kata-chatbot'),
            __('Để được hỗ trợ tốt nhất, bạn có thể liên hệ trực tiếp với chúng tôi qua hotline: 0123-456-789 hoặc email: info@timona.vn', 'kata-chatbot'),
        );
        
        return $fallback_responses[array_rand($fallback_responses)];
    }
    
    /**
     * Search knowledge base for relevant answers
     */
    private function search_knowledge_base($message) {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        // Simple keyword matching
        $keywords = $this->extract_keywords($message);
        if (empty($keywords)) {
            return null;
        }
        
        $keyword_conditions = array();
        foreach ($keywords as $keyword) {
            $keyword_conditions[] = $wpdb->prepare(
                "(question LIKE %s OR answer LIKE %s OR keywords LIKE %s)",
                '%' . $keyword . '%',
                '%' . $keyword . '%',
                '%' . $keyword . '%'
            );
        }
        
        $where_clause = implode(' OR ', $keyword_conditions);
        
        $query = "SELECT * FROM $knowledge_table 
                  WHERE status = 'active' 
                  AND ($where_clause) 
                  ORDER BY priority DESC 
                  LIMIT 1";
        
        $result = $wpdb->get_row($query);
        
        if ($result) {
            return $result->answer . "\n\n" . __('Bạn còn câu hỏi nào khác không? 😊', 'kata-chatbot');
        }
        
        return null;
    }
    
    /**
     * Extract keywords from message
     */
    private function extract_keywords($message) {
        // Remove common Vietnamese stop words
        $stop_words = array(
            'và', 'của', 'cho', 'với', 'từ', 'trong', 'trên', 'dưới', 'này', 'đó', 'những', 'các', 'một', 'có', 'là', 'được', 'sẽ', 'đã', 'bạn', 'tôi', 'chúng', 'họ'
        );
        
        // Convert to lowercase and split into words
        $words = preg_split('/\s+/', mb_strtolower($message, 'UTF-8'));
        
        // Filter out stop words and short words
        $keywords = array_filter($words, function($word) use ($stop_words) {
            return strlen($word) > 2 && !in_array($word, $stop_words);
        });
        
        return array_unique($keywords);
    }
    
    /**
     * Test API connection
     */
    public function test_connection() {
        try {
            $response = $this->generate_response('Hello, this is a test message.');
            return array(
                'success' => true,
                'message' => __('Kết nối AI thành công!', 'kata-chatbot'),
                'response_time' => $response['response_time']
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
    
    /**
     * Get available models
     */
    public function get_available_models() {
        return array(
            'gemini-pro' => array(
                'name' => 'Gemini Pro',
                'description' => __('Mô hình AI tiên tiến cho hội thoại tự nhiên', 'kata-chatbot')
            ),
            'gemini-pro-vision' => array(
                'name' => 'Gemini Pro Vision',
                'description' => __('Mô hình AI có thể xử lý cả text và hình ảnh', 'kata-chatbot')
            )
        );
    }
    
    /**
     * Validate API key format
     */
    public function validate_api_key($api_key) {
        // Basic validation for Google AI Studio API key format
        if (empty($api_key)) {
            return array(
                'valid' => false,
                'message' => __('API key không được để trống', 'kata-chatbot')
            );
        }
        
        if (strlen($api_key) < 20) {
            return array(
                'valid' => false,
                'message' => __('API key không đúng định dạng', 'kata-chatbot')
            );
        }
        
        return array(
            'valid' => true,
            'message' => __('API key hợp lệ', 'kata-chatbot')
        );
    }
}
