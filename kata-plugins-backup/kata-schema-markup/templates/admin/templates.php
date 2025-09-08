<?php
/**
 * Templates Page Template
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
    <h1><?php _e('Schema Templates', 'kata-schema-markup'); ?></h1>
    
    <div class="kata-schema-templates">
        <div class="kata-templates-header">
            <div class="kata-templates-actions">
                <button type="button" class="button button-primary" id="kata-add-template">
                    <?php _e('Add New Template', 'kata-schema-markup'); ?>
                </button>
                <button type="button" class="button" id="kata-import-templates">
                    <?php _e('Import Templates', 'kata-schema-markup'); ?>
                </button>
                <button type="button" class="button" id="kata-export-templates">
                    <?php _e('Export Templates', 'kata-schema-markup'); ?>
                </button>
            </div>
        </div>
        
        <div class="kata-templates-list">
            <?php if (!empty($templates)): ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-cb check-column">
                                <input type="checkbox" id="cb-select-all-1">
                            </th>
                            <th scope="col" class="manage-column"><?php _e('Name', 'kata-schema-markup'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Type', 'kata-schema-markup'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Post Types', 'kata-schema-markup'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Status', 'kata-schema-markup'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Created', 'kata-schema-markup'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Actions', 'kata-schema-markup'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($templates as $template): ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="template[]" value="<?php echo esc_attr($template->id); ?>">
                                </th>
                                <td class="column-name">
                                    <strong><?php echo esc_html($template->name); ?></strong>
                                </td>
                                <td class="column-type">
                                    <span class="schema-type-badge schema-type-<?php echo esc_attr($template->type); ?>">
                                        <?php echo esc_html(ucfirst($template->type)); ?>
                                    </span>
                                </td>
                                <td class="column-post-types">
                                    <?php 
                                    $post_types = !empty($template->post_types) ? explode(',', $template->post_types) : array();
                                    if (!empty($post_types)): ?>
                                        <?php foreach ($post_types as $post_type): ?>
                                            <span class="post-type-tag"><?php echo esc_html(trim($post_type)); ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="description"><?php _e('All', 'kata-schema-markup'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="column-status">
                                    <?php if ($template->status === 'active'): ?>
                                        <span class="status-active"><?php _e('Active', 'kata-schema-markup'); ?></span>
                                    <?php else: ?>
                                        <span class="status-inactive"><?php _e('Inactive', 'kata-schema-markup'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="column-created">
                                    <?php echo esc_html(mysql2date(get_option('date_format'), $template->created_at)); ?>
                                </td>
                                <td class="column-actions">
                                    <button type="button" class="button button-small kata-edit-template" 
                                            data-template-id="<?php echo esc_attr($template->id); ?>">
                                        <?php _e('Edit', 'kata-schema-markup'); ?>
                                    </button>
                                    <button type="button" class="button button-small kata-duplicate-template" 
                                            data-template-id="<?php echo esc_attr($template->id); ?>">
                                        <?php _e('Duplicate', 'kata-schema-markup'); ?>
                                    </button>
                                    <button type="button" class="button button-small button-link-delete kata-delete-template" 
                                            data-template-id="<?php echo esc_attr($template->id); ?>">
                                        <?php _e('Delete', 'kata-schema-markup'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="tablenav bottom">
                    <div class="alignleft actions bulkactions">
                        <select name="action2" id="bulk-action-selector-bottom">
                            <option value=""><?php _e('Bulk Actions', 'kata-schema-markup'); ?></option>
                            <option value="activate"><?php _e('Activate', 'kata-schema-markup'); ?></option>
                            <option value="deactivate"><?php _e('Deactivate', 'kata-schema-markup'); ?></option>
                            <option value="delete"><?php _e('Delete', 'kata-schema-markup'); ?></option>
                        </select>
                        <input type="submit" id="doaction2" class="button action" value="<?php _e('Apply', 'kata-schema-markup'); ?>">
                    </div>
                </div>
                
            <?php else: ?>
                <div class="kata-templates-empty">
                    <div class="kata-empty-state">
                        <div class="kata-empty-icon">
                            <span class="dashicons dashicons-admin-page"></span>
                        </div>
                        <h3><?php _e('No Templates Found', 'kata-schema-markup'); ?></h3>
                        <p><?php _e('Create your first schema template to get started.', 'kata-schema-markup'); ?></p>
                        <button type="button" class="button button-primary" id="kata-add-first-template">
                            <?php _e('Add New Template', 'kata-schema-markup'); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Template Editor Modal -->
<div id="kata-template-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h2 id="kata-modal-title"><?php _e('Add New Template', 'kata-schema-markup'); ?></h2>
            <button type="button" class="kata-modal-close">&times;</button>
        </div>
        <div class="kata-modal-body">
            <form id="kata-template-form">
                <input type="hidden" id="template-id" name="template_id" value="">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="template-name"><?php _e('Template Name', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="template-name" name="template_name" class="regular-text" required>
                            <p class="description"><?php _e('Enter a descriptive name for this template.', 'kata-schema-markup'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="template-type"><?php _e('Schema Type', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="template-type" name="template_type" required>
                                <option value=""><?php _e('Select Type', 'kata-schema-markup'); ?></option>
                                <option value="article"><?php _e('Article', 'kata-schema-markup'); ?></option>
                                <option value="organization"><?php _e('Organization', 'kata-schema-markup'); ?></option>
                                <option value="website"><?php _e('Website', 'kata-schema-markup'); ?></option>
                                <option value="breadcrumb"><?php _e('Breadcrumb', 'kata-schema-markup'); ?></option>
                                <option value="product"><?php _e('Product', 'kata-schema-markup'); ?></option>
                                <option value="review"><?php _e('Review', 'kata-schema-markup'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="template-post-types"><?php _e('Post Types', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <?php
                            $post_types = get_post_types(array('public' => true), 'objects');
                            foreach ($post_types as $post_type): ?>
                                <label>
                                    <input type="checkbox" name="template_post_types[]" value="<?php echo esc_attr($post_type->name); ?>">
                                    <?php echo esc_html($post_type->label); ?>
                                </label><br>
                            <?php endforeach; ?>
                            <p class="description"><?php _e('Select post types where this template should be available.', 'kata-schema-markup'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="template-conditions"><?php _e('Conditions', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <textarea id="template-conditions" name="template_conditions" rows="3" class="large-text"></textarea>
                            <p class="description">
                                <?php _e('Optional: Enter conditions when this template should be used (e.g., category=news, tag=featured).', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="template-schema"><?php _e('Schema JSON', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <textarea id="template-schema" name="template_schema" rows="15" class="large-text code"></textarea>
                            <p class="description">
                                <?php _e('Enter the JSON-LD schema template. Use variables like {{post_title}}, {{author_name}}, etc.', 'kata-schema-markup'); ?>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="template-status"><?php _e('Status', 'kata-schema-markup'); ?></label>
                        </th>
                        <td>
                            <select id="template-status" name="template_status">
                                <option value="active"><?php _e('Active', 'kata-schema-markup'); ?></option>
                                <option value="inactive"><?php _e('Inactive', 'kata-schema-markup'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="kata-modal-footer">
            <button type="button" class="button" id="kata-cancel-template"><?php _e('Cancel', 'kata-schema-markup'); ?></button>
            <button type="button" class="button button-primary" id="kata-save-template"><?php _e('Save Template', 'kata-schema-markup'); ?></button>
        </div>
    </div>
</div>

<style>
.kata-schema-templates {
    margin-top: 20px;
}

.kata-templates-header {
    margin-bottom: 20px;
}

.kata-templates-actions {
    display: flex;
    gap: 10px;
}

.schema-type-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.schema-type-article { background: #e3f2fd; color: #1976d2; }
.schema-type-organization { background: #f3e5f5; color: #7b1fa2; }
.schema-type-website { background: #e8f5e8; color: #388e3c; }
.schema-type-breadcrumb { background: #fff3e0; color: #f57c00; }
.schema-type-product { background: #fce4ec; color: #c2185b; }
.schema-type-review { background: #f1f8e9; color: #689f38; }

.post-type-tag {
    display: inline-block;
    padding: 2px 6px;
    margin: 2px;
    background: #f0f0f1;
    border-radius: 2px;
    font-size: 11px;
}

.status-active {
    color: #00a32a;
    font-weight: 600;
}

.status-inactive {
    color: #d63638;
    font-weight: 600;
}

.kata-templates-empty {
    padding: 60px 20px;
    text-align: center;
}

.kata-empty-state {
    max-width: 400px;
    margin: 0 auto;
}

.kata-empty-icon {
    font-size: 64px;
    color: #c3c4c7;
    margin-bottom: 20px;
}

.kata-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.kata-modal-content {
    background-color: #fff;
    margin: 5% auto;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 80%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
}

.kata-modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-modal-header h2 {
    margin: 0;
}

.kata-modal-close {
    background: none;
    border: none;
    font-size: 28px;
    cursor: pointer;
    color: #666;
}

.kata-modal-body {
    padding: 20px;
}

.kata-modal-footer {
    padding: 20px;
    border-top: 1px solid #ddd;
    text-align: right;
}

.kata-modal-footer .button {
    margin-left: 10px;
}

#template-schema {
    font-family: 'Courier New', monospace;
    font-size: 13px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add new template
    $('#kata-add-template, #kata-add-first-template').on('click', function() {
        $('#kata-template-modal').show();
        $('#kata-modal-title').text('<?php _e('Add New Template', 'kata-schema-markup'); ?>');
        $('#kata-template-form')[0].reset();
        $('#template-id').val('');
    });
    
    // Close modal
    $('.kata-modal-close, #kata-cancel-template').on('click', function() {
        $('#kata-template-modal').hide();
    });
    
    // Edit template
    $('.kata-edit-template').on('click', function() {
        var templateId = $(this).data('template-id');
        // Load template data via AJAX
        // Implementation would go here
        $('#kata-template-modal').show();
        $('#kata-modal-title').text('<?php _e('Edit Template', 'kata-schema-markup'); ?>');
    });
    
    // Save template
    $('#kata-save-template').on('click', function() {
        var formData = $('#kata-template-form').serialize();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=kata_save_schema_template&nonce=' + kata_schema_admin.nonce,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || '<?php _e('Error saving template', 'kata-schema-markup'); ?>');
                }
            }
        });
    });
    
    // Delete template
    $('.kata-delete-template').on('click', function() {
        if (!confirm('<?php _e('Are you sure you want to delete this template?', 'kata-schema-markup'); ?>')) {
            return;
        }
        
        var templateId = $(this).data('template-id');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_delete_schema_template',
                template_id: templateId,
                nonce: kata_schema_admin.nonce
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.data.message || '<?php _e('Error deleting template', 'kata-schema-markup'); ?>');
                }
            }
        });
    });
});
</script>
