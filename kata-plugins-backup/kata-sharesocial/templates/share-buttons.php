<?php
/**
 * Share Buttons Template
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Use extracted variables or fallback to safe defaults
$post_id = isset($post_id) ? $post_id : (isset($args['post_id']) ? $args['post_id'] : 0);
$style = isset($style) ? $style : (isset($args['style']) ? $args['style'] : 'rounded');
$size = isset($size) ? $size : (isset($args['size']) ? $args['size'] : 'medium');
$show_count = isset($show_count) ? $show_count : (isset($args['show_count']) ? $args['show_count'] : true);
?>

<div class="kata-share-buttons kata-style-<?php echo esc_attr($style); ?> kata-size-<?php echo esc_attr($size); ?>" data-post-id="<?php echo esc_attr($post_id); ?>">
    
    <?php if ($show_count): ?>
        <?php 
        $platforms = KataShareSocial_Platforms::get_instance();
        $total_shares = $platforms->get_share_count($post_id);
        ?>
        <?php if ($total_shares > 0): ?>
            <div class="kata-share-total">
                <div class="kata-share-count-number"><?php echo esc_html(number_format($total_shares)); ?></div>
                <div class="kata-share-count-label"><?php echo esc_html__('Shares', 'kata-sharesocial'); ?></div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="kata-share-buttons-list">
        <?php 
        // Ensure we have the required variables
        if (isset($enabled_platforms) && isset($platforms) && !empty($enabled_platforms)) {
            foreach ($enabled_platforms as $platform_id => $platform): 
        ?>
            <?php 
            $share_url = $platforms->generate_share_url($platform_id, $post_id);
            $platform_count = $show_count ? $platforms->get_share_count($post_id, $platform_id) : 0;
            ?>
            
            <a href="<?php echo esc_url($share_url); ?>" 
               class="kata-share-button kata-platform-<?php echo esc_attr($platform_id); ?>" 
               data-platform="<?php echo esc_attr($platform_id); ?>"
               data-popup-width="<?php echo esc_attr($platform['popup_width']); ?>"
               data-popup-height="<?php echo esc_attr($platform['popup_height']); ?>"
               data-special-action="<?php echo esc_attr($platform['special_action'] ?? ''); ?>"
               style="background-color: <?php echo esc_attr($platform['color']); ?>;"
               title="<?php echo esc_attr(sprintf(__('Share on %s', 'kata-sharesocial'), $platform['name'])); ?>"
               target="_blank"
               rel="noopener noreferrer">
                
                <span class="kata-button-icon">
                    <i class="<?php echo esc_attr($platform['icon']); ?>"></i>
                </span>
                
                <span class="kata-button-text">
                    <span class="kata-platform-name"><?php echo esc_html($platform['name']); ?></span>
                    <?php if ($show_count && $platform_count > 0): ?>
                        <span class="kata-platform-count"><?php echo esc_html(number_format($platform_count)); ?></span>
                    <?php endif; ?>
                </span>
            </a>
            
        <?php 
            endforeach;
        } else {
            echo '<p>' . __('No sharing platforms configured.', 'kata-sharesocial') . '</p>';
        }
        ?>
    </div>
    
    <?php if (get_option('kata_sharesocial_analytics_enabled', true)): ?>
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SocialMediaPosting",
            "url": "<?php echo esc_url(get_permalink($post_id)); ?>",
            "headline": "<?php echo esc_js(get_the_title($post_id)); ?>",
            "datePublished": "<?php echo esc_attr(get_the_date('c', $post_id)); ?>",
            "author": {
                "@type": "Person",
                "name": "<?php echo esc_js(get_the_author_meta('display_name', get_post_field('post_author', $post_id))); ?>"
            }
        }
        </script>
    <?php endif; ?>
    
</div>

<?php
// Add custom CSS if any
$custom_css = get_option('kata_sharesocial_custom_css', '');
if (!empty($custom_css)):
?>
<style type="text/css">
<?php echo wp_kses($custom_css, array()); ?>
</style>
<?php endif; ?>
