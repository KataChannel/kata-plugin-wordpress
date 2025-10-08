<?php
/**
 * Social Platforms Manager
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Social Platforms Class
 */
class KataShareSocial_Platforms {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Available platforms
     */
    private $platforms = array();
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_platforms();
        $this->add_hooks();
    }
    
    /**
     * Initialize platforms
     */
    private function init_platforms() {
        $this->platforms = array(
            'facebook' => array(
                'name' => 'Facebook',
                'icon' => 'fab fa-facebook-f',
                'color' => '#1877f2',
                'url_template' => 'https://www.facebook.com/sharer/sharer.php?u={{url}}',
                'popup_width' => 600,
                'popup_height' => 400
            ),
            'twitter' => array(
                'name' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'color' => '#1da1f2',
                'url_template' => 'https://twitter.com/intent/tweet?url={{url}}&text={{title}}',
                'popup_width' => 600,
                'popup_height' => 400
            ),
            'linkedin' => array(
                'name' => 'LinkedIn',
                'icon' => 'fab fa-linkedin-in',
                'color' => '#0077b5',
                'url_template' => 'https://www.linkedin.com/sharing/share-offsite/?url={{url}}',
                'popup_width' => 700,
                'popup_height' => 500
            ),
            'pinterest' => array(
                'name' => 'Pinterest',
                'icon' => 'fab fa-pinterest-p',
                'color' => '#bd081c',
                'url_template' => 'https://pinterest.com/pin/create/button/?url={{url}}&description={{title}}&media={{image}}',
                'popup_width' => 750,
                'popup_height' => 550
            ),
            'telegram' => array(
                'name' => 'Telegram',
                'icon' => 'fab fa-telegram-plane',
                'color' => '#0088cc',
                'url_template' => 'https://t.me/share/url?url={{url}}&text={{title}}',
                'popup_width' => 600,
                'popup_height' => 400
            ),
            'whatsapp' => array(
                'name' => 'WhatsApp',
                'icon' => 'fab fa-whatsapp',
                'color' => '#25d366',
                'url_template' => 'https://wa.me/?text={{title}} {{url}}',
                'popup_width' => 600,
                'popup_height' => 400
            ),
            'reddit' => array(
                'name' => 'Reddit',
                'icon' => 'fab fa-reddit-alien',
                'color' => '#ff4500',
                'url_template' => 'https://reddit.com/submit?url={{url}}&title={{title}}',
                'popup_width' => 700,
                'popup_height' => 500
            ),
            'tumblr' => array(
                'name' => 'Tumblr',
                'icon' => 'fab fa-tumblr',
                'color' => '#35465c',
                'url_template' => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl={{url}}&title={{title}}',
                'popup_width' => 600,
                'popup_height' => 400
            ),
            'email' => array(
                'name' => 'Email',
                'icon' => 'fas fa-envelope',
                'color' => '#6c757d',
                'url_template' => 'mailto:?subject={{title}}&body={{url}}',
                'popup_width' => 0,
                'popup_height' => 0
            ),
            'copy_link' => array(
                'name' => 'Copy Link',
                'icon' => 'fas fa-link',
                'color' => '#6c757d',
                'url_template' => '',
                'popup_width' => 0,
                'popup_height' => 0,
                'special_action' => 'copy_link'
            )
        );
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // AJAX handler for share tracking
        add_action('wp_ajax_kata_share_track', array($this, 'track_share'));
        add_action('wp_ajax_nopriv_kata_share_track', array($this, 'track_share'));
    }
    
    /**
     * Get all platforms
     */
    public function get_platforms() {
        return apply_filters('kata_sharesocial_platforms', $this->platforms);
    }
    
    /**
     * Get specific platform
     */
    public function get_platform($platform_id) {
        $platforms = $this->get_platforms();
        return isset($platforms[$platform_id]) ? $platforms[$platform_id] : false;
    }
    
    /**
     * Get enabled platforms
     */
    public function get_enabled_platforms() {
        $enabled = get_option('kata_sharesocial_enabled_platforms', array('facebook', 'twitter', 'linkedin'));
        $platforms = $this->get_platforms();
        
        $enabled_platforms = array();
        foreach ($enabled as $platform_id) {
            if (isset($platforms[$platform_id])) {
                $enabled_platforms[$platform_id] = $platforms[$platform_id];
            }
        }
        
        return $enabled_platforms;
    }
    
    /**
     * Generate share URL for platform
     */
    public function generate_share_url($platform_id, $post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $platform = $this->get_platform($platform_id);
        if (!$platform) {
            return false;
        }
        
        // Get post data
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }
        
        $url = get_permalink($post_id);
        $title = get_the_title($post_id);
        $excerpt = get_the_excerpt($post_id);
        $image = get_the_post_thumbnail_url($post_id, 'large');
        
        // Replace placeholders
        $share_url = $platform['url_template'];
        $share_url = str_replace('{{url}}', urlencode($url), $share_url);
        $share_url = str_replace('{{title}}', urlencode($title), $share_url);
        $share_url = str_replace('{{excerpt}}', urlencode($excerpt), $share_url);
        $share_url = str_replace('{{image}}', urlencode($image), $share_url);
        
        return apply_filters('kata_sharesocial_share_url', $share_url, $platform_id, $post_id);
    }
    
    /**
     * Get share count for post and platform
     */
    public function get_share_count($post_id, $platform_id = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_share_stats';
        
        if ($platform_id) {
            // Get count for specific platform
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT share_count FROM $table_name WHERE post_id = %d AND platform = %s",
                $post_id,
                $platform_id
            ));
            return $count ? intval($count) : 0;
        } else {
            // Get total count for all platforms
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT SUM(share_count) FROM $table_name WHERE post_id = %d",
                $post_id
            ));
            return $count ? intval($count) : 0;
        }
    }
    
    /**
     * Track share action
     */
    public function track_share() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_sharesocial_nonce')) {
            wp_die('Security check failed');
        }
        
        $post_id = intval($_POST['post_id']);
        $platform = sanitize_text_field($_POST['platform']);
        
        if (!$post_id || !$platform) {
            wp_send_json_error('Invalid data');
        }
        
        // Update share count
        $this->increment_share_count($post_id, $platform);
        
        // Get updated count
        $count = $this->get_share_count($post_id, $platform);
        
        wp_send_json_success(array(
            'count' => $count,
            'total' => $this->get_share_count($post_id)
        ));
    }
    
    /**
     * Increment share count
     */
    private function increment_share_count($post_id, $platform) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_share_stats';
        
        // Try to update existing record
        $updated = $wpdb->query($wpdb->prepare(
            "UPDATE $table_name 
             SET share_count = share_count + 1, 
                 last_shared = NOW(), 
                 updated_at = NOW() 
             WHERE post_id = %d AND platform = %s",
            $post_id,
            $platform
        ));
        
        // If no record exists, insert new one
        if ($updated === 0) {
            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'platform' => $platform,
                    'share_count' => 1,
                    'last_shared' => current_time('mysql'),
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%s', '%d', '%s', '%s', '%s')
            );
        }
    }
    
    /**
     * Get share statistics for dashboard
     */
    public function get_share_statistics($days = 30) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_share_stats';
        
        // Get total shares in period
        $total_shares = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(share_count) FROM $table_name 
             WHERE last_shared >= DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
        
        // Get shares by platform
        $platform_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT platform, SUM(share_count) as total_shares
             FROM $table_name 
             WHERE last_shared >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY platform
             ORDER BY total_shares DESC",
            $days
        ));
        
        // Get top shared posts
        $top_posts = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, SUM(share_count) as total_shares
             FROM $table_name 
             WHERE last_shared >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY post_id
             ORDER BY total_shares DESC
             LIMIT 10",
            $days
        ));
        
        return array(
            'total_shares' => intval($total_shares),
            'platform_stats' => $platform_stats,
            'top_posts' => $top_posts
        );
    }
}
