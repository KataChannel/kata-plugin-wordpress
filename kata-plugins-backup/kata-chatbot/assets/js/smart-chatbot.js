/**
 * KATA Smart Chatbot - Frontend JavaScript
 * 
 * Handles chatbot interactions, triggers, and content detection
 * 
 * @package KATA_SEO_Manager
 * @since 2.2.0
 */

(function($) {
    'use strict';
    
    const KATAChatbot = {
        
        settings: {},
        context: {},
        messages: [],
        sessionId: null,
        isOpen: false,
        scrollDepth: 0,
        timeOnPage: 0,
        hasShownExitIntent: false,
        
        /**
         * Initialize chatbot
         */
        init: function() {
            if (typeof kataChatbot === 'undefined') {
                console.error('KATA Chatbot: Configuration not found');
                return;
            }
            
            this.settings = kataChatbot.settings;
            this.context = kataChatbot.contentContext;
            this.messages = kataChatbot.messages;
            this.sessionId = this.generateSessionId();
            
            this.setupElements();
            this.setupEventListeners();
            this.setupTriggers();
            this.showWelcomeMessage();
            
            // Show chatbot container
            $('#kata-chatbot-container').fadeIn();
            
            console.log('KATA Smart Chatbot initialized', {
                context: this.context,
                messages: this.messages
            });
        },
        
        /**
         * Setup DOM elements
         */
        setupElements: function() {
            this.$container = $('#kata-chatbot-container');
            this.$toggle = $('#kata-chatbot-toggle');
            this.$window = $('#kata-chatbot-window');
            this.$messages = $('#kata-chatbot-messages');
            this.$input = $('#kata-chatbot-input');
            this.$form = $('#kata-chatbot-form');
            this.$quickActions = $('#kata-chatbot-quick-actions');
            this.$leadForm = $('#kata-chatbot-lead-form');
            this.$badge = $('.kata-chatbot-badge');
        },
        
        /**
         * Setup event listeners
         */
        setupEventListeners: function() {
            const self = this;
            
            // Toggle chatbot
            this.$toggle.on('click', function() {
                self.toggleChatbot();
            });
            
            // Close button
            $('.kata-chatbot-close, .kata-chatbot-minimize').on('click', function() {
                self.closeChatbot();
            });
            
            // Send message
            this.$form.on('submit', function(e) {
                e.preventDefault();
                self.sendMessage();
            });
            
            // Lead form
            $('#kata-chatbot-lead-form-submit').on('submit', function(e) {
                e.preventDefault();
                self.submitLeadForm();
            });
            
            $('#kata-chatbot-cancel-lead').on('click', function() {
                self.hideleadForm();
            });
            
            // Scroll depth tracking
            $(window).on('scroll', function() {
                self.trackScrollDepth();
            });
            
            // Exit intent
            $(document).on('mouseleave', function(e) {
                if (e.clientY < 0) {
                    self.handleExitIntent();
                }
            });
            
            // Time on page
            setInterval(function() {
                self.timeOnPage++;
            }, 1000);
        },
        
        /**
         * Setup smart triggers
         */
        setupTriggers: function() {
            const self = this;
            
            // Time-based trigger
            if (this.settings.autoOpen && this.settings.openDelay > 0) {
                setTimeout(function() {
                    if (!self.isOpen) {
                        self.openChatbot();
                        self.showContextualMessage();
                    }
                }, this.settings.openDelay * 1000);
            }
        },
        
        /**
         * Track scroll depth
         */
        trackScrollDepth: function() {
            const windowHeight = $(window).height();
            const documentHeight = $(document).height();
            const scrollTop = $(window).scrollTop();
            const scrollPercent = Math.round((scrollTop / (documentHeight - windowHeight)) * 100);
            
            if (scrollPercent > this.scrollDepth) {
                this.scrollDepth = scrollPercent;
                
                // Trigger on scroll threshold
                if (scrollPercent >= this.settings.scrollTrigger && !this.isOpen) {
                    this.openChatbot();
                    this.showContextualMessage();
                }
            }
        },
        
        /**
         * Handle exit intent
         */
        handleExitIntent: function() {
            if (!this.settings.exitIntent || this.hasShownExitIntent || this.isOpen) {
                return;
            }
            
            this.hasShownExitIntent = true;
            this.openChatbot();
            
            // Show exit intent message
            const exitMessage = this.messages.find(m => m.trigger === 'exit');
            if (exitMessage) {
                setTimeout(() => {
                    this.addBotMessage(exitMessage.message, exitMessage.buttons);
                }, 500);
            }
        },
        
        /**
         * Toggle chatbot
         */
        toggleChatbot: function() {
            if (this.isOpen) {
                this.closeChatbot();
            } else {
                this.openChatbot();
            }
        },
        
        /**
         * Open chatbot
         */
        openChatbot: function() {
            this.$window.slideDown(300);
            this.isOpen = true;
            this.$toggle.find('.kata-chatbot-icon').hide();
            this.$toggle.find('.kata-chatbot-close-icon').show();
            this.$badge.hide();
            
            // Focus input
            setTimeout(() => {
                this.$input.focus();
            }, 300);
        },
        
        /**
         * Close chatbot
         */
        closeChatbot: function() {
            this.$window.slideUp(300);
            this.isOpen = false;
            this.$toggle.find('.kata-chatbot-icon').show();
            this.$toggle.find('.kata-chatbot-close-icon').hide();
        },
        
        /**
         * Show welcome message
         */
        showWelcomeMessage: function() {
            const welcomeMsg = this.settings.welcomeMessage || 'Xin chào! Tôi có thể giúp gì cho bạn?';
            this.addBotMessage(welcomeMsg);
            
            // Show initial quick suggestions
            this.showQuickSuggestions([
                'Xem khóa học',
                'Học phí',
                'Đăng ký',
                'Liên hệ'
            ]);
        },
        
        /**
         * Show contextual message based on content
         */
        showContextualMessage: function() {
            // Find relevant message based on context
            let relevantMessage = null;
            
            for (const msg of this.messages) {
                if (msg.trigger === 'exit') continue;
                
                // Check if message matches context
                if (msg.type === 'course' && 
                    (this.context.schemas.includes('Course') || 
                     this.context.keywords.includes('khóa học'))) {
                    relevantMessage = msg;
                    break;
                }
                
                if (msg.type === 'faq' && this.context.schemas.includes('FAQPage')) {
                    relevantMessage = msg;
                    break;
                }
                
                if (msg.type === 'article' && this.context.type === 'post') {
                    relevantMessage = msg;
                    break;
                }
            }
            
            // Show message with delay
            if (relevantMessage) {
                setTimeout(() => {
                    this.addBotMessage(relevantMessage.message, relevantMessage.buttons);
                    this.$badge.text('1').show();
                }, relevantMessage.delay || 3000);
            }
        },
        
        /**
         * Send message
         */
        sendMessage: function() {
            const message = this.$input.val().trim();
            
            if (!message) {
                return;
            }
            
            // Add user message to UI
            this.addUserMessage(message);
            
            // Clear input
            this.$input.val('');
            
            // Show typing indicator
            this.showTypingIndicator();
            
            // Send to server
            $.ajax({
                url: kataChatbot.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_chatbot_send_message',
                    nonce: kataChatbot.nonce,
                    message: message,
                    session_id: this.sessionId,
                    context: JSON.stringify(this.context)
                },
                success: (response) => {
                    this.hideTypingIndicator();
                    
                    if (response.success) {
                        const data = response.data;
                        this.addBotMessage(data.message, data.buttons);
                        
                        // Show suggestions if available
                        if (data.suggestions) {
                            this.showQuickSuggestions(data.suggestions);
                        }
                        
                        // Handle actions
                        if (data.action === 'show_registration_form' || 
                            data.action === 'show_lead_form' ||
                            data.action === 'show_contact_form') {
                            this.showLeadForm();
                        }
                    } else {
                        this.addBotMessage('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại!');
                    }
                },
                error: () => {
                    this.hideTypingIndicator();
                    this.addBotMessage('Không thể kết nối. Vui lòng kiểm tra kết nối mạng!');
                }
            });
        },
        
        /**
         * Add user message to UI
         */
        addUserMessage: function(message) {
            const html = `
                <div class="kata-chat-message kata-chat-user">
                    <div class="kata-chat-bubble">
                        ${this.escapeHtml(message)}
                    </div>
                    <div class="kata-chat-time">${this.getCurrentTime()}</div>
                </div>
            `;
            
            this.$messages.append(html);
            this.scrollToBottom();
        },
        
        /**
         * Add bot message to UI
         */
        addBotMessage: function(message, buttons) {
            let html = `
                <div class="kata-chat-message kata-chat-bot">
                    <div class="kata-chat-avatar">
                        <img src="${this.settings.avatar}" alt="${this.settings.botName}">
                    </div>
                    <div class="kata-chat-content">
                        <div class="kata-chat-bubble">
                            ${this.formatMessage(message)}
                        </div>
            `;
            
            // Add buttons if provided
            if (buttons && buttons.length > 0) {
                html += '<div class="kata-chat-buttons">';
                buttons.forEach(btn => {
                    html += `<button class="kata-chat-btn" data-action="${btn.action}">${btn.text}</button>`;
                });
                html += '</div>';
            }
            
            html += `
                        <div class="kata-chat-time">${this.getCurrentTime()}</div>
                    </div>
                </div>
            `;
            
            this.$messages.append(html);
            this.scrollToBottom();
            
            // Setup button handlers
            if (buttons) {
                this.setupButtonHandlers();
            }
        },
        
        /**
         * Setup button handlers
         */
        setupButtonHandlers: function() {
            const self = this;
            
            $('.kata-chat-btn').off('click').on('click', function() {
                const action = $(this).data('action');
                self.handleButtonAction(action);
            });
        },
        
        /**
         * Handle button actions
         */
        handleButtonAction: function(action) {
            switch(action) {
                case 'show_course_info':
                    this.$input.val('Cho tôi xem thông tin khóa học').trigger('submit');
                    break;
                case 'show_registration_form':
                case 'show_lead_form':
                case 'show_contact_form':
                case 'show_consultation_form':
                    this.showLeadForm();
                    break;
                case 'show_price_consultation':
                case 'show_price_list':
                    this.$input.val('Học phí bao nhiêu?').trigger('submit');
                    break;
                case 'open_chat':
                    this.$input.focus();
                    break;
                default:
                    console.log('Action:', action);
            }
        },
        
        /**
         * Show quick suggestions
         */
        showQuickSuggestions: function(suggestions) {
            if (!suggestions || suggestions.length === 0) {
                this.$quickActions.hide();
                return;
            }
            
            let html = '';
            suggestions.forEach(suggestion => {
                html += `<button class="kata-quick-action">${suggestion}</button>`;
            });
            
            this.$quickActions.html(html).show();
            
            // Handle clicks
            const self = this;
            $('.kata-quick-action').on('click', function() {
                const text = $(this).text();
                self.$input.val(text);
                self.sendMessage();
                self.$quickActions.hide();
            });
        },
        
        /**
         * Show typing indicator
         */
        showTypingIndicator: function() {
            const html = `
                <div class="kata-chat-message kata-chat-bot kata-typing-indicator">
                    <div class="kata-chat-avatar">
                        <img src="${this.settings.avatar}" alt="${this.settings.botName}">
                    </div>
                    <div class="kata-chat-bubble">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            `;
            
            this.$messages.append(html);
            this.scrollToBottom();
        },
        
        /**
         * Hide typing indicator
         */
        hideTypingIndicator: function() {
            $('.kata-typing-indicator').remove();
        },
        
        /**
         * Show lead form
         */
        showLeadForm: function() {
            this.$messages.parent().hide();
            this.$quickActions.hide();
            this.$form.parent().hide();
            this.$leadForm.slideDown(300);
        },
        
        /**
         * Hide lead form
         */
        hideleadForm: function() {
            this.$leadForm.slideUp(300);
            this.$messages.parent().show();
            this.$form.parent().show();
        },
        
        /**
         * Submit lead form
         */
        submitLeadForm: function() {
            const formData = {
                action: 'kata_chatbot_save_lead',
                nonce: kataChatbot.nonce,
                session_id: this.sessionId,
                name: $('input[name="name"]').val(),
                email: $('input[name="email"]').val(),
                phone: $('input[name="phone"]').val(),
                interest_type: $('select[name="interest_type"]').val(),
                message: $('textarea[name="message"]').val()
            };
            
            $.ajax({
                url: kataChatbot.ajaxUrl,
                type: 'POST',
                data: formData,
                success: (response) => {
                    if (response.success) {
                        this.showToast('✅ Cảm ơn bạn! Chúng tôi sẽ liên hệ sớm nhất.', 'success');
                        this.hideleadForm();
                        this.addBotMessage('Cảm ơn bạn đã để lại thông tin! Chúng tôi sẽ liên hệ trong thời gian sớm nhất. 🙏');
                        
                        // Reset form
                        $('#kata-chatbot-lead-form-submit')[0].reset();
                    } else {
                        this.showToast('❌ Có lỗi xảy ra. Vui lòng thử lại!', 'error');
                    }
                },
                error: () => {
                    this.showToast('❌ Không thể gửi thông tin. Vui lòng thử lại!', 'error');
                }
            });
        },
        
        /**
         * Show toast notification
         */
        showToast: function(message, type) {
            const $toast = $('#kata-chatbot-toast');
            $toast.text(message)
                .removeClass('success error')
                .addClass(type)
                .fadeIn()
                .delay(3000)
                .fadeOut();
        },
        
        /**
         * Scroll to bottom of messages
         */
        scrollToBottom: function() {
            this.$messages.animate({
                scrollTop: this.$messages[0].scrollHeight
            }, 300);
        },
        
        /**
         * Format message (preserve line breaks)
         */
        formatMessage: function(message) {
            return this.escapeHtml(message).replace(/\n/g, '<br>');
        },
        
        /**
         * Escape HTML
         */
        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },
        
        /**
         * Get current time
         */
        getCurrentTime: function() {
            const now = new Date();
            return now.getHours().toString().padStart(2, '0') + ':' + 
                   now.getMinutes().toString().padStart(2, '0');
        },
        
        /**
         * Generate session ID
         */
        generateSessionId: function() {
            return 'kata_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        KATAChatbot.init();
    });
    
})(jQuery);
