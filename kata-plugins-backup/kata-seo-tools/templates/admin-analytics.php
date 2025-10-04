<?php
/**
 * Admin Analytics Template
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get statistics
global $wpdb;

// Time filters
$time_filter = isset($_GET['time']) ? sanitize_text_field($_GET['time']) : '7days';
$date_condition = '';

switch ($time_filter) {
    case '24hours':
        $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
        break;
    case '7days':
        $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        break;
    case '30days':
        $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        break;
    case '90days':
        $date_condition = "AND created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
        break;
    case 'all':
    default:
        $date_condition = "";
        break;
}
?>

<div class="wrap kata-seo-analytics">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- Filters -->
    <div class="kata-analytics-filters">
        <form method="get">
            <input type="hidden" name="page" value="kata-seo-analytics">
            
            <select name="time" onchange="this.form.submit()">
                <option value="24hours" <?php selected($time_filter, '24hours'); ?>>Last 24 Hours</option>
                <option value="7days" <?php selected($time_filter, '7days'); ?>>Last 7 Days</option>
                <option value="30days" <?php selected($time_filter, '30days'); ?>>Last 30 Days</option>
                <option value="90days" <?php selected($time_filter, '90days'); ?>>Last 90 Days</option>
                <option value="all" <?php selected($time_filter, 'all'); ?>>All Time</option>
            </select>
            
            <button type="button" class="button" onclick="window.print();">
                <span class="dashicons dashicons-download"></span> Export Report
            </button>
        </form>
    </div>

    <!-- Overview Stats -->
    <div class="kata-stats-grid">
        <?php
        // Quiz stats
        $quiz_total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_quiz_results WHERE 1=1 $date_condition");
        $quiz_avg_score = $wpdb->get_var("SELECT AVG(score) FROM {$wpdb->prefix}kata_seo_quiz_results WHERE 1=1 $date_condition");
        
        // Poll stats
        $poll_total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_poll_votes WHERE 1=1 $date_condition");
        
        // Rating stats
        $rating_total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_ratings WHERE 1=1 $date_condition");
        $rating_avg = $wpdb->get_var("SELECT AVG(rating) FROM {$wpdb->prefix}kata_seo_ratings WHERE 1=1 $date_condition");
        
        // Form stats
        $form_total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_form_submissions WHERE 1=1 $date_condition");
        
        // Wheel stats
        $wheel_total = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_seo_wheel_spins WHERE 1=1 $date_condition");
        
        // Social share stats
        $share_total = $wpdb->get_var("SELECT SUM(share_count) FROM {$wpdb->prefix}kata_seo_social_shares");
        ?>
        
        <div class="kata-stat-card">
            <h3>Quiz Submissions</h3>
            <p class="stat-number"><?php echo number_format($quiz_total ?? 0); ?></p>
            <p class="stat-meta">Avg Score: <?php echo number_format($quiz_avg_score ?? 0, 1); ?>%</p>
        </div>
        
                <div class="kata-stat-card">
            <h3>Poll Votes</h3>
            <p class="stat-number"><?php echo number_format($poll_total ?? 0); ?></p>
        </div>
        
        <div class="kata-stat-card">
            <h3>Ratings</h3>
            <p class="stat-number"><?php echo number_format($rating_total ?? 0); ?></p>
            <p class="stat-meta">Avg: <?php echo number_format($rating_avg ?? 0, 1); ?> ⭐</p>
        </div>
        
        <div class="kata-stat-card">
            <h3>Form Submissions</h3>
            <p class="stat-number"><?php echo number_format($form_total ?? 0); ?></p>
        </div>
        
        <div class="kata-stat-card">
            <h3>Wheel Spins</h3>
            <p class="stat-number"><?php echo number_format($wheel_total ?? 0); ?></p>
        </div>
        
        <div class="kata-stat-card">
            <h3>Social Shares</h3>
            <p class="stat-number"><?php echo number_format($share_total ?? 0); ?></p>
        </div>
    </div>

    <!-- Detailed Charts -->
    <div class="kata-charts-container">
        
        <!-- Quiz Performance -->
        <div class="kata-chart-section">
            <h2>Quiz Performance Over Time</h2>
            <canvas id="quizPerformanceChart"></canvas>
            
            <?php
            // Get daily quiz data
            $quiz_data = $wpdb->get_results("
                SELECT DATE(created_at) as date, 
                       COUNT(*) as count,
                       AVG(score) as avg_score
                FROM {$wpdb->prefix}kata_seo_quiz_results 
                WHERE 1=1 $date_condition
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            ?>
        </div>

        <!-- Top Quizzes -->
        <div class="kata-chart-section">
            <h2>Most Popular Quizzes</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Submissions</th>
                        <th>Avg Score</th>
                        <th>Pass Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $top_quizzes = $wpdb->get_results("
                        SELECT quiz_id,
                               COUNT(*) as submissions,
                               AVG(score) as avg_score,
                               (SUM(CASE WHEN score >= 70 THEN 1 ELSE 0 END) / COUNT(*) * 100) as pass_rate
                        FROM {$wpdb->prefix}kata_seo_quiz_results
                        WHERE 1=1 $date_condition
                        GROUP BY quiz_id
                        ORDER BY submissions DESC
                        LIMIT 10
                    ");
                    
                    if ($top_quizzes) :
                        foreach ($top_quizzes as $quiz) :
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($quiz->quiz_id); ?></strong></td>
                            <td><?php echo number_format($quiz->submissions ?? 0); ?></td>
                            <td><?php echo number_format($quiz->avg_score ?? 0, 1); ?>%</td>
                            <td><?php echo number_format($quiz->pass_rate ?? 0, 1); ?>%</td>
                        </tr>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <tr>
                            <td colspan="4">No quiz data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Social Share Breakdown -->
        <div class="kata-chart-section">
            <h2>Social Share Distribution</h2>
            <canvas id="socialShareChart"></canvas>
            
            <?php
            $share_by_platform = $wpdb->get_results("
                SELECT platform, SUM(share_count) as total_shares
                FROM {$wpdb->prefix}kata_seo_social_shares
                GROUP BY platform
                ORDER BY total_shares DESC
            ");
            ?>
        </div>

        <!-- Rating Breakdown -->
        <div class="kata-chart-section">
            <h2>Rating Distribution</h2>
            <canvas id="ratingChart"></canvas>
            
            <?php
            $rating_breakdown = $wpdb->get_results("
                SELECT rating, COUNT(*) as count
                FROM {$wpdb->prefix}kata_seo_ratings
                WHERE 1=1 $date_condition
                GROUP BY rating
                ORDER BY rating DESC
            ");
            ?>
        </div>

        <!-- Form Submissions -->
        <div class="kata-chart-section">
            <h2>Recent Form Submissions</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Form ID</th>
                        <th>Post</th>
                        <th>Submissions</th>
                        <th>Latest</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $form_stats = $wpdb->get_results("
                        SELECT form_id,
                               post_id,
                               COUNT(*) as submissions,
                               MAX(created_at) as latest
                        FROM {$wpdb->prefix}kata_seo_form_submissions
                        WHERE 1=1 $date_condition
                        GROUP BY form_id, post_id
                        ORDER BY submissions DESC
                        LIMIT 10
                    ");
                    
                    if ($form_stats) :
                        foreach ($form_stats as $form) :
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($form->form_id); ?></strong></td>
                            <td><?php echo esc_html(get_the_title($form->post_id)); ?></td>
                            <td><?php echo number_format($form->submissions ?? 0); ?></td>
                            <td><?php echo esc_html(human_time_diff(strtotime($form->latest), current_time('timestamp'))); ?> ago</td>
                            <td>
                                <a href="#" class="button button-small">Export CSV</a>
                            </td>
                        </tr>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <tr>
                            <td colspan="5">No form submissions yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Quiz performance chart
    const quizData = <?php echo json_encode($quiz_data); ?>;
    const quizLabels = quizData.map(d => d.date);
    const quizCounts = quizData.map(d => parseInt(d.count));
    const quizScores = quizData.map(d => parseFloat(d.avg_score));
    
    new Chart(document.getElementById('quizPerformanceChart'), {
        type: 'line',
        data: {
            labels: quizLabels,
            datasets: [{
                label: 'Submissions',
                data: quizCounts,
                borderColor: '#4CAF50',
                yAxisID: 'y'
            }, {
                label: 'Avg Score (%)',
                data: quizScores,
                borderColor: '#2196F3',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    type: 'linear',
                    position: 'left'
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    // Social share chart
    const shareData = <?php echo json_encode($share_by_platform); ?>;
    const shareLabels = shareData.map(d => d.platform);
    const shareCounts = shareData.map(d => parseInt(d.total_shares));
    
    new Chart(document.getElementById('socialShareChart'), {
        type: 'doughnut',
        data: {
            labels: shareLabels,
            datasets: [{
                data: shareCounts,
                backgroundColor: ['#1877f2', '#1da1f2', '#0077b5', '#e60023', '#25d366', '#229ED9', '#000000']
            }]
        }
    });
    
    // Rating chart
    const ratingData = <?php echo json_encode($rating_breakdown); ?>;
    const ratingLabels = ratingData.map(d => d.rating + ' Stars');
    const ratingCounts = ratingData.map(d => parseInt(d.count));
    
    new Chart(document.getElementById('ratingChart'), {
        type: 'bar',
        data: {
            labels: ratingLabels,
            datasets: [{
                label: 'Ratings',
                data: ratingCounts,
                backgroundColor: '#FFC107'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
