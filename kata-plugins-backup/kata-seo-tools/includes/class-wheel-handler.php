<?php
/**
 * Wheel Handler
 * 
 * Handles lucky wheel gamification with prizes and spins tracking
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_Wheel_Handler {
    
    /**
     * Render wheel shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'id' => 'wheel-' . uniqid(),
            'prizes' => '',
            'max_spins' => 1,
            'require_email' => 'no',
            'button_text' => 'Spin to Win!',
            'colors' => '#FF6B6B,#4ECDC4,#45B7D1,#FFA07A,#98D8C8,#F7DC6F'
        ), $atts);
        
        if (empty($atts['prizes'])) {
            return '<div class="kata-wheel-error">No prizes configured.</div>';
        }
        
        $prizes = json_decode(base64_decode($atts['prizes']), true);
        if (!$prizes) {
            $prizes = json_decode($atts['prizes'], true);
        }
        
        if (!$prizes || !is_array($prizes)) {
            return '<div class="kata-wheel-error">Invalid prizes format.</div>';
        }
        
        $post_id = get_the_ID();
        $colors = explode(',', $atts['colors']);
        $spins_left = $this->get_spins_left($atts['id'], $atts['max_spins']);
        
        ob_start();
        ?>
        <div class="kata-wheel-wrapper" 
             data-wheel-id="<?php echo esc_attr($atts['id']); ?>"
             data-post-id="<?php echo esc_attr($post_id); ?>"
             data-max-spins="<?php echo esc_attr($atts['max_spins']); ?>">
            
            <div class="kata-wheel-container">
                <div class="wheel-pointer">
                    <svg viewBox="0 0 24 24" width="40" height="40" fill="#FF6B6B">
                        <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
                    </svg>
                </div>
                
                <canvas id="wheel-canvas-<?php echo esc_attr($atts['id']); ?>" 
                        width="400" 
                        height="400"
                        class="kata-wheel-canvas"></canvas>
                
                <button type="button" 
                        class="kata-wheel-spin-btn"
                        <?php echo $spins_left <= 0 ? 'disabled' : ''; ?>
                        onclick="kataSEO.spinWheel('<?php echo esc_js($atts['id']); ?>')">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12,4V1L8,5L12,9V6A6,6 0 0,1 18,12C18,13.57 17.37,15 16.32,16L17.77,17.45C19.22,15.93 20,13.87 20,12A8,8 0 0,0 12,4M12,18A6,6 0 0,1 6,12C6,10.43 6.63,9 7.68,8L6.23,6.55C4.78,8.07 4,10.13 4,12A8,8 0 0,0 12,20V23L16,19L12,15V18Z"/>
                    </svg>
                    <?php echo esc_html($atts['button_text']); ?>
                </button>
                
                <div class="kata-wheel-spins-left">
                    Số lượt chơi còn lại: <strong><?php echo $spins_left; ?></strong>
                </div>
            </div>
            
            <?php if ($atts['require_email'] === 'yes') : ?>
                <div class="kata-wheel-email-form" style="display:none;">
                    <h4>Nhập email để nhận giải thưởng</h4>
                    <input type="email" 
                           class="kata-wheel-email-input" 
                           placeholder="your@email.com"
                           required>
                    <button type="button" 
                            class="kata-wheel-email-submit"
                            onclick="kataSEO.submitWheelEmail('<?php echo esc_js($atts['id']); ?>')">
                        Xác nhận
                    </button>
                </div>
            <?php endif; ?>
            
            <div class="kata-wheel-result" style="display:none;">
                <div class="wheel-result-content">
                    <div class="result-icon">🎉</div>
                    <h3 class="result-title">Chúc mừng!</h3>
                    <p class="result-prize"></p>
                    <button type="button" 
                            class="kata-wheel-close-btn"
                            onclick="kataSEO.closeWheelResult()">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
        
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            kataSEO.initWheel('<?php echo esc_js($atts['id']); ?>', 
                <?php echo wp_json_encode($prizes); ?>,
                <?php echo wp_json_encode($colors); ?>);
        });
        </script>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Spin wheel and get prize
     * 
     * @param array $data Spin data
     * @return array Result with prize
     */
    public function spin_wheel($data) {
        global $wpdb;
        
        $wheel_id = sanitize_text_field($data['wheel_id']);
        $post_id = intval($data['post_id']);
        $max_spins = intval($data['max_spins']);
        
        // Check remaining spins
        if ($this->get_spins_left($wheel_id, $max_spins) <= 0) {
            return array('error' => 'Bạn đã hết lượt chơi!');
        }
        
        // Get prizes
        $prizes = json_decode(base64_decode($data['prizes']), true);
        if (!$prizes) {
            return array('error' => 'Invalid prizes data');
        }
        
        // Select random prize based on probability
        $prize = $this->select_random_prize($prizes);
        
        // Save spin result
        $table = $wpdb->prefix . 'kata_seo_wheel_spins';
        $wpdb->insert($table, array(
            'post_id' => $post_id,
            'wheel_id' => $wheel_id,
            'prize' => $prize['text'],
            'prize_value' => isset($prize['value']) ? $prize['value'] : '',
            'user_id' => get_current_user_id() ?: null,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255),
            'email' => isset($data['email']) ? sanitize_email($data['email']) : null,
            'spun_at' => current_time('mysql')
        ), array('%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s'));
        
        return array(
            'success' => true,
            'prize' => $prize,
            'spins_left' => $this->get_spins_left($wheel_id, $max_spins)
        );
    }
    
    /**
     * Select random prize based on probability
     * 
     * @param array $prizes Prize list
     * @return array Selected prize
     */
    private function select_random_prize($prizes) {
        $total_probability = 0;
        foreach ($prizes as $prize) {
            $total_probability += isset($prize['probability']) ? floatval($prize['probability']) : 1;
        }
        
        $random = mt_rand(1, $total_probability * 100) / 100;
        $cumulative = 0;
        
        foreach ($prizes as $prize) {
            $probability = isset($prize['probability']) ? floatval($prize['probability']) : 1;
            $cumulative += $probability;
            
            if ($random <= $cumulative) {
                return $prize;
            }
        }
        
        return $prizes[0];
    }
    
    /**
     * Get remaining spins for user
     * 
     * @param string $wheel_id Wheel ID
     * @param int $max_spins Maximum spins allowed
     * @return int Spins left
     */
    private function get_spins_left($wheel_id, $max_spins) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_wheel_spins';
        $ip = $this->get_client_ip();
        $user_id = get_current_user_id();
        
        $spins_used = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) 
             FROM $table 
             WHERE wheel_id = %s 
             AND (ip_address = %s OR (user_id IS NOT NULL AND user_id = %d))",
            $wheel_id, $ip, $user_id
        ));
        
        return max(0, $max_spins - intval($spins_used));
    }
    
    /**
     * Get wheel statistics
     * 
     * @param string $wheel_id Wheel ID
     * @return array Statistics
     */
    public function get_wheel_stats($wheel_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_wheel_spins';
        
        $stats = array(
            'total_spins' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE wheel_id = %s",
                $wheel_id
            )),
            'unique_players' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT ip_address) FROM $table WHERE wheel_id = %s",
                $wheel_id
            )),
            'prize_distribution' => $wpdb->get_results($wpdb->prepare(
                "SELECT prize, COUNT(*) as count 
                 FROM $table 
                 WHERE wheel_id = %s 
                 GROUP BY prize",
                $wheel_id
            ), ARRAY_A),
            'spins_by_date' => $wpdb->get_results($wpdb->prepare(
                "SELECT DATE(spun_at) as date, COUNT(*) as count 
                 FROM $table 
                 WHERE wheel_id = %s 
                 GROUP BY DATE(spun_at) 
                 ORDER BY date DESC 
                 LIMIT 30",
                $wheel_id
            ), ARRAY_A)
        );
        
        return $stats;
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
