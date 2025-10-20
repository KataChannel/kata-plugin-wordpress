/**
 * KATA Wheel Frontend JavaScript - UPGRADED VERSION
 * New Features:
 * 1. Spin button in center of wheel
 * 2. Modal for user info before spinning
 * 3. Result shown in modal + below wheel after closing
 */

(function($) {
    'use strict';

    /**
     * Main Wheel Class
     */
    class KataWheel {
        constructor(container) {
            this.$container = $(container);
            this.wheelId = this.$container.data('wheel-id');
            this.isSimple = this.$container.hasClass('kata-wheel-simple');
            
            // Initialize elements based on style
            if (this.isSimple) {
                this.$circle = null;
                this.$center = this.$container.find('.kata-wheel-simple-btn');
                this.$form = this.$container.find('.kata-wheel-form-simple');
                this.$formModal = null; // Simple style uses inline form
                this.$resultModal = this.$container.find('.kata-wheel-result-simple');
                this.$spinsInfo = this.$container.find('.kata-wheel-info-simple strong');
                this.$resultDisplay = null;
            } else {
                this.$circle = this.$container.find('.kata-wheel-circle');
                this.$center = this.$container.find('.kata-wheel-center');
                this.$form = this.$container.find('.kata-wheel-form');
                this.$formModal = this.$container.find('.kata-wheel-form-modal');
                this.$resultModal = this.$container.find('.kata-wheel-result-modal');
                this.$spinsInfo = this.$container.find('.kata-wheel-info strong');
                this.$resultDisplay = this.$container.find('.kata-wheel-result-display');
            }
            
            this.wheelData = null;
            this.isSpinning = false;
            this.canSpin = false;
            this.remainingSpins = 0;
            this.lastPrize = null;
            
            this.init();
        }

        /**
         * Initialize wheel
         */
        init() {
            console.log('Initializing KATA Wheel #' + this.wheelId);
            
            if (typeof kata_ajax === 'undefined') {
                this.showError('Lỗi: AJAX không được cấu hình đúng.');
                return;
            }
            
            this.loadWheelData();
            this.bindEvents();
            this.checkEligibility();
        }

        /**
         * Load wheel data from server
         */
        loadWheelData() {
            $.ajax({
                url: kata_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_get_wheel_data',
                    nonce: kata_ajax.nonce,
                    wheel_id: this.wheelId
                },
                success: (response) => {
                    if (response.success) {
                        this.wheelData = response.data;
                        this.setupWheelSegments();
                    } else {
                        this.showError(response.data.message || 'Không thể tải dữ liệu vòng quay.');
                    }
                },
                error: () => {
                    this.showError('Lỗi kết nối. Vui lòng thử lại.');
                }
            });
        }

        /**
         * Setup wheel segments with colors and positions
         */
        setupWheelSegments() {
            if (!this.wheelData || !this.wheelData.prizes || this.isSimple) return;
            
            const prizes = this.wheelData.prizes;
            const segmentAngle = 360 / prizes.length;
            
            prizes.forEach((prize, index) => {
                const rotation = index * segmentAngle;
                const $segment = this.$circle.find(`.kata-wheel-segment:eq(${index})`);
                
                $segment.css('--rotation', rotation + 'deg');
                
                if (!$segment.css('--segment-color')) {
                    const colors = [
                        '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', 
                        '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'
                    ];
                    $segment.css('--segment-color', colors[index % colors.length]);
                }
            });
        }

        /**
         * Check if user can spin
         */
        checkEligibility() {
            $.ajax({
                url: kata_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_check_wheel_eligibility',
                    nonce: kata_ajax.nonce,
                    wheel_id: this.wheelId,
                    user_identifier: this.getUserIdentifier()
                },
                success: (response) => {
                    if (response.success) {
                        this.canSpin = response.data.can_spin || false;
                        this.remainingSpins = response.data.remaining_spins || 0;
                        this.$spinsInfo.text(this.remainingSpins);
                        
                        if (this.canSpin && this.remainingSpins > 0) {
                            this.$center.removeClass('disabled');
                            console.log('Wheel ready! Remaining spins: ' + this.remainingSpins);
                        } else {
                            this.$center.addClass('disabled');
                            this.showError('Bạn đã hết lượt quay!');
                        }
                    } else {
                        this.canSpin = false;
                        this.remainingSpins = 0;
                        this.$spinsInfo.text(0);
                        this.$center.addClass('disabled');
                    }
                },
                error: () => {
                    console.error('Could not check eligibility');
                }
            });
        }

        /**
         * Bind event handlers
         */
        bindEvents() {
            // BUGFIX: Check if elements exist before binding events
            if (!this.$center || this.$center.length === 0) {
                console.warn('Wheel: Spin button not found, skipping event binding');
                return;
            }
            
            // Spin button click - UPDATED: Show form modal first if needed
            this.$center.on('click', (e) => {
                e.preventDefault();
                console.log('Center button clicked');
                this.handleSpinClick();
            });
            
            // Form modal submit (for default style)
            if (!this.isSimple && this.$formModal && this.$formModal.length) {
                this.$formModal.find('.kata-wheel-submit-btn').on('click', (e) => {
                    e.preventDefault();
                    console.log('Form modal submit clicked');
                    this.handleFormModalSubmit();
                });
                
                // Close form modal
                this.$formModal.find('.kata-wheel-form-close').on('click', () => {
                    this.closeFormModal();
                });
                
                this.$formModal.find('.kata-wheel-form-overlay').on('click', () => {
                    this.closeFormModal();
                });
            }
            
            // Form inline submit (for simple style or old structure)
            if (this.$form && this.$form.length) {
                this.$form.find('.kata-wheel-submit-btn, .kata-wheel-submit-btn-simple').on('click', (e) => {
                    e.preventDefault();
                    console.log('Inline form submit clicked');
                    this.handleFormSubmit();
                });
            }
            
            // Result modal close
            if (this.$resultModal && this.$resultModal.length) {
                if (this.isSimple) {
                    this.$resultModal.find('.kata-wheel-result-simple-btn').on('click', () => {
                        this.closeResultModal();
                    });
                } else {
                    this.$resultModal.find('.kata-wheel-result-close, .kata-wheel-result-btn').on('click', () => {
                        this.closeResultModal();
                    });
                    
                    this.$resultModal.find('.kata-wheel-result-overlay').on('click', () => {
                        this.closeResultModal();
                    });
                    
                    this.$resultModal.on('click', (e) => {
                        if ($(e.target).is('.kata-wheel-result-modal')) {
                            this.closeResultModal();
                        }
                    });
                }
            }
            
            console.log('Events bound successfully');
        }

        /**
         * Handle spin button click - UPDATED LOGIC
         */
        handleSpinClick() {
            console.log('Spin clicked! isSpinning:', this.isSpinning, 'canSpin:', this.canSpin, 'remainingSpins:', this.remainingSpins);
            
            if (this.isSpinning) {
                console.log('Already spinning, ignoring click');
                return;
            }
            
            if (!this.canSpin || this.remainingSpins <= 0) {
                console.log('Cannot spin - no spins remaining');
                this.showError('Bạn đã hết lượt quay!');
                return;
            }
            
            if (!this.wheelData) {
                console.log('Wheel data not loaded yet, waiting...');
                this.showError('Đang tải dữ liệu vòng quay, vui lòng thử lại...');
                return;
            }
            
            // NEW: Check if form is required
            // BUGFIX: requirement is in wheelData.wheel.requirement, not wheelData.requirement
            const wheelConfig = this.wheelData.wheel || this.wheelData;
            const requiresForm = wheelConfig.requirement && wheelConfig.requirement !== 'none';
            const hasData = this.hasUserData();
            
            console.log('Form required:', requiresForm, 'Has user data:', hasData, 'Requirement:', wheelConfig.requirement);
            
            // NEW: Show form modal BEFORE spinning if needed
            if (requiresForm && !hasData) {
                console.log('Showing form modal for user data collection');
                this.showFormModal();
                return;
            }
            
            // Spin immediately if no form required or data already exists
            console.log('Spinning wheel now...');
            this.spin();
        }

        /**
         * NEW: Show form modal
         */
        showFormModal() {
            if (this.isSimple) {
                // Simple style uses inline form
                this.showForm();
            } else if (this.$formModal && this.$formModal.length) {
                // Default style uses modal
                this.$formModal.addClass('show');
                this.$formModal.find('input:visible:first').focus();
            } else {
                // Fallback to inline form
                this.showForm();
            }
        }

        /**
         * NEW: Close form modal
         */
        closeFormModal() {
            if (this.$formModal && this.$formModal.length) {
                this.$formModal.removeClass('show');
            }
        }

        /**
         * NEW: Handle form modal submit
         */
        handleFormModalSubmit() {
            const email = this.$formModal.find('input[type="email"]').val();
            const phone = this.$formModal.find('input[type="tel"]').val();
            const name = this.$formModal.find('input[type="text"]').val();
            
            // Validate
            if (!this.validateFormData(email, phone, this.$formModal)) {
                return;
            }
            
            // Save user data
            this.saveUserData(email, phone, name);
            
            // Close modal
            this.closeFormModal();
            
            // Spin immediately
            this.spin();
        }

        /**
         * Handle inline form submission
         */
        handleFormSubmit() {
            const email = this.$form.find('input[type="email"]').val();
            const phone = this.$form.find('input[type="tel"]').val();
            const name = this.$form.find('input[type="text"]').val();
            
            // Validate
            if (!this.validateFormData(email, phone, this.$form)) {
                return;
            }
            
            // Save user data
            this.saveUserData(email, phone, name);
            
            // Hide form
            this.hideForm();
            
            // Spin
            this.spin();
        }

        /**
         * Validate form data
         */
        validateFormData(email, phone, $context) {
            let isValid = true;
            
            // BUGFIX: requirement is in wheelData.wheel.requirement
            const wheelConfig = this.wheelData.wheel || this.wheelData;
            const requirement = wheelConfig.requirement;
            
            if (requirement === 'email' || requirement === 'both') {
                if (!this.validateEmail(email)) {
                    $context.find('input[type="email"]').addClass('error');
                    isValid = false;
                } else {
                    $context.find('input[type="email"]').removeClass('error');
                }
            }
            
            if (requirement === 'phone' || requirement === 'both') {
                if (!this.validatePhone(phone)) {
                    $context.find('input[type="tel"]').addClass('error');
                    isValid = false;
                } else {
                    $context.find('input[type="tel"]').removeClass('error');
                }
            }
            
            if (!isValid) {
                this.showError('Vui lòng nhập đầy đủ thông tin hợp lệ.');
            }
            
            return isValid;
        }

        /**
         * Show inline user form
         */
        showForm() {
            console.log('Showing inline form...');
            
            if (!this.$form || this.$form.length === 0) {
                console.error('Form element not found!');
                this.showError('Lỗi: Không tìm thấy form nhập liệu.');
                return;
            }
            
            this.$form.stop(true, true).css('display', 'block').addClass('active').slideDown(400);
            
            setTimeout(() => {
                const $firstInput = this.$form.find('input:visible:first');
                if ($firstInput.length) {
                    $firstInput.focus();
                }
            }, 450);
            
            console.log('Inline form should now be visible');
        }

        /**
         * Hide inline form
         */
        hideForm() {
            console.log('Hiding inline form...');
            if (this.$form && this.$form.length) {
                this.$form.stop(true, true).removeClass('active').slideUp(400, function() {
                    $(this).css('display', 'none');
                });
            }
        }

        /**
         * Spin the wheel
         */
        spin() {
            if (this.isSpinning || !this.canSpin) return;
            
            this.isSpinning = true;
            this.$center.addClass('disabled spinning');
            
            $.ajax({
                url: kata_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_spin_wheel',
                    nonce: kata_ajax.nonce,
                    wheel_id: this.wheelId,
                    user_identifier: this.getUserIdentifier(),
                    user_email: this.getUserEmail(),
                    user_phone: this.getUserPhone(),
                    user_name: this.getUserName()
                },
                success: (response) => {
                    if (response.success) {
                        const prize = response.data.prize;
                        this.lastPrize = prize; // Store for later display
                        const prizeIndex = this.getPrizeIndex(prize.id);
                        
                        // Animate wheel
                        this.animateWheel(prizeIndex, () => {
                            // Update remaining spins
                            this.remainingSpins = response.data.remaining_spins;
                            this.$spinsInfo.text(this.remainingSpins);
                            
                            if (this.remainingSpins <= 0) {
                                this.canSpin = false;
                                this.$center.addClass('disabled');
                            }
                            
                            // Show result in modal
                            this.showResultModal(prize);
                            
                            this.isSpinning = false;
                            this.$center.removeClass('spinning');
                            
                            if (this.canSpin) {
                                this.$center.removeClass('disabled');
                            }
                        });
                    } else {
                        this.isSpinning = false;
                        this.$center.removeClass('disabled spinning');
                        this.showError(response.data.message || 'Không thể quay. Vui lòng thử lại.');
                    }
                },
                error: () => {
                    this.isSpinning = false;
                    this.$center.removeClass('disabled spinning');
                    this.showError('Lỗi kết nối. Vui lòng thử lại.');
                }
            });
        }

        /**
         * Animate wheel rotation
         */
        animateWheel(prizeIndex, callback) {
            if (this.isSimple) {
                // Simple style: button animation
                this.$center.text('⏳ Đang quay...');
                
                setTimeout(() => {
                    this.$center.html('<span class="kata-wheel-btn-icon">🎲</span><span class="kata-wheel-btn-text">QUAY NGAY</span><span class="kata-wheel-btn-subtitle">Nhấn để quay!</span>');
                    if (callback) callback();
                }, 2500);
            } else {
                // Default style: full wheel animation
                const segmentAngle = 360 / this.wheelData.prizes.length;
                const prizeAngle = prizeIndex * segmentAngle;
                
                // Spin 5 full rotations + land on prize
                const spins = 5;
                const randomOffset = Math.random() * (segmentAngle * 0.6) - (segmentAngle * 0.3);
                const finalRotation = (spins * 360) + (360 - prizeAngle) + randomOffset;
                
                this.$circle.addClass('spinning');
                this.$circle.css('transform', `rotate(${finalRotation}deg)`);
                
                setTimeout(() => {
                    this.$circle.removeClass('spinning');
                    if (callback) callback();
                }, 4000);
            }
        }

        /**
         * Get prize index by ID
         */
        getPrizeIndex(prizeId) {
            if (!this.wheelData || !this.wheelData.prizes) return 0;
            
            for (let i = 0; i < this.wheelData.prizes.length; i++) {
                if (this.wheelData.prizes[i].id == prizeId) {
                    return i;
                }
            }
            return 0;
        }

        /**
         * NEW: Show result modal
         */
        showResultModal(prize) {
            // ✅ BUGFIX: Validate prize object exists
            if (!prize) {
                console.error('Wheel: Prize data is missing');
                return;
            }
            
            const $modal = this.$resultModal;
            
            // Update content
            $modal.find('#kata-wheel-prize-name-' + this.wheelId).html(
                this.escapeHtml(prize.name)
            );
            
            if (prize.value) {
                $modal.find('#kata-wheel-prize-value-' + this.wheelId).html(
                    this.escapeHtml(prize.value)
                );
            }
            
            // Show modal
            if (this.isSimple) {
                $modal.show();
            } else {
                $modal.addClass('show');
                this.createConfetti();
            }
        }

        /**
         * NEW: Close result modal and show result below wheel
         */
        closeResultModal() {
            if (this.isSimple) {
                this.$resultModal.hide();
            } else {
                this.$resultModal.removeClass('show');
            }
            
            // NEW: Show result display below wheel
            if (this.lastPrize && this.$resultDisplay && this.$resultDisplay.length) {
                this.showResultDisplay(this.lastPrize);
            }
        }

        /**
         * NEW: Show result below wheel
         */
        showResultDisplay(prize) {
            // Update content
            this.$resultDisplay.find('.kata-wheel-result-display-name').html(
                this.escapeHtml(prize.name)
            );
            
            if (prize.value) {
                this.$resultDisplay.find('.kata-wheel-result-display-value').html(
                    this.escapeHtml(prize.value)
                ).show();
            } else {
                this.$resultDisplay.find('.kata-wheel-result-display-value').hide();
            }
            
            // Show with animation
            this.$resultDisplay.slideDown(400).addClass('show');
        }

        /**
         * Create confetti effect
         */
        createConfetti() {
            const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const $confetti = $('<div class="confetti"></div>');
                    $confetti.css({
                        left: Math.random() * 100 + '%',
                        background: colors[Math.floor(Math.random() * colors.length)],
                        animationDelay: Math.random() * 0.5 + 's'
                    });
                    $('body').append($confetti);
                    
                    setTimeout(() => $confetti.remove(), 3000);
                }, i * 30);
            }
        }

        /**
         * Show error message
         */
        showError(message) {
            const $error = $('<div class="kata-wheel-error">' + this.escapeHtml(message) + '</div>');
            this.$container.append($error);
            
            setTimeout(() => {
                $error.fadeOut(() => $error.remove());
            }, 5000);
        }

        /**
         * User data helpers
         */
        getUserIdentifier() {
            let identifier = localStorage.getItem('kata_wheel_user_id');
            if (!identifier) {
                identifier = 'user_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                localStorage.setItem('kata_wheel_user_id', identifier);
            }
            return identifier;
        }

        hasUserData() {
            return localStorage.getItem('kata_wheel_user_email') || 
                   localStorage.getItem('kata_wheel_user_phone');
        }

        saveUserData(email, phone, name) {
            if (email) localStorage.setItem('kata_wheel_user_email', email);
            if (phone) localStorage.setItem('kata_wheel_user_phone', phone);
            if (name) localStorage.setItem('kata_wheel_user_name', name);
        }

        getUserEmail() {
            return localStorage.getItem('kata_wheel_user_email') || '';
        }

        getUserPhone() {
            return localStorage.getItem('kata_wheel_user_phone') || '';
        }

        getUserName() {
            return localStorage.getItem('kata_wheel_user_name') || '';
        }

        /**
         * Validation helpers
         */
        validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        validatePhone(phone) {
            const re = /^[0-9]{10,11}$/;
            return re.test(phone.replace(/[\s\-]/g, ''));
        }

        /**
         * Escape HTML
         */
        escapeHtml(text) {
            // ✅ BUGFIX: Handle null/undefined values
            if (text === null || text === undefined) {
                return '';
            }
            
            // Convert to string if not already
            const str = String(text);
            
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return str.replace(/[&<>"']/g, m => map[m]);
        }
    }

    /**
     * Initialize all wheels on page
     */
    $(document).ready(function() {
        $('.kata-wheel-container').each(function() {
            new KataWheel(this);
        });
    });

})(jQuery);
