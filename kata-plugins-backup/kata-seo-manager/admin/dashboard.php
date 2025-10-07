<?php
/**
 * Dashboard Page - Senior UI Design
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$statistics = new KATA_SEO_Statistics();
$overview = $statistics->get_overview();
$validation_report = $statistics->get_validation_report();
?>

<div class="wrap kata-seo-dashboard-v2">
    <!-- Header Section -->
    <div class="kata-dashboard-header">
        <div class="kata-header-content">
            <div class="kata-brand">
                <div class="kata-logo">
                    <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
                        <path d="M8 8h16v16H8z" fill="#2563eb"/>
                        <path d="M12 12h8v8h-8z" fill="#ffffff"/>
                        <path d="M14 14h4v4h-4z" fill="#2563eb"/>
                    </svg>
                </div>
                <div class="kata-title">
                    <h1><?php _e('KATA SEO Manager', 'kata-seo-manager'); ?></h1>
                    <p class="kata-subtitle"><?php _e('Hệ thống quản lý Schema nâng cao', 'kata-seo-manager'); ?></p>
                </div>
            </div>
            <div class="kata-header-actions">
                <button class="kata-btn kata-btn-outline" onclick="location.reload()">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                        <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                    </svg>
                    <?php _e('Làm mới', 'kata-seo-manager'); ?>
                </button>
                <button class="kata-btn kata-btn-primary" onclick="window.open('https://developers.google.com/search/docs/appearance/structured-data', '_blank')">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-.5-.5C11.5 7.364 10 5.5 8.636 3.5z"/>
                        <path d="M6.5 7.5a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v.938l.4 1.599a1 1 0 0 1-.416 1.074l-.93.62a1 1 0 0 1-1.109 0l-.93-.62a1 1 0 0 1-.415-1.074l.4-1.599V7.5z"/>
                    </svg>
                    <?php _e('Hướng dẫn Schema', 'kata-seo-manager'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="kata-dashboard-container">
        <!-- Modern Stats Cards -->
        <div class="kata-stats-overview">
            <div class="kata-stat-card-v2 total-schemas">
                <div class="kata-stat-header">
                    <div class="kata-stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.5 2A1.5 1.5 0 0 0 4 3.5v9a1.5 1.5 0 0 0 1.5 1.5h8a1.5 1.5 0 0 0 1.5-1.5v-9a1.5 1.5 0 0 0-1.5-1.5h-8ZM5 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9Z"/>
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2H2Z"/>
                        </svg>
                    </div>
                    <div class="kata-stat-trend positive">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                            <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
                        </svg>
                        <span>+12%</span>
                    </div>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number" data-count="<?php echo esc_attr($overview['total_schemas']); ?>">0</div>
                    <div class="kata-stat-label"><?php _e('Tổng Schema', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-description"><?php _e('Các triển khai schema hoạt động', 'kata-seo-manager'); ?></div>
                </div>
            </div>
            
            <div class="kata-stat-card-v2 active-schemas">
                <div class="kata-stat-header">
                    <div class="kata-stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        </svg>
                    </div>
                    <div class="kata-stat-trend positive">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                            <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
                        </svg>
                        <span>+8%</span>
                    </div>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number" data-count="<?php echo esc_attr($overview['active_schemas']); ?>">0</div>
                    <div class="kata-stat-label"><?php _e('Schema Hoạt động', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-description"><?php _e('Hiện đang hiển thị trên site', 'kata-seo-manager'); ?></div>
                </div>
            </div>
            
            <div class="kata-stat-card-v2 validation-rate">
                <div class="kata-stat-header">
                    <div class="kata-stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                            <path d="M10.854 5.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 7.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                    </div>
                    <div class="kata-stat-trend neutral">
                        <span>100%</span>
                    </div>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number" data-count="<?php echo esc_attr($overview['validation']['pass_rate']); ?>">0</div>
                    <div class="kata-stat-label"><?php _e('Tỷ lệ Xác thực', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-description"><?php _e('Phần trăm hợp lệ của schema', 'kata-seo-manager'); ?></div>
                </div>
            </div>
            
            <div class="kata-stat-card-v2 schema-types">
                <div class="kata-stat-header">
                    <div class="kata-stat-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                        </svg>
                    </div>
                    <div class="kata-stat-trend positive">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                            <path d="m7.247 4.86-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>
                        </svg>
                        <span>+5</span>
                    </div>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number" data-count="<?php echo count($overview['schemas_by_type']); ?>">0</div>
                    <div class="kata-stat-label"><?php _e('Các loại Schema', 'kata-seo-manager'); ?></div>
                    <div class="kata-stat-description"><?php _e('Các loại khác nhau đang sử dụng', 'kata-seo-manager'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Main Content Grid -->
        <div class="kata-main-content">
            <!-- Schema Usage Analytics -->
            <div class="kata-card kata-chart-card">
                <div class="kata-card-header">
                    <h3><?php _e('Phân tích Phân phối Schema', 'kata-seo-manager'); ?></h3>
                    <div class="kata-card-actions">
                        <button class="kata-btn-icon" title="<?php _e('Xuất dữ liệu', 'kata-seo-manager'); ?>">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                            </svg>
                        </button>
                        <button class="kata-btn-icon" title="<?php _e('Làm mới biểu đồ', 'kata-seo-manager'); ?>">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="kata-card-content">
                    <div class="kata-chart-wrapper">
                        <canvas id="kata-schema-usage-chart"></canvas>
                    </div>
                    <div class="kata-chart-legend">
                        <?php $colors = ['#2563eb', '#7c3aed', '#059669', '#dc2626', '#ea580c', '#ca8a04']; $i = 0; ?>
                        <?php foreach (array_slice($overview['schemas_by_type'], 0, 6) as $type): ?>
                            <div class="kata-legend-item">
                                <span class="kata-legend-color" style="background-color: <?php echo $colors[$i % count($colors)]; ?>"></span>
                                <span class="kata-legend-label"><?php echo esc_html(ucfirst($type['schema_type'])); ?></span>
                                <span class="kata-legend-value"><?php echo esc_html($type['count']); ?></span>
                            </div>
                            <?php $i++; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="kata-card kata-activity-card">
                <div class="kata-card-header">
                    <h3><?php _e('Hoạt động Schema gần đây', 'kata-seo-manager'); ?></h3>
                    <div class="kata-card-actions">
                        <select class="kata-filter-select">
                            <option value="all"><?php _e('Tất cả loại', 'kata-seo-manager'); ?></option>
                            <option value="article"><?php _e('Bài viết', 'kata-seo-manager'); ?></option>
                            <option value="product"><?php _e('Sản phẩm', 'kata-seo-manager'); ?></option>
                            <option value="organization"><?php _e('Tổ chức', 'kata-seo-manager'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="kata-card-content">
                    <div class="kata-activity-list">
                        <?php if (!empty($overview['recent_schemas'])): ?>
                            <?php foreach (array_slice($overview['recent_schemas'], 0, 8) as $schema): ?>
                                <div class="kata-activity-item">
                                    <div class="kata-activity-icon">
                                        <div class="kata-schema-badge <?php echo esc_attr(strtolower($schema['schema_type'])); ?>">
                                            <?php echo esc_html(strtoupper(substr($schema['schema_type'], 0, 2))); ?>
                                        </div>
                                    </div>
                                    <div class="kata-activity-content">
                                        <div class="kata-activity-title">
                                            <a href="<?php echo get_edit_post_link($schema['post_id']); ?>">
                                                <?php echo esc_html($schema['post_title'] ?? __('Chưa có tiêu đề', 'kata-seo-manager')); ?>
                                            </a>
                                        </div>
                                        <div class="kata-activity-meta">
                                            <span class="kata-schema-type"><?php echo esc_html(ucfirst($schema['schema_type'])); ?></span>
                                            <span class="kata-activity-time"><?php echo esc_html(human_time_diff(strtotime($schema['created_at']))); ?> <?php _e('ago', 'kata-seo-manager'); ?></span>
                                        </div>
                                    </div>
                                    <div class="kata-activity-status">
                                        <span class="kata-status-dot active" title="<?php _e('Active & Valid', 'kata-seo-manager'); ?>"></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="kata-empty-state">
                                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p><?php _e('No schemas found. Start by adding schema to your posts.', 'kata-seo-manager'); ?></p>
                                <button class="kata-btn kata-btn-primary kata-btn-sm"><?php _e('Add Schema', 'kata-seo-manager'); ?></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="kata-sidebar">
            <!-- Quick Actions -->
            <div class="kata-card kata-quick-actions-card">
                <div class="kata-card-header">
                    <h3><?php _e('Hành động nhanh', 'kata-seo-manager'); ?></h3>
                </div>
                <div class="kata-card-content">
                    <div class="kata-quick-actions-grid">
                        <button class="kata-quick-action-btn" id="kata-generate-demo-btn">
                            <div class="kata-quick-action-icon">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                            </div>
                            <div class="kata-quick-action-content">
                                <div class="kata-quick-action-title"><?php _e('Tạo Dữ Liệu Mẫu', 'kata-seo-manager'); ?></div>
                                <div class="kata-quick-action-desc"><?php _e('Tạo schemas, polls, wheels demo', 'kata-seo-manager'); ?></div>
                            </div>
                        </button>
                        
                        <button class="kata-quick-action-btn kata-danger" id="kata-delete-demo-btn">
                            <div class="kata-quick-action-icon">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                            </div>
                            <div class="kata-quick-action-content">
                                <div class="kata-quick-action-title"><?php _e('Xóa Dữ Liệu Mẫu', 'kata-seo-manager'); ?></div>
                                <div class="kata-quick-action-desc"><?php _e('Xóa tất cả demo content', 'kata-seo-manager'); ?></div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Validation Health -->
            <div class="kata-card kata-validation-card">
                <div class="kata-card-header">
                    <h3><?php _e('Schema Health', 'kata-seo-manager'); ?></h3>
                    <div class="kata-health-indicator excellent">
                        <span class="kata-health-dot"></span>
                        <?php _e('Excellent', 'kata-seo-manager'); ?>
                    </div>
                </div>
                <div class="kata-card-content">
                    <div class="kata-validation-overview">
                        <div class="kata-validation-metric">
                            <div class="kata-metric-value valid"><?php echo esc_html($validation_report['valid'] ?? $overview['total_schemas']); ?></div>
                            <div class="kata-metric-label"><?php _e('Valid Schemas', 'kata-seo-manager'); ?></div>
                        </div>
                        <div class="kata-validation-metric">
                            <div class="kata-metric-value warning"><?php echo esc_html($validation_report['with_warnings'] ?? 0); ?></div>
                            <div class="kata-metric-label"><?php _e('With Warnings', 'kata-seo-manager'); ?></div>
                        </div>
                        <div class="kata-validation-metric">
                            <div class="kata-metric-value error"><?php echo esc_html($validation_report['invalid'] ?? 0); ?></div>
                            <div class="kata-metric-label"><?php _e('Invalid', 'kata-seo-manager'); ?></div>
                        </div>
                    </div>
                    
                    <div class="kata-validation-progress">
                        <div class="kata-progress-bar">
                            <div class="kata-progress-fill" style="width: <?php echo esc_attr($overview['validation']['pass_rate']); ?>%"></div>
                        </div>
                        <div class="kata-progress-text"><?php echo esc_html($overview['validation']['pass_rate']); ?>% <?php _e('Health Score', 'kata-seo-manager'); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="kata-card kata-actions-card">
                <div class="kata-card-header">
                    <h3><?php _e('Quick Actions', 'kata-seo-manager'); ?></h3>
                </div>
                <div class="kata-card-content">
                    <div class="kata-action-grid">
                        <a href="<?php echo admin_url('admin.php?page=kata-seo-schema-types'); ?>" class="kata-action-item">
                            <div class="kata-action-icon primary">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319z"/>
                                </svg>
                            </div>
                            <div class="kata-action-content">
                                <div class="kata-action-title"><?php _e('Schema Types', 'kata-seo-manager'); ?></div>
                                <div class="kata-action-desc"><?php _e('Manage schema configurations', 'kata-seo-manager'); ?></div>
                            </div>
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=kata-seo-statistics'); ?>" class="kata-action-item">
                            <div class="kata-action-icon success">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zM1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                                </svg>
                            </div>
                            <div class="kata-action-content">
                                <div class="kata-action-title"><?php _e('Analytics', 'kata-seo-manager'); ?></div>
                                <div class="kata-action-desc"><?php _e('View detailed statistics', 'kata-seo-manager'); ?></div>
                            </div>
                        </a>
                        
                        <a href="<?php echo admin_url('admin.php?page=kata-seo-settings'); ?>" class="kata-action-item">
                            <div class="kata-action-icon warning">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                                </svg>
                            </div>
                            <div class="kata-action-content">
                                <div class="kata-action-title"><?php _e('Settings', 'kata-seo-manager'); ?></div>
                                <div class="kata-action-desc"><?php _e('Configure plugin options', 'kata-seo-manager'); ?></div>
                            </div>
                        </a>
                        
                        <a href="javascript:void(0)" class="kata-action-item" onclick="kataExportSchemas()">
                            <div class="kata-action-icon info">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                    <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-.33 2.679 2.679 0 0 0-.129-.14c-.065-.069-.132-.138-.198-.207a2.269 2.269 0 0 1-.137-.180c-.07-.107-.129-.22-.158-.334a.926.926 0 0 1 .053-.505c.115-.34.398-.617.711-.811a2.657 2.657 0 0 1 .644-.314c.191-.058.387-.088.584-.088.197 0 .393.03.584.088.246.081.473.196.644.314.313.194.596.471.711.811a.926.926 0 0 1 .053.505c-.029.114-.088.227-.158.334a2.269 2.269 0 0 1-.137.18c-.066.069-.133.138-.198.207-.043.047-.086.094-.129.14.39.1.716.2 1.062.33.485.18.945.398 1.482.645.371.219.699.48.897.787.21.326.275.714.08 1.102a.81.81 0 0 1-.438.42c-.257.107-.519.005-.709-.081a13.44 13.44 0 0 0-.859-.35c-.314-.116-.627-.23-.945-.344a19.697 19.697 0 0 0-1.062-.33c-.39-.1-.716-.2-1.062-.33a13.44 13.44 0 0 0-.945.344c-.286.126-.572.248-.859.35-.19.086-.452.188-.709.081z"/>
                                </svg>
                            </div>
                            <div class="kata-action-content">
                                <div class="kata-action-title"><?php _e('Export Data', 'kata-seo-manager'); ?></div>
                                <div class="kata-action-desc"><?php _e('Download schema report', 'kata-seo-manager'); ?></div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer Info -->
    <div class="kata-dashboard-footer">
        <div class="kata-footer-content">
            <div class="kata-footer-info">
                <p><?php _e('KATA SEO Manager v1.0.0 - Advanced Schema Management for WordPress', 'kata-seo-manager'); ?></p>
                <p><?php printf(__('Last updated: %s | Next scan: %s', 'kata-seo-manager'), date('M j, Y'), date('M j, Y', strtotime('+1 day'))); ?></p>
            </div>
            <div class="kata-footer-links">
                <a href="https://schema.org" target="_blank"><?php _e('Schema.org', 'kata-seo-manager'); ?></a>
                <a href="https://developers.google.com/search/docs/appearance/structured-data" target="_blank"><?php _e('Google Guide', 'kata-seo-manager'); ?></a>
                <a href="#" onclick="kataHelp()"><?php _e('Help & Support', 'kata-seo-manager'); ?></a>
            </div>
        </div>
    </div>
</div>

<script>
// Animation for number counting
function animateNumbers() {
    const numbers = document.querySelectorAll('.kata-stat-number[data-count]');
    numbers.forEach(number => {
        const target = parseInt(number.dataset.count);
        const increment = target / 50;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            number.textContent = Math.floor(current);
        }, 20);
    });
}

// Export schemas function
function kataExportSchemas() {
    const data = <?php echo json_encode($overview); ?>;
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Schema Type,Count,Status\n" +
        data.schemas_by_type.map(item => `${item.schema_type},${item.count},Active`).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "kata-schema-export-" + new Date().toISOString().split('T')[0] + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Help function
function kataHelp() {
    alert('<?php _e('For support, please visit our documentation or contact the development team.', 'kata-seo-manager'); ?>');
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    animateNumbers();
    
    // Chart initialization (placeholder)
    const ctx = document.getElementById('kata-schema-usage-chart');
    if (ctx) {
        // Simple chart placeholder - would integrate with Chart.js in production
        ctx.style.background = 'linear-gradient(45deg, #f3f4f6, #e5e7eb)';
        ctx.style.borderRadius = '8px';
    }
});

// Schema usage data for chart
var schemaUsageData = <?php echo json_encode($overview['schemas_by_type']); ?>;
</script>

<style>
/* Modern Dashboard Styles */
.kata-seo-dashboard-v2 {
    margin: 0;
    background: #f8fafc;
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

/* Header Styles */
.kata-dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin: 0 -20px 2rem 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.kata-header-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.kata-logo {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kata-title h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: white;
}

.kata-subtitle {
    margin: 0.25rem 0 0 0;
    opacity: 0.8;
    font-size: 0.875rem;
}

.kata-header-actions {
    display: flex;
    gap: 1rem;
}

/* Button Styles */
.kata-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}

