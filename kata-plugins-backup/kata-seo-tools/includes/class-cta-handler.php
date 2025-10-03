<?php
/**
 * CTA (Call-to-Action) Handler
 * 
 * Handles custom CTA boxes with conversion tracking
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_CTA_Handler {
    
    /**
     * Render CTA shortcode
     * 
     * @param array $atts Shortcode attributes
     * @param string $content CTA content
     * @return string HTML output
     */
    public function render($atts, $content = null) {
        $atts = shortcode_atts(array(
            'title' => '',
            'button_text' => 'Learn More',
            'button_url' => '#',
            'button_style' => 'primary',
            'bg_color' => '',
            'text_color' => '',
            'bg_image' => '',
            'style' => 'box',
            'alignment' => 'center',
            'track' => 'yes'
        ), $atts);
        
        $cta_id = 'cta-' . uniqid();
        $post_id = get_the_ID();
        
        $wrapper_style = '';
        if (!empty($atts['bg_color'])) {
            $wrapper_style .= 'background-color:' . esc_attr($atts['bg_color']) . ';';
        }
        if (!empty($atts['text_color'])) {
            $wrapper_style .= 'color:' . esc_attr($atts['text_color']) . ';';
        }
        if (!empty($atts['bg_image'])) {
            $wrapper_style .= 'background-image:url(' . esc_url($atts['bg_image']) . ');';
            $wrapper_style .= 'background-size:cover;background-position:center;';
        }
        
        $wrapper_class = 'kata-cta kata-cta-' . esc_attr($atts['style']);
        $wrapper_class .= ' kata-cta-align-' . esc_attr($atts['alignment']);
        
        ob_start();
        ?>
        <div class="<?php echo $wrapper_class; ?>" 
             style="<?php echo $wrapper_style; ?>"
             data-cta-id="<?php echo esc_attr($cta_id); ?>"
             data-post-id="<?php echo esc_attr($post_id); ?>">
            
            <?php if ($atts['style'] === 'banner') : ?>
                <div class="kata-cta-banner-content">
                    <?php if (!empty($atts['title'])) : ?>
                        <h3 class="kata-cta-title"><?php echo esc_html($atts['title']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ($content) : ?>
                        <div class="kata-cta-content"><?php echo wp_kses_post($content); ?></div>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($atts['button_url']); ?>" 
                       class="kata-cta-button kata-cta-button-<?php echo esc_attr($atts['button_style']); ?>"
                       onclick="kataSEO.trackCTA('<?php echo esc_js($cta_id); ?>', <?php echo esc_js($post_id); ?>)">
                        <?php echo esc_html($atts['button_text']); ?>
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                        </svg>
                    </a>
                </div>
                
            <?php elseif ($atts['style'] === 'card') : ?>
                <div class="kata-cta-card">
                    <div class="kata-cta-icon">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="currentColor">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                        </svg>
                    </div>
                    
                    <?php if (!empty($atts['title'])) : ?>
                        <h3 class="kata-cta-title"><?php echo esc_html($atts['title']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ($content) : ?>
                        <div class="kata-cta-content"><?php echo wp_kses_post($content); ?></div>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url($atts['button_url']); ?>" 
                       class="kata-cta-button kata-cta-button-<?php echo esc_attr($atts['button_style']); ?>"
                       onclick="kataSEO.trackCTA('<?php echo esc_js($cta_id); ?>', <?php echo esc_js($post_id); ?>)">
                        <?php echo esc_html($atts['button_text']); ?>
                    </a>
                </div>
                
            <?php else : // box style ?>
                <div class="kata-cta-box-content">
                    <?php if (!empty($atts['title'])) : ?>
                        <h3 class="kata-cta-title"><?php echo esc_html($atts['title']); ?></h3>
                    <?php endif; ?>
                    
                    <?php if ($content) : ?>
                        <div class="kata-cta-content"><?php echo wp_kses_post($content); ?></div>
                    <?php endif; ?>
                    
                    <div class="kata-cta-actions">
                        <a href="<?php echo esc_url($atts['button_url']); ?>" 
                           class="kata-cta-button kata-cta-button-<?php echo esc_attr($atts['button_style']); ?>"
                           onclick="kataSEO.trackCTA('<?php echo esc_js($cta_id); ?>', <?php echo esc_js($post_id); ?>)">
                            <?php echo esc_html($atts['button_text']); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Track CTA click
     * 
     * @param array $data CTA click data
     * @return array Result
     */
    public function track_click($data) {
        global $wpdb;
        
        $cta_id = sanitize_text_field($data['cta_id']);
        $post_id = intval($data['post_id']);
        
        $table = $wpdb->prefix . 'kata_seo_cta_clicks';
        
        $wpdb->insert($table, array(
            'post_id' => $post_id,
            'cta_id' => $cta_id,
            'user_id' => get_current_user_id() ?: null,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255),
            'referrer' => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '',
            'clicked_at' => current_time('mysql')
        ), array('%d', '%s', '%d', '%s', '%s', '%s', '%s'));
        
        return array('success' => true);
    }
    
    /**
     * Get CTA statistics
     * 
     * @param string $cta_id CTA ID
     * @return array Statistics
     */
    public function get_cta_stats($cta_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_cta_clicks';
        
        $stats = array(
            'total_clicks' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE cta_id = %s",
                $cta_id
            )),
            'unique_clicks' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(DISTINCT ip_address) FROM $table WHERE cta_id = %s",
                $cta_id
            )),
            'clicks_by_date' => $wpdb->get_results($wpdb->prepare(
                "SELECT DATE(clicked_at) as date, COUNT(*) as count 
                 FROM $table 
                 WHERE cta_id = %s 
                 GROUP BY DATE(clicked_at) 
                 ORDER BY date DESC 
                 LIMIT 30",
                $cta_id
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
