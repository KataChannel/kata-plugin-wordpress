<?php
/**
 * Analytics and Tracking
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Analytics Class
 */
class KataShareSocial_Analytics {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
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
        $this->add_hooks();
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // Track share events
        add_action('wp_ajax_kata_track_share_event', array($this, 'track_share_event'));
        add_action('wp_ajax_nopriv_kata_track_share_event', array($this, 'track_share_event'));
        
        // Daily cleanup
        add_action('kata_sharesocial_daily_cleanup', array($this, 'daily_cleanup'));
        
        // Schedule cleanup if not already scheduled
        if (!wp_next_scheduled('kata_sharesocial_daily_cleanup')) {
            wp_schedule_event(time(), 'daily', 'kata_sharesocial_daily_cleanup');
        }
    }
    
    /**
     * Track share event via AJAX
     */
    public function track_share_event() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_sharesocial_nonce')) {
            wp_die('Security check failed');
        }
        
        $post_id = intval($_POST['post_id']);
        $platform = sanitize_text_field($_POST['platform']);
        $user_agent = sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '');
        $ip_address = $this->get_user_ip();
        
        // Record the share event
        $this->record_share_event($post_id, $platform, $user_agent, $ip_address);
        
        wp_send_json_success(array(
            'message' => __('Share tracked successfully', 'kata-sharesocial')
        ));
    }
    
    /**
     * Record share event in database
     */
    private function record_share_event($post_id, $platform, $user_agent = '', $ip_address = '') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_share_events';
        
        // Create events table if it doesn't exist
        $this->maybe_create_events_table();
        
        // Insert event record
        $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'platform' => $platform,
                'user_agent' => $user_agent,
                'ip_address' => $ip_address,
                'shared_at' => current_time('mysql'),
                'date_created' => current_time('mysql')
            ),
            array('%d', '%s', '%s', '%s', '%s', '%s')
        );
    }
    
    /**
     * Create events table if needed
     */
    private function maybe_create_events_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_share_events';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
        
        if (!$table_exists) {
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                post_id int(11) NOT NULL,
                platform varchar(50) NOT NULL,
                user_agent text,
                ip_address varchar(45),
                shared_at datetime DEFAULT CURRENT_TIMESTAMP,
                date_created datetime DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY post_id (post_id),
                KEY platform (platform),
                KEY shared_at (shared_at)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    /**
     * Get user IP address
     */
    private function get_user_ip() {
        $ip_keys = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    /**
     * Get detailed analytics for period
     */
    public function get_detailed_analytics($days = 30) {
        global $wpdb;
        
        $stats_table = $wpdb->prefix . 'kata_share_stats';
        $events_table = $wpdb->prefix . 'kata_share_events';
        
        // Basic statistics
        $total_shares = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(share_count) FROM $stats_table 
             WHERE last_shared >= DATE_SUB(NOW(), INTERVAL %d DAY)",
            $days
        ));
        
        // Shares by platform
        $platform_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT platform, SUM(share_count) as total_shares
             FROM $stats_table 
             WHERE last_shared >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY platform
             ORDER BY total_shares DESC",
            $days
        ));
        
        // Daily share trends (if events table exists)
        $daily_trends = array();
        if ($wpdb->get_var("SHOW TABLES LIKE '$events_table'") === $events_table) {
            $daily_trends = $wpdb->get_results($wpdb->prepare(
                "SELECT DATE(shared_at) as date, COUNT(*) as shares
                 FROM $events_table 
                 WHERE shared_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
                 GROUP BY DATE(shared_at)
                 ORDER BY date ASC",
                $days
            ));
        }
        
        // Top performing posts
        $top_posts = $wpdb->get_results($wpdb->prepare(
            "SELECT s.post_id, SUM(s.share_count) as total_shares, p.post_title
             FROM $stats_table s
             JOIN {$wpdb->posts} p ON s.post_id = p.ID
             WHERE s.last_shared >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY s.post_id
             ORDER BY total_shares DESC
             LIMIT 10",
            $days
        ));
        
        // Platform performance comparison
        $platform_comparison = array();
        foreach ($platform_stats as $stat) {
            $platform_comparison[$stat->platform] = array(
                'shares' => intval($stat->total_shares),
                'percentage' => $total_shares > 0 ? round(($stat->total_shares / $total_shares) * 100, 2) : 0
            );
        }
        
        return array(
            'total_shares' => intval($total_shares),
            'platform_stats' => $platform_stats,
            'daily_trends' => $daily_trends,
            'top_posts' => $top_posts,
            'platform_comparison' => $platform_comparison,
            'period' => $days
        );
    }
    
    /**
     * Get share trends by hour
     */
    public function get_hourly_trends($days = 7) {
        global $wpdb;
        
        $events_table = $wpdb->prefix . 'kata_share_events';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$events_table'") !== $events_table) {
            return array();
        }
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT HOUR(shared_at) as hour, COUNT(*) as shares
             FROM $events_table 
             WHERE shared_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
             GROUP BY HOUR(shared_at)
             ORDER BY hour ASC",
            $days
        ));
    }
    
    /**
     * Get device/browser statistics
     */
    public function get_device_stats($days = 30) {
        global $wpdb;
        
        $events_table = $wpdb->prefix . 'kata_share_events';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$events_table'") !== $events_table) {
            return array();
        }
        
        $user_agents = $wpdb->get_results($wpdb->prepare(
            "SELECT user_agent, COUNT(*) as count
             FROM $events_table 
             WHERE shared_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
             AND user_agent != ''
             GROUP BY user_agent
             ORDER BY count DESC",
            $days
        ));
        
        $device_stats = array(
            'mobile' => 0,
            'desktop' => 0,
            'tablet' => 0,
            'unknown' => 0
        );
        
        foreach ($user_agents as $ua) {
            $user_agent = strtolower($ua->user_agent);
            $count = intval($ua->count);
            
            if (preg_match('/mobile|android|iphone|ipod/', $user_agent)) {
                $device_stats['mobile'] += $count;
            } elseif (preg_match('/ipad|tablet/', $user_agent)) {
                $device_stats['tablet'] += $count;
            } elseif (preg_match('/windows|mac|linux/', $user_agent)) {
                $device_stats['desktop'] += $count;
            } else {
                $device_stats['unknown'] += $count;
            }
        }
        
        return $device_stats;
    }
    
    /**
     * Export analytics data
     */
    public function export_analytics($format = 'csv', $days = 30) {
        $analytics = $this->get_detailed_analytics($days);
        
        if ($format === 'csv') {
            return $this->export_to_csv($analytics);
        } elseif ($format === 'json') {
            return $this->export_to_json($analytics);
        }
        
        return false;
    }
    
    /**
     * Export to CSV
     */
    private function export_to_csv($analytics) {
        $csv_data = array();
        
        // Headers
        $csv_data[] = array('Platform', 'Total Shares', 'Percentage');
        
        // Platform data
        foreach ($analytics['platform_comparison'] as $platform => $data) {
            $csv_data[] = array(
                $platform,
                $data['shares'],
                $data['percentage'] . '%'
            );
        }
        
        return $csv_data;
    }
    
    /**
     * Export to JSON
     */
    private function export_to_json($analytics) {
        return json_encode($analytics, JSON_PRETTY_PRINT);
    }
    
    /**
     * Daily cleanup routine
     */
    public function daily_cleanup() {
        global $wpdb;
        
        $events_table = $wpdb->prefix . 'kata_share_events';
        
        // Delete events older than 90 days
        if ($wpdb->get_var("SHOW TABLES LIKE '$events_table'") === $events_table) {
            $wpdb->query(
                "DELETE FROM $events_table 
                 WHERE shared_at < DATE_SUB(NOW(), INTERVAL 90 DAY)"
            );
        }
        
        // Optimize tables
        $wpdb->query("OPTIMIZE TABLE {$wpdb->prefix}kata_share_stats");
        if ($wpdb->get_var("SHOW TABLES LIKE '$events_table'") === $events_table) {
            $wpdb->query("OPTIMIZE TABLE $events_table");
        }
    }
    
    /**
     * Get real-time statistics
     */
    public function get_realtime_stats() {
        global $wpdb;
        
        $stats_table = $wpdb->prefix . 'kata_share_stats';
        $events_table = $wpdb->prefix . 'kata_share_events';
        
        // Shares in last hour
        $last_hour = 0;
        if ($wpdb->get_var("SHOW TABLES LIKE '$events_table'") === $events_table) {
            $last_hour = $wpdb->get_var(
                "SELECT COUNT(*) FROM $events_table 
                 WHERE shared_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"
            );
        }
        
        // Shares today
        $today = $wpdb->get_var(
            "SELECT SUM(share_count) FROM $stats_table 
             WHERE DATE(last_shared) = CURDATE()"
        );
        
        // Most shared post today
        $top_today = $wpdb->get_row(
            "SELECT s.post_id, SUM(s.share_count) as shares, p.post_title
             FROM $stats_table s
             JOIN {$wpdb->posts} p ON s.post_id = p.ID
             WHERE DATE(s.last_shared) = CURDATE()
             GROUP BY s.post_id
             ORDER BY shares DESC
             LIMIT 1"
        );
        
        return array(
            'last_hour' => intval($last_hour),
            'today' => intval($today),
            'top_post_today' => $top_today
        );
    }
    
    /**
     * Get share statistics
     */
    public function get_share_statistics() {
        global $wpdb;
        
        $stats_table = $wpdb->prefix . 'kata_share_stats';
        
        // Total shares
        $total_shares = $wpdb->get_var("SELECT SUM(share_count) FROM $stats_table");
        
        // Total posts with shares
        $shared_posts = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $stats_table WHERE share_count > 0");
        
        // Active platforms
        $active_platforms = $wpdb->get_var("SELECT COUNT(DISTINCT platform) FROM $stats_table WHERE share_count > 0");
        
        // Average shares per post
        $avg_shares_per_post = $shared_posts > 0 ? ($total_shares / $shared_posts) : 0;
        
        return array(
            'total_shares' => intval($total_shares),
            'shared_posts' => intval($shared_posts),
            'active_platforms' => intval($active_platforms),
            'avg_shares_per_post' => floatval($avg_shares_per_post)
        );
    }
    
    /**
     * Get top shared posts
     */
    public function get_top_shared_posts($limit = 10) {
        global $wpdb;
        
        $stats_table = $wpdb->prefix . 'kata_share_stats';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT post_id, SUM(share_count) as total_shares
             FROM $stats_table 
             WHERE share_count > 0
             GROUP BY post_id 
             ORDER BY total_shares DESC 
             LIMIT %d",
            $limit
        ));
        
        $top_posts = array();
        foreach ($results as $row) {
            // Get platform breakdown for this post
            $platforms = $wpdb->get_results($wpdb->prepare(
                "SELECT platform, share_count 
                 FROM $stats_table 
                 WHERE post_id = %d AND share_count > 0",
                $row->post_id
            ), ARRAY_A);
            
            $platform_data = array();
            foreach ($platforms as $platform) {
                $platform_data[$platform['platform']] = intval($platform['share_count']);
            }
            
            $top_posts[] = array(
                'post_id' => intval($row->post_id),
                'total_shares' => intval($row->total_shares),
                'platforms' => $platform_data
            );
        }
        
        return $top_posts;
    }
    
    /**
     * Get platform statistics
     */
    public function get_platform_statistics() {
        global $wpdb;
        
        $stats_table = $wpdb->prefix . 'kata_share_stats';
        
        $results = $wpdb->get_results(
            "SELECT platform, SUM(share_count) as shares, COUNT(DISTINCT post_id) as posts
             FROM $stats_table 
             WHERE share_count > 0
             GROUP BY platform 
             ORDER BY shares DESC"
        );
        
        $total_shares = array_sum(array_column($results, 'shares'));
        $enabled_platforms = get_option('kata_sharesocial_enabled_platforms', array());
        
        $platform_stats = array();
        foreach ($results as $row) {
            $percentage = $total_shares > 0 ? ($row->shares / $total_shares * 100) : 0;
            $avg_per_post = $row->posts > 0 ? ($row->shares / $row->posts) : 0;
            
            $platform_stats[$row->platform] = array(
                'shares' => intval($row->shares),
                'posts' => intval($row->posts),
                'percentage' => floatval($percentage),
                'avg_per_post' => floatval($avg_per_post),
                'active' => in_array($row->platform, $enabled_platforms)
            );
        }
        
        return $platform_stats;
    }
}
