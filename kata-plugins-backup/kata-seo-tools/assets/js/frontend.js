/**
 * Kata SEO Tools - Frontend JavaScript
 * 
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * Main Kata SEO object
     */
    window.kataSEO = {
        
        /**
         * AJAX URL and nonce
         */
        ajax_url: kata_seo_ajax.ajax_url || '/wp-admin/admin-ajax.php',
        nonce: kata_seo_ajax.nonce || '',
        
        /**
         * Initialize all components
         */
        init: function() {
            this.initQuiz();
            this.initPoll();
            this.initRating();
            this.initFAQ();
            this.initForm();
            this.initSocialShare();
        },
        
        // ===================================
        // SOCIAL SHARE
        // ===================================
        
        /**
         * Initialize social share tracking
         */
        initSocialShare: function() {
            $('.kata-social-btn').on('click', function(e) {
                var platform = $(this).data('platform');
                var postId = $(this).closest('.kata-social-share').data('post-id');
                
                if (platform && postId) {
                    kataSEO.trackShare(platform, postId);
                }
            });
        },
        
        /**
         * Track social share
         */
        trackShare: function(platform, postId) {
            $.ajax({
                url: this.ajax_url,
                method: 'POST',
                data: {
                    action: 'kata_seo_track_share',
                    nonce: this.nonce,
                    platform: platform,
                    post_id: postId
                }
            });
        },
        
        /**
         * Copy quote to clipboard
         */
        copyQuote: function(btn) {
            var quote = $(btn).data('quote');
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(quote).then(function() {
                    var originalText = $(btn).html();
                    $(btn).html('<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg> Copied!');
                    
                    setTimeout(function() {
                        $(btn).html(originalText);
                    }, 2000);
                });
            } else {
                alert('Your browser does not support clipboard copy');
            }
        },
        
        // ===================================
        // QUIZ
        // ===================================
        
        /**
         * Initialize quiz functionality
         */
        initQuiz: function() {
            var self = this;
            
            // Update progress on answer selection
            $('.kata-quiz').each(function() {
                var quizEl = $(this);
                
                quizEl.find('input[type="radio"]').on('change', function() {
                    self.updateQuizProgress(quizEl);
                });
            });
            
            // Submit quiz
            $('.quiz-submit-btn').on('click', function() {
                var quizEl = $(this).closest('.kata-quiz');
                self.submitQuiz(quizEl);
            });
            
            // Reset quiz
            $('.quiz-reset-btn').on('click', function() {
                var quizEl = $(this).closest('.kata-quiz');
                self.resetQuiz(quizEl);
            });
        },
        
        /**
         * Update quiz progress
         */
        updateQuizProgress: function(quizEl) {
            var totalQuestions = quizEl.find('.quiz-question').length;
            var answeredQuestions = quizEl.find('.quiz-question').filter(function() {
                return $(this).find('input[type="radio"]:checked').length > 0;
            }).length;
            
            var percentage = (answeredQuestions / totalQuestions) * 100;
            
            quizEl.find('.progress-fill').css('width', percentage + '%');
            quizEl.find('.progress-text').text(Math.round(percentage) + '%');
        },
        
        /**
         * Submit quiz
         */
        submitQuiz: function(quizEl) {
            var self = this;
            var quizId = quizEl.data('quiz-id');
            var postId = quizEl.data('post-id');
            var passScore = quizEl.data('pass-score') || 70;
            
            var answers = [];
            var allAnswered = true;
            
            quizEl.find('.quiz-question').each(function(index) {
                var selected = $(this).find('input[type="radio"]:checked');
                
                if (selected.length === 0) {
                    allAnswered = false;
                    return false;
                }
                
                answers.push({
                    question_index: index,
                    answer_index: parseInt(selected.val()),
                    is_correct: selected.data('correct') === 1
                });
            });
            
            if (!allAnswered) {
                alert('Vui lòng trả lời tất cả câu hỏi!');
                return;
            }
            
            // Disable submit button
            quizEl.find('.quiz-submit-btn').prop('disabled', true).text('Đang xử lý...');
            
            $.ajax({
                url: self.ajax_url,
                method: 'POST',
                data: {
                    action: 'kata_seo_submit_quiz',
                    nonce: self.nonce,
                    quiz_id: quizId,
                    post_id: postId,
                    pass_score: passScore,
                    answers: JSON.stringify(answers)
                },
                success: function(response) {
                    if (response.success) {
                        self.showQuizResult(quizEl, response.data, answers);
                    } else {
                        alert(response.data || 'Có lỗi xảy ra');
                        quizEl.find('.quiz-submit-btn').prop('disabled', false).text('Nộp bài');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                    quizEl.find('.quiz-submit-btn').prop('disabled', false).text('Nộp bài');
                }
            });
        },
        
        /**
         * Show quiz result
         */
        showQuizResult: function(quizEl, result, answers) {
            // Hide questions and submit button
            quizEl.find('.quiz-questions, .quiz-submit-btn').hide();
            
            // Mark correct/incorrect answers
            quizEl.find('.quiz-question').each(function(index) {
                var questionEl = $(this);
                var answer = answers[index];
                
                questionEl.find('.quiz-option').each(function(optIndex) {
                    var optionEl = $(this);
                    var isCorrect = $(this).find('input').data('correct') === 1;
                    var wasSelected = parseInt($(this).find('input').val()) === answer.answer_index;
                    
                    if (wasSelected && answer.is_correct) {
                        optionEl.addClass('correct');
                    } else if (wasSelected && !answer.is_correct) {
                        optionEl.addClass('incorrect');
                    } else if (isCorrect) {
                        optionEl.addClass('correct');
                    }
                });
                
                // Show explanation
                questionEl.find('.question-explanation').show();
            });
            
            // Show result
            var resultIcon = result.passed ? '🎉' : '📚';
            quizEl.find('.result-icon').text(resultIcon);
            quizEl.find('.quiz-score').html(
                '<strong>' + result.score + '/' + result.total + '</strong> câu đúng (' + result.percentage + '%)'
            );
            quizEl.find('.quiz-message').text(result.message);
            quizEl.find('.quiz-result').show();
            quizEl.find('.quiz-reset-btn').show();
            
            // Scroll to result
            $('html, body').animate({
                scrollTop: quizEl.find('.quiz-result').offset().top - 100
            }, 500);
        },
        
        /**
         * Reset quiz
         */
        resetQuiz: function(quizEl) {
            quizEl.find('input[type="radio"]').prop('checked', false);
            quizEl.find('.quiz-option').removeClass('correct incorrect');
            quizEl.find('.question-explanation').hide();
            quizEl.find('.quiz-questions, .quiz-submit-btn').show();
            quizEl.find('.quiz-result, .quiz-reset-btn').hide();
            quizEl.find('.quiz-submit-btn').prop('disabled', false).html(
                '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M9,20.42L2.79,14.21L5.62,11.38L9,14.77L18.88,4.88L21.71,7.71L9,20.42Z"/></svg> Nộp bài'
            );
            quizEl.find('.progress-fill').css('width', '0%');
            quizEl.find('.progress-text').text('0%');
            
            // Scroll to top of quiz
            $('html, body').animate({
                scrollTop: quizEl.offset().top - 100
            }, 500);
        },
        
        // ===================================
        // POLL
        // ===================================
        
        /**
         * Initialize poll functionality
         */
        initPoll: function() {
            var self = this;
            
            $('.poll-submit-btn').on('click', function() {
                var pollEl = $(this).closest('.kata-poll');
                self.submitPoll(pollEl);
            });
        },
        
        /**
         * Submit poll vote
         */
        submitPoll: function(pollEl) {
            var self = this;
            var pollId = pollEl.data('poll-id');
            var postId = pollEl.data('post-id');
            var selectedOptions = [];
            
            pollEl.find('input[type="radio"]:checked, input[type="checkbox"]:checked').each(function() {
                selectedOptions.push(parseInt($(this).val()));
            });
            
            if (selectedOptions.length === 0) {
                alert('Vui lòng chọn ít nhất một đáp án!');
                return;
            }
            
            // Disable inputs
            pollEl.find('input, button').prop('disabled', true);
            pollEl.find('.poll-submit-btn').text('Đang xử lý...');
            
            $.ajax({
                url: self.ajax_url,
                method: 'POST',
                data: {
                    action: 'kata_seo_submit_poll',
                    nonce: self.nonce,
                    poll_id: pollId,
                    post_id: postId,
                    option_id: selectedOptions[0],
                    option_ids: selectedOptions
                },
                success: function(response) {
                    if (response.success) {
                        self.showPollResults(pollEl, response.data.results);
                        pollEl.find('.poll-submit-btn').hide();
                        pollEl.find('.poll-voted-message').show();
                    } else {
                        alert(response.data || 'Có lỗi xảy ra');
                        pollEl.find('input, button').prop('disabled', false);
                        pollEl.find('.poll-submit-btn').html(
                            '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/></svg> Vote'
                        );
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                    pollEl.find('input, button').prop('disabled', false);
                }
            });
        },
        
        /**
         * Show poll results
         */
        showPollResults: function(pollEl, results) {
            var totalVotes = 0;
            $.each(results, function(optionId, data) {
                totalVotes += data.votes;
            });
            
            pollEl.find('.poll-option').each(function(index) {
                var optionEl = $(this);
                var result = results[index] || {votes: 0, percentage: 0};
                
                var resultHtml = '<div class="poll-result-bar">' +
                    '<div class="poll-bar" style="width: ' + result.percentage + '%"></div>' +
                    '<div class="poll-stats">' +
                    '<span class="poll-percentage">' + result.percentage.toFixed(1) + '%</span>' +
                    '<span class="poll-votes">(' + result.votes + ' votes)</span>' +
                    '</div>' +
                    '</div>';
                
                optionEl.find('.poll-result-bar').remove();
                optionEl.append(resultHtml);
            });
            
            // Update total votes
            if (totalVotes > 0) {
                pollEl.find('.poll-total strong').text(totalVotes.toLocaleString());
                pollEl.find('.poll-total').show();
            }
        },
        
        // ===================================
        // RATING
        // ===================================
        
        /**
         * Initialize rating functionality
         */
        initRating: function() {
            var self = this;
            
            // Hover effect
            $('.kata-rating-star-btn').on('mouseenter', function() {
                var rating = $(this).data('rating');
                $(this).addClass('selected').prevAll('.kata-rating-star-btn').addClass('selected');
                $(this).nextAll('.kata-rating-star-btn').removeClass('selected');
            });
            
            $('.kata-rating-stars-input').on('mouseleave', function() {
                var selected = $(this).find('.kata-rating-star-btn.selected').last().data('rating');
                if (!selected) {
                    $(this).find('.kata-rating-star-btn').removeClass('selected');
                }
            });
            
            // Submit rating
            $('.kata-rating-submit-btn').on('click', function() {
                var wrapperEl = $(this).closest('.kata-rating-wrapper');
                self.submitRating(wrapperEl);
            });
        },
        
        /**
         * Select rating
         */
        selectRating: function(starBtn) {
            var starsContainer = $(starBtn).closest('.kata-rating-stars-input');
            var rating = $(starBtn).data('rating');
            
            starsContainer.find('.kata-rating-star-btn').removeClass('selected');
            $(starBtn).addClass('selected').prevAll('.kata-rating-star-btn').addClass('selected');
        },
        
        /**
         * Submit rating
         */
        submitRating: function(wrapperEl) {
            var self = this;
            var postId = wrapperEl.data('post-id');
            var rating = wrapperEl.find('.kata-rating-star-btn.selected').last().data('rating');
            var comment = wrapperEl.find('.rating-review-text').val();
            
            if (!rating) {
                alert('Vui lòng chọn số sao đánh giá!');
                return;
            }
            
            wrapperEl.find('.kata-rating-submit-btn').prop('disabled', true).text('Đang gửi...');
            
            $.ajax({
                url: self.ajax_url,
                method: 'POST',
                data: {
                    action: 'kata_seo_submit_rating',
                    nonce: self.nonce,
                    post_id: postId,
                    rating: rating,
                    comment: comment
                },
                success: function(response) {
                    if (response.success) {
                        self.updateRatingDisplay(wrapperEl, response.data.aggregate);
                        alert(response.data.message);
                        wrapperEl.find('.kata-rating-submit-btn').text('Cập nhật đánh giá');
                    } else {
                        alert(response.data || 'Có lỗi xảy ra');
                    }
                    wrapperEl.find('.kata-rating-submit-btn').prop('disabled', false);
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                    wrapperEl.find('.kata-rating-submit-btn').prop('disabled', false).text('Gửi đánh giá');
                }
            });
        },
        
        /**
         * Update rating display
         */
        updateRatingDisplay: function(wrapperEl, aggregate) {
            wrapperEl.find('.kata-rating-average').text(aggregate.average.toFixed(1));
            wrapperEl.find('.count-number').text(aggregate.count.toLocaleString());
            
            // Update stars
            var avgRating = aggregate.average;
            wrapperEl.find('.kata-rating-star').each(function(index) {
                var starValue = index + 1;
                $(this).removeClass('filled half-filled');
                
                if (avgRating >= starValue) {
                    $(this).addClass('filled');
                } else if (avgRating >= starValue - 0.5) {
                    $(this).addClass('half-filled');
                }
            });
        },
        
        // ===================================
        // FAQ
        // ===================================
        
        /**
         * Initialize FAQ functionality
         */
        initFAQ: function() {
            // Already handled via onclick in PHP template
        },
        
        /**
         * Toggle FAQ item
         */
        toggleFAQ: function(questionEl) {
            var item = $(questionEl).closest('.kata-faq-item');
            var isActive = item.hasClass('active');
            
            // Close all other items in accordion
            item.siblings('.kata-faq-item').removeClass('active');
            
            // Toggle current item
            if (isActive) {
                item.removeClass('active');
            } else {
                item.addClass('active');
            }
        },
        
        /**
         * Switch FAQ tab
         */
        switchFAQTab: function(tabBtn) {
            var tabId = $(tabBtn).data('tab');
            
            $(tabBtn).addClass('active').siblings().removeClass('active');
            $('#' + tabId).addClass('active').siblings().removeClass('active');
        },
        
        // ===================================
        // FORM
        // ===================================
        
        /**
         * Initialize form functionality
         */
        initForm: function() {
            // Already handled via onsubmit in PHP template
        },
        
        /**
         * Submit custom form
         */
        submitForm: function(event, formEl) {
            event.preventDefault();
            var self = this;
            var wrapperEl = $(formEl).closest('.kata-form-wrapper');
            var formData = $(formEl).serialize();
            
            // Add action and nonce
            formData += '&action=kata_seo_submit_form&nonce=' + self.nonce;
            
            // Disable submit button
            $(formEl).find('.kata-form-submit-btn').prop('disabled', true).html(
                '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="rotating"><path d="M12,4V2A10,10 0 0,0 2,12H4A8,8 0 0,1 12,4Z"/></svg> Đang gửi...'
            );
            
            $.ajax({
                url: self.ajax_url,
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        wrapperEl.find('.kata-form-message')
                            .removeClass('error')
                            .addClass('success')
                            .text(response.data.message)
                            .show();
                        
                        // Reset form
                        formEl.reset();
                        
                        // Scroll to message
                        $('html, body').animate({
                            scrollTop: wrapperEl.find('.kata-form-message').offset().top - 100
                        }, 500);
                    } else {
                        wrapperEl.find('.kata-form-message')
                            .removeClass('success')
                            .addClass('error')
                            .text(response.data || 'Có lỗi xảy ra')
                            .show();
                    }
                    
                    $(formEl).find('.kata-form-submit-btn').prop('disabled', false).html(
                        '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/></svg> Submit'
                    );
                },
                error: function() {
                    wrapperEl.find('.kata-form-message')
                        .removeClass('success')
                        .addClass('error')
                        .text('Có lỗi xảy ra. Vui lòng thử lại!')
                        .show();
                    
                    $(formEl).find('.kata-form-submit-btn').prop('disabled', false).html(
                        '<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/></svg> Submit'
                    );
                }
            });
        },
        
        // ===================================
        // WHEEL
        // ===================================
        
        /**
         * Initialize wheel
         */
        initWheel: function(wheelId, prizes, colors) {
            var canvas = document.getElementById('wheel-canvas-' + wheelId);
            if (!canvas) return;
            
            var ctx = canvas.getContext('2d');
            var centerX = canvas.width / 2;
            var centerY = canvas.height / 2;
            var radius = Math.min(centerX, centerY) - 10;
            
            // Store wheel data
            canvas.wheelData = {
                prizes: prizes,
                colors: colors,
                rotation: 0,
                spinning: false
            };
            
            this.drawWheel(canvas, ctx, centerX, centerY, radius, prizes, colors, 0);
        },
        
        /**
         * Draw wheel
         */
        drawWheel: function(canvas, ctx, centerX, centerY, radius, prizes, colors, rotation) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            var sliceAngle = (2 * Math.PI) / prizes.length;
            
            prizes.forEach(function(prize, index) {
                var startAngle = rotation + (index * sliceAngle);
                var endAngle = startAngle + sliceAngle;
                
                // Draw slice
                ctx.beginPath();
                ctx.fillStyle = colors[index % colors.length];
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.lineTo(centerX, centerY);
                ctx.fill();
                
                // Draw border
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 3;
                ctx.stroke();
                
                // Draw text
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + sliceAngle / 2);
                ctx.textAlign = 'right';
                ctx.fillStyle = '#fff';
                ctx.font = 'bold 14px Arial';
                ctx.fillText(prize.text, radius - 20, 5);
                ctx.restore();
            });
        },
        
        /**
         * Spin wheel
         */
        spinWheel: function(wheelId) {
            var self = this;
            var canvas = document.getElementById('wheel-canvas-' + wheelId);
            var wrapperEl = $('[data-wheel-id="' + wheelId + '"]');
            
            if (!canvas || canvas.wheelData.spinning) return;
            
            var postId = wrapperEl.data('post-id');
            var maxSpins = wrapperEl.data('max-spins');
            
            $.ajax({
                url: self.ajax_url,
                method: 'POST',
                data: {
                    action: 'kata_seo_spin_wheel',
                    nonce: self.nonce,
                    wheel_id: wheelId,
                    post_id: postId,
                    max_spins: maxSpins,
                    prizes: btoa(JSON.stringify(canvas.wheelData.prizes))
                },
                success: function(response) {
                    if (response.success) {
                        self.animateWheel(canvas, response.data.prize, function() {
                            self.showWheelPrize(wrapperEl, response.data);
                        });
                    } else {
                        alert(response.data || 'Có lỗi xảy ra');
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng thử lại!');
                }
            });
        },
        
        /**
         * Animate wheel spin
         */
        animateWheel: function(canvas, targetPrize, callback) {
            var self = this;
            var data = canvas.wheelData;
            var ctx = canvas.getContext('2d');
            var centerX = canvas.width / 2;
            var centerY = canvas.height / 2;
            var radius = Math.min(centerX, centerY) - 10;
            
            data.spinning = true;
            
            // Find target index
            var targetIndex = data.prizes.findIndex(p => p.text === targetPrize.text);
            var sliceAngle = (2 * Math.PI) / data.prizes.length;
            var targetRotation = (targetIndex * sliceAngle) + (sliceAngle / 2);
            
            // Add multiple rotations
            targetRotation += (Math.PI * 2 * 5); // 5 full rotations
            
            var startRotation = data.rotation;
            var duration = 4000; // 4 seconds
            var startTime = Date.now();
            
            function animate() {
                var elapsed = Date.now() - startTime;
                var progress = Math.min(elapsed / duration, 1);
                
                // Easing function
                var easeProgress = 1 - Math.pow(1 - progress, 3);
                
                data.rotation = startRotation + (targetRotation - startRotation) * easeProgress;
                
                self.drawWheel(canvas, ctx, centerX, centerY, radius, data.prizes, data.colors, data.rotation);
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    data.spinning = false;
                    if (callback) callback();
                }
            }
            
            animate();
        },
        
        /**
         * Show wheel prize
         */
        showWheelPrize: function(wrapperEl, data) {
            wrapperEl.find('.result-prize').text(data.prize.text);
            wrapperEl.find('.kata-wheel-result').fadeIn();
            wrapperEl.find('.kata-wheel-spins-left strong').text(data.spins_left);
            
            if (data.spins_left <= 0) {
                wrapperEl.find('.kata-wheel-spin-btn').prop('disabled', true);
            }
        },
        
        /**
         * Close wheel result
         */
        closeWheelResult: function() {
            $('.kata-wheel-result').fadeOut();
        },
        
        /**
         * Track CTA click
         */
        trackCTA: function(ctaId, postId) {
            $.ajax({
                url: this.ajax_url,
                method: 'POST',
                data: {
                    action: 'kata_seo_track_cta',
                    nonce: this.nonce,
                    cta_id: ctaId,
                    post_id: postId
                }
            });
            
            return true;
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        kataSEO.init();
    });
    
})(jQuery);
