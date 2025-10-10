<?php
/**
 * Plugin Name: Kata ShareSocial
 * Plugin URI: https://climaxthemes.com/kata/
 * Description: A comprehensive social sharing plugin for WordPress
 * Version: 1.0.0
 * Author: Climax Themes
 * Author URI: https://climaxthemes.com/
 * License: GPL v2 or later
 * Text Domain: kata-sharesocial
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KATA_SHARESOCIAL_VERSION', '1.0.0');
define('KATA_SHARESOCIAL_PLUGIN_FILE', __FILE__);
define('KATA_SHARESOCIAL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SHARESOCIAL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('KATA_SHARESOCIAL_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Main plugin class
if (!class_exists('KataShareSocial')) {
    class KataShareSocial {
        
        /**
         * Single instance of the plugin
         */
        private static $instance = null;
        
        /**
         * Plugin components
         */
        public $platforms = null;
        public $frontend = null;
        public $admin = null;
        public $analytics = null;
        
        /**
         * Get single instance
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
            $this->define_hooks();
            $this->load_dependencies();
            $this->init_components();
        }
        
        /**
         * Define WordPress hooks
         */
        private function define_hooks() {
            register_activation_hook(__FILE__, array($this, 'activate'));
            register_deactivation_hook(__FILE__, array($this, 'deactivate'));
            
            add_action('plugins_loaded', array($this, 'load_textdomain'));
            add_action('init', array($this, 'init'));
        }
        
        /**
         * Load plugin dependencies
         */
        private function load_dependencies() {
            // Load core classes
            require_once KATA_SHARESOCIAL_PLUGIN_PATH . 'includes/class-platforms.php';
            require_once KATA_SHARESOCIAL_PLUGIN_PATH . 'includes/class-frontend.php';
            require_once KATA_SHARESOCIAL_PLUGIN_PATH . 'includes/class-analytics.php';
            
            // Load admin classes
            if (is_admin()) {
                require_once KATA_SHARESOCIAL_PLUGIN_PATH . 'includes/admin/class-admin.php';
            }
        }
        
        /**
         * Initialize plugin components
         */
        private function init_components() {
            $this->platforms = KataShareSocial_Platforms::get_instance();
            $this->frontend = KataShareSocial_Frontend::get_instance();
            $this->analytics = KataShareSocial_Analytics::get_instance();
            
            if (is_admin()) {
                $this->admin = KataShareSocial_Admin::get_instance();
            }
        }
        
        /**
         * Initialize plugin
         */
        public function init() {
            // Plugin initialization code
            do_action('kata_sharesocial_init');
        }
        
        /**
         * Load plugin textdomain
         */
        public function load_textdomain() {
            load_plugin_textdomain(
                'kata-sharesocial',
                false,
                dirname(KATA_SHARESOCIAL_PLUGIN_BASENAME) . '/languages'
            );
        }
        
        /**
         * Plugin activation
         */
        public function activate() {
            // Create database tables if needed
            $this->create_database_tables();
            
            // Set default options
            $this->set_default_options();
            
            // Flush rewrite rules
            flush_rewrite_rules();
        }
        
        /**
         * Plugin deactivation
         */
        public function deactivate() {
            // Clean up if needed
            flush_rewrite_rules();
        }
        
        /**
         * Create database tables
         */
        private function create_database_tables() {
            global $wpdb;
            
            $table_name = $wpdb->prefix . 'kata_sharesocial_stats';
            
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                post_id bigint(20) NOT NULL,
                platform varchar(50) NOT NULL,
                share_count bigint(20) DEFAULT 0,
                last_updated datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY post_platform (post_id, platform),
                KEY post_id (post_id),
                KEY platform (platform)
            ) $charset_collate;";
            
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        }
        
        /**
         * Set default plugin options
         */
        private function set_default_options() {
            $default_options = array(
                'enabled_platforms' => array('facebook', 'twitter', 'linkedin', 'telegram', 'whatsapp'),
                'button_style' => 'rounded',
                'button_size' => 'medium',
                'show_count' => true,
                'auto_add_buttons' => true,
                'button_position' => 'bottom',
                'exclude_post_types' => array(),
                'analytics_enabled' => true,
                'custom_css' => ''
            );
            
            foreach ($default_options as $option_key => $option_value) {
                if (false === get_option('kata_sharesocial_' . $option_key)) {
                    add_option('kata_sharesocial_' . $option_key, $option_value);
                }
            }
        }
        
        /**
         * Get plugin option
         */
        public static function get_option($option_key, $default = false) {
            return get_option('kata_sharesocial_' . $option_key, $default);
        }
        
        /**
         * Update plugin option
         */
        public static function update_option($option_key, $option_value) {
            return update_option('kata_sharesocial_' . $option_key, $option_value);
        }
    }
}

// Initialize the plugin
function kata_sharesocial() {
    return KataShareSocial::get_instance();
}

// Start the plugin
kata_sharesocial();
