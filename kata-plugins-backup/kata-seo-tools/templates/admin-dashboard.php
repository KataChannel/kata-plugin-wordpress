<?php
/**
 * Admin Dashboard Template
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get statistics
global $wpdb;

// Quiz stats
$quiz_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_quiz_results");
$quiz_avg = $wpdb->get_var("SELECT AVG(score) FROM {$wpdb->prefix}kata_seo_quiz_results");

// Poll stats
$poll_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_poll_votes");

// Rating stats
$rating_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_ratings");
$rating_avg = $wpdb->get_var("SELECT AVG(rating) FROM {$wpdb->prefix}kata_seo_ratings");

// Form stats
$form_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_form_submissions");

// Wheel stats
$wheel_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_wheel_spins");

// Social share stats
$share_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_social_shares");

// Recent activity
$recent_quiz = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_seo_quiz_results ORDER BY submitted_at DESC LIMIT 5");
$recent_forms = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_seo_form_submissions ORDER BY submitted_at DESC LIMIT 5");
?>

<div class="wrap kata-seo-dashboard">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- Stats Overview -->
    <div class="kata-stats-grid">
        <div class="kata-stat-card quiz">
            <div class="stat-icon">
                <span class="dashicons dashicons-welcome-learn-more"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo esc_html($quiz_count); ?></h3>
                <p>Quiz Submissions</p>
                <span class="stat-meta">Avg Score: <?php echo esc_html(number_format($quiz_avg, 1)); ?>%</span>
            </div>
        </div>

        <div class="kata-stat-card poll">
            <div class="stat-icon">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo esc_html($poll_count); ?></h3>
                <p>Poll Votes</p>
                <span class="stat-meta">Total Responses</span>
            </div>
        </div>

        <div class="kata-stat-card rating">
            <div class="stat-icon">
                <span class="dashicons dashicons-star-filled"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo esc_html($rating_count); ?></h3>
                <p>Ratings Received</p>
                <span class="stat-meta">Avg: <?php echo esc_html(number_format($rating_avg, 1)); ?> ⭐</span>
            </div>
        </div>

        <div class="kata-stat-card form">
            <div class="stat-icon">
                <span class="dashicons dashicons-feedback"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo esc_html($form_count); ?></h3>
                <p>Form Submissions</p>
                <span class="stat-meta">Total Leads</span>
            </div>
        </div>

        <div class="kata-stat-card wheel">
            <div class="stat-icon">
                <span class="dashicons dashicons-games"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo esc_html($wheel_count); ?></h3>
                <p>Wheel Spins</p>
                <span class="stat-meta">Gamification</span>
            </div>
        </div>

        <div class="kata-stat-card social">
            <div class="stat-icon">
                <span class="dashicons dashicons-share"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo esc_html($share_count); ?></h3>
                <p>Social Shares</p>
                <span class="stat-meta">Viral Reach</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="kata-charts-row">
        <div class="kata-chart-card">
            <h2>Quiz Performance (Last 7 Days)</h2>
            <canvas id="quizChart"></canvas>
        </div>

        <div class="kata-chart-card">
            <h2>Social Share Distribution</h2>
            <canvas id="shareChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="kata-recent-activity">
        <div class="kata-activity-card">
            <h2>Recent Quiz Submissions</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>User</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_quiz)) : ?>
                        <?php foreach ($recent_quiz as $quiz) : ?>
                            <tr>
                                <td>#<?php echo esc_html($quiz->quiz_id); ?></td>
                                <td>
                                    <?php 
                                    if ($quiz->user_id) {
                                        $user = get_user_by('id', $quiz->user_id);
                                        echo esc_html($user ? $user->display_name : 'Guest');
                                    } else {
                                        echo esc_html($quiz->user_name ?: 'Anonymous');
                                    }
                                    ?>
                                </td>
                                <td><strong><?php echo esc_html($quiz->score); ?>%</strong></td>
                                <td><?php echo esc_html(human_time_diff(strtotime($quiz->submitted_at), current_time('timestamp'))); ?> ago</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No quiz submissions yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="kata-activity-card">
            <h2>Recent Form Submissions</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Form ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_forms)) : ?>
                        <?php foreach ($recent_forms as $form) : ?>
                            <?php 
                            $data = json_decode($form->form_data, true);
                            ?>
                            <tr>
                                <td>#<?php echo esc_html($form->form_id); ?></td>
                                <td><?php echo esc_html($data['name'] ?? 'N/A'); ?></td>
                                <td><?php echo esc_html($data['email'] ?? 'N/A'); ?></td>
                                <td><?php echo esc_html(human_time_diff(strtotime($form->submitted_at), current_time('timestamp'))); ?> ago</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4">No form submissions yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="kata-quick-actions">
        <h2>Quick Actions</h2>
        <div class="kata-actions-grid">
            <a href="<?php echo admin_url('admin.php?page=kata-seo-analytics'); ?>" class="kata-action-btn">
                <span class="dashicons dashicons-chart-area"></span>
                View Analytics
            </a>
            <a href="<?php echo admin_url('admin.php?page=kata-seo-settings'); ?>" class="kata-action-btn">
                <span class="dashicons dashicons-admin-settings"></span>
                Settings
            </a>
            <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="kata-action-btn">
                <span class="dashicons dashicons-edit-page"></span>
                Add Shortcodes
            </a>
            <a href="https://docs.kata-seo-tools.com" class="kata-action-btn" target="_blank">
                <span class="dashicons dashicons-media-document"></span>
                Documentation
            </a>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Quiz performance chart
    const quizCtx = document.getElementById('quizChart');
    if (quizCtx) {
        new Chart(quizCtx, {
            type: 'line',
            data: {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                datasets: [{
                    label: 'Quiz Submissions',
                    data: [12, 19, 15, 25, 22, 30, 28],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Social share distribution chart
    const shareCtx = document.getElementById('shareChart');
    if (shareCtx) {
        new Chart(shareCtx, {
            type: 'doughnut',
            data: {
                labels: ['Facebook', 'Twitter', 'LinkedIn', 'Pinterest', 'WhatsApp'],
                datasets: [{
                    data: [300, 150, 100, 80, 70],
                    backgroundColor: [
                        '#1877f2',
                        '#1da1f2',
                        '#0077b5',
                        '#e60023',
                        '#25d366'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right'
                    }
                }
            }
        });
    }
});
</script>
