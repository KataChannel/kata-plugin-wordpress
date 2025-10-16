<?php
/**
 * Plugin Name: KATA Schema
 * Plugin URI: https://katachannel.com/kata-schema
 * Description: Quản lý Schema Markup đơn giản và mạnh mẽ - Tạo schema mẫu, custom schema, chèn qua shortcode TinyMCE
 * Version: 1.0.0
 * Author: KATA Channel
 * Author URI: https://katachannel.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kata-schema
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package KATA_Schema
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('KATA_SCHEMA_VERSION', '1.0.0');
define('KATA_SCHEMA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATA_SCHEMA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SCHEMA_PLUGIN_FILE', __FILE__);

/**
 * Main KATA Schema Class
 */
class KATA_Schema {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Store schemas to output in wp_head
     * @var array
     */
    private $schemas_to_output = array();
    
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
        require_once KATA_SCHEMA_PLUGIN_DIR . 'includes/class-database.php';
        require_once KATA_SCHEMA_PLUGIN_DIR . 'includes/class-schema-templates.php';
        require_once KATA_SCHEMA_PLUGIN_DIR . 'includes/class-schema-renderer.php';
        require_once KATA_SCHEMA_PLUGIN_DIR . 'includes/class-tinymce-integration.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Load text domain
        add_action('init', array($this, 'load_plugin_textdomain'));
        
