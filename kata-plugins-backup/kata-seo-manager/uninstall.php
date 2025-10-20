<?php
/**
 * Uninstall KATA SEO Manager
 *
 * Fired when the plugin is uninstalled.
 *
 * @package KATA_SEO_Manager
 * @version 1.0.0
 */

// Exit if accessed directly or not from uninstall process
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check for multisite
if (is_multisite()) {
    global $wpdb;
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        kata_seo_manager_uninstall();
        restore_current_blog();
    }
} else {
    kata_seo_manager_uninstall();
}

/**
 * Perform uninstall actions
 */
function kata_seo_manager_uninstall() {
    global $wpdb;
    
    // Get the option to determine if data should be deleted
    $delete_data = get_option('kata_seo_manager_delete_data_on_uninstall', false);
    
    if ($delete_data) {
        // Drop custom database tables
        $tables = array(
            $wpdb->prefix . 'kata_seo_schemas',
            $wpdb->prefix . 'kata_seo_schema_stats',
            $wpdb->prefix . 'kata_seo_schema_validation',
            $wpdb->prefix . 'kata_seo_schema_templates',
            $wpdb->prefix . 'kata_seo_schema_tracking',
            $wpdb->prefix . 'kata_seo_activity_log'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }
        
        // Delete all plugin options
        delete_option('kata_seo_manager_version');
        delete_option('kata_seo_manager_settings');
        delete_option('kata_seo_manager_db_version');
        delete_option('kata_seo_manager_activation_date');
        delete_option('kata_seo_manager_delete_data_on_uninstall');
        
        // Delete all transients
        delete_transient('kata_seo_manager_schema_cache');
        delete_transient('kata_seo_manager_stats_cache');
        
        // Delete all post meta created by plugin
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'kata_seo_%'");
        
        // Delete all user meta created by plugin
        $wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'kata_seo_%'");
        
        // Clear any scheduled events
        wp_clear_scheduled_hook('kata_seo_manager_daily_cleanup');
        wp_clear_scheduled_hook('kata_seo_manager_weekly_report');
        
    } else {
        // Just delete activation flag to allow clean reinstallation
        delete_option('kata_seo_manager_activation_date');
        delete_transient('kata_seo_manager_schema_cache');
        delete_transient('kata_seo_manager_stats_cache');
    }
}

/**
 * Log uninstall event
 */
function kata_seo_manager_log_uninstall() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'kata_seo_activity_log';
    
    // Check if table exists before logging
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name) {
        $wpdb->insert(
            $table_name,
            array(
                'action' => 'plugin_uninstalled',
                'user_id' => get_current_user_id(),
                'details' => json_encode(array(
                    'timestamp' => current_time('mysql'),
                    'site_url' => get_site_url(),
                    'wp_version' => get_bloginfo('version'),
                    'plugin_version' => '1.0.0'
                )),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%d', '%s', '%s')
        );
    }
}

// Log the uninstall event before cleanup
kata_seo_manager_log_uninstall();
