<?php
/**
 * Person Shortcode
 * 
 * [kata_person]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Person extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_person';
    protected $schema_type = 'person';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'job_title' => '',
            'email' => '',
            'phone' => '',
            'url' => '',
            'image' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Person name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'jobTitle' => $atts['job_title'],
            'email' => $atts['email'],
            'telephone' => $atts['phone'],
            'url' => $atts['url'],
            'image' => $atts['image']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
