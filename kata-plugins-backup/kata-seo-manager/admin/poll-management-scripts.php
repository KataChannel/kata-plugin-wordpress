<script>
jQuery(document).ready(function($) {
    
    // ================================================
    // MODAL MANAGEMENT
    // ================================================
    
    const modal = $('#poll-modal');
    const modalTitleText = $('#modal-title-text');
    const modalTitleIcon = $('#modal-title .dashicons');
    const formAction = $('#form-action');
    const pollIdField = $('#poll-id');
    const statusRow = $('#status-row');
    const submitBtnText = $('#submit-btn-text');
    
    // Open modal for creating new poll
    $('#create-poll-btn, #create-first-poll').click(function() {
        openModal('create');
    });
    
    // Open modal for editing poll
    $(document).on('click', '.edit-poll-btn', function() {
        const pollId = $(this).data('poll-id');
        openModal('edit', pollId);
    });
    
    // Close modal
    $('#close-modal, #cancel-btn, .kata-modal-overlay').click(function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Close modal with ESC key
    $(document).keydown(function(e) {
        if (e.key === 'Escape' && modal.hasClass('active')) {
            closeModal();
        }
    });
    
    /**
     * Open modal for create or edit
     */
    function openModal(mode, pollId = null) {
        if (mode === 'create') {
            modalTitleText.text('Tạo Poll Mới');
            modalTitleIcon.removeClass('dashicons-edit').addClass('dashicons-plus-alt');
            formAction.val('create');
            pollIdField.val('');
            statusRow.hide();
            submitBtnText.text('Tạo Poll');
            resetForm();
            initializeOptions(2); // Start with 2 options
        } else {
            modalTitleText.text('Chỉnh Sửa Poll');
            modalTitleIcon.removeClass('dashicons-plus-alt').addClass('dashicons-edit');
            formAction.val('update');
            pollIdField.val(pollId);
            statusRow.show();
            submitBtnText.text('Cập nhật Poll');
            loadPollData(pollId);
        }
        
        modal.addClass('active');
        $('body').css('overflow', 'hidden');
        $('#poll_title').focus();
    }
    
    /**
     * Close modal
     */
    function closeModal() {
        modal.removeClass('active');
        $('body').css('overflow', '');
        setTimeout(resetForm, 300);
    }
    
    /**
     * Reset form to initial state
     */
    function resetForm() {
        $('#poll-form')[0].reset();
        $('#poll-options-container').empty();
    }
    
    // ================================================
    // AJAX LOAD POLL DATA FOR EDITING
    // ================================================
    
    /**
     * Load poll data via AJAX
     */
    function loadPollData(pollId) {
        // Show loading state
        const loadingHTML = '<div class="loading-message" style="text-align: center; padding: 40px;"><span class="dashicons dashicons-update" style="font-size: 32px; animation: spin 1s linear infinite;"></span><p>Đang tải dữ liệu...</p></div>';
        $('#poll-options-container').html(loadingHTML);
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_get_poll_for_edit',
                poll_id: pollId,
                nonce: '<?php echo wp_create_nonce('kata_poll_edit'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    populateEditForm(response.data);
                } else {
                    alert('Lỗi: ' + (response.data || 'Không thể tải dữ liệu poll'));
                    closeModal();
                }
            },
            error: function() {
                alert('Lỗi kết nối. Vui lòng thử lại!');
                closeModal();
            }
        });
    }
    
    /**
     * Populate form with poll data
     */
    function populateEditForm(data) {
        $('#poll_title').val(data.poll.poll_title);
        $('#poll_description').val(data.poll.poll_description);
        $('#poll_question').val(data.poll.poll_question);
        $('#poll_status').val(data.poll.status);
        
        // Populate options
        $('#poll-options-container').empty();
        if (data.options && data.options.length > 0) {
            data.options.forEach(function(option, index) {
                addOptionRow(index + 1, option);
            });
        } else {
            initializeOptions(2);
        }
    }
    
    // ================================================
    // POLL OPTIONS MANAGEMENT
    // ================================================
    
    /**
     * Initialize options with default count
     */
    function initializeOptions(count) {
        $('#poll-options-container').empty();
        for (let i = 1; i <= count; i++) {
            addOptionRow(i, '');
        }
    }
    
    /**
     * Add new option row
     */
    function addOptionRow(number, value = '') {
        const container = $('#poll-options-container');
        const showDelete = container.children().length >= 2 || number > 2;
        
        const row = $(`
            <div class="option-row">
                <span class="option-number">${number}</span>
                <input type="text" name="poll_options[]" value="${escapeHtml(value)}" 
                       placeholder="Nhập tùy chọn ${number}" 
                       class="form-control option-input">
                <button type="button" class="remove-option-btn" ${!showDelete ? 'style="display:none;"' : ''}>
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </div>
        `);
        
        container.append(row);
    }
    
    /**
     * Add option button click
     */
    $('#add-option-btn').click(function() {
        const container = $('#poll-options-container');
        const currentCount = container.find('.option-row').length;
        addOptionRow(currentCount + 1, '');
        updateOptionNumbers();
        
        // Show all delete buttons
        container.find('.remove-option-btn').show();
    });
    
    /**
     * Remove option button click
     */
    $(document).on('click', '.remove-option-btn', function() {
        const container = $('#poll-options-container');
        const rows = container.find('.option-row');
        
        if (rows.length > 2) {
            $(this).closest('.option-row').fadeOut(300, function() {
                $(this).remove();
                updateOptionNumbers();
            });
        } else {
            showNotification('⚠️ Cần ít nhất 2 tùy chọn!', 'warning');
        }
    });
    
    /**
     * Update option numbers after add/remove
     */
    function updateOptionNumbers() {
        const container = $('#poll-options-container');
        container.find('.option-row').each(function(index) {
            $(this).find('.option-number').text(index + 1);
            $(this).find('.option-input').attr('placeholder', 'Nhập tùy chọn ' + (index + 1));
        });
        
        // Hide delete button for first two options if only 2 exist
        if (container.find('.option-row').length === 2) {
            container.find('.option-row:lt(2) .remove-option-btn').hide();
        }
    }
    
    // ================================================
    // FORM VALIDATION
    // ================================================
    
    $('#poll-form').submit(function(e) {
        const title = $('#poll_title').val().trim();
        const question = $('#poll_question').val().trim();
        const options = [];
        
        $('.option-input').each(function() {
            const val = $(this).val().trim();
            if (val) {
                options.push(val);
            }
        });
        
        // Validation
        if (!title) {
            e.preventDefault();
            showNotification('⚠️ Vui lòng nhập tiêu đề poll!', 'warning');
            $('#poll_title').focus();
            return false;
        }
        
        if (!question) {
            e.preventDefault();
            showNotification('⚠️ Vui lòng nhập câu hỏi poll!', 'warning');
            $('#poll_question').focus();
            return false;
        }
        
        if (options.length < 2) {
            e.preventDefault();
            showNotification('⚠️ Cần ít nhất 2 tùy chọn có nội dung!', 'warning');
            return false;
        }
        
        return true;
    });
    
    // ================================================
    // LIST TOGGLE
    // ================================================
    
    $('#toggle-list-btn').click(function() {
        const container = $('#polls-list-container');
        const btn = $(this);
        const icon = btn.find('.dashicons');
        const text = btn.find('.toggle-text');
        
        if (container.hasClass('hidden')) {
            // Show list
            container.removeClass('hidden');
            icon.removeClass('dashicons-visibility').addClass('dashicons-list-view');
            text.text('Ẩn Danh Sách');
        } else {
            // Hide list
            container.addClass('hidden');
            icon.removeClass('dashicons-list-view').addClass('dashicons-visibility');
            text.text('Hiện Danh Sách');
        }
    });
    
    // ================================================
    // SEARCH & FILTER
    // ================================================
    
    let searchTimeout;
    
    $('#search-polls').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(filterPolls, 300);
    });
    
    $('#filter-status').change(filterPolls);
    
    /**
     * Filter polls by search and status
     */
    function filterPolls() {
        const searchTerm = $('#search-polls').val().toLowerCase();
        const statusFilter = $('#filter-status').val();
        let visibleCount = 0;
        
        $('.poll-card').each(function() {
            const $card = $(this);
            const title = $card.find('.poll-title').text().toLowerCase();
            const question = $card.find('.poll-question').text().toLowerCase();
            const status = $card.data('status');
            
            const matchesSearch = !searchTerm || title.includes(searchTerm) || question.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                $card.removeClass('hidden').fadeIn(200);
                visibleCount++;
            } else {
                $card.addClass('hidden').fadeOut(200);
            }
        });
        
        // Show/hide empty state
        if (visibleCount === 0 && !$('.empty-state').length) {
            $('.polls-grid').append(`
                <div class="empty-state" style="grid-column: 1/-1;">
                    <div class="empty-icon">
                        <span class="dashicons dashicons-search"></span>
                    </div>
                    <h3>Không tìm thấy kết quả</h3>
                    <p>Thử tìm kiếm với từ khóa khác hoặc thay đổi bộ lọc</p>
                </div>
            `);
        } else if (visibleCount > 0) {
            $('.polls-grid .empty-state').remove();
        }
    }
    
    // ================================================
    // COPY SHORTCODE
    // ================================================
    
    $(document).on('click', '.copy-shortcode-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $btn = $(this);
        const shortcode = $btn.data('shortcode');
        
        navigator.clipboard.writeText(shortcode).then(function() {
            // Visual feedback
            const originalHTML = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes"></span>')
                .css('color', '#06D6A0');
            
            setTimeout(function() {
                $btn.html(originalHTML).css('color', '');
            }, 2000);
            
            showNotification('✅ Đã copy: ' + shortcode, 'success');
        }).catch(function() {
            // Fallback
            const textArea = document.createElement('textarea');
            textArea.value = shortcode;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            showNotification('✅ Đã copy: ' + shortcode, 'success');
        });
    });
    
    // ================================================
    // TOGGLE STATUS
    // ================================================
    
    $(document).on('click', '.toggle-status-btn', function() {
        const pollId = $(this).data('poll-id');
        const currentStatus = $(this).data('status');
        const newStatus = currentStatus === 'active' ? 'closed' : 'active';
        
        if (confirm('Bạn có chắc muốn thay đổi trạng thái poll này?')) {
            $('#toggle-poll-id').val(pollId);
            $('#toggle-new-status').val(newStatus);
            $('#toggle-status-form').submit();
        }
    });
    
    // ================================================
    // DELETE POLL
    // ================================================
    
    $(document).on('click', '.delete-poll-btn', function() {
        const pollId = $(this).data('poll-id');
        
        if (confirm('⚠️ Bạn có chắc muốn xóa poll này?\n\nTất cả votes sẽ bị mất và không thể khôi phục!')) {
            $('#delete-poll-id').val(pollId);
            $('#delete-poll-form').submit();
        }
    });
    
    // ================================================
    // NOTIFICATION SYSTEM
    // ================================================
    
    /**
     * Show notification toast
     */
    function showNotification(message, type = 'info') {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        
        const colors = {
            success: '#06D6A0',
            error: '#e74c3c',
            warning: '#f39c12',
            info: '#3498db'
        };
        
        const $notification = $(`
            <div class="kata-notification" style="
                position: fixed;
                top: 80px;
                right: 20px;
                background: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 8px 24px rgba(0,0,0,0.15);
                border-left: 4px solid ${colors[type]};
                z-index: 100001;
                display: flex;
                align-items: center;
                gap: 12px;
                min-width: 300px;
                max-width: 500px;
                opacity: 0;
                transform: translateX(400px);
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            ">
                <span style="font-size: 20px;">${icons[type]}</span>
                <span style="flex: 1; font-size: 14px; font-weight: 500; color: #2c3e50;">${message}</span>
            </div>
        `);
        
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.css({
                opacity: 1,
                transform: 'translateX(0)'
            });
        }, 10);
        
        setTimeout(function() {
            $notification.css({
                opacity: 0,
                transform: 'translateX(400px)'
            });
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
    
    // ================================================
    // UTILITY FUNCTIONS
    // ================================================
    
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
    
    // ================================================
    // CSS ANIMATIONS
    // ================================================
    
    // Add spin animation for loading
    const style = $('<style>@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>');
    $('head').append(style);
    
    // Stagger animation for cards
    $('.poll-card').each(function(index) {
        $(this).css('animation-delay', (index * 0.05) + 's');
    });
    
});
</script>
