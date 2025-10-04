<?php
/**
 * KATA Schema Markup Meta Box Template - Enhanced with Multiple Schema Support
 *
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current schema types
$current_schema_types = is_array($schema_types) ? $schema_types : array();

// Available schema types
$available_schemas = array(
    'Article' => 'Article (Bài viết)',
    'BlogPosting' => 'Blog Posting (Bài blog)',
    'NewsArticle' => 'News Article (Tin tức)',
    'Product' => 'Product (Sản phẩm)',
    'Review' => 'Review (Đánh giá)',
    'Recipe' => 'Recipe (Công thức nấu ăn)',
    'Event' => 'Event (Sự kiện)',
    'VideoObject' => 'Video (Video)',
    'Course' => 'Course (Khóa học)',
    'FAQPage' => 'FAQ Page (Trang hỏi đáp)',
    'HowTo' => 'How To (Hướng dẫn)',
    'JobPosting' => 'Job Posting (Tuyển dụng)',
    'LocalBusiness' => 'Local Business (Doanh nghiệp địa phương)',
    'Organization' => 'Organization (Tổ chức)',
    'Person' => 'Person (Người)',
    'WebPage' => 'Web Page (Trang web)'
);
?>

<div class="kata-schema-meta-box">
    <style>
        .kata-schema-meta-box { padding: 10px 0; }
        .kata-schema-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px; margin: 15px 0; }
        .kata-schema-checkbox { padding: 10px; border: 1px solid #ddd; border-radius: 4px; background: #f9f9f9; }
        .kata-schema-checkbox:hover { background: #f0f0f0; border-color: #2271b1; }
        .kata-schema-checkbox input[type="checkbox"]:checked + label { font-weight: 600; color: #2271b1; }
        .kata-schema-checkbox label { cursor: pointer; display: block; margin: 0; }
        .kata-schema-field { margin: 15px 0; padding: 15px; border: 1px solid #ccd0d4; border-radius: 4px; background: #fff; display: none; }
        .kata-schema-field.active { display: block; }
        .kata-schema-field h4 { margin: 0 0 15px 0; padding: 0 0 10px 0; border-bottom: 2px solid #2271b1; color: #2271b1; }
        .kata-schema-field label { display: block; margin: 10px 0 5px 0; font-weight: 500; }
        .kata-schema-field input[type="text"],
        .kata-schema-field input[type="number"],
        .kata-schema-field input[type="url"],
        .kata-schema-field input[type="date"],
        .kata-schema-field input[type="datetime-local"],
        .kata-schema-field select,
        .kata-schema-field textarea { width: 100%; padding: 8px; border: 1px solid #8c8f94; border-radius: 4px; }
        .kata-schema-field textarea { min-height: 80px; }
        .kata-schema-field small { display: block; margin-top: 5px; color: #646970; font-style: italic; }
    </style>

    <div class="kata-schema-enable">
        <label>
            <input type="checkbox" name="kata_seo_schema_enabled" value="1" <?php checked($schema_enabled, true); ?>>
            <strong>Bật Schema Markup cho bài viết này</strong>
        </label>
        <p class="description">Kích hoạt để thêm dữ liệu có cấu trúc Schema.org vào bài viết, giúp tăng khả năng hiển thị Rich Snippets trên Google.</p>
    </div>

    <div id="kata-schema-types-section" style="margin-top: 20px; <?php echo $schema_enabled ? '' : 'display:none;'; ?>">
        <h3 style="margin: 20px 0 10px 0;">Chọn loại Schema Markup (có thể chọn nhiều)</h3>
        <p class="description">Chọn một hoặc nhiều loại schema phù hợp với nội dung bài viết. Mỗi loại schema sẽ có các trường thông tin riêng.</p>
        
        <div class="kata-schema-grid">
            <?php foreach ($available_schemas as $schema_key => $schema_label): ?>
                <div class="kata-schema-checkbox">
                    <input type="checkbox" 
                           id="kata_schema_<?php echo esc_attr($schema_key); ?>" 
                           name="kata_seo_schema_types[]" 
                           value="<?php echo esc_attr($schema_key); ?>"
                           class="kata-schema-type-checkbox"
                           <?php checked(in_array($schema_key, $current_schema_types)); ?>>
                    <label for="kata_schema_<?php echo esc_attr($schema_key); ?>">
                        <?php echo esc_html($schema_label); ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Product Schema Fields -->
        <div id="kata-schema-fields-Product" class="kata-schema-field <?php echo in_array('Product', $current_schema_types) ? 'active' : ''; ?>">
            <h4>🛍️ Thông tin Sản phẩm (Product)</h4>
            
            <label for="kata_seo_product_price">Giá sản phẩm *</label>
            <input type="number" step="0.01" id="kata_seo_product_price" name="kata_seo_product_price" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_product_price', true)); ?>">
            <small>Ví dụ: 199000 hoặc 199.99</small>

            <label for="kata_seo_product_currency">Đơn vị tiền tệ</label>
            <select id="kata_seo_product_currency" name="kata_seo_product_currency">
                <option value="VND" <?php selected(get_post_meta($post->ID, '_kata_seo_product_currency', true), 'VND'); ?>>VND (Việt Nam Đồng)</option>
                <option value="USD" <?php selected(get_post_meta($post->ID, '_kata_seo_product_currency', true), 'USD'); ?>>USD (US Dollar)</option>
                <option value="EUR" <?php selected(get_post_meta($post->ID, '_kata_seo_product_currency', true), 'EUR'); ?>>EUR (Euro)</option>
            </select>

            <label for="kata_seo_product_brand">Thương hiệu</label>
            <input type="text" id="kata_seo_product_brand" name="kata_seo_product_brand" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_product_brand', true)); ?>">

            <label for="kata_seo_product_availability">Tình trạng kho</label>
            <select id="kata_seo_product_availability" name="kata_seo_product_availability">
                <option value="InStock" <?php selected(get_post_meta($post->ID, '_kata_seo_product_availability', true), 'InStock'); ?>>Còn hàng</option>
                <option value="OutOfStock" <?php selected(get_post_meta($post->ID, '_kata_seo_product_availability', true), 'OutOfStock'); ?>>Hết hàng</option>
                <option value="PreOrder" <?php selected(get_post_meta($post->ID, '_kata_seo_product_availability', true), 'PreOrder'); ?>>Đặt trước</option>
            </select>
        </div>

        <!-- Review Schema Fields -->
        <div id="kata-schema-fields-Review" class="kata-schema-field <?php echo in_array('Review', $current_schema_types) ? 'active' : ''; ?>">
            <h4>⭐ Thông tin Đánh giá (Review)</h4>
            
            <label for="kata_seo_review_rating">Điểm đánh giá (1-5) *</label>
            <input type="number" step="0.1" min="1" max="5" id="kata_seo_review_rating" name="kata_seo_review_rating" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_review_rating', true)); ?>">
            <small>Ví dụ: 4.5 (từ 1 đến 5 sao)</small>

            <label for="kata_seo_review_item">Tên sản phẩm/dịch vụ được đánh giá *</label>
            <input type="text" id="kata_seo_review_item" name="kata_seo_review_item" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_review_item', true)); ?>">
        </div>

        <!-- Recipe Schema Fields -->
        <div id="kata-schema-fields-Recipe" class="kata-schema-field <?php echo in_array('Recipe', $current_schema_types) ? 'active' : ''; ?>">
            <h4>🍳 Thông tin Công thức (Recipe)</h4>
            
            <label for="kata_seo_recipe_prep_time">Thời gian chuẩn bị (phút)</label>
            <input type="number" id="kata_seo_recipe_prep_time" name="kata_seo_recipe_prep_time" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_recipe_prep_time', true)); ?>">
            <small>Ví dụ: 30 (phút)</small>

            <label for="kata_seo_recipe_cook_time">Thời gian nấu (phút)</label>
            <input type="number" id="kata_seo_recipe_cook_time" name="kata_seo_recipe_cook_time" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_recipe_cook_time', true)); ?>">

            <label for="kata_seo_recipe_calories">Calories</label>
            <input type="number" id="kata_seo_recipe_calories" name="kata_seo_recipe_calories" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_recipe_calories', true)); ?>">
            <small>Ví dụ: 450 (calories/khẩu phần)</small>
        </div>

        <!-- Event Schema Fields -->
        <div id="kata-schema-fields-Event" class="kata-schema-field <?php echo in_array('Event', $current_schema_types) ? 'active' : ''; ?>">
            <h4>📅 Thông tin Sự kiện (Event)</h4>
            
            <label for="kata_seo_event_start_date">Ngày bắt đầu *</label>
            <input type="datetime-local" id="kata_seo_event_start_date" name="kata_seo_event_start_date" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_event_start_date', true)); ?>">

            <label for="kata_seo_event_end_date">Ngày kết thúc</label>
            <input type="datetime-local" id="kata_seo_event_end_date" name="kata_seo_event_end_date" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_event_end_date', true)); ?>">

            <label for="kata_seo_event_location">Địa điểm *</label>
            <input type="text" id="kata_seo_event_location" name="kata_seo_event_location" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_event_location', true)); ?>">
        </div>

        <!-- VideoObject Schema Fields -->
        <div id="kata-schema-fields-VideoObject" class="kata-schema-field <?php echo in_array('VideoObject', $current_schema_types) ? 'active' : ''; ?>">
            <h4>🎥 Thông tin Video (VideoObject)</h4>
            
            <label for="kata_seo_video_duration">Thời lượng video (giây)</label>
            <input type="number" id="kata_seo_video_duration" name="kata_seo_video_duration" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_video_duration', true)); ?>">
            <small>Ví dụ: 300 (5 phút = 300 giây)</small>

            <label for="kata_seo_video_upload_date">Ngày tải lên</label>
            <input type="date" id="kata_seo_video_upload_date" name="kata_seo_video_upload_date" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_video_upload_date', true)); ?>">

            <label for="kata_seo_video_thumbnail">URL ảnh thumbnail</label>
            <input type="url" id="kata_seo_video_thumbnail" name="kata_seo_video_thumbnail" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_video_thumbnail', true)); ?>">
        </div>

        <!-- Course Schema Fields -->
        <div id="kata-schema-fields-Course" class="kata-schema-field <?php echo in_array('Course', $current_schema_types) ? 'active' : ''; ?>">
            <h4>🎓 Thông tin Khóa học (Course)</h4>
            
            <label for="kata_seo_course_provider">Nhà cung cấp khóa học *</label>
            <input type="text" id="kata_seo_course_provider" name="kata_seo_course_provider" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_course_provider', true)); ?>">

            <label for="kata_seo_course_price">Học phí</label>
            <input type="number" step="0.01" id="kata_seo_course_price" name="kata_seo_course_price" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_course_price', true)); ?>">
            <small>Nhập 0 nếu khóa học miễn phí</small>
        </div>

        <!-- HowTo Schema Fields -->
        <div id="kata-schema-fields-HowTo" class="kata-schema-field <?php echo in_array('HowTo', $current_schema_types) ? 'active' : ''; ?>">
            <h4>📝 Thông tin Hướng dẫn (How To)</h4>
            
            <label for="kata_seo_howto_total_time">Tổng thời gian (phút)</label>
            <input type="number" id="kata_seo_howto_total_time" name="kata_seo_howto_total_time" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_howto_total_time', true)); ?>">

            <label for="kata_seo_howto_supply">Vật liệu cần thiết (mỗi dòng một vật liệu)</label>
            <textarea id="kata_seo_howto_supply" name="kata_seo_howto_supply"><?php echo esc_textarea(get_post_meta($post->ID, '_kata_seo_howto_supply', true)); ?></textarea>

            <label for="kata_seo_howto_tool">Công cụ cần thiết (mỗi dòng một công cụ)</label>
            <textarea id="kata_seo_howto_tool" name="kata_seo_howto_tool"><?php echo esc_textarea(get_post_meta($post->ID, '_kata_seo_howto_tool', true)); ?></textarea>
        </div>

        <!-- JobPosting Schema Fields -->
        <div id="kata-schema-fields-JobPosting" class="kata-schema-field <?php echo in_array('JobPosting', $current_schema_types) ? 'active' : ''; ?>">
            <h4>💼 Thông tin Tuyển dụng (Job Posting)</h4>
            
            <label for="kata_seo_job_salary">Mức lương *</label>
            <input type="text" id="kata_seo_job_salary" name="kata_seo_job_salary" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_job_salary', true)); ?>">
            <small>Ví dụ: 15000000-20000000 VND</small>

            <label for="kata_seo_job_location">Địa điểm làm việc *</label>
            <input type="text" id="kata_seo_job_location" name="kata_seo_job_location" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_job_location', true)); ?>">

            <label for="kata_seo_job_date_posted">Ngày đăng</label>
            <input type="date" id="kata_seo_job_date_posted" name="kata_seo_job_date_posted" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_job_date_posted', true)); ?>">

            <label for="kata_seo_job_valid_through">Hạn nộp hồ sơ</label>
            <input type="date" id="kata_seo_job_valid_through" name="kata_seo_job_valid_through" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_job_valid_through', true)); ?>">
        </div>

        <!-- LocalBusiness Schema Fields -->
        <div id="kata-schema-fields-LocalBusiness" class="kata-schema-field <?php echo in_array('LocalBusiness', $current_schema_types) ? 'active' : ''; ?>">
            <h4>🏪 Thông tin Doanh nghiệp (Local Business)</h4>
            
            <label for="kata_seo_business_address">Địa chỉ *</label>
            <textarea id="kata_seo_business_address" name="kata_seo_business_address"><?php echo esc_textarea(get_post_meta($post->ID, '_kata_seo_business_address', true)); ?></textarea>

            <label for="kata_seo_business_phone">Số điện thoại *</label>
            <input type="text" id="kata_seo_business_phone" name="kata_seo_business_phone" 
                   value="<?php echo esc_attr(get_post_meta($post->ID, '_kata_seo_business_phone', true)); ?>">

            <label for="kata_seo_business_hours">Giờ làm việc (mỗi dòng một ngày)</label>
            <textarea id="kata_seo_business_hours" name="kata_seo_business_hours"><?php echo esc_textarea(get_post_meta($post->ID, '_kata_seo_business_hours', true)); ?></textarea>
            <small>Ví dụ: Thứ 2-6: 8:00-17:00<br>Thứ 7: 8:00-12:00</small>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Toggle schema types section when enable checkbox changes
        $('input[name="kata_seo_schema_enabled"]').on('change', function() {
            if ($(this).is(':checked')) {
                $('#kata-schema-types-section').slideDown();
            } else {
                $('#kata-schema-types-section').slideUp();
            }
        });

        // Toggle schema fields when checkboxes change
        $('.kata-schema-type-checkbox').on('change', function() {
            var schemaType = $(this).val();
            var fieldsDiv = $('#kata-schema-fields-' + schemaType);
            
            if ($(this).is(':checked')) {
                fieldsDiv.slideDown().addClass('active');
            } else {
                fieldsDiv.slideUp().removeClass('active');
            }
        });
    });
    </script>
</div>
