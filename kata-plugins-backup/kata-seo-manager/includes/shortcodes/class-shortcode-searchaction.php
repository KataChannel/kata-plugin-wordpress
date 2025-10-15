<?php
/**
 * Search Action Shortcode
 * 
 * [kata_searchaction]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_SearchAction extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_searchaction';
    protected $schema_type = 'searchaction';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'target' => home_url('/?s={search_term_string}'),
            'query_input' => 'required name=search_term_string',
            'show_output' => 'false'
        ));
        
        $data = array(
            'target' => $atts['target'],
            'query-input' => $atts['query_input']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '';
    }
}
