<?php
/**
 * Branch Handler Class
 * 
 * Handles branch management and contact information
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataChatbot_Branch_Handler {
    
    /**
     * Database handler instance
     */
    private $db_handler;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db_handler = new KataChatbot_DB_Handler();
        $this->init();
    }
    
    /**
     * Initialize branch functionality
     */
    private function init() {
        // AJAX handlers
        add_action('wp_ajax_kata_chatbot_save_branch', array($this, 'save_branch'));
        add_action('wp_ajax_kata_chatbot_delete_branch', array($this, 'delete_branch'));
        add_action('wp_ajax_kata_chatbot_get_branch', array($this, 'get_branch'));
        add_action('wp_ajax_kata_chatbot_get_branches', array($this, 'get_branches'));
        
        // Frontend AJAX
        add_action('wp_ajax_nopriv_kata_chatbot_get_branch_contacts', array($this, 'get_branch_contacts'));
        add_action('wp_ajax_kata_chatbot_get_branch_contacts', array($this, 'get_branch_contacts'));
    }
    
    /**
     * Create branches table if not exists
     */
    public function create_branches_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            address text,
            phone varchar(50),
            email varchar(100),
            facebook_url varchar(255),
            facebook_page_id varchar(100),
            zalo_url varchar(255),
            zalo_oa_id varchar(100),
            hotline varchar(50),
            working_hours text,
            description text,
            is_active tinyint(1) DEFAULT 1,
            display_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Execute table creation with error handling
        $result = dbDelta($sql);
        
        // Log the result for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Kata Chatbot Branch Table Creation Result: ' . wp_json_encode($result));
            if ($wpdb->last_error) {
                error_log('Kata Chatbot Branch Table Error: ' . $wpdb->last_error);
            }
        }
        
        // Verify table was created
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        if (!$table_exists) {
            // Log error and try alternative approach
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Kata Chatbot: Branch table creation failed, attempting manual creation');
            }
            
            // Try direct query as fallback
            $direct_result = $wpdb->query($sql);
            if ($direct_result === false && defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Kata Chatbot: Direct branch table creation also failed: ' . $wpdb->last_error);
            }
        }
        
        // Create default branch if table exists and is empty
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $this->create_default_branch();
        }
    }
    
    /**
     * Create default branch
     */
    private function create_default_branch() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        // Check if table exists first
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Kata Chatbot: Cannot create default branch - table does not exist');
            }
            return false;
        }
        
        try {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            
            if ($count == 0) {
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        'name' => 'Chi nhánh chính',
                        'address' => 'Địa chỉ chi nhánh chính',
                        'phone' => '0123456789',
                        'email' => 'contact@example.com',
                        'facebook_url' => '',
                        'facebook_page_id' => '',
                        'zalo_url' => '',
                        'zalo_oa_id' => '',
                        'hotline' => '0987654321',
                        'working_hours' => 'Thứ 2 - Thứ 6: 8:00 - 17:30',
                        'description' => 'Chi nhánh chính của công ty',
                        'is_active' => 1,
                        'display_order' => 1
                    ),
                    array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d')
                );
                
                if ($result === false && defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Kata Chatbot: Failed to insert default branch: ' . $wpdb->last_error);
                    return false;
                }
                
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Kata Chatbot: Default branch created successfully');
                }
                return true;
            }
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Kata Chatbot: Exception in create_default_branch: ' . $e->getMessage());
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Check and repair branch table if needed
     */
    public function check_and_repair_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        // Check if table exists
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
        
        if (!$table_exists) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Kata Chatbot: Branch table missing, attempting to recreate');
            }
            
            // Try to recreate table
            $this->create_branches_table();
            
            // Verify creation
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;
            
            if (!$table_exists) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Kata Chatbot: Failed to recreate branch table');
                }
                return false;
            }
        }
        
        // Check if table has data
        try {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            if ($count == 0) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('Kata Chatbot: Branch table empty, creating default branch');
                }
                $this->create_default_branch();
            }
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('Kata Chatbot: Error checking branch table: ' . $e->getMessage());
            }
            return false;
        }
        
        return true;
    }
    
    /**
     * Get all branches
     */
    public function get_all_branches($active_only = false) {
        // Ensure table exists and has data
        $this->check_and_repair_table();
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        $where = $active_only ? 'WHERE is_active = 1' : '';
        
        $results = $wpdb->get_results(
            "SELECT * FROM $table_name $where ORDER BY display_order ASC, name ASC"
        );
        
        return $results ? $results : array();
    }
    
    /**
     * Get branch by ID
     */
    public function get_branch_by_id($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        return $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id)
        );
    }
    
    /**
     * Save branch (create or update)
     */
    public function save_branch_data($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        $branch_data = array(
            'name' => sanitize_text_field($data['name']),
            'address' => sanitize_textarea_field($data['address']),
            'phone' => sanitize_text_field($data['phone']),
            'email' => sanitize_email($data['email']),
            'facebook_url' => esc_url_raw($data['facebook_url']),
            'facebook_page_id' => sanitize_text_field($data['facebook_page_id']),
            'zalo_url' => esc_url_raw($data['zalo_url']),
            'zalo_oa_id' => sanitize_text_field($data['zalo_oa_id']),
            'hotline' => sanitize_text_field($data['hotline']),
            'working_hours' => sanitize_textarea_field($data['working_hours']),
            'description' => sanitize_textarea_field($data['description']),
            'is_active' => isset($data['is_active']) ? 1 : 0,
            'display_order' => intval($data['display_order'])
        );
        
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d');
        
        if (isset($data['id']) && $data['id'] > 0) {
            // Update existing branch
            $result = $wpdb->update(
                $table_name,
                $branch_data,
                array('id' => intval($data['id'])),
                $format,
                array('%d')
            );
            
            return $result !== false ? intval($data['id']) : false;
        } else {
            // Create new branch
            $result = $wpdb->insert($table_name, $branch_data, $format);
            
            return $result ? $wpdb->insert_id : false;
        }
    }
    
    /**
     * Delete branch
     */
    public function delete_branch_by_id($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_chatbot_branches';
        
        return $wpdb->delete(
            $table_name,
            array('id' => intval($id)),
            array('%d')
        );
    }
    
    /**
     * AJAX: Save branch
     */
    public function save_branch() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_chatbot_admin')) {
            wp_die(__('Security check failed', 'kata-chatbot'));
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-chatbot'));
        }
        
        $result = $this->save_branch_data($_POST);
        
        if ($result) {
            wp_send_json_success(array(
                'message' => __('Branch saved successfully', 'kata-chatbot'),
                'branch_id' => $result
            ));
        } else {
            wp_send_json_error(__('Failed to save branch', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX: Delete branch
     */
    public function delete_branch() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_chatbot_admin')) {
            wp_die(__('Security check failed', 'kata-chatbot'));
        }
        
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions', 'kata-chatbot'));
        }
        
        $id = intval($_POST['id']);
        
        if ($id <= 0) {
            wp_send_json_error(__('Invalid branch ID', 'kata-chatbot'));
        }
        
        $result = $this->delete_branch_by_id($id);
        
        if ($result) {
            wp_send_json_success(__('Branch deleted successfully', 'kata-chatbot'));
        } else {
            wp_send_json_error(__('Failed to delete branch', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX: Get branch
     */
    public function get_branch() {
        // Check nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_chatbot_admin')) {
            wp_die(__('Security check failed', 'kata-chatbot'));
        }
        
        $id = intval($_POST['id']);
        $branch = $this->get_branch_by_id($id);
        
        if ($branch) {
            wp_send_json_success($branch);
        } else {
            wp_send_json_error(__('Branch not found', 'kata-chatbot'));
        }
    }
    
    /**
     * AJAX: Get branches
     */
    public function get_branches() {
        $branches = $this->get_all_branches();
        wp_send_json_success($branches);
    }
    
    /**
     * AJAX: Get branch contacts (frontend)
     */
    public function get_branch_contacts() {
        $id = intval($_POST['branch_id']);
        $branch = $this->get_branch_by_id($id);
        
        if ($branch && $branch->is_active) {
            $contacts = array(
                'name' => $branch->name,
                'phone' => $branch->phone,
                'hotline' => $branch->hotline,
                'facebook_url' => $branch->facebook_url,
                'zalo_url' => $branch->zalo_url,
                'address' => $branch->address,
                'working_hours' => $branch->working_hours
            );
            
            wp_send_json_success($contacts);
        } else {
            wp_send_json_error(__('Branch not found or inactive', 'kata-chatbot'));
        }
    }
}
