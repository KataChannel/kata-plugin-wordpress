<?php
/**
 * Plugin Name: KATA SEO Manager
 * Plugin URI: https://katachannel.com/kata-seo-manager
 * Description: Complete SEO Schema Manager with 26 Schema Types - Manage, configure, and track all KATA SEO features with Google-compliant Schema Markup
 * Version: 1.0.0
 * Author: KATA Channel
 * Author URI: https://katachannel.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kata-seo-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package KATA_SEO_Manager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('KATA_SEO_MANAGER_VERSION', '1.0.0');
define('KATA_SEO_MANAGER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATA_SEO_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SEO_MANAGER_PLUGIN_FILE', __FILE__);
define('KATA_SEO_MANAGER_PATH', KATA_SEO_MANAGER_PLUGIN_DIR);

/**
 * Main KATA SEO Manager Class
 */
class KATA_SEO_Manager {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
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
        $this->includes();
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        // Core classes
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-database.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-schema-generator.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-editor-integration.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-admin-settings.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-statistics.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-quiz-manager.php';
        
        // Base schema class (must be loaded first)
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-base-schema.php';
        
        // Schema handlers (26 types)
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-article-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-breadcrumb-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-carousel-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-course-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-dataset-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-forum-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-edu-qa-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-employer-rating-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-event-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-faq-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-how-to-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-image-metadata-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-job-posting-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-local-business-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-math-solver-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-movie-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-organization-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-practice-problem-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-product-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-profile-page-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-recipe-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-review-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-sitelinks-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-speakable-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-video-schema.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-web-page-schema.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        
        // Admin notices
        add_action('admin_notices', array($this, 'show_admin_notices'));
        
        // Schema output
        add_action('wp_head', array($this, 'output_schema_markup'), 1);
        
        // AJAX handlers
        add_action('wp_ajax_kata_seo_create_schema', array($this, 'ajax_create_schema'));
        add_action('wp_ajax_nopriv_kata_seo_create_schema', array($this, 'ajax_create_schema'));
        add_action('wp_ajax_kata_seo_get_stats', array($this, 'ajax_get_stats'));
        add_action('wp_ajax_nopriv_kata_seo_get_stats', array($this, 'ajax_get_stats'));
        add_action('wp_ajax_kata_seo_validate_schema', array($this, 'ajax_validate_schema'));
        add_action('wp_ajax_nopriv_kata_seo_validate_schema', array($this, 'ajax_validate_schema'));
        
        // User Interaction AJAX handlers
        add_action('wp_ajax_kata_submit_user_interaction', array($this, 'ajax_submit_user_interaction'));
        add_action('wp_ajax_nopriv_kata_submit_user_interaction', array($this, 'ajax_submit_user_interaction'));
        add_action('wp_ajax_kata_load_user_interactions', array($this, 'ajax_load_user_interactions'));
        add_action('wp_ajax_nopriv_kata_load_user_interactions', array($this, 'ajax_load_user_interactions'));
        
        // Poll AJAX handlers
        add_action('wp_ajax_kata_submit_poll_vote', array($this, 'ajax_submit_poll_vote'));
        add_action('wp_ajax_nopriv_kata_submit_poll_vote', array($this, 'ajax_submit_poll_vote'));
        add_action('wp_ajax_kata_get_poll_results', array($this, 'ajax_get_poll_results'));
        add_action('wp_ajax_nopriv_kata_get_poll_results', array($this, 'ajax_get_poll_results'));
        
        // Poll management AJAX
        add_action('wp_ajax_kata_get_poll_for_edit', array($this, 'ajax_get_poll_for_edit'));
        
        // Wheel AJAX handlers
        add_action('wp_ajax_kata_get_wheel_data', array($this, 'ajax_get_wheel_data'));
        add_action('wp_ajax_nopriv_kata_get_wheel_data', array($this, 'ajax_get_wheel_data'));
        add_action('wp_ajax_kata_spin_wheel', array($this, 'ajax_spin_wheel'));
        add_action('wp_ajax_nopriv_kata_spin_wheel', array($this, 'ajax_spin_wheel'));
        add_action('wp_ajax_kata_check_wheel_eligibility', array($this, 'ajax_check_wheel_eligibility'));
        add_action('wp_ajax_nopriv_kata_check_wheel_eligibility', array($this, 'ajax_check_wheel_eligibility'));
        
        // Wheel management AJAX
        add_action('wp_ajax_kata_get_wheel_for_edit', array($this, 'ajax_get_wheel_for_edit'));
        
        // Shortcodes - need to be registered on init
        add_action('init', array($this, 'register_shortcodes'));
        
        // TinyMCE Integration
        add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        add_filter('mce_external_plugins', array($this, 'register_tinymce_plugin'));
        
        // Ensure TinyMCE works properly
        add_action('admin_head', array($this, 'tinymce_admin_head'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        try {
            // Create database tables
            $database = new KATA_SEO_Database();
            $result = $database->create_tables();
            
            if (!$result) {
                throw new Exception('Failed to create database tables');
            }
            
            // Set default options
            add_option('kata_seo_manager_version', KATA_SEO_MANAGER_VERSION);
            add_option('kata_seo_manager_settings', array(
                'enable_auto_schema' => true,
                'default_schema_types' => array('Article', 'Breadcrumb', 'WebPage'),
                'enable_editor_button' => true,
                'enable_statistics' => true,
                'installation_date' => current_time('mysql'),
                'activation_count' => 1
            ));
            
            // Initialize plugin data
            $this->initialize_default_schemas();
            
            // Set activation success flag
            update_option('kata_seo_manager_activated', true);
            update_option('kata_seo_manager_activation_date', current_time('mysql'));
            
            flush_rewrite_rules();
            
        } catch (Exception $e) {
            // Log error and show admin notice
            error_log('KATA SEO Manager Activation Error: ' . $e->getMessage());
            add_option('kata_seo_manager_activation_error', $e->getMessage());
        }
    }
    
    /**
     * Initialize default schemas for better setup
     */
    private function initialize_default_schemas() {
        $default_schemas = array(
            'article' => array(
                'enabled' => true,
                'auto_insert' => true,
                'post_types' => array('post')
            ),
            'webpage' => array(
                'enabled' => true,
                'auto_insert' => true,
                'post_types' => array('page')
            ),
            'breadcrumb' => array(
                'enabled' => true,
                'auto_insert' => true,
                'post_types' => array('post', 'page')
            )
        );
        
        add_option('kata_seo_default_schemas', $default_schemas);
    }
    
    /**
     * Create plugin database tables
     */
    public function create_plugin_tables() {
        $database = new KATA_SEO_Database();
        return $database->create_tables();
    }
    
    /**
     * Show admin notices
     */
    public function show_admin_notices() {
        // Check for activation errors
        $activation_error = get_option('kata_seo_manager_activation_error');
        if ($activation_error) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p><strong>KATA SEO Manager:</strong> Activation error - ' . esc_html($activation_error) . '</p>';
            echo '<p>Please check your database permissions and try again.</p>';
            echo '</div>';
            delete_option('kata_seo_manager_activation_error');
        }
        
        // Show welcome notice for new installations
        if (get_option('kata_seo_manager_activated') && !get_option('kata_seo_welcome_shown')) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p><strong>🎉 KATA SEO Manager activated successfully!</strong></p>';
            echo '<p>Plugin is ready to use. Visit <a href="' . admin_url('admin.php?page=kata-seo-manager') . '">Settings</a> to configure.</p>';
            echo '</div>';
            update_option('kata_seo_welcome_shown', true);
        }
        
