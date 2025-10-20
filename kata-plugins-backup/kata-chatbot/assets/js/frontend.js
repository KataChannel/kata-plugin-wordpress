/**
 * Kata Chatbot Frontend JavaScript
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    // Kata Chatbot Class
    window.KataChatbot = function(options) {
        var self = this;
        
        // Default settings
        var settings = Object.assign({
            ajaxUrl: window.kata_chatbot_ajax?.ajax_url || '/wp-admin/admin-ajax.php',
            nonce: window.kata_chatbot_ajax?.nonce || '',
            soundEnabled: true,
            autoOpen: false,
            position: 'bottom-right',
            theme: 'blue'
        }, options || {});
        
        // State variables
        var isOpen = false;
        var isMinimized = false;
        var sessionId = null;
        var messageHistory = [];
        var isTyping = false;
        var connectionRetries = 0;
        var maxRetries = 3;
        
        // DOM elements
        var container, toggleBtn, chatWindow, messagesContainer, messageInput, sendBtn;
        
        /**
         * Initialize chatbot
         */
        this.init = function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeElements);
            } else {
                initializeElements();
            }
        };
        
        /**
         * Initialize DOM elements and bind events
         */
        function initializeElements() {
            container = document.getElementById('kata-chatbot-container');
            if (!container) {
                console.error('Kata Chatbot: Container not found');
                return;
            }
            
            // Get DOM elements
            toggleBtn = document.getElementById('kata-chat-toggle');
            chatWindow = document.getElementById('kata-chat-window');
            messagesContainer = document.getElementById('kata-chat-messages');
            messageInput = document.getElementById('kata-message-input');
            sendBtn = document.getElementById('kata-send-btn');
            
            if (!toggleBtn || !chatWindow || !messagesContainer || !messageInput || !sendBtn) {
                console.error('Kata Chatbot: Required elements not found');
                return;
            }
            
            // Initialize session
            initializeSession();
            
            // Bind events
            bindEvents();
            
            // Load preferences
            loadUserPreferences();
            
            // Check online status
            updateOnlineStatus();
            
            // Auto-open if specified
            if (settings.autoOpen) {
                setTimeout(openChat, 1000);
            }
            
            console.log('Kata Chatbot initialized successfully');
        }
        
        /**
         * Bind event listeners
         */
        function bindEvents() {
            // BUGFIX: Check if elements exist before binding events
            if (!toggleBtn || !sendBtn || !messageInput) {
                console.warn('Chatbot: Required elements not found for event binding');
                return;
            }
            
            // Chat toggle
            toggleBtn.addEventListener('click', toggleChat);
            
            // Window close button (new)
            var windowCloseBtn = document.getElementById('kata-window-close');
            if (windowCloseBtn) {
                windowCloseBtn.addEventListener('click', closeChat);
            }
            
            // Header actions (legacy)
            var closeBtn = document.getElementById('kata-close-btn');
            var minimizeBtn = document.getElementById('kata-minimize-btn');
            
            if (closeBtn) closeBtn.addEventListener('click', closeChat);
            if (minimizeBtn) minimizeBtn.addEventListener('click', minimizeChat);
            
            // Message sending
            sendBtn.addEventListener('click', sendMessage);
            messageInput.addEventListener('keydown', handleInputKeydown);
            messageInput.addEventListener('input', handleInputChange);
            
            // Emoji picker
            var emojiBtn = document.getElementById('kata-emoji-btn');
            if (emojiBtn) {
                emojiBtn.addEventListener('click', toggleEmojiPicker);
            }
            
            // Emoji selection
            document.addEventListener('click', handleEmojiSelection);
            
            // Suggestion buttons
            document.addEventListener('click', handleSuggestionClick);
            
            // Feedback system
            var feedbackBtn = document.getElementById('kata-feedback-btn');
            if (feedbackBtn) {
                feedbackBtn.addEventListener('click', showFeedbackModal);
            }
            
            // Sound toggle
            var soundToggle = document.getElementById('kata-sound-toggle');
            if (soundToggle) {
                soundToggle.addEventListener('click', toggleSound);
            }
            
            // Modal events
            bindModalEvents();
            
            // Online/offline detection
            window.addEventListener('online', updateOnlineStatus);
            window.addEventListener('offline', updateOnlineStatus);
            
            // Window resize
            window.addEventListener('resize', handleWindowResize);
            
            // Click outside to close emoji picker
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#kata-emoji-picker') && !e.target.closest('#kata-emoji-btn')) {
                    hideEmojiPicker();
                }
            });
        }
        
        /**
         * Initialize session
         */
        function initializeSession() {
            sessionId = generateSessionId();
            console.log('Session initialized:', sessionId);
        }
        
        /**
         * Generate unique session ID
         */
        function generateSessionId() {
            var timestamp = Date.now();
            var random = Math.random().toString(36).substr(2, 9);
            var userAgent = navigator.userAgent.slice(-10);
            return 'chat_' + timestamp + '_' + random + '_' + btoa(userAgent).slice(0, 6);
        }
        
        /**
         * Toggle chat window
         */
        function toggleChat() {
            if (isOpen) {
                closeChat();
            } else {
                openChat();
            }
        }
        
        /**
         * Open chat window
         */
        function openChat() {
            isOpen = true;
            isMinimized = false;
            container.classList.add('kata-chat-open');
            container.classList.add('open'); // Add 'open' class for CSS
            container.classList.remove('kata-chat-minimized');
            
            // Focus input
            setTimeout(function() {
                if (messageInput) {
                    messageInput.focus();
                }
            }, 300);
            
            // Hide notification badge
            hideNotificationBadge();
            
            // Scroll to bottom
            scrollToBottom();
            
            // Track event
            trackEvent('chat_opened');
        }
        
        /**
         * Close chat window
         */
        function closeChat() {
            isOpen = false;
            isMinimized = false;
            container.classList.remove('kata-chat-open', 'kata-chat-minimized', 'open'); // Remove 'open' class
            hideEmojiPicker();
            
            // Track event
            trackEvent('chat_closed');
        }
        
        /**
         * Minimize chat window
         */
        function minimizeChat() {
            isMinimized = true;
            container.classList.add('kata-chat-minimized');
            container.classList.remove('kata-chat-open');
            
            // Track event
            trackEvent('chat_minimized');
        }
        
        /**
         * Handle input keydown
         */
        function handleInputKeydown(e) {
            if (e.key === 'Enter') {
                if (e.shiftKey) {
                    // Allow new line
                    return;
                } else {
                    e.preventDefault();
                    sendMessage();
                }
            }
        }
        
        /**
         * Handle input change
         */
        function handleInputChange() {
            autoResizeTextarea();
            updateCharCounter();
            updateSendButtonState();
        }
        
        /**
         * Send message
         */
        function sendMessage() {
            var message = messageInput.value.trim();
            
            if (!message || isTyping) {
                return;
            }
            
            // Validate message length
            var maxLength = parseInt(document.getElementById('kata-char-limit').textContent);
            if (message.length > maxLength) {
                showError('Message is too long');
                return;
            }
            
            // Clear input
            messageInput.value = '';
            autoResizeTextarea();
            updateCharCounter();
            updateSendButtonState();
            hideEmojiPicker();
            
            // Send user message
            sendUserMessage(message);
        }
        
        /**
         * Send user message
         */
        function sendUserMessage(message) {
            // Add message to chat
            addMessage('user', message);
            
            // Hide suggestions
            hideSuggestedActions();
            
            // Show typing indicator
            showTypingIndicator();
            
            // Send to backend
            sendToBackend(message);
            
            // Track event
            trackEvent('message_sent', { message_length: message.length });
        }
        
        /**
         * Send message to backend
         */
        function sendToBackend(message) {
            var startTime = Date.now();
            
            fetch(settings.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'kata_chatbot_send_message',
                    message: message,
                    session_id: sessionId,
                    nonce: settings.nonce
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                var responseTime = Date.now() - startTime;
                hideTypingIndicator();
                connectionRetries = 0; // Reset retry counter
                
                if (data.success) {
                    handleSuccessfulResponse(data.data, responseTime);
                } else {
                    handleErrorResponse(data.data || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Chat error:', error);
                hideTypingIndicator();
                handleNetworkError();
            });
        }
        
        /**
         * Handle successful response
         */
        function handleSuccessfulResponse(data, responseTime) {
            // Add bot response
            addMessage('bot', data.message);
            
            // Play notification sound
            if (settings.soundEnabled) {
                playNotificationSound();
            }
            
            // Show notification if chat is closed
            if (!isOpen) {
                showNotificationBadge();
            }
            
            // Show suggestions
            if (data.suggestions && data.suggestions.length > 0) {
                showSuggestedActions(data.suggestions);
            }
            
            // Track successful interaction
            trackEvent('response_received', {
                response_time: responseTime,
                message_length: data.message.length
            });
        }
        
        /**
         * Handle error response
         */
        function handleErrorResponse(error) {
            var errorMessage = typeof error === 'string' ? error : 'Sorry, something went wrong. Please try again.';
            addMessage('bot', errorMessage);
            
            // Track error
            trackEvent('response_error', { error: errorMessage });
        }
        
        /**
         * Handle network error
         */
        function handleNetworkError() {
            connectionRetries++;
            
            if (connectionRetries <= maxRetries) {
                var retryMessage = 'Connection lost. Retrying... (' + connectionRetries + '/' + maxRetries + ')';
                showError(retryMessage);
                
                // Retry after delay
                setTimeout(function() {
                    var lastMessage = messageHistory[messageHistory.length - 2]; // Get user's last message
                    if (lastMessage && lastMessage.type === 'user') {
                        sendToBackend(lastMessage.text);
                    }
                }, 2000 * connectionRetries);
            } else {
                var errorMessage = 'Unable to connect. Please check your internet connection and try again.';
                addMessage('bot', errorMessage);
                connectionRetries = 0;
                
                // Track connection error
                trackEvent('connection_error');
            }
        }
        
        /**
         * Add message to chat
         */
        function addMessage(type, text, options) {
            options = options || {};
            
            var messageEl = document.createElement('div');
            messageEl.className = 'kata-message kata-' + type + '-message';
            
            var now = new Date();
            var timeStr = formatTime(now);
            
            var messageContent = createMessageContent(type, text, timeStr, options);
            messageEl.innerHTML = messageContent;
            
            // Add animation class
            messageEl.classList.add('kata-message-entering');
            
            messagesContainer.appendChild(messageEl);
            
            // Trigger animation
            setTimeout(function() {
                messageEl.classList.remove('kata-message-entering');
                messageEl.classList.add('kata-message-entered');
            }, 10);
            
            // Store in history
            messageHistory.push({
                type: type,
                text: text,
                time: now,
                element: messageEl
            });
            
            // Scroll to bottom
            scrollToBottom();
            
            // Limit message history
            limitMessageHistory();
        }
        
        /**
         * Create message content HTML
         */
        function createMessageContent(type, text, timeStr, options) {
            var avatarUrl = options.avatarUrl || getDefaultAvatar();
            
            if (type === 'bot') {
                return `
                    <div class="kata-message-avatar">
                        <img src="${avatarUrl}" alt="Kata AI" />
                    </div>
                    <div class="kata-message-content">
                        <div class="kata-message-bubble">${formatMessageText(text)}</div>
                        <div class="kata-message-time">${timeStr}</div>
                    </div>
                `;
            } else {
                return `
                    <div class="kata-message-content">
                        <div class="kata-message-bubble">${formatMessageText(text)}</div>
                        <div class="kata-message-time">${timeStr}</div>
                    </div>
                `;
            }
        }
        
        /**
         * Format message text
         */
        function formatMessageText(text) {
            // Basic HTML sanitization and formatting
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/\n/g, '<br>')
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>');
        }
        
        /**
         * Format time
         */
        function formatTime(date) {
            return date.getHours().toString().padStart(2, '0') + ':' + 
                   date.getMinutes().toString().padStart(2, '0');
        }
        
        /**
         * Get default avatar URL
         */
        function getDefaultAvatar() {
            return window.kata_chatbot_ajax?.avatar_url || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiMyMjcxYjEiLz4KPGV4dCB4PSIyMCIgeT0iMjUiIGZpbGw9IndoaXRlIiBmb250LXNpemU9IjE0IiBmb250LWZhbWlseT0iQXJpYWwiIGFuY2hvcj0ibWlkZGxlIj7wn6S7PC90ZXh0Pgo8L3N2Zz4K';
        }
        
        /**
         * Show/hide typing indicator
         */
        function showTypingIndicator() {
            isTyping = true;
            var indicator = document.getElementById('kata-typing-indicator');
            if (indicator) {
                indicator.style.display = 'block';
                scrollToBottom();
            }
        }
        
        function hideTypingIndicator() {
            isTyping = false;
            var indicator = document.getElementById('kata-typing-indicator');
            if (indicator) {
                indicator.style.display = 'none';
            }
        }
        
        /**
         * Scroll to bottom of messages
         */
        function scrollToBottom() {
            setTimeout(function() {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }, 100);
        }
        
        /**
         * Auto resize textarea
         */
        function autoResizeTextarea() {
            messageInput.style.height = 'auto';
            var newHeight = Math.min(messageInput.scrollHeight, 100);
            messageInput.style.height = newHeight + 'px';
        }
        
        /**
         * Update character counter
         */
        function updateCharCounter() {
            var charCount = document.getElementById('kata-char-count');
            var charLimit = document.getElementById('kata-char-limit');
            
            if (charCount && charLimit) {
                var count = messageInput.value.length;
                var limit = parseInt(charLimit.textContent);
                
                charCount.textContent = count;
                
                // Update color based on usage
                if (count > limit * 0.9) {
                    charCount.style.color = count >= limit ? '#d63638' : '#dba617';
                } else {
                    charCount.style.color = '#666';
                }
            }
        }
        
        /**
         * Update send button state
         */
        function updateSendButtonState() {
            var message = messageInput.value.trim();
            var charLimit = parseInt(document.getElementById('kata-char-limit').textContent);
            var isValid = message.length > 0 && message.length <= charLimit && !isTyping;
            
            sendBtn.disabled = !isValid;
        }
        
        /**
         * Show/hide suggested actions
         */
        function showSuggestedActions(suggestions) {
            hideSuggestedActions(); // Remove existing suggestions
            
            if (!suggestions || suggestions.length === 0) return;
            
            var suggestionsEl = document.createElement('div');
            suggestionsEl.className = 'kata-suggested-actions';
            
            suggestions.forEach(function(suggestion) {
                var btn = document.createElement('button');
                btn.className = 'kata-suggestion-btn';
                btn.setAttribute('data-message', suggestion);
                btn.textContent = suggestion;
                suggestionsEl.appendChild(btn);
            });
            
            messagesContainer.appendChild(suggestionsEl);
            scrollToBottom();
        }
        
        function hideSuggestedActions() {
            var existing = messagesContainer.querySelector('.kata-suggested-actions');
            if (existing) {
                existing.remove();
            }
        }
        
        /**
         * Handle suggestion click
         */
        function handleSuggestionClick(e) {
            if (e.target.classList.contains('kata-suggestion-btn')) {
                var message = e.target.getAttribute('data-message');
                if (message) {
                    sendUserMessage(message);
                }
            }
        }
        
        /**
         * Notification badge
         */
        function showNotificationBadge() {
            var badge = document.getElementById('kata-notification-badge');
            if (badge) {
                var countEl = badge.querySelector('span');
                var count = parseInt(countEl.textContent) || 0;
                countEl.textContent = count + 1;
                badge.style.display = 'flex';
            }
        }
        
        function hideNotificationBadge() {
            var badge = document.getElementById('kata-notification-badge');
            if (badge) {
                badge.style.display = 'none';
                badge.querySelector('span').textContent = '1';
            }
        }
        
        /**
         * Emoji picker functions
         */
        function toggleEmojiPicker() {
            var picker = document.getElementById('kata-emoji-picker');
            if (picker) {
                var isVisible = picker.style.display !== 'none';
                picker.style.display = isVisible ? 'none' : 'block';
            }
        }
        
        function hideEmojiPicker() {
            var picker = document.getElementById('kata-emoji-picker');
            if (picker) {
                picker.style.display = 'none';
            }
        }
        
        function handleEmojiSelection(e) {
            if (e.target.classList.contains('kata-emoji-item')) {
                insertEmoji(e.target.textContent);
            }
        }
        
        function insertEmoji(emoji) {
            var start = messageInput.selectionStart;
            var end = messageInput.selectionEnd;
            var text = messageInput.value;
            
            messageInput.value = text.substring(0, start) + emoji + text.substring(end);
            messageInput.selectionStart = messageInput.selectionEnd = start + emoji.length;
            messageInput.focus();
            
            handleInputChange();
            hideEmojiPicker();
        }
        
        /**
         * Sound functions
         */
        function playNotificationSound() {
            if (!settings.soundEnabled) return;
            
            try {
                var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                var oscillator = audioContext.createOscillator();
                var gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
                
                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.3);
            } catch (e) {
                console.log('Audio not supported');
            }
        }
        
        function toggleSound() {
            settings.soundEnabled = !settings.soundEnabled;
            var btn = document.getElementById('kata-sound-toggle');
            if (btn) {
                btn.textContent = settings.soundEnabled ? '🔊' : '🔇';
                btn.title = settings.soundEnabled ? 'Tắt âm thanh' : 'Bật âm thanh';
            }
            
            // Save preference
            saveUserPreference('soundEnabled', settings.soundEnabled);
            
            // Track event
            trackEvent('sound_toggled', { enabled: settings.soundEnabled });
        }
        
        /**
         * Modal functions
         */
        function bindModalEvents() {
            // Feedback modal
            var feedbackModal = document.getElementById('kata-feedback-modal');
            if (feedbackModal) {
                var closeButtons = feedbackModal.querySelectorAll('.kata-modal-close, #kata-feedback-skip');
                closeButtons.forEach(function(btn) {
                    btn.addEventListener('click', hideFeedbackModal);
                });
                
                var submitBtn = feedbackModal.querySelector('#kata-feedback-submit');
                if (submitBtn) {
                    submitBtn.addEventListener('click', submitFeedback);
                }
                
                // Star rating
                var stars = feedbackModal.querySelectorAll('.kata-star');
                stars.forEach(function(star) {
                    star.addEventListener('click', function() {
                        var rating = parseInt(this.getAttribute('data-rating'));
                        updateStarRating(rating);
                    });
                });
            }
        }
        
        function showFeedbackModal() {
            var modal = document.getElementById('kata-feedback-modal');
            if (modal) {
                modal.style.display = 'flex';
                trackEvent('feedback_modal_opened');
            }
        }
        
        function hideFeedbackModal() {
            var modal = document.getElementById('kata-feedback-modal');
            if (modal) {
                modal.style.display = 'none';
                resetFeedbackForm();
            }
        }
        
        function resetFeedbackForm() {
            var stars = document.querySelectorAll('.kata-star');
            stars.forEach(function(star) {
                star.classList.remove('active');
            });
            
            var textarea = document.getElementById('kata-feedback-text');
            if (textarea) {
                textarea.value = '';
            }
        }
        
        function updateStarRating(rating) {
            var stars = document.querySelectorAll('.kata-star');
            stars.forEach(function(star, index) {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
        
        function submitFeedback() {
            var rating = document.querySelectorAll('.kata-star.active').length;
            var feedback = document.getElementById('kata-feedback-text').value;
            
            fetch(settings.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'kata_chatbot_submit_feedback',
                    session_id: sessionId,
                    rating: rating,
                    feedback: feedback,
                    nonce: settings.nonce
                })
            })
            .then(response => response.json())
            .then(data => {
                hideFeedbackModal();
                if (data.success) {
                    addMessage('bot', 'Thank you for your feedback! Your opinion helps us improve. 😊');
                }
                
                // Track feedback submission
                trackEvent('feedback_submitted', {
                    rating: rating,
                    has_text: feedback.length > 0
                });
            })
            .catch(error => {
                console.error('Feedback error:', error);
                showError('Unable to submit feedback. Please try again.');
            });
        }
        
        /**
         * Online status functions
         */
        function updateOnlineStatus() {
            var statusIndicator = document.querySelector('.kata-status-indicator');
            var statusText = document.querySelector('.kata-status-text');
            
            if (statusIndicator && statusText) {
                if (navigator.onLine) {
                    statusIndicator.className = 'kata-status-indicator kata-status-online';
                    statusText.textContent = 'Online';
                } else {
                    statusIndicator.className = 'kata-status-indicator kata-status-offline';
                    statusText.textContent = 'Offline';
                }
            }
        }
        
        /**
         * User preferences
         */
        function saveUserPreference(key, value) {
            try {
                localStorage.setItem('kata_chatbot_' + key, JSON.stringify(value));
            } catch (e) {
                console.log('Unable to save preference:', e);
            }
        }
        
        function getUserPreference(key, defaultValue) {
            try {
                var stored = localStorage.getItem('kata_chatbot_' + key);
                return stored !== null ? JSON.parse(stored) : defaultValue;
            } catch (e) {
                return defaultValue;
            }
        }
        
        function loadUserPreferences() {
            settings.soundEnabled = getUserPreference('soundEnabled', settings.soundEnabled);
            
            // Update UI
            var soundBtn = document.getElementById('kata-sound-toggle');
            if (soundBtn) {
                soundBtn.textContent = settings.soundEnabled ? '🔊' : '🔇';
            }
        }
        
        /**
         * Utility functions
         */
        function showError(message) {
            // Could be enhanced to show a toast notification
            console.error('Kata Chatbot Error:', message);
        }
        
        function limitMessageHistory() {
            var maxMessages = 100;
            if (messageHistory.length > maxMessages) {
                var toRemove = messageHistory.splice(0, messageHistory.length - maxMessages);
                toRemove.forEach(function(msg) {
                    if (msg.element && msg.element.parentNode) {
                        msg.element.parentNode.removeChild(msg.element);
                    }
                });
            }
        }
        
        function handleWindowResize() {
            // Adjust chat window position on mobile
            if (window.innerWidth <= 768) {
                container.classList.add('kata-mobile');
            } else {
                container.classList.remove('kata-mobile');
            }
        }
        
        function trackEvent(eventName, properties) {
            properties = properties || {};
            properties.session_id = sessionId;
            properties.timestamp = Date.now();
            
            // Send to analytics if available
            if (window.gtag) {
                window.gtag('event', eventName, {
                    event_category: 'kata_chatbot',
                    custom_properties: properties
                });
            }
            
            // Could also send to custom analytics endpoint
            console.log('Kata Chatbot Event:', eventName, properties);
        }
        
        /**
         * Public API methods
         */
        this.openChat = openChat;
        this.closeChat = closeChat;
        this.sendMessage = function(message) {
            if (message && typeof message === 'string') {
                messageInput.value = message;
                sendMessage();
            }
        };
        this.isOpen = function() { return isOpen; };
        this.getSessionId = function() { return sessionId; };
        this.getMessageHistory = function() { return messageHistory.slice(); };
        
        // Initialize when created
        this.init();
    };
    
    // Auto-initialize if container exists
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.getElementById('kata-chatbot-container');
        
        // BUGFIX: Only initialize if container exists AND KataChatbot is a constructor
        if (container && typeof window.KataChatbot === 'function') {
            try {
                window.kataChatbotInstance = new window.KataChatbot(window.kata_chatbot_settings || {});
            } catch (error) {
                console.error('Kata Chatbot initialization error:', error);
            }
        } else if (!container) {
            console.log('Kata Chatbot: Container not found on this page');
        }
    });
    
})();
