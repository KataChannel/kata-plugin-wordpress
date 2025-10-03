<?php
/**
 * Plugin Name: Kata SEO Tools
 * Plugin URI: https://timona.vn/plugins/kata-seo-tools
 * Description: Plugin SEO toàn diện với Schema Markup, Social Share, Gamification, Interactive Features và nhiều hơn nữa
 * Version: 1.0.0
 * Author: Kata Team
 * Author URI: https://timona.vn
 * License: GPL v2 or later
 * Text Domain: kata-seo-tools
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('KATA_SEO_VERSION', '1.0.0');
define('KATA_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATA_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SEO_PLUGIN_FILE', __FILE__);

/**
 * Main Plugin Class
 */
class Kata_SEO_Tools {
    
    private static $instance = null;
    
    /**
     * Get singleton instance
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
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(KATA_SEO_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(KATA_SEO_PLUGIN_FILE, array($this, 'deactivate'));
        
        // Load plugin textdomain
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Load dependencies
        add_action('plugins_loaded', array($this, 'load_dependencies'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_post_meta'), 10, 2);
        
        // Register shortcodes
        add_action('init', array($this, 'register_shortcodes'));
        
        // Add schema markup to head
        add_action('wp_head', array($this, 'output_schema_markup'), 1);
        
        // Add social meta tags
        add_action('wp_head', array($this, 'output_social_meta_tags'), 2);
        
        // Register AJAX handlers
        $this->register_ajax_handlers();
        
        // Register custom post types
        add_action('init', array($this, 'register_custom_post_types'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add settings
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        $this->create_database_tables();
        $this->set_default_options();
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Quiz results table
        $table_quiz = $wpdb->prefix . 'kata_seo_quiz_results';
        $sql_quiz = "CREATE TABLE IF NOT EXISTS $table_quiz (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            quiz_id varchar(100) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            score int(11) NOT NULL,
            total_questions int(11) NOT NULL,
            answers longtext,
            completion_time int(11) DEFAULT NULL,
            ip_address varchar(100),
            user_agent text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY quiz_id (quiz_id),
            KEY user_id (user_id)
        ) $charset_collate;";
        dbDelta($sql_quiz);
        
        // Poll votes table
        $table_poll = $wpdb->prefix . 'kata_seo_poll_votes';
        $sql_poll = "CREATE TABLE IF NOT EXISTS $table_poll (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            poll_id varchar(100) NOT NULL,
            option_id int(11) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            ip_address varchar(100),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY poll_id (poll_id),
            UNIQUE KEY unique_vote (poll_id, ip_address, user_id)
        ) $charset_collate;";
        dbDelta($sql_poll);
        
        // Wheel spin results table
        $table_wheel = $wpdb->prefix . 'kata_seo_wheel_spins';
        $sql_wheel = "CREATE TABLE IF NOT EXISTS $table_wheel (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            wheel_id varchar(100) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            prize_id int(11) NOT NULL,
            prize_name varchar(255),
            ip_address varchar(100),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY wheel_id (wheel_id),
            KEY user_id (user_id),
            UNIQUE KEY unique_spin (wheel_id, ip_address, user_id)
        ) $charset_collate;";
        dbDelta($sql_wheel);
        
        // Ratings table
        $table_ratings = $wpdb->prefix . 'kata_seo_ratings';
        $sql_ratings = "CREATE TABLE IF NOT EXISTS $table_ratings (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            rating int(1) NOT NULL,
            review text,
            ip_address varchar(100),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY user_id (user_id),
            UNIQUE KEY unique_rating (post_id, user_id, ip_address)
        ) $charset_collate;";
        dbDelta($sql_ratings);
        
        // Form submissions table
        $table_forms = $wpdb->prefix . 'kata_seo_form_submissions';
        $sql_forms = "CREATE TABLE IF NOT EXISTS $table_forms (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            form_id varchar(100) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            form_data longtext,
            ip_address varchar(100),
            user_agent text,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY form_id (form_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta($sql_forms);
        
        // Social shares tracking table
        $table_shares = $wpdb->prefix . 'kata_seo_social_shares';
        $sql_shares = "CREATE TABLE IF NOT EXISTS $table_shares (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            platform varchar(50) NOT NULL,
            share_count int(11) DEFAULT 1,
            last_shared datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY platform (platform)
        ) $charset_collate;";
        dbDelta($sql_shares);
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = array(
            'kata_seo_schema_enabled' => 1,
            'kata_seo_social_share_enabled' => 1,
            'kata_seo_gamification_enabled' => 1,
            'kata_seo_ratings_enabled' => 1,
            'kata_seo_default_schema_type' => 'Article',
            'kata_seo_social_platforms' => array('facebook', 'twitter', 'linkedin'),
            'kata_seo_company_name' => get_bloginfo('name'),
            'kata_seo_company_logo' => get_site_icon_url()
        );
        
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                add_option($key, $value);
            }
        }
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('kata-seo-tools', false, dirname(plugin_basename(KATA_SEO_PLUGIN_FILE)) . '/languages');
    }
    
    /**
     * Load dependencies
     */
    public function load_dependencies() {
        // Core classes
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-schema-generator.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-social-share.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-quote-generator.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-quiz-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-wheel-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-poll-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-rating-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-faq-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-cta-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-form-handler.php';
        require_once KATA_SEO_PLUGIN_DIR . 'includes/class-admin.php';
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // CSS
        wp_enqueue_style('kata-seo-frontend', KATA_SEO_PLUGIN_URL . 'assets/css/frontend.css', array(), KATA_SEO_VERSION);
        
        // JavaScript
        wp_enqueue_script('kata-seo-frontend', KATA_SEO_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), KATA_SEO_VERSION, true);
        
