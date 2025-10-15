<?php
/**
 * Admin Notices Handler
 * 
 * Manages admin notices and alerts
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Admin_Notices {
    
    /**
     * Notices to display
     * @var array
     */
    private $notices = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_notices', array($this, 'display_notices'));
        $this->load_notices();
    }
    
    /**
     * Load notices from options
     */
    private function load_notices() {
        // Check for activation notice
        if (get_option('kata_seo_show_activation_notice')) {
            $this->add_notice(
                'success',
                __('KATA SEO Manager has been activated successfully! Get started by creating your first schema.', 'kata-seo-manager'),
                true
            );
            delete_option('kata_seo_show_activation_notice');
        }
        
        // Check for update notice
        $current_version = get_option('kata_seo_version');
        if (version_compare($current_version, KATA_SEO_VERSION, '<')) {
            $this->add_notice(
                'info',
                sprintf(
                    __('KATA SEO Manager has been updated to version %s. Check out the changelog for new features!', 'kata-seo-manager'),
                    KATA_SEO_VERSION
                ),
                true
            );
            update_option('kata_seo_version', KATA_SEO_VERSION);
        }
        
        // Check for schema validation errors
        $validation_errors = get_transient('kata_seo_validation_errors');
        if ($validation_errors) {
            $this->add_notice(
                'error',
                __('Some schemas have validation errors. Please review and fix them.', 'kata-seo-manager')
            );
            delete_transient('kata_seo_validation_errors');
        }
    }
    
    /**
     * Add notice
     * 
     * @param string $type Notice type (success, error, warning, info)
     * @param string $message Notice message
     * @param bool $dismissible Whether notice is dismissible
     */
    public function add_notice($type, $message, $dismissible = false) {
        $this->notices[] = array(
            'type' => $type,
            'message' => $message,
            'dismissible' => $dismissible
        );
    }
    
    /**
     * Display notices
     */
    public function display_notices() {
        // Only show on plugin pages
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'kata-seo') === false) {
            return;
        }
        
        foreach ($this->notices as $notice) {
            $class = 'notice notice-' . $notice['type'];
            if ($notice['dismissible']) {
                $class .= ' is-dismissible';
            }
            
            printf(
                '<div class="%1$s"><p>%2$s</p></div>',
                esc_attr($class),
                wp_kses_post($notice['message'])
            );
        }
    }
    
    /**
     * Add success notice
     * 
     * @param string $message Notice message
     */
    public static function success($message) {
        set_transient('kata_seo_success_notice', $message, 30);
    }
    
    /**
     * Add error notice
     * 
     * @param string $message Notice message
     */
    public static function error($message) {
        set_transient('kata_seo_error_notice', $message, 30);
    }
    
    /**
     * Add warning notice
     * 
     * @param string $message Notice message
     */
    public static function warning($message) {
        set_transient('kata_seo_warning_notice', $message, 30);
    }
    
    /**
     * Add info notice
     * 
     * @param string $message Notice message
     */
    public static function info($message) {
        set_transient('kata_seo_info_notice', $message, 30);
    }
}
