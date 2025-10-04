<?php
/**
 * Quiz Manager Class
 * 
 * Handles quiz creation, management, and analytics
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Quiz_Manager {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // AJAX handlers
        add_action('wp_ajax_kata_quiz_submit', array($this, 'handle_quiz_submission'));
        add_action('wp_ajax_nopriv_kata_quiz_submit', array($this, 'handle_quiz_submission'));
        add_action('wp_ajax_kata_quiz_analytics', array($this, 'get_quiz_analytics'));
        add_action('wp_ajax_kata_quiz_track_view', array($this, 'track_quiz_view'));
        add_action('wp_ajax_nopriv_kata_quiz_track_view', array($this, 'track_quiz_view'));
        
        // Advanced Analytics AJAX handlers
        add_action('wp_ajax_kata_get_quiz_details', array($this, 'get_quiz_details'));
        add_action('wp_ajax_kata_export_quiz_data', array($this, 'export_quiz_data'));
        
        // Shortcode handlers
        add_shortcode('kata_quiz', array($this, 'render_quiz_shortcode'));
        add_shortcode('kata_quiz_question', array($this, 'render_quiz_question_shortcode'));
        add_shortcode('kata_quiz_option', array($this, 'render_quiz_option_shortcode'));
        
        // Frontend scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
    }
    
    /**
     * Enqueue frontend scripts
     */
    public function enqueue_frontend_scripts() {
        if ($this->has_quiz_shortcode()) {
            wp_enqueue_style('kata-quiz-frontend', KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/quiz-frontend.css', array(), KATA_SEO_MANAGER_VERSION);
            wp_enqueue_script('kata-quiz-frontend', KATA_SEO_MANAGER_PLUGIN_URL . 'assets/js/quiz-frontend.js', array('jquery'), KATA_SEO_MANAGER_VERSION, true);
            
            wp_localize_script('kata-quiz-frontend', 'kataQuiz', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kata_quiz_nonce'),
                'strings' => array(
                    'loading' => __('Đang xử lý...', 'kata-seo-manager'),
                    'error' => __('Có lỗi xảy ra', 'kata-seo-manager'),
                    'success' => __('Cảm ơn bạn đã tham gia!', 'kata-seo-manager'),
                    'submit' => __('Nộp bài', 'kata-seo-manager'),
                    'next' => __('Câu tiếp theo', 'kata-seo-manager'),
                    'prev' => __('Câu trước', 'kata-seo-manager'),
                    'finish' => __('Hoàn thành', 'kata-seo-manager'),
                    'restart' => __('Làm lại', 'kata-seo-manager'),
                    'share' => __('Chia sẻ kết quả', 'kata-seo-manager')
                )
            ));
        }
    }
    
    /**
     * Check if current page has quiz shortcode
     */
    private function has_quiz_shortcode() {
        global $post;
        return (is_object($post) && has_shortcode($post->post_content, 'kata_quiz'));
    }
    
    /**
     * Render quiz shortcode
     */
    public function render_quiz_shortcode($atts, $content = '') {
        $atts = shortcode_atts(array(
            'title' => 'Quiz Tương Tác',
            'id' => '',
            'timer' => '0',
            'show_results' => 'true',
            'randomize' => 'false',
            'allow_retake' => 'true',
            'pass_score' => '70'
        ), $atts);
        
        // Generate unique quiz ID if not provided
        if (empty($atts['id'])) {
            $atts['id'] = 'quiz_' . uniqid();
        }
        
        // Set global flag for parsing
        global $kata_quiz_parsing;
        $kata_quiz_parsing = true;
        
        // Parse quiz content
        $quiz_data = $this->parse_quiz_content($content);
        
        // Clear global flag
        $kata_quiz_parsing = false;
        
        // Save quiz to database
        $quiz_id = $this->save_quiz_to_database($atts, $quiz_data);
        
        // Track quiz view
        $this->track_quiz_view_internal($quiz_id);
        
        // Generate quiz HTML
        return $this->generate_quiz_html($quiz_id, $atts, $quiz_data);
    }
    
    /**
     * Parse quiz content to extract questions and options
     */
    private function parse_quiz_content($content) {
        $questions = array();
        
        // Match quiz questions
        preg_match_all('/\[kata_quiz_question[^\]]*question="([^"]*)"[^\]]*correct="(\d+)"[^\]]*\](.*?)\[\/kata_quiz_question\]/s', $content, $matches);
        
        if (!empty($matches[0])) {
            for ($i = 0; $i < count($matches[0]); $i++) {
                $question_text = $matches[1][$i];
                $correct_answer = intval($matches[2][$i]);
                $options_content = $matches[3][$i];
                
                // Parse options - handle nested shortcodes
                $options_content = do_shortcode($options_content);
                preg_match_all('/\[kata_quiz_option\](.*?)\[\/kata_quiz_option\]/s', $options_content, $option_matches);
                
                $questions[] = array(
                    'question' => $question_text,
                    'options' => $option_matches[1],
                    'correct' => $correct_answer,
                    'explanation' => $this->extract_explanation($options_content)
                );
            }
        }
        
        return $questions;
    }
    
    /**
     * Extract explanation from question content
     */
    private function extract_explanation($content) {
        preg_match('/\[kata_quiz_explanation\](.*?)\[\/kata_quiz_explanation\]/', $content, $matches);
        return isset($matches[1]) ? $matches[1] : '';
    }
    
    /**
     * Save quiz to database
     */
    private function save_quiz_to_database($atts, $quiz_data) {
        global $wpdb, $post;
        
        $table_name = $wpdb->prefix . 'kata_seo_quizzes';
        
        $quiz_config = array(
            'timer' => intval($atts['timer']),
            'show_results' => $atts['show_results'] === 'true',
            'randomize' => $atts['randomize'] === 'true',
            'allow_retake' => $atts['allow_retake'] === 'true',
            'pass_score' => floatval($atts['pass_score'])
        );
        
        // Check if quiz already exists
        $existing_quiz = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table_name WHERE post_id = %d AND quiz_title = %s",
            $post->ID,
            $atts['title']
        ));
        
        $quiz_data_json = json_encode($quiz_data);
        $quiz_config_json = json_encode($quiz_config);
        
        if ($existing_quiz) {
            // Update existing quiz
            $wpdb->update(
                $table_name,
                array(
                    'quiz_data' => $quiz_data_json,
                    'quiz_config' => $quiz_config_json,
                    'total_questions' => count($quiz_data),
                    'updated_at' => current_time('mysql')
                ),
                array('id' => $existing_quiz->id),
                array('%s', '%s', '%d', '%s'),
                array('%d')
            );
            return $existing_quiz->id;
        } else {
            // Insert new quiz
            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post->ID,
                    'quiz_title' => $atts['title'],
                    'quiz_data' => $quiz_data_json,
                    'quiz_config' => $quiz_config_json,
                    'total_questions' => count($quiz_data)
                ),
                array('%d', '%s', '%s', '%s', '%d')
            );
            return $wpdb->insert_id;
        }
    }
    
    /**
     * Generate quiz HTML
     */
    private function generate_quiz_html($quiz_id, $atts, $quiz_data) {
        // If no quiz data, return empty
        if (empty($quiz_data)) {
            return '<div class="kata-quiz-error">Không có dữ liệu quiz để hiển thị.</div>';
        }
        
        ob_start();
        ?>
        <div id="kata-quiz-<?php echo esc_attr($quiz_id); ?>" class="kata-quiz-container" 
             data-quiz-id="<?php echo esc_attr($quiz_id); ?>"
             data-timer="<?php echo esc_attr($atts['timer']); ?>"
             data-total-questions="<?php echo count($quiz_data); ?>"
             data-pass-score="<?php echo esc_attr($atts['pass_score']); ?>"
             data-allow-retake="<?php echo esc_attr($atts['allow_retake']); ?>">
            
            <div class="kata-quiz-header">
                <h3 class="kata-quiz-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php if ($atts['timer'] > 0): ?>
                    <div class="kata-quiz-timer" data-timer="<?php echo esc_attr($atts['timer']); ?>">
                        <span class="timer-icon">⏱️</span>
                        <span class="timer-text">Thời gian: <span class="timer-count"><?php echo gmdate('i:s', $atts['timer']); ?></span></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="kata-quiz-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo count($quiz_data) > 0 ? (100 / count($quiz_data)) : 0; ?>%"></div>
                </div>
                <span class="progress-text">Câu hỏi <span class="current-question">1</span> / <span class="total-questions"><?php echo count($quiz_data); ?></span></span>
            </div>
            
            <form class="kata-quiz-form" data-current-question="0">
                <div class="kata-quiz-questions">
                    <?php foreach ($quiz_data as $index => $question): ?>
                        <div class="kata-quiz-question" 
                             data-question="<?php echo $index; ?>" 
                             data-correct="<?php echo esc_attr($question['correct']); ?>"
                             style="<?php echo $index > 0 ? 'display: none;' : ''; ?>">
                            <h4 class="question-text">
                                <span class="question-number"><?php echo ($index + 1); ?>.</span>
                                <?php echo esc_html($question['question']); ?>
                            </h4>
                            <div class="question-options">
                                <?php foreach ($question['options'] as $option_index => $option): ?>
                                    <label class="option-label">
                                        <input type="radio" 
                                               name="question_<?php echo $index; ?>" 
                                               value="<?php echo $option_index; ?>" 
                                               data-question="<?php echo $index; ?>" />
                                        <span class="option-indicator"></span>
                                        <span class="option-text"><?php echo esc_html($option); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?php if (!empty($question['explanation'])): ?>
                                <div class="question-explanation" style="display: none;">
                                    <div class="explanation-content">
                                        <strong>💡 Giải thích:</strong> <?php echo wp_kses_post($question['explanation']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="kata-quiz-controls">
                    <button type="button" class="btn-quiz btn-prev" style="display: none;" disabled>
                        ⬅️ Câu trước
                    </button>
                    <button type="button" class="btn-quiz btn-next" <?php echo count($quiz_data) <= 1 ? 'style="display: none;"' : ''; ?>>
                        Câu tiếp theo ➡️
                    </button>
                    <button type="button" class="btn-quiz btn-submit" style="display: none;">
                        🎯 Hoàn thành Quiz
                    </button>
                </div>
            </form>
            
            <div class="kata-quiz-results" style="display: none;">
                <div class="results-header">
                    <h4>🎉 Kết quả Quiz của bạn</h4>
                </div>
                <div class="results-content">
                    <div class="score-display">
                        <div class="score-circle">
                            <span class="score-percentage">0%</span>
                            <span class="score-label">Điểm số</span>
                        </div>
                        <div class="score-details">
                            <p class="score-text">
                                Bạn đã trả lời đúng <strong><span class="correct-count">0</span></strong> / <strong><span class="total-count"><?php echo count($quiz_data); ?></span></strong> câu hỏi
                            </p>
                            <p class="score-message"></p>
                        </div>
                    </div>
                </div>
                <div class="results-actions">
                    <?php if ($atts['allow_retake'] === 'true'): ?>
                        <button type="button" class="btn-quiz btn-restart">🔄 Làm lại</button>
                    <?php endif; ?>
                    <button type="button" class="btn-quiz btn-share">📤 Chia sẻ kết quả</button>
                </div>
            </div>
            
            <div class="kata-quiz-loading" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Đang xử lý...</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Handle quiz submission
     */
    public function handle_quiz_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_quiz_nonce')) {
            wp_send_json_error(array('message' => 'Xác thực không hợp lệ'));
        }
        
        $quiz_id = intval($_POST['quiz_id']);
        $answers = json_decode(stripslashes($_POST['answers']), true);
        $time_taken = intval($_POST['time_taken']) ?? 0;
        
        // Get quiz data
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_seo_quizzes';
        $quiz = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $quiz_id));
        
        if (!$quiz) {
            wp_send_json_error(array('message' => 'Không tìm thấy quiz'));
        }
        
        $quiz_data = json_decode($quiz->quiz_data, true);
        
        // Calculate score
        $correct_answers = 0;
        $total_questions = count($quiz_data);
        $detailed_results = array();
        
        foreach ($answers as $question_index => $user_answer) {
            $correct_answer = intval($quiz_data[$question_index]['correct']);
            $is_correct = (intval($user_answer) === $correct_answer);
            
            if ($is_correct) {
                $correct_answers++;
            }
            
            $detailed_results[] = array(
                'question' => $quiz_data[$question_index]['question'],
                'user_answer' => intval($user_answer),
                'correct_answer' => $correct_answer,
                'is_correct' => $is_correct,
                'explanation' => $quiz_data[$question_index]['explanation'] ?? ''
            );
        }
        
        $score_percentage = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100, 2) : 0;
        $pass_score = intval($quiz->pass_score ?? 70);
        $passed = $score_percentage >= $pass_score;
        
        // Save attempt to database
        $attempts_table = $wpdb->prefix . 'kata_seo_quiz_attempts';
        $attempt_data = array(
            'quiz_id' => $quiz_id,
            'user_id' => get_current_user_id(),
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'score' => $score_percentage,
            'correct_answers' => $correct_answers,
            'total_questions' => $total_questions,
            'time_taken' => $time_taken,
            'answers' => json_encode($answers),
            'passed' => $passed ? 1 : 0,
            'attempt_date' => current_time('mysql')
        );
        
        $wpdb->insert($attempts_table, $attempt_data);
        
        // Update analytics
        $this->update_quiz_analytics_submission($quiz_id, $score_percentage, $passed);
        
        // Prepare response
        $response = array(
            'success' => true,
            'score' => $score_percentage,
            'correct_answers' => $correct_answers,
            'total_questions' => $total_questions,
            'passed' => $passed,
            'pass_score' => $pass_score,
            'time_taken' => $time_taken,
            'detailed_results' => $detailed_results,
            'message' => $this->get_score_message($score_percentage, $passed)
        );
        
        wp_send_json_success($response);
    }
    
    /**
     * Get score message based on performance
     */
    private function get_score_message($score, $passed) {
        if ($score >= 90) {
            return '🌟 Xuất sắc! Bạn đã làm rất tốt!';
        } elseif ($score >= 80) {
            return '👍 Tốt lắm! Bạn đã nắm vững kiến thức!';
        } elseif ($score >= 70) {
            return '✅ Khá tốt! Bạn đã vượt qua quiz!';
        } elseif ($score >= 60) {
            return '😊 Không tệ! Hãy cố gắng thêm nhé!';
        } else {
            return '💪 Đừng nản lòng! Hãy thử lại để cải thiện kết quả!';
        }
    }
    
    /**
     * Update quiz analytics after submission - overloaded method
     */
    private function update_quiz_analytics_submission($quiz_id, $score, $passed) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_seo_quiz_analytics';
        $today = current_time('Y-m-d');
        
        // Get existing record for today
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE quiz_id = %d AND date = %s",
            $quiz_id, $today
        ));
        
        if ($existing) {
            // Update existing record
            $wpdb->update(
                $table_name,
                array(
                    'total_submissions' => $existing->total_submissions + 1,
                    'average_score' => (($existing->average_score * $existing->total_submissions) + $score) / ($existing->total_submissions + 1),
                    'pass_rate' => ($passed) ? 
                        (($existing->pass_rate * $existing->total_submissions) + 100) / ($existing->total_submissions + 1) :
                        (($existing->pass_rate * $existing->total_submissions)) / ($existing->total_submissions + 1),
                    'updated_at' => current_time('mysql')
                ),
                array('quiz_id' => $quiz_id, 'date' => $today),
                array('%d', '%f', '%f', '%s'),
                array('%d', '%s')
            );
        } else {
            // Create new record
            $wpdb->insert(
                $table_name,
                array(
                    'quiz_id' => $quiz_id,
                    'date' => $today,
                    'total_views' => 0,
                    'total_submissions' => 1,
                    'average_score' => $score,
                    'pass_rate' => $passed ? 100 : 0,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql')
                ),
                array('%d', '%s', '%d', '%d', '%f', '%f', '%s', '%s')
            );
        }
    }
    
    /**
     * Save quiz attempt to database
     */
    private function save_quiz_attempt($quiz_id, $answers, $score, $total_questions, $correct_answers, $time_taken, $completion_rate) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_seo_quiz_attempts';
        
        $wpdb->insert(
            $table_name,
            array(
                'quiz_id' => $quiz_id,
                'user_id' => get_current_user_id() ?: null,
                'user_ip' => $this->get_user_ip(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'answers' => json_encode($answers),
                'score' => $score,
                'total_questions' => $total_questions,
                'correct_answers' => $correct_answers,
                'time_taken' => $time_taken,
                'completion_rate' => $completion_rate
            ),
            array('%d', '%d', '%s', '%s', '%s', '%f', '%d', '%d', '%d', '%f')
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Track quiz view
     */
    public function track_quiz_view() {
        if (!wp_verify_nonce($_POST['nonce'], 'kata_quiz_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce'));
        }
        
        $quiz_id = intval($_POST['quiz_id']);
        $this->track_quiz_view_internal($quiz_id);
        
        wp_send_json_success();
    }
    
    /**
     * Track quiz view internal
     */
    private function track_quiz_view_internal($quiz_id) {
        $this->update_quiz_analytics($quiz_id, 'view');
    }
    
    /**
     * Update quiz analytics
     */
    private function update_quiz_analytics($quiz_id, $action = 'view') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kata_seo_quiz_analytics';
        $today = current_time('Y-m-d');
        
        // Get existing record for today
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE quiz_id = %d AND date = %s",
            $quiz_id, $today
        ));
        
        if ($existing) {
            // Update existing record
            $update_data = array();
            
            if ($action === 'view') {
                $update_data['total_views'] = $existing->total_views + 1;
            } elseif ($action === 'attempt') {
                $update_data['total_attempts'] = $existing->total_attempts + 1;
            } elseif ($action === 'completion') {
                $update_data['total_completions'] = $existing->total_completions + 1;
            }
            
            if (!empty($update_data)) {
                $wpdb->update($table_name, $update_data, array('id' => $existing->id));
            }
        } else {
            // Insert new record
            $insert_data = array(
                'quiz_id' => $quiz_id,
                'date' => $today,
                'total_views' => $action === 'view' ? 1 : 0,
                'total_attempts' => $action === 'attempt' ? 1 : 0,
                'total_completions' => $action === 'completion' ? 1 : 0
            );
            
            $wpdb->insert($table_name, $insert_data);
        }
    }
    
    /**
     * Get quiz analytics
     */
    public function get_quiz_analytics() {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }
        
        $quiz_id = intval($_GET['quiz_id']);
        $days = intval($_GET['days']) ?: 30;
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_seo_quiz_analytics';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE quiz_id = %d AND date >= DATE_SUB(CURDATE(), INTERVAL %d DAY) ORDER BY date ASC",
            $quiz_id, $days
        ));
        
        wp_send_json_success($results);
    }
    
    /**
     * Get user IP address
     */
    private function get_user_ip() {
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Render quiz question shortcode (for backward compatibility)
     */
    public function render_quiz_question_shortcode($atts, $content = '') {
        // This is handled within the main quiz shortcode
        return '';
    }
    
    /**
     * Render quiz option shortcode
     */
    public function render_quiz_option_shortcode($atts, $content = '') {
        // Check if we're inside a quiz shortcode by checking global
        global $kata_quiz_parsing;
        
        // When used outside quiz context, return formatted option
        if (empty($kata_quiz_parsing)) {
            return '<div class="kata-quiz-standalone-option">' . wp_kses_post($content) . '</div>';
        }
        // Within quiz context, return content for parsing
        return $content;
    }
    
    /**
     * Get detailed quiz information for analytics
     */
    public function get_quiz_details() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_quiz_nonce')) {
            wp_send_json_error('Nonce verification failed');
            return;
        }
        
        $quiz_id = intval($_POST['quiz_id']);
        if (!$quiz_id) {
            wp_send_json_error('Invalid quiz ID');
            return;
        }
        
        global $wpdb;
        $table_quizzes = $wpdb->prefix . 'kata_seo_quizzes';
        $table_attempts = $wpdb->prefix . 'kata_seo_quiz_attempts';
        $table_sessions = $wpdb->prefix . 'kata_seo_quiz_sessions';
        $table_question_analytics = $wpdb->prefix . 'kata_seo_quiz_question_analytics';
        
        // Get quiz details
        $quiz = $wpdb->get_row($wpdb->prepare("
            SELECT q.*, p.post_title, p.post_status
            FROM {$table_quizzes} q
            LEFT JOIN {$wpdb->posts} p ON q.post_id = p.ID
            WHERE q.id = %d
        ", $quiz_id));
        
        if (!$quiz) {
            wp_send_json_error('Quiz not found');
            return;
        }
        
        // Get comprehensive stats
        $stats = $wpdb->get_row($wpdb->prepare("
            SELECT 
                COUNT(DISTINCT a.id) as total_attempts,
                COUNT(DISTINCT s.id) as total_sessions,
                AVG(a.score) as avg_score,
                MIN(a.score) as min_score,
                MAX(a.score) as max_score,
                COUNT(DISTINCT CASE WHEN a.score >= 70 THEN a.id END) as total_passes,
                COUNT(DISTINCT a.user_id) as unique_users,
                AVG(a.time_taken) as avg_time_taken,
                MIN(a.time_taken) as min_time_taken,
                MAX(a.time_taken) as max_time_taken
            FROM {$table_quizzes} q
            LEFT JOIN {$table_attempts} a ON q.id = a.quiz_id
            LEFT JOIN {$table_sessions} s ON q.id = s.quiz_id
            WHERE q.id = %d
        ", $quiz_id));
        
        // Get recent attempts
        $recent_attempts = $wpdb->get_results($wpdb->prepare("
            SELECT a.*, u.display_name
            FROM {$table_attempts} a
            LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
            WHERE a.quiz_id = %d
            ORDER BY a.created_at DESC
            LIMIT 10
        ", $quiz_id));
        
        // Get question performance
        $question_stats = $wpdb->get_results($wpdb->prepare("
            SELECT 
                question_index,
                correct_answers,
                total_answers,
                (correct_answers * 100.0 / NULLIF(total_answers, 0)) as success_rate
            FROM {$table_question_analytics}
            WHERE quiz_id = %d
            ORDER BY question_index
        ", $quiz_id));
        
        // Generate HTML response
        ob_start();
        ?>
        <div class="kata-quiz-details">
            <div class="quiz-overview">
                <h4><?php echo esc_html($quiz->quiz_title); ?></h4>
                <div class="quiz-meta">
                    <p><strong>Bài viết:</strong> <?php echo $quiz->post_title ? esc_html($quiz->post_title) : 'N/A'; ?></p>
                    <p><strong>Tổng câu hỏi:</strong> <?php echo intval($quiz->total_questions); ?></p>
                    <p><strong>Trạng thái:</strong> <?php echo $quiz->is_active ? 'Hoạt động' : 'Tạm dừng'; ?></p>
                    <p><strong>Ngày tạo:</strong> <?php echo date_i18n('d/m/Y H:i', strtotime($quiz->created_at)); ?></p>
                </div>
            </div>
            
            <div class="quiz-statistics">
                <h5>Thống kê tổng quan</h5>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($stats->total_attempts ?? 0); ?></div>
                        <div class="stat-label">Tổng lượt thử</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($stats->unique_users ?? 0); ?></div>
                        <div class="stat-label">Người dùng duy nhất</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo number_format($stats->avg_score ?? 0, 1); ?>%</div>
                        <div class="stat-label">Điểm trung bình</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo gmdate('i:s', intval($stats->avg_time_taken ?? 0)); ?></div>
                        <div class="stat-label">Thời gian TB</div>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($question_stats)): ?>
            <div class="question-performance">
                <h5>Hiệu suất từng câu hỏi</h5>
                <div class="question-list">
                    <?php foreach ($question_stats as $q_stat): ?>
                    <div class="question-item">
                        <div class="question-number">Câu <?php echo intval($q_stat->question_index) + 1; ?></div>
                        <div class="question-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo number_format($q_stat->success_rate, 1); ?>%"></div>
                            </div>
                            <div class="progress-text"><?php echo number_format($q_stat->success_rate, 1); ?>% đúng</div>
                        </div>
                        <div class="question-count"><?php echo intval($q_stat->total_answers); ?> lượt trả lời</div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($recent_attempts)): ?>
            <div class="recent-attempts">
                <h5>Lượt thử gần đây</h5>
                <div class="attempts-table">
                    <table class="wp-list-table widefat">
                        <thead>
                            <tr>
                                <th>Người dùng</th>
                                <th>Điểm</th>
                                <th>Thời gian</th>
                                <th>Ngày thử</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_attempts as $attempt): ?>
                            <tr>
                                <td><?php echo $attempt->display_name ? esc_html($attempt->display_name) : 'Khách'; ?></td>
                                <td><span class="score-badge score-<?php echo $attempt->score >= 70 ? 'pass' : 'fail'; ?>">
                                    <?php echo number_format($attempt->score, 1); ?>%
                                </span></td>
                                <td><?php echo gmdate('i:s', intval($attempt->time_taken)); ?></td>
                                <td><?php echo date_i18n('d/m/Y H:i', strtotime($attempt->created_at)); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <style>
        .kata-quiz-details {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .quiz-overview h4 {
            margin: 0 0 16px 0;
            color: #1d2327;
        }
        .quiz-meta p {
            margin: 8px 0;
            font-size: 14px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 16px;
            margin: 16px 0;
        }
        .stat-item {
            text-align: center;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 4px;
        }
        .question-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 16px;
        }
        .question-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 14px;
        }
        .question-number {
            font-weight: 600;
            min-width: 60px;
        }
        .question-progress {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .progress-bar {
            flex: 1;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #43e97b, #38f9d7);
            transition: width 0.3s ease;
        }
        .progress-text {
            font-size: 12px;
            font-weight: 600;
            color: #28a745;
            min-width: 60px;
        }
        .question-count {
            font-size: 12px;
            color: #666;
            min-width: 80px;
            text-align: right;
        }
        .attempts-table {
            margin-top: 16px;
        }
        .attempts-table table {
            border-radius: 8px;
            overflow: hidden;
        }
        .score-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .score-pass {
            background: #d4edda;
            color: #155724;
        }
        .score-fail {
            background: #f8d7da;
            color: #721c24;
        }
        </style>
        <?php
        $html = ob_get_clean();
        
        wp_send_json_success(array('html' => $html));
    }
    
    /**
     * Export quiz data to CSV
     */
    public function export_quiz_data() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'kata_export_nonce')) {
            wp_send_json_error('Nonce verification failed');
            return;
        }
        
        // Check user permissions
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
            return;
        }
        
        $quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        
        global $wpdb;
        $table_quizzes = $wpdb->prefix . 'kata_seo_quizzes';
        $table_attempts = $wpdb->prefix . 'kata_seo_quiz_attempts';
        
        // Build query based on filters
        $where_clause = "WHERE a.created_at BETWEEN %s AND %s";
        $params = array($start_date . ' 00:00:00', $end_date . ' 23:59:59');
        
        if ($quiz_id) {
            $where_clause .= " AND q.id = %d";
            $params[] = $quiz_id;
        }
        
        // Get export data
        $export_data = $wpdb->get_results($wpdb->prepare("
            SELECT 
                q.quiz_title as 'Tên Quiz',
                p.post_title as 'Bài viết',
                COALESCE(u.display_name, 'Khách') as 'Người dùng',
                a.score as 'Điểm số',
                a.time_taken as 'Thời gian (giây)',
                CASE WHEN a.score >= 70 THEN 'Pass' ELSE 'Fail' END as 'Kết quả',
                a.user_answers as 'Câu trả lời',
                a.ip_address as 'IP Address',
                a.user_agent as 'User Agent',
                a.created_at as 'Ngày thử'
            FROM {$table_attempts} a
            LEFT JOIN {$table_quizzes} q ON a.quiz_id = q.id
            LEFT JOIN {$wpdb->posts} p ON q.post_id = p.ID
            LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
            $where_clause
            ORDER BY a.created_at DESC
        ", $params), ARRAY_A);
        
        if (empty($export_data)) {
            wp_send_json_error('No data to export');
            return;
        }
        
        // Generate CSV content
        $csv_content = '';
        
        // Add BOM for UTF-8
        $csv_content .= "\xEF\xBB\xBF";
        
        // Add headers
        $headers = array_keys($export_data[0]);
        $csv_content .= '"' . implode('","', $headers) . '"' . "\n";
        
        // Add data rows
        foreach ($export_data as $row) {
            $csv_row = array();
            foreach ($row as $value) {
                // Clean and escape the value
                $clean_value = str_replace('"', '""', $value);
                $csv_row[] = $clean_value;
            }
            $csv_content .= '"' . implode('","', $csv_row) . '"' . "\n";
        }
        
        // Generate filename
        $filename = 'kata-quiz-export-' . date('Y-m-d-H-i-s') . '.csv';
        if ($quiz_id) {
            $filename = 'kata-quiz-' . $quiz_id . '-export-' . date('Y-m-d-H-i-s') . '.csv';
        }
        
        wp_send_json_success(array(
            'csv' => $csv_content,
            'filename' => $filename
        ));
    }
}

// Initialize the quiz manager
new KATA_SEO_Quiz_Manager();