<?php
/**
 * Creative Work Shortcode
 * 
 * [kata_creativework]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_CreativeWork extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_creativework';
    protected $schema_type = 'creativework';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'author' => '',
            'date_published' => '',
            'url' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Creative work name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            ),
            'datePublished' => $atts['date_published'],
            'url' => $atts['url']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
