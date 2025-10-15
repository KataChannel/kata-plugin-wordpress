<?php
/**
 * Enqueue Handler
 * 
 * Manages all script and style enqueuing
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Enqueue_Handler {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        $plugin_url = plugin_dir_url(KATA_SEO_MANAGER_PLUGIN_DIR . 'kata-seo-manager.php');
        $version = KATA_SEO_MANAGER_VERSION;
        
        // Post editor scripts
        if (in_array($hook, array('post.php', 'post-new.php'))) {
            wp_enqueue_script(
                'kata-schema-attributes',
                $plugin_url . 'assets/js/schema-attributes-config.js',
                array(),
                $version,
                true
            );
            
            wp_enqueue_script(
                'kata-schema-builder',
                $plugin_url . 'assets/js/schema-builder-dialog.js',
                array('kata-schema-attributes'),
                $version,
                true
            );
        }
        
        // Only load on plugin pages
        if (strpos($hook, 'kata-seo') === false) {
            return;
        }
        
        // Admin CSS
        wp_enqueue_style(
            'kata-seo-admin',
            $plugin_url . 'assets/css/admin.css',
            array(),
            $version
        );
        
        // Schema Types CSS
        if ($hook === 'kata-seo_page_kata-seo-schema-types') {
            wp_enqueue_style(
                'kata-schema-types',
                $plugin_url . 'assets/css/schema-types.css',
                array('kata-seo-admin'),
                $version
            );
        }
        
        // Admin JS
        wp_enqueue_script(
            'kata-seo-admin',
            $plugin_url . 'assets/js/admin.js',
            array('jquery', 'wp-color-picker'),
            $version,
            true
        );
        
        // Localize script
        wp_localize_script('kata-seo-admin', 'kata_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_ajax_nonce'),
            'plugin_url' => $plugin_url
        ));
        
        // Color picker
        wp_enqueue_style('wp-color-picker');
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        $plugin_url = plugin_dir_url(KATA_SEO_MANAGER_PLUGIN_DIR . 'kata-seo-manager.php');
        $version = KATA_SEO_MANAGER_VERSION;
        
        // Main CSS
        wp_enqueue_style(
            'kata-seo-frontend',
            $plugin_url . 'assets/css/frontend.css',
            array(),
            $version
        );
        
        // Main JS
        wp_enqueue_script(
            'kata-seo-frontend',
            $plugin_url . 'assets/js/frontend.js',
            array('jquery'),
            $version,
            true
        );
        
        // Wheel assets
        wp_enqueue_style(
            'kata-wheel-frontend',
            $plugin_url . 'assets/css/wheel-frontend.css',
            array('kata-seo-frontend'),
            $version
        );
        
        wp_enqueue_script(
            'kata-wheel-frontend',
            $plugin_url . 'assets/js/wheel-frontend.js',
            array('jquery', 'kata-seo-frontend'),
            $version,
            true
        );
        
        // Poll assets
        wp_enqueue_style(
            'kata-poll-frontend',
            $plugin_url . 'assets/css/poll-frontend.css',
            array('kata-seo-frontend'),
            $version
        );
        
        wp_enqueue_script(
            'kata-poll-frontend',
            $plugin_url . 'assets/js/poll-frontend.js',
            array('jquery', 'kata-seo-frontend'),
            $version,
            true
        );
        
        // Schema CSS
        wp_enqueue_style(
            'kata-schema-frontend',
            $plugin_url . 'assets/css/schema-frontend.css',
            array('kata-seo-frontend'),
            $version
        );
        
        // Localize script
        wp_localize_script('kata-seo-frontend', 'kata_ajax', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_ajax_nonce'),
            'plugin_url' => $plugin_url,
            'is_user_logged_in' => is_user_logged_in(),
            'current_user_id' => get_current_user_id()
        ));
    }
}
