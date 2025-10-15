<?php
/**
 * Wheel AJAX Handler
 * 
 * Handles all wheel of fortune AJAX requests
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Wheel_AJAX extends KATA_SEO_AJAX_Handler {
    
    /**
     * Initialize AJAX hooks
     */
    public function init() {
        // Wheel management
        add_action('wp_ajax_kata_save_wheel', array($this, 'save_wheel'));
        add_action('wp_ajax_kata_delete_wheel', array($this, 'delete_wheel'));
        add_action('wp_ajax_kata_get_wheel', array($this, 'get_wheel'));
        
        // Wheel spinning (frontend)
        add_action('wp_ajax_kata_spin_wheel', array($this, 'spin'));
        add_action('wp_ajax_nopriv_kata_spin_wheel', array($this, 'spin'));
        
        // Wheel results
        add_action('wp_ajax_kata_get_wheel_stats', array($this, 'get_stats'));
    }
    
    /**
     * Save wheel
     */
    public function save_wheel() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $wheel_id = $this->get_post('wheel_id');
        $wheel_data = $this->get_post('wheel_data');
        
        if (empty($wheel_data)) {
            $this->error(__('Wheel data is required', 'kata-seo-manager'));
        }
        
        if ($wheel_id) {
            update_option('kata_wheel_' . $wheel_id, $wheel_data);
            $message = __('Wheel updated successfully', 'kata-seo-manager');
        } else {
            $wheel_id = uniqid('wheel_');
            add_option('kata_wheel_' . $wheel_id, $wheel_data);
            $message = __('Wheel created successfully', 'kata-seo-manager');
        }
        
        $this->success(array(
            'message' => $message,
            'wheel_id' => $wheel_id
        ));
    }
    
    /**
     * Delete wheel
     */
    public function delete_wheel() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $wheel_id = $this->sanitize_text($this->get_post('wheel_id'));
        
        if (empty($wheel_id)) {
            $this->error(__('Invalid wheel ID', 'kata-seo-manager'));
        }
        
        delete_option('kata_wheel_' . $wheel_id);
        delete_option('kata_wheel_spins_' . $wheel_id);
        
        $this->success(array(
            'message' => __('Wheel deleted successfully', 'kata-seo-manager')
        ));
    }
    
    /**
     * Get wheel
     */
    public function get_wheel() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $wheel_id = $this->sanitize_text($this->get_post('wheel_id'));
        
        if (empty($wheel_id)) {
            $this->error(__('Invalid wheel ID', 'kata-seo-manager'));
        }
        
        $wheel_data = get_option('kata_wheel_' . $wheel_id);
        
        if (!$wheel_data) {
            $this->error(__('Wheel not found', 'kata-seo-manager'));
        }
        
        $this->success(array('wheel' => $wheel_data));
    }
    
    /**
     * Spin the wheel
     */
    public function spin() {
        // No admin check for frontend spinning
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kata_wheel_spin')) {
            $this->error(__('Invalid security token', 'kata-seo-manager'));
        }
        
        $wheel_id = $this->sanitize_text($this->get_post('wheel_id'));
        
        if (empty($wheel_id)) {
            $this->error(__('Invalid wheel ID', 'kata-seo-manager'));
        }
        
        // Check if already spun
        $cookie_name = 'kata_wheel_spun_' . $wheel_id;
        if (isset($_COOKIE[$cookie_name])) {
            $this->error(__('You have already spun this wheel', 'kata-seo-manager'));
        }
        
        // Get wheel data
        $wheel_data = get_option('kata_wheel_' . $wheel_id);
        
        if (!$wheel_data || empty($wheel_data['segments'])) {
            $this->error(__('Wheel not found or has no segments', 'kata-seo-manager'));
        }
        
        // Calculate weighted random segment
        $segments = $wheel_data['segments'];
        $total_probability = array_sum(array_column($segments, 'probability'));
        $random = mt_rand(1, $total_probability);
        
        $current_probability = 0;
        $winning_segment = null;
        
        foreach ($segments as $index => $segment) {
            $current_probability += $segment['probability'];
            if ($random <= $current_probability) {
                $winning_segment = array(
                    'index' => $index,
                    'label' => $segment['label'],
                    'value' => $segment['value']
                );
                break;
            }
        }
        
        // Record spin
        $spins = get_option('kata_wheel_spins_' . $wheel_id, array());
        $spins[] = array(
            'segment' => $winning_segment['index'],
            'time' => current_time('mysql'),
            'user_ip' => $_SERVER['REMOTE_ADDR']
        );
        update_option('kata_wheel_spins_' . $wheel_id, $spins);
        
        // Set cookie to prevent duplicate spins (24 hours)
        setcookie($cookie_name, '1', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        
        $this->success(array(
            'message' => __('Spin successful', 'kata-seo-manager'),
            'result' => $winning_segment
        ));
    }
    
    /**
     * Get wheel statistics
     */
    public function get_stats() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $wheel_id = $this->sanitize_text($this->get_post('wheel_id'));
        
        if (empty($wheel_id)) {
            $this->error(__('Invalid wheel ID', 'kata-seo-manager'));
        }
        
        $spins = get_option('kata_wheel_spins_' . $wheel_id, array());
        $wheel_data = get_option('kata_wheel_' . $wheel_id);
        
        // Calculate statistics
        $stats = array(
            'total_spins' => count($spins),
            'segment_counts' => array()
        );
        
        foreach ($spins as $spin) {
            $segment_index = $spin['segment'];
            if (!isset($stats['segment_counts'][$segment_index])) {
                $stats['segment_counts'][$segment_index] = 0;
            }
            $stats['segment_counts'][$segment_index]++;
        }
        
        $this->success(array(
            'stats' => $stats,
            'wheel' => $wheel_data
        ));
    }
}
