<?php
/**
 * Asset Manager Class
 * 
 * Centralized asset management with proper enqueue, dependencies, and conditional loading
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.4
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Asset_Manager {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Plugin version for cache busting
     */
    private $version;
    
    /**
     * Assets base URL
     */
    private $assets_url;
    
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
        $this->version = KATA_SEO_MANAGER_VERSION;
        $this->assets_url = KATA_SEO_MANAGER_PLUGIN_URL . 'assets/';
        
        // Admin assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Frontend assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        
        // Editor assets
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only on plugin pages
        if (strpos($hook, 'kata-seo') === false) {
            return;
        }
        
        // CSS Variables (load first)
        wp_enqueue_style(
            'kata-seo-variables',
            $this->assets_url . 'css/kata-seo-variables.css',
            array(),
            $this->version
        );
        
        // Admin main CSS
        wp_enqueue_style(
            'kata-seo-manager-admin',
            $this->assets_url . 'css/kata-seo-admin.css',
            array('kata-seo-variables'),
            $this->version
        );
        
        // Admin main JS
        wp_enqueue_script(
            'kata-seo-manager-admin',
            $this->assets_url . 'js/kata-seo-admin.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            true
        );
        
        // Color picker
        wp_enqueue_style('wp-color-picker');
        
        // Schema builder (only on schema pages)
        if (strpos($hook, 'schema') !== false) {
            $this->enqueue_schema_builder();
        }
        
        // Statistics (only on stats pages)
        if (strpos($hook, 'statistics') !== false || strpos($hook, 'analytics') !== false) {
            $this->enqueue_statistics();
        }
        
        // Localize admin script
        wp_localize_script('kata-seo-manager-admin', 'kataSeoAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_admin_nonce'),
            'i18n' => array(
                'saving' => __('Đang lưu...', 'kata-seo-manager'),
                'saved' => __('Đã lưu!', 'kata-seo-manager'),
                'error' => __('Có lỗi xảy ra', 'kata-seo-manager'),
                'confirm' => __('Bạn có chắc chắn?', 'kata-seo-manager'),
            )
        ));
    }
    
    /**
     * Enqueue schema builder assets
     */
    private function enqueue_schema_builder() {
        wp_enqueue_style(
            'kata-seo-manager-schema-builder',
            $this->assets_url . 'css/kata-seo-schema-builder.css',
            array('kata-seo-variables', 'kata-seo-manager-admin'),
            $this->version
        );
        
        wp_enqueue_style(
            'kata-seo-manager-schema-dialog',
            $this->assets_url . 'css/kata-seo-schema-dialog.css',
            array('kata-seo-variables', 'kata-seo-manager-schema-builder'),
            $this->version
        );
        
        wp_enqueue_script(
            'kata-seo-manager-schema-builder',
            $this->assets_url . 'js/kata-seo-schema-builder.js',
            array('jquery', 'kata-seo-manager-admin'),
            $this->version,
            true
        );
        
        wp_enqueue_script(
            'kata-seo-manager-schema-dialog',
            $this->assets_url . 'js/kata-seo-schema-dialog.js',
            array('kata-seo-manager-schema-builder'),
            $this->version,
            true
        );
        
        // Schema attributes config
        if (file_exists(KATA_SEO_MANAGER_PLUGIN_DIR . 'assets/js/schema-attributes-config.js')) {
            wp_enqueue_script(
                'kata-seo-manager-schema-config',
                $this->assets_url . 'js/schema-attributes-config.js',
                array('kata-seo-manager-schema-builder'),
                $this->version,
                true
            );
        }
    }
    
    /**
     * Enqueue statistics assets
     */
    private function enqueue_statistics() {
        wp_enqueue_style(
            'kata-seo-manager-statistics',
            $this->assets_url . 'css/kata-seo-statistics.css',
            array('kata-seo-variables', 'kata-seo-manager-admin'),
            $this->version
        );
        
        wp_enqueue_script(
            'kata-seo-manager-statistics',
            $this->assets_url . 'js/kata-seo-statistics.js',
            array('jquery', 'kata-seo-manager-admin'),
            $this->version,
            true
        );
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        global $post;
        
        // CSS Variables (load first)
        wp_enqueue_style(
            'kata-seo-variables',
            $this->assets_url . 'css/kata-seo-variables.css',
            array(),
            $this->version
        );
        
        // Frontend main CSS (always load - contains base styles)
        wp_enqueue_style(
            'kata-seo-manager-frontend',
            $this->assets_url . 'css/kata-seo-frontend.css',
            array('kata-seo-variables'),
            $this->version
        );
        
        // Frontend main JS (conditional)
        if ($this->should_load_frontend_js()) {
            wp_enqueue_script(
                'kata-seo-manager-frontend',
                $this->assets_url . 'js/kata-seo-frontend.js',
                array('jquery'),
                $this->version,
                true
            );
            
            wp_localize_script('kata-seo-manager-frontend', 'kataSeoFrontend', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kata_seo_frontend_nonce'),
            ));
        }
        
        // Conditional widget loading
        if ($post && is_singular()) {
            $content = $post->post_content;
            
            // Poll widget
            if (has_shortcode($content, 'kata_poll')) {
                $this->enqueue_poll_widget();
            }
            
            // Quiz widget
            if (has_shortcode($content, 'kata_quiz')) {
                $this->enqueue_quiz_widget();
            }
            
            // Wheel widget
            if (has_shortcode($content, 'kata_wheel')) {
                $this->enqueue_wheel_widget();
            }
        }
        
        // User tracking (conditional)
        if ($this->should_load_user_tracking()) {
            $this->enqueue_user_tracking();
        }
        
        // Schema frontend styles (if schemas exist)
        if ($this->has_schema_markup()) {
            wp_enqueue_style(
                'kata-seo-manager-schema-frontend',
                $this->assets_url . 'css/schema-frontend.css',
                array('kata-seo-manager-frontend'),
                $this->version
            );
        }
    }
    
    /**
     * Enqueue poll widget assets
     */
    private function enqueue_poll_widget() {
        wp_enqueue_style(
            'kata-seo-manager-poll',
            $this->assets_url . 'css/kata-seo-poll.css',
            array('kata-seo-manager-frontend'),
            $this->version
        );
        
        wp_enqueue_script(
            'kata-seo-manager-poll',
            $this->assets_url . 'js/kata-seo-poll.js',
            array('jquery', 'kata-seo-manager-frontend'),
            $this->version,
            true
        );
        
        wp_localize_script('kata-seo-manager-poll', 'kataSeoPoll', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_poll_nonce'),
            'i18n' => array(
                'voting' => __('Đang bình chọn...', 'kata-seo-manager'),
                'voted' => __('Cảm ơn bạn đã bình chọn!', 'kata-seo-manager'),
                'error' => __('Có lỗi xảy ra', 'kata-seo-manager'),
            )
        ));
    }
    
    /**
     * Enqueue quiz widget assets
     */
    private function enqueue_quiz_widget() {
        wp_enqueue_style(
            'kata-seo-manager-quiz',
            $this->assets_url . 'css/kata-seo-quiz.css',
            array('kata-seo-manager-frontend'),
            $this->version
        );
        
        wp_enqueue_script(
            'kata-seo-manager-quiz',
            $this->assets_url . 'js/kata-seo-quiz.js',
            array('jquery', 'kata-seo-manager-frontend'),
            $this->version,
            true
        );
        
        wp_localize_script('kata-seo-manager-quiz', 'kataSeoQuiz', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_quiz_nonce'),
            'i18n' => array(
                'submitting' => __('Đang gửi...', 'kata-seo-manager'),
                'submitted' => __('Đã gửi!', 'kata-seo-manager'),
                'selectAnswer' => __('Vui lòng chọn câu trả lời', 'kata-seo-manager'),
            )
        ));
    }
    
    /**
     * Enqueue wheel widget assets
     */
    private function enqueue_wheel_widget() {
        wp_enqueue_style(
            'kata-seo-manager-wheel',
            $this->assets_url . 'css/kata-seo-wheel.css',
            array('kata-seo-manager-frontend'),
            $this->version
        );
        
        wp_enqueue_script(
            'kata-seo-manager-wheel',
            $this->assets_url . 'js/kata-seo-wheel.js',
            array('jquery', 'kata-seo-manager-frontend'),
            $this->version,
            true
        );
        
        wp_localize_script('kata-seo-manager-wheel', 'kataSeoWheel', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_wheel_nonce'),
            'i18n' => array(
                'spinning' => __('Đang quay...', 'kata-seo-manager'),
                'congratulations' => __('Chúc mừng!', 'kata-seo-manager'),
                'tryAgain' => __('Thử lại', 'kata-seo-manager'),
            )
        ));
    }
    
    /**
     * Enqueue user tracking assets
     */
    private function enqueue_user_tracking() {
        wp_enqueue_style(
            'kata-seo-manager-user-tracking',
            $this->assets_url . 'css/kata-seo-user-tracking.css',
            array(),
            $this->version
        );
        
        wp_enqueue_script(
            'kata-seo-manager-user-tracking',
            $this->assets_url . 'js/kata-seo-user-tracking.js',
            array('jquery'),
            $this->version,
            true
        );
        
        wp_localize_script('kata-seo-manager-user-tracking', 'kataSeoTracking', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_seo_tracking_nonce'),
            'postId' => get_the_ID(),
        ));
    }
    
    /**
     * Enqueue editor assets
     */
    public function enqueue_editor_assets() {
        // TinyMCE styles
        wp_enqueue_style(
            'kata-seo-manager-tinymce',
            $this->assets_url . 'css/kata-seo-tinymce.css',
            array(),
            $this->version
        );
        
        // Editor styles
        if (file_exists(KATA_SEO_MANAGER_PLUGIN_DIR . 'assets/css/editor-styles.css')) {
            wp_enqueue_style(
                'kata-seo-manager-editor',
                $this->assets_url . 'css/editor-styles.css',
                array(),
                $this->version
            );
        }
    }
    
    /**
     * Check if should load frontend JS
     */
    private function should_load_frontend_js() {
        // Load on singular posts/pages
        if (is_singular()) {
            return true;
        }
        
        // Load on archive pages with widgets
        if (is_archive() && is_active_sidebar('kata-seo-widgets')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if should load user tracking
     */
    private function should_load_user_tracking() {
        // Check if tracking is enabled
        $tracking_enabled = get_option('kata_seo_enable_tracking', true);
        
        if (!$tracking_enabled) {
            return false;
        }
        
        // Only on singular posts/pages
        return is_singular();
    }
    
    /**
     * Check if page has schema markup
     */
    private function has_schema_markup() {
        if (!is_singular()) {
            return false;
        }
        
        $post_id = get_the_ID();
        
        // Check post meta
        $has_schema = get_post_meta($post_id, '_kata_seo_schema_type', true);
        
        return !empty($has_schema);
    }
    
    /**
     * Register TinyMCE plugin
     */
    public function register_tinymce_plugin($plugins) {
        $plugins['kata_seo'] = $this->assets_url . 'js/kata-seo-tinymce-plugin.js';
        return $plugins;
    }
    
    /**
     * Add TinyMCE button
     */
    public function register_tinymce_button($buttons) {
        array_push($buttons, 'kata_seo_button');
        return $buttons;
    }
}
