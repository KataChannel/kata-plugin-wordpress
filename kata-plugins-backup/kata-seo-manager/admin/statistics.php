<?php
/**
 * Statistics Page - Modern UI
 * 
 * Display SEO statistics and analytics
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get statistics data
$stats = new KATA_SEO_Statistics();
$all_stats = $stats->get_all_stats();
$overview = $stats->get_overview();

global $wpdb;
?>

<div class="wrap kata-seo-statistics-v2">
    <!-- Header Section -->
    <div class="kata-page-header">
        <div class="kata-header-content">
            <div class="kata-page-title">
                <div class="kata-page-icon">
                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zM1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                    </svg>
                </div>
                <div>
                    <h1><?php _e('Phân tích & Thống kê', 'kata-seo-manager'); ?></h1>
                    <p class="kata-page-description"><?php _e('Thông tin chi tiết về hiệu suất schema và phân tích toàn diện', 'kata-seo-manager'); ?></p>
                </div>
            </div>
            <div class="kata-header-actions">
                <button class="kata-btn kata-btn-outline" onclick="location.reload()">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                    <?php _e('Làm mới dữ liệu', 'kata-seo-manager'); ?>
                </button>
                <button class="kata-btn kata-btn-primary" onclick="kataExportStatistics()">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                    <?php _e('Xuất báo cáo', 'kata-seo-manager'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="kata-statistics-container">
        <!-- Key Metrics -->
        <div class="kata-metrics-overview">
            <div class="kata-metric-card primary">
                <div class="kata-metric-header">
                    <div class="kata-metric-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zM1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                        </svg>
                    </div>
                    <div class="kata-metric-trend positive">+15%</div>
                </div>
                <div class="kata-metric-value" data-count="<?php echo esc_attr($all_stats['total_schemas'] ?? 0); ?>">0</div>
                <div class="kata-metric-label"><?php _e('Tổng Schema', 'kata-seo-manager'); ?></div>
                <div class="kata-metric-description"><?php _e('Các triển khai schema hoạt động', 'kata-seo-manager'); ?></div>
            </div>
            
            <div class="kata-metric-card success">
                <div class="kata-metric-header">
                    <div class="kata-metric-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"/>
                        </svg>
                    </div>
                    <div class="kata-metric-trend positive">+3</div>
                </div>
                <div class="kata-metric-value" data-count="<?php echo esc_attr($all_stats['schema_types_count'] ?? 0); ?>">0</div>
                <div class="kata-metric-label"><?php _e('Các loại Schema', 'kata-seo-manager'); ?></div>
                <div class="kata-metric-description"><?php _e('Các loại khác nhau đang sử dụng', 'kata-seo-manager'); ?></div>
            </div>
            
            <div class="kata-metric-card warning">
                <div class="kata-metric-header">
                    <div class="kata-metric-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                            <path d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </div>
                    <div class="kata-metric-trend positive">+8</div>
                </div>
                <div class="kata-metric-value" data-count="<?php echo esc_attr($all_stats['posts_with_schema'] ?? 0); ?>">0</div>
                <div class="kata-metric-label"><?php _e('Bài viết có Schema', 'kata-seo-manager'); ?></div>
                <div class="kata-metric-description"><?php _e('Nội dung có dữ liệu có cấu trúc', 'kata-seo-manager'); ?></div>
            </div>
            
            <div class="kata-metric-card info">
                <div class="kata-metric-header">
                    <div class="kata-metric-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                    </div>
                    <div class="kata-metric-status"><?php _e('Hoạt động', 'kata-seo-manager'); ?></div>
                </div>
                <div class="kata-metric-value-text"><?php echo esc_html($all_stats['last_updated'] ?? __('Chưa bao giờ', 'kata-seo-manager')); ?></div>
                <div class="kata-metric-label"><?php _e('Cập nhật lần cuối', 'kata-seo-manager'); ?></div>
                <div class="kata-metric-description"><?php _e('Thay đổi schema gần nhất', 'kata-seo-manager'); ?></div>
            </div>
        </div>
    
        <!-- Main Content Area -->
        <div class="kata-statistics-main">
            <!-- Schema Type Distribution -->
            <div class="kata-card kata-distribution-card">
                <div class="kata-card-header">
                    <h3><?php _e('Phân phối loại Schema', 'kata-seo-manager'); ?></h3>
                    <div class="kata-card-actions">
                        <select class="kata-filter-select" onchange="filterSchemaTypes(this.value)">
                            <option value="all"><?php _e('Tất cả loại', 'kata-seo-manager'); ?></option>
                            <option value="high"><?php _e('Sử dụng cao (>5)', 'kata-seo-manager'); ?></option>
                            <option value="medium"><?php _e('Sử dụng trung bình (2-5)', 'kata-seo-manager'); ?></option>
                            <option value="low"><?php _e('Sử dụng thấp (1)', 'kata-seo-manager'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="kata-card-content">
                    <div class="kata-distribution-grid">
                        <?php
                        // Fix: Query from postmeta instead of custom table
                        $schema_distribution = $wpdb->get_results(
                            "SELECT meta_value as schema_type, COUNT(*) as count 
                             FROM {$wpdb->postmeta} 
                             WHERE meta_key = '_kata_seo_schema_type' 
                             GROUP BY meta_value 
                             ORDER BY count DESC",
                            ARRAY_A
                        );
                        
                        $total = $all_stats['total_schemas'] ?? 1;
                        $colors = ['#2563eb', '#7c3aed', '#059669', '#dc2626', '#ea580c', '#ca8a04', '#8b5cf6', '#ef4444', '#10b981', '#f59e0b'];
                        
                        if ($schema_distribution && !empty($schema_distribution)) {
                            foreach ($schema_distribution as $index => $row) {
                                $percentage = ($row['count'] / $total) * 100;
                                $color = $colors[$index % count($colors)];
                                ?>
                                <div class="kata-distribution-item" data-usage="<?php echo $row['count'] > 5 ? 'high' : ($row['count'] > 1 ? 'medium' : 'low'); ?>">
                                    <div class="kata-distribution-header">
                                        <div class="kata-distribution-icon" style="background: <?php echo $color; ?>">
                                            <?php echo esc_html(strtoupper(substr($row['schema_type'], 0, 2))); ?>
                                        </div>
                                        <div class="kata-distribution-info">
                                            <div class="kata-distribution-name"><?php echo esc_html(ucfirst($row['schema_type'])); ?></div>
                                            <div class="kata-distribution-count"><?php echo esc_html($row['count']); ?> <?php _e('schema', 'kata-seo-manager'); ?></div>
                                        </div>
                                        <div class="kata-distribution-percentage"><?php echo number_format($percentage, 1); ?>%</div>
                                    </div>
                                    <div class="kata-distribution-bar">
                                        <div class="kata-distribution-progress" style="width: <?php echo $percentage; ?>%; background: <?php echo $color; ?>"></div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="kata-empty-state">
                                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <p><?php _e('No schema data found. Start by adding schemas to your content.', 'kata-seo-manager'); ?></p>
                                <button class="kata-btn kata-btn-primary kata-btn-sm" onclick="location.href='admin.php?page=kata-seo-schema-types'"><?php _e('Add Schema Types', 'kata-seo-manager'); ?></button>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
    
    <!-- Recent Schemas -->
                <!-- Recent Activity -->
            <div class="kata-card kata-activity-card">
                <div class="kata-card-header">
                    <h3><?php _e('Recent Schema Activity', 'kata-seo-manager'); ?></h3>
                    <button class="kata-btn kata-btn-outline kata-btn-sm" onclick="refreshRecentActivity()">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                        </svg>
                        <?php _e('Refresh', 'kata-seo-manager'); ?>
                    </button>
                </div>
                <div class="kata-card-content">
                    <div class="kata-activity-list">
                        <?php
                        // Get recent schemas from postmeta
                        $recent_schemas = $wpdb->get_results($wpdb->prepare(
                            "SELECT p.ID, p.post_title, p.post_date, pm.meta_value as schema_type
                            FROM {$wpdb->posts} p
                            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
                            WHERE pm.meta_key = '_kata_seo_schema_type'
                            AND p.post_status = 'publish'
                            ORDER BY p.post_date DESC
                            LIMIT %d",
                            10
                        ));
                        
                        if ($recent_schemas) {
                            foreach ($recent_schemas as $schema) {
                                $time_ago = human_time_diff(strtotime($schema->post_date), current_time('timestamp'));
                                ?>
                                <div class="kata-activity-item">
                                    <div class="kata-activity-icon">
                                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                        </svg>
                                    </div>
                                    <div class="kata-activity-content">
                                        <div class="kata-activity-title">
                                            <a href="<?php echo get_edit_post_link($schema->ID); ?>">
                                                <?php echo esc_html($schema->post_title); ?>
                                            </a>
                                        </div>
                                        <div class="kata-activity-meta">
                                            <span class="kata-schema-type-badge"><?php echo esc_html(ucfirst($schema->schema_type)); ?></span>
                                            <span class="kata-activity-time"><?php echo sprintf(__('%s ago', 'kata-seo-manager'), $time_ago); ?></span>
                                        </div>
                                    </div>
                                    <div class="kata-activity-status">
                                        <span class="kata-status-dot success"></span>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div class="kata-empty-state">
                                <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p><?php _e('No recent schema activity found.', 'kata-seo-manager'); ?></p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
</div>

<style>
/* Root Variables */
:root {
    --kata-primary: #667eea;
    --kata-primary-dark: #5a67d8;
    --kata-secondary: #764ba2;
    --kata-success: #10b981;
    --kata-warning: #f59e0b;
    --kata-error: #ef4444;
    --kata-surface: #ffffff;
    --kata-surface-light: #f8fafc;
    --kata-border: #e2e8f0;
    --kata-text: #1e293b;
    --kata-text-muted: #64748b;
    --kata-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --kata-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --kata-radius: 8px;
    --kata-radius-lg: 12px;
    --kata-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Layout */
.kata-statistics-wrapper {
    margin: 20px 20px 20px 0;
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 20px;
}

.kata-statistics-main {
    min-width: 0;
}

.kata-statistics-sidebar {
    min-width: 0;
}

/* Page Header */
.kata-page-header {
    background: var(--kata-surface);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius-lg);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--kata-shadow);
}

