<?php
/**
 * Movie Shortcode
 * 
 * [kata_movie]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Movie extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_movie';
    protected $schema_type = 'movie';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'director' => '',
            'actor' => '',
            'date_published' => '',
            'description' => '',
            'duration' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Movie name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'director' => array(
                '@type' => 'Person',
                'name' => $atts['director']
            ),
            'actor' => array(
                '@type' => 'Person',
                'name' => $atts['actor']
            ),
            'datePublished' => $atts['date_published'],
            'description' => $atts['description'],
            'duration' => $atts['duration']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
