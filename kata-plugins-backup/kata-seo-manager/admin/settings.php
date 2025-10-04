<?php
/**
 * Settings Page
 * 
 * Plugin settings and configuration
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Save settings
if (isset($_POST['kata_seo_save_settings']) && check_admin_referer('kata_seo_settings_nonce')) {
    $settings = array(
        'enable_auto_schema' => isset($_POST['enable_auto_schema']),
        'default_schema_types' => isset($_POST['default_schema_types']) ? array_map('sanitize_text_field', $_POST['default_schema_types']) : array(),
        'enable_editor_button' => isset($_POST['enable_editor_button']),
        'enable_statistics' => isset($_POST['enable_statistics']),
        'enable_shortcodes' => isset($_POST['enable_shortcodes']),
        'enable_rest_api' => isset($_POST['enable_rest_api']),
        'schema_validation' => isset($_POST['schema_validation']),
    );
    
    update_option('kata_seo_manager_settings', $settings);
    
    echo '<div class="notice notice-success is-dismissible"><p>' . 
         __('Settings saved successfully!', 'kata-seo-manager') . '</p></div>';
}

// Get current settings
$settings = get_option('kata_seo_manager_settings', array(
    'enable_auto_schema' => true,
    'default_schema_types' => array('Article', 'Breadcrumb', 'WebPage'),
    'enable_editor_button' => true,
    'enable_statistics' => true,
    'enable_shortcodes' => true,
    'enable_rest_api' => false,
    'schema_validation' => true,
));

// Get all schema types
$schema_types = array(
    'Article', 'Breadcrumb', 'Carousel', 'Course', 'Dataset', 'Forum',
    'EduQA', 'EmployerRating', 'Event', 'FAQ', 'HowTo', 'ImageMetadata',
    'JobPosting', 'LocalBusiness', 'MathSolver', 'Movie', 'Organization',
    'PracticeProblem', 'Product', 'ProfilePage', 'Recipe', 'Review',
    'Sitelinks', 'Speakable', 'Video', 'WebPage'
);
?>

<div class="wrap kata-seo-settings">
    <h1><?php _e('KATA SEO Settings', 'kata-seo-manager'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('kata_seo_settings_nonce'); ?>
        
        <div class="kata-settings-container">
            <!-- General Settings -->
            <div class="kata-settings-card">
                <h2><?php _e('General Settings', 'kata-seo-manager'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_auto_schema">
                                <?php _e('Enable Auto Schema', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_auto_schema" id="enable_auto_schema" 
                                       <?php checked($settings['enable_auto_schema'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Automatically generate basic schema for posts and pages', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_editor_button">
                                <?php _e('Enable Editor Button', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_editor_button" id="enable_editor_button" 
                                       <?php checked($settings['enable_editor_button'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Show "Insert Schema" button in post editor', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_statistics">
                                <?php _e('Enable Statistics', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_statistics" id="enable_statistics" 
                                       <?php checked($settings['enable_statistics'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Track schema usage statistics', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_shortcodes">
                                <?php _e('Enable Shortcodes', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_shortcodes" id="enable_shortcodes" 
                                       <?php checked($settings['enable_shortcodes'] ?? true, true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Enable schema shortcodes for easy insertion', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="schema_validation">
                                <?php _e('Schema Validation', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="schema_validation" id="schema_validation" 
                                       <?php checked($settings['schema_validation'] ?? true, true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Validate schema before output', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Default Schema Types -->
            <div class="kata-settings-card">
                <h2><?php _e('Default Schema Types', 'kata-seo-manager'); ?></h2>
                <p class="description">
                    <?php _e('Select which schema types to enable by default for new posts', 'kata-seo-manager'); ?>
                </p>
                
                <div class="kata-schema-types-grid">
                    <?php foreach ($schema_types as $type) : ?>
                        <label class="kata-schema-checkbox">
                            <input type="checkbox" name="default_schema_types[]" 
                                   value="<?php echo esc_attr($type); ?>"
                                   <?php checked(in_array($type, $settings['default_schema_types'])); ?>>
                            <span><?php echo esc_html($type); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div class="kata-settings-card">
                <h2><?php _e('Advanced Settings', 'kata-seo-manager'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_rest_api">
                                <?php _e('Enable REST API', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_rest_api" id="enable_rest_api" 
                                       <?php checked($settings['enable_rest_api'] ?? false, true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Enable REST API endpoints for schema management', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Plugin Information -->
            <div class="kata-settings-card kata-info-card">
                <h2><?php _e('Plugin Information', 'kata-seo-manager'); ?></h2>
                
                <table class="kata-info-table">
                    <tr>
                        <td><strong><?php _e('Version:', 'kata-seo-manager'); ?></strong></td>
                        <td><?php echo KATA_SEO_MANAGER_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Schema Types:', 'kata-seo-manager'); ?></strong></td>
                        <td>26 types</td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Author:', 'kata-seo-manager'); ?></strong></td>
                        <td>KATA Channel</td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Website:', 'kata-seo-manager'); ?></strong></td>
                        <td><a href="https://katachannel.com" target="_blank">katachannel.com</a></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <p class="submit">
            <button type="submit" name="kata_seo_save_settings" class="button button-primary button-large">
                <span class="dashicons dashicons-yes"></span>
                <?php _e('Save Settings', 'kata-seo-manager'); ?>
            </button>
        </p>
    </form>
</div>

<style>
.kata-seo-settings {
    margin: 20px 20px 20px 0;
}

.kata-settings-container {
    max-width: 1200px;
}

.kata-settings-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.kata-settings-card h2 {
    margin: 0 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    font-size: 20px;
}

/* Toggle Switch */
.kata-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.kata-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.kata-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.kata-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .kata-slider {
    background-color: #667eea;
}

input:checked + .kata-slider:before {
    transform: translateX(26px);
}

/* Schema Types Grid */
.kata-schema-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.kata-schema-checkbox {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: #f9f9f9;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.kata-schema-checkbox:hover {
    background: #f0f0f0;
    border-color: #667eea;
}

.kata-schema-checkbox input[type="checkbox"] {
    margin: 0 10px 0 0;
}

.kata-schema-checkbox input[type="checkbox"]:checked + span {
    font-weight: 600;
    color: #667eea;
}

/* Info Card */
.kata-info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.kata-info-card h2 {
    color: #fff;
    border-bottom-color: rgba(255,255,255,0.2);
}

.kata-info-table {
    width: 100%;
}

.kata-info-table tr td {
    padding: 10px 0;
    color: #fff;
}

.kata-info-table a {
    color: #fff;
    text-decoration: underline;
}

/* Submit Button */
.submit .button-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    font-size: 16px;
    height: auto;
}

.submit .button-primary .dashicons {
    font-size: 20px;
    width: 20px;
    height: 20px;
}
</style>
