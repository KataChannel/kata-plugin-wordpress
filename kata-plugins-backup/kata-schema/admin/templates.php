<?php
/**
 * KATA Schema Templates Browser Page
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

$schema_types = KATA_Schema_Templates::get_schema_types();
$database = new KATA_Schema_Database();
?>

<div class="wrap kata-schema-templates">
    <h1>
        <span class="dashicons dashicons-book" style="font-size: 28px; margin-right: 8px;"></span>
        Schema Templates
    </h1>
    <a href="<?php echo admin_url('admin.php?page=kata-schema'); ?>" class="page-title-action">
        ← Quay Lại Danh Sách
    </a>
    <a href="<?php echo admin_url('admin.php?page=kata-schema-add'); ?>" class="page-title-action">
        Tạo Schema Tùy Chỉnh
    </a>
    <hr class="wp-heading-inline" />
    
    <p style="margin-top: 20px; font-size: 14px;">
        Chọn một template bên dưới để tạo schema mới. Bạn có thể chỉnh sửa dữ liệu sau khi tạo.
    </p>
    
    <!-- Templates Grid -->
    <div class="templates-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
        
        <?php foreach ($schema_types as $type => $label): ?>
        <div class="template-card" data-type="<?php echo esc_attr($type); ?>">
            <div class="template-header" style="background: linear-gradient(135deg, #042277 0%, #040B1E 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0;">
                <h2 style="margin: 0; font-size: 20px;">
                    <?php 
                    $icons = array(
                        'Article' => 'admin-post',
                        'LocalBusiness' => 'store',
                        'Product' => 'products',
                        'FAQ' => 'format-status',
                        'HowTo' => 'editor-ol',
                        'Organization' => 'building',
                        'Person' => 'admin-users',
                        'Event' => 'calendar-alt',
                        'Recipe' => 'carrot',
                        'Course' => 'welcome-learn-more'
                    );
                    $icon = isset($icons[$type]) ? $icons[$type] : 'admin-generic';
                    ?>
                    <span class="dashicons dashicons-<?php echo $icon; ?>" style="margin-right: 8px;"></span>
                    <?php echo esc_html($label); ?>
                </h2>
            </div>
            
            <div class="template-body" style="background: white; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px;">
                <p class="template-description">
                    <?php
                    $descriptions = array(
                        'Article' => 'Cho bài viết, blog post, tin tức. Giúp Google hiển thị rich snippets với hình ảnh, tác giả, ngày đăng.',
                        'LocalBusiness' => 'Cho doanh nghiệp địa phương. Hiển thị địa chỉ, số điện thoại, giờ mở cửa trên Google Maps.',
                        'Product' => 'Cho sản phẩm. Hiển thị giá, đánh giá, tình trạng còn hàng trên kết quả tìm kiếm.',
                        'FAQ' => 'Cho trang câu hỏi thường gặp. Hiển thị dạng accordion trên Google Search.',
                        'HowTo' => 'Cho hướng dẫn từng bước. Hiển thị danh sách các bước thực hiện.',
                        'Organization' => 'Cho thông tin tổ chức, công ty. Hiển thị logo, địa chỉ, thông tin liên hệ.',
                        'Person' => 'Cho hồ sơ cá nhân, chuyên gia. Hiển thị thông tin nghề nghiệp, mạng xã hội.',
                        'Event' => 'Cho sự kiện. Hiển thị ngày giờ, địa điểm, giá vé trên Google Search.',
                        'Recipe' => 'Cho công thức nấu ăn. Hiển thị nguyên liệu, cách làm, thời gian nấu.',
                        'Course' => 'Cho khóa học. Hiển thị thông tin giảng viên, giá, thời lượng.'
                    );
                    echo isset($descriptions[$type]) ? $descriptions[$type] : 'Schema template cho ' . $label;
                    ?>
                </p>
                
                <div class="template-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                    <button class="button button-primary use-template" data-type="<?php echo esc_attr($type); ?>">
                        <span class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></span>
                        Sử Dụng Template
                    </button>
                    <button class="button preview-template" data-type="<?php echo esc_attr($type); ?>">
                        <span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span>
                        Xem Mẫu
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
    </div>
    
    <!-- Preview Modal -->
    <div id="template-preview-modal" style="display: none;">
        <div class="modal-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); z-index: 9998;"></div>
        <div class="modal-content" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 8px; max-width: 800px; max-height: 80vh; overflow-y: auto; z-index: 9999; box-shadow: 0 4px 20px rgba(0,0,0,0.3);">
            <button class="close-modal" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; line-height: 30px; text-align: center;">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
            <h2 id="preview-title" style="margin-top: 0;"></h2>
            <pre id="preview-code" style="background: #f5f5f5; padding: 20px; border-radius: 4px; overflow-x: auto; font-size: 13px; line-height: 1.5;"></pre>
        </div>
    </div>
</div>

<style>
.kata-schema-templates .template-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.kata-schema-templates .template-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.kata-schema-templates .template-description {
    color: #555;
    line-height: 1.6;
    min-height: 80px;
}

.kata-schema-templates .template-actions {
    display: flex;
    gap: 10px;
}

.kata-schema-templates .template-actions .button {
    flex: 1;
}

#template-preview-modal .modal-overlay {
    cursor: pointer;
}

#template-preview-modal pre {
    font-family: 'Courier New', Courier, monospace;
}

#template-preview-modal .close-modal:hover {
    background: #f0f0f1;
    border-radius: 50%;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Template samples (same as in class-schema-templates.php)
    var templateSamples = <?php 
        // Get sample templates
        $samples = array();
        $reflection = new ReflectionClass('KATA_Schema_Templates');
        $methods = $reflection->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PRIVATE);
        
        foreach ($schema_types as $type => $label) {
            // Create a sample based on type
            $sample = array(
                '@context' => 'https://schema.org',
                '@type' => $type,
                'name' => 'Tên ' . $label,
                'description' => 'Mô tả về ' . $label
            );
            $samples[$type] = $sample;
        }
        
        echo json_encode($samples, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    ?>;
    
    // Use template
    $('.use-template').on('click', function() {
        var type = $(this).data('type');
        
        if (confirm('Tạo schema mới từ template "' + type + '"?')) {
            // Redirect to add page with type
            window.location.href = '<?php echo admin_url('admin.php?page=kata-schema-add'); ?>&template=' + type;
        }
    });
    
    // Preview template
    $('.preview-template').on('click', function() {
        var type = $(this).data('type');
        var sample = templateSamples[type];
        
        $('#preview-title').text('Template: ' + type);
        $('#preview-code').text(JSON.stringify(sample, null, 2));
        $('#template-preview-modal').fadeIn();
    });
    
    // Close modal
    $('.close-modal, .modal-overlay').on('click', function() {
        $('#template-preview-modal').fadeOut();
    });
    
    // Prevent modal content click from closing
    $('.modal-content').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
