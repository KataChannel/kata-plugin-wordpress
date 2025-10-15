<?php
/**
 * Job Posting Shortcode
 * 
 * [kata_job]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Job extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_job';
    protected $schema_type = 'jobposting';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'title' => '',
            'description' => '',
            'company' => '',
            'location' => '',
            'employment_type' => 'FULL_TIME',
            'date_posted' => '',
            'salary' => '',
            'currency' => 'USD',
            'show_output' => 'false'
        ));
        
        if (empty($atts['title'])) {
            $this->log_error('Job title is required');
            return '';
        }
        
        $data = array(
            'title' => $atts['title'],
            'description' => $atts['description'],
            'hiringOrganization' => array(
                '@type' => 'Organization',
                'name' => $atts['company']
            ),
            'jobLocation' => array(
                '@type' => 'Place',
                'address' => $atts['location']
            ),
            'employmentType' => $atts['employment_type'],
            'datePosted' => $atts['date_posted']
        );
        
        if (!empty($atts['salary'])) {
            $data['baseSalary'] = array(
                '@type' => 'MonetaryAmount',
                'currency' => $atts['currency'],
                'value' => array(
                    '@type' => 'QuantitativeValue',
                    'value' => $atts['salary'],
                    'unitText' => 'YEAR'
                )
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
