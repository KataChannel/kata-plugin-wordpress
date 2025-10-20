/**
 * KATA User Interaction Frontend JavaScript - ES6 Refactored
 * 
 * Handles form submission, rating interactions, and AJAX requests
 * 
 * @package KATA_SEO_Manager
 * @since 2.2.0
 * @version 2.2.0
 */

(function($) {
    'use strict';

    /**
     * User Interaction Controller Class
     * @class KataUserInteraction
     */
    class KataUserInteraction {
        /**
         * Initialize User Interaction Controller
         * @constructor
         */
        constructor() {
            this.config = {
                feedbackDuration: 2000,
                messageDuration: 5000,
                scrollOffset: 100,
                scrollDuration: 500,
                perPage: 10
            };
            this.init();
        }

        /**
         * Initialize all interaction functionality
         * @returns {void}
         */
        init() {
            this.initStarRatings();
            this.initFormSubmission();
            this.initReplySystem();
            this.initLoadMore();
        }

        /**
         * Initialize star rating functionality
         * @returns {void}
         */
        initStarRatings() {
            $('.kata-star-rating').each((index, element) => {
                const $container = $(element);
                const $stars = $container.find('.kata-star');
                const $input = $container.find('input[type="hidden"]');
                
                $stars.on('click', (e) => {
                    const value = parseInt($(e.currentTarget).data('value'));
                    $input.val(value);
                    
                    $stars.each((idx, star) => {
                        const $star = $(star);
                        if (idx < value) {
                            $star.addClass('kata-star-active').removeClass('kata-star-inactive');
                        } else {
                            $star.addClass('kata-star-inactive').removeClass('kata-star-active');
                        }
                    });
                    
                    this.showRatingFeedback($container, value);
                });
                
                $stars.on('mouseenter', (e) => {
                    const value = parseInt($(e.currentTarget).data('value'));
                    $stars.each((idx, star) => {
                        $(star)[idx < value ? 'addClass' : 'removeClass']('kata-star-hover');
                    });
                });
                
                $container.on('mouseleave', () => {
                    $stars.removeClass('kata-star-hover');
                });
            });
        }

        /**
         * Show rating feedback
         * @param {jQuery} $container - Rating container
         * @param {number} value - Rating value
         * @returns {void}
         */
        showRatingFeedback($container, value) {
            $container.find('.kata-rating-feedback').remove();
            
            const feedbackTexts = {
                1: 'Rất không hài lòng 😞',
                2: 'Không hài lòng 😕', 
                3: 'Bình thường 😐',
                4: 'Hài lòng 😊',
                5: 'Rất hài lòng 😍'
            };
            
            if (feedbackTexts[value]) {
                const $feedback = $(`<div class="kata-rating-feedback">${feedbackTexts[value]}</div>`);
                $container.append($feedback);
                setTimeout(() => $feedback.fadeOut(), this.config.feedbackDuration);
            }
        }

        /**
         * Initialize form submission
         * @returns {void}
         */
        initFormSubmission() {
            $('.kata-interaction-form').on('submit', (e) => {
                e.preventDefault();
                
                const $form = $(e.target);
                const $submitBtn = $form.find('.kata-submit-btn');
                const $submitText = $submitBtn.find('.kata-submit-text');
                const $submitLoading = $submitBtn.find('.kata-submit-loading');
                
                if (!this.validateForm($form)) return;
                
                $submitBtn.prop('disabled', true);
                $submitText.hide();
                $submitLoading.show();
                
                const formData = new FormData($form[0]);
                
                $form.find('.kata-star-rating input[type="hidden"]').each(function() {
                    const name = $(this).attr('name');
                    const value = $(this).val();
                    if (value > 0) formData.set(name, value);
                });
                
                $.ajax({
                    url: kataUserInteraction.ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.success) {
                            this.showMessage('success', response.data.message);
                            this.resetForm($form);
                            
                            const $container = $form.closest('.kata-user-interaction-container');
                            const $listContainer = $container.find('.kata-interaction-list-container');
                            if ($listContainer.length) {
                                this.loadInteractions($container);
                            }
                        } else {
                            this.showMessage('error', response.data || 'Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    },
                    error: () => {
                        this.showMessage('error', 'Có lỗi kết nối. Vui lòng thử lại.');
                    },
                    complete: () => {
                        $submitBtn.prop('disabled', false);
                        $submitText.show();
                        $submitLoading.hide();
                    }
                });
            });
        }

        /**
         * Validate form before submission
         * @param {jQuery} $form - Form element
         * @returns {boolean} Validation result
         */
        validateForm($form) {
            let isValid = true;
            const errors = [];
            
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
            
            const hasReviewContent = $form.find('[name="review_content"]').val().trim();
            const hasCommentContent = $form.find('[name="comment_content"]').val().trim();
            const hasRating = $form.find('.kata-star-rating input[type="hidden"]')
                .filter(function() { return parseInt($(this).val()) > 0; }).length > 0;
            
            if (!hasReviewContent && !hasCommentContent && !hasRating) {
                isValid = false;
                errors.push('Vui lòng điền ít nhất một nội dung đánh giá, bình luận hoặc chọn số sao.');
            }
            
            const $email = $form.find('[name="user_email"]');
            if ($email.length && $email.val()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test($email.val())) {
                    isValid = false;
                    $email.addClass('kata-field-error');
                    errors.push('Email không hợp lệ.');
                }
            }
            
            if (!isValid) {
                this.showMessage('error', errors.join('<br>'));
            }
            
            return isValid;
        }

        /**
         * Reset form after successful submission
         * @param {jQuery} $form - Form element
         * @returns {void}
         */
        resetForm($form) {
            $form[0].reset();
            $form.find('.kata-star-rating .kata-star').removeClass('kata-star-active kata-star-inactive');
            $form.find('.kata-star-rating input[type="hidden"]').val('0');
            $form.find('.kata-rating-feedback').remove();
            $form.find('.kata-field-error').removeClass('kata-field-error');
        }

        /**
         * Show message to user
         * @param {string} type - Message type (success|error)
         * @param {string} message - Message content
         * @returns {void}
         */
        showMessage(type, message) {
            $('.kata-message').remove();
            
            const $message = $(`<div class="kata-message kata-message-${type}">${message}</div>`);
            $('.kata-interaction-form-container').prepend($message);
            
            if (type === 'success') {
                setTimeout(() => $message.fadeOut(), this.config.messageDuration);
            }
            
            $('html, body').animate({
                scrollTop: $message.offset().top - this.config.scrollOffset
            }, this.config.scrollDuration);
        }

        /**
         * Initialize reply system
         * @returns {void}
         */
        initReplySystem() {
            $(document).on('click', '.kata-reply-btn', (e) => {
                e.preventDefault();
                
                const $btn = $(e.currentTarget);
                const parentId = $btn.data('parent-id');
                const $item = $btn.closest('.kata-interaction-item');
                
                if ($item.find('.kata-reply-form').length) {
                    $item.find('.kata-reply-form').slideToggle();
                    return;
                }
                
                const $replyForm = this.createReplyForm(parentId);
                $item.append($replyForm);
                $replyForm.slideDown();
                $replyForm.find('textarea').focus();
            });
            
            $(document).on('submit', '.kata-reply-form', (e) => {
                e.preventDefault();
                
                const $form = $(e.target);
                const content = $form.find('textarea').val().trim();
                
                if (!content) {
                    this.showMessage('error', 'Vui lòng nhập nội dung trả lời.');
                    return;
                }
                
                $form.find('button').prop('disabled', true);
                const formData = new FormData($form[0]);
                
                $.ajax({
                    url: kataUserInteraction.ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: (response) => {
                        if (response.success) {
                            this.showMessage('success', 'Trả lời của bạn đã được gửi và sẽ hiển thị sau khi được duyệt.');
                            $form.slideUp();
                            const $container = $form.closest('.kata-user-interaction-container');
                            this.loadInteractions($container);
                        } else {
                            this.showMessage('error', response.data || 'Có lỗi xảy ra khi gửi trả lời.');
                        }
                    },
                    error: () => {
                        this.showMessage('error', 'Có lỗi kết nối. Vui lòng thử lại.');
                    },
                    complete: () => {
                        $form.find('button').prop('disabled', false);
                    }
                });
            });
            
            $(document).on('click', '.kata-reply-cancel', (e) => {
                e.preventDefault();
                $(e.target).closest('.kata-reply-form').slideUp();
            });
        }

        /**
         * Create reply form HTML
         * @param {number} parentId - Parent interaction ID
         * @returns {jQuery} Reply form element
         */
        createReplyForm(parentId) {
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
         * @returns {void}
         */
        initLoadMore() {
            $(document).on('click', '.kata-load-more-btn', (e) => {
                e.preventDefault();
                
                const $btn = $(e.currentTarget);
                const $container = $btn.closest('.kata-user-interaction-container');
                const page = parseInt($btn.data('page')) + 1;
                
                $btn.text('Đang tải...').prop('disabled', true);
                
                this.loadInteractions($container, page, true);
            });
        }

        /**
         * Load interactions via AJAX
         * @param {jQuery} $container - Container element
         * @param {number} page - Page number
         * @param {boolean} append - Append or replace content
         * @returns {void}
         */
        loadInteractions($container, page = 1, append = false) {
            const postId = $container.data('post-id');
            
            $.ajax({
                url: kataUserInteraction.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_load_user_interactions',
                    post_id: postId,
                    page: page,
                    per_page: this.config.perPage
                },
                success: (response) => {
                    if (response.success) {
                        const data = response.data;
                        const $listContainer = $container.find('.kata-interaction-list-container');
                        const $itemsContainer = $listContainer.find('.kata-interactions-items');
                        
                        if (append) {
                            data.interactions.forEach((interaction) => {
                                $itemsContainer.append(this.createInteractionHTML(interaction));
                            });
                            
                            const $loadMoreBtn = $listContainer.find('.kata-load-more-btn');
                            if (page >= data.total_pages) {
                                $loadMoreBtn.remove();
                            } else {
                                $loadMoreBtn.data('page', page).text('Xem thêm').prop('disabled', false);
                            }
                        } else {
                            $listContainer.html(this.createInteractionListHTML(data));
                        }
                    }
                },
                error: () => {
                    this.showMessage('error', 'Có lỗi khi tải danh sách đánh giá.');
                }
            });
        }

        /**
         * Create interaction item HTML
         * @param {Object} interaction - Interaction data
         * @returns {string} HTML string
         */
        createInteractionHTML(interaction) {
            const ratingHTML = interaction.overall_rating > 0 ? `
                <div class="kata-interaction-rating">
                    <div class="kata-rating-stars">${this.createStarsHTML(interaction.overall_rating)}</div>
                    <span class="kata-rating-value">${interaction.overall_rating}/5</span>
                </div>
            ` : '';
            
            const reviewTitleHTML = interaction.review_title ? `
                <h5 class="kata-review-title">${this.escapeHtml(interaction.review_title)}</h5>
            ` : '';
            
            const reviewContentHTML = interaction.review_content ? `
                <div class="kata-review-content">${this.escapeHtml(interaction.review_content).replace(/\n/g, '<br>')}</div>
            ` : '';
            
            const prosConsHTML = (interaction.review_pros || interaction.review_cons) ? `
                <div class="kata-review-proscons">
                    ${interaction.review_pros ? `<div class="kata-pros"><strong>👍 Điểm tốt:</strong> ${this.escapeHtml(interaction.review_pros)}</div>` : ''}
                    ${interaction.review_cons ? `<div class="kata-cons"><strong>👎 Điểm chưa tốt:</strong> ${this.escapeHtml(interaction.review_cons)}</div>` : ''}
                </div>
            ` : '';
            
            const commentHTML = interaction.comment_content ? `
                <div class="kata-comment-content">${this.escapeHtml(interaction.comment_content).replace(/\n/g, '<br>')}</div>
            ` : '';
            
            return `
                <div class="kata-interaction-item" data-id="${interaction.id}">
                    <div class="kata-interaction-header">
                        <div class="kata-user-info">
                            <strong class="kata-user-name">${this.escapeHtml(interaction.user_name)}</strong>
                            <span class="kata-interaction-date">${this.formatDate(interaction.created_at)}</span>
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
         * @param {number} rating - Rating value
         * @returns {string} HTML string
         */
        createStarsHTML(rating) {
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
         * @param {string} dateString - Date string
         * @returns {string} Formatted date
         */
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN');
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
            return text.replace(/[&<>"']/g, (m) => map[m]);
        }
    }

    // Initialize on document ready
    $(document).ready(() => {
        new KataUserInteraction();
    });

    // Make class available globally for backward compatibility
    window.KataUserInteraction = KataUserInteraction;

})(jQuery);