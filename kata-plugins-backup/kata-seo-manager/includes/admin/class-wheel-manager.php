<?php
/**
 * Wheel Manager
 * 
 * Manages wheel of fortune operations in admin
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Wheel_Manager {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_init', array($this, 'handle_actions'));
    }
    
    /**
     * Handle admin actions
     */
    public function handle_actions() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'kata-seo-wheels') {
            return;
        }
        
        if (!isset($_GET['action'])) {
            return;
        }
        
        $action = sanitize_text_field($_GET['action']);
        
        switch ($action) {
            case 'delete':
                $this->delete_wheel();
                break;
            case 'duplicate':
                $this->duplicate_wheel();
                break;
            case 'export':
                $this->export_wheel();
                break;
            case 'reset_spins':
                $this->reset_spins();
                break;
        }
    }
    
    /**
     * Delete wheel
     */
    private function delete_wheel() {
        if (!isset($_GET['wheel_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_wheel_' . $_GET['wheel_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $wheel_id = sanitize_text_field($_GET['wheel_id']);
        delete_option('kata_wheel_' . $wheel_id);
        delete_option('kata_wheel_spins_' . $wheel_id);
        
        wp_redirect(add_query_arg(array(
            'page' => 'kata-seo-wheels',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Duplicate wheel
     */
    private function duplicate_wheel() {
        if (!isset($_GET['wheel_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'duplicate_wheel_' . $_GET['wheel_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $wheel_id = sanitize_text_field($_GET['wheel_id']);
        $wheel_data = get_option('kata_wheel_' . $wheel_id);
        
        if (!$wheel_data) {
            wp_die(__('Wheel not found', 'kata-seo-manager'));
        }
        
        // Create duplicate with new ID
        $new_wheel_id = uniqid('wheel_');
        $wheel_data['title'] = $wheel_data['title'] . ' (Copy)';
        add_option('kata_wheel_' . $new_wheel_id, $wheel_data);
        
        wp_redirect(add_query_arg(array(
            'page' => 'kata-seo-wheels',
            'message' => 'duplicated'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Export wheel
     */
    private function export_wheel() {
        if (!isset($_GET['wheel_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'export_wheel_' . $_GET['wheel_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $wheel_id = sanitize_text_field($_GET['wheel_id']);
        $wheel_data = get_option('kata_wheel_' . $wheel_id);
        $spins = get_option('kata_wheel_spins_' . $wheel_id, array());
        
        if (!$wheel_data) {
            wp_die(__('Wheel not found', 'kata-seo-manager'));
        }
        
        $export = array(
            'wheel' => $wheel_data,
            'spins' => $spins,
            'exported_at' => current_time('mysql')
        );
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="wheel-' . $wheel_id . '-' . date('Y-m-d') . '.json"');
        echo json_encode($export, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Reset wheel spins
     */
    private function reset_spins() {
        if (!isset($_GET['wheel_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'reset_spins_' . $_GET['wheel_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $wheel_id = sanitize_text_field($_GET['wheel_id']);
        delete_option('kata_wheel_spins_' . $wheel_id);
        
        wp_redirect(add_query_arg(array(
            'page' => 'kata-seo-wheels',
            'message' => 'spins_reset'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Get all wheels
     * 
     * @return array
     */
    public function get_all_wheels() {
        global $wpdb;
        
        $wheels = array();
        $options = $wpdb->get_results(
            "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'kata_wheel_%' AND option_name NOT LIKE 'kata_wheel_spins_%'"
        );
        
        foreach ($options as $option) {
            $wheel_id = str_replace('kata_wheel_', '', $option->option_name);
            $wheel_data = maybe_unserialize($option->option_value);
            
            $wheels[] = array(
                'id' => $wheel_id,
                'data' => $wheel_data,
                'spins' => $this->get_wheel_spins($wheel_id)
            );
        }
        
        return $wheels;
    }
    
    /**
     * Get wheel spins
     * 
     * @param string $wheel_id Wheel ID
     * @return array
     */
    public function get_wheel_spins($wheel_id) {
        return get_option('kata_wheel_spins_' . $wheel_id, array());
    }
    
    /**
     * Get wheel statistics
     * 
     * @param string $wheel_id Wheel ID
     * @return array
     */
    public function get_wheel_statistics($wheel_id) {
        $spins = $this->get_wheel_spins($wheel_id);
        
        $stats = array(
            'total_spins' => count($spins),
            'segment_distribution' => array(),
            'last_spin' => null
        );
        
        if (!empty($spins)) {
            // Count segment distribution
            foreach ($spins as $spin) {
                $segment = $spin['segment'];
                if (!isset($stats['segment_distribution'][$segment])) {
                    $stats['segment_distribution'][$segment] = 0;
                }
                $stats['segment_distribution'][$segment]++;
            }
            
            // Get last spin
            $stats['last_spin'] = end($spins);
        }
        
        return $stats;
    }
}
