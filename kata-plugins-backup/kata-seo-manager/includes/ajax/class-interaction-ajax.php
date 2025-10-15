<?php
/**
 * Interaction AJAX Handler
 * 
 * Handles user interaction tracking
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Interaction_AJAX extends KATA_SEO_AJAX_Handler {
    
    /**
     * Initialize AJAX hooks
     */
    public function init() {
        // Track interactions
        add_action('wp_ajax_kata_track_interaction', array($this, 'track'));
        add_action('wp_ajax_nopriv_kata_track_interaction', array($this, 'track'));
        
        // Get interaction stats
        add_action('wp_ajax_kata_get_interaction_stats', array($this, 'get_stats'));
    }
    
    /**
     * Track user interaction
     */
    public function track() {
        $interaction_type = $this->sanitize_text($this->get_post('type'));
        $element_id = $this->sanitize_text($this->get_post('element_id'));
        $page_url = esc_url_raw($this->get_post('page_url'));
        
        if (empty($interaction_type) || empty($element_id)) {
            $this->error(__('Invalid interaction data', 'kata-seo-manager'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_interactions';
        
        // Create table if not exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
                id bigint(20) NOT NULL AUTO_INCREMENT,
                interaction_type varchar(50) NOT NULL,
                element_id varchar(100) NOT NULL,
                page_url varchar(255) DEFAULT NULL,
                user_ip varchar(45) DEFAULT NULL,
                user_agent text DEFAULT NULL,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY interaction_type (interaction_type),
                KEY element_id (element_id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        // Insert interaction
        $result = $wpdb->insert(
            $table_name,
            array(
                'interaction_type' => $interaction_type,
                'element_id' => $element_id,
                'page_url' => $page_url,
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            $this->error(__('Failed to track interaction', 'kata-seo-manager'));
        }
        
        $this->success(array(
            'message' => __('Interaction tracked', 'kata-seo-manager')
        ));
    }
    
    /**
     * Get interaction statistics
     */
    public function get_stats() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_interactions';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $this->success(array('stats' => array()));
            return;
        }
        
        $element_id = $this->sanitize_text($this->get_post('element_id'));
        $days = intval($this->get_post('days', 30));
        
        $where = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)";
        $params = array($days);
        
        if (!empty($element_id)) {
            $where .= " AND element_id = %s";
            $params[] = $element_id;
        }
        
        // Get total interactions
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name $where",
            $params
        ));
        
        // Get by type
        $by_type = $wpdb->get_results($wpdb->prepare(
            "SELECT interaction_type, COUNT(*) as count 
             FROM $table_name $where 
             GROUP BY interaction_type 
             ORDER BY count DESC",
            $params
        ));
        
        // Get by element
        $by_element = $wpdb->get_results($wpdb->prepare(
            "SELECT element_id, COUNT(*) as count 
             FROM $table_name $where 
             GROUP BY element_id 
             ORDER BY count DESC 
             LIMIT 10",
            $params
        ));
        
        // Get daily trends
        $daily = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM $table_name $where 
             GROUP BY DATE(created_at) 
             ORDER BY date DESC",
            $params
        ));
        
        $this->success(array(
            'stats' => array(
                'total' => $total,
                'by_type' => $by_type,
                'by_element' => $by_element,
                'daily' => $daily
            )
        ));
    }
}