.kata-page-title {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 0 0 10px 0;
}

.kata-page-title h1 {
    font-size: 32px;
    font-weight: 700;
    color: var(--kata-text);
    margin: 0;
    line-height: 1.2;
}

.kata-page-title-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
    border-radius: var(--kata-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.kata-page-subtitle {
    color: var(--kata-text-muted);
    font-size: 16px;
    margin: 0 0 20px 0;
}

.kata-page-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Buttons */
.kata-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    border-radius: var(--kata-radius);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--kata-transition);
    white-space: nowrap;
}

.kata-btn-primary {
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
    color: white;
}

.kata-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--kata-shadow-lg);
}

.kata-btn-outline {
    background: transparent;
    color: var(--kata-primary);
    border: 2px solid var(--kata-primary);
}

.kata-btn-outline:hover {
    background: var(--kata-primary);
    color: white;
}

.kata-btn-sm {
    padding: 8px 16px;
    font-size: 13px;
}

/* Cards */
.kata-card {
    background: var(--kata-surface);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius-lg);
    box-shadow: var(--kata-shadow);
    transition: var(--kata-transition);
    margin-bottom: 20px;
}

.kata-card:hover {
    box-shadow: var(--kata-shadow-lg);
}

.kata-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 25px;
    border-bottom: 1px solid var(--kata-border);
}

