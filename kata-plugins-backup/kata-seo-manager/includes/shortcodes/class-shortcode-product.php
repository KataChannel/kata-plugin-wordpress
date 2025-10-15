<?php
/**
 * Product Shortcode
 * 
 * [kata_product]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Product extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_product';
    protected $schema_type = 'product';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'image' => '',
            'brand' => '',
            'sku' => '',
            'price' => '',
            'currency' => 'USD',
            'availability' => 'InStock',
            'rating_value' => '',
            'rating_count' => '',
            'show_output' => 'true'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Product name is required');
            return '';
        }
        
        // Build schema data
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'image' => $atts['image'],
            'brand' => array(
                '@type' => 'Brand',
                'name' => $atts['brand']
            ),
            'sku' => $atts['sku'],
            'offers' => array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency'],
                'availability' => 'https://schema.org/' . $atts['availability']
            )
        );
        
        // Add rating if provided
        if (!empty($atts['rating_value'])) {
            $data['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'reviewCount' => $atts['rating_count']
            );
        }
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <div class="kata-product" itemscope itemtype="https://schema.org/Product">
                <?php if (!empty($atts['image'])): ?>
                    <img itemprop="image" src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
                <?php endif; ?>
                <h3 itemprop="name"><?php echo esc_html($atts['name']); ?></h3>
                <p itemprop="description"><?php echo esc_html($atts['description']); ?></p>
                <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <span class="price" itemprop="price" content="<?php echo esc_attr($atts['price']); ?>">
                        <?php echo esc_html($atts['currency'] . ' ' . $atts['price']); ?>
                    </span>
                    <meta itemprop="priceCurrency" content="<?php echo esc_attr($atts['currency']); ?>">
                    <link itemprop="availability" href="https://schema.org/<?php echo esc_attr($atts['availability']); ?>">
                </div>
                <?php if (!empty($atts['rating_value'])): ?>
                    <div itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                        <span itemprop="ratingValue"><?php echo esc_html($atts['rating_value']); ?></span>/5
                        (<span itemprop="reviewCount"><?php echo esc_html($atts['rating_count']); ?></span> reviews)
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
