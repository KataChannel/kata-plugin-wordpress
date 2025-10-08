<?php
/**
 * Admin Settings Class
 * 
 * Manages plugin settings and configuration
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Admin_Settings {
    
    /**
     * Settings option name
     */
    const OPTION_NAME = 'kata_seo_settings';
    
    /**
     * Default settings
     */
    private $defaults = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->set_defaults();
        $this->init_hooks();
    }
    
    /**
     * Set default settings
     */
    private function set_defaults() {
        $this->defaults = array(
            // General
            'enable_auto_schema' => true,
            'enable_validation' => true,
            'strict_validation' => false,
            'output_location' => 'head', // head or footer
            
            // Schema Types (all enabled by default)
            'enabled_schemas' => array(
                'Article', 'Breadcrumb', 'Carousel', 'Course', 'Dataset',
                'Forum', 'EduQA', 'EmployerRating', 'Event', 'FAQ',
                'HowTo', 'ImageMetadata', 'JobPosting', 'LocalBusiness',
                'MathSolver', 'Movie', 'Organization', 'PracticeProblem',
                'Product', 'ProfilePage', 'Recipe', 'Review',
                'Sitelinks', 'Speakable', 'Video', 'WebPage'
            ),
            
            // Default Organization
            'organization_name' => get_bloginfo('name'),
            'organization_url' => get_bloginfo('url'),
            'organization_logo' => '',
            
            // Google Search Console
            'gsc_enabled' => false,
            'gsc_site_url' => '',
            'gsc_client_id' => '',
            'gsc_client_secret' => '',
            
            // Performance
            'cache_enabled' => true,
            'cache_duration' => 3600,
            
            // Developer
            'debug_mode' => false,
            'show_in_source' => true,
        );
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'kata_seo_settings_group',
            self::OPTION_NAME,
            array($this, 'sanitize_settings')
        );
        
        // General Section
        add_settings_section(
            'kata_seo_general_section',
            __('General Settings', 'kata-seo-manager'),
            array($this, 'render_general_section'),
            'kata-seo-settings'
        );
        
        add_settings_field(
            'enable_auto_schema',
            __('Auto Schema Generation', 'kata-seo-manager'),
            array($this, 'render_checkbox_field'),
            'kata-seo-settings',
            'kata_seo_general_section',
            array(
                'name' => 'enable_auto_schema',
                'label' => __('Automatically generate schema for posts/pages', 'kata-seo-manager')
            )
        );
        
        add_settings_field(
            'enable_validation',
            __('Schema Validation', 'kata-seo-manager'),
            array($this, 'render_checkbox_field'),
            'kata-seo-settings',
            'kata_seo_general_section',
            array(
                'name' => 'enable_validation',
                'label' => __('Enable schema validation before output', 'kata-seo-manager')
            )
        );
        
        add_settings_field(
            'strict_validation',
            __('Strict Validation', 'kata-seo-manager'),
            array($this, 'render_checkbox_field'),
            'kata-seo-settings',
            'kata_seo_general_section',
            array(
                'name' => 'strict_validation',
                'label' => __('Only output schemas that pass validation', 'kata-seo-manager')
            )
        );
        
        // Organization Section
        add_settings_section(
            'kata_seo_organization_section',
            __('Organization Details', 'kata-seo-manager'),
            array($this, 'render_organization_section'),
            'kata-seo-settings'
        );
        
        add_settings_field(
            'organization_name',
            __('Organization Name', 'kata-seo-manager'),
            array($this, 'render_text_field'),
            'kata-seo-settings',
            'kata_seo_organization_section',
            array(
                'name' => 'organization_name',
                'placeholder' => get_bloginfo('name')
            )
        );
        
        add_settings_field(
            'organization_url',
            __('Organization URL', 'kata-seo-manager'),
            array($this, 'render_text_field'),
            'kata-seo-settings',
            'kata_seo_organization_section',
            array(
                'name' => 'organization_url',
                'type' => 'url',
                'placeholder' => get_bloginfo('url')
            )
        );
        
        add_settings_field(
            'organization_logo',
            __('Organization Logo', 'kata-seo-manager'),
            array($this, 'render_image_field'),
            'kata-seo-settings',
            'kata_seo_organization_section',
            array(
                'name' => 'organization_logo'
            )
        );
        
        // Performance Section
        add_settings_section(
            'kata_seo_performance_section',
            __('Performance', 'kata-seo-manager'),
            array($this, 'render_performance_section'),
            'kata-seo-settings'
        );
        
        add_settings_field(
            'cache_enabled',
            __('Enable Caching', 'kata-seo-manager'),
            array($this, 'render_checkbox_field'),
            'kata-seo-settings',
            'kata_seo_performance_section',
            array(
                'name' => 'cache_enabled',
                'label' => __('Cache generated schemas', 'kata-seo-manager')
            )
        );
        
        add_settings_field(
            'cache_duration',
            __('Cache Duration', 'kata-seo-manager'),
            array($this, 'render_number_field'),
            'kata-seo-settings',
            'kata_seo_performance_section',
            array(
                'name' => 'cache_duration',
                'min' => 0,
                'max' => 86400,
                'suffix' => __('seconds', 'kata-seo-manager')
            )
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        // Checkboxes
        $checkboxes = array('enable_auto_schema', 'enable_validation', 'strict_validation', 'gsc_enabled', 'cache_enabled', 'debug_mode', 'show_in_source');
        foreach ($checkboxes as $checkbox) {
            $sanitized[$checkbox] = !empty($input[$checkbox]);
        }
        
        // Text fields
        $sanitized['organization_name'] = sanitize_text_field($input['organization_name'] ?? '');
        $sanitized['organization_url'] = esc_url_raw($input['organization_url'] ?? '');
        $sanitized['organization_logo'] = esc_url_raw($input['organization_logo'] ?? '');
        $sanitized['gsc_site_url'] = esc_url_raw($input['gsc_site_url'] ?? '');
        $sanitized['gsc_client_id'] = sanitize_text_field($input['gsc_client_id'] ?? '');
        $sanitized['gsc_client_secret'] = sanitize_text_field($input['gsc_client_secret'] ?? '');
        
        // Numbers
        $sanitized['cache_duration'] = absint($input['cache_duration'] ?? 3600);
        
        // Arrays
        $sanitized['enabled_schemas'] = isset($input['enabled_schemas']) && is_array($input['enabled_schemas']) 
            ? array_map('sanitize_text_field', $input['enabled_schemas']) 
            : array();
        
        // Output location
        $sanitized['output_location'] = in_array($input['output_location'] ?? 'head', array('head', 'footer')) 
            ? $input['output_location'] 
            : 'head';
        
        return $sanitized;
    }
    
    /**
     * Render section callbacks
     */
    public function render_general_section() {
        echo '<p>' . __('Configure general schema settings.', 'kata-seo-manager') . '</p>';
    }
    
    public function render_organization_section() {
        echo '<p>' . __('Default organization information for schema markup.', 'kata-seo-manager') . '</p>';
    }
    
    public function render_performance_section() {
        echo '<p>' . __('Optimize schema generation performance.', 'kata-seo-manager') . '</p>';
    }
    
    /**
     * Render field callbacks
     */
    public function render_checkbox_field($args) {
        $settings = $this->get_settings();
        $name = $args['name'];
        $value = isset($settings[$name]) ? $settings[$name] : false;
        $label = isset($args['label']) ? $args['label'] : '';
        
        printf(
            '<label><input type="checkbox" name="%s[%s]" value="1" %s> %s</label>',
            self::OPTION_NAME,
            esc_attr($name),
            checked($value, true, false),
            esc_html($label)
        );
    }
    
    public function render_text_field($args) {
        $settings = $this->get_settings();
        $name = $args['name'];
        $value = isset($settings[$name]) ? $settings[$name] : '';
        $type = isset($args['type']) ? $args['type'] : 'text';
        $placeholder = isset($args['placeholder']) ? $args['placeholder'] : '';
        
        printf(
            '<input type="%s" name="%s[%s]" value="%s" placeholder="%s" class="regular-text">',
            esc_attr($type),
            self::OPTION_NAME,
            esc_attr($name),
            esc_attr($value),
            esc_attr($placeholder)
        );
    }
    
    public function render_number_field($args) {
        $settings = $this->get_settings();
        $name = $args['name'];
        $value = isset($settings[$name]) ? $settings[$name] : 0;
        $min = isset($args['min']) ? $args['min'] : 0;
        $max = isset($args['max']) ? $args['max'] : 999999;
        $suffix = isset($args['suffix']) ? $args['suffix'] : '';
        
        printf(
            '<input type="number" name="%s[%s]" value="%s" min="%d" max="%d" class="small-text"> %s',
            self::OPTION_NAME,
            esc_attr($name),
            esc_attr($value),
            $min,
            $max,
            esc_html($suffix)
        );
    }
    
    public function render_image_field($args) {
        $settings = $this->get_settings();
        $name = $args['name'];
        $value = isset($settings[$name]) ? $settings[$name] : '';
        
        ?>
        <div class="kata-image-field">
            <input type="url" name="<?php echo self::OPTION_NAME; ?>[<?php echo esc_attr($name); ?>]" 
                   value="<?php echo esc_attr($value); ?>" class="regular-text kata-image-url">
            <button type="button" class="button kata-upload-image">
                <?php _e('Upload Image', 'kata-seo-manager'); ?>
            </button>
            <?php if ($value): ?>
                <div class="kata-image-preview">
                    <img src="<?php echo esc_url($value); ?>" style="max-width: 200px; height: auto;">
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Get settings
     */
    public function get_settings() {
        $settings = get_option(self::OPTION_NAME, array());
        return wp_parse_args($settings, $this->defaults);
    }
    
    /**
     * Get single setting
     */
    public function get_setting($key, $default = null) {
        $settings = $this->get_settings();
        
        if (isset($settings[$key])) {
            return $settings[$key];
        }
        
        return $default !== null ? $default : (isset($this->defaults[$key]) ? $this->defaults[$key] : null);
    }
    
    /**
     * Update setting
     */
    public function update_setting($key, $value) {
        $settings = $this->get_settings();
        $settings[$key] = $value;
        return update_option(self::OPTION_NAME, $settings);
    }
    
    /**
     * Reset settings to defaults
     */
    public function reset_settings() {
        return update_option(self::OPTION_NAME, $this->defaults);
    }
}
