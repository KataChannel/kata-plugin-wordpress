<?php
/**
 * Competitor Analysis Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Set default values if variables are not set
$competitors = isset($competitors) ? $competitors : array();
$keyword_analysis = isset($keyword_analysis) ? $keyword_analysis : array();
?>

<div class="wrap kata-seo-competitors">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">🎯</span>
            Competitor Analysis
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary add-competitor-btn">
                <span class="dashicons dashicons-plus"></span>
                Add Competitor
            </button>
            <button class="kata-btn kata-btn-secondary refresh-data-btn">
                <span class="dashicons dashicons-update"></span>
                Refresh Data
            </button>
        </div>
    </div>

    <!-- Competitors Overview -->
    <div class="competitors-section">
        <h2>Tracked Competitors</h2>
        
        <?php if (!empty($competitors) && is_array($competitors)): ?>
            <div class="competitors-grid">
                <?php foreach ($competitors as $competitor): ?>
                    <div class="competitor-card">
                        <div class="competitor-header">
                            <h3><?php echo esc_html($competitor['domain'] ?? 'Unknown Domain'); ?></h3>
                            <div class="competitor-actions">
                                <button class="kata-btn kata-btn-small analyze-btn" data-domain="<?php echo esc_attr($competitor['domain'] ?? ''); ?>">
                                    Analyze
                                </button>
                                <button class="kata-btn kata-btn-small kata-btn-danger remove-btn" data-id="<?php echo esc_attr($competitor['id'] ?? ''); ?>">
                                    Remove
                                </button>
                            </div>
                        </div>
                        
                        <div class="competitor-stats">
                            <div class="stat">
                                <span class="label">Domain Authority:</span>
                                <span class="value"><?php echo esc_html($competitor['domain_authority'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="stat">
                                <span class="label">Organic Keywords:</span>
                                <span class="value"><?php echo esc_html($competitor['organic_keywords'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="stat">
                                <span class="label">Traffic Estimate:</span>
                                <span class="value"><?php echo esc_html($competitor['traffic_estimate'] ?? 'N/A'); ?></span>
                            </div>
                            <div class="stat">
                                <span class="label">Last Updated:</span>
                                <span class="value"><?php echo esc_html($competitor['last_checked'] ?? 'Never'); ?></span>
                            </div>
                        </div>

                        <?php if (!empty($competitor['top_keywords'])): ?>
                            <div class="competitor-keywords">
                                <h4>Top Keywords</h4>
                                <div class="keywords-list">
                                    <?php foreach (array_slice($competitor['top_keywords'], 0, 5) as $keyword): ?>
                                        <span class="keyword-tag"><?php echo esc_html($keyword); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-competitors">
                <div class="no-competitors-icon">
                    <span class="dashicons dashicons-search"></span>
                </div>
                <h3>No Competitors Added Yet</h3>
                <p>Start tracking your competitors to gain valuable SEO insights and stay ahead of the competition.</p>
                <button class="kata-btn kata-btn-primary add-first-competitor">
                    <span class="dashicons dashicons-plus"></span>
                    Add Your First Competitor
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Keyword Gap Analysis -->
    <div class="keyword-gap-section">
        <h2>Keyword Gap Analysis</h2>
        
        <?php if (!empty($keyword_analysis) && is_array($keyword_analysis)): ?>
            <div class="keyword-gap-table">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Your Position</th>
                            <th>Competitor Position</th>
                            <th>Search Volume</th>
                            <th>Difficulty</th>
                            <th>Opportunity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keyword_analysis as $keyword_data): ?>
                            <tr>
                                <td><strong><?php echo esc_html($keyword_data['keyword'] ?? ''); ?></strong></td>
                                <td>
                                    <span class="position-badge <?php echo ($keyword_data['your_position'] ?? 999) <= 10 ? 'good' : 'poor'; ?>">
                                        <?php echo $keyword_data['your_position'] ?? 'Not Ranking'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="position-badge competitor">
                                        <?php echo $keyword_data['competitor_position'] ?? 'N/A'; ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($keyword_data['search_volume'] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="difficulty-badge <?php echo strtolower($keyword_data['difficulty'] ?? 'medium'); ?>">
                                        <?php echo esc_html($keyword_data['difficulty'] ?? 'Medium'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="opportunity-badge <?php echo strtolower($keyword_data['opportunity'] ?? 'medium'); ?>">
                                        <?php echo esc_html($keyword_data['opportunity'] ?? 'Medium'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-keyword-data">
                <p>No keyword gap analysis available. Add competitors and run analysis to see keyword opportunities.</p>
                <button class="kata-btn kata-btn-primary run-keyword-analysis">
                    Run Keyword Analysis
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Recommendations -->
    <div class="competitor-recommendations">
        <h2>Competitive Insights</h2>
        <div class="insights-grid">
            <div class="insight-card">
                <div class="insight-icon">
                    <span class="dashicons dashicons-chart-line"></span>
                </div>
                <h4>Keyword Opportunities</h4>
                <p>Find keywords your competitors rank for but you don't.</p>
                <button class="kata-btn kata-btn-secondary find-opportunities">Find Opportunities</button>
            </div>

            <div class="insight-card">
                <div class="insight-icon">
                    <span class="dashicons dashicons-admin-links"></span>
                </div>
                <h4>Backlink Analysis</h4>
                <p>Discover where your competitors get their backlinks.</p>
                <button class="kata-btn kata-btn-secondary analyze-backlinks">Analyze Backlinks</button>
            </div>

            <div class="insight-card">
                <div class="insight-icon">
                    <span class="dashicons dashicons-editor-alignleft"></span>
                </div>
                <h4>Content Gaps</h4>
                <p>Identify content topics you're missing compared to competitors.</p>
                <button class="kata-btn kata-btn-secondary find-content-gaps">Find Content Gaps</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Competitor Modal -->
<div id="add-competitor-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h3>Add New Competitor</h3>
            <span class="kata-modal-close">&times;</span>
        </div>
        <div class="kata-modal-body">
            <form id="add-competitor-form">
                <div class="form-group">
                    <label for="competitor-domain">Competitor Domain:</label>
                    <input type="url" id="competitor-domain" name="domain" placeholder="https://example.com" required>
                </div>
                <div class="form-group">
                    <label for="competitor-keywords">Keywords to Track (comma-separated):</label>
                    <textarea id="competitor-keywords" name="keywords" placeholder="seo, digital marketing, website optimization" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="kata-btn kata-btn-primary">Add Competitor</button>
                    <button type="button" class="kata-btn kata-btn-secondary cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.kata-seo-competitors {
    background: #f0f0f1;
    margin: 0 -20px;
    padding: 20px;
}

.competitors-section, .keyword-gap-section, .competitor-recommendations {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.competitors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.competitor-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background: #f9f9f9;
}

.competitor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.competitor-header h3 {
    margin: 0;
    color: #1d2327;
}

.competitor-actions {
    display: flex;
    gap: 8px;
}

.competitor-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 15px;
}

.stat {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.stat .label {
    font-weight: 600;
    color: #646970;
}

.competitor-keywords h4 {
    margin: 10px 0 8px 0;
    font-size: 14px;
}

.keywords-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.keyword-tag {
    background: #e0e0e0;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    color: #1d2327;
}

.position-badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.position-badge.good {
    background: #00a32a;
    color: white;
}

.position-badge.poor {
    background: #d63638;
    color: white;
}

.position-badge.competitor {
    background: #135e96;
    color: white;
}

.difficulty-badge, .opportunity-badge {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.difficulty-badge.low, .opportunity-badge.high {
    background: #00a32a;
    color: white;
}

.difficulty-badge.medium, .opportunity-badge.medium {
    background: #dba617;
    color: white;
}

.difficulty-badge.high, .opportunity-badge.low {
    background: #d63638;
    color: white;
}

.no-competitors, .no-keyword-data {
    text-align: center;
    padding: 40px;
}

.no-competitors-icon .dashicons {
    font-size: 48px;
    color: #646970;
}

.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.insight-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-align: center;
    background: #f9f9f9;
}

.insight-icon .dashicons {
    font-size: 32px;
    color: #135e96;
}

.kata-modal {
    position: fixed;
    z-index: 100000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.kata-modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 0;
    border-radius: 8px;
    width: 500px;
    max-width: 90%;
}

.kata-modal-header {
    padding: 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add competitor modal
    $('.add-competitor-btn, .add-first-competitor').click(function() {
        $('#add-competitor-modal').show();
    });

    $('.kata-modal-close, .cancel-btn').click(function() {
        $('#add-competitor-modal').hide();
    });

    // Close modal when clicking outside
    $(window).click(function(event) {
        if (event.target == document.getElementById('add-competitor-modal')) {
            $('#add-competitor-modal').hide();
        }
    });
});
</script>
