<?php
/**
 * KATA Schema Database Class
 * 
 * Handles database operations for schema storage
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class KATA_Schema_Database {
    
    /**
     * Table name
     */
    private $table_name;
    
    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'kata_schemas';
    }
    
    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            schema_name varchar(255) NOT NULL,
            schema_type varchar(100) NOT NULL,
            schema_data longtext NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY schema_type (schema_type),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        return $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") === $this->table_name;
    }
    
    /**
     * Verify tables exist
     */
    public function verify_tables() {
        global $wpdb;
        return $wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") === $this->table_name;
    }
    
    /**
     * Get all schemas
     */
    public function get_all_schemas($status = '') {
        global $wpdb;
        
        $where = '';
        if (!empty($status)) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return $wpdb->get_results(
            "SELECT * FROM {$this->table_name}{$where} ORDER BY created_at DESC"
        );
    }
    
    /**
     * Get schema by ID
     */
    public function get_schema($id) {
        global $wpdb;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Get schemas by type
     */
    public function get_schemas_by_type($type) {
        global $wpdb;
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$this->table_name} WHERE schema_type = %s AND status = 'active' ORDER BY schema_name ASC",
            $type
        ));
    }
    
    /**
     * Save schema
     */
    public function save_schema($data) {
        global $wpdb;
        
        if (isset($data['id']) && $data['id'] > 0) {
            // Update
            $id = $data['id'];
            unset($data['id']);
            
            return $wpdb->update(
                $this->table_name,
                $data,
                array('id' => $id)
            );
        } else {
            // Insert
            if (isset($data['id'])) {
                unset($data['id']);
            }
            
            $result = $wpdb->insert($this->table_name, $data);
            
            if ($result) {
                return $wpdb->insert_id;
            }
            return false;
        }
    }
    
    /**
     * Delete schema
     */
    public function delete_schema($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $this->table_name,
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Get schema count by type
     */
    public function get_count_by_type() {
        global $wpdb;
        
        return $wpdb->get_results(
            "SELECT schema_type, COUNT(*) as count 
             FROM {$this->table_name} 
             WHERE status = 'active' 
             GROUP BY schema_type 
             ORDER BY count DESC"
        );
    }
    
    /**
     * Get total schema count
     */
    public function get_total_count($status = '') {
        global $wpdb;
        
        $where = '';
        if (!empty($status)) {
            $where = $wpdb->prepare(" WHERE status = %s", $status);
        }
        
        return $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name}{$where}"
        );
    }
}
