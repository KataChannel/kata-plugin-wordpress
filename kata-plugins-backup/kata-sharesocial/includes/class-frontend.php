<?php
/**
 * Frontend Display Handler
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend Class
 */
class KataShareSocial_Frontend {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Platforms instance
     */
    private $platforms;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->platforms = KataShareSocial_Platforms::get_instance();
        $this->add_hooks();
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // Auto add share buttons to content
        if (get_option('kata_sharesocial_auto_add_buttons', true)) {
            add_filter('the_content', array($this, 'add_share_buttons_to_content'));
        }
        
        // Shortcode support
        add_shortcode('kata_share_buttons', array($this, 'shortcode_share_buttons'));
        
        // Widget support
        add_action('widgets_init', array($this, 'register_widget'));
    }
    
    /**
     * Add share buttons to content automatically
     */
    public function add_share_buttons_to_content($content) {
        // Only add to single posts/pages
        if (!is_single() && !is_page()) {
            return $content;
        }
        
        // Check if current post type is excluded
        $excluded_types = get_option('kata_sharesocial_exclude_post_types', array());
        if (in_array(get_post_type(), $excluded_types)) {
            return $content;
        }
        
        $position = get_option('kata_sharesocial_button_position', 'bottom');
        $share_buttons = $this->render_share_buttons();
        
        switch ($position) {
            case 'top':
                return $share_buttons . $content;
            case 'both':
                return $share_buttons . $content . $share_buttons;
            case 'bottom':
            default:
                return $content . $share_buttons;
        }
    }
    
    /**
     * Shortcode handler
     */
    public function shortcode_share_buttons($atts) {
        $atts = shortcode_atts(array(
            'platforms' => '',
            'style' => '',
            'size' => '',
            'show_count' => '',
            'post_id' => get_the_ID()
        ), $atts);
        
        return $this->render_share_buttons($atts);
    }
    
    /**
     * Render share buttons
     */
    public function render_share_buttons($args = array()) {
        // Default arguments
        $defaults = array(
            'platforms' => get_option('kata_sharesocial_enabled_platforms', array()),
            'style' => get_option('kata_sharesocial_button_style', 'rounded'),
            'size' => get_option('kata_sharesocial_button_size', 'medium'),
            'show_count' => get_option('kata_sharesocial_show_count', true),
            'post_id' => get_the_ID()
        );
        
        $args = wp_parse_args($args, $defaults);
        
        // Convert comma-separated platforms to array
        if (is_string($args['platforms'])) {
            $args['platforms'] = explode(',', $args['platforms']);
            $args['platforms'] = array_map('trim', $args['platforms']);
        }
        
        // Get enabled platforms
        $enabled_platforms = $this->platforms->get_enabled_platforms();
        
        // Filter platforms if specified
        if (!empty($args['platforms'])) {
            $enabled_platforms = array_intersect_key($enabled_platforms, array_flip($args['platforms']));
        }
        
        if (empty($enabled_platforms)) {
            return '';
        }
        
        // Start output buffering
        ob_start();
        
        // Extract variables for template
        $post_id = $args['post_id'];
        $style = $args['style'];
        $size = $args['size'];
        $show_count = $args['show_count'];
        $platforms = $this->platforms;
        
        // Load template
        $template_file = KATA_SHARESOCIAL_PLUGIN_PATH . 'templates/share-buttons.php';
        if (file_exists($template_file)) {
            include $template_file;
        } else {
            $this->render_default_buttons($enabled_platforms, $args);
        }
        
        return ob_get_clean();
    }
    
    /**
     * Render default buttons (fallback)
     */
    private function render_default_buttons($platforms, $args) {
        $post_id = $args['post_id'];
        $style = $args['style'];
        $size = $args['size'];
        $show_count = $args['show_count'];
        
        echo '<div class="kata-share-buttons kata-style-' . esc_attr($style) . ' kata-size-' . esc_attr($size) . '" data-post-id="' . esc_attr($post_id) . '">';
        
        if ($show_count) {
            $total_shares = $this->platforms->get_share_count($post_id);
            if ($total_shares > 0) {
                echo '<div class="kata-share-total">';
                echo '<span class="kata-share-count">' . esc_html($total_shares) . '</span>';
                echo '<span class="kata-share-label">' . esc_html__('Shares', 'kata-sharesocial') . '</span>';
                echo '</div>';
            }
        }
        
        echo '<div class="kata-share-buttons-list">';
        
        foreach ($platforms as $platform_id => $platform) {
            $share_url = $this->platforms->generate_share_url($platform_id, $post_id);
            $platform_count = $show_count ? $this->platforms->get_share_count($post_id, $platform_id) : 0;
            
            echo '<a href="' . esc_url($share_url) . '" ';
            echo 'class="kata-share-button kata-platform-' . esc_attr($platform_id) . '" ';
            echo 'data-platform="' . esc_attr($platform_id) . '" ';
            echo 'data-popup-width="' . esc_attr($platform['popup_width']) . '" ';
            echo 'data-popup-height="' . esc_attr($platform['popup_height']) . '" ';
            echo 'style="background-color: ' . esc_attr($platform['color']) . ';" ';
            echo 'title="' . esc_attr(sprintf(__('Share on %s', 'kata-sharesocial'), $platform['name'])) . '">';
            
            echo '<i class="' . esc_attr($platform['icon']) . '"></i>';
            echo '<span class="kata-platform-name">' . esc_html($platform['name']) . '</span>';
            
            if ($show_count && $platform_count > 0) {
                echo '<span class="kata-platform-count">' . esc_html($platform_count) . '</span>';
            }
            
            echo '</a>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Register widget
     */
    public function register_widget() {
        register_widget('KataShareSocial_Widget');
    }
    
    /**
     * Get share button HTML for specific post
     */
    public function get_share_buttons($post_id = null, $args = array()) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $args['post_id'] = $post_id;
        return $this->render_share_buttons($args);
    }
}

