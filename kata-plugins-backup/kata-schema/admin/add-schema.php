<?php
/**
 * KATA Schema Add/Edit Page
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get schema if editing
$schema = null;
$schema_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($schema_id > 0) {
    $database = new KATA_Schema_Database();
    $schema = $database->get_schema($schema_id);
}

$is_edit = !empty($schema);
$schema_types = KATA_Schema_Templates::get_schema_types();
?>

<div class="wrap kata-schema-add">
    <h1>
        <span class="dashicons dashicons-code-standards" style="font-size: 28px; margin-right: 8px;"></span>
        <?php echo $is_edit ? 'Chỉnh Sửa Schema' : 'Thêm Schema Mới'; ?>
    </h1>
    <a href="<?php echo admin_url('admin.php?page=kata-schema'); ?>" class="page-title-action">
        ← Quay Lại Danh Sách
    </a>
    <hr class="wp-heading-inline" />
    
    <form id="kata-schema-form" style="margin-top: 20px;">
        <input type="hidden" name="schema_id" value="<?php echo $schema_id; ?>" />
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="schema_name">Tên Schema <span class="required" style="color: red;">*</span></label>
                </th>
                <td>
                    <input type="text" 
                           id="schema_name" 
                           name="schema_name" 
                           class="regular-text" 
                           value="<?php echo $is_edit ? esc_attr($schema->schema_name) : ''; ?>" 
                           required />
                    <p class="description">Tên mô tả cho schema này (chỉ dùng nội bộ)</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="schema_type">Loại Schema <span class="required" style="color: red;">*</span></label>
                </th>
                <td>
                    <select id="schema_type" name="schema_type" class="regular-text" required>
                        <option value="">-- Chọn loại schema --</option>
                        <?php foreach ($schema_types as $type => $label): ?>
                        <option value="<?php echo esc_attr($type); ?>" 
                                <?php echo ($is_edit && $schema->schema_type == $type) ? 'selected' : ''; ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Chọn loại schema phù hợp với nội dung</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="schema_data">Dữ Liệu Schema (JSON) <span class="required" style="color: red;">*</span></label>
                </th>
                <td>
                    <textarea id="schema_data" 
                              name="schema_data" 
                              rows="20" 
                              class="large-text code" 
                              required><?php echo $is_edit ? esc_textarea($schema->schema_data) : ''; ?></textarea>
                    <p class="description">
                        Nhập dữ liệu schema ở định dạng JSON. 
                        <button type="button" class="button button-small" id="validate-json">
                            <span class="dashicons dashicons-yes-alt" style="vertical-align: middle;"></span> Kiểm Tra JSON
                        </button>
                        <button type="button" class="button button-small" id="format-json">
                            <span class="dashicons dashicons-editor-alignleft" style="vertical-align: middle;"></span> Format JSON
                        </button>
                    </p>
                    <div id="json-validation-result" style="margin-top: 10px;"></div>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="status">Trạng Thái</label>
                </th>
                <td>
                    <select id="status" name="status" class="regular-text">
                        <option value="active" <?php echo (!$is_edit || $schema->status == 'active') ? 'selected' : ''; ?>>
                            Hoạt động
                        </option>
                        <option value="inactive" <?php echo ($is_edit && $schema->status == 'inactive') ? 'selected' : ''; ?>>
                            Ngừng hoạt động
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-saved" style="vertical-align: middle;"></span>
                <?php echo $is_edit ? 'Cập Nhật Schema' : 'Tạo Schema'; ?>
            </button>
            <a href="<?php echo admin_url('admin.php?page=kata-schema'); ?>" class="button button-large">
                Hủy
            </a>
        </p>
    </form>
    
    <!-- Quick Reference -->
    <div class="kata-schema-reference" style="margin-top: 30px; background: #fff; padding: 20px; border-left: 4px solid #042277;">
        <h2>Tham Khảo Nhanh</h2>
        
        <div class="reference-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div>
                <h3>Các Thuộc Tính Bắt Buộc</h3>
                <ul style="line-height: 1.8;">
                    <li><code>@context</code>: "https://schema.org"</li>
                    <li><code>@type</code>: Loại schema (Article, Product, etc.)</li>
                    <li><strong>Article</strong>: headline, image, author, datePublished</li>
                    <li><strong>Product</strong>: name, image, description, offers</li>
                    <li><strong>LocalBusiness</strong>: name, address, telephone</li>
                </ul>
            </div>
            
            <div>
                <h3>Công Cụ Hữu Ích</h3>
                <ul style="line-height: 1.8;">
                    <li><a href="https://schema.org/" target="_blank">Schema.org Documentation</a></li>
                    <li><a href="https://search.google.com/test/rich-results" target="_blank">Google Rich Results Test</a></li>
                    <li><a href="https://validator.schema.org/" target="_blank">Schema Markup Validator</a></li>
                    <li><a href="https://jsonlint.com/" target="_blank">JSONLint - JSON Validator</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.kata-schema-add .required {
    font-weight: bold;
}

.kata-schema-add .form-table th {
    padding-top: 20px;
    vertical-align: top;
}

.kata-schema-add .form-table td {
    padding-top: 15px;
}

.kata-schema-add code {
    background: #f0f0f1;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 13px;
}

.kata-schema-add #schema_data {
    font-family: 'Courier New', Courier, monospace;
    font-size: 13px;
    line-height: 1.5;
}

.kata-schema-reference {
    border-radius: 4px;
}

.json-valid {
    color: green;
    padding: 10px;
    background: #d4edda;
    border: 1px solid #c3e6cb;
    border-radius: 3px;
}

.json-invalid {
    color: red;
    padding: 10px;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 3px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Validate JSON
    $('#validate-json').on('click', function() {
        var jsonData = $('#schema_data').val();
        var resultDiv = $('#json-validation-result');
        
        try {
            JSON.parse(jsonData);
            resultDiv.html('<div class="json-valid"><span class="dashicons dashicons-yes-alt"></span> JSON hợp lệ!</div>');
            setTimeout(function() { resultDiv.html(''); }, 3000);
        } catch (e) {
            resultDiv.html('<div class="json-invalid"><span class="dashicons dashicons-dismiss"></span> Lỗi JSON: ' + e.message + '</div>');
        }
    });
    
    // Format JSON
    $('#format-json').on('click', function() {
        var jsonData = $('#schema_data').val();
        
        try {
            var formatted = JSON.stringify(JSON.parse(jsonData), null, 2);
            $('#schema_data').val(formatted);
            $('#json-validation-result').html('<div class="json-valid"><span class="dashicons dashicons-yes-alt"></span> Đã format JSON!</div>');
            setTimeout(function() { $('#json-validation-result').html(''); }, 2000);
        } catch (e) {
            $('#json-validation-result').html('<div class="json-invalid"><span class="dashicons dashicons-dismiss"></span> Không thể format: ' + e.message + '</div>');
        }
    });
    
    // Submit form
    $('#kata-schema-form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate JSON before submit
        var jsonData = $('#schema_data').val();
        try {
            JSON.parse(jsonData);
        } catch (e) {
            alert('Dữ liệu JSON không hợp lệ. Vui lòng kiểm tra lại.');
            return false;
        }
        
        var formData = {
            action: 'kata_save_schema',
            nonce: kataSchemaAdmin.nonce,
            id: $('input[name="schema_id"]').val(),
            schema_name: $('#schema_name').val(),
            schema_type: $('#schema_type').val(),
            schema_data: jsonData,
            status: $('#status').val()
        };
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Đã lưu schema thành công!');
                    window.location.href = '<?php echo admin_url('admin.php?page=kata-schema'); ?>';
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
