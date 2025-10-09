<?php
/**
 * KATA Schema Customization Admin UI
 * 
 * Visual builder for schema and content customization
 * 
 * @package KATA_SEO_Manager
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_Schema_Admin_UI {
    
    /**
     * Initialize admin UI
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_admin_page'), 20); // Priority 20 to run after main menu
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
        add_action('wp_ajax_kata_save_schema_preset', array(__CLASS__, 'save_preset'));
        add_action('wp_ajax_kata_load_schema_preset', array(__CLASS__, 'load_preset'));
        add_action('wp_ajax_kata_delete_schema_preset', array(__CLASS__, 'delete_preset'));
        add_action('wp_ajax_kata_preview_schema', array(__CLASS__, 'preview_schema'));
    }
    
    /**
     * Add admin menu page
     */
    public static function add_admin_page() {
        add_submenu_page(
            'kata-seo-manager',
            __('Schema Customization', 'kata-seo-manager'),
            __('Schema Customization', 'kata-seo-manager'),
            'manage_options',
            'kata-schema-customization',
            array(__CLASS__, 'render_admin_page')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_scripts($hook) {
        if ($hook !== 'kata-seo-manager_page_kata-schema-customization') {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'kata-schema-admin-ui',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/schema-admin-ui.css',
            array(),
            KATA_SEO_MANAGER_VERSION
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'kata-schema-admin-ui',
            KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/schema-admin-ui.js',
            array('jquery'),
            KATA_SEO_MANAGER_VERSION,
            true
        );
        
        // Pass data to JavaScript
        wp_localize_script('kata-schema-admin-ui', 'kataSchemaAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_schema_admin_nonce'),
            'schemaTypes' => self::get_schema_types(),
            'defaultProperties' => KATA_Schema_Customizer::get_all_default_properties()
        ));
    }
    
    /**
     * Get available schema types
     */
    private static function get_schema_types() {
        return array(
            'article' => __('Article', 'kata-seo-manager'),
            'faq' => __('FAQ', 'kata-seo-manager'),
            'recipe' => __('Recipe', 'kata-seo-manager'),
            'product' => __('Product', 'kata-seo-manager'),
            'event' => __('Event', 'kata-seo-manager'),
            'howto' => __('How-To', 'kata-seo-manager'),
            'video' => __('Video', 'kata-seo-manager'),
            'organization' => __('Organization', 'kata-seo-manager'),
            'rating' => __('Rating/Review', 'kata-seo-manager'),
            'quiz' => __('Quiz', 'kata-seo-manager'),
            'poll' => __('Poll', 'kata-seo-manager'),
            'wheel' => __('Wheel/Game', 'kata-seo-manager'),
            'course' => __('Course', 'kata-seo-manager'),
            'localbusiness' => __('Local Business', 'kata-seo-manager'),
            'jobposting' => __('Job Posting', 'kata-seo-manager')
        );
    }
    
    /**
     * Render admin page
     */
    public static function render_admin_page() {
        ?>
        <div class="wrap kata-schema-customization-page">
            <h1><?php _e('Schema Customization Builder', 'kata-seo-manager'); ?></h1>
            
            <div class="kata-schema-admin-container">
                <!-- Left Panel: Builder -->
                <div class="kata-schema-builder-panel">
                    <div class="panel-header">
                        <h2><?php _e('Customize Schema & Content', 'kata-seo-manager'); ?></h2>
                    </div>
                    
                    <div class="panel-content">
                        <!-- Schema Type Selection -->
                        <div class="builder-section">
                            <label class="section-title"><?php _e('Select Schema Type', 'kata-seo-manager'); ?></label>
                            <select id="kata-schema-type" class="kata-select-full">
                                <option value=""><?php _e('-- Choose Schema Type --', 'kata-seo-manager'); ?></option>
                                <?php foreach (self::get_schema_types() as $type => $label) : ?>
                                    <option value="<?php echo esc_attr($type); ?>"><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Mode Selection -->
                        <div class="builder-section">
                            <label class="section-title"><?php _e('Customization Mode', 'kata-seo-manager'); ?></label>
                            <div class="mode-tabs">
                                <button class="mode-tab active" data-mode="schema">
                                    <span class="dashicons dashicons-code-standards"></span>
                                    <?php _e('Schema Filtering', 'kata-seo-manager'); ?>
                                    <small><?php _e('Filter JSON-LD output', 'kata-seo-manager'); ?></small>
                                </button>
                                <button class="mode-tab" data-mode="content">
                                    <span class="dashicons dashicons-visibility"></span>
                                    <?php _e('Content Display', 'kata-seo-manager'); ?>
                                    <small><?php _e('Hide/show HTML elements', 'kata-seo-manager'); ?></small>
                                </button>
                                <button class="mode-tab" data-mode="both">
                                    <span class="dashicons dashicons-admin-settings"></span>
                                    <?php _e('Both Modes', 'kata-seo-manager'); ?>
                                    <small><?php _e('Combine both customizations', 'kata-seo-manager'); ?></small>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Schema Filtering Panel -->
                        <div id="schema-mode-panel" class="builder-section customization-panel">
                            <label class="section-title">
                                <?php _e('Schema Properties (JSON-LD)', 'kata-seo-manager'); ?>
                                <span class="description"><?php _e('Select which properties to include in schema', 'kata-seo-manager'); ?></span>
                            </label>
                            
                            <div class="properties-grid" id="schema-properties">
                                <!-- Will be populated dynamically -->
                                <p class="placeholder-text"><?php _e('Select a schema type to see available properties', 'kata-seo-manager'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Content Display Panel -->
                        <div id="content-mode-panel" class="builder-section customization-panel" style="display:none;">
                            <label class="section-title">
                                <?php _e('Content Elements (HTML)', 'kata-seo-manager'); ?>
                                <span class="description"><?php _e('Select which elements to display in content', 'kata-seo-manager'); ?></span>
                            </label>
                            
                            <div class="properties-grid" id="content-properties">
                                <!-- Will be populated dynamically -->
                                <p class="placeholder-text"><?php _e('Select a schema type to see content elements', 'kata-seo-manager'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="builder-actions">
                            <button id="kata-generate-shortcode" class="button button-primary button-large">
                                <span class="dashicons dashicons-shortcode"></span>
                                <?php _e('Generate Shortcode', 'kata-seo-manager'); ?>
                            </button>
                            
                            <button id="kata-save-preset" class="button button-secondary">
                                <span class="dashicons dashicons-saved"></span>
                                <?php _e('Save as Preset', 'kata-seo-manager'); ?>
                            </button>
                            
                            <button id="kata-load-preset" class="button button-secondary">
                                <span class="dashicons dashicons-download"></span>
                                <?php _e('Load Preset', 'kata-seo-manager'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Right Panel: Preview & Output -->
                <div class="kata-schema-preview-panel">
                    <div class="panel-header">
                        <h2><?php _e('Preview & Output', 'kata-seo-manager'); ?></h2>
                    </div>
                    
                    <div class="panel-content">
                        <!-- Shortcode Output -->
                        <div class="preview-section">
                            <label class="section-title"><?php _e('Generated Shortcode', 'kata-seo-manager'); ?></label>
                            <div class="shortcode-output-container">
                                <textarea id="kata-shortcode-output" class="shortcode-output" readonly placeholder="<?php _e('Shortcode will appear here...', 'kata-seo-manager'); ?>"></textarea>
                                <button id="kata-copy-shortcode" class="button button-small copy-button">
                                    <span class="dashicons dashicons-clipboard"></span>
                                    <?php _e('Copy', 'kata-seo-manager'); ?>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Schema Preview -->
                        <div class="preview-section">
                            <label class="section-title">
                                <?php _e('Schema Preview (JSON-LD)', 'kata-seo-manager'); ?>
                                <button id="kata-validate-schema" class="button button-small">
                                    <span class="dashicons dashicons-yes-alt"></span>
                                    <?php _e('Validate', 'kata-seo-manager'); ?>
                                </button>
                            </label>
                            <pre id="kata-schema-preview" class="schema-preview"><code>{
  "Select schema type and properties to see preview"
}</code></pre>
                        </div>
                        
                        <!-- HTML Preview -->
                        <div class="preview-section">
                            <label class="section-title"><?php _e('HTML Preview', 'kata-seo-manager'); ?></label>
                            <div id="kata-html-preview" class="html-preview">
                                <p class="placeholder-text"><?php _e('HTML content preview will appear here', 'kata-seo-manager'); ?></p>
                            </div>
                        </div>
                        
                        <!-- Tips -->
                        <div class="preview-section tips-section">
                            <label class="section-title">
                                <span class="dashicons dashicons-lightbulb"></span>
                                <?php _e('Tips & Best Practices', 'kata-seo-manager'); ?>
                            </label>
                            <ul class="tips-list">
                                <li><?php _e('<strong>Schema Filtering:</strong> Reduces JSON-LD size but may affect Rich Results', 'kata-seo-manager'); ?></li>
                                <li><?php _e('<strong>Content Display:</strong> Keeps schema full for SEO, only hides HTML elements', 'kata-seo-manager'); ?></li>
                                <li><?php _e('<strong>Recommended:</strong> Keep schema full, customize content display only', 'kata-seo-manager'); ?></li>
                                <li><?php _e('<strong>Required Fields:</strong> @context and @type are always included', 'kata-seo-manager'); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Presets Modal -->
            <div id="kata-presets-modal" class="kata-modal" style="display:none;">
                <div class="kata-modal-content">
                    <div class="kata-modal-header">
                        <h3><?php _e('Saved Presets', 'kata-seo-manager'); ?></h3>
                        <button class="kata-modal-close">&times;</button>
                    </div>
                    <div class="kata-modal-body">
                        <div id="kata-presets-list">
                            <!-- Presets will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * AJAX: Save preset
     */
    public static function save_preset() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'kata-seo-manager')));
        }
        
        $preset_name = sanitize_text_field($_POST['preset_name'] ?? '');
        $schema_type = sanitize_text_field($_POST['schema_type'] ?? '');
        $schema_properties = json_decode(stripslashes($_POST['schema_properties'] ?? '[]'), true);
        $content_properties = json_decode(stripslashes($_POST['content_properties'] ?? '[]'), true);
        
        if (empty($preset_name) || empty($schema_type)) {
            wp_send_json_error(array('message' => __('Invalid data', 'kata-seo-manager')));
        }
        
        $presets = get_option('kata_schema_presets', array());
        $preset_id = sanitize_title($preset_name);
        
        $presets[$preset_id] = array(
            'name' => $preset_name,
            'schema_type' => $schema_type,
            'schema_properties' => $schema_properties,
            'content_properties' => $content_properties,
            'created' => current_time('mysql')
        );
        
        update_option('kata_schema_presets', $presets);
        
        wp_send_json_success(array(
            'message' => __('Preset saved successfully', 'kata-seo-manager'),
            'preset_id' => $preset_id
        ));
    }
    
    /**
     * AJAX: Load preset
     */
    public static function load_preset() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        $preset_id = sanitize_text_field($_POST['preset_id'] ?? '');
        $presets = get_option('kata_schema_presets', array());
        
        if (!isset($presets[$preset_id])) {
            wp_send_json_error(array('message' => __('Preset not found', 'kata-seo-manager')));
        }
        
        wp_send_json_success($presets[$preset_id]);
    }
    
    /**
     * AJAX: Delete preset
     */
    public static function delete_preset() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'kata-seo-manager')));
        }
        
        $preset_id = sanitize_text_field($_POST['preset_id'] ?? '');
        $presets = get_option('kata_schema_presets', array());
        
        if (isset($presets[$preset_id])) {
            unset($presets[$preset_id]);
            update_option('kata_schema_presets', $presets);
            wp_send_json_success(array('message' => __('Preset deleted', 'kata-seo-manager')));
        } else {
            wp_send_json_error(array('message' => __('Preset not found', 'kata-seo-manager')));
        }
    }
    
    /**
     * AJAX: Preview schema
     */
    public static function preview_schema() {
        check_ajax_referer('kata_schema_admin_nonce', 'nonce');
        
        $schema_type = sanitize_text_field($_POST['schema_type'] ?? '');
        $properties = json_decode(stripslashes($_POST['properties'] ?? '[]'), true);
        
        // Generate sample schema
        $sample_schema = self::generate_sample_schema($schema_type, $properties);
        
        wp_send_json_success(array(
            'schema' => $sample_schema,
            'json' => json_encode($sample_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ));
    }
    
    /**
     * Generate sample schema for preview
     */
    private static function generate_sample_schema($schema_type, $properties) {
        $sample_data = array(
            'article' => array(
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => 'Sample Article Title',
                'description' => 'This is a sample description',
                'author' => array('@type' => 'Person', 'name' => 'John Doe'),
                'datePublished' => '2025-01-15T10:00:00+07:00',
                'image' => 'https://example.com/image.jpg'
            ),
            'recipe' => array(
                '@context' => 'https://schema.org',
                '@type' => 'Recipe',
                'name' => 'Sample Recipe',
                'description' => 'Delicious recipe description',
                'author' => array('@type' => 'Person', 'name' => 'Chef John'),
                'prepTime' => 'PT30M',
                'cookTime' => 'PT1H',
                'recipeIngredient' => array('Ingredient 1', 'Ingredient 2'),
                'recipeInstructions' => array('Step 1', 'Step 2')
            ),
            'product' => array(
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => 'Sample Product',
                'description' => 'Product description',
                'brand' => array('@type' => 'Brand', 'name' => 'Brand Name'),
                'offers' => array(
                    '@type' => 'Offer',
                    'price' => '99.99',
                    'priceCurrency' => 'USD'
                )
            )
        );
        
        $schema = $sample_data[$schema_type] ?? array('@context' => 'https://schema.org', '@type' => 'Thing');
        
        // Filter based on selected properties
        if (!empty($properties)) {
            $filtered = array('@context' => $schema['@context'], '@type' => $schema['@type']);
            foreach ($properties as $prop) {
                if (isset($schema[$prop])) {
                    $filtered[$prop] = $schema[$prop];
                }
            }
            return $filtered;
        }
        
        return $schema;
    }
    
    /**
     * Get all default properties for all schema types
     */
    public static function get_default_properties() {
        return array(
            'article' => array(
                'headline' => true,
                'description' => true,
                'author' => true,
                'datePublished' => true,
                'dateModified' => true,
                'image' => true,
                'publisher' => true,
                'wordCount' => false,
                'articleBody' => false,
                'keywords' => false,
                'articleSection' => false,
                'inLanguage' => false
            ),
            'faq' => array(
                'mainEntity' => true
            ),
            'recipe' => array(
                'name' => true,
                'description' => true,
                'image' => true,
                'author' => true,
                'prepTime' => true,
                'cookTime' => true,
                'totalTime' => true,
                'recipeYield' => true,
                'recipeCategory' => true,
                'recipeCuisine' => true,
                'recipeIngredient' => true,
                'recipeInstructions' => true,
                'nutrition' => false,
                'aggregateRating' => false,
                'keywords' => false,
                'video' => false
            ),
            'product' => array(
                'name' => true,
                'description' => true,
                'image' => true,
                'brand' => true,
                'sku' => true,
                'mpn' => false,
                'gtin' => false,
                'offers' => true,
                'aggregateRating' => false,
                'review' => false,
                'manufacturer' => false,
                'category' => false,
                'color' => false,
                'weight' => false,
                'width' => false,
                'height' => false,
                'depth' => false
            ),
            'event' => array(
                'name' => true,
                'description' => true,
                'startDate' => true,
                'endDate' => false,
                'location' => true,
                'image' => true,
                'organizer' => true,
                'performer' => false,
                'offers' => false,
                'eventStatus' => false,
                'eventAttendanceMode' => false
            ),
            'howto' => array(
                'name' => true,
                'description' => true,
                'image' => true,
                'step' => true,
                'totalTime' => false,
                'estimatedCost' => false,
                'supply' => false,
                'tool' => false
            ),
            'quiz' => array(
                'name' => true,
                'description' => true,
                'questions' => true,
                'educationalLevel' => false,
                'learningResourceType' => false
            ),
            'poll' => array(
                'name' => true,
                'description' => true,
                'options' => true,
                'totalVotes' => false
            ),
            'wheel' => array(
                'name' => true,
                'description' => true,
                'segments' => true,
                'numberOfSegments' => true
            ),
            'course' => array(
                'name' => true,
                'description' => true,
                'provider' => true,
                'offers' => false,
                'aggregateRating' => false,
                'courseCode' => false,
                'hasCourseInstance' => false
            ),
            'localbusiness' => array(
                'name' => true,
                'description' => true,
                'address' => true,
                'telephone' => true,
                'image' => true,
                'openingHours' => false,
                'priceRange' => false,
                'geo' => false,
                'aggregateRating' => false
            ),
            'jobposting' => array(
                'title' => true,
                'description' => true,
                'hiringOrganization' => true,
                'jobLocation' => true,
                'datePosted' => true,
                'employmentType' => true,
                'baseSalary' => false,
                'validThrough' => false,
                'qualifications' => false
            ),
            'video' => array(
                'name' => true,
                'description' => true,
                'thumbnailUrl' => true,
                'uploadDate' => true,
                'duration' => false,
                'contentUrl' => false,
                'embedUrl' => false
            ),
            'organization' => array(
                'name' => true,
                'description' => true,
                'url' => true,
                'logo' => true,
                'contactPoint' => false,
                'sameAs' => false,
                'address' => false
            ),
            'rating' => array(
                'ratingValue' => true,
                'bestRating' => true,
                'worstRating' => true,
                'ratingCount' => false,
                'reviewCount' => false
            )
        );
    }
}

// Initialize
KATA_Schema_Admin_UI::init();

