<?php
/**
 * Monitoring Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Set default values if variables are not set
$monitoring_data = isset($monitoring_data) ? $monitoring_data : array();
$alerts = isset($alerts) ? $alerts : array();
?>

<div class="wrap kata-seo-monitoring">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">📊</span>
            SEO Monitoring & Alerts
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary run-full-scan">
                <span class="dashicons dashicons-search"></span>
                Run Full Scan
            </button>
            <button class="kata-btn kata-btn-secondary setup-alerts">
                <span class="dashicons dashicons-bell"></span>
                Setup Alerts
            </button>
        </div>
    </div>

    <!-- Status Overview -->
    <div class="monitoring-overview">
        <div class="status-grid">
            <div class="status-card uptime">
                <div class="card-header">
                    <h3><span class="dashicons dashicons-admin-site"></span> Site Uptime</h3>
                    <div class="status-indicator good">●</div>
                </div>
                <div class="card-content">
                    <div class="metric-value"><?php echo esc_html($monitoring_data['uptime'] ?? '99.9%'); ?></div>
                    <div class="metric-label">Last 30 days</div>
                </div>
            </div>

            <div class="status-card response-time">
                <div class="card-header">
                    <h3><span class="dashicons dashicons-performance"></span> Response Time</h3>
                    <div class="status-indicator good">●</div>
                </div>
                <div class="card-content">
                    <div class="metric-value"><?php echo esc_html($monitoring_data['average_response_time'] ?? '450ms'); ?></div>
                    <div class="metric-label">Average response</div>
                </div>
            </div>

            <div class="status-card traffic">
                <div class="card-header">
                    <h3><span class="dashicons dashicons-chart-line"></span> Site Traffic</h3>
                    <div class="status-indicator good">●</div>
                </div>
                <div class="card-content">
                    <div class="metric-value"><?php echo esc_html($monitoring_data['total_visits'] ?? '1,250'); ?></div>
                    <div class="metric-label">Visits this month</div>
                </div>
            </div>

            <div class="status-card bounce-rate">
                <div class="card-header">
                    <h3><span class="dashicons dashicons-dismiss"></span> Bounce Rate</h3>
                    <div class="status-indicator warning">●</div>
                </div>
                <div class="card-content">
                    <div class="metric-value"><?php echo esc_html($monitoring_data['bounce_rate'] ?? '35%'); ?></div>
                    <div class="metric-label">This month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    <div class="alerts-section">
        <h2>Active Alerts</h2>
        
        <?php if (!empty($alerts) && is_array($alerts)): ?>
            <div class="alerts-list">
                <?php foreach ($alerts as $alert): ?>
                    <div class="alert-item <?php echo esc_attr($alert['severity'] ?? 'medium'); ?>">
                        <div class="alert-icon">
                            <?php
                            switch ($alert['severity'] ?? 'medium') {
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
                        <div class="alert-content">
                            <h4><?php echo esc_html($alert['title'] ?? 'Unknown Alert'); ?></h4>
                            <p><?php echo esc_html($alert['description'] ?? 'No description available.'); ?></p>
                            <div class="alert-meta">
                                <span class="alert-time">
                                    <?php echo esc_html(date('M j, Y H:i', strtotime($alert['created_at'] ?? 'now'))); ?>
                                </span>
                            </div>
                        </div>
                        <div class="alert-actions">
                            <button class="kata-btn kata-btn-small resolve-alert" data-alert-id="<?php echo esc_attr($alert['id'] ?? ''); ?>">
                                Resolve
                            </button>
                            <button class="kata-btn kata-btn-small kata-btn-secondary snooze-alert" data-alert-id="<?php echo esc_attr($alert['id'] ?? ''); ?>">
                                Snooze
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-alerts">
                <div class="no-alerts-icon">
                    <span class="dashicons dashicons-yes-alt"></span>
                </div>
                <h3>No Active Alerts</h3>
                <p>Great! Your website is running smoothly with no critical issues detected.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Monitoring Settings -->
    <div class="monitoring-settings">
        <h2>Monitoring Configuration</h2>
        
        <div class="settings-grid">
            <div class="settings-card">
                <h4><span class="dashicons dashicons-admin-tools"></span> What to Monitor</h4>
                <div class="monitoring-options">
                    <label>
                        <input type="checkbox" checked>
                        <span>Site uptime and availability</span>
                    </label>
                    <label>
                        <input type="checkbox" checked>
                        <span>Page load speed</span>
                    </label>
                    <label>
                        <input type="checkbox" checked>
                        <span>SEO score changes</span>
                    </label>
                    <label>
                        <input type="checkbox">
                        <span>Keyword ranking changes</span>
                    </label>
                    <label>
                        <input type="checkbox">
                        <span>Technical SEO issues</span>
                    </label>
                    <label>
                        <input type="checkbox">
                        <span>Security alerts</span>
                    </label>
                </div>
            </div>

            <div class="settings-card">
                <h4><span class="dashicons dashicons-clock"></span> Check Frequency</h4>
                <div class="frequency-options">
                    <label>
                        <input type="radio" name="check_frequency" value="5" checked>
                        <span>Every 5 minutes</span>
                    </label>
                    <label>
                        <input type="radio" name="check_frequency" value="15">
                        <span>Every 15 minutes</span>
                    </label>
                    <label>
                        <input type="radio" name="check_frequency" value="60">
                        <span>Every hour</span>
                    </label>
                    <label>
                        <input type="radio" name="check_frequency" value="1440">
                        <span>Daily</span>
                    </label>
                </div>
            </div>

            <div class="settings-card">
                <h4><span class="dashicons dashicons-email"></span> Alert Notifications</h4>
                <div class="notification-options">
                    <div class="option-group">
                        <label for="email-alerts">
                            <input type="checkbox" id="email-alerts" checked>
                            <span>Email notifications</span>
                        </label>
                        <input type="email" placeholder="admin@example.com" class="kata-input" style="margin-top: 5px;">
                    </div>
                    
                    <div class="option-group">
                        <label>
                            <input type="checkbox">
                            <span>SMS notifications</span>
                        </label>
                        <input type="tel" placeholder="+1234567890" class="kata-input" style="margin-top: 5px;">
                    </div>
                    
                    <div class="option-group">
                        <label>
                            <input type="checkbox" checked>
                            <span>Dashboard notifications</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h4><span class="dashicons dashicons-shield"></span> Alert Thresholds</h4>
                <div class="threshold-options">
                    <div class="threshold-item">
                        <label>Page load time threshold:</label>
                        <input type="number" value="3" min="1" max="10" class="kata-input-small"> seconds
                    </div>
                    <div class="threshold-item">
                        <label>Uptime threshold:</label>
                        <input type="number" value="99" min="90" max="100" class="kata-input-small">%
                    </div>
                    <div class="threshold-item">
                        <label>SEO score drop threshold:</label>
                        <input type="number" value="5" min="1" max="50" class="kata-input-small"> points
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-actions">
            <button class="kata-btn kata-btn-primary save-settings">
                <span class="dashicons dashicons-yes"></span>
                Save Settings
            </button>
            <button class="kata-btn kata-btn-secondary test-alerts">
                <span class="dashicons dashicons-bell"></span>
                Test Alerts
            </button>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <h2>Recent Monitoring Activity</h2>
        
        <div class="activity-timeline">
            <div class="activity-item">
                <div class="activity-time">2 hours ago</div>
                <div class="activity-icon good">✓</div>
                <div class="activity-content">
                    <div class="activity-title">Site uptime check: All good</div>
                    <div class="activity-description">Response time: 420ms</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-time">6 hours ago</div>
                <div class="activity-icon good">✓</div>
                <div class="activity-content">
                    <div class="activity-title">SEO score check: No changes</div>
                    <div class="activity-description">Average score remains at 85/100</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-time">1 day ago</div>
                <div class="activity-icon warning">⚠</div>
                <div class="activity-content">
                    <div class="activity-title">Slow response time detected</div>
                    <div class="activity-description">Response time exceeded 3 seconds for 2 minutes</div>
                </div>
            </div>

            <div class="activity-item">
                <div class="activity-time">2 days ago</div>
                <div class="activity-icon good">✓</div>
                <div class="activity-content">
                    <div class="activity-title">Weekly SEO scan completed</div>
                    <div class="activity-description">247 pages analyzed, 3 new issues found</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kata-seo-monitoring {
    background: #f0f0f1;
    margin: 0 -20px;
    padding: 20px;
}

.monitoring-overview, .alerts-section, .monitoring-settings, .recent-activity {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.status-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    background: #f9f9f9;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.card-header h3 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    font-size: 12px;
}

.status-indicator.good {
    color: #00a32a;
}

.status-indicator.warning {
    color: #dba617;
}

.status-indicator.error {
    color: #d63638;
}

.metric-value {
    font-size: 32px;
    font-weight: 700;
    color: #1d2327;
}

.metric-label {
    font-size: 12px;
    color: #646970;
    margin-top: 5px;
}

.alert-item {
    display: flex;
    gap: 15px;
    padding: 15px;
    border-left: 4px solid #ddd;
    margin-bottom: 15px;
    background: #f9f9f9;
    border-radius: 0 8px 8px 0;
}

.alert-item.high {
    border-left-color: #d63638;
}

.alert-item.medium {
    border-left-color: #dba617;
}

.alert-item.low {
    border-left-color: #135e96;
}

.alert-content {
    flex: 1;
}

.alert-content h4 {
    margin: 0 0 5px 0;
}

.alert-meta {
    font-size: 12px;
    color: #646970;
    margin-top: 10px;
}

.alert-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.no-alerts {
    text-align: center;
    padding: 40px;
}

.no-alerts-icon .dashicons {
    font-size: 48px;
    color: #00a32a;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.settings-card {
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

.settings-card h4 {
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.monitoring-options label,
.frequency-options label {
    display: block;
    margin-bottom: 8px;
    cursor: pointer;
}

.option-group {
    margin-bottom: 15px;
}

.threshold-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.kata-input-small {
    width: 60px;
}

.settings-actions {
    display: flex;
    gap: 10px;
}

.activity-timeline {
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: grid;
    grid-template-columns: 80px 30px 1fr;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-time {
    font-size: 12px;
    color: #646970;
}

.activity-icon {
    text-align: center;
    font-weight: bold;
}

.activity-icon.good {
    color: #00a32a;
}

.activity-icon.warning {
    color: #dba617;
}

.activity-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.activity-description {
    font-size: 12px;
    color: #646970;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Save settings
    $('.save-settings').click(function() {
        alert('Settings saved successfully!');
    });

    // Test alerts
    $('.test-alerts').click(function() {
        alert('Test alert sent!');
    });

    // Resolve alert
    $('.resolve-alert').click(function() {
        $(this).closest('.alert-item').fadeOut();
    });

    // Snooze alert
    $('.snooze-alert').click(function() {
        $(this).closest('.alert-item').addClass('snoozed');
        $(this).text('Snoozed');
    });
});
</script>
