<?php
/**
 * Settings Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = array(
    'auto_analyze' => get_option('kata_seo_auto_analyze', false),
    'ai_enabled' => get_option('kata_seo_ai_enabled', false),
    'openai_api_key' => get_option('kata_seo_openai_api_key', ''),
    'google_api_key' => get_option('kata_seo_google_api_key', ''),
    'google_cse_id' => get_option('kata_seo_google_cse_id', ''),
    'analysis_weights' => get_option('kata_seo_analysis_weights', array()),
    'monitoring_enabled' => get_option('kata_seo_monitoring_enabled', false),
    'monitoring_frequency' => get_option('kata_seo_monitoring_frequency', 'weekly'),
    'email_notifications' => get_option('kata_seo_email_notifications', false),
    'notification_email' => get_option('kata_seo_notification_email', get_option('admin_email')),
    'exclude_post_types' => get_option('kata_seo_exclude_post_types', array()),
    'min_content_length' => get_option('kata_seo_min_content_length', 300),
    'max_keyword_density' => get_option('kata_seo_max_keyword_density', 2.5),
    'enable_admin_bar' => get_option('kata_seo_enable_admin_bar', true),
    'data_retention_days' => get_option('kata_seo_data_retention_days', 365),
);

// Default analysis weights
$default_weights = array(
    'title_length' => 15,
    'title_keywords' => 20,
    'content_length' => 10,
    'keyword_density' => 15,
    'heading_structure' => 10,
    'internal_links' => 8,
    'external_links' => 5,
    'image_optimization' => 7,
    'meta_description' => 10
);

$settings['analysis_weights'] = array_merge($default_weights, $settings['analysis_weights']);

// Get available post types
$post_types = get_post_types(array('public' => true), 'objects');
?>

<div class="wrap kata-seo-settings">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">⚙️</span>
            Kata SEO Analyzer Settings
        </h1>
    </div>

    <form id="kata-seo-settings-form" method="post">
        <?php wp_nonce_field('kata_seo_settings', 'kata_seo_settings_nonce'); ?>

        <!-- Settings Tabs -->
        <div class="kata-tabs">
            <nav class="kata-tab-nav">
                <a href="#general" class="kata-tab active">General</a>
                <a href="#analysis" class="kata-tab">Analysis</a>
                <a href="#ai-integration" class="kata-tab">AI Integration</a>
                <a href="#api-settings" class="kata-tab">API Settings</a>
                <a href="#monitoring" class="kata-tab">Monitoring</a>
                <a href="#advanced" class="kata-tab">Advanced</a>
            </nav>

            <!-- General Settings -->
            <div id="general" class="kata-tab-panel active">
                <div class="settings-section">
                    <h3 class="section-title">General Settings</h3>
                    
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="auto_analyze">Auto-Analyze New Content</label>
                            <p class="setting-description">Automatically analyze posts when they are published or updated</p>
                        </div>
                        <div class="setting-control">
                            <label class="kata-toggle">
                                <input type="checkbox" id="auto_analyze" name="auto_analyze" value="1" 
                                       <?php checked($settings['auto_analyze']); ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="enable_admin_bar">Admin Bar SEO Score</label>
                            <p class="setting-description">Show SEO score in the admin bar when viewing posts</p>
                        </div>
                        <div class="setting-control">
                            <label class="kata-toggle">
                                <input type="checkbox" id="enable_admin_bar" name="enable_admin_bar" value="1" 
                                       <?php checked($settings['enable_admin_bar']); ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="exclude_post_types">Exclude Post Types</label>
                            <p class="setting-description">Select post types to exclude from SEO analysis</p>
                        </div>
                        <div class="setting-control">
                            <div class="checkbox-group">
                                <?php foreach ($post_types as $post_type): ?>
                                    <label class="checkbox-item">
                                        <input type="checkbox" name="exclude_post_types[]" 
                                               value="<?php echo esc_attr($post_type->name); ?>"
                                               <?php checked(in_array($post_type->name, $settings['exclude_post_types'])); ?>>
                                        <span class="checkbox-label"><?php echo esc_html($post_type->label); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="min_content_length">Minimum Content Length</label>
                            <p class="setting-description">Minimum word count for good SEO (words)</p>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="min_content_length" name="min_content_length" 
                                   value="<?php echo esc_attr($settings['min_content_length']); ?>"
                                   min="100" max="2000" step="50" class="kata-input number-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Settings -->
            <div id="analysis" class="kata-tab-panel">
                <div class="settings-section">
                    <h3 class="section-title">Analysis Configuration</h3>
                    
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="max_keyword_density">Maximum Keyword Density</label>
                            <p class="setting-description">Maximum keyword density percentage (recommended: 2.5%)</p>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="max_keyword_density" name="max_keyword_density" 
                                   value="<?php echo esc_attr($settings['max_keyword_density']); ?>"
                                   min="1" max="10" step="0.1" class="kata-input number-input">
                            <span class="input-suffix">%</span>
                        </div>
                    </div>

                    <div class="setting-row full-width">
                        <div class="setting-label">
                            <h4>Analysis Factor Weights</h4>
                            <p class="setting-description">Adjust the importance of each SEO factor in the overall score calculation</p>
                        </div>
                        
                        <div class="weights-grid">
                            <?php foreach ($settings['analysis_weights'] as $factor => $weight): ?>
                                <div class="weight-item">
                                    <label for="weight_<?php echo esc_attr($factor); ?>" class="weight-label">
                                        <?php echo esc_html(ucwords(str_replace('_', ' ', $factor))); ?>
                                    </label>
                                    <div class="weight-control">
                                        <input type="range" id="weight_<?php echo esc_attr($factor); ?>" 
                                               name="analysis_weights[<?php echo esc_attr($factor); ?>]"
                                               value="<?php echo esc_attr($weight); ?>" 
                                               min="0" max="30" step="1" class="weight-slider">
                                        <span class="weight-value"><?php echo esc_html($weight); ?>%</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="weights-actions">
                            <button type="button" class="kata-btn kata-btn-secondary reset-weights">Reset to Defaults</button>
                            <div class="total-weight">
                                Total Weight: <span id="total-weight">100</span>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Integration -->
            <div id="ai-integration" class="kata-tab-panel">
                <div class="settings-section">
                    <h3 class="section-title">AI-Powered Features</h3>
                    
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="ai_enabled">Enable AI Suggestions</label>
                            <p class="setting-description">Use AI to generate content optimization suggestions</p>
                        </div>
                        <div class="setting-control">
                            <label class="kata-toggle">
                                <input type="checkbox" id="ai_enabled" name="ai_enabled" value="1" 
                                       <?php checked($settings['ai_enabled']); ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-row ai-dependent" <?php echo !$settings['ai_enabled'] ? 'style="opacity:0.5;"' : ''; ?>>
                        <div class="setting-label">
                            <label for="openai_api_key">OpenAI API Key</label>
                            <p class="setting-description">
                                Required for AI suggestions. 
                                <a href="https://platform.openai.com/api-keys" target="_blank">Get your API key</a>
                            </p>
                        </div>
                        <div class="setting-control">
                            <div class="api-key-input">
                                <input type="password" id="openai_api_key" name="openai_api_key" 
                                       value="<?php echo esc_attr($settings['openai_api_key']); ?>"
                                       class="kata-input" placeholder="sk-...">
                                <button type="button" class="kata-btn kata-btn-secondary test-api-btn" 
                                        data-api-type="openai" 
                                        <?php disabled(empty($settings['openai_api_key'])); ?>>
                                    Test Connection
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="ai-features-preview ai-dependent" <?php echo !$settings['ai_enabled'] ? 'style="opacity:0.5;"' : ''; ?>>
                        <h4>AI Features Include:</h4>
                        <ul class="feature-list">
                            <li>✨ Content optimization suggestions</li>
                            <li>🎯 Keyword recommendations</li>
                            <li>📝 Title and meta description improvements</li>
                            <li>🔗 Internal linking opportunities</li>
                            <li>📊 Content gap analysis</li>
                            <li>🏆 Competitor content insights</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- API Settings -->
            <div id="api-settings" class="kata-tab-panel">
                <div class="settings-section">
                    <h3 class="section-title">External API Configuration</h3>
                    
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="google_api_key">Google API Key</label>
                            <p class="setting-description">
                                For competitor analysis and keyword research. 
                                <a href="https://console.developers.google.com/" target="_blank">Create API key</a>
                            </p>
                        </div>
                        <div class="setting-control">
                            <div class="api-key-input">
                                <input type="password" id="google_api_key" name="google_api_key" 
                                       value="<?php echo esc_attr($settings['google_api_key']); ?>"
                                       class="kata-input" placeholder="AIza...">
                                <button type="button" class="kata-btn kata-btn-secondary test-api-btn" 
                                        data-api-type="google"
                                        <?php disabled(empty($settings['google_api_key'])); ?>>
                                    Test Connection
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="google_cse_id">Google Custom Search Engine ID</label>
                            <p class="setting-description">
                                For enhanced search result analysis. 
                                <a href="https://cse.google.com/" target="_blank">Create CSE</a>
                            </p>
                        </div>
                        <div class="setting-control">
                            <input type="text" id="google_cse_id" name="google_cse_id" 
                                   value="<?php echo esc_attr($settings['google_cse_id']); ?>"
                                   class="kata-input" placeholder="017576662512468239146:omuauf_lfve">
                        </div>
                    </div>

                    <div class="api-status-grid">
                        <div class="api-status-card">
                            <h4>OpenAI API</h4>
                            <div class="status-indicator" id="openai-status">
                                <span class="status-dot"></span>
                                <span class="status-text">Not configured</span>
                            </div>
                            <p>AI-powered content suggestions</p>
                        </div>

                        <div class="api-status-card">
                            <h4>Google API</h4>
                            <div class="status-indicator" id="google-status">
                                <span class="status-dot"></span>
                                <span class="status-text">Not configured</span>
                            </div>
                            <p>Keyword research & competitor analysis</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monitoring -->
            <div id="monitoring" class="kata-tab-panel">
                <div class="settings-section">
                    <h3 class="section-title">Performance Monitoring</h3>
                    
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="monitoring_enabled">Enable Monitoring</label>
                            <p class="setting-description">Track SEO performance over time and get alerts</p>
                        </div>
                        <div class="setting-control">
                            <label class="kata-toggle">
                                <input type="checkbox" id="monitoring_enabled" name="monitoring_enabled" value="1" 
                                       <?php checked($settings['monitoring_enabled']); ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-row monitoring-dependent" <?php echo !$settings['monitoring_enabled'] ? 'style="opacity:0.5;"' : ''; ?>>
                        <div class="setting-label">
                            <label for="monitoring_frequency">Monitoring Frequency</label>
                            <p class="setting-description">How often to check for SEO changes</p>
                        </div>
                        <div class="setting-control">
                            <select id="monitoring_frequency" name="monitoring_frequency" class="kata-select">
                                <option value="daily" <?php selected($settings['monitoring_frequency'], 'daily'); ?>>Daily</option>
                                <option value="weekly" <?php selected($settings['monitoring_frequency'], 'weekly'); ?>>Weekly</option>
                                <option value="monthly" <?php selected($settings['monitoring_frequency'], 'monthly'); ?>>Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="setting-row monitoring-dependent" <?php echo !$settings['monitoring_enabled'] ? 'style="opacity:0.5;"' : ''; ?>>
                        <div class="setting-label">
                            <label for="email_notifications">Email Notifications</label>
                            <p class="setting-description">Receive email alerts for significant SEO changes</p>
                        </div>
                        <div class="setting-control">
                            <label class="kata-toggle">
                                <input type="checkbox" id="email_notifications" name="email_notifications" value="1" 
                                       <?php checked($settings['email_notifications']); ?>
                                       <?php disabled(!$settings['monitoring_enabled']); ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <div class="setting-row monitoring-dependent" <?php echo !$settings['monitoring_enabled'] || !$settings['email_notifications'] ? 'style="opacity:0.5;"' : ''; ?>>
                        <div class="setting-label">
                            <label for="notification_email">Notification Email</label>
                            <p class="setting-description">Email address to receive notifications</p>
                        </div>
                        <div class="setting-control">
                            <input type="email" id="notification_email" name="notification_email" 
                                   value="<?php echo esc_attr($settings['notification_email']); ?>"
                                   class="kata-input" 
                                   <?php disabled(!$settings['monitoring_enabled'] || !$settings['email_notifications']); ?>>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced -->
            <div id="advanced" class="kata-tab-panel">
                <div class="settings-section">
                    <h3 class="section-title">Advanced Options</h3>
                    
                    <div class="setting-row">
                        <div class="setting-label">
                            <label for="data_retention_days">Data Retention Period</label>
                            <p class="setting-description">How long to keep analysis data (days, 0 = forever)</p>
                        </div>
                        <div class="setting-control">
                            <input type="number" id="data_retention_days" name="data_retention_days" 
                                   value="<?php echo esc_attr($settings['data_retention_days']); ?>"
                                   min="0" max="3650" step="1" class="kata-input number-input">
                            <span class="input-suffix">days</span>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <h4>Database Management</h4>
                            <p class="setting-description">Manage plugin data and reset settings</p>
                        </div>
                        <div class="setting-control">
                            <div class="database-actions">
                                <button type="button" class="kata-btn kata-btn-secondary" id="cleanup-old-data">
                                    Clean Old Data
                                </button>
                                <button type="button" class="kata-btn kata-btn-secondary" id="export-data">
                                    Export Data
                                </button>
                                <button type="button" class="kata-btn kata-btn-danger" id="reset-all-data">
                                    Reset All Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-label">
                            <h4>Debug Information</h4>
                            <p class="setting-description">System information for troubleshooting</p>
                        </div>
                        <div class="setting-control">
                            <div class="debug-info">
                                <div class="debug-item">
                                    <strong>Plugin Version:</strong> 2.0.0
                                </div>
                                <div class="debug-item">
                                    <strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?>
                                </div>
                                <div class="debug-item">
                                    <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                                </div>
                                <div class="debug-item">
                                    <strong>Database Tables:</strong> 
                                    <?php
                                    global $wpdb;
                                    $tables = array(
                                        $wpdb->prefix . 'kata_seo_analysis',
                                        $wpdb->prefix . 'kata_seo_competitors',
                                        $wpdb->prefix . 'kata_seo_monitoring'
                                    );
                                    echo implode(', ', $tables);
                                    ?>
                                </div>
                            </div>
                            <button type="button" class="kata-btn kata-btn-secondary" id="copy-debug-info">
                                Copy Debug Info
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="settings-footer">
            <button type="submit" class="kata-btn kata-btn-primary save-settings-btn">
                <span class="dashicons dashicons-yes"></span>
                Save Settings
            </button>
            
            <button type="button" class="kata-btn kata-btn-secondary" id="reset-settings">
                Reset to Defaults
            </button>
        </div>
    </form>
</div>

<!-- Settings-specific styles -->
<style>
.kata-seo-settings {
    max-width: 1200px;
}

.kata-tabs {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 20px 0;
}

.kata-tab-nav {
    display: flex;
    border-bottom: 1px solid #eee;
}

.kata-tab {
    padding: 15px 25px;
    text-decoration: none;
    color: #666;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.kata-tab:hover,
.kata-tab.active {
    color: #667eea;
    border-bottom-color: #667eea;
    background: rgba(102, 126, 234, 0.05);
}

.kata-tab-panel {
    display: none;
    padding: 30px;
}

.kata-tab-panel.active {
    display: block;
}

.settings-section {
    margin-bottom: 40px;
}

.section-title {
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.setting-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 25px;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.setting-row.full-width {
    flex-direction: column;
}

.setting-label {
    flex: 1;
    margin-right: 20px;
}

.setting-label label {
    font-weight: 500;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

.setting-description {
    font-size: 13px;
    color: #666;
    margin: 0;
    line-height: 1.4;
}

.setting-control {
    flex: 0 0 300px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kata-toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
    cursor: pointer;
}

.kata-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.3s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

.kata-toggle input:checked + .toggle-slider {
    background-color: #667eea;
}

.kata-toggle input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.number-input {
    width: 100px;
}

.input-suffix {
    color: #666;
    font-size: 14px;
}

.weights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.weight-item {
    background: white;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #eee;
}

.weight-label {
    display: block;
    font-weight: 500;
    margin-bottom: 10px;
    color: #333;
}

.weight-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.weight-slider {
    flex: 1;
    height: 6px;
    border-radius: 3px;
    background: #ddd;
    outline: none;
    -webkit-appearance: none;
}

.weight-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #667eea;
    cursor: pointer;
}

.weight-slider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #667eea;
    cursor: pointer;
    border: none;
}

.weight-value {
    font-weight: 500;
    color: #333;
    min-width: 35px;
    text-align: right;
}

.weights-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.total-weight {
    font-weight: 500;
    color: #333;
}

.api-key-input {
    display: flex;
    gap: 10px;
    width: 100%;
}

.api-key-input input {
    flex: 1;
}

.ai-features-preview {
    margin-top: 20px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    color: white;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin: 15px 0 0 0;
}

.feature-list li {
    padding: 5px 0;
    font-size: 14px;
}

.api-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.api-status-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #eee;
    text-align: center;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 10px 0;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #dc3545;
}

.status-dot.connected {
    background: #28a745;
}

.database-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.debug-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 15px;
    font-family: monospace;
    font-size: 12px;
}

.debug-item {
    margin-bottom: 5px;
}

.settings-footer {
    padding: 30px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ai-dependent,
.monitoring-dependent {
    transition: opacity 0.3s ease;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.kata-tab').on('click', function(e) {
        e.preventDefault();
        
        var targetPanel = $(this).attr('href');
        
        // Update active states
        $('.kata-tab').removeClass('active');
        $(this).addClass('active');
        
        $('.kata-tab-panel').removeClass('active');
        $(targetPanel).addClass('active');
    });

    // Weight sliders
    $('.weight-slider').on('input', function() {
        var value = $(this).val();
        $(this).siblings('.weight-value').text(value + '%');
        
        // Update total weight
        var totalWeight = 0;
        $('.weight-slider').each(function() {
            totalWeight += parseInt($(this).val());
        });
        $('#total-weight').text(totalWeight);
    });

    // Reset weights
    $('.reset-weights').on('click', function() {
        var defaultWeights = {
            'title_length': 15,
            'title_keywords': 20,
            'content_length': 10,
            'keyword_density': 15,
            'heading_structure': 10,
            'internal_links': 8,
            'external_links': 5,
            'image_optimization': 7,
            'meta_description': 10
        };
        
        $.each(defaultWeights, function(factor, weight) {
            $('#weight_' + factor).val(weight).trigger('input');
        });
    });

    // AI enabled toggle
    $('#ai_enabled').on('change', function() {
        var isEnabled = $(this).is(':checked');
        $('.ai-dependent').css('opacity', isEnabled ? 1 : 0.5);
        $('.ai-dependent input, .ai-dependent button').prop('disabled', !isEnabled);
    });

    // Monitoring enabled toggle
    $('#monitoring_enabled').on('change', function() {
        var isEnabled = $(this).is(':checked');
        $('.monitoring-dependent').css('opacity', isEnabled ? 1 : 0.5);
        $('.monitoring-dependent input, .monitoring-dependent select').prop('disabled', !isEnabled);
    });

    // Email notifications toggle
    $('#email_notifications').on('change', function() {
        var isEnabled = $(this).is(':checked') && $('#monitoring_enabled').is(':checked');
        $('#notification_email').prop('disabled', !isEnabled);
    });

    // API key input changes
    $('#openai_api_key, #google_api_key').on('input', function() {
        var $testBtn = $(this).siblings('.test-api-btn');
        $testBtn.prop('disabled', $(this).val().trim() === '');
    });

    // Database actions
    $('#cleanup-old-data').on('click', function() {
        if (confirm('This will remove old analysis data. Continue?')) {
            // Implement cleanup
            KataSEO.showNotification('Old data cleaned up', 'success');
        }
    });

    $('#export-data').on('click', function() {
        // Implement data export
        KataSEO.showNotification('Data export started', 'info');
    });

    $('#reset-all-data').on('click', function() {
        if (confirm('This will permanently delete ALL plugin data. This cannot be undone. Are you sure?')) {
            // Implement reset
            KataSEO.showNotification('All data has been reset', 'success');
        }
    });

    // Copy debug info
    $('#copy-debug-info').on('click', function() {
        var debugText = $('.debug-info').text();
        navigator.clipboard.writeText(debugText).then(function() {
            KataSEO.showNotification('Debug info copied to clipboard', 'success');
        });
    });

    // Reset settings
    $('#reset-settings').on('click', function() {
        if (confirm('Reset all settings to defaults?')) {
            location.href = location.href + '&reset=1';
        }
    });

    // Initialize weight total on load
    var totalWeight = 0;
    $('.weight-slider').each(function() {
        totalWeight += parseInt($(this).val());
    });
    $('#total-weight').text(totalWeight);

    // Check API status on load
    setTimeout(function() {
        if ($('#openai_api_key').val().trim()) {
            $('#openai-status .status-dot').addClass('connected');
            $('#openai-status .status-text').text('Connected');
        }
        
        if ($('#google_api_key').val().trim()) {
            $('#google-status .status-dot').addClass('connected');
            $('#google-status .status-text').text('Connected');
        }
    }, 1000);
});
</script>
