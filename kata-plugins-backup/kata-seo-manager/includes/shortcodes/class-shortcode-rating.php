<?php
/**
 * Aggregate Rating Shortcode
 * 
 * [kata_rating]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Rating extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_rating';
    protected $schema_type = 'aggregaterating';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'rating_value' => '',
            'best_rating' => '5',
            'rating_count' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['rating_value'])) {
            $this->log_error('Rating value is required');
            return '';
        }
        
        $data = array(
            'ratingValue' => $atts['rating_value'],
            'bestRating' => $atts['best_rating'],
            'ratingCount' => $atts['rating_count']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
