<?php
/**
 * Poll Manager
 * 
 * Manages poll operations in admin
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Poll_Manager {
    
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
        if (!isset($_GET['page']) || $_GET['page'] !== 'kata-seo-polls') {
            return;
        }
        
        if (!isset($_GET['action'])) {
            return;
        }
        
        $action = sanitize_text_field($_GET['action']);
        
        switch ($action) {
            case 'delete':
                $this->delete_poll();
                break;
            case 'duplicate':
                $this->duplicate_poll();
                break;
            case 'export':
                $this->export_poll();
                break;
        }
    }
    
    /**
     * Delete poll
     */
    private function delete_poll() {
        if (!isset($_GET['poll_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_poll_' . $_GET['poll_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $poll_id = sanitize_text_field($_GET['poll_id']);
        delete_option('kata_poll_' . $poll_id);
        delete_option('kata_poll_votes_' . $poll_id);
        
        wp_redirect(add_query_arg(array(
            'page' => 'kata-seo-polls',
            'message' => 'deleted'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Duplicate poll
     */
    private function duplicate_poll() {
        if (!isset($_GET['poll_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'duplicate_poll_' . $_GET['poll_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $poll_id = sanitize_text_field($_GET['poll_id']);
        $poll_data = get_option('kata_poll_' . $poll_id);
        
        if (!$poll_data) {
            wp_die(__('Poll not found', 'kata-seo-manager'));
        }
        
        // Create duplicate with new ID
        $new_poll_id = uniqid('poll_');
        $poll_data['title'] = $poll_data['title'] . ' (Copy)';
        add_option('kata_poll_' . $new_poll_id, $poll_data);
        
        wp_redirect(add_query_arg(array(
            'page' => 'kata-seo-polls',
            'message' => 'duplicated'
        ), admin_url('admin.php')));
        exit;
    }
    
    /**
     * Export poll
     */
    private function export_poll() {
        if (!isset($_GET['poll_id']) || !isset($_GET['_wpnonce'])) {
            return;
        }
        
        if (!wp_verify_nonce($_GET['_wpnonce'], 'export_poll_' . $_GET['poll_id'])) {
            wp_die(__('Invalid security token', 'kata-seo-manager'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-seo-manager'));
        }
        
        $poll_id = sanitize_text_field($_GET['poll_id']);
        $poll_data = get_option('kata_poll_' . $poll_id);
        $votes = get_option('kata_poll_votes_' . $poll_id, array());
        
        if (!$poll_data) {
            wp_die(__('Poll not found', 'kata-seo-manager'));
        }
        
        $export = array(
            'poll' => $poll_data,
            'votes' => $votes,
            'exported_at' => current_time('mysql')
        );
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="poll-' . $poll_id . '-' . date('Y-m-d') . '.json"');
        echo json_encode($export, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Get all polls
     * 
     * @return array
     */
    public function get_all_polls() {
        global $wpdb;
        
        $polls = array();
        $options = $wpdb->get_results(
            "SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE 'kata_poll_%' AND option_name NOT LIKE 'kata_poll_votes_%'"
        );
        
        foreach ($options as $option) {
            $poll_id = str_replace('kata_poll_', '', $option->option_name);
            $poll_data = maybe_unserialize($option->option_value);
            
            $polls[] = array(
                'id' => $poll_id,
                'data' => $poll_data,
                'votes' => $this->get_poll_votes($poll_id)
            );
        }
        
        return $polls;
    }
    
    /**
     * Get poll votes
     * 
     * @param string $poll_id Poll ID
     * @return array
     */
    public function get_poll_votes($poll_id) {
        return get_option('kata_poll_votes_' . $poll_id, array());
    }
}