.kata-card-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--kata-text);
    margin: 0;
}

.kata-card-content {
    padding: 25px;
}

/* Metrics Grid */
.kata-metrics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

/* Metric Cards */
.kata-metric-card {
    background: var(--kata-surface);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius-lg);
    padding: 25px;
    box-shadow: var(--kata-shadow);
    transition: var(--kata-transition);
    position: relative;
    overflow: hidden;
}

.kata-metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--kata-primary), var(--kata-secondary));
}

.kata-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--kata-shadow-lg);
}

.kata-metric-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.kata-metric-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--kata-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.kata-metric-icon.primary {
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
}

.kata-metric-icon.success {
    background: linear-gradient(135deg, var(--kata-success), #059669);
}

.kata-metric-icon.warning {
    background: linear-gradient(135deg, var(--kata-warning), #d97706);
}

.kata-metric-trend {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 12px;
}

.kata-metric-trend.up {
    background: rgba(16, 185, 129, 0.1);
    color: var(--kata-success);
}

.kata-metric-value {
    font-size: 36px;
    font-weight: 700;
    color: var(--kata-text);
    margin: 10px 0;
    line-height: 1;
}

.kata-metric-label {
    color: var(--kata-text-muted);
    font-size: 14px;
    font-weight: 500;
}

/* Schema Distribution */
.kata-distribution-filters {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.kata-filter-btn {
    padding: 8px 16px;
    border: 2px solid var(--kata-border);
    background: var(--kata-surface);
    color: var(--kata-text-muted);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--kata-transition);
}

.kata-filter-btn.active,
.kata-filter-btn:hover {
    border-color: var(--kata-primary);
    color: var(--kata-primary);
    background: rgba(102, 126, 234, 0.1);
}

.kata-distribution-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
}

.kata-distribution-item {
    background: var(--kata-surface-light);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius);
    padding: 20px;
    transition: var(--kata-transition);
}

