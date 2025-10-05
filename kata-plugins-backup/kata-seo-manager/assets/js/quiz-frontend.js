/**
 * KATA Quiz Frontend JavaScript
 * Enhanced for better user experience
 * 
 * @package KATA_SEO_Manager
 */

(function($) {
    'use strict';
    
    class KataQuiz {
        constructor(container) {
            this.container = $(container);
            this.quizId = this.container.data('quiz-id');
            this.totalQuestions = parseInt(this.container.data('total-questions'));
            this.questions = this.container.find('.kata-quiz-question');
            this.currentQuestion = 0;
            this.answers = {};
            this.timer = null;
            this.timeRemaining = parseInt(this.container.data('timer')) || 0;
            this.startTime = Date.now();
            this.passScore = parseInt(this.container.data('pass-score')) || 70;
            this.allowRetake = this.container.data('allow-retake') === 'true';
            this.remainingAttempts = parseInt(this.container.data('remaining-attempts')) || 0;
            this.maxAttempts = parseInt(this.container.data('max-attempts')) || 3;
            
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.initTimer();
            this.trackView();
            this.updateProgress();
            this.showCurrentQuestion();
            this.showAttemptWarning();
        }
        
        bindEvents() {
            // Navigation buttons
            this.container.on('click', '.btn-next', (e) => {
                e.preventDefault();
                this.nextQuestion();
            });
            
            this.container.on('click', '.btn-prev', (e) => {
                e.preventDefault();
                this.prevQuestion();
            });
            
            this.container.on('click', '.btn-submit', (e) => {
                e.preventDefault();
                this.submitQuiz();
            });
            
            this.container.on('click', '.btn-restart', (e) => {
                e.preventDefault();
                this.restartQuiz();
            });
            
            this.container.on('click', '.btn-share', (e) => {
                e.preventDefault();
                this.shareResults();
            });
            
            // Answer selection
            this.container.on('change', 'input[type="radio"]', (e) => {
                this.handleAnswerSelection(e);
            });
            
            // Keyboard navigation
            $(document).on('keydown', (e) => {
                if (this.container.is(':visible') && !this.container.find('.kata-quiz-results').is(':visible')) {
                    this.handleKeyboard(e);
                }
            });
        }
        
        initTimer() {
            const timerElement = this.container.find('.kata-quiz-timer');
            if (timerElement.length) {
                this.timeRemaining = parseInt(timerElement.data('timer'));
                this.startTimer();
            }
        }
        
        startTimer() {
            if (this.timeRemaining <= 0) return;
            
            this.timer = setInterval(() => {
                this.timeRemaining--;
                this.updateTimerDisplay();
                
                if (this.timeRemaining <= 0) {
                    this.timeUp();
                }
            }, 1000);
        }
        
        updateTimerDisplay() {
            const minutes = Math.floor(this.timeRemaining / 60);
            const seconds = this.timeRemaining % 60;
            const timeString = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            this.container.find('.timer-count').text(timeString);
            
            // Warning colors
            if (this.timeRemaining <= 60) {
                this.container.find('.kata-quiz-timer').addClass('warning');
            }
            if (this.timeRemaining <= 30) {
                this.container.find('.kata-quiz-timer').addClass('critical');
            }
        }
        
        timeUp() {
            clearInterval(this.timer);
            this.showNotification('⏰ Hết thời gian! Quiz sẽ được nộp tự động.', 'warning');
            setTimeout(() => this.submitQuiz(), 2000);
        }
        
        showAttemptWarning() {
            if (this.remainingAttempts <= 1) {
                const warningMessage = this.remainingAttempts === 1 
                    ? '⚠️ Đây là lần thử cuối cùng của bạn! Hãy cẩn thận khi trả lời.'
                    : '🚨 Bạn đã hết lượt thử cho quiz này.';
                    
                this.showNotification(warningMessage, 'warning', 8000);
            } else if (this.remainingAttempts <= 2) {
                this.showNotification(`ℹ️ Bạn còn ${this.remainingAttempts} lượt thử cho quiz này.`, 'info', 5000);
            }
        }
        
        trackView() {
            $.post(kataQuiz.ajax_url, {
                action: 'kata_quiz_track_view',
                quiz_id: this.quizId,
                nonce: kataQuiz.nonce
            });
        }
        
        handleAnswerSelection(e) {
            const input = $(e.target);
            const questionIndex = parseInt(input.data('question'));
            const answer = parseInt(input.val());
            
            this.answers[questionIndex] = answer;
            
            // Visual feedback
            input.closest('.question-options').find('.option-label').removeClass('selected');
            input.closest('.option-label').addClass('selected');
            
            // Enable next/submit button
            this.updateNavigationButtons();
            
            // Update progress
            this.updateProgress();
            
            // Auto-advance option (can be enabled via settings)
            const autoAdvance = false; // Can be made configurable
            if (autoAdvance && this.currentQuestion < this.totalQuestions - 1) {
                setTimeout(() => this.nextQuestion(), 1000);
            }
        }
        
        showCurrentQuestion() {
            this.questions.hide();
            $(this.questions[this.currentQuestion]).show();
        }
        
        hideCurrentQuestion() {
            $(this.questions[this.currentQuestion]).hide();
        }
        
        isCurrentQuestionAnswered() {
            return this.answers.hasOwnProperty(this.currentQuestion);
        }
        
        updateNavigationButtons() {
            const prevBtn = this.container.find('.btn-prev');
            const nextBtn = this.container.find('.btn-next');
            const submitBtn = this.container.find('.btn-submit');
            
            // Previous button
            if (this.currentQuestion === 0) {
                prevBtn.hide();
            } else {
                prevBtn.show().prop('disabled', false);
            }
            
            // Next/Submit button
            if (this.currentQuestion === this.totalQuestions - 1) {
                nextBtn.hide();
                submitBtn.show();
            } else {
                nextBtn.show();
                submitBtn.hide();
            }
            
            // Enable/disable based on answer
            const isAnswered = this.isCurrentQuestionAnswered();
            nextBtn.prop('disabled', !isAnswered);
            submitBtn.prop('disabled', Object.keys(this.answers).length !== this.totalQuestions);
        }
        
        updateProgress() {
            const progress = ((this.currentQuestion + 1) / this.totalQuestions) * 100;
            this.container.find('.progress-fill').css('width', progress + '%');
            this.container.find('.current-question').text(this.currentQuestion + 1);
            this.container.find('.total-questions').text(this.totalQuestions);
        }
        
        handleKeyboard(e) {
            switch(e.key) {
                case 'ArrowRight':
                case 'Enter':
                    if (e.key === 'Enter' && !$(e.target).is('button')) {
                        return;
                    }
                    e.preventDefault();
                    this.nextQuestion();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.prevQuestion();
                    break;
                case '1': case '2': case '3': case '4': case '5':
                    e.preventDefault();
                    this.selectOption(parseInt(e.key) - 1);
                    break;
            }
        }
        
        selectOption(optionIndex) {
            const currentQuestionElement = this.questions.eq(this.currentQuestion);
            const options = currentQuestionElement.find('input[type="radio"]');
            
            if (options.eq(optionIndex).length) {
                options.eq(optionIndex).prop('checked', true).trigger('change');
            }
        }
        
        nextQuestion() {
            // Check if current question is answered
            if (!this.isCurrentQuestionAnswered() && this.totalQuestions > 1) {
                this.showNotification('⚠️ Vui lòng chọn một đáp án trước khi tiếp tục!', 'warning');
                return;
            }
            
            if (this.currentQuestion < this.totalQuestions - 1) {
                this.hideCurrentQuestion();
                this.currentQuestion++;
                this.showCurrentQuestion();
                this.updateProgress();
                this.updateNavigationButtons();
                
                // Smooth scroll to top of quiz
                this.container[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
        
        prevQuestion() {
            if (this.currentQuestion > 0) {
                this.hideCurrentQuestion();
                this.currentQuestion--;
                this.showCurrentQuestion();
                this.updateProgress();
                this.updateNavigationButtons();
            }
        }
        
        showQuestion(index) {
            // Hide all questions
            this.questions.hide();
            
            // Show current question with animation
            this.questions.eq(index).fadeIn(300);
            
            // Update progress
            this.updateProgress();
            
            // Update navigation buttons
            this.updateNavigationButtons();
            
            // Focus on first option for accessibility
            setTimeout(() => {
                this.questions.eq(index).find('input[type="radio"]').first().focus();
            }, 100);
        }
        
        updateProgress() {
            const progress = ((this.currentQuestion + 1) / this.questions.length) * 100;
            this.container.find('.progress-fill').css('width', `${progress}%`);
            this.container.find('.progress-text').text(`Câu hỏi ${this.currentQuestion + 1} / ${this.questions.length}`);
        }
        
        updateNavigationButtons() {
            const prevBtn = this.container.find('.btn-prev');
            const nextBtn = this.container.find('.btn-next');
            const submitBtn = this.container.find('.btn-submit');
            
            // Previous button
            if (this.currentQuestion === 0) {
                prevBtn.hide();
            } else {
                prevBtn.show();
            }
            
            // Next/Submit button
            if (this.currentQuestion === this.questions.length - 1) {
                nextBtn.hide();
                submitBtn.show();
            } else {
                nextBtn.show();
                submitBtn.hide();
            }
            
            // Enable/disable based on answer
            const currentAnswered = this.answers.hasOwnProperty(this.currentQuestion);
            if (currentAnswered) {
                nextBtn.prop('disabled', false);
                submitBtn.prop('disabled', false);
            } else {
                nextBtn.prop('disabled', true);
                submitBtn.prop('disabled', true);
            }
        }
        
        isQuizComplete() {
            return Object.keys(this.answers).length === this.questions.length;
        }
        
        submitQuiz() {
            // Validate all questions answered
            const answeredQuestions = Object.keys(this.answers).length;
            if (answeredQuestions !== this.totalQuestions) {
                const remaining = this.totalQuestions - answeredQuestions;
                this.showNotification(`⚠️ Bạn còn ${remaining} câu hỏi chưa trả lời!`, 'warning');
                return;
            }
            
            // Confirm submission
            if (!confirm('🚀 Bạn có chắc chắn muốn nộp bài không?')) {
                return;
            }
            
            const timeTaken = Math.floor((Date.now() - this.startTime) / 1000);
            
            // Clear timer
            if (this.timer) {
                clearInterval(this.timer);
            }
            
            // Show loading
            this.showLoading(true);
            
            $.post(kataQuiz.ajax_url, {
                action: 'kata_quiz_submit',
                quiz_id: this.quizId,
                answers: JSON.stringify(this.answers),
                time_taken: timeTaken,
                nonce: kataQuiz.nonce
            })
            .done((response) => {
                if (response.success) {
                    // Giảm số lần thử còn lại
                    this.remainingAttempts = Math.max(0, this.remainingAttempts - 1);
                    this.updateAttemptInfo();
                    
                    this.showResults(response.data);
                    this.showNotification('🎉 Hoàn thành quiz thành công!', 'success');
                    
                    // Hiển thị thông báo về số lượt còn lại
                    if (this.remainingAttempts > 0) {
                        setTimeout(() => {
                            this.showNotification(`ℹ️ Bạn còn ${this.remainingAttempts} lượt thử cho quiz này.`, 'info', 5000);
                        }, 2000);
                    } else {
                        setTimeout(() => {
                            this.showNotification('⚠️ Bạn đã hết lượt thử cho quiz này.', 'warning', 8000);
                        }, 2000);
                    }
                } else {
                    this.showNotification('❌ ' + (response.data?.message || 'Có lỗi xảy ra'), 'error');
                }
            })
            .fail((xhr, status, error) => {
                console.error('Quiz submission failed:', error);
                this.showNotification('❌ Có lỗi xảy ra khi nộp bài. Vui lòng thử lại!', 'error');
            })
            .always(() => {
                this.showLoading(false);
            });
        }
        
        showResults(data) {
            // Hide quiz questions and controls
            this.container.find('.kata-quiz-questions, .kata-quiz-controls, .kata-quiz-progress, .kata-quiz-timer').hide();
            
            // Update results display
            const resultsContainer = this.container.find('.kata-quiz-results');
            resultsContainer.find('.score-percentage').text(data.score + '%');
            resultsContainer.find('.correct-count').text(data.correct_answers);
            resultsContainer.find('.total-count').text(data.total_questions);
            resultsContainer.find('.score-message').text(data.message);
            
            // Update score circle color based on performance
            const scoreCircle = resultsContainer.find('.score-circle');
            scoreCircle.removeClass('excellent good average poor');
            
            if (data.score >= 90) {
                scoreCircle.addClass('excellent');
            } else if (data.score >= 80) {
                scoreCircle.addClass('good');
            } else if (data.score >= 60) {
                scoreCircle.addClass('average');
            } else {
                scoreCircle.addClass('poor');
            }
            
            // Show/hide retake button based on settings
            if (!this.allowRetake) {
                resultsContainer.find('.btn-restart').hide();
            }
            
            // Show results
            resultsContainer.show();
            
            // Scroll to results
            resultsContainer[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        showLoading(show) {
            if (show) {
                this.container.find('.kata-quiz-loading').show();
                this.container.find('.kata-quiz-controls button').prop('disabled', true);
            } else {
                this.container.find('.kata-quiz-loading').hide();
                this.container.find('.kata-quiz-controls button').prop('disabled', false);
            }
        }
        
        updateAttemptInfo() {
            // Cập nhật hiển thị số lượt thử còn lại
            const attemptText = this.container.find('.attempt-text');
            if (attemptText.length) {
                attemptText.html(`Còn lại: <strong>${this.remainingAttempts}</strong> lượt thử`);
                
                // Thay đổi màu sắc dựa trên số lượt còn lại
                const attemptInfo = this.container.find('.kata-quiz-attempt-info');
                attemptInfo.removeClass('warning danger');
                
                if (this.remainingAttempts <= 0) {
                    attemptInfo.addClass('danger');
                } else if (this.remainingAttempts <= 1) {
                    attemptInfo.addClass('warning');
                }
            }
        }
        
        showNotification(message, type = 'info', duration = 4000) {
            // Remove existing notifications
            $('.kata-quiz-notification').remove();
            
            const notification = $(`
                <div class="kata-quiz-notification kata-quiz-notification-${type}">
                    <span class="notification-message">${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);
            
            // Insert notification
            this.container.prepend(notification);
            
            // Auto hide after specified duration
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, duration);
            
            // Manual close
            notification.find('.notification-close').on('click', () => {
                notification.fadeOut(() => notification.remove());
            });
        }
        
        restartQuiz() {
            if (!confirm('🔄 Bạn có chắc chắn muốn làm lại quiz không?')) {
                return;
            }
            
            // Reset quiz state
            this.currentQuestion = 0;
            this.answers = {};
            this.startTime = Date.now();
            
            // Reset timer if exists
            if (this.timer) {
                clearInterval(this.timer);
            }
            this.timeRemaining = parseInt(this.container.data('timer')) || 0;
            
            // Reset UI
            this.container.find('.kata-quiz-results').hide();
            this.container.find('.kata-quiz-questions, .kata-quiz-controls, .kata-quiz-progress').show();
            
            // Reset form
            this.container.find('input[type="radio"]').prop('checked', false);
            this.container.find('.option-label').removeClass('selected');
            
            // Show first question
            this.showCurrentQuestion();
            this.updateProgress();
            this.updateNavigationButtons();
            
            // Restart timer if needed
            if (this.timeRemaining > 0) {
                this.startTimer();
            }
            
            // Scroll to top
            this.container[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        shareResults() {
            const resultsContainer = this.container.find('.kata-quiz-results');
            const score = resultsContainer.find('.score-percentage').text();
            const quizTitle = this.container.find('.kata-quiz-title').text();
            
            const shareText = `🎯 Tôi vừa hoàn thành "${quizTitle}" với điểm số ${score}!`;
            const shareUrl = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: quizTitle,
                    text: shareText,
                    url: shareUrl
                }).catch(console.error);
            } else {
                // Fallback: copy to clipboard
                const textToCopy = `${shareText} ${shareUrl}`;
                navigator.clipboard.writeText(textToCopy).then(() => {
                    this.showNotification('📋 Đã sao chép kết quả vào clipboard!', 'success');
                }).catch(() => {
                    // Ultimate fallback: show share dialog
                    const shareDialog = $(`
                        <div class="kata-quiz-share-dialog">
                            <h4>Chia sẻ kết quả</h4>
                            <textarea readonly>${textToCopy}</textarea>
                            <button class="btn-copy">📋 Sao chép</button>
                            <button class="btn-close">✖ Đóng</button>
                        </div>
                    `);
                    
                    this.container.append(shareDialog);
                    
                    shareDialog.find('.btn-copy').on('click', () => {
                        shareDialog.find('textarea').select();
                        document.execCommand('copy');
                        this.showNotification('📋 Đã sao chép!', 'success');
                        shareDialog.remove();
                    });
                    
                    shareDialog.find('.btn-close').on('click', () => shareDialog.remove());
                });
            }
        }
        }
        
        showCorrectAnswers() {
            // This would require quiz data from server
            // For now, we'll just highlight selected answers
            this.questions.each((index, question) => {
                const $question = $(question);
                const userAnswer = this.answers[index];
                
                $question.find('.option-label').each((optionIndex, option) => {
                    if (optionIndex === userAnswer) {
                        $(option).addClass('user-selected');
                    }
                });
            });
        }
        
        restartQuiz() {
            // Reset quiz state
            this.currentQuestion = 0;
            this.answers = {};
            this.startTime = Date.now();
            
            // Reset UI
            this.container.find('.kata-quiz-results').hide();
            this.container.find('.kata-quiz-questions, .kata-quiz-controls, .kata-quiz-progress').show();
            
            // Clear selections
            this.container.find('input[type="radio"]').prop('checked', false);
            this.container.find('.option-label').removeClass('selected user-selected');
            
            // Show first question
            this.showQuestion(0);
            
            // Restart timer if needed
            if (this.container.find('.kata-quiz-timer').length) {
                this.timeRemaining = parseInt(this.container.find('.kata-quiz-timer').data('timer'));
                this.startTimer();
            }
            
            // Track new attempt
            this.trackView();
        }
        
        shareResults() {
            const scorePercentage = Math.round(this.container.find('.score-percentage').text().replace('%', ''));
            const quizTitle = this.container.find('.kata-quiz-title').text();
            
            const shareText = `Tôi vừa hoàn thành "${quizTitle}" và đạt ${scorePercentage}%! 🎯`;
            const shareUrl = window.location.href;
            
            if (navigator.share) {
                navigator.share({
                    title: quizTitle,
                    text: shareText,
                    url: shareUrl,
                });
            } else {
                // Fallback to copy to clipboard
                const fullShareText = `${shareText}\n\n${shareUrl}`;
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(fullShareText).then(() => {
                        this.showNotification('📋 Đã sao chép kết quả vào clipboard!', 'success');
                    });
                } else {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = fullShareText;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    this.showNotification('📋 Đã sao chép kết quả vào clipboard!', 'success');
                }
            }
        }
        
        trackCompletion(data) {
            // Track completion event for analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'quiz_completion', {
                    quiz_id: this.quizId,
                    score: data.score,
                    time_taken: data.time_taken
                });
            }
        }
        
        showNotification(message, type = 'info') {
            const notification = $(`
                <div class="kata-quiz-notification kata-quiz-notification-${type}">
                    ${message}
                </div>
            `);
            
            $('body').append(notification);
            
            // Show notification
            setTimeout(() => notification.addClass('show'), 100);
            
            // Hide notification
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }
    }
    
    // Initialize quizzes when page loads
    $(document).ready(function() {
        $('.kata-quiz-container').each(function() {
            new KataQuiz(this);
        });
    });
    
    // Additional CSS for notifications
    const notificationCSS = `
        <style>
        .kata-quiz-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .kata-quiz-notification.show {
            transform: translateX(0);
        }
        
        .kata-quiz-notification-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .kata-quiz-notification-error {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
        }
        
        .kata-quiz-notification-warning {
            background: linear-gradient(135deg, #ffc107, #ff8c00);
        }
        
        .kata-quiz-notification-info {
            background: linear-gradient(135deg, #007bff, #6f42c1);
        }
        
        .kata-quiz-timer.warning {
            background: linear-gradient(135deg, #ffc107, #ff8c00) !important;
        }
        
        .kata-quiz-timer.critical {
            background: linear-gradient(135deg, #dc3545, #fd7e14) !important;
            animation: flashWarning 1s infinite;
        }
        
        @keyframes flashWarning {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .score-circle.excellent {
            background: conic-gradient(from 0deg, #28a745 0%, #20c997 var(--score-angle, 0%), #e0e0e0 var(--score-angle, 0%));
        }
        
        .score-circle.good {
            background: conic-gradient(from 0deg, #ffc107 0%, #ff8c00 var(--score-angle, 0%), #e0e0e0 var(--score-angle, 0%));
        }
        
        .score-circle.needs-improvement {
            background: conic-gradient(from 0deg, #dc3545 0%, #fd7e14 var(--score-angle, 0%), #e0e0e0 var(--score-angle, 0%));
        }
        
        @media (max-width: 768px) {
            .kata-quiz-notification {
                top: 10px;
                right: 10px;
                left: 10px;
                max-width: none;
                text-align: center;
            }
        }
        </style>
    `;
    
    $('head').append(notificationCSS);
    
})(jQuery);