        // Localize script
        wp_localize_script('kata-seo-frontend', 'kataSEO', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_nonce'),
            'post_id' => get_the_ID(),
            'strings' => array(
                'loading' => __('Đang tải...', 'kata-seo-tools'),
                'error' => __('Có lỗi xảy ra. Vui lòng thử lại.', 'kata-seo-tools'),
                'success' => __('Thành công!', 'kata-seo-tools')
            )
        ));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            wp_enqueue_style('kata-seo-admin', KATA_SEO_PLUGIN_URL . 'assets/css/admin.css', array(), KATA_SEO_VERSION);
            wp_enqueue_script('kata-seo-admin', KATA_SEO_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), KATA_SEO_VERSION, true);
            
            wp_localize_script('kata-seo-admin', 'kataSEOAdmin', array(
                'nonce' => wp_create_nonce('kata_seo_admin_nonce')
            ));
        }
    }
    
    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode('kata_quiz', array($this, 'quiz_shortcode'));
        add_shortcode('kata_poll', array($this, 'poll_shortcode'));
        add_shortcode('kata_wheel', array($this, 'wheel_shortcode'));
        add_shortcode('kata_rating', array($this, 'rating_shortcode'));
        add_shortcode('kata_faq', array($this, 'faq_shortcode'));
        add_shortcode('kata_quote', array($this, 'quote_shortcode'));
        add_shortcode('kata_social_share', array($this, 'social_share_shortcode'));
        add_shortcode('kata_cta', array($this, 'cta_shortcode'));
        add_shortcode('kata_form', array($this, 'form_shortcode'));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        $post_types = array('post', 'page');
        
        foreach ($post_types as $post_type) {
            // Schema markup meta box
            add_meta_box(
                'kata_seo_schema',
                __('Schema Markup', 'kata-seo-tools'),
                array($this, 'render_schema_meta_box'),
                $post_type,
                'side',
                'high'
            );
            
            // Social share meta box
            add_meta_box(
                'kata_seo_social',
                __('Social Sharing', 'kata-seo-tools'),
                array($this, 'render_social_meta_box'),
                $post_type,
                'normal',
                'high'
            );
            
            // FAQ meta box
            add_meta_box(
                'kata_seo_faq',
                __('FAQ Schema', 'kata-seo-tools'),
                array($this, 'render_faq_meta_box'),
                $post_type,
                'normal',
                'default'
            );
        }
    }
    
    /**
     * Render schema meta box
     */
    public function render_schema_meta_box($post) {
        wp_nonce_field('kata_seo_schema_meta', 'kata_seo_schema_nonce');
        
        $schema_type = get_post_meta($post->ID, '_kata_seo_schema_type', true) ?: 'Article';
        $schema_enabled = get_post_meta($post->ID, '_kata_seo_schema_enabled', true) !== '0';
        
        include KATA_SEO_PLUGIN_DIR . 'templates/admin/schema-meta-box.php';
    }
    
    /**
     * Render social meta box
     */
    public function render_social_meta_box($post) {
        wp_nonce_field('kata_seo_social_meta', 'kata_seo_social_nonce');
        
        $og_title = get_post_meta($post->ID, '_kata_seo_og_title', true);
        $og_description = get_post_meta($post->ID, '_kata_seo_og_description', true);
        $og_image = get_post_meta($post->ID, '_kata_seo_og_image', true);
        $twitter_card = get_post_meta($post->ID, '_kata_seo_twitter_card', true) ?: 'summary_large_image';
        
        include KATA_SEO_PLUGIN_DIR . 'templates/admin/social-meta-box.php';
    }
    
    /**
     * Render FAQ meta box
     */
    public function render_faq_meta_box($post) {
        wp_nonce_field('kata_seo_faq_meta', 'kata_seo_faq_nonce');
        
        $faqs = get_post_meta($post->ID, '_kata_seo_faqs', true) ?: array();
        
        include KATA_SEO_PLUGIN_DIR . 'templates/admin/faq-meta-box.php';
    }
    
    /**
     * Save post meta
     */
    public function save_post_meta($post_id, $post) {
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Save schema meta
        if (isset($_POST['kata_seo_schema_nonce']) && wp_verify_nonce($_POST['kata_seo_schema_nonce'], 'kata_seo_schema_meta')) {
            if (isset($_POST['kata_seo_schema_type'])) {
                update_post_meta($post_id, '_kata_seo_schema_type', sanitize_text_field($_POST['kata_seo_schema_type']));
            }
            
            $schema_enabled = isset($_POST['kata_seo_schema_enabled']) ? '1' : '0';
            update_post_meta($post_id, '_kata_seo_schema_enabled', $schema_enabled);
        }
        
        // Save social meta
        if (isset($_POST['kata_seo_social_nonce']) && wp_verify_nonce($_POST['kata_seo_social_nonce'], 'kata_seo_social_meta')) {
            $fields = array('og_title', 'og_description', 'og_image', 'twitter_card');
            foreach ($fields as $field) {
                if (isset($_POST["kata_seo_$field"])) {
                    update_post_meta($post_id, "_kata_seo_$field", sanitize_text_field($_POST["kata_seo_$field"]));
                }
            }
        }
        
        // Save FAQ meta
        if (isset($_POST['kata_seo_faq_nonce']) && wp_verify_nonce($_POST['kata_seo_faq_nonce'], 'kata_seo_faq_meta')) {
            if (isset($_POST['kata_seo_faqs'])) {
                $faqs = array();
                foreach ($_POST['kata_seo_faqs'] as $faq) {
                    if (!empty($faq['question']) && !empty($faq['answer'])) {
                        $faqs[] = array(
                            'question' => sanitize_text_field($faq['question']),
                            'answer' => wp_kses_post($faq['answer'])
                        );
                    }
                }
                update_post_meta($post_id, '_kata_seo_faqs', $faqs);
            }
        }
    }
    
    /**
     * Output schema markup
     */
    public function output_schema_markup() {
        if (is_singular(array('post', 'page'))) {
            $schema_enabled = get_post_meta(get_the_ID(), '_kata_seo_schema_enabled', true);
            if ($schema_enabled !== '0') {
                $schema_generator = new Kata_SEO_Schema_Generator();
                echo $schema_generator->generate_schema(get_the_ID());
            }
        }
    }
    
    /**
     * Output social meta tags
     */
    public function output_social_meta_tags() {
        if (is_singular(array('post', 'page'))) {
            $social_share = new Kata_SEO_Social_Share();
            echo $social_share->generate_meta_tags(get_the_ID());
        }
    }
    
    /**
     * Register AJAX handlers
     */
    private function register_ajax_handlers() {
        // Quiz
        add_action('wp_ajax_kata_seo_submit_quiz', array($this, 'ajax_submit_quiz'));
        add_action('wp_ajax_nopriv_kata_seo_submit_quiz', array($this, 'ajax_submit_quiz'));
        
        // Poll
        add_action('wp_ajax_kata_seo_submit_poll', array($this, 'ajax_submit_poll'));
        add_action('wp_ajax_nopriv_kata_seo_submit_poll', array($this, 'ajax_submit_poll'));
        
        // Wheel
        add_action('wp_ajax_kata_seo_spin_wheel', array($this, 'ajax_spin_wheel'));
        add_action('wp_ajax_nopriv_kata_seo_spin_wheel', array($this, 'ajax_spin_wheel'));
        
        // Rating
        add_action('wp_ajax_kata_seo_submit_rating', array($this, 'ajax_submit_rating'));
        add_action('wp_ajax_nopriv_kata_seo_submit_rating', array($this, 'ajax_submit_rating'));
        
        // Form
        add_action('wp_ajax_kata_seo_submit_form', array($this, 'ajax_submit_form'));
        add_action('wp_ajax_nopriv_kata_seo_submit_form', array($this, 'ajax_submit_form'));
        
        // Social share tracking
        add_action('wp_ajax_kata_seo_track_share', array($this, 'ajax_track_share'));
        add_action('wp_ajax_nopriv_kata_seo_track_share', array($this, 'ajax_track_share'));
    }
    
    /**
     * AJAX: Submit quiz
     */
    public function ajax_submit_quiz() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $quiz_handler = new Kata_SEO_Quiz_Handler();
        $result = $quiz_handler->submit_quiz($_POST);
        
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Không thể gửi kết quả quiz', 'kata-seo-tools'));
        }
    }
    
    /**
     * AJAX: Submit poll
     */
    public function ajax_submit_poll() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $poll_handler = new Kata_SEO_Poll_Handler();
        $result = $poll_handler->submit_vote($_POST);
        
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Không thể gửi vote', 'kata-seo-tools'));
        }
    }
    
    /**
     * AJAX: Spin wheel
     */
    public function ajax_spin_wheel() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $wheel_handler = new Kata_SEO_Wheel_Handler();
        $result = $wheel_handler->spin_wheel($_POST);
        
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Không thể quay thưởng', 'kata-seo-tools'));
        }
    }
    
    /**
     * AJAX: Submit rating
     */
    public function ajax_submit_rating() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $rating_handler = new Kata_SEO_Rating_Handler();
        $result = $rating_handler->submit_rating($_POST);
        
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Không thể gửi đánh giá', 'kata-seo-tools'));
        }
    }
    
    /**
     * AJAX: Submit form
     */
    public function ajax_submit_form() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $form_handler = new Kata_SEO_Form_Handler();
        $result = $form_handler->submit_form($_POST);
        
        if ($result) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error(__('Không thể gửi form', 'kata-seo-tools'));
        }
    }
    
    /**
     * AJAX: Track social share
     */
    public function ajax_track_share() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $social_share = new Kata_SEO_Social_Share();
        $result = $social_share->track_share($_POST);
        
        wp_send_json_success($result);
    }
    
    /**
     * Register custom post types
     */
    public function register_custom_post_types() {
        // You can add custom post types here if needed
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Kata SEO Tools', 'kata-seo-tools'),
            __('Kata SEO', 'kata-seo-tools'),
            'manage_options',
            'kata-seo-tools',
            array($this, 'render_admin_page'),
            'dashicons-chart-line',
            30
        );
        
        add_submenu_page(
            'kata-seo-tools',
            __('Settings', 'kata-seo-tools'),
            __('Settings', 'kata-seo-tools'),
            'manage_options',
            'kata-seo-settings',
            array($this, 'render_settings_page')
        );
        
        add_submenu_page(
            'kata-seo-tools',
            __('Analytics', 'kata-seo-tools'),
            __('Analytics', 'kata-seo-tools'),
            'manage_options',
            'kata-seo-analytics',
            array($this, 'render_analytics_page')
        );
    }
    
    /**
     * Render admin page
     */
    public function render_admin_page() {
        include KATA_SEO_PLUGIN_DIR . 'templates/admin/dashboard.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        include KATA_SEO_PLUGIN_DIR . 'templates/admin/settings.php';
    }
    
    /**
     * Render analytics page
     */
    public function render_analytics_page() {
        include KATA_SEO_PLUGIN_DIR . 'templates/admin/analytics.php';
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('kata_seo_options', 'kata_seo_schema_enabled');
        register_setting('kata_seo_options', 'kata_seo_social_share_enabled');
        register_setting('kata_seo_options', 'kata_seo_gamification_enabled');
        register_setting('kata_seo_options', 'kata_seo_ratings_enabled');
        register_setting('kata_seo_options', 'kata_seo_default_schema_type');
        register_setting('kata_seo_options', 'kata_seo_social_platforms');
        register_setting('kata_seo_options', 'kata_seo_company_name');
        register_setting('kata_seo_options', 'kata_seo_company_logo');
    }
    
    /**
     * Shortcode handlers
     */
    public function quiz_shortcode($atts) {
        $quiz_handler = new Kata_SEO_Quiz_Handler();
        return $quiz_handler->render($atts);
    }
    
    public function poll_shortcode($atts) {
        $poll_handler = new Kata_SEO_Poll_Handler();
        return $poll_handler->render($atts);
    }
    
    public function wheel_shortcode($atts) {
        $wheel_handler = new Kata_SEO_Wheel_Handler();
        return $wheel_handler->render($atts);
    }
    
    public function rating_shortcode($atts) {
        $rating_handler = new Kata_SEO_Rating_Handler();
        return $rating_handler->render($atts);
    }
    
    public function faq_shortcode($atts) {
        $faq_handler = new Kata_SEO_FAQ_Handler();
        return $faq_handler->render($atts);
    }
    
    public function quote_shortcode($atts, $content = null) {
        $quote_generator = new Kata_SEO_Quote_Generator();
        return $quote_generator->render($atts, $content);
    }
    
    public function social_share_shortcode($atts) {
        $social_share = new Kata_SEO_Social_Share();
        return $social_share->render($atts);
    }
    
    public function cta_shortcode($atts, $content = null) {
        $cta_handler = new Kata_SEO_CTA_Handler();
        return $cta_handler->render($atts, $content);
    }
    
    public function form_shortcode($atts) {
        $form_handler = new Kata_SEO_Form_Handler();
        return $form_handler->render($atts);
    }
}

/**
 * Initialize the plugin
 */
function kata_seo_tools_init() {
    return Kata_SEO_Tools::get_instance();
}

// Start the plugin
add_action('plugins_loaded', 'kata_seo_tools_init', 10);
