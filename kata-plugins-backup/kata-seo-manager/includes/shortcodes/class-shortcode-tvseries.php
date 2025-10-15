<?php
/**
 * TV Series Shortcode
 * 
 * [kata_tvseries]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_TVSeries extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_tvseries';
    protected $schema_type = 'tvseries';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'actor' => '',
            'number_of_seasons' => '',
            'number_of_episodes' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('TV Series name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'actor' => array(
                '@type' => 'Person',
                'name' => $atts['actor']
            ),
            'numberOfSeasons' => $atts['number_of_seasons'],
            'numberOfEpisodes' => $atts['number_of_episodes']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
