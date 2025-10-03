<?php
/**
 * Deactivation Hook Handler
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin deactivation callback
 */
function kata_seo_tools_deactivate() {
    global $wpdb;
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Clear all caches
    wp_cache_flush();
    
    // Delete transients
    delete_transient('kata_seo_quiz_stats');
    delete_transient('kata_seo_poll_stats');
    delete_transient('kata_seo_rating_stats');
    delete_transient('kata_seo_form_stats');
    delete_transient('kata_seo_wheel_stats');
    delete_transient('kata_seo_social_stats');
    
    // Log deactivation
    error_log('Kata SEO Tools deactivated at ' . current_time('mysql'));
    
    // Note: We don't delete options or database tables here
    // That's done in uninstall.php if user chooses to delete data
}

register_deactivation_hook(KATA_SEO_TOOLS_FILE, 'kata_seo_tools_deactivate');
