<?php
/**
 * Validation Page Template
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
    <h1><?php _e('Schema Validation', 'kata-schema-markup'); ?></h1>
    
    <div class="kata-validation-wrapper">
        <!-- Validation Tools -->
        <div class="kata-validation-tools">
            <div class="kata-validation-section">
                <h2><?php _e('URL Validation', 'kata-schema-markup'); ?></h2>
                <p class="description">
                    <?php _e('Test schema markup on any URL to check for errors and compliance with Schema.org standards.', 'kata-schema-markup'); ?>
                </p>
                
                <form method="post" class="kata-url-validation-form">
                    <?php wp_nonce_field('kata_validation_nonce', 'validation_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="validate_url"><?php _e('URL to Validate', 'kata-schema-markup'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="validate_url" name="validate_url" class="regular-text" 
                                       value="<?php echo isset($_POST['validate_url']) ? esc_url($_POST['validate_url']) : ''; ?>"
                                       placeholder="https://example.com/page-to-test" required>
                                <input type="submit" class="button button-primary" value="<?php _e('Validate Schema', 'kata-schema-markup'); ?>">
                            </td>
                        </tr>
                    </table>
                </form>
                
                <?php if (!empty($validation_results)): ?>
                    <div class="kata-validation-results">
                        <h3><?php _e('Validation Results', 'kata-schema-markup'); ?></h3>
                        
                        <div class="validation-summary">
                            <div class="validation-score">
                                <span class="score-label"><?php _e('Overall Score:', 'kata-schema-markup'); ?></span>
                                <span class="score-value"><?php echo esc_html($validation_results['score'] ?? 0); ?>/100</span>
                            </div>
                            <div class="validation-status">
                                <?php if (isset($validation_results['valid']) && $validation_results['valid']): ?>
                                    <span class="status-valid"><?php _e('Valid Schema Found', 'kata-schema-markup'); ?></span>
                                <?php else: ?>
                                    <span class="status-invalid"><?php _e('Issues Found', 'kata-schema-markup'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($validation_results['errors'])): ?>
                            <div class="validation-errors">
                                <h4><?php _e('Errors', 'kata-schema-markup'); ?></h4>
                                <ul class="validation-list error-list">
                                    <?php foreach ($validation_results['errors'] as $error): ?>
                                        <li><?php echo esc_html($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($validation_results['warnings'])): ?>
                            <div class="validation-warnings">
                                <h4><?php _e('Warnings', 'kata-schema-markup'); ?></h4>
                                <ul class="validation-list warning-list">
                                    <?php foreach ($validation_results['warnings'] as $warning): ?>
                                        <li><?php echo esc_html($warning); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($validation_results['schemas'])): ?>
                            <div class="validation-schemas">
                                <h4><?php _e('Found Schemas', 'kata-schema-markup'); ?></h4>
                                <div class="schemas-grid">
                                    <?php foreach ($validation_results['schemas'] as $schema): ?>
                                        <div class="schema-item">
                                            <div class="schema-type">
                                                <strong><?php echo esc_html($schema['@type'] ?? 'Unknown'); ?></strong>
                                            </div>
                                            <div class="schema-details">
                                                <?php if (isset($schema['name'])): ?>
                                                    <p><strong><?php _e('Name:', 'kata-schema-markup'); ?></strong> <?php echo esc_html($schema['name']); ?></p>
                                                <?php endif; ?>
                                                <?php if (isset($schema['headline'])): ?>
                                                    <p><strong><?php _e('Headline:', 'kata-schema-markup'); ?></strong> <?php echo esc_html($schema['headline']); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Batch Validation -->
            <div class="kata-validation-section">
                <h2><?php _e('Batch Validation', 'kata-schema-markup'); ?></h2>
                <p class="description">
                    <?php _e('Validate schema markup for multiple posts at once.', 'kata-schema-markup'); ?>
                </p>
                
                <div class="batch-validation-controls">
                    <button type="button" class="button" id="validate-all-posts">
                        <?php _e('Validate All Posts', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="validate-pages">
                        <?php _e('Validate All Pages', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="validate-recent">
                        <?php _e('Validate Recent Posts', 'kata-schema-markup'); ?>
                    </button>
                </div>
                
                <div id="batch-validation-results" class="batch-results" style="display: none;">
                    <div class="batch-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>
                        <div class="progress-text">
                            <span id="progress-current">0</span> / <span id="progress-total">0</span> 
                            <?php _e('posts validated', 'kata-schema-markup'); ?>
                        </div>
                    </div>
                    
                    <div class="batch-summary">
                        <div class="summary-stats">
                            <div class="stat-item">
                                <span class="stat-number" id="valid-count">0</span>
                                <span class="stat-label"><?php _e('Valid', 'kata-schema-markup'); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="warning-count">0</span>
                                <span class="stat-label"><?php _e('Warnings', 'kata-schema-markup'); ?></span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number" id="error-count">0</span>
                                <span class="stat-label"><?php _e('Errors', 'kata-schema-markup'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="batch-details">
                        <h4><?php _e('Validation Details', 'kata-schema-markup'); ?></h4>
                        <div id="validation-details-list"></div>
                    </div>
                </div>
            </div>
            
            <!-- External Tools -->
            <div class="kata-validation-section">
                <h2><?php _e('External Validation Tools', 'kata-schema-markup'); ?></h2>
                <p class="description">
                    <?php _e('Use external tools to validate and test your schema markup.', 'kata-schema-markup'); ?>
                </p>
                
                <div class="external-tools-grid">
                    <div class="tool-card">
                        <div class="tool-icon">
                            <span class="dashicons dashicons-search"></span>
                        </div>
                        <div class="tool-content">
                            <h4><?php _e('Google Rich Results Test', 'kata-schema-markup'); ?></h4>
                            <p><?php _e('Test how your pages appear in Google search results.', 'kata-schema-markup'); ?></p>
                            <a href="https://search.google.com/test/rich-results" target="_blank" class="button">
                                <?php _e('Open Tool', 'kata-schema-markup'); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="tool-card">
                        <div class="tool-icon">
                            <span class="dashicons dashicons-admin-tools"></span>
                        </div>
                        <div class="tool-content">
                            <h4><?php _e('Schema.org Validator', 'kata-schema-markup'); ?></h4>
                            <p><?php _e('Validate your markup against Schema.org standards.', 'kata-schema-markup'); ?></p>
                            <a href="https://validator.schema.org/" target="_blank" class="button">
                                <?php _e('Open Tool', 'kata-schema-markup'); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div class="tool-card">
                        <div class="tool-icon">
                            <span class="dashicons dashicons-analytics"></span>
                        </div>
                        <div class="tool-content">
                            <h4><?php _e('Structured Data Testing Tool', 'kata-schema-markup'); ?></h4>
                            <p><?php _e('Test and validate structured data markup.', 'kata-schema-markup'); ?></p>
                            <a href="https://search.google.com/structured-data/testing-tool" target="_blank" class="button">
                                <?php _e('Open Tool', 'kata-schema-markup'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Validation Settings -->
            <div class="kata-validation-section">
                <h2><?php _e('Validation Settings', 'kata-schema-markup'); ?></h2>
                
                <form method="post" class="validation-settings-form">
                    <?php wp_nonce_field('kata_validation_settings', 'validation_settings_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="auto_validation"><?php _e('Auto Validation', 'kata-schema-markup'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="auto_validation" name="auto_validation" value="1" 
                                       <?php checked(get_option('kata_schema_auto_validation', 1)); ?>>
                                <label for="auto_validation"><?php _e('Automatically validate schema when saving posts', 'kata-schema-markup'); ?></label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="validation_level"><?php _e('Validation Level', 'kata-schema-markup'); ?></label>
                            </th>
                            <td>
                                <select id="validation_level" name="validation_level">
                                    <option value="basic" <?php selected(get_option('kata_schema_validation_level', 'standard'), 'basic'); ?>>
                                        <?php _e('Basic - Essential validation only', 'kata-schema-markup'); ?>
                                    </option>
                                    <option value="standard" <?php selected(get_option('kata_schema_validation_level', 'standard'), 'standard'); ?>>
                                        <?php _e('Standard - Recommended validation', 'kata-schema-markup'); ?>
                                    </option>
                                    <option value="strict" <?php selected(get_option('kata_schema_validation_level', 'standard'), 'strict'); ?>>
                                        <?php _e('Strict - Comprehensive validation', 'kata-schema-markup'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="show_warnings"><?php _e('Show Warnings', 'kata-schema-markup'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="show_warnings" name="show_warnings" value="1" 
                                       <?php checked(get_option('kata_schema_show_warnings', 1)); ?>>
                                <label for="show_warnings"><?php _e('Display validation warnings in admin', 'kata-schema-markup'); ?></label>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="save_validation_settings" class="button-primary" 
                               value="<?php _e('Save Settings', 'kata-schema-markup'); ?>">
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.kata-validation-wrapper {
    margin-top: 20px;
}

.kata-validation-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.kata-validation-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.kata-url-validation-form .form-table td {
    display: flex;
    gap: 10px;
    align-items: center;
}

.kata-url-validation-form input[type="url"] {
    flex: 1;
}

.kata-validation-results {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}

.validation-summary {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #fff;
    border-radius: 4px;
}

.validation-score .score-value {
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.status-valid {
    color: #00a32a;
    font-weight: bold;
}

.status-invalid {
    color: #d63638;
    font-weight: bold;
}

.validation-list {
    list-style: none;
    padding: 0;
}

.validation-list li {
    padding: 8px 12px;
    margin: 5px 0;
    border-radius: 3px;
}

.error-list li {
    background: #ffeaea;
    border-left: 4px solid #d63638;
}

.warning-list li {
    background: #fff3cd;
    border-left: 4px solid #f0ad4e;
}

.schemas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.schema-item {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
}

.schema-type {
    font-size: 16px;
    margin-bottom: 10px;
    color: #0073aa;
}

.batch-validation-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.batch-results {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 4px;
    margin-top: 20px;
}

.progress-bar {
    width: 100%;
    height: 20px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: #0073aa;
    transition: width 0.3s ease;
}

.progress-text {
    text-align: center;
    font-size: 14px;
    color: #666;
}

.summary-stats {
    display: flex;
    gap: 30px;
    justify-content: center;
    margin: 20px 0;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #0073aa;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
}

.external-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.tool-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: box-shadow 0.3s ease;
}

.tool-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.tool-icon {
    font-size: 48px;
    color: #0073aa;
    margin-bottom: 15px;
}

.tool-content h4 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.tool-content p {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
}

.validation-settings-form .form-table {
    margin-top: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Batch validation handlers
    $('#validate-all-posts, #validate-pages, #validate-recent').on('click', function() {
        var type = $(this).attr('id').replace('validate-', '');
        startBatchValidation(type);
    });
    
    function startBatchValidation(type) {
        $('#batch-validation-results').show();
        
        // Reset counters
        $('#valid-count, #warning-count, #error-count').text('0');
        $('#progress-current').text('0');
        $('.progress-fill').css('width', '0%');
        $('#validation-details-list').empty();
        
        // Simulate batch validation (replace with actual AJAX calls)
        var total = type === 'recent' ? 10 : 50;
        $('#progress-total').text(total);
        
        var current = 0;
        var validCount = 0;
        var warningCount = 0;
        var errorCount = 0;
        
        var interval = setInterval(function() {
            current++;
            var progress = (current / total) * 100;
            
            $('.progress-fill').css('width', progress + '%');
            $('#progress-current').text(current);
            
            // Simulate random results
            var hasErrors = Math.random() < 0.2;
            var hasWarnings = Math.random() < 0.3;
            
            if (hasErrors) {
                errorCount++;
            } else if (hasWarnings) {
                warningCount++;
            } else {
                validCount++;
            }
            
            $('#valid-count').text(validCount);
            $('#warning-count').text(warningCount);
            $('#error-count').text(errorCount);
            
            // Add detail item
            var status = hasErrors ? 'error' : (hasWarnings ? 'warning' : 'valid');
            var statusText = hasErrors ? 'Errors found' : (hasWarnings ? 'Warnings found' : 'Valid');
            var statusClass = hasErrors ? 'error' : (hasWarnings ? 'warning' : 'success');
            
            $('#validation-details-list').append(
                '<div class="validation-detail-item ' + statusClass + '">' +
                '<strong>Post ' + current + '</strong>: ' + statusText +
                '</div>'
            );
            
            if (current >= total) {
                clearInterval(interval);
            }
        }, 200);
    }
});
</script>

<style>
.validation-detail-item {
    padding: 8px 12px;
    margin: 5px 0;
    border-radius: 3px;
}

.validation-detail-item.success {
    background: #eafaf1;
    border-left: 4px solid #00a32a;
}

.validation-detail-item.warning {
    background: #fff3cd;
    border-left: 4px solid #f0ad4e;
}

.validation-detail-item.error {
    background: #ffeaea;
    border-left: 4px solid #d63638;
}
</style>
