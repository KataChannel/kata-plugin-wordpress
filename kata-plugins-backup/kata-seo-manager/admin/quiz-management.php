<?php
/**
 * Quiz Management Admin Page
 * 
 * @package KATA_SEO_Manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submissions
if (isset($_POST['action']) && wp_verify_nonce($_POST['_wpnonce'], 'kata_quiz_management')) {
    switch ($_POST['action']) {
        case 'create_quiz':
            $result = handle_create_quiz($_POST);
            break;
        case 'update_quiz':
            $result = handle_update_quiz($_POST);
            break;
        case 'delete_quiz':
            $result = handle_delete_quiz($_POST);
            break;
        case 'toggle_status':
            $result = handle_toggle_status($_POST);
            break;
    }
}

// Get current action
$current_action = isset($_GET['action']) ? sanitize_key($_GET['action']) : '';
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Get quiz data if editing
$quiz_data = null;
if ($current_action === 'edit' && $quiz_id) {
    global $wpdb;
    $quiz_data = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}kata_seo_quizzes WHERE id = %d",
        $quiz_id
    ));
}

/**
 * Handle create quiz
 */
function handle_create_quiz($data) {
    global $wpdb;
    
    $quiz_title = sanitize_text_field($data['quiz_title']);
    $post_id = intval($data['post_id']) ?: null;
    
    // Parse questions data
    $questions = array();
    if (isset($data['questions']) && is_array($data['questions'])) {
        foreach ($data['questions'] as $index => $question) {
            if (!empty($question['question'])) {
                $questions[] = array(
                    'id' => $index + 1,
                    'question' => sanitize_text_field($question['question']),
                    'options' => array_map('sanitize_text_field', $question['options']),
                    'correct_answer' => intval($question['correct_answer']),
                    'explanation' => sanitize_textarea_field($question['explanation'])
                );
            }
        }
    }
    
    $quiz_config = array(
        'timer' => intval($data['timer']) ?: 0,
        'pass_score' => intval($data['pass_score']) ?: 70,
        'show_results' => isset($data['show_results']),
        'allow_retake' => false, // Disabled due to IP limiting
        'randomize' => isset($data['randomize'])
    );
    
    $result = $wpdb->insert(
        $wpdb->prefix . 'kata_seo_quizzes',
        array(
            'post_id' => $post_id,
            'quiz_title' => $quiz_title,
            'quiz_data' => json_encode(array('questions' => $questions)),
            'quiz_config' => json_encode($quiz_config),
            'total_questions' => count($questions),
            'is_active' => 1,
            'created_at' => current_time('mysql')
        ),
        array('%d', '%s', '%s', '%s', '%d', '%d', '%s')
    );
    
    if ($result !== false) {
        $message = 'Quiz created successfully! Quiz ID: ' . $wpdb->insert_id;
        $message_type = 'success';
    } else {
        $message = 'Error creating quiz: ' . $wpdb->last_error;
        $message_type = 'error';
    }
    
    return array('message' => $message, 'type' => $message_type);
}

/**
 * Handle update quiz
 */
