<?php
/**
 * Admin Page Loader
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get the current page
$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';

// Enqueue admin assets
wp_enqueue_script('kata-seo-admin');
wp_enqueue_script('kata-seo-analyzer');
wp_enqueue_style('kata-seo-admin');

// Add Chart.js for dashboard
if ($current_page === 'kata-seo-analyzer') {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
}

// Localize script with AJAX data
wp_localize_script('kata-seo-admin', 'kataSEO', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('kata_seo_nonce'),
    'postId' => get_the_ID(),
    'autoRefresh' => get_option('kata_seo_auto_refresh', true),
    'strings' => array(
        'analyzing' => __('Analyzing...', 'kata-seo-analyzer'),
        'analyzed' => __('Analysis completed', 'kata-seo-analyzer'),
        'error' => __('An error occurred', 'kata-seo-analyzer'),
        'confirm_delete' => __('Are you sure you want to delete this item?', 'kata-seo-analyzer'),
        'no_posts_selected' => __('Please select at least one post', 'kata-seo-analyzer'),
        'bulk_analysis_complete' => __('Bulk analysis completed', 'kata-seo-analyzer'),
    ),
));

// Load appropriate template based on current page
switch ($current_page) {
    case 'kata-seo-analyzer':
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/dashboard.php';
        break;
        
    case 'kata-seo-content':
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/content-analysis.php';
        break;
        
    case 'kata-seo-competitors':
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/competitors.php';
        break;
        
    case 'kata-seo-keywords':
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/keywords.php';
        break;
        
    case 'kata-seo-reports':
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/reports.php';
        break;
        
    case 'kata-seo-settings':
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/settings.php';
        break;
        
    default:
        // Fallback to dashboard
        include_once KATA_SEO_PLUGIN_DIR . 'templates/admin/dashboard.php';
        break;
}
?>
