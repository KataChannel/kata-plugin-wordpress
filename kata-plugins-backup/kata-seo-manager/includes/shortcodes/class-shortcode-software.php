<?php
/**
 * Software Application Shortcode
 * 
 * [kata_software]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Software extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_software';
    protected $schema_type = 'softwareapplication';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'operating_system' => '',
            'application_category' => '',
            'price' => '',
            'currency' => 'USD',
            'rating_value' => '',
            'rating_count' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Software name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'operatingSystem' => $atts['operating_system'],
            'applicationCategory' => $atts['application_category'],
            'offers' => array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency']
            )
        );
        
        if (!empty($atts['rating_value'])) {
            $data['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
