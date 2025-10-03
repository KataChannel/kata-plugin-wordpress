<?php
/**
 * Widget: Kata CTA Widget
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_CTA_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kata_cta_widget',
            'Kata Call-to-Action',
            array(
                'description' => 'Display a call-to-action box in sidebar',
                'classname' => 'kata-cta-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['cta_title']) ? $instance['cta_title'] : '';
        $description = !empty($instance['description']) ? $instance['description'] : '';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Learn More';
        $button_url = !empty($instance['button_url']) ? $instance['button_url'] : '#';
        $style = !empty($instance['style']) ? $instance['style'] : 'box';
        
        // Generate CTA
        if (class_exists('Kata_CTA_Handler')) {
            $cta_handler = new Kata_CTA_Handler();
            echo $cta_handler->render(array(
                'title' => $title,
                'description' => $description,
                'button_text' => $button_text,
                'button_url' => $button_url,
                'style' => $style
            ));
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $cta_title = !empty($instance['cta_title']) ? $instance['cta_title'] : 'Get Started Today!';
        $description = !empty($instance['description']) ? $instance['description'] : 'Join thousands of happy customers';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Learn More';
        $button_url = !empty($instance['button_url']) ? $instance['button_url'] : '#';
        $style = !empty($instance['style']) ? $instance['style'] : 'box';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('cta_title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('cta_title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('cta_title')); ?>" 
                   type="text" value="<?php echo esc_attr($cta_title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('description')); ?>">Description:</label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('description')); ?>" 
                      name="<?php echo esc_attr($this->get_field_name('description')); ?>" 
                      rows="3"><?php echo esc_textarea($description); ?></textarea>
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>">Button Text:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" 
                   type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_url')); ?>">Button URL:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_url')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('button_url')); ?>" 
                   type="url" value="<?php echo esc_attr($button_url); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>">Style:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="box" <?php selected($style, 'box'); ?>>Box</option>
                <option value="banner" <?php selected($style, 'banner'); ?>>Banner</option>
                <option value="card" <?php selected($style, 'card'); ?>>Card</option>
            </select>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['cta_title'] = (!empty($new_instance['cta_title'])) ? sanitize_text_field($new_instance['cta_title']) : '';
        $instance['description'] = (!empty($new_instance['description'])) ? sanitize_textarea_field($new_instance['description']) : '';
        $instance['button_text'] = (!empty($new_instance['button_text'])) ? sanitize_text_field($new_instance['button_text']) : '';
        $instance['button_url'] = (!empty($new_instance['button_url'])) ? esc_url_raw($new_instance['button_url']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? sanitize_text_field($new_instance['style']) : 'box';
        return $instance;
    }
}

// Register widget
function kata_register_cta_widget() {
    register_widget('Kata_CTA_Widget');
}
add_action('widgets_init', 'kata_register_cta_widget');
