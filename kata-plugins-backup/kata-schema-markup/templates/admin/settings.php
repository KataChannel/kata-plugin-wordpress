<?php
/**
 * Settings Page Template
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Schema Markup Settings', 'kata-schema-markup'); ?></h1>
    
    <div class="kata-settings-wrapper">
        <form method="post" action="options.php" id="kata-settings-form">
            <?php
            settings_fields('kata_schema_settings');
            do_settings_sections('kata_schema_settings');
            ?>
            
            <!-- General Settings -->
            <div class="kata-settings-section">
                <h2><?php _e('General Settings', 'kata-schema-markup'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_enabled"><?php _e('Enable Schema Markup', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="kata_schema_enabled" name="kata_schema_enabled" value="1" 
                                   <?php checked(get_option('kata_schema_enabled', 1)); ?>>
                            <label for="kata_schema_enabled"><?php _e('Enable automatic schema markup generation', 'kata-schema-markup'); ?></label>
                            <p class="description">
                                <?php _e('When enabled, schema markup will be automatically added to your posts and pages.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_default_type"><?php _e('Default Schema Type', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="kata_schema_default_type" name="kata_schema_default_type">
                                <option value="Article" <?php selected(get_option('kata_schema_default_type', 'Article'), 'Article'); ?>>
                                    <?php _e('Article', 'kata-schema-markup'); ?>
                                </option>
                                <option value="BlogPosting" <?php selected(get_option('kata_schema_default_type', 'Article'), 'BlogPosting'); ?>>
                                    <?php _e('Blog Posting', 'kata-schema-markup'); ?>
                                </option>
                                <option value="NewsArticle" <?php selected(get_option('kata_schema_default_type', 'Article'), 'NewsArticle'); ?>>
                                    <?php _e('News Article', 'kata-schema-markup'); ?>
                                </option>
                                <option value="WebPage" <?php selected(get_option('kata_schema_default_type', 'Article'), 'WebPage'); ?>>
                                    <?php _e('Web Page', 'kata-schema-markup'); ?>
                                </option>
                                <option value="Product" <?php selected(get_option('kata_schema_default_type', 'Article'), 'Product'); ?>>
                                    <?php _e('Product', 'kata-schema-markup'); ?>
                                </option>
                                <option value="Service" <?php selected(get_option('kata_schema_default_type', 'Article'), 'Service'); ?>>
                                    <?php _e('Service', 'kata-schema-markup'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('Default schema type for new posts and pages.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_post_types"><?php _e('Enabled Post Types', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <?php
                            $post_types = get_post_types(array('public' => true), 'objects');
                            $enabled_types = get_option('kata_schema_post_types', array('post', 'page'));
                            
                            foreach ($post_types as $post_type):
                                $checked = in_array($post_type->name, $enabled_types) ? 'checked' : '';
                            ?>
                                <label>
                                    <input type="checkbox" name="kata_schema_post_types[]" 
                                           value="<?php echo esc_attr($post_type->name); ?>" <?php echo $checked; ?>>
                                    <?php echo esc_html($post_type->label); ?>
                                </label><br>
                            <?php endforeach; ?>
                            <p class="description">
                                <?php _e('Select which post types should have schema markup enabled.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Organization Settings -->
            <div class="kata-settings-section">
                <h2><?php _e('Organization Settings', 'kata-schema-markup'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_org_name"><?php _e('Organization Name', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="kata_schema_org_name" name="kata_schema_org_name" 
                                   class="regular-text" value="<?php echo esc_attr(get_option('kata_schema_org_name', get_bloginfo('name'))); ?>">
                            <p class="description">
                                <?php _e('The name of your organization or website.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_org_logo"><?php _e('Organization Logo', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="url" id="kata_schema_org_logo" name="kata_schema_org_logo" 
                                   class="regular-text" value="<?php echo esc_url(get_option('kata_schema_org_logo')); ?>"
                                   placeholder="https://example.com/logo.png">
                            <button type="button" class="button" id="upload-logo-btn">
                                <?php _e('Upload Logo', 'kata-schema-markup'); ?>
                            </button>
                            <p class="description">
                                <?php _e('URL to your organization logo (recommended: 600x60px minimum).', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_org_type"><?php _e('Organization Type', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="kata_schema_org_type" name="kata_schema_org_type">
                                <option value="Organization" <?php selected(get_option('kata_schema_org_type', 'Organization'), 'Organization'); ?>>
                                    <?php _e('Organization', 'kata-schema-markup'); ?>
                                </option>
                                <option value="Corporation" <?php selected(get_option('kata_schema_org_type', 'Organization'), 'Corporation'); ?>>
                                    <?php _e('Corporation', 'kata-schema-markup'); ?>
                                </option>
                                <option value="LocalBusiness" <?php selected(get_option('kata_schema_org_type', 'Organization'), 'LocalBusiness'); ?>>
                                    <?php _e('Local Business', 'kata-schema-markup'); ?>
                                </option>
                                <option value="NGO" <?php selected(get_option('kata_schema_org_type', 'Organization'), 'NGO'); ?>>
                                    <?php _e('NGO', 'kata-schema-markup'); ?>
                                </option>
                                <option value="EducationalOrganization" <?php selected(get_option('kata_schema_org_type', 'Organization'), 'EducationalOrganization'); ?>>
                                    <?php _e('Educational Organization', 'kata-schema-markup'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('The type of organization your website represents.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_org_contact"><?php _e('Contact Information', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="email" id="kata_schema_org_contact" name="kata_schema_org_contact" 
                                   class="regular-text" value="<?php echo esc_attr(get_option('kata_schema_org_contact')); ?>"
                                   placeholder="contact@example.com">
                            <p class="description">
                                <?php _e('Primary contact email for your organization.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Author Settings -->
            <div class="kata-settings-section">
                <h2><?php _e('Author Settings', 'kata-schema-markup'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_author_fallback"><?php _e('Author Fallback', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="kata_schema_author_fallback" name="kata_schema_author_fallback">
                                <option value="post_author" <?php selected(get_option('kata_schema_author_fallback', 'post_author'), 'post_author'); ?>>
                                    <?php _e('Use Post Author', 'kata-schema-markup'); ?>
                                </option>
                                <option value="organization" <?php selected(get_option('kata_schema_author_fallback', 'post_author'), 'organization'); ?>>
                                    <?php _e('Use Organization', 'kata-schema-markup'); ?>
                                </option>
                                <option value="custom" <?php selected(get_option('kata_schema_author_fallback', 'post_author'), 'custom'); ?>>
                                    <?php _e('Custom Author', 'kata-schema-markup'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('What to use as author when no specific author is set.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr id="custom-author-row" style="display: none;">
                        <th scope="row">
                            <label for="kata_schema_custom_author"><?php _e('Custom Author Name', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="kata_schema_custom_author" name="kata_schema_custom_author" 
                                   class="regular-text" value="<?php echo esc_attr(get_option('kata_schema_custom_author')); ?>">
                            <p class="description">
                                <?php _e('Name to use when "Custom Author" is selected above.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_author_social"><?php _e('Include Author Social Links', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="kata_schema_author_social" name="kata_schema_author_social" value="1" 
                                   <?php checked(get_option('kata_schema_author_social', 1)); ?>>
                            <label for="kata_schema_author_social"><?php _e('Include author social media profiles in schema', 'kata-schema-markup'); ?></label>
                            <p class="description">
                                <?php _e('Pulls social links from user profiles and includes them in author schema.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Performance Settings -->
            <div class="kata-settings-section">
                <h2><?php _e('Performance Settings', 'kata-schema-markup'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_cache_enabled"><?php _e('Enable Caching', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="kata_schema_cache_enabled" name="kata_schema_cache_enabled" value="1" 
                                   <?php checked(get_option('kata_schema_cache_enabled', 1)); ?>>
                            <label for="kata_schema_cache_enabled"><?php _e('Cache generated schema markup for better performance', 'kata-schema-markup'); ?></label>
                            <p class="description">
                                <?php _e('Recommended for high-traffic sites. Schema will be regenerated when posts are updated.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_cache_duration"><?php _e('Cache Duration', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="kata_schema_cache_duration" name="kata_schema_cache_duration">
                                <option value="3600" <?php selected(get_option('kata_schema_cache_duration', '86400'), '3600'); ?>>
                                    <?php _e('1 Hour', 'kata-schema-markup'); ?>
                                </option>
                                <option value="43200" <?php selected(get_option('kata_schema_cache_duration', '86400'), '43200'); ?>>
                                    <?php _e('12 Hours', 'kata-schema-markup'); ?>
                                </option>
                                <option value="86400" <?php selected(get_option('kata_schema_cache_duration', '86400'), '86400'); ?>>
                                    <?php _e('24 Hours', 'kata-schema-markup'); ?>
                                </option>
                                <option value="604800" <?php selected(get_option('kata_schema_cache_duration', '86400'), '604800'); ?>>
                                    <?php _e('1 Week', 'kata-schema-markup'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('How long to cache schema markup before regenerating.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_minify"><?php _e('Minify Output', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="kata_schema_minify" name="kata_schema_minify" value="1" 
                                   <?php checked(get_option('kata_schema_minify', 0)); ?>>
                            <label for="kata_schema_minify"><?php _e('Remove unnecessary whitespace from schema markup', 'kata-schema-markup'); ?></label>
                            <p class="description">
                                <?php _e('Reduces HTML size but makes schema less readable in source code.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_clear_cache"><?php _e('Cache Management', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <button type="button" class="button" id="clear-schema-cache">
                                <?php _e('Clear All Cache', 'kata-schema-markup'); ?>
                            </button>
                            <p class="description">
                                <?php _e('Clear all cached schema markup. Schema will be regenerated on next page load.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Advanced Settings -->
            <div class="kata-settings-section">
                <h2><?php _e('Advanced Settings', 'kata-schema-markup'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_output_format"><?php _e('Output Format', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="kata_schema_output_format" name="kata_schema_output_format">
                                <option value="json-ld" <?php selected(get_option('kata_schema_output_format', 'json-ld'), 'json-ld'); ?>>
                                    <?php _e('JSON-LD (Recommended)', 'kata-schema-markup'); ?>
                                </option>
                                <option value="microdata" <?php selected(get_option('kata_schema_output_format', 'json-ld'), 'microdata'); ?>>
                                    <?php _e('Microdata', 'kata-schema-markup'); ?>
                                </option>
                                <option value="rdfa" <?php selected(get_option('kata_schema_output_format', 'json-ld'), 'rdfa'); ?>>
                                    <?php _e('RDFa', 'kata-schema-markup'); ?>
                                </option>
                            </select>
                            <p class="description">
                                <?php _e('Format for schema markup output. JSON-LD is recommended by Google.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_debug_mode"><?php _e('Debug Mode', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="kata_schema_debug_mode" name="kata_schema_debug_mode" value="1" 
                                   <?php checked(get_option('kata_schema_debug_mode', 0)); ?>>
                            <label for="kata_schema_debug_mode"><?php _e('Enable debug mode for troubleshooting', 'kata-schema-markup'); ?></label>
                            <p class="description">
                                <?php _e('Adds HTML comments showing schema generation info. Disable on production sites.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_custom_types"><?php _e('Custom Schema Types', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <textarea id="kata_schema_custom_types" name="kata_schema_custom_types" 
                                      class="large-text" rows="5" placeholder='{"CustomType": {"name": "Custom Type", "properties": ["name", "description"]}}'><?php echo esc_textarea(get_option('kata_schema_custom_types')); ?></textarea>
                            <p class="description">
                                <?php _e('JSON configuration for custom schema types. Advanced users only.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_exclude_pages"><?php _e('Exclude Pages', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="kata_schema_exclude_pages" name="kata_schema_exclude_pages" 
                                   class="regular-text" value="<?php echo esc_attr(get_option('kata_schema_exclude_pages')); ?>"
                                   placeholder="1,2,3 or contact,about">
                            <p class="description">
                                <?php _e('Page IDs or slugs to exclude from schema markup (comma-separated).', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Import/Export Settings -->
            <div class="kata-settings-section">
                <h2><?php _e('Import/Export Settings', 'kata-schema-markup'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_export"><?php _e('Export Settings', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <button type="button" class="button" id="export-settings-btn">
                                <?php _e('Export Settings', 'kata-schema-markup'); ?>
                            </button>
                            <p class="description">
                                <?php _e('Export current plugin settings as a JSON file.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="kata_schema_import"><?php _e('Import Settings', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="file" id="kata_schema_import" accept=".json">
                            <button type="button" class="button" id="import-settings-btn">
                                <?php _e('Import Settings', 'kata-schema-markup'); ?>
                            </button>
                            <p class="description">
                                <?php _e('Import plugin settings from a JSON file. This will overwrite current settings.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(); ?>
        </form>
        
        <!-- Reset Settings -->
        <div class="kata-settings-section">
            <h2><?php _e('Reset Settings', 'kata-schema-markup'); ?></h2>
            <p class="description">
                <?php _e('Reset all plugin settings to their default values. This action cannot be undone.', 'kata-schema-markup'); ?>
            </p>
            <button type="button" class="button button-secondary" id="reset-settings-btn">
                <?php _e('Reset to Defaults', 'kata-schema-markup'); ?>
            </button>
        </div>
    </div>
</div>

<style>
.kata-settings-wrapper {
    margin-top: 20px;
}

.kata-settings-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.kata-settings-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.kata-settings-section .form-table {
    margin-top: 20px;
}

.kata-settings-section .form-table th {
    width: 200px;
    padding-left: 0;
}

.kata-settings-section .form-table td {
    padding-left: 20px;
}

#custom-author-row {
    transition: all 0.3s ease;
}

#kata_schema_custom_types {
    font-family: 'Courier New', monospace;
    font-size: 12px;
}

.settings-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.kata-settings-section .description {
    font-style: italic;
    color: #666;
    margin-top: 5px;
}

#reset-settings-btn {
    color: #d63638;
    border-color: #d63638;
}

#reset-settings-btn:hover {
    background: #d63638;
    color: #fff;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Show/hide custom author field
    function toggleCustomAuthor() {
        if ($('#kata_schema_author_fallback').val() === 'custom') {
            $('#custom-author-row').show();
        } else {
            $('#custom-author-row').hide();
        }
    }
    
    $('#kata_schema_author_fallback').on('change', toggleCustomAuthor);
    toggleCustomAuthor(); // Initialize on page load
    
    // Logo upload
    $('#upload-logo-btn').on('click', function(e) {
        e.preventDefault();
        
        var mediaUploader = wp.media({
            title: '<?php _e("Select Logo", "kata-schema-markup"); ?>',
            button: {
                text: '<?php _e("Use this logo", "kata-schema-markup"); ?>'
            },
            multiple: false
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#kata_schema_org_logo').val(attachment.url);
        });
        
        mediaUploader.open();
    });
    
    // Clear cache
    $('#clear-schema-cache').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to clear all cached schema markup?", "kata-schema-markup"); ?>')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_clear_schema_cache',
                    nonce: '<?php echo wp_create_nonce("kata_clear_cache"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('<?php _e("Cache cleared successfully!", "kata-schema-markup"); ?>');
                    } else {
                        alert('<?php _e("Error clearing cache. Please try again.", "kata-schema-markup"); ?>');
                    }
                }
            });
        }
    });
    
    // Export settings
    $('#export-settings-btn').on('click', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_export_settings',
                nonce: '<?php echo wp_create_nonce("kata_export_settings"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(response.data, null, 2));
                    var downloadAnchorNode = document.createElement('a');
                    downloadAnchorNode.setAttribute("href", dataStr);
                    downloadAnchorNode.setAttribute("download", "kata-schema-settings.json");
                    document.body.appendChild(downloadAnchorNode);
                    downloadAnchorNode.click();
                    downloadAnchorNode.remove();
                } else {
                    alert('<?php _e("Error exporting settings. Please try again.", "kata-schema-markup"); ?>');
                }
            }
        });
    });
    
    // Import settings
    $('#import-settings-btn').on('click', function() {
        var file = $('#kata_schema_import')[0].files[0];
        if (!file) {
            alert('<?php _e("Please select a file to import.", "kata-schema-markup"); ?>');
            return;
        }
        
        var reader = new FileReader();
        reader.onload = function(e) {
            try {
                var settings = JSON.parse(e.target.result);
                
                if (confirm('<?php _e("This will overwrite your current settings. Are you sure?", "kata-schema-markup"); ?>')) {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'kata_import_settings',
                            settings: JSON.stringify(settings),
                            nonce: '<?php echo wp_create_nonce("kata_import_settings"); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('<?php _e("Settings imported successfully! Reloading page...", "kata-schema-markup"); ?>');
                                location.reload();
                            } else {
                                alert('<?php _e("Error importing settings: ", "kata-schema-markup"); ?>' + (response.data || '<?php _e("Unknown error", "kata-schema-markup"); ?>'));
                            }
                        }
                    });
                }
            } catch (error) {
                alert('<?php _e("Invalid JSON file. Please check the file format.", "kata-schema-markup"); ?>');
            }
        };
        reader.readAsText(file);
    });
    
    // Reset settings
    $('#reset-settings-btn').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to reset all settings to their default values? This action cannot be undone.", "kata-schema-markup"); ?>')) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_reset_settings',
                    nonce: '<?php echo wp_create_nonce("kata_reset_settings"); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        alert('<?php _e("Settings reset successfully! Reloading page...", "kata-schema-markup"); ?>');
                        location.reload();
                    } else {
                        alert('<?php _e("Error resetting settings. Please try again.", "kata-schema-markup"); ?>');
                    }
                }
            });
        }
    });
    
    // Form validation
    $('#kata-settings-form').on('submit', function(e) {
        var customTypes = $('#kata_schema_custom_types').val();
        if (customTypes.trim() !== '') {
            try {
                JSON.parse(customTypes);
            } catch (error) {
                e.preventDefault();
                alert('<?php _e("Invalid JSON in Custom Schema Types field. Please check the syntax.", "kata-schema-markup"); ?>');
                $('#kata_schema_custom_types').focus();
                return false;
            }
        }
        
        // Validate logo URL
        var logoUrl = $('#kata_schema_org_logo').val();
        if (logoUrl && !isValidUrl(logoUrl)) {
            e.preventDefault();
            alert('<?php _e("Please enter a valid URL for the organization logo.", "kata-schema-markup"); ?>');
            $('#kata_schema_org_logo').focus();
            return false;
        }
    });
    
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
});
</script>
