/**
 * Frontend JavaScript for KATA SEO Manager Interactive Elements
 * 
 * Handles Quiz, Poll, Wheel, Rating and other interactive shortcodes
 * 
 * @package KATA_SEO_Manager
 */

(function() {
    'use strict';
    
    // Quiz Functions
    window.kataSubmitQuiz = function(quizId) {
        var container = document.getElementById(quizId);
        if (!container) return;
        
        var form = container.querySelector('.kata-quiz-form');
        var questions = container.querySelectorAll('.kata-quiz-question');
        var results = container.querySelector('.kata-quiz-results');
        
        var score = 0;
        var total = questions.length;
        var answers = [];
        
        questions.forEach(function(question, index) {
            var selected = question.querySelector('input[type="radio"]:checked');
            var questionData = window[quizId.replace(/-/g, '_') + '_questions'] || [];
            
            if (selected && questionData[index]) {
                var selectedValue = parseInt(selected.value);
                var correctAnswer = questionData[index].correct || 0;
                
                answers.push({
                    question: questionData[index].question,
                    selected: questionData[index].options[selectedValue] || '',
                    correct: questionData[index].options[correctAnswer] || '',
                    isCorrect: selectedValue === correctAnswer
                });
                
                if (selectedValue === correctAnswer) {
                    score++;
                }
            }
        });
        
        var percentage = Math.round((score / total) * 100);
        
        var resultHTML = '<h4>Kết Quả Quiz</h4>';
        resultHTML += '<div class="kata-quiz-score">Điểm số: ' + score + '/' + total + ' (' + percentage + '%)</div>';
        
        if (percentage >= 80) {
            resultHTML += '<div class="kata-quiz-message success">🎉 Xuất sắc!</div>';
        } else if (percentage >= 60) {
            resultHTML += '<div class="kata-quiz-message good">👍 Tốt!</div>';
        } else {
            resultHTML += '<div class="kata-quiz-message try-again">💪 Hãy thử lại!</div>';
        }
        
        resultHTML += '<div class="kata-quiz-details">';
        answers.forEach(function(answer, index) {
            var status = answer.isCorrect ? '✅' : '❌';
            resultHTML += '<div class="kata-answer-review">';
            resultHTML += '<strong>' + status + ' ' + answer.question + '</strong><br>';
            resultHTML += 'Bạn chọn: ' + answer.selected + '<br>';
            if (!answer.isCorrect) {
                resultHTML += 'Đáp án đúng: ' + answer.correct + '<br>';
            }
            resultHTML += '</div>';
        });
        resultHTML += '</div>';
        
        results.innerHTML = resultHTML;
        results.style.display = 'block';
        
        // Scroll to results
        results.scrollIntoView({ behavior: 'smooth' });
        
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'quiz_complete', {
                'event_category': 'engagement',
                'event_label': quizId,
                'value': percentage
            });
        }
    };
    
    // Poll Functions
    window.kataSubmitPoll = function(pollId) {
        var container = document.getElementById(pollId);
        if (!container) return;
        
        var form = container.querySelector('.kata-poll-form');
        var selected = form.querySelector('input[type="radio"]:checked');
        var results = container.querySelector('.kata-poll-results');
        var submitBtn = container.querySelector('.kata-poll-submit');
        
        if (!selected) {
            alert('Vui lòng chọn một tùy chọn!');
            return;
        }
        
        var selectedValue = selected.value;
        var selectedText = selected.nextElementSibling.textContent;
        
        // Disable form
        var inputs = form.querySelectorAll('input');
        inputs.forEach(function(input) {
            input.disabled = true;
        });
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đã Bình Chọn';
        
        // Show thank you message
        var resultHTML = '<div class="kata-poll-thank-you">';
        resultHTML += '<h4>Cảm ơn bạn đã bình chọn!</h4>';
        resultHTML += '<p>Bạn đã chọn: <strong>' + selectedText + '</strong></p>';
        resultHTML += '</div>';
        
        if (results) {
            results.innerHTML = resultHTML;
            results.style.display = 'block';
        } else {
            // Create results div if not exists
            var newResults = document.createElement('div');
            newResults.className = 'kata-poll-results';
            newResults.innerHTML = resultHTML;
            form.appendChild(newResults);
        }
        
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'poll_vote', {
                'event_category': 'engagement',
                'event_label': pollId + '_' + selectedValue,
                'value': parseInt(selectedValue)
            });
        }
        
        // Optional: Submit to backend
        if (typeof kata_seo_ajax !== 'undefined') {
            jQuery.post(kata_seo_ajax.url, {
                action: 'kata_seo_submit_poll',
                poll_id: pollId,
                option: selectedValue,
                nonce: kata_seo_ajax.nonce
            });
        }
    };
    
    // NOTE: Wheel functions moved to wheel-frontend.js (KataWheel class)
    // The wheel now uses a modern ES6 class-based approach with proper AJAX integration
    
    // ✅ BUGFIX: FAQ Toggle Function
    window.kataToggleFAQ = function(faqId) {
        var faqItem = document.getElementById(faqId);
        if (!faqItem) {
            console.warn('FAQ item not found:', faqId);
            return;
        }
        
        var answer = faqItem.querySelector('.kata-faq-answer');
        var question = faqItem.querySelector('.kata-faq-question');
        
        if (!answer) {
            console.warn('FAQ answer element not found in:', faqId);
            return;
        }
        
        // Toggle visibility
        if (answer.style.display === 'none' || !answer.style.display) {
            answer.style.display = 'block';
            if (question) {
                question.classList.add('active');
                question.setAttribute('aria-expanded', 'true');
            }
        } else {
            answer.style.display = 'none';
            if (question) {
                question.classList.remove('active');
                question.setAttribute('aria-expanded', 'false');
            }
        }
        
        // Analytics tracking
        if (typeof gtag !== 'undefined') {
            gtag('event', 'faq_toggle', {
                'event_category': 'engagement',
                'event_label': faqId,
                'value': answer.style.display === 'block' ? 1 : 0
            });
        }
    };
    
    // Rating Functions
    window.kataSetRating = function(ratingId, event) {
        var container = document.getElementById(ratingId);
        if (!container) return;
        
        var starsContainer = container.querySelector('.kata-rating-stars');
        var clickedStar = event.target;
        
        if (!clickedStar.classList.contains('kata-rating-star')) return;
        
        var rating = parseInt(clickedStar.dataset.star);
        var maxStars = parseInt(starsContainer.dataset.max);
        
        // Update visual rating
        var stars = starsContainer.querySelectorAll('.kata-rating-star');
        stars.forEach(function(star, index) {
            if (index < rating) {
                star.classList.add('filled');
            } else {
                star.classList.remove('filled');
            }
        });
        
        // Update average display
        var averageDisplay = container.querySelector('.kata-rating-score');
        if (averageDisplay) {
            averageDisplay.textContent = rating + '.0';
        }
        
        // Update data attribute
        starsContainer.dataset.rating = rating;
        
        // Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'rating_set', {
                'event_category': 'engagement',
                'event_label': ratingId,
                'value': rating
            });
        }
        
        // Optional: Submit to backend
        if (typeof kata_seo_ajax !== 'undefined') {
            jQuery.post(kata_seo_ajax.url, {
                action: 'kata_seo_submit_rating',
                rating_id: ratingId,
                rating: rating,
                nonce: kata_seo_ajax.nonce
            });
        }
    };
    
    // Add CSS styles
    var styles = `
        <style>
        .kata-quiz-results {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #0073aa;
        }
        .kata-quiz-score {
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
            margin-bottom: 15px;
        }
        .kata-quiz-message.success { color: #28a745; font-size: 18px; }
        .kata-quiz-message.good { color: #17a2b8; font-size: 18px; }
        .kata-quiz-message.try-again { color: #dc3545; font-size: 18px; }
        .kata-answer-review {
            margin: 10px 0;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }
        .kata-poll-results {
            margin-top: 20px;
            padding: 20px;
            background: #e8f5e8;
            border-radius: 8px;
            text-align: center;
        }
        .kata-wheel-container {
            text-align: center;
        }
        .kata-wheel-spinner {
            margin: 20px auto;
            position: relative;
        }
        .kata-wheel-pointer {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 20px solid #333;
        }
        .kata-wheel-spin {
            padding: 12px 24px;
            background: #0073aa;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .kata-wheel-spin:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .kata-wheel-result {
            margin-top: 20px;
            padding: 20px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
        }
        .kata-rating-stars {
            cursor: pointer;
            font-size: 24px;
            margin: 10px 0;
        }
        .kata-rating-star {
            color: #ddd;
            transition: color 0.2s;
            margin: 0 2px;
        }
        .kata-rating-star.filled {
            color: #ffc107;
        }
        .kata-rating-star:hover {
            color: #ffb300;
        }
        .kata-rating-average {
            margin-top: 10px;
            font-size: 18px;
            color: #666;
        }
        </style>
    `;
    
    document.head.insertAdjacentHTML('beforeend', styles);
    
    // Poll Functions
    window.kataSubmitPoll = function(pollId) {
        var container = document.getElementById(pollId);
        if (!container) return;
        
        var form = container.querySelector('.kata-poll-form');
        var selected = form.querySelector('input[type="radio"]:checked');
        
        if (!selected) {
            alert('Vui lòng chọn một tùy chọn.');
            return;
        }
        
        var submitButton = form.querySelector('.kata-poll-submit');
        submitButton.disabled = true;
        submitButton.textContent = 'Đang xử lý...';
        
        var pollIdFromData = container.getAttribute('data-poll-id');
        if (!pollIdFromData) {
            alert('Lỗi: Không tìm thấy ID cuộc bình chọn.');
            submitButton.disabled = false;
            submitButton.textContent = 'Bình Chọn';
            return;
        }
        
        var formData = new FormData();
        formData.append('action', 'kata_submit_poll_vote');
        formData.append('poll_id', pollIdFromData);
        formData.append('option_value', selected.value);
        
        fetch(kata_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide form and show results
                form.style.display = 'none';
                kataLoadPollResults(pollId, pollIdFromData);
                
                // Show success message
                var message = document.createElement('div');
                message.className = 'kata-poll-success';
                message.innerHTML = '<p style="color: green; font-weight: bold;">' + data.data.message + '</p>';
                container.insertBefore(message, container.querySelector('.kata-poll-results'));
            } else {
                alert('Lỗi: ' + data.data);
                submitButton.disabled = false;
                submitButton.textContent = 'Bình Chọn';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi phiếu bình chọn.');
            submitButton.disabled = false;
            submitButton.textContent = 'Bình Chọn';
        });
    };
    
    window.kataLoadPollResults = function(pollId, pollIdFromData) {
        var container = document.getElementById(pollId);
        if (!container) return;
        
        var resultsDiv = container.querySelector('.kata-poll-results');
        if (!resultsDiv) return;
        
        var formData = new FormData();
        formData.append('action', 'kata_get_poll_results');
        formData.append('poll_id', pollIdFromData);
        
        fetch(kata_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var results = data.data.results;
                var totalVotes = data.data.total_votes;
                
                var html = '<h4>Kết Quả Bình Chọn</h4>';
                results.forEach(function(result) {
                    html += '<div class="kata-poll-result-item">';
                    html += '<span class="kata-poll-option-text">' + result.option + '</span>';
                    html += '<span class="kata-poll-votes">(' + result.votes + ' phiếu)</span>';
                    html += '<div class="kata-poll-progress-bar">';
                    html += '<div class="kata-poll-progress" style="width: ' + result.percentage + '%"></div>';
                    html += '</div>';
                    html += '<span class="kata-poll-percentage">' + result.percentage + '%</span>';
                    html += '</div>';
                });
                html += '<p class="kata-poll-total">Tổng số phiếu: ' + totalVotes + '</p>';
                
                resultsDiv.innerHTML = html;
                resultsDiv.style.display = 'block';
            } else {
                console.error('Error loading poll results:', data.data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    };
    
})();