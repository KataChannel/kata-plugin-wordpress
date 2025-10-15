<?php
/**
 * Recipe Shortcode
 * 
 * [kata_recipe]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Recipe extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_recipe';
    protected $schema_type = 'recipe';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => '',
            'description' => '',
            'image' => '',
            'author' => '',
            'prep_time' => '',
            'cook_time' => '',
            'total_time' => '',
            'yield' => '',
            'ingredients' => '',
            'instructions' => '',
            'rating_value' => '',
            'rating_count' => '',
            'show_output' => 'true'
        ));
        
        if (empty($atts['name'])) {
            $this->log_error('Recipe name is required');
            return '';
        }
        
        // Build schema data
        $data = array(
            'name' => $atts['name'],
            'description' => $atts['description'],
            'image' => $atts['image'],
            'author' => array(
                '@type' => 'Person',
                'name' => $atts['author']
            ),
            'prepTime' => $atts['prep_time'],
            'cookTime' => $atts['cook_time'],
            'totalTime' => $atts['total_time'],
            'recipeYield' => $atts['yield'],
            'recipeIngredient' => array_filter(array_map('trim', explode('|', $atts['ingredients']))),
            'recipeInstructions' => array_filter(array_map('trim', explode('|', $atts['instructions'])))
        );
        
        if (!empty($atts['rating_value'])) {
            $data['aggregateRating'] = array(
                '@type' => 'AggregateRating',
                'ratingValue' => $atts['rating_value'],
                'ratingCount' => $atts['rating_count']
            );
        }
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <div class="kata-recipe" itemscope itemtype="https://schema.org/Recipe">
                <h3 itemprop="name"><?php echo esc_html($atts['name']); ?></h3>
                <p itemprop="description"><?php echo esc_html($atts['description']); ?></p>
                <?php if (!empty($atts['image'])): ?>
                    <img itemprop="image" src="<?php echo esc_url($atts['image']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
                <?php endif; ?>
                <div class="kata-recipe-meta">
                    <p><strong><?php _e('Prep Time:', 'kata-seo-manager'); ?></strong> <span itemprop="prepTime"><?php echo esc_html($atts['prep_time']); ?></span></p>
                    <p><strong><?php _e('Cook Time:', 'kata-seo-manager'); ?></strong> <span itemprop="cookTime"><?php echo esc_html($atts['cook_time']); ?></span></p>
                    <p><strong><?php _e('Servings:', 'kata-seo-manager'); ?></strong> <span itemprop="recipeYield"><?php echo esc_html($atts['yield']); ?></span></p>
                </div>
                <?php if (!empty($atts['ingredients'])): ?>
                    <div class="kata-recipe-ingredients">
                        <h4><?php _e('Ingredients:', 'kata-seo-manager'); ?></h4>
                        <ul>
                            <?php foreach (array_filter(array_map('trim', explode('|', $atts['ingredients']))) as $ingredient): ?>
                                <li itemprop="recipeIngredient"><?php echo esc_html($ingredient); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
