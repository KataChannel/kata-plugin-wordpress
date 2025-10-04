<?php
/**
 * Quiz Handler
 * 
 * Handles quiz gamification with scoring and analytics
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_Quiz_Handler {
    
    /**
     * Render quiz shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'id' => 'quiz-' . uniqid(),
            'title' => 'Quiz',
            'questions' => '',
            'style' => 'default',
            'show_results' => 'yes',
            'pass_score' => 70
        ), $atts);
        
        // Try to load quiz data from multiple sources
        $questions = $this->get_quiz_data($atts);
        
        if (!$questions || !isset($questions['questions']) || empty($questions['questions'])) {
            return $this->render_error_message($atts);
        }
        
        $post_id = get_the_ID();
        
        ob_start();
        ?>
        <div class="kata-quiz kata-quiz-<?php echo esc_attr($atts['style']); ?>" 
             data-quiz-id="<?php echo esc_attr($atts['id']); ?>"
             data-post-id="<?php echo esc_attr($post_id); ?>"
             data-pass-score="<?php echo esc_attr($atts['pass_score']); ?>">\n            <div class="quiz-header">
                <h3 class="quiz-title"><?php echo esc_html($atts['title']); ?></h3>
                <div class="quiz-info">
                    <span class="quiz-question-count">
                        <?php echo count($questions['questions']); ?> câu hỏi
                    </span>
                    <span class="quiz-time" data-time="<?php echo esc_attr($questions['time_limit'] ?? 0); ?>">
                        <?php if (isset($questions['time_limit']) && $questions['time_limit'] > 0) : ?>
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                <path d="M12,20A7,7 0 0,1 5,13A7,7 0 0,1 12,6A7,7 0 0,1 19,13A7,7 0 0,1 12,20M12,4A9,9 0 0,0 3,13A9,9 0 0,0 12,22A9,9 0 0,0 21,13A9,9 0 0,0 12,4M12.5,8H11V14L15.75,16.85L16.5,15.62L12.5,13.25V8M7.88,3.39L6.6,1.86L2,5.71L3.29,7.24L7.88,3.39M22,5.72L17.4,1.86L16.11,3.39L20.71,7.25L22,5.72Z"/>
                            </svg>
                            <?php echo $questions['time_limit']; ?> phút
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="quiz-questions">
                <?php foreach ($questions['questions'] as $index => $question) : ?>
                    <div class="quiz-question" data-question-index="<?php echo $index; ?>">
                        <div class="question-number">Câu <?php echo $index + 1; ?></div>
                        <div class="question-text"><?php echo esc_html($question['question']); ?></div>
                        
                        <?php if (!empty($question['image'])) : ?>
                            <div class="question-image">
                                <img src="<?php echo esc_url($question['image']); ?>" 
                                     alt="Question <?php echo $index + 1; ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="quiz-options">
                            <?php foreach ($question['options'] as $opt_index => $option) : ?>
                                <label class="quiz-option">
                                    <input type="radio" 
                                           name="question_<?php echo $index; ?>" 
                                           value="<?php echo $opt_index; ?>"
                                           data-correct="<?php echo ($opt_index === $question['correct']) ? '1' : '0'; ?>">
                                    <span class="option-text"><?php echo esc_html($option); ?></span>
                                    <span class="option-indicator"></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (!empty($question['explanation'])) : ?>
                            <div class="question-explanation" style="display:none;">
                                <strong>Giải thích:</strong> <?php echo esc_html($question['explanation']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="quiz-actions">
                <button type="button" class="quiz-submit-btn">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/>
                    </svg>
                    Nộp bài
                </button>
                <button type="button" class="quiz-reset-btn" style="display:none;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                        <path d="M12,4C14.1,4 16.1,4.8 17.6,6.3C20.7,9.4 20.7,14.5 17.6,17.6C15.8,19.5 13.3,20.2 10.9,19.9L11.4,17.9C13.1,18.1 14.9,17.5 16.2,16.2C18.5,13.9 18.5,10.1 16.2,7.7C15.1,6.6 13.5,6 12,6V10.6L7,5.6L12,0.6V4M6.3,17.6C3.7,15 3.3,11 5.1,7.9L6.6,9.4C5.5,11.6 5.9,14.4 7.8,16.2C8.3,16.7 8.9,17.1 9.6,17.4L9,19.4C8,19 7.1,18.4 6.3,17.6Z"/>
                    </svg>
                    Làm lại
                </button>
            </div>
            
            <div class="quiz-result" style="display:none;">
                <div class="result-icon"></div>
                <div class="quiz-score"></div>
                <div class="quiz-message"></div>
                
                <?php if ($atts['show_results'] === 'yes') : ?>
                    <div class="quiz-answers-review">
                        <h4>Xem lại đáp án</h4>
                        <div class="answers-list"></div>
                    </div>
                <?php endif; ?>
                
                <div class="quiz-share-result">
                    <p>Chia sẻ kết quả của bạn:</p>
                    <div class="kata-social-share-inline"></div>
                </div>
            </div>
            
            <div class="quiz-progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
                <div class="progress-text">0%</div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Submit quiz and calculate score
     * 
     * @param array $data Quiz submission data
     * @return array Results
     */
    public function submit_quiz($data) {
        global $wpdb;
        
        $quiz_id = sanitize_text_field($data['quiz_id']);
        $post_id = intval($data['post_id']);
        $answers = json_decode(stripslashes($data['answers']), true);
        
        if (!$answers || !is_array($answers)) {
            return array('error' => 'Invalid answers data');
        }
        
        $score = 0;
        $total = count($answers);
        $correct_answers = array();
        
        foreach ($answers as $answer) {
            if (isset($answer['is_correct']) && $answer['is_correct']) {
                $score++;
                $correct_answers[] = $answer['question_index'];
            }
        }
        
        $percentage = round(($score / $total) * 100, 2);
        
        // Save to database
        $table = $wpdb->prefix . 'kata_seo_quiz_results';
        $wpdb->insert($table, array(
            'post_id' => $post_id,
            'quiz_id' => $quiz_id,
            'score' => $score,
            'total_questions' => $total,
            'answers' => wp_json_encode($answers),
            'ip_address' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'user_id' => get_current_user_id() ?: null,
            'completed_at' => current_time('mysql')
        ), array('%d', '%s', '%d', '%d', '%s', '%s', '%s', '%d', '%s'));
        
        return array(
            'score' => $score,
            'total' => $total,
            'percentage' => $percentage,
            'message' => $this->get_score_message($percentage),
            'passed' => $percentage >= floatval($data['pass_score'] ?? 70),
            'correct_answers' => $correct_answers
        );
    }
    
    /**
     * Get quiz statistics
     * 
     * @param string $quiz_id Quiz ID
     * @return array Statistics
     */
    public function get_quiz_stats($quiz_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_quiz_results';
        
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                COUNT(*) as total_attempts,
                AVG(score) as avg_score,
                AVG(score / total_questions * 100) as avg_percentage,
                MAX(score) as highest_score,
                MIN(score) as lowest_score
            FROM $table 
            WHERE quiz_id = %s",
            $quiz_id
        ), ARRAY_A);
        
        return $stats;
    }
    
    /**
     * Get quiz data from multiple sources
     * 
     * @param array $atts Shortcode attributes
     * @return array|false Quiz data or false
     */
    private function get_quiz_data($atts) {
        // Method 1: Simple numeric mode - generate sample quiz
        if (isset($atts['questions']) && is_numeric($atts['questions'])) {
            return $this->get_sample_quiz_data($atts);
        }
        
        // Method 2: Base64 encoded JSON (advanced mode)
        if (!empty($atts['questions']) && strpos($atts['questions'], '{') === false) {
            $decoded = json_decode(base64_decode($atts['questions']), true);
            if ($decoded && isset($decoded['questions'])) {
                return $decoded;
            }
        }
        
        // Method 3: Load from database by quiz ID
        if (!empty($atts['id']) && strpos($atts['id'], 'quiz-') !== 0) {
            global $wpdb;
            $table = $wpdb->prefix . 'kata_seo_quiz_data';
            $quiz_data = $wpdb->get_var($wpdb->prepare(
                "SELECT quiz_data FROM $table WHERE quiz_id = %s LIMIT 1",
                $atts['id']
            ));
            
            if ($quiz_data) {
                $decoded = json_decode($quiz_data, true);
                if ($decoded && isset($decoded['questions'])) {
                    return $decoded;
                }
            }
        }
        
        // Method 4: Load from post meta
        $post_id = get_the_ID();
        if ($post_id) {
            $quiz_meta = get_post_meta($post_id, '_kata_quiz_data', true);
            if ($quiz_meta && is_array($quiz_meta) && isset($quiz_meta['questions'])) {
                return $quiz_meta;
            }
        }
        
        // Fallback: Generate default sample quiz
        return $this->get_sample_quiz_data($atts);
    }
    
    /**
     * Generate sample quiz data for demo
     * 
     * @param array $atts Shortcode attributes  
     * @return array Sample quiz data
     */
    private function get_sample_quiz_data($atts) {
        $num_questions = isset($atts['questions']) && is_numeric($atts['questions']) 
            ? intval($atts['questions']) 
            : 3;
        
        // Sample SEO quiz questions
        $sample_questions = array(
            array(
                'question' => 'SEO là viết tắt của từ gì?',
                'options' => array(
                    'Search Engine Optimization',
                    'Social Engine Optimization',
                    'Site Engine Optimization',
                    'System Engine Optimization'
                ),
                'correct' => 0,
                'explanation' => 'SEO là Search Engine Optimization - Tối ưu hóa công cụ tìm kiếm'
            ),
            array(
                'question' => 'Yếu tố nào quan trọng nhất cho On-Page SEO?',
                'options' => array(
                    'Backlinks',
                    'Content chất lượng',
                    'Social signals',
                    'Domain age'
                ),
                'correct' => 1,
                'explanation' => 'Content chất lượng là yếu tố quan trọng nhất cho On-Page SEO'
            ),
            array(
                'question' => 'Meta description có ảnh hưởng trực tiếp đến ranking không?',
                'options' => array(
                    'Có, rất quan trọng',
                    'Không, chỉ ảnh hưởng CTR',
                    'Có, nhưng ít',
                    'Không biết'
                ),
                'correct' => 1,
                'explanation' => 'Meta description không ảnh hưởng trực tiếp đến ranking nhưng cải thiện CTR'
            ),
            array(
                'question' => 'Title tag nên dài bao nhiêu ký tự?',
                'options' => array(
                    '30-40 ký tự',
                    '50-60 ký tự',
                    '70-80 ký tự',
                    '100+ ký tự'
                ),
                'correct' => 1,
                'explanation' => 'Title tag lý tưởng nên dài 50-60 ký tự để hiển thị đầy đủ trên Google'
            ),
            array(
                'question' => 'Backlink là gì?',
                'options' => array(
                    'Link nội bộ trong website',
                    'Link từ website khác trỏ về website của bạn',
                    'Link trong footer',
                    'Link trong menu'
                ),
                'correct' => 1,
                'explanation' => 'Backlink là link từ website khác trỏ về website của bạn, rất quan trọng cho SEO'
            ),
            array(
                'question' => 'Canonical tag dùng để làm gì?',
                'options' => array(
                    'Tăng ranking',
                    'Ngăn duplicate content',
                    'Tăng tốc trang',
                    'Tạo sitemap'
                ),
                'correct' => 1,
                'explanation' => 'Canonical tag giúp ngăn chặn vấn đề duplicate content'
            ),
            array(
                'question' => 'Alt text của hình ảnh quan trọng vì lý do gì?',
                'options' => array(
                    'Giúp hình ảnh load nhanh hơn',
                    'Giúp Google hiểu nội dung hình ảnh',
                    'Tăng kích thước hình',
                    'Không quan trọng'
                ),
                'correct' => 1,
                'explanation' => 'Alt text giúp Google hiểu nội dung hình ảnh và tối ưu cho SEO hình ảnh'
            ),
            array(
                'question' => 'Schema markup giúp gì cho SEO?',
                'options' => array(
                    'Tăng tốc website',
                    'Giúp Google hiểu nội dung tốt hơn',
                    'Giảm bounce rate',
                    'Tăng backlinks'
                ),
                'correct' => 1,
                'explanation' => 'Schema markup giúp Google hiểu cấu trúc và nội dung website tốt hơn'
            )
        );
        
        // Select random questions
        shuffle($sample_questions);
        $selected = array_slice($sample_questions, 0, min($num_questions, count($sample_questions)));
        
        return array(
            'questions' => $selected,
            'time_limit' => 0
        );
    }
    
    /**
     * Render error message with helpful instructions
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML error message
     */
    private function render_error_message($atts) {
        ob_start();
        ?>
        <div class="kata-quiz-error" style="padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 4px; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #856404;">⚠️ Quiz chưa có dữ liệu</h4>
            <p><strong>Cách sử dụng quiz:</strong></p>
            <ol>
                <li><strong>Option 1:</strong> Để hiển thị sample quiz, chỉ cần dùng:
                    <code>[kata_quiz title="Tên quiz" questions="5"]</code>
                </li>
                <li><strong>Option 2:</strong> Tạo quiz trong Dashboard > KATA SEO > Quizzes, sau đó dùng:
                    <code>[kata_quiz id="your-quiz-id"]</code>
                </li>
                <li><strong>Option 3:</strong> Thêm quiz data vào post meta <code>_kata_quiz_data</code></li>
            </ol>
            <p style="margin-bottom: 0;"><em>Quiz hiện tại: <?php echo esc_html($atts['title']); ?></em></p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get score message based on percentage
     * 
     * @param float $percentage Score percentage
     * @return string Message
     */
    private function get_score_message($percentage) {
        if ($percentage >= 90) {
            return '🏆 Xuất sắc! Bạn là chuyên gia thực thụ!';
        } elseif ($percentage >= 80) {
            return '🌟 Rất tốt! Bạn có kiến thức vững vàng!';
        } elseif ($percentage >= 70) {
            return '👍 Tốt! Bạn đã nắm được phần lớn kiến thức!';
        } elseif ($percentage >= 60) {
            return '😊 Khá! Còn một chút nữa thôi!';
        } elseif ($percentage >= 50) {
            return '📚 Cần cố gắng thêm! Hãy ôn lại nhé!';
        } else {
            return '💪 Hãy học thêm! Bạn làm được mà!';
        }
    }
    
    /**
     * Get client IP address
     * 
     * @return string IP address
     */
    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field($ip);
    }
}
