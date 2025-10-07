<?php
/**
 * Admin Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin Class
 */
class KataSchema_Admin {
    
    /**
     * Schema generator instance
     */
    private $generator;
    
    /**
     * Validator instance
     */
    private $validator;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->generator = new KataSchema_Generator();
        $this->validator = new KataSchema_Validator();
        $this->add_hooks();
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // Meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box_data'));
        
        // AJAX handlers
        add_action('wp_ajax_kata_save_schema_template', array($this, 'ajax_save_template'));
        add_action('wp_ajax_kata_delete_schema_template', array($this, 'ajax_delete_template'));
        add_action('wp_ajax_kata_test_schema', array($this, 'ajax_test_schema'));
        add_action('wp_ajax_kata_export_schema', array($this, 'ajax_export_schema'));
        add_action('wp_ajax_kata_import_schema', array($this, 'ajax_import_schema'));
        
        // Settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Admin notices
        add_action('admin_notices', array($this, 'admin_notices'));
        
        // Quick edit
        add_action('quick_edit_custom_box', array($this, 'quick_edit_schema'), 10, 2);
        add_action('wp_ajax_kata_quick_edit_schema', array($this, 'ajax_quick_edit_save'));
        
        // Bulk actions
        add_filter('bulk_actions-edit-post', array($this, 'add_bulk_actions'));
        add_filter('handle_bulk_actions-edit-post', array($this, 'handle_bulk_actions'), 10, 3);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main menu
        add_menu_page(
            __('Schema Markup', 'kata-schema-markup'),
            __('Schema Markup', 'kata-schema-markup'),
            'manage_options',
            'kata-schema-markup',
            array($this, 'admin_dashboard_page'),
            'dashicons-networking',
            85
        );
        
        // Dashboard
        add_submenu_page(
            'kata-schema-markup',
            __('Dashboard', 'kata-schema-markup'),
            __('Dashboard', 'kata-schema-markup'),
            'manage_options',
            'kata-schema-markup',
            array($this, 'admin_dashboard_page')
        );
        
        // Templates
        add_submenu_page(
            'kata-schema-markup',
            __('Templates', 'kata-schema-markup'),
            __('Templates', 'kata-schema-markup'),
            'manage_options',
            'kata-schema-templates',
            array($this, 'admin_templates_page')
        );
        
        // Validation
        add_submenu_page(
            'kata-schema-markup',
            __('Validation', 'kata-schema-markup'),
            __('Validation', 'kata-schema-markup'),
            'manage_options',
            'kata-schema-validation',
            array($this, 'admin_validation_page')
        );
        
        // Settings
        add_submenu_page(
            'kata-schema-markup',
            __('Settings', 'kata-schema-markup'),
            __('Settings', 'kata-schema-markup'),
            'manage_options',
            'kata-schema-settings',
            array($this, 'admin_settings_page')
        );
        
        // Tools
        add_submenu_page(
            'kata-schema-markup',
            __('Tools', 'kata-schema-markup'),
            __('Tools', 'kata-schema-markup'),
            'manage_options',
            'kata-schema-tools',
            array($this, 'admin_tools_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on plugin pages
        if (strpos($hook, 'kata-schema') === false && !in_array($hook, array('post.php', 'post-new.php', 'edit.php'))) {
            return;
        }
        
        // Admin CSS
        wp_enqueue_style(
            'kata-schema-admin',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/admin.css',
            array(),
            KATA_SCHEMA_VERSION
        );
        
        // Admin JS
        wp_enqueue_script(
            'kata-schema-admin',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/admin.js',
            array('jquery', 'wp-util', 'wp-api'),
            KATA_SCHEMA_VERSION,
            true
        );
        
        // CodeMirror for JSON editing
        wp_enqueue_code_editor(array('type' => 'application/json'));
        
        // Localize script
        wp_localize_script('kata-schema-admin', 'kataSchemaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_schema_admin_nonce'),
            'pluginUrl' => plugin_dir_url(dirname(__FILE__)),
            'i18n' => array(
                'saving' => __('Saving...', 'kata-schema-markup'),
                'saved' => __('Saved!', 'kata-schema-markup'),
                'error' => __('Error occurred', 'kata-schema-markup'),
                'confirmDelete' => __('Are you sure you want to delete this template?', 'kata-schema-markup'),
                'validating' => __('Validating...', 'kata-schema-markup'),
                'valid' => __('Schema is valid!', 'kata-schema-markup'),
                'invalid' => __('Schema has errors', 'kata-schema-markup')
            ),
            'schemaTypes' => $this->generator->get_schema_types()
        ));
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        $post_types = get_option('kata_schema_post_types', array('post', 'page'));
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'kata-schema-metabox',
                __('Schema Markup', 'kata-schema-markup'),
                array($this, 'render_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('kata_schema_metabox', 'kata_schema_metabox_nonce');
        
        $schema_type = get_post_meta($post->ID, '_kata_schema_type', true);
        $schema_data = get_post_meta($post->ID, '_kata_schema_data', true);
        $schema_enabled = get_post_meta($post->ID, '_kata_schema_enabled', true);
        
        if ($schema_enabled === '') {
            $schema_enabled = '1'; // Default enabled
        }
        
        include dirname(__FILE__) . '/../templates/admin/post-metabox.php';
    }
    
