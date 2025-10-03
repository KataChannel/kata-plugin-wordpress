<?php
/**
 * Widget: Kata Social Share Widget
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_Social_Share_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kata_social_share_widget',
            'Kata Social Share',
            array(
                'description' => 'Display social sharing buttons in sidebar',
                'classname' => 'kata-social-share-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $style = !empty($instance['style']) ? $instance['style'] : 'default';
        $platforms = !empty($instance['platforms']) ? $instance['platforms'] : array('facebook', 'twitter', 'linkedin');
        
        // Generate social share buttons
        if (class_exists('Kata_Social_Share')) {
            $social_share = new Kata_Social_Share();
            echo $social_share->render(array(
                'style' => $style,
                'platforms' => implode(',', $platforms),
                'title' => get_the_title(),
                'url' => get_permalink()
            ));
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Share This';
        $style = !empty($instance['style']) ? $instance['style'] : 'default';
        $platforms = !empty($instance['platforms']) ? $instance['platforms'] : array('facebook', 'twitter', 'linkedin');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>">Style:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="default" <?php selected($style, 'default'); ?>>Default</option>
                <option value="circle" <?php selected($style, 'circle'); ?>>Circle</option>
                <option value="square" <?php selected($style, 'square'); ?>>Square</option>
                <option value="minimal" <?php selected($style, 'minimal'); ?>>Minimal</option>
            </select>
        </p>
        
        <p>
            <label>Platforms:</label><br>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('platforms')); ?>[]" 
                       value="facebook" <?php checked(in_array('facebook', $platforms)); ?>>
                Facebook
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('platforms')); ?>[]" 
                       value="twitter" <?php checked(in_array('twitter', $platforms)); ?>>
                Twitter
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('platforms')); ?>[]" 
                       value="linkedin" <?php checked(in_array('linkedin', $platforms)); ?>>
                LinkedIn
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('platforms')); ?>[]" 
                       value="pinterest" <?php checked(in_array('pinterest', $platforms)); ?>>
                Pinterest
            </label><br>
            <label>
                <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('platforms')); ?>[]" 
                       value="whatsapp" <?php checked(in_array('whatsapp', $platforms)); ?>>
                WhatsApp
            </label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? sanitize_text_field($new_instance['style']) : 'default';
        $instance['platforms'] = (!empty($new_instance['platforms'])) ? array_map('sanitize_text_field', $new_instance['platforms']) : array();
        return $instance;
    }
}

// Register widget
function kata_register_social_share_widget() {
    register_widget('Kata_Social_Share_Widget');
}
add_action('widgets_init', 'kata_register_social_share_widget');