/**
 * Widget Class
 */
class KataShareSocial_Widget extends WP_Widget {
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'kata_sharesocial_widget',
            __('Kata Share Buttons', 'kata-sharesocial'),
            array(
                'description' => __('Display social share buttons in sidebar', 'kata-sharesocial')
            )
        );
    }
    
    /**
     * Widget output
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        // Only show on single posts/pages
        if (is_single() || is_page()) {
            $frontend = KataShareSocial_Frontend::get_instance();
            echo $frontend->render_share_buttons(array(
                'style' => $instance['style'] ?? 'rounded',
                'size' => $instance['size'] ?? 'small',
                'show_count' => $instance['show_count'] ?? false
            ));
        }
        
        echo $args['after_widget'];
    }
    
    /**
     * Widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Share This', 'kata-sharesocial');
        $style = !empty($instance['style']) ? $instance['style'] : 'rounded';
        $size = !empty($instance['size']) ? $instance['size'] : 'small';
        $show_count = !empty($instance['show_count']) ? $instance['show_count'] : false;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'kata-sharesocial'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_attr_e('Style:', 'kata-sharesocial'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>">
                <option value="rounded" <?php selected($style, 'rounded'); ?>><?php esc_html_e('Rounded', 'kata-sharesocial'); ?></option>
                <option value="square" <?php selected($style, 'square'); ?>><?php esc_html_e('Square', 'kata-sharesocial'); ?></option>
                <option value="circle" <?php selected($style, 'circle'); ?>><?php esc_html_e('Circle', 'kata-sharesocial'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('size')); ?>"><?php esc_attr_e('Size:', 'kata-sharesocial'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('size')); ?>" name="<?php echo esc_attr($this->get_field_name('size')); ?>">
                <option value="small" <?php selected($size, 'small'); ?>><?php esc_html_e('Small', 'kata-sharesocial'); ?></option>
                <option value="medium" <?php selected($size, 'medium'); ?>><?php esc_html_e('Medium', 'kata-sharesocial'); ?></option>
                <option value="large" <?php selected($size, 'large'); ?>><?php esc_html_e('Large', 'kata-sharesocial'); ?></option>
            </select>
        </p>
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_count); ?> id="<?php echo esc_attr($this->get_field_id('show_count')); ?>" name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" />
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><?php esc_attr_e('Show share count', 'kata-sharesocial'); ?></label>
        </p>
        <?php
    }
    
    /**
     * Update widget
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['style'] = (!empty($new_instance['style'])) ? sanitize_text_field($new_instance['style']) : 'rounded';
        $instance['size'] = (!empty($new_instance['size'])) ? sanitize_text_field($new_instance['size']) : 'small';
        $instance['show_count'] = (!empty($new_instance['show_count'])) ? 1 : 0;
        
        return $instance;
    }
}
