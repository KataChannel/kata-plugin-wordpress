<?php
/**
 * Local Business Shortcode
 * 
 * [kata_localbusiness]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_LocalBusiness extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_localbusiness';
    protected $schema_type = 'localbusiness';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'image' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => '',
            'phone' => '',
            'price_range' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Business name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'image' => $atts['image'],
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => $atts['address'],
                'addressLocality' => $atts['city'],
                'addressRegion' => $atts['state'],
                'postalCode' => $atts['postal_code'],
                'addressCountry' => $atts['country']
            ),
            'telephone' => $atts['phone'],
            'priceRange' => $atts['price_range']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
