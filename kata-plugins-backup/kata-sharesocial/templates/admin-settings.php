<?php
/**
 * Admin Settings Template
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$platforms = KataShareSocial_Platforms::get_instance();
$all_platforms = $platforms->get_platforms();
$enabled_platforms = get_option('kata_sharesocial_enabled_platforms', array('facebook', 'twitter', 'linkedin'));
$button_style = get_option('kata_sharesocial_button_style', 'rounded');
$button_size = get_option('kata_sharesocial_button_size', 'medium');
$show_count = get_option('kata_sharesocial_show_count', true);
$auto_add_buttons = get_option('kata_sharesocial_auto_add_buttons', true);
$button_position = get_option('kata_sharesocial_button_position', 'bottom');
$exclude_post_types = get_option('kata_sharesocial_exclude_post_types', array());
$custom_css = get_option('kata_sharesocial_custom_css', '');
$analytics_enabled = get_option('kata_sharesocial_analytics_enabled', true);

$post_types = get_post_types(array('public' => true), 'objects');
?>

<div class="wrap kata-sharesocial-settings">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-admin-settings" style="font-size: 28px; vertical-align: middle;"></span>
        <?php esc_html_e('Kata ShareSocial Settings', 'kata-sharesocial'); ?>
    </h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('kata_sharesocial_settings'); ?>
        
        <div class="kata-settings-container">
            
            <!-- Platform Settings -->
            <div class="kata-settings-section">
                <h2><?php esc_html_e('Social Platforms', 'kata-sharesocial'); ?></h2>
                <p class="description"><?php esc_html_e('Select which social media platforms you want to enable for sharing.', 'kata-sharesocial'); ?></p>
                
                <div class="kata-platforms-grid">
                    <?php foreach ($all_platforms as $platform_id => $platform): ?>
                        <label class="kata-platform-option">
                            <input type="checkbox" 
                                   name="enabled_platforms[]" 
                                   value="<?php echo esc_attr($platform_id); ?>"
                                   <?php checked(in_array($platform_id, $enabled_platforms)); ?>>
                            <div class="kata-platform-card">
                                <div class="kata-platform-icon" style="color: <?php echo esc_attr($platform['color']); ?>;">
                                    <i class="<?php echo esc_attr($platform['icon']); ?>"></i>
                                </div>
                                <div class="kata-platform-name"><?php echo esc_html($platform['name']); ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Button Appearance -->
            <div class="kata-settings-section">
                <h2><?php esc_html_e('Button Appearance', 'kata-sharesocial'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Button Style', 'kata-sharesocial'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="button_style" value="rounded" <?php checked($button_style, 'rounded'); ?>>
                                    <?php esc_html_e('Rounded', 'kata-sharesocial'); ?>
                                </label><br>
                                <label>
                                    <input type="radio" name="button_style" value="square" <?php checked($button_style, 'square'); ?>>
                                    <?php esc_html_e('Square', 'kata-sharesocial'); ?>
                                </label><br>
                                <label>
                                    <input type="radio" name="button_style" value="circle" <?php checked($button_style, 'circle'); ?>>
                                    <?php esc_html_e('Circle (Icon Only)', 'kata-sharesocial'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e('Button Size', 'kata-sharesocial'); ?></th>
                        <td>
                            <select name="button_size">
                                <option value="small" <?php selected($button_size, 'small'); ?>><?php esc_html_e('Small', 'kata-sharesocial'); ?></option>
                                <option value="medium" <?php selected($button_size, 'medium'); ?>><?php esc_html_e('Medium', 'kata-sharesocial'); ?></option>
                                <option value="large" <?php selected($button_size, 'large'); ?>><?php esc_html_e('Large', 'kata-sharesocial'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e('Show Share Count', 'kata-sharesocial'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="show_count" value="1" <?php checked($show_count); ?>>
                                <?php esc_html_e('Display the number of shares for each platform and total', 'kata-sharesocial'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Display Settings -->
            <div class="kata-settings-section">
                <h2><?php esc_html_e('Display Settings', 'kata-sharesocial'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Auto Add Buttons', 'kata-sharesocial'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="auto_add_buttons" value="1" <?php checked($auto_add_buttons); ?>>
                                <?php esc_html_e('Automatically add share buttons to posts and pages', 'kata-sharesocial'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e('Button Position', 'kata-sharesocial'); ?></th>
                        <td>
                            <select name="button_position">
                                <option value="top" <?php selected($button_position, 'top'); ?>><?php esc_html_e('Before Content', 'kata-sharesocial'); ?></option>
                                <option value="bottom" <?php selected($button_position, 'bottom'); ?>><?php esc_html_e('After Content', 'kata-sharesocial'); ?></option>
                                <option value="both" <?php selected($button_position, 'both'); ?>><?php esc_html_e('Before and After Content', 'kata-sharesocial'); ?></option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php esc_html_e('Exclude Post Types', 'kata-sharesocial'); ?></th>
                        <td>
                            <fieldset>
                                <?php foreach ($post_types as $post_type): ?>
                                    <label>
                                        <input type="checkbox" 
                                               name="exclude_post_types[]" 
                                               value="<?php echo esc_attr($post_type->name); ?>"
                                               <?php checked(in_array($post_type->name, $exclude_post_types)); ?>>
                                        <?php echo esc_html($post_type->label); ?>
                                    </label><br>
                                <?php endforeach; ?>
                                <p class="description"><?php esc_html_e('Select post types where you do NOT want to show share buttons automatically.', 'kata-sharesocial'); ?></p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Analytics Settings -->
            <div class="kata-settings-section">
                <h2><?php esc_html_e('Analytics & Tracking', 'kata-sharesocial'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Enable Analytics', 'kata-sharesocial'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="analytics_enabled" value="1" <?php checked($analytics_enabled); ?>>
                                <?php esc_html_e('Track sharing statistics and generate analytics reports', 'kata-sharesocial'); ?>
                            </label>
                            <p class="description"><?php esc_html_e('This will track when users click on share buttons and provide detailed analytics.', 'kata-sharesocial'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Custom CSS -->
            <div class="kata-settings-section">
                <h2><?php esc_html_e('Custom Styling', 'kata-sharesocial'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="custom_css"><?php esc_html_e('Custom CSS', 'kata-sharesocial'); ?></label>
                        </th>
                        <td>
                            <textarea name="custom_css" id="custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
                            <p class="description"><?php esc_html_e('Add your custom CSS to override the default styling.', 'kata-sharesocial'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Preview -->
            <div class="kata-settings-section">
                <h2><?php esc_html_e('Preview', 'kata-sharesocial'); ?></h2>
                <p class="description"><?php esc_html_e('Preview how your share buttons will look with current settings.', 'kata-sharesocial'); ?></p>
                
                <div class="kata-preview-container">
                    <div id="kata-preview-buttons">
                        <?php
                        $frontend = KataShareSocial_Frontend::get_instance();
                        echo $frontend->render_share_buttons(array(
                            'platforms' => $enabled_platforms,
                            'style' => $button_style,
                            'size' => $button_size,
                            'show_count' => false,
                            'post_id' => get_option('page_on_front') ?: 1
                        ));
                        ?>
                    </div>
                </div>
            </div>
            
        </div>
        
        <?php 
        if (function_exists('submit_button')) {
            submit_button(__('Save Settings', 'kata-sharesocial')); 
        } else {
            echo '<p class="submit"><input type="submit" name="submit" class="button-primary" value="' . esc_attr__('Save Settings', 'kata-sharesocial') . '" /></p>';
        }
        ?>
    </form>
</div>

<style>
.kata-sharesocial-settings {
    margin-top: 20px;
}

.kata-settings-container {
    max-width: 900px;
}

.kata-settings-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 6px;
    margin: 20px 0;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.kata-settings-section h2 {
    margin-top: 0;
    margin-bottom: 15px;
    font-size: 18px;
    color: #1d2327;
}

.kata-platforms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.kata-platform-option {
    cursor: pointer;
    display: block;
}

.kata-platform-option input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.kata-platform-card {
    border: 2px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: all 0.3s ease;
    background: #fff;
}

.kata-platform-option input[type="checkbox"]:checked + .kata-platform-card {
    border-color: #0073aa;
    background: #f0f8ff;
}

.kata-platform-card:hover {
    border-color: #0073aa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.kata-platform-icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.kata-platform-name {
    font-weight: 600;
    color: #1d2327;
}

.kata-preview-container {
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 15px;
}

.form-table th {
    width: 200px;
    padding: 20px 10px 20px 0;
    vertical-align: top;
}

.form-table td {
    padding: 15px 10px;
}

.form-table fieldset label {
    display: block;
    margin-bottom: 8px;
}

.description {
    color: #646970;
    font-style: italic;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .kata-platforms-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 10px;
    }
    
    .kata-platform-card {
        padding: 15px;
    }
    
    .kata-platform-icon {
        font-size: 24px;
    }
    
    .form-table th,
    .form-table td {
        display: block;
        width: 100%;
        padding: 10px 0;
    }
    
    .form-table th {
        border-bottom: none;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Update preview when settings change
    function updatePreview() {
        var selectedPlatforms = [];
        $('input[name="enabled_platforms[]"]:checked').each(function() {
            selectedPlatforms.push($(this).val());
        });
        
        var style = $('input[name="button_style"]:checked').val();
        var size = $('select[name="button_size"]').val();
        
        // Update preview classes
        var previewContainer = $('#kata-preview-buttons .kata-share-buttons');
        previewContainer.removeClass('kata-style-rounded kata-style-square kata-style-circle');
        previewContainer.removeClass('kata-size-small kata-size-medium kata-size-large');
        previewContainer.addClass('kata-style-' + style);
        previewContainer.addClass('kata-size-' + size);
        
        // Show/hide platforms
        previewContainer.find('.kata-share-button').each(function() {
            var platform = $(this).data('platform');
            if (selectedPlatforms.indexOf(platform) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    // Bind events
    $('input[name="enabled_platforms[]"], input[name="button_style"], select[name="button_size"]').on('change', updatePreview);
    
    // Initial preview update
    updatePreview();
});
</script>
