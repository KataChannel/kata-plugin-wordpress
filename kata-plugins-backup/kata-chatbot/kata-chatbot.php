<?php
/**
 * Plugin Name: Kata Chatbot
 * Plugin URI: https://timona.vn/plugins/kata-chatbot
 * Description: Plugin chatbot AI hỗ trợ tư vấn dịch vụ sử dụng Google AI Studio với lưu trữ database hoàn chỉnh.
 * Version: 1.0.0
 * Author: Timona Team
 * Author URI: https://timona.vn
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kata-chatbot
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 *
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KATA_CHATBOT_VERSION', '1.0.0');
define('KATA_CHATBOT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_CHATBOT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KATA_CHATBOT_PLUGIN_FILE', __FILE__);
define('KATA_CHATBOT_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class KataChatbot {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * AI handler instance
     */
    private $ai_handler;
    
    /**
     * Database handler instance
     */
    private $db_handler;
    
    /**
     * Chat handler instance
     */
    private $chat_handler;
    
    /**
     * Branch handler instance
     */
    private $branch_handler;
    
    /**
     * Admin handler instance
     */
    private $admin;
    
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
        $this->init();
    }
    
    /**
     * Initialize plugin
     */
    private function init() {
        // Load text domain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize components
        add_action('init', array($this, 'init_components'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // AJAX hooks
        add_action('wp_ajax_kata_chatbot_send_message', array($this, 'ajax_send_message'));
        add_action('wp_ajax_nopriv_kata_chatbot_send_message', array($this, 'ajax_send_message'));
        add_action('wp_ajax_kata_chatbot_rate_message', array($this, 'ajax_rate_message'));
        add_action('wp_ajax_nopriv_kata_chatbot_rate_message', array($this, 'ajax_rate_message'));
        add_action('wp_ajax_kata_chatbot_submit_feedback', array($this, 'ajax_submit_feedback'));
        add_action('wp_ajax_nopriv_kata_chatbot_submit_feedback', array($this, 'ajax_submit_feedback'));
        add_action('wp_ajax_kata_chatbot_get_conversations', array($this, 'ajax_get_conversations'));
        add_action('wp_ajax_kata_chatbot_delete_conversation', array($this, 'ajax_delete_conversation'));
        add_action('wp_ajax_kata_chatbot_get_history', array($this, 'ajax_get_history'));
        add_action('wp_ajax_nopriv_kata_chatbot_get_history', array($this, 'ajax_get_history'));
        
        // Add chatbot to frontend
        add_action('wp_footer', array($this, 'render_chatbot_widget'));
        
        // REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'kata-chatbot',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Load dependencies first
        $this->load_dependencies();
        
        // Create database tables
        $this->create_tables();
        
        // Set default options
        $this->set_default_options();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clear any caches
        wp_cache_flush();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear any cached data
        wp_cache_flush();
        
        // Clear chatbot cache
        delete_transient('kata_chatbot_cache');
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Conversations table
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$conversations_table'") != $conversations_table) {
            $sql = "CREATE TABLE $conversations_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                session_id varchar(255) NOT NULL,
                user_id int(11) DEFAULT NULL,
                user_ip varchar(45) NOT NULL,
                user_agent text,
                started_at datetime DEFAULT CURRENT_TIMESTAMP,
                last_activity datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                status varchar(20) DEFAULT 'active',
                total_messages int(11) DEFAULT 0,
                metadata longtext,
                PRIMARY KEY (id),
                KEY session_id (session_id),
                KEY user_id (user_id),
                KEY status (status)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        } else {
            // Add metadata column if it doesn't exist
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $conversations_table LIKE 'metadata'");
            if (empty($columns)) {
                $wpdb->query("ALTER TABLE $conversations_table ADD COLUMN metadata longtext");
            }
        }
        
        // Messages table
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$messages_table'") != $messages_table) {
            $sql = "CREATE TABLE $messages_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                conversation_id int(11) NOT NULL,
                sender_type enum('user','bot') NOT NULL,
                message_text longtext NOT NULL,
                ai_response longtext,
                ai_model varchar(100),
                response_time decimal(10,3),
                rating tinyint(1) DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                metadata json,
                PRIMARY KEY (id),
                KEY conversation_id (conversation_id),
                KEY sender_type (sender_type),
                KEY created_at (created_at),
                KEY rating (rating),
                FOREIGN KEY (conversation_id) REFERENCES $conversations_table(id) ON DELETE CASCADE
            ) $charset_collate;";
            
            dbDelta($sql);
        } else {
            // Add rating column if it doesn't exist
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $messages_table LIKE 'rating'");
            if (empty($columns)) {
                $wpdb->query("ALTER TABLE $messages_table ADD COLUMN rating tinyint(1) DEFAULT NULL AFTER response_time");
            }
        }
        
        // Analytics table
        $analytics_table = $wpdb->prefix . 'kata_chatbot_analytics';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$analytics_table'") != $analytics_table) {
            $sql = "CREATE TABLE $analytics_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                date date NOT NULL,
                total_conversations int(11) DEFAULT 0,
                total_messages int(11) DEFAULT 0,
                avg_response_time decimal(10,3) DEFAULT 0,
                user_satisfaction decimal(3,2) DEFAULT 0,
                popular_topics json,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY date (date)
            ) $charset_collate;";
            
            dbDelta($sql);
        }
        
        // Knowledge base table
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$knowledge_table'") != $knowledge_table) {
            $sql = "CREATE TABLE $knowledge_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                category varchar(100) NOT NULL,
                question text NOT NULL,
                answer longtext NOT NULL,
                keywords text,
                priority int(11) DEFAULT 1,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY category (category),
                KEY status (status),
                FULLTEXT KEY search_idx (question, answer, keywords)
            ) $charset_collate;";
            
            dbDelta($sql);
        }
        
        // Create branches table using branch handler
        // Load dependencies first to ensure classes are available
        $this->load_dependencies();
        
        if (class_exists('KataChatbot_Branch_Handler')) {
            $branch_handler_temp = new KataChatbot_Branch_Handler();
            $branch_handler_temp->create_branches_table();
        } else {
            // Fallback: create table directly if class not available
            $this->create_branches_table_fallback();
        }
        
        // Log table creation results for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $branches_table = $wpdb->prefix . 'kata_chatbot_branches';
            $tables_created = array(
                'conversations' => $wpdb->get_var("SHOW TABLES LIKE '$conversations_table'") == $conversations_table,
                'messages' => $wpdb->get_var("SHOW TABLES LIKE '$messages_table'") == $messages_table,
                'analytics' => $wpdb->get_var("SHOW TABLES LIKE '$analytics_table'") == $analytics_table,
                'knowledge' => $wpdb->get_var("SHOW TABLES LIKE '$knowledge_table'") == $knowledge_table,
                'branches' => $wpdb->get_var("SHOW TABLES LIKE '$branches_table'") == $branches_table
            );
            error_log('Kata Chatbot Tables: ' . wp_json_encode($tables_created));
        }
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            'enabled' => true,
            'ai_provider' => 'google_ai_studio',
            'ai_api_key' => '',
            'ai_model' => 'gemini-pro',
            'welcome_message' => __('Xin chào! Tôi là trợ lý AI của Timona. Tôi có thể giúp gì cho bạn?', 'kata-chatbot'),
            'offline_message' => __('Chatbot hiện đang offline. Vui lòng thử lại sau.', 'kata-chatbot'),
            'position' => 'bottom-right',
            'theme_color' => '#0073aa',
            'auto_open' => false,
            'show_on_pages' => array('all'),
            'max_message_length' => 1000,
            'rate_limit' => 10, // messages per minute
            'enable_analytics' => true,
            'enable_knowledge_base' => true,
            'response_timeout' => 30,
            'context_memory' => 5, // number of previous messages to remember
        );
        
        foreach ($default_options as $option_name => $option_value) {
            add_option('kata_chatbot_' . $option_name, $option_value);
        }
        
        // Add sample knowledge base entries
        $this->add_sample_knowledge();
    }
    
    /**
     * Add sample knowledge base entries
     */
    private function add_sample_knowledge() {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        $sample_data = array(
            array(
                'category' => 'services',
                'question' => 'Timona cung cấp những dịch vụ gì?',
                'answer' => 'Timona cung cấp các dịch vụ: Thiết kế website, SEO, Digital Marketing, Phát triển ứng dụng mobile, Tư vấn công nghệ.',
                'keywords' => 'dịch vụ, services, website, seo, marketing, mobile app'
            ),
            array(
                'category' => 'pricing',
                'question' => 'Giá cả dịch vụ như thế nào?',
                'answer' => 'Giá cả dịch vụ phụ thuộc vào yêu cầu cụ thể. Vui lòng liên hệ để được tư vấn báo giá chi tiết phù hợp với ngân sách.',
                'keywords' => 'giá, pricing, cost, báo giá, chi phí'
            ),
            array(
                'category' => 'contact',
                'question' => 'Làm sao để liên hệ với Timona?',
                'answer' => 'Bạn có thể liên hệ qua: Email: info@timona.vn, Hotline: 0123456789, hoặc chat trực tiếp tại đây.',
                'keywords' => 'liên hệ, contact, email, phone, hotline'
            ),
            array(
                'category' => 'support',
                'question' => 'Timona có hỗ trợ sau bán hàng không?',
                'answer' => 'Có, chúng tôi cung cấp hỗ trợ 24/7 sau bán hàng bao gồm bảo trì, cập nhật và tư vấn kỹ thuật.',
                'keywords' => 'hỗ trợ, support, bảo trì, maintenance, 24/7'
            )
        );
        
        foreach ($sample_data as $data) {
            $wpdb->insert(
                $knowledge_table,
                $data,
                array('%s', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Create branches table fallback (when class not available)
     */
    private function create_branches_table_fallback() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            address text,
            phone varchar(50),
            email varchar(100),
            facebook_url varchar(255),
            facebook_page_id varchar(100),
            zalo_url varchar(255),
            zalo_oa_id varchar(100),
            hotline varchar(50),
            working_hours text,
            description text,
            is_active tinyint(1) DEFAULT 1,
            display_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Create default branch if none exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count == 0) {
            $wpdb->insert(
                $table_name,
                array(
                    'name' => 'Chi nhánh chính',
                    'address' => 'Địa chỉ chi nhánh chính',
                    'phone' => '0123456789',
                    'email' => 'contact@example.com',
                    'facebook_url' => '',
                    'facebook_page_id' => '',
                    'zalo_url' => '',
                    'zalo_oa_id' => '',
                    'hotline' => '0987654321',
                    'working_hours' => 'Thứ 2 - Thứ 6: 8:00 - 17:30',
                    'description' => 'Chi nhánh chính của công ty',
                    'is_active' => 1,
                    'display_order' => 1
                ),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d')
            );
        }
    }
    
    /**
     * Initialize components
     */
    public function init_components() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize components
        $this->ai_handler = new KataChatbot_AI_Handler();
        $this->db_handler = new KataChatbot_DB_Handler();
        $this->chat_handler = new KataChatbot_Chat_Handler();
        $this->branch_handler = new KataChatbot_Branch_Handler();
        
        // Initialize admin if in admin area
        if (is_admin()) {
            $this->admin = new KataChatbot_Admin();
        }
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        $files = array(
            'includes/class-ai-handler.php',
            'includes/class-db-handler.php',
            'includes/class-chat-handler.php',
            'includes/class-branch-handler.php',
        );
        
        if (is_admin()) {
            $files[] = 'includes/class-admin.php';
        }
        
        foreach ($files as $file) {
            $file_path = KATA_CHATBOT_PLUGIN_PATH . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_scripts() {
        if (!get_option('kata_chatbot_enabled', true)) {
            return;
        }
        
        wp_enqueue_style(
            'kata-chatbot-frontend',
            KATA_CHATBOT_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            KATA_CHATBOT_VERSION
        );
        
        wp_enqueue_script(
            'kata-chatbot-frontend',
            KATA_CHATBOT_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            KATA_CHATBOT_VERSION,
            true
        );
        
        wp_localize_script('kata-chatbot-frontend', 'kataChatbot', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_chatbot_nonce'),
            'welcomeMessage' => get_option('kata_chatbot_welcome_message', ''),
            'offlineMessage' => get_option('kata_chatbot_offline_message', ''),
            'themeColor' => get_option('kata_chatbot_theme_color', '#0073aa'),
            'autoOpen' => get_option('kata_chatbot_auto_open', false),
            'maxLength' => get_option('kata_chatbot_max_message_length', 1000),
            'strings' => array(
                'typing' => __('Đang nhập...', 'kata-chatbot'),
                'send' => __('Gửi', 'kata-chatbot'),
                'placeholder' => __('Nhập tin nhắn...', 'kata-chatbot'),
                'error' => __('Có lỗi xảy ra. Vui lòng thử lại.', 'kata-chatbot'),
                'minimize' => __('Thu nhỏ', 'kata-chatbot'),
                'close' => __('Đóng', 'kata-chatbot'),
            )
        ));
    }
    
    /**
     * Enqueue admin scripts
     */
    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'kata-chatbot') === false) {
            return;
        }
        
        wp_enqueue_style(
            'kata-chatbot-admin',
            KATA_CHATBOT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            KATA_CHATBOT_VERSION
        );
        
        wp_enqueue_script(
            'kata-chatbot-admin',
            KATA_CHATBOT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            KATA_CHATBOT_VERSION,
            true
        );
        
        wp_enqueue_style('wp-color-picker');
        
        wp_localize_script('kata-chatbot-admin', 'kataChatbotAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_chatbot_admin_nonce'),
            'strings' => array(
                'saved' => __('Đã lưu thành công!', 'kata-chatbot'),
                'error' => __('Có lỗi xảy ra!', 'kata-chatbot'),
                'confirm' => __('Bạn có chắc chắn?', 'kata-chatbot'),
            )
        ));
    }
    
    /**
     * AJAX handler for sending messages
     */
    public function ajax_send_message() {
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $message = sanitize_textarea_field($_POST['message'] ?? '');
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        
        if (empty($message)) {
            wp_send_json_error(__('Tin nhắn không được để trống.', 'kata-chatbot'));
        }
        
        // Rate limiting
        if (!$this->check_rate_limit()) {
            wp_send_json_error(__('Bạn đang gửi tin nhắn quá nhanh. Vui lòng chờ một chút.', 'kata-chatbot'));
        }
        
        try {
            $response = $this->chat_handler->process_message($message, $session_id);
            wp_send_json_success($response);
        } catch (Exception $e) {
            error_log('Kata Chatbot Error: ' . $e->getMessage());
            wp_send_json_error(__('Có lỗi xảy ra. Vui lòng thử lại sau.', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX handler for getting chat history
     */
    public function ajax_get_history() {
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        
        if (empty($session_id)) {
            wp_send_json_error(__('Session ID không hợp lệ.', 'kata-chatbot'));
        }
        
        try {
            $history = $this->db_handler->get_conversation_history($session_id);
            wp_send_json_success($history);
        } catch (Exception $e) {
            error_log('Kata Chatbot History Error: ' . $e->getMessage());
            wp_send_json_error(__('Không thể tải lịch sử chat.', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX handler for rating messages
     */
    public function ajax_rate_message() {
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $message_id = intval($_POST['message_id'] ?? 0);
        $rating = sanitize_text_field($_POST['rating'] ?? '');
        
        if (empty($message_id) || !in_array($rating, ['positive', 'negative'])) {
            wp_send_json_error(__('Dữ liệu đánh giá không hợp lệ.', 'kata-chatbot'));
        }
        
        try {
            global $wpdb;
            $table = $wpdb->prefix . 'kata_chatbot_messages';
            
            $updated = $wpdb->update(
                $table,
                array('metadata' => json_encode(array('rating' => $rating, 'rated_at' => current_time('mysql')))),
                array('id' => $message_id),
                array('%s'),
                array('%d')
            );
            
            if ($updated !== false) {
                wp_send_json_success(__('Cảm ơn bạn đã đánh giá!', 'kata-chatbot'));
            } else {
                wp_send_json_error(__('Không thể lưu đánh giá.', 'kata-chatbot'));
            }
        } catch (Exception $e) {
            error_log('Kata Chatbot Rating Error: ' . $e->getMessage());
            wp_send_json_error(__('Lỗi khi lưu đánh giá.', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX handler for getting conversations (admin)
     */
    public function ajax_get_conversations() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Không có quyền truy cập.', 'kata-chatbot'));
        }
        
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $page = intval($_POST['page'] ?? 1);
        $per_page = intval($_POST['per_page'] ?? 20);
        $filters = $_POST['filters'] ?? array();
        
        try {
            $conversations = $this->db_handler->get_conversations_for_admin($page, $per_page, $filters);
            wp_send_json_success($conversations);
        } catch (Exception $e) {
            error_log('Kata Chatbot Get Conversations Error: ' . $e->getMessage());
            wp_send_json_error(__('Không thể tải danh sách cuộc hội thoại.', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX handler for deleting conversations (admin)
     */
    public function ajax_delete_conversation() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Không có quyền truy cập.', 'kata-chatbot'));
        }
        
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $conversation_id = intval($_POST['conversation_id'] ?? 0);
        
        if (empty($conversation_id)) {
            wp_send_json_error(__('ID cuộc hội thoại không hợp lệ.', 'kata-chatbot'));
        }
        
        try {
            global $wpdb;
            
            // Delete messages first
            $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
            $wpdb->delete($messages_table, array('conversation_id' => $conversation_id), array('%d'));
            
            // Delete conversation
            $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
            $deleted = $wpdb->delete($conversations_table, array('id' => $conversation_id), array('%d'));
            
            if ($deleted) {
                wp_send_json_success(__('Cuộc hội thoại đã được xóa.', 'kata-chatbot'));
            } else {
                wp_send_json_error(__('Không thể xóa cuộc hội thoại.', 'kata-chatbot'));
            }
        } catch (Exception $e) {
            error_log('Kata Chatbot Delete Conversation Error: ' . $e->getMessage());
            wp_send_json_error(__('Lỗi khi xóa cuộc hội thoại.', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX handler for submitting feedback
     */
    public function ajax_submit_feedback() {
        check_ajax_referer('kata_chatbot_nonce', 'nonce');
        
        $session_id = sanitize_text_field($_POST['session_id'] ?? '');
        $rating = sanitize_text_field($_POST['rating'] ?? '');
        $feedback = sanitize_textarea_field($_POST['feedback'] ?? '');
        
        if (empty($session_id) || !in_array($rating, ['positive', 'negative'])) {
            wp_send_json_error(__('Dữ liệu feedback không hợp lệ.', 'kata-chatbot'));
        }
        
        try {
            // Save feedback to analytics or conversations table
            global $wpdb;
            $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
            
            $metadata = array(
                'feedback_rating' => $rating,
                'feedback_text' => $feedback,
                'feedback_at' => current_time('mysql')
            );
            
            $updated = $wpdb->update(
                $conversations_table,
                array('metadata' => json_encode($metadata)),
                array('session_id' => $session_id),
                array('%s'),
                array('%s')
            );
            
            if ($updated !== false) {
                wp_send_json_success(__('Cảm ơn bạn đã gửi phản hồi!', 'kata-chatbot'));
            } else {
                wp_send_json_error(__('Không thể lưu phản hồi.', 'kata-chatbot'));
            }
        } catch (Exception $e) {
            error_log('Kata Chatbot Feedback Error: ' . $e->getMessage());
            wp_send_json_error(__('Lỗi khi lưu phản hồi.', 'kata-chatbot'));
        }
    }
    
    /**
     * Check rate limiting
     */
    private function check_rate_limit() {
        $user_key = 'kata_chatbot_rate_' . $this->get_user_identifier();
        $rate_limit = get_option('kata_chatbot_rate_limit', 10);
        
        $requests = get_transient($user_key);
        if ($requests === false) {
            set_transient($user_key, 1, MINUTE_IN_SECONDS);
            return true;
        }
        
        if ($requests >= $rate_limit) {
            return false;
        }
        
        set_transient($user_key, $requests + 1, MINUTE_IN_SECONDS);
        return true;
    }
    
    /**
     * Get user identifier for rate limiting
     */
    private function get_user_identifier() {
        if (is_user_logged_in()) {
            return 'user_' . get_current_user_id();
        }
        return 'ip_' . $this->get_client_ip();
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Render chatbot widget
     */
    public function render_chatbot_widget() {
        // Check if chatbot is enabled - try new settings first, fallback to old
        $new_settings = get_option('kata_chatbot_settings', array());
        $old_options = get_option('kata_chatbot_options', array());
        
        // Use new settings if available, otherwise fallback to old
        if (!empty($new_settings)) {
            $enabled = isset($new_settings['enabled']) ? $new_settings['enabled'] : 1;
        } else {
            $enabled = isset($old_options['enabled']) ? $old_options['enabled'] : 1;
        }
        
        if (!$enabled) {
            return;
        }
        
        // Don't show in admin area
        if (is_admin()) {
            return;
        }
        
        // Check if should show on current page
        $show_on_pages = get_option('kata_chatbot_show_on_pages', array('all'));
        if (!in_array('all', $show_on_pages) && !$this->should_show_on_current_page($show_on_pages)) {
            return;
        }
        
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/chatbot-widget.php';
    }
    
    /**
     * Check if should show on current page
     */
    private function should_show_on_current_page($show_on_pages) {
        global $post;
        
        if (is_home() && in_array('home', $show_on_pages)) return true;
        if (is_page() && in_array('pages', $show_on_pages)) return true;
        if (is_single() && in_array('posts', $show_on_pages)) return true;
        if (is_shop() && in_array('shop', $show_on_pages)) return true;
        
        // Check specific page IDs
        if ($post && in_array($post->ID, $show_on_pages)) return true;
        
        return false;
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('kata-chatbot/v1', '/chat', array(
            'methods' => 'POST',
            'callback' => array($this, 'rest_chat_endpoint'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('kata-chatbot/v1', '/history/(?P<session_id>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'rest_history_endpoint'),
            'permission_callback' => '__return_true'
        ));
    }
    
    /**
     * REST API chat endpoint
     */
    public function rest_chat_endpoint($request) {
        $message = sanitize_textarea_field($request->get_param('message'));
        $session_id = sanitize_text_field($request->get_param('session_id'));
        
        if (empty($message)) {
            return new WP_Error('invalid_message', __('Message is required', 'kata-chatbot'), array('status' => 400));
        }
        
        try {
            $response = $this->chat_handler->process_message($message, $session_id);
            return rest_ensure_response($response);
        } catch (Exception $e) {
            return new WP_Error('chat_error', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * REST API history endpoint
     */
    public function rest_history_endpoint($request) {
        $session_id = $request->get_param('session_id');
        
        try {
            $history = $this->db_handler->get_conversation_history($session_id);
            return rest_ensure_response($history);
        } catch (Exception $e) {
            return new WP_Error('history_error', $e->getMessage(), array('status' => 500));
        }
    }
}

// Initialize plugin
function kata_chatbot_init() {
    return KataChatbot::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'kata_chatbot_init');

// Global functions for template usage
function kata_chatbot_render_widget() {
    $plugin = KataChatbot::get_instance();
    $plugin->render_chatbot_widget();
}

function kata_chatbot_is_enabled() {
    return get_option('kata_chatbot_enabled', true);
}
