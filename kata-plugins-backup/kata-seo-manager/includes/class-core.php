<?php
/**
 * Core Plugin Class
 * 
 * Main singleton class that initializes the plugin
 * Handles plugin lifecycle and component initialization
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class KATA_SEO_Core {
    
    /**
     * Single instance
     * @var KATA_SEO_Core
     */
    private static $instance = null;
    
    /**
     * Store schemas generated from shortcodes
     * @var array
     */
    private $shortcode_schemas = array();
    
    /**
     * Plugin components
     * @var array
     */
    private $components = array();
    
    /**
     * Get instance (Singleton pattern)
     * 
     * @return KATA_SEO_Core
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Private constructor (Singleton)
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_components();
        $this->init_hooks();
    }
    
    /**
     * Load required dependencies
     */
    private function load_dependencies() {
        // Core components
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-activator.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-enqueue-handler.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-schema-renderer.php';
        
        // Admin components
        if (is_admin()) {
            require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
            require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/admin/class-admin-notices.php';
            require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/admin/class-poll-manager.php';
            require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/admin/class-wheel-manager.php';
        }
        
        // AJAX handlers
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/ajax/class-ajax-handler.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/ajax/class-schema-ajax.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/ajax/class-poll-ajax.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/ajax/class-wheel-ajax.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/ajax/class-interaction-ajax.php';
        
        // Shortcode base class
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/shortcodes/class-shortcode-base.php';
        
        // Legacy includes (keep existing functionality)
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-database.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-sample-data.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-demo-content.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-schema-generator.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-editor-integration.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-admin-settings.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-statistics.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-quiz-manager.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-schema-customizer.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-schema-admin-ui.php';
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/class-smart-chatbot.php';
        
        // Schema handlers
        $this->load_schema_handlers();
    }
    
    /**
     * Load schema handler classes
     */
    private function load_schema_handlers() {
        require_once KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-base-schema.php';
        
        $schema_types = array(
            'article', 'breadcrumb', 'carousel', 'course', 'dataset',
            'forum', 'edu-qa', 'employer-rating', 'event', 'faq',
            'how-to', 'image-metadata', 'job-posting', 'local-business',
            'math-solver', 'movie', 'organization', 'practice-problem',
            'product', 'profile-page', 'recipe', 'review', 'sitelinks',
            'speakable', 'video', 'web-page'
        );
        
        foreach ($schema_types as $type) {
            $file = KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/schemas/class-' . $type . '-schema.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }
    
    /**
     * Initialize plugin components
     */
    private function init_components() {
        // Enqueue handler
        $this->components['enqueue'] = new KATA_SEO_Enqueue_Handler();
        
        // Schema renderer
        $this->components['schema_renderer'] = new KATA_SEO_Schema_Renderer($this);
        
        // Admin components (only in admin)
        if (is_admin()) {
            $this->components['admin_menu'] = new KATA_SEO_Admin_Menu();
            $this->components['admin_notices'] = new KATA_SEO_Admin_Notices();
            $this->components['poll_manager'] = new KATA_SEO_Poll_Manager();
            $this->components['wheel_manager'] = new KATA_SEO_Wheel_Manager();
        }
        
        // AJAX handlers (both frontend and backend)
        $this->components['schema_ajax'] = new KATA_SEO_Schema_Ajax();
        $this->components['poll_ajax'] = new KATA_SEO_Poll_Ajax();
        $this->components['wheel_ajax'] = new KATA_SEO_Wheel_Ajax();
        $this->components['interaction_ajax'] = new KATA_SEO_Interaction_Ajax();
        
        // Legacy component initialization
        KATA_Schema_Admin_UI::init();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Text domain
        add_action('init', array($this, 'load_plugin_textdomain'), 0);
        
        // Shortcodes registration
        add_action('init', array($this, 'register_shortcodes'), 5);
        
        // Page builder compatibility
        add_filter('ux_builder_shortcodes', array($this, 'add_uxbuilder_support'));
        add_filter('the_content', array($this, 'ensure_shortcode_processing'), 999);
        
        // TinyMCE integration
        add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        add_filter('mce_external_plugins', array($this, 'register_tinymce_plugin'));
        add_action('admin_head', array($this, 'tinymce_admin_head'));
    }
    
    /**
     * Load plugin text domain for translations
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'kata-seo-manager',
            false,
            dirname(plugin_basename(KATA_SEO_MANAGER_PLUGIN_DIR . 'kata-seo-manager.php')) . '/languages'
        );
    }
    
    /**
     * Register all shortcodes
     */
    public function register_shortcodes() {
        // Load all shortcode classes dynamically
        $shortcode_files = glob(KATA_SEO_MANAGER_PLUGIN_DIR . 'includes/shortcodes/class-*-shortcode.php');
        
        foreach ($shortcode_files as $file) {
            require_once $file;
            
            // Extract class name from filename
            $basename = basename($file, '.php');
            $class_name = str_replace('class-', '', $basename);
            $class_name = str_replace('-', '_', $class_name);
            $class_name = 'KATA_SEO_' . ucwords($class_name, '_');
            
            // Initialize shortcode class if exists
            if (class_exists($class_name)) {
                new $class_name($this);
            }
        }
    }
    
    /**
     * Add UX Builder support
     */
    public function add_uxbuilder_support($shortcodes) {
        if (!is_array($shortcodes)) {
            $shortcodes = array();
        }
        
        $kata_shortcodes = array(
            'kata_article', 'kata_recipe', 'kata_product', 'kata_event',
            'kata_howto', 'kata_faq', 'kata_faq_item', 'kata_video',
            'kata_organization', 'kata_localbusiness', 'kata_jobposting',
            'kata_course', 'kata_review', 'kata_breadcrumb', 'kata_rating',
            'kata_person', 'kata_software', 'kata_book', 'kata_music',
            'kata_movie', 'kata_website', 'kata_blog', 'kata_offer',
            'kata_aggregate_rating', 'kata_search_box', 'kata_site_navigation',
            'kata_quiz', 'kata_quiz_question', 'kata_poll', 'kata_poll_option',
            'kata_calculator', 'kata_countdown', 'kata_progress_bar',
            'kata_wheel_of_fortune', 'kata_dynamic'
        );
        
        return array_merge($shortcodes, $kata_shortcodes);
    }
    
    /**
     * Ensure shortcode processing
     */
    public function ensure_shortcode_processing($content) {
        if (stripos($content, '[kata_') !== false) {
            $content = do_shortcode($content);
        }
        return $content;
    }
    
    /**
     * Register TinyMCE button
     */
    public function register_tinymce_button($buttons) {
        array_push($buttons, 'kata_schema_button');
        return $buttons;
    }
    
    /**
     * Register TinyMCE plugin
     */
    public function register_tinymce_plugin($plugins) {
        $plugins['kata_schema'] = plugin_dir_url(KATA_SEO_MANAGER_PLUGIN_DIR . 'kata-seo-manager.php') . 'assets/js/tinymce-plugin.js';
        return $plugins;
    }
    
    /**
     * TinyMCE admin head
     */
    public function tinymce_admin_head() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        
        echo '<style>
            .kata-schema-button { background-image: url(' . plugin_dir_url(KATA_SEO_MANAGER_PLUGIN_DIR . 'kata-seo-manager.php') . 'assets/images/schema-icon.png); }
        </style>';
    }
    
    /**
     * Store schema from shortcode
     */
    public function store_shortcode_schema($schema, $schema_type = '') {
        if (!is_array($schema) || empty($schema)) {
            return;
        }
        
        $schema_key = md5(json_encode($schema));
        
        $this->shortcode_schemas[$schema_key] = array(
            'schema' => $schema,
            'type' => $schema_type,
            'added_at' => microtime(true)
        );
    }
    
    /**
     * Get stored schemas
     */
    public function get_shortcode_schemas() {
        $schemas = array();
        foreach ($this->shortcode_schemas as $data) {
            $schemas[] = $data['schema'];
        }
        return $schemas;
    }
    
    /**
     * Get component
     */
    public function get_component($name) {
        return isset($this->components[$name]) ? $this->components[$name] : null;
    }
}
