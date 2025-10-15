<?php
/**
 * Schema AJAX Handler
 * 
 * Handles all schema-related AJAX requests
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Schema_AJAX extends KATA_SEO_AJAX_Handler {
    
    /**
     * Initialize AJAX hooks
     */
    public function init() {
        // Schema management
        add_action('wp_ajax_kata_save_schema', array($this, 'save_schema'));
        add_action('wp_ajax_kata_delete_schema', array($this, 'delete_schema'));
        add_action('wp_ajax_kata_update_schema_status', array($this, 'update_schema_status'));
        add_action('wp_ajax_kata_get_schema', array($this, 'get_schema'));
        
        // Schema builder
        add_action('wp_ajax_kata_get_schema_attributes', array($this, 'get_schema_attributes'));
        add_action('wp_ajax_kata_get_schema_types', array($this, 'get_schema_types'));
        
        // Validation
        add_action('wp_ajax_kata_validate_schema', array($this, 'validate_schema'));
    }
    
    /**
     * Save schema
     */
    public function save_schema() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schema_id = $this->get_post('schema_id');
        $schema_name = $this->sanitize_text($this->get_post('schema_name'));
        $schema_type = $this->sanitize_text($this->get_post('schema_type'));
        $schema_data = $this->get_post('schema_data');
        
        if (empty($schema_name) || empty($schema_type)) {
            $this->error(__('Schema name and type are required', 'kata-seo-manager'));
        }
        
        $data = array(
            'schema_name' => $schema_name,
            'schema_type' => $schema_type,
            'schema_data' => wp_json_encode($schema_data),
            'updated_at' => current_time('mysql')
        );
        
        if ($schema_id) {
            // Update existing
            $result = $wpdb->update($table_name, $data, array('id' => $schema_id));
            $message = __('Schema updated successfully', 'kata-seo-manager');
        } else {
            // Insert new
            $data['created_at'] = current_time('mysql');
            $data['status'] = 'active';
            $result = $wpdb->insert($table_name, $data);
            $schema_id = $wpdb->insert_id;
            $message = __('Schema created successfully', 'kata-seo-manager');
        }
        
        if ($result === false) {
            $this->error(__('Failed to save schema', 'kata-seo-manager'));
        }
        
        $this->success(array(
            'message' => $message,
            'schema_id' => $schema_id
        ));
    }
    
    /**
     * Delete schema
     */
    public function delete_schema() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schema_id = intval($this->get_post('schema_id'));
        
        if (!$schema_id) {
            $this->error(__('Invalid schema ID', 'kata-seo-manager'));
        }
        
        $result = $wpdb->delete($table_name, array('id' => $schema_id));
        
        if ($result === false) {
            $this->error(__('Failed to delete schema', 'kata-seo-manager'));
        }
        
        $this->success(array(
            'message' => __('Schema deleted successfully', 'kata-seo-manager')
        ));
    }
    
    /**
     * Update schema status
     */
    public function update_schema_status() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schema_id = intval($this->get_post('schema_id'));
        $status = $this->sanitize_text($this->get_post('status'));
        
        if (!$schema_id || !in_array($status, array('active', 'inactive'))) {
            $this->error(__('Invalid parameters', 'kata-seo-manager'));
        }
        
        $result = $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $schema_id)
        );
        
        if ($result === false) {
            $this->error(__('Failed to update status', 'kata-seo-manager'));
        }
        
        $this->success(array(
            'message' => __('Status updated successfully', 'kata-seo-manager')
        ));
    }
    
    /**
     * Get schema
     */
    public function get_schema() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        $schema_id = intval($this->get_post('schema_id'));
        
        if (!$schema_id) {
            $this->error(__('Invalid schema ID', 'kata-seo-manager'));
        }
        
        $schema = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $schema_id));
        
        if (!$schema) {
            $this->error(__('Schema not found', 'kata-seo-manager'));
        }
        
        $schema->schema_data = json_decode($schema->schema_data, true);
        
        $this->success(array('schema' => $schema));
    }
    
    /**
     * Get schema attributes
     */
    public function get_schema_attributes() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $schema_type = $this->sanitize_text($this->get_post('schema_type'));
        
        if (empty($schema_type)) {
            $this->error(__('Schema type is required', 'kata-seo-manager'));
        }
        
        $generator = new KATA_SEO_Schema_Generator();
        $attributes = $generator->get_schema_attributes($schema_type);
        
        $this->success(array('attributes' => $attributes));
    }
    
    /**
     * Get schema types
     */
    public function get_schema_types() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $generator = new KATA_SEO_Schema_Generator();
        $schema_types = $generator->get_all_schema_types();
        
        $this->success(array('schema_types' => $schema_types));
    }
    
    /**
     * Validate schema
     */
    public function validate_schema() {
        $verified = $this->verify_request('kata_seo_nonce');
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        $schema_type = $this->sanitize_text($this->get_post('schema_type'));
        $schema_data = $this->get_post('schema_data');
        
        if (empty($schema_type) || empty($schema_data)) {
            $this->error(__('Schema type and data are required', 'kata-seo-manager'));
        }
        
        $generator = new KATA_SEO_Schema_Generator();
        $result = $generator->generate($schema_type, $schema_data);
        
        if (isset($result['errors']) && !empty($result['errors'])) {
            $this->success(array(
                'valid' => false,
                'errors' => $result['errors']
            ));
        } else {
            $this->success(array(
                'valid' => true,
                'schema' => $result
            ));
        }
    }
}
