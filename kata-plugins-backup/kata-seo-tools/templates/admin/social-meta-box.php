<?php
/**
 * Social Meta Box Template
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="kata-meta-box kata-social-meta-box">
    <h4>Open Graph (Facebook, LinkedIn)</h4>
    
    <div class="kata-field">
        <label for="kata_seo_og_title">OG Title:</label>
        <input type="text" 
               id="kata_seo_og_title" 
               name="kata_seo_og_title" 
               value="<?php echo esc_attr($og_title); ?>" 
               class="widefat"
               placeholder="<?php echo esc_attr(get_the_title($post)); ?>">
        <p class="description">Leave empty to use post title. Recommended: 60-90 characters.</p>
    </div>

    <div class="kata-field">
        <label for="kata_seo_og_description">OG Description:</label>
        <textarea id="kata_seo_og_description" 
                  name="kata_seo_og_description" 
                  rows="3" 
                  class="widefat"
                  placeholder="<?php echo esc_attr(wp_trim_words(get_the_excerpt($post), 20)); ?>"><?php echo esc_textarea($og_description); ?></textarea>
        <p class="description">Leave empty to use post excerpt. Recommended: 150-300 characters.</p>
    </div>

    <div class="kata-field">
        <label for="kata_seo_og_image">OG Image URL:</label>
        <div class="kata-image-field">
            <input type="text" 
                   id="kata_seo_og_image" 
                   name="kata_seo_og_image" 
                   value="<?php echo esc_url($og_image); ?>" 
                   class="widefat kata-image-url"
                   placeholder="<?php echo esc_url(get_the_post_thumbnail_url($post, 'large')); ?>">
            <button type="button" class="button kata-upload-image">Upload Image</button>
            <button type="button" class="button kata-remove-image" style="<?php echo $og_image ? '' : 'display:none;'; ?>">Remove</button>
        </div>
        <div class="kata-image-preview" style="margin-top: 10px; <?php echo $og_image ? '' : 'display:none;'; ?>">
            <img src="<?php echo esc_url($og_image); ?>" style="max-width: 300px; height: auto; border: 1px solid #ddd; padding: 5px;">
        </div>
        <p class="description">Recommended: 1200x630px. Leave empty to use featured image.</p>
    </div>

    <hr style="margin: 20px 0;">
    
    <h4>Twitter Card</h4>
    
    <div class="kata-field">
        <label for="kata_seo_twitter_card">Twitter Card Type:</label>
        <select id="kata_seo_twitter_card" name="kata_seo_twitter_card" class="widefat">
            <option value="summary" <?php selected($twitter_card, 'summary'); ?>>Summary</option>
            <option value="summary_large_image" <?php selected($twitter_card, 'summary_large_image'); ?>>Summary Large Image</option>
            <option value="app" <?php selected($twitter_card, 'app'); ?>>App</option>
            <option value="player" <?php selected($twitter_card, 'player'); ?>>Player</option>
        </select>
        <p class="description">Choose the Twitter card type. Most common: Summary Large Image.</p>
    </div>

    <div class="kata-field">
        <label for="kata_seo_twitter_title">Twitter Title:</label>
        <input type="text" 
               id="kata_seo_twitter_title" 
               name="kata_seo_twitter_title" 
               value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_twitter_title', true)); ?>" 
               class="widefat"
               placeholder="<?php echo esc_attr($og_title ?: get_the_title($post)); ?>">
        <p class="description">Leave empty to use OG title. Max: 70 characters.</p>
    </div>

    <div class="kata-field">
        <label for="kata_seo_twitter_description">Twitter Description:</label>
        <textarea id="kata_seo_twitter_description" 
                  name="kata_seo_twitter_description" 
                  rows="3" 
                  class="widefat"
                  placeholder="<?php echo esc_attr($og_description ?: wp_trim_words(get_the_excerpt($post), 20)); ?>"><?php echo esc_textarea(get_post_meta($post->ID, '_kata_seo_twitter_description', true)); ?></textarea>
        <p class="description">Leave empty to use OG description. Max: 200 characters.</p>
    </div>

    <hr style="margin: 20px 0;">

    <div class="kata-field">
        <p class="description">
            <strong>Preview:</strong> Test your social sharing with:
            <br>• Facebook: <a href="https://developers.facebook.com/tools/debug/" target="_blank">Sharing Debugger</a>
            <br>• Twitter: <a href="https://cards-dev.twitter.com/validator" target="_blank">Card Validator</a>
        </p>
    </div>
</div>

<style>
.kata-social-meta-box h4 {
    margin: 15px 0 10px;
    font-size: 14px;
    font-weight: 600;
}
.kata-image-field {
    display: flex;
    gap: 5px;
    align-items: center;
}
.kata-image-field .kata-image-url {
    flex: 1;
}
.kata-image-preview img {
    display: block;
}
</style>

<script>
jQuery(document).ready(function($) {
    var mediaUploader;
    
    // Upload image
    $('.kata-upload-image').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var inputField = button.siblings('.kata-image-url');
        var preview = button.closest('.kata-field').find('.kata-image-preview');
        var removeBtn = button.siblings('.kata-remove-image');
        
        // If the media uploader exists, open it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        
        // Create media uploader
        mediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        
        // When image is selected
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            inputField.val(attachment.url);
            preview.html('<img src="' + attachment.url + '" style="max-width: 300px; height: auto; border: 1px solid #ddd; padding: 5px;">');
            preview.show();
            removeBtn.show();
        });
        
        mediaUploader.open();
    });
    
    // Remove image
    $('.kata-remove-image').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var inputField = button.siblings('.kata-image-url');
        var preview = button.closest('.kata-field').find('.kata-image-preview');
        
        inputField.val('');
        preview.html('').hide();
        button.hide();
    });
});
</script>
