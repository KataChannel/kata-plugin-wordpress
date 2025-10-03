<?php
/**
 * Rating Handler
 * 
 * Handles star ratings and reviews with aggregate rating
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_Rating_Handler {
    
    /**
     * Render rating shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
            'style' => 'stars',
            'show_count' => 'yes',
            'allow_review' => 'yes',
            'require_login' => 'no'
        ), $atts);
        
        $post_id = intval($atts['post_id']);
        $aggregate = $this->get_aggregate_rating($post_id);
        $user_rating = $this->get_user_rating($post_id);
        $can_rate = $this->can_user_rate($post_id, $atts['require_login']);
        
        ob_start();
        ?>
        <div class="kata-rating-wrapper" 
             data-post-id="<?php echo $post_id; ?>"
             data-style="<?php echo esc_attr($atts['style']); ?>">
            
            <div class="kata-rating-display">
                <div class="kata-rating-stars kata-rating-<?php echo esc_attr($atts['style']); ?>">
                    <?php for ($i = 1; $i <= 5; $i++) : 
                        $fill_class = '';
                        if ($aggregate['average'] >= $i) {
                            $fill_class = 'filled';
                        } elseif ($aggregate['average'] >= $i - 0.5) {
                            $fill_class = 'half-filled';
                        }
                    ?>
                        <span class="kata-rating-star <?php echo $fill_class; ?>" data-rating="<?php echo $i; ?>">
                            <?php if ($atts['style'] === 'stars') : ?>
                                <svg viewBox="0 0 24 24" width="24" height="24">
                                    <path class="star-empty" fill="#ddd" d="M12,15.39L8.24,17.66L9.23,13.38L5.91,10.5L10.29,10.13L12,6.09L13.71,10.13L18.09,10.5L14.77,13.38L15.76,17.66M22,9.24L14.81,8.63L12,2L9.19,8.63L2,9.24L7.45,13.97L5.82,21L12,17.27L18.18,21L16.54,13.97L22,9.24Z"/>
                                    <path class="star-filled" fill="#ffc107" d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.63L12,2L9.19,8.63L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                                </svg>
                            <?php else : ?>
                                ★
                            <?php endif; ?>
                        </span>
                    <?php endfor; ?>
                </div>
                
                <div class="kata-rating-info">
                    <span class="kata-rating-average"><?php echo number_format($aggregate['average'], 1); ?></span>
                    
                    <?php if ($atts['show_count'] === 'yes') : ?>
                        <span class="kata-rating-count">
                            (<span class="count-number"><?php echo number_format($aggregate['count']); ?></span> 
                            <?php echo $aggregate['count'] === 1 ? 'đánh giá' : 'đánh giá'; ?>)
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($can_rate) : ?>
                <div class="kata-rating-input">
                    <p class="rating-prompt">
                        <?php echo $user_rating ? 'Đánh giá của bạn:' : 'Đánh giá bài viết này:'; ?>
                    </p>
                    
                    <div class="kata-rating-stars-input">
                        <?php for ($i = 1; $i <= 5; $i++) : ?>
                            <span class="kata-rating-star-btn <?php echo ($user_rating && $user_rating >= $i) ? 'selected' : ''; ?>" 
                                  data-rating="<?php echo $i; ?>"
                                  onclick="kataSEO.selectRating(this)">
                                <svg viewBox="0 0 24 24" width="32" height="32">
                                    <path fill="currentColor" d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.63L12,2L9.19,8.63L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                                </svg>
                            </span>
                        <?php endfor; ?>
                    </div>
                    
                    <?php if ($atts['allow_review'] === 'yes') : ?>
                        <div class="kata-rating-review">
                            <textarea name="review_comment" 
                                      class="rating-review-text"
                                      placeholder="Viết đánh giá của bạn... (không bắt buộc)"
                                      rows="4"><?php echo $user_rating ? esc_textarea($this->get_user_review($post_id)) : ''; ?></textarea>
                            
                            <button type="button" class="kata-rating-submit-btn">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                    <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                                </svg>
                                <?php echo $user_rating ? 'Cập nhật đánh giá' : 'Gửi đánh giá'; ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif ($atts['require_login'] === 'yes' && !is_user_logged_in()) : ?>
                <p class="kata-rating-login-required">
                    <a href="<?php echo wp_login_url(get_permalink()); ?>">Đăng nhập</a> để đánh giá
                </p>
            <?php endif; ?>
            
            <?php if ($aggregate['count'] > 0) : ?>
                <div class="kata-rating-breakdown">
                    <h4>Phân bố đánh giá</h4>
                    <?php 
                    $breakdown = $this->get_rating_breakdown($post_id);
                    for ($i = 5; $i >= 1; $i--) :
                        $count = isset($breakdown[$i]) ? $breakdown[$i] : 0;
                        $percentage = $aggregate['count'] > 0 ? ($count / $aggregate['count']) * 100 : 0;
                    ?>
                        <div class="rating-breakdown-row">
                            <span class="breakdown-stars"><?php echo $i; ?> ★</span>
                            <div class="breakdown-bar">
                                <div class="breakdown-fill" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <span class="breakdown-count"><?php echo $count; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Submit rating
     * 
     * @param array $data Rating data
     * @return array Result
     */
    public function submit_rating($data) {
        global $wpdb;
        
        $post_id = intval($data['post_id']);
        $rating = intval($data['rating']);
        $comment = isset($data['comment']) ? sanitize_textarea_field($data['comment']) : '';
        
        if ($rating < 1 || $rating > 5) {
            return array('error' => 'Invalid rating value');
        }
        
        $table = $wpdb->prefix . 'kata_seo_ratings';
        $user_id = get_current_user_id();
        $ip_address = $this->get_client_ip();
        
        // Check if user already rated
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE post_id = %d AND (user_id = %d OR ip_address = %s)",
            $post_id, $user_id, $ip_address
        ));
        
        if ($existing) {
            // Update existing rating
            $wpdb->update($table, 
                array(
                    'rating' => $rating,
                    'comment' => $comment,
                    'updated_at' => current_time('mysql')
                ),
                array('id' => $existing),
                array('%d', '%s', '%s'),
                array('%d')
            );
        } else {
            // Insert new rating
            $wpdb->insert($table, array(
                'post_id' => $post_id,
                'rating' => $rating,
                'comment' => $comment,
                'user_id' => $user_id ?: null,
                'ip_address' => $ip_address,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255),
                'created_at' => current_time('mysql')
            ), array('%d', '%d', '%s', '%d', '%s', '%s', '%s'));
        }
        
        $aggregate = $this->get_aggregate_rating($post_id);
        
        return array(
            'success' => true,
            'message' => 'Cảm ơn bạn đã đánh giá!',
            'aggregate' => $aggregate
        );
    }
    
    /**
     * Get aggregate rating for a post
     * 
     * @param int $post_id Post ID
     * @return array Aggregate data
     */
    public function get_aggregate_rating($post_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_ratings';
        
        $result = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                COUNT(*) as count,
                AVG(rating) as average,
                SUM(rating) as total
             FROM $table 
             WHERE post_id = %d",
            $post_id
        ), ARRAY_A);
        
        return array(
            'count' => intval($result['count']),
            'average' => floatval($result['average']),
            'total' => intval($result['total'])
        );
    }
    
    /**
     * Get rating breakdown
     * 
     * @param int $post_id Post ID
     * @return array Breakdown by star rating
     */
    public function get_rating_breakdown($post_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_ratings';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT rating, COUNT(*) as count 
             FROM $table 
             WHERE post_id = %d 
             GROUP BY rating",
            $post_id
        ), ARRAY_A);
        
        $breakdown = array();
        foreach ($results as $result) {
            $breakdown[$result['rating']] = intval($result['count']);
        }
        
        return $breakdown;
    }
    
    /**
     * Get user's rating for a post
     * 
     * @param int $post_id Post ID
     * @return int|null Rating value or null
     */
    private function get_user_rating($post_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_ratings';
        $user_id = get_current_user_id();
        $ip_address = $this->get_client_ip();
        
        $rating = $wpdb->get_var($wpdb->prepare(
            "SELECT rating FROM $table WHERE post_id = %d AND (user_id = %d OR ip_address = %s)",
            $post_id, $user_id, $ip_address
        ));
        
        return $rating ? intval($rating) : null;
    }
    
    /**
     * Get user's review comment
     * 
     * @param int $post_id Post ID
     * @return string Review comment
     */
    private function get_user_review($post_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_ratings';
        $user_id = get_current_user_id();
        $ip_address = $this->get_client_ip();
        
        $comment = $wpdb->get_var($wpdb->prepare(
            "SELECT comment FROM $table WHERE post_id = %d AND (user_id = %d OR ip_address = %s)",
            $post_id, $user_id, $ip_address
        ));
        
        return $comment ?: '';
    }
    
    /**
     * Check if user can rate
     * 
     * @param int $post_id Post ID
     * @param string $require_login Require login setting
     * @return bool Can rate
     */
    private function can_user_rate($post_id, $require_login) {
        if ($require_login === 'yes' && !is_user_logged_in()) {
            return false;
        }
        
        return true;
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
}
