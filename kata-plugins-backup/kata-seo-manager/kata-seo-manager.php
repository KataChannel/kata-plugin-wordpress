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
        
        // Admin notices
        add_action('admin_notices', array($this, 'show_admin_notices'));
        
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
        
        // Enqueue TinyMCE editor styles
        wp_enqueue_style(
            'kata-seo-manager-tinymce',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/tinymce-editor.css',
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
     * Register TinyMCE button
     */
    public function register_tinymce_button($buttons) {
        array_push($buttons, 'kata_seo_manager', 'kata_seo_quick');
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
