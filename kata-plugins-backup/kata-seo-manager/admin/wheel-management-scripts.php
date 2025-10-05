<script>
jQuery(document).ready(function($) {
    'use strict';
    
    /* ==================================================
       MODAL MANAGEMENT
       ================================================== */
    
    const modal = $('#wheelFormModal');
    const modalOverlay = $('.kata-modal-overlay');
    const closeBtn = $('#closeWheelModal');
    const cancelBtn = $('#cancelFormBtn');
    const openCreateBtn = $('#openCreateWheelModal');
    
    // Open modal for creating new wheel
    openCreateBtn.on('click', function() {
        resetForm();
        $('#modalTitleText').text('➕ Tạo Vòng Quay Mới');
        $('#submitBtnText').text('Tạo Vòng Quay');
        $('#formAction').val('create');
        $('#wheelId').remove();
        openModal();
    });
    
    // Open modal for editing (from wheel cards)
    $(document).on('click', '.edit-wheel-btn', function() {
        const wheelId = $(this).data('wheel-id');
        loadWheelDataForEdit(wheelId);
    });
    
    // Also handle edit from URL parameter (backward compatibility)
    <?php if ($edit_mode): ?>
    setTimeout(function() {
        openModal();
    }, 300);
    <?php endif; ?>
    
    // Open modal
    function openModal() {
        modal.addClass('kata-modal-show');
        $('body').css('overflow', 'hidden');
    }
    
    // Close modal
    function closeModal() {
        modal.removeClass('kata-modal-show');
        $('body').css('overflow', '');
        
        // If edit mode from URL, redirect back
        <?php if ($edit_mode): ?>
        window.location.href = '?page=kata-seo-wheel-management';
        <?php endif; ?>
    }
    
    closeBtn.on('click', closeModal);
    cancelBtn.on('click', closeModal);
    modalOverlay.on('click', closeModal);
    
    // Close on ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && modal.hasClass('kata-modal-show')) {
            closeModal();
        }
    });
    
    // Prevent modal close when clicking inside modal content
    $('.kata-modal-container').on('click', function(e) {
        e.stopPropagation();
    });
    
    /* ==================================================
       FORM MANAGEMENT
       ================================================== */
    
    function resetForm() {
        $('#wheelForm')[0].reset();
        
        // Reset to default 6 prizes
        $('#prizesContainer').empty();
        for (let i = 0; i < 6; i++) {
            addPrizeRow(i + 1);
        }
    }
    
    function loadWheelDataForEdit(wheelId) {
        // Show loading state
        openModal();
        $('.kata-modal-body').html('<div style="text-align:center;padding:60px;"><span class="dashicons dashicons-update" style="font-size:48px;animation:spin 1s linear infinite;"></span><p>Đang tải dữ liệu...</p></div>');
        
        // AJAX load wheel data
        $.post(ajaxurl, {
            action: 'kata_get_wheel_for_edit',
            wheel_id: wheelId,
            nonce: '<?php echo wp_create_nonce('kata_wheel_edit'); ?>'
        }, function(response) {
            if (response.success) {
                populateEditForm(response.data);
                $('.kata-modal-body').html($('#wheelForm').parent().html());
                $('#modalTitleText').text('✏️ Chỉnh Sửa Vòng Quay');
                $('#submitBtnText').text('Cập nhật Vòng Quay');
            } else {
                alert('❌ Lỗi: ' + response.data.message);
                closeModal();
            }
        }).fail(function() {
            alert('❌ Không thể tải dữ liệu vòng quay');
            closeModal();
        });
    }
    
    function populateEditForm(data) {
        $('#formAction').val('update');
        if (!$('#wheelId').length) {
            $('#formAction').after('<input type="hidden" name="wheel_id" value="' + data.wheel.id + '" id="wheelId">');
        } else {
            $('#wheelId').val(data.wheel.id);
        }
        
        $('#wheel_title').val(data.wheel.wheel_title);
        $('#wheel_description').val(data.wheel.wheel_description);
        $('#requirement').val(data.wheel.requirement);
        $('#max_spins_per_user').val(data.wheel.max_spins_per_user);
        $('#max_spins_per_day').val(data.wheel.max_spins_per_day);
        
        if ($('#wheel_status').length) {
            $('#wheel_status').val(data.wheel.status);
        }
        
        // Populate prizes
        $('#prizesContainer').empty();
        if (data.prizes && data.prizes.length > 0) {
            data.prizes.forEach(function(prize, index) {
                addPrizeRow(index + 1, prize);
            });
        } else {
            for (let i = 0; i < 6; i++) {
                addPrizeRow(i + 1);
            }
        }
    }
    
    /* ==================================================
       PRIZE MANAGEMENT
       ================================================== */
    
    function addPrizeRow(number, prizeData = null) {
        const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f7b731', '#5f27cd', '#00d2d3', '#ee5a6f', '#3867d6'];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        
        const row = $('<div class="prize-row"></div>');
        row.html(`
            <span class="prize-number">${number}</span>
            <input type="text" name="prize_texts[]" value="${prizeData ? prizeData.prize_text : ''}" 
                   placeholder="Tên giải (VD: Giảm 50%)" class="form-control prize-text" required>
            <input type="text" name="prize_values[]" value="${prizeData ? prizeData.prize_value : ''}" 
                   placeholder="Giá trị" class="form-control prize-value">
            <select name="prize_types[]" class="form-control prize-type">
                <option value="discount" ${prizeData && prizeData.prize_type === 'discount' ? 'selected' : ''}>💰 Giảm giá</option>
                <option value="gift" ${prizeData && prizeData.prize_type === 'gift' ? 'selected' : ''}>🎁 Quà tặng</option>
                <option value="service" ${prizeData && prizeData.prize_type === 'service' ? 'selected' : ''}>🛠️ Dịch vụ</option>
                <option value="retry" ${prizeData && prizeData.prize_type === 'retry' ? 'selected' : ''}>🔄 Thử lại</option>
                <option value="nothing" ${prizeData && prizeData.prize_type === 'nothing' ? 'selected' : ''}>❌ Chúc may mắn</option>
            </select>
            <input type="number" name="probabilities[]" value="${prizeData ? prizeData.probability : 10}" 
                   placeholder="%" class="form-control prize-prob" min="0" max="100" step="0.1" required>
            <input type="color" name="colors[]" value="${prizeData ? prizeData.color : randomColor}" 
                   class="form-control prize-color">
            <button type="button" class="kata-btn-icon kata-btn-delete remove-prize" ${number <= 3 ? 'style="display:none;"' : ''}>
                <span class="dashicons dashicons-trash"></span>
            </button>
        `);
        
        $('#prizesContainer').append(row);
        updatePrizeNumbers();
    }
    
    // Add prize button
    $('#addPrizeBtn').on('click', function() {
        const count = $('#prizesContainer .prize-row').length;
        addPrizeRow(count + 1);
        
        // Smooth scroll to new prize
        setTimeout(function() {
            $('#prizesContainer').animate({
                scrollTop: $('#prizesContainer')[0].scrollHeight
            }, 300);
        }, 100);
    });
    
    // Remove prize
    $(document).on('click', '.remove-prize', function() {
        const totalPrizes = $('#prizesContainer .prize-row').length;
        if (totalPrizes > 3) {
            $(this).closest('.prize-row').fadeOut(300, function() {
                $(this).remove();
                updatePrizeNumbers();
            });
        } else {
            alert('⚠️ Cần ít nhất 3 giải thưởng!');
        }
    });
    
    function updatePrizeNumbers() {
        $('#prizesContainer .prize-row').each(function(index) {
            $(this).find('.prize-number').text(index + 1);
            
            // Hide delete button for first 3 prizes
            if (index < 3) {
                $(this).find('.remove-prize').hide();
            } else {
                $(this).find('.remove-prize').show();
            }
        });
    }
    
    /* ==================================================
       LIST TOGGLE
       ================================================== */
    
    let listVisible = true;
    
    $('#toggleWheelList').on('click', function() {
        const container = $('#wheelsListContainer');
        const toggleText = $(this).find('.toggle-text');
        
        if (listVisible) {
            container.addClass('hiding');
            setTimeout(function() {
                container.hide();
                container.removeClass('hiding');
            }, 300);
            toggleText.text('Hiện');
            $(this).find('.dashicons').removeClass('dashicons-list-view').addClass('dashicons-visibility');
        } else {
            container.show();
            toggleText.text('Ẩn');
            $(this).find('.dashicons').removeClass('dashicons-visibility').addClass('dashicons-list-view');
        }
        
        listVisible = !listVisible;
    });
    
    /* ==================================================
       SEARCH & FILTER
       ================================================== */
    
    // Search functionality
    $('#searchWheels').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        filterWheels();
    });
    
    // Filter by status
    $('#filterStatus').on('change', function() {
        filterWheels();
    });
    
    function filterWheels() {
        const searchTerm = $('#searchWheels').val().toLowerCase();
        const statusFilter = $('#filterStatus').val();
        
        $('.wheel-card').each(function() {
            const title = $(this).data('title');
            const status = $(this).data('status');
            
            const matchesSearch = searchTerm === '' || title.includes(searchTerm);
            const matchesStatus = statusFilter === '' || status === statusFilter;
            
            if (matchesSearch && matchesStatus) {
                $(this).fadeIn(300);
            } else {
                $(this).fadeOut(300);
            }
        });
        
        // Show/hide empty state
        const visibleCards = $('.wheel-card:visible').length;
        if (visibleCards === 0) {
            if (!$('.no-results').length) {
                $('.wheels-grid').append(`
                    <div class="no-results" style="grid-column: 1/-1; text-align:center; padding:60px;">
                        <div style="font-size:64px; opacity:0.3;">🔍</div>
                        <h3>Không tìm thấy kết quả</h3>
                        <p>Thử thay đổi từ khóa hoặc bộ lọc</p>
                    </div>
                `);
            }
        } else {
            $('.no-results').remove();
        }
    }
    
    /* ==================================================
       COPY SHORTCODE
       ================================================== */
    
    $(document).on('click', '.copy-shortcode-btn', function(e) {
        e.preventDefault();
        const shortcode = $(this).data('shortcode');
        const btn = $(this);
        
        navigator.clipboard.writeText(shortcode).then(function() {
            // Visual feedback
            btn.html('<span class="dashicons dashicons-yes-alt"></span>');
            btn.css('color', '#28a745');
            
            // Show tooltip
            const tooltip = $('<div class="copy-tooltip">✅ Đã copy!</div>');
            tooltip.css({
                position: 'absolute',
                background: '#28a745',
                color: 'white',
                padding: '6px 12px',
                borderRadius: '6px',
                fontSize: '12px',
                fontWeight: '600',
                zIndex: '1000',
                boxShadow: '0 2px 8px rgba(0,0,0,0.2)',
                animation: 'fadeInUp 0.3s ease'
            });
            
            btn.parent().append(tooltip);
            
            setTimeout(function() {
                btn.html('<span class="dashicons dashicons-clipboard"></span>');
                btn.css('color', '');
                tooltip.fadeOut(200, function() {
                    tooltip.remove();
                });
            }, 2000);
        }).catch(function() {
            alert('❌ Không thể copy. Vui lòng copy thủ công: ' + shortcode);
        });
    });
    
    /* ==================================================
       FORM VALIDATION
       ================================================== */
    
    $('#wheelForm').on('submit', function(e) {
        // Validate probability sum
        let totalProb = 0;
        $('input[name="probabilities[]"]').each(function() {
            totalProb += parseFloat($(this).val()) || 0;
        });
        
        if (Math.abs(totalProb - 100) > 0.1) {
            e.preventDefault();
            alert('⚠️ Tổng xác suất các giải phải bằng 100%\n\nHiện tại: ' + totalProb.toFixed(1) + '%');
            return false;
        }
        
        // Validate prize texts
        let emptyPrizes = 0;
        $('input[name="prize_texts[]"]').each(function() {
            if ($(this).val().trim() === '') {
                emptyPrizes++;
            }
        });
        
        if (emptyPrizes > 0) {
            e.preventDefault();
            alert('⚠️ Vui lòng điền tên cho tất cả các giải thưởng');
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<span class="dashicons dashicons-update" style="animation:spin 1s linear infinite;"></span> Đang xử lý...');
    });
    
    /* ==================================================
       ANIMATIONS & EFFECTS
       ================================================== */
    
    // Add entrance animation to cards
    $('.wheel-card').each(function(index) {
        $(this).css({
            animation: `fadeInUp 0.5s ease ${index * 0.05}s both`
        });
    });
    
    // Smooth scroll to top when opening modal
    function smoothScrollToTop() {
        $('html, body').animate({ scrollTop: 0 }, 400);
    }
    
    // Add fade in up animation
    const style = $('<style></style>');
    style.text(`
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    `);
    $('head').append(style);
    
    /* ==================================================
       ACCESSIBILITY
       ================================================== */
    
    // Focus trap in modal
    const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
    
    modal.on('keydown', function(e) {
        if (e.key === 'Tab') {
            const focusables = modal.find(focusableElements);
            const firstFocusable = focusables.first();
            const lastFocusable = focusables.last();
            
            if (e.shiftKey) {
                if (document.activeElement === firstFocusable[0]) {
                    lastFocusable.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusable[0]) {
                    firstFocusable.focus();
                    e.preventDefault();
                }
            }
        }
    });
    
    // Auto-focus first input when modal opens
    modal.on('shown', function() {
        $('#wheel_title').focus();
    });
    
    console.log('🎡 Kata Wheel Management - Senior UI/UX loaded successfully!');
});
</script>

<!-- Additional CSS for animations -->
<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.copy-tooltip {
    animation: fadeInUp 0.3s ease !important;
}
</style>
