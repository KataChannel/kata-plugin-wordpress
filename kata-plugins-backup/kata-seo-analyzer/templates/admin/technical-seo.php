<?php
/**
 * Technical SEO Analysis Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Set default values if variables are not set
$technical_issues = isset($technical_issues) ? $technical_issues : array();
$site_speed = isset($site_speed) ? $site_speed : array();
$mobile_friendly = isset($mobile_friendly) ? $mobile_friendly : array();
$structured_data = isset($structured_data) ? $structured_data : array();
?>

<div class="wrap kata-seo-technical">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">⚙️</span>
            Technical SEO Analysis
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary run-technical-scan">
                <span class="dashicons dashicons-search"></span>
                Run Technical Scan
            </button>
            <button class="kata-btn kata-btn-secondary export-technical-report">
                <span class="dashicons dashicons-download"></span>
                Export Report
            </button>
        </div>
    </div>

    <!-- Technical Issues Overview -->
    <div class="technical-overview-grid">
        <div class="technical-card site-speed">
            <div class="card-header">
                <h3><span class="dashicons dashicons-performance"></span> Site Speed</h3>
                <div class="score-badge <?php echo !empty($site_speed['score']) && $site_speed['score'] >= 80 ? 'good' : 'needs-improvement'; ?>">
                    <?php echo !empty($site_speed['score']) ? $site_speed['score'] : 'N/A'; ?>
                </div>
            </div>
            <div class="card-content">
                <div class="metric">
                    <span class="label">Page Load Time:</span>
                    <span class="value"><?php echo !empty($site_speed['load_time']) ? $site_speed['load_time'] . 's' : 'Unknown'; ?></span>
                </div>
                <div class="metric">
                    <span class="label">First Contentful Paint:</span>
                    <span class="value"><?php echo !empty($site_speed['fcp']) ? $site_speed['fcp'] . 's' : 'Unknown'; ?></span>
                </div>
                <div class="metric">
                    <span class="label">Largest Contentful Paint:</span>
                    <span class="value"><?php echo !empty($site_speed['lcp']) ? $site_speed['lcp'] . 's' : 'Unknown'; ?></span>
                </div>
            </div>
        </div>

        <div class="technical-card mobile-friendly">
            <div class="card-header">
                <h3><span class="dashicons dashicons-smartphone"></span> Mobile Friendliness</h3>
                <div class="status-badge <?php echo !empty($mobile_friendly['is_mobile_friendly']) ? 'good' : 'warning'; ?>">
                    <?php echo !empty($mobile_friendly['is_mobile_friendly']) ? 'Friendly' : 'Needs Work'; ?>
                </div>
            </div>
            <div class="card-content">
                <div class="metric">
                    <span class="label">Responsive Design:</span>
                    <span class="value <?php echo !empty($mobile_friendly['responsive']) ? 'good' : 'warning'; ?>">
                        <?php echo !empty($mobile_friendly['responsive']) ? 'Yes' : 'No'; ?>
                    </span>
                </div>
                <div class="metric">
                    <span class="label">Viewport Meta Tag:</span>
                    <span class="value <?php echo !empty($mobile_friendly['viewport']) ? 'good' : 'warning'; ?>">
                        <?php echo !empty($mobile_friendly['viewport']) ? 'Present' : 'Missing'; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="technical-card structured-data">
            <div class="card-header">
                <h3><span class="dashicons dashicons-editor-code"></span> Structured Data</h3>
                <div class="count-badge">
                    <?php echo !empty($structured_data['schemas_found']) ? count($structured_data['schemas_found']) : '0'; ?> Schemas
                </div>
            </div>
            <div class="card-content">
                <?php if (!empty($structured_data['schemas_found']) && is_array($structured_data['schemas_found'])): ?>
                    <?php foreach ($structured_data['schemas_found'] as $schema): ?>
                        <div class="schema-item">
                            <span class="schema-type"><?php echo esc_html($schema); ?></span>
                            <span class="schema-status good">✓</span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-schemas">
                        <span class="dashicons dashicons-info"></span>
                        No structured data found. Consider adding schema markup.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Technical Issues List -->
    <div class="technical-issues-section">
        <h2>Technical Issues</h2>
        
        <?php if (!empty($technical_issues) && is_array($technical_issues)): ?>
            <div class="issues-list">
                <?php foreach ($technical_issues as $issue): ?>
                    <div class="issue-item <?php echo esc_attr($issue['severity'] ?? 'medium'); ?>">
                        <div class="issue-icon">
                            <?php
                            switch ($issue['severity'] ?? 'medium') {
                                case 'high':
                                    echo '<span class="dashicons dashicons-warning" style="color: #d63638;"></span>';
                                    break;
                                case 'medium':
                                    echo '<span class="dashicons dashicons-info" style="color: #dba617;"></span>';
                                    break;
                                default:
                                    echo '<span class="dashicons dashicons-info-outline" style="color: #135e96;"></span>';
                                    break;
                            }
                            ?>
                        </div>
                        <div class="issue-content">
                            <h4><?php echo esc_html($issue['title'] ?? 'Unknown Issue'); ?></h4>
                            <p><?php echo esc_html($issue['description'] ?? 'No description available.'); ?></p>
                            <?php if (!empty($issue['fix_suggestion'])): ?>
                                <div class="fix-suggestion">
                                    <strong>How to fix:</strong> <?php echo esc_html($issue['fix_suggestion']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="issue-actions">
                            <button class="kata-btn kata-btn-small mark-resolved" data-issue-id="<?php echo esc_attr($issue['id'] ?? ''); ?>">
                                Mark as Resolved
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-issues">
                <div class="no-issues-icon">
                    <span class="dashicons dashicons-thumbs-up"></span>
                </div>
                <h3>No Technical Issues Found!</h3>
                <p>Your website appears to be technically sound. Keep up the good work!</p>
                <button class="kata-btn kata-btn-primary run-deep-scan">
                    Run Deep Technical Scan
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recommendations -->
    <div class="technical-recommendations">
        <h2>Technical SEO Recommendations</h2>
        <div class="recommendations-grid">
            <div class="recommendation-card">
                <div class="recommendation-icon">
                    <span class="dashicons dashicons-performance"></span>
                </div>
                <h4>Optimize Site Speed</h4>
                <p>Faster websites rank better and provide better user experience.</p>
                <ul>
                    <li>Optimize images</li>
                    <li>Enable caching</li>
                    <li>Minify CSS/JS</li>
                    <li>Use a CDN</li>
                </ul>
            </div>

            <div class="recommendation-card">
                <div class="recommendation-icon">
                    <span class="dashicons dashicons-smartphone"></span>
                </div>
                <h4>Mobile Optimization</h4>
                <p>Ensure your site works perfectly on mobile devices.</p>
                <ul>
                    <li>Responsive design</li>
                    <li>Touch-friendly buttons</li>
                    <li>Fast mobile loading</li>
                    <li>Mobile-first indexing</li>
                </ul>
            </div>

            <div class="recommendation-card">
                <div class="recommendation-icon">
                    <span class="dashicons dashicons-admin-tools"></span>
                </div>
                <h4>Technical Infrastructure</h4>
                <p>Maintain a solid technical foundation for SEO.</p>
                <ul>
                    <li>XML sitemaps</li>
                    <li>Robots.txt</li>
                    <li>SSL certificate</li>
                    <li>Clean URLs</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
.kata-seo-technical {
    background: #f0f0f1;
    margin: 0 -20px;
    padding: 20px;
}

.technical-overview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.technical-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.technical-card .card-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 15px;
}

.technical-card h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.score-badge, .status-badge, .count-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.score-badge.good, .status-badge.good {
    background: #00a32a;
    color: white;
}

.score-badge.needs-improvement, .status-badge.warning {
    background: #dba617;
    color: white;
}

.count-badge {
    background: #135e96;
    color: white;
}

.technical-issues-section, .technical-recommendations {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.issue-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-left: 4px solid #ddd;
    margin-bottom: 15px;
    background: #f9f9f9;
}

.issue-item.high {
    border-left-color: #d63638;
}

.issue-item.medium {
    border-left-color: #dba617;
}

.issue-item.low {
    border-left-color: #135e96;
}

.recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.recommendation-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

.no-issues {
    text-align: center;
    padding: 40px;
}

.no-issues-icon .dashicons {
    font-size: 48px;
    color: #00a32a;
}
</style>
