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
        
        // Editor integration
        add_action('media_buttons', array($this, 'add_schema_button'));
        add_action('admin_footer', array($this, 'add_schema_modal'));
        
        // Schema output
        add_action('wp_head', array($this, 'output_schema_markup'), 1);
        
        // AJAX handlers
        add_action('wp_ajax_kata_seo_create_schema', array($this, 'ajax_create_schema'));
        add_action('wp_ajax_nopriv_kata_seo_create_schema', array($this, 'ajax_create_schema'));
        add_action('wp_ajax_kata_seo_get_stats', array($this, 'ajax_get_stats'));
        add_action('wp_ajax_nopriv_kata_seo_get_stats', array($this, 'ajax_get_stats'));
        add_action('wp_ajax_kata_seo_validate_schema', array($this, 'ajax_validate_schema'));
        add_action('wp_ajax_nopriv_kata_seo_validate_schema', array($this, 'ajax_validate_schema'));
        
        // Shortcodes
        $this->register_shortcodes();
        
        // TinyMCE Integration
        add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        add_filter('mce_external_plugins', array($this, 'register_tinymce_plugin'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $database = new KATA_SEO_Database();
        $database->create_tables();
        
        // Set default options
        add_option('kata_seo_manager_version', KATA_SEO_MANAGER_VERSION);
        add_option('kata_seo_manager_settings', array(
            'enable_auto_schema' => true,
            'default_schema_types' => array('Article', 'Breadcrumb', 'WebPage'),
            'enable_editor_button' => true,
            'enable_statistics' => true
        ));
        
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
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
            __('Dashboard', 'kata-seo-manager'),
            __('Dashboard', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-manager',
            array($this, 'admin_dashboard_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Schema Types', 'kata-seo-manager'),
            __('Schema Types', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-schema-types',
            array($this, 'admin_schema_types_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Statistics', 'kata-seo-manager'),
            __('Statistics', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-statistics',
            array($this, 'admin_statistics_page')
        );
        
        add_submenu_page(
            'kata-seo-manager',
            __('Settings', 'kata-seo-manager'),
            __('Settings', 'kata-seo-manager'),
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
     * Settings page
     */
    public function admin_settings_page() {
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/settings.php';
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'kata-seo') === false && $hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        
        wp_enqueue_style(
            'kata-seo-manager-admin',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            KATA_SEO_MANAGER_VERSION
        );
        
        wp_enqueue_script(
            'kata-seo-manager-admin',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            KATA_SEO_MANAGER_VERSION,
            true
        );
        
        wp_localize_script('kata-seo-manager-admin', 'kataSEOManager', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_manager_nonce'),
            'schema_types' => $this->get_schema_types(),
            'strings' => array(
                'loading' => __('Loading...', 'kata-seo-manager'),
                'error' => __('An error occurred', 'kata-seo-manager'),
                'success' => __('Success!', 'kata-seo-manager')
            )
        ));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'kata-seo-manager-frontend',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            KATA_SEO_MANAGER_VERSION
        );
        
        wp_enqueue_script(
            'kata-seo-manager-frontend',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            KATA_SEO_MANAGER_VERSION,
            true
        );
        
        wp_localize_script('kata-seo-manager-frontend', 'kata_seo_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_manager_nonce')
        ));
    }
    
    /**
     * Add schema button to editor
     */
    public function add_schema_button() {
        echo '<button type="button" class="button kata-seo-insert-button" id="kata-seo-insert-schema">';
        echo '<span class="dashicons dashicons-editor-code" style="margin-top: 3px;"></span> ';
        echo __('KATA Insert Schema', 'kata-seo-manager');
        echo '</button>';
    }
    
    /**
     * Add schema modal
     */
    public function add_schema_modal() {
        // Make schema types available to modal
        $schema_types = $this->get_schema_types();
        include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/modal-schema.php';
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
            wp_send_json_error(array('message' => __('Security check failed', 'kata-seo-manager')));
            return;
        }
        
        // Capability check for logged-in users
        if (is_user_logged_in() && !current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'kata-seo-manager')));
            return;
        }
        
        $type = sanitize_text_field($_POST['type'] ?? '');
        $data = json_decode(stripslashes($_POST['data'] ?? '{}'), true);
        $post_id = intval($_POST['post_id'] ?? 0);
        
        if (empty($type) || empty($data)) {
            wp_send_json_error(array('message' => __('Invalid data provided', 'kata-seo-manager')));
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
    private function register_shortcodes() {
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
    }
    
    /**
     * Render shortcodes (examples)
     */
    public function render_article($atts) {
        // Will be implemented in individual schema classes
        return '';
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
    
    public function render_howto($atts) {
        return '';
    }
    
    public function render_event($atts) {
        return '';
    }
    
    public function render_recipe($atts) {
        return '';
    }
    
    public function render_product($atts) {
        return '';
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
            'title' => 'Quiz',
            'description' => '',
            'style' => 'default',
            'show_results' => 'true'
        ), $atts, 'kata_quiz');
        
        if (empty($content)) {
            return '<div class="kata-quiz-empty">Không có câu hỏi quiz nào được tìm thấy.</div>';
        }
        
        global $kata_quiz_questions;
        $kata_quiz_questions = array();
        
        do_shortcode($content);
        
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
            'title' => 'Bình Chọn',
            'description' => '',
            'style' => 'default',
            'show_results' => 'false'
        ), $atts, 'kata_poll');
        
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

    public function render_wheel($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Vòng Quay May Mắn',
            'options' => 'Giải 1,Giải 2,Giải 3,Chúc May Mắn Lần Sau',
            'colors' => '#FF6B6B,#4ECDC4,#45B7D1,#96CEB4',
            'size' => '300'
        ), $atts, 'kata_wheel');
        
        $wheel_id = 'kata-wheel-' . uniqid();
        $options = array_map('trim', explode(',', $atts['options']));
        $colors = array_map('trim', explode(',', $atts['colors']));
        
        $output = '<div id="' . $wheel_id . '" class="kata-wheel-container">';
        $output .= '<h3 class="kata-wheel-title">' . esc_html($atts['title']) . '</h3>';
        
        $output .= '<div class="kata-wheel-spinner" style="width:' . intval($atts['size']) . 'px;height:' . intval($atts['size']) . 'px;">';
        $output .= '<canvas id="' . $wheel_id . '-canvas" width="' . intval($atts['size']) . '" height="' . intval($atts['size']) . '"></canvas>';
        $output .= '<div class="kata-wheel-pointer"></div>';
        $output .= '</div>';
        
        $output .= '<button type="button" class="kata-wheel-spin" onclick="kataSpinWheel(\'' . $wheel_id . '\')">QUAY</button>';
        $output .= '<div class="kata-wheel-result" style="display:none;"></div>';
        
        $output .= '<script>';
        $output .= 'var ' . $wheel_id . '_options = ' . wp_json_encode($options) . ';';
        $output .= 'var ' . $wheel_id . '_colors = ' . wp_json_encode($colors) . ';';
        $output .= 'kataInitWheel("' . $wheel_id . '", ' . $wheel_id . '_options, ' . $wheel_id . '_colors);';
        $output .= '</script>';
        
        $output .= '</div>';
        
        return $output;
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
    
    /**
     * Register TinyMCE button
     */
    public function register_tinymce_button($buttons) {
        array_push($buttons, 'kata_seo_manager');
        return $buttons;
    }
    
    /**
     * Register TinyMCE plugin
     */
    public function register_tinymce_plugin($plugins) {
        $plugins['kata_seo_manager'] = KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/tinymce-plugin.js';
        return $plugins;
    }
}

// Initialize plugin
function kata_seo_manager() {
    return KATA_SEO_Manager::get_instance();
}

// Start the plugin
kata_seo_manager();
