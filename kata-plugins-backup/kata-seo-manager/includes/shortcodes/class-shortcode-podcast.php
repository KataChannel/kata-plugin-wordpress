<?php
/**
 * Podcast Shortcode
 * 
 * [kata_podcast]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Podcast extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_podcast';
    protected $schema_type = 'podcastseries';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'author' => '',
            'url' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Podcast name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            ),
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
