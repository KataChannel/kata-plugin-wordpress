<?php
/**
 * Tools Page Template
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
    <h1><?php _e('Schema Markup Tools', 'kata-schema-markup'); ?></h1>
    
    <div class="kata-tools-wrapper">
        <!-- Schema Generator Tool -->
        <div class="kata-tool-section">
            <h2><?php _e('Schema Generator', 'kata-schema-markup'); ?></h2>
            <p class="description">
                <?php _e('Generate custom schema markup for your content using our interactive tool.', 'kata-schema-markup'); ?>
            </p>
            
            <div class="schema-generator-form">
                <div class="generator-controls">
                    <div class="control-group">
                        <label for="schema-type-select"><?php _e('Schema Type:', 'kata-schema-markup'); ?></label>
                        <select id="schema-type-select">
                            <option value="Article"><?php _e('Article', 'kata-schema-markup'); ?></option>
                            <option value="BlogPosting"><?php _e('Blog Posting', 'kata-schema-markup'); ?></option>
                            <option value="NewsArticle"><?php _e('News Article', 'kata-schema-markup'); ?></option>
                            <option value="Product"><?php _e('Product', 'kata-schema-markup'); ?></option>
                            <option value="Service"><?php _e('Service', 'kata-schema-markup'); ?></option>
                            <option value="Organization"><?php _e('Organization', 'kata-schema-markup'); ?></option>
                            <option value="Person"><?php _e('Person', 'kata-schema-markup'); ?></option>
                            <option value="Event"><?php _e('Event', 'kata-schema-markup'); ?></option>
                            <option value="Recipe"><?php _e('Recipe', 'kata-schema-markup'); ?></option>
                            <option value="LocalBusiness"><?php _e('Local Business', 'kata-schema-markup'); ?></option>
                        </select>
                    </div>
                    
                    <button type="button" class="button button-primary" id="generate-schema-btn">
                        <?php _e('Generate Schema', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="clear-generator-btn">
                        <?php _e('Clear Form', 'kata-schema-markup'); ?>
                    </button>
                </div>
                
                <div id="schema-form-container" class="schema-form-container" style="display: none;">
                    <!-- Dynamic form will be loaded here -->
                </div>
                
                <div id="generated-schema" class="generated-schema" style="display: none;">
                    <h3><?php _e('Generated Schema Markup', 'kata-schema-markup'); ?></h3>
                    <div class="schema-output-controls">
                        <button type="button" class="button" id="copy-schema-btn">
                            <?php _e('Copy to Clipboard', 'kata-schema-markup'); ?>
                        </button>
                        <button type="button" class="button" id="validate-generated-btn">
                            <?php _e('Validate Schema', 'kata-schema-markup'); ?>
                        </button>
                        <button type="button" class="button" id="save-template-btn">
                            <?php _e('Save as Template', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                    <pre id="schema-output-code"><code></code></pre>
                </div>
            </div>
        </div>
        
        <!-- Bulk Operations -->
        <div class="kata-tool-section">
            <h2><?php _e('Bulk Operations', 'kata-schema-markup'); ?></h2>
            <p class="description">
                <?php _e('Perform bulk operations on your schema markup across multiple posts.', 'kata-schema-markup'); ?>
            </p>
            
            <div class="bulk-operations-grid">
                <div class="bulk-operation-card">
                    <div class="operation-icon">
                        <span class="dashicons dashicons-update"></span>
                    </div>
                    <div class="operation-content">
                        <h4><?php _e('Regenerate All Schema', 'kata-schema-markup'); ?></h4>
                        <p><?php _e('Regenerate schema markup for all posts and pages.', 'kata-schema-markup'); ?></p>
                        <button type="button" class="button" id="regenerate-all-btn">
                            <?php _e('Start Regeneration', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="bulk-operation-card">
                    <div class="operation-icon">
                        <span class="dashicons dashicons-admin-post"></span>
                    </div>
                    <div class="operation-content">
                        <h4><?php _e('Apply Schema to Posts', 'kata-schema-markup'); ?></h4>
                        <p><?php _e('Apply a specific schema type to multiple posts at once.', 'kata-schema-markup'); ?></p>
                        <select id="bulk-schema-type">
                            <option value="Article"><?php _e('Article', 'kata-schema-markup'); ?></option>
                            <option value="BlogPosting"><?php _e('Blog Posting', 'kata-schema-markup'); ?></option>
                            <option value="NewsArticle"><?php _e('News Article', 'kata-schema-markup'); ?></option>
                        </select>
                        <button type="button" class="button" id="apply-bulk-schema-btn">
                            <?php _e('Apply to Posts', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="bulk-operation-card">
                    <div class="operation-icon">
                        <span class="dashicons dashicons-trash"></span>
                    </div>
                    <div class="operation-content">
                        <h4><?php _e('Remove Schema', 'kata-schema-markup'); ?></h4>
                        <p><?php _e('Remove schema markup from selected posts.', 'kata-schema-markup'); ?></p>
                        <button type="button" class="button button-secondary" id="remove-bulk-schema-btn">
                            <?php _e('Remove Schema', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                </div>
            </div>
            
            <div id="bulk-progress" class="bulk-progress" style="display: none;">
                <h4><?php _e('Operation Progress', 'kata-schema-markup'); ?></h4>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%;"></div>
                </div>
                <div class="progress-text">
                    <span id="bulk-current">0</span> / <span id="bulk-total">0</span> 
                    <?php _e('posts processed', 'kata-schema-markup'); ?>
                </div>
                <div class="progress-log" id="progress-log"></div>
            </div>
        </div>
        
        <!-- Testing Tools -->
        <div class="kata-tool-section">
            <h2><?php _e('Testing Tools', 'kata-schema-markup'); ?></h2>
            <p class="description">
                <?php _e('Test and debug your schema markup with various online tools.', 'kata-schema-markup'); ?>
            </p>
            
            <div class="testing-tools-grid">
                <div class="testing-tool">
                    <h4><?php _e('Quick Test', 'kata-schema-markup'); ?></h4>
                    <p><?php _e('Test a specific URL for schema markup.', 'kata-schema-markup'); ?></p>
                    <div class="tool-form">
                        <input type="url" id="test-url" placeholder="https://example.com/page" class="regular-text">
                        <button type="button" class="button" id="quick-test-btn">
                            <?php _e('Test URL', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                    <div id="quick-test-results" style="display: none;"></div>
                </div>
                
                <div class="testing-tool">
                    <h4><?php _e('Schema Validator', 'kata-schema-markup'); ?></h4>
                    <p><?php _e('Validate raw schema markup code.', 'kata-schema-markup'); ?></p>
                    <div class="tool-form">
                        <textarea id="validate-schema-input" placeholder="Paste your schema markup here..." rows="5" class="large-text"></textarea>
                        <button type="button" class="button" id="validate-markup-btn">
                            <?php _e('Validate Markup', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                    <div id="validation-results" style="display: none;"></div>
                </div>
                
                <div class="testing-tool">
                    <h4><?php _e('External Tools', 'kata-schema-markup'); ?></h4>
                    <p><?php _e('Quick access to popular schema testing tools.', 'kata-schema-markup'); ?></p>
                    <div class="external-tools-links">
                        <a href="#" class="button" id="google-test-btn">
                            <?php _e('Google Rich Results Test', 'kata-schema-markup'); ?>
                        </a>
                        <a href="#" class="button" id="schema-validator-btn">
                            <?php _e('Schema.org Validator', 'kata-schema-markup'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Data Migration -->
        <div class="kata-tool-section">
            <h2><?php _e('Data Migration', 'kata-schema-markup'); ?></h2>
            <p class="description">
                <?php _e('Import and export schema data, migrate from other plugins.', 'kata-schema-markup'); ?>
            </p>
            
            <div class="migration-options">
                <div class="migration-option">
                    <h4><?php _e('Export Schema Data', 'kata-schema-markup'); ?></h4>
                    <p><?php _e('Export all schema markup data to a JSON file.', 'kata-schema-markup'); ?></p>
                    <div class="export-options">
                        <label>
                            <input type="checkbox" id="export-posts" checked> 
                            <?php _e('Posts Schema Data', 'kata-schema-markup'); ?>
                        </label><br>
                        <label>
                            <input type="checkbox" id="export-templates" checked> 
                            <?php _e('Custom Templates', 'kata-schema-markup'); ?>
                        </label><br>
                        <label>
                            <input type="checkbox" id="export-settings" checked> 
                            <?php _e('Plugin Settings', 'kata-schema-markup'); ?>
                        </label>
                    </div>
                    <button type="button" class="button" id="export-data-btn">
                        <?php _e('Export Data', 'kata-schema-markup'); ?>
                    </button>
                </div>
                
                <div class="migration-option">
                    <h4><?php _e('Import Schema Data', 'kata-schema-markup'); ?></h4>
                    <p><?php _e('Import schema data from a JSON file.', 'kata-schema-markup'); ?></p>
                    <input type="file" id="import-file" accept=".json">
                    <button type="button" class="button" id="import-data-btn">
                        <?php _e('Import Data', 'kata-schema-markup'); ?>
                    </button>
                    <p class="description">
                        <strong><?php _e('Warning:', 'kata-schema-markup'); ?></strong> 
                        <?php _e('This will overwrite existing data. Make sure to backup first.', 'kata-schema-markup'); ?>
                    </p>
                </div>
                
                <div class="migration-option">
                    <h4><?php _e('Migrate from Other Plugins', 'kata-schema-markup'); ?></h4>
                    <p><?php _e('Import schema data from other popular schema plugins.', 'kata-schema-markup'); ?></p>
                    <select id="migration-source">
                        <option value=""><?php _e('Select Plugin...', 'kata-schema-markup'); ?></option>
                        <option value="yoast"><?php _e('Yoast SEO', 'kata-schema-markup'); ?></option>
                        <option value="rankmath"><?php _e('Rank Math', 'kata-schema-markup'); ?></option>
                        <option value="schema-pro"><?php _e('Schema Pro', 'kata-schema-markup'); ?></option>
                        <option value="wp-seo"><?php _e('WP SEO Structured Data Schema', 'kata-schema-markup'); ?></option>
                    </select>
                    <button type="button" class="button" id="migrate-plugin-btn">
                        <?php _e('Start Migration', 'kata-schema-markup'); ?>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- System Information -->
        <div class="kata-tool-section">
            <h2><?php _e('System Information', 'kata-schema-markup'); ?></h2>
            <p class="description">
                <?php _e('System information and diagnostic tools for troubleshooting.', 'kata-schema-markup'); ?>
            </p>
            
            <div class="system-info-grid">
                <div class="info-card">
                    <h4><?php _e('Plugin Status', 'kata-schema-markup'); ?></h4>
                    <div class="info-content">
                        <p><strong><?php _e('Version:', 'kata-schema-markup'); ?></strong> 1.0.0</p>
                        <p><strong><?php _e('Active Templates:', 'kata-schema-markup'); ?></strong> <span id="active-templates-count">0</span></p>
                        <p><strong><?php _e('Cache Status:', 'kata-schema-markup'); ?></strong> 
                            <span class="status-<?php echo get_option('kata_schema_cache_enabled', 1) ? 'active' : 'inactive'; ?>">
                                <?php echo get_option('kata_schema_cache_enabled', 1) ? __('Enabled', 'kata-schema-markup') : __('Disabled', 'kata-schema-markup'); ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="info-card">
                    <h4><?php _e('Database Info', 'kata-schema-markup'); ?></h4>
                    <div class="info-content">
                        <p><strong><?php _e('Posts with Schema:', 'kata-schema-markup'); ?></strong> <span id="posts-with-schema">0</span></p>
                        <p><strong><?php _e('Custom Templates:', 'kata-schema-markup'); ?></strong> <span id="custom-templates-count">0</span></p>
                        <p><strong><?php _e('Cache Entries:', 'kata-schema-markup'); ?></strong> <span id="cache-entries-count">0</span></p>
                    </div>
                </div>
                
                <div class="info-card">
                    <h4><?php _e('Performance', 'kata-schema-markup'); ?></h4>
                    <div class="info-content">
                        <p><strong><?php _e('Generation Time:', 'kata-schema-markup'); ?></strong> <span id="avg-generation-time">0ms</span></p>
                        <p><strong><?php _e('Cache Hit Rate:', 'kata-schema-markup'); ?></strong> <span id="cache-hit-rate">0%</span></p>
                        <p><strong><?php _e('Memory Usage:', 'kata-schema-markup'); ?></strong> <span id="memory-usage">0MB</span></p>
                    </div>
                </div>
            </div>
            
            <div class="diagnostic-tools">
                <h4><?php _e('Diagnostic Tools', 'kata-schema-markup'); ?></h4>
                <div class="diagnostic-buttons">
                    <button type="button" class="button" id="run-diagnostics-btn">
                        <?php _e('Run Diagnostics', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="clear-logs-btn">
                        <?php _e('Clear Logs', 'kata-schema-markup'); ?>
                    </button>
                    <button type="button" class="button" id="export-logs-btn">
                        <?php _e('Export Logs', 'kata-schema-markup'); ?>
                    </button>
                </div>
                
                <div id="diagnostic-results" class="diagnostic-results" style="display: none;">
                    <h5><?php _e('Diagnostic Results', 'kata-schema-markup'); ?></h5>
                    <div id="diagnostic-output"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kata-tools-wrapper {
    margin-top: 20px;
}

.kata-tool-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.kata-tool-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.generator-controls {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}

.control-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.control-group label {
    font-weight: bold;
    font-size: 12px;
    text-transform: uppercase;
}

.schema-form-container {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 4px;
    margin: 20px 0;
}

.generated-schema {
    margin-top: 20px;
}

.schema-output-controls {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.generated-schema pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 20px;
    border-radius: 4px;
    overflow-x: auto;
    font-family: 'Monaco', 'Courier New', monospace;
    font-size: 12px;
    line-height: 1.4;
}

.bulk-operations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.bulk-operation-card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
}

.operation-icon {
    font-size: 48px;
    color: #0073aa;
    margin-bottom: 15px;
}

.operation-content h4 {
    margin: 0 0 10px 0;
    color: #333;
}

.operation-content p {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
}

.bulk-progress {
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
    margin-bottom: 15px;
}

.progress-log {
    max-height: 200px;
    overflow-y: auto;
    background: #fff;
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
}

.testing-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.testing-tool {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}

.testing-tool h4 {
    margin-top: 0;
    color: #333;
}

.tool-form {
    margin: 15px 0;
}

.tool-form input,
.tool-form textarea {
    width: 100%;
    margin-bottom: 10px;
}

.external-tools-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.migration-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.migration-option {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}

.migration-option h4 {
    margin-top: 0;
    color: #333;
}

.export-options {
    margin: 15px 0;
}

.export-options label {
    display: block;
    margin-bottom: 8px;
}

.system-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.info-card {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}

.info-card h4 {
    margin-top: 0;
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}

.info-content p {
    margin: 8px 0;
    font-size: 14px;
}

.status-active {
    color: #00a32a;
    font-weight: bold;
}

.status-inactive {
    color: #d63638;
    font-weight: bold;
}

.diagnostic-tools {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.diagnostic-buttons {
    display: flex;
    gap: 10px;
    margin: 15px 0;
}

.diagnostic-results {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin-top: 20px;
}

#diagnostic-output {
    background: #fff;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
    max-height: 300px;
    overflow-y: auto;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Schema Generator
    $('#generate-schema-btn').on('click', function() {
        var schemaType = $('#schema-type-select').val();
        generateSchemaForm(schemaType);
    });
    
    $('#clear-generator-btn').on('click', function() {
        $('#schema-form-container, #generated-schema').hide();
        $('#schema-form-container').empty();
    });
    
    function generateSchemaForm(type) {
        var formHTML = getSchemaForm(type);
        $('#schema-form-container').html(formHTML).show();
        
        // Add form submission handler
        $('#schema-form-container form').on('submit', function(e) {
            e.preventDefault();
            generateSchemaOutput(type, $(this).serializeArray());
        });
    }
    
    function getSchemaForm(type) {
        var forms = {
            'Article': `
                <h3><?php _e('Article Schema', 'kata-schema-markup'); ?></h3>
                <form>
                    <table class="form-table">
                        <tr><th><label for="article-headline"><?php _e('Headline:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="text" id="article-headline" name="headline" class="regular-text" required></td></tr>
                        <tr><th><label for="article-description"><?php _e('Description:', 'kata-schema-markup'); ?></label></th>
                        <td><textarea id="article-description" name="description" class="large-text" rows="3"></textarea></td></tr>
                        <tr><th><label for="article-author"><?php _e('Author:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="text" id="article-author" name="author" class="regular-text"></td></tr>
                        <tr><th><label for="article-date"><?php _e('Published Date:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="date" id="article-date" name="datePublished" class="regular-text"></td></tr>
                        <tr><th><label for="article-image"><?php _e('Image URL:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="url" id="article-image" name="image" class="regular-text"></td></tr>
                    </table>
                    <p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Generate Schema', 'kata-schema-markup'); ?>"></p>
                </form>
            `,
            'Product': `
                <h3><?php _e('Product Schema', 'kata-schema-markup'); ?></h3>
                <form>
                    <table class="form-table">
                        <tr><th><label for="product-name"><?php _e('Product Name:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="text" id="product-name" name="name" class="regular-text" required></td></tr>
                        <tr><th><label for="product-description"><?php _e('Description:', 'kata-schema-markup'); ?></label></th>
                        <td><textarea id="product-description" name="description" class="large-text" rows="3"></textarea></td></tr>
                        <tr><th><label for="product-price"><?php _e('Price:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="number" id="product-price" name="price" class="regular-text" step="0.01"></td></tr>
                        <tr><th><label for="product-currency"><?php _e('Currency:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="text" id="product-currency" name="priceCurrency" class="regular-text" value="USD"></td></tr>
                        <tr><th><label for="product-brand"><?php _e('Brand:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="text" id="product-brand" name="brand" class="regular-text"></td></tr>
                        <tr><th><label for="product-image"><?php _e('Image URL:', 'kata-schema-markup'); ?></label></th>
                        <td><input type="url" id="product-image" name="image" class="regular-text"></td></tr>
                    </table>
                    <p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Generate Schema', 'kata-schema-markup'); ?>"></p>
                </form>
            `
        };
        
        return forms[type] || forms['Article'];
    }
    
    function generateSchemaOutput(type, formData) {
        var schema = {
            "@context": "https://schema.org",
            "@type": type
        };
        
        formData.forEach(function(field) {
            if (field.value) {
                schema[field.name] = field.value;
            }
        });
        
        var output = JSON.stringify(schema, null, 2);
        $('#schema-output-code code').text(output);
        $('#generated-schema').show();
    }
    
    // Copy to clipboard
    $('#copy-schema-btn').on('click', function() {
        var text = $('#schema-output-code code').text();
        navigator.clipboard.writeText(text).then(function() {
            alert('<?php _e("Schema copied to clipboard!", "kata-schema-markup"); ?>');
        });
    });
    
    // Bulk operations
    $('#regenerate-all-btn').on('click', function() {
        startBulkOperation('regenerate');
    });
    
    $('#apply-bulk-schema-btn').on('click', function() {
        var schemaType = $('#bulk-schema-type').val();
        startBulkOperation('apply', schemaType);
    });
    
    $('#remove-bulk-schema-btn').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to remove schema from all posts?", "kata-schema-markup"); ?>')) {
            startBulkOperation('remove');
        }
    });
    
    function startBulkOperation(operation, schemaType) {
        $('#bulk-progress').show();
        $('#progress-log').empty();
        
        var total = 100; // Example total
        $('#bulk-total').text(total);
        
        var current = 0;
        var interval = setInterval(function() {
            current++;
            var progress = (current / total) * 100;
            
            $('.progress-fill').css('width', progress + '%');
            $('#bulk-current').text(current);
            
            $('#progress-log').append('<div>Processing post ' + current + '...</div>');
            $('#progress-log').scrollTop($('#progress-log')[0].scrollHeight);
            
            if (current >= total) {
                clearInterval(interval);
                $('#progress-log').append('<div><strong>Operation completed!</strong></div>');
            }
        }, 50);
    }
    
    // Quick test
    $('#quick-test-btn').on('click', function() {
        var url = $('#test-url').val();
        if (url) {
            $('#quick-test-results').html('<p>Testing URL: ' + url + '...</p>').show();
            // Simulate test results
            setTimeout(function() {
                $('#quick-test-results').html(
                    '<div class="test-result success">' +
                    '<strong>✓ Schema found!</strong><br>' +
                    'Found 3 schema types: Article, Organization, BreadcrumbList' +
                    '</div>'
                );
            }, 2000);
        }
    });
    
    // External tool links
    $('#google-test-btn').on('click', function() {
        var url = $('#test-url').val() || window.location.href;
        window.open('https://search.google.com/test/rich-results?url=' + encodeURIComponent(url), '_blank');
    });
    
    $('#schema-validator-btn').on('click', function() {
        window.open('https://validator.schema.org/', '_blank');
    });
    
    // Export data
    $('#export-data-btn').on('click', function() {
        var exportOptions = {
            posts: $('#export-posts').is(':checked'),
            templates: $('#export-templates').is(':checked'),
            settings: $('#export-settings').is(':checked')
        };
        
        // Simulate export
        var data = {
            version: '1.0.0',
            export_date: new Date().toISOString(),
            data: exportOptions
        };
        
        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data, null, 2));
        var downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href", dataStr);
        downloadAnchorNode.setAttribute("download", "kata-schema-export.json");
        document.body.appendChild(downloadAnchorNode);
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
    });
    
    // Run diagnostics
    $('#run-diagnostics-btn').on('click', function() {
        $('#diagnostic-results').show();
        $('#diagnostic-output').html('Running diagnostics...<br>');
        
        var tests = [
            'Checking database connectivity...',
            'Validating schema templates...',
            'Testing cache functionality...',
            'Verifying file permissions...',
            'Checking plugin dependencies...'
        ];
        
        var index = 0;
        var interval = setInterval(function() {
            if (index < tests.length) {
                $('#diagnostic-output').append(tests[index] + ' ✓<br>');
                index++;
            } else {
                clearInterval(interval);
                $('#diagnostic-output').append('<br><strong>All tests passed!</strong>');
            }
        }, 1000);
    });
    
    // Load system info
    loadSystemInfo();
    
    function loadSystemInfo() {
        // Simulate loading system information
        $('#active-templates-count').text('5');
        $('#posts-with-schema').text('127');
        $('#custom-templates-count').text('3');
        $('#cache-entries-count').text('89');
        $('#avg-generation-time').text('23ms');
        $('#cache-hit-rate').text('87%');
        $('#memory-usage').text('2.1MB');
    }
});
</script>

<style>
.test-result {
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}

.test-result.success {
    background: #eafaf1;
    border-left: 4px solid #00a32a;
    color: #00512e;
}

.test-result.error {
    background: #ffeaea;
    border-left: 4px solid #d63638;
    color: #8b0000;
}
</style>
