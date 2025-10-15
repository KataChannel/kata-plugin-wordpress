<?php
/**
 * Schema Renderer
 * 
 * Handles schema markup output in wp_head
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Schema_Renderer {
    
    /**
     * Core instance
     * @var KATA_SEO_Core
     */
    private $core;
    
    /**
     * Constructor
     */
    public function __construct($core) {
        $this->core = $core;
        add_action('wp_head', array($this, 'output_schema_markup'), 999);
    }
    
    /**
     * Output schema markup in <head>
     * 
     * @param bool $force_output Force output for testing
     */
    public function output_schema_markup($force_output = false) {
        // Only output on singular pages or front page
        if (!$force_output && !is_singular() && !is_front_page()) {
            return;
        }
        
        global $post, $wpdb;
        
        // Get post for front page
        if (is_front_page() && get_option('page_on_front')) {
            $page_id = get_option('page_on_front');
            $post = get_post($page_id);
        }
        
        if (empty($post)) {
            return;
        }
        
        $output_schemas = array();
        $generator = new KATA_SEO_Schema_Generator();
        
        // PREPROCESS: Extract schemas from shortcodes
        if (!empty($post->post_content) && strpos($post->post_content, '[kata_') !== false) {
            do_shortcode($post->post_content);
        }
        
        // SOURCE 1: Post meta schemas (legacy)
        $post_meta_schemas = get_post_meta($post->ID, '_kata_seo_schemas', true);
        if (!empty($post_meta_schemas) && is_array($post_meta_schemas)) {
            foreach ($post_meta_schemas as $schema_data) {
                $result = $generator->generate($schema_data['type'], $schema_data['data']);
                
                $schema_markup = null;
                if (is_array($result) && isset($result['schema'])) {
                    $schema_markup = $result['schema'];
                } elseif (is_array($result) && !isset($result['errors'])) {
                    $schema_markup = $result;
                }
                
                if ($schema_markup && !is_wp_error($schema_markup)) {
                    $output_schemas[] = $schema_markup;
                }
            }
        }
        
        // SOURCE 2: Database schemas
        $table_name = $wpdb->prefix . 'kata_schemas';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
            $current_post_type = get_post_type();
            $current_post_id = get_the_ID();
            
            $db_schemas = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active' ORDER BY id ASC");
            
            if (!empty($db_schemas)) {
                foreach ($db_schemas as $schema) {
                    $schema_data = json_decode($schema->schema_data, true);
                    if (empty($schema_data)) {
                        continue;
                    }
                    
                    $should_output = false;
                    
                    // Check auto-insert
                    if (isset($schema_data['auto_insert']) && $schema_data['auto_insert']) {
                        if (isset($schema_data['post_types']) && is_array($schema_data['post_types'])) {
                            if (in_array($current_post_type, $schema_data['post_types'])) {
                                $should_output = true;
                            }
                        }
                    }
                    
                    // Check specific post assignment
                    if (!$should_output && isset($schema_data['assigned_posts']) && is_array($schema_data['assigned_posts'])) {
                        if (in_array($current_post_id, $schema_data['assigned_posts'])) {
                            $should_output = true;
                        }
                    }
                    
                    if ($should_output) {
                        $result = $generator->generate($schema->schema_type, $schema_data);
                        
                        $schema_markup = null;
                        if (is_array($result) && isset($result['schema'])) {
                            $schema_markup = $result['schema'];
                        } elseif (is_array($result) && !isset($result['errors'])) {
                            $schema_markup = $result;
                        }
                        
                        if ($schema_markup && !is_wp_error($schema_markup)) {
                            $output_schemas[] = $schema_markup;
                        }
                    }
                }
            }
        }
        
        // SOURCE 3: Shortcode schemas
        $shortcode_schemas = $this->core->get_shortcode_schemas();
        if (!empty($shortcode_schemas)) {
            foreach ($shortcode_schemas as $schema_markup) {
                $output_schemas[] = $schema_markup;
            }
        }
        
        // OUTPUT schemas
        if (!empty($output_schemas)) {
            echo "\n<!-- KATA SEO Schema Markup -->\n";
            foreach ($output_schemas as $schema_markup) {
                echo '<script type="application/ld+json">' . "\n";
                echo json_encode($schema_markup, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                echo "\n" . '</script>' . "\n";
            }
            echo "<!-- /KATA SEO Schema Markup -->\n\n";
        }
    }
}