        // Check database tables
        if (get_option('kata_seo_manager_activated')) {
            $database = new KATA_SEO_Database();
            if (!$database->verify_tables()) {
                echo '<div class="notice notice-warning is-dismissible">';
                echo '<p><strong>KATA SEO Manager:</strong> Database tables missing. <a href="' . admin_url('admin.php?page=kata-seo-manager&action=repair-db') . '">Repair Database</a></p>';
                echo '</div>';
            }
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'kata-seo') === false) {
            return;
        }
        
        $plugin_url = plugin_dir_url(__FILE__);
        $version = KATA_SEO_MANAGER_VERSION;
        
        // Admin CSS
        wp_enqueue_style(
            'kata-seo-admin',
            $plugin_url . 'assets/css/admin.css',
            array(),
            $version
        );
        
        // Admin JS
        wp_enqueue_script(
            'kata-seo-admin',
            $plugin_url . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            $version,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('kata-seo-admin', 'kata_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_ajax_nonce'),
            'plugin_url' => $plugin_url
        ));
        
        // Color picker
        wp_enqueue_style('wp-color-picker');
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_frontend_scripts() {
        $plugin_url = plugin_dir_url(__FILE__);
        $version = KATA_SEO_MANAGER_VERSION;
        
        // Main frontend CSS
        wp_enqueue_style(
            'kata-seo-frontend',
            $plugin_url . 'assets/css/frontend.css',
            array(),
            $version
        );
        
        // Main frontend JS
        wp_enqueue_script(
            'kata-seo-frontend',
            $plugin_url . 'assets/js/frontend.js',
            array('jquery'),
            $version,
            true
        );
        
        // Wheel CSS & JS - Always load for better compatibility
        wp_enqueue_style(
            'kata-wheel-frontend',
            $plugin_url . 'assets/css/wheel-frontend.css',
            array('kata-seo-frontend'),
            $version
        );
        
        wp_enqueue_script(
            'kata-wheel-frontend',
            $plugin_url . 'assets/js/wheel-frontend.js',
            array('jquery', 'kata-seo-frontend'),
            $version,
            true
        );
        
        // Poll CSS & JS - Always load for better compatibility
        wp_enqueue_style(
            'kata-poll-frontend',
            $plugin_url . 'assets/css/poll-frontend.css',
            array('kata-seo-frontend'),
            $version
        );
        
        wp_enqueue_script(
            'kata-poll-frontend',
            $plugin_url . 'assets/js/poll-frontend.js',
            array('jquery', 'kata-seo-frontend'),
            $version,
            true
        );
        
        // Schema CSS
        wp_enqueue_style(
            'kata-schema-frontend',
            $plugin_url . 'assets/css/schema-frontend.css',
            array('kata-seo-frontend'),
            $version
        );
        
        // Localize script for AJAX
        wp_localize_script('kata-seo-frontend', 'kata_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_ajax_nonce'),
            'plugin_url' => $plugin_url,
            'is_user_logged_in' => is_user_logged_in(),
            'current_user_id' => get_current_user_id()
        ));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('KATA SEO Manager', 'kata-seo-manager'),
            __('KATA SEO', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-manager',
            array($this, 'admin_dashboard_page'),
            'dashicons-chart-line',
            30
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Bảng điều khiển', 'kata-seo-manager'),
            __('Bảng điều khiển', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-manager',
            array($this, 'admin_dashboard_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Loại Schema', 'kata-seo-manager'),
            __('Loại Schema', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-schema-types',
            array($this, 'admin_schema_types_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Thống kê', 'kata-seo-manager'),
            __('Thống kê', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-statistics',
            array($this, 'admin_statistics_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Quiz Management', 'kata-seo-manager'),
            __('Quiz Management', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-quiz-management',
            array($this, 'admin_quiz_management_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Phân tích Quiz', 'kata-seo-manager'),
            __('Phân tích Quiz', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-quiz-analytics',
            array($this, 'admin_quiz_analytics_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('User Interactions', 'kata-seo-manager'),
            __('User Interactions', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-user-interactions',
            array($this, 'admin_user_interactions_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Quản lý Poll', 'kata-seo-manager'),
            __('Quản lý Poll', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-poll-management',
            array($this, 'admin_poll_management_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Phân tích Poll', 'kata-seo-manager'),
            __('Phân tích Poll', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-poll-analytics',
            array($this, 'admin_poll_analytics_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Quản lý Vòng Quay', 'kata-seo-manager'),
            __('Quản lý Vòng Quay', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-wheel-management',
            array($this, 'admin_wheel_management_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Phân tích Vòng Quay', 'kata-seo-manager'),
            __('Phân tích Vòng Quay', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-wheel-analytics',
            array($this, 'admin_wheel_analytics_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Cài đặt', 'kata-seo-manager'),
            __('Cài đặt', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-settings',
            array($this, 'admin_settings_page')
        );
    }
    
    /**
     * Dashboard page
     */
    public function admin_dashboard_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/dashboard.php';
    }
    
    /**
     * Schema types page
     */
    public function admin_schema_types_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/schema-types.php';
    }
    
    /**
     * Statistics page
     */
    public function admin_statistics_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/statistics.php';
    }
    
    /**
     * Quiz management page
     */
    public function admin_quiz_management_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/quiz-management.php';
    }
    
    /**
     * Quiz analytics page
     */
    public function admin_quiz_analytics_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/quiz-analytics.php';
    }
    
    /**
     * User Interactions page
     */
    public function admin_user_interactions_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/user-interactions.php';
    }
    
    /**
     * Poll Management page
     */
    public function admin_poll_management_page() {
        $this->handle_poll_actions();
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/poll-management.php';
    }
    
    /**
     * Poll Analytics page
     */
    public function admin_poll_analytics_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/poll-analytics.php';
    }
    
    /**
     * Wheel Management page
     */
    public function admin_wheel_management_page() {
        $this->handle_wheel_actions();
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/wheel-management.php';
    }
    
    /**
     * Wheel Analytics page
     */
    public function admin_wheel_analytics_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/wheel-analytics.php';
    }
    
    /**
     * Handle wheel management actions
     */
    private function handle_wheel_actions() {
        if (!isset($_POST['kata_wheel_action']) || !wp_verify_nonce($_POST['_wpnonce'], 'kata_wheel_action')) {
            return;
        }
        
        global $wpdb;
        $action = sanitize_text_field($_POST['kata_wheel_action']);
        
        switch ($action) {
            case 'create':
                $this->handle_create_wheel();
                break;
            case 'update':
                $this->handle_update_wheel();
                break;
            case 'delete':
                $this->handle_delete_wheel();
                break;
            case 'toggle_status':
                $this->handle_toggle_wheel_status();
                break;
        }
    }
    
    /**
     * Handle create wheel
     */
    private function handle_create_wheel() {
        global $wpdb;
        
        $wheel_title = sanitize_text_field($_POST['wheel_title']);
        $wheel_description = sanitize_textarea_field($_POST['wheel_description'] ?? '');
        $requirement = sanitize_text_field($_POST['requirement'] ?? 'email');
        $max_spins_per_user = intval($_POST['max_spins_per_user'] ?? 1);
        $max_spins_per_day = intval($_POST['max_spins_per_day'] ?? 1);
        
        // Insert wheel
        $result = $wpdb->insert(
            $wpdb->prefix . 'kata_wheels',
            array(
                'wheel_title' => $wheel_title,
                'wheel_description' => $wheel_description,
                'requirement' => $requirement,
                'max_spins_per_user' => $max_spins_per_user,
                'max_spins_per_day' => $max_spins_per_day,
                'status' => 'active'
            ),
            array('%s', '%s', '%s', '%d', '%d', '%s')
        );
        
        if ($result) {
            $wheel_id = $wpdb->insert_id;
            
            // Insert prizes
            if (!empty($_POST['prize_texts'])) {
                $prize_texts = $_POST['prize_texts'];
                $prize_values = $_POST['prize_values'] ?? array();
                $prize_types = $_POST['prize_types'] ?? array();
                $probabilities = $_POST['probabilities'] ?? array();
                $colors = $_POST['colors'] ?? array();
                
                foreach ($prize_texts as $index => $text) {
                    if (!empty($text)) {
                        $wpdb->insert(
                            $wpdb->prefix . 'kata_wheel_prizes',
                            array(
                                'wheel_id' => $wheel_id,
                                'prize_text' => sanitize_text_field($text),
                                'prize_value' => sanitize_text_field($prize_values[$index] ?? ''),
                                'prize_type' => sanitize_text_field($prize_types[$index] ?? 'discount'),
                                'probability' => floatval($probabilities[$index] ?? 0),
                                'color' => sanitize_hex_color($colors[$index] ?? '#ff6b6b'),
                                'position_order' => $index
                            ),
                            array('%d', '%s', '%s', '%s', '%f', '%s', '%d')
                        );
                    }
                }
            }
            
            echo '<div class="notice notice-success"><p>Đã tạo vòng quay thành công!</p></div>';
        }
    }
    
    /**
     * Handle update wheel
     */
    private function handle_update_wheel() {
        global $wpdb;
        
        $wheel_id = intval($_POST['wheel_id']);
        $wheel_title = sanitize_text_field($_POST['wheel_title']);
        $wheel_description = sanitize_textarea_field($_POST['wheel_description'] ?? '');
        $requirement = sanitize_text_field($_POST['requirement'] ?? 'email');
        $max_spins_per_user = intval($_POST['max_spins_per_user'] ?? 1);
        $max_spins_per_day = intval($_POST['max_spins_per_day'] ?? 1);
        $status = sanitize_text_field($_POST['wheel_status'] ?? 'active');
        
        $wpdb->update(
            $wpdb->prefix . 'kata_wheels',
            array(
                'wheel_title' => $wheel_title,
                'wheel_description' => $wheel_description,
                'requirement' => $requirement,
                'max_spins_per_user' => $max_spins_per_user,
                'max_spins_per_day' => $max_spins_per_day,
                'status' => $status
            ),
            array('id' => $wheel_id),
            array('%s', '%s', '%s', '%d', '%d', '%s'),
            array('%d')
        );
        
        // Update prizes
        if (!empty($_POST['prize_texts'])) {
            // Delete existing prizes
            $wpdb->delete($wpdb->prefix . 'kata_wheel_prizes', array('wheel_id' => $wheel_id), array('%d'));
            
            // Insert updated prizes
            $prize_texts = $_POST['prize_texts'];
            $prize_values = $_POST['prize_values'] ?? array();
            $prize_types = $_POST['prize_types'] ?? array();
            $probabilities = $_POST['probabilities'] ?? array();
            $colors = $_POST['colors'] ?? array();
            
            foreach ($prize_texts as $index => $text) {
                if (!empty($text)) {
                    $wpdb->insert(
                        $wpdb->prefix . 'kata_wheel_prizes',
                        array(
                            'wheel_id' => $wheel_id,
                            'prize_text' => sanitize_text_field($text),
                            'prize_value' => sanitize_text_field($prize_values[$index] ?? ''),
                            'prize_type' => sanitize_text_field($prize_types[$index] ?? 'discount'),
                            'probability' => floatval($probabilities[$index] ?? 0),
                            'color' => sanitize_hex_color($colors[$index] ?? '#ff6b6b'),
                            'position_order' => $index
                        ),
                        array('%d', '%s', '%s', '%s', '%f', '%s', '%d')
                    );
                }
            }
        }
        
        echo '<div class="notice notice-success"><p>Đã cập nhật vòng quay thành công!</p></div>';
    }
    
    /**
     * Handle delete wheel
     */
    private function handle_delete_wheel() {
        global $wpdb;
        
        $wheel_id = intval($_POST['wheel_id']);
        
        // Delete prizes
        $wpdb->delete($wpdb->prefix . 'kata_wheel_prizes', array('wheel_id' => $wheel_id), array('%d'));
        
        // Delete wheel
        $wpdb->delete($wpdb->prefix . 'kata_wheels', array('id' => $wheel_id), array('%d'));
        
        echo '<div class="notice notice-success"><p>Đã xóa vòng quay thành công!</p></div>';
    }
    
    /**
     * Handle toggle wheel status
     */
    private function handle_toggle_wheel_status() {
        global $wpdb;
        
        $wheel_id = intval($_POST['wheel_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        $wpdb->update(
            $wpdb->prefix . 'kata_wheels',
            array('status' => $new_status),
            array('id' => $wheel_id),
            array('%s'),
            array('%d')
        );
        
        echo '<div class="notice notice-success"><p>Đã cập nhật trạng thái thành công!</p></div>';
    }
    
    /**
     * Handle poll management actions
     */
    private function handle_poll_actions() {
        if (!isset($_POST['kata_poll_action']) || !wp_verify_nonce($_POST['_wpnonce'], 'kata_poll_action')) {
            return;
        }
        
        global $wpdb;
        $action = sanitize_text_field($_POST['kata_poll_action']);
        
        switch ($action) {
            case 'create':
                $this->handle_create_poll();
                break;
            case 'update':
                $this->handle_update_poll();
                break;
            case 'delete':
                $this->handle_delete_poll();
                break;
            case 'toggle_status':
                $this->handle_toggle_poll_status();
                break;
        }
    }
    
    /**
     * Handle create poll
     */
    private function handle_create_poll() {
        $title = sanitize_text_field($_POST['poll_title']);
        $description = sanitize_textarea_field($_POST['poll_description']);
        $question = sanitize_textarea_field($_POST['poll_question']);
        $options = array_map('sanitize_text_field', $_POST['poll_options']);
        $options = array_filter($options); // Remove empty options
        
        if (empty($title) || empty($question) || count($options) < 2) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Vui lòng điền đầy đủ thông tin poll với ít nhất 2 tùy chọn.</p></div>';
            });
            return;
        }
        
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'kata_polls',
            array(
                'post_id' => 0,
                'poll_title' => $title,
                'poll_description' => $description,
                'poll_question' => $question,
                'poll_options' => json_encode($options),
                'total_votes' => 0,
                'status' => 'active',
                'show_results' => 'after_vote',
                'allow_multiple' => 'no',
                'require_login' => 'no',
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Poll đã được tạo thành công!</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Lỗi khi tạo poll: ' . $wpdb->last_error . '</p></div>';
            });
        }
    }
    
    /**
     * Handle update poll
     */
    private function handle_update_poll() {
        $poll_id = intval($_POST['poll_id']);
        $title = sanitize_text_field($_POST['poll_title']);
        $description = sanitize_textarea_field($_POST['poll_description']);
        $question = sanitize_textarea_field($_POST['poll_question']);
        $options = array_map('sanitize_text_field', $_POST['poll_options']);
        $options = array_filter($options);
        $status = sanitize_text_field($_POST['poll_status']);
        
        if (empty($title) || empty($question) || count($options) < 2) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Vui lòng điền đầy đủ thông tin poll với ít nhất 2 tùy chọn.</p></div>';
            });
            return;
        }
        
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'kata_polls',
            array(
                'poll_title' => $title,
                'poll_description' => $description,
                'poll_question' => $question,
                'poll_options' => json_encode($options),
                'status' => $status,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $poll_id),
            array('%s', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Poll đã được cập nhật thành công!</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Lỗi khi cập nhật poll.</p></div>';
            });
        }
    }
    
    /**
     * Handle delete poll
     */
    private function handle_delete_poll() {
        $poll_id = intval($_POST['poll_id']);
        
        global $wpdb;
        // Delete votes first
        $wpdb->delete($wpdb->prefix . 'kata_poll_votes', array('poll_id' => $poll_id), array('%d'));
        // Delete poll
        $result = $wpdb->delete($wpdb->prefix . 'kata_polls', array('id' => $poll_id), array('%d'));
        
        if ($result) {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success"><p>Poll đã được xóa thành công!</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-error"><p>Lỗi khi xóa poll.</p></div>';
            });
        }
    }
    
    /**
     * Handle toggle poll status
     */
    private function handle_toggle_poll_status() {
        $poll_id = intval($_POST['poll_id']);
        $new_status = sanitize_text_field($_POST['new_status']);
        
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'kata_polls',
            array('status' => $new_status, 'updated_at' => current_time('mysql')),
            array('id' => $poll_id),
            array('%s', '%s'),
            array('%d')
        );
        
        if ($result !== false) {
            add_action('admin_notices', function() use ($new_status) {
                $status_text = $new_status === 'active' ? 'kích hoạt' : 'tạm dừng';
                echo '<div class="notice notice-success"><p>Poll đã được ' . $status_text . ' thành công!</p></div>';
            });
        }
    }
    
    /**
     * Settings page
     */
    public function admin_settings_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/settings.php';
    }
    

    

    

    

    
    /**
     * Output schema markup
     */
    public function output_schema_markup() {
        if (!is_singular()) {
            return;
        }
        
        global $post;
        $schemas = get_post_meta($post->ID, '_kata_seo_schemas', true);
        
        if (empty($schemas) || !is_array($schemas)) {
            return;
        }
        
        $generator = new KATA_SEO_Schema_Generator();
        
        foreach ($schemas as $schema_data) {
            $result = $generator->generate($schema_data['type'], $schema_data['data']);
            
            // Handle both array return and direct schema
            $schema_markup = null;
            if (is_array($result) && isset($result['schema'])) {
                $schema_markup = $result['schema'];
            } elseif (is_array($result) && !isset($result['errors'])) {
                $schema_markup = $result;
            }
            
            if ($schema_markup && !is_wp_error($schema_markup)) {
                echo '<script type="application/ld+json">' . "\n";
                echo json_encode($schema_markup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                echo "\n" . '</script>' . "\n";
            }
        }
    }
    
    /**
     * AJAX: Create schema
     */
    public function ajax_create_schema() {
        // Security check
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kata_seo_manager_nonce')) {
            wp_send_json_error(array('message' => __('Kiểm tra bảo mật thất bại', 'kata-seo-manager')));
            return;
        }
        
        // Capability check for logged-in users
        if (is_user_logged_in() && !current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Không đủ quyền hạn', 'kata-seo-manager')));
            return;
        }
        
        $type = sanitize_text_field($_POST['type'] ?? '');
        $data = json_decode(stripslashes($_POST['data'] ?? '{}'), true);
        $post_id = intval($_POST['post_id'] ?? 0);
        
        if (empty($type) || empty($data)) {
            wp_send_json_error(array('message' => __('Dữ liệu không hợp lệ', 'kata-seo-manager')));
            return;
        }
        
        $generator = new KATA_SEO_Schema_Generator();
        $schema = $generator->generate($type, $data);
        
        if ($schema) {
            $schemas = get_post_meta($post_id, '_kata_seo_schemas', true);
            if (!is_array($schemas)) {
                $schemas = array();
            }
            
            $schemas[] = array(
                'type' => $type,
                'data' => $data,
                'created' => current_time('mysql')
            );
            
            update_post_meta($post_id, '_kata_seo_schemas', $schemas);
            
            wp_send_json_success(array(
                'schema' => $schema,
                'message' => __('Schema created successfully', 'kata-seo-manager')
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to generate schema', 'kata-seo-manager')
            ));
        }
    }
    
    /**
     * AJAX: Get statistics
     */
    public function ajax_get_stats() {
        // Security check
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kata_seo_manager_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'kata-seo-manager')));
            return;
        }
        
        // Capability check for logged-in users
        if (is_user_logged_in() && !current_user_can('read')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'kata-seo-manager')));
            return;
        }
        
        $stats = new KATA_SEO_Statistics();
        $data = $stats->get_all_stats();
        
        wp_send_json_success($data);
    }
    
    /**
     * AJAX: Validate schema
     */
    public function ajax_validate_schema() {
        // Security check
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'kata_seo_manager_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'kata-seo-manager')));
            return;
        }
        
        // Capability check for logged-in users
        if (is_user_logged_in() && !current_user_can('read')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'kata-seo-manager')));
            return;
        }
        
        $schema_data = $_POST['schema'] ?? '';
        if (empty($schema_data)) {
            wp_send_json_error(array('message' => __('No schema data provided', 'kata-seo-manager')));
            return;
        }
        
        $schema = json_decode(stripslashes($schema_data), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid JSON data', 'kata-seo-manager')));
            return;
        }
        
        $generator = new KATA_SEO_Schema_Generator();
        $validation = $generator->validate($schema);
        
        wp_send_json_success($validation);
    }
    
    /**
     * Get all schema types
     */
    public function get_schema_types() {
        return array(
            'Article' => __('Article (Bài viết)', 'kata-seo-manager'),
            'Breadcrumb' => __('Breadcrumb (Đường dẫn)', 'kata-seo-manager'),
            'Carousel' => __('Carousel (Băng chuyền)', 'kata-seo-manager'),
            'Course' => __('Course (Khóa học)', 'kata-seo-manager'),
            'Dataset' => __('Dataset (Bộ dữ liệu)', 'kata-seo-manager'),
            'Forum' => __('Discussion Forum (Diễn đàn)', 'kata-seo-manager'),
            'EduQA' => __('Education Q&A (Hỏi đáp giáo dục)', 'kata-seo-manager'),
            'EmployerRating' => __('Employer Rating (Đánh giá nhà tuyển dụng)', 'kata-seo-manager'),
            'Event' => __('Event (Sự kiện)', 'kata-seo-manager'),
            'FAQ' => __('FAQ (Câu hỏi thường gặp)', 'kata-seo-manager'),
            'HowTo' => __('HowTo (Hướng dẫn)', 'kata-seo-manager'),
            'ImageMetadata' => __('Image Metadata (Siêu dữ liệu hình ảnh)', 'kata-seo-manager'),
            'JobPosting' => __('Job Posting (Tin tuyển dụng)', 'kata-seo-manager'),
            'LocalBusiness' => __('Local Business (Doanh nghiệp địa phương)', 'kata-seo-manager'),
            'MathSolver' => __('Math Solver (Giải toán)', 'kata-seo-manager'),
            'Movie' => __('Movie (Phim ảnh)', 'kata-seo-manager'),
            'Organization' => __('Organization (Tổ chức)', 'kata-seo-manager'),
            'PracticeProblem' => __('Practice Problem (Bài tập thực hành)', 'kata-seo-manager'),
            'Product' => __('Product (Sản phẩm)', 'kata-seo-manager'),
            'ProfilePage' => __('Profile Page (Trang hồ sơ)', 'kata-seo-manager'),
            'Recipe' => __('Recipe (Công thức nấu ăn)', 'kata-seo-manager'),
            'Review' => __('Review (Đánh giá)', 'kata-seo-manager'),
            'Sitelinks' => __('Sitelinks Searchbox (Hộp tìm kiếm)', 'kata-seo-manager'),
            'Speakable' => __('Speakable (Nội dung đọc to)', 'kata-seo-manager'),
            'Video' => __('Video (Video)', 'kata-seo-manager'),
            'WebPage' => __('WebPage (Trang web)', 'kata-seo-manager')
        );
    }
    
    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('kata_article', array($this, 'render_article'));
        add_shortcode('kata_breadcrumb', array($this, 'render_breadcrumb'));
        add_shortcode('kata_faq', array($this, 'render_faq'));
        add_shortcode('kata_faq_item', array($this, 'render_faq_item'));
        add_shortcode('kata_howto', array($this, 'render_howto'));
        add_shortcode('kata_event', array($this, 'render_event'));
        add_shortcode('kata_recipe', array($this, 'render_recipe'));
        add_shortcode('kata_product', array($this, 'render_product'));
        add_shortcode('kata_video', array($this, 'render_video'));
        add_shortcode('kata_quiz', array($this, 'render_quiz'));
        add_shortcode('kata_quiz_question', array($this, 'render_quiz_question'));
        add_shortcode('kata_poll', array($this, 'render_poll'));
        add_shortcode('kata_poll_option', array($this, 'render_poll_option'));
        add_shortcode('kata_form', array($this, 'render_form'));
        add_shortcode('kata_wheel', array($this, 'render_wheel'));
        add_shortcode('kata_rating', array($this, 'render_rating'));
        add_shortcode('kata_organization', array($this, 'render_organization'));
        add_shortcode('kata_localbusiness', array($this, 'render_localbusiness'));
        add_shortcode('kata_jobposting', array($this, 'render_jobposting'));
        // Additional schema shortcodes
        add_shortcode('kata_review', array($this, 'render_review'));
        add_shortcode('kata_movie', array($this, 'render_movie'));
        add_shortcode('kata_course', array($this, 'render_course'));
        add_shortcode('kata_software', array($this, 'render_software'));
        add_shortcode('kata_book', array($this, 'render_book'));
        add_shortcode('kata_webpage', array($this, 'render_webpage'));
        
        // Additional schema shortcodes for demo posts
        add_shortcode('kata_person', array($this, 'render_person'));
        add_shortcode('kata_service', array($this, 'render_service'));
        add_shortcode('kata_vehicle', array($this, 'render_vehicle'));
        add_shortcode('kata_realestate', array($this, 'render_realestate'));
        add_shortcode('kata_restaurant', array($this, 'render_restaurant'));
        add_shortcode('kata_medicalorganization', array($this, 'render_medicalorganization'));
        add_shortcode('kata_creativework', array($this, 'render_creativework'));
        add_shortcode('kata_videoobject', array($this, 'render_videoobject'));
        add_shortcode('kata_newsarticle', array($this, 'render_newsarticle'));
        add_shortcode('kata_blogposting', array($this, 'render_blogposting'));
        add_shortcode('kata_website', array($this, 'render_website'));
        add_shortcode('kata_breadcrumblist', array($this, 'render_breadcrumblist'));
        
        // New integrated user interaction shortcode
        add_shortcode('kata_user_interaction', array($this, 'render_user_interaction'));
        add_shortcode('kata_reviews', array($this, 'render_user_interaction')); // Alias
        add_shortcode('kata_comments', array($this, 'render_user_interaction')); // Alias
    }
    
    /**
     * Render shortcodes (examples)
     */
    public function render_article($atts) {
        $atts = shortcode_atts(array(
            'title' => '',
            'description' => '',
            'author' => '',
            'date_published' => '',
            'date_modified' => '',
            'image' => '',
            'url' => '',
            'type' => 'Article', // Article, NewsArticle, BlogPosting
            'category' => '',
            'tags' => '',
            'word_count' => '',
            'reading_time' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_article');
        
        if (empty($atts['title'])) {
            return '<div class="kata-article-error">Tiêu đề bài viết không được để trống.</div>';
        }
        
        // Use current post data if not provided
        global $post;
        if (empty($atts['author']) && $post) {
            $atts['author'] = get_the_author_meta('display_name', $post->post_author);
        }
        if (empty($atts['date_published']) && $post) {
            $atts['date_published'] = get_the_date('c', $post);
        }
        if (empty($atts['date_modified']) && $post) {
            $atts['date_modified'] = get_the_modified_date('c', $post);
        }
        if (empty($atts['url']) && $post) {
            $atts['url'] = get_permalink($post);
        }
        if (empty($atts['image']) && $post) {
            $thumbnail_id = get_post_thumbnail_id($post);
            if ($thumbnail_id) {
                $atts['image'] = wp_get_attachment_url($thumbnail_id);
            }
        }
        
        $article_id = 'kata-article-' . uniqid();
        
        // Generate schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $atts['type'],
            'headline' => $atts['title'],
            'name' => $atts['title']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['date_published'])) {
            $schema['datePublished'] = $atts['date_published'];
        }
        if (!empty($atts['date_modified'])) {
            $schema['dateModified'] = $atts['date_modified'];
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $atts['image']
            );
        }
        if (!empty($atts['word_count'])) {
            $schema['wordCount'] = intval($atts['word_count']);
        }
        
        // Add categories and tags
        if (!empty($atts['category'])) {
            $categories = array_map('trim', explode(',', $atts['category']));
            $schema['articleSection'] = $categories;
        }
        if (!empty($atts['tags'])) {
            $tags = array_map('trim', explode(',', $atts['tags']));
            $schema['keywords'] = implode(', ', $tags);
        }
        
        // Add publisher (website info)
        $schema['publisher'] = array(
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        );
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<article id="' . $article_id . '" class="kata-article-container">';
            $output .= '<header class="kata-article-header">';
            $output .= '<h1 class="kata-article-title">' . esc_html($atts['title']) . '</h1>';
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-article-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '<div class="kata-article-meta">';
            if (!empty($atts['author'])) {
                $output .= '<span class="kata-article-author">Tác giả: ' . esc_html($atts['author']) . '</span>';
            }
            if (!empty($atts['date_published'])) {
                $output .= '<span class="kata-article-date">Ngày đăng: ' . date('d/m/Y', strtotime($atts['date_published'])) . '</span>';
            }
            if (!empty($atts['reading_time'])) {
                $output .= '<span class="kata-article-reading-time">Thời gian đọc: ' . esc_html($atts['reading_time']) . '</span>';
            }
            $output .= '</div>';
            
            $output .= '</header>';
            
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-article-image">';
                $output .= '<img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['title']) . '" />';
                $output .= '</div>';
            }
            
            $output .= '</article>';
        }
        
        // Add JSON-LD Schema
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }
    
    public function render_breadcrumb($atts) {
        return '';
    }
    
    public function render_faq($atts, $content = null) {
        // Default attributes
        $atts = shortcode_atts(array(
            'style' => 'default',
            'theme' => 'light',
            'show_schema' => 'true'
        ), $atts, 'kata_faq');
        
        if (empty($content)) {
            return '<div class="kata-faq-empty">Không có câu hỏi FAQ nào được tìm thấy.</div>';
        }
        
        // Process nested kata_faq_item shortcodes
        global $kata_faq_items;
        $kata_faq_items = array();
        
        // Execute nested shortcodes to collect FAQ items
        do_shortcode($content);
        
        if (empty($kata_faq_items)) {
            return '<div class="kata-faq-empty">Không có câu hỏi FAQ nào được tìm thấy.</div>';
        }
        
        // Generate unique ID for this FAQ instance
        $faq_id = 'kata-faq-' . uniqid();
        
        // Start output
        $output = '<div id="' . $faq_id . '" class="kata-faq-container kata-faq-' . esc_attr($atts['style']) . ' kata-faq-theme-' . esc_attr($atts['theme']) . '" itemscope itemtype="https://schema.org/FAQPage">';
        
        $schema_items = array();
        $item_count = 0;
        
        foreach ($kata_faq_items as $item) {
            $item_count++;
            $item_id = $faq_id . '-item-' . $item_count;
            
            $output .= '<div class="kata-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">';
            $output .= '<div class="kata-faq-question" id="' . $item_id . '-q" itemprop="name" onclick="kataToggleFAQ(\'' . $item_id . '\')">';
            $output .= '<span class="kata-faq-icon">❓</span>';
            $output .= '<span class="kata-faq-text">' . esc_html($item['question']) . '</span>';
            $output .= '<span class="kata-faq-toggle">▼</span>';
            $output .= '</div>';
            $output .= '<div class="kata-faq-answer" id="' . $item_id . '-a" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">';
            $output .= '<div class="kata-faq-answer-content" itemprop="text">' . wpautop(esc_html($item['answer'])) . '</div>';
            $output .= '</div>';
            $output .= '</div>';
            
            // Build schema data
            if ($atts['show_schema'] === 'true') {
                $schema_items[] = array(
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => $item['answer']
                    )
                );
            }
        }
        
        $output .= '</div>';
        
        // Add JSON-LD Schema
        if (!empty($schema_items) && $atts['show_schema'] === 'true') {
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => $schema_items
            );
            
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        // Add CSS and JavaScript
        $output .= $this->get_faq_styles_and_scripts();
        
        // Clear the global variable
        $kata_faq_items = array();
        
        return $output;
    }
    
    public function render_faq_item($atts) {
        global $kata_faq_items;
        
        $atts = shortcode_atts(array(
            'question' => '',
            'answer' => ''
        ), $atts, 'kata_faq_item');
        
        if (empty($atts['question']) || empty($atts['answer'])) {
            return '';
        }
        
        // Store FAQ item data in global variable
        if (!isset($kata_faq_items)) {
            $kata_faq_items = array();
        }
        
        $kata_faq_items[] = array(
            'question' => $atts['question'],
            'answer' => $atts['answer']
        );
        
        return ''; // Don't output anything here, parent shortcode will handle rendering
    }
    
    private function get_faq_styles_and_scripts() {
        static $styles_loaded = false;
        
        if ($styles_loaded) {
            return '';
        }
        
        $styles_loaded = true;
        
        return '
        <style>
            .kata-faq-container {
                margin: 20px 0;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            .kata-faq-item {
                border: 1px solid #e1e5e9;
                border-radius: 8px;
                margin: 10px 0;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            .kata-faq-item:hover {
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            }
            .kata-faq-question {
                display: flex;
                align-items: center;
                background: #f8f9fa;
                color: #0073aa;
                margin: 0;
                padding: 15px 20px;
                cursor: pointer;
                border-bottom: 1px solid #e1e5e9;
                transition: background-color 0.3s ease;
                font-weight: 600;
            }
            .kata-faq-question:hover {
                background: #e9ecef;
            }
            .kata-faq-icon {
                margin-right: 10px;
                font-size: 16px;
            }
            .kata-faq-text {
                flex: 1;
            }
            .kata-faq-toggle {
                transition: transform 0.3s ease;
                font-size: 12px;
            }
            .kata-faq-answer {
                padding: 0;
                background: white;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease, padding 0.3s ease;
            }
            .kata-faq-answer.active {
                max-height: 500px;
                padding: 15px 20px;
            }
            .kata-faq-answer-content {
                line-height: 1.6;
                color: #333;
            }
            .kata-faq-answer-content p {
                margin: 0 0 10px 0;
            }
            .kata-faq-answer-content p:last-child {
                margin-bottom: 0;
            }
            .kata-faq-empty {
                text-align: center;
                padding: 30px 20px;
                color: #666;
                font-style: italic;
                background: #f8f9fa;
                border-radius: 8px;
                border: 1px dashed #ddd;
            }
            .kata-faq-item.active .kata-faq-toggle {
                transform: rotate(180deg);
            }
            
            /* Dark theme */
            .kata-faq-theme-dark .kata-faq-question {
                background: #2c3e50;
                color: #ecf0f1;
                border-bottom-color: #34495e;
            }
            .kata-faq-theme-dark .kata-faq-question:hover {
                background: #34495e;
            }
            .kata-faq-theme-dark .kata-faq-answer {
                background: #34495e;
            }
            .kata-faq-theme-dark .kata-faq-answer-content {
                color: #ecf0f1;
            }
            .kata-faq-theme-dark .kata-faq-item {
                border-color: #34495e;
            }
        </style>
        
        <script>
            function kataToggleFAQ(itemId) {
                var question = document.getElementById(itemId + \'-q\');
                var answer = document.getElementById(itemId + \'-a\');
                var item = question.closest(\'.kata-faq-item\');
                
                // Toggle active state
                item.classList.toggle(\'active\');
                answer.classList.toggle(\'active\');
                
                // Track FAQ interaction (optional analytics)
                if (typeof gtag !== \'undefined\') {
                    gtag(\'event\', \'faq_toggle\', {
                        \'event_category\': \'engagement\',
                        \'event_label\': question.querySelector(\'.kata-faq-text\').textContent
                    });
                }
            }
            
            // Auto-expand first FAQ (optional)
            document.addEventListener(\'DOMContentLoaded\', function() {
                var firstFAQ = document.querySelector(\'.kata-faq-container .kata-faq-item:first-child\');
                if (firstFAQ && window.kataFAQAutoExpand) {
                    var firstAnswer = firstFAQ.querySelector(\'.kata-faq-answer\');
                    firstFAQ.classList.add(\'active\');
                    firstAnswer.classList.add(\'active\');
                }
            });
        </script>';
    }
    
    public function render_howto($atts, $content = null) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'image' => '',
            'video' => '',
            'total_time' => '',
            'prep_time' => '',
            'perform_time' => '',
            'yield' => '',
            'cost' => '',
            'currency' => 'VND',
            'difficulty' => '',
            'category' => '',
            'keywords' => '',
            'tools' => '',
            'materials' => '',
            'steps' => '',
            'author' => '',
            'date_published' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_howto');
        
        if (empty($atts['name'])) {
            return '<div class="kata-howto-error">Tên hướng dẫn không được để trống.</div>';
        }
        
        $howto_id = 'kata-howto-' . uniqid();
        
        // Generate schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $atts['image']
            );
        }
        if (!empty($atts['video'])) {
            $schema['video'] = array(
                '@type' => 'VideoObject',
                'contentUrl' => $atts['video']
            );
        }
        
        // Time durations
        if (!empty($atts['total_time'])) {
            $schema['totalTime'] = 'PT' . $atts['total_time'];
        }
        if (!empty($atts['prep_time'])) {
            $schema['prepTime'] = 'PT' . $atts['prep_time'];
        }
        if (!empty($atts['perform_time'])) {
            $schema['performTime'] = 'PT' . $atts['perform_time'];
        }
        
        // Additional properties
        if (!empty($atts['yield'])) {
            $schema['yield'] = $atts['yield'];
        }
        if (!empty($atts['cost'])) {
            $schema['estimatedCost'] = array(
                '@type' => 'MonetaryAmount',
                'value' => $atts['cost'],
                'currency' => $atts['currency']
            );
        }
        if (!empty($atts['difficulty'])) {
            $schema['difficulty'] = $atts['difficulty'];
        }
        if (!empty($atts['category'])) {
            $schema['category'] = $atts['category'];
        }
        if (!empty($atts['keywords'])) {
            $schema['keywords'] = $atts['keywords'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['date_published'])) {
            $schema['datePublished'] = $atts['date_published'];
        }
        
        // Tools and materials
        if (!empty($atts['tools'])) {
            $tools = array_map('trim', explode('|', $atts['tools']));
            $tool_objects = array();
            foreach ($tools as $tool) {
                $tool_objects[] = array(
                    '@type' => 'HowToTool',
                    'name' => $tool
                );
            }
            $schema['tool'] = $tool_objects;
        }
        
        if (!empty($atts['materials'])) {
            $materials = array_map('trim', explode('|', $atts['materials']));
            $supply_objects = array();
            foreach ($materials as $material) {
                $supply_objects[] = array(
                    '@type' => 'HowToSupply',
                    'name' => $material
                );
            }
            $schema['supply'] = $supply_objects;
        }
        
        // Steps
        if (!empty($atts['steps'])) {
            $steps = array_map('trim', explode('|', $atts['steps']));
            $step_objects = array();
            foreach ($steps as $index => $step) {
                $step_objects[] = array(
                    '@type' => 'HowToStep',
                    'position' => $index + 1,
                    'name' => 'Bước ' . ($index + 1),
                    'text' => $step
                );
            }
            $schema['step'] = $step_objects;
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $howto_id . '" class="kata-howto-container">';
            
            // Header
            $output .= '<header class="kata-howto-header">';
            $output .= '<h2 class="kata-howto-title">' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-howto-description">' . esc_html($atts['description']) . '</p>';
            }
            
            // Meta info
            $output .= '<div class="kata-howto-meta">';
            if (!empty($atts['author'])) {
                $output .= '<span class="kata-howto-author">👤 ' . esc_html($atts['author']) . '</span>';
            }
            if (!empty($atts['total_time'])) {
                $output .= '<span class="kata-howto-time">⏱️ Tổng thời gian: ' . esc_html($atts['total_time']) . '</span>';
            }
            if (!empty($atts['difficulty'])) {
                $difficulty_icons = array(
                    'beginner' => '🟢 Dễ',
                    'intermediate' => '🟡 Trung bình', 
                    'advanced' => '🔴 Khó'
                );
                $difficulty_text = $difficulty_icons[strtolower($atts['difficulty'])] ?? '📊 ' . $atts['difficulty'];
                $output .= '<span class="kata-howto-difficulty">' . $difficulty_text . '</span>';
            }
            if (!empty($atts['cost'])) {
                $formatted_cost = number_format($atts['cost'], 0, ',', '.') . ' ' . $atts['currency'];
                $output .= '<span class="kata-howto-cost">💰 Chi phí: ' . $formatted_cost . '</span>';
            }
            if (!empty($atts['yield'])) {
                $output .= '<span class="kata-howto-yield">📦 Kết quả: ' . esc_html($atts['yield']) . '</span>';
            }
            $output .= '</div>';
            
            $output .= '</header>';
            
            // Image/Video
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-howto-media">';
                $output .= '<img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" />';
                $output .= '</div>';
            } elseif (!empty($atts['video'])) {
                $output .= '<div class="kata-howto-media">';
                $output .= '<video controls>';
                $output .= '<source src="' . esc_url($atts['video']) . '">';
                $output .= 'Trình duyệt không hỗ trợ video.';
                $output .= '</video>';
                $output .= '</div>';
            }
            
            // Time breakdown
            if (!empty($atts['prep_time']) || !empty($atts['perform_time'])) {
                $output .= '<div class="kata-howto-time-breakdown">';
                $output .= '<h3>⏰ Phân Bổ Thời Gian</h3>';
                if (!empty($atts['prep_time'])) {
                    $output .= '<div class="kata-time-item">Chuẩn bị: <strong>' . esc_html($atts['prep_time']) . '</strong></div>';
                }
                if (!empty($atts['perform_time'])) {
                    $output .= '<div class="kata-time-item">Thực hiện: <strong>' . esc_html($atts['perform_time']) . '</strong></div>';
                }
                $output .= '</div>';
            }
            
            // Tools
            if (!empty($atts['tools'])) {
                $tools = array_map('trim', explode('|', $atts['tools']));
                $output .= '<div class="kata-howto-tools">';
                $output .= '<h3>🔧 Công Cụ Cần Thiết</h3>';
                $output .= '<ul class="kata-tools-list">';
                foreach ($tools as $tool) {
                    $output .= '<li>' . esc_html($tool) . '</li>';
                }
                $output .= '</ul>';
                $output .= '</div>';
            }
            
            // Materials
            if (!empty($atts['materials'])) {
                $materials = array_map('trim', explode('|', $atts['materials']));
                $output .= '<div class="kata-howto-materials">';
                $output .= '<h3>📋 Vật Liệu</h3>';
                $output .= '<ul class="kata-materials-list">';
                foreach ($materials as $material) {
                    $output .= '<li>' . esc_html($material) . '</li>';
                }
                $output .= '</ul>';
                $output .= '</div>';
            }
            
            // Steps
            if (!empty($atts['steps'])) {
                $steps = array_map('trim', explode('|', $atts['steps']));
                $output .= '<div class="kata-howto-steps">';
                $output .= '<h3>📝 Hướng Dẫn Chi Tiết</h3>';
                $output .= '<ol class="kata-steps-list">';
                foreach ($steps as $index => $step) {
                    $output .= '<li class="kata-step-item">';
                    $output .= '<div class="kata-step-number">' . ($index + 1) . '</div>';
                    $output .= '<div class="kata-step-content">' . esc_html($step) . '</div>';
                    $output .= '</li>';
                }
                $output .= '</ol>';
                $output .= '</div>';
            }
            
            // Keywords/Tags
            if (!empty($atts['keywords'])) {
                $keywords = array_map('trim', explode(',', $atts['keywords']));
                $output .= '<div class="kata-howto-keywords">';
                $output .= '<h3>🏷️ Từ Khóa</h3>';
                $output .= '<div class="kata-keywords-list">';
                foreach ($keywords as $keyword) {
                    $output .= '<span class="kata-keyword-tag">' . esc_html($keyword) . '</span>';
                }
                $output .= '</div>';
                $output .= '</div>';
            }
            
            $output .= '</div>'; // Close howto-container
        }
        
        // Add JSON-LD Schema
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }
    
    public function render_event($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'start_date' => '',
            'end_date' => '',
            'start_time' => '',
            'end_time' => '',
            'location_name' => '',
            'location_address' => '',
            'location_city' => '',
            'location_country' => 'VN',
            'organizer_name' => '',
            'organizer_url' => '',
            'performer_name' => '',
            'performer_type' => 'Person', // Person, Organization, MusicGroup
            'image' => '',
            'url' => '',
            'event_status' => 'EventScheduled', // EventScheduled, EventCancelled, EventPostponed, EventRescheduled
            'event_attendance_mode' => 'OfflineEventAttendanceMode', // OfflineEventAttendanceMode, OnlineEventAttendanceMode, MixedEventAttendanceMode
            'price' => '',
            'currency' => 'VND',
            'availability' => 'InStock',
            'category' => '',
            'audience' => '',
            'language' => 'vi',
            'duration' => '',
            'max_attendance' => '',
            'remaining_attendance' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_event');
        
        if (empty($atts['name']) || empty($atts['start_date'])) {
            return '<div class="kata-event-error">Tên sự kiện và ngày bắt đầu không được để trống.</div>';
        }
        
        $event_id = 'kata-event-' . uniqid();
        
        // Generate schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $atts['image']
            );
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        
        // Event dates
        $start_datetime = $atts['start_date'];
        if (!empty($atts['start_time'])) {
            $start_datetime .= 'T' . $atts['start_time'];
        }
        $schema['startDate'] = $start_datetime;
        
        if (!empty($atts['end_date'])) {
            $end_datetime = $atts['end_date'];
            if (!empty($atts['end_time'])) {
                $end_datetime .= 'T' . $atts['end_time'];
            }
            $schema['endDate'] = $end_datetime;
        }
        
        // Location
        if (!empty($atts['location_name']) || !empty($atts['location_address'])) {
            $location = array('@type' => 'Place');
            
            if (!empty($atts['location_name'])) {
                $location['name'] = $atts['location_name'];
            }
            
            if (!empty($atts['location_address'])) {
                $address = array('@type' => 'PostalAddress');
                $address['streetAddress'] = $atts['location_address'];
                
                if (!empty($atts['location_city'])) {
                    $address['addressLocality'] = $atts['location_city'];
                }
                if (!empty($atts['location_country'])) {
                    $address['addressCountry'] = $atts['location_country'];
                }
                
                $location['address'] = $address;
            }
            
            $schema['location'] = $location;
        }
        
        // Organizer
        if (!empty($atts['organizer_name'])) {
            $organizer = array(
                '@type' => 'Organization',
                'name' => $atts['organizer_name']
            );
            
            if (!empty($atts['organizer_url'])) {
                $organizer['url'] = $atts['organizer_url'];
            }
            
            $schema['organizer'] = $organizer;
        }
        
        // Performer
        if (!empty($atts['performer_name'])) {
            $performer = array(
                '@type' => $atts['performer_type'],
                'name' => $atts['performer_name']
            );
            
            $schema['performer'] = $performer;
        }
        
        // Event status and attendance mode
        $schema['eventStatus'] = 'https://schema.org/' . $atts['event_status'];
        $schema['eventAttendanceMode'] = 'https://schema.org/' . $atts['event_attendance_mode'];
        
        // Offers (ticket price)
        if (!empty($atts['price'])) {
            $offer = array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency'],
                'availability' => 'https://schema.org/' . $atts['availability']
            );
            
            if (!empty($atts['url'])) {
                $offer['url'] = $atts['url'];
            }
            
            $schema['offers'] = $offer;
        }
        
        // Additional properties
        if (!empty($atts['category'])) {
            $schema['category'] = $atts['category'];
        }
        if (!empty($atts['audience'])) {
            $schema['audience'] = array(
                '@type' => 'Audience',
                'audienceType' => $atts['audience']
            );
        }
        if (!empty($atts['language'])) {
            $schema['inLanguage'] = $atts['language'];
        }
        if (!empty($atts['duration'])) {
            $schema['duration'] = 'PT' . $atts['duration'];
        }
        if (!empty($atts['max_attendance'])) {
            $schema['maximumAttendeeCapacity'] = intval($atts['max_attendance']);
        }
        if (!empty($atts['remaining_attendance'])) {
            $schema['remainingAttendeeCapacity'] = intval($atts['remaining_attendance']);
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $event_id . '" class="kata-event-container">';
            
            // Event header
            $output .= '<header class="kata-event-header">';
            $output .= '<h2 class="kata-event-title">' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-event-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '</header>';
            
            // Event image
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-event-image">';
                $output .= '<img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" />';
                $output .= '</div>';
            }
            
            // Event details
            $output .= '<div class="kata-event-details">';
            
            // Date and time
            $output .= '<div class="kata-event-datetime">';
            $output .= '<h3>📅 Thời Gian</h3>';
            $formatted_start = date('d/m/Y', strtotime($atts['start_date']));
            if (!empty($atts['start_time'])) {
                $formatted_start .= ' lúc ' . $atts['start_time'];
            }
            $output .= '<div class="kata-datetime-item">Bắt đầu: <strong>' . $formatted_start . '</strong></div>';
            
            if (!empty($atts['end_date'])) {
                $formatted_end = date('d/m/Y', strtotime($atts['end_date']));
                if (!empty($atts['end_time'])) {
                    $formatted_end .= ' lúc ' . $atts['end_time'];
                }
                $output .= '<div class="kata-datetime-item">Kết thúc: <strong>' . $formatted_end . '</strong></div>';
            }
            
            if (!empty($atts['duration'])) {
                $output .= '<div class="kata-datetime-item">Thời lượng: <strong>' . esc_html($atts['duration']) . '</strong></div>';
            }
            $output .= '</div>';
            
            // Location
            if (!empty($atts['location_name']) || !empty($atts['location_address'])) {
                $output .= '<div class="kata-event-location">';
                $output .= '<h3>📍 Địa Điểm</h3>';
                
                if (!empty($atts['location_name'])) {
                    $output .= '<div class="kata-location-name"><strong>' . esc_html($atts['location_name']) . '</strong></div>';
                }
                if (!empty($atts['location_address'])) {
                    $output .= '<div class="kata-location-address">' . esc_html($atts['location_address']);
                    if (!empty($atts['location_city'])) {
                        $output .= ', ' . esc_html($atts['location_city']);
                    }
                    $output .= '</div>';
                }
                $output .= '</div>';
            }
            
            // Organizer and Performer
            if (!empty($atts['organizer_name']) || !empty($atts['performer_name'])) {
                $output .= '<div class="kata-event-people">';
                $output .= '<h3>👥 Người Tổ Chức & Diễn Giả</h3>';
                
                if (!empty($atts['organizer_name'])) {
                    $output .= '<div class="kata-organizer">Tổ chức: <strong>' . esc_html($atts['organizer_name']) . '</strong></div>';
                }
                if (!empty($atts['performer_name'])) {
                    $performer_label = ($atts['performer_type'] === 'MusicGroup') ? 'Ban nhạc' : 
                                      (($atts['performer_type'] === 'Organization') ? 'Tổ chức trình diễn' : 'Diễn giả');
                    $output .= '<div class="kata-performer">' . $performer_label . ': <strong>' . esc_html($atts['performer_name']) . '</strong></div>';
                }
                $output .= '</div>';
            }
            
            // Event info
            $output .= '<div class="kata-event-info">';
            $output .= '<h3>ℹ️ Thông Tin Chi Tiết</h3>';
            
            // Status
            $status_text = array(
                'EventScheduled' => '✅ Đã lên lịch',
                'EventCancelled' => '❌ Đã hủy',
                'EventPostponed' => '⏸️ Tạm hoãn',
                'EventRescheduled' => '🔄 Đổi lịch'
            );
            $output .= '<div class="kata-info-item">Trạng thái: <span>' . ($status_text[$atts['event_status']] ?? $atts['event_status']) . '</span></div>';
            
            // Attendance mode
            $attendance_text = array(
                'OfflineEventAttendanceMode' => '🏢 Trực tiếp',
                'OnlineEventAttendanceMode' => '💻 Trực tuyến',
                'MixedEventAttendanceMode' => '🔀 Kết hợp'
            );
            $output .= '<div class="kata-info-item">Hình thức: <span>' . ($attendance_text[$atts['event_attendance_mode']] ?? $atts['event_attendance_mode']) . '</span></div>';
            
            if (!empty($atts['category'])) {
                $output .= '<div class="kata-info-item">Thể loại: <span>' . esc_html($atts['category']) . '</span></div>';
            }
            if (!empty($atts['audience'])) {
                $output .= '<div class="kata-info-item">Đối tượng: <span>' . esc_html($atts['audience']) . '</span></div>';
            }
            if (!empty($atts['max_attendance'])) {
                $output .= '<div class="kata-info-item">Sức chứa: <span>' . esc_html($atts['max_attendance']) . ' người</span></div>';
            }
            if (!empty($atts['remaining_attendance'])) {
                $output .= '<div class="kata-info-item">Còn lại: <span>' . esc_html($atts['remaining_attendance']) . ' chỗ</span></div>';
            }
            
            $output .= '</div>';
            
            // Ticket price
            if (!empty($atts['price'])) {
                $formatted_price = ($atts['price'] === '0' || $atts['price'] === 'free') ? 'Miễn phí' : 
                                  number_format($atts['price'], 0, ',', '.') . ' ' . $atts['currency'];
                
                $output .= '<div class="kata-event-price">';
                $output .= '<h3>🎫 Vé Tham Dự</h3>';
                $output .= '<div class="kata-price-amount">' . $formatted_price . '</div>';
                $output .= '</div>';
            }
            
            // Register button
            if (!empty($atts['url'])) {
                $output .= '<div class="kata-event-actions">';
                $output .= '<a href="' . esc_url($atts['url']) . '" class="kata-register-button" target="_blank">🎫 Đăng Ký Tham Dự</a>';
                $output .= '</div>';
            }
            
            $output .= '</div>'; // Close event-details
            $output .= '</div>'; // Close event-container
        }
        
        // Add JSON-LD Schema
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }
    
    public function render_recipe($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'image' => '',
            'author' => '',
            'prep_time' => '',
            'cook_time' => '',
            'total_time' => '',
            'yield' => '',
            'category' => '',
            'cuisine' => '',
            'difficulty' => '',
            'ingredients' => '',
            'instructions' => '',
            'nutrition_calories' => '',
            'nutrition_fat' => '',
            'nutrition_protein' => '',
            'nutrition_carbs' => '',
            'rating_value' => '',
            'rating_count' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_recipe');
        
        if (empty($atts['name'])) {
            return '<div class="kata-recipe-error">Tên công thức không được để trống.</div>';
        }
        
        $recipe_id = 'kata-recipe-' . uniqid();
        
        // Generate schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $atts['image']
            );
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['prep_time'])) {
            $schema['prepTime'] = 'PT' . $atts['prep_time'];
        }
        if (!empty($atts['cook_time'])) {
            $schema['cookTime'] = 'PT' . $atts['cook_time'];
        }
        if (!empty($atts['total_time'])) {
            $schema['totalTime'] = 'PT' . $atts['total_time'];
        }
        if (!empty($atts['yield'])) {
            $schema['recipeYield'] = $atts['yield'];
        }
        if (!empty($atts['category'])) {
            $schema['recipeCategory'] = $atts['category'];
        }
        if (!empty($atts['cuisine'])) {
            $schema['recipeCuisine'] = $atts['cuisine'];
        }
        
        // Add ingredients
        if (!empty($atts['ingredients'])) {
            $ingredients = array_map('trim', explode('|', $atts['ingredients']));
            $schema['recipeIngredient'] = $ingredients;
        }
        
        // Add instructions
        if (!empty($atts['instructions'])) {
            $instructions = array_map('trim', explode('|', $atts['instructions']));
            $instruction_objects = array();
            foreach ($instructions as $index => $instruction) {
                $instruction_objects[] = array(
                    '@type' => 'HowToStep',
                    'position' => $index + 1,
                    'text' => $instruction
                );
            }
            $schema['recipeInstructions'] = $instruction_objects;
        }
        
        // Add nutrition info
        if (!empty($atts['nutrition_calories']) || !empty($atts['nutrition_fat']) || 
            !empty($atts['nutrition_protein']) || !empty($atts['nutrition_carbs'])) {
            
            $nutrition = array('@type' => 'NutritionInformation');
            if (!empty($atts['nutrition_calories'])) {
                $nutrition['calories'] = $atts['nutrition_calories'];
            }
            if (!empty($atts['nutrition_fat'])) {
                $nutrition['fatContent'] = $atts['nutrition_fat'] . 'g';
            }
            if (!empty($atts['nutrition_protein'])) {
                $nutrition['proteinContent'] = $atts['nutrition_protein'] . 'g';
            }
            if (!empty($atts['nutrition_carbs'])) {
                $nutrition['carbohydrateContent'] = $atts['nutrition_carbs'] . 'g';
            }
            $schema['nutrition'] = $nutrition;
        }
        
        // Add rating
        if (!empty($atts['rating_value']) && !empty($atts['rating_count'])) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $recipe_id . '" class="kata-recipe-container">';
            
            // Header
            $output .= '<header class="kata-recipe-header">';
            $output .= '<h2 class="kata-recipe-title">' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-recipe-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '<div class="kata-recipe-meta">';
            if (!empty($atts['author'])) {
                $output .= '<span class="kata-recipe-author">👨‍🍳 ' . esc_html($atts['author']) . '</span>';
            }
            if (!empty($atts['prep_time'])) {
                $output .= '<span class="kata-recipe-prep-time">⏱️ Chuẩn bị: ' . esc_html($atts['prep_time']) . '</span>';
            }
            if (!empty($atts['cook_time'])) {
                $output .= '<span class="kata-recipe-cook-time">🍳 Nấu: ' . esc_html($atts['cook_time']) . '</span>';
            }
            if (!empty($atts['yield'])) {
                $output .= '<span class="kata-recipe-yield">👥 Khẩu phần: ' . esc_html($atts['yield']) . '</span>';
            }
            if (!empty($atts['difficulty'])) {
                $output .= '<span class="kata-recipe-difficulty">📊 Độ khó: ' . esc_html($atts['difficulty']) . '</span>';
            }
            $output .= '</div>';
            
            $output .= '</header>';
            
            // Image
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-recipe-image">';
                $output .= '<img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" />';
                $output .= '</div>';
            }
            
            // Ingredients
            if (!empty($atts['ingredients'])) {
                $ingredients = array_map('trim', explode('|', $atts['ingredients']));
                $output .= '<div class="kata-recipe-ingredients">';
                $output .= '<h3>🛒 Nguyên Liệu</h3>';
                $output .= '<ul>';
                foreach ($ingredients as $ingredient) {
                    $output .= '<li>' . esc_html($ingredient) . '</li>';
                }
                $output .= '</ul>';
                $output .= '</div>';
            }
            
            // Instructions
            if (!empty($atts['instructions'])) {
                $instructions = array_map('trim', explode('|', $atts['instructions']));
                $output .= '<div class="kata-recipe-instructions">';
                $output .= '<h3>📝 Cách Làm</h3>';
                $output .= '<ol>';
                foreach ($instructions as $instruction) {
                    $output .= '<li>' . esc_html($instruction) . '</li>';
                }
                $output .= '</ol>';
                $output .= '</div>';
            }
            
            // Nutrition
            if (!empty($atts['nutrition_calories'])) {
                $output .= '<div class="kata-recipe-nutrition">';
                $output .= '<h3>📊 Thông Tin Dinh Dưỡng</h3>';
                $output .= '<div class="kata-nutrition-grid">';
                if (!empty($atts['nutrition_calories'])) {
                    $output .= '<div class="kata-nutrition-item">Calo: ' . esc_html($atts['nutrition_calories']) . '</div>';
                }
                if (!empty($atts['nutrition_fat'])) {
                    $output .= '<div class="kata-nutrition-item">Chất béo: ' . esc_html($atts['nutrition_fat']) . 'g</div>';
                }
                if (!empty($atts['nutrition_protein'])) {
                    $output .= '<div class="kata-nutrition-item">Protein: ' . esc_html($atts['nutrition_protein']) . 'g</div>';
                }
                if (!empty($atts['nutrition_carbs'])) {
                    $output .= '<div class="kata-nutrition-item">Carbs: ' . esc_html($atts['nutrition_carbs']) . 'g</div>';
                }
                $output .= '</div>';
                $output .= '</div>';
            }
            
            // Rating
            if (!empty($atts['rating_value'])) {
                $rating_stars = str_repeat('⭐', round(floatval($atts['rating_value'])));
                $output .= '<div class="kata-recipe-rating">';
                $output .= '<span class="kata-rating-stars">' . $rating_stars . '</span>';
                $output .= '<span class="kata-rating-text"> ' . esc_html($atts['rating_value']) . '/5';
                if (!empty($atts['rating_count'])) {
                    $output .= ' (' . esc_html($atts['rating_count']) . ' đánh giá)';
                }
                $output .= '</span>';
                $output .= '</div>';
            }
            
            $output .= '</div>';
        }
        
        // Add JSON-LD Schema
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }
    
    public function render_product($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'image' => '',
            'brand' => '',
            'model' => '',
            'sku' => '',
            'price' => '',
            'currency' => 'VND',
            'availability' => 'InStock', // InStock, OutOfStock, PreOrder
            'condition' => 'NewCondition', // NewCondition, UsedCondition, RefurbishedCondition
            'category' => '',
            'rating_value' => '',
            'rating_count' => '',
            'review_count' => '',
            'url' => '',
            'offers_valid_until' => '',
            'offers_price_valid_until' => '',
            'gtin' => '',
            'mpn' => '',
            'weight' => '',
            'dimensions' => '',
            'color' => '',
            'size' => '',
            'material' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_product');
        
        if (empty($atts['name'])) {
            return '<div class="kata-product-error">Tên sản phẩm không được để trống.</div>';
        }
        
        $product_id = 'kata-product-' . uniqid();
        
        // Generate schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $atts['image']
            );
        }
        if (!empty($atts['brand'])) {
            $schema['brand'] = array(
                '@type' => 'Brand',
                'name' => $atts['brand']
            );
        }
        if (!empty($atts['model'])) {
            $schema['model'] = $atts['model'];
        }
        if (!empty($atts['sku'])) {
            $schema['sku'] = $atts['sku'];
        }
        if (!empty($atts['gtin'])) {
            $schema['gtin'] = $atts['gtin'];
        }
        if (!empty($atts['mpn'])) {
            $schema['mpn'] = $atts['mpn'];
        }
        if (!empty($atts['category'])) {
            $schema['category'] = $atts['category'];
        }
        if (!empty($atts['color'])) {
            $schema['color'] = $atts['color'];
        }
        if (!empty($atts['size'])) {
            $schema['size'] = $atts['size'];
        }
        if (!empty($atts['material'])) {
            $schema['material'] = $atts['material'];
        }
        if (!empty($atts['weight'])) {
            $schema['weight'] = array(
                '@type' => 'QuantitativeValue',
                'value' => $atts['weight']
            );
        }
        
        // Add offers (price info)
        if (!empty($atts['price'])) {
            $offer = array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency'],
                'availability' => 'https://schema.org/' . $atts['availability'],
                'itemCondition' => 'https://schema.org/' . $atts['condition']
            );
            
            if (!empty($atts['url'])) {
                $offer['url'] = $atts['url'];
            }
            if (!empty($atts['offers_valid_until'])) {
                $offer['priceValidUntil'] = $atts['offers_valid_until'];
            }
            
            $schema['offers'] = $offer;
        }
        
        // Add rating
        if (!empty($atts['rating_value']) && !empty($atts['rating_count'])) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
            
            if (!empty($atts['review_count'])) {
                $schema['aggregateRating']['reviewCount'] = $atts['review_count'];
            }
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $product_id . '" class="kata-product-container">';
            
            // Product header
            $output .= '<div class="kata-product-main">';
            
            // Image
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-product-image">';
                $output .= '<img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" />';
                $output .= '</div>';
            }
            
            // Product info
            $output .= '<div class="kata-product-info">';
            $output .= '<h2 class="kata-product-title">' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['brand'])) {
                $output .= '<div class="kata-product-brand">Thương hiệu: <strong>' . esc_html($atts['brand']) . '</strong></div>';
            }
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-product-description">' . esc_html($atts['description']) . '</p>';
            }
            
            // Price
            if (!empty($atts['price'])) {
                $formatted_price = number_format($atts['price'], 0, ',', '.') . ' ' . $atts['currency'];
                $output .= '<div class="kata-product-price">';
                $output .= '<span class="kata-price-amount">' . $formatted_price . '</span>';
                $output .= '</div>';
            }
            
            // Availability
            $availability_text = array(
                'InStock' => '✅ Còn hàng',
                'OutOfStock' => '❌ Hết hàng',
                'PreOrder' => '📅 Đặt trước'
            );
            $output .= '<div class="kata-product-availability">';
            $output .= '<span class="kata-availability-text">' . ($availability_text[$atts['availability']] ?? $atts['availability']) . '</span>';
            $output .= '</div>';
            
            // Product details
            $output .= '<div class="kata-product-details">';
            $output .= '<h3>Chi Tiết Sản Phẩm</h3>';
            
            if (!empty($atts['sku'])) {
                $output .= '<div class="kata-detail-item">SKU: <span>' . esc_html($atts['sku']) . '</span></div>';
            }
            if (!empty($atts['model'])) {
                $output .= '<div class="kata-detail-item">Model: <span>' . esc_html($atts['model']) . '</span></div>';
            }
            if (!empty($atts['category'])) {
                $output .= '<div class="kata-detail-item">Danh mục: <span>' . esc_html($atts['category']) . '</span></div>';
            }
            if (!empty($atts['color'])) {
                $output .= '<div class="kata-detail-item">Màu sắc: <span>' . esc_html($atts['color']) . '</span></div>';
            }
            if (!empty($atts['size'])) {
                $output .= '<div class="kata-detail-item">Kích thước: <span>' . esc_html($atts['size']) . '</span></div>';
            }
            if (!empty($atts['material'])) {
                $output .= '<div class="kata-detail-item">Chất liệu: <span>' . esc_html($atts['material']) . '</span></div>';
            }
            if (!empty($atts['weight'])) {
                $output .= '<div class="kata-detail-item">Trọng lượng: <span>' . esc_html($atts['weight']) . '</span></div>';
            }
            if (!empty($atts['condition'])) {
                $condition_text = array(
                    'NewCondition' => 'Mới',
                    'UsedCondition' => 'Đã sử dụng',
                    'RefurbishedCondition' => 'Tân trang'
                );
                $output .= '<div class="kata-detail-item">Tình trạng: <span>' . ($condition_text[$atts['condition']] ?? $atts['condition']) . '</span></div>';
            }
            
            $output .= '</div>';
            
            // Rating
            if (!empty($atts['rating_value'])) {
                $rating_stars = str_repeat('⭐', round(floatval($atts['rating_value'])));
                $output .= '<div class="kata-product-rating">';
                $output .= '<span class="kata-rating-stars">' . $rating_stars . '</span>';
                $output .= '<span class="kata-rating-text"> ' . esc_html($atts['rating_value']) . '/5';
                if (!empty($atts['rating_count'])) {
                    $output .= ' (' . esc_html($atts['rating_count']) . ' đánh giá)';
                }
                $output .= '</span>';
                $output .= '</div>';
            }
            
            // Buy button
            if (!empty($atts['url'])) {
                $output .= '<div class="kata-product-actions">';
                $output .= '<a href="' . esc_url($atts['url']) . '" class="kata-buy-button" target="_blank">🛒 Mua Ngay</a>';
                $output .= '</div>';
            }
            
            $output .= '</div>'; // Close product-info
            $output .= '</div>'; // Close product-main
            $output .= '</div>'; // Close product-container
        }
        
        // Add JSON-LD Schema
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }
    
    public function render_video($atts) {
        $atts = shortcode_atts(array(
            'title' => '',
            'description' => '',
            'url' => '',
            'thumbnail' => '',
            'duration' => '',
            'upload_date' => ''
        ), $atts, 'kata_video');
        
        if (empty($atts['title']) || empty($atts['url'])) {
            return '<div class="kata-video-error">Video title và URL không được để trống.</div>';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $atts['title'],
            'contentUrl' => $atts['url']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['thumbnail'])) {
            $schema['thumbnailUrl'] = $atts['thumbnail'];
        }
        if (!empty($atts['duration'])) {
            $schema['duration'] = $atts['duration'];
        }
        if (!empty($atts['upload_date'])) {
            $schema['uploadDate'] = $atts['upload_date'];
        }
        
        $output = '<div class="kata-video-container">';
        $output .= '<h3>' . esc_html($atts['title']) . '</h3>';
        if (!empty($atts['description'])) {
            $output .= '<p>' . esc_html($atts['description']) . '</p>';
        }
        $output .= '<video controls>';
        $output .= '<source src="' . esc_url($atts['url']) . '">';
        $output .= 'Trình duyệt của bạn không hỗ trợ video.';
        $output .= '</video>';
        $output .= '</div>';
        
        $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        
        return $output;
    }

    public function render_quiz($atts, $content = null) {
        $atts = shortcode_atts(array(
            'id' => '',
            'title' => 'Quiz',
            'description' => '',
            'style' => 'default',
            'show_results' => 'true'
        ), $atts, 'kata_quiz');
        
        global $kata_quiz_questions;
        $kata_quiz_questions = array();
        
        // If ID is provided, load quiz from database
        if (!empty($atts['id'])) {
            global $wpdb;
            $quiz_id = intval($atts['id']);
            $quiz = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}kata_seo_quizzes WHERE id = %d",
                $quiz_id
            ));
            
            if (!$quiz) {
                return '<div class="kata-quiz-empty">Quiz với ID "' . esc_html($quiz_id) . '" không tồn tại.</div>';
            }
            
            // Override attributes with database values
            $atts['title'] = $quiz->quiz_title;
            if (!empty($quiz->quiz_data)) {
                $quiz_data = json_decode($quiz->quiz_data, true);
                if ($quiz_data && isset($quiz_data['questions'])) {
                    // Convert database format to JavaScript expected format
                    $kata_quiz_questions = array();
                    foreach ($quiz_data['questions'] as $question) {
                        $kata_quiz_questions[] = array(
                            'question' => $question['question'],
                            'options' => $question['options'],
                            'correct' => isset($question['correct_answer']) ? $question['correct_answer'] : 0,
                            'explanation' => isset($question['explanation']) ? $question['explanation'] : ''
                        );
                    }
                }
            }
        } else {
            // Legacy content-based quiz
            if (empty($content)) {
                return '<div class="kata-quiz-empty">Không có câu hỏi quiz nào được tìm thấy.</div>';
            }
            
            do_shortcode($content);
        }
        
        if (empty($kata_quiz_questions)) {
            return '<div class="kata-quiz-empty">Không có câu hỏi quiz nào được tìm thấy.</div>';
        }
        
        $quiz_id = 'kata-quiz-' . uniqid();
        
        $output = '<div id="' . $quiz_id . '" class="kata-quiz-container kata-quiz-' . esc_attr($atts['style']) . '">';
        $output .= '<h3 class="kata-quiz-title">' . esc_html($atts['title']) . '</h3>';
        
        if (!empty($atts['description'])) {
            $output .= '<p class="kata-quiz-description">' . esc_html($atts['description']) . '</p>';
        }
        
        $output .= '<form class="kata-quiz-form">';
        foreach ($kata_quiz_questions as $index => $question) {
            $output .= '<div class="kata-quiz-question" data-question="' . $index . '">';
            $output .= '<h4>' . esc_html($question['question']) . '</h4>';
            
            if (!empty($question['options'])) {
                foreach ($question['options'] as $opt_index => $option) {
                    $input_id = $quiz_id . '-q' . $index . '-opt' . $opt_index;
                    $output .= '<label for="' . $input_id . '">';
                    $output .= '<input type="radio" id="' . $input_id . '" name="question_' . $index . '" value="' . $opt_index . '">';
                    $output .= esc_html($option);
                    $output .= '</label>';
                }
            }
            $output .= '</div>';
        }
        
        if ($atts['show_results'] === 'true') {
            $output .= '<button type="button" class="kata-quiz-submit" onclick="kataSubmitQuiz(\'' . $quiz_id . '\')">Xem Kết Quả</button>';
            $output .= '<div class="kata-quiz-results" style="display:none;"></div>';
        }
        
        $output .= '</form>';
        
        // Add JavaScript data for quiz scoring
        $js_var_name = str_replace('-', '_', $quiz_id) . '_questions';
        $quiz_data_js = json_encode($kata_quiz_questions, JSON_HEX_APOS | JSON_HEX_QUOT);
        $output .= '<script type="text/javascript">';
        $output .= 'window.' . $js_var_name . ' = ' . $quiz_data_js . ';';
        $output .= '</script>';
        
        $output .= '</div>';
        
        $kata_quiz_questions = array();
        
        return $output;
    }

    public function render_quiz_question($atts) {
        global $kata_quiz_questions;
        
        $atts = shortcode_atts(array(
            'question' => '',
            'correct' => '0',
            'option1' => '',
            'option2' => '',
            'option3' => '',
            'option4' => ''
        ), $atts, 'kata_quiz_question');
        
        if (empty($atts['question'])) {
            return '';
        }
        
        if (!isset($kata_quiz_questions)) {
            $kata_quiz_questions = array();
        }
        
        $options = array();
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($atts['option' . $i])) {
                $options[] = $atts['option' . $i];
            }
        }
        
        $kata_quiz_questions[] = array(
            'question' => $atts['question'],
            'options' => $options,
            'correct' => intval($atts['correct'])
        );
        
        return '';
    }

    public function render_poll($atts, $content = null) {
        $atts = shortcode_atts(array(
            'id' => '',
            'title' => 'Bình Chọn',
            'description' => '',
            'style' => 'default',
            'show_results' => 'false'
        ), $atts, 'kata_poll');
        
        // If poll ID is provided, get from database
        if (!empty($atts['id'])) {
            global $wpdb;
            $poll = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}kata_polls WHERE id = %d",
                $atts['id']
            ));
            
            if (!$poll) {
                return '<div class="kata-poll-empty">Không tìm thấy cuộc bình chọn.</div>';
            }
            
            $options = !empty($poll->poll_options) ? json_decode($poll->poll_options, true) : array();
            $poll_id = 'kata-poll-' . $poll->id;
            
            // Get current vote counts
            $vote_counts = $wpdb->get_results($wpdb->prepare(
                "SELECT option_index, COUNT(*) as count FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d GROUP BY option_index",
                $poll->id
            ), OBJECT_K);
            
            $total_votes = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d",
                $poll->id
            ));
            
            $output = '<div id="' . $poll_id . '" class="kata-poll-container kata-poll-' . esc_attr($atts['style']) . '" data-poll-id="' . $poll->id . '">';
            $output .= '<h3 class="kata-poll-title">' . esc_html($poll->poll_title) . '</h3>';
            
            if (!empty($poll->poll_description)) {
                $output .= '<p class="kata-poll-description">' . esc_html($poll->poll_description) . '</p>';
            }
            
            // Check if user has already voted
            $user_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
            $user_id = get_current_user_id();
            
            $has_voted = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d AND (voter_user_id = %d OR voter_ip = %s)",
                $poll->id, $user_id, $user_ip
            ));
            
            if ($has_voted > 0 || $atts['show_results'] === 'true') {
                // Show results
                $output .= '<div class="kata-poll-results">';
                foreach ($options as $index => $option) {
                    $votes = isset($vote_counts[$index]) ? $vote_counts[$index]->count : 0;
                    $percentage = $total_votes > 0 ? round(($votes / $total_votes) * 100, 1) : 0;
                    
                    $output .= '<div class="kata-poll-result-item">';
                    $output .= '<span class="kata-poll-option-text">' . esc_html($option) . '</span>';
                    $output .= '<span class="kata-poll-votes">(' . $votes . ' phiếu)</span>';
                    $output .= '<div class="kata-poll-progress-bar">';
                    $output .= '<div class="kata-poll-progress" style="width: ' . $percentage . '%"></div>';
                    $output .= '</div>';
                    $output .= '<span class="kata-poll-percentage">' . $percentage . '%</span>';
                    $output .= '</div>';
                }
                $output .= '<p class="kata-poll-total">Tổng số phiếu: ' . $total_votes . '</p>';
                $output .= '</div>';
            } else {
                // Show voting form
                $output .= '<form class="kata-poll-form">';
                foreach ($options as $index => $option) {
                    $input_id = $poll_id . '-opt' . $index;
                    $output .= '<label for="' . $input_id . '" class="kata-poll-option">';
                    $output .= '<input type="radio" id="' . $input_id . '" name="poll_option" value="' . $index . '">';
                    $output .= '<span>' . esc_html($option) . '</span>';
                    $output .= '</label>';
                }
                
                $output .= '<button type="button" class="kata-poll-submit" onclick="kataSubmitPoll(\'' . $poll_id . '\')">Bình Chọn</button>';
                $output .= '<div class="kata-poll-results" style="display:none;"></div>';
                $output .= '</form>';
            }
            
            $output .= '</div>';
            
            return $output;
        }
        
        // Original shortcode-based poll
        if (empty($content)) {
            return '<div class="kata-poll-empty">Không có tùy chọn bình chọn nào được tìm thấy.</div>';
        }
        
        global $kata_poll_options;
        $kata_poll_options = array();
        
        do_shortcode($content);
        
        if (empty($kata_poll_options)) {
            return '<div class="kata-poll-empty">Không có tùy chọn bình chọn nào được tìm thấy.</div>';
        }
        
        $poll_id = 'kata-poll-' . uniqid();
        
        $output = '<div id="' . $poll_id . '" class="kata-poll-container kata-poll-' . esc_attr($atts['style']) . '">';
        $output .= '<h3 class="kata-poll-title">' . esc_html($atts['title']) . '</h3>';
        
        if (!empty($atts['description'])) {
            $output .= '<p class="kata-poll-description">' . esc_html($atts['description']) . '</p>';
        }
        
        $output .= '<form class="kata-poll-form">';
        foreach ($kata_poll_options as $index => $option) {
            $input_id = $poll_id . '-opt' . $index;
            $output .= '<label for="' . $input_id . '" class="kata-poll-option">';
            $output .= '<input type="radio" id="' . $input_id . '" name="poll_option" value="' . $index . '">';
            $output .= '<span>' . esc_html($option) . '</span>';
            $output .= '</label>';
        }
        
        $output .= '<button type="button" class="kata-poll-submit" onclick="kataSubmitPoll(\'' . $poll_id . '\')">Bình Chọn</button>';
        
        if ($atts['show_results'] === 'true') {
            $output .= '<div class="kata-poll-results" style="display:none;"></div>';
        }
        
        $output .= '</form>';
        $output .= '</div>';
        
        $kata_poll_options = array();
        
        return $output;
    }

    public function render_poll_option($atts) {
        global $kata_poll_options;
        
        $atts = shortcode_atts(array(
            'text' => ''
        ), $atts, 'kata_poll_option');
        
        if (empty($atts['text'])) {
            return '';
        }
        
        if (!isset($kata_poll_options)) {
            $kata_poll_options = array();
        }
        
        $kata_poll_options[] = $atts['text'];
        
        return '';
    }

    public function render_form($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Liên Hệ',
            'action' => '',
            'method' => 'post',
            'success_message' => 'Cảm ơn bạn đã gửi thông tin!',
            'fields' => 'name,email,message'
        ), $atts, 'kata_form');
        
        $form_id = 'kata-form-' . uniqid();
        $fields = explode(',', $atts['fields']);
        
        $output = '<div id="' . $form_id . '" class="kata-form-container">';
        $output .= '<h3 class="kata-form-title">' . esc_html($atts['title']) . '</h3>';
        
        $action = !empty($atts['action']) ? $atts['action'] : admin_url('admin-ajax.php');
        $output .= '<form class="kata-form" action="' . esc_url($action) . '" method="' . esc_attr($atts['method']) . '">';
        
        if (empty($atts['action'])) {
            $output .= '<input type="hidden" name="action" value="kata_seo_submit_form">';
            $output .= wp_nonce_field('kata_form_nonce', 'form_nonce', true, false);
        }
        
        foreach ($fields as $field) {
            $field = trim($field);
            $field_id = $form_id . '-' . $field;
            
            switch ($field) {
                case 'name':
                    $output .= '<div class="kata-form-field">';
                    $output .= '<label for="' . $field_id . '">Họ Tên *</label>';
                    $output .= '<input type="text" id="' . $field_id . '" name="' . $field . '" required>';
                    $output .= '</div>';
                    break;
                    
                case 'email':
                    $output .= '<div class="kata-form-field">';
                    $output .= '<label for="' . $field_id . '">Email *</label>';
                    $output .= '<input type="email" id="' . $field_id . '" name="' . $field . '" required>';
                    $output .= '</div>';
                    break;
                    
                case 'phone':
                    $output .= '<div class="kata-form-field">';
                    $output .= '<label for="' . $field_id . '">Số Điện Thoại</label>';
                    $output .= '<input type="tel" id="' . $field_id . '" name="' . $field . '">';
                    $output .= '</div>';
                    break;
                    
                case 'message':
                    $output .= '<div class="kata-form-field">';
                    $output .= '<label for="' . $field_id . '">Tin Nhắn *</label>';
                    $output .= '<textarea id="' . $field_id . '" name="' . $field . '" rows="5" required></textarea>';
                    $output .= '</div>';
                    break;
            }
        }
        
        $output .= '<div class="kata-form-field">';
        $output .= '<button type="submit" class="kata-form-submit">Gửi</button>';
        $output .= '</div>';
        
        $output .= '</form>';
        $output .= '<div class="kata-form-message" style="display:none;"></div>';
        $output .= '</div>';
        
        return $output;
    }

    /**
     * Render Lucky Wheel - Refactored for better maintainability
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_wheel($atts) {
        // Parse and validate attributes
        $atts = shortcode_atts(array(
            'id' => 0,
            'style' => 'default' // 'default' or 'simple'
        ), $atts, 'kata_wheel');
        
        $wheel_id = intval($atts['id']);
        $style = sanitize_text_field($atts['style']);
        
        // Validate wheel ID
        if ($wheel_id === 0) {
            return $this->render_wheel_error('Vui lòng chỉ định ID vòng quay. VD: [kata_wheel id="1"]');
        }
        
        global $wpdb;
        
        // Get wheel data
        $wheel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d",
            $wheel_id
        ));
        
        if (!$wheel) {
            return $this->render_wheel_error('Vòng quay ID #' . $wheel_id . ' không tồn tại');
        }
        
        if ($wheel->status !== 'active') {
            return $this->render_wheel_inactive('Vòng quay hiện không hoạt động');
        }
        
        // Get prizes
        $prizes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheel_prizes 
             WHERE wheel_id = %d AND is_active = 1 
             ORDER BY position_order",
            $wheel_id
        ));
        
        if (empty($prizes)) {
            return $this->render_wheel_error('Vòng quay chưa có giải thưởng nào');
        }
        
        // Enqueue assets
        wp_enqueue_style('kata-wheel-frontend', KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/wheel-frontend.css', array(), KATA_SEO_MANAGER_VERSION);
        wp_enqueue_script('kata-wheel-frontend', KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/wheel-frontend.js', array('jquery'), KATA_SEO_MANAGER_VERSION, true);
        
        // Localize script
        wp_localize_script('kata-wheel-frontend', 'kata_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_wheel_nonce')
        ));
        
        // Render based on style
        if ($style === 'simple') {
            return $this->render_wheel_simple($wheel, $prizes, $wheel_id);
        }
        
        return $this->render_wheel_default($wheel, $prizes, $wheel_id);
    }

    /**
     * Render error message for wheel
     */
    private function render_wheel_error($message) {
        return '<div class="kata-wheel-error">
                    <span class="kata-wheel-error-icon">❌</span>
                    <strong>Lỗi:</strong> ' . esc_html($message) . '
                </div>';
    }

    /**
     * Render inactive message for wheel
     */
    private function render_wheel_inactive($message) {
        return '<div class="kata-wheel-inactive">
                    <span class="kata-wheel-inactive-icon">⚠️</span>
                    <strong>Thông báo:</strong> ' . esc_html($message) . '
                </div>';
    }

    /**
     * Render wheel with default style (canvas-based)
     */
    private function render_wheel_default($wheel, $prizes, $wheel_id) {
        ob_start();
        ?>
        <div class="kata-wheel-container" 
             id="kata-wheel-<?php echo esc_attr($wheel_id); ?>" 
             data-wheel-id="<?php echo esc_attr($wheel_id); ?>"
             data-requirement="<?php echo esc_attr($wheel->requirement); ?>">
            
            <!-- Header -->
            <div class="kata-wheel-header">
                <h2 class="kata-wheel-title"><?php echo esc_html($wheel->wheel_title); ?></h2>
                <?php if ($wheel->wheel_description): ?>
                    <p class="kata-wheel-description"><?php echo esc_html($wheel->wheel_description); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Wheel Canvas -->
            <div class="kata-wheel-canvas">
                <div class="kata-wheel-pointer">▼</div>
                <div class="kata-wheel-circle" id="kata-wheel-circle-<?php echo esc_attr($wheel_id); ?>">
                    <?php 
                    $prize_count = count($prizes);
                    $angle_per_prize = 360 / $prize_count;
                    foreach ($prizes as $index => $prize): 
                        $rotation = $index * $angle_per_prize;
                    ?>
                        <div class="kata-wheel-segment" 
                             style="--rotation: <?php echo esc_attr($rotation); ?>deg; --segment-color: <?php echo esc_attr($prize->color); ?>;" 
                             data-prize-id="<?php echo esc_attr($prize->id); ?>">
                            <div class="kata-wheel-segment-content">
                                <span class="kata-wheel-prize-text"><?php echo esc_html($prize->prize_text); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Center Spin Button -->
                <button type="button" class="kata-wheel-center" id="kata-wheel-spin-btn-<?php echo esc_attr($wheel_id); ?>">
                    <span>QUAY</span>
                </button>
            </div>
            
            <!-- User Form (if required) -->
            <?php if ($wheel->requirement !== 'none'): ?>
                <div class="kata-wheel-form" id="kata-wheel-form-<?php echo esc_attr($wheel_id); ?>">
                    <div class="kata-wheel-form-inner">
                        <h3>Nhập Thông Tin Để Quay</h3>
                        
                        <?php if (in_array($wheel->requirement, array('email', 'both'))): ?>
                            <div class="kata-wheel-form-field">
                                <label for="kata-wheel-email-<?php echo esc_attr($wheel_id); ?>">
                                    Email <span class="required">*</span>
                                </label>
                                <input type="email" 
                                       id="kata-wheel-email-<?php echo esc_attr($wheel_id); ?>" 
                                       class="kata-wheel-input" 
                                       placeholder="your@email.com" 
                                       required>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (in_array($wheel->requirement, array('phone', 'both'))): ?>
                            <div class="kata-wheel-form-field">
                                <label for="kata-wheel-phone-<?php echo esc_attr($wheel_id); ?>">
                                    Số Điện Thoại <span class="required">*</span>
                                </label>
                                <input type="tel" 
                                       id="kata-wheel-phone-<?php echo esc_attr($wheel_id); ?>" 
                                       class="kata-wheel-input" 
                                       placeholder="0123456789" 
                                       required>
                            </div>
                        <?php endif; ?>
                        
                        <div class="kata-wheel-form-field">
                            <label for="kata-wheel-name-<?php echo esc_attr($wheel_id); ?>">
                                Tên (không bắt buộc)
                            </label>
                            <input type="text" 
                                   id="kata-wheel-name-<?php echo esc_attr($wheel_id); ?>" 
                                   class="kata-wheel-input" 
                                   placeholder="Tên của bạn">
                        </div>
                        
                        <button type="button" class="kata-wheel-submit-btn">
                            ✨ Xác Nhận & Quay
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Result Modal -->
            <div class="kata-wheel-result-modal" id="kata-wheel-result-<?php echo esc_attr($wheel_id); ?>">
                <div class="kata-wheel-result-overlay"></div>
                <div class="kata-wheel-result-content">
                    <button type="button" class="kata-wheel-result-close">×</button>
                    <div class="kata-wheel-result-icon">🎉</div>
                    <h3 class="kata-wheel-result-title">Chúc Mừng!</h3>
                    <div class="kata-wheel-result-prize" id="kata-wheel-prize-name-<?php echo esc_attr($wheel_id); ?>"></div>
                    <div class="kata-wheel-result-value" id="kata-wheel-prize-value-<?php echo esc_attr($wheel_id); ?>"></div>
                    <p class="kata-wheel-result-note">Vui lòng liên hệ với chúng tôi để nhận giải thưởng!</p>
                    <button type="button" class="kata-wheel-result-btn">Đóng</button>
                </div>
            </div>
            
            <!-- Spins Info -->
            <div class="kata-wheel-info" id="kata-wheel-info-<?php echo esc_attr($wheel_id); ?>">
                <p class="kata-wheel-spins-remaining">
                    Lượt quay còn lại: <strong id="kata-wheel-spins-left-<?php echo esc_attr($wheel_id); ?>">--</strong>
                </p>
            </div>
        </div>
        
        <!-- Schema.org Markup -->
        <script type="application/ld+json">
        <?php echo wp_json_encode($this->generate_wheel_schema($wheel, $prizes), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
        </script>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Render wheel with simple style (list-based)
     */
    private function render_wheel_simple($wheel, $prizes, $wheel_id) {
        ob_start();
        ?>
        <div class="kata-wheel-container kata-wheel-simple" 
             id="kata-wheel-<?php echo esc_attr($wheel_id); ?>" 
             data-wheel-id="<?php echo esc_attr($wheel_id); ?>"
             data-requirement="<?php echo esc_attr($wheel->requirement); ?>">
            
            <!-- Simple Header -->
            <div class="kata-wheel-header-simple">
                <h3 class="kata-wheel-title-simple"><?php echo esc_html($wheel->wheel_title); ?></h3>
                <?php if ($wheel->wheel_description): ?>
                    <p class="kata-wheel-description-simple"><?php echo esc_html($wheel->wheel_description); ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Prizes List -->
            <div class="kata-wheel-prizes-list">
                <div class="kata-wheel-prizes-title">🎁 Giải thưởng:</div>
                <div class="kata-wheel-prizes-grid">
                    <?php foreach ($prizes as $prize): ?>
                        <div class="kata-wheel-prize-item" style="border-left: 4px solid <?php echo esc_attr($prize->color); ?>;">
                            <span class="kata-wheel-prize-name"><?php echo esc_html($prize->prize_text); ?></span>
                            <?php if ($prize->prize_value): ?>
                                <span class="kata-wheel-prize-value"><?php echo esc_html($prize->prize_value); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Spin Button -->
            <div class="kata-wheel-simple-spin">
                <button type="button" class="kata-wheel-simple-btn" id="kata-wheel-spin-btn-<?php echo esc_attr($wheel_id); ?>">
                    <span class="kata-wheel-btn-icon">🎲</span>
                    <span class="kata-wheel-btn-text">QUAY NGAY</span>
                    <span class="kata-wheel-btn-subtitle">Nhấn để quay!</span>
                </button>
            </div>
            
            <!-- User Form (if required) -->
            <?php if ($wheel->requirement !== 'none'): ?>
                <div class="kata-wheel-form kata-wheel-form-simple" id="kata-wheel-form-<?php echo esc_attr($wheel_id); ?>">
                    <div class="kata-wheel-form-simple-inner">
                        <div class="kata-wheel-form-simple-title">
                            <span class="kata-wheel-form-icon">✨</span>
                            <h4>Nhập thông tin để quay</h4>
                        </div>
                        
                        <?php if (in_array($wheel->requirement, array('email', 'both'))): ?>
                            <div class="kata-wheel-form-field-simple">
                                <label for="kata-wheel-email-<?php echo esc_attr($wheel_id); ?>">
                                    📧 Email <span class="required">*</span>
                                </label>
                                <input type="email" 
                                       id="kata-wheel-email-<?php echo esc_attr($wheel_id); ?>" 
                                       class="kata-wheel-input" 
                                       placeholder="your@email.com" 
                                       required>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (in_array($wheel->requirement, array('phone', 'both'))): ?>
                            <div class="kata-wheel-form-field-simple">
                                <label for="kata-wheel-phone-<?php echo esc_attr($wheel_id); ?>">
                                    📱 Số điện thoại <span class="required">*</span>
                                </label>
                                <input type="tel" 
                                       id="kata-wheel-phone-<?php echo esc_attr($wheel_id); ?>" 
                                       class="kata-wheel-input" 
                                       placeholder="0123456789" 
                                       required>
                            </div>
                        <?php endif; ?>
                        
                        <div class="kata-wheel-form-field-simple">
                            <label for="kata-wheel-name-<?php echo esc_attr($wheel_id); ?>">
                                👤 Tên (không bắt buộc)
                            </label>
                            <input type="text" 
                                   id="kata-wheel-name-<?php echo esc_attr($wheel_id); ?>" 
                                   class="kata-wheel-input" 
                                   placeholder="Tên của bạn">
                        </div>
                        
                        <button type="button" class="kata-wheel-submit-btn-simple">
                            🎯 Xác nhận & quay
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Result Display -->
            <div class="kata-wheel-result kata-wheel-result-simple" id="kata-wheel-result-<?php echo esc_attr($wheel_id); ?>">
                <div class="kata-wheel-result-simple-content">
                    <div class="kata-wheel-result-simple-icon">🎉</div>
                    <div class="kata-wheel-result-simple-title">Chúc mừng bạn!</div>
                    <div class="kata-wheel-result-simple-prize">
                        <div class="kata-wheel-result-prize-name" id="kata-wheel-prize-name-<?php echo esc_attr($wheel_id); ?>"></div>
                        <div class="kata-wheel-result-prize-value" id="kata-wheel-prize-value-<?php echo esc_attr($wheel_id); ?>"></div>
                    </div>
                    <div class="kata-wheel-result-simple-note">💡 Liên hệ với chúng tôi để nhận giải thưởng!</div>
                    <button type="button" class="kata-wheel-result-simple-btn">
                        👍 Đã hiểu
                    </button>
                </div>
            </div>
            
            <!-- Spins Info -->
            <div class="kata-wheel-info-simple" id="kata-wheel-info-<?php echo esc_attr($wheel_id); ?>">
                <div class="kata-wheel-spins-simple">
                    🎯 Lượt quay còn lại: <strong id="kata-wheel-spins-left-<?php echo esc_attr($wheel_id); ?>">--</strong>
                </div>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Generate wheel schema markup
     */
    private function generate_wheel_schema($wheel, $prizes) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Game',
            'name' => $wheel->wheel_title,
            'description' => $wheel->wheel_description ? $wheel->wheel_description : 'Vòng quay may mắn - quay để nhận giải thưởng',
            'genre' => 'Promotional Game',
            'gamePlatform' => 'Web Browser',
            'numberOfPlayers' => '1',
            'offers' => array(
                '@type' => 'AggregateOffer',
                'offerCount' => count($prizes),
                'offers' => array()
            )
        );
        
        // Add aggregate rating if available
        if (isset($wheel->total_spins) && $wheel->total_spins > 0) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => (string)$wheel->total_spins,
                'bestRating' => '5',
                'worstRating' => '1'
            );
        }
        
        // Add prize offers
        foreach ($prizes as $prize) {
            $schema['offers']['offers'][] = array(
                '@type' => 'Offer',
                'itemOffered' => array(
                    '@type' => ($prize->prize_type === 'discount' ? 'Discount' : 'Product'),
                    'name' => $prize->prize_text,
                    'description' => $prize->prize_value
                )
            );
        }
        
        return $schema;
    }

    public function render_rating($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Đánh Giá',
            'max_stars' => '5',
            'current_rating' => '0',
            'allow_rating' => 'true',
            'show_average' => 'true'
        ), $atts, 'kata_rating');
        
        $rating_id = 'kata-rating-' . uniqid();
        $max_stars = intval($atts['max_stars']);
        $current_rating = floatval($atts['current_rating']);
        
        $output = '<div id="' . $rating_id . '" class="kata-rating-container">';
        $output .= '<h4 class="kata-rating-title">' . esc_html($atts['title']) . '</h4>';
        
        $output .= '<div class="kata-rating-stars" data-rating="' . $current_rating . '" data-max="' . $max_stars . '"';
        if ($atts['allow_rating'] === 'true') {
            $output .= ' onclick="kataSetRating(\'' . $rating_id . '\', event)"';
        }
        $output .= '>';
        
        for ($i = 1; $i <= $max_stars; $i++) {
            $filled = ($i <= $current_rating) ? 'filled' : '';
            $output .= '<span class="kata-rating-star ' . $filled . '" data-star="' . $i . '">★</span>';
        }
        
        $output .= '</div>';
        
        if ($atts['show_average'] === 'true') {
            $output .= '<div class="kata-rating-average">';
            $output .= '<span class="kata-rating-score">' . number_format($current_rating, 1) . '</span>';
            $output .= '<span class="kata-rating-max">/' . $max_stars . '</span>';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        return $output;
    }

    public function render_organization($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'url' => '',
            'logo' => '',
            'description' => '',
            'address' => '',
            'phone' => '',
            'email' => ''
        ), $atts, 'kata_organization');
        
        if (empty($atts['name'])) {
            return '<div class="kata-organization-error">Tên tổ chức không được để trống.</div>';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $atts['name']
        );
        
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['logo'])) {
            $schema['logo'] = $atts['logo'];
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['address'])) {
            $schema['address'] = $atts['address'];
        }
        if (!empty($atts['phone'])) {
            $schema['telephone'] = $atts['phone'];
        }
        if (!empty($atts['email'])) {
            $schema['email'] = $atts['email'];
        }
        
        $output = '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        
        return $output;
    }

    public function render_localbusiness($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'type' => 'LocalBusiness',
            'address' => '',
            'phone' => '',
            'url' => '',
            'hours' => '',
            'price_range' => ''
        ), $atts, 'kata_localbusiness');
        
        if (empty($atts['name'])) {
            return '<div class="kata-localbusiness-error">Tên doanh nghiệp không được để trống.</div>';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $atts['type'],
            'name' => $atts['name']
        );
        
        if (!empty($atts['address'])) {
            $schema['address'] = $atts['address'];
        }
        if (!empty($atts['phone'])) {
            $schema['telephone'] = $atts['phone'];
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['hours'])) {
            $schema['openingHours'] = $atts['hours'];
        }
        if (!empty($atts['price_range'])) {
            $schema['priceRange'] = $atts['price_range'];
        }
        
        $output = '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        
        return $output;
    }

    public function render_jobposting($atts) {
        $atts = shortcode_atts(array(
            'title' => '',
            'company' => '',
            'location' => '',
            'description' => '',
            'salary' => '',
            'employment_type' => 'FULL_TIME',
            'date_posted' => ''
        ), $atts, 'kata_jobposting');
        
        if (empty($atts['title']) || empty($atts['company'])) {
            return '<div class="kata-jobposting-error">Tiêu đề công việc và tên công ty không được để trống.</div>';
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $atts['title'],
            'hiringOrganization' => array(
                '@type' => 'Organization',
                'name' => $atts['company']
            )
        );
        
        if (!empty($atts['location'])) {
            $schema['jobLocation'] = array(
                '@type' => 'Place',
                'address' => $atts['location']
            );
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['salary'])) {
            $schema['baseSalary'] = array(
                '@type' => 'MonetaryAmount',
                'value' => $atts['salary']
            );
        }
        if (!empty($atts['employment_type'])) {
            $schema['employmentType'] = $atts['employment_type'];
        }
        if (!empty($atts['date_posted'])) {
            $schema['datePosted'] = $atts['date_posted'];
        }
        
        $output = '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        
        return $output;
    }

    public function render_review($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'review_body' => '',
            'item_name' => '',
            'item_type' => 'Product', // Product, Movie, Book, Restaurant, etc.
            'rating_value' => '',
            'best_rating' => '5',
            'worst_rating' => '1',
            'author' => '',
            'date_published' => '',
            'publisher' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_review');
        
        if (empty($atts['name']) || empty($atts['item_name'])) {
            return '<div class="kata-review-error">Tên đánh giá và tên sản phẩm không được để trống.</div>';
        }
        
        $review_id = 'kata-review-' . uniqid();
        
        // Generate schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'name' => $atts['name'],
            'itemReviewed' => array(
                '@type' => $atts['item_type'],
                'name' => $atts['item_name']
            )
        );
        
        if (!empty($atts['review_body'])) {
            $schema['reviewBody'] = $atts['review_body'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['date_published'])) {
            $schema['datePublished'] = $atts['date_published'];
        }
        if (!empty($atts['publisher'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            );
        }
        if (!empty($atts['rating_value'])) {
            $schema['reviewRating'] = array(
                '@type' => 'Rating',
                'ratingValue' => $atts['rating_value'],
                'bestRating' => $atts['best_rating'],
                'worstRating' => $atts['worst_rating']
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $review_id . '" class="kata-review-container">';
            $output .= '<h3 class="kata-review-title">' . esc_html($atts['name']) . '</h3>';
            $output .= '<div class="kata-review-item">Đánh giá về: <strong>' . esc_html($atts['item_name']) . '</strong></div>';
            
            if (!empty($atts['rating_value'])) {
                $stars = str_repeat('⭐', round(floatval($atts['rating_value'])));
                $output .= '<div class="kata-review-rating">' . $stars . ' (' . esc_html($atts['rating_value']) . '/' . esc_html($atts['best_rating']) . ')</div>';
            }
            
            if (!empty($atts['review_body'])) {
                $output .= '<div class="kata-review-body">' . wpautop(esc_html($atts['review_body'])) . '</div>';
            }
            
            if (!empty($atts['author'])) {
                $output .= '<div class="kata-review-author">Người đánh giá: ' . esc_html($atts['author']) . '</div>';
            }
            
            $output .= '</div>';
        }
        
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }

    public function render_movie($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'director' => '',
            'actor' => '',
            'genre' => '',
            'duration' => '',
            'release_date' => '',
            'production_company' => '',
            'country' => '',
            'language' => 'vi',
            'rating_value' => '',
            'rating_count' => '',
            'image' => '',
            'trailer' => '',
            'url' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_movie');
        
        if (empty($atts['name'])) {
            return '<div class="kata-movie-error">Tên phim không được để trống.</div>';
        }
        
        $movie_id = 'kata-movie-' . uniqid();
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Movie',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['director'])) {
            $directors = array_map('trim', explode(',', $atts['director']));
            $director_objects = array();
            foreach ($directors as $director) {
                $director_objects[] = array('@type' => 'Person', 'name' => $director);
            }
            $schema['director'] = count($director_objects) === 1 ? $director_objects[0] : $director_objects;
        }
        if (!empty($atts['actor'])) {
            $actors = array_map('trim', explode(',', $atts['actor']));
            $actor_objects = array();
            foreach ($actors as $actor) {
                $actor_objects[] = array('@type' => 'Person', 'name' => $actor);
            }
            $schema['actor'] = $actor_objects;
        }
        if (!empty($atts['genre'])) {
            $schema['genre'] = array_map('trim', explode(',', $atts['genre']));
        }
        if (!empty($atts['duration'])) {
            $schema['duration'] = 'PT' . $atts['duration'];
        }
        if (!empty($atts['release_date'])) {
            $schema['dateCreated'] = $atts['release_date'];
        }
        if (!empty($atts['production_company'])) {
            $schema['productionCompany'] = array(
                '@type' => 'Organization',
                'name' => $atts['production_company']
            );
        }
        if (!empty($atts['country'])) {
            $schema['countryOfOrigin'] = $atts['country'];
        }
        if (!empty($atts['language'])) {
            $schema['inLanguage'] = $atts['language'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = $atts['image'];
        }
        if (!empty($atts['trailer'])) {
            $schema['trailer'] = array(
                '@type' => 'VideoObject',
                'contentUrl' => $atts['trailer']
            );
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['rating_value']) && !empty($atts['rating_count'])) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $movie_id . '" class="kata-movie-container">';
            $output .= '<h2 class="kata-movie-title">🎬 ' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-movie-poster"><img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" /></div>';
            }
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-movie-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '<div class="kata-movie-details">';
            if (!empty($atts['director'])) {
                $output .= '<div class="kata-detail">Đạo diễn: <strong>' . esc_html($atts['director']) . '</strong></div>';
            }
            if (!empty($atts['actor'])) {
                $output .= '<div class="kata-detail">Diễn viên: <strong>' . esc_html($atts['actor']) . '</strong></div>';
            }
            if (!empty($atts['genre'])) {
                $output .= '<div class="kata-detail">Thể loại: <strong>' . esc_html($atts['genre']) . '</strong></div>';
            }
            if (!empty($atts['duration'])) {
                $output .= '<div class="kata-detail">Thời lượng: <strong>' . esc_html($atts['duration']) . '</strong></div>';
            }
            if (!empty($atts['release_date'])) {
                $output .= '<div class="kata-detail">Ngày phát hành: <strong>' . date('d/m/Y', strtotime($atts['release_date'])) . '</strong></div>';
            }
            $output .= '</div>';
            
            if (!empty($atts['rating_value'])) {
                $stars = str_repeat('⭐', round(floatval($atts['rating_value'])));
                $output .= '<div class="kata-movie-rating">' . $stars . ' ' . esc_html($atts['rating_value']) . '/5</div>';
            }
            
            $output .= '</div>';
        }
        
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }

    public function render_course($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'provider' => '',
            'instructor' => '',
            'price' => '',
            'currency' => 'VND',
            'duration' => '',
            'course_mode' => 'online', // online, offline, blended
            'level' => '', // beginner, intermediate, advanced
            'language' => 'vi',
            'category' => '',
            'skills' => '',
            'requirements' => '',
            'url' => '',
            'image' => '',
            'rating_value' => '',
            'rating_count' => '',
            'enrollment_count' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_course');
        
        if (empty($atts['name'])) {
            return '<div class="kata-course-error">Tên khóa học không được để trống.</div>';
        }
        
        $course_id = 'kata-course-' . uniqid();
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['provider'])) {
            $schema['provider'] = array(
                '@type' => 'Organization',
                'name' => $atts['provider']
            );
        }
        if (!empty($atts['instructor'])) {
            $schema['instructor'] = array(
                '@type' => 'Person',
                'name' => $atts['instructor']
            );
        }
        if (!empty($atts['price'])) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency']
            );
        }
        if (!empty($atts['duration'])) {
            $schema['timeRequired'] = 'PT' . $atts['duration'];
        }
        if (!empty($atts['course_mode'])) {
            $schema['courseMode'] = $atts['course_mode'];
        }
        if (!empty($atts['level'])) {
            $schema['educationalLevel'] = $atts['level'];
        }
        if (!empty($atts['language'])) {
            $schema['inLanguage'] = $atts['language'];
        }
        if (!empty($atts['category'])) {
            $schema['about'] = $atts['category'];
        }
        if (!empty($atts['skills'])) {
            $schema['teaches'] = array_map('trim', explode(',', $atts['skills']));
        }
        if (!empty($atts['requirements'])) {
            $schema['coursePrerequisites'] = array_map('trim', explode(',', $atts['requirements']));
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = $atts['image'];
        }
        if (!empty($atts['rating_value']) && !empty($atts['rating_count'])) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $course_id . '" class="kata-course-container">';
            $output .= '<h2 class="kata-course-title">📚 ' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-course-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '<div class="kata-course-details">';
            if (!empty($atts['provider'])) {
                $output .= '<div class="kata-detail">Nhà cung cấp: <strong>' . esc_html($atts['provider']) . '</strong></div>';
            }
            if (!empty($atts['instructor'])) {
                $output .= '<div class="kata-detail">Giảng viên: <strong>' . esc_html($atts['instructor']) . '</strong></div>';
            }
            if (!empty($atts['duration'])) {
                $output .= '<div class="kata-detail">Thời lượng: <strong>' . esc_html($atts['duration']) . '</strong></div>';
            }
            if (!empty($atts['level'])) {
                $level_text = array(
                    'beginner' => '🟢 Cơ bản',
                    'intermediate' => '🟡 Trung cấp',
                    'advanced' => '🔴 Nâng cao'
                );
                $output .= '<div class="kata-detail">Trình độ: <strong>' . ($level_text[$atts['level']] ?? $atts['level']) . '</strong></div>';
            }
            if (!empty($atts['price'])) {
                $formatted_price = ($atts['price'] === '0') ? 'Miễn phí' : number_format($atts['price'], 0, ',', '.') . ' ' . $atts['currency'];
                $output .= '<div class="kata-detail">Học phí: <strong>' . $formatted_price . '</strong></div>';
            }
            $output .= '</div>';
            
            if (!empty($atts['skills'])) {
                $skills = array_map('trim', explode(',', $atts['skills']));
                $output .= '<div class="kata-course-skills"><h4>🎯 Kỹ năng học được:</h4><ul>';
                foreach ($skills as $skill) {
                    $output .= '<li>' . esc_html($skill) . '</li>';
                }
                $output .= '</ul></div>';
            }
            
            if (!empty($atts['url'])) {
                $output .= '<div class="kata-course-action"><a href="' . esc_url($atts['url']) . '" class="kata-enroll-button" target="_blank">📝 Đăng Ký Học</a></div>';
            }
            
            $output .= '</div>';
        }
        
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }

    public function render_software($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'version' => '',
            'operating_system' => '',
            'application_category' => '',
            'price' => '',
            'currency' => 'VND',
            'file_size' => '',
            'download_url' => '',
            'screenshot' => '',
            'rating_value' => '',
            'rating_count' => '',
            'author' => '',
            'release_date' => '',
            'requirements' => '',
            'features' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_software');
        
        if (empty($atts['name'])) {
            return '<div class="kata-software-error">Tên phần mềm không được để trống.</div>';
        }
        
        $software_id = 'kata-software-' . uniqid();
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => $atts['name']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['version'])) {
            $schema['softwareVersion'] = $atts['version'];
        }
        if (!empty($atts['operating_system'])) {
            $schema['operatingSystem'] = $atts['operating_system'];
        }
        if (!empty($atts['application_category'])) {
            $schema['applicationCategory'] = $atts['application_category'];
        }
        if (!empty($atts['price'])) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency']
            );
        }
        if (!empty($atts['file_size'])) {
            $schema['fileSize'] = $atts['file_size'];
        }
        if (!empty($atts['download_url'])) {
            $schema['downloadUrl'] = $atts['download_url'];
        }
        if (!empty($atts['screenshot'])) {
            $schema['screenshot'] = $atts['screenshot'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['release_date'])) {
            $schema['datePublished'] = $atts['release_date'];
        }
        if (!empty($atts['requirements'])) {
            $schema['requirements'] = $atts['requirements'];
        }
        if (!empty($atts['rating_value']) && !empty($atts['rating_count'])) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $software_id . '" class="kata-software-container">';
            $output .= '<h2 class="kata-software-title">💻 ' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['screenshot'])) {
                $output .= '<div class="kata-software-screenshot"><img src="' . esc_url($atts['screenshot']) . '" alt="' . esc_attr($atts['name']) . '" /></div>';
            }
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-software-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '<div class="kata-software-details">';
            if (!empty($atts['version'])) {
                $output .= '<div class="kata-detail">Phiên bản: <strong>' . esc_html($atts['version']) . '</strong></div>';
            }
            if (!empty($atts['operating_system'])) {
                $output .= '<div class="kata-detail">Hệ điều hành: <strong>' . esc_html($atts['operating_system']) . '</strong></div>';
            }
            if (!empty($atts['file_size'])) {
                $output .= '<div class="kata-detail">Dung lượng: <strong>' . esc_html($atts['file_size']) . '</strong></div>';
            }
            if (!empty($atts['price'])) {
                $formatted_price = ($atts['price'] === '0') ? 'Miễn phí' : number_format($atts['price'], 0, ',', '.') . ' ' . $atts['currency'];
                $output .= '<div class="kata-detail">Giá: <strong>' . $formatted_price . '</strong></div>';
            }
            $output .= '</div>';
            
            if (!empty($atts['download_url'])) {
                $output .= '<div class="kata-software-action"><a href="' . esc_url($atts['download_url']) . '" class="kata-download-button" target="_blank">⬇️ Tải Xuống</a></div>';
            }
            
            $output .= '</div>';
        }
        
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }

    public function render_book($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'author' => '',
            'description' => '',
            'isbn' => '',
            'publisher' => '',
            'publication_date' => '',
            'pages' => '',
            'genre' => '',
            'language' => 'vi',
            'format' => 'Paperback', // Paperback, Hardcover, EBook, AudioBook
            'price' => '',
            'currency' => 'VND',
            'image' => '',
            'url' => '',
            'rating_value' => '',
            'rating_count' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_book');
        
        if (empty($atts['name']) || empty($atts['author'])) {
            return '<div class="kata-book-error">Tên sách và tác giả không được để trống.</div>';
        }
        
        $book_id = 'kata-book-' . uniqid();
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $atts['name'],
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            )
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['isbn'])) {
            $schema['isbn'] = $atts['isbn'];
        }
        if (!empty($atts['publisher'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            );
        }
        if (!empty($atts['publication_date'])) {
            $schema['datePublished'] = $atts['publication_date'];
        }
        if (!empty($atts['pages'])) {
            $schema['numberOfPages'] = intval($atts['pages']);
        }
        if (!empty($atts['genre'])) {
            $schema['genre'] = $atts['genre'];
        }
        if (!empty($atts['language'])) {
            $schema['inLanguage'] = $atts['language'];
        }
        if (!empty($atts['format'])) {
            $schema['bookFormat'] = 'https://schema.org/' . $atts['format'] . 'Format';
        }
        if (!empty($atts['image'])) {
            $schema['image'] = $atts['image'];
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['price'])) {
            $schema['offers'] = array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency']
            );
        }
        if (!empty($atts['rating_value']) && !empty($atts['rating_count'])) {
            $schema['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div id="' . $book_id . '" class="kata-book-container">';
            $output .= '<h2 class="kata-book-title">📖 ' . esc_html($atts['name']) . '</h2>';
            
            if (!empty($atts['image'])) {
                $output .= '<div class="kata-book-cover"><img src="' . esc_url($atts['image']) . '" alt="' . esc_attr($atts['name']) . '" /></div>';
            }
            
            $output .= '<div class="kata-book-author">Tác giả: <strong>' . esc_html($atts['author']) . '</strong></div>';
            
            if (!empty($atts['description'])) {
                $output .= '<p class="kata-book-description">' . esc_html($atts['description']) . '</p>';
            }
            
            $output .= '<div class="kata-book-details">';
            if (!empty($atts['publisher'])) {
                $output .= '<div class="kata-detail">Nhà xuất bản: <strong>' . esc_html($atts['publisher']) . '</strong></div>';
            }
            if (!empty($atts['publication_date'])) {
                $output .= '<div class="kata-detail">Ngày xuất bản: <strong>' . date('d/m/Y', strtotime($atts['publication_date'])) . '</strong></div>';
            }
            if (!empty($atts['pages'])) {
                $output .= '<div class="kata-detail">Số trang: <strong>' . esc_html($atts['pages']) . '</strong></div>';
            }
            if (!empty($atts['isbn'])) {
                $output .= '<div class="kata-detail">ISBN: <strong>' . esc_html($atts['isbn']) . '</strong></div>';
            }
            if (!empty($atts['genre'])) {
                $output .= '<div class="kata-detail">Thể loại: <strong>' . esc_html($atts['genre']) . '</strong></div>';
            }
            if (!empty($atts['format'])) {
                $format_text = array(
                    'Paperback' => 'Bìa mềm',
                    'Hardcover' => 'Bìa cứng',
                    'EBook' => 'Sách điện tử',
                    'AudioBook' => 'Sách nói'
                );
                $output .= '<div class="kata-detail">Định dạng: <strong>' . ($format_text[$atts['format']] ?? $atts['format']) . '</strong></div>';
            }
            $output .= '</div>';
            
            if (!empty($atts['rating_value'])) {
                $stars = str_repeat('⭐', round(floatval($atts['rating_value'])));
                $output .= '<div class="kata-book-rating">' . $stars . ' ' . esc_html($atts['rating_value']) . '/5</div>';
            }
            
            if (!empty($atts['url'])) {
                $output .= '<div class="kata-book-action"><a href="' . esc_url($atts['url']) . '" class="kata-buy-book-button" target="_blank">📚 Mua Sách</a></div>';
            }
            
            $output .= '</div>';
        }
        
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }

    public function render_webpage($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'url' => '',
            'image' => '',
            'author' => '',
            'publisher' => '',
            'date_published' => '',
            'date_modified' => '',
            'keywords' => '',
            'language' => 'vi',
            'breadcrumb' => '',
            'show_content' => 'false',
            'show_schema' => 'true'
        ), $atts, 'kata_webpage');
        
        global $post;
        
        // Use current page data if not provided
        if (empty($atts['name']) && $post) {
            $atts['name'] = get_the_title($post);
        }
        if (empty($atts['description']) && $post) {
            $atts['description'] = get_the_excerpt($post);
        }
        if (empty($atts['url']) && $post) {
            $atts['url'] = get_permalink($post);
        }
        if (empty($atts['date_published']) && $post) {
            $atts['date_published'] = get_the_date('c', $post);
        }
        if (empty($atts['date_modified']) && $post) {
            $atts['date_modified'] = get_the_modified_date('c', $post);
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $atts['name'],
            'url' => $atts['url']
        );
        
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = $atts['image'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['publisher'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            );
        }
        if (!empty($atts['date_published'])) {
            $schema['datePublished'] = $atts['date_published'];
        }
        if (!empty($atts['date_modified'])) {
            $schema['dateModified'] = $atts['date_modified'];
        }
        if (!empty($atts['keywords'])) {
            $schema['keywords'] = $atts['keywords'];
        }
        if (!empty($atts['language'])) {
            $schema['inLanguage'] = $atts['language'];
        }
        
        // Add breadcrumb if provided
        if (!empty($atts['breadcrumb'])) {
            $breadcrumb_items = array_map('trim', explode('>', $atts['breadcrumb']));
            $breadcrumb_list = array();
            
            foreach ($breadcrumb_items as $index => $item) {
                $breadcrumb_list[] = array(
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $item
                );
            }
            
            $schema['breadcrumb'] = array(
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumb_list
            );
        }
        
        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-webpage-info">';
            $output .= '<h3>🌐 Thông Tin Trang Web</h3>';
            $output .= '<div class="kata-webpage-details">';
            $output .= '<div class="kata-detail">Tiêu đề: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['description'])) {
                $output .= '<div class="kata-detail">Mô tả: ' . esc_html($atts['description']) . '</div>';
            }
            if (!empty($atts['url'])) {
                $output .= '<div class="kata-detail">URL: <a href="' . esc_url($atts['url']) . '" target="_blank">' . esc_html($atts['url']) . '</a></div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }
        
        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }
        
        return $output;
    }

    /**
     * Render Person Schema
     */
    public function render_person($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'job_title' => '',
            'company' => '',
            'description' => '',
            'url' => '',
            'image' => '',
            'email' => '',
            'address' => '',
            'birth_date' => '',
            'nationality' => '',
            'skills' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_person');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $atts['name']
        );

        if (!empty($atts['job_title'])) {
            $schema['jobTitle'] = $atts['job_title'];
        }
        if (!empty($atts['company'])) {
            $schema['worksFor'] = array(
                '@type' => 'Organization',
                'name' => $atts['company']
            );
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['image'])) {
            $schema['image'] = $atts['image'];
        }
        if (!empty($atts['email'])) {
            $schema['email'] = $atts['email'];
        }
        if (!empty($atts['address'])) {
            $schema['address'] = $atts['address'];
        }
        if (!empty($atts['birth_date'])) {
            $schema['birthDate'] = $atts['birth_date'];
        }
        if (!empty($atts['nationality'])) {
            $schema['nationality'] = $atts['nationality'];
        }
        if (!empty($atts['skills'])) {
            $schema['knowsAbout'] = array_map('trim', explode('|', $atts['skills']));
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-person-info">';
            $output .= '<h3>👨‍💼 Thông Tin Cá Nhân</h3>';
            $output .= '<div class="kata-person-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['job_title'])) {
                $output .= '<div class="kata-detail">Chức vụ: ' . esc_html($atts['job_title']) . '</div>';
            }
            if (!empty($atts['company'])) {
                $output .= '<div class="kata-detail">Công ty: ' . esc_html($atts['company']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Service Schema
     */
    public function render_service($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'provider' => '',
            'type' => '',
            'description' => '',
            'area_served' => '',
            'price_range' => '',
            'currency' => 'VND',
            'duration' => '',
            'category' => '',
            'features' => '',
            'guarantee' => '',
            'contact_phone' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_service');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $atts['name']
        );

        if (!empty($atts['provider'])) {
            $schema['provider'] = array(
                '@type' => 'Organization',
                'name' => $atts['provider']
            );
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['area_served'])) {
            $schema['areaServed'] = $atts['area_served'];
        }
        if (!empty($atts['category'])) {
            $schema['serviceType'] = $atts['category'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-service-info">';
            $output .= '<h3>🛠️ Thông Tin Dịch Vụ</h3>';
            $output .= '<div class="kata-service-details">';
            $output .= '<div class="kata-detail">Dịch vụ: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['provider'])) {
                $output .= '<div class="kata-detail">Nhà cung cấp: ' . esc_html($atts['provider']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Vehicle Schema
     */
    public function render_vehicle($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'brand' => '',
            'model' => '',
            'year' => '',
            'type' => '',
            'fuel_type' => '',
            'engine' => '',
            'transmission' => '',
            'doors' => '',
            'seats' => '',
            'price' => '',
            'currency' => 'VND',
            'color' => '',
            'mileage' => '',
            'condition' => '',
            'description' => '',
            'features' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_vehicle');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Vehicle',
            'name' => $atts['name']
        );

        if (!empty($atts['brand'])) {
            $schema['brand'] = $atts['brand'];
        }
        if (!empty($atts['model'])) {
            $schema['model'] = $atts['model'];
        }
        if (!empty($atts['year'])) {
            $schema['vehicleModelDate'] = $atts['year'];
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-vehicle-info">';
            $output .= '<h3>🚗 Thông Tin Xe</h3>';
            $output .= '<div class="kata-vehicle-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['brand'])) {
                $output .= '<div class="kata-detail">Hãng: ' . esc_html($atts['brand']) . '</div>';
            }
            if (!empty($atts['model'])) {
                $output .= '<div class="kata-detail">Model: ' . esc_html($atts['model']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Real Estate Schema
     */
    public function render_realestate($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'type' => 'Apartment',
            'price' => '',
            'currency' => 'VND',
            'address' => '',
            'bedrooms' => '',
            'bathrooms' => '',
            'area' => '',
            'area_unit' => 'm2',
            'floor' => '',
            'total_floors' => '',
            'year_built' => '',
            'description' => '',
            'amenities' => '',
            'direction' => '',
            'legal' => '',
            'contact_phone' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_realestate');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateListing',
            'name' => $atts['name']
        );

        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['address'])) {
            $schema['address'] = $atts['address'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-realestate-info">';
            $output .= '<h3>🏠 Thông Tin Bất Động Sản</h3>';
            $output .= '<div class="kata-realestate-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['type'])) {
                $output .= '<div class="kata-detail">Loại: ' . esc_html($atts['type']) . '</div>';
            }
            if (!empty($atts['address'])) {
                $output .= '<div class="kata-detail">Địa chỉ: ' . esc_html($atts['address']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Restaurant Schema
     */
    public function render_restaurant($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'cuisine' => '',
            'address' => '',
            'phone' => '',
            'website' => '',
            'price_range' => '',
            'opening_hours' => '',
            'accepts_reservations' => '',
            'delivery' => '',
            'description' => '',
            'specialties' => '',
            'atmosphere' => '',
            'rating_value' => '',
            'rating_count' => '',
            'image' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_restaurant');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => $atts['name']
        );

        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['address'])) {
            $schema['address'] = $atts['address'];
        }
        if (!empty($atts['phone'])) {
            $schema['telephone'] = $atts['phone'];
        }
        if (!empty($atts['cuisine'])) {
            $schema['servesCuisine'] = $atts['cuisine'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-restaurant-info">';
            $output .= '<h3>🍽️ Thông Tin Nhà Hàng</h3>';
            $output .= '<div class="kata-restaurant-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['cuisine'])) {
                $output .= '<div class="kata-detail">Ẩm thực: ' . esc_html($atts['cuisine']) . '</div>';
            }
            if (!empty($atts['address'])) {
                $output .= '<div class="kata-detail">Địa chỉ: ' . esc_html($atts['address']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Medical Organization Schema
     */
    public function render_medicalorganization($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'type' => 'Hospital',
            'description' => '',
            'address' => '',
            'phone' => '',
            'website' => '',
            'specialties' => '',
            'services' => '',
            'insurance_accepted' => '',
            'opening_hours' => '',
            'rating_value' => '',
            'rating_count' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_medicalorganization');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'MedicalOrganization',
            'name' => $atts['name']
        );

        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['address'])) {
            $schema['address'] = $atts['address'];
        }
        if (!empty($atts['phone'])) {
            $schema['telephone'] = $atts['phone'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-medical-info">';
            $output .= '<h3>🏥 Thông Tin Y Tế</h3>';
            $output .= '<div class="kata-medical-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['type'])) {
                $output .= '<div class="kata-detail">Loại: ' . esc_html($atts['type']) . '</div>';
            }
            if (!empty($atts['address'])) {
                $output .= '<div class="kata-detail">Địa chỉ: ' . esc_html($atts['address']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Creative Work Schema
     */
    public function render_creativework($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'creator' => '',
            'type' => 'CreativeWork',
            'description' => '',
            'medium' => '',
            'dimensions' => '',
            'creation_date' => '',
            'genre' => '',
            'style' => '',
            'price' => '',
            'currency' => 'VND',
            'copyright' => '',
            'exhibition' => '',
            'location' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_creativework');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $atts['name']
        );

        if (!empty($atts['creator'])) {
            $schema['creator'] = array(
                '@type' => 'Person',
                'name' => $atts['creator']
            );
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['creation_date'])) {
            $schema['dateCreated'] = $atts['creation_date'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-creative-info">';
            $output .= '<h3>🎨 Thông Tin Tác Phẩm</h3>';
            $output .= '<div class="kata-creative-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['creator'])) {
                $output .= '<div class="kata-detail">Tác giả: ' . esc_html($atts['creator']) . '</div>';
            }
            if (!empty($atts['type'])) {
                $output .= '<div class="kata-detail">Loại: ' . esc_html($atts['type']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Video Object Schema
     */
    public function render_videoobject($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'description' => '',
            'duration' => '',
            'upload_date' => '',
            'thumbnail' => '',
            'embed_url' => '',
            'content_url' => '',
            'creator' => '',
            'publisher' => '',
            'category' => '',
            'language' => '',
            'quality' => '',
            'view_count' => '',
            'like_count' => '',
            'comment_count' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_videoobject');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $atts['name']
        );

        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['duration'])) {
            $schema['duration'] = $atts['duration'];
        }
        if (!empty($atts['upload_date'])) {
            $schema['uploadDate'] = $atts['upload_date'];
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-video-info">';
            $output .= '<h3>📹 Thông Tin Video</h3>';
            $output .= '<div class="kata-video-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['creator'])) {
                $output .= '<div class="kata-detail">Tác giả: ' . esc_html($atts['creator']) . '</div>';
            }
            if (!empty($atts['duration'])) {
                $output .= '<div class="kata-detail">Thời lượng: ' . esc_html($atts['duration']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render News Article Schema
     */
    public function render_newsarticle($atts) {
        $atts = shortcode_atts(array(
            'headline' => '',
            'description' => '',
            'author' => '',
            'publisher' => '',
            'publication_date' => '',
            'location' => '',
            'category' => '',
            'keywords' => '',
            'image' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_newsarticle');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $atts['headline']
        );

        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['publisher'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            );
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-news-info">';
            $output .= '<h3>📰 Thông Tin Tin Tức</h3>';
            $output .= '<div class="kata-news-details">';
            $output .= '<div class="kata-detail">Tiêu đề: <strong>' . esc_html($atts['headline']) . '</strong></div>';
            if (!empty($atts['author'])) {
                $output .= '<div class="kata-detail">Tác giả: ' . esc_html($atts['author']) . '</div>';
            }
            if (!empty($atts['publisher'])) {
                $output .= '<div class="kata-detail">Nhà xuất bản: ' . esc_html($atts['publisher']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Blog Posting Schema
     */
    public function render_blogposting($atts) {
        $atts = shortcode_atts(array(
            'headline' => '',
            'description' => '',
            'author' => '',
            'publisher' => '',
            'publication_date' => '',
            'category' => '',
            'tags' => '',
            'word_count' => '',
            'reading_time' => '',
            'comment_count' => '',
            'share_count' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_blogposting');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $atts['headline']
        );

        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['author'])) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name' => $atts['author']
            );
        }
        if (!empty($atts['publisher'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            );
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-blog-info">';
            $output .= '<h3>📝 Thông Tin Blog</h3>';
            $output .= '<div class="kata-blog-details">';
            $output .= '<div class="kata-detail">Tiêu đề: <strong>' . esc_html($atts['headline']) . '</strong></div>';
            if (!empty($atts['author'])) {
                $output .= '<div class="kata-detail">Tác giả: ' . esc_html($atts['author']) . '</div>';
            }
            if (!empty($atts['word_count'])) {
                $output .= '<div class="kata-detail">Số từ: ' . esc_html($atts['word_count']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Website Schema
     */
    public function render_website($atts) {
        $atts = shortcode_atts(array(
            'name' => '',
            'url' => '',
            'description' => '',
            'publisher' => '',
            'category' => '',
            'language' => '',
            'search_action' => '',
            'search_url' => '',
            'about' => '',
            'keywords' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_website');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $atts['name']
        );

        if (!empty($atts['url'])) {
            $schema['url'] = $atts['url'];
        }
        if (!empty($atts['description'])) {
            $schema['description'] = $atts['description'];
        }
        if (!empty($atts['publisher'])) {
            $schema['publisher'] = array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            );
        }

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-website-info">';
            $output .= '<h3>🌐 Thông Tin Website</h3>';
            $output .= '<div class="kata-website-details">';
            $output .= '<div class="kata-detail">Tên: <strong>' . esc_html($atts['name']) . '</strong></div>';
            if (!empty($atts['url'])) {
                $output .= '<div class="kata-detail">URL: <a href="' . esc_url($atts['url']) . '">' . esc_html($atts['url']) . '</a></div>';
            }
            if (!empty($atts['description'])) {
                $output .= '<div class="kata-detail">Mô tả: ' . esc_html($atts['description']) . '</div>';
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }

    /**
     * Render Breadcrumb List Schema
     */
    public function render_breadcrumblist($atts) {
        $atts = shortcode_atts(array(
            'items' => '',
            'show_content' => 'true',
            'show_schema' => 'true'
        ), $atts, 'kata_breadcrumblist');

        $breadcrumb_items = array();
        
        if (!empty($atts['items'])) {
            $items = explode('|', $atts['items']);
            foreach ($items as $index => $item) {
                $parts = explode('>', $item);
                if (count($parts) >= 2) {
                    $breadcrumb_items[] = array(
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => trim($parts[0]),
                        'item' => trim($parts[1])
                    );
                }
            }
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumb_items
        );

        $output = '';
        
        if ($atts['show_content'] === 'true') {
            $output .= '<div class="kata-breadcrumb-info">';
            $output .= '<h3>🧭 Breadcrumb Navigation</h3>';
            $output .= '<div class="kata-breadcrumb-list">';
            foreach ($breadcrumb_items as $item) {
                $output .= '<span class="breadcrumb-item">' . esc_html($item['name']) . '</span>';
                if ($item !== end($breadcrumb_items)) {
                    $output .= ' > ';
                }
            }
            $output .= '</div>';
            $output .= '</div>';
        }

        if ($atts['show_schema'] === 'true') {
            $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>';
        }

        return $output;
    }



    /**
     * Render User Interaction shortcode (Reviews + Ratings + Comments)
     */
    public function render_user_interaction($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
            'type' => 'combined', // combined, review, rating, comment
            'enable_reviews' => 'true',
            'enable_ratings' => 'true', 
            'enable_comments' => 'true',
            'enable_replies' => 'true',
            'require_login' => 'false',
            'require_moderation' => 'true',
            'show_form' => 'true',
            'show_list' => 'true',
            'items_per_page' => '10',
            'rating_criteria' => '', // JSON or comma-separated
            'title' => 'Đánh giá & Bình luận',
            'style' => 'default' // default, card, minimal
        ), $atts, 'kata_user_interaction');

        $post_id = intval($atts['post_id']);
        if (!$post_id) {
            // Try to get current post ID
            $current_post_id = get_the_ID();
            if ($current_post_id) {
                $post_id = $current_post_id;
            } else {
                // For testing or general use, allow post_id = 1 as default
                $post_id = 1;
            }
        }

        // Generate unique container ID
        $container_id = 'kata-user-interaction-' . uniqid();
        
        // Enqueue required assets
        wp_enqueue_script('kata-user-interaction', KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/user-interaction.js', array('jquery'), KATA_SEO_MANAGER_VERSION, true);
        wp_enqueue_style('kata-user-interaction', KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/user-interaction.css', array(), KATA_SEO_MANAGER_VERSION);
        
        // Localize script
        wp_localize_script('kata-user-interaction', 'kataUserInteraction', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_user_interaction_nonce'),
            'postId' => $post_id,
            'strings' => array(
                'submitSuccess' => 'Cảm ơn bạn đã đánh giá!',
                'submitError' => 'Có lỗi xảy ra. Vui lòng thử lại.',
                'loginRequired' => 'Bạn cần đăng nhập để đánh giá.',
                'fillRequired' => 'Vui lòng điền đầy đủ thông tin bắt buộc.',
                'rating' => 'Đánh giá',
                'stars' => 'sao'
            )
        ));

        ob_start();
        ?>
        <div id="<?php echo esc_attr($container_id); ?>" class="kata-user-interaction-container kata-style-<?php echo esc_attr($atts['style']); ?>" data-post-id="<?php echo esc_attr($post_id); ?>">
            
            <?php if (!empty($atts['title'])): ?>
            <h3 class="kata-interaction-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>

            <?php if ($atts['show_form'] === 'true'): ?>
            <!-- User Interaction Form -->
            <div class="kata-interaction-form-container">
                <?php echo $this->render_interaction_form($atts, $post_id); ?>
            </div>
            <?php endif; ?>

            <?php if ($atts['show_list'] === 'true'): ?>
            <!-- Existing Interactions List -->
            <div class="kata-interaction-list-container">
                <?php echo $this->render_interaction_list($atts, $post_id); ?>
            </div>
            <?php endif; ?>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render interaction form
     */
    private function render_interaction_form($atts, $post_id) {
        $require_login = ($atts['require_login'] === 'true');
        $is_logged_in = is_user_logged_in();
        
        if ($require_login && !$is_logged_in) {
            return '<p class="kata-login-required">Bạn cần <a href="' . wp_login_url(get_permalink()) . '">đăng nhập</a> để đánh giá.</p>';
        }

        // Get rating criteria
        $rating_criteria = $this->parse_rating_criteria($atts['rating_criteria']);
        
        ob_start();
        ?>
        <form class="kata-interaction-form" data-post-id="<?php echo esc_attr($post_id); ?>">
            
            <?php if (!$is_logged_in): ?>
            <!-- Guest user info -->
            <div class="kata-user-info">
                <div class="kata-form-row">
                    <div class="kata-form-group">
                        <label for="user_name">Họ tên <span class="required">*</span></label>
                        <input type="text" id="user_name" name="user_name" required>
                    </div>
                    <div class="kata-form-group">
                        <label for="user_email">Email <span class="required">*</span></label>
                        <input type="email" id="user_email" name="user_email" required>
                    </div>
                </div>
                <div class="kata-form-group">
                    <label for="user_website">Website</label>
                    <input type="url" id="user_website" name="user_website">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($atts['enable_reviews'] === 'true'): ?>
            <!-- Review Section -->
            <div class="kata-review-section">
                <h4>📝 Đánh giá chi tiết</h4>
                <div class="kata-form-group">
                    <label for="review_title">Tiêu đề đánh giá</label>
                    <input type="text" id="review_title" name="review_title" placeholder="Tóm tắt trải nghiệm của bạn...">
                </div>
                <div class="kata-form-group">
                    <label for="review_content">Nội dung đánh giá</label>
                    <textarea id="review_content" name="review_content" rows="4" placeholder="Chia sẻ chi tiết về trải nghiệm của bạn..."></textarea>
                </div>
                <div class="kata-form-row">
                    <div class="kata-form-group">
                        <label for="review_pros">Điểm tốt</label>
                        <textarea id="review_pros" name="review_pros" rows="2" placeholder="Những gì bạn thích..."></textarea>
                    </div>
                    <div class="kata-form-group">
                        <label for="review_cons">Điểm chưa tốt</label>
                        <textarea id="review_cons" name="review_cons" rows="2" placeholder="Những gì cần cải thiện..."></textarea>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($atts['enable_ratings'] === 'true'): ?>
            <!-- Rating Section -->
            <div class="kata-rating-section">
                <h4>⭐ Đánh giá bằng sao</h4>
                <?php foreach ($rating_criteria as $key => $criterion): ?>
                <div class="kata-rating-group">
                    <label><?php echo esc_html($criterion['label']); ?></label>
                    <div class="kata-star-rating" data-rating="<?php echo esc_attr($key); ?>">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="kata-star" data-value="<?php echo $i; ?>">⭐</span>
                        <?php endfor; ?>
                        <input type="hidden" name="<?php echo esc_attr($key); ?>_rating" value="0">
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if ($atts['enable_comments'] === 'true'): ?>
            <!-- Comment Section -->
            <div class="kata-comment-section">
                <h4>💬 Bình luận</h4>
                <div class="kata-form-group">
                    <label for="comment_content">Bình luận của bạn</label>
                    <textarea id="comment_content" name="comment_content" rows="3" placeholder="Để lại bình luận..."></textarea>
                </div>
            </div>
            <?php endif; ?>

            <div class="kata-form-actions">
                <button type="submit" class="kata-submit-btn">
                    <span class="kata-submit-text">Gửi đánh giá</span>
                    <span class="kata-submit-loading" style="display:none;">Đang gửi...</span>
                </button>
            </div>

            <input type="hidden" name="action" value="kata_submit_user_interaction">
            <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
            <input type="hidden" name="interaction_type" value="<?php echo esc_attr($atts['type']); ?>">
            <?php wp_nonce_field('kata_user_interaction_nonce', 'nonce'); ?>
        </form>
        <?php
        return ob_get_clean();
    }

    /**
     * Render interaction list
     */
    private function render_interaction_list($atts, $post_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_user_interactions';
        $per_page = intval($atts['items_per_page']);
        $page = isset($_GET['interaction_page']) ? max(1, intval($_GET['interaction_page'])) : 1;
        $offset = ($page - 1) * $per_page;

        // Get interactions
        $interactions = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM $table_name 
            WHERE post_id = %d AND status = 'approved' 
            ORDER BY created_at DESC 
            LIMIT %d OFFSET %d
        ", $post_id, $per_page, $offset));

        // Get total count for pagination
        $total = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $table_name 
            WHERE post_id = %d AND status = 'approved'
        ", $post_id));

        ob_start();
        ?>
        <div class="kata-interaction-list">
            
            <?php if ($interactions): ?>
            <div class="kata-interactions-summary">
                <h4>💭 Đánh giá từ cộng đồng (<?php echo intval($total); ?>)</h4>
                <?php echo $this->render_rating_summary($post_id); ?>
            </div>

            <div class="kata-interactions-items">
                <?php foreach ($interactions as $interaction): ?>
                <div class="kata-interaction-item" data-id="<?php echo esc_attr($interaction->id); ?>">
                    
                    <div class="kata-interaction-header">
                        <div class="kata-user-info">
                            <strong class="kata-user-name"><?php echo esc_html($interaction->user_name); ?></strong>
                            <span class="kata-interaction-date"><?php echo date('j/n/Y', strtotime($interaction->created_at)); ?></span>
                        </div>
                        <?php if ($interaction->overall_rating > 0): ?>
                        <div class="kata-interaction-rating">
                            <?php echo $this->render_stars($interaction->overall_rating); ?>
                            <span class="kata-rating-value"><?php echo number_format($interaction->overall_rating, 1); ?>/5</span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="kata-interaction-content">
                        <?php if ($interaction->review_title): ?>
                        <h5 class="kata-review-title"><?php echo esc_html($interaction->review_title); ?></h5>
                        <?php endif; ?>
                        
                        <?php if ($interaction->review_content): ?>
                        <div class="kata-review-content"><?php echo nl2br(esc_html($interaction->review_content)); ?></div>
                        <?php endif; ?>

                        <?php if ($interaction->review_pros || $interaction->review_cons): ?>
                        <div class="kata-review-proscons">
                            <?php if ($interaction->review_pros): ?>
                            <div class="kata-pros">
                                <strong>👍 Điểm tốt:</strong> <?php echo esc_html($interaction->review_pros); ?>
                            </div>
                            <?php endif; ?>
                            <?php if ($interaction->review_cons): ?>
                            <div class="kata-cons">
                                <strong>👎 Điểm chưa tốt:</strong> <?php echo esc_html($interaction->review_cons); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($interaction->comment_content): ?>
                        <div class="kata-comment-content"><?php echo nl2br(esc_html($interaction->comment_content)); ?></div>
                        <?php endif; ?>
                    </div>

                    <?php if ($atts['enable_replies'] === 'true'): ?>
                    <div class="kata-interaction-actions">
                        <button class="kata-reply-btn" data-parent-id="<?php echo esc_attr($interaction->id); ?>">Trả lời</button>
                    </div>
                    <?php endif; ?>

                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total > $per_page): ?>
            <div class="kata-pagination">
                <?php echo $this->render_pagination($total, $per_page, $page); ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="kata-no-interactions">
                <p>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá!</p>
            </div>
            <?php endif; ?>

        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Parse rating criteria
     */
    private function parse_rating_criteria($criteria_string) {
        $default_criteria = array(
            'overall' => array('label' => 'Tổng thể', 'enabled' => true),
            'quality' => array('label' => 'Chất lượng', 'enabled' => true),
            'value' => array('label' => 'Giá trị', 'enabled' => true),
            'service' => array('label' => 'Dịch vụ', 'enabled' => false)
        );

        if (empty($criteria_string)) {
            return array_filter($default_criteria, function($criterion) {
                return $criterion['enabled'];
            });
        }

        // Try to parse as JSON first
        $json_criteria = json_decode($criteria_string, true);
        if (is_array($json_criteria)) {
            return $json_criteria;
        }

        // Parse as comma-separated values
        $criteria = array();
        $items = explode(',', $criteria_string);
        foreach ($items as $item) {
            $item = trim($item);
            if (!empty($item)) {
                $key = sanitize_title($item);
                $criteria[$key] = array('label' => $item, 'enabled' => true);
            }
        }

        return $criteria ?: $default_criteria;
    }

    /**
     * Render rating summary
     */
    private function render_rating_summary($post_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_user_interactions';
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(*) as total_count,
                AVG(overall_rating) as avg_rating,
                COUNT(CASE WHEN overall_rating >= 4 THEN 1 END) as positive_count
            FROM $table_name 
            WHERE post_id = %d AND status = 'approved' AND overall_rating > 0
        ", $post_id));

        if (!$stats || $stats->total_count == 0) {
            return '';
        }

        $avg_rating = round($stats->avg_rating, 1);
        $positive_percentage = round(($stats->positive_count / $stats->total_count) * 100);

        ob_start();
        ?>
        <div class="kata-rating-summary">
            <div class="kata-avg-rating">
                <div class="kata-rating-display">
                    <span class="kata-rating-number"><?php echo $avg_rating; ?></span>
                    <div class="kata-rating-stars"><?php echo $this->render_stars($avg_rating); ?></div>
                    <span class="kata-rating-count">(<?php echo intval($stats->total_count); ?> đánh giá)</span>
                </div>
            </div>
            <div class="kata-rating-stats">
                <span class="kata-positive-rate"><?php echo $positive_percentage; ?>% hài lòng</span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render star rating display
     */
    private function render_stars($rating) {
        $rating = floatval($rating);
        $full_stars = floor($rating);
        $has_half = ($rating - $full_stars) >= 0.5;
        $empty_stars = 5 - $full_stars - ($has_half ? 1 : 0);

        $output = '';
        
        // Full stars
        for ($i = 0; $i < $full_stars; $i++) {
            $output .= '<span class="kata-star kata-star-full">⭐</span>';
        }
        
        // Half star
        if ($has_half) {
            $output .= '<span class="kata-star kata-star-half">⭐</span>';
        }
        
        // Empty stars
        for ($i = 0; $i < $empty_stars; $i++) {
            $output .= '<span class="kata-star kata-star-empty">☆</span>';
        }

        return $output;
    }

    /**
     * Render pagination
     */
    private function render_pagination($total, $per_page, $current_page) {
        $total_pages = ceil($total / $per_page);
        
        if ($total_pages <= 1) {
            return '';
        }

        $output = '<div class="kata-pagination-links">';
        
        for ($i = 1; $i <= $total_pages; $i++) {
            $class = ($i == $current_page) ? 'current' : '';
            $url = add_query_arg('interaction_page', $i);
            $output .= sprintf('<a href="%s" class="kata-page-link %s">%d</a>', esc_url($url), $class, $i);
        }
        
        $output .= '</div>';
        return $output;
    }
    /**
     * AJAX handler for submitting user interactions
     */
    public function ajax_submit_user_interaction() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_user_interaction_nonce')) {
            wp_die('Security check failed');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_user_interactions';

        // Get data from POST
        $post_id = intval($_POST['post_id']);
        $interaction_type = sanitize_text_field($_POST['interaction_type']);
        
        // User info
        $user_id = get_current_user_id();
        $user_name = $user_id ? wp_get_current_user()->display_name : sanitize_text_field($_POST['user_name']);
        $user_email = $user_id ? wp_get_current_user()->user_email : sanitize_email($_POST['user_email']);
        $user_website = isset($_POST['user_website']) ? esc_url_raw($_POST['user_website']) : '';

        // Review data
        $review_title = isset($_POST['review_title']) ? sanitize_text_field($_POST['review_title']) : '';
        $review_content = isset($_POST['review_content']) ? sanitize_textarea_field($_POST['review_content']) : '';
        $review_pros = isset($_POST['review_pros']) ? sanitize_textarea_field($_POST['review_pros']) : '';
        $review_cons = isset($_POST['review_cons']) ? sanitize_textarea_field($_POST['review_cons']) : '';

        // Rating data
        $overall_rating = isset($_POST['overall_rating']) ? floatval($_POST['overall_rating']) : 0;
        $quality_rating = isset($_POST['quality_rating']) ? floatval($_POST['quality_rating']) : 0;
        $value_rating = isset($_POST['value_rating']) ? floatval($_POST['value_rating']) : 0;
        $service_rating = isset($_POST['service_rating']) ? floatval($_POST['service_rating']) : 0;

        // Comment data
        $comment_content = isset($_POST['comment_content']) ? sanitize_textarea_field($_POST['comment_content']) : '';
        $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : null;

        // Validation
        if (!$post_id) {
            wp_send_json_error('ID bài viết không hợp lệ.');
        }

        if (!$user_id && (empty($user_name) || empty($user_email))) {
            wp_send_json_error('Vui lòng điền đầy đủ thông tin cá nhân.');
        }

        if (empty($review_content) && empty($comment_content) && $overall_rating == 0) {
            wp_send_json_error('Vui lòng điền ít nhất một nội dung đánh giá.');
        }

        // Prepare data for insertion
        $data = array(
            'post_id' => $post_id,
            'user_id' => $user_id ?: null,
            'user_name' => $user_name,
            'user_email' => $user_email,
            'user_website' => $user_website,
            'interaction_type' => $interaction_type,
            'review_title' => $review_title,
            'review_content' => $review_content,
            'review_pros' => $review_pros,
            'review_cons' => $review_cons,
            'overall_rating' => $overall_rating,
            'quality_rating' => $quality_rating,
            'value_rating' => $value_rating,
            'service_rating' => $service_rating,
            'comment_content' => $comment_content,
            'parent_id' => $parent_id,
            'status' => 'pending', // Default to pending for moderation
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        );

        // Insert into database
        $result = $wpdb->insert($table_name, $data);

        if ($result === false) {
            wp_send_json_error('Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại.');
        }

        // Get the inserted ID
        $interaction_id = $wpdb->insert_id;

        // Send success response
        wp_send_json_success(array(
            'message' => 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn sẽ được duyệt sớm.',
            'interaction_id' => $interaction_id,
            'status' => 'pending'
        ));
    }

    /**
     * AJAX handler for loading user interactions
     */
    public function ajax_load_user_interactions() {
        $post_id = intval($_POST['post_id']);
        $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
        $per_page = isset($_POST['per_page']) ? intval($_POST['per_page']) : 10;

        if (!$post_id) {
            wp_send_json_error('ID bài viết không hợp lệ.');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_user_interactions';
        $offset = ($page - 1) * $per_page;

        // Get interactions
        $interactions = $wpdb->get_results($wpdb->prepare("
            SELECT * FROM $table_name 
            WHERE post_id = %d AND status = 'approved' 
            ORDER BY created_at DESC 
            LIMIT %d OFFSET %d
        ", $post_id, $per_page, $offset));

        // Get total count
        $total = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM $table_name 
            WHERE post_id = %d AND status = 'approved'
        ", $post_id));

        // Format interactions for JSON response
        $formatted_interactions = array();
        foreach ($interactions as $interaction) {
            $formatted_interactions[] = array(
                'id' => $interaction->id,
                'user_name' => $interaction->user_name,
                'user_website' => $interaction->user_website,
                'review_title' => $interaction->review_title,
                'review_content' => $interaction->review_content,
                'review_pros' => $interaction->review_pros,
                'review_cons' => $interaction->review_cons,
                'overall_rating' => floatval($interaction->overall_rating),
                'quality_rating' => floatval($interaction->quality_rating),
                'value_rating' => floatval($interaction->value_rating),
                'service_rating' => floatval($interaction->service_rating),
                'comment_content' => $interaction->comment_content,
                'created_at' => $interaction->created_at,
                'is_verified' => intval($interaction->is_verified),
                'is_featured' => intval($interaction->is_featured)
            );
        }

        wp_send_json_success(array(
            'interactions' => $formatted_interactions,
            'total' => intval($total),
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ));
    }

    /**
     * Submit poll vote
     */
    public function ajax_submit_poll_vote() {
        $poll_id = intval($_POST['poll_id']);
        $option_value = intval($_POST['option_value']);
        
        if (!$poll_id || !isset($_POST['option_value'])) {
            wp_send_json_error('Dữ liệu không hợp lệ.');
        }
        
        global $wpdb;
        
        // Check if poll exists
        $poll = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_polls WHERE id = %d",
            $poll_id
        ));
        
        if (!$poll) {
            wp_send_json_error('Không tìm thấy cuộc bình chọn.');
        }
        
        // Check if user already voted
        $user_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
        $user_id = get_current_user_id();
        
        $existing_vote = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d AND (voter_user_id = %d OR voter_ip = %s)",
            $poll_id, $user_id, $user_ip
        ));
        
        if ($existing_vote > 0) {
            wp_send_json_error('Bạn đã bình chọn cho cuộc thăm dò này rồi.');
        }
        
        // Get option text from poll options
        $poll_options = json_decode($poll->poll_options, true);
        $option_text = isset($poll_options[$option_value]) ? $poll_options[$option_value] : '';
        
        // Insert vote
        $result = $wpdb->insert(
            $wpdb->prefix . 'kata_poll_votes',
            array(
                'poll_id' => $poll_id,
                'voter_user_id' => $user_id > 0 ? $user_id : null,
                'option_index' => $option_value,
                'option_text' => $option_text,
                'voter_ip' => $user_ip,
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown',
                'voted_at' => current_time('mysql')
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            wp_send_json_error('Không thể lưu phiếu bình chọn.');
        }
        
        // Update total votes count in poll table
        $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}kata_polls SET total_votes = (
                SELECT COUNT(*) FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d
            ) WHERE id = %d",
            $poll_id, $poll_id
        ));
        
        wp_send_json_success(array('message' => 'Cảm ơn bạn đã bình chọn!'));
    }
    
    /**
     * Get poll results
     */
    public function ajax_get_poll_results() {
        $poll_id = intval($_POST['poll_id']);
        
        if (!$poll_id) {
            wp_send_json_error('ID cuộc bình chọn không hợp lệ.');
        }
        
        global $wpdb;
        
        // Get poll details
        $poll = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_polls WHERE id = %d",
            $poll_id
        ));
        
        if (!$poll) {
            wp_send_json_error('Không tìm thấy cuộc bình chọn.');
        }
        
        $options = json_decode($poll->poll_options, true);
        
        // Get vote counts
        $vote_counts = $wpdb->get_results($wpdb->prepare(
            "SELECT option_index, COUNT(*) as count FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d GROUP BY option_index",
            $poll_id
        ), OBJECT_K);
        
        $total_votes = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}kata_poll_votes WHERE poll_id = %d",
            $poll_id
        ));
        
        $results = array();
        foreach ($options as $index => $option) {
            $votes = isset($vote_counts[$index]) ? $vote_counts[$index]->count : 0;
            $percentage = $total_votes > 0 ? round(($votes / $total_votes) * 100, 1) : 0;
            
            $results[] = array(
                'option' => $option,
                'votes' => intval($votes),
                'percentage' => $percentage
            );
        }
        
        wp_send_json_success(array(
            'results' => $results,
            'total_votes' => intval($total_votes)
        ));
    }

    /**
     * Get poll data for editing (AJAX handler)
     */
    public function ajax_get_poll_for_edit() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kata_poll_edit')) {
            wp_send_json_error('Nonce verification failed');
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access');
        }
        
        // Get poll ID
        $poll_id = isset($_POST['poll_id']) ? intval($_POST['poll_id']) : 0;
        if (!$poll_id) {
            wp_send_json_error('Invalid poll ID');
        }
        
        global $wpdb;
        
        // Get poll data
        $poll = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_polls WHERE id = %d",
            $poll_id
        ));
        
        if (!$poll) {
            wp_send_json_error('Poll not found');
        }
        
        // Parse options
        $options = array();
        if (!empty($poll->poll_options)) {
            $options = json_decode($poll->poll_options, true);
            if (!is_array($options)) {
                $options = array();
            }
        }
        
        // Return poll data
        wp_send_json_success(array(
            'poll' => $poll,
            'options' => $options
        ));
    }

    /**
     * TinyMCE admin head
     */
    public function tinymce_admin_head() {
        global $current_screen;
        if (isset($current_screen) && in_array($current_screen->base, array('post', 'page'))) {
            // Enqueue TinyMCE editor CSS file
            wp_enqueue_style(
                'kata-tinymce-editor',
                KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/tinymce-editor.css',
                array(),
                KATA_SEO_MANAGER_VERSION
            );
            
            ?>
            <style>
                /* Ensure TinyMCE toolbar is visible */
                .mce-toolbar-grp {
                    position: relative !important;
                    z-index: 10 !important;
                    visibility: visible !important;
                    display: block !important;
                }
                
                .mce-btn[aria-label*="KATA"] {
                    visibility: visible !important;
                    display: inline-block !important;
                }
                
                /* Fix potential conflicts */
                .kata-seo-manager .mce-toolbar {
                    position: relative !important;
                    top: auto !important;
                    left: auto !important;
                }
                
                /* Fix TinyMCE Editor Area Padding Issue - Additional Override */
                .mce-edit-area.mce-container.mce-panel.mce-stack-layout-item {
                    padding-top: 0 !important;
                }
                
                .mce-edit-area {
                    padding-top: 0 !important;
                }
                
                /* Additional TinyMCE container fixes */
                .mce-container.mce-panel.mce-stack-layout-item.mce-edit-area {
                    padding-top: 0 !important;
                    margin-top: 0 !important;
                }
                
                /* Ensure iframe content area is properly positioned */
                .mce-edit-area iframe {
                    padding-top: 0 !important;
                    margin-top: 0 !important;
                }
                
                /* Fix for specific TinyMCE layout classes - Force override of 74px padding */
                .mce-stack-layout-item.mce-edit-area,
                div[class*="mce-edit-area"] {
                    padding-top: 0 !important;
                }
            </style>
            <?php
        }
    }
    
    /**
     * Register TinyMCE button
     */
    public function register_tinymce_button($buttons) {
        // Only add buttons on post edit screens
        global $current_screen;
        if (isset($current_screen) && in_array($current_screen->base, array('post', 'page'))) {
            array_push($buttons, 'kata_seo_manager', 'kata_seo_quick');
        }
        return $buttons;
    }
    
    /**
     * Register TinyMCE plugin
     */
    public function register_tinymce_plugin($plugins) {
        global $current_screen;
        if (isset($current_screen) && in_array($current_screen->base, array('post', 'page'))) {
            $plugins['kata_seo_manager'] = KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/tinymce-plugin.js?v=' . KATA_SEO_MANAGER_VERSION;
        }
        return $plugins;
    }
    
    /**
     * AJAX: Get wheel data
     */
    public function ajax_get_wheel_data() {
        check_ajax_referer('kata_wheel_nonce', 'nonce');
        
        $wheel_id = intval($_POST['wheel_id']);
        global $wpdb;
        
        // Get wheel data
        $wheel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d AND status = 'active'",
            $wheel_id
        ));
        
        if (!$wheel) {
            wp_send_json_error(array('message' => 'Vòng quay không tồn tại hoặc đã hết hạn'));
        }
        
        // Get prizes
        $prizes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheel_prizes 
             WHERE wheel_id = %d AND is_active = 1 
             ORDER BY position_order ASC",
            $wheel_id
        ));
        
        wp_send_json_success(array(
            'wheel' => $wheel,
            'prizes' => $prizes
        ));
    }
    
    /**
     * AJAX: Check wheel eligibility
     */
    public function ajax_check_wheel_eligibility() {
        check_ajax_referer('kata_wheel_nonce', 'nonce');
        
        $wheel_id = intval($_POST['wheel_id']);
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $user_id = get_current_user_id();
        
        global $wpdb;
        
        // Get wheel config
        $wheel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d",
            $wheel_id
        ));
        
        if (!$wheel) {
            wp_send_json_error(array('message' => 'Vòng quay không tồn tại'));
        }
        
        // Check total spins per user
        $user_spins = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}kata_wheel_spins 
             WHERE wheel_id = %d AND user_ip = %s",
            $wheel_id,
            $user_ip
        ));
        
        if ($user_spins >= $wheel->max_spins_per_user) {
            wp_send_json_error(array(
                'message' => 'Bạn đã hết lượt quay cho vòng quay này',
                'remaining_spins' => 0
            ));
        }
        
        // Check spins today
        $today_spins = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}kata_wheel_spins 
             WHERE wheel_id = %d AND user_ip = %s AND DATE(spin_date) = CURDATE()",
            $wheel_id,
            $user_ip
        ));
        
        if ($today_spins >= $wheel->max_spins_per_day) {
            wp_send_json_error(array(
                'message' => 'Bạn đã hết lượt quay hôm nay',
                'remaining_spins' => 0
            ));
        }
        
        $remaining_spins = $wheel->max_spins_per_user - $user_spins;
        
        wp_send_json_success(array(
            'can_spin' => true,
            'remaining_spins' => $remaining_spins,
            'remaining_today' => $wheel->max_spins_per_day - $today_spins
        ));
    }
    
    /**
     * AJAX: Spin wheel
     */
    public function ajax_spin_wheel() {
        check_ajax_referer('kata_wheel_nonce', 'nonce');
        
        $wheel_id = intval($_POST['wheel_id']);
        $user_email = sanitize_email($_POST['user_email'] ?? '');
        $user_phone = sanitize_text_field($_POST['user_phone'] ?? '');
        $user_name = sanitize_text_field($_POST['user_name'] ?? '');
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        global $wpdb;
        
        // Get wheel
        $wheel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d AND status = 'active'",
            $wheel_id
        ));
        
        if (!$wheel) {
            wp_send_json_error(array('message' => 'Vòng quay không khả dụng'));
        }
        
        // Validate requirements
        if ($wheel->requirement === 'email' && empty($user_email)) {
            wp_send_json_error(array('message' => 'Vui lòng nhập email'));
        }
        if ($wheel->requirement === 'phone' && empty($user_phone)) {
            wp_send_json_error(array('message' => 'Vui lòng nhập số điện thoại'));
        }
        if ($wheel->requirement === 'both' && (empty($user_email) || empty($user_phone))) {
            wp_send_json_error(array('message' => 'Vui lòng nhập đầy đủ email và số điện thoại'));
        }
        
        // Check eligibility
        $user_spins = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}kata_wheel_spins 
             WHERE wheel_id = %d AND user_ip = %s",
            $wheel_id,
            $user_ip
        ));
        
        if ($user_spins >= $wheel->max_spins_per_user) {
            wp_send_json_error(array('message' => 'Bạn đã hết lượt quay'));
        }
        
        // Get available prizes
        $prizes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheel_prizes 
             WHERE wheel_id = %d AND is_active = 1 
             AND (total_available = -1 OR total_won < total_available)
             ORDER BY position_order ASC",
            $wheel_id
        ));
        
        if (empty($prizes)) {
            wp_send_json_error(array('message' => 'Không còn giải thưởng'));
        }
        
        // Select prize based on probability
        $selected_prize = $this->select_prize_by_probability($prizes);
        
        if (!$selected_prize) {
            wp_send_json_error(array('message' => 'Lỗi khi chọn giải thưởng'));
        }
        
        // Record spin
        $spin_data = array(
            'wheel_id' => $wheel_id,
            'prize_id' => $selected_prize->id,
            'user_email' => $user_email,
            'user_phone' => $user_phone,
            'user_name' => $user_name,
            'user_ip' => $user_ip,
            'user_agent' => $user_agent,
            'prize_text' => $selected_prize->prize_text,
            'prize_type' => $selected_prize->prize_type,
            'prize_value' => $selected_prize->prize_value,
            'spin_date' => current_time('mysql')
        );
        
        $wpdb->insert($wpdb->prefix . 'kata_wheel_spins', $spin_data);
        
        // Update wheel stats
        $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}kata_wheels 
             SET total_spins = total_spins + 1, 
                 total_prizes_won = total_prizes_won + 1 
             WHERE id = %d",
            $wheel_id
        ));
        
        // Update prize stats
        $wpdb->query($wpdb->prepare(
            "UPDATE {$wpdb->prefix}kata_wheel_prizes 
             SET total_won = total_won + 1 
             WHERE id = %d",
            $selected_prize->id
        ));
        
        // Update analytics
        $this->update_wheel_analytics($wheel_id);
        
        wp_send_json_success(array(
            'prize' => $selected_prize,
            'spin_id' => $wpdb->insert_id
        ));
    }
    
    /**
     * Select prize by probability
     */
    private function select_prize_by_probability($prizes) {
        $total_probability = 0;
        foreach ($prizes as $prize) {
            $total_probability += $prize->probability;
        }
        
        $random = mt_rand(1, $total_probability * 100) / 100;
        $cumulative = 0;
        
        foreach ($prizes as $prize) {
            $cumulative += $prize->probability;
            if ($random <= $cumulative) {
                return $prize;
            }
        }
        
        return $prizes[0]; // Fallback
    }
    
    /**
     * Update wheel analytics
     */
    private function update_wheel_analytics($wheel_id) {
        global $wpdb;
        
        $today = current_time('Y-m-d');
        
        // Check if record exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}kata_wheel_analytics 
             WHERE wheel_id = %d AND date = %s",
            $wheel_id,
            $today
        ));
        
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                COUNT(*) as total_spins,
                COUNT(DISTINCT user_ip) as unique_users,
                COUNT(CASE WHEN user_email != '' THEN 1 END) as emails_collected,
                COUNT(CASE WHEN user_phone != '' THEN 1 END) as phones_collected
             FROM {$wpdb->prefix}kata_wheel_spins 
             WHERE wheel_id = %d AND DATE(spin_date) = %s",
            $wheel_id,
            $today
        ));
        
        $data = array(
            'total_spins' => $stats->total_spins,
            'total_prizes_won' => $stats->total_spins,
            'total_emails_collected' => $stats->emails_collected,
            'total_phones_collected' => $stats->phones_collected,
            'unique_users' => $stats->unique_users,
            'avg_spins_per_user' => $stats->unique_users > 0 ? round($stats->total_spins / $stats->unique_users, 2) : 0
        );
        
        if ($exists) {
            $wpdb->update(
                $wpdb->prefix . 'kata_wheel_analytics',
                $data,
                array('wheel_id' => $wheel_id, 'date' => $today)
            );
        } else {
            $data['wheel_id'] = $wheel_id;
            $data['date'] = $today;
            $wpdb->insert($wpdb->prefix . 'kata_wheel_analytics', $data);
        }
    }
    
    /**
     * AJAX: Get wheel data for editing (admin only)
     */
    public function ajax_get_wheel_for_edit() {
        // Check nonce
        if (!check_ajax_referer('kata_wheel_edit', 'nonce', false)) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }
        
        // Check admin permission
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }
        
        $wheel_id = intval($_POST['wheel_id']);
        if (!$wheel_id) {
            wp_send_json_error(array('message' => 'Invalid wheel ID'));
        }
        
        global $wpdb;
        
        // Get wheel data
        $wheel = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d",
            $wheel_id
        ));
        
        if (!$wheel) {
            wp_send_json_error(array('message' => 'Vòng quay không tồn tại'));
        }
        
        // Get prizes
        $prizes = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_wheel_prizes 
             WHERE wheel_id = %d 
             ORDER BY position_order ASC",
            $wheel_id
        ));
        
        wp_send_json_success(array(
            'wheel' => $wheel,
            'prizes' => $prizes
        ));
    }
}

// Initialize plugin
function kata_seo_manager() {
    return KATA_SEO_Manager::get_instance();
}

// Start the plugin
kata_seo_manager();
