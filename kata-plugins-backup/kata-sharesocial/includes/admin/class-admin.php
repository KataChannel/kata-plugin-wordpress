<?php
/**
 * Admin Management
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Class
 */
class KataShareSocial_Admin {
    
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
        $this->add_hooks();
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Settings
        add_action('admin_init', array($this, 'init_settings'));
        
        // Meta box for posts
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box'));
        
        // Admin notices
        add_action('admin_notices', array($this, 'admin_notices'));
        
        // Plugin action links
        add_filter('plugin_action_links_' . plugin_basename(KATA_SHARESOCIAL_PLUGIN_FILE), array($this, 'plugin_action_links'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Kata ShareSocial', 'kata-sharesocial'),
            __('ShareSocial', 'kata-sharesocial'),
            'manage_options',
            'kata-sharesocial',
            array($this, 'admin_page_dashboard'),
            'dashicons-share',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'kata-sharesocial',
            __('Dashboard', 'kata-sharesocial'),
            __('Dashboard', 'kata-sharesocial'),
            'manage_options',
            'kata-sharesocial',
            array($this, 'admin_page_dashboard')
        );
        
        // Settings submenu
        add_submenu_page(
            'kata-sharesocial',
            __('Settings', 'kata-sharesocial'),
            __('Settings', 'kata-sharesocial'),
            'manage_options',
            'kata-sharesocial-settings',
            array($this, 'admin_page_settings')
        );
        
        // Analytics submenu
        add_submenu_page(
            'kata-sharesocial',
            __('Analytics', 'kata-sharesocial'),
            __('Analytics', 'kata-sharesocial'),
            'manage_options',
            'kata-sharesocial-analytics',
            array($this, 'admin_page_analytics')
        );
    }
    
    /**
     * Dashboard page
     */
    public function admin_page_dashboard() {
        $platforms = KataShareSocial_Platforms::get_instance();
        $stats = $platforms->get_share_statistics(30);
        
        include KATA_SHARESOCIAL_PLUGIN_PATH . 'templates/admin-dashboard.php';
    }
    
    /**
     * Settings page
     */
    public function admin_page_settings() {
        // Handle form submission
        if (isset($_POST['submit'])) {
            check_admin_referer('kata_sharesocial_settings');
            $this->save_settings();
        }
        
        include KATA_SHARESOCIAL_PLUGIN_PATH . 'templates/admin-settings.php';
    }
    
    /**
     * Analytics page
     */
    public function admin_page_analytics() {
        $platforms = KataShareSocial_Platforms::get_instance();
        $period = isset($_GET['period']) ? intval($_GET['period']) : 30;
        $stats = $platforms->get_share_statistics($period);
        
        include KATA_SHARESOCIAL_PLUGIN_PATH . 'templates/admin-analytics.php';
    }
    