        // Register shortcode
        add_action('init', array($this, 'register_shortcode'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Frontend hooks
        add_action('wp_head', array($this, 'output_schemas'), 999);
        
        // AJAX handlers
        add_action('wp_ajax_kata_schema_save', array($this, 'ajax_save_schema'));
        add_action('wp_ajax_kata_schema_delete', array($this, 'ajax_delete_schema'));
        add_action('wp_ajax_kata_schema_get', array($this, 'ajax_get_schema'));
        add_action('wp_ajax_kata_schema_list', array($this, 'ajax_list_schemas'));
    }
    
    /**
     * Load plugin text domain
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'kata-schema',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages'
        );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        try {
            // Create database tables
            $database = new KATA_Schema_Database();
            $result = $database->create_tables();
            
            if (!$result) {
                throw new Exception('Failed to create database tables');
            }
            
            // Create default schema templates
            KATA_Schema_Templates::create_default_templates();
            
            // Set activation flag
            update_option('kata_schema_activated', true);
            update_option('kata_schema_activation_date', current_time('mysql'));
            update_option('kata_schema_version', KATA_SCHEMA_VERSION);
            
            flush_rewrite_rules();
            
        } catch (Exception $e) {
            error_log('KATA Schema Activation Error: ' . $e->getMessage());
            add_option('kata_schema_activation_error', $e->getMessage());
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
            __('KATA Schema', 'kata-schema'),
            __('KATA Schema', 'kata-schema'),
            'manage_options',
            'kata-schema',
            array($this, 'admin_dashboard_page'),
            'dashicons-media-code',
            31
        );
        
        add_submenu_page(
            'kata-schema',
            __('Tất cả Schemas', 'kata-schema'),
            __('Tất cả Schemas', 'kata-schema'),
            'manage_options',
            'kata-schema',
            array($this, 'admin_dashboard_page')
        );
        
        add_submenu_page(
            'kata-schema',
            __('Thêm Schema Mới', 'kata-schema'),
            __('Thêm Schema Mới', 'kata-schema'),
            'manage_options',
            'kata-schema-add',
            array($this, 'admin_add_schema_page')
        );
        
        add_submenu_page(
            'kata-schema',
            __('Mẫu Schema', 'kata-schema'),
            __('Mẫu Schema', 'kata-schema'),
            'manage_options',
            'kata-schema-templates',
            array($this, 'admin_templates_page')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'kata-schema') === false && !in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }
        
        $plugin_url = KATA_SCHEMA_PLUGIN_URL;
        $version = KATA_SCHEMA_VERSION;
        
        // CSS
        wp_enqueue_style(
            'kata-schema-admin',
            $plugin_url . 'assets/css/admin.css',
            array(),
            $version
        );
        
        // JavaScript
        wp_enqueue_script(
            'kata-schema-admin',
            $plugin_url . 'assets/js/admin.js',
            array('jquery', 'wp-util'),
            $version,
            true
        );
        
        // Localize script
        wp_localize_script('kata-schema-admin', 'kataSchema', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_schema_nonce'),
            'pluginUrl' => $plugin_url,
            'i18n' => array(
                'confirmDelete' => __('Bạn có chắc muốn xóa schema này?', 'kata-schema'),
                'saved' => __('Đã lưu thành công!', 'kata-schema'),
                'error' => __('Có lỗi xảy ra!', 'kata-schema')
            )
        ));
        
        // TinyMCE integration
        if (in_array($hook, array('post.php', 'post-new.php'))) {
            KATA_Schema_TinyMCE::enqueue_scripts();
        }
    }
    
    /**
     * Register shortcode
     */
    public function register_shortcode() {
        add_shortcode('kata_schema', array($this, 'render_shortcode'));
    }
    
    /**
     * Render shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string
     */
    public function render_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
            'show_schema' => 'true',
            'show_frontend' => 'false'
        ), $atts, 'kata_schema');
        
        $schema_id = intval($atts['id']);
        
        if ($schema_id <= 0) {
            return '<!-- KATA Schema: Invalid ID -->';
        }
        
        // Get schema from database
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schema = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND status = 'active'",
            $schema_id
        ));
        
        if (!$schema) {
            return '<!-- KATA Schema: Schema not found -->';
        }
        
        // Parse schema data
        $schema_data = json_decode($schema->schema_data, true);
        
        if (!$schema_data || !is_array($schema_data)) {
            return '<!-- KATA Schema: Invalid schema data -->';
        }
        
        // Store for wp_head output
        if ($atts['show_schema'] === 'true') {
            $this->add_schema_to_output($schema_data);
        }
        
        // Render frontend display
        $output = '';
        if ($atts['show_frontend'] === 'true') {
            $output = KATA_Schema_Renderer::render_frontend($schema_data, $schema->schema_type);
        }
        
        return $output;
    }
    
    /**
     * Add schema to output array
     * 
     * @param array $schema_data Schema data
     */
    public function add_schema_to_output($schema_data) {
        $schema_key = md5(json_encode($schema_data));
        
        if (!isset($this->schemas_to_output[$schema_key])) {
            $this->schemas_to_output[$schema_key] = $schema_data;
        }
    }
    
    /**
     * Output schemas in wp_head
     */
    public function output_schemas() {
        if (empty($this->schemas_to_output)) {
            return;
        }
        
        echo "\n<!-- KATA Schema Markup -->\n";
        
        foreach ($this->schemas_to_output as $schema) {
            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            echo "\n" . '</script>' . "\n";
        }
        
        echo "<!-- /KATA Schema Markup -->\n\n";
    }
    
    /**
     * Dashboard page
     */
    public function admin_dashboard_page() {
        include KATA_SCHEMA_PLUGIN_DIR . 'admin/dashboard.php';
    }
    
    /**
     * Add schema page
     */
    public function admin_add_schema_page() {
        include KATA_SCHEMA_PLUGIN_DIR . 'admin/add-schema.php';
    }
    
    /**
     * Templates page
     */
    public function admin_templates_page() {
        include KATA_SCHEMA_PLUGIN_DIR . 'admin/templates.php';
    }
    
    /**
     * AJAX: Save schema
     */
    public function ajax_save_schema() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $schema_id = intval($_POST['schema_id'] ?? 0);
        $schema_name = sanitize_text_field($_POST['schema_name'] ?? '');
        $schema_type = sanitize_text_field($_POST['schema_type'] ?? '');
        $schema_data = stripslashes($_POST['schema_data'] ?? '');
        $status = sanitize_text_field($_POST['status'] ?? 'active');
        
        if (empty($schema_name) || empty($schema_type) || empty($schema_data)) {
            wp_send_json_error('Missing required fields');
        }
        
        // Validate JSON
        $schema_array = json_decode($schema_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('Invalid JSON: ' . json_last_error_msg());
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $data = array(
            'schema_name' => $schema_name,
            'schema_type' => $schema_type,
            'schema_data' => $schema_data,
            'status' => $status,
            'updated_at' => current_time('mysql')
        );
        
        if ($schema_id > 0) {
            // Update existing
            $result = $wpdb->update(
                $table_name,
                $data,
                array('id' => $schema_id),
                array('%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
            
            $message = 'Schema đã được cập nhật!';
        } else {
            // Insert new
            $data['created_at'] = current_time('mysql');
            
            $result = $wpdb->insert(
                $table_name,
                $data,
                array('%s', '%s', '%s', '%s', '%s', '%s')
            );
            
            $schema_id = $wpdb->insert_id;
            $message = 'Schema đã được tạo mới!';
        }
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => $message,
                'schema_id' => $schema_id
            ));
        } else {
            wp_send_json_error('Database error: ' . $wpdb->last_error);
        }
    }
    
    /**
     * AJAX: Delete schema
     */
    public function ajax_delete_schema() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }
        
        $schema_id = intval($_POST['schema_id'] ?? 0);
        
        if ($schema_id <= 0) {
            wp_send_json_error('Invalid schema ID');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $result = $wpdb->delete(
            $table_name,
            array('id' => $schema_id),
            array('%d')
        );
        
        if ($result !== false) {
            wp_send_json_success('Schema đã được xóa!');
        } else {
            wp_send_json_error('Database error');
        }
    }
    
    /**
     * AJAX: Get schema
     */
    public function ajax_get_schema() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }
        
        $schema_id = intval($_POST['schema_id'] ?? 0);
        
        if ($schema_id <= 0) {
            wp_send_json_error('Invalid schema ID');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schema = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $schema_id
        ));
        
        if ($schema) {
            wp_send_json_success($schema);
        } else {
            wp_send_json_error('Schema not found');
        }
    }
    
    /**
     * AJAX: List schemas
     */
    public function ajax_list_schemas() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schemas = $wpdb->get_results(
            "SELECT id, schema_name, schema_type, status, created_at 
             FROM $table_name 
             WHERE status = 'active' 
             ORDER BY schema_name ASC"
        );
        
        wp_send_json_success($schemas);
    }
}

// Initialize plugin
function kata_schema() {
    return KATA_Schema::get_instance();
}

// Start the plugin
kata_schema();
