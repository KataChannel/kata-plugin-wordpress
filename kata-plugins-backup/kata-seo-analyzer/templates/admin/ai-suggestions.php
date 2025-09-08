<?php
/**
 * AI Suggestions Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Set default values if variables are not set
$suggestions = isset($suggestions) ? $suggestions : array();
$ai_stats = isset($ai_stats) ? $ai_stats : array();
?>

<div class="wrap kata-seo-ai-suggestions">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">🤖</span>
            AI-Powered SEO Suggestions
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary generate-suggestions-btn">
                <span class="dashicons dashicons-lightbulb"></span>
                Generate New Suggestions
            </button>
            <button class="kata-btn kata-btn-secondary refresh-suggestions-btn">
                <span class="dashicons dashicons-update"></span>
                Refresh
            </button>
        </div>
    </div>

    <!-- AI Stats Overview -->
    <div class="ai-stats-grid">
        <div class="stat-card total">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo esc_html($ai_stats['total_suggestions'] ?? 0); ?></div>
                <div class="stat-label">Total Suggestions</div>
            </div>
        </div>

        <div class="stat-card pending">
            <div class="stat-icon">⏳</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo esc_html($ai_stats['pending'] ?? 0); ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>

        <div class="stat-card completed">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo esc_html($ai_stats['completed'] ?? 0); ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>

        <div class="stat-card high-priority">
            <div class="stat-icon">🔥</div>
            <div class="stat-content">
                <div class="stat-number"><?php echo esc_html($ai_stats['high_priority'] ?? 0); ?></div>
                <div class="stat-label">High Priority</div>
            </div>
        </div>
    </div>

    <!-- Suggestions List -->
    <div class="suggestions-section">
        <div class="suggestions-header">
            <h2>AI Recommendations</h2>
            <div class="suggestions-filters">
                <select id="priority-filter" class="kata-select">
                    <option value="all">All Priorities</option>
                    <option value="high">High Priority</option>
                    <option value="medium">Medium Priority</option>
                    <option value="low">Low Priority</option>
                </select>
                <select id="status-filter" class="kata-select">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="dismissed">Dismissed</option>
                </select>
                <select id="type-filter" class="kata-select">
                    <option value="all">All Types</option>
                    <option value="content">Content</option>
                    <option value="technical">Technical</option>
                    <option value="keyword">Keywords</option>
                </select>
            </div>
        </div>

        <?php if (!empty($suggestions) && is_array($suggestions)): ?>
            <div class="suggestions-list">
                <?php foreach ($suggestions as $suggestion): ?>
                    <div class="suggestion-item <?php echo esc_attr($suggestion['priority'] ?? 'medium'); ?> <?php echo esc_attr($suggestion['status'] ?? 'pending'); ?>"
                         data-id="<?php echo esc_attr($suggestion['id'] ?? ''); ?>"
                         data-priority="<?php echo esc_attr($suggestion['priority'] ?? 'medium'); ?>"
                         data-status="<?php echo esc_attr($suggestion['status'] ?? 'pending'); ?>"
                         data-type="<?php echo esc_attr($suggestion['type'] ?? 'general'); ?>">
                        
                        <div class="suggestion-priority">
                            <span class="priority-badge <?php echo esc_attr($suggestion['priority'] ?? 'medium'); ?>">
                                <?php
                                switch ($suggestion['priority'] ?? 'medium') {
                                    case 'high':
                                        echo '🔥 High';
                                        break;
                                    case 'medium':
                                        echo '⚡ Medium';
                                        break;
                                    case 'low':
                                        echo '💡 Low';
                                        break;
                                    default:
                                        echo '📝 Normal';
                                }
                                ?>
                            </span>
                        </div>

                        <div class="suggestion-content">
                            <div class="suggestion-type">
                                <span class="type-badge <?php echo esc_attr($suggestion['type'] ?? 'general'); ?>">
                                    <?php echo esc_html(ucfirst($suggestion['type'] ?? 'General')); ?>
                                </span>
                            </div>
                            
                            <div class="suggestion-text">
                                <?php echo esc_html($suggestion['suggestion'] ?? 'No suggestion text available.'); ?>
                            </div>
                            
                            <div class="suggestion-meta">
                                <span class="suggestion-date">
                                    <?php echo esc_html(date('M j, Y', strtotime($suggestion['created_at'] ?? 'now'))); ?>
                                </span>
                                <?php if (!empty($suggestion['post_id']) && $suggestion['post_id'] > 0): ?>
                                    <span class="suggestion-post">
                                        for <a href="<?php echo esc_url(get_edit_post_link($suggestion['post_id'])); ?>">
                                            <?php echo esc_html(get_the_title($suggestion['post_id']) ?: 'Post #' . $suggestion['post_id']); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="suggestion-actions">
                            <?php if (($suggestion['status'] ?? 'pending') === 'pending'): ?>
                                <button class="kata-btn kata-btn-small mark-completed" data-id="<?php echo esc_attr($suggestion['id'] ?? ''); ?>">
                                    <span class="dashicons dashicons-yes"></span>
                                    Mark Complete
                                </button>
                                <button class="kata-btn kata-btn-small kata-btn-secondary dismiss-suggestion" data-id="<?php echo esc_attr($suggestion['id'] ?? ''); ?>">
                                    <span class="dashicons dashicons-dismiss"></span>
                                    Dismiss
                                </button>
                            <?php else: ?>
                                <span class="status-indicator <?php echo esc_attr($suggestion['status'] ?? 'pending'); ?>">
                                    <?php echo esc_html(ucfirst($suggestion['status'] ?? 'Pending')); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-suggestions">
                <div class="no-suggestions-icon">
                    <span class="dashicons dashicons-lightbulb"></span>
                </div>
                <h3>No AI Suggestions Available</h3>
                <p>Generate AI-powered recommendations to improve your SEO performance.</p>
                <button class="kata-btn kata-btn-primary generate-first-suggestions">
                    <span class="dashicons dashicons-lightbulb"></span>
                    Generate Your First Suggestions
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- AI Configuration -->
    <div class="ai-config-section">
        <h2>AI Configuration</h2>
        <div class="config-grid">
            <div class="config-card">
                <h4><span class="dashicons dashicons-admin-tools"></span> Suggestion Types</h4>
                <div class="config-options">
                    <label><input type="checkbox" checked> Content Optimization</label>
                    <label><input type="checkbox" checked> Technical SEO</label>
                    <label><input type="checkbox" checked> Keyword Research</label>
                    <label><input type="checkbox" checked> User Experience</label>
                </div>
            </div>

            <div class="config-card">
                <h4><span class="dashicons dashicons-clock"></span> Generation Frequency</h4>
                <div class="config-options">
                    <select class="kata-select">
                        <option value="daily">Daily</option>
                        <option value="weekly" selected>Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="manual">Manual Only</option>
                    </select>
                </div>
            </div>

            <div class="config-card">
                <h4><span class="dashicons dashicons-bell"></span> Notifications</h4>
                <div class="config-options">
                    <label><input type="checkbox" checked> Email alerts for high priority suggestions</label>
                    <label><input type="checkbox"> Dashboard notifications</label>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kata-seo-ai-suggestions {
    background: #f0f0f1;
    margin: 0 -20px;
    padding: 20px;
}

.ai-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.suggestions-section, .ai-config-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.suggestions-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.suggestions-filters {
    display: flex;
    gap: 10px;
}

.suggestion-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 15px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #f9f9f9;
    transition: all 0.3s ease;
}

.suggestion-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.suggestion-item.high {
    border-left: 4px solid #d63638;
}

.suggestion-item.medium {
    border-left: 4px solid #dba617;
}

.suggestion-item.low {
    border-left: 4px solid #135e96;
}

.priority-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.priority-badge.high {
    background: #d63638;
    color: white;
}

.priority-badge.medium {
    background: #dba617;
    color: white;
}

.priority-badge.low {
    background: #135e96;
    color: white;
}

.type-badge {
    padding: 2px 8px;
    background: #e0e0e0;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}

.suggestion-text {
    font-size: 14px;
    line-height: 1.5;
    margin: 10px 0;
}

.suggestion-meta {
    font-size: 12px;
    color: #646970;
    display: flex;
    gap: 15px;
}

.suggestion-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.no-suggestions {
    text-align: center;
    padding: 40px;
}

.no-suggestions-icon .dashicons {
    font-size: 48px;
    color: #646970;
}

.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.config-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

.config-card h4 {
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.config-options label {
    display: block;
    margin-bottom: 8px;
}

.status-indicator {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status-indicator.completed {
    background: #00a32a;
    color: white;
}

.status-indicator.dismissed {
    background: #646970;
    color: white;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Filter functionality
    $('#priority-filter, #status-filter, #type-filter').change(function() {
        var priorityFilter = $('#priority-filter').val();
        var statusFilter = $('#status-filter').val();
        var typeFilter = $('#type-filter').val();
        
        $('.suggestion-item').each(function() {
            var $item = $(this);
            var priority = $item.data('priority');
            var status = $item.data('status');
            var type = $item.data('type');
            
            var showItem = true;
            
            if (priorityFilter !== 'all' && priority !== priorityFilter) {
                showItem = false;
            }
            
            if (statusFilter !== 'all' && status !== statusFilter) {
                showItem = false;
            }
            
            if (typeFilter !== 'all' && type !== typeFilter) {
                showItem = false;
            }
            
            $item.toggle(showItem);
        });
    });
});
</script>
