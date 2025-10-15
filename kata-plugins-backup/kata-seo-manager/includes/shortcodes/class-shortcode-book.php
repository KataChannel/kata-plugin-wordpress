<?php
/**
 * Book Shortcode
 * 
 * [kata_book]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Book extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_book';
    protected $schema_type = 'book';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'author' => '',
            'isbn' => '',
            'publisher' => '',
            'date_published' => '',
            'description' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Book name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            ),
            'isbn' => $atts['isbn'],
            'publisher' => array(
                '@type' => 'Organization',
                'name' => $atts['publisher']
            ),
            'datePublished' => $atts['date_published'],
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