.kata-btn-primary {
    background: #2563eb;
    color: white;
}

.kata-btn-primary:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.kata-btn-outline {
    background: transparent;
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
}

.kata-btn-outline:hover {
    background: rgba(255,255,255,0.1);
}

.kata-btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.kata-btn-icon {
    padding: 0.5rem;
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
}

.kata-btn-icon:hover {
    background: #f3f4f6;
    color: #374151;
}

/* Container */
.kata-dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
}

/* Stats Overview */
.kata-stats-overview {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.kata-stat-card-v2 {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.kata-stat-card-v2:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.kata-stat-card-v2::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.kata-stat-card-v2.total-schemas::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
.kata-stat-card-v2.active-schemas::before { background: linear-gradient(90deg, #10b981, #059669); }
.kata-stat-card-v2.validation-rate::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
.kata-stat-card-v2.schema-types::before { background: linear-gradient(90deg, #8b5cf6, #7c3aed); }

.kata-stat-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.kata-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.total-schemas .kata-stat-icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.active-schemas .kata-stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
.validation-rate .kata-stat-icon { background: linear-gradient(135deg, #f59e0b, #d97706); }
.schema-types .kata-stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.kata-stat-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.kata-stat-trend.positive {
    background: #dcfce7;
    color: #166534;
}

.kata-stat-trend.neutral {
    background: #fef3c7;
    color: #92400e;
}

.kata-stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.kata-stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.25rem;
}

.kata-stat-description {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Main Content */
.kata-main-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Card Styles */
.kata-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.kata-card-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
}

.kata-card-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.kata-card-content {
    padding: 1.5rem;
}

/* Chart Card */
.kata-chart-wrapper {
    height: 300px;
    margin-bottom: 1rem;
}

.kata-chart-legend {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 0.75rem;
}

.kata-legend-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.kata-legend-color {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    flex-shrink: 0;
}

.kata-legend-label {
    flex: 1;
    color: #374151;
}

.kata-legend-value {
    font-weight: 600;
    color: #111827;
}

/* Activity List */
.kata-activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.kata-activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.kata-activity-item:hover {
    background: #f9fafb;
    border-color: #d1d5db;
}

.kata-activity-icon {
    flex-shrink: 0;
}

.kata-schema-badge {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: white;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
}

.kata-activity-content {
    flex: 1;
}

.kata-activity-title {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
}

.kata-activity-title a {
    color: inherit;
    text-decoration: none;
}

.kata-activity-title a:hover {
    color: #2563eb;
}

.kata-activity-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.kata-schema-type {
    background: #f3f4f6;
    padding: 0.125rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
}

.kata-activity-status {
    flex-shrink: 0;
}

.kata-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
}

/* Empty State */
.kata-empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.kata-empty-state svg {
    margin-bottom: 1rem;
    opacity: 0.5;
}

.kata-empty-state p {
    margin-bottom: 1rem;
    font-size: 0.875rem;
}

/* Sidebar */
.kata-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Validation Card */
.kata-health-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
}

.kata-health-indicator.excellent {
    background: #dcfce7;
    color: #166534;
}

.kata-health-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.kata-validation-overview {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.kata-validation-metric {
    text-align: center;
}

.kata-metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.kata-metric-value.valid { color: #10b981; }
.kata-metric-value.warning { color: #f59e0b; }
.kata-metric-value.error { color: #ef4444; }

.kata-metric-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.kata-validation-progress {
    text-align: center;
}

.kata-progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.kata-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #10b981, #059669);
    border-radius: 4px;
    transition: width 0.3s ease;
}

.kata-progress-text {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

/* Action Grid */
.kata-action-grid {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.kata-action-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
}

.kata-action-item:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    transform: translateX(2px);
}

.kata-action-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.kata-action-icon.primary { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.kata-action-icon.success { background: linear-gradient(135deg, #10b981, #059669); }
.kata-action-icon.warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
.kata-action-icon.info { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.kata-action-content {
    flex: 1;
}

.kata-action-title {
    font-weight: 500;
    color: #111827;
    margin-bottom: 0.25rem;
}

.kata-action-desc {
    font-size: 0.75rem;
    color: #6b7280;
}

/* Filter Select */
.kata-filter-select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    background: white;
    color: #374151;
}

/* Footer */
.kata-dashboard-footer {
    margin-top: 3rem;
    padding: 2rem 0;
    border-top: 1px solid #e5e7eb;
    background: white;
}

.kata-footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-footer-info p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
}

.kata-footer-links {
    display: flex;
    gap: 1.5rem;
}

.kata-footer-links a {
    font-size: 0.875rem;
    color: #2563eb;
    text-decoration: none;
}

.kata-footer-links a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .kata-header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .kata-dashboard-container {
        grid-template-columns: 1fr;
        padding: 0 1rem;
    }
    
    .kata-stats-overview {
        grid-template-columns: 1fr;
    }
    
    .kata-footer-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}
.stat-label {
    font-size: 14px;
    color: #646970;
}
.kata-dashboard-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}
.kata-dashboard-card h2 {
    margin-top: 0;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}
.kata-chart-container {
    padding: 20px 0;
    min-height: 300px;
}
.kata-status-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}
.kata-status-badge.active {
    background: #d4edda;
    color: #155724;
}
.kata-status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}
.kata-validation-summary {
    display: flex;
    gap: 20px;
    padding: 20px 0;
}
.validation-stat {
    text-align: center;
    flex: 1;
    padding: 15px;
    border-radius: 4px;
}
.validation-stat.valid {
    background: #d4edda;
}
.validation-stat.invalid {
    background: #f8d7da;
}
.validation-stat.warnings {
    background: #fff3cd;
}
.validation-stat .count {
    display: block;
    font-size: 36px;
    font-weight: bold;
}
.validation-stat .label {
    display: block;
    font-size: 14px;
    margin-top: 5px;
}
.kata-error-list {
    list-style: none;
    padding: 0;
}
.kata-error-list li {
    padding: 10px;
    border-left: 3px solid #dc3232;
    background: #f8d7da;
    margin-bottom: 5px;
}
.error-time {
    float: right;
    color: #646970;
    font-size: 12px;
}
.kata-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}
.kata-quick-actions .button-hero {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px 20px;
}
</style>
