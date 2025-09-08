<?php
/**
 * Plugin Name: Kata Schema Markup
 * Plugin URI: https://timona.vn/plugins/kata-schema-markup
 * Description: Plugin tự động tạo và quản lý Schema Markup (Dữ liệu có cấu trúc) để tối ưu hóa SEO và hiển thị rich snippets trên Google.
 * Version: 1.0.0
 * Author: Timona Team
 * Author URI: https://timona.vn
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kata-schema-markup
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Network: false
 *
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KATA_SCHEMA_VERSION', '1.0.0');
define('KATA_SCHEMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SCHEMA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KATA_SCHEMA_PLUGIN_FILE', __FILE__);
define('KATA_SCHEMA_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class KataSchemaMarkup {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Schema generator instance
     */
    private $schema_generator;
    
    /**
     * Generator instance (compatibility)
     */
    public $generator;
    
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
        
        // Add schema markup to frontend
        add_action('wp_head', array($this, 'output_schema_markup'), 1);
        
        // Add custom meta fields
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_fields'));
    }
    
    /**
     * Load text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'kata-schema-markup',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
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
        
        // Clear schema cache
        delete_transient('kata_schema_cache');
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Schema templates table
        $table_name = $wpdb->prefix . 'kata_schema_templates';
        
        // Check if table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                name varchar(255) NOT NULL,
                type varchar(100) NOT NULL,
                schema_data longtext NOT NULL,
                post_types text,
                conditions text,
                status varchar(20) DEFAULT 'active',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY type (type),
                KEY status (status),
                KEY post_types (post_types(100))
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        // Schema cache table for performance
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        
        // Check if cache table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$cache_table'") != $cache_table) {
            $cache_sql = "CREATE TABLE $cache_table (
                id int(11) NOT NULL AUTO_INCREMENT,
                post_id int(11) NOT NULL,
                schema_type varchar(100) NOT NULL,
                schema_json longtext NOT NULL,
                hash varchar(32) NOT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY post_schema (post_id, schema_type),
                KEY hash (hash)
            ) $charset_collate;";
            
            dbDelta($cache_sql);
        }
        
        // Log table creation results for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $templates_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
            $cache_exists = $wpdb->get_var("SHOW TABLES LIKE '$cache_table'") == $cache_table;
            error_log("Kata Schema Tables - Templates: " . ($templates_exists ? 'OK' : 'FAILED') . ", Cache: " . ($cache_exists ? 'OK' : 'FAILED'));
        }
    }
    
    /**
     * Set default options
     */
    private function set_default_options() {
        $default_options = array(
            'enabled_schemas' => array('article', 'breadcrumb', 'organization', 'website'),
            'auto_generate' => true,
            'rich_snippets' => true,
            'validate_schema' => true,
            'cache_enabled' => true,
            'cache_duration' => 24, // hours
            'organization_name' => get_bloginfo('name'),
            'organization_logo' => '',
            'organization_type' => 'Organization',
            'social_profiles' => array(),
            'default_author_type' => 'Person',
            'enable_faq_schema' => true,
            'enable_howto_schema' => true,
            'enable_review_schema' => true,
            'enable_product_schema' => true,
            'enable_event_schema' => true,
            'enable_recipe_schema' => true,
            'custom_schemas' => array()
        );
        
        foreach ($default_options as $key => $value) {
            $option_name = 'kata_schema_' . $key;
            if (get_option($option_name) === false) {
                add_option($option_name, $value);
            }
        }
        
        // Insert default schema templates
        $this->insert_default_templates();
    }
    
    /**
     * Insert default schema templates
     */
    private function insert_default_templates() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_schema_templates';
        
        // Check if templates already exist
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($existing > 0) {
            return;
        }
        
        $default_templates = array(
            array(
                'name' => 'Article Schema',
                'type' => 'article',
                'schema_data' => json_encode(array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => '{{post_title}}',
                    'description' => '{{post_excerpt}}',
                    'image' => '{{featured_image}}',
                    'author' => array(
                        '@type' => 'Person',
                        'name' => '{{author_name}}'
                    ),
                    'publisher' => array(
                        '@type' => 'Organization',
                        'name' => '{{site_name}}',
                        'logo' => '{{site_logo}}'
                    ),
                    'datePublished' => '{{publish_date}}',
                    'dateModified' => '{{modified_date}}'
                )),
                'post_types' => 'post,page',
                'conditions' => json_encode(array())
            ),
            array(
                'name' => 'Breadcrumb Schema',
                'type' => 'breadcrumb',
                'schema_data' => json_encode(array(
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => '{{breadcrumb_items}}'
                )),
                'post_types' => 'post,page,product',
                'conditions' => json_encode(array())
            ),
            array(
                'name' => 'Organization Schema',
                'type' => 'organization',
                'schema_data' => json_encode(array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => '{{organization_name}}',
                    'url' => '{{site_url}}',
                    'logo' => '{{organization_logo}}',
                    'sameAs' => '{{social_profiles}}'
                )),
                'post_types' => 'all',
                'conditions' => json_encode(array('is_home' => true))
            ),
            array(
                'name' => 'Website Schema',
                'type' => 'website',
                'schema_data' => json_encode(array(
                    '@context' => 'https://schema.org',
                    '@type' => 'WebSite',
                    'name' => '{{site_name}}',
                    'url' => '{{site_url}}',
                    'potentialAction' => array(
                        '@type' => 'SearchAction',
                        'target' => '{{search_url}}',
                        'query-input' => 'required name=search_term_string'
                    )
                )),
                'post_types' => 'all',
                'conditions' => json_encode(array('is_home' => true))
            )
        );
        
        foreach ($default_templates as $template) {
            $wpdb->insert($table_name, $template);
        }
    }
    
    /**
     * Initialize components
     */
    public function init_components() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize schema generator
        $this->schema_generator = new KataSchema_Generator();
        
        // Add compatibility for older property name
        $this->generator = $this->schema_generator;
        
        // Initialize admin if in admin area
        if (is_admin()) {
            new KataSchema_Admin();
        }
        
        // Initialize frontend functionality
        new KataSchema_Frontend();
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        $files = array(
            'includes/class-schema-generator.php',
            'includes/class-frontend.php',
            'includes/class-validator.php',
            'schemas/class-article.php',
            'schemas/class-breadcrumb.php',
            'schemas/class-organization.php',
            'schemas/class-website.php'
        );
        
        if (is_admin()) {
            $files[] = 'includes/class-admin.php';
        }
        
        foreach ($files as $file) {
            $file_path = KATA_SCHEMA_PLUGIN_PATH . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue if needed
        if (!is_admin()) {
            wp_enqueue_style(
                'kata-schema-frontend',
                KATA_SCHEMA_PLUGIN_URL . 'assets/css/frontend.css',
                array(),
                KATA_SCHEMA_VERSION
            );
        }
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function admin_enqueue_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'kata-schema') === false && !in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        wp_enqueue_style(
            'kata-schema-admin',
            KATA_SCHEMA_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            KATA_SCHEMA_VERSION
        );
        
        wp_enqueue_script(
            'kata-schema-admin',
            KATA_SCHEMA_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            KATA_SCHEMA_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('kata-schema-admin', 'kataSchema', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_schema_nonce'),
            'strings' => array(
                'validating' => __('Validating schema...', 'kata-schema-markup'),
                'valid' => __('Schema is valid!', 'kata-schema-markup'),
                'invalid' => __('Schema is invalid!', 'kata-schema-markup'),
                'error' => __('Validation error occurred!', 'kata-schema-markup')
            )
        ));
    }
    
    /**
     * Output schema markup in head
     */
    public function output_schema_markup() {
        if (!get_option('kata_schema_auto_generate', true)) {
            return;
        }
        
        if ($this->schema_generator) {
            echo $this->schema_generator->generate_schema_output();
        }
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'kata-schema-markup',
                __('Schema Markup', 'kata-schema-markup'),
                array($this, 'render_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        // Get current post meta values
        $schema_enabled = get_post_meta($post->ID, '_kata_schema_enabled', true);
        $schema_type = get_post_meta($post->ID, '_kata_schema_type', true);
        $custom_schema = get_post_meta($post->ID, '_kata_custom_schema', true);
        
        // Set defaults if empty
        if (empty($schema_type)) {
            $schema_type = 'Article';
        }
        
        // Get schema types from generator
        if (isset($this->schema_generator)) {
            $schema_types = $this->schema_generator->get_schema_types();
        } else {
            $schema_types = array();
        }
        
        // Get generated schema data for preview
        $schema_data = '';
        if ($schema_enabled && isset($this->schema_generator)) {
            $generated_schema = $this->schema_generator->generate_schema($post->ID, $schema_type);
            if ($generated_schema) {
                $schema_data = wp_json_encode($generated_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        }
        
        // Include meta box template
        include KATA_SCHEMA_PLUGIN_PATH . 'templates/admin/post-metabox.php';
    }
    
    /**
     * Save meta fields
     */
    public function save_meta_fields($post_id) {
        // Verify nonce
        if (!isset($_POST['kata_schema_metabox_nonce']) || !wp_verify_nonce($_POST['kata_schema_metabox_nonce'], 'kata_schema_metabox')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save schema data
        if (isset($_POST['kata_schema_data'])) {
            $schema_data = sanitize_textarea_field($_POST['kata_schema_data']);
            update_post_meta($post_id, '_kata_schema_data', $schema_data);
        }
        
        // Save schema type
        if (isset($_POST['kata_schema_type'])) {
            $schema_type = sanitize_text_field($_POST['kata_schema_type']);
            update_post_meta($post_id, '_kata_schema_type', $schema_type);
        }
        
        // Save custom fields
        $custom_fields = array(
            'kata_schema_title',
            'kata_schema_description',
            'kata_schema_image',
            'kata_schema_author',
            'kata_schema_rating',
            'kata_schema_price',
            'kata_schema_availability'
        );
        
        foreach ($custom_fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
        
        // Clear cache for this post
        $this->clear_post_schema_cache($post_id);
    }
    
    /**
     * Clear schema cache for specific post
     */
    private function clear_post_schema_cache($post_id) {
        global $wpdb;
        
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        $wpdb->delete($cache_table, array('post_id' => $post_id));
        
        // Clear related transients
        delete_transient('kata_schema_' . $post_id);
    }
    
    /**
     * Get schema generator instance
     */
    public function get_schema_generator() {
        return $this->schema_generator;
    }
}

/**
 * Initialize the plugin
 */
function kata_schema_markup_init() {
    return KataSchemaMarkup::get_instance();
}

// Start the plugin
kata_schema_markup_init();

/**
 * Helper function to get plugin instance
 */
function kata_schema() {
    return KataSchemaMarkup::get_instance();
}

/**
 * Helper function to generate schema for specific post
 */
function kata_generate_schema($post_id = null, $schema_type = null) {
    $instance = kata_schema();
    $generator = $instance->get_schema_generator();
    
    if ($generator) {
        return $generator->generate_schema($post_id, $schema_type);
    }
    
    return false;
}

/**
 * Output schema markup for display
 */
function kata_schema_output($schema_type = 'article', $post_id = null) {
    if ($post_id === null) {
        $post_id = get_the_ID();
    }
    
    $schema = kata_generate_schema($post_id, $schema_type);
    
    if ($schema) {
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
    }
}

/**
 * Get schema data for a post
 */
function kata_get_schema_data($schema_type = 'article', $post_id = null) {
    if ($post_id === null) {
        $post_id = get_the_ID();
    }
    
    return kata_generate_schema($post_id, $schema_type);
}
