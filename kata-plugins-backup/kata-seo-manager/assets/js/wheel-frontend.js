/**
 * KATA Wheel Frontend JavaScript
 * Handles wheel initialization, spinning, AJAX calls, and user interactions
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
                this.$circle = null; // Simple style doesn't have wheel circle
                this.$center = this.$container.find('.kata-wheel-simple-btn');
                this.$form = this.$container.find('.kata-wheel-form-simple');
                this.$modal = this.$container.find('.kata-wheel-result-simple');
                this.$spinsInfo = this.$container.find('.kata-wheel-info-simple strong');
            } else {
                this.$circle = this.$container.find('.kata-wheel-circle');
                this.$center = this.$container.find('.kata-wheel-center');
                this.$form = this.$container.find('.kata-wheel-form');
                this.$modal = this.$container.find('.kata-wheel-result-modal');
                this.$spinsInfo = this.$container.find('.kata-wheel-info strong');
            }
            
            this.wheelData = null;
            this.isSpinning = false;
            this.canSpin = false;
            this.remainingSpins = 0;
            
            this.init();
        }

        /**
         * Initialize wheel
         */
        init() {
            console.log('Initializing KATA Wheel #' + this.wheelId);
            
            // Check if kata_ajax is available
            if (typeof kata_ajax === 'undefined') {
                this.showError('Lỗi: AJAX không được cấu hình đúng.');
                return;
            }
            
            // Load wheel data
            this.loadWheelData();
            
            // Bind events
            this.bindEvents();
            
            // Check eligibility
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
         * Setup wheel segments with colors and positions (only for default style)
         */
        setupWheelSegments() {
            if (!this.wheelData || !this.wheelData.prizes || this.isSimple) return;
            
            const prizes = this.wheelData.prizes;
            const segmentAngle = 360 / prizes.length;
            
            prizes.forEach((prize, index) => {
                const rotation = index * segmentAngle;
                const $segment = this.$circle.find(`.kata-wheel-segment:eq(${index})`);
                
                // Update segment rotation
                $segment.css('--rotation', rotation + 'deg');
                
                // Update segment color if not set
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
                        
                        // Enable button if can spin
                        if (this.canSpin && this.remainingSpins > 0) {
                            this.$center.removeClass('disabled');
                            console.log('Wheel ready! Remaining spins: ' + this.remainingSpins);
                        } else {
                            this.$center.addClass('disabled');
                            this.showError('Bạn đã hết lượt quay!');
                        }
                    } else {
                        // Error response
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
            // Spin button click
            this.$center.on('click', (e) => {
                e.preventDefault();
                console.log('Center button clicked');
                this.handleSpinClick();
            });
            
            // Form submit - handle both styles
            const $submitBtn = this.isSimple ? 
                this.$form.find('.kata-wheel-submit-btn-simple') : 
                this.$form.find('.kata-wheel-submit-btn');
            
            console.log('Found submit button:', $submitBtn.length);
            
            $submitBtn.on('click', (e) => {
                e.preventDefault();
                console.log('Form submit button clicked');
                this.handleFormSubmit();
            });
            
            // Modal close - handle both styles
            if (this.isSimple) {
                this.$modal.find('.kata-wheel-result-simple-btn').on('click', () => {
                    this.closeModal();
                });
            } else {
                this.$modal.find('.kata-wheel-result-close').on('click', () => {
                    this.closeModal();
                });
                
                // Overlay click to close
                this.$modal.find('.kata-wheel-result-overlay').on('click', () => {
                    this.closeModal();
                });
                
                // Click on modal backdrop to close
                this.$modal.on('click', (e) => {
                    if ($(e.target).is('.kata-wheel-result-modal')) {
                        this.closeModal();
                    }
                });
            }
            
            console.log('Events bound successfully');
        }

        /**
         * Handle spin button click
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
            
            // Check if wheelData is loaded
            if (!this.wheelData) {
                console.log('Wheel data not loaded yet, waiting...');
                this.showError('Đang tải dữ liệu vòng quay, vui lòng thử lại...');
                return;
            }
            
            // Check if form is required
            const requiresForm = this.wheelData.requirement && this.wheelData.requirement !== 'none';
            const hasData = this.hasUserData();
            
            console.log('Form required:', requiresForm, 'Has user data:', hasData, 'Requirement:', this.wheelData.requirement);
            
            if (requiresForm && !hasData) {
                console.log('Showing form for user data collection');
                this.showForm();
                return;
            }
            
            // Spin the wheel
            console.log('Spinning wheel now...');
            this.spin();
        }

        /**
         * Handle form submission
         */
        handleFormSubmit() {
            const email = this.$form.find('input[type="email"]').val();
            const phone = this.$form.find('input[type="tel"]').val();
            const name = this.$form.find('input[type="text"]').val();
            
            // Validate
            let isValid = true;
            
            if (this.wheelData.requirement === 'email' || this.wheelData.requirement === 'both') {
                if (!this.validateEmail(email)) {
                    this.$form.find('input[type="email"]').addClass('error');
                    isValid = false;
                } else {
                    this.$form.find('input[type="email"]').removeClass('error');
                }
            }
            
            if (this.wheelData.requirement === 'phone' || this.wheelData.requirement === 'both') {
                if (!this.validatePhone(phone)) {
                    this.$form.find('input[type="tel"]').addClass('error');
                    isValid = false;
                } else {
                    this.$form.find('input[type="tel"]').removeClass('error');
                }
            }
            
            if (!isValid) {
                this.showError('Vui lòng nhập đầy đủ thông tin hợp lệ.');
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
         * Show user form
         */
        showForm() {
            this.$form.slideDown();
            this.$form.find('input:first').focus();
        }

        /**
         * Spin the wheel
         */
        spin() {
            if (this.isSpinning || !this.canSpin) return;
            
            this.isSpinning = true;
            this.$center.addClass('disabled');
            
            // AJAX call to get prize
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
                            
                            // Show result
                            this.showResult(prize);
                            
                            this.isSpinning = false;
                            
                            // Re-enable center button if can still spin
                            if (this.canSpin) {
                                this.$center.removeClass('disabled');
                            }
                        });
                    } else {
                        this.isSpinning = false;
                        this.$center.removeClass('disabled');
                        this.showError(response.data.message || 'Không thể quay. Vui lòng thử lại.');
                    }
                },
                error: () => {
                    this.isSpinning = false;
                    this.$center.removeClass('disabled');
                    this.showError('Lỗi kết nối. Vui lòng thử lại.');
                }
            });
        }

        /**
         * Animate wheel rotation (for default style) or simulate for simple style
         */
        animateWheel(prizeIndex, callback) {
            if (this.isSimple) {
                // Simple style: just show spinning animation on button
                this.$center.addClass('spinning').text('⏳ Đang quay...');
                
                // Simulate spinning time
                setTimeout(() => {
                    this.$center.removeClass('spinning').html('<span class="kata-wheel-btn-icon">🎲</span><span class="kata-wheel-btn-text">QUAY NGAY</span><span class="kata-wheel-btn-subtitle">Nhấn để quay!</span>');
                    if (callback) callback();
                }, 2000);
            } else {
                // Default style: full wheel animation
                const segmentAngle = 360 / this.wheelData.prizes.length;
                const prizeAngle = prizeIndex * segmentAngle;
                
                // Calculate final rotation
                // Spin 5 full rotations + land on prize (adjusted for pointer at top)
                const spins = 5;
                const randomOffset = Math.random() * (segmentAngle * 0.8) - (segmentAngle * 0.4);
                const finalRotation = (spins * 360) + (360 - prizeAngle) + randomOffset;
                
                // Apply rotation
                this.$circle.addClass('spinning');
                this.$circle.css('transform', `rotate(${finalRotation}deg)`);
                
                // Callback after animation
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
         * Show result modal
         */
        showResult(prize) {
            const $modal = this.$modal;
            
            // Update content
            $modal.find('#kata-wheel-prize-name-' + this.wheelId).html(
                this.escapeHtml(prize.name)
            );
            
            if (prize.value) {
                $modal.find('#kata-wheel-prize-value-' + this.wheelId).html(
                    this.escapeHtml(prize.value)
                );
            }
            
            // Show modal - different styles
            if (this.isSimple) {
                $modal.show();
            } else {
                $modal.addClass('show');
                // Create confetti effect for default style
                this.createConfetti();
            }
        }

        /**
         * Close modal
         */
        closeModal() {
            if (this.isSimple) {
                this.$modal.hide();
            } else {
                this.$modal.removeClass('show');
            }
        }

        /**
         * Show form for user info
         */
        showForm() {
            console.log('Showing form...');
            
            // Make sure form exists
            if (!this.$form || this.$form.length === 0) {
                console.error('Form element not found!');
                this.showError('Lỗi: Không tìm thấy form nhập liệu.');
                return;
            }
            
            // Show form with animation
            this.$form.stop(true, true); // Stop any ongoing animation
            this.$form.css('display', 'block').addClass('active').slideDown(400);
            
            // Focus on first input
            setTimeout(() => {
                const $firstInput = this.$form.find('input:visible:first');
                if ($firstInput.length) {
                    $firstInput.focus();
                }
            }, 450);
            
            console.log('Form should now be visible');
        }

        /**
         * Hide form
         */
        hideForm() {
            console.log('Hiding form...');
            if (this.$form && this.$form.length) {
                this.$form.stop(true, true).removeClass('active').slideUp(400, function() {
                    $(this).css('display', 'none');
                });
            }
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
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
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
