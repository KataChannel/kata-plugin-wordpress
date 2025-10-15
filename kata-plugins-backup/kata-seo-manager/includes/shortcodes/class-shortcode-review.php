<?php
/**
 * Review Shortcode
 * 
 * [kata_review]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Review extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_review';
    protected $schema_type = 'review';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'item_name' => '',
            'rating_value' => '',
            'best_rating' => '5',
            'author' => '',
            'review_body' => '',
            'date_published' => '',
            'show_output' => 'true'
        ));
        
        if (empty($atts['item_name']) || empty($atts['rating_value'])) {
            $this->log_error('Item name and rating are required');
            return '';
        }
        
        $data = array(
            'itemReviewed' => array(
                '@type' => 'Thing',
                'name' => $atts['item_name']
            ),
            'reviewRating' => array(
                '@type' => 'Rating',
                'ratingValue' => $atts['rating_value'],
                'bestRating' => $atts['best_rating']
            ),
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            ),
            'reviewBody' => $atts['review_body'],
            'datePublished' => $atts['date_published']
        );
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <div class="kata-review" itemscope itemtype="https://schema.org/Review">
                <div itemprop="reviewRating" itemscope itemtype="https://schema.org/Rating">
                    <span itemprop="ratingValue"><?php echo esc_html($atts['rating_value']); ?></span>/
                    <span itemprop="bestRating"><?php echo esc_html($atts['best_rating']); ?></span>
                </div>
                <p itemprop="reviewBody"><?php echo esc_html($atts['review_body']); ?></p>
                <span itemprop="author" itemscope itemtype="https://schema.org/Person">
                    <span itemprop="name"><?php echo esc_html($atts['author']); ?></span>
                </span>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
