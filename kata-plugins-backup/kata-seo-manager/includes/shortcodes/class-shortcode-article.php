<?php
/**
 * Article Shortcode
 * 
 * [kata_article]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Article extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_article';
    protected $schema_type = 'article';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'headline' => get_the_title(),
            'author' => get_the_author(),
            'date_published' => get_the_date('c'),
            'date_modified' => get_the_modified_date('c'),
            'image' => get_the_post_thumbnail_url(null, 'full'),
            'publisher_name' => get_bloginfo('name'),
            'publisher_logo' => '',
            'description' => get_the_excerpt(),
            'article_type' => 'Article',
            'show_output' => 'false'
        ));
        
        // Build schema data
        $data = array(
            'headline' => $atts['headline'],
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            ),
            'datePublished' => $atts['date_published'],
            'dateModified' => $atts['date_modified'],
            'description' => $atts['description'],
            'publisher' => array(
                '@type' => 'Organization',
                'name' => $atts['publisher_name'],
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $atts['publisher_logo']
                )
            )
        );
        
        // Add image if available
        if (!empty($atts['image'])) {
            $data['image'] = $atts['image'];
        }
        
        // Generate and store schema
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        // Return HTML output if requested
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <article class="kata-article">
                <h1><?php echo esc_html($atts['headline']); ?></h1>
                <div class="kata-article-meta">
                    <span class="author"><?php _e('By', 'kata-seo-manager'); ?> <?php echo esc_html($atts['author']); ?></span>
                    <time datetime="<?php echo esc_attr($atts['date_published']); ?>">
                        <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($atts['date_published']))); ?>
                    </time>
                </div>
                <?php if (!empty($atts['image'])): ?>
                    <img src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['headline']); ?>">
                <?php endif; ?>
                <div class="kata-article-content">
                    <?php echo wpautop(do_shortcode($content)); ?>
                </div>
            </article>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
