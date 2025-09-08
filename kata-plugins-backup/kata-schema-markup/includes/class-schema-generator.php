<?php
/**
 * Schema Generator Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Schema Generator Class
 */
class KataSchema_Generator {
    
    /**
     * Cache duration in seconds
     */
    private $cache_duration;
    
    /**
     * Available schema types
     */
    private $schema_types = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->cache_duration = get_option('kata_schema_cache_duration', 24) * HOUR_IN_SECONDS;
        $this->init_schema_types();
        $this->add_hooks();
    }
    
    /**
     * Initialize schema types
     */
    private function init_schema_types() {
        $this->schema_types = array(
            'article' => array(
                'name' => __('Article', 'kata-schema-markup'),
                'class' => 'KataSchema_Article',
                'description' => __('News articles, blog posts, and other written content', 'kata-schema-markup')
            ),
            'breadcrumb' => array(
                'name' => __('Breadcrumb', 'kata-schema-markup'),
                'class' => 'KataSchema_Breadcrumb',
                'description' => __('Navigation breadcrumbs for better site structure', 'kata-schema-markup')
            ),
            'organization' => array(
                'name' => __('Organization', 'kata-schema-markup'),
                'class' => 'KataSchema_Organization',
                'description' => __('Information about your organization or business', 'kata-schema-markup')
            ),
            'website' => array(
                'name' => __('Website', 'kata-schema-markup'),
                'class' => 'KataSchema_Website',
                'description' => __('General website information and search functionality', 'kata-schema-markup')
            ),
            'product' => array(
                'name' => __('Product', 'kata-schema-markup'),
                'class' => 'KataSchema_Product',
                'description' => __('Products with pricing, availability, and reviews', 'kata-schema-markup')
            ),
            'review' => array(
                'name' => __('Review', 'kata-schema-markup'),
                'class' => 'KataSchema_Review',
                'description' => __('Reviews and ratings for products or services', 'kata-schema-markup')
            ),
            'faq' => array(
                'name' => __('FAQ', 'kata-schema-markup'),
                'class' => 'KataSchema_FAQ',
                'description' => __('Frequently Asked Questions with answers', 'kata-schema-markup')
            ),
            'howto' => array(
                'name' => __('How-To', 'kata-schema-markup'),
                'class' => 'KataSchema_HowTo',
                'description' => __('Step-by-step instructions and tutorials', 'kata-schema-markup')
            ),
            'event' => array(
                'name' => __('Event', 'kata-schema-markup'),
                'class' => 'KataSchema_Event',
                'description' => __('Events with dates, locations, and ticket information', 'kata-schema-markup')
            ),
            'recipe' => array(
                'name' => __('Recipe', 'kata-schema-markup'),
                'class' => 'KataSchema_Recipe',
                'description' => __('Cooking recipes with ingredients and instructions', 'kata-schema-markup')
            )
        );
        
        // Filter to allow custom schema types
        $this->schema_types = apply_filters('kata_schema_types', $this->schema_types);
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // AJAX handlers
        add_action('wp_ajax_kata_validate_schema', array($this, 'ajax_validate_schema'));
        add_action('wp_ajax_kata_generate_schema_preview', array($this, 'ajax_generate_preview'));
        add_action('wp_ajax_kata_clear_schema_cache', array($this, 'ajax_clear_cache'));
    }
    
    /**
     * Generate schema output for current page
     */
    public function generate_schema_output() {
        $schemas = array();
        
        // Get enabled schema types
        $enabled_schemas = get_option('kata_schema_enabled_schemas', array('article', 'breadcrumb'));
        
        foreach ($enabled_schemas as $schema_type) {
            if ($this->should_generate_schema($schema_type)) {
                $schema = $this->generate_schema(null, $schema_type);
                if ($schema) {
                    $schemas[] = $schema;
                }
            }
        }
        
        // Custom post-specific schemas
        if (is_singular()) {
            $post_id = get_the_ID();
            $custom_schema = get_post_meta($post_id, '_kata_schema_data', true);
            $custom_type = get_post_meta($post_id, '_kata_schema_type', true);
            
            if ($custom_schema && $custom_type) {
                $parsed_schema = $this->parse_schema_variables($custom_schema, $post_id);
                if ($parsed_schema) {
                    $schemas[] = $parsed_schema;
                }
            }
        }
        
        // Combine all schemas
        if (!empty($schemas)) {
            $output = "\n<!-- Kata Schema Markup -->\n";
            $output .= '<script type="application/ld+json">' . "\n";
            
            if (count($schemas) === 1) {
                $output .= wp_json_encode($schemas[0], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            } else {
                $output .= wp_json_encode($schemas, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }
            
            $output .= "\n" . '</script>' . "\n";
            $output .= "<!-- End Kata Schema Markup -->\n";
            
            return $output;
        }
        
        return '';
    }
    
    /**
     * Generate schema for specific post and type
     */
    public function generate_schema($post_id = null, $schema_type = 'article') {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        // Check cache first
        if (get_option('kata_schema_cache_enabled', true)) {
            $cached = $this->get_cached_schema($post_id, $schema_type);
            if ($cached !== false) {
                return $cached;
            }
        }
        
        // Generate new schema
        $schema = $this->generate_fresh_schema($post_id, $schema_type);
        
        // Cache the result
        if ($schema && get_option('kata_schema_cache_enabled', true)) {
            $this->cache_schema($post_id, $schema_type, $schema);
        }
        
        return $schema;
    }
    
    /**
     * Generate fresh schema without cache
     */
    private function generate_fresh_schema($post_id, $schema_type) {
        if (!isset($this->schema_types[$schema_type])) {
            return false;
        }
        
        $schema_class = $this->schema_types[$schema_type]['class'];
        
        if (!class_exists($schema_class)) {
            return false;
        }
        
        $schema_instance = new $schema_class();
        
        if (method_exists($schema_instance, 'generate')) {
            return $schema_instance->generate($post_id);
        }
        
        return false;
    }
    
    /**
     * Check if schema should be generated for current context
     */
    private function should_generate_schema($schema_type) {
        global $wpdb;
        
        // Get template conditions
        $table_name = $wpdb->prefix . 'kata_schema_templates';
        $template = $wpdb->get_row($wpdb->prepare(
            "SELECT conditions, post_types FROM $table_name WHERE type = %s AND status = 'active'",
            $schema_type
        ));
        
        if (!$template) {
            return false;
        }
        
        // Check post type conditions
        if ($template->post_types && $template->post_types !== 'all') {
            $allowed_post_types = explode(',', $template->post_types);
            $current_post_type = get_post_type();
            
            if ($current_post_type && !in_array($current_post_type, $allowed_post_types)) {
                return false;
            }
        }
        
        // Check conditional logic
        if ($template->conditions) {
            $conditions = json_decode($template->conditions, true);
            
            if (is_array($conditions)) {
                foreach ($conditions as $condition => $value) {
                    if (!$this->check_condition($condition, $value)) {
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * Check individual condition
     */
    private function check_condition($condition, $expected_value) {
        switch ($condition) {
            case 'is_home':
                return is_home() === $expected_value;
            case 'is_front_page':
                return is_front_page() === $expected_value;
            case 'is_single':
                return is_single() === $expected_value;
            case 'is_page':
                return is_page() === $expected_value;
            case 'is_category':
                return is_category() === $expected_value;
            case 'is_tag':
                return is_tag() === $expected_value;
            case 'is_archive':
                return is_archive() === $expected_value;
            case 'is_search':
                return is_search() === $expected_value;
            case 'post_type':
                return get_post_type() === $expected_value;
            default:
                return true;
        }
    }
    
    /**
     * Parse schema variables
     */
    public function parse_schema_variables($schema_json, $post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }
        
        // Define variable replacements
        $variables = array(
            '{{post_title}}' => get_the_title($post_id),
            '{{post_excerpt}}' => get_the_excerpt($post_id),
            '{{post_content}}' => wp_strip_all_tags(get_the_content(null, false, $post_id)),
            '{{featured_image}}' => get_the_post_thumbnail_url($post_id, 'large'),
            '{{author_name}}' => get_the_author_meta('display_name', $post->post_author),
            '{{author_email}}' => get_the_author_meta('email', $post->post_author),
            '{{author_url}}' => get_the_author_meta('url', $post->post_author),
            '{{publish_date}}' => get_the_date('c', $post_id),
            '{{modified_date}}' => get_the_modified_date('c', $post_id),
            '{{site_name}}' => get_bloginfo('name'),
            '{{site_description}}' => get_bloginfo('description'),
            '{{site_url}}' => home_url('/'),
            '{{site_logo}}' => get_option('kata_schema_organization_logo', ''),
            '{{organization_name}}' => get_option('kata_schema_organization_name', get_bloginfo('name')),
            '{{organization_logo}}' => get_option('kata_schema_organization_logo', ''),
            '{{social_profiles}}' => $this->get_social_profiles_array(),
            '{{breadcrumb_items}}' => $this->generate_breadcrumb_items($post_id),
            '{{search_url}}' => home_url('/?s={search_term_string}'),
            '{{post_url}}' => get_permalink($post_id),
            '{{post_type}}' => get_post_type($post_id),
            '{{categories}}' => $this->get_post_categories($post_id),
            '{{tags}}' => $this->get_post_tags($post_id),
            '{{reading_time}}' => $this->calculate_reading_time($post_id),
            '{{word_count}}' => str_word_count(wp_strip_all_tags(get_the_content(null, false, $post_id)))
        );
        
        // Custom field variables
        $custom_fields = get_post_meta($post_id);
        foreach ($custom_fields as $key => $values) {
            if (strpos($key, '_kata_schema_') === 0) {
                $field_name = str_replace('_kata_schema_', '', $key);
                $variables['{{' . $field_name . '}}'] = $values[0] ?? '';
            }
        }
        
        // Apply filters to allow custom variables
        $variables = apply_filters('kata_schema_variables', $variables, $post_id);
        
        // Replace variables in schema
        $parsed_schema = str_replace(array_keys($variables), array_values($variables), $schema_json);
        
        // Parse JSON
        $schema_array = json_decode($parsed_schema, true);
        
        if (json_last_error() === JSON_ERROR_NONE) {
            return $this->clean_schema_array($schema_array);
        }
        
        return false;
    }
    
    /**
     * Clean schema array by removing empty values
     */
    private function clean_schema_array($schema) {
        if (is_array($schema)) {
            foreach ($schema as $key => $value) {
                if (is_array($value)) {
                    $schema[$key] = $this->clean_schema_array($value);
                    if (empty($schema[$key])) {
                        unset($schema[$key]);
                    }
                } elseif (empty($value) || $value === '{{}}' || strpos($value, '{{') !== false) {
                    unset($schema[$key]);
                }
            }
        }
        
        return $schema;
    }
    
    /**
     * Get social profiles as array
     */
    private function get_social_profiles_array() {
        $profiles = get_option('kata_schema_social_profiles', array());
        return is_array($profiles) ? array_filter($profiles) : array();
    }
    
    /**
     * Generate breadcrumb items
     */
    private function generate_breadcrumb_items($post_id) {
        $items = array();
        $position = 1;
        
        // Home page
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_bloginfo('name'),
            'item' => home_url('/')
        );
        
        if (is_single() || is_page()) {
            $post = get_post($post_id);
            
            // Add category for posts
            if ($post->post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    $category = $categories[0];
                    $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $category->name,
                        'item' => get_category_link($category->term_id)
                    );
                }
            }
            
            // Add current page/post
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title($post_id),
                'item' => get_permalink($post_id)
            );
        }
        
        return $items;
    }
    
    /**
     * Get post categories
     */
    private function get_post_categories($post_id) {
        $categories = get_the_category($post_id);
        $category_names = array();
        
        foreach ($categories as $category) {
            $category_names[] = $category->name;
        }
        
        return $category_names;
    }
    
    /**
     * Get post tags
     */
    private function get_post_tags($post_id) {
        $tags = get_the_tags($post_id);
        $tag_names = array();
        
        if ($tags) {
            foreach ($tags as $tag) {
                $tag_names[] = $tag->name;
            }
        }
        
        return $tag_names;
    }
    
    /**
     * Calculate reading time
     */
    private function calculate_reading_time($post_id) {
        $content = get_the_content(null, false, $post_id);
        $word_count = str_word_count(wp_strip_all_tags($content));
        $reading_time = ceil($word_count / 200); // 200 words per minute
        
        return max(1, $reading_time); // Minimum 1 minute
    }
    
    /**
     * Get cached schema
     */
    private function get_cached_schema($post_id, $schema_type) {
        global $wpdb;
        
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        $cached = $wpdb->get_row($wpdb->prepare(
            "SELECT schema_json, created_at FROM $cache_table 
             WHERE post_id = %d AND schema_type = %s",
            $post_id, $schema_type
        ));
        
        if ($cached) {
            $cache_age = time() - strtotime($cached->created_at);
            if ($cache_age < $this->cache_duration) {
                return json_decode($cached->schema_json, true);
            }
        }
        
        return false;
    }
    
    /**
     * Cache schema
     */
    private function cache_schema($post_id, $schema_type, $schema) {
        global $wpdb;
        
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        $schema_json = wp_json_encode($schema);
        $hash = md5($schema_json);
        
        $wpdb->replace($cache_table, array(
            'post_id' => $post_id,
            'schema_type' => $schema_type,
            'schema_json' => $schema_json,
            'hash' => $hash,
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql')
        ));
    }
    
    /**
     * AJAX: Validate schema
     */
    public function ajax_validate_schema() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        $schema_json = sanitize_textarea_field($_POST['schema']);
        
        // Basic JSON validation
        $schema = json_decode($schema_json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array(
                'message' => __('Invalid JSON format', 'kata-schema-markup'),
                'error' => json_last_error_msg()
            ));
        }
        
        // Check required Schema.org fields
        $validation_result = $this->validate_schema_structure($schema);
        
        if ($validation_result['valid']) {
            wp_send_json_success(array(
                'message' => __('Schema is valid!', 'kata-schema-markup'),
                'warnings' => $validation_result['warnings']
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Schema validation failed', 'kata-schema-markup'),
                'errors' => $validation_result['errors']
            ));
        }
    }
    
    /**
     * Validate schema structure
     */
    private function validate_schema_structure($schema) {
        $errors = array();
        $warnings = array();
        
        // Check @context
        if (!isset($schema['@context']) || $schema['@context'] !== 'https://schema.org') {
            $errors[] = __('@context must be "https://schema.org"', 'kata-schema-markup');
        }
        
        // Check @type
        if (!isset($schema['@type'])) {
            $errors[] = __('@type is required', 'kata-schema-markup');
        }
        
        // Type-specific validations
        if (isset($schema['@type'])) {
            switch ($schema['@type']) {
                case 'Article':
                    if (!isset($schema['headline'])) {
                        $warnings[] = __('headline is recommended for Article', 'kata-schema-markup');
                    }
                    if (!isset($schema['author'])) {
                        $warnings[] = __('author is recommended for Article', 'kata-schema-markup');
                    }
                    break;
                
                case 'Product':
                    if (!isset($schema['name'])) {
                        $errors[] = __('name is required for Product', 'kata-schema-markup');
                    }
                    break;
                
                case 'Organization':
                    if (!isset($schema['name'])) {
                        $errors[] = __('name is required for Organization', 'kata-schema-markup');
                    }
                    break;
            }
        }
        
        return array(
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        );
    }
    
    /**
     * AJAX: Generate schema preview
     */
    public function ajax_generate_preview() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $schema_type = sanitize_text_field($_POST['schema_type']);
        
        $schema = $this->generate_schema($post_id, $schema_type);
        
        if ($schema) {
            wp_send_json_success(array(
                'schema' => wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to generate schema', 'kata-schema-markup')
            ));
        }
    }
    
    /**
     * AJAX: Clear cache
     */
    public function ajax_clear_cache() {
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        global $wpdb;
        
        $cache_table = $wpdb->prefix . 'kata_schema_cache';
        $wpdb->query("TRUNCATE TABLE $cache_table");
        
        // Clear related transients
        delete_transient('kata_schema_cache');
        
        wp_send_json_success(array(
            'message' => __('Cache cleared successfully', 'kata-schema-markup')
        ));
    }
    
    /**
     * Get available schema types
     */
    public function get_schema_types() {
        return $this->schema_types;
    }
}
