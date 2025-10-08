<?php
/**
 * Post Metabox Template
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$schema_types = $this->generator->get_schema_types();
?>

<div id="kata-schema-metabox">
    <?php wp_nonce_field('kata_schema_metabox', 'kata_schema_metabox_nonce'); ?>
    <div class="kata-schema-tabs">
        <nav class="nav-tab-wrapper">
            <a href="#tab-general" class="nav-tab nav-tab-active"><?php _e('General', 'kata-schema-markup'); ?></a>
            <a href="#tab-custom" class="nav-tab"><?php _e('Custom Schema', 'kata-schema-markup'); ?></a>
            <a href="#tab-preview" class="nav-tab"><?php _e('Preview', 'kata-schema-markup'); ?></a>
            <a href="#tab-validation" class="nav-tab"><?php _e('Validation', 'kata-schema-markup'); ?></a>
        </nav>
        
        <!-- General Tab -->
        <div id="tab-general" class="tab-content active">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="kata_schema_enabled"><?php _e('Enable Schema', 'kata-schema-markup'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="kata_schema_enabled" id="kata_schema_enabled" 
                                   value="1" <?php checked($schema_enabled, '1'); ?> />
                            <?php _e('Generate schema markup for this post', 'kata-schema-markup'); ?>
                        </label>
                        <p class="description">
                            <?php _e('When enabled, schema markup will be automatically generated based on the selected type and post content.', 'kata-schema-markup'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="kata_schema_type"><?php _e('Schema Type', 'kata-schema-markup'); ?></label>
                    </th>
                    <td>
                        <select name="kata_schema_type" id="kata_schema_type" class="widefat">
                            <option value=""><?php _e('Auto-detect', 'kata-schema-markup'); ?></option>
                            <?php foreach ($schema_types as $type_key => $type_data): ?>
                                <option value="<?php echo esc_attr($type_key); ?>" 
                                        <?php selected($schema_type, $type_key); ?>
                                        data-description="<?php echo esc_attr($type_data['description']); ?>">
                                    <?php echo esc_html($type_data['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div id="schema-type-description" class="description">
                            <?php if ($schema_type && isset($schema_types[$schema_type])): ?>
                                <p><?php echo esc_html($schema_types[$schema_type]['description']); ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
            
            <!-- Additional Schema Fields -->
            <div id="additional-schema-fields" class="kata-schema-additional-fields">
                <h4><?php _e('Additional Schema Properties', 'kata-schema-markup'); ?></h4>
                
                <table class="form-table">
                    <tr class="article-fields">
                        <th scope="row">
                            <label for="kata_schema_article_section"><?php _e('Article Section', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kata_schema_article_section" id="kata_schema_article_section" 
                                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_schema_article_section', true)); ?>" 
                                   class="widefat" />
                            <p class="description"><?php _e('The section or category this article belongs to (e.g., Sports, Technology, Lifestyle)', 'kata-schema-markup'); ?></p>
                        </td>
                    </tr>
                    
                    <tr class="article-fields">
                        <th scope="row">
                            <label for="kata_schema_article_about"><?php _e('About', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <textarea name="kata_schema_article_about" id="kata_schema_article_about" 
                                      rows="3" class="widefat"><?php echo esc_textarea(get_post_meta($post->ID, '_kata_schema_article_about', true)); ?></textarea>
                            <p class="description"><?php _e('What this article is about (main subject or topic)', 'kata-schema-markup'); ?></p>
                        </td>
                    </tr>
                    
                    <tr class="article-fields">
                        <th scope="row">
                            <label for="kata_schema_article_mentions"><?php _e('Mentions', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kata_schema_article_mentions" id="kata_schema_article_mentions" 
                                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_schema_article_mentions', true)); ?>" 
                                   class="widefat" />
                            <p class="description"><?php _e('People, organizations, or entities mentioned in this article (comma-separated)', 'kata-schema-markup'); ?></p>
                        </td>
                    </tr>
                    
                    <tr class="article-fields">
                        <th scope="row">
                            <label for="kata_schema_article_audience"><?php _e('Target Audience', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kata_schema_article_audience" id="kata_schema_article_audience" 
                                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_schema_article_audience', true)); ?>" 
                                   class="widefat" />
                            <p class="description"><?php _e('Target audience for this article (e.g., Beginners, Professionals, General Public)', 'kata-schema-markup'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Schema Variables Help -->
            <div class="kata-schema-variables-help">
                <h4><?php _e('Available Variables', 'kata-schema-markup'); ?></h4>
                <p class="description"><?php _e('You can use these variables in custom schema JSON:', 'kata-schema-markup'); ?></p>
                <div class="kata-schema-variables-grid">
                    <div class="variable-group">
                        <strong><?php _e('Post Variables:', 'kata-schema-markup'); ?></strong>
                        <code>{{post_title}}</code>
                        <code>{{post_excerpt}}</code>
                        <code>{{post_content}}</code>
                        <code>{{featured_image}}</code>
                        <code>{{publish_date}}</code>
                        <code>{{modified_date}}</code>
                        <code>{{post_url}}</code>
                    </div>
                    <div class="variable-group">
                        <strong><?php _e('Author Variables:', 'kata-schema-markup'); ?></strong>
                        <code>{{author_name}}</code>
                        <code>{{author_email}}</code>
                        <code>{{author_url}}</code>
                    </div>
                    <div class="variable-group">
                        <strong><?php _e('Site Variables:', 'kata-schema-markup'); ?></strong>
                        <code>{{site_name}}</code>
                        <code>{{site_description}}</code>
                        <code>{{site_url}}</code>
                        <code>{{organization_name}}</code>
                        <code>{{organization_logo}}</code>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Custom Schema Tab -->
        <div id="tab-custom" class="tab-content">
            <p class="description">
                <?php _e('Enter custom JSON-LD schema markup. This will override the automatically generated schema.', 'kata-schema-markup'); ?>
            </p>
            
            <div class="kata-schema-editor-wrapper">
                <div class="kata-schema-editor-toolbar">
                    <button type="button" class="button" id="kata-schema-format-json">
                        <?php _e('Format JSON', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="kata-schema-validate-json">
                        <?php _e('Validate', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="kata-schema-reset-json">
                        <?php _e('Reset', 'kata-schema-markup'); ?>
                    </button>
                </div>
                
                <textarea name="kata_schema_data" id="kata_schema_data" rows="20" class="widefat code"><?php 
                    echo esc_textarea($schema_data); 
                ?></textarea>
                
                <div id="kata-schema-json-messages" class="kata-schema-messages"></div>
            </div>
            
            <div class="kata-schema-template-selector">
                <h4><?php _e('Schema Templates', 'kata-schema-markup'); ?></h4>
                <select id="kata-schema-template-select">
                    <option value=""><?php _e('Select a template...', 'kata-schema-markup'); ?></option>
                    <?php foreach ($schema_types as $type_key => $type_data): ?>
                        <option value="<?php echo esc_attr($type_key); ?>">
                            <?php echo esc_html($type_data['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="button" id="kata-schema-load-template">
                    <?php _e('Load Template', 'kata-schema-markup'); ?>
                </button>
            </div>
        </div>
        
        <!-- Preview Tab -->
        <div id="tab-preview" class="tab-content">
            <div class="kata-schema-preview-toolbar">
                <button type="button" class="button button-primary" id="kata-schema-generate-preview">
                    <?php _e('Generate Preview', 'kata-schema-markup'); ?>
                </button>
                <button type="button" class="button" id="kata-schema-copy-preview">
                    <?php _e('Copy to Clipboard', 'kata-schema-markup'); ?>
                </button>
            </div>
            
            <div id="kata-schema-preview-content" class="kata-schema-preview">
                <p class="description"><?php _e('Click "Generate Preview" to see how the schema will look.', 'kata-schema-markup'); ?></p>
            </div>
        </div>
        
        <!-- Validation Tab -->
        <div id="tab-validation" class="tab-content">
            <div class="kata-schema-validation-toolbar">
                <button type="button" class="button button-primary" id="kata-schema-validate">
                    <?php _e('Validate Schema', 'kata-schema-markup'); ?>
                </button>
                <button type="button" class="button" id="kata-schema-test-google">
                    <?php _e('Test with Google', 'kata-schema-markup'); ?>
                </button>
            </div>
            
            <div id="kata-schema-validation-results" class="kata-schema-validation">
                <p class="description"><?php _e('Click "Validate Schema" to check for errors and warnings.', 'kata-schema-markup'); ?></p>
            </div>
            
            <div class="kata-schema-validation-help">
                <h4><?php _e('Validation Tips', 'kata-schema-markup'); ?></h4>
                <ul>
                    <li><?php _e('Ensure all required properties are present for the selected schema type', 'kata-schema-markup'); ?></li>
                    <li><?php _e('Use valid URLs for image and link properties', 'kata-schema-markup'); ?></li>
                    <li><?php _e('Date properties should be in ISO 8601 format (YYYY-MM-DD or YYYY-MM-DDTHH:MM:SS)', 'kata-schema-markup'); ?></li>
                    <li><?php _e('Include recommended properties to improve rich snippet appearance', 'kata-schema-markup'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.kata-schema-tabs .nav-tab-wrapper {
    border-bottom: 1px solid #ccd0d4;
    margin-bottom: 20px;
}

.kata-schema-tabs .tab-content {
    display: none;
}

.kata-schema-tabs .tab-content.active {
    display: block;
}

.kata-schema-additional-fields {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
}

.kata-schema-variables-help {
    margin-top: 20px;
    padding: 15px;
    background: #e7f3ff;
    border-left: 4px solid #0073aa;
}

.kata-schema-variables-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 10px;
}

.variable-group code {
    display: block;
    font-size: 11px;
    margin: 2px 0;
    padding: 2px 4px;
    background: rgba(0,0,0,0.1);
    border-radius: 3px;
}

.kata-schema-editor-wrapper {
    position: relative;
}

.kata-schema-editor-toolbar {
    margin-bottom: 10px;
}

.kata-schema-editor-toolbar .button {
    margin-right: 10px;
}

.kata-schema-messages {
    margin-top: 10px;
    min-height: 20px;
}

.kata-schema-messages .notice {
    margin: 5px 0;
    padding: 8px 12px;
}

.kata-schema-template-selector {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
}

.kata-schema-template-selector select {
    margin-right: 10px;
    width: 200px;
}

.kata-schema-preview-toolbar,
.kata-schema-validation-toolbar {
    margin-bottom: 15px;
}

.kata-schema-preview,
.kata-schema-validation {
    background: #f9f9f9;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
    padding: 15px;
    min-height: 200px;
}

.kata-schema-preview pre {
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 4px;
    overflow-x: auto;
    font-size: 12px;
    line-height: 1.4;
}

.kata-schema-validation-help {
    margin-top: 20px;
    padding: 15px;
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}

.kata-schema-validation-help ul {
    margin: 10px 0 0 20px;
}

.kata-schema-validation-score {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 15px;
}

.kata-schema-validation-score.good {
    color: #46b450;
}

.kata-schema-validation-score.warning {
    color: #ffb900;
}

.kata-schema-validation-score.error {
    color: #dc3232;
}

.kata-schema-validation-errors,
.kata-schema-validation-warnings {
    margin: 15px 0;
}

.kata-schema-validation-errors h5,
.kata-schema-validation-warnings h5 {
    margin: 0 0 10px;
}

.kata-schema-validation-errors ul,
.kata-schema-validation-warnings ul {
    margin: 0 0 0 20px;
}

.kata-schema-validation-errors li {
    color: #dc3232;
    margin-bottom: 5px;
}

.kata-schema-validation-warnings li {
    color: #ffb900;
    margin-bottom: 5px;
}

#schema-type-description {
    margin-top: 8px;
}

@media (max-width: 782px) {
    .kata-schema-variables-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-schema-template-selector select {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab navigation
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        var target = $(this).attr('href');
        
        // Update tab states
        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        
        // Update content
        $('.tab-content').removeClass('active');
        $(target).addClass('active');
    });
    
    // Schema type change
    $('#kata_schema_type').on('change', function() {
        var selectedType = $(this).val();
        var description = $(this).find('option:selected').data('description');
        
        if (description) {
            $('#schema-type-description').html('<p>' + description + '</p>').show();
        } else {
            $('#schema-type-description').hide();
        }
        
        // Show/hide additional fields based on type
        $('.article-fields').toggle(selectedType === 'article' || selectedType === '');
    });
    
    // Initialize CodeMirror for JSON editor
    if (typeof wp !== 'undefined' && wp.codeEditor) {
        var editorSettings = wp.codeEditor.defaultSettings ? 
            _.clone(wp.codeEditor.defaultSettings) : {};
        editorSettings.codemirror = _.extend(
            {},
            editorSettings.codemirror,
            {
                mode: 'application/json',
                lineNumbers: true,
                theme: 'default',
                indentUnit: 2,
                tabSize: 2
            }
        );
        
        var editor = wp.codeEditor.initialize($('#kata_schema_data'), editorSettings);
        
        // Format JSON button
        $('#kata-schema-format-json').on('click', function() {
            try {
                var json = JSON.parse(editor.codemirror.getValue());
                var formatted = JSON.stringify(json, null, 2);
                editor.codemirror.setValue(formatted);
                showMessage('JSON formatted successfully!', 'success');
            } catch (e) {
                showMessage('Invalid JSON: ' + e.message, 'error');
            }
        });
    }
    
    // Validation
    $('#kata-schema-validate, #kata-schema-validate-json').on('click', function() {
        var schemaData = $('#kata_schema_data').val();
        
        if (!schemaData.trim()) {
            showMessage('Please enter schema JSON to validate.', 'error');
            return;
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_validate_schema',
                schema: schemaData,
                nonce: $('#kata_schema_metabox_nonce').val()
            },
            beforeSend: function() {
                showMessage('Validating schema...', 'info');
            },
            success: function(response) {
                if (response.success) {
                    displayValidationResults(response.data);
                } else {
                    showMessage('Validation failed: ' + response.data.message, 'error');
                }
            },
            error: function() {
                showMessage('Error occurred during validation.', 'error');
            }
        });
    });
    
    // Preview generation
    $('#kata-schema-generate-preview').on('click', function() {
        var postId = $('#post_ID').val();
        var schemaType = $('#kata_schema_type').val() || 'article';
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_generate_schema_preview',
                post_id: postId,
                schema_type: schemaType,
                nonce: $('#kata_schema_metabox_nonce').val()
            },
            beforeSend: function() {
                $('#kata-schema-preview-content').html('<p>Generating preview...</p>');
            },
            success: function(response) {
                if (response.success) {
                    $('#kata-schema-preview-content').html(
                        '<pre><code>' + response.data.schema + '</code></pre>'
                    );
                } else {
                    $('#kata-schema-preview-content').html(
                        '<p class="error">Failed to generate preview: ' + response.data.message + '</p>'
                    );
                }
            },
            error: function() {
                $('#kata-schema-preview-content').html(
                    '<p class="error">Error occurred while generating preview.</p>'
                );
            }
        });
    });
    
    // Copy to clipboard
    $('#kata-schema-copy-preview').on('click', function() {
        var previewText = $('#kata-schema-preview-content pre code').text();
        
        if (previewText) {
            navigator.clipboard.writeText(previewText).then(function() {
                showMessage('Schema copied to clipboard!', 'success');
            });
        }
    });
    
    // Helper functions
    function showMessage(message, type) {
        var messageClass = 'notice notice-' + (type === 'error' ? 'error' : (type === 'success' ? 'success' : 'info'));
        var messageHtml = '<div class="' + messageClass + '"><p>' + message + '</p></div>';
        
        $('#kata-schema-json-messages').html(messageHtml);
        
        setTimeout(function() {
            $('#kata-schema-json-messages').empty();
        }, 5000);
    }
    
    function displayValidationResults(data) {
        var html = '';
        
        if (data.validation) {
            var validation = data.validation;
            var scoreClass = validation.score >= 80 ? 'good' : (validation.score >= 60 ? 'warning' : 'error');
            
            html += '<div class="kata-schema-validation-score ' + scoreClass + '">';
            html += 'Validation Score: ' + validation.score + '/100';
            html += '</div>';
            
            if (validation.errors && validation.errors.length > 0) {
                html += '<div class="kata-schema-validation-errors">';
                html += '<h5>Errors:</h5><ul>';
                validation.errors.forEach(function(error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul></div>';
            }
            
            if (validation.warnings && validation.warnings.length > 0) {
                html += '<div class="kata-schema-validation-warnings">';
                html += '<h5>Warnings:</h5><ul>';
                validation.warnings.forEach(function(warning) {
                    html += '<li>' + warning + '</li>';
                });
                html += '</ul></div>';
            }
            
            if (validation.valid && validation.errors.length === 0) {
                html += '<div class="notice notice-success"><p>Schema is valid!</p></div>';
            }
        }
        
        $('#kata-schema-validation-results').html(html);
        
        // Switch to validation tab if not already active
        if (!$('#tab-validation').hasClass('active')) {
            $('a[href="#tab-validation"]').click();
        }
    }
    
    // Initialize
    $('#kata_schema_type').trigger('change');
});
</script>
