<?php
/**
 * Course Shortcode
 * 
 * [kata_course]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Course extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_course';
    protected $schema_type = 'course';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'provider' => '',
            'url' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Course name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'provider' => array(
                '@type' => 'Organization',
                'name' => $atts['provider']
            )
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
