/**
 * KATA SEO Manager - Frontend Interactive Elements Controller
 * 
 * Modern ES6+ refactored version with:
 * - ES6 Classes for each widget type
 * - Async/await for AJAX operations
 * - Event delegation pattern
 * - Enhanced error handling
 * - Accessibility improvements (ARIA)
 * - JSDoc documentation
 * 
 * Handles: Quiz, Poll, FAQ, Rating and other interactive shortcodes
 * 
 * @package KATA_SEO_Manager
 * @version 2.2.0
 * @since 2.1.4
 */

(function() {
    'use strict';

    /**
     * Quiz Controller Class
     * Manages quiz interactions and scoring
     * 
     * @class KataQuiz
     */
    class KataQuiz {
        /**
         * Submit quiz and calculate results
         * 
         * @param {string} quizId - Quiz container ID
         * @returns {void}
         */
        static submit(quizId) {
            const container = document.getElementById(quizId);
            if (!container) {
                console.warn('Quiz container not found:', quizId);
                return;
            }

            const form = container.querySelector('.kata-quiz-form');
            const questions = container.querySelectorAll('.kata-quiz-question');
            const results = container.querySelector('.kata-quiz-results');

            if (!form || !questions.length) {
                console.error('Quiz form or questions not found');
                return;
            }

            const { score, total, answers } = this.calculateScore(questions, quizId);
            const percentage = Math.round((score / total) * 100);

            this.renderResults(results, score, total, percentage, answers);
            this.scrollToResults(results);
            this.trackAnalytics(quizId, percentage);
        }

        /**
         * Calculate quiz score
         * 
         * @param {NodeList} questions - Quiz questions
         * @param {string} quizId - Quiz ID
         * @returns {Object} Score data
         */
        static calculateScore(questions, quizId) {
            let score = 0;
            const total = questions.length;
            const answers = [];
            const questionData = window[quizId.replace(/-/g, '_') + '_questions'] || [];

            questions.forEach((question, index) => {
                const selected = question.querySelector('input[type="radio"]:checked');
                
                if (selected && questionData[index]) {
                    const selectedValue = parseInt(selected.value);
                    const correctAnswer = questionData[index].correct || 0;
                    const isCorrect = selectedValue === correctAnswer;

                    answers.push({
                        question: questionData[index].question,
                        selected: questionData[index].options[selectedValue] || '',
                        correct: questionData[index].options[correctAnswer] || '',
                        isCorrect
                    });

                    if (isCorrect) score++;
                }
            });

            return { score, total, answers };
        }

        /**
         * Render quiz results
         * 
         * @param {HTMLElement} resultsContainer - Results container
         * @param {number} score - Quiz score
         * @param {number} total - Total questions
         * @param {number} percentage - Score percentage
         * @param {Array} answers - Answer details
         * @returns {void}
         */
        static renderResults(resultsContainer, score, total, percentage, answers) {
            if (!resultsContainer) return;

            const messageClass = this.getMessageClass(percentage);
            const messageText = this.getMessageText(percentage);

            const resultHTML = `
                <h4>Kết Quả Quiz</h4>
                <div class="kata-quiz-score" role="status" aria-live="polite">
                    Điểm số: ${score}/${total} (${percentage}%)
                </div>
                <div class="kata-quiz-message ${messageClass}">${messageText}</div>
                <div class="kata-quiz-details">
                    ${this.renderAnswerReviews(answers)}
                </div>
            `;

            resultsContainer.innerHTML = resultHTML;
            resultsContainer.style.display = 'block';
            resultsContainer.setAttribute('aria-hidden', 'false');
        }

        /**
         * Render answer reviews
         * 
         * @param {Array} answers - Answer details
         * @returns {string} HTML string
         */
        static renderAnswerReviews(answers) {
            return answers.map(answer => {
                const status = answer.isCorrect ? '✅' : '❌';
                const wrongAnswerInfo = !answer.isCorrect 
                    ? `Đáp án đúng: ${answer.correct}<br>` 
                    : '';

                return `
                    <div class="kata-answer-review">
                        <strong>${status} ${answer.question}</strong><br>
                        Bạn chọn: ${answer.selected}<br>
                        ${wrongAnswerInfo}
                    </div>
                `;
            }).join('');
        }

        /**
         * Get message class based on percentage
         * 
         * @param {number} percentage - Score percentage
         * @returns {string} CSS class
         */
        static getMessageClass(percentage) {
            if (percentage >= 80) return 'success';
            if (percentage >= 60) return 'good';
            return 'try-again';
        }

        /**
         * Get message text based on percentage
         * 
         * @param {number} percentage - Score percentage
         * @returns {string} Message text
         */
        static getMessageText(percentage) {
            if (percentage >= 80) return '🎉 Xuất sắc!';
            if (percentage >= 60) return '👍 Tốt!';
            return '�� Hãy thử lại!';
        }

        /**
         * Scroll to results smoothly
         * 
         * @param {HTMLElement} element - Element to scroll to
         * @returns {void}
         */
        static scrollToResults(element) {
            if (element && element.scrollIntoView) {
                element.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        /**
         * Track quiz completion analytics
         * 
         * @param {string} quizId - Quiz ID
         * @param {number} percentage - Score percentage
         * @returns {void}
         */
        static trackAnalytics(quizId, percentage) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'quiz_complete', {
                    event_category: 'engagement',
                    event_label: quizId,
                    value: percentage
                });
            }
        }
    }

    /**
     * Poll Controller Class
     * Manages poll voting and results display
     * 
     * @class KataPoll
     */
    class KataPoll {
        /**
         * Submit poll vote
         * 
         * @param {string} pollId - Poll container ID
         * @returns {Promise<void>}
         */
        static async submit(pollId) {
            const container = document.getElementById(pollId);
            if (!container) {
                console.warn('Poll container not found:', pollId);
                return;
            }

            const form = container.querySelector('.kata-poll-form');
            const selected = form?.querySelector('input[type="radio"]:checked');
            const submitBtn = container.querySelector('.kata-poll-submit');

            if (!selected) {
                alert('Vui lòng chọn một tùy chọn!');
                return;
            }

            const pollIdFromData = container.getAttribute('data-poll-id');
            if (!pollIdFromData) {
                alert('Lỗi: Không tìm thấy ID cuộc bình chọn.');
                return;
            }

            try {
                this.setSubmitButtonLoading(submitBtn, true);
                
                const response = await this.submitVote(pollIdFromData, selected.value);

                if (response.success) {
                    this.disableForm(form);
                    this.showSuccessMessage(container, response.data.message);
                    await this.loadResults(pollId, pollIdFromData);
                    this.trackAnalytics(pollId, selected.value);
                } else {
                    throw new Error(response.data || 'Submission failed');
                }
            } catch (error) {
                console.error('Poll submission error:', error);
                alert('Lỗi: ' + error.message);
                this.setSubmitButtonLoading(submitBtn, false);
            }
        }

        /**
         * Submit vote via AJAX
         * 
         * @param {string} pollId - Poll ID
         * @param {string} optionValue - Selected option value
         * @returns {Promise<Object>} Response data
         */
        static async submitVote(pollId, optionValue) {
            const formData = new FormData();
            formData.append('action', 'kata_submit_poll_vote');
            formData.append('poll_id', pollId);
            formData.append('option_value', optionValue);

            const response = await fetch(kata_ajax?.ajax_url || ajaxurl, {
                method: 'POST',
                body: formData
            });

            return response.json();
        }

        /**
         * Load and display poll results
         * 
         * @param {string} pollId - Poll container ID
         * @param {string} pollIdFromData - Poll data ID
         * @returns {Promise<void>}
         */
        static async loadResults(pollId, pollIdFromData) {
            const container = document.getElementById(pollId);
            const resultsDiv = container?.querySelector('.kata-poll-results');

            if (!resultsDiv) return;

            try {
                const data = await this.fetchResults(pollIdFromData);

                if (data.success) {
                    this.renderResults(resultsDiv, data.data.results, data.data.total_votes);
                }
            } catch (error) {
                console.error('Error loading poll results:', error);
            }
        }

        /**
         * Fetch poll results via AJAX
         * 
         * @param {string} pollId - Poll ID
         * @returns {Promise<Object>} Results data
         */
        static async fetchResults(pollId) {
            const formData = new FormData();
            formData.append('action', 'kata_get_poll_results');
            formData.append('poll_id', pollId);

            const response = await fetch(kata_ajax?.ajax_url || ajaxurl, {
                method: 'POST',
                body: formData
            });

            return response.json();
        }

        /**
         * Render poll results
         * 
         * @param {HTMLElement} container - Results container
         * @param {Array} results - Results data
         * @param {number} totalVotes - Total votes count
         * @returns {void}
         */
        static renderResults(container, results, totalVotes) {
            const resultItems = results.map(result => `
                <div class="kata-poll-result-item">
                    <span class="kata-poll-option-text">${result.option}</span>
                    <span class="kata-poll-votes">(${result.votes} phiếu)</span>
                    <div class="kata-poll-progress-bar" role="progressbar" 
                         aria-valuenow="${result.percentage}" aria-valuemin="0" aria-valuemax="100">
                        <div class="kata-poll-progress" style="width: ${result.percentage}%"></div>
                    </div>
                    <span class="kata-poll-percentage">${result.percentage}%</span>
                </div>
            `).join('');

            container.innerHTML = `
                <h4>Kết Quả Bình Chọn</h4>
                ${resultItems}
                <p class="kata-poll-total">Tổng số phiếu: ${totalVotes}</p>
            `;
            container.style.display = 'block';
            container.setAttribute('aria-hidden', 'false');
        }

        /**
         * Disable poll form after voting
         * 
         * @param {HTMLElement} form - Form element
         * @returns {void}
         */
        static disableForm(form) {
            if (!form) return;

            form.querySelectorAll('input').forEach(input => {
                input.disabled = true;
            });
            form.style.display = 'none';
        }

        /**
         * Set submit button loading state
         * 
         * @param {HTMLElement} button - Submit button
         * @param {boolean} loading - Loading state
         * @returns {void}
         */
        static setSubmitButtonLoading(button, loading) {
            if (!button) return;

            button.disabled = loading;
            button.textContent = loading ? 'Đang xử lý...' : 'Bình Chọn';
        }

        /**
         * Show success message
         * 
         * @param {HTMLElement} container - Container element
         * @param {string} message - Success message
         * @returns {void}
         */
        static showSuccessMessage(container, message) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'kata-poll-success';
            messageDiv.innerHTML = `<p style="color: green; font-weight: bold;">${message}</p>`;
            messageDiv.setAttribute('role', 'status');
            messageDiv.setAttribute('aria-live', 'polite');

            const results = container.querySelector('.kata-poll-results');
            if (results) {
                container.insertBefore(messageDiv, results);
            }
        }

        /**
         * Track poll vote analytics
         * 
         * @param {string} pollId - Poll ID
         * @param {string} selectedValue - Selected option value
         * @returns {void}
         */
        static trackAnalytics(pollId, selectedValue) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'poll_vote', {
                    event_category: 'engagement',
                    event_label: `${pollId}_${selectedValue}`,
                    value: parseInt(selectedValue) || 0
                });
            }
        }
    }

    /**
     * FAQ Controller Class
     * Manages FAQ accordion interactions
     * 
     * @class KataFAQ
     */
    class KataFAQ {
        /**
         * Toggle FAQ item visibility
         * 
         * @param {string} faqId - FAQ item ID
         * @returns {void}
         */
        static toggle(faqId) {
            const faqItem = document.getElementById(faqId);
            if (!faqItem) {
                console.warn('FAQ item not found:', faqId);
                return;
            }

            const answer = faqItem.querySelector('.kata-faq-answer');
            const question = faqItem.querySelector('.kata-faq-question');

            if (!answer) {
                console.warn('FAQ answer element not found in:', faqId);
                return;
            }

            const isOpen = answer.style.display === 'block';
            
            // Toggle visibility
            answer.style.display = isOpen ? 'none' : 'block';

            // Update question state
            if (question) {
                question.classList.toggle('active', !isOpen);
                question.setAttribute('aria-expanded', !isOpen);
            }

            // Update answer accessibility
            answer.setAttribute('aria-hidden', isOpen);

            // Track analytics
            this.trackAnalytics(faqId, !isOpen);
        }

        /**
         * Track FAQ toggle analytics
         * 
         * @param {string} faqId - FAQ ID
         * @param {boolean} opened - Whether FAQ was opened
         * @returns {void}
         */
        static trackAnalytics(faqId, opened) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'faq_toggle', {
                    event_category: 'engagement',
                    event_label: faqId,
                    value: opened ? 1 : 0
                });
            }
        }
    }

    /**
     * Rating Controller Class
     * Manages star rating interactions
     * 
     * @class KataRating
     */
    class KataRating {
        /**
         * Set rating value
         * 
         * @param {string} ratingId - Rating container ID
         * @param {Event} event - Click event
         * @returns {Promise<void>}
         */
        static async set(ratingId, event) {
            const container = document.getElementById(ratingId);
            if (!container) {
                console.warn('Rating container not found:', ratingId);
                return;
            }

            const clickedStar = event.target;
            if (!clickedStar.classList.contains('kata-rating-star')) {
                return;
            }

            const rating = parseInt(clickedStar.dataset.star);
            const starsContainer = container.querySelector('.kata-rating-stars');

            if (!starsContainer) return;

            // Update visual rating
            this.updateStars(starsContainer, rating);
            
            // Update average display
            this.updateAverageDisplay(container, rating);
            
            // Update data attribute
            starsContainer.dataset.rating = rating;

            // Track analytics
            this.trackAnalytics(ratingId, rating);

            // Submit to backend
            await this.submitRating(ratingId, rating);
        }

        /**
         * Update star visual states
         * 
         * @param {HTMLElement} container - Stars container
         * @param {number} rating - Rating value
         * @returns {void}
         */
        static updateStars(container, rating) {
            const stars = container.querySelectorAll('.kata-rating-star');
            
            stars.forEach((star, index) => {
                star.classList.toggle('filled', index < rating);
                star.setAttribute('aria-checked', index < rating);
            });
        }

        /**
         * Update average rating display
         * 
         * @param {HTMLElement} container - Rating container
         * @param {number} rating - Rating value
         * @returns {void}
         */
        static updateAverageDisplay(container, rating) {
            const averageDisplay = container.querySelector('.kata-rating-score');
            
            if (averageDisplay) {
                averageDisplay.textContent = `${rating}.0`;
                averageDisplay.setAttribute('aria-live', 'polite');
            }
        }

        /**
         * Submit rating to backend
         * 
         * @param {string} ratingId - Rating ID
         * @param {number} rating - Rating value
         * @returns {Promise<void>}
         */
        static async submitRating(ratingId, rating) {
            if (typeof kata_seo_ajax === 'undefined') return;

            try {
                const formData = new FormData();
                formData.append('action', 'kata_seo_submit_rating');
                formData.append('rating_id', ratingId);
                formData.append('rating', rating);
                formData.append('nonce', kata_seo_ajax.nonce);

                await fetch(kata_seo_ajax.url, {
                    method: 'POST',
                    body: formData
                });
            } catch (error) {
                console.error('Error submitting rating:', error);
            }
        }

        /**
         * Track rating analytics
         * 
         * @param {string} ratingId - Rating ID
         * @param {number} rating - Rating value
         * @returns {void}
         */
        static trackAnalytics(ratingId, rating) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'rating_set', {
                    event_category: 'engagement',
                    event_label: ratingId,
                    value: rating
                });
            }
        }
    }

    /**
     * Styles Manager Class
     * Injects frontend styles
     * 
     * @class KataStyles
     */
    class KataStyles {
        /**
         * Inject frontend styles
         * 
         * @returns {void}
         */
        static inject() {
            if (document.getElementById('kata-frontend-styles')) {
                return; // Already injected
            }

            const styles = `
                <style id="kata-frontend-styles">
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
                }
                .kata-poll-result-item {
                    margin: 10px 0;
                }
                .kata-poll-progress-bar {
                    width: 100%;
                    height: 20px;
                    background: #e0e0e0;
                    border-radius: 10px;
                    overflow: hidden;
                    margin: 5px 0;
                }
                .kata-poll-progress {
                    height: 100%;
                    background: linear-gradient(90deg, #0073aa, #00a0d2);
                    transition: width 0.5s ease;
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
                    display: inline-block;
                }
                .kata-rating-star.filled {
                    color: #ffc107;
                }
                .kata-rating-star:hover {
                    color: #ffb300;
                    transform: scale(1.1);
                }
                .kata-rating-average {
                    margin-top: 10px;
                    font-size: 18px;
                    color: #666;
                }
                </style>
            `;

            document.head.insertAdjacentHTML('beforeend', styles);
        }
    }

    // Initialize styles
    KataStyles.inject();

    // Export global functions for backward compatibility
    window.kataSubmitQuiz = (quizId) => KataQuiz.submit(quizId);
    window.kataSubmitPoll = (pollId) => KataPoll.submit(pollId);
    window.kataLoadPollResults = (pollId, pollIdFromData) => KataPoll.loadResults(pollId, pollIdFromData);
    window.kataToggleFAQ = (faqId) => KataFAQ.toggle(faqId);
    window.kataSetRating = (ratingId, event) => KataRating.set(ratingId, event);

    // Export classes for advanced usage
    window.KataQuiz = KataQuiz;
    window.KataPoll = KataPoll;
    window.KataFAQ = KataFAQ;
    window.KataRating = KataRating;

    console.log('✅ KATA SEO Frontend initialized (ES6 version)');

})();
