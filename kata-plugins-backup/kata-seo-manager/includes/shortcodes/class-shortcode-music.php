<?php
/**
 * Music Recording Shortcode
 * 
 * [kata_music]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Music extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_music';
    protected $schema_type = 'musicrecording';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'artist' => '',
            'album' => '',
            'duration' => '',
            'url' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Music name is required');
            return '';
        }
        
        $data = array(
            'name' => $atts['name'],
            'byArtist' => array(
                '@type' => 'MusicGroup',
                'name' => $atts['artist']
            ),
            'inAlbum' => array(
                '@type' => 'MusicAlbum',
                'name' => $atts['album']
            ),
            'duration' => $atts['duration'],
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