function handle_update_quiz($data) {
    global $wpdb;
    
    $quiz_id = intval($data['quiz_id']);
    $quiz_title = sanitize_text_field($data['quiz_title']);
    $post_id = intval($data['post_id']) ?: null;
    
    // Parse questions data
    $questions = array();
    if (isset($data['questions']) && is_array($data['questions'])) {
        foreach ($data['questions'] as $index => $question) {
            if (!empty($question['question'])) {
                $questions[] = array(
                    'id' => $index + 1,
                    'question' => sanitize_text_field($question['question']),
                    'options' => array_map('sanitize_text_field', $question['options']),
                    'correct_answer' => intval($question['correct_answer']),
                    'explanation' => sanitize_textarea_field($question['explanation'])
                );
            }
        }
    }
    
    $quiz_config = array(
        'timer' => intval($data['timer']) ?: 0,
        'pass_score' => intval($data['pass_score']) ?: 70,
        'show_results' => isset($data['show_results']),
        'allow_retake' => false, // Disabled due to IP limiting
        'randomize' => isset($data['randomize'])
    );
    
    $result = $wpdb->update(
        $wpdb->prefix . 'kata_seo_quizzes',
        array(
            'post_id' => $post_id,
            'quiz_title' => $quiz_title,
            'quiz_data' => json_encode(array('questions' => $questions)),
            'quiz_config' => json_encode($quiz_config),
            'total_questions' => count($questions),
            'updated_at' => current_time('mysql')
        ),
        array('id' => $quiz_id),
        array('%d', '%s', '%s', '%s', '%d', '%s'),
        array('%d')
    );
    
    if ($result !== false) {
        $message = 'Quiz updated successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error updating quiz: ' . $wpdb->last_error;
        $message_type = 'error';
    }
    
    return array('message' => $message, 'type' => $message_type);
}

/**
 * Handle delete quiz
 */
function handle_delete_quiz($data) {
    global $wpdb;
    
    $quiz_id = intval($data['quiz_id']);
    
    // Delete related attempts first
    $wpdb->delete($wpdb->prefix . 'kata_seo_quiz_attempts', array('quiz_id' => $quiz_id));
    
    // Delete quiz
    $result = $wpdb->delete($wpdb->prefix . 'kata_seo_quizzes', array('id' => $quiz_id));
    
    if ($result !== false) {
        $message = 'Quiz deleted successfully!';
        $message_type = 'success';
    } else {
        $message = 'Error deleting quiz: ' . $wpdb->last_error;
        $message_type = 'error';
    }
    
    return array('message' => $message, 'type' => $message_type);
}

/**
 * Handle toggle status
 */
