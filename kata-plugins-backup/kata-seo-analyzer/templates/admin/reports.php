<?php
/**
 * Reports Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Set default values if variables are not set
$report_type = isset($_GET['report_type']) ? sanitize_text_field($_GET['report_type']) : 'overview';
$report_data = isset($report_data) ? $report_data : array();
?>

<div class="wrap kata-seo-reports">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">📊</span>
            SEO Reports & Analytics
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary export-report-btn" data-report-type="<?php echo esc_attr($report_type); ?>">
                <span class="dashicons dashicons-download"></span>
                Export Report
            </button>
            <button class="kata-btn kata-btn-secondary schedule-report-btn">
                <span class="dashicons dashicons-calendar-alt"></span>
                Schedule Reports
            </button>
        </div>
    </div>

    <!-- Report Navigation -->
    <div class="report-nav">
        <nav class="nav-tab-wrapper">
            <a href="?page=kata-seo-reports&report_type=overview" 
               class="nav-tab <?php echo $report_type === 'overview' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-chart-area"></span>
                Overview
            </a>
            <a href="?page=kata-seo-reports&report_type=content" 
               class="nav-tab <?php echo $report_type === 'content' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-edit"></span>
                Content Performance
            </a>
            <a href="?page=kata-seo-reports&report_type=technical" 
               class="nav-tab <?php echo $report_type === 'technical' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-admin-tools"></span>
                Technical SEO
            </a>
            <a href="?page=kata-seo-reports&report_type=competitor" 
               class="nav-tab <?php echo $report_type === 'competitor' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-chart-line"></span>
                Competitor Analysis
            </a>
        </nav>
    </div>

    <!-- Date Range Selector -->
    <div class="report-controls">
        <div class="date-range-selector">
            <label for="date-range">Date Range:</label>
            <select id="date-range" class="kata-select">
                <option value="7">Last 7 days</option>
                <option value="30" selected>Last 30 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last year</option>
                <option value="custom">Custom range</option>
            </select>
        </div>
        <div class="custom-date-range" style="display: none;">
            <input type="date" id="start-date" class="kata-input">
            <span>to</span>
            <input type="date" id="end-date" class="kata-input">
            <button class="kata-btn kata-btn-secondary apply-range">Apply</button>
        </div>
    </div>

    <!-- Report Content -->
    <div class="report-content">
        <?php if ($report_type === 'overview'): ?>
            <!-- Overview Report -->
            <div class="overview-report">
                <div class="report-summary">
                    <h2>SEO Performance Summary</h2>
                    <div class="summary-grid">
                        <div class="summary-card">
                            <div class="card-icon">📈</div>
                            <div class="card-content">
                                <div class="card-number">85</div>
                                <div class="card-label">Average SEO Score</div>
                                <div class="card-change positive">+5% from last month</div>
                            </div>
                        </div>

                        <div class="summary-card">
                            <div class="card-icon">🔍</div>
                            <div class="card-content">
                                <div class="card-number">247</div>
                                <div class="card-label">Pages Analyzed</div>
                                <div class="card-change positive">+12% from last month</div>
                            </div>
                        </div>

                        <div class="summary-card">
                            <div class="card-icon">⚠️</div>
                            <div class="card-content">
                                <div class="card-number">23</div>
                                <div class="card-label">Issues Found</div>
                                <div class="card-change negative">-8% from last month</div>
                            </div>
                        </div>

                        <div class="summary-card">
                            <div class="card-icon">🎯</div>
                            <div class="card-content">
                                <div class="card-number">156</div>
                                <div class="card-label">Keywords Tracked</div>
                                <div class="card-change positive">+15% from last month</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="report-charts">
                    <div class="chart-container">
                        <h3>SEO Score Trend</h3>
                        <div class="chart-placeholder">
                            <canvas id="seo-score-chart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <div class="chart-container">
                        <h3>Issues Distribution</h3>
                        <div class="chart-placeholder">
                            <canvas id="issues-chart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($report_type === 'content'): ?>
            <!-- Content Performance Report -->
            <div class="content-report">
                <h2>Content Performance Analysis</h2>
                
                <div class="content-metrics">
                    <div class="metric-card">
                        <h4>Top Performing Content</h4>
                        <div class="content-list">
                            <div class="content-item">
                                <div class="content-title">SEO Best Practices Guide</div>
                                <div class="content-score">Score: 95/100</div>
                            </div>
                            <div class="content-item">
                                <div class="content-title">WordPress Optimization Tips</div>
                                <div class="content-score">Score: 88/100</div>
                            </div>
                            <div class="content-item">
                                <div class="content-title">Content Marketing Strategy</div>
                                <div class="content-score">Score: 82/100</div>
                            </div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <h4>Content Issues Summary</h4>
                        <div class="issues-breakdown">
                            <div class="issue-type">
                                <span class="issue-label">Missing Meta Descriptions</span>
                                <span class="issue-count">12 pages</span>
                            </div>
                            <div class="issue-type">
                                <span class="issue-label">Thin Content</span>
                                <span class="issue-count">8 pages</span>
                            </div>
                            <div class="issue-type">
                                <span class="issue-label">Missing Focus Keywords</span>
                                <span class="issue-count">15 pages</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($report_type === 'technical'): ?>
            <!-- Technical SEO Report -->
            <div class="technical-report">
                <h2>Technical SEO Analysis</h2>
                
                <div class="technical-metrics">
                    <div class="metric-card">
                        <h4>Site Performance</h4>
                        <div class="performance-metrics">
                            <div class="metric">
                                <span class="metric-label">Page Speed Score</span>
                                <span class="metric-value good">85/100</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">Mobile Friendly</span>
                                <span class="metric-value good">✓ Yes</span>
                            </div>
                            <div class="metric">
                                <span class="metric-label">SSL Certificate</span>
                                <span class="metric-value good">✓ Valid</span>
                            </div>
                        </div>
                    </div>

                    <div class="metric-card">
                        <h4>Technical Issues</h4>
                        <div class="technical-issues">
                            <div class="issue-item low">
                                <span class="issue-severity">Low</span>
                                <span class="issue-description">Some images missing alt text</span>
                            </div>
                            <div class="issue-item medium">
                                <span class="issue-severity">Medium</span>
                                <span class="issue-description">robots.txt could be optimized</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($report_type === 'competitor'): ?>
            <!-- Competitor Analysis Report -->
            <div class="competitor-report">
                <h2>Competitor Analysis Report</h2>
                
                <div class="competitor-metrics">
                    <div class="metric-card">
                        <h4>Keyword Gap Analysis</h4>
                        <p>Competitor analysis features are available in the premium version.</p>
                        <button class="kata-btn kata-btn-primary upgrade-btn">
                            Upgrade to Premium
                        </button>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- Report Actions -->
    <div class="report-actions">
        <button class="kata-btn kata-btn-secondary print-report">
            <span class="dashicons dashicons-printer"></span>
            Print Report
        </button>
        <button class="kata-btn kata-btn-secondary email-report">
            <span class="dashicons dashicons-email"></span>
            Email Report
        </button>
        <button class="kata-btn kata-btn-secondary share-report">
            <span class="dashicons dashicons-share"></span>
            Share Report
        </button>
    </div>
</div>

<style>
.kata-seo-reports {
    background: #f0f0f1;
    margin: 0 -20px;
    padding: 20px;
}

.report-nav {
    background: white;
    border-radius: 8px;
    padding: 0;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.report-controls {
    background: white;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.report-content {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.summary-card {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.card-icon {
    font-size: 32px;
}

.card-number {
    font-size: 32px;
    font-weight: 700;
    color: #1d2327;
}

.card-label {
    font-size: 14px;
    color: #646970;
    margin: 5px 0;
}

.card-change {
    font-size: 12px;
    font-weight: 600;
}

.card-change.positive {
    color: #00a32a;
}

.card-change.negative {
    color: #d63638;
}

.report-charts {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.chart-container h3 {
    margin-bottom: 15px;
}

.chart-placeholder {
    background: #f0f0f0;
    border: 2px dashed #ccc;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    color: #646970;
}

.content-metrics, .technical-metrics, .competitor-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
}

.metric-card {
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.metric-card h4 {
    margin: 0 0 15px 0;
    color: #1d2327;
}

.content-item, .metric {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.content-item:last-child, .metric:last-child {
    border-bottom: none;
}

.metric-value.good {
    color: #00a32a;
    font-weight: 600;
}

.issue-item {
    display: flex;
    gap: 10px;
    padding: 8px 0;
}

.issue-severity {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.issue-item.low .issue-severity {
    background: #135e96;
    color: white;
}

.issue-item.medium .issue-severity {
    background: #dba617;
    color: white;
}

.issue-item.high .issue-severity {
    background: #d63638;
    color: white;
}

.report-actions {
    background: white;
    border-radius: 8px;
    padding: 15px 20px;
    display: flex;
    gap: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script>
jQuery(document).ready(function($) {
    // Date range selector
    $('#date-range').change(function() {
        if ($(this).val() === 'custom') {
            $('.custom-date-range').show();
        } else {
            $('.custom-date-range').hide();
        }
    });
});
</script>
