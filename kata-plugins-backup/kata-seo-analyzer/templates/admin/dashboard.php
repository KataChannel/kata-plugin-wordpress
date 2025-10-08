<?php
/**
 * Dashboard Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get dashboard data  
$admin = KataSEO_Admin::get_instance();
$analyzer = KataSEO_Analyzer::get_instance();

// Get statistics
$stats = $admin->get_dashboard_stats();
$recent_analyses = $admin->get_recent_analyses(10);
$top_performing = $admin->get_top_performing_posts(5);
$issues_summary = $admin->get_issues_summary();
?>

<div class="wrap kata-seo-dashboard">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">🔍</span>
            Kata SEO Analyzer Dashboard
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary bulk-analyze-btn">
                <span class="dashicons dashicons-search"></span>
                Bulk Analyze
            </button>
            <button class="kata-btn kata-btn-secondary export-report-btn" data-report-type="overview">
                <span class="dashicons dashicons-download"></span>
                Export Report
            </button>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card excellent" data-stat="total_posts">
            <div class="stat-icon">📝</div>
            <div class="stat-content">
                <div class="stat-number" data-count="<?php echo esc_attr($stats['total_posts']); ?>">
                    <?php echo esc_html($stats['total_posts']); ?>
                </div>
                <div class="stat-label">Total Posts</div>
            </div>
        </div>

        <div class="stat-card <?php echo esc_attr($admin->get_score_class($stats['average_score'])); ?>" data-stat="average_score">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <div class="stat-number" data-count="<?php echo esc_attr($stats['average_score']); ?>">
                    <?php echo esc_html($stats['average_score']); ?>
                </div>
                <div class="stat-label">Average Score</div>
            </div>
        </div>

        <div class="stat-card <?php echo $stats['analyzed_posts'] > 0 ? 'good' : 'poor'; ?>" data-stat="analyzed_posts">
            <div class="stat-icon">🔍</div>
            <div class="stat-content">
                <div class="stat-number" data-count="<?php echo esc_attr($stats['analyzed_posts']); ?>">
                    <?php echo esc_html($stats['analyzed_posts']); ?>
                </div>
                <div class="stat-label">Analyzed Posts</div>
            </div>
        </div>

        <div class="stat-card <?php echo $stats['total_issues'] === 0 ? 'excellent' : 'needs-improvement'; ?>" data-stat="total_issues">
            <div class="stat-icon">⚠️</div>
            <div class="stat-content">
                <div class="stat-number" data-count="<?php echo esc_attr($stats['total_issues']); ?>">
                    <?php echo esc_html($stats['total_issues']); ?>
                </div>
                <div class="stat-label">Issues Found</div>
            </div>
        </div>

        <div class="stat-card good" data-stat="ai_suggestions">
            <div class="stat-icon">🤖</div>
            <div class="stat-content">
                <div class="stat-number" data-count="<?php echo esc_attr($stats['ai_suggestions']); ?>">
                    <?php echo esc_html($stats['ai_suggestions']); ?>
                </div>
                <div class="stat-label">AI Suggestions</div>
            </div>
        </div>

        <div class="stat-card fair" data-stat="competitors_tracked">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <div class="stat-number" data-count="<?php echo esc_attr($stats['competitors_tracked']); ?>">
                    <?php echo esc_html($stats['competitors_tracked']); ?>
                </div>
                <div class="stat-label">Competitors</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
            <div class="chart-header">
                <h3>SEO Score Trend</h3>
                <div class="chart-filters">
                    <select class="chart-period-select" data-chart="score-trend">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="score-trend-chart" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h3>Score Distribution</h3>
            </div>
            <div class="chart-body">
                <canvas id="score-distribution-chart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Recent Activity -->
        <div class="widget recent-activity">
            <div class="widget-header">
                <h3>Recent Analysis</h3>
                <a href="<?php echo admin_url('admin.php?page=kata-seo-content'); ?>" class="widget-link">View All</a>
            </div>
            <div class="widget-content">
                <?php if (!empty($recent_analyses)): ?>
                    <div class="activity-list">
                        <?php foreach ($recent_analyses as $analysis): ?>
                            <div class="activity-item">
                                <div class="activity-score score-<?php echo esc_attr($admin->get_score_class($analysis->score)); ?>">
                                    <?php echo esc_html($analysis->score); ?>
                                </div>
                                <div class="activity-details">
                                    <div class="activity-title">
                                        <a href="<?php echo get_edit_post_link($analysis->post_id); ?>">
                                            <?php echo esc_html(get_the_title($analysis->post_id)); ?>
                                        </a>
                                    </div>
                                    <div class="activity-meta">
                                        <?php 
                                        $created_at = $analysis->created_at ?? null;
                                        if ($created_at) {
                                            echo esc_html(human_time_diff(strtotime($created_at))); 
                                        } else {
                                            echo esc_html__('Unknown time', 'kata-seo-analyzer');
                                        }
                                        ?> ago
                                    </div>
                                </div>
                                <div class="activity-actions">
                                    <button class="analyze-post-btn" data-post-id="<?php echo esc_attr($analysis->post_id); ?>">
                                        Re-analyze
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No analyses yet. <a href="<?php echo admin_url('admin.php?page=kata-seo-content'); ?>">Start analyzing your content</a>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Performing Posts -->
        <div class="widget top-performing">
            <div class="widget-header">
                <h3>Top Performing Posts</h3>
                <a href="<?php echo admin_url('admin.php?page=kata-seo-content&orderby=score&order=desc'); ?>" class="widget-link">View All</a>
            </div>
            <div class="widget-content">
                <?php if (!empty($top_performing)): ?>
                    <div class="performance-list">
                        <?php foreach ($top_performing as $post): ?>
                            <div class="performance-item">
                                <div class="performance-score score-<?php echo esc_attr($admin->get_score_class($post->score)); ?>">
                                    <?php echo esc_html($post->score); ?>
                                </div>
                                <div class="performance-details">
                                    <div class="performance-title">
                                        <a href="<?php echo get_edit_post_link($post->post_id); ?>">
                                            <?php echo esc_html(get_the_title($post->post_id)); ?>
                                        </a>
                                    </div>
                                    <div class="performance-meta">
                                        <?php echo esc_html(get_post_type($post->post_id)); ?> • 
                                        <?php echo esc_html(get_post_status($post->post_id)); ?>
                                    </div>
                                </div>
                                <div class="performance-trend">
                                    <span class="trend-indicator positive">↗</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No analyzed posts yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Issues Summary -->
        <div class="widget issues-summary">
            <div class="widget-header">
                <h3>Common Issues</h3>
                <a href="<?php echo admin_url('admin.php?page=kata-seo-issues'); ?>" class="widget-link">View All</a>
            </div>
            <div class="widget-content">
                <?php if (!empty($issues_summary)): ?>
                    <div class="issues-list">
                        <?php foreach ($issues_summary as $issue): ?>
                            <div class="issue-item">
                                <div class="issue-icon">⚠️</div>
                                <div class="issue-details">
                                    <div class="issue-title"><?php echo esc_html($issue->issue_type); ?></div>
                                    <div class="issue-count"><?php echo esc_html($issue->count); ?> posts affected</div>
                                </div>
                                <div class="issue-priority priority-<?php echo esc_attr($issue->priority); ?>">
                                    <?php echo esc_html(ucfirst($issue->priority)); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No issues found! 🎉</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="widget quick-actions">
            <div class="widget-header">
                <h3>Quick Actions</h3>
            </div>
            <div class="widget-content">
                <div class="quick-actions-grid">
                    <a href="<?php echo admin_url('admin.php?page=kata-seo-content'); ?>" class="quick-action">
                        <div class="action-icon">📝</div>
                        <div class="action-title">Analyze Content</div>
                        <div class="action-description">Review and optimize your posts</div>
                    </a>

                    <a href="<?php echo admin_url('admin.php?page=kata-seo-competitors'); ?>" class="quick-action">
                        <div class="action-icon">🎯</div>
                        <div class="action-title">Track Competitors</div>
                        <div class="action-description">Monitor competitor performance</div>
                    </a>

                    <a href="<?php echo admin_url('admin.php?page=kata-seo-keywords'); ?>" class="quick-action">
                        <div class="action-icon">🔑</div>
                        <div class="action-title">Keyword Research</div>
                        <div class="action-description">Find new keyword opportunities</div>
                    </a>

                    <a href="<?php echo admin_url('admin.php?page=kata-seo-settings'); ?>" class="quick-action">
                        <div class="action-icon">⚙️</div>
                        <div class="action-title">Settings</div>
                        <div class="action-description">Configure plugin options</div>
                    </a>
                </div>
            </div>
        </div>

        <!-- AI Suggestions Widget -->
        <?php if (get_option('kata_seo_ai_enabled', false)): ?>
        <div class="widget ai-suggestions-widget">
            <div class="widget-header">
                <h3>AI Insights</h3>
                <button class="refresh-ai-btn" title="Refresh AI insights">🔄</button>
            </div>
            <div class="widget-content">
                <div id="dashboard-ai-insights">
                    <div class="ai-insight">
                        <div class="insight-icon">💡</div>
                        <div class="insight-content">
                            <div class="insight-title">Content Gap Opportunity</div>
                            <div class="insight-description">
                                Consider creating content about "wordpress security best practices" - 
                                high search volume, low competition in your niche.
                            </div>
                        </div>
                    </div>

                    <div class="ai-insight">
                        <div class="insight-icon">📈</div>
                        <div class="insight-content">
                            <div class="insight-title">Performance Improvement</div>
                            <div class="insight-description">
                                Your posts with H2 headings score 23% higher on average. 
                                Add more structured headings to improve SEO.
                            </div>
                        </div>
                    </div>

                    <div class="ai-insight">
                        <div class="insight-icon">🎯</div>
                        <div class="insight-content">
                            <div class="insight-title">Competitor Analysis</div>
                            <div class="insight-description">
                                Your competitors are ranking for "SEO tools comparison" - 
                                an opportunity for you to create comprehensive content.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Help Section -->
    <div class="help-section">
        <div class="help-card">
            <h3>Getting Started</h3>
            <p>Welcome to Kata SEO Analyzer! Here's how to get the most out of your SEO optimization:</p>
            <ul>
                <li><strong>Analyze Content:</strong> Start by analyzing your existing posts to identify optimization opportunities</li>
                <li><strong>Focus Keywords:</strong> Set focus keywords for each post to target specific search terms</li>
                <li><strong>AI Suggestions:</strong> Enable AI suggestions for automated content optimization recommendations</li>
                <li><strong>Monitor Competitors:</strong> Track competitor performance to stay ahead in search rankings</li>
            </ul>
            <div class="help-actions">
                <a href="<?php echo admin_url('admin.php?page=kata-seo-content'); ?>" class="kata-btn kata-btn-primary">
                    Start Analyzing
                </a>
                <a href="#" class="kata-btn kata-btn-secondary" onclick="return false;">
                    View Documentation
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard-specific styles -->
<style>
.kata-seo-dashboard .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.kata-seo-dashboard .charts-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin: 40px 0;
}

.kata-seo-dashboard .content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 30px;
    margin: 40px 0;
}

.activity-item, .performance-item, .issue-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child,
.performance-item:last-child,
.issue-item:last-child {
    border-bottom: none;
}

.activity-score, .performance-score {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    margin-right: 15px;
    font-size: 14px;
}

.activity-details, .performance-details, .issue-details {
    flex: 1;
}

.activity-title a, .performance-title a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

.activity-title a:hover, .performance-title a:hover {
    color: #0073aa;
}

.activity-meta, .performance-meta, .issue-count {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.quick-action {
    display: block;
    padding: 20px;
    text-align: center;
    text-decoration: none;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.quick-action:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.quick-action .action-icon {
    font-size: 24px;
    margin-bottom: 10px;
}

.quick-action .action-title {
    font-weight: 500;
    color: #333;
    margin-bottom: 5px;
}

.quick-action .action-description {
    font-size: 12px;
    color: #666;
}

.ai-insight {
    display: flex;
    align-items: flex-start;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.ai-insight:last-child {
    border-bottom: none;
}

.ai-insight .insight-icon {
    font-size: 20px;
    margin-right: 15px;
    margin-top: 2px;
}

.ai-insight .insight-title {
    font-weight: 500;
    margin-bottom: 5px;
}

.ai-insight .insight-description {
    font-size: 13px;
    color: #666;
    line-height: 1.4;
}

.help-section {
    margin: 40px 0;
}

.help-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
}

.help-card h3 {
    color: white;
    margin-bottom: 15px;
}

.help-card ul {
    margin: 20px 0;
    padding-left: 20px;
}

.help-card li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.help-actions {
    margin-top: 25px;
}

.help-actions .kata-btn {
    margin-right: 10px;
}
</style>

<script>
// Dashboard-specific JavaScript
jQuery(document).ready(function($) {
    // Auto-refresh dashboard data every 5 minutes
    setInterval(function() {
        if (typeof KataSEO !== 'undefined') {
            KataSEO.loadDashboardData();
        }
    }, 300000);

    // Refresh AI insights
    $('.refresh-ai-btn').on('click', function() {
        var $button = $(this);
        var originalText = $button.text();
        
        $button.text('🔄').addClass('rotating');
        
        // Simulate AI insight refresh
        setTimeout(function() {
            $button.text(originalText).removeClass('rotating');
            // In real implementation, load fresh AI insights
        }, 2000);
    });
});
</script>
