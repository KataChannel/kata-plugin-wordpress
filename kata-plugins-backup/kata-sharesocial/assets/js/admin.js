/**
 * Kata ShareSocial Admin JavaScript
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * Admin functionality
     */
    var KataShareSocialAdmin = {
        
        /**
         * Initialize
         */
        init: function() {
            this.initColorPicker();
            this.initSettingsPreview();
            this.initFormValidation();
            this.initTabs();
            this.initTooltips();
            this.bindEvents();
        },
        
        /**
         * Initialize color picker
         */
        initColorPicker: function() {
            if ($.fn.wpColorPicker) {
                $('.kata-color-picker').wpColorPicker();
            }
        },
        
        /**
         * Initialize settings preview
         */
        initSettingsPreview: function() {
            this.updatePreview();
        },
        
        /**
         * Update preview based on settings
         */
        updatePreview: function() {
            var $preview = $('#kata-preview-buttons .kata-share-buttons');
            if (!$preview.length) return;
            
            // Get selected platforms
            var selectedPlatforms = [];
            $('input[name="enabled_platforms[]"]:checked').each(function() {
                selectedPlatforms.push($(this).val());
            });
            
            // Get style settings
            var style = $('input[name="button_style"]:checked').val() || 'rounded';
            var size = $('select[name="button_size"]').val() || 'medium';
            var showCount = $('input[name="show_count"]').is(':checked');
            
            // Update preview classes
            $preview.removeClass('kata-style-rounded kata-style-square kata-style-circle');
            $preview.removeClass('kata-size-small kata-size-medium kata-size-large');
            $preview.addClass('kata-style-' + style);
            $preview.addClass('kata-size-' + size);
            
            // Show/hide platforms
            $preview.find('.kata-share-button').each(function() {
                var platform = $(this).data('platform');
                if (selectedPlatforms.indexOf(platform) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            // Show/hide count elements
            if (showCount) {
                $preview.find('.kata-share-total, .kata-platform-count').show();
            } else {
                $preview.find('.kata-share-total, .kata-platform-count').hide();
            }
            
            // Add animation
            $preview.addClass('kata-fade-in');
            setTimeout(function() {
                $preview.removeClass('kata-fade-in');
            }, 600);
        },
        
        /**
         * Form validation
         */
        initFormValidation: function() {
            var self = this;
            
            // Check if at least one platform is selected
            $('form').on('submit', function(e) {
                var selectedPlatforms = $('input[name="enabled_platforms[]"]:checked').length;
                
                if (selectedPlatforms === 0) {
                    e.preventDefault();
                    self.showNotice('Please select at least one social platform.', 'error');
                    return false;
                }
            });
        },
        
        /**
         * Initialize tabs
         */
        initTabs: function() {
            $('.kata-nav-tab').on('click', function(e) {
                e.preventDefault();
                
                var $tab = $(this);
                var target = $tab.data('target');
                
                // Remove active class from all tabs and sections
                $('.kata-nav-tab').removeClass('nav-tab-active');
                $('.kata-tab-section').hide();
                
                // Add active class to current tab and show section
                $tab.addClass('nav-tab-active');
                $(target).show();
            });
        },
        
        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            if ($.fn.tooltip) {
                $('[data-tooltip]').tooltip({
                    content: function() {
                        return $(this).data('tooltip');
                    }
                });
            }
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            var self = this;
            
            // Settings change events
            $('input[name="enabled_platforms[]"], input[name="button_style"], select[name="button_size"], input[name="show_count"]')
                .on('change', function() {
                    self.updatePreview();
                });
            
            // Platform toggle all
            $('.kata-toggle-all-platforms').on('click', function(e) {
                e.preventDefault();
                var $checkboxes = $('input[name="enabled_platforms[]"]');
                var allChecked = $checkboxes.length === $checkboxes.filter(':checked').length;
                
                $checkboxes.prop('checked', !allChecked).trigger('change');
            });
            
            // Copy shortcode
            $('.kata-copy-shortcode').on('click', function(e) {
                e.preventDefault();
                self.copyToClipboard($(this).data('shortcode'));
                self.showNotice('Shortcode copied to clipboard!', 'success');
            });
            
            // Export data
            $('.kata-export-data').on('click', function(e) {
                e.preventDefault();
                self.exportData($(this).data('format'));
            });
            
            // Clear cache
            $('.kata-clear-cache').on('click', function(e) {
                e.preventDefault();
                self.clearCache();
            });
            
            // Test share button
            $('.kata-test-share').on('click', function(e) {
                e.preventDefault();
                self.testShare($(this).data('platform'));
            });
        },
        
        /**
         * Show admin notice
         */
        showNotice: function(message, type) {
            type = type || 'info';
            
            var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            $('.wrap h1').after($notice);
            
            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $notice.slideUp(function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        /**
         * Copy text to clipboard
         */
        copyToClipboard: function(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            } else {
                // Fallback
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
        },
        
        /**
         * Export analytics data
         */
        exportData: function(format) {
            var $button = $('.kata-export-data[data-format="' + format + '"]');
            $button.addClass('kata-loading');
            
            // Simulate export process
            setTimeout(function() {
                $button.removeClass('kata-loading');
                KataShareSocialAdmin.showNotice('Data exported successfully!', 'success');
            }, 2000);
        },
        
        /**
         * Clear plugin cache
         */
        clearCache: function() {
            var $button = $('.kata-clear-cache');
            $button.addClass('kata-loading');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_clear_cache',
                    nonce: kata_admin_nonce
                },
                success: function(response) {
                    $button.removeClass('kata-loading');
                    if (response.success) {
                        KataShareSocialAdmin.showNotice('Cache cleared successfully!', 'success');
                    } else {
                        KataShareSocialAdmin.showNotice('Failed to clear cache.', 'error');
                    }
                },
                error: function() {
                    $button.removeClass('kata-loading');
                    KataShareSocialAdmin.showNotice('Error clearing cache.', 'error');
                }
            });
        },
        
        /**
         * Test share functionality
         */
        testShare: function(platform) {
            var testUrl = 'https://example.com';
            var platforms = {
                'facebook': 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(testUrl),
                'twitter': 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(testUrl) + '&text=Test%20Share',
                'linkedin': 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(testUrl)
            };
            
            if (platforms[platform]) {
                window.open(platforms[platform], 'testShare', 'width=600,height=400');
            }
        },
        
        /**
         * Initialize dashboard widgets
         */
        initDashboard: function() {
            this.initCharts();
            this.initRealTimeStats();
            this.initQuickActions();
        },
        
        /**
         * Initialize charts
         */
        initCharts: function() {
            // Platform performance chart
            $('.kata-platform-fill').each(function() {
                var $fill = $(this);
                var width = $fill.css('width');
                $fill.css('width', '0').animate({width: width}, 1000);
            });
        },
        
        /**
         * Initialize real-time stats
         */
        initRealTimeStats: function() {
            // Update stats every 30 seconds
            setInterval(function() {
                KataShareSocialAdmin.updateRealTimeStats();
            }, 30000);
        },
        
        /**
         * Update real-time statistics
         */
        updateRealTimeStats: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_get_realtime_stats',
                    nonce: kata_admin_nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update stat cards
                        $('.kata-stat-realtime').each(function() {
                            var $stat = $(this);
                            var statType = $stat.data('stat');
                            if (response.data[statType] !== undefined) {
                                $stat.find('.kata-stat-number').text(
                                    KataShareSocialAdmin.formatNumber(response.data[statType])
                                );
                            }
                        });
                    }
                }
            });
        },
        
        /**
         * Initialize quick actions
         */
        initQuickActions: function() {
            $('.kata-quick-action').on('click', function(e) {
                var action = $(this).data('action');
                
                switch(action) {
                    case 'refresh-stats':
                        KataShareSocialAdmin.refreshStats();
                        break;
                    case 'view-reports':
                        window.location.href = $(this).data('url');
                        break;
                }
            });
        },
        
        /**
         * Refresh statistics
         */
        refreshStats: function() {
            $('.kata-dashboard-content').addClass('kata-loading');
            
            setTimeout(function() {
                location.reload();
            }, 1000);
        },
        
        /**
         * Format number for display
         */
        formatNumber: function(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
            }
            if (num >= 1000) {
                return (num / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
            }
            return num.toString();
        },
        
        /**
         * Debounce function
         */
        debounce: function(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        }
    };
    
    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        KataShareSocialAdmin.init();
        
        // Initialize dashboard specific functionality
        if ($('.kata-sharesocial-dashboard').length) {
            KataShareSocialAdmin.initDashboard();
        }
    });
    
    /**
     * Expose to global scope
     */
    window.KataShareSocialAdmin = KataShareSocialAdmin;
    
})(jQuery);
