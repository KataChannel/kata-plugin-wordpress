<?php
/**
 * Base Shortcode Class
 * 
 * Template for all schema shortcodes
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class KATA_SEO_Shortcode_Base {
    
    /**
     * Shortcode tag
     * @var string
     */
    protected $tag;
    
    /**
     * Schema type
     * @var string
     */
    protected $schema_type;
    
    /**
     * Schema generator
     * @var KATA_SEO_Schema_Generator
     */
    protected $generator;
    
    /**
     * Core instance
     * @var KATA_SEO_Core
     */
    protected $core;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->generator = new KATA_SEO_Schema_Generator();
        $this->core = KATA_SEO_Core::get_instance();
    }
    
    /**
     * Register shortcode
     */
    public function register() {
        if (empty($this->tag)) {
            return;
        }
        
        add_shortcode($this->tag, array($this, 'render'));
    }
    
    /**
     * Render shortcode
     * Must be implemented by child classes
     * 
     * @param array $atts Shortcode attributes
     * @param string $content Shortcode content
     * @return string
     */
    abstract public function render($atts, $content = null);
    
    /**
     * Generate and store schema
     * 
     * @param array $data Schema data
     * @return array|WP_Error
     */
    protected function generate_schema($data) {
        if (empty($this->schema_type)) {
            return new WP_Error('no_schema_type', 'Schema type not defined');
        }
        
        $result = $this->generator->generate($this->schema_type, $data);
        
        // Handle errors
        if (isset($result['errors']) && !empty($result['errors'])) {
            return new WP_Error('validation_errors', implode(', ', $result['errors']));
        }
        
        // Extract schema markup
        $schema = null;
        if (is_array($result) && isset($result['schema'])) {
            $schema = $result['schema'];
        } elseif (is_array($result) && !isset($result['errors'])) {
            $schema = $result;
        }
        
        // Store schema
        if ($schema && !is_wp_error($schema)) {
            $this->core->store_shortcode_schema($schema);
        }
        
        return $schema;
    }
    
    /**
     * Sanitize attributes
     * 
     * @param array $atts Raw attributes
     * @param array $defaults Default values
     * @return array
     */
    protected function sanitize_atts($atts, $defaults = array()) {
        $atts = shortcode_atts($defaults, $atts, $this->tag);
        
        foreach ($atts as $key => $value) {
            if (is_string($value)) {
                $atts[$key] = sanitize_text_field($value);
            }
        }
        
        return $atts;
    }
    
    /**
     * Render HTML output
     * 
     * @param string $content HTML content
     * @param string $class Additional CSS class
     * @return string
     */
    protected function render_html($content, $class = '') {
        $classes = array('kata-seo-shortcode', 'kata-seo-' . $this->schema_type);
        
        if (!empty($class)) {
            $classes[] = $class;
        }
        
        return sprintf(
            '<div class="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            $content
        );
    }
    
    /**
     * Log error
     * 
     * @param string $message Error message
     */
    protected function log_error($message) {
        if (WP_DEBUG && WP_DEBUG_LOG) {
            error_log('[KATA SEO] [' . $this->tag . '] ' . $message);
        }
    }
}
