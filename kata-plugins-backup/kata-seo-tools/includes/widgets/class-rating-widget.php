<?php
/**
 * Widget: Kata Rating Widget
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_Rating_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kata_rating_widget',
            'Kata Rating',
            array(
                'description' => 'Display post/page rating in sidebar',
                'classname' => 'kata-rating-widget'
            )
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $post_id = get_the_ID();
        $show_form = !empty($instance['show_form']) ? true : false;
        
        // Generate rating display
        if (class_exists('Kata_Rating_Handler')) {
            $rating_handler = new Kata_Rating_Handler();
            echo $rating_handler->render(array(
                'post_id' => $post_id,
                'show_form' => $show_form ? 'true' : 'false'
            ));
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Rate This';
        $show_form = !empty($instance['show_form']) ? $instance['show_form'] : true;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" 
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label>
                <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('show_form')); ?>" 
                       name="<?php echo esc_attr($this->get_field_name('show_form')); ?>" 
                       value="1" <?php checked($show_form, true); ?>>
                Show rating form
            </label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_form'] = (!empty($new_instance['show_form'])) ? true : false;
        return $instance;
    }
}

// Register widget
function kata_register_rating_widget() {
    register_widget('Kata_Rating_Widget');
}
add_action('widgets_init', 'kata_register_rating_widget');
