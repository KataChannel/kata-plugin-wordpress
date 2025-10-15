<?php
/**
 * Base AJAX Handler
 * 
 * Provides common AJAX functionality for all handlers
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class KATA_SEO_AJAX_Handler {
    
    /**
     * Initialize AJAX hooks
     */
    abstract public function init();
    
    /**
     * Verify nonce and permissions
     * 
     * @param string $nonce_action Nonce action name
     * @param string $capability Required capability (default: edit_posts)
     * @return bool|WP_Error
     */
    protected function verify_request($nonce_action, $capability = 'edit_posts') {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], $nonce_action)) {
            return new WP_Error('invalid_nonce', __('Invalid security token', 'kata-seo-manager'));
        }
        
        // Check permissions
        if (!current_user_can($capability)) {
            return new WP_Error('insufficient_permissions', __('You do not have permission to perform this action', 'kata-seo-manager'));
        }
        
        return true;
    }
    
    /**
     * Send JSON success response
     * 
     * @param mixed $data Response data
     */
    protected function success($data = null) {
        wp_send_json_success($data);
    }
    
    /**
     * Send JSON error response
     * 
     * @param string $message Error message
     * @param mixed $data Additional error data
     */
    protected function error($message, $data = null) {
        wp_send_json_error(array(
            'message' => $message,
            'data' => $data
        ));
    }
    
    /**
     * Get POST parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function get_post($key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Sanitize text input
     * 
     * @param string $value Input value
     * @return string
     */
    protected function sanitize_text($value) {
        return sanitize_text_field(wp_unslash($value));
    }
    
    /**
     * Sanitize textarea input
     * 
     * @param string $value Input value
     * @return string
     */
    protected function sanitize_textarea($value) {
        return sanitize_textarea_field(wp_unslash($value));
    }
}
