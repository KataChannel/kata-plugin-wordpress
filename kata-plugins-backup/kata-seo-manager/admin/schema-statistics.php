<?php
/**
 * Schema Statistics Page - Thống kê Schema
 * 
 * Displays comprehensive schema statistics:
 * - Schema types overview
 * - Posts/Pages using each schema type
 * - Recommended schemas for each post/page
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

// Enqueue styles and scripts
wp_enqueue_style(
    'kata-schema-statistics',
    KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/schema-statistics.css',
    array(),
    KATA_SEO_MANAGER_VERSION
);

wp_enqueue_script(
    'kata-schema-statistics',
    KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/schema-statistics.js',
    array('jquery'),
    KATA_SEO_MANAGER_VERSION,
    true
);

global $wpdb;

// Get all schema types available
$available_schema_types = array(
    'Article', 'Breadcrumb', 'Carousel', 'Course', 'Dataset',
    'Forum', 'EduQA', 'EmployerRating', 'Event', 'FAQ',
    'HowTo', 'ImageMetadata', 'JobPosting', 'LocalBusiness',
    'MathSolver', 'Movie', 'Organization', 'PracticeProblem',
    'Product', 'ProfilePage', 'Recipe', 'Review',
    'Sitelinks', 'Speakable', 'Video', 'WebPage'
);

// Function to extract all shortcodes from content
function kata_extract_schemas_from_content($content) {
    $schemas = array();
    
    // Pattern to match all kata schema shortcodes
    // Matches: [kata_article ...], [kata_faq ...], etc.
    preg_match_all('/\[kata_([\w_]+)(?:\s+[^\]]*?)?\]/i', $content, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $schema_shortcode) {
            // Convert shortcode to schema type name
            // kata_article -> Article, kata_faq -> FAQ, kata_local_business -> LocalBusiness
            $schema_type = str_replace('_', '', ucwords($schema_shortcode, '_'));
            $schemas[] = $schema_type;
        }
    }
    
    return array_unique($schemas);
}

// Get all published posts and pages with their content
$posts_pages = $wpdb->get_results("
    SELECT ID, post_title, post_type, post_content, post_date, post_modified
    FROM {$wpdb->posts}
    WHERE post_status = 'publish' 
    AND post_type IN ('post', 'page')
    ORDER BY post_modified DESC
");

// Analyze schema usage
$schema_usage = array();
$post_schema_map = array();
$schema_count_by_type = array();

foreach ($posts_pages as $post) {
    $schemas_in_post = kata_extract_schemas_from_content($post->post_content);
    
    if (!empty($schemas_in_post)) {
        $post_schema_map[$post->ID] = array(
            'title' => $post->post_title,
            'type' => $post->post_type,
            'schemas' => $schemas_in_post,
            'schema_count' => count($schemas_in_post),
            'modified' => $post->post_modified,
            'edit_link' => get_edit_post_link($post->ID)
        );
        
        foreach ($schemas_in_post as $schema) {
            if (!isset($schema_count_by_type[$schema])) {
                $schema_count_by_type[$schema] = 0;
            }
            $schema_count_by_type[$schema]++;
        }
    }
}

// Sort schema types by usage count
arsort($schema_count_by_type);

// Calculate recommendations for posts without certain schemas
function kata_recommend_schemas($post_type, $post_title, $current_schemas) {
    $recommendations = array();
    
    // Base recommendations for all posts/pages
    $base_schemas = array('Breadcrumb', 'WebPage');
    
    if ($post_type === 'post') {
        $base_schemas[] = 'Article';
        
        // Content-based recommendations
        if (stripos($post_title, 'hướng dẫn') !== false || stripos($post_title, 'cách') !== false) {
            $base_schemas[] = 'HowTo';
        }
        if (stripos($post_title, 'câu hỏi') !== false || stripos($post_title, 'FAQ') !== false) {
            $base_schemas[] = 'FAQ';
        }
        if (stripos($post_title, 'video') !== false) {
            $base_schemas[] = 'Video';
        }
        if (stripos($post_title, 'công thức') !== false || stripos($post_title, 'recipe') !== false) {
            $base_schemas[] = 'Recipe';
        }
        if (stripos($post_title, 'review') !== false || stripos($post_title, 'đánh giá') !== false) {
            $base_schemas[] = 'Review';
        }
        if (stripos($post_title, 'sản phẩm') !== false || stripos($post_title, 'product') !== false) {
            $base_schemas[] = 'Product';
        }
        if (stripos($post_title, 'sự kiện') !== false || stripos($post_title, 'event') !== false) {
            $base_schemas[] = 'Event';
        }
        if (stripos($post_title, 'khóa học') !== false || stripos($post_title, 'course') !== false) {
            $base_schemas[] = 'Course';
        }
    } else if ($post_type === 'page') {
        // Page-specific recommendations
        if (stripos($post_title, 'liên hệ') !== false || stripos($post_title, 'contact') !== false) {
            $base_schemas[] = 'LocalBusiness';
            $base_schemas[] = 'Organization';
        }
        if (stripos($post_title, 'giới thiệu') !== false || stripos($post_title, 'about') !== false) {
            $base_schemas[] = 'Organization';
        }
        if (stripos($post_title, 'tuyển dụng') !== false || stripos($post_title, 'job') !== false) {
            $base_schemas[] = 'JobPosting';
        }
    }
    
    // Filter out already existing schemas
    foreach ($base_schemas as $schema) {
        if (!in_array($schema, $current_schemas)) {
            $recommendations[] = $schema;
        }
    }
    
    return array_unique($recommendations);
}

// Statistics
$total_posts_pages = count($posts_pages);
$posts_with_schema = count($post_schema_map);
$posts_without_schema = $total_posts_pages - $posts_with_schema;
$total_schema_types_used = count($schema_count_by_type);
$unused_schema_types = array_diff($available_schema_types, array_keys($schema_count_by_type));

?>

<div class="wrap kata-schema-statistics">
    <!-- Header -->
    <div class="kata-stats-header">
        <div class="kata-stats-header-content">
            <div class="kata-stats-brand">
                <div class="kata-stats-icon">
                    📊
                </div>
                <div class="kata-stats-title">
                    <h1><?php _e('Thống kê Schema', 'kata-seo-manager'); ?></h1>
                    <p class="kata-subtitle"><?php _e('Phân tích và gợi ý Schema cho nội dung', 'kata-seo-manager'); ?></p>
                </div>
            </div>
            <div class="kata-stats-actions">
                <button class="kata-btn kata-btn-outline" onclick="location.reload()">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                    </svg>
                    <?php _e('Làm mới', 'kata-seo-manager'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="kata-stats-container">
        <!-- Overview Cards -->
        <div class="kata-stats-overview">
            <div class="kata-stat-card total">
                <div class="kata-stat-icon-wrapper">
                    <span class="dashicons dashicons-media-document"></span>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo $total_posts_pages; ?></div>
                    <div class="kata-stat-label"><?php _e('Tổng Posts & Pages', 'kata-seo-manager'); ?></div>
                </div>
            </div>

            <div class="kata-stat-card with-schema">
                <div class="kata-stat-icon-wrapper success">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo $posts_with_schema; ?></div>
                    <div class="kata-stat-label"><?php _e('Có Schema', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-percent">
                        <?php 
                        $percent = $total_posts_pages > 0 ? round(($posts_with_schema / $total_posts_pages) * 100, 1) : 0;
                        echo $percent . '%';
                        ?>
                    </div>
                </div>
            </div>

            <div class="kata-stat-card without-schema">
                <div class="kata-stat-icon-wrapper warning">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo $posts_without_schema; ?></div>
                    <div class="kata-stat-label"><?php _e('Chưa có Schema', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-percent">
                        <?php 
                        $percent_without = $total_posts_pages > 0 ? round(($posts_without_schema / $total_posts_pages) * 100, 1) : 0;
                        echo $percent_without . '%';
                        ?>
                    </div>
                </div>
            </div>

            <div class="kata-stat-card schema-types">
                <div class="kata-stat-icon-wrapper info">
                    <span class="dashicons dashicons-tag"></span>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo $total_schema_types_used; ?></div>
                    <div class="kata-stat-label"><?php _e('Schema Types đang dùng', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-meta">
                        <?php echo sprintf(__('trên %d types có sẵn', 'kata-seo-manager'), count($available_schema_types)); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schema Types Usage -->
        <div class="kata-stats-section">
            <div class="kata-section-header">
                <h2>
                    <span class="dashicons dashicons-chart-bar"></span>
                    <?php _e('Thống kê theo loại Schema', 'kata-seo-manager'); ?>
                </h2>
                <div class="kata-section-meta">
                    <?php echo sprintf(__('%d/%d Schema Types đang được sử dụng', 'kata-seo-manager'), $total_schema_types_used, count($available_schema_types)); ?>
                </div>
            </div>

            <?php if (!empty($schema_count_by_type)): ?>
            <div class="kata-schema-types-grid">
                <?php foreach ($schema_count_by_type as $schema_type => $count): ?>
                <div class="kata-schema-type-card">
                    <div class="schema-type-header">
                        <h3><?php echo esc_html($schema_type); ?></h3>
                        <span class="schema-count-badge"><?php echo $count; ?></span>
                    </div>
                    <div class="schema-type-bar">
                        <div class="schema-type-bar-fill" style="width: <?php echo ($count / $posts_with_schema * 100); ?>%"></div>
                    </div>
                    <div class="schema-type-meta">
                        <?php echo sprintf(__('%d bài đang dùng (%d%%)', 'kata-seo-manager'), $count, round($count / $posts_with_schema * 100, 1)); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="kata-empty-state">
                <span class="dashicons dashicons-info"></span>
                <p><?php _e('Chưa có Schema nào được sử dụng trong nội dung.', 'kata-seo-manager'); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Unused Schema Types -->
        <?php if (!empty($unused_schema_types)): ?>
        <div class="kata-stats-section">
            <div class="kata-section-header">
                <h2>
                    <span class="dashicons dashicons-editor-help"></span>
                    <?php _e('Schema Types chưa sử dụng', 'kata-seo-manager'); ?>
                </h2>
                <div class="kata-section-meta">
                    <?php echo sprintf(__('%d Schema Types có thể thêm vào nội dung', 'kata-seo-manager'), count($unused_schema_types)); ?>
                </div>
            </div>
            <div class="kata-unused-schemas">
                <?php foreach ($unused_schema_types as $unused_schema): ?>
                <span class="kata-schema-tag unused"><?php echo esc_html($unused_schema); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Posts/Pages with Schema Details -->
        <div class="kata-stats-section">
            <div class="kata-section-header">
                <h2>
                    <span class="dashicons dashicons-admin-page"></span>
                    <?php _e('Chi tiết Posts & Pages có Schema', 'kata-seo-manager'); ?>
                </h2>
                <div class="kata-section-actions">
                    <input type="text" id="kata-search-posts" class="kata-search-input" placeholder="<?php _e('Tìm kiếm bài viết...', 'kata-seo-manager'); ?>">
                    <select id="kata-filter-schema" class="kata-filter-select">
                        <option value=""><?php _e('Tất cả Schema Types', 'kata-seo-manager'); ?></option>
                        <?php foreach (array_keys($schema_count_by_type) as $schema_type): ?>
                        <option value="<?php echo esc_attr($schema_type); ?>"><?php echo esc_html($schema_type); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if (!empty($post_schema_map)): ?>
            <div class="kata-posts-table-wrapper">
                <table class="kata-posts-table">
                    <thead>
                        <tr>
                            <th><?php _e('Bài viết', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Loại', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Schema đang dùng', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Gợi ý thêm', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Cập nhật', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Thao tác', 'kata-seo-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="kata-posts-tbody">
                        <?php foreach ($post_schema_map as $post_id => $post_data): 
                            $post_obj = get_post($post_id);
                            $recommendations = kata_recommend_schemas($post_data['type'], $post_data['title'], $post_data['schemas']);
                        ?>
                        <tr data-post-id="<?php echo $post_id; ?>" 
                            data-schemas="<?php echo esc_attr(implode(',', $post_data['schemas'])); ?>"
                            data-title="<?php echo esc_attr(strtolower($post_data['title'])); ?>">
                            <td>
                                <div class="post-title-cell">
                                    <strong><?php echo esc_html($post_data['title']); ?></strong>
                                    <div class="post-meta">
                                        ID: <?php echo $post_id; ?> | 
                                        <?php echo $post_data['schema_count']; ?> schema(s)
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="post-type-badge <?php echo $post_data['type']; ?>">
                                    <?php echo $post_data['type'] === 'post' ? __('Bài viết', 'kata-seo-manager') : __('Trang', 'kata-seo-manager'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="schema-tags">
                                    <?php foreach ($post_data['schemas'] as $schema): ?>
                                    <span class="kata-schema-tag active"><?php echo esc_html($schema); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($recommendations)): ?>
                                <div class="schema-recommendations">
                                    <?php foreach ($recommendations as $rec_schema): ?>
                                    <span class="kata-schema-tag recommended" 
                                          title="<?php echo sprintf(__('Có thể thêm %s schema', 'kata-seo-manager'), $rec_schema); ?>">
                                        <span class="dashicons dashicons-plus-alt2"></span>
                                        <?php echo esc_html($rec_schema); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <span class="no-recommendations"><?php _e('—', 'kata-seo-manager'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="post-modified">
                                    <?php echo human_time_diff(strtotime($post_data['modified']), current_time('timestamp')); ?> 
                                    <?php _e('trước', 'kata-seo-manager'); ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?php echo esc_url($post_data['edit_link']); ?>" class="kata-btn-small kata-btn-edit">
                                    <span class="dashicons dashicons-edit"></span>
                                    <?php _e('Sửa', 'kata-seo-manager'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="kata-empty-state">
                <span class="dashicons dashicons-info"></span>
                <p><?php _e('Không có bài viết hoặc trang nào sử dụng Schema.', 'kata-seo-manager'); ?></p>
                <p><?php _e('Hãy mở trình soạn thảo và thêm Schema vào nội dung bằng nút KATA SEO trên thanh công cụ!', 'kata-seo-manager'); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Posts WITHOUT Schema (for recommendations) -->
        <?php 
        $posts_without_any_schema = array_filter($posts_pages, function($post) use ($post_schema_map) {
            return !isset($post_schema_map[$post->ID]);
        });
        
        if (!empty($posts_without_any_schema)):
        ?>
        <div class="kata-stats-section">
            <div class="kata-section-header">
                <h2>
                    <span class="dashicons dashicons-lightbulb"></span>
                    <?php _e('Posts & Pages cần thêm Schema', 'kata-seo-manager'); ?>
                </h2>
                <div class="kata-section-meta">
                    <?php echo sprintf(__('%d bài chưa có schema nào', 'kata-seo-manager'), count($posts_without_any_schema)); ?>
                </div>
            </div>

            <div class="kata-posts-table-wrapper">
                <table class="kata-posts-table">
                    <thead>
                        <tr>
                            <th><?php _e('Bài viết', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Loại', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Schema gợi ý', 'kata-seo-manager'); ?></th>
                            <th><?php _e('Thao tác', 'kata-seo-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0;
                        foreach ($posts_without_any_schema as $post): 
                            if ($count >= 20) break; // Chỉ hiện 20 bài đầu tiên
                            $recommendations = kata_recommend_schemas($post->post_type, $post->post_title, array());
                            $count++;
                        ?>
                        <tr>
                            <td>
                                <div class="post-title-cell">
                                    <strong><?php echo esc_html($post->post_title); ?></strong>
                                    <div class="post-meta">ID: <?php echo $post->ID; ?></div>
                                </div>
                            </td>
                            <td>
                                <span class="post-type-badge <?php echo $post->post_type; ?>">
                                    <?php echo $post->post_type === 'post' ? __('Bài viết', 'kata-seo-manager') : __('Trang', 'kata-seo-manager'); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($recommendations)): ?>
                                <div class="schema-recommendations">
                                    <?php foreach ($recommendations as $rec_schema): ?>
                                    <span class="kata-schema-tag suggested">
                                        <span class="dashicons dashicons-star-filled"></span>
                                        <?php echo esc_html($rec_schema); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <span class="no-recommendations"><?php _e('Thêm schema phù hợp với nội dung', 'kata-seo-manager'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>" class="kata-btn-small kata-btn-primary">
                                    <span class="dashicons dashicons-plus-alt"></span>
                                    <?php _e('Thêm Schema', 'kata-seo-manager'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($posts_without_any_schema) > 20): ?>
                <div class="kata-table-footer">
                    <p><?php echo sprintf(__('Hiển thị 20/%d bài. Sử dụng tính năng tìm kiếm để xem thêm.', 'kata-seo-manager'), count($posts_without_any_schema)); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Help Section -->
        <div class="kata-stats-section kata-help-section">
            <div class="kata-section-header">
                <h2>
                    <span class="dashicons dashicons-sos"></span>
                    <?php _e('Hướng dẫn sử dụng', 'kata-seo-manager'); ?>
                </h2>
            </div>
            <div class="kata-help-content">
                <div class="kata-help-card">
                    <h3><?php _e('🎯 Cách thêm Schema vào bài viết', 'kata-seo-manager'); ?></h3>
                    <ol>
                        <li><?php _e('Mở bài viết cần thêm schema', 'kata-seo-manager'); ?></li>
                        <li><?php _e('Click nút "KATA SEO" trên thanh công cụ editor', 'kata-seo-manager'); ?></li>
                        <li><?php _e('Chọn loại schema phù hợp (Article, FAQ, HowTo...)', 'kata-seo-manager'); ?></li>
                        <li><?php _e('Điền thông tin và click "Insert"', 'kata-seo-manager'); ?></li>
                        <li><?php _e('Lưu bài viết', 'kata-seo-manager'); ?></li>
                    </ol>
                </div>
                <div class="kata-help-card">
                    <h3><?php _e('📊 Hiểu biểu đồ thống kê', 'kata-seo-manager'); ?></h3>
                    <ul>
                        <li><strong><?php _e('Schema đang dùng:', 'kata-seo-manager'); ?></strong> <?php _e('Các schema có trong nội dung', 'kata-seo-manager'); ?></li>
                        <li><strong><?php _e('Gợi ý thêm:', 'kata-seo-manager'); ?></strong> <?php _e('Schema phù hợp dựa trên tiêu đề', 'kata-seo-manager'); ?></li>
                        <li><strong><?php _e('Schema chưa dùng:', 'kata-seo-manager'); ?></strong> <?php _e('Các loại schema chưa triển khai', 'kata-seo-manager'); ?></li>
                    </ul>
                </div>
                <div class="kata-help-card">
                    <h3><?php _e('💡 Tips tối ưu SEO', 'kata-seo-manager'); ?></h3>
                    <ul>
                        <li><?php _e('Luôn thêm Breadcrumb cho mọi bài viết/trang', 'kata-seo-manager'); ?></li>
                        <li><?php _e('Sử dụng Article cho blog posts', 'kata-seo-manager'); ?></li>
                        <li><?php _e('Thêm FAQ schema cho bài viết dạng câu hỏi', 'kata-seo-manager'); ?></li>
                        <li><?php _e('HowTo schema tốt cho hướng dẫn từng bước', 'kata-seo-manager'); ?></li>
                        <li><?php _e('LocalBusiness cho trang liên hệ/giới thiệu công ty', 'kata-seo-manager'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
