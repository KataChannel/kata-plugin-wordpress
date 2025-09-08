<?php
/**
 * Frontend Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend Class
 */
class KataSchema_Frontend {
    
    /**
     * Schema generator instance
     */
    private $generator;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->generator = new KataSchema_Generator();
        $this->add_hooks();
    }
    
    /**
     * Add hooks
     */
    private function add_hooks() {
        // Add schema to head
        add_action('wp_head', array($this, 'output_schema'), 10);
        
        // Add schema shortcodes
        add_shortcode('kata_schema', array($this, 'schema_shortcode'));
        add_shortcode('kata_breadcrumb', array($this, 'breadcrumb_shortcode'));
        add_shortcode('kata_schema_breadcrumb', array($this, 'breadcrumb_shortcode'));
        
        // Filter for manual schema insertion
        add_filter('kata_schema_output', array($this, 'get_schema_output'));
        
        // AJAX handlers for frontend
        add_action('wp_ajax_kata_schema_preview', array($this, 'ajax_schema_preview'));
        add_action('wp_ajax_nopriv_kata_schema_preview', array($this, 'ajax_schema_preview'));
        
        // Register scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add body class for schema-enabled pages
        add_filter('body_class', array($this, 'add_body_class'));
    }
    
    /**
     * Output schema in head
     */
    public function output_schema() {
        // Check if schema output is enabled
        if (!get_option('kata_schema_output_enabled', true)) {
            return;
        }
        
        // Skip admin pages
        if (is_admin()) {
            return;
        }
        
        // Skip if doing AJAX
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }
        
        // Generate and output schema
        $schema_output = $this->generator->generate_schema_output();
        
        if (!empty($schema_output)) {
            echo $schema_output;
        }
    }
    
    /**
     * Schema shortcode
     * 
     * Usage: [kata_schema type="article" post_id="123"]
     */
    public function schema_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'article',
            'post_id' => get_the_ID(),
            'display' => 'json', // json, preview, debug
            'inline' => false
        ), $atts, 'kata_schema');
        
        $post_id = intval($atts['post_id']);
        $schema_type = sanitize_text_field($atts['type']);
        $display = sanitize_text_field($atts['display']);
        $inline = filter_var($atts['inline'], FILTER_VALIDATE_BOOLEAN);
        
        // Generate schema
        $schema = $this->generator->generate_schema($post_id, $schema_type);
        
        if (!$schema) {
            return '<!-- Kata Schema: No schema generated -->';
        }
        
        switch ($display) {
            case 'preview':
                return $this->render_schema_preview($schema);
                
            case 'debug':
                if (current_user_can('manage_options')) {
                    return $this->render_schema_debug($schema);
                }
                return '';
                
            case 'json':
            default:
                if ($inline) {
                    return '<span class="kata-schema-inline" data-schema="' . esc_attr(wp_json_encode($schema)) . '"></span>';
                } else {
                    $output = '<script type="application/ld+json">';
                    $output .= wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    $output .= '</script>';
                    return $output;
                }
        }
    }
    
    /**
     * Breadcrumb shortcode
     * 
     * Usage: [kata_breadcrumb separator=" > " show_home="true"]
     */
    public function breadcrumb_shortcode($atts) {
        $atts = shortcode_atts(array(
            'separator' => ' &raquo; ',
            'show_home' => true,
            'home_text' => __('Home', 'kata-schema-markup'),
            'class' => 'kata-breadcrumb',
            'schema' => true
        ), $atts, 'kata_breadcrumb');
        
        $show_home = filter_var($atts['show_home'], FILTER_VALIDATE_BOOLEAN);
        $include_schema = filter_var($atts['schema'], FILTER_VALIDATE_BOOLEAN);
        
        $breadcrumbs = $this->generate_breadcrumb_html($atts);
        
        if ($include_schema) {
            $schema = $this->generator->generate_schema(get_the_ID(), 'breadcrumb');
            if ($schema) {
                $breadcrumbs .= '<script type="application/ld+json">';
                $breadcrumbs .= wp_json_encode($schema, JSON_UNESCAPED_SLASHES);
                $breadcrumbs .= '</script>';
            }
        }
        
        return $breadcrumbs;
    }
    
    /**
     * Generate breadcrumb HTML
     */
    private function generate_breadcrumb_html($args) {
        $items = array();
        
        // Home
        if ($args['show_home']) {
            $items[] = sprintf(
                '<a href="%s" class="breadcrumb-home">%s</a>',
                esc_url(home_url('/')),
                esc_html($args['home_text'])
            );
        }
        
        // Current page context
        if (is_single() || is_page()) {
            $post = get_queried_object();
            
            // Add category for posts
            if ($post->post_type === 'post' && is_single()) {
                $categories = get_the_category($post->ID);
                if (!empty($categories)) {
                    $category = $categories[0];
                    $items[] = sprintf(
                        '<a href="%s" class="breadcrumb-category">%s</a>',
                        esc_url(get_category_link($category->term_id)),
                        esc_html($category->name)
                    );
                }
            }
            
            // Add parent pages
            if ($post->post_parent) {
                $parents = array();
                $parent_id = $post->post_parent;
                
                while ($parent_id) {
                    $parent = get_post($parent_id);
                    $parents[] = $parent;
                    $parent_id = $parent->post_parent;
                }
                
                $parents = array_reverse($parents);
                
                foreach ($parents as $parent) {
                    $items[] = sprintf(
                        '<a href="%s" class="breadcrumb-parent">%s</a>',
                        esc_url(get_permalink($parent->ID)),
                        esc_html($parent->post_title)
                    );
                }
            }
            
            // Current page
            $items[] = sprintf(
                '<span class="breadcrumb-current">%s</span>',
                esc_html($post->post_title)
            );
            
        } elseif (is_category()) {
            $category = get_queried_object();
            $items[] = sprintf(
                '<span class="breadcrumb-current">%s</span>',
                esc_html($category->name)
            );
            
        } elseif (is_tag()) {
            $tag = get_queried_object();
            $items[] = sprintf(
                '<span class="breadcrumb-current">%s</span>',
                esc_html($tag->name)
            );
            
        } elseif (is_archive()) {
            $items[] = sprintf(
                '<span class="breadcrumb-current">%s</span>',
                esc_html(get_the_archive_title())
            );
            
        } elseif (is_search()) {
            $items[] = sprintf(
                '<span class="breadcrumb-current">%s "%s"</span>',
                esc_html__('Search Results for', 'kata-schema-markup'),
                esc_html(get_search_query())
            );
            
        } elseif (is_404()) {
            $items[] = sprintf(
                '<span class="breadcrumb-current">%s</span>',
                esc_html__('Page Not Found', 'kata-schema-markup')
            );
        }
        
        if (empty($items)) {
            return '';
        }
        
        $output = sprintf(
            '<nav class="%s" aria-label="%s">',
            esc_attr($args['class']),
            esc_attr__('Breadcrumb navigation', 'kata-schema-markup')
        );
        
        $output .= implode(wp_kses_post($args['separator']), $items);
        $output .= '</nav>';
        
        return $output;
    }
    
    /**
     * Render schema preview
     */
    private function render_schema_preview($schema) {
        if (!current_user_can('edit_posts')) {
            return '';
        }
        
        $output = '<div class="kata-schema-preview">';
        $output .= '<h4>' . __('Schema Preview', 'kata-schema-markup') . '</h4>';
        
        if (isset($schema['@type'])) {
            $output .= '<p><strong>' . __('Type:', 'kata-schema-markup') . '</strong> ' . esc_html($schema['@type']) . '</p>';
        }
        
        if (isset($schema['name'])) {
            $output .= '<p><strong>' . __('Name:', 'kata-schema-markup') . '</strong> ' . esc_html($schema['name']) . '</p>';
        }
        
        if (isset($schema['headline'])) {
            $output .= '<p><strong>' . __('Headline:', 'kata-schema-markup') . '</strong> ' . esc_html($schema['headline']) . '</p>';
        }
        
        if (isset($schema['description'])) {
            $description = wp_trim_words($schema['description'], 20);
            $output .= '<p><strong>' . __('Description:', 'kata-schema-markup') . '</strong> ' . esc_html($description) . '</p>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render schema debug
     */
    private function render_schema_debug($schema) {
        $output = '<div class="kata-schema-debug">';
        $output .= '<h4>' . __('Schema Debug', 'kata-schema-markup') . '</h4>';
        $output .= '<pre style="background: #f5f5f5; padding: 10px; overflow: auto; font-size: 12px;">';
        $output .= esc_html(wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $output .= '</pre>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Get schema output for manual insertion
     */
    public function get_schema_output($post_id = null, $schema_type = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        if ($schema_type) {
            return $this->generator->generate_schema($post_id, $schema_type);
        } else {
            return $this->generator->generate_schema_output();
        }
    }
    
    /**
     * AJAX schema preview
     */
    public function ajax_schema_preview() {
        if (!current_user_can('edit_posts')) {
            wp_die(__('Insufficient permissions', 'kata-schema-markup'));
        }
        
        check_ajax_referer('kata_schema_nonce', 'nonce');
        
        $post_id = intval($_POST['post_id']);
        $schema_type = sanitize_text_field($_POST['schema_type']);
        
        $schema = $this->generator->generate_schema($post_id, $schema_type);
        
        if ($schema) {
            wp_send_json_success(array(
                'html' => $this->render_schema_preview($schema),
                'json' => wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('Failed to generate schema preview', 'kata-schema-markup')
            ));
        }
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Only on frontend
        if (is_admin()) {
            return;
        }
        
        // Enqueue breadcrumb styles if breadcrumbs are used
        if (get_option('kata_schema_breadcrumb_enabled', false)) {
            wp_enqueue_style(
                'kata-schema-breadcrumb',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/breadcrumb.css',
                array(),
                KATA_SCHEMA_VERSION
            );
        }
        
        // Enqueue frontend script for interactive features
        if (get_option('kata_schema_frontend_features', false)) {
            wp_enqueue_script(
                'kata-schema-frontend',
                plugin_dir_url(dirname(__FILE__)) . 'assets/js/frontend.js',
                array('jquery'),
                KATA_SCHEMA_VERSION,
                true
            );
            
            wp_localize_script('kata-schema-frontend', 'kataSchema', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kata_schema_nonce'),
                'i18n' => array(
                    'loading' => __('Loading...', 'kata-schema-markup'),
                    'error' => __('An error occurred', 'kata-schema-markup')
                )
            ));
        }
    }
    
    /**
     * Add body class for schema-enabled pages
     */
    public function add_body_class($classes) {
        if (get_option('kata_schema_output_enabled', true)) {
            $classes[] = 'kata-schema-enabled';
            
            // Add specific schema type classes
            $enabled_schemas = get_option('kata_schema_enabled_schemas', array());
            foreach ($enabled_schemas as $schema_type) {
                if ($this->should_show_schema($schema_type)) {
                    $classes[] = 'kata-schema-' . sanitize_html_class($schema_type);
                }
            }
        }
        
        return $classes;
    }
    
    /**
     * Check if schema should be shown on current page
     */
    private function should_show_schema($schema_type) {
        // Simple check - can be expanded based on conditions
        return true;
    }
    
    /**
     * Get schema for specific post
     */
    public function get_post_schema($post_id, $schema_type = null) {
        if (!$schema_type) {
            $schema_type = get_post_meta($post_id, '_kata_schema_type', true);
            if (!$schema_type) {
                $schema_type = 'article';
            }
        }
        
        return $this->generator->generate_schema($post_id, $schema_type);
    }
    
    /**
     * Check if current page has schema
     */
    public function has_schema() {
        $schema_output = $this->generator->generate_schema_output();
        return !empty($schema_output);
    }
    
    /**
     * Get breadcrumb data
     */
    public function get_breadcrumb_data($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        return $this->generator->generate_schema($post_id, 'breadcrumb');
    }
    
    /**
     * Render schema validation widget for logged-in users
     */
    public function render_validation_widget() {
        if (!current_user_can('edit_posts') || !get_option('kata_schema_show_validation_widget', false)) {
            return;
        }
        
        $post_id = get_the_ID();
        if (!$post_id) {
            return;
        }
        
        $schema = $this->get_post_schema($post_id);
        if (!$schema) {
            return;
        }
        
        $validator = new KataSchema_Validator();
        $validation = $validator->validate_schema($schema);
        
        ?>
        <div id="kata-schema-validation-widget" style="position: fixed; bottom: 20px; right: 20px; background: #fff; border: 1px solid #ddd; padding: 15px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 9999; max-width: 300px;">
            <h4 style="margin: 0 0 10px;"><?php _e('Schema Validation', 'kata-schema-markup'); ?></h4>
            <p><strong><?php _e('Score:', 'kata-schema-markup'); ?></strong> <?php echo esc_html($validation['score']); ?>/100</p>
            
            <?php if (!empty($validation['errors'])): ?>
                <div style="color: #d63638;">
                    <strong><?php _e('Errors:', 'kata-schema-markup'); ?></strong>
                    <ul style="margin: 5px 0;">
                        <?php foreach ($validation['errors'] as $error): ?>
                            <li style="font-size: 12px;"><?php echo esc_html($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($validation['warnings'])): ?>
                <div style="color: #dba617;">
                    <strong><?php _e('Warnings:', 'kata-schema-markup'); ?></strong>
                    <ul style="margin: 5px 0;">
                        <?php foreach (array_slice($validation['warnings'], 0, 3) as $warning): ?>
                            <li style="font-size: 12px;"><?php echo esc_html($warning); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <button onclick="this.parentElement.style.display='none'" style="background: #0073aa; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 12px;">
                <?php _e('Close', 'kata-schema-markup'); ?>
            </button>
        </div>
        <?php
    }
}

// Initialize frontend if not in admin
if (!is_admin()) {
    add_action('init', function() {
        new KataSchema_Frontend();
    });
}
