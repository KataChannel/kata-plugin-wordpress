<?php
/**
 * KATA Schema TinyMCE Integration Class
 * 
 * Handles TinyMCE button and modal integration
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class KATA_Schema_TinyMCE {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Only run in admin
        if (!is_admin()) {
            return;
        }
        
        add_action('admin_init', array($this, 'setup_tinymce'));
    }
    
    /**
     * Setup TinyMCE integration
     */
    public function setup_tinymce() {
        // Check if user can edit posts/pages
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        
        // Check if Rich Editing is enabled
        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_external_plugins', array($this, 'register_tinymce_plugin'));
            add_filter('mce_buttons', array($this, 'register_tinymce_button'));
        }
    }
    
    /**
     * Register TinyMCE plugin
     */
    public function register_tinymce_plugin($plugins) {
        // Prevent duplicate registration (learned from kata-seo-manager bugfix)
        if (isset($plugins['kata_schema'])) {
            return $plugins;
        }
        
        $plugins['kata_schema'] = KATA_SCHEMA_PLUGIN_URL . 'assets/js/tinymce-button.js';
        
        return $plugins;
    }
    
    /**
     * Register TinyMCE button
     */
    public function register_tinymce_button($buttons) {
        // Prevent duplicate button
        if (in_array('kata_schema', $buttons)) {
            return $buttons;
        }
        
        array_push($buttons, 'kata_schema');
        
        return $buttons;
    }
    
    /**
     * Enqueue scripts for TinyMCE
     */
    public function enqueue_scripts() {
        // Get all schemas for the modal
        $database = new KATA_Schema_Database();
        $schemas = $database->get_all_schemas('active');
        
        // Group by type
        $grouped_schemas = array();
        foreach ($schemas as $schema) {
            if (!isset($grouped_schemas[$schema->schema_type])) {
                $grouped_schemas[$schema->schema_type] = array();
            }
            $grouped_schemas[$schema->schema_type][] = array(
                'id' => $schema->id,
                'name' => $schema->schema_name,
                'type' => $schema->schema_type
            );
        }
        
        // Pass data to JavaScript
        wp_localize_script('kata-schema-admin', 'kataSchemaData', array(
            'schemas' => $grouped_schemas,
            'schemaTypes' => KATA_Schema_Templates::get_schema_types(),
            'nonce' => wp_create_nonce('kata_schema_tinymce')
        ));
    }
}
