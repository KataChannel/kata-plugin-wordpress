/**
 * Kata Chatbot Admin JavaScript
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    // Admin object
    var KataChatbotAdmin = {
        
        /**
         * Initialize admin functionality
         */
        init: function() {
            this.bindEvents();
            this.initComponents();
        },
        
        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Quick settings save
            $('.kata-save-quick-settings').on('click', this.saveQuickSettings);
            
            // API status check
            $('#kata-check-api').on('click', this.checkApiStatus);
            
            // Conversation actions
            $(document).on('click', '.kata-view-conversation', this.viewConversation);
            $(document).on('click', '.kata-delete-conversation', this.deleteConversation);
            
            // Export data
            $('#kata-export-data').on('click', this.exportData);
            
            // Knowledge base form
            $('#kata-knowledge-form').on('submit', this.handleKnowledgeForm);
            
            // Settings tabs
            $('.kata-admin-nav a').on('click', this.switchTab);
            
            // Auto-refresh dashboard
            if ($('#kata-dashboard-auto-refresh').is(':checked')) {
                setInterval(this.refreshDashboard, 30000);
            }
        },
        
        /**
         * Initialize components
         */
        initComponents: function() {
            // Initialize charts if available
            this.initCharts();
            
            // Initialize tooltips
            this.initTooltips();
            
            // Check API status on load
            if ($('#kata-api-status').length) {
                this.checkApiStatus();
            }
        },
        
        /**
         * Save quick settings
         */
        saveQuickSettings: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text(kata_chatbot_admin.strings.saving).prop('disabled', true);
            
            var settings = {
                kata_chatbot_enabled: $('#kata-chatbot-enabled').is(':checked'),
                kata_chatbot_sound_enabled: $('#kata-chatbot-sound').is(':checked'),
                kata_chatbot_offline_mode: $('#kata-chatbot-offline-mode').is(':checked')
            };
            
            $.ajax({
                url: kata_chatbot_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_chatbot_save_quick_settings',
                    settings: settings,
                    nonce: kata_chatbot_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KataChatbotAdmin.showNotice(kata_chatbot_admin.strings.saved, 'success');
                    } else {
                        KataChatbotAdmin.showNotice(response.data.message || kata_chatbot_admin.strings.error, 'error');
                    }
                },
                error: function() {
                    KataChatbotAdmin.showNotice(kata_chatbot_admin.strings.error, 'error');
                },
                complete: function() {
                    $button.text(originalText).prop('disabled', false);
                }
            });
        },
        
        /**
         * Check API status
         */
        checkApiStatus: function(e) {
            if (e) e.preventDefault();
            
            var $status = $('#kata-api-status');
            $status.text('Checking...').removeClass('kata-status-ok kata-status-error kata-status-warning');
            
            $.ajax({
                url: kata_chatbot_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_chatbot_check_api_status',
                    nonce: kata_chatbot_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $status.text('Connected').addClass('kata-status-ok');
                    } else {
                        $status.text('Error: ' + (response.data.message || 'Unknown error')).addClass('kata-status-error');
                    }
                },
                error: function() {
                    $status.text('Connection failed').addClass('kata-status-error');
                }
            });
        },
        
        /**
         * View conversation details
         */
        viewConversation: function(e) {
            e.preventDefault();
            
            var conversationId = $(this).data('conversation-id');
            
            $.ajax({
                url: kata_chatbot_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_chatbot_get_conversation_details',
                    conversation_id: conversationId,
                    nonce: kata_chatbot_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KataChatbotAdmin.showConversationModal(response.data);
                    } else {
                        KataChatbotAdmin.showNotice(response.data.message || kata_chatbot_admin.strings.error, 'error');
                    }
                },
                error: function() {
                    KataChatbotAdmin.showNotice(kata_chatbot_admin.strings.error, 'error');
                }
            });
        },
        
        /**
         * Delete conversation
         */
        deleteConversation: function(e) {
            e.preventDefault();
            
            if (!confirm(kata_chatbot_admin.strings.confirm_delete)) {
                return;
            }
            
            var $button = $(this);
            var conversationId = $button.data('conversation-id');
            var $row = $button.closest('.kata-conversation-item');
            
            $button.text(kata_chatbot_admin.strings.deleting).prop('disabled', true);
            
            $.ajax({
                url: kata_chatbot_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_chatbot_delete_conversation',
                    conversation_id: conversationId,
                    nonce: kata_chatbot_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(function() {
                            $row.remove();
                        });
                        KataChatbotAdmin.showNotice(kata_chatbot_admin.strings.deleted, 'success');
                    } else {
                        KataChatbotAdmin.showNotice(response.data.message || kata_chatbot_admin.strings.error, 'error');
                        $button.text('Delete').prop('disabled', false);
                    }
                },
                error: function() {
                    KataChatbotAdmin.showNotice(kata_chatbot_admin.strings.error, 'error');
                    $button.text('Delete').prop('disabled', false);
                }
            });
        },
        
        /**
         * Export data
         */
        exportData: function(e) {
            e.preventDefault();
            
            var exportType = $('#kata-export-type').val();
            var dateFrom = $('#kata-date-from').val();
            var dateTo = $('#kata-date-to').val();
            
            var $button = $(this);
            var originalText = $button.text();
            
            $button.text('Exporting...').prop('disabled', true);
            
            $.ajax({
                url: kata_chatbot_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_chatbot_export_data',
                    export_type: exportType,
                    date_from: dateFrom,
                    date_to: dateTo,
                    nonce: kata_chatbot_admin.nonce
                },
                success: function(response) {
                    if (response.success) {
                        KataChatbotAdmin.downloadCSV(response.data.data, response.data.filename);
                        KataChatbotAdmin.showNotice('Export completed successfully', 'success');
                    } else {
                        KataChatbotAdmin.showNotice(response.data.message || kata_chatbot_admin.strings.error, 'error');
                    }
                },
                error: function() {
                    KataChatbotAdmin.showNotice(kata_chatbot_admin.strings.error, 'error');
                },
                complete: function() {
                    $button.text(originalText).prop('disabled', false);
                }
            });
        },
        
        /**
         * Handle knowledge base form
         */
        handleKnowledgeForm: function(e) {
            // Form is handled by PHP, just add loading state
            var $submitButton = $(this).find('input[type="submit"]');
            $submitButton.val('Saving...').prop('disabled', true);
        },
        
        /**
         * Switch settings tab
         */
        switchTab: function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var targetTab = $tab.attr('href').substring(1);
            
            // Update active tab
            $('.kata-admin-nav a').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Show target tab content
            $('.kata-tab-content').hide();
            $('#' + targetTab).show();
            
            // Update URL hash
            window.location.hash = targetTab;
        },
        
        /**
         * Refresh dashboard data
         */
        refreshDashboard: function() {
            if ($('.kata-chatbot-admin .kata-stats-grid').length) {
                location.reload();
            }
        },
        
        /**
         * Show conversation modal
         */
        showConversationModal: function(data) {
            var modal = $('<div class="kata-modal-overlay">');
            var modalContent = $('<div class="kata-modal-content">');
            
            var html = '<div class="kata-modal-header">';
            html += '<h3>Conversation Details</h3>';
            html += '<button class="kata-modal-close">&times;</button>';
            html += '</div>';
            
            html += '<div class="kata-modal-body">';
            html += '<div class="kata-conversation-info">';
            html += '<p><strong>Session ID:</strong> ' + data.conversation.session_id + '</p>';
            html += '<p><strong>Started:</strong> ' + data.conversation.started_at + '</p>';
            html += '<p><strong>Status:</strong> ' + data.conversation.status + '</p>';
            html += '</div>';
            
            html += '<div class="kata-messages-list">';
            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(function(message) {
                    html += '<div class="kata-message kata-' + message.sender_type + '-message">';
                    html += '<div class="kata-message-content">' + message.message_text + '</div>';
                    html += '<div class="kata-message-time">' + message.sent_at + '</div>';
                    html += '</div>';
                });
            } else {
                html += '<p>No messages found.</p>';
            }
            html += '</div>';
            html += '</div>';
            
            modalContent.html(html);
            modal.append(modalContent);
            $('body').append(modal);
            
            // Close modal events
            modal.on('click', '.kata-modal-close, .kata-modal-overlay', function(e) {
                if (e.target === this) {
                    modal.remove();
                }
            });
        },
        
        /**
         * Download CSV file
         */
        downloadCSV: function(data, filename) {
            var csv = this.arrayToCSV(data);
            var blob = new Blob([csv], { type: 'text/csv' });
            var url = window.URL.createObjectURL(blob);
            
            var a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        },
        
        /**
         * Convert array to CSV
         */
        arrayToCSV: function(data) {
            if (!data || data.length === 0) return '';
            
            var headers = Object.keys(data[0]);
            var csv = headers.join(',') + '\n';
            
            data.forEach(function(row) {
                var values = headers.map(function(header) {
                    var value = row[header] || '';
                    // Escape quotes and wrap in quotes if contains comma
                    if (typeof value === 'string' && (value.includes(',') || value.includes('"'))) {
                        value = '"' + value.replace(/"/g, '""') + '"';
                    }
                    return value;
                });
                csv += values.join(',') + '\n';
            });
            
            return csv;
        },
        
        /**
         * Initialize charts
         */
        initCharts: function() {
            // Placeholder for chart initialization
            // Could integrate with Chart.js or similar library
            if (typeof Chart !== 'undefined') {
                // Initialize charts here
            }
        },
        
        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            // Simple tooltip implementation
            $('[data-tooltip]').hover(
                function() {
                    var tooltip = $('<div class="kata-tooltip">' + $(this).data('tooltip') + '</div>');
                    $('body').append(tooltip);
                    
                    var offset = $(this).offset();
                    tooltip.css({
                        top: offset.top - tooltip.outerHeight() - 5,
                        left: offset.left + ($(this).outerWidth() / 2) - (tooltip.outerWidth() / 2)
                    });
                },
                function() {
                    $('.kata-tooltip').remove();
                }
            );
        },
        
        /**
         * Show admin notice
         */
        showNotice: function(message, type) {
            type = type || 'info';
            
            var notice = $('<div class="notice notice-' + type + ' is-dismissible">');
            notice.html('<p>' + message + '</p>');
            
            $('.kata-chatbot-admin .wrap h1').after(notice);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                notice.fadeOut(function() {
                    notice.remove();
                });
            }, 5000);
        },
        
        /**
         * Utility: Debounce function
         */
        debounce: function(func, wait) {
            var timeout;
            return function executedFunction() {
                var later = function() {
                    clearTimeout(timeout);
                    func.apply(this, arguments);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        KataChatbotAdmin.init();
        
        // Handle URL hash for tabs
        if (window.location.hash) {
            var hash = window.location.hash.substring(1);
            $('.kata-admin-nav a[href="#' + hash + '"]').click();
        }
    });
    
    // Export to global scope
    window.KataChatbotAdmin = KataChatbotAdmin;
    
})(jQuery);