.kata-distribution-item:hover {
    border-color: var(--kata-primary);
    background: var(--kata-surface);
}

.kata-distribution-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.kata-schema-type {
    font-weight: 600;
    color: var(--kata-text);
    font-size: 16px;
}

.kata-schema-count {
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
}

.kata-progress-container {
    margin-bottom: 10px;
}

.kata-progress-bar {
    width: 100%;
    height: 8px;
    background: var(--kata-border);
    border-radius: 4px;
    overflow: hidden;
}

.kata-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--kata-primary), var(--kata-secondary));
    border-radius: 4px;
    transition: width 1s ease-out;
}

.kata-progress-text {
    font-size: 13px;
    color: var(--kata-text-muted);
    margin-top: 5px;
}

/* Performance Insights */
.kata-insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.kata-insight-item {
    text-align: center;
    padding: 20px;
    background: var(--kata-surface-light);
    border-radius: var(--kata-radius);
    border: 1px solid var(--kata-border);
}

.kata-insight-metric {
    font-size: 32px;
    font-weight: 700;
    color: var(--kata-primary);
    margin-bottom: 8px;
}

.kata-insight-label {
    color: var(--kata-text-muted);
    font-size: 14px;
    font-weight: 500;
}

/* Activity List */
.kata-activity-list {
    max-height: 400px;
    overflow-y: auto;
}

.kata-activity-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid var(--kata-border);
}

.kata-activity-item:last-child {
    border-bottom: none;
}

.kata-activity-icon {
    width: 40px;
    height: 40px;
    background: var(--kata-surface-light);
    border: 1px solid var(--kata-border);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--kata-primary);
    flex-shrink: 0;
}

.kata-activity-content {
    flex: 1;
    min-width: 0;
}

.kata-activity-title {
    font-weight: 600;
    color: var(--kata-text);
    margin-bottom: 5px;
}

.kata-activity-title a {
    color: inherit;
    text-decoration: none;
}

.kata-activity-title a:hover {
    color: var(--kata-primary);
}

.kata-activity-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.kata-schema-type-badge {
    background: rgba(102, 126, 234, 0.1);
    color: var(--kata-primary);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.kata-activity-time {
    color: var(--kata-text-muted);
    font-size: 12px;
}

.kata-activity-status {
    flex-shrink: 0;
}

.kata-status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.kata-status-dot.success {
    background: var(--kata-success);
}

/* Quick Actions */
.kata-action-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.kata-action-link {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: var(--kata-surface-light);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius);
    text-decoration: none;
    color: var(--kata-text);
    transition: var(--kata-transition);
}

