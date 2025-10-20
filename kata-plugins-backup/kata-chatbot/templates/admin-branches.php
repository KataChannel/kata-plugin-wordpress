<?php
/**
 * Admin Branches Page Template
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get branch handler
$branch_handler = new KataChatbot_Branch_Handler();
$branches = $branch_handler->get_all_branches();
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="kata-chatbot-admin-header">
        <div class="kata-admin-actions">
            <button type="button" class="button button-primary" id="add-new-branch">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php _e('Thêm chi nhánh mới', 'kata-chatbot'); ?>
            </button>
        </div>
    </div>
    
    <div class="kata-branches-container">
        <!-- Branches List -->
        <div class="kata-branches-list">
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-cb check-column">
                            <input type="checkbox" />
                        </th>
                        <th scope="col" class="manage-column"><?php _e('Tên chi nhánh', 'kata-chatbot'); ?></th>
                        <th scope="col" class="manage-column"><?php _e('Điện thoại', 'kata-chatbot'); ?></th>
                        <th scope="col" class="manage-column"><?php _e('Hotline', 'kata-chatbot'); ?></th>
                        <th scope="col" class="manage-column"><?php _e('Facebook', 'kata-chatbot'); ?></th>
                        <th scope="col" class="manage-column"><?php _e('Zalo', 'kata-chatbot'); ?></th>
                        <th scope="col" class="manage-column"><?php _e('Trạng thái', 'kata-chatbot'); ?></th>
                        <th scope="col" class="manage-column"><?php _e('Hành động', 'kata-chatbot'); ?></th>
                    </tr>
                </thead>
                <tbody id="branches-table-body">
                    <?php if (!empty($branches)) : ?>
                        <?php foreach ($branches as $branch) : ?>
                            <tr data-branch-id="<?php echo esc_attr($branch->id); ?>">
                                <th scope="row" class="check-column">
                                    <input type="checkbox" value="<?php echo esc_attr($branch->id); ?>" />
                                </th>
                                <td class="branch-name">
                                    <strong><?php echo esc_html($branch->name); ?></strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="#" class="edit-branch" data-id="<?php echo esc_attr($branch->id); ?>">
                                                <?php _e('Edit', 'kata-chatbot'); ?>
                                            </a> |
                                        </span>
                                        <span class="delete">
                                            <a href="#" class="delete-branch" data-id="<?php echo esc_attr($branch->id); ?>">
                                                <?php _e('Delete', 'kata-chatbot'); ?>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                <td><?php echo esc_html($branch->phone); ?></td>
                                <td><?php echo esc_html($branch->hotline); ?></td>
                                <td>
                                    <?php if (!empty($branch->facebook_url)) : ?>
                                        <span class="dashicons dashicons-facebook-alt" title="<?php _e('Facebook configured', 'kata-chatbot'); ?>"></span>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-minus" title="<?php _e('No Facebook', 'kata-chatbot'); ?>"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($branch->zalo_url)) : ?>
                                        <span class="dashicons dashicons-format-chat" title="<?php _e('Zalo configured', 'kata-chatbot'); ?>"></span>
                                    <?php else : ?>
                                        <span class="dashicons dashicons-minus" title="<?php _e('No Zalo', 'kata-chatbot'); ?>"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $branch->is_active ? 'active' : 'inactive'; ?>">
                                        <?php echo $branch->is_active ? __('Active', 'kata-chatbot') : __('Inactive', 'kata-chatbot'); ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="button button-small edit-branch" data-id="<?php echo esc_attr($branch->id); ?>">
                                        <?php _e('Edit', 'kata-chatbot'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="no-items">
                                <?php _e('No branches found. Click "Add New Branch" to create your first branch.', 'kata-chatbot'); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Branch Form Modal -->
<div id="branch-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h2 id="modal-title"><?php _e('Add New Branch', 'kata-chatbot'); ?></h2>
            <span class="kata-modal-close">&times;</span>
        </div>
        
        <div class="kata-modal-body">
            <form id="branch-form">
                <input type="hidden" id="branch-id" name="id" value="">
                
                <div class="kata-form-tabs">
                    <div class="kata-tab-nav">
                        <button type="button" class="kata-tab-button active" data-tab="basic">
                            <?php _e('Basic Info', 'kata-chatbot'); ?>
                        </button>
                        <button type="button" class="kata-tab-button" data-tab="contact">
                            <?php _e('Contact Info', 'kata-chatbot'); ?>
                        </button>
                        <button type="button" class="kata-tab-button" data-tab="social">
                            <?php _e('Social Media', 'kata-chatbot'); ?>
                        </button>
                    </div>
                    
                    <!-- Basic Info Tab -->
                    <div class="kata-tab-content active" id="tab-basic">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="branch-name"><?php _e('Branch Name', 'kata-chatbot'); ?> *</label>
                                </th>
                                <td>
                                    <input type="text" id="branch-name" name="name" class="regular-text" required>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-address"><?php _e('Address', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <textarea id="branch-address" name="address" class="large-text" rows="3"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-description"><?php _e('Description', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <textarea id="branch-description" name="description" class="large-text" rows="3"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-working-hours"><?php _e('Working Hours', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <textarea id="branch-working-hours" name="working_hours" class="large-text" rows="2" placeholder="<?php _e('e.g., 8:00 - 17:00 (Mon - Fri)', 'kata-chatbot'); ?>"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-order"><?php _e('Display Order', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="branch-order" name="display_order" min="0" value="0">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-active"><?php _e('Status', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" id="branch-active" name="is_active" value="1" checked>
                                        <?php _e('Active', 'kata-chatbot'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Contact Info Tab -->
                    <div class="kata-tab-content" id="tab-contact">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="branch-phone"><?php _e('Phone Number', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="tel" id="branch-phone" name="phone" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-hotline"><?php _e('Hotline', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="tel" id="branch-hotline" name="hotline" class="regular-text">
                                    <p class="description"><?php _e('Main customer service hotline', 'kata-chatbot'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-email"><?php _e('Email', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="branch-email" name="email" class="regular-text">
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Social Media Tab -->
                    <div class="kata-tab-content" id="tab-social">
                        <table class="form-table">
                            <tr>
                                <th colspan="2">
                                    <h3><?php _e('Facebook Settings', 'kata-chatbot'); ?></h3>
                                </th>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-facebook-url"><?php _e('Facebook Page URL', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="url" id="branch-facebook-url" name="facebook_url" class="large-text" placeholder="https://facebook.com/yourpage">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-facebook-page-id"><?php _e('Facebook Page ID', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="branch-facebook-page-id" name="facebook_page_id" class="regular-text">
                                    <p class="description"><?php _e('Used for Messenger integration', 'kata-chatbot'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th colspan="2">
                                    <h3><?php _e('Zalo Settings', 'kata-chatbot'); ?></h3>
                                </th>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-zalo-url"><?php _e('Zalo Page URL', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="url" id="branch-zalo-url" name="zalo_url" class="large-text" placeholder="https://zalo.me/yourpage">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="branch-zalo-oa-id"><?php _e('Zalo OA ID', 'kata-chatbot'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="branch-zalo-oa-id" name="zalo_oa_id" class="regular-text">
                                    <p class="description"><?php _e('Zalo Official Account ID', 'kata-chatbot'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="kata-modal-footer">
            <button type="button" class="button button-large" id="cancel-branch">
                <?php _e('Cancel', 'kata-chatbot'); ?>
            </button>
            <button type="button" class="button button-primary button-large" id="save-branch">
                <?php _e('Save Branch', 'kata-chatbot'); ?>
            </button>
        </div>
    </div>
</div>

<style>
.kata-chatbot-admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 15px 0;
    border-bottom: 1px solid #ddd;
}

.kata-admin-actions .button {
    margin-right: 10px;
}

.kata-branches-container {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.status-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-badge.active {
    background: #46b450;
    color: #fff;
}

.status-badge.inactive {
    background: #dc3232;
    color: #fff;
}

/* Modal Styles */
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
    max-height: 80vh;
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
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.kata-modal-close:hover {
    color: #dc3232;
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

/* Tab Styles */
.kata-form-tabs {
    margin-top: 20px;
}

.kata-tab-nav {
    border-bottom: 1px solid #ddd;
    margin-bottom: 20px;
}

.kata-tab-button {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-right: 10px;
}

.kata-tab-button.active {
    border-bottom-color: #0073aa;
    color: #0073aa;
    font-weight: bold;
}

.kata-tab-content {
    display: none;
}

.kata-tab-content.active {
    display: block;
}

.kata-tab-content h3 {
    margin: 0 0 15px 0;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    color: #666;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Tab switching
    $('.kata-tab-button').on('click', function() {
        var tab = $(this).data('tab');
        
        $('.kata-tab-button').removeClass('active');
        $(this).addClass('active');
        
        $('.kata-tab-content').removeClass('active');
        $('#tab-' + tab).addClass('active');
    });
    
    // Add new branch
    $('#add-new-branch').on('click', function() {
        resetBranchForm();
        $('#modal-title').text('<?php _e('Add New Branch', 'kata-chatbot'); ?>');
        $('#branch-modal').show();
    });
    
    // Edit branch
    $(document).on('click', '.edit-branch', function() {
        var branchId = $(this).data('id');
        loadBranchData(branchId);
    });
    
    // Delete branch
    $(document).on('click', '.delete-branch', function() {
        var branchId = $(this).data('id');
        
        if (confirm('<?php _e('Are you sure you want to delete this branch?', 'kata-chatbot'); ?>')) {
            deleteBranch(branchId);
        }
    });
    
    // Close modal
    $('.kata-modal-close, #cancel-branch').on('click', function() {
        $('#branch-modal').hide();
    });
    
    // Save branch
    $('#save-branch').on('click', function() {
        saveBranch();
    });
    
    // Close modal when clicking outside
    $(window).on('click', function(event) {
        if ($(event.target).is('#branch-modal')) {
            $('#branch-modal').hide();
        }
    });
    
    function resetBranchForm() {
        $('#branch-form')[0].reset();
        $('#branch-id').val('');
        $('#branch-active').prop('checked', true);
        
        // Reset to first tab
        $('.kata-tab-button').removeClass('active');
        $('.kata-tab-button').first().addClass('active');
        $('.kata-tab-content').removeClass('active');
        $('.kata-tab-content').first().addClass('active');
    }
    
    function loadBranchData(branchId) {
        $.post(ajaxurl, {
            action: 'kata_chatbot_get_branch',
            nonce: kata_chatbot_admin.nonce,
            id: branchId
        }, function(response) {
            if (response.success) {
                var branch = response.data;
                
                $('#branch-id').val(branch.id);
                $('#branch-name').val(branch.name);
                $('#branch-address').val(branch.address);
                $('#branch-phone').val(branch.phone);
                $('#branch-email').val(branch.email);
                $('#branch-facebook-url').val(branch.facebook_url);
                $('#branch-facebook-page-id').val(branch.facebook_page_id);
                $('#branch-zalo-url').val(branch.zalo_url);
                $('#branch-zalo-oa-id').val(branch.zalo_oa_id);
                $('#branch-hotline').val(branch.hotline);
                $('#branch-working-hours').val(branch.working_hours);
                $('#branch-description').val(branch.description);
                $('#branch-order').val(branch.display_order);
                $('#branch-active').prop('checked', branch.is_active == 1);
                
                $('#modal-title').text('<?php _e('Edit Branch', 'kata-chatbot'); ?>');
                $('#branch-modal').show();
            } else {
                alert('<?php _e('Error loading branch data', 'kata-chatbot'); ?>');
            }
        });
    }
    
    function saveBranch() {
        var formData = $('#branch-form').serialize();
        formData += '&action=kata_chatbot_save_branch&nonce=' + kata_chatbot_admin.nonce;
        
        $('#save-branch').prop('disabled', true).text('<?php _e('Saving...', 'kata-chatbot'); ?>');
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                $('#branch-modal').hide();
                location.reload(); // Refresh the page to show updated data
            } else {
                alert('<?php _e('Error saving branch', 'kata-chatbot'); ?>: ' + response.data);
            }
        }).always(function() {
            $('#save-branch').prop('disabled', false).text('<?php _e('Save Branch', 'kata-chatbot'); ?>');
        });
    }
    
    function deleteBranch(branchId) {
        $.post(ajaxurl, {
            action: 'kata_chatbot_delete_branch',
            nonce: kata_chatbot_admin.nonce,
            id: branchId
        }, function(response) {
            if (response.success) {
                $('[data-branch-id="' + branchId + '"]').fadeOut();
            } else {
                alert('<?php _e('Error deleting branch', 'kata-chatbot'); ?>: ' + response.data);
            }
        });
    }
});
</script>
