<?php
/**
 * Plugin Name: KATA SEO Manager
 * Plugin URI: https://katachannel.com/kata-seo-manager
 * Description: Complete SEO Schema Manager with 26 Schema Types - Manage, configure, and track all KATA SEO features with Google-compliant Schema Markup
 * Version: 2.1.3
 * Author: KATA Channel
 * Author URI: https://katachannel.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kata-seo-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * @package KATA_SEO_Manager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Constants
 */
define('KATA_SEO_VERSION', '2.1.3');
define('KATA_SEO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATA_SEO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_SEO_PLUGIN_FILE', __FILE__);

// Legacy constants for backward compatibility
define('KATA_SEO_MANAGER_VERSION', KATA_SEO_VERSION);
define('KATA_SEO_MANAGER_PLUGIN_DIR', KATA_SEO_PLUGIN_DIR);
define('KATA_SEO_MANAGER_PLUGIN_URL', KATA_SEO_PLUGIN_URL);
define('KATA_SEO_MANAGER_PLUGIN_FILE', KATA_SEO_PLUGIN_FILE);
define('KATA_SEO_MANAGER_PATH', KATA_SEO_PLUGIN_DIR);

/**
 * Load legacy includes (required for existing functionality)
 */
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-database.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-sample-data.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-demo-content.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-schema-generator.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-editor-integration.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-admin-settings.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-statistics.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-quiz-manager.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-schema-customizer.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-schema-admin-ui.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-smart-chatbot.php';

// Load schema handlers
require_once KATA_SEO_PLUGIN_DIR . 'includes/schemas/class-base-schema.php';

$schema_files = array(
    'class-article-schema.php',
    'class-breadcrumb-schema.php',
    'class-carousel-schema.php',
    'class-course-schema.php',
    'class-dataset-schema.php',
    'class-forum-schema.php',
    'class-edu-qa-schema.php',
    'class-employer-rating-schema.php',
    'class-event-schema.php',
    'class-faq-schema.php',
    'class-how-to-schema.php',
    'class-image-metadata-schema.php',
    'class-job-posting-schema.php',
    'class-local-business-schema.php',
    'class-math-solver-schema.php',
    'class-media-object-schema.php',
    'class-organization-schema.php',
    'class-product-schema.php',
    'class-recipe-schema.php',
    'class-review-schema.php',
    'class-software-app-schema.php',
    'class-speakable-schema.php',
    'class-video-schema.php',
    'class-website-schema.php',
    'class-person-schema.php',
    'class-creative-work-schema.php'
);

foreach ($schema_files as $file) {
    $filepath = KATA_SEO_PLUGIN_DIR . 'includes/schemas/' . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

/**
 * Load new modular architecture
 */
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-activator.php';
require_once KATA_SEO_PLUGIN_DIR . 'includes/class-core.php';

/**
 * Initialize plugin
 * 
 * @return KATA_SEO_Core
 */
function kata_seo_init() {
    return KATA_SEO_Core::get_instance();
}

/**
 * Activation hook
 */
function kata_seo_activate() {
    KATA_SEO_Activator::activate();
}

/**
 * Deactivation hook
 */
function kata_seo_deactivate() {
    KATA_SEO_Activator::deactivate();
}

// Register hooks
register_activation_hook(__FILE__, 'kata_seo_activate');
register_deactivation_hook(__FILE__, 'kata_seo_deactivate');

// Start plugin
add_action('plugins_loaded', 'kata_seo_init', 10);

/**
 * Legacy function for backward compatibility
 * 
 * @deprecated 2.1.3 Use kata_seo_init() instead
 * @return KATA_SEO_Core
 */
function kata_seo_manager() {
    return kata_seo_init();
}
