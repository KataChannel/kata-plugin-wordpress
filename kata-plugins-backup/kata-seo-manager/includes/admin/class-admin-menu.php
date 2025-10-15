<?php
/**
 * Admin Menu Handler
 * 
 * Manages plugin admin menu and pages
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Admin_Menu {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'register_menu'));
    }
    
    /**
     * Register admin menu
     */
    public function register_menu() {
        // Main menu
        add_menu_page(
            __('KATA SEO Manager', 'kata-seo-manager'),
            __('KATA SEO', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-manager',
            array($this, 'render_dashboard'),
            'dashicons-chart-line',
            30
        );
        
        // Dashboard
        add_submenu_page(
            'kata-seo-manager',
            __('Dashboard', 'kata-seo-manager'),
            __('Dashboard', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-manager',
            array($this, 'render_dashboard')
        );
        
        // Schema Types
        add_submenu_page(
            'kata-seo-manager',
            __('Schema Types', 'kata-seo-manager'),
            __('Schema Types', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-schema-types',
            array($this, 'render_schema_types')
        );
        
        // Schemas
        add_submenu_page(
            'kata-seo-manager',
            __('All Schemas', 'kata-seo-manager'),
            __('All Schemas', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-schemas',
            array($this, 'render_schemas')
        );
        
        // Add Schema
        add_submenu_page(
            'kata-seo-manager',
            __('Add Schema', 'kata-seo-manager'),
            __('Add Schema', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-add-schema',
            array($this, 'render_add_schema')
        );
        
        // Polls
        add_submenu_page(
            'kata-seo-manager',
            __('Polls', 'kata-seo-manager'),
            __('Polls', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-polls',
            array($this, 'render_polls')
        );
        
        // Wheels
        add_submenu_page(
            'kata-seo-manager',
            __('Wheels', 'kata-seo-manager'),
            __('Wheels', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-wheels',
            array($this, 'render_wheels')
        );
        
        // Analytics
        add_submenu_page(
            'kata-seo-manager',
            __('Analytics', 'kata-seo-manager'),
            __('Analytics', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-analytics',
            array($this, 'render_analytics')
        );
        
        // Settings
        add_submenu_page(
            'kata-seo-manager',
            __('Settings', 'kata-seo-manager'),
            __('Settings', 'kata-seo-manager'),
            'manage_options',
            'kata-seo-settings',
            array($this, 'render_settings')
        );
    }
    
    /**
     * Render dashboard page
     */
    public function render_dashboard() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/dashboard.php';
    }
    
    /**
     * Render schema types page
     */
    public function render_schema_types() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/schema-types.php';
    }
    
    /**
     * Render schemas list page
     */
    public function render_schemas() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/schemas.php';
    }
    
    /**
     * Render add schema page
     */
    public function render_add_schema() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/add-schema.php';
    }
    
    /**
     * Render polls page
     */
    public function render_polls() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/polls.php';
    }
    
    /**
     * Render wheels page
     */
    public function render_wheels() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/wheels.php';
    }
    
    /**
     * Render analytics page
     */
    public function render_analytics() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/analytics.php';
    }
    
    /**
     * Render settings page
     */
    public function render_settings() {
        include KATA_SEO_PLUGIN_DIR . 'admin/views/settings.php';
    }
}
