/**
 * KATA User Interaction Frontend JavaScript
 * 
 * Handles form submission, rating interactions, and AJAX requests
 * 
 * @package KATA_SEO_Manager
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        initUserInteractions();
    });

    /**
     * Initialize user interaction functionality
     */
    function initUserInteractions() {
        // Star rating interaction
        initStarRatings();
        
        // Form submission
        initFormSubmission();
        
        // Reply functionality
        initReplySystem();
        
        // Load more interactions
        initLoadMore();
    }

    /**
     * Initialize star rating functionality
     */
    function initStarRatings() {
        $('.kata-star-rating').each(function() {
            const $container = $(this);
            const $stars = $container.find('.kata-star');
            const $input = $container.find('input[type="hidden"]');
            
            $stars.on('click', function() {
                const value = parseInt($(this).data('value'));
                $input.val(value);
                
                // Update visual state
                $stars.each(function(index) {
                    if (index < value) {
                        $(this).addClass('kata-star-active').removeClass('kata-star-inactive');
                    } else {
                        $(this).addClass('kata-star-inactive').removeClass('kata-star-active');
                    }
                });
                
                // Add feedback
                showRatingFeedback($container, value);
            });
            
            // Hover effects
            $stars.on('mouseenter', function() {
                const value = parseInt($(this).data('value'));
                $stars.each(function(index) {
                    if (index < value) {
                        $(this).addClass('kata-star-hover');
                    } else {
                        $(this).removeClass('kata-star-hover');
                    }
                });
            });
            
            $container.on('mouseleave', function() {
                $stars.removeClass('kata-star-hover');
            });
        });
    }

    /**
     * Show rating feedback
     */
    function showRatingFeedback($container, value) {
        // Remove existing feedback
        $container.find('.kata-rating-feedback').remove();
        
        const feedbackTexts = {
            1: 'Rất không hài lòng 😞',
            2: 'Không hài lòng 😕', 
            3: 'Bình thường 😐',
            4: 'Hài lòng 😊',
            5: 'Rất hài lòng 😍'
        };
        
        if (feedbackTexts[value]) {
            const $feedback = $('<div class="kata-rating-feedback">' + feedbackTexts[value] + '</div>');
            $container.append($feedback);
            
            // Auto hide after 2 seconds
            setTimeout(function() {
                $feedback.fadeOut();
            }, 2000);
        }
    }

    /**
     * Initialize form submission
     */
    function initFormSubmission() {
        $('.kata-interaction-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('.kata-submit-btn');
            const $submitText = $submitBtn.find('.kata-submit-text');
            const $submitLoading = $submitBtn.find('.kata-submit-loading');
            
            // Validate form
            if (!validateForm($form)) {
                return;
            }
            
            // Show loading state
            $submitBtn.prop('disabled', true);
            $submitText.hide();
            $submitLoading.show();
            
            // Prepare form data
            const formData = new FormData($form[0]);
            
            // Add rating values
            $form.find('.kata-star-rating input[type="hidden"]').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                if (value > 0) {
                    formData.set(name, value);
                }
            });
            
            // Submit via AJAX
            $.ajax({
                url: kataUserInteraction.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showMessage('success', response.data.message);
                        resetForm($form);
                        
                        // Reload interactions list if visible
                        const $container = $form.closest('.kata-user-interaction-container');
                        const $listContainer = $container.find('.kata-interaction-list-container');
                        if ($listContainer.length) {
                            loadInteractions($container);
                        }
                    } else {
                        showMessage('error', response.data || 'Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                },
                error: function() {
                    showMessage('error', 'Có lỗi kết nối. Vui lòng thử lại.');
                },
                complete: function() {
                    // Reset button state
                    $submitBtn.prop('disabled', false);
                    $submitText.show();
                    $submitLoading.hide();
                }
            });
        });
    }

    /**
     * Validate form before submission
     */
    function validateForm($form) {
        let isValid = true;
        const errors = [];
        
        // Check required fields
        $form.find('[required]').each(function() {
            const $field = $(this);
            if (!$field.val().trim()) {
                isValid = false;
                $field.addClass('kata-field-error');
                errors.push('Vui lòng điền đầy đủ thông tin bắt buộc.');
            } else {
                $field.removeClass('kata-field-error');
            }
        });
        
        // Check if at least one content field is filled
        const hasReviewContent = $form.find('[name="review_content"]').val().trim();
        const hasCommentContent = $form.find('[name="comment_content"]').val().trim();
        const hasRating = $form.find('.kata-star-rating input[type="hidden"]').filter(function() {
            return parseInt($(this).val()) > 0;
        }).length > 0;
        
        if (!hasReviewContent && !hasCommentContent && !hasRating) {
            isValid = false;
            errors.push('Vui lòng điền ít nhất một nội dung đánh giá, bình luận hoặc chọn số sao.');
        }
        
        // Email validation
        const $email = $form.find('[name="user_email"]');
        if ($email.length && $email.val()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test($email.val())) {
                isValid = false;
                $email.addClass('kata-field-error');
                errors.push('Email không hợp lệ.');
            }
        }
        
        // Show errors
        if (!isValid) {
            showMessage('error', errors.join('<br>'));
        }
        
        return isValid;
    }

    /**
     * Reset form after successful submission
     */
    function resetForm($form) {
        $form[0].reset();
        
        // Reset star ratings
        $form.find('.kata-star-rating .kata-star').removeClass('kata-star-active kata-star-inactive');
        $form.find('.kata-star-rating input[type="hidden"]').val('0');
        $form.find('.kata-rating-feedback').remove();
        
        // Remove error states
        $form.find('.kata-field-error').removeClass('kata-field-error');
    }

    /**
     * Show message to user
     */
    function showMessage(type, message) {
        // Remove existing messages
        $('.kata-message').remove();
        
        const $message = $('<div class="kata-message kata-message-' + type + '">' + message + '</div>');
        
        // Insert at the top of the form container
        $('.kata-interaction-form-container').prepend($message);
        
        // Auto hide success messages
        if (type === 'success') {
            setTimeout(function() {
                $message.fadeOut();
            }, 5000);
        }
        
        // Scroll to message
        $('html, body').animate({
            scrollTop: $message.offset().top - 100
        }, 500);
    }

    /**
     * Initialize reply system
     */
    function initReplySystem() {
        $(document).on('click', '.kata-reply-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const parentId = $btn.data('parent-id');
            const $item = $btn.closest('.kata-interaction-item');
            
            // Check if reply form already exists
            if ($item.find('.kata-reply-form').length) {
                $item.find('.kata-reply-form').slideToggle();
                return;
            }
            
            // Create reply form
            const $replyForm = createReplyForm(parentId);
            $item.append($replyForm);
            $replyForm.slideDown();
            
            // Focus on textarea
            $replyForm.find('textarea').focus();
        });
        
        // Handle reply form submission
        $(document).on('submit', '.kata-reply-form', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $textarea = $form.find('textarea');
            const content = $textarea.val().trim();
            
            if (!content) {
                showMessage('error', 'Vui lòng nhập nội dung trả lời.');
                return;
            }
            
            // Disable form during submission
            $form.find('button').prop('disabled', true);
            
            // Submit reply
            const formData = new FormData($form[0]);
            
            $.ajax({
                url: kataUserInteraction.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showMessage('success', 'Trả lời của bạn đã được gửi và sẽ hiển thị sau khi được duyệt.');
                        $form.slideUp();
                        
                        // Reload interactions
                        const $container = $form.closest('.kata-user-interaction-container');
                        loadInteractions($container);
                    } else {
                        showMessage('error', response.data || 'Có lỗi xảy ra khi gửi trả lời.');
                    }
                },
                error: function() {
                    showMessage('error', 'Có lỗi kết nối. Vui lòng thử lại.');
                },
                complete: function() {
                    $form.find('button').prop('disabled', false);
                }
            });
        });
        
        // Cancel reply
        $(document).on('click', '.kata-reply-cancel', function(e) {
            e.preventDefault();
            $(this).closest('.kata-reply-form').slideUp();
        });
    }

    /**
     * Create reply form HTML
     */
    function createReplyForm(parentId) {
        const currentUser = kataUserInteraction.currentUser || {};
        const userFields = currentUser.id ? '' : `
            <div class="kata-form-row">
                <input type="text" name="user_name" placeholder="Họ tên *" required style="width: 48%; margin-right: 4%;">
                <input type="email" name="user_email" placeholder="Email *" required style="width: 48%;">
            </div>
        `;
        
        return $(`
            <div class="kata-reply-form" style="display: none; margin-top: 15px; padding: 15px; background: #f9f9f9; border-radius: 6px;">
                <form>
                    ${userFields}
                    <textarea name="comment_content" rows="3" placeholder="Nhập trả lời của bạn..." required style="width: 100%; margin-bottom: 10px;"></textarea>
                    <div class="kata-reply-actions">
                        <button type="submit" class="kata-reply-submit">Gửi trả lời</button>
                        <button type="button" class="kata-reply-cancel">Hủy</button>
                    </div>
                    <input type="hidden" name="action" value="kata_submit_user_interaction">
                    <input type="hidden" name="post_id" value="${kataUserInteraction.postId}">
                    <input type="hidden" name="parent_id" value="${parentId}">
                    <input type="hidden" name="interaction_type" value="comment">
                    <input type="hidden" name="nonce" value="${kataUserInteraction.nonce}">
                </form>
            </div>
        `);
    }

    /**
     * Initialize load more functionality
     */
    function initLoadMore() {
        $(document).on('click', '.kata-load-more-btn', function(e) {
            e.preventDefault();
            
            const $btn = $(this);
            const $container = $btn.closest('.kata-user-interaction-container');
            const page = parseInt($btn.data('page')) + 1;
            
            $btn.text('Đang tải...').prop('disabled', true);
            
            loadInteractions($container, page, true);
        });
    }

    /**
     * Load interactions via AJAX
     */
    function loadInteractions($container, page = 1, append = false) {
        const postId = $container.data('post-id');
        
        $.ajax({
            url: kataUserInteraction.ajaxUrl,
            type: 'POST',
            data: {
                action: 'kata_load_user_interactions',
                post_id: postId,
                page: page,
                per_page: 10
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const $listContainer = $container.find('.kata-interaction-list-container');
                    const $itemsContainer = $listContainer.find('.kata-interactions-items');
                    
                    if (append) {
                        // Append new items
                        data.interactions.forEach(function(interaction) {
                            $itemsContainer.append(createInteractionHTML(interaction));
                        });
                        
                        // Update or remove load more button
                        const $loadMoreBtn = $listContainer.find('.kata-load-more-btn');
                        if (page >= data.total_pages) {
                            $loadMoreBtn.remove();
                        } else {
                            $loadMoreBtn.data('page', page).text('Xem thêm').prop('disabled', false);
                        }
                    } else {
                        // Replace entire list
                        $listContainer.html(createInteractionListHTML(data));
                    }
                }
            },
            error: function() {
                showMessage('error', 'Có lỗi khi tải danh sách đánh giá.');
            }
        });
    }

    /**
     * Create interaction item HTML
     */
    function createInteractionHTML(interaction) {
        const ratingHTML = interaction.overall_rating > 0 ? `
            <div class="kata-interaction-rating">
                <div class="kata-rating-stars">${createStarsHTML(interaction.overall_rating)}</div>
                <span class="kata-rating-value">${interaction.overall_rating}/5</span>
            </div>
        ` : '';
        
        const reviewTitleHTML = interaction.review_title ? `
            <h5 class="kata-review-title">${escapeHtml(interaction.review_title)}</h5>
        ` : '';
        
        const reviewContentHTML = interaction.review_content ? `
            <div class="kata-review-content">${escapeHtml(interaction.review_content).replace(/\n/g, '<br>')}</div>
        ` : '';
        
        const prosConsHTML = (interaction.review_pros || interaction.review_cons) ? `
            <div class="kata-review-proscons">
                ${interaction.review_pros ? `<div class="kata-pros"><strong>👍 Điểm tốt:</strong> ${escapeHtml(interaction.review_pros)}</div>` : ''}
                ${interaction.review_cons ? `<div class="kata-cons"><strong>👎 Điểm chưa tốt:</strong> ${escapeHtml(interaction.review_cons)}</div>` : ''}
            </div>
        ` : '';
        
        const commentHTML = interaction.comment_content ? `
            <div class="kata-comment-content">${escapeHtml(interaction.comment_content).replace(/\n/g, '<br>')}</div>
        ` : '';
        
        return `
            <div class="kata-interaction-item" data-id="${interaction.id}">
                <div class="kata-interaction-header">
                    <div class="kata-user-info">
                        <strong class="kata-user-name">${escapeHtml(interaction.user_name)}</strong>
                        <span class="kata-interaction-date">${formatDate(interaction.created_at)}</span>
                    </div>
                    ${ratingHTML}
                </div>
                <div class="kata-interaction-content">
                    ${reviewTitleHTML}
                    ${reviewContentHTML}
                    ${prosConsHTML}
                    ${commentHTML}
                </div>
                <div class="kata-interaction-actions">
                    <button class="kata-reply-btn" data-parent-id="${interaction.id}">Trả lời</button>
                </div>
            </div>
        `;
    }

    /**
     * Create stars HTML
     */
    function createStarsHTML(rating) {
        let html = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                html += '<span class="kata-star kata-star-full">⭐</span>';
            } else if (i - 0.5 <= rating) {
                html += '<span class="kata-star kata-star-half">⭐</span>';
            } else {
                html += '<span class="kata-star kata-star-empty">☆</span>';
            }
        }
        return html;
    }

    /**
     * Format date for display
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN');
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Make functions available globally if needed
    window.kataUserInteractionJS = {
        loadInteractions: loadInteractions,
        showMessage: showMessage
    };

})(jQuery);