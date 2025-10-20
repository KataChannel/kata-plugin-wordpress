/**
 * Poll Frontend JavaScript - ES6 Refactored
 * 
 * @package KATA_SEO_Manager
 * @since 2.2.0
 * @version 2.2.0
 */

(function($) {
    'use strict';

    /**
     * Poll Widget Controller Class
     * Manages interactive poll voting and results display
     * 
     * @class KataPoll
     */
    class KataPoll {
        /**
         * Initialize Poll Controller
         * @constructor
         */
        constructor() {
            // Configuration
            this.config = {
                ajaxUrl: kata_ajax?.url || '/wp-admin/admin-ajax.php',
                nonce: kata_ajax?.nonce || '',
                animationDuration: 300,
                resultDisplayDelay: 500,
                messageAutoHideDelay: 5000,
                progressAnimationDuration: 800
            };

            // Check dependencies
            if (typeof kata_ajax === 'undefined') {
                console.warn('kata_ajax object not found - poll functionality may be limited');
            }

            // Initialize
            this.init();
        }

        /**
         * Initialize polls on page
         * @returns {void}
         */
        init() {
            console.log('Kata Poll Frontend initialized (ES6)');
            
            this.initializePollsOnPage();
            this.bindEvents();
            this.addCustomEasing();
        }

        /**
         * Initialize all polls found on the page
         * @returns {void}
         */
        initializePollsOnPage() {
            $('.kata-poll-container').each((index, element) => {
                this.initializePoll($(element));
            });
        }

        /**
         * Initialize a single poll
         * @param {jQuery} $container - Poll container element
         * @returns {void}
         */
        initializePoll($container) {
            const pollId = $container.data('poll-id');
            if (!pollId) {
                console.warn('Poll container missing data-poll-id attribute');
                return;
            }

            this.checkVoteStatus($container, pollId);
            this.addAccessibilityFeatures($container);
        }

        /**
         * Bind event listeners
         * @returns {void}
         */
        bindEvents() {
            // Poll submission
            $(document).on('click', '.kata-poll-submit', (e) => {
                e.preventDefault();
                const $container = $(e.target).closest('.kata-poll-container');
                this.submitVote($container);
            });

            // Option selection
            $(document).on('change', '.kata-poll-option input[type="radio"]', (e) => {
                const $container = $(e.target).closest('.kata-poll-container');
                this.highlightSelection($container, $(e.target));
            });

            // Keyboard navigation
            $(document).on('keydown', '.kata-poll-option', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(e.target).find('input[type="radio"]').click();
                }
            });
        }

        /**
         * Submit poll vote via AJAX
         * @param {jQuery} $container - Poll container element
         * @returns {Promise<void>}
         */
        async submitVote($container) {
            const pollId = $container.data('poll-id');
            const $selectedOption = $container.find('input[name="poll_option"]:checked');
            
            // Validation
            if (!$selectedOption.length) {
                this.showMessage($container, 'Vui lòng chọn một tùy chọn trước khi bình chọn.', 'error');
                return;
            }

            const optionValue = $selectedOption.val();
            
            // Show loading state
            this.setLoadingState($container, true);

            try {
                const response = await this.submitVoteAjax(pollId, optionValue);
                this.handleVoteSuccess($container, response);
            } catch (error) {
                this.handleVoteError($container, error);
            } finally {
                this.setLoadingState($container, false);
            }
        }

        /**
         * Submit vote via AJAX
         * @param {string|number} pollId - Poll ID
         * @param {string} optionValue - Selected option value
         * @returns {Promise<Object>}
         */
        async submitVoteAjax(pollId, optionValue) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: this.config.ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'kata_submit_poll_vote',
                        poll_id: pollId,
                        option_value: optionValue,
                        nonce: this.config.nonce
                    },
                    success: resolve,
                    error: reject
                });
            });
        }

        /**
         * Handle successful vote submission
         * @param {jQuery} $container - Poll container
         * @param {Object} response - Server response
         * @returns {void}
         */
        handleVoteSuccess($container, response) {
            if (response.success) {
                this.showMessage($container, 'Cảm ơn bạn đã bình chọn!', 'success');
                
                // Load results after delay
                setTimeout(() => {
                    this.loadResults($container);
                }, this.config.resultDisplayDelay);
            } else {
                const message = response.data?.message || 'Có lỗi xảy ra khi bình chọn.';
                this.showMessage($container, message, 'error');
            }
        }

        /**
         * Handle vote submission error
         * @param {jQuery} $container - Poll container
         * @param {Object} error - Error object
         * @returns {void}
         */
        handleVoteError($container, error) {
            console.error('Poll vote error:', {
                status: error.status,
                statusText: error.statusText,
                responseText: error.responseText
            });
            
            const errorMessages = {
                400: 'Dữ liệu không hợp lệ.',
                403: 'Bạn không có quyền thực hiện hành động này.',
                500: 'Lỗi server. Vui lòng thử lại sau.',
                default: 'Không thể kết nối đến server. Vui lòng thử lại.'
            };
            
            const message = errorMessages[error.status] || errorMessages.default;
            this.showMessage($container, message, 'error');
        }

        /**
         * Load and display poll results
         * @param {jQuery} $container - Poll container
         * @returns {Promise<void>}
         */
        async loadResults($container) {
            const pollId = $container.data('poll-id');

            try {
                const response = await this.getResultsAjax(pollId);
                
                if (response.success) {
                    this.displayResults($container, response.data);
                } else {
                    console.error('Failed to load poll results:', response.data);
                }
            } catch (error) {
                console.error('Poll results error:', error);
            }
        }

        /**
         * Get poll results via AJAX
         * @param {string|number} pollId - Poll ID
         * @returns {Promise<Object>}
         */
        async getResultsAjax(pollId) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: this.config.ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'kata_get_poll_results',
                        poll_id: pollId,
                        nonce: this.config.nonce
                    },
                    success: resolve,
                    error: reject
                });
            });
        }

        /**
         * Display poll results with animation
         * @param {jQuery} $container - Poll container
         * @param {Object} data - Results data
         * @returns {void}
         */
        displayResults($container, data) {
            const $form = $container.find('.kata-poll-form');
            const $resultsContainer = $container.find('.kata-poll-results');
            
            // Hide form with animation
            $form.fadeOut(this.config.animationDuration, () => {
                const resultsHtml = this.buildResultsHtml(data);
                
                // Show results
                $resultsContainer.html(resultsHtml).fadeIn(this.config.animationDuration);
                
                // Animate progress bars
                setTimeout(() => {
                    this.animateProgressBars($container);
                }, 100);
            });
        }

        /**
         * Build results HTML with template literals
         * @param {Object} data - Results data
         * @returns {string} HTML string
         */
        buildResultsHtml(data) {
            const results = data.results || [];
            const totalVotes = data.total_votes || 0;
            
            const resultItems = results.map(result => {
                const percentage = totalVotes > 0 
                    ? Math.round((result.votes / totalVotes) * 100) 
                    : 0;
                
                return `
                    <div class="kata-poll-result-item">
                        <span class="kata-poll-option-text">${this.escapeHtml(result.option)}</span>
                        <span class="kata-poll-votes">(${result.votes} phiếu)</span>
                        <div class="kata-poll-progress-bar">
                            <div class="kata-poll-progress" 
                                 data-percentage="${percentage}" 
                                 style="width: 0%">
                            </div>
                        </div>
                        <span class="kata-poll-percentage">${percentage}%</span>
                    </div>
                `;
            }).join('');
            
            return `
                ${resultItems}
                <p class="kata-poll-total">Tổng số phiếu: ${totalVotes}</p>
            `;
        }

        /**
         * Animate progress bars
         * @param {jQuery} $container - Poll container
         * @returns {void}
         */
        animateProgressBars($container) {
            $container.find('.kata-poll-progress').each((index, element) => {
                const $bar = $(element);
                const percentage = $bar.data('percentage');
                
                $bar.animate({
                    width: `${percentage}%`
                }, this.config.progressAnimationDuration, 'easeOutQuart');
            });
        }

        /**
         * Set loading state
         * @param {jQuery} $container - Poll container
         * @param {boolean} loading - Loading state
         * @returns {void}
         */
        setLoadingState($container, loading) {
            const $submitBtn = $container.find('.kata-poll-submit');
            
            if (loading) {
                $container.addClass('kata-poll-loading');
                $submitBtn.prop('disabled', true).text('Đang xử lý...');
            } else {
                $container.removeClass('kata-poll-loading');
                $submitBtn.prop('disabled', false).text('Bình Chọn');
            }
        }

        /**
         * Check if user already voted
         * @param {jQuery} $container - Poll container
         * @param {string|number} pollId - Poll ID
         * @returns {Promise<void>}
         */
        async checkVoteStatus($container, pollId) {
            try {
                const response = await this.checkVoteStatusAjax(pollId);
                
                if (response.success && response.data.has_voted) {
                    this.loadResults($container);
                }
            } catch (error) {
                // Silently fail - user can still vote
                console.debug('Vote status check failed (non-critical):', error);
            }
        }

        /**
         * Check vote status via AJAX
         * @param {string|number} pollId - Poll ID
         * @returns {Promise<Object>}
         */
        async checkVoteStatusAjax(pollId) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: this.config.ajaxUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'kata_check_poll_vote_status',
                        poll_id: pollId,
                        nonce: this.config.nonce
                    },
                    success: resolve,
                    error: reject
                });
            });
        }

        /**
         * Highlight selected option
         * @param {jQuery} $container - Poll container
         * @param {jQuery} $selectedInput - Selected input element
         * @returns {void}
         */
        highlightSelection($container, $selectedInput) {
            // Remove previous selection
            $container.find('.kata-poll-option').removeClass('selected');
            
            // Add selection styling
            $selectedInput.closest('.kata-poll-option').addClass('selected');
            
            // Enable submit button
            $container.find('.kata-poll-submit').prop('disabled', false);
        }

        /**
         * Show message to user
         * @param {jQuery} $container - Poll container
         * @param {string} message - Message text
         * @param {string} type - Message type (info|error|success)
         * @returns {void}
         */
        showMessage($container, message, type = 'info') {
            // Remove existing messages
            $container.find('.kata-poll-message').remove();
            
            // Create message element
            const $message = $(`
                <div class="kata-poll-message kata-poll-${type}">
                    ${this.escapeHtml(message)}
                </div>
            `);
            
            // Insert and animate
            $container.prepend($message);
            $message.hide().slideDown(this.config.animationDuration);
            
            // Auto-remove (except success messages)
            if (type !== 'success') {
                setTimeout(() => {
                    $message.slideUp(this.config.animationDuration, () => {
                        $message.remove();
                    });
                }, this.config.messageAutoHideDelay);
            }
        }

        /**
         * Add accessibility features
         * @param {jQuery} $container - Poll container
         * @returns {void}
         */
        addAccessibilityFeatures($container) {
            // Add ARIA labels
            $container
                .attr('role', 'application')
                .attr('aria-label', 'Cuộc bình chọn tương tác');
            
            // Add keyboard navigation
            $container.find('.kata-poll-option')
                .attr('tabindex', '0')
                .attr('role', 'radio');
            
            // Add submit button accessibility
            $container.find('.kata-poll-submit')
                .attr('aria-describedby', 'poll-instructions');
            
            // Add instructions for screen readers
            if (!$container.find('#poll-instructions').length) {
                $container.append(`
                    <div id="poll-instructions" class="screen-reader-text">
                        Chọn một tùy chọn và nhấn nút Bình Chọn để gửi phiếu bầu.
                    </div>
                `);
            }
        }

        /**
         * Escape HTML to prevent XSS
         * @param {string} text - Text to escape
         * @returns {string} Escaped text
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }

        /**
         * Add custom jQuery easing function
         * @returns {void}
         */
        addCustomEasing() {
            if (typeof $.easing !== 'undefined') {
                $.easing.easeOutQuart = function(x, t, b, c, d) {
                    return -c * ((t=t/d-1)*t*t*t - 1) + b;
                };
            }
        }
    }

    // Create global instance
    const kataPollInstance = new KataPoll();
    window.KataPoll = kataPollInstance;

    // Backward compatibility function
    window.kataSubmitPoll = function(pollId) {
        const $container = $(`#${pollId}`);
        if ($container.length) {
            kataPollInstance.submitVote($container);
        }
    };

    // Initialize when DOM is ready
    $(document).ready(() => {
        // Instance already created and initialized
        console.log('Kata Poll ready on DOM load');
    });

    // Re-initialize polls for dynamic content (MutationObserver recommended)
    $(document).on('DOMNodeInserted', '.kata-poll-container', function() {
        kataPollInstance.initializePoll($(this));
    });

})(jQuery);

