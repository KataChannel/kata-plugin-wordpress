<?php
/**
 * Breadcrumb Shortcode
 * 
 * [kata_breadcrumb]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Breadcrumb extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_breadcrumb';
    protected $schema_type = 'breadcrumb';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'items' => '',
            'urls' => '',
            'show_output' => 'true'
        ));
        
        $items = array_filter(array_map('trim', explode('|', $atts['items'])));
        $urls = array_filter(array_map('trim', explode('|', $atts['urls'])));
        
        if (empty($items)) {
            $this->log_error('Breadcrumb items are required');
            return '';
        }
        
        $breadcrumb_items = array();
        foreach ($items as $index => $item) {
            $breadcrumb_items[] = array(
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item,
                'item' => isset($urls[$index]) ? $urls[$index] : ''
            );
        }
        
        $data = array('itemListElement' => $breadcrumb_items);
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <nav class="kata-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
                <?php foreach ($breadcrumb_items as $item): ?>
                    <span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <?php if (!empty($item['item'])): ?>
                            <a itemprop="item" href="<?php echo esc_url($item['item']); ?>">
                                <span itemprop="name"><?php echo esc_html($item['name']); ?></span>
                            </a>
                        <?php else: ?>
                            <span itemprop="name"><?php echo esc_html($item['name']); ?></span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?php echo esc_attr($item['position']); ?>">
                    </span>
                    <?php if ($item['position'] < count($breadcrumb_items)): ?>
                        <span class="separator"> › </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