    /**
     * Save meta box data
     */
    public function save_meta_box_data($post_id) {
        // Check nonce
        if (!isset($_POST['kata_schema_metabox_nonce']) || 
            !wp_verify_nonce($_POST['kata_schema_metabox_nonce'], 'kata_schema_metabox')) {
            return;
        }
        
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save schema type
        if (isset($_POST['kata_schema_type'])) {
            update_post_meta($post_id, '_kata_schema_type', sanitize_text_field($_POST['kata_schema_type']));
        }
        
        // Save schema data
        if (isset($_POST['kata_schema_data'])) {
            $schema_data = wp_unslash($_POST['kata_schema_data']);
            
            // Validate JSON
            $json_data = json_decode($schema_data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                update_post_meta($post_id, '_kata_schema_data', $schema_data);
            }
        }
        
        // Save enabled status
        $enabled = isset($_POST['kata_schema_enabled']) ? '1' : '0';
        update_post_meta($post_id, '_kata_schema_enabled', $enabled);
        
        // Clear cache for this post
        $this->clear_post_cache($post_id);
    }
    
    /**
     * Dashboard page
     */
    public function admin_dashboard_page() {
        $stats = $this->get_dashboard_stats();
        include dirname(__FILE__) . '/../templates/admin/dashboard.php';
    }
    
    /**
     * Templates page
     */
    public function admin_templates_page() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_schema_templates';
        $templates = $wpdb->get_results("SELECT * FROM $table_name ORDER BY type ASC");
        
