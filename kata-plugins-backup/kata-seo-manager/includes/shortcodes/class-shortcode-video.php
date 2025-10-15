<?php
/**
 * Video Shortcode
 * 
 * [kata_video]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Video extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_video';
    protected $schema_type = 'video';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'thumbnail_url' => '',
            'upload_date' => '',
            'duration' => '',
            'content_url' => '',
            'embed_url' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['name']) || empty($atts['thumbnail_url'])) {
            $this->log_error('Video name and thumbnail are required');
            return '';
        }
        
        // Build schema data
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'thumbnailUrl' => $atts['thumbnail_url'],
            'uploadDate' => $atts['upload_date'],
            'duration' => $atts['duration'],
            'contentUrl' => $atts['content_url'],
            'embedUrl' => $atts['embed_url']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <div class="kata-video" itemscope itemtype="https://schema.org/VideoObject">
                <h3 itemprop="name"><?php echo esc_html($atts['name']); ?></h3>
                <p itemprop="description"><?php echo esc_html($atts['description']); ?></p>
                <?php if (!empty($atts['embed_url'])): ?>
                    <iframe src="<?php echo esc_url($atts['embed_url']); ?>" frameborder="0" allowfullscreen></iframe>
                <?php endif; ?>
                <meta itemprop="thumbnailUrl" content="<?php echo esc_url($atts['thumbnail_url']); ?>">
                <meta itemprop="uploadDate" content="<?php echo esc_attr($atts['upload_date']); ?>">
                <meta itemprop="duration" content="<?php echo esc_attr($atts['duration']); ?>">
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
