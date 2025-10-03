<?php
/**
 * FAQ Handler
 * 
 * Handles dynamic FAQ with Schema.org FAQPage markup
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_FAQ_Handler {
    
    /**
     * Render FAQ shortcode
     * 
     * @param array $atts Shortcode attributes
     * @param string $content FAQ items
     * @return string HTML output
     */
    public function render($atts, $content = null) {
        $atts = shortcode_atts(array(
            'style' => 'accordion',
            'show_schema' => 'yes',
            'open_first' => 'no',
            'icon' => 'plus'
        ), $atts);
        
        // Get FAQ items from meta or content
        $post_id = get_the_ID();
        $faq_items = get_post_meta($post_id, '_kata_seo_faq_items', true);
        
        if (empty($faq_items)) {
            return '<div class="kata-faq-error">No FAQ items found.</div>';
        }
        
        ob_start();
        ?>
        <div class="kata-faq kata-faq-<?php echo esc_attr($atts['style']); ?>" 
             data-style="<?php echo esc_attr($atts['style']); ?>">
            
            <?php if ($atts['style'] === 'accordion') : ?>
                <div class="kata-faq-accordion">
                    <?php foreach ($faq_items as $index => $item) : 
                        $is_open = ($atts['open_first'] === 'yes' && $index === 0);
                    ?>
                        <div class="kata-faq-item <?php echo $is_open ? 'active' : ''; ?>">
                            <div class="kata-faq-question" onclick="kataSEO.toggleFAQ(this)">
                                <span class="faq-question-text"><?php echo esc_html($item['question']); ?></span>
                                <span class="faq-icon">
                                    <?php if ($atts['icon'] === 'plus') : ?>
                                        <svg class="icon-plus" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                                        </svg>
                                        <svg class="icon-minus" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <path d="M19,13H5V11H19V13Z"/>
                                        </svg>
                                    <?php else : ?>
                                        <svg class="icon-chevron" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                                            <path d="M7.41,8.58L12,13.17L16.59,8.58L18,10L12,16L6,10L7.41,8.58Z"/>
                                        </svg>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="kata-faq-answer" style="<?php echo $is_open ? 'display:block;' : ''; ?>">
                                <?php echo wp_kses_post($item['answer']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
            <?php elseif ($atts['style'] === 'tabs') : ?>
                <div class="kata-faq-tabs">
                    <div class="faq-tabs-nav">
                        <?php foreach ($faq_items as $index => $item) : ?>
                            <button class="faq-tab-btn <?php echo $index === 0 ? 'active' : ''; ?>"
                                    data-tab="faq-tab-<?php echo $index; ?>"
                                    onclick="kataSEO.switchFAQTab(this)">
                                <?php echo esc_html($item['question']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="faq-tabs-content">
                        <?php foreach ($faq_items as $index => $item) : ?>
                            <div id="faq-tab-<?php echo $index; ?>" 
                                 class="faq-tab-panel <?php echo $index === 0 ? 'active' : ''; ?>">
                                <?php echo wp_kses_post($item['answer']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
            <?php else : // list style ?>
                <div class="kata-faq-list">
                    <?php foreach ($faq_items as $item) : ?>
                        <div class="kata-faq-item">
                            <h4 class="kata-faq-question"><?php echo esc_html($item['question']); ?></h4>
                            <div class="kata-faq-answer"><?php echo wp_kses_post($item['answer']); ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($atts['show_schema'] === 'yes') : ?>
                <script type="application/ld+json">
                <?php echo wp_json_encode($this->generate_faq_schema($faq_items), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>
                </script>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Generate FAQ Schema markup
     * 
     * @param array $faq_items FAQ items
     * @return array Schema data
     */
    public function generate_faq_schema($faq_items) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );
        
        foreach ($faq_items as $item) {
            $schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $item['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => strip_tags($item['answer'])
                )
            );
        }
        
        return $schema;
    }
    
    /**
     * Save FAQ items from meta box
     * 
     * @param int $post_id Post ID
     * @param array $faq_items FAQ items
     * @return bool Success
     */
    public function save_faq_items($post_id, $faq_items) {
        if (!is_array($faq_items)) {
            return false;
        }
        
        $sanitized = array();
        foreach ($faq_items as $item) {
            if (!empty($item['question']) && !empty($item['answer'])) {
                $sanitized[] = array(
                    'question' => sanitize_text_field($item['question']),
                    'answer' => wp_kses_post($item['answer'])
                );
            }
        }
        
        return update_post_meta($post_id, '_kata_seo_faq_items', $sanitized);
    }
    
    /**
     * Add FAQ to post
     * 
     * @param int $post_id Post ID
     * @param string $question Question
     * @param string $answer Answer
     * @return bool Success
     */
    public function add_faq_item($post_id, $question, $answer) {
        $faq_items = get_post_meta($post_id, '_kata_seo_faq_items', true);
        
        if (!is_array($faq_items)) {
            $faq_items = array();
        }
        
        $faq_items[] = array(
            'question' => sanitize_text_field($question),
            'answer' => wp_kses_post($answer)
        );
        
        return update_post_meta($post_id, '_kata_seo_faq_items', $faq_items);
    }
    
    /**
     * Get FAQ items for a post
     * 
     * @param int $post_id Post ID
     * @return array FAQ items
     */
    public function get_faq_items($post_id) {
        $items = get_post_meta($post_id, '_kata_seo_faq_items', true);
        return is_array($items) ? $items : array();
    }
}
