<?php
/**
 * Senior Quiz Analytics Dashboard
 * Advanced analytics with comprehensive insights and data visualization
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Ensure database tables exist
$db_instance = new KATA_SEO_Database();
$db_instance->create_tables();

// Get quiz analytics data
global $wpdb;
$table_quizzes = $wpdb->prefix . 'kata_seo_quizzes';
$table_attempts = $wpdb->prefix . 'kata_seo_quiz_attempts';
$table_analytics = $wpdb->prefix . 'kata_seo_quiz_analytics';
$table_sessions = $wpdb->prefix . 'kata_seo_quiz_sessions';
$table_question_analytics = $wpdb->prefix . 'kata_seo_quiz_question_analytics';

// Date range filters
$start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');
$quiz_filter = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Build WHERE clause for filters
$date_filter = "a.created_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'";
$quiz_filter_clause = $quiz_filter ? "AND q.id = $quiz_filter" : '';

// Get comprehensive quiz stats
$quizzes_query = "
    SELECT q.*, 
           COUNT(DISTINCT a.id) as total_attempts,
           COUNT(DISTINCT s.id) as total_sessions,
           AVG(a.score) as avg_score,
           COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) as total_passes,
           COUNT(DISTINCT CASE WHEN a.time_taken > 0 THEN a.id END) as total_completions,
           AVG(a.time_taken) as avg_time_taken,
           MIN(a.created_at) as first_attempt,
           MAX(a.created_at) as last_attempt,
           p.post_title,
           p.post_status,
           (COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) * 100.0 / NULLIF(COUNT(DISTINCT a.id), 0)) as pass_rate,
           (COUNT(DISTINCT CASE WHEN a.time_taken > 0 THEN a.id END) * 100.0 / NULLIF(COUNT(DISTINCT s.id), 0)) as completion_rate,
           COALESCE(SUM(ana.total_views), 0) as total_views
    FROM {$table_quizzes} q
    LEFT JOIN {$table_attempts} a ON q.id = a.quiz_id AND $date_filter
    LEFT JOIN {$table_sessions} s ON q.id = s.quiz_id AND s.started_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
    LEFT JOIN {$table_analytics} ana ON q.id = ana.quiz_id AND ana.date BETWEEN '$start_date' AND '$end_date'
    LEFT JOIN {$wpdb->posts} p ON q.post_id = p.ID
    WHERE q.is_active = 1 $quiz_filter_clause
    GROUP BY q.id
    ORDER BY total_attempts DESC, q.created_at DESC
";

$quizzes = $wpdb->get_results($quizzes_query);

// Get recent activity with more details
$recent_attempts = $wpdb->get_results("
    SELECT a.*, q.quiz_title, q.total_questions, p.post_title, u.display_name,
           CASE 
               WHEN a.score >= 90 THEN 'excellent'
               WHEN a.score >= 80 THEN 'good'  
               WHEN a.score >= 70 THEN 'pass'
               WHEN a.score >= 60 THEN 'fair'
               ELSE 'poor'
           END as performance_level
    FROM {$table_attempts} a
    LEFT JOIN {$table_quizzes} q ON a.quiz_id = q.id
    LEFT JOIN {$wpdb->posts} p ON q.post_id = p.ID
    LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
    WHERE $date_filter $quiz_filter_clause
    ORDER BY a.created_at DESC
    LIMIT 15
");

// Get comprehensive overall stats
$overall_stats = $wpdb->get_row("
    SELECT 
        COUNT(DISTINCT q.id) as total_quizzes,
        COUNT(DISTINCT a.id) as total_attempts,
        COUNT(DISTINCT s.id) as total_sessions,
        AVG(a.score) as avg_score,
        COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) as total_passes,
        COUNT(DISTINCT CASE WHEN a.time_taken > 0 THEN a.id END) as total_completions,
        COUNT(DISTINCT a.user_id) as unique_users,
        AVG(a.time_taken) as avg_time_taken,
        (COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) * 100.0 / NULLIF(COUNT(DISTINCT a.id), 0)) as overall_pass_rate,
        (COUNT(DISTINCT CASE WHEN a.time_taken > 0 THEN a.id END) * 100.0 / NULLIF(COUNT(DISTINCT s.id), 0)) as overall_completion_rate,
        COALESCE(SUM(ana.total_views), 0) as total_views
    FROM {$table_quizzes} q
    LEFT JOIN {$table_attempts} a ON q.id = a.quiz_id AND $date_filter
    LEFT JOIN {$table_sessions} s ON q.id = s.quiz_id AND s.started_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
    LEFT JOIN {$table_analytics} ana ON q.id = ana.quiz_id AND ana.date BETWEEN '$start_date' AND '$end_date'
    WHERE q.is_active = 1 $quiz_filter_clause
");

// Get performance trends (last 7 days)
$trends_data = $wpdb->get_results("
    SELECT DATE(a.created_at) as date,
           COUNT(DISTINCT a.id) as attempts,
           AVG(a.score) as avg_score,
           COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) as passes
    FROM {$table_attempts} a
    LEFT JOIN {$table_quizzes} q ON a.quiz_id = q.id
    WHERE a.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
          AND q.is_active = 1 $quiz_filter_clause
    GROUP BY DATE(a.created_at)
    ORDER BY date DESC
");

// Get top performing quizzes
$top_quizzes = $wpdb->get_results("
    SELECT q.quiz_title, q.id,
           COUNT(DISTINCT a.id) as attempts,
           AVG(a.score) as avg_score,
           (COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) * 100.0 / NULLIF(COUNT(DISTINCT a.id), 0)) as pass_rate
    FROM {$table_quizzes} q
    LEFT JOIN {$table_attempts} a ON q.id = a.quiz_id AND $date_filter
    WHERE q.is_active = 1 $quiz_filter_clause
    GROUP BY q.id
    HAVING attempts > 0
    ORDER BY pass_rate DESC, avg_score DESC
    LIMIT 5
");

// Get user engagement metrics
$engagement_stats = $wpdb->get_row("
    SELECT 
        AVG(s.time_spent) as avg_session_time,
        AVG(s.questions_answered * 100.0 / NULLIF(q.total_questions, 0)) as avg_completion_percentage,
        COUNT(DISTINCT CASE WHEN s.status = 'completed' THEN s.id END) as completed_sessions,
        COUNT(DISTINCT CASE WHEN s.status = 'abandoned' THEN s.id END) as abandoned_sessions,
        (COUNT(DISTINCT CASE WHEN s.status = 'abandoned' THEN s.id END) * 100.0 / NULLIF(COUNT(DISTINCT s.id), 0)) as abandonment_rate
    FROM {$table_sessions} s
    LEFT JOIN {$table_quizzes} q ON s.quiz_id = q.id
    WHERE s.started_at BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
          AND q.is_active = 1 $quiz_filter_clause
");
?>

<div class="wrap kata-quiz-analytics">
    <div class="kata-page-header">
        <div class="kata-header-content">
            <div class="kata-page-title">
                <div class="kata-page-icon">
                    <svg width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M1.5 1A1.5 1.5 0 0 0 0 2.5v11A1.5 1.5 0 0 0 1.5 15h13a1.5 1.5 0 0 0 1.5-1.5v-11A1.5 1.5 0 0 0 14.5 1h-13zM1 2.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v2H1v-2zm0 3h14v7.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-7.5z"/>
                        <path d="M2 6a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V6z"/>
                    </svg>
                </div>
                <div>
                    <h1><?php _e('Phân tích Quiz', 'kata-seo-manager'); ?></h1>
                    <p class="kata-page-description"><?php _e('Theo dõi hiệu suất và phân tích chi tiết của các quiz tương tác', 'kata-seo-manager'); ?></p>
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
                <button class="kata-btn kata-btn-primary" onclick="exportQuizData()">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                    </svg>
                    <?php _e('Xuất báo cáo', 'kata-seo-manager'); ?>
                </button>
            </div>
        </div>
    </div>

    <!-- Date Range Filters -->
    <div class="kata-analytics-filters">
        <form method="get" action="" class="kata-filter-form">
            <input type="hidden" name="page" value="kata-seo-quiz-analytics">
            
            <div class="filter-group">
                <label for="start_date"><?php _e('Từ ngày:', 'kata-seo-manager'); ?></label>
                <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>" class="kata-date-input">
            </div>
            
            <div class="filter-group">
                <label for="end_date"><?php _e('Đến ngày:', 'kata-seo-manager'); ?></label>
                <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>" class="kata-date-input">
            </div>
            
            <div class="filter-group">
                <label for="quiz_filter"><?php _e('Quiz:', 'kata-seo-manager'); ?></label>
                <select id="quiz_filter" name="quiz_id" class="kata-select">
                    <option value="0"><?php _e('Tất cả Quiz', 'kata-seo-manager'); ?></option>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?php echo esc_attr($quiz->id); ?>" <?php selected($quiz_filter, $quiz->id); ?>>
                            <?php echo esc_html($quiz->quiz_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="kata-btn kata-btn-primary">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                    <?php _e('Áp dụng bộ lọc', 'kata-seo-manager'); ?>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=kata-seo-quiz-analytics'); ?>" class="kata-btn kata-btn-outline">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                        <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                    </svg>
                    <?php _e('Đặt lại', 'kata-seo-manager'); ?>
                </a>
            </div>
        </form>
    </div>

    <div class="kata-analytics-container">
        <!-- Overview Stats -->
        <div class="kata-stats-overview">
            <div class="kata-stat-card total-quizzes">
                <div class="kata-stat-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                    </svg>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo number_format($overall_stats->total_quizzes ?? 0); ?></div>
                    <div class="kata-stat-label"><?php _e('Tổng Quiz', 'kata-seo-manager'); ?></div>
                </div>
            </div>

            <div class="kata-stat-card total-attempts">
                <div class="kata-stat-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                    </svg>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo number_format($overall_stats->total_attempts ?? 0); ?></div>
                    <div class="kata-stat-label"><?php _e('Lượt thử', 'kata-seo-manager'); ?></div>
                </div>
            </div>

            <div class="kata-stat-card avg-score">
                <div class="kata-stat-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68L9.669.864zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702 1.509.229z"/>
                        <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1 4 11.794z"/>
                    </svg>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php echo number_format($overall_stats->avg_score ?? 0, 1); ?>%</div>
                    <div class="kata-stat-label"><?php _e('Điểm trung bình', 'kata-seo-manager'); ?></div>
                </div>
            </div>

            <div class="kata-stat-card completion-rate">
                <div class="kata-stat-icon">
                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                        <path d="M8 1a7 7 0 1 0 0 14 7 7 0 0 0 0-14zM0 8a8 8 0 1 1 16 0 8 8 0 0 1-16 0z"/>
                    </svg>
                </div>
                <div class="kata-stat-content">
                    <div class="kata-stat-number"><?php 
                        $completion_rate = $overall_stats->total_attempts > 0 ? 
                            ($overall_stats->total_completions / $overall_stats->total_attempts) * 100 : 0;
                        echo number_format($completion_rate, 1);
                    ?>%</div>
                    <div class="kata-stat-label"><?php _e('Tỷ lệ hoàn thành', 'kata-seo-manager'); ?></div>
                </div>
            </div>
        </div>

        <!-- Advanced Analytics Section -->
        <div class="kata-analytics-advanced">
            <!-- Performance Trends Chart -->
            <div class="kata-card kata-chart-card">
                <div class="kata-card-header">
                    <h3><?php _e('Biểu đồ hiệu suất 7 ngày qua', 'kata-seo-manager'); ?></h3>
                    <div class="kata-chart-controls">
                        <select id="chart-type" class="kata-select-small">
                            <option value="attempts"><?php _e('Lượt thử', 'kata-seo-manager'); ?></option>
                            <option value="scores"><?php _e('Điểm số', 'kata-seo-manager'); ?></option>
                            <option value="passes"><?php _e('Lượt pass', 'kata-seo-manager'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="kata-card-content">
                    <canvas id="trendsChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Top Performing Quizzes -->
            <div class="kata-card">
                <div class="kata-card-header">
                    <h3><?php _e('Top Quiz hiệu suất cao', 'kata-seo-manager'); ?></h3>
                </div>
                <div class="kata-card-content">
                    <?php if (empty($top_quizzes)): ?>
                        <div class="kata-empty-state-small">
                            <p><?php _e('Chưa có dữ liệu hiệu suất.', 'kata-seo-manager'); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="kata-top-quizzes">
                            <?php foreach ($top_quizzes as $index => $quiz): ?>
                                <div class="kata-top-quiz-item">
                                    <div class="quiz-rank">
                                        <?php if ($index == 0): ?>🥇<?php elseif ($index == 1): ?>🥈<?php elseif ($index == 2): ?>🥉<?php else: ?><?php echo $index + 1; ?><?php endif; ?>
                                    </div>
                                    <div class="quiz-info">
                                        <div class="quiz-name"><?php echo esc_html($quiz->quiz_title); ?></div>
                                        <div class="quiz-stats">
                                            <?php echo intval($quiz->attempts); ?> lượt thử • 
                                            <?php echo number_format($quiz->avg_score, 1); ?>% điểm TB • 
                                            <?php echo number_format($quiz->pass_rate, 1); ?>% pass rate
                                        </div>
                                    </div>
                                    <div class="quiz-score-badge">
                                        <?php echo number_format($quiz->pass_rate, 1); ?>%
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- User Engagement Metrics -->
            <div class="kata-card">
                <div class="kata-card-header">
                    <h3><?php _e('Phân tích tương tác người dùng', 'kata-seo-manager'); ?></h3>
                </div>
                <div class="kata-card-content">
                    <div class="kata-engagement-metrics">
                        <div class="engagement-metric">
                            <div class="metric-icon">⏱️</div>
                            <div class="metric-content">
                                <div class="metric-value">
                                    <?php 
                                    $avg_session = intval($engagement_stats->avg_session_time ?? 0);
                                    echo $avg_session > 0 ? gmdate('i:s', $avg_session) : '--:--';
                                    ?>
                                </div>
                                <div class="metric-label"><?php _e('Thời gian TB/phiên', 'kata-seo-manager'); ?></div>
                            </div>
                        </div>

                        <div class="engagement-metric">
                            <div class="metric-icon">📊</div>
                            <div class="metric-content">
                                <div class="metric-value">
                                    <?php echo number_format($engagement_stats->avg_completion_percentage ?? 0, 1); ?>%
                                </div>
                                <div class="metric-label"><?php _e('Tỷ lệ hoàn thành TB', 'kata-seo-manager'); ?></div>
                            </div>
                        </div>

                        <div class="engagement-metric">
                            <div class="metric-icon">✅</div>
                            <div class="metric-content">
                                <div class="metric-value">
                                    <?php echo intval($engagement_stats->completed_sessions ?? 0); ?>
                                </div>
                                <div class="metric-label"><?php _e('Phiên hoàn thành', 'kata-seo-manager'); ?></div>
                            </div>
                        </div>

                        <div class="engagement-metric">
                            <div class="metric-icon">⚠️</div>
                            <div class="metric-content">
                                <div class="metric-value">
                                    <?php echo number_format($engagement_stats->abandonment_rate ?? 0, 1); ?>%
                                </div>
                                <div class="metric-label"><?php _e('Tỷ lệ bỏ dở', 'kata-seo-manager'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quiz List -->
        <div class="kata-analytics-main">
            <div class="kata-card">
                <div class="kata-card-header">
                    <h3><?php _e('Danh sách Quiz', 'kata-seo-manager'); ?></h3>
                    <div class="kata-card-actions">
                        <input type="text" placeholder="<?php _e('Tìm kiếm quiz...', 'kata-seo-manager'); ?>" class="kata-search-input" />
                        <button class="kata-btn kata-btn-outline" onclick="refreshAnalytics()">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
                            </svg>
                            <?php _e('Làm mới', 'kata-seo-manager'); ?>
                        </button>
                    </div>
                </div>
                <div class="kata-card-content">
                    <?php if (empty($quizzes)): ?>
                        <div class="kata-empty-state">
                            <div class="kata-empty-icon">🎯</div>
                            <h4><?php _e('Chưa có quiz nào', 'kata-seo-manager'); ?></h4>
                            <p><?php _e('Tạo quiz đầu tiên của bạn bằng cách sử dụng shortcode [kata_quiz] trong bài viết hoặc trang.', 'kata-seo-manager'); ?></p>
                            <a href="<?php echo admin_url('admin.php?page=kata-seo-schema-types'); ?>" class="kata-btn kata-btn-primary">
                                <?php _e('Tìm hiểu cách tạo Quiz', 'kata-seo-manager'); ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="kata-quiz-table">
                            <table class="wp-list-table widefat fixed striped">
                                <thead>
                                    <tr>
                                        <th><?php _e('Quiz', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Bài viết/Trang', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Câu hỏi', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Lượt thử', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Điểm TB', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Hoàn thành', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Ngày tạo', 'kata-seo-manager'); ?></th>
                                        <th><?php _e('Thao tác', 'kata-seo-manager'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo esc_html($quiz->quiz_title); ?></strong>
                                                <div class="quiz-status">
                                                    <?php if ($quiz->is_active): ?>
                                                        <span class="status-active">🟢 <?php _e('Hoạt động', 'kata-seo-manager'); ?></span>
                                                    <?php else: ?>
                                                        <span class="status-inactive">🔴 <?php _e('Tạm dừng', 'kata-seo-manager'); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($quiz->post_title): ?>
                                                    <a href="<?php echo get_permalink($quiz->post_id); ?>" target="_blank">
                                                        <?php echo esc_html($quiz->post_title); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <em><?php _e('Bài viết đã xóa', 'kata-seo-manager'); ?></em>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="quiz-questions"><?php echo intval($quiz->total_questions); ?></span>
                                            </td>
                                            <td>
                                                <span class="quiz-attempts"><?php echo intval($quiz->total_attempts); ?></span>
                                            </td>
                                            <td>
                                                <span class="quiz-score <?php echo $quiz->avg_score >= 70 ? 'score-good' : 'score-average'; ?>">
                                                    <?php echo $quiz->avg_score ? number_format($quiz->avg_score, 1) . '%' : 'N/A'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="quiz-completions"><?php echo isset($quiz->completions) ? intval($quiz->completions) : (isset($quiz->total_completions) ? intval($quiz->total_completions) : 0); ?></span>
                                            </td>
                                            <td>
                                                <span class="quiz-date"><?php echo date_i18n('d/m/Y', strtotime($quiz->created_at)); ?></span>
                                            </td>
                                            <td>
                                                <div class="quiz-actions">
                                                    <button class="kata-btn-icon" onclick="viewQuizDetails(<?php echo $quiz->id; ?>)" title="<?php _e('Xem chi tiết', 'kata-seo-manager'); ?>">
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                        </svg>
                                                    </button>
                                                    <button class="kata-btn-icon" onclick="exportQuizData(<?php echo $quiz->id; ?>)" title="<?php _e('Xuất dữ liệu', 'kata-seo-manager'); ?>">
                                                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="kata-card">
                <div class="kata-card-header">
                    <h3><?php _e('Hoạt động gần đây', 'kata-seo-manager'); ?></h3>
                </div>
                <div class="kata-card-content">
                    <?php if (empty($recent_attempts)): ?>
                        <div class="kata-empty-state-small">
                            <p><?php _e('Chưa có hoạt động nào được ghi nhận.', 'kata-seo-manager'); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="kata-activity-list">
                            <?php foreach ($recent_attempts as $attempt): ?>
                                <div class="kata-activity-item">
                                    <div class="activity-icon">
                                        <?php if ($attempt->score >= 80): ?>🏆<?php elseif ($attempt->score >= 60): ?>⭐<?php else: ?>📝<?php endif; ?>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">
                                            <?php if ($attempt->display_name): ?>
                                                <strong><?php echo esc_html($attempt->display_name); ?></strong>
                                            <?php else: ?>
                                                <strong><?php _e('Khách', 'kata-seo-manager'); ?></strong>
                                            <?php endif; ?>
                                            <?php _e('đã hoàn thành', 'kata-seo-manager'); ?>
                                            <em><?php echo esc_html($attempt->quiz_title); ?></em>
                                        </div>
                                        <div class="activity-meta">
                                            <?php _e('Điểm:', 'kata-seo-manager'); ?> <span class="score-highlight"><?php echo number_format($attempt->score, 1); ?>%</span>
                                            | <?php _e('Thời gian:', 'kata-seo-manager'); ?> <?php echo gmdate('i:s', $attempt->time_taken); ?>
                                            | <?php echo human_time_diff(strtotime($attempt->created_at), current_time('timestamp')); ?> <?php _e('trước', 'kata-seo-manager'); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Quiz Analytics Styles */