        include dirname(__FILE__) . '/../templates/admin/templates.php';
    }
    
    /**
     * Validation page
     */
    public function admin_validation_page() {
        $validation_results = array();
        
        if (isset($_POST['validate_url'])) {
            $url = sanitize_url($_POST['validate_url']);
            $validation_results = $this->validate_url_schema($url);
        }
        
        include dirname(__FILE__) . '/../templates/admin/validation.php';
    }
    
    /**
     * Settings page
     */
    public function admin_settings_page() {
        if (isset($_POST['submit'])) {
            $this->save_settings();
        }
        
        include dirname(__FILE__) . '/../templates/admin/settings.php';
    }
    
    /**
     * Tools page
     */
    public function admin_tools_page() {
        include dirname(__FILE__) . '/../templates/admin/tools.php';
    }
    
    /**
     * Get dashboard stats
     */
    private function get_dashboard_stats() {
        global $wpdb;
        
        $stats = array();
        
        // Total posts with schema
        $stats['posts_with_schema'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} 
             WHERE meta_key = '_kata_schema_enabled' AND meta_value = '1'"
        );
        
        // Schema types usage
        $stats['schema_types'] = $wpdb->get_results(
            "SELECT meta_value as type, COUNT(*) as count 
             FROM {$wpdb->postmeta} 
             WHERE meta_key = '_kata_schema_type' 
             GROUP BY meta_value 
             ORDER BY count DESC"
        );
        
        // Cache stats
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        $stats['cached_schemas'] = $wpdb->get_var("SELECT COUNT(*) FROM $cache_table");
        
        // Recent activity
        $stats['recent_posts'] = $wpdb->get_results(
            "SELECT p.ID, p.post_title, p.post_type, pm.meta_value as schema_type
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_kata_schema_type'
             LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_kata_schema_enabled'
             WHERE pm2.meta_value = '1'
             AND p.post_status = 'publish'
             ORDER BY p.post_modified DESC
             LIMIT 10"
        );
        
        return $stats;
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('kata_schema_settings', 'kata_schema_output_enabled');
        register_setting('kata_schema_settings', 'kata_schema_enabled_schemas');
        register_setting('kata_schema_settings', 'kata_schema_post_types');
        register_setting('kata_schema_settings', 'kata_schema_cache_enabled');
        register_setting('kata_schema_settings', 'kata_schema_cache_duration');
        register_setting('kata_schema_settings', 'kata_schema_organization_name');
        register_setting('kata_schema_settings', 'kata_schema_organization_logo');
        register_setting('kata_schema_settings', 'kata_schema_social_profiles');
        register_setting('kata_schema_settings', 'kata_schema_breadcrumb_enabled');
        register_setting('kata_schema_settings', 'kata_schema_frontend_features');
        register_setting('kata_schema_settings', 'kata_schema_show_validation_widget');
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        check_admin_referer('kata_schema_settings');
        
        $settings = array(
            'kata_schema_output_enabled',
            'kata_schema_enabled_schemas',
            'kata_schema_post_types',
            'kata_schema_cache_enabled',
            'kata_schema_cache_duration',
            'kata_schema_organization_name',
            'kata_schema_organization_logo',
            'kata_schema_social_profiles',
            'kata_schema_breadcrumb_enabled',
            'kata_schema_frontend_features',
            'kata_schema_show_validation_widget'
        );
        
        foreach ($settings as $setting) {
            if (isset($_POST[$setting])) {
                update_option($setting, $_POST[$setting]);
            } else {
                delete_option($setting);
            }
        }
        
        add_settings_error('kata_schema_settings', 'settings_saved', __('Settings saved successfully!', 'kata-schema-markup'), 'success');
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        settings_errors('kata_schema_settings');
    }
    
    /**
     * AJAX: Save template
     */
    public function ajax_save_template() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'kata-schema-markup')));
        }
        
        global $wpdb;
        
        $template_id = intval($_POST['template_id']);
        $name = sanitize_text_field($_POST['name']);
        $type = sanitize_text_field($_POST['type']);
        $schema_json = wp_unslash($_POST['schema_json']);
        $conditions = sanitize_textarea_field($_POST['conditions']);
        $post_types = sanitize_text_field($_POST['post_types']);
        $status = sanitize_text_field($_POST['status']);
        
        // Validate JSON
        $schema_array = json_decode($schema_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid JSON format', 'kata-schema-markup')));
        }
        
        $table_name = $wpdb->prefix . 'kata_schema_templates';
        
        $data = array(
            'name' => $name,
            'type' => $type,
            'schema_json' => $schema_json,
            'conditions' => $conditions,
            'post_types' => $post_types,
            'status' => $status,
            'updated_at' => current_time('mysql')
        );
        
        if ($template_id) {
            // Update existing template
            $result = $wpdb->update($table_name, $data, array('id' => $template_id));
        } else {
            // Create new template
            $data['created_at'] = current_time('mysql');
            $result = $wpdb->insert($table_name, $data);
            $template_id = $wpdb->insert_id;
        }
        
        if ($result !== false) {
            wp_send_json_success(array(
                'message' => __('Template saved successfully!', 'kata-schema-markup'),
                'template_id' => $template_id
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to save template', 'kata-schema-markup')));
        }
    }
    
    /**
     * AJAX: Delete template
     */
    public function ajax_delete_template() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Insufficient permissions', 'kata-schema-markup')));
        }
        
        global $wpdb;
        
        $template_id = intval($_POST['template_id']);
        
        $table_name = $wpdb->prefix . 'kata_schema_templates';
        $result = $wpdb->delete($table_name, array('id' => $template_id));
        
        if ($result !== false) {
            wp_send_json_success(array('message' => __('Template deleted successfully!', 'kata-schema-markup')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete template', 'kata-schema-markup')));
        }
    }
    
    /**
     * AJAX: Test schema
     */
    public function ajax_test_schema() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        $schema_json = wp_unslash($_POST['schema_json']);
        
        // Parse JSON
        $schema = json_decode($schema_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid JSON format', 'kata-schema-markup')));
        }
        
        // Validate schema
        $validation = $this->validator->validate_schema($schema);
        
        wp_send_json_success(array(
            'validation' => $validation,
            'report' => $this->validator->generate_report($schema)
        ));
    }
    
    /**
     * Validate URL schema
     */
    private function validate_url_schema($url) {
        // This would typically fetch the page and extract schema
        // For now, return placeholder
        return array(
            'url' => $url,
            'schemas_found' => 0,
            'errors' => array(),
            'warnings' => array()
        );
    }
    
    /**
     * Clear post cache
     */
    private function clear_post_cache($post_id) {
        global $wpdb;
        
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        $wpdb->delete($cache_table, array('post_id' => $post_id));
    }
    
    /**
     * Quick edit schema
     */
    public function quick_edit_schema($column_name, $post_type) {
        if ($column_name !== 'kata_schema') {
            return;
        }
        
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php _e('Schema Type', 'kata-schema-markup'); ?></span>
                    <select name="kata_schema_type">
                        <option value=""><?php _e('Default', 'kata-schema-markup'); ?></option>
                        <?php foreach ($this->generator->get_schema_types() as $key => $type): ?>
                            <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($type['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    <input type="checkbox" name="kata_schema_enabled" value="1">
                    <span class="checkbox-title"><?php _e('Enable Schema', 'kata-schema-markup'); ?></span>
                </label>
            </div>
        </fieldset>
        <?php
    }
    
    /**
     * Add bulk actions
     */
    public function add_bulk_actions($bulk_actions) {
        $bulk_actions['kata_enable_schema'] = __('Enable Schema', 'kata-schema-markup');
        $bulk_actions['kata_disable_schema'] = __('Disable Schema', 'kata-schema-markup');
        return $bulk_actions;
    }
    
    /**
     * Handle bulk actions
     */
    public function handle_bulk_actions($redirect_to, $action, $post_ids) {
        if (!in_array($action, array('kata_enable_schema', 'kata_disable_schema'))) {
            return $redirect_to;
        }
        
        $value = ($action === 'kata_enable_schema') ? '1' : '0';
        
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, '_kata_schema_enabled', $value);
            $this->clear_post_cache($post_id);
        }
        
        $redirect_to = add_query_arg('kata_schema_bulk', count($post_ids), $redirect_to);
        return $redirect_to;
    }
}
