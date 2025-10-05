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
    
    // Wheel Functions
    window.kataInitWheel = function(wheelId, options, colors) {
        var canvas = document.getElementById(wheelId + '-canvas');
        if (!canvas) return;
        
        var ctx = canvas.getContext('2d');
        var centerX = canvas.width / 2;
        var centerY = canvas.height / 2;
        var radius = Math.min(centerX, centerY) - 10;
        
        // Store wheel data
        window[wheelId + '_data'] = {
            options: options,
            colors: colors,
            spinning: false,
            currentAngle: 0
        };
        
        function drawWheel() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            var anglePerSlice = (2 * Math.PI) / options.length;
            var currentAngle = window[wheelId + '_data'].currentAngle;
            
            for (var i = 0; i < options.length; i++) {
                var startAngle = currentAngle + (i * anglePerSlice);
                var endAngle = startAngle + anglePerSlice;
                var color = colors[i % colors.length];
                
                // Draw slice
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.fillStyle = color;
                ctx.fill();
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 2;
                ctx.stroke();
                
                // Draw text
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + anglePerSlice / 2);
                ctx.fillStyle = '#000';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(options[i], radius * 0.7, 5);
                ctx.restore();
            }
        }
        
        drawWheel();
    };
    
    window.kataSpinWheel = function(wheelId) {
        var wheelData = window[wheelId + '_data'];
        if (!wheelData || wheelData.spinning) return;
        
        var container = document.getElementById(wheelId);
        var button = container.querySelector('.kata-wheel-spin');
        var result = container.querySelector('.kata-wheel-result');
        var canvas = document.getElementById(wheelId + '-canvas');
        var ctx = canvas.getContext('2d');
        
        wheelData.spinning = true;
        button.disabled = true;
        button.textContent = 'ĐANG QUAY...';
        
        // Random spin
        var spins = 5 + Math.random() * 5; // 5-10 spins
        var finalAngle = Math.random() * 2 * Math.PI;
        var totalRotation = spins * 2 * Math.PI + finalAngle;
        
        var startTime = Date.now();
        var duration = 3000; // 3 seconds
        
        function animate() {
            var elapsed = Date.now() - startTime;
            var progress = Math.min(elapsed / duration, 1);
            
            // Easing function
            var easedProgress = 1 - Math.pow(1 - progress, 3);
            
            wheelData.currentAngle = totalRotation * easedProgress;
            
            // Redraw wheel
            var ctx = canvas.getContext('2d');
            var centerX = canvas.width / 2;
            var centerY = canvas.height / 2;
            var radius = Math.min(centerX, centerY) - 10;
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            var anglePerSlice = (2 * Math.PI) / wheelData.options.length;
            
            for (var i = 0; i < wheelData.options.length; i++) {
                var startAngle = wheelData.currentAngle + (i * anglePerSlice);
                var endAngle = startAngle + anglePerSlice;
                var color = wheelData.colors[i % wheelData.colors.length];
                
                // Draw slice
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, radius, startAngle, endAngle);
                ctx.fillStyle = color;
                ctx.fill();
                ctx.strokeStyle = '#fff';
                ctx.lineWidth = 2;
                ctx.stroke();
                
                // Draw text
                ctx.save();
                ctx.translate(centerX, centerY);
                ctx.rotate(startAngle + anglePerSlice / 2);
                ctx.fillStyle = '#000';
                ctx.font = '12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(wheelData.options[i], radius * 0.7, 5);
                ctx.restore();
            }
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                // Determine winner
                var normalizedAngle = (2 * Math.PI - (wheelData.currentAngle % (2 * Math.PI))) % (2 * Math.PI);
                var winnerIndex = Math.floor(normalizedAngle / anglePerSlice);
                var winner = wheelData.options[winnerIndex];
                
                // Show result
                result.innerHTML = '<h4>🎉 Chúc Mừng!</h4><p>Bạn nhận được: <strong>' + winner + '</strong></p>';
                result.style.display = 'block';
                
                // Reset button
                button.disabled = false;
                button.textContent = 'QUAY LẠI';
                wheelData.spinning = false;
                
                // Analytics
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'wheel_spin', {
                        'event_category': 'engagement',
                        'event_label': wheelId,
                        'value': winnerIndex
                    });
                }
            }
        }
        
        animate();
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
    
})();