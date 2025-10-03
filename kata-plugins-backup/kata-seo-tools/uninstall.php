<?php
/**
 * Uninstall Handler
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Get plugin settings
$settings = get_option('kata_seo_settings', array());
$delete_data = isset($settings['delete_data_on_uninstall']) ? $settings['delete_data_on_uninstall'] : false;

// Only delete data if user opted in
if ($delete_data) {
    
    // Delete all database tables
    $tables = array(
        $wpdb->prefix . 'kata_seo_quiz_results',
        $wpdb->prefix . 'kata_seo_poll_votes',
        $wpdb->prefix . 'kata_seo_wheel_spins',
        $wpdb->prefix . 'kata_seo_ratings',
        $wpdb->prefix . 'kata_seo_form_submissions',
        $wpdb->prefix . 'kata_seo_social_shares'
    );
    
    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
    
    // Delete all options
    delete_option('kata_seo_settings');
    delete_option('kata_seo_tools_version');
    delete_option('kata_seo_tools_activated');
    
    // Delete all post meta
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'kata_seo_%'");
    
    // Delete all transients
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_kata_seo_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_kata_seo_%'");
    
    // Delete uploaded files (if any)
    $upload_dir = wp_upload_dir();
    $kata_upload_dir = $upload_dir['basedir'] . '/kata-seo-tools';
    
    if (is_dir($kata_upload_dir)) {
        kata_seo_delete_directory($kata_upload_dir);
    }
    
    // Clear all caches
    wp_cache_flush();
    
    // Log uninstall
    error_log('Kata SEO Tools uninstalled and all data deleted at ' . current_time('mysql'));
    
} else {
    // Just log that plugin was uninstalled but data kept
    error_log('Kata SEO Tools uninstalled (data retained) at ' . current_time('mysql'));
}

/**
 * Recursively delete a directory
 *
 * @param string $dir Directory path
 * @return bool
 */
function kata_seo_delete_directory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        if (!kata_seo_delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    
    return rmdir($dir);
}
