<?php
/**
 * Event Shortcode
 * 
 * [kata_event]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Event extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_event';
    protected $schema_type = 'event';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'start_date' => '',
            'end_date' => '',
            'location_name' => '',
            'location_address' => '',
            'description' => '',
            'image' => '',
            'performer' => '',
            'price' => '',
            'currency' => 'USD',
            'url' => '',
            'show_output' => 'true'
        ));
        
        if (empty($atts['name']) || empty($atts['start_date'])) {
            $this->log_error('Event name and start date are required');
            return '';
        }
        
        // Build schema data
        $data = array(
            'name' => $atts['name'],
            'startDate' => $atts['start_date'],
            'endDate' => $atts['end_date'],
            'description' => $atts['description'],
            'image' => $atts['image'],
            'location' => array(
                '@type' => 'Place',
                'name' => $atts['location_name'],
                'address' => $atts['location_address']
            ),
            'performer' => array(
                '@type' => 'Person',
                'name' => $atts['performer']
            )
        );
        
        if (!empty($atts['price'])) {
            $data['offers'] = array(
                '@type' => 'Offer',
                'price' => $atts['price'],
                'priceCurrency' => $atts['currency'],
                'url' => $atts['url']
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
            <div class="kata-event" itemscope itemtype="https://schema.org/Event">
                <h3 itemprop="name"><?php echo esc_html($atts['name']); ?></h3>
                <p itemprop="description"><?php echo esc_html($atts['description']); ?></p>
                <div class="kata-event-details">
                    <p><strong><?php _e('When:', 'kata-seo-manager'); ?></strong> 
                        <time itemprop="startDate" datetime="<?php echo esc_attr($atts['start_date']); ?>">
                            <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($atts['start_date']))); ?>
                        </time>
                    </p>
                    <p><strong><?php _e('Where:', 'kata-seo-manager'); ?></strong> 
                        <span itemprop="location" itemscope itemtype="https://schema.org/Place">
                            <span itemprop="name"><?php echo esc_html($atts['location_name']); ?></span>
                        </span>
                    </p>
                    <?php if (!empty($atts['performer'])): ?>
                        <p><strong><?php _e('Performer:', 'kata-seo-manager'); ?></strong> 
                            <span itemprop="performer" itemscope itemtype="https://schema.org/Person">
                                <span itemprop="name"><?php echo esc_html($atts['performer']); ?></span>
                            </span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
