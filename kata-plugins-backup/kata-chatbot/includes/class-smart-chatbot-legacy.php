<?php
/**
 * KATA Smart Chatbot Class (Legacy from SEO Manager)
 * 
 * AI-powered chatbot with content detection and contextual messaging
 * Automatically engages users based on content they're reading
 * 
 * Migrated from KATA SEO Manager to KATA Chatbot Plugin
 * 
 * @package KataChatbot
 * @subpackage Smart_Chatbot
 * @since 1.1.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class KATA_Smart_Chatbot_Legacy {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Table name for chat logs
     */
    private $table_chatlogs;
    
    /**
     * Table name for leads
     */
    private $table_leads;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        global $wpdb;
        $this->table_chatlogs = $wpdb->prefix . 'kata_chatbot_logs';
        $this->table_leads = $wpdb->prefix . 'kata_chatbot_leads';
        
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('wp_footer', array($this, 'render_chatbot_ui'));
        
        // AJAX endpoints
        add_action('wp_ajax_kata_chatbot_send_message', array($this, 'handle_send_message'));
        add_action('wp_ajax_nopriv_kata_chatbot_send_message', array($this, 'handle_send_message'));
        add_action('wp_ajax_kata_chatbot_save_lead', array($this, 'handle_save_lead'));
        add_action('wp_ajax_nopriv_kata_chatbot_save_lead', array($this, 'handle_save_lead'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Chat logs table
        $sql_logs = "CREATE TABLE IF NOT EXISTS {$this->table_chatlogs} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            message_type varchar(20) NOT NULL,
            message text NOT NULL,
            context_data longtext,
            page_url varchar(500),
            user_agent varchar(500),
            ip_address varchar(50),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Leads table
        $sql_leads = "CREATE TABLE IF NOT EXISTS {$this->table_leads} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(100) NOT NULL,
            name varchar(200),
            email varchar(200),
            phone varchar(50),
            message text,
            interest_type varchar(100),
            course_interest varchar(200),
            status varchar(50) DEFAULT 'new',
            source_page varchar(500),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY email (email),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        dbDelta($sql_logs);
        dbDelta($sql_leads);
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (!$this->is_chatbot_enabled()) {
            return;
        }
        
        // CSS
        wp_enqueue_style(
            'kata-smart-chatbot',
            KATA_CHATBOT_PLUGIN_URL . 'assets/css/smart-chatbot.css',
            array(),
            KATA_CHATBOT_VERSION
        );
        
        // JavaScript
        wp_enqueue_script(
            'kata-smart-chatbot',
            KATA_CHATBOT_PLUGIN_URL . 'assets/js/smart-chatbot.js',
            array('jquery'),
            KATA_CHATBOT_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('kata-smart-chatbot', 'kataChatbot', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_chatbot_nonce'),
            'settings' => $this->get_frontend_settings(),
            'contentContext' => $this->get_content_context(),
            'messages' => $this->get_contextual_messages()
        ));
    }
    
    /**
     * Get frontend settings
     */
    private function get_frontend_settings() {
        return array(
            'enabled' => get_option('kata_chatbot_enabled', true),
            'autoOpen' => get_option('kata_chatbot_auto_open', false),
            'openDelay' => get_option('kata_chatbot_open_delay', 5),
            'scrollTrigger' => get_option('kata_chatbot_scroll_trigger', 50),
            'exitIntent' => get_option('kata_chatbot_exit_intent', true),
            'position' => get_option('kata_chatbot_position', 'bottom-right'),
            'primaryColor' => get_option('kata_chatbot_primary_color', '#042277'),
            'secondaryColor' => get_option('kata_chatbot_secondary_color', '#040B1E'),
            'welcomeMessage' => get_option('kata_chatbot_welcome_message', 'Xin chào! Tôi có thể giúp gì cho bạn?'),
            'botName' => get_option('kata_chatbot_bot_name', 'KATA Assistant'),
            'avatar' => get_option('kata_chatbot_avatar', KATA_CHATBOT_PLUGIN_URL . 'assets/images/chatbot-avatar.png')
        );
    }
    
    /**
     * Get content context from current page
     */
    private function get_content_context() {
        global $post;
        
        $context = array(
            'type' => 'general',
            'title' => '',
            'keywords' => array(),
            'schemas' => array(),
            'categories' => array()
        );
        
        if (is_singular()) {
            $context['type'] = get_post_type();
            $context['title'] = get_the_title();
            $context['keywords'] = $this->extract_keywords($post->post_content);
            $context['schemas'] = $this->detect_schemas();
            
            if (is_singular('post')) {
                $categories = get_the_category();
                $context['categories'] = wp_list_pluck($categories, 'name');
            }
        }
        
        return $context;
    }
    
    /**
     * Extract keywords from content
     */
    private function extract_keywords($content) {
        // Course-related keywords
        $course_keywords = array('khóa học', 'đào tạo', 'học viện', 'chứng chỉ', 'giảng viên', 'học phí');
        
        // Product-related keywords
        $product_keywords = array('sản phẩm', 'mua', 'giá', 'khuyến mãi', 'ưu đãi');
        
        // Service keywords
        $service_keywords = array('dịch vụ', 'tư vấn', 'hỗ trợ', 'liên hệ');
        
        $all_keywords = array_merge($course_keywords, $product_keywords, $service_keywords);
        $found_keywords = array();
        
        // Use mb_strtolower if available, otherwise fallback to strtolower
        $content_lower = function_exists('mb_strtolower') 
            ? mb_strtolower(strip_tags($content), 'UTF-8')
            : strtolower(strip_tags($content));
        
        foreach ($all_keywords as $keyword) {
            $keyword_lower = function_exists('mb_strtolower')
                ? mb_strtolower($keyword, 'UTF-8')
                : strtolower($keyword);
            
            $search_func = function_exists('mb_strpos') ? 'mb_strpos' : 'strpos';
            if ($search_func($content_lower, $keyword_lower) !== false) {
                $found_keywords[] = $keyword;
            }
        }
        
        return array_unique($found_keywords);
    }
    
    /**
     * Detect schema types on current page
     */
    private function detect_schemas() {
        $schemas = array();
        
        // Check if page has schemas in meta
        if (is_singular()) {
            global $wpdb;
            $post_id = get_the_ID();
            
            // Query kata_schemas table
            $table_name = $wpdb->prefix . 'kata_schemas';
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT schema_type FROM $table_name WHERE post_id = %d AND status = 'published'",
                    $post_id
                ));
                
                foreach ($results as $row) {
                    $schemas[] = $row->schema_type;
                }
            }
        }
        
        return $schemas;
    }
    
    /**
     * Get contextual messages based on content
     */
    private function get_contextual_messages() {
        $context = $this->get_content_context();
        $messages = array();
        
        // Course-related messages
        if (in_array('Course', $context['schemas']) || 
            array_intersect(['khóa học', 'đào tạo', 'học viện'], $context['keywords'])) {
            $messages[] = array(
                'type' => 'course',
                'message' => '👋 Bạn đang quan tâm đến khóa học này? Tôi có thể tư vấn chi tiết cho bạn!',
                'delay' => 3000,
                'buttons' => array(
                    array('text' => 'Xem chi tiết khóa học', 'action' => 'show_course_info'),
                    array('text' => 'Đăng ký ngay', 'action' => 'show_registration_form'),
                    array('text' => 'Tư vấn học phí', 'action' => 'show_price_consultation')
                )
            );
        }
        
        // FAQ-related messages
        if (in_array('FAQPage', $context['schemas'])) {
            $messages[] = array(
                'type' => 'faq',
                'message' => '💡 Bạn có câu hỏi nào khác? Tôi sẵn sàng hỗ trợ!',
                'delay' => 5000,
                'buttons' => array(
                    array('text' => 'Hỏi thêm câu khác', 'action' => 'open_chat'),
                    array('text' => 'Liên hệ tư vấn', 'action' => 'show_contact_form')
                )
            );
        }
        
        // Article-related messages
        if (in_array('Article', $context['schemas']) || $context['type'] === 'post') {
            $messages[] = array(
                'type' => 'article',
                'message' => '📚 Bạn muốn tìm hiểu thêm về chủ đề này? Chúng tôi có khóa học chuyên sâu!',
                'delay' => 10000,
                'buttons' => array(
                    array('text' => 'Xem khóa học liên quan', 'action' => 'show_related_courses'),
                    array('text' => 'Nhận tư vấn', 'action' => 'show_consultation_form')
                )
            );
        }
        
        // Exit intent message
        $messages[] = array(
            'type' => 'exit_intent',
            'message' => '⏰ Đừng bỏ lỡ! Để lại thông tin để nhận tư vấn miễn phí và ưu đãi đặc biệt!',
            'trigger' => 'exit',
            'buttons' => array(
                array('text' => 'Nhận tư vấn ngay', 'action' => 'show_lead_form'),
                array('text' => 'Xem ưu đãi', 'action' => 'show_promotions')
            )
        );
        
        return $messages;
    }
    
    /**
     * Check if chatbot is enabled
     */
    private function is_chatbot_enabled() {
        return get_option('kata_chatbot_enabled', true);
    }
    
    /**
     * Render chatbot UI
     */
    public function render_chatbot_ui() {
        if (!$this->is_chatbot_enabled()) {
            return;
        }
        
        $template_file = KATA_CHATBOT_PLUGIN_PATH . 'templates/chatbot-ui-legacy.php';
        if (file_exists($template_file)) {
            include $template_file;
        }
    }
    
    /**
     * Handle send message AJAX
     */
    public function handle_send_message() {
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $message = sanitize_text_field($_POST['message'] ?? '');
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $context = json_decode(stripslashes($_POST['context'] ?? '{}'), true);
        
        if (empty($message)) {
            wp_send_json_error('Message is required');
        }
        
        // Save user message
        $this->save_chat_log($session_id, 'user', $message, $context);
        
        // Generate bot response
        $response = $this->generate_bot_response($message, $context);
        
        // Save bot response
        $this->save_chat_log($session_id, 'bot', $response['message'], $context);
        
        wp_send_json_success($response);
    }
    
    /**
     * Generate bot response
     */
    private function generate_bot_response($message, $context) {
        $message_lower = mb_strtolower($message, 'UTF-8');
        
        // Course inquiry
        if (preg_match('/(khóa học|đào tạo|học phí|chi phí|giá|thời gian)/u', $message_lower)) {
            return array(
                'message' => '📚 Chúng tôi có nhiều khóa học chất lượng cao! Bạn quan tâm đến lĩnh vực nào? (Thẩm mỹ, Spa, Nail, Make-up...)',
                'type' => 'course_inquiry',
                'suggestions' => array('Khóa học Thẩm mỹ', 'Khóa học Spa', 'Khóa học Nail Art', 'Xem tất cả khóa học')
            );
        }
        
        // Registration inquiry
        if (preg_match('/(đăng ký|tham gia|ghi danh|sign up)/u', $message_lower)) {
            return array(
                'message' => '✨ Tuyệt vời! Để đăng ký khóa học, bạn vui lòng cung cấp thông tin liên hệ. Tôi sẽ chuyển đến bộ phận tư vấn để hỗ trợ bạn tốt nhất!',
                'type' => 'registration',
                'action' => 'show_registration_form'
            );
        }
        
        // Price inquiry
        if (preg_match('/(giá|học phí|chi phí|cost|price)/u', $message_lower)) {
            return array(
                'message' => '💰 Học phí phụ thuộc vào khóa học bạn chọn. Để tư vấn chính xác, bạn có thể:
                
1️⃣ Xem bảng giá chi tiết các khóa học
2️⃣ Nhận tư vấn trực tiếp qua hotline: 1900 xxxx
3️⃣ Để lại thông tin để nhận báo giá và ưu đãi',
                'type' => 'price_inquiry',
                'buttons' => array(
                    array('text' => 'Xem bảng giá', 'action' => 'show_price_list'),
                    array('text' => 'Nhận tư vấn', 'action' => 'show_contact_form')
                )
            );
        }
        
        // Contact inquiry
        if (preg_match('/(liên hệ|gọi|số điện thoại|địa chỉ|contact)/u', $message_lower)) {
            $contact_info = $this->get_contact_info();
            return array(
                'message' => "📞 Thông tin liên hệ:\n\n" . $contact_info,
                'type' => 'contact_info'
            );
        }
        
        // Default response with AI-ready hook
        $ai_response = apply_filters('kata_chatbot_ai_response', null, $message, $context);
        
        if ($ai_response) {
            return $ai_response;
        }
        
        return array(
            'message' => 'Cảm ơn bạn đã quan tâm! Tôi có thể giúp bạn về:
            
📚 Thông tin khóa học
💰 Học phí và ưu đãi
📝 Đăng ký học
📞 Liên hệ tư vấn
            
Bạn muốn biết thêm về điều gì?',
            'type' => 'general',
            'suggestions' => array('Xem khóa học', 'Học phí', 'Đăng ký', 'Liên hệ')
        );
    }
    
    /**
     * Get contact info from settings
     */
    private function get_contact_info() {
        $info = '';
        
        $phone = get_option('kata_chatbot_contact_phone', '');
        $email = get_option('kata_chatbot_contact_email', '');
        $address = get_option('kata_chatbot_contact_address', '');
        
        if ($phone) $info .= "☎️ Hotline: $phone\n";
        if ($email) $info .= "📧 Email: $email\n";
        if ($address) $info .= "📍 Địa chỉ: $address\n";
        
        if (empty($info)) {
            $info = "Vui lòng liên hệ qua website hoặc fanpage của chúng tôi!";
        }
        
        return $info;
    }
    
    /**
     * Save chat log
     */
    private function save_chat_log($session_id, $type, $message, $context = array()) {
        global $wpdb;
        
        $wpdb->insert(
            $this->table_chatlogs,
            array(
                'session_id' => $session_id,
                'user_id' => get_current_user_id() ?: null,
                'message_type' => $type,
                'message' => $message,
                'context_data' => json_encode($context),
                'page_url' => $_SERVER['HTTP_REFERER'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
            ),
            array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Handle save lead AJAX
     */
    public function handle_save_lead() {
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');
        $interest_type = sanitize_text_field($_POST['interest_type'] ?? '');
        $course_interest = sanitize_text_field($_POST['course_interest'] ?? '');
        
        if (empty($name) || empty($email)) {
            wp_send_json_error('Name and email are required');
        }
        
        global $wpdb;
        
        $result = $wpdb->insert(
            $this->table_leads,
            array(
                'session_id' => $session_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'message' => $message,
                'interest_type' => $interest_type,
                'course_interest' => $course_interest,
                'source_page' => $_SERVER['HTTP_REFERER'] ?? '',
                'status' => 'new'
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result) {
            // Send notification email
            $this->send_lead_notification($name, $email, $phone, $message);
            
            wp_send_json_success(array(
                'message' => 'Cảm ơn bạn! Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất.'
            ));
        } else {
            wp_send_json_error('Failed to save lead');
        }
    }
    
    /**
     * Send lead notification email
     */
    private function send_lead_notification($name, $email, $phone, $message) {
        $admin_email = get_option('admin_email');
        $subject = '[KATA Chatbot] Khách hàng tiềm năng mới: ' . $name;
        
        $body = "Thông tin khách hàng từ KATA Smart Chatbot:\n\n";
        $body .= "Tên: $name\n";
        $body .= "Email: $email\n";
        $body .= "Số điện thoại: $phone\n";
        $body .= "Tin nhắn: $message\n\n";
        $body .= "Thời gian: " . current_time('mysql') . "\n";
        
        wp_mail($admin_email, $subject, $body);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'kata-chatbot',
            'Smart Chatbot (Legacy)',
            'Smart Chatbot',
            'manage_options',
            'kata-smart-chatbot',
            array($this, 'render_admin_page')
        );
        
        add_submenu_page(
            'kata-chatbot',
            'Chat Logs',
            'Chat Logs',
            'manage_options',
            'kata-chatbot-logs',
            array($this, 'render_logs_page')
        );
        
        add_submenu_page(
            'kata-chatbot',
            'Chatbot Leads',
            'Leads',
            'manage_options',
            'kata-chatbot-leads',
            array($this, 'render_leads_page')
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'kata-smart-chatbot') === false && 
            strpos($hook, 'kata-chatbot-logs') === false &&
            strpos($hook, 'kata-chatbot-leads') === false) {
            return;
        }
        
        // Use main admin CSS if chatbot-admin.css doesn't exist
        $admin_css = KATA_CHATBOT_PLUGIN_URL . 'assets/css/chatbot-admin.css';
        if (!file_exists(KATA_CHATBOT_PLUGIN_PATH . 'assets/css/chatbot-admin.css')) {
            $admin_css = KATA_CHATBOT_PLUGIN_URL . 'assets/css/admin.css';
        }
        
        wp_enqueue_style(
            'kata-chatbot-admin-legacy',
            $admin_css,
            array(),
            KATA_CHATBOT_VERSION
        );
        
        // Use main admin JS if chatbot-admin.js doesn't exist
        $admin_js = KATA_CHATBOT_PLUGIN_URL . 'assets/js/chatbot-admin.js';
        if (!file_exists(KATA_CHATBOT_PLUGIN_PATH . 'assets/js/chatbot-admin.js')) {
            $admin_js = KATA_CHATBOT_PLUGIN_URL . 'assets/js/admin.js';
        }
        
        wp_enqueue_script(
            'kata-chatbot-admin-legacy',
            $admin_js,
            array('jquery'),
            KATA_CHATBOT_VERSION,
            true
        );
        
        // Add color picker for settings page
        if (strpos($hook, 'kata-smart-chatbot') !== false) {
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
        }
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Chatbot settings
        register_setting('kata_chatbot_settings', 'kata_chatbot_enabled');
        register_setting('kata_chatbot_settings', 'kata_chatbot_auto_open');
        register_setting('kata_chatbot_settings', 'kata_chatbot_open_delay');
        register_setting('kata_chatbot_settings', 'kata_chatbot_scroll_trigger');
        register_setting('kata_chatbot_settings', 'kata_chatbot_exit_intent');
        register_setting('kata_chatbot_settings', 'kata_chatbot_position');
        register_setting('kata_chatbot_settings', 'kata_chatbot_primary_color');
        register_setting('kata_chatbot_settings', 'kata_chatbot_secondary_color');
        register_setting('kata_chatbot_settings', 'kata_chatbot_welcome_message');
        register_setting('kata_chatbot_settings', 'kata_chatbot_bot_name');
        register_setting('kata_chatbot_settings', 'kata_chatbot_avatar');
        register_setting('kata_chatbot_settings', 'kata_chatbot_contact_phone');
        register_setting('kata_chatbot_settings', 'kata_chatbot_contact_email');
        register_setting('kata_chatbot_settings', 'kata_chatbot_contact_address');
        
        // AI settings
        register_setting('kata_chatbot_settings', 'kata_chatbot_ai_enabled');
        register_setting('kata_chatbot_settings', 'kata_chatbot_ai_provider');
        register_setting('kata_chatbot_settings', 'kata_chatbot_ai_api_key');
        register_setting('kata_chatbot_settings', 'kata_chatbot_ai_model');
    }
    
    /**
     * Render admin settings page
     */
    public function render_admin_page() {
        $template_file = KATA_CHATBOT_PLUGIN_PATH . 'admin/chatbot-settings-legacy.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap">';
            echo '<h1>Smart Chatbot Settings</h1>';
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Error:</strong> Template file not found: ' . esc_html($template_file);
            echo '</p></div>';
            echo '</div>';
        }
    }
    
    /**
     * Render logs page
     */
    public function render_logs_page() {
        $template_file = KATA_CHATBOT_PLUGIN_PATH . 'admin/chatbot-logs-legacy.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap">';
            echo '<h1>Chat Logs</h1>';
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Error:</strong> Template file not found: ' . esc_html($template_file);
            echo '</p></div>';
            echo '</div>';
        }
    }
    
    /**
     * Render leads page
     */
    public function render_leads_page() {
        $template_file = KATA_CHATBOT_PLUGIN_PATH . 'admin/chatbot-leads-legacy.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            echo '<div class="wrap">';
            echo '<h1>Chatbot Leads</h1>';
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Error:</strong> Template file not found: ' . esc_html($template_file);
            echo '</p></div>';
            echo '</div>';
        }
    }
    
    /**
     * Get chat statistics
     */
    public function get_statistics($days = 30) {
        global $wpdb;
        
        $date_from = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        $stats = array();
        
        // Total chats
        $stats['total_chats'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM {$this->table_chatlogs} WHERE created_at >= %s",
            $date_from
        ));
        
        // Total messages
        $stats['total_messages'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_chatlogs} WHERE created_at >= %s",
            $date_from
        ));
        
        // Total leads
        $stats['total_leads'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_leads} WHERE created_at >= %s",
            $date_from
        ));
        
        // New leads (today)
        $stats['new_leads_today'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_leads} WHERE DATE(created_at) = CURDATE()"
        );
        
        // Conversion rate
        if ($stats['total_chats'] > 0) {
            $stats['conversion_rate'] = round(($stats['total_leads'] / $stats['total_chats']) * 100, 2);
        } else {
            $stats['conversion_rate'] = 0;
        }
        
        return $stats;
    }
}