// Add inline accessibility styles
(() => {
    if (document.querySelector('#kata-poll-accessibility-styles')) {
        return; // Styles already added
    }

    const style = document.createElement('style');
    style.id = 'kata-poll-accessibility-styles';
    style.textContent = `
        .screen-reader-text {
            position: absolute !important;
            clip: rect(1px, 1px, 1px, 1px);
            width: 1px !important;
            height: 1px !important;
            overflow: hidden;
        }
        
        .kata-poll-message {
            padding: 15px;
            margin: 0 0 20px 0;
            border-radius: 8px;
            font-weight: 500;
            animation: slideInDown 0.3s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .kata-poll-info {
            background: var(--kata-info-bg, #d1ecf1);
            color: var(--kata-info-text, #0c5460);
            border: 1px solid var(--kata-info-border, #b8daff);
        }
        
        .kata-poll-error {
            background: var(--kata-error-bg, #f8d7da);
            color: var(--kata-error-text, #721c24);
            border: 1px solid var(--kata-error-border, #f5c2c7);
        }
        
        .kata-poll-success {
            background: var(--kata-success-bg, #d1e7dd);
            color: var(--kata-success-text, #0a3622);
            border: 1px solid var(--kata-success-border, #a3cfbb);
        }
        
        .kata-poll-option.selected {
            background: var(--kata-primary-color, #667eea) !important;
            color: #fff !important;
            border-color: var(--kata-primary-dark, #5a6fd8) !important;
            transform: translateX(5px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .kata-poll-option:focus {
            outline: 2px solid var(--kata-primary-color, #667eea);
            outline-offset: 2px;
        }
        
        .kata-poll-loading {
            position: relative;
            pointer-events: none;
        }
        
        .kata-poll-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    `;
    
    document.head.appendChild(style);
})();