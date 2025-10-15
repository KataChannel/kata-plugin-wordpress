<?php
/**
 * Website Shortcode
 * 
 * [kata_website]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Website extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_website';
    protected $schema_type = 'website';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'description' => get_bloginfo('description'),
            'show_output' => 'false'
        ));
        
        $data = array(
            'name' => $atts['name'],
            'url' => $atts['url'],
            'description' => $atts['description']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
