<?php
/**
 * Statistics Class
 * 
 * Manages schema statistics and analytics
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Statistics {
    
    /**
     * Get all statistics (alias for get_overview)
     */
    public function get_all_stats() {
        global $wpdb;
        $schemas_table = $wpdb->prefix . 'kata_seo_schemas';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$schemas_table'");
        
        if (!$table_exists) {
            return array(
                'total_schemas' => 0,
                'schema_types_count' => 0,
                'posts_with_schema' => 0,
                'last_updated' => __('Never', 'kata-seo-manager')
            );
        }
        
        // Total schemas
        $total_schemas = $wpdb->get_var("SELECT COUNT(*) FROM $schemas_table");
        
        // Unique schema types
        $schema_types_count = $wpdb->get_var("SELECT COUNT(DISTINCT schema_type) FROM $schemas_table");
        
        // Posts with schemas
        $posts_with_schema = $wpdb->get_var("SELECT COUNT(DISTINCT post_id) FROM $schemas_table");
        
        // Last updated
        $last_updated = $wpdb->get_var("SELECT MAX(updated_at) FROM $schemas_table");
        if (!$last_updated) {
            $last_updated = $wpdb->get_var("SELECT MAX(created_at) FROM $schemas_table");
        }
        
        return array(
            'total_schemas' => intval($total_schemas),
            'schema_types_count' => intval($schema_types_count),
            'posts_with_schema' => intval($posts_with_schema),
            'last_updated' => $last_updated ? mysql2date('Y-m-d H:i', $last_updated) : __('Never', 'kata-seo-manager')
        );
    }
    
    /**
     * Get overview statistics
     */
    public function get_overview() {
        global $wpdb;
        
        $schemas_table = $wpdb->prefix . 'kata_seo_schemas';
        $stats_table = $wpdb->prefix . 'kata_seo_schema_stats';
        $validation_table = $wpdb->prefix . 'kata_seo_schema_validation';
        
        // Total schemas
        $total_schemas = $wpdb->get_var("SELECT COUNT(*) FROM $schemas_table");
        
        // Active schemas
        $active_schemas = $wpdb->get_var("SELECT COUNT(*) FROM $schemas_table WHERE is_active = 1");
        
        // Schemas by type
        $schemas_by_type = $wpdb->get_results(
            "SELECT schema_type, COUNT(*) as count 
             FROM $schemas_table 
             GROUP BY schema_type 
             ORDER BY count DESC",
            ARRAY_A
        );
        
        // Validation stats
        $validation_stats = $wpdb->get_row(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_valid = 1 THEN 1 ELSE 0 END) as valid,
                SUM(CASE WHEN is_valid = 0 THEN 1 ELSE 0 END) as invalid
             FROM $validation_table",
            ARRAY_A
        );
        
        // Recent schemas
        $recent_schemas = $wpdb->get_results(
            "SELECT s.*, p.post_title, p.post_type
             FROM $schemas_table s
             LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
             ORDER BY s.created_at DESC
             LIMIT 10",
            ARRAY_A
        );
        
        return array(
            'total_schemas' => intval($total_schemas),
            'active_schemas' => intval($active_schemas),
            'inactive_schemas' => intval($total_schemas) - intval($active_schemas),
            'schemas_by_type' => $schemas_by_type,
            'validation' => array(
                'total' => intval($validation_stats['total'] ?? 0),
                'valid' => intval($validation_stats['valid'] ?? 0),
                'invalid' => intval($validation_stats['invalid'] ?? 0),
                'pass_rate' => $validation_stats['total'] > 0 
                    ? round(($validation_stats['valid'] / $validation_stats['total']) * 100, 2) 
                    : 0
            ),
            'recent_schemas' => $recent_schemas
        );
    }
    
    /**
     * Get schema statistics by type
     */
    public function get_type_stats($type) {
        global $wpdb;
        
        $schemas_table = $wpdb->prefix . 'kata_seo_schemas';
        $stats_table = $wpdb->prefix . 'kata_seo_schema_stats';
        
        // Total schemas of this type
        $total = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $schemas_table WHERE schema_type = %s",
            $type
        ));
        
        // Active schemas
        $active = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $schemas_table WHERE schema_type = %s AND is_active = 1",
            $type
        ));
        
        // Get performance data
        $performance = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                SUM(st.impressions) as total_impressions,
                SUM(st.clicks) as total_clicks,
                AVG(st.ctr) as avg_ctr,
                AVG(st.position) as avg_position
             FROM $stats_table st
             INNER JOIN $schemas_table s ON st.schema_id = s.id
             WHERE s.schema_type = %s",
            $type
        ), ARRAY_A);
        
        // Get trend data (last 30 days)
        $trend = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                st.date,
                SUM(st.impressions) as impressions,
                SUM(st.clicks) as clicks,
                AVG(st.ctr) as ctr
             FROM $stats_table st
             INNER JOIN $schemas_table s ON st.schema_id = s.id
             WHERE s.schema_type = %s 
             AND st.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY st.date
             ORDER BY st.date ASC",
            $type
        ), ARRAY_A);
        
        return array(
            'total' => intval($total),
            'active' => intval($active),
            'performance' => array(
                'impressions' => intval($performance['total_impressions'] ?? 0),
                'clicks' => intval($performance['total_clicks'] ?? 0),
                'ctr' => floatval($performance['avg_ctr'] ?? 0),
                'position' => floatval($performance['avg_position'] ?? 0)
            ),
            'trend' => $trend
        );
    }
    
    /**
     * Get post schema statistics
     */
    public function get_post_stats($post_id) {
        global $wpdb;
        
        $schemas_table = $wpdb->prefix . 'kata_seo_schemas';
        $stats_table = $wpdb->prefix . 'kata_seo_schema_stats';
        
        // Get schemas for this post
        $schemas = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $schemas_table WHERE post_id = %d ORDER BY created_at DESC",
            $post_id
        ), ARRAY_A);
        
        if (empty($schemas)) {
            return array();
        }
        
        $schema_ids = wp_list_pluck($schemas, 'id');
        $placeholders = implode(',', array_fill(0, count($schema_ids), '%d'));
        
        // Get performance for each schema
        foreach ($schemas as &$schema) {
            $perf = $wpdb->get_row($wpdb->prepare(
                "SELECT 
                    SUM(impressions) as impressions,
                    SUM(clicks) as clicks,
                    AVG(ctr) as ctr,
                    AVG(position) as position
                 FROM $stats_table 
                 WHERE schema_id = %d",
                $schema['id']
            ), ARRAY_A);
            
            $schema['performance'] = $perf;
        }
        
        return $schemas;
    }
    
    /**
     * Track schema impression/click
     */
    public function track_event($schema_id, $event_type, $data = array()) {
        global $wpdb;
        
        $stats_table = $wpdb->prefix . 'kata_seo_schema_stats';
        $today = current_time('Y-m-d');
        
        // Get or create today's record
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $stats_table WHERE schema_id = %d AND date = %s",
            $schema_id,
            $today
        ), ARRAY_A);
        
        if ($existing) {
            // Update existing record
            $updates = array();
            
            if ($event_type === 'impression') {
                $updates['impressions'] = $existing['impressions'] + 1;
            } elseif ($event_type === 'click') {
                $updates['clicks'] = $existing['clicks'] + 1;
            }
            
            // Recalculate CTR
            if (!empty($updates)) {
                $new_impressions = isset($updates['impressions']) ? $updates['impressions'] : $existing['impressions'];
                $new_clicks = isset($updates['clicks']) ? $updates['clicks'] : $existing['clicks'];
                
                if ($new_impressions > 0) {
                    $updates['ctr'] = ($new_clicks / $new_impressions) * 100;
                }
                
                $wpdb->update(
                    $stats_table,
                    $updates,
                    array('id' => $existing['id'])
                );
            }
        } else {
            // Insert new record
            $wpdb->insert($stats_table, array(
                'schema_id' => $schema_id,
                'impressions' => $event_type === 'impression' ? 1 : 0,
                'clicks' => $event_type === 'click' ? 1 : 0,
                'ctr' => 0,
                'position' => isset($data['position']) ? $data['position'] : 0,
                'date' => $today
            ));
        }
    }
    
    /**
     * Get schema validation report
     */
    public function get_validation_report() {
        global $wpdb;
        
        $validation_table = $wpdb->prefix . 'kata_seo_schema_validation';
        $schemas_table = $wpdb->prefix . 'kata_seo_schemas';
        
        // Get all validations with schema info
        $results = $wpdb->get_results(
            "SELECT v.*, s.schema_type, s.post_id, p.post_title
             FROM $validation_table v
             INNER JOIN $schemas_table s ON v.schema_id = s.id
             LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
             ORDER BY v.validated_at DESC
             LIMIT 100",
            ARRAY_A
        );
        
        // Group by status
        $report = array(
            'total' => count($results),
            'valid' => 0,
            'invalid' => 0,
            'with_warnings' => 0,
            'by_type' => array(),
            'recent_errors' => array()
        );
        
        foreach ($results as $result) {
            if ($result['is_valid']) {
                $report['valid']++;
            } else {
                $report['invalid']++;
                $report['recent_errors'][] = $result;
            }
            
            $errors = json_decode($result['errors'], true);
            $warnings = json_decode($result['warnings'], true);
            
            if (!empty($warnings)) {
                $report['with_warnings']++;
            }
            
            // Count by type
            $type = $result['schema_type'];
            if (!isset($report['by_type'][$type])) {
                $report['by_type'][$type] = array(
                    'total' => 0,
                    'valid' => 0,
                    'invalid' => 0
                );
            }
            
            $report['by_type'][$type]['total']++;
            if ($result['is_valid']) {
                $report['by_type'][$type]['valid']++;
            } else {
                $report['by_type'][$type]['invalid']++;
            }
        }
        
        // Limit recent errors
        $report['recent_errors'] = array_slice($report['recent_errors'], 0, 10);
        
        return $report;
    }
    
    /**
     * Export statistics to CSV
     */
    public function export_to_csv($type = 'all') {
        global $wpdb;
        
        $schemas_table = $wpdb->prefix . 'kata_seo_schemas';
        $stats_table = $wpdb->prefix . 'kata_seo_schema_stats';
        
        $where = $type !== 'all' ? $wpdb->prepare("WHERE s.schema_type = %s", $type) : '';
        
        $results = $wpdb->get_results(
            "SELECT 
                s.id,
                s.schema_type,
                s.post_id,
                p.post_title,
                s.is_active,
                s.created_at,
                COALESCE(SUM(st.impressions), 0) as impressions,
                COALESCE(SUM(st.clicks), 0) as clicks,
                COALESCE(AVG(st.ctr), 0) as ctr,
                COALESCE(AVG(st.position), 0) as position
             FROM $schemas_table s
             LEFT JOIN {$wpdb->posts} p ON s.post_id = p.ID
             LEFT JOIN $stats_table st ON s.id = st.schema_id
             $where
             GROUP BY s.id
             ORDER BY s.created_at DESC",
            ARRAY_A
        );
        
        // Generate CSV
        $csv = array();
        $csv[] = array('ID', 'Type', 'Post ID', 'Post Title', 'Active', 'Created', 'Impressions', 'Clicks', 'CTR', 'Position');
        
        foreach ($results as $row) {
            $csv[] = array(
                $row['id'],
                $row['schema_type'],
                $row['post_id'],
                $row['post_title'],
                $row['is_active'] ? 'Yes' : 'No',
                $row['created_at'],
                $row['impressions'],
                $row['clicks'],
                number_format($row['ctr'], 2) . '%',
                number_format($row['position'], 2)
            );
        }
        
        return $csv;
    }
}
