<?php
/**
 * Plugin Activator
 * 
 * Handles plugin activation and deactivation
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Activator {
    
    /**
     * Plugin activation
     */
    public static function activate() {
        try {
            // Create database tables
            $database = new KATA_SEO_Database();
            $result = $database->create_tables();
            
            if (!$result) {
                throw new Exception('Failed to create database tables');
            }
            
            // Create chatbot tables
            $chatbot = KATA_Smart_Chatbot::get_instance();
            $chatbot->create_tables();
            
            // Set default options
            add_option('kata_seo_manager_version', KATA_SEO_MANAGER_VERSION);
            add_option('kata_seo_manager_settings', array(
                'schema_output_location' => 'head',
                'enable_rich_snippets' => true,
                'enable_breadcrumbs' => true,
                'enable_article_schema' => true,
                'enable_faq_schema' => true,
                'activation_count' => 1
            ));
            
            // Set default chatbot options
            add_option('kata_chatbot_enabled', true);
            add_option('kata_chatbot_primary_color', '#042277');
            add_option('kata_chatbot_secondary_color', '#040B1E');
            add_option('kata_chatbot_bot_name', 'KATA Assistant');
            add_option('kata_chatbot_welcome_message', 'Xin chào! Tôi có thể giúp gì cho bạn?');
            
            // Initialize default schemas
            self::initialize_default_schemas();
            
            // Generate sample data
            KATA_SEO_Sample_Data::generate_all_sample_data();
            
            // Set activation flags
            update_option('kata_seo_manager_activated', true);
            update_option('kata_seo_manager_activation_date', current_time('mysql'));
            update_option('kata_seo_sample_data_created', true);
            
            flush_rewrite_rules();
            
        } catch (Exception $e) {
            error_log('KATA SEO Manager Activation Error: ' . $e->getMessage());
            add_option('kata_seo_manager_activation_error', $e->getMessage());
        }
    }
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * Initialize default schemas
     */
    private static function initialize_default_schemas() {
        $default_schemas = array(
            'article' => array(
                'enabled' => true,
                'auto_insert' => true,
                'post_types' => array('post')
            ),
            'webpage' => array(
                'enabled' => true,
                'auto_insert' => true,
                'post_types' => array('page')
            ),
            'breadcrumb' => array(
                'enabled' => true,
                'auto_insert' => false,
                'post_types' => array()
            )
        );
        
        add_option('kata_seo_default_schemas', $default_schemas);
    }
}
