<?php
/**
 * KATA Schema Dashboard Page
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get database instance
$database = new KATA_Schema_Database();
$schemas = $database->get_all_schemas();
$stats = $database->get_count_by_type();
?>

<div class="wrap kata-schema-dashboard">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-code-standards" style="font-size: 28px; margin-right: 8px;"></span>
        KATA Schema Manager
    </h1>
    <a href="<?php echo admin_url('admin.php?page=kata-schema-add'); ?>" class="page-title-action">
        Thêm Schema Mới
    </a>
    <a href="<?php echo admin_url('admin.php?page=kata-schema-templates'); ?>" class="page-title-action">
        Duyệt Mẫu Templates
    </a>
    <hr class="wp-heading-inline" />
    
    <!-- Statistics -->
    <div class="kata-schema-stats" style="margin: 20px 0;">
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
            <div class="stat-box" style="background: #fff; padding: 20px; border-left: 4px solid #042277; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #042277;">Tổng Schemas</h3>
                <p style="font-size: 32px; font-weight: bold; margin: 0; color: #040B1E;">
                    <?php echo $database->get_total_count('active'); ?>
                </p>
            </div>
            
            <?php foreach ($stats as $stat): ?>
            <div class="stat-box" style="background: #fff; padding: 20px; border-left: 4px solid #0073aa; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <h3 style="margin: 0 0 10px 0; color: #0073aa;">
                    <?php 
                    $types = KATA_Schema_Templates::get_schema_types();
                    echo isset($types[$stat->schema_type]) ? $types[$stat->schema_type] : $stat->schema_type;
                    ?>
                </h3>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #040B1E;">
                    <?php echo $stat->count; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Schemas List -->
    <div class="kata-schema-list">
        <h2>Danh Sách Schemas</h2>
        
        <?php if (empty($schemas)): ?>
            <div class="notice notice-info">
                <p>Chưa có schema nào. <a href="<?php echo admin_url('admin.php?page=kata-schema-add'); ?>">Tạo schema đầu tiên</a> hoặc <a href="<?php echo admin_url('admin.php?page=kata-schema-templates'); ?>">sử dụng template có sẵn</a>.</p>
            </div>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Tên Schema</th>
                        <th style="width: 150px;">Loại</th>
                        <th style="width: 100px;">Trạng Thái</th>
                        <th style="width: 150px;">Ngày Tạo</th>
                        <th style="width: 200px;">Shortcode</th>
                        <th style="width: 150px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schemas as $schema): ?>
                    <tr>
                        <td><?php echo $schema->id; ?></td>
                        <td>
                            <strong><?php echo esc_html($schema->schema_name); ?></strong>
                        </td>
                        <td>
                            <?php 
                            $types = KATA_Schema_Templates::get_schema_types();
                            echo isset($types[$schema->schema_type]) ? $types[$schema->schema_type] : $schema->schema_type;
                            ?>
                        </td>
                        <td>
                            <?php if ($schema->status == 'active'): ?>
                                <span class="dashicons dashicons-yes-alt" style="color: green;"></span> Hoạt động
                            <?php else: ?>
                                <span class="dashicons dashicons-dismiss" style="color: red;"></span> Ngừng
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($schema->created_at)); ?></td>
                        <td>
                            <code style="background: #f0f0f1; padding: 4px 8px; border-radius: 3px; font-size: 12px; cursor: pointer;" 
                                  onclick="navigator.clipboard.writeText('[kata_schema id=<?php echo $schema->id; ?>]'); alert('Đã copy shortcode!');">
                                [kata_schema id=<?php echo $schema->id; ?>]
                            </code>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=kata-schema-add&id=' . $schema->id); ?>" 
                               class="button button-small">
                                <span class="dashicons dashicons-edit" style="vertical-align: middle;"></span> Sửa
                            </a>
                            <button class="button button-small kata-delete-schema" 
                                    data-id="<?php echo $schema->id; ?>" 
                                    data-name="<?php echo esc_attr($schema->schema_name); ?>">
                                <span class="dashicons dashicons-trash" style="vertical-align: middle;"></span> Xóa
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Quick Guide -->
    <div class="kata-schema-guide" style="margin-top: 30px; background: #fff; padding: 20px; border-left: 4px solid #042277;">
        <h2>Hướng Dẫn Sử Dụng</h2>
        <ol>
            <li><strong>Tạo Schema:</strong> Click "Thêm Schema Mới" hoặc chọn từ Templates có sẵn</li>
            <li><strong>Chỉnh Sửa:</strong> Điều chỉnh dữ liệu JSON theo nội dung của bạn</li>
            <li><strong>Chèn Vào Bài:</strong> 
                <ul>
                    <li>Cách 1: Click nút <strong>KATA Schema</strong> trên TinyMCE editor</li>
                    <li>Cách 2: Copy shortcode <code>[kata_schema id=X]</code> và paste vào bài</li>
                </ul>
            </li>
            <li><strong>Kiểm Tra:</strong> Sử dụng <a href="https://search.google.com/test/rich-results" target="_blank">Google Rich Results Test</a> để kiểm tra schema</li>
        </ol>
        
        <h3>Tùy Chọn Shortcode</h3>
        <ul>
            <li><code>[kata_schema id=1]</code> - Chỉ xuất schema markup (ẩn trên frontend)</li>
            <li><code>[kata_schema id=1 show_frontend="true"]</code> - Hiển thị visual trên frontend</li>
            <li><code>[kata_schema id=1 show_schema="false"]</code> - Chỉ hiển thị visual, không xuất schema markup</li>
        </ul>
    </div>
</div>

<style>
.kata-schema-dashboard .stats-grid {
    margin-bottom: 30px;
}

.kata-schema-dashboard .stat-box {
    border-radius: 4px;
}

.kata-schema-dashboard .stat-box h3 {
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kata-schema-dashboard code {
    user-select: all;
}

.kata-schema-guide {
    border-radius: 4px;
}

.kata-schema-guide ul,
.kata-schema-guide ol {
    line-height: 1.8;
}

.kata-schema-guide code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 13px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Delete schema
    $('.kata-delete-schema').on('click', function() {
        var schemaId = $(this).data('id');
        var schemaName = $(this).data('name');
        
        if (!confirm('Bạn có chắc muốn xóa schema "' + schemaName + '"?')) {
            return;
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_delete_schema',
                nonce: kataSchemaAdmin.nonce,
                id: schemaId
            },
            success: function(response) {
                if (response.success) {
                    alert('Đã xóa schema thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + response.data.message);
                }
            },
            error: function() {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    });
});
</script>
