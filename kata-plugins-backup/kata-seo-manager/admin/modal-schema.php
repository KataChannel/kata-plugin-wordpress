<?php
/**
 * Schema Modal
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div id="kata-schema-modal" class="kata-modal" style="display:none;">
    <div class="kata-modal-overlay"></div>
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h2><?php _e('Insert Schema Markup', 'kata-seo-manager'); ?></h2>
            <button type="button" class="kata-modal-close">&times;</button>
        </div>
        
        <div class="kata-modal-body">
            <!-- Step 1: Select Schema Type -->
            <div class="kata-modal-step" data-step="1">
                <h3><?php _e('Select Schema Type', 'kata-seo-manager'); ?></h3>
                <div class="kata-schema-type-grid">
                    <?php foreach ($schema_types as $type => $label): ?>
                        <button type="button" class="kata-schema-type-btn" data-type="<?php echo esc_attr($type); ?>">
                            <span class="dashicons dashicons-code-standards"></span>
                            <span class="type-label"><?php echo esc_html($label); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Step 2: Fill Schema Data -->
            <div class="kata-modal-step" data-step="2" style="display:none;">
                <div class="kata-step-header">
                    <button type="button" class="kata-back-btn">&larr; <?php _e('Back', 'kata-seo-manager'); ?></button>
                    <h3><?php _e('Enter Schema Data', 'kata-seo-manager'); ?></h3>
                </div>
                
                <div class="kata-schema-tabs">
                    <button type="button" class="kata-tab-btn active" data-tab="form">
                        <?php _e('Form', 'kata-seo-manager'); ?>
                    </button>
                    <button type="button" class="kata-tab-btn" data-tab="template">
                        <?php _e('Templates', 'kata-seo-manager'); ?>
                    </button>
                    <button type="button" class="kata-tab-btn" data-tab="preview">
                        <?php _e('Preview', 'kata-seo-manager'); ?>
                    </button>
                </div>
                
                <div class="kata-tab-content" data-tab="form">
                    <div id="kata-schema-form"></div>
                </div>
                
                <div class="kata-tab-content" data-tab="template" style="display:none;">
                    <p><?php _e('Select a template to get started:', 'kata-seo-manager'); ?></p>
                    <select id="kata-template-selector" class="large-text">
                        <option value=""><?php _e('Select Template...', 'kata-seo-manager'); ?></option>
                    </select>
                    <p class="description"><?php _e('Templates use placeholders like {post_title}, {post_excerpt}, {featured_image} that will be replaced with actual post data.', 'kata-seo-manager'); ?></p>
                </div>
                
                <div class="kata-tab-content" data-tab="preview" style="display:none;">
                    <pre id="kata-schema-preview"><code><?php _e('Fill the form to see preview...', 'kata-seo-manager'); ?></code></pre>
                </div>
            </div>
        </div>
        
        <div class="kata-modal-footer">
            <button type="button" class="button button-secondary kata-cancel-btn">
                <?php _e('Cancel', 'kata-seo-manager'); ?>
            </button>
            <button type="button" class="button button-primary kata-insert-btn" disabled>
                <?php _e('Insert Schema', 'kata-seo-manager'); ?>
            </button>
            <button type="button" class="button button-secondary kata-validate-btn" style="display:none;">
                <?php _e('Validate', 'kata-seo-manager'); ?>
            </button>
        </div>
    </div>
</div>

<style>
.kata-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100000;
}
.kata-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
}
.kata-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    border-radius: 4px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
}
.kata-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    border-bottom: 1px solid #ddd;
}
.kata-modal-header h2 {
    margin: 0;
}
.kata-modal-close {
    background: none;
    border: none;
    font-size: 30px;
    cursor: pointer;
    color: #666;
}
.kata-modal-close:hover {
    color: #000;
}
.kata-modal-body {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}
.kata-schema-type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
}
.kata-schema-type-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px;
    border: 2px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}
.kata-schema-type-btn:hover {
    border-color: #2271b1;
    background: #f0f6fc;
}
.kata-schema-type-btn .dashicons {
    font-size: 30px;
    color: #2271b1;
}
.type-label {
    font-size: 13px;
    text-align: center;
}
.kata-step-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}
.kata-back-btn {
    background: none;
    border: none;
    color: #2271b1;
    cursor: pointer;
    font-size: 14px;
}
.kata-schema-tabs {
    display: flex;
    gap: 10px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}
.kata-tab-btn {
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 10px 15px;
    cursor: pointer;
}
.kata-tab-btn.active {
    border-bottom-color: #2271b1;
    color: #2271b1;
}
#kata-schema-form {
    display: grid;
    gap: 15px;
}
#kata-schema-preview {
    background: #f5f5f5;
    padding: 15px;
    border-radius: 4px;
    overflow-x: auto;
    max-height: 400px;
}
.kata-modal-footer {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding: 20px;
    border-top: 1px solid #ddd;
}
</style>
