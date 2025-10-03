<?php
/**
 * Poll Handler
 * 
 * Handles poll/voting system with real-time results
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_Poll_Handler {
    
    /**
     * Render poll shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'id' => 'poll-' . uniqid(),
            'question' => 'Poll Question',
            'options' => '[]',
            'style' => 'default',
            'allow_multiple' => 'no',
            'show_results' => 'after_vote'
        ), $atts);
        
        // Decode options
        $options = json_decode($atts['options'], true);
        if (!$options) {
            $options = json_decode(base64_decode($atts['options']), true);
        }
        
        if (!$options || !is_array($options)) {
            return '<div class="kata-poll-error">Invalid poll options.</div>';
        }
        
        $post_id = get_the_ID();
        $results = $this->get_poll_results($atts['id']);
        $has_voted = $this->has_user_voted($atts['id']);
        $show_results = ($atts['show_results'] === 'always' || ($atts['show_results'] === 'after_vote' && $has_voted));
        
        ob_start();
        ?>
        <div class="kata-poll kata-poll-<?php echo esc_attr($atts['style']); ?>" 
             data-poll-id="<?php echo esc_attr($atts['id']); ?>"
             data-post-id="<?php echo esc_attr($post_id); ?>"
             data-has-voted="<?php echo $has_voted ? '1' : '0'; ?>">
            
            <div class="poll-header">
                <h4 class="poll-question">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M18,13H13V18H11V13H6V11H11V6H13V11H18V13Z"/>
                    </svg>
                    <?php echo esc_html($atts['question']); ?>
                </h4>
            </div>
            
            <div class="poll-options">
                <?php foreach ($options as $index => $option) : 
                    $option_text = is_array($option) ? $option['text'] : $option;
                    $option_image = is_array($option) && isset($option['image']) ? $option['image'] : '';
                    $votes = isset($results[$index]) ? $results[$index]['votes'] : 0;
                    $percentage = isset($results[$index]) ? $results[$index]['percentage'] : 0;
                ?>
                    <div class="poll-option" data-option-index="<?php echo $index; ?>">
                        <label class="poll-option-label">
                            <input type="<?php echo $atts['allow_multiple'] === 'yes' ? 'checkbox' : 'radio'; ?>" 
                                   name="poll_option<?php echo $atts['allow_multiple'] === 'yes' ? '[]' : ''; ?>" 
                                   value="<?php echo $index; ?>"
                                   <?php echo $has_voted ? 'disabled' : ''; ?>>
                            
                            <?php if ($option_image) : ?>
                                <img src="<?php echo esc_url($option_image); ?>" 
                                     alt="<?php echo esc_attr($option_text); ?>"
                                     class="option-image">
                            <?php endif; ?>
                            
                            <span class="option-text"><?php echo esc_html($option_text); ?></span>
                        </label>
                        
                        <?php if ($show_results) : ?>
                            <div class="poll-result-bar">
                                <div class="poll-bar" 
                                     style="width: <?php echo $percentage; ?>%"
                                     data-percentage="<?php echo $percentage; ?>">
                                </div>
                                <div class="poll-stats">
                                    <span class="poll-percentage"><?php echo number_format($percentage, 1); ?>%</span>
                                    <span class="poll-votes">(<?php echo number_format($votes); ?> votes)</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!$has_voted) : ?>
                <div class="poll-actions">
                    <button type="button" class="poll-submit-btn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                            <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                        </svg>
                        Vote
                    </button>
                </div>
            <?php else : ?>
                <div class="poll-voted-message">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                    </svg>
                    Bạn đã vote rồi! Cảm ơn bạn đã tham gia.
                </div>
            <?php endif; ?>
            
            <?php 
            $total_votes = array_sum(array_column($results, 'votes'));
            if ($total_votes > 0 && $show_results) : 
            ?>
                <div class="poll-total">
                    Tổng số phiếu: <strong><?php echo number_format($total_votes); ?></strong>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Submit poll vote
     * 
     * @param array $data Vote data
     * @return array Results
     */
    public function submit_vote($data) {
        global $wpdb;
        
        $poll_id = sanitize_text_field($data['poll_id']);
        $option_ids = isset($data['option_ids']) ? $data['option_ids'] : array($data['option_id']);
        $post_id = intval($data['post_id']);
        
        if (!is_array($option_ids)) {
            $option_ids = array($option_ids);
        }
        
        $table = $wpdb->prefix . 'kata_seo_poll_votes';
        
        // Check if already voted
        if ($this->has_user_voted($poll_id)) {
            return array('error' => 'Bạn đã vote rồi!');
        }
        
        // Insert votes
        $success = true;
        foreach ($option_ids as $option_id) {
            $option_id = intval($option_id);
            
            $result = $wpdb->insert($table, array(
                'post_id' => $post_id,
                'poll_id' => $poll_id,
                'option_id' => $option_id,
                'user_id' => get_current_user_id() ?: null,
                'ip_address' => $this->get_client_ip(),
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255),
                'voted_at' => current_time('mysql')
            ), array('%d', '%s', '%d', '%d', '%s', '%s', '%s'));
            
            if (!$result) {
                $success = false;
            }
        }
        
        if (!$success) {
            return array('error' => 'Có lỗi xảy ra khi lưu vote');
        }
        
        return array(
            'success' => true,
            'message' => 'Vote thành công!',
            'results' => $this->get_poll_results($poll_id)
        );
    }
    
    /**
     * Get poll results
     * 
     * @param string $poll_id Poll ID
     * @return array Results by option
     */
    public function get_poll_results($poll_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_poll_votes';
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT option_id, COUNT(*) as votes 
             FROM $table 
             WHERE poll_id = %s 
             GROUP BY option_id",
            $poll_id
        ), ARRAY_A);
        
        $total = array_sum(array_column($results, 'votes'));
        
        $formatted = array();
        foreach ($results as $result) {
            $formatted[$result['option_id']] = array(
                'votes' => intval($result['votes']),
                'percentage' => $total > 0 ? round(($result['votes'] / $total) * 100, 1) : 0
            );
        }
        
        return $formatted;
    }
    
    /**
     * Check if user has voted
     * 
     * @param string $poll_id Poll ID
     * @return bool True if voted
     */
    private function has_user_voted($poll_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_poll_votes';
        $ip = $this->get_client_ip();
        $user_id = get_current_user_id();
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
             FROM $table 
             WHERE poll_id = %s 
             AND (ip_address = %s OR (user_id IS NOT NULL AND user_id = %d))",
            $poll_id, $ip, $user_id
        ));
        
        return $count > 0;
    }
    
    /**
     * Get client IP address
     * 
     * @return string IP address
     */
    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field(trim($ip));
    }
    
    /**
     * Get poll analytics
     * 
     * @param string $poll_id Poll ID
     * @return array Analytics data
     */
    public function get_poll_analytics($poll_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_poll_votes';
        
        $analytics = array(
            'total_votes' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE poll_id = %s",
                $poll_id
            )),
            'unique_voters' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT ip_address) FROM $table WHERE poll_id = %s",
                $poll_id
            )),
            'results' => $this->get_poll_results($poll_id),
            'votes_over_time' => $wpdb->get_results($wpdb->prepare(
                "SELECT DATE(voted_at) as date, COUNT(*) as count 
                 FROM $table 
                 WHERE poll_id = %s 
                 GROUP BY DATE(voted_at) 
                 ORDER BY date DESC 
                 LIMIT 30",
                $poll_id
            ), ARRAY_A)
        );
        
        return $analytics;
    }
}
