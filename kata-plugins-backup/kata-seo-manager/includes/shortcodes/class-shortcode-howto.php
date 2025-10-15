<?php
/**
 * HowTo Shortcode
 * 
 * [kata_howto]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_HowTo extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_howto';
    protected $schema_type = 'howto';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'image' => '',
            'total_time' => '',
            'steps' => '',
            'tools' => '',
            'supplies' => '',
            'show_output' => 'true'
        ));
        
        if (empty($atts['name']) || empty($atts['steps'])) {
            $this->log_error('HowTo name and steps are required');
            return '';
        }
        
        // Build steps
        $steps = array_filter(array_map('trim', explode('|', $atts['steps'])));
        $step_items = array();
        foreach ($steps as $index => $step) {
            $step_items[] = array(
                '@type' => 'HowToStep',
                'name' => 'Step ' . ($index + 1),
                'text' => $step
            );
        }
        
        // Build schema data
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'image' => $atts['image'],
            'totalTime' => $atts['total_time'],
            'step' => $step_items
        );
        
        if (!empty($atts['tools'])) {
            $data['tool'] = array_filter(array_map('trim', explode('|', $atts['tools'])));
        }
        
        if (!empty($atts['supplies'])) {
            $data['supply'] = array_filter(array_map('trim', explode('|', $atts['supplies'])));
        }
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <div class="kata-howto" itemscope itemtype="https://schema.org/HowTo">
                <h3 itemprop="name"><?php echo esc_html($atts['name']); ?></h3>
                <p itemprop="description"><?php echo esc_html($atts['description']); ?></p>
                <ol>
                    <?php foreach ($step_items as $step): ?>
                        <li itemprop="step" itemscope itemtype="https://schema.org/HowToStep">
                            <span itemprop="text"><?php echo esc_html($step['text']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
