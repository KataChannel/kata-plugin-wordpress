/**
 * Poll Frontend JavaScript
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Global poll functionality
    window.KataPoll = {
        // Configuration
        config: {
            ajaxUrl: kata_ajax?.url || '/wp-admin/admin-ajax.php',
            nonce: kata_ajax?.nonce || '',
            animationDuration: 300,
            resultDisplayDelay: 500
        },

        // Initialize all polls on page
        init: function() {
            console.log('Kata Poll Frontend initialized');
            
            // Check if required dependencies exist
            if (typeof kata_ajax === 'undefined') {
                console.warn('kata_ajax object not found - poll functionality may be limited');
            }

            // Initialize existing polls
            this.initializePollsOnPage();
            
            // Set up event listeners
            this.bindEvents();
        },

        // Initialize all polls found on the page
        initializePollsOnPage: function() {
            $('.kata-poll-container').each(function() {
                KataPoll.initializePoll($(this));
            });
        },

        // Initialize a single poll
        initializePoll: function($container) {
            var pollId = $container.data('poll-id');
            if (!pollId) return;

            // Check if user already voted
            this.checkVoteStatus($container, pollId);
            
            // Add accessibility improvements
            this.addAccessibilityFeatures($container);
        },

        // Bind event listeners
        bindEvents: function() {
            // Poll submission
            $(document).on('click', '.kata-poll-submit', function(e) {
                e.preventDefault();
                var $container = $(this).closest('.kata-poll-container');
                KataPoll.submitVote($container);
            });

            // Option selection
            $(document).on('change', '.kata-poll-option input[type="radio"]', function() {
                var $container = $(this).closest('.kata-poll-container');
                KataPoll.highlightSelection($container, $(this));
            });

            // Keyboard navigation
            $(document).on('keydown', '.kata-poll-option', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).find('input[type="radio"]').click();
                }
            });
        },

        // Submit poll vote
        submitVote: function($container) {
            var pollId = $container.data('poll-id');
            var $selectedOption = $container.find('input[name="poll_option"]:checked');
            
            if (!$selectedOption.length) {
                this.showMessage($container, 'Vui lòng chọn một tùy chọn trước khi bình chọn.', 'error');
                return;
            }

            var optionValue = $selectedOption.val();
            
            // Show loading state
            this.setLoadingState($container, true);
            
            // Submit via AJAX
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
                success: function(response) {
                    KataPoll.handleVoteSuccess($container, response);
                },
                error: function(xhr, status, error) {
                    KataPoll.handleVoteError($container, xhr, status, error);
                },
                complete: function() {
                    KataPoll.setLoadingState($container, false);
                }
            });
        },

        // Handle successful vote submission
        handleVoteSuccess: function($container, response) {
            if (response.success) {
                this.showMessage($container, 'Cảm ơn bạn đã bình chọn!', 'success');
                
                // Load and display results after a short delay
                setTimeout(function() {
                    KataPoll.loadResults($container);
                }, this.config.resultDisplayDelay);
                
            } else {
                var message = response.data?.message || 'Có lỗi xảy ra khi bình chọn.';
                this.showMessage($container, message, 'error');
            }
        },

        // Handle vote submission error
        handleVoteError: function($container, xhr, status, error) {
            console.error('Poll vote error:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            
            var message = 'Không thể kết nối đến server. Vui lòng thử lại.';
            if (xhr.status === 400) {
                message = 'Dữ liệu không hợp lệ.';
            } else if (xhr.status === 403) {
                message = 'Bạn không có quyền thực hiện hành động này.';
            } else if (xhr.status === 500) {
                message = 'Lỗi server. Vui lòng thử lại sau.';
            }
            
            this.showMessage($container, message, 'error');
        },

        // Load and display poll results
        loadResults: function($container) {
            var pollId = $container.data('poll-id');
            
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'kata_get_poll_results',
                    poll_id: pollId,
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KataPoll.displayResults($container, response.data);
                    } else {
                        console.error('Failed to load poll results:', response.data);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Poll results error:', error);
                }
            });
        },

        // Display poll results
        displayResults: function($container, data) {
            var $form = $container.find('.kata-poll-form');
            var $resultsContainer = $container.find('.kata-poll-results');
            
            // Hide form with animation
            $form.fadeOut(this.config.animationDuration, function() {
                // Build results HTML
                var resultsHtml = KataPoll.buildResultsHtml(data);
                
                // Show results
                $resultsContainer.html(resultsHtml).fadeIn(KataPoll.config.animationDuration);
                
                // Animate progress bars
                setTimeout(function() {
                    KataPoll.animateProgressBars($container);
                }, 100);
            });
        },

        // Build results HTML
        buildResultsHtml: function(data) {
            var html = '';
            var results = data.results || [];
            var totalVotes = data.total_votes || 0;
            
            results.forEach(function(result, index) {
                var percentage = totalVotes > 0 ? Math.round((result.votes / totalVotes) * 100) : 0;
                
                html += '<div class="kata-poll-result-item">';
                html += '<span class="kata-poll-option-text">' + escapeHtml(result.option) + '</span>';
                html += '<span class="kata-poll-votes">(' + result.votes + ' phiếu)</span>';
                html += '<div class="kata-poll-progress-bar">';
                html += '<div class="kata-poll-progress" data-percentage="' + percentage + '" style="width: 0%"></div>';
                html += '</div>';
                html += '<span class="kata-poll-percentage">' + percentage + '%</span>';
                html += '</div>';
            });
            
            html += '<p class="kata-poll-total">Tổng số phiếu: ' + totalVotes + '</p>';
            
            return html;
        },

        // Animate progress bars
        animateProgressBars: function($container) {
            $container.find('.kata-poll-progress').each(function() {
                var $bar = $(this);
                var percentage = $bar.data('percentage');
                
                // Animate width
                $bar.animate({
                    width: percentage + '%'
                }, 800, 'easeOutQuart');
            });
        },

        // Set loading state
        setLoadingState: function($container, loading) {
            if (loading) {
                $container.addClass('kata-poll-loading');
                $container.find('.kata-poll-submit').prop('disabled', true).text('Đang xử lý...');
            } else {
                $container.removeClass('kata-poll-loading');
                $container.find('.kata-poll-submit').prop('disabled', false).text('Bình Chọn');
            }
        },

        // Check if user already voted
        checkVoteStatus: function($container, pollId) {
            $.ajax({
                url: this.config.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'kata_check_poll_vote_status',
                    poll_id: pollId,
                    nonce: this.config.nonce
                },
                success: function(response) {
                    if (response.success && response.data.has_voted) {
                        // User already voted, show results
                        KataPoll.loadResults($container);
                    }
                },
                error: function() {
                    // Silently fail - user can still vote
                }
            });
        },

        // Highlight selected option
        highlightSelection: function($container, $selectedInput) {
            // Remove previous selection styling
            $container.find('.kata-poll-option').removeClass('selected');
            
            // Add selection styling to current option
            $selectedInput.closest('.kata-poll-option').addClass('selected');
            
            // Enable submit button
            $container.find('.kata-poll-submit').prop('disabled', false);
        },

        // Show message to user
        showMessage: function($container, message, type) {
            type = type || 'info';
            
            // Remove existing messages
            $container.find('.kata-poll-message').remove();
            
            // Create message element
            var $message = $('<div class="kata-poll-message kata-poll-' + type + '">' + escapeHtml(message) + '</div>');
            
            // Insert message
            $container.prepend($message);
            
            // Animate in
            $message.hide().slideDown(this.config.animationDuration);
            
            // Auto-remove after delay (except for success messages)
            if (type !== 'success') {
                setTimeout(function() {
                    $message.slideUp(KataPoll.config.animationDuration, function() {
                        $message.remove();
                    });
                }, 5000);
            }
        },

        // Add accessibility features
        addAccessibilityFeatures: function($container) {
            // Add ARIA labels
            $container.attr('role', 'application').attr('aria-label', 'Cuộc bình chọn tương tác');
            
            // Add keyboard navigation hints
            $container.find('.kata-poll-option').attr('tabindex', '0').attr('role', 'radio');
            
            // Add submit button accessibility
            $container.find('.kata-poll-submit').attr('aria-describedby', 'poll-instructions');
            
            // Add instructions for screen readers
            if (!$container.find('#poll-instructions').length) {
                $container.append('<div id="poll-instructions" class="screen-reader-text">Chọn một tùy chọn và nhấn nút Bình Chọn để gửi phiếu bầu.</div>');
            }
        }
    };

    // Global function for backward compatibility
    window.kataSubmitPoll = function(pollId) {
        var $container = $('#' + pollId);
        if ($container.length) {
            KataPoll.submitVote($container);
        }
    };

    // Helper function to escape HTML
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Add custom easing function
    $.easing.easeOutQuart = function(x, t, b, c, d) {
        return -c * ((t=t/d-1)*t*t*t - 1) + b;
    };

    // Initialize when DOM is ready
    $(document).ready(function() {
        KataPoll.init();
    });

    // Re-initialize polls for dynamic content
    $(document).on('DOMNodeInserted', '.kata-poll-container', function() {
        KataPoll.initializePoll($(this));
    });

})(jQuery);

// Add screen reader only CSS if not already present
if (!document.querySelector('#kata-poll-accessibility-styles')) {
    var style = document.createElement('style');
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
        }
        
        .kata-poll-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #b8daff;
        }
        
        .kata-poll-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c2c7;
        }
        
        .kata-poll-success {
            background: #d1e7dd;
            color: #0a3622;
            border: 1px solid #a3cfbb;
        }
        
        .kata-poll-option.selected {
            background: #667eea !important;
            color: #fff !important;
            border-color: #5a6fd8 !important;
            transform: translateX(5px);
        }
        
        .kata-poll-option:focus {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }
    `;
    document.head.appendChild(style);
}