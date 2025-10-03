<?php
/**
 * Quote Generator Handler
 * 
 * Generates beautiful quotes with hashtags and social sharing
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_Quote_Generator {
    
    /**
     * Render quote shortcode
     * 
     * @param array $atts Shortcode attributes
     * @param string $content Quote content
     * @return string HTML output
     */
    public function render($atts, $content = null) {
        $atts = shortcode_atts(array(
            'style' => 'modern',
            'author' => '',
            'hashtags' => '',
            'bg_color' => '',
            'text_color' => '',
            'share' => 'no',
            'animation' => 'fade-in'
        ), $atts);
        
        $class = 'kata-quote kata-quote-' . esc_attr($atts['style']);
        $class .= ' kata-quote-' . esc_attr($atts['animation']);
        
        $style_attr = '';
        if (!empty($atts['bg_color'])) {
            $style_attr .= 'background-color:' . esc_attr($atts['bg_color']) . ';';
        }
        if (!empty($atts['text_color'])) {
            $style_attr .= 'color:' . esc_attr($atts['text_color']) . ';';
        }
        
        ob_start();
        ?>
        <div class="<?php echo $class; ?>" style="<?php echo $style_attr; ?>" data-aos="<?php echo esc_attr($atts['animation']); ?>">
            <div class="kata-quote-icon">&ldquo;</div>
            
            <div class="kata-quote-content"><?php echo wp_kses_post($content); ?></div>
            
            <?php if (!empty($atts['author'])) : ?>
                <div class="kata-quote-author">— <?php echo esc_html($atts['author']); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($atts['hashtags'])) : 
                $hashtags = array_map('trim', explode(' ', $atts['hashtags']));
            ?>
                <div class="kata-quote-hashtags">
                    <?php foreach ($hashtags as $tag) : 
                        if (strpos($tag, '#') === 0) :
                            $tag_clean = substr($tag, 1);
                            $tag_url = 'https://twitter.com/hashtag/' . urlencode($tag_clean);
                    ?>
                        <a href="<?php echo esc_url($tag_url); ?>" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="kata-hashtag">
                            <?php echo esc_html($tag); ?>
                        </a>
                    <?php 
                        endif;
                    endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['share'] === 'yes') : 
                $share_text = strip_tags($content);
                if (!empty($atts['author'])) {
                    $share_text .= ' — ' . $atts['author'];
                }
                if (!empty($atts['hashtags'])) {
                    $share_text .= ' ' . $atts['hashtags'];
                }
                
                $twitter_url = 'https://twitter.com/intent/tweet?text=' . urlencode($share_text);
                $facebook_url = 'https://www.facebook.com/sharer/sharer.php?quote=' . urlencode($share_text);
            ?>
                <div class="kata-quote-share">
                    <button class="kata-quote-share-btn kata-share-twitter" 
                            onclick="window.open('<?php echo esc_js($twitter_url); ?>', '_blank', 'width=600,height=400')">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                        </svg>
                        Tweet
                    </button>
                    
                    <button class="kata-quote-share-btn kata-share-facebook" 
                            onclick="window.open('<?php echo esc_js($facebook_url); ?>', '_blank', 'width=600,height=400')">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                        </svg>
                        Share
                    </button>
                    
                    <button class="kata-quote-share-btn kata-share-copy" 
                            onclick="kataSEO.copyQuote(this)" 
                            data-quote="<?php echo esc_attr($share_text); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                            <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                        </svg>
                        Copy
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get random quote from database
     * 
     * @param string $category Quote category
     * @return array Quote data
     */
    public function get_random_quote($category = '') {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_quotes';
        
        if ($category) {
            $quote = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table WHERE category = %s ORDER BY RAND() LIMIT 1",
                $category
            ), ARRAY_A);
        } else {
            $quote = $wpdb->get_row(
                "SELECT * FROM $table ORDER BY RAND() LIMIT 1",
                ARRAY_A
            );
        }
        
        return $quote;
    }
    
    /**
     * Save custom quote
     * 
     * @param array $data Quote data
     * @return int|false Insert ID or false on failure
     */
    public function save_quote($data) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_quotes';
        
        $insert_data = array(
            'content' => sanitize_textarea_field($data['content']),
            'author' => sanitize_text_field($data['author']),
            'category' => sanitize_text_field($data['category']),
            'hashtags' => sanitize_text_field($data['hashtags'])
        );
        
        $result = $wpdb->insert($table, $insert_data);
        
        if ($result) {
            return $wpdb->insert_id;
        }
        
        return false;
    }
    
    /**
     * Generate quote image for social sharing
     * 
     * @param string $quote Quote text
     * @param string $author Author name
     * @return string Image URL
     */
    public function generate_quote_image($quote, $author = '') {
        // This would integrate with image generation service
        // For now, return placeholder
        $params = array(
            'text' => urlencode($quote),
            'author' => urlencode($author),
            'width' => 1200,
            'height' => 630,
            'bg' => 'gradient',
            'color' => 'white'
        );
        
        // Example using external service or generate locally with GD/ImageMagick
        return add_query_arg($params, home_url('/quote-image/'));
    }
}
