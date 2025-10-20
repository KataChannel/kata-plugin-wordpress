<?php
/**
 * Admin Knowledge Base Template
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get knowledge base items
$knowledge_items = isset($knowledge_items) ? $knowledge_items : array();
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

// Calculate pagination
$total_items = count($knowledge_items);
$total_pages = ceil($total_items / $per_page);
$offset = ($page - 1) * $per_page;
$current_items = array_slice($knowledge_items, $offset, $per_page);
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="kata-knowledge-container">
        <!-- Add New Item Form -->
        <div class="kata-add-knowledge-form">
            <h2><?php _e('Thêm mục tri thức mới', 'kata-chatbot'); ?></h2>
            <form id="add-knowledge-form" method="post">
                <?php wp_nonce_field('kata_add_knowledge', 'kata_knowledge_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="knowledge_question"><?php _e('Câu hỏi', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="knowledge_question" name="question" class="regular-text" required>
                            <p class="description"><?php _e('Nhập câu hỏi mà người dùng có thể hỏi', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="knowledge_answer"><?php _e('Câu trả lời', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <textarea id="knowledge_answer" name="answer" rows="5" class="large-text" required></textarea>
                            <p class="description"><?php _e('Nhập câu trả lời mà chatbot sẽ cung cấp', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="knowledge_keywords"><?php _e('Từ khóa', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="knowledge_keywords" name="keywords" class="regular-text">
                            <p class="description"><?php _e('Nhập từ khóa phân tách bằng dấu phẩy (tùy chọn)', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="knowledge_status"><?php _e('Trạng thái', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <select id="knowledge_status" name="status">
                                <option value="active"><?php _e('Hoạt động', 'kata-chatbot'); ?></option>
                                <option value="inactive"><?php _e('Không hoạt động', 'kata-chatbot'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="add_knowledge" class="button-primary" value="<?php _e('Thêm mục tri thức', 'kata-chatbot'); ?>">
                </p>
            </form>
        </div>
        
        <!-- Search and Filter -->
        <div class="kata-knowledge-filters">
            <form method="get" class="kata-search-form">
                <input type="hidden" name="page" value="kata-chatbot-knowledge">
                
                <p class="search-box">
                    <input type="search" id="knowledge-search" name="search" value="<?php echo esc_attr($search); ?>" placeholder="<?php _e('Tìm kiếm mục tri thức...', 'kata-chatbot'); ?>">
                    <input type="submit" id="search-submit" class="button" value="<?php _e('Tìm kiếm', 'kata-chatbot'); ?>">
                </p>
            </form>
        </div>
        
        <!-- Knowledge Items List -->
        <div class="kata-knowledge-list">
            <?php if (!empty($current_items)) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-question column-primary">
                                <?php _e('Question', 'kata-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column column-answer">
                                <?php _e('Answer', 'kata-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column column-keywords">
                                <?php _e('Keywords', 'kata-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column column-status">
                                <?php _e('Status', 'kata-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column column-created">
                                <?php _e('Created', 'kata-chatbot'); ?>
                            </th>
                            <th scope="col" class="manage-column column-actions">
                                <?php _e('Actions', 'kata-chatbot'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($current_items as $item) : ?>
                            <tr data-id="<?php echo esc_attr($item['id']); ?>">
                                <td class="question column-question column-primary">
                                    <strong><?php echo esc_html($item['question']); ?></strong>
                                    <div class="row-actions">
                                        <span class="edit">
                                            <a href="#" class="edit-knowledge" data-id="<?php echo esc_attr($item['id']); ?>">
                                                <?php _e('Edit', 'kata-chatbot'); ?>
                                            </a> |
                                        </span>
                                        <span class="delete">
                                            <a href="#" class="delete-knowledge text-danger" data-id="<?php echo esc_attr($item['id']); ?>">
                                                <?php _e('Delete', 'kata-chatbot'); ?>
                                            </a>
                                        </span>
                                    </div>
                                </td>
                                <td class="answer column-answer">
                                    <div class="answer-preview">
                                        <?php echo esc_html(wp_trim_words($item['answer'], 20)); ?>
                                    </div>
                                </td>
                                <td class="keywords column-keywords">
                                    <?php if (!empty($item['keywords'])) : ?>
                                        <div class="keywords-list">
                                            <?php 
                                            $keywords = explode(',', $item['keywords']);
                                            foreach ($keywords as $keyword) : 
                                                $keyword = trim($keyword);
                                                if (!empty($keyword)) :
                                            ?>
                                                <span class="keyword-tag"><?php echo esc_html($keyword); ?></span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="status column-status">
                                    <span class="status-badge status-<?php echo esc_attr($item['status']); ?>">
                                        <?php echo esc_html(ucfirst($item['status'])); ?>
                                    </span>
                                </td>
                                <td class="created column-created">
                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($item['created_at']))); ?>
                                </td>
                                <td class="actions column-actions">
                                    <button type="button" class="button button-small edit-knowledge" data-id="<?php echo esc_attr($item['id']); ?>">
                                        <?php _e('Edit', 'kata-chatbot'); ?>
                                    </button>
                                    <button type="button" class="button button-small delete-knowledge" data-id="<?php echo esc_attr($item['id']); ?>">
                                        <?php _e('Delete', 'kata-chatbot'); ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1) : ?>
                    <div class="tablenav bottom">
                        <div class="tablenav-pages">
                            <?php
                            $pagination_args = array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'current' => $page,
                                'total' => $total_pages,
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;'
                            );
                            echo paginate_links($pagination_args);
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else : ?>
                <div class="kata-empty-state">
                    <p><?php _e('No knowledge items found.', 'kata-chatbot'); ?></p>
                    <?php if (!empty($search)) : ?>
                        <p>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=kata-chatbot-knowledge')); ?>" class="button">
                                <?php _e('Clear Search', 'kata-chatbot'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit Knowledge Modal -->
<div id="edit-knowledge-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h2><?php _e('Edit Knowledge Item', 'kata-chatbot'); ?></h2>
            <span class="kata-modal-close">&times;</span>
        </div>
        <div class="kata-modal-body">
            <form id="edit-knowledge-form">
                <input type="hidden" id="edit-knowledge-id" name="id">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="edit_knowledge_question"><?php _e('Question', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="edit_knowledge_question" name="question" class="regular-text" required>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="edit_knowledge_answer"><?php _e('Answer', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <textarea id="edit_knowledge_answer" name="answer" rows="5" class="large-text" required></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="edit_knowledge_keywords"><?php _e('Keywords', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="edit_knowledge_keywords" name="keywords" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="edit_knowledge_status"><?php _e('Status', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <select id="edit_knowledge_status" name="status">
                                <option value="active"><?php _e('Active', 'kata-chatbot'); ?></option>
                                <option value="inactive"><?php _e('Inactive', 'kata-chatbot'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <div class="kata-modal-actions">
                    <button type="button" class="button kata-modal-cancel"><?php _e('Cancel', 'kata-chatbot'); ?></button>
                    <button type="submit" class="button-primary"><?php _e('Update Knowledge Item', 'kata-chatbot'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.kata-knowledge-container {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.kata-add-knowledge-form {
    background: #f9f9f9;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 20px;
}

.kata-add-knowledge-form h2 {
    margin-top: 0;
}

.kata-knowledge-filters {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.kata-search-form .search-box {
    display: flex;
    gap: 10px;
    align-items: center;
}

.kata-search-form input[type="search"] {
    flex: 1;
    max-width: 300px;
}

.answer-preview {
    max-width: 300px;
    word-wrap: break-word;
}

.keywords-list {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.keyword-tag {
    background: #0073aa;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    white-space: nowrap;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.kata-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.kata-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.kata-modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 80%;
    max-width: 600px;
    position: relative;
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
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #999;
}

.kata-modal-close:hover {
    color: #333;
}

.kata-modal-body {
    padding: 20px;
}

.kata-modal-actions {
    text-align: right;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #ddd;
}

.kata-modal-actions .button {
    margin-left: 10px;
}

.text-danger {
    color: #dc3545 !important;
}

@media (max-width: 768px) {
    .kata-modal-content {
        width: 95%;
        margin: 10% auto;
    }
    
    .kata-search-form .search-box {
        flex-direction: column;
        align-items: stretch;
    }
    
    .kata-search-form input[type="search"] {
        max-width: none;
        margin-bottom: 10px;
    }
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Add Knowledge Form
    $('#add-knowledge-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'kata_add_knowledge',
            nonce: $('#kata_knowledge_nonce').val(),
            question: $('#knowledge_question').val(),
            answer: $('#knowledge_answer').val(),
            keywords: $('#knowledge_keywords').val(),
            status: $('#knowledge_status').val()
        };
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data || '<?php _e('Error adding knowledge item', 'kata-chatbot'); ?>');
            }
        });
    });
    
    // Edit Knowledge
    $('.edit-knowledge').on('click', function(e) {
        e.preventDefault();
        
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        // Get current values
        var question = row.find('.question strong').text();
        var answer = row.find('.answer-preview').text();
        var keywords = '';
        var status = row.find('.status-badge').text().toLowerCase();
        
        // Populate edit form
        $('#edit-knowledge-id').val(id);
        $('#edit_knowledge_question').val(question);
        $('#edit_knowledge_answer').val(answer);
        $('#edit_knowledge_keywords').val(keywords);
        $('#edit_knowledge_status').val(status);
        
        // Show modal
        $('#edit-knowledge-modal').show();
    });
    
    // Edit Knowledge Form Submit
    $('#edit-knowledge-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            action: 'kata_update_knowledge',
            nonce: '<?php echo wp_create_nonce("kata_update_knowledge"); ?>',
            id: $('#edit-knowledge-id').val(),
            question: $('#edit_knowledge_question').val(),
            answer: $('#edit_knowledge_answer').val(),
            keywords: $('#edit_knowledge_keywords').val(),
            status: $('#edit_knowledge_status').val()
        };
        
        $.post(ajaxurl, formData, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.data || '<?php _e('Error updating knowledge item', 'kata-chatbot'); ?>');
            }
        });
    });
    
    // Delete Knowledge
    $('.delete-knowledge').on('click', function(e) {
        e.preventDefault();
        
        if (!confirm('<?php _e('Are you sure you want to delete this knowledge item?', 'kata-chatbot'); ?>')) {
            return;
        }
        
        var id = $(this).data('id');
        var row = $(this).closest('tr');
        
        var data = {
            action: 'kata_delete_knowledge',
            nonce: '<?php echo wp_create_nonce("kata_delete_knowledge"); ?>',
            id: id
        };
        
        $.post(ajaxurl, data, function(response) {
            if (response.success) {
                row.fadeOut(300, function() {
                    $(this).remove();
                });
            } else {
                alert(response.data || '<?php _e('Error deleting knowledge item', 'kata-chatbot'); ?>');
            }
        });
    });
    
    // Modal Controls
    $('.kata-modal-close, .kata-modal-cancel').on('click', function() {
        $('#edit-knowledge-modal').hide();
    });
    
    // Close modal when clicking outside
    $('#edit-knowledge-modal').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });
});
</script>
