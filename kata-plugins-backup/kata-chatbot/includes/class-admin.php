<?php
/**
 * Admin Class
 * 
 * Handles admin functionality and pages
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataChatbot_Admin {
    
    /**
     * Database handler instance
     */
    private $db_handler;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db_handler = new KataChatbot_DB_Handler();
        $this->init();
    }
    
    /**
     * Initialize admin functionality
     */
    private function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // AJAX handlers for admin
        add_action('wp_ajax_kata_chatbot_save_quick_settings', array($this, 'save_quick_settings'));
        add_action('wp_ajax_kata_chatbot_check_api_status', array($this, 'check_api_status'));
        add_action('wp_ajax_kata_chatbot_get_conversation_details', array($this, 'get_conversation_details'));
        add_action('wp_ajax_kata_chatbot_delete_conversation', array($this, 'delete_conversation'));
        add_action('wp_ajax_kata_chatbot_export_data', array($this, 'export_data'));
    }
    
    /**
     * Add admin menu pages
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('Kata Chatbot', 'kata-chatbot'),
            __('Kata Chatbot', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot',
            array($this, 'display_dashboard_page'),
            'dashicons-format-chat',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'kata-chatbot',
            __('Tổng quan', 'kata-chatbot'),
            __('Tổng quan', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot',
            array($this, 'display_dashboard_page')
        );
        
        // Conversations submenu
        add_submenu_page(
            'kata-chatbot',
            __('Cuộc trò chuyện', 'kata-chatbot'),
            __('Cuộc trò chuyện', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot-conversations',
            array($this, 'display_conversations_page')
        );
        
        // Analytics submenu
        add_submenu_page(
            'kata-chatbot',
            __('Thống kê', 'kata-chatbot'),
            __('Thống kê', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot-analytics',
            array($this, 'display_analytics_page')
        );
        
        // Knowledge Base submenu
        add_submenu_page(
            'kata-chatbot',
            __('Cơ sở tri thức', 'kata-chatbot'),
            __('Cơ sở tri thức', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot-knowledge',
            array($this, 'display_knowledge_page')
        );
        
        // Branches submenu
        add_submenu_page(
            'kata-chatbot',
            __('Quản lý chi nhánh', 'kata-chatbot'),
            __('Chi nhánh', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot-branches',
            array($this, 'display_branches_page')
        );
        
        // Settings submenu
        add_submenu_page(
            'kata-chatbot',
            __('Cài đặt', 'kata-chatbot'),
            __('Cài đặt', 'kata-chatbot'),
            'manage_options',
            'kata-chatbot-settings',
            array($this, 'display_settings_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'kata-chatbot') === false) {
            return;
        }
        
        // Enqueue admin CSS
        wp_enqueue_style(
            'kata-chatbot-admin',
            KATA_CHATBOT_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            KATA_CHATBOT_VERSION
        );
        
        // Enqueue admin JS
        wp_enqueue_script(
            'kata-chatbot-admin',
            KATA_CHATBOT_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            KATA_CHATBOT_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('kata-chatbot-admin', 'kata_chatbot_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_chatbot_admin'),
            'strings' => array(
                'confirm_delete' => __('Bạn có chắc chắn muốn xóa cuộc trò chuyện này?', 'kata-chatbot'),
                'deleting' => __('Đang xóa...', 'kata-chatbot'),
                'deleted' => __('Đã xóa thành công', 'kata-chatbot'),
                'error' => __('Có lỗi xảy ra', 'kata-chatbot'),
                'saving' => __('Đang lưu...', 'kata-chatbot'),
                'saved' => __('Đã lưu cài đặt', 'kata-chatbot')
            )
        ));
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        // General settings
        register_setting('kata_chatbot_general', 'kata_chatbot_enabled');
        register_setting('kata_chatbot_general', 'kata_chatbot_name');
        register_setting('kata_chatbot_general', 'kata_chatbot_welcome_message');
        register_setting('kata_chatbot_general', 'kata_chatbot_offline_message');
        
        // API settings
        register_setting('kata_chatbot_api', 'kata_chatbot_google_api_key');
        register_setting('kata_chatbot_api', 'kata_chatbot_ai_model');
        register_setting('kata_chatbot_api', 'kata_chatbot_max_tokens');
        register_setting('kata_chatbot_api', 'kata_chatbot_temperature');
        
        // Widget settings
        register_setting('kata_chatbot_widget', 'kata_chatbot_position');
        register_setting('kata_chatbot_widget', 'kata_chatbot_theme');
        register_setting('kata_chatbot_widget', 'kata_chatbot_sound_enabled');
        register_setting('kata_chatbot_widget', 'kata_chatbot_avatar');
        
        // Advanced settings
        register_setting('kata_chatbot_advanced', 'kata_chatbot_max_message_length');
        register_setting('kata_chatbot_advanced', 'kata_chatbot_context_memory');
        register_setting('kata_chatbot_advanced', 'kata_chatbot_rate_limit');
        register_setting('kata_chatbot_advanced', 'kata_chatbot_debug_mode');
    }
    
    /**
     * Display dashboard page
     */
    public function display_dashboard_page() {
        // Get dashboard statistics
        $stats = $this->db_handler->get_dashboard_stats();
        
        // Get recent conversations
        $recent_conversations = $this->db_handler->get_admin_conversations(5, 0);
        
        // Get analytics data for charts
        $analytics_data = $this->db_handler->get_analytics_data(30);
        
        // Get knowledge base stats
        $knowledge_stats = array(
            'total_items' => count($this->db_handler->get_knowledge_base_items()),
            'active_items' => count($this->db_handler->get_knowledge_base_items('', 'active'))
        );
        
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/admin-dashboard.php';
    }
    
    /**
     * Display conversations page
     */
    public function display_conversations_page() {
        $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($page - 1) * $per_page;
        
        $conversations = $this->db_handler->get_admin_conversations($per_page, $offset);
        $total_conversations = $this->db_handler->get_total_conversations();
        $total_pages = ceil($total_conversations / $per_page);
        
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/admin-conversations.php';
    }
    
    /**
     * Display analytics page
     */
    public function display_analytics_page() {
        $stats = $this->db_handler->get_dashboard_stats();
        $analytics_data = $this->db_handler->get_analytics_data();
        
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/admin-analytics.php';
    }
    
    /**
     * Display knowledge base page
     */
    public function display_knowledge_page() {
        // Handle form submission
        if (isset($_POST['submit_knowledge']) && wp_verify_nonce($_POST['_wpnonce'], 'kata_chatbot_knowledge')) {
            $this->handle_knowledge_form();
        }
        
        $knowledge_items = $this->db_handler->get_knowledge_base_items();
        
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/admin-knowledge.php';
    }
    
    /**
     * Display settings page
     */
    public function display_settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
        
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/admin-settings.php';
    }
    
    /**
     * Handle knowledge base form submission
     */
    private function handle_knowledge_form() {
        $action = sanitize_text_field($_POST['action']);
        
        if ($action === 'add') {
            $question = sanitize_textarea_field($_POST['question']);
            $answer = sanitize_textarea_field($_POST['answer']);
            $category = sanitize_text_field($_POST['category']);
            
            if ($question && $answer) {
                $result = $this->db_handler->add_knowledge_item($question, $answer, $category);
                if ($result) {
                    add_settings_error('kata_chatbot_knowledge', 'success', __('Đã thêm mục tri thức thành công', 'kata-chatbot'), 'success');
                } else {
                    add_settings_error('kata_chatbot_knowledge', 'error', __('Failed to add knowledge item', 'kata-chatbot'), 'error');
                }
            }
        } elseif ($action === 'edit') {
            $id = intval($_POST['item_id']);
            $question = sanitize_textarea_field($_POST['question']);
            $answer = sanitize_textarea_field($_POST['answer']);
            $category = sanitize_text_field($_POST['category']);
            
            if ($id && $question && $answer) {
                $result = $this->db_handler->update_knowledge_item($id, $question, $answer, $category);
                if ($result) {
                    add_settings_error('kata_chatbot_knowledge', 'success', __('Knowledge item updated successfully', 'kata-chatbot'), 'success');
                } else {
                    add_settings_error('kata_chatbot_knowledge', 'error', __('Failed to update knowledge item', 'kata-chatbot'), 'error');
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['item_id']);
            
            if ($id) {
                $result = $this->db_handler->delete_knowledge_item($id);
                if ($result) {
                    add_settings_error('kata_chatbot_knowledge', 'success', __('Knowledge item deleted successfully', 'kata-chatbot'), 'success');
                } else {
                    add_settings_error('kata_chatbot_knowledge', 'error', __('Failed to delete knowledge item', 'kata-chatbot'), 'error');
                }
            }
        }
    }
    
    /**
     * AJAX: Save quick settings
     */
    public function save_quick_settings() {
        check_ajax_referer('kata_chatbot_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'kata-chatbot'));
        }
        
        $settings = $_POST['settings'];
        
        if (isset($settings['kata_chatbot_enabled'])) {
            update_option('kata_chatbot_enabled', (bool) $settings['kata_chatbot_enabled']);
        }
        
        if (isset($settings['kata_chatbot_sound_enabled'])) {
            update_option('kata_chatbot_sound_enabled', (bool) $settings['kata_chatbot_sound_enabled']);
        }
        
        if (isset($settings['kata_chatbot_offline_mode'])) {
            update_option('kata_chatbot_offline_mode', (bool) $settings['kata_chatbot_offline_mode']);
        }
        
        wp_send_json_success(array('message' => __('Settings saved successfully', 'kata-chatbot')));
    }
    
    /**
     * AJAX: Check API status
     */
    public function check_api_status() {
        check_ajax_referer('kata_chatbot_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'kata-chatbot'));
        }
        
        $api_key = get_option('kata_chatbot_google_api_key');
        
        if (empty($api_key)) {
            wp_send_json_error(array('message' => __('API key not configured', 'kata-chatbot')));
        }
        
        // Simple API test
        try {
            $ai_handler = new KataChatbot_AI_Handler();
            $response = $ai_handler->generate_response('test', array());
            
            if ($response['success']) {
                wp_send_json_success(array('status' => 'ok', 'message' => __('API is working', 'kata-chatbot')));
            } else {
                wp_send_json_error(array('message' => __('API test failed', 'kata-chatbot')));
            }
        } catch (Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
    
    /**
     * AJAX: Get conversation details
     */
    public function get_conversation_details() {
        check_ajax_referer('kata_chatbot_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'kata-chatbot'));
        }
        
        $conversation_id = intval($_POST['conversation_id']);
        
        if (!$conversation_id) {
            wp_send_json_error(array('message' => __('Invalid conversation ID', 'kata-chatbot')));
        }
        
        $conversation = $this->db_handler->get_conversation_by_id($conversation_id);
        $messages = $this->db_handler->get_conversation_messages($conversation_id);
        
        if (!$conversation) {
            wp_send_json_error(array('message' => __('Conversation not found', 'kata-chatbot')));
        }
        
        wp_send_json_success(array(
            'conversation' => $conversation,
            'messages' => $messages
        ));
    }
    
    /**
     * AJAX: Delete conversation
     */
    public function delete_conversation() {
        check_ajax_referer('kata_chatbot_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'kata-chatbot'));
        }
        
        $conversation_id = intval($_POST['conversation_id']);
        
        if (!$conversation_id) {
            wp_send_json_error(array('message' => __('Invalid conversation ID', 'kata-chatbot')));
        }
        
        $result = $this->db_handler->delete_conversation($conversation_id);
        
        if ($result) {
            wp_send_json_success(array('message' => __('Conversation deleted successfully', 'kata-chatbot')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete conversation', 'kata-chatbot')));
        }
    }
    
    /**
     * AJAX: Export data
     */
    public function export_data() {
        check_ajax_referer('kata_chatbot_admin', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'kata-chatbot'));
        }
        
        $export_type = sanitize_text_field($_POST['export_type']);
        $date_from = sanitize_text_field($_POST['date_from']);
        $date_to = sanitize_text_field($_POST['date_to']);
        
        try {
            $data = array();
            
            switch ($export_type) {
                case 'conversations':
                    $data = $this->db_handler->export_conversations($date_from, $date_to);
                    break;
                case 'messages':
                    $data = $this->db_handler->export_messages($date_from, $date_to);
                    break;
                case 'analytics':
                    $data = $this->db_handler->export_analytics($date_from, $date_to);
                    break;
                default:
                    wp_send_json_error(array('message' => __('Invalid export type', 'kata-chatbot')));
            }
            
            wp_send_json_success(array(
                'data' => $data,
                'filename' => 'kata_chatbot_' . $export_type . '_' . date('Y-m-d') . '.csv'
            ));
            
        } catch (Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }
    
    /**
     * Add admin notices
     */
    public function add_admin_notices() {
        // Check if API key is configured
        $api_key = get_option('kata_chatbot_google_api_key');
        if (empty($api_key) && isset($_GET['page']) && strpos($_GET['page'], 'kata-chatbot') !== false) {
            ?>
            <div class="notice notice-warning">
                <p>
                    <?php 
                    printf(
                        __('Kata Chatbot: Please configure your Google AI API key in %s to enable the chatbot.', 'kata-chatbot'),
                        '<a href="' . admin_url('admin.php?page=kata-chatbot-settings&tab=api') . '">' . __('Settings', 'kata-chatbot') . '</a>'
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }
    
    /**
     * Get admin page URL
     */
    public function get_admin_url($page = 'kata-chatbot', $params = array()) {
        $url = admin_url('admin.php?page=' . $page);
        
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        
        return $url;
    }
    
    /**
     * Display branches management page
     */
    public function display_branches_page() {
        // Include the branches page template
        include KATA_CHATBOT_PLUGIN_PATH . 'templates/admin-branches.php';
    }
    
    /**
     * Check if current page is plugin admin page
     */
    public function is_plugin_page() {
        return isset($_GET['page']) && strpos($_GET['page'], 'kata-chatbot') !== false;
    }
}