.kata-action-link:hover {
    background: var(--kata-surface);
    border-color: var(--kata-primary);
    transform: translateY(-1px);
}

.kata-action-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--kata-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.kata-action-icon.primary {
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
}

.kata-action-icon.success {
    background: linear-gradient(135deg, var(--kata-success), #059669);
}

.kata-action-icon.warning {
    background: linear-gradient(135deg, var(--kata-warning), #d97706);
}

.kata-action-content {
    flex: 1;
    min-width: 0;
}

.kata-action-title {
    font-weight: 600;
    color: var(--kata-text);
    margin-bottom: 2px;
}

.kata-action-desc {
    color: var(--kata-text-muted);
    font-size: 13px;
}

/* Health Card */
.kata-health-card .kata-card-header {
    align-items: flex-start;
}

.kata-health-status {
    background: rgba(16, 185, 129, 0.1);
    color: var(--kata-success);
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.kata-health-score {
    display: flex;
    align-items: center;
    gap: 20px;
}

.kata-health-circle {
    width: 100px;
    height: 100px;
    flex-shrink: 0;
}

.kata-circular-chart {
    display: block;
    width: 100%;
    height: 100%;
}

.kata-circle-bg {
    fill: none;
    stroke: var(--kata-border);
    stroke-width: 2;
}

.kata-circle {
    fill: none;
    stroke: var(--kata-success);
    stroke-width: 2;
    stroke-linecap: round;
    animation: progress 1s ease-out forwards;
}

.kata-percentage {
    fill: var(--kata-text);
    font-size: 0.5em;
    text-anchor: middle;
    font-weight: 600;
}

.kata-health-details {
    flex: 1;
}

.kata-health-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    font-size: 14px;
}

.kata-health-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.kata-health-dot.valid {
    background: var(--kata-success);
}

.kata-health-dot.warning {
    background: var(--kata-warning);
}

.kata-health-dot.error {
    background: var(--kata-error);
}

/* Empty State */
.kata-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: var(--kata-text-muted);
}

.kata-empty-state svg {
    margin-bottom: 15px;
    opacity: 0.5;
}

.kata-empty-state p {
    margin-bottom: 20px;
    font-size: 16px;
}

/* Animations */
@keyframes progress {
    0% {
        stroke-dasharray: 0 100;
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .kata-statistics-wrapper {
        grid-template-columns: 1fr;
    }
    
    .kata-statistics-sidebar {
        order: -1;
    }
}

@media (max-width: 768px) {
    .kata-statistics-wrapper {
        margin: 10px;
    }
    
    .kata-metrics-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-distribution-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-insights-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .kata-page-actions {
        width: 100%;
    }
    
    .kata-btn {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .kata-insights-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-health-score {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
// Initialize statistics page
document.addEventListener('DOMContentLoaded', function() {
    initializeCounters();
    initializeFilters();
});

// Animate metric counters
function initializeCounters() {
    const counters = document.querySelectorAll('.kata-metric-value[data-count]');
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.count);
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Filter schema types
function initializeFilters() {
    // Add filter functionality if needed
}

function filterSchemaTypes(filter) {
    const items = document.querySelectorAll('.kata-distribution-item');
    items.forEach(item => {
        const usage = item.dataset.usage;
        if (filter === 'all' || usage === filter) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Export statistics
function kataExportStatistics() {
    const data = <?php echo json_encode($schema_distribution ?? []); ?>;
    if (!data || data.length === 0) {
        alert('<?php _e('No data available to export.', 'kata-seo-manager'); ?>');
        return;
    }

    const csvContent = "data:text/csv;charset=utf-8," + 
        "Schema Type,Count,Percentage\n" +
        data.map(item => {
            const total = <?php echo $total ?? 1; ?>;
            const percentage = ((parseInt(item.count) / total) * 100).toFixed(1);
            return `${item.schema_type},${item.count},${percentage}%`;
        }).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `kata-statistics-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Validate all schemas
function kataValidateAllSchemas() {
    alert('<?php _e('Schema validation will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

// Refresh recent activity
function refreshRecentActivity() {
    location.reload();
}
</script>