.kata-quiz-analytics {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Filters */
.kata-analytics-filters {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.kata-filter-form {
    display: flex;
    align-items: end;
    gap: 20px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-group label {
    font-size: 13px;
    font-weight: 600;
    color: #1d2327;
}

.kata-date-input,
.kata-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    min-width: 140px;
    transition: border-color 0.2s ease;
}

.kata-date-input:focus,
.kata-select:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
}

.filter-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.kata-analytics-container {
    max-width: 1200px;
    margin: 0 auto;
}

.kata-stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kata-stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s ease;
}

.kata-stat-card:hover {
    transform: translateY(-2px);
}

.kata-stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.total-quizzes .kata-stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
.total-attempts .kata-stat-icon { background: linear-gradient(135deg, #f093fb, #f5576c); }
.avg-score .kata-stat-icon { background: linear-gradient(135deg, #4facfe, #00f2fe); }
.completion-rate .kata-stat-icon { background: linear-gradient(135deg, #43e97b, #38f9d7); }

.kata-stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #1d2327;
    line-height: 1;
}

.kata-stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 4px;
}

.kata-quiz-table table {
    border-radius: 8px;
    overflow: hidden;
}

.quiz-status {
    font-size: 12px;
    margin-top: 4px;
}

.status-active { color: #28a745; }
.status-inactive { color: #dc3545; }

.score-good { color: #28a745; font-weight: 600; }
.score-average { color: #ffc107; font-weight: 600; }

.quiz-actions {
    display: flex;
    gap: 8px;
}

.kata-btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    background: #f8f9fa;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.kata-btn-icon:hover {
    background: #e9ecef;
    transform: scale(1.05);
}

.kata-activity-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.kata-activity-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.activity-icon {
    font-size: 20px;
    width: 32px;
    text-align: center;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-size: 14px;
    margin-bottom: 4px;
}

.activity-meta {
    font-size: 12px;
    color: #666;
}

.score-highlight {
    font-weight: 600;
    color: #667eea;
}

.kata-empty-state {
    text-align: center;
    padding: 60px 20px;
}

.kata-empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.kata-empty-state h4 {
    margin-bottom: 8px;
    color: #1d2327;
}

.kata-empty-state p {
    color: #666;
    margin-bottom: 24px;
}

/* Advanced Analytics Styles */
.kata-analytics-advanced {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.kata-chart-card {
    grid-column: span 3;
}

.kata-chart-controls {
    display: flex;
    gap: 10px;
    align-items: center;
}

.kata-select-small {
    padding: 6px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    background: white;
}

.kata-top-quizzes {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.kata-top-quiz-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.kata-top-quiz-item:hover {
    background: #e9ecef;
}

.quiz-rank {
    font-size: 18px;
    font-weight: bold;
    width: 30px;
    text-align: center;
}

.quiz-info {
    flex: 1;
}

.quiz-name {
    font-weight: 600;
    font-size: 14px;
    color: #1d2327;
    margin-bottom: 4px;
}

.quiz-stats {
    font-size: 12px;
    color: #666;
}

.quiz-score-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 600;
}

.kata-engagement-metrics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.engagement-metric {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.metric-icon {
    font-size: 24px;
    width: 40px;
    text-align: center;
}

.metric-content {
    flex: 1;
}

.metric-value {
    font-size: 20px;
    font-weight: bold;
    color: #1d2327;
    line-height: 1;
}

.metric-label {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}

.kata-card-actions {
    display: flex;
    gap: 10px;
    align-items: center;
}

.kata-btn {
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    border: none;
}

.kata-btn-primary {
    background: #667eea;
    color: white;
}

.kata-btn-primary:hover {
    background: #5a6fd8;
    color: white;
}

.kata-btn-outline {
    background: white;
    color: #667eea;
    border: 1px solid #667eea;
}

.kata-btn-outline:hover {
    background: #667eea;
    color: white;
}

.kata-search-input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    min-width: 200px;
}

/* Chart Styles */
#trendsChart {
    max-width: 100%;
    height: auto;
}

@media (max-width: 1200px) {
    .kata-analytics-advanced {
        grid-template-columns: 1fr;
    }
    
    .kata-chart-card {
        grid-column: span 1;
    }
}

@media (max-width: 768px) {
    .kata-stats-overview {
        grid-template-columns: 1fr;
    }
    
    .kata-analytics-advanced {
        grid-template-columns: 1fr;
    }
    
    .kata-engagement-metrics {
        grid-template-columns: 1fr;
    }
    
    .kata-quiz-table {
        overflow-x: auto;
    }
    
    .quiz-actions {
        flex-direction: column;
    }
    
    .kata-card-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .kata-search-input {
        min-width: auto;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Chart Data
const trendsData = <?php echo json_encode($trends_data); ?>;

// Initialize Charts
let trendsChart;

jQuery(document).ready(function($) {
    initializeTrendsChart();
    
    // Chart type selector
    $('#chart-type').on('change', function() {
        updateTrendsChart($(this).val());
    });
    
    // Search functionality
    $('.kata-search-input').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.kata-quiz-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Real-time updates every 5 minutes
    setInterval(function() {
        refreshAnalytics();
    }, 300000);
});

function initializeTrendsChart() {
    const ctx = document.getElementById('trendsChart').getContext('2d');
    
    const labels = trendsData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
    }).reverse();
    
    const attemptsData = trendsData.map(item => parseInt(item.attempts)).reverse();
    const scoresData = trendsData.map(item => parseFloat(item.avg_score) || 0).reverse();
    const passesData = trendsData.map(item => parseInt(item.passes)).reverse();
    
    trendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Lượt thử',
                data: attemptsData,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: '#667eea',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        color: '#666'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666'
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 8
                }
            }
        }
    });
}

function updateTrendsChart(type) {
    if (!trendsChart) return;
    
    const labels = trendsData.map(item => {
        const date = new Date(item.date);
        return date.toLocaleDateString('vi-VN', { month: 'short', day: 'numeric' });
    }).reverse();
    
    let data, label, color;
    
    switch(type) {
        case 'attempts':
            data = trendsData.map(item => parseInt(item.attempts)).reverse();
            label = 'Lượt thử';
            color = '#667eea';
            break;
        case 'scores':
            data = trendsData.map(item => parseFloat(item.avg_score) || 0).reverse();
            label = 'Điểm số trung bình';
            color = '#4facfe';
            break;
        case 'passes':
            data = trendsData.map(item => parseInt(item.passes)).reverse();
            label = 'Lượt pass';
            color = '#43e97b';
            break;
    }
    
    trendsChart.data.datasets[0] = {
        label: label,
        data: data,
        borderColor: color,
        backgroundColor: color + '20',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: color,
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 6
    };
    
    trendsChart.update();
}

function viewQuizDetails(quizId) {
    // Create detailed modal/popup
    const modal = `
        <div id="quiz-detail-modal" class="kata-modal">
            <div class="kata-modal-content">
                <div class="kata-modal-header">
                    <h3>Chi tiết Quiz #${quizId}</h3>
                    <button class="kata-modal-close" onclick="closeQuizModal()">&times;</button>
                </div>
                <div class="kata-modal-body">
                    <div class="loading-spinner">Đang tải dữ liệu...</div>
                </div>
            </div>
        </div>
    `;
    
    $('body').append(modal);
    $('#quiz-detail-modal').fadeIn();
    
    // Load detailed data via AJAX
    $.post(ajaxurl, {
        action: 'kata_get_quiz_details',
        quiz_id: quizId,
        nonce: '<?php echo wp_create_nonce("kata_quiz_nonce"); ?>'
    }, function(response) {
        if (response.success) {
            $('.kata-modal-body').html(response.data.html);
        } else {
            $('.kata-modal-body').html('<p>Không thể tải dữ liệu chi tiết.</p>');
        }
    });
}

function closeQuizModal() {
    $('#quiz-detail-modal').fadeOut(function() {
        $(this).remove();
    });
}

function exportQuizData(quizId = null) {
    const exportButton = $('[onclick*="exportQuizData"]');
    const originalText = exportButton.html();
    
    exportButton.html('<span class="spinner"></span> Đang xuất...');
    exportButton.prop('disabled', true);
    
    const data = {
        action: 'kata_export_quiz_data',
        quiz_id: quizId,
        start_date: '<?php echo $start_date; ?>',
        end_date: '<?php echo $end_date; ?>',
        nonce: '<?php echo wp_create_nonce("kata_export_nonce"); ?>'
    };
    
    $.post(ajaxurl, data, function(response) {
        if (response.success) {
            // Create download link
            const blob = new Blob([response.data.csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = response.data.filename;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            showNotification('Xuất dữ liệu thành công!', 'success');
        } else {
            showNotification('Lỗi xuất dữ liệu: ' + response.data, 'error');
        }
    }).always(function() {
        exportButton.html(originalText);
        exportButton.prop('disabled', false);
    });
}

function refreshAnalytics() {
    location.reload();
}

function showNotification(message, type = 'info') {
    const notification = `
        <div class="kata-notification kata-notification-${type}">
            <div class="kata-notification-content">
                <span class="kata-notification-icon">
                    ${type === 'success' ? '✅' : type === 'error' ? '❌' : 'ℹ️'}
                </span>
                <span class="kata-notification-message">${message}</span>
            </div>
        </div>
    `;
    
    $('body').append(notification);
    
    const $notification = $('.kata-notification').last();
    $notification.fadeIn().delay(3000).fadeOut(function() {
        $(this).remove();
    });
}

// Modal styles
const modalStyles = `
<style>
.kata-modal {
    display: none;
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
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 800px;
    max-height: 80vh;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.kata-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
}

.kata-modal-header h3 {
    margin: 0;
    color: #1d2327;
}

.kata-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.kata-modal-close:hover {
    background: #e9ecef;
    color: #333;
}

.kata-modal-body {
    padding: 24px;
    max-height: calc(80vh - 80px);
    overflow-y: auto;
}

.kata-notification {
    position: fixed;
    top: 32px;
    right: 20px;
    z-index: 100001;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 16px 20px;
    min-width: 300px;
    border-left: 4px solid;
}

.kata-notification-success {
    border-left-color: #28a745;
}

.kata-notification-error {
    border-left-color: #dc3545;
}

.kata-notification-info {
    border-left-color: #17a2b8;
}

.kata-notification-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.kata-notification-icon {
    font-size: 18px;
}

.kata-notification-message {
    font-weight: 500;
    color: #1d2327;
}

.loading-spinner {
    text-align: center;
    padding: 40px;
    color: #666;
}

.spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
`;

$('head').append(modalStyles);

</script>