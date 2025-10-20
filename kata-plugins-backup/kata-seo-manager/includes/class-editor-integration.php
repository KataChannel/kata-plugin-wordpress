<?php
/**
 * Editor Integration Class
 * 
 * Integrates schema builder with WordPress editors (Classic & Gutenberg)
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Editor_Integration {
    
    /**
     * Schema generator instance
     */
    private $generator;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->generator = new KATA_SEO_Schema_Generator();
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Classic Editor
        add_action('media_buttons', array($this, 'add_schema_button'));
        add_action('admin_footer', array($this, 'render_schema_modal'));
        
        // Gutenberg
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_gutenberg_assets'));
        
        // Meta box
        add_action('add_meta_boxes', array($this, 'add_schema_meta_box'));
        add_action('save_post', array($this, 'save_schema_meta_box'));
        
        // AJAX handlers
        add_action('wp_ajax_kata_get_schema_fields', array($this, 'ajax_get_schema_fields'));
        add_action('wp_ajax_kata_preview_schema', array($this, 'ajax_preview_schema'));
        add_action('wp_ajax_kata_insert_schema', array($this, 'ajax_insert_schema'));
    }
    
    /**
     * Add schema button to Classic Editor
     */
    public function add_schema_button() {
        global $post;
        
        if (!$post || !current_user_can('edit_posts')) {
            return;
        }
        
        echo '<button type="button" class="button kata-insert-schema-btn" data-editor="classic">
            <span class="dashicons dashicons-code-standards" style="vertical-align: middle;"></span> ' . 
            __('Insert Schema', 'kata-seo-manager') . 
        '</button>';
    }
    
    /**
     * Render schema modal
     */
    public function render_schema_modal() {
        global $post;
        
        if (!$post || !current_user_can('edit_posts')) {
            return;
        }
        
        $schema_types = KATA_SEO_Manager::get_instance()->get_schema_types();
        
        include KATA_SEO_MANAGER_PATH . 'admin/modal-schema.php';
    }
    
    /**
     * Enqueue Gutenberg assets
     */
    public function enqueue_gutenberg_assets() {
        wp_enqueue_script(
            'kata-seo-gutenberg',
            KATA_SEO_MANAGER_URL . 'assets/js/gutenberg-schema.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
            KATA_SEO_MANAGER_VERSION,
            true
        );
        
        wp_localize_script('kata-seo-gutenberg', 'kataGutenberg', array(
            'schema_types' => KATA_SEO_Manager::get_instance()->get_schema_types(),
            'nonce' => wp_create_nonce('kata_seo_nonce')
        ));
    }
    
    /**
     * Add schema meta box
     */
    public function add_schema_meta_box() {
        $post_types = get_post_types(array('public' => true));
        
        foreach ($post_types as $post_type) {
            add_meta_box(
                'kata-seo-schema-meta-box',
                __('Schema Markup', 'kata-seo-manager'),
                array($this, 'render_schema_meta_box'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Render schema meta box
     */
    public function render_schema_meta_box($post) {
        wp_nonce_field('kata_seo_schema_meta_box', 'kata_seo_schema_nonce');
        
        $schemas = get_post_meta($post->ID, '_kata_seo_schemas', true);
        if (!is_array($schemas)) {
            $schemas = array();
        }
        
        $schema_types = KATA_SEO_Manager::get_instance()->get_schema_types();
        
        ?>
        <div class="kata-schema-meta-box">
            <div class="kata-schema-list">
                <?php if (empty($schemas)): ?>
                    <p class="description"><?php _e('No schemas added yet. Click "Add Schema" to get started.', 'kata-seo-manager'); ?></p>
                <?php else: ?>
                    <?php foreach ($schemas as $index => $schema): ?>
                        <div class="kata-schema-item" data-index="<?php echo esc_attr($index); ?>">
                            <div class="schema-header">
                                <strong><?php echo esc_html($schema['type']); ?></strong>
                                <span class="schema-status <?php echo $schema['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $schema['is_active'] ? __('Active', 'kata-seo-manager') : __('Inactive', 'kata-seo-manager'); ?>
                                </span>
                                <div class="schema-actions">
                                    <button type="button" class="button-link kata-edit-schema" data-index="<?php echo esc_attr($index); ?>">
                                        <?php _e('Edit', 'kata-seo-manager'); ?>
                                    </button>
                                    <button type="button" class="button-link kata-toggle-schema" data-index="<?php echo esc_attr($index); ?>">
                                        <?php echo $schema['is_active'] ? __('Disable', 'kata-seo-manager') : __('Enable', 'kata-seo-manager'); ?>
                                    </button>
                                    <button type="button" class="button-link kata-delete-schema" data-index="<?php echo esc_attr($index); ?>">
                                        <?php _e('Delete', 'kata-seo-manager'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="schema-preview">
                                <code><?php echo esc_html(substr(json_encode($schema['data'], JSON_PRETTY_PRINT), 0, 200)); ?>...</code>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <p class="kata-schema-actions">
                <button type="button" class="button button-primary kata-add-schema">
                    <?php _e('Add Schema', 'kata-seo-manager'); ?>
                </button>
                <button type="button" class="button kata-validate-all-schemas">
                    <?php _e('Validate All', 'kata-seo-manager'); ?>
                </button>
            </p>
            
            <input type="hidden" name="kata_seo_schemas" id="kata-seo-schemas-data" value="<?php echo esc_attr(json_encode($schemas)); ?>">
        </div>
        
        <style>
            .kata-schema-meta-box {
                padding: 10px 0;
            }
            .kata-schema-item {
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 10px;
                background: #f9f9f9;
            }
            .schema-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 10px;
            }
            .schema-header strong {
                flex: 1;
            }
            .schema-status {
                padding: 2px 8px;
                border-radius: 3px;
                font-size: 11px;
                text-transform: uppercase;
            }
            .schema-status.active {
                background: #46b450;
                color: white;
            }
            .schema-status.inactive {
                background: #dc3232;
                color: white;
            }
            .schema-actions {
                display: flex;
                gap: 10px;
            }
            .schema-preview {
                background: white;
                padding: 10px;
                border-radius: 3px;
                font-family: monospace;
                font-size: 12px;
                overflow-x: auto;
            }
            .kata-schema-actions {
                margin-top: 15px;
            }
        </style>
        <?php
    }
    
    /**
     * Save schema meta box
     */
    public function save_schema_meta_box($post_id) {
        // Verify nonce
        if (!isset($_POST['kata_seo_schema_nonce']) || !wp_verify_nonce($_POST['kata_seo_schema_nonce'], 'kata_seo_schema_meta_box')) {
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
        
        // Save schemas
        if (isset($_POST['kata_seo_schemas'])) {
            $schemas = json_decode(stripslashes($_POST['kata_seo_schemas']), true);
            
            if (is_array($schemas)) {
                update_post_meta($post_id, '_kata_seo_schemas', $schemas);
                
                // Save to database table for statistics
                global $wpdb;
                $table_name = $wpdb->prefix . 'kata_seo_schemas';
                
                foreach ($schemas as $schema) {
                    $existing = $wpdb->get_var($wpdb->prepare(
                        "SELECT id FROM $table_name WHERE post_id = %d AND schema_type = %s",
                        $post_id,
                        $schema['type']
                    ));
                    
                    $data = array(
                        'post_id' => $post_id,
                        'schema_type' => $schema['type'],
                        'schema_data' => json_encode($schema['data']),
                        'schema_json' => json_encode($schema['schema']),
                        'is_active' => !empty($schema['is_active']) ? 1 : 0,
                        'updated_at' => current_time('mysql')
                    );
                    
                    if ($existing) {
                        $wpdb->update($table_name, $data, array('id' => $existing));
                    } else {
                        $data['created_at'] = current_time('mysql');
                        $wpdb->insert($table_name, $data);
                    }
                }
            }
        }
    }
    
    /**
     * AJAX: Get schema fields for specific type
     */
    public function ajax_get_schema_fields() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        
        if (empty($type)) {
            wp_send_json_error(array('message' => __('Schema type is required', 'kata-seo-manager')));
        }
        
        $fields = $this->generator->get_fields($type);
        $example = $this->generator->get_example($type);
        
        if (is_wp_error($fields)) {
            wp_send_json_error(array('message' => $fields->get_error_message()));
        }
        
        wp_send_json_success(array(
            'fields' => $fields,
            'example' => $example
        ));
    }
    
    /**
     * AJAX: Preview schema
     */
    public function ajax_preview_schema() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : array();
        
        if (empty($type)) {
            wp_send_json_error(array('message' => __('Schema type is required', 'kata-seo-manager')));
        }
        
        $result = $this->generator->generate($type, $data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        $json = $this->generator->to_json($result['schema'], true);
        
        wp_send_json_success(array(
            'schema' => $result['schema'],
            'json' => $json,
            'validation' => $result['validation']
        ));
    }
    
    /**
     * AJAX: Insert schema to post
     */
    public function ajax_insert_schema() {
        check_ajax_referer('kata_seo_nonce', 'nonce');
        
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
        $data = isset($_POST['data']) ? $_POST['data'] : array();
        
        if (!$post_id || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error(array('message' => __('Permission denied', 'kata-seo-manager')));
        }
        
        if (empty($type)) {
            wp_send_json_error(array('message' => __('Schema type is required', 'kata-seo-manager')));
        }
        
        // Generate schema
        $result = $this->generator->generate($type, $data);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        // Get existing schemas
        $schemas = get_post_meta($post_id, '_kata_seo_schemas', true);
        if (!is_array($schemas)) {
            $schemas = array();
        }
        
        // Add new schema
        $schemas[] = array(
            'type' => $type,
            'data' => $data,
            'schema' => $result['schema'],
            'is_active' => true,
            'created_at' => current_time('mysql')
        );
        
        // Save to post meta
        update_post_meta($post_id, '_kata_seo_schemas', $schemas);
        
        wp_send_json_success(array(
            'message' => __('Schema added successfully', 'kata-seo-manager'),
            'schema' => $result['schema'],
            'validation' => $result['validation']
        ));
    }
}