    /**
     * Initialize settings
     */
    public function init_settings() {
        // Register settings
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_enabled_platforms');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_button_style');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_button_size');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_show_count');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_auto_add_buttons');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_button_position');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_exclude_post_types');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_custom_css');
        register_setting('kata_sharesocial_settings', 'kata_sharesocial_analytics_enabled');
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        // Enabled platforms
        $enabled_platforms = isset($_POST['enabled_platforms']) ? array_map('sanitize_text_field', $_POST['enabled_platforms']) : array();
        update_option('kata_sharesocial_enabled_platforms', $enabled_platforms);
        
        // Button style
        $button_style = sanitize_text_field($_POST['button_style'] ?? 'rounded');
        update_option('kata_sharesocial_button_style', $button_style);
        
        // Button size
        $button_size = sanitize_text_field($_POST['button_size'] ?? 'medium');
        update_option('kata_sharesocial_button_size', $button_size);
        
        // Show count
        $show_count = isset($_POST['show_count']) ? 1 : 0;
        update_option('kata_sharesocial_show_count', $show_count);
        
        // Auto add buttons
        $auto_add = isset($_POST['auto_add_buttons']) ? 1 : 0;
        update_option('kata_sharesocial_auto_add_buttons', $auto_add);
        
        // Button position
        $position = sanitize_text_field($_POST['button_position'] ?? 'bottom');
        update_option('kata_sharesocial_button_position', $position);
        
        // Exclude post types
        $exclude_types = isset($_POST['exclude_post_types']) ? array_map('sanitize_text_field', $_POST['exclude_post_types']) : array();
        update_option('kata_sharesocial_exclude_post_types', $exclude_types);
        
        // Custom CSS
        $custom_css = sanitize_textarea_field($_POST['custom_css'] ?? '');
        update_option('kata_sharesocial_custom_css', $custom_css);
        
        // Analytics
        $analytics = isset($_POST['analytics_enabled']) ? 1 : 0;
        update_option('kata_sharesocial_analytics_enabled', $analytics);
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully!', 'kata-sharesocial') . '</p></div>';
        });
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        $post_types = get_post_types(array('public' => true), 'names');
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'kata-sharesocial-stats',
                __('Share Statistics', 'kata-sharesocial'),
                array($this, 'meta_box_stats'),
                $post_type,
                'side',
                'default'
            );
        }
    }
    
    /**
     * Meta box content
     */
    public function meta_box_stats($post) {
        $platforms = KataShareSocial_Platforms::get_instance();
        $total_shares = $platforms->get_share_count($post->ID);
        $enabled_platforms = $platforms->get_enabled_platforms();
        
        echo '<div class="kata-share-stats">';
        echo '<p><strong>' . esc_html__('Total Shares:', 'kata-sharesocial') . '</strong> ' . esc_html($total_shares) . '</p>';
        
        if (!empty($enabled_platforms)) {
            echo '<div class="kata-platform-stats">';
            foreach ($enabled_platforms as $platform_id => $platform) {
                $count = $platforms->get_share_count($post->ID, $platform_id);
                echo '<div class="kata-platform-stat">';
                echo '<i class="' . esc_attr($platform['icon']) . '"></i> ';
                echo esc_html($platform['name']) . ': <strong>' . esc_html($count) . '</strong>';
                echo '</div>';
            }
            echo '</div>';
        }
        
        // Preview buttons
        echo '<div class="kata-share-preview">';
        echo '<p><strong>' . esc_html__('Preview:', 'kata-sharesocial') . '</strong></p>';
        $frontend = KataShareSocial_Frontend::get_instance();
        echo $frontend->render_share_buttons(array('post_id' => $post->ID, 'show_count' => false));
        echo '</div>';
        
        echo '</div>';
        
        // Add inline styles for meta box
        echo '<style>
        .kata-share-stats .kata-platform-stat {
            margin: 5px 0;
            padding: 3px 0;
        }
        .kata-share-stats .kata-platform-stat i {
            width: 16px;
            text-align: center;
        }
        .kata-share-preview {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .kata-share-preview .kata-share-buttons {
            margin: 10px 0;
        }
        </style>';
    }
    
    /**
     * Save meta box
     */
    public function save_meta_box($post_id) {
        // Nothing to save for now, just display stats
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        // Check if FontAwesome is needed
        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'kata-sharesocial') !== false) {
            echo '<div class="notice notice-info"><p>';
            echo esc_html__('Make sure your theme includes FontAwesome icons for the best display of share buttons.', 'kata-sharesocial');
            echo ' <a href="https://fontawesome.com/" target="_blank">' . esc_html__('Learn more', 'kata-sharesocial') . '</a>';
            echo '</p></div>';
        }
    }
    
    /**
     * Plugin action links
     */
    public function plugin_action_links($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=kata-sharesocial-settings') . '">' . esc_html__('Settings', 'kata-sharesocial') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
    
    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats() {
        $platforms = KataShareSocial_Platforms::get_instance();
        return $platforms->get_share_statistics(30);
    }
}
