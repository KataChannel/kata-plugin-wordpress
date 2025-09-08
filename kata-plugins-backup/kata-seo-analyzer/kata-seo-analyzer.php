<?php
/**
 * Plugin Name: Kata SEO Analyzer Pro
 * Plugin URI: https://timona.edu.vn/kata-seo
 * Description: Phân tích và tối ưu SEO nâng cao với AI, competitor analysis, real-time monitoring và báo cáo chi tiết. Phiên bản nâng cấp từ Timona SEO Analyzer.
 * Version: 2.0.0
 * Author: Kata Development Team
 * Author URI: https://timona.edu.vn
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: kata-seo-analyzer
 * Domain Path: /languages
 * 
 * Requires at least: 5.5
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 * 
 * @package KataSEOAnalyzer
 * @version 2.0.0
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

// Define plugin constants
define('KATA_SEO_VERSION', '2.0.0');
define('KATA_SEO_PLUGIN_FILE', __FILE__);
define('KATA_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SEO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KATA_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATA_SEO_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('KATA_SEO_DB_VERSION', '2.0');

// Check PHP version compatibility
if (version_compare(PHP_VERSION, '7.4', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="notice notice-error"><p>';
        echo sprintf(
            esc_html__('Kata SEO Analyzer requires PHP version 7.4 or higher. You are running version %s.', 'kata-seo-analyzer'),
            PHP_VERSION
        );
        echo '</p></div>';
    });
    return;
}

// Main plugin class
class KataSEOAnalyzer {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Plugin modules
     */
    public $admin;
    public $analyzer;
    public $api;
    public $competitor;
    public $monitoring;
    public $ai_suggestions;
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
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
        // Load plugin text domain
        add_action('plugins_loaded', array($this, 'loadTextDomain'));
        
        // Initialize components
        add_action('init', array($this, 'initComponents'));
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Admin hooks
        if (is_admin()) {
            add_action('admin_menu', array($this, 'addAdminMenu'));
            add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
            add_action('admin_init', array($this, 'adminInit'));
        }
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueueFrontendScripts'));
        
        // AJAX hooks
        $this->initAjaxHooks();
        
        // REST API
        add_action('rest_api_init', array($this, 'initRestAPI'));
        
        // Cron jobs
        add_action('kata_seo_daily_analysis', array($this, 'dailyAnalysis'));
        add_action('kata_seo_competitor_check', array($this, 'competitorCheck'));
    }
    
    /**
     * Load text domain for translations
     */
    public function loadTextDomain() {
        load_plugin_textdomain(
            'kata-seo-analyzer',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Initialize plugin components
     */
    public function initComponents() {
        // Load dependencies
        $this->loadDependencies();
        
        // Initialize modules that exist using singleton pattern
        if (class_exists('KataSEO_Admin')) {
            $this->admin = KataSEO_Admin::get_instance();
        }
        
        if (class_exists('KataSEO_Analyzer')) {
            $this->analyzer = KataSEO_Analyzer::get_instance();
        }
        
        if (class_exists('KataSEO_AI_Suggestions')) {
            $this->ai_suggestions = KataSEO_AI_Suggestions::get_instance();
        }
        
        if (class_exists('KataSEO_Competitor_Analyzer')) {
            $this->competitor = new KataSEO_Competitor_Analyzer();
        }
        
        // Initialize optional modules if classes exist
        if (class_exists('KataSEO_API')) {
            $this->api = new KataSEO_API();
        }
        
        if (class_exists('KataSEO_Monitoring')) {
            $this->monitoring = new KataSEO_Monitoring();
        }
    }
    
    /**
     * Load plugin dependencies
     */
    private function loadDependencies() {
        // Core classes that exist
        require_once KATA_SEO_PLUGIN_PATH . 'includes/admin/class-admin.php';
        require_once KATA_SEO_PLUGIN_PATH . 'includes/analyzer/class-analyzer.php';
        require_once KATA_SEO_PLUGIN_PATH . 'includes/analyzer/class-competitor-analyzer.php';
        require_once KATA_SEO_PLUGIN_PATH . 'includes/class-ai-suggestions.php';
        
        // Load additional classes if they exist
        $optional_files = array(
            'includes/analyzer/class-content-analyzer.php',
            'includes/analyzer/class-technical-analyzer.php',
            'includes/api/class-api.php',
            'includes/api/class-google-api.php',
            'includes/api/class-openai-api.php',
            'includes/class-monitoring.php',
            'includes/class-database.php',
            'includes/class-utils.php'
        );
        
        foreach ($optional_files as $file) {
            $file_path = KATA_SEO_PLUGIN_PATH . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        // Only add menu if admin object exists
        if (!$this->admin) {
            return;
        }
        
        // Main menu
        add_menu_page(
            __('Kata SEO Analyzer', 'kata-seo-analyzer'),
            __('Kata SEO', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-analyzer',
            array($this->admin, 'dashboardPage'),
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'),
            25
        );
        
        // Sub-menus
        add_submenu_page(
            'kata-seo-analyzer',
            __('Dashboard', 'kata-seo-analyzer'),
            __('Dashboard', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-analyzer',
            array($this->admin, 'dashboardPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('Content Analysis', 'kata-seo-analyzer'),
            __('Content Analysis', 'kata-seo-analyzer'),
            'edit_posts',
            'kata-seo-content',
            array($this->admin, 'contentAnalysisPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('Competitor Analysis', 'kata-seo-analyzer'),
            __('Competitor Analysis', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-competitor',
            array($this->admin, 'competitorAnalysisPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('Technical SEO', 'kata-seo-analyzer'),
            __('Technical SEO', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-technical',
            array($this->admin, 'technicalSEOPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('AI Suggestions', 'kata-seo-analyzer'),
            __('AI Suggestions', 'kata-seo-analyzer'),
            'edit_posts',
            'kata-seo-ai',
            array($this->admin, 'aiSuggestionsPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('Reports', 'kata-seo-analyzer'),
            __('Reports', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-reports',
            array($this->admin, 'reportsPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('Monitoring', 'kata-seo-analyzer'),
            __('Monitoring', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-monitoring',
            array($this->admin, 'monitoringPage')
        );
        
        add_submenu_page(
            'kata-seo-analyzer',
            __('Settings', 'kata-seo-analyzer'),
            __('Settings', 'kata-seo-analyzer'),
            'manage_options',
            'kata-seo-settings',
            array($this->admin, 'settingsPage')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueueAdminScripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'kata-seo') === false) {
            return;
        }
        
        // Styles
        wp_enqueue_style(
            'kata-seo-admin',
            KATA_SEO_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            KATA_SEO_VERSION
        );
        
        wp_enqueue_style(
            'kata-seo-charts',
            KATA_SEO_PLUGIN_URL . 'assets/css/charts.css',
            array(),
            KATA_SEO_VERSION
        );
        
        // Scripts
        wp_enqueue_script(
            'kata-seo-admin',
            KATA_SEO_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            KATA_SEO_VERSION,
            true
        );
        
        wp_enqueue_script(
            'kata-seo-charts',
            KATA_SEO_PLUGIN_URL . 'assets/js/charts.min.js',
            array('jquery'),
            KATA_SEO_VERSION,
            true
        );
        
        wp_enqueue_script(
            'kata-seo-analyzer',
            KATA_SEO_PLUGIN_URL . 'assets/js/analyzer.js',
            array('jquery', 'kata-seo-admin'),
            KATA_SEO_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('kata-seo-admin', 'kataSEO', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'resturl' => rest_url('kata-seo/v1/'),
            'nonce' => wp_create_nonce('kata_seo_nonce'),
            'rest_nonce' => wp_create_nonce('wp_rest'),
            'strings' => array(
                'analyzing' => __('Analyzing...', 'kata-seo-analyzer'),
                'success' => __('Success!', 'kata-seo-analyzer'),
                'error' => __('Error occurred', 'kata-seo-analyzer'),
                'confirm_delete' => __('Are you sure you want to delete this?', 'kata-seo-analyzer'),
                'loading' => __('Loading...', 'kata-seo-analyzer'),
                'no_data' => __('No data available', 'kata-seo-analyzer'),
            )
        ));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueueFrontendScripts() {
        if (is_admin() || !is_singular()) {
            return;
        }
        
        wp_enqueue_style(
            'kata-seo-frontend',
            KATA_SEO_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            KATA_SEO_VERSION
        );
        
        wp_enqueue_script(
            'kata-seo-frontend',
            KATA_SEO_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            KATA_SEO_VERSION,
            true
        );
    }
    
    /**
     * Initialize admin
     */
    public function adminInit() {
        // Register settings
        $this->registerSettings();
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'addMetaBoxes'));
        
        // Save post hooks
        add_action('save_post', array($this, 'savePostMeta'), 10, 2);
        
        // Add custom columns
        add_filter('manage_posts_columns', array($this, 'addSEOColumns'));
        add_filter('manage_pages_columns', array($this, 'addSEOColumns'));
        add_action('manage_posts_custom_column', array($this, 'displaySEOColumns'), 10, 2);
        add_action('manage_pages_custom_column', array($this, 'displaySEOColumns'), 10, 2);
    }
    
    /**
     * Initialize AJAX hooks
     */
    private function initAjaxHooks() {
        $ajax_actions = array(
            'analyze_content',
            'analyze_competitor',
            'get_ai_suggestions',
            'save_settings',
            'get_monitoring_data',
            'export_report',
            'bulk_analyze',
            'get_keyword_suggestions',
            'check_site_speed',
            'analyze_backlinks'
        );
        
        foreach ($ajax_actions as $action) {
            add_action("wp_ajax_kata_seo_{$action}", array($this, 'ajax_' . $action));
        }
    }
    
    /**
     * Initialize REST API
     */
    public function initRestAPI() {
        if ($this->api && method_exists($this->api, 'register_routes')) {
            $this->api->register_routes();
        }
    }
    
    /**
     * Register plugin settings
     */
    private function registerSettings() {
        register_setting('kata_seo_settings', 'kata_seo_options', array(
            'sanitize_callback' => array($this, 'sanitizeSettings')
        ));
    }
    
    /**
     * Add meta boxes
     */
    public function addMetaBoxes() {
        // Only add metaboxes if admin object exists
        if (!$this->admin) {
            return;
        }
        
        $post_types = get_post_types(array('public' => true));
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'kata-seo-analysis',
                __('Kata SEO Analysis', 'kata-seo-analyzer'),
                array($this->admin, 'seoAnalysisMetaBox'),
                $post_type,
                'side',
                'high'
            );
            
            add_meta_box(
                'kata-seo-ai-suggestions',
                __('AI SEO Suggestions', 'kata-seo-analyzer'),
                array($this->admin, 'aiSuggestionsMetaBox'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Add SEO columns to post lists
     */
    public function addSEOColumns($columns) {
        $new_columns = array();
        foreach ($columns as $key => $title) {
            $new_columns[$key] = $title;
            if ($key === 'title') {
                $new_columns['kata_seo_score'] = __('SEO Score', 'kata-seo-analyzer');
                $new_columns['kata_seo_issues'] = __('Issues', 'kata-seo-analyzer');
            }
        }
        return $new_columns;
    }
    
    /**
     * Display SEO columns content
     */
    public function displaySEOColumns($column, $post_id) {
        if ($column === 'kata_seo_score') {
            $score = $this->analyzer->calculateSEOScore($post_id);
            $color = $this->getScoreColor($score);
            echo '<span style="color: ' . esc_attr($color) . '; font-weight: bold;">' . 
                 esc_html($score) . '/100</span>';
        } elseif ($column === 'kata_seo_issues') {
            $issues = $this->analyzer->getTopIssues($post_id, 2);
            if (!empty($issues)) {
                echo '<span class="kata-seo-issues">' . 
                     esc_html(implode(', ', $issues)) . '</span>';
            } else {
                echo '<span style="color: #46b450;">✓</span>';
            }
        }
    }
    
    /**
     * Save post meta
     */
    public function savePostMeta($post_id, $post) {
        // Skip autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Prevent infinite loops - don't auto-analyze if we're already in an analysis
        if (defined('KATA_SEO_ANALYZING') && KATA_SEO_ANALYZING) {
            return;
        }
        
        // Auto-analyze on save (scheduled to avoid recursion)
        if (get_option('kata_seo_auto_analyze', true)) {
            wp_schedule_single_event(time() + 5, 'kata_seo_analyze_post', array($post_id));
        }
    }
    
    /**
     * Get color for SEO score
     */
    private function getScoreColor($score) {
        if ($score >= 90) return '#0073aa'; // Excellent - Blue
        if ($score >= 80) return '#46b450'; // Good - Green  
        if ($score >= 60) return '#ffb900'; // Fair - Orange
        if ($score >= 40) return '#f56e28'; // Poor - Red-Orange
        return '#dc3232'; // Very Poor - Red
    }
    
    /**
     * Sanitize settings
     */
    public function sanitizeSettings($input) {
        $sanitized = array();
        
        // Boolean settings
        $boolean_settings = array(
            'auto_analyze',
            'enable_ai_suggestions',
            'enable_monitoring',
            'enable_competitor_tracking'
        );
        
        foreach ($boolean_settings as $setting) {
            $sanitized[$setting] = !empty($input[$setting]);
        }
        
        // Text settings
        if (isset($input['google_api_key'])) {
            $sanitized['google_api_key'] = sanitize_text_field($input['google_api_key']);
        }
        
        if (isset($input['openai_api_key'])) {
            $sanitized['openai_api_key'] = sanitize_text_field($input['openai_api_key']);
        }
        
        return $sanitized;
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables
        $this->createTables();
        
        // Set default options
        $this->setDefaultOptions();
        
        // Schedule cron jobs
        $this->scheduleCronJobs();
        
        // Create upload directories
        $this->createUploadDirectories();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear scheduled cron jobs
        wp_clear_scheduled_hook('kata_seo_daily_analysis');
        wp_clear_scheduled_hook('kata_seo_competitor_check');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create database tables
     */
    private function createTables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // SEO analysis results table
        $table_analysis = $wpdb->prefix . 'kata_seo_analysis';
        $sql_analysis = "CREATE TABLE $table_analysis (
            id int(11) NOT NULL AUTO_INCREMENT,
            post_id int(11) NOT NULL,
            analysis_type varchar(50) NOT NULL,
            score int(3) NOT NULL,
            results longtext NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY post_id (post_id),
            KEY analysis_type (analysis_type),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Competitor tracking table
        $table_competitors = $wpdb->prefix . 'kata_seo_competitors';
        $sql_competitors = "CREATE TABLE $table_competitors (
            id int(11) NOT NULL AUTO_INCREMENT,
            domain varchar(255) NOT NULL,
            keywords text NOT NULL,
            data longtext NOT NULL,
            last_checked datetime DEFAULT CURRENT_TIMESTAMP,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY domain (domain),
            KEY last_checked (last_checked)
        ) $charset_collate;";
        
        // Monitoring data table
        $table_monitoring = $wpdb->prefix . 'kata_seo_monitoring';
        $sql_monitoring = "CREATE TABLE $table_monitoring (
            id int(11) NOT NULL AUTO_INCREMENT,
            metric_type varchar(50) NOT NULL,
            metric_value text NOT NULL,
            recorded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY metric_type (metric_type),
            KEY recorded_at (recorded_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_analysis);
        dbDelta($sql_competitors);
        dbDelta($sql_monitoring);
        
        // Update database version
        update_option('kata_seo_db_version', KATA_SEO_DB_VERSION);
    }
    
    /**
     * Set default options
     */
    private function setDefaultOptions() {
        $default_options = array(
            'auto_analyze' => true,
            'enable_ai_suggestions' => true,
            'enable_monitoring' => true,
            'enable_competitor_tracking' => false,
            'analysis_frequency' => 'daily',
            'max_suggestions' => 10,
            'google_api_key' => '',
            'openai_api_key' => ''
        );
        
        foreach ($default_options as $option => $value) {
            if (!get_option("kata_seo_{$option}")) {
                update_option("kata_seo_{$option}", $value);
            }
        }
    }
    
    /**
     * Schedule cron jobs
     */
    private function scheduleCronJobs() {
        if (!wp_next_scheduled('kata_seo_daily_analysis')) {
            wp_schedule_event(time(), 'daily', 'kata_seo_daily_analysis');
        }
        
        if (!wp_next_scheduled('kata_seo_competitor_check')) {
            wp_schedule_event(time(), 'twicedaily', 'kata_seo_competitor_check');
        }
    }
    
    /**
     * Create upload directories
     */
    private function createUploadDirectories() {
        $upload_dir = wp_upload_dir();
        $kata_dir = $upload_dir['basedir'] . '/kata-seo/';
        
        if (!file_exists($kata_dir)) {
            wp_mkdir_p($kata_dir);
            wp_mkdir_p($kata_dir . 'reports/');
            wp_mkdir_p($kata_dir . 'exports/');
            wp_mkdir_p($kata_dir . 'cache/');
        }
    }
    
    /**
     * Daily analysis cron job
     */
    public function dailyAnalysis() {
        if (!get_option('kata_seo_enable_monitoring', true)) {
            return;
        }
        
        // Analyze recent posts
        $recent_posts = get_posts(array(
            'numberposts' => 50,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_kata_seo_last_analysis',
                    'value' => date('Y-m-d', strtotime('-7 days')),
                    'compare' => '<='
                )
            )
        ));
        
        foreach ($recent_posts as $post) {
            $this->analyzer->analyzePost($post->ID);
        }
        
        // Update monitoring metrics
        if ($this->monitoring && method_exists($this->monitoring, 'recordDailyMetrics')) {
            $this->monitoring->recordDailyMetrics();
        }
    }
    
    /**
     * Competitor check cron job
     */
    public function competitorCheck() {
        if (!get_option('kata_seo_enable_competitor_tracking', false)) {
            return;
        }
        
        $this->competitor->checkAllCompetitors();
    }
    
    // AJAX handlers
    public function ajax_analyze_content() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(__('Invalid post ID or insufficient permissions', 'kata-seo-analyzer'));
        }
        
        $results = $this->analyzer->analyzePost($post_id);
        wp_send_json_success($results);
    }
    
    public function ajax_get_ai_suggestions() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(__('Invalid post ID or insufficient permissions', 'kata-seo-analyzer'));
        }
        
        $suggestions = $this->ai_suggestions->getSuggestions($post_id);
        wp_send_json_success($suggestions);
    }
    
    // Add more AJAX handlers...
}

// Initialize plugin
function kata_seo_init() {
    return KataSEOAnalyzer::getInstance();
}

// Start the plugin
add_action('plugins_loaded', 'kata_seo_init');

// Utility functions
function kata_seo() {
    return KataSEOAnalyzer::getInstance();
}

function kata_seo_get_score($post_id) {
    return kata_seo()->analyzer->calculateSEOScore($post_id);
}

function kata_seo_get_suggestions($post_id) {
    return kata_seo()->ai_suggestions->getSuggestions($post_id);
}
