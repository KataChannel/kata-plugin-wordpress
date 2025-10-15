<?php
/**
 * Poll AJAX Handler
 * 
 * Handles all poll-related AJAX requests
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Poll_AJAX extends KATA_SEO_AJAX_Handler {
    
    /**
     * Initialize AJAX hooks
     */
    public function init() {
        // Poll management
        add_action('wp_ajax_kata_save_poll', array($this, 'save_poll'));
        add_action('wp_ajax_kata_delete_poll', array($this, 'delete_poll'));
        add_action('wp_ajax_kata_get_poll', array($this, 'get_poll'));
        
        // Poll voting (frontend)
        add_action('wp_ajax_kata_poll_vote', array($this, 'vote'));
        add_action('wp_ajax_nopriv_kata_poll_vote', array($this, 'vote'));
        
        // Poll results
        add_action('wp_ajax_kata_get_poll_results', array($this, 'get_results'));
        add_action('wp_ajax_nopriv_kata_get_poll_results', array($this, 'get_results'));
    }
    
    /**
     * Save poll
     */
    public function save_poll() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $poll_id = $this->get_post('poll_id');
        $poll_data = $this->get_post('poll_data');
        
        if (empty($poll_data)) {
            $this->error(__('Poll data is required', 'kata-seo-manager'));
        }
        
        if ($poll_id) {
            update_option('kata_poll_' . $poll_id, $poll_data);
            $message = __('Poll updated successfully', 'kata-seo-manager');
        } else {
            $poll_id = uniqid('poll_');
            add_option('kata_poll_' . $poll_id, $poll_data);
            $message = __('Poll created successfully', 'kata-seo-manager');
        }
        
        $this->success(array(
            'message' => $message,
            'poll_id' => $poll_id
        ));
    }
    
    /**
     * Delete poll
     */
    public function delete_poll() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $poll_id = $this->sanitize_text($this->get_post('poll_id'));
        
        if (empty($poll_id)) {
            $this->error(__('Invalid poll ID', 'kata-seo-manager'));
        }
        
        delete_option('kata_poll_' . $poll_id);
        delete_option('kata_poll_votes_' . $poll_id);
        
        $this->success(array(
            'message' => __('Poll deleted successfully', 'kata-seo-manager')
        ));
    }
    
    /**
     * Get poll
     */
    public function get_poll() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $poll_id = $this->sanitize_text($this->get_post('poll_id'));
        
        if (empty($poll_id)) {
            $this->error(__('Invalid poll ID', 'kata-seo-manager'));
        }
        
        $poll_data = get_option('kata_poll_' . $poll_id);
        
        if (!$poll_data) {
            $this->error(__('Poll not found', 'kata-seo-manager'));
        }
        
        $this->success(array('poll' => $poll_data));
    }
    
    /**
     * Vote in poll
     */
    public function vote() {
        // No admin check for frontend voting
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kata_poll_vote')) {
            $this->error(__('Invalid security token', 'kata-seo-manager'));
        }
        
        $poll_id = $this->sanitize_text($this->get_post('poll_id'));
        $option_index = intval($this->get_post('option'));
        
        if (empty($poll_id)) {
            $this->error(__('Invalid poll ID', 'kata-seo-manager'));
        }
        
        // Check if already voted
        $cookie_name = 'kata_poll_voted_' . $poll_id;
        if (isset($_COOKIE[$cookie_name])) {
            $this->error(__('You have already voted in this poll', 'kata-seo-manager'));
        }
        
        // Get current votes
        $votes = get_option('kata_poll_votes_' . $poll_id, array());
        
        if (!isset($votes[$option_index])) {
            $votes[$option_index] = 0;
        }
        
        $votes[$option_index]++;
        
        update_option('kata_poll_votes_' . $poll_id, $votes);
        
        // Set cookie to prevent duplicate voting (24 hours)
        setcookie($cookie_name, '1', time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        
        $this->success(array(
            'message' => __('Vote recorded successfully', 'kata-seo-manager'),
            'votes' => $votes
        ));
    }
    
    /**
     * Get poll results
     */
    public function get_results() {
        $poll_id = $this->sanitize_text($this->get_post('poll_id'));
        
        if (empty($poll_id)) {
            $this->error(__('Invalid poll ID', 'kata-seo-manager'));
        }
        
        $votes = get_option('kata_poll_votes_' . $poll_id, array());
        $poll_data = get_option('kata_poll_' . $poll_id);
        
        $this->success(array(
            'votes' => $votes,
            'poll' => $poll_data
        ));
    }
}
