<?php
/**
 * Schema Generator Class
 * 
 * Generates Google-compliant JSON-LD schema markup
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Schema_Generator {
    
    /**
     * Schema handlers registry
     */
    private $schema_handlers = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->load_schema_handlers();
    }
    
    /**
     * Load all schema handler classes
     */
    private function load_schema_handlers() {
        $schema_types = array(
            'Article', 'Breadcrumb', 'Carousel', 'Course', 'Dataset',
            'Forum', 'EduQA', 'EmployerRating', 'Event', 'FAQ',
            'HowTo', 'ImageMetadata', 'JobPosting', 'LocalBusiness',
            'MathSolver', 'Movie', 'Organization', 'PracticeProblem',
            'Product', 'ProfilePage', 'Recipe', 'Review',
            'Sitelinks', 'Speakable', 'Video', 'WebPage'
        );
        
        foreach ($schema_types as $type) {
            $class_name = 'KATA_' . str_replace(' ', '_', ucwords(str_replace('-', ' ', $type))) . '_Schema';
            $file_name = 'class-' . strtolower(str_replace('_', '-', str_replace('KATA_', '', $class_name))) . '.php';
            $file_path = KATA_SEO_MANAGER_PATH . 'includes/schemas/' . $file_name;
            
            if (file_exists($file_path)) {
                require_once $file_path;
                if (class_exists($class_name)) {
                    $this->schema_handlers[$type] = new $class_name();
                }
            }
        }
    }
    
    /**
     * Generate schema markup
     * 
     * @param string $type Schema type
     * @param array $data Schema data
     * @param array $options Additional options
     * @return array|WP_Error Generated schema or error
     */
    public function generate($type, $data, $options = array()) {
        // Validate type
        if (!isset($this->schema_handlers[$type])) {
            return new WP_Error('invalid_schema_type', sprintf(__('Schema type "%s" not found', 'kata-seo-manager'), $type));
        }
        
        // Sanitize data
        $data = $this->sanitize_data($data);
        
        // Generate schema using specific handler
        $handler = $this->schema_handlers[$type];
        $schema = $handler->generate($data, $options);
        
        if (is_wp_error($schema)) {
            return $schema;
        }
        
        // Add WordPress context
        $schema = $this->add_wordpress_context($schema, $data, $options);
        
        // Validate generated schema
        $validation = $this->validate($schema, $type);
        
        if (!$validation['is_valid'] && !empty($options['strict'])) {
            return new WP_Error('invalid_schema', __('Generated schema failed validation', 'kata-seo-manager'), $validation);
        }
        
        return array(
            'schema' => $schema,
            'validation' => $validation
        );
    }
    
    /**
     * Validate schema against Google requirements
     * 
     * @param array $schema Schema to validate
     * @param string $type Schema type
     * @return array Validation result
     */
    public function validate($schema, $type = '') {
        $errors = array();
        $warnings = array();
        
        // Check @context
        if (empty($schema['@context'])) {
            $errors[] = __('@context is required', 'kata-seo-manager');
        } elseif ($schema['@context'] !== 'https://schema.org') {
            $warnings[] = __('@context should be https://schema.org', 'kata-seo-manager');
        }
        
        // Check @type
        if (empty($schema['@type'])) {
            $errors[] = __('@type is required', 'kata-seo-manager');
        }
        
        // Type-specific validation
        if ($type && isset($this->schema_handlers[$type])) {
            $handler = $this->schema_handlers[$type];
            if (method_exists($handler, 'validate')) {
                $type_validation = $handler->validate($schema);
                if (!empty($type_validation['errors'])) {
                    $errors = array_merge($errors, $type_validation['errors']);
                }
                if (!empty($type_validation['warnings'])) {
                    $warnings = array_merge($warnings, $type_validation['warnings']);
                }
            }
        }
        
        // Check for common required fields
        $this->validate_common_fields($schema, $errors, $warnings);
        
        return array(
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'validated_at' => current_time('mysql')
        );
    }
    
    /**
     * Validate common schema fields
     */
    private function validate_common_fields($schema, &$errors, &$warnings) {
        // Check for empty arrays
        foreach ($schema as $key => $value) {
            if (is_array($value) && empty($value) && $key !== '@context') {
                $warnings[] = sprintf(__('Field "%s" is an empty array', 'kata-seo-manager'), $key);
            }
        }
        
        // Check URL fields
        $url_fields = array('url', 'image', 'logo', 'sameAs');
        foreach ($url_fields as $field) {
            if (isset($schema[$field])) {
                $urls = is_array($schema[$field]) ? $schema[$field] : array($schema[$field]);
                foreach ($urls as $url) {
                    if (is_string($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
                        $errors[] = sprintf(__('Invalid URL in field "%s": %s', 'kata-seo-manager'), $field, $url);
                    }
                }
            }
        }
        
        // Check date fields
        $date_fields = array('datePublished', 'dateModified', 'startDate', 'endDate');
        foreach ($date_fields as $field) {
            if (isset($schema[$field]) && !$this->is_valid_date($schema[$field])) {
                $warnings[] = sprintf(__('Field "%s" may not be a valid ISO 8601 date', 'kata-seo-manager'), $field);
            }
        }
    }
    
    /**
     * Check if date is valid ISO 8601 format
     */
    private function is_valid_date($date) {
        if (empty($date)) {
            return false;
        }
        
        // Check ISO 8601 format
        $patterns = array(
            '/^\d{4}-\d{2}-\d{2}$/', // YYYY-MM-DD
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', // YYYY-MM-DDTHH:MM:SS
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/' // with timezone
        );
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $date)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Add WordPress-specific context to schema
     */
    private function add_wordpress_context($schema, $data, $options) {
        global $post;
        
        // Add post URL if available
        if (!empty($data['post_id'])) {
            $post_url = get_permalink($data['post_id']);
            if (empty($schema['url']) && $post_url) {
                $schema['url'] = $post_url;
            }
        } elseif (is_object($post) && !empty($post->ID)) {
            $post_url = get_permalink($post->ID);
            if (empty($schema['url']) && $post_url) {
                $schema['url'] = $post_url;
            }
        }
        
        // Add site name to Organization/Publisher if not set
        if (isset($schema['publisher']) && is_array($schema['publisher'])) {
            if (empty($schema['publisher']['name'])) {
                $schema['publisher']['name'] = get_bloginfo('name');
            }
            if (empty($schema['publisher']['logo']['url'])) {
                $custom_logo_id = get_theme_mod('custom_logo');
                if ($custom_logo_id) {
                    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                    if ($logo_url) {
                        $schema['publisher']['logo']['url'] = $logo_url;
                    }
                }
            }
        }
        
        return $schema;
    }
    
    /**
     * Sanitize schema data
     */
    private function sanitize_data($data) {
        if (!is_array($data)) {
            return array();
        }
        
        $sanitized = array();
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitize_data($value);
            } elseif (is_string($value)) {
                // Sanitize based on field name
                if (strpos($key, 'url') !== false || strpos($key, 'image') !== false) {
                    $sanitized[$key] = esc_url_raw($value);
                } elseif (strpos($key, 'email') !== false) {
                    $sanitized[$key] = sanitize_email($value);
                } elseif (strpos($key, 'description') !== false || strpos($key, 'text') !== false) {
                    $sanitized[$key] = sanitize_textarea_field($value);
                } else {
                    $sanitized[$key] = sanitize_text_field($value);
                }
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Convert schema array to JSON-LD string
     * 
     * @param array $schema Schema array
     * @param bool $pretty Pretty print
     * @return string JSON-LD string
     */
    public function to_json($schema, $pretty = false) {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }
        
        return json_encode($schema, $flags);
    }
    
    /**
     * Get schema handler for specific type
     * 
     * @param string $type Schema type
     * @return object|null Schema handler or null
     */
    public function get_handler($type) {
        return isset($this->schema_handlers[$type]) ? $this->schema_handlers[$type] : null;
    }
    
    /**
     * Get all available schema types
     * 
     * @return array Schema types
     */
    public function get_available_types() {
        return array_keys($this->schema_handlers);
    }
    
    /**
     * Get example data for schema type
     * 
     * @param string $type Schema type
     * @return array|WP_Error Example data or error
     */
    public function get_example($type) {
        if (!isset($this->schema_handlers[$type])) {
            return new WP_Error('invalid_schema_type', sprintf(__('Schema type "%s" not found', 'kata-seo-manager'), $type));
        }
        
        $handler = $this->schema_handlers[$type];
        
        if (method_exists($handler, 'get_example')) {
            return $handler->get_example();
        }
        
        return array();
    }
    
    /**
     * Get field definitions for schema type
     * 
     * @param string $type Schema type
     * @return array|WP_Error Field definitions or error
     */
    public function get_fields($type) {
        if (!isset($this->schema_handlers[$type])) {
            return new WP_Error('invalid_schema_type', sprintf(__('Schema type "%s" not found', 'kata-seo-manager'), $type));
        }
        
        $handler = $this->schema_handlers[$type];
        
        if (method_exists($handler, 'get_fields')) {
            return $handler->get_fields();
        }
        
        return array();
    }
    
    /**
     * Batch generate schemas
     * 
     * @param array $schemas Array of schemas to generate
     * @return array Results
     */
    public function batch_generate($schemas) {
        $results = array();
        
        foreach ($schemas as $index => $schema_data) {
            if (empty($schema_data['type']) || empty($schema_data['data'])) {
                $results[$index] = new WP_Error('invalid_schema_data', __('Missing type or data', 'kata-seo-manager'));
                continue;
            }
            
            $options = isset($schema_data['options']) ? $schema_data['options'] : array();
            $results[$index] = $this->generate($schema_data['type'], $schema_data['data'], $options);
        }
        
        return $results;
    }
}