function handle_toggle_status($data) {
    global $wpdb;
    
    $quiz_id = intval($data['quiz_id']);
    $new_status = intval($data['new_status']);
    
    $result = $wpdb->update(
        $wpdb->prefix . 'kata_seo_quizzes',
        array('is_active' => $new_status),
        array('id' => $quiz_id),
        array('%d'),
        array('%d')
    );
    
    if ($result !== false) {
        $status_text = $new_status ? 'activated' : 'deactivated';
        $message = "Quiz {$status_text} successfully!";
        $message_type = 'success';
    } else {
        $message = 'Error updating quiz status: ' . $wpdb->last_error;
        $message_type = 'error';
    }
    
    return array('message' => $message, 'type' => $message_type);
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-clipboard" style="font-size: 24px; margin-right: 10px;"></span>
        <?php _e('Quiz Management', 'kata-seo-manager'); ?>
    </h1>
    
    <?php if ($current_action !== 'create' && $current_action !== 'edit'): ?>
    <a href="<?php echo admin_url('admin.php?page=kata-seo-quiz-management&action=create'); ?>" class="page-title-action">
        <?php _e('Thêm Quiz Mới', 'kata-seo-manager'); ?>
    </a>
    <?php endif; ?>
    
    <hr class="wp-header-end">
    
    <?php
    // Show messages
    if (isset($result)) {
        $class = $result['type'] === 'success' ? 'notice-success' : 'notice-error';
        echo '<div class="notice ' . $class . ' is-dismissible"><p>' . esc_html($result['message']) . '</p></div>';
    }
    ?>
    
    <?php if ($current_action === 'create' || $current_action === 'edit'): ?>
        <!-- Quiz Form -->
        <div class="kata-quiz-form-container">
            <h2><?php echo $current_action === 'edit' ? __('Chỉnh sửa Quiz', 'kata-seo-manager') : __('Tạo Quiz Mới', 'kata-seo-manager'); ?></h2>
            
            <form method="post" action="" id="quiz-form">
                <?php wp_nonce_field('kata_quiz_management'); ?>
                <input type="hidden" name="action" value="<?php echo $current_action === 'edit' ? 'update_quiz' : 'create_quiz'; ?>">
                <?php if ($current_action === 'edit'): ?>
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                <?php endif; ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="quiz_title"><?php _e('Tiêu đề Quiz', 'kata-seo-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="quiz_title" name="quiz_title" 
                                   value="<?php echo $quiz_data ? esc_attr($quiz_data->quiz_title) : ''; ?>" 
                                   class="regular-text" required>
                            <p class="description"><?php _e('Nhập tiêu đề quiz của bạn', 'kata-seo-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="post_id"><?php _e('Liên kết với Post/Page', 'kata-seo-manager'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="post_id" name="post_id" 
                                   value="<?php echo $quiz_data ? esc_attr($quiz_data->post_id) : ''; ?>" 
                                   class="small-text">
                            <p class="description"><?php _e('ID của post/page để liên kết (tùy chọn)', 'kata-seo-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="timer"><?php _e('Thời gian (giây)', 'kata-seo-manager'); ?></label>
                        </th>
                        <td>
                            <?php 
                            $config = $quiz_data ? json_decode($quiz_data->quiz_config, true) : array();
                            $timer = isset($config['timer']) ? $config['timer'] : 0;
                            ?>
                            <input type="number" id="timer" name="timer" value="<?php echo $timer; ?>" class="small-text" min="0">
                            <p class="description"><?php _e('0 = không giới hạn thời gian', 'kata-seo-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="pass_score"><?php _e('Điểm đỗ (%)', 'kata-seo-manager'); ?></label>
                        </th>
                        <td>
                            <?php $pass_score = isset($config['pass_score']) ? $config['pass_score'] : 70; ?>
                            <input type="number" id="pass_score" name="pass_score" value="<?php echo $pass_score; ?>" 
                                   class="small-text" min="0" max="100">
                            <p class="description"><?php _e('Điểm tối thiểu để đỗ quiz', 'kata-seo-manager'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Tùy chọn', 'kata-seo-manager'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="show_results" <?php checked(isset($config['show_results']) ? $config['show_results'] : true); ?>>
                                    <?php _e('Hiển thị kết quả sau khi hoàn thành', 'kata-seo-manager'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="randomize" <?php checked(isset($config['randomize']) ? $config['randomize'] : false); ?>>
                                    <?php _e('Xáo trộn thứ tự câu hỏi', 'kata-seo-manager'); ?>
                                </label><br>
                                
                                <p class="description">
                                    <strong><?php _e('Lưu ý:', 'kata-seo-manager'); ?></strong> 
                                    <?php _e('Tính năng làm lại quiz đã bị tắt do có giới hạn IP (3 lần/24h)', 'kata-seo-manager'); ?>
                                </p>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                
                <h3><?php _e('Câu hỏi', 'kata-seo-manager'); ?></h3>
                
                <div id="questions-container">
                    <?php
                    $questions = array();
                    if ($quiz_data && $quiz_data->quiz_data) {
                        $quiz_parsed = json_decode($quiz_data->quiz_data, true);
                        $questions = isset($quiz_parsed['questions']) ? $quiz_parsed['questions'] : array();
                    }
                    
                    if (empty($questions)) {
                        $questions = array(array('question' => '', 'options' => array('', ''), 'correct_answer' => 0, 'explanation' => ''));
                    }
                    
                    foreach ($questions as $index => $question):
                    ?>
                    <div class="question-item" data-index="<?php echo $index; ?>">
                        <h4><?php printf(__('Câu hỏi %d', 'kata-seo-manager'), $index + 1); ?></h4>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Câu hỏi', 'kata-seo-manager'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="questions[<?php echo $index; ?>][question]" 
                                           value="<?php echo esc_attr($question['question']); ?>" 
                                           class="large-text" required>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Đáp án', 'kata-seo-manager'); ?></label>
                                </th>
                                <td>
                                    <div class="options-container">
                                        <?php
                                        $options = isset($question['options']) ? $question['options'] : array('', '');
                                        foreach ($options as $opt_index => $option):
                                        ?>
                                        <div class="option-item">
                                            <label>
                                                <input type="radio" name="questions[<?php echo $index; ?>][correct_answer]" 
                                                       value="<?php echo $opt_index; ?>" 
                                                       <?php checked($question['correct_answer'], $opt_index); ?>>
                                                <input type="text" name="questions[<?php echo $index; ?>][options][]" 
                                                       value="<?php echo esc_attr($option); ?>" 
                                                       placeholder="<?php printf(__('Đáp án %s', 'kata-seo-manager'), chr(65 + $opt_index)); ?>" 
                                                       class="regular-text" required>
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <button type="button" class="button add-option" data-question="<?php echo $index; ?>">
                                            <?php _e('Thêm đáp án', 'kata-seo-manager'); ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Giải thích', 'kata-seo-manager'); ?></label>
                                </th>
                                <td>
                                    <textarea name="questions[<?php echo $index; ?>][explanation]" 
                                              class="large-text" rows="3"><?php echo esc_textarea($question['explanation']); ?></textarea>
                                    <p class="description"><?php _e('Giải thích tại sao đáp án này đúng', 'kata-seo-manager'); ?></p>
                                </td>
                            </tr>
                        </table>
                        
                        <button type="button" class="button button-secondary remove-question">
                            <?php _e('Xóa câu hỏi', 'kata-seo-manager'); ?>
                        </button>
                        
                        <hr>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <p>
                    <button type="button" id="add-question" class="button button-secondary">
                        <?php _e('Thêm câu hỏi', 'kata-seo-manager'); ?>
                    </button>
                </p>
                
                <p class="submit">
                    <input type="submit" name="submit" class="button-primary" 
                           value="<?php echo $current_action === 'edit' ? __('Cập nhật Quiz', 'kata-seo-manager') : __('Tạo Quiz', 'kata-seo-manager'); ?>">
                    <a href="<?php echo admin_url('admin.php?page=kata-seo-quiz-management'); ?>" class="button">
                        <?php _e('Hủy', 'kata-seo-manager'); ?>
                    </a>
                </p>
            </form>
        </div>
        
        <style>
        .kata-quiz-form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        
        .question-item {
            background: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid #2271b1;
        }
        
        .question-item h4 {
            margin-top: 0;
            color: #2271b1;
        }
        
        .option-item {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .option-item label {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        
        .options-container {
            margin-bottom: 10px;
        }
        
        .add-option, .remove-question {
            margin-top: 10px;
        }
        
        .remove-question {
            background: #d63638;
            color: white;
            border-color: #d63638;
        }
        
        .remove-question:hover {
            background: #b32d2e;
            border-color: #b32d2e;
        }
        </style>
        
        <script>
        jQuery(document).ready(function($) {
            let questionIndex = <?php echo count($questions); ?>;
            
            // Add new question
            $('#add-question').on('click', function() {
                const questionHtml = `
                    <div class="question-item" data-index="${questionIndex}">
                        <h4><?php _e('Câu hỏi', 'kata-seo-manager'); ?> ${questionIndex + 1}</h4>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Câu hỏi', 'kata-seo-manager'); ?></label>
                                </th>
                                <td>
                                    <input type="text" name="questions[${questionIndex}][question]" 
                                           value="" class="large-text" required>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Đáp án', 'kata-seo-manager'); ?></label>
                                </th>
                                <td>
                                    <div class="options-container">
                                        <div class="option-item">
                                            <label>
                                                <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                                                       value="0" checked>
                                                <input type="text" name="questions[${questionIndex}][options][]" 
                                                       value="" placeholder="<?php _e('Đáp án A', 'kata-seo-manager'); ?>" 
                                                       class="regular-text" required>
                                            </label>
                                        </div>
                                        <div class="option-item">
                                            <label>
                                                <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                                                       value="1">
                                                <input type="text" name="questions[${questionIndex}][options][]" 
                                                       value="" placeholder="<?php _e('Đáp án B', 'kata-seo-manager'); ?>" 
                                                       class="regular-text" required>
                                            </label>
                                        </div>
                                        
                                        <button type="button" class="button add-option" data-question="${questionIndex}">
                                            <?php _e('Thêm đáp án', 'kata-seo-manager'); ?>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Giải thích', 'kata-seo-manager'); ?></label>
                                </th>
                                <td>
                                    <textarea name="questions[${questionIndex}][explanation]" 
                                              class="large-text" rows="3"></textarea>
                                    <p class="description"><?php _e('Giải thích tại sao đáp án này đúng', 'kata-seo-manager'); ?></p>
                                </td>
                            </tr>
                        </table>
                        
                        <button type="button" class="button button-secondary remove-question">
                            <?php _e('Xóa câu hỏi', 'kata-seo-manager'); ?>
                        </button>
                        
                        <hr>
                    </div>
                `;
                
                $('#questions-container').append(questionHtml);
                questionIndex++;
                updateQuestionNumbers();
            });
            
            // Remove question
            $(document).on('click', '.remove-question', function() {
                if ($('.question-item').length > 1) {
                    $(this).closest('.question-item').remove();
                    updateQuestionNumbers();
                } else {
                    alert('<?php _e('Phải có ít nhất 1 câu hỏi', 'kata-seo-manager'); ?>');
                }
            });
            
            // Add option
            $(document).on('click', '.add-option', function() {
                const questionIndex = $(this).data('question');
                const optionsContainer = $(this).closest('.options-container');
                const currentOptions = optionsContainer.find('.option-item').length;
                const optionLetter = String.fromCharCode(65 + currentOptions);
                
                const optionHtml = `
                    <div class="option-item">
                        <label>
                            <input type="radio" name="questions[${questionIndex}][correct_answer]" 
                                   value="${currentOptions}">
                            <input type="text" name="questions[${questionIndex}][options][]" 
                                   value="" placeholder="<?php _e('Đáp án', 'kata-seo-manager'); ?> ${optionLetter}" 
                                   class="regular-text" required>
                        </label>
                    </div>
                `;
                
                $(this).before(optionHtml);
            });
            
            function updateQuestionNumbers() {
                $('.question-item').each(function(index) {
                    $(this).find('h4').text('<?php _e('Câu hỏi', 'kata-seo-manager'); ?> ' + (index + 1));
                });
            }
        });
        </script>
        
    <?php else: ?>
        <!-- Quiz List -->
        <div class="kata-quiz-list">
            <?php
            global $wpdb;
            $quizzes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_seo_quizzes ORDER BY created_at DESC");
            ?>
            
            <?php if (empty($quizzes)): ?>
                <div class="notice notice-info">
                    <p><?php _e('Chưa có quiz nào. Hãy tạo quiz đầu tiên!', 'kata-seo-manager'); ?></p>
                </div>
            <?php else: ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col" class="manage-column column-title column-primary">
                                <?php _e('Tiêu đề', 'kata-seo-manager'); ?>
                            </th>
                            <th scope="col" class="manage-column"><?php _e('ID', 'kata-seo-manager'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Số câu hỏi', 'kata-seo-manager'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Shortcode', 'kata-seo-manager'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Trạng thái', 'kata-seo-manager'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Ngày tạo', 'kata-seo-manager'); ?></th>
                            <th scope="col" class="manage-column"><?php _e('Thao tác', 'kata-seo-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $quiz): ?>
                        <tr>
                            <td class="title column-title column-primary" data-colname="<?php _e('Tiêu đề', 'kata-seo-manager'); ?>">
                                <strong>
                                    <a href="<?php echo admin_url('admin.php?page=kata-seo-quiz-management&action=edit&quiz_id=' . $quiz->id); ?>">
                                        <?php echo esc_html($quiz->quiz_title); ?>
                                    </a>
                                </strong>
                                <div class="row-actions">
                                    <span class="edit">
                                        <a href="<?php echo admin_url('admin.php?page=kata-seo-quiz-management&action=edit&quiz_id=' . $quiz->id); ?>">
                                            <?php _e('Chỉnh sửa', 'kata-seo-manager'); ?>
                                        </a> |
                                    </span>
                                    <span class="trash">
                                        <a href="#" onclick="deleteQuiz(<?php echo $quiz->id; ?>)" class="submitdelete">
                                            <?php _e('Xóa', 'kata-seo-manager'); ?>
                                        </a>
                                    </span>
                                </div>
                            </td>
                            <td data-colname="<?php _e('ID', 'kata-seo-manager'); ?>">
                                <code>#<?php echo $quiz->id; ?></code>
                            </td>
                            <td data-colname="<?php _e('Số câu hỏi', 'kata-seo-manager'); ?>">
                                <?php echo $quiz->total_questions; ?>
                            </td>
                            <td data-colname="<?php _e('Shortcode', 'kata-seo-manager'); ?>">
                                <code onclick="copyToClipboard(this)">[kata_quiz id="<?php echo $quiz->id; ?>"]</code>
                                <span class="copy-notice" style="display:none; color: green;">✓ Copied!</span>
                            </td>
                            <td data-colname="<?php _e('Trạng thái', 'kata-seo-manager'); ?>">
                                <span class="status-badge <?php echo $quiz->is_active ? 'active' : 'inactive'; ?>">
                                    <?php echo $quiz->is_active ? __('Hoạt động', 'kata-seo-manager') : __('Tạm dừng', 'kata-seo-manager'); ?>
                                </span>
                            </td>
                            <td data-colname="<?php _e('Ngày tạo', 'kata-seo-manager'); ?>">
                                <?php echo date_i18n('d/m/Y H:i', strtotime($quiz->created_at)); ?>
                            </td>
                            <td data-colname="<?php _e('Thao tác', 'kata-seo-manager'); ?>">
                                <a href="<?php echo admin_url('admin.php?page=kata-seo-quiz-analytics&quiz_id=' . $quiz->id); ?>" 
                                   class="button button-small">
                                    <?php _e('Xem thống kê', 'kata-seo-manager'); ?>
                                </a>
                                
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('kata_quiz_management'); ?>
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>">
                                    <input type="hidden" name="new_status" value="<?php echo $quiz->is_active ? 0 : 1; ?>">
                                    <input type="submit" class="button button-small" 
                                           value="<?php echo $quiz->is_active ? __('Tạm dừng', 'kata-seo-manager') : __('Kích hoạt', 'kata-seo-manager'); ?>">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <style>
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .kata-quiz-list code {
            background: #f1f1f1;
            padding: 4px 8px;
            border-radius: 3px;
            cursor: pointer;
            user-select: all;
        }
        
        .kata-quiz-list code:hover {
            background: #e1e1e1;
        }
        </style>
        
        <script>
        function copyToClipboard(element) {
            const text = element.textContent;
            navigator.clipboard.writeText(text).then(function() {
                const notice = element.nextElementSibling;
                notice.style.display = 'inline';
                setTimeout(() => {
                    notice.style.display = 'none';
                }, 2000);
            });
        }
        
        function deleteQuiz(quizId) {
            if (confirm('<?php _e('Bạn có chắc muốn xóa quiz này? Thao tác này không thể hoàn tác.', 'kata-seo-manager'); ?>')) {
                const form = document.createElement('form');
                form.method = 'post';
                form.innerHTML = `
                    <?php echo wp_nonce_field('kata_quiz_management', '_wpnonce', true, false); ?>
                    <input type="hidden" name="action" value="delete_quiz">
                    <input type="hidden" name="quiz_id" value="${quizId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        </script>
        
    <?php endif; ?>
</div>