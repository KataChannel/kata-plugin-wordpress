/**
 * Kata ShareSocial Frontend JavaScript
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * Share buttons handler
     */
    var KataShareSocial = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initCopyLinkFunction();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            $(document).on('click', '.kata-share-button', this.handleShareClick);
        },
        
        /**
         * Handle share button click
         */
        handleShareClick: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var platform = $button.data('platform');
            var postId = $button.closest('.kata-share-buttons').data('post-id');
            var shareUrl = $button.attr('href');
            var specialAction = $button.data('special-action');
            
            // Handle special actions
            if (specialAction === 'copy_link') {
                KataShareSocial.copyLink($button, shareUrl);
                return;
            }
            
            // Track the share
            KataShareSocial.trackShare(postId, platform, $button);
            
            // Open share popup
            if ($button.data('popup-width') > 0 && $button.data('popup-height') > 0) {
                KataShareSocial.openSharePopup(shareUrl, $button.data('popup-width'), $button.data('popup-height'));
            } else {
                // For email and other non-popup shares
                window.location.href = shareUrl;
            }
        },
        
        /**
         * Open share popup
         */
        openSharePopup: function(url, width, height) {
            var left = (screen.width / 2) - (width / 2);
            var top = (screen.height / 2) - (height / 2);
            
            var popup = window.open(
                url,
                'shareWindow',
                'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top + ',scrollbars=yes,resizable=yes'
            );
            
            if (popup && popup.focus) {
                popup.focus();
            }
            
            return popup;
        },
        
        /**
         * Copy link to clipboard
         */
        copyLink: function($button, url) {
            var postUrl = url || window.location.href;
            
            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(postUrl).then(function() {
                    KataShareSocial.showCopyToast('Link copied to clipboard!');
                    KataShareSocial.animateButton($button, 'success');
                }).catch(function() {
                    KataShareSocial.fallbackCopyLink(postUrl, $button);
                });
            } else {
                KataShareSocial.fallbackCopyLink(postUrl, $button);
            }
        },
        
        /**
         * Fallback copy method for older browsers
         */
        fallbackCopyLink: function(text, $button) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                var successful = document.execCommand('copy');
                if (successful) {
                    KataShareSocial.showCopyToast('Link copied to clipboard!');
                    KataShareSocial.animateButton($button, 'success');
                } else {
                    KataShareSocial.showCopyToast('Could not copy link', 'error');
                }
            } catch (err) {
                // Fallback: select the text for manual copy
                KataShareSocial.showCopyToast('Please copy the link manually: ' + text, 'info');
            }
            
            document.body.removeChild(textArea);
        },
        
        /**
         * Show copy toast message
         */
        showCopyToast: function(message, type) {
            type = type || 'success';
            
            // Remove existing toast
            $('.kata-copy-toast').remove();
            
            var $toast = $('<div class="kata-copy-toast kata-toast-' + type + '">' + message + '</div>');
            $('body').append($toast);
            
            // Show toast
            setTimeout(function() {
                $toast.addClass('show');
            }, 100);
            
            // Hide toast after 3 seconds
            setTimeout(function() {
                $toast.removeClass('show');
                setTimeout(function() {
                    $toast.remove();
                }, 300);
            }, 3000);
        },
        
        /**
         * Animate button feedback
         */
        animateButton: function($button, state) {
            $button.addClass(state);
            
            setTimeout(function() {
                $button.removeClass(state);
            }, 1500);
        },
        
        /**
         * Track share action
         */
        trackShare: function(postId, platform, $button) {
            // Only track if analytics is enabled
            if (!kataShareSocial.nonce) {
                return;
            }
            
            $button.addClass('loading');
            
            $.ajax({
                url: kataShareSocial.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'kata_share_track',
                    post_id: postId,
                    platform: platform,
                    nonce: kataShareSocial.nonce
                },
                success: function(response) {
                    $button.removeClass('loading');
                    
                    if (response.success) {
                        // Update share counts in UI
                        KataShareSocial.updateShareCounts(postId, platform, response.data);
                        KataShareSocial.animateButton($button, 'success');
                    }
                },
                error: function() {
                    $button.removeClass('loading');
                    console.log('Share tracking failed');
                }
            });
        },
        
        /**
         * Update share counts in UI
         */
        updateShareCounts: function(postId, platform, data) {
            var $container = $('.kata-share-buttons[data-post-id="' + postId + '"]');
            
            // Update platform specific count
            var $platformButton = $container.find('.kata-platform-' + platform);
            var $platformCount = $platformButton.find('.kata-platform-count');
            
            if ($platformCount.length && data.count > 0) {
                $platformCount.text(KataShareSocial.formatNumber(data.count));
            } else if (data.count > 0 && $platformCount.length === 0) {
                // Add count if it doesn't exist
                var $buttonText = $platformButton.find('.kata-button-text');
                if ($buttonText.length) {
                    $buttonText.append('<span class="kata-platform-count">' + KataShareSocial.formatNumber(data.count) + '</span>');
                }
            }
            
            // Update total count
            var $totalCount = $container.find('.kata-share-count-number');
            if ($totalCount.length && data.total > 0) {
                $totalCount.text(KataShareSocial.formatNumber(data.total));
            }
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
         * Initialize copy link functionality
         */
        initCopyLinkFunction: function() {
            // Check if copy is supported
            if (!navigator.clipboard && !document.execCommand) {
                // Hide copy link buttons if not supported
                $('.kata-platform-copy_link').hide();
            }
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
        KataShareSocial.init();
    });
    
    /**
     * Handle window events
     */
    $(window).on('load', function() {
        // Additional initialization after page load if needed
    });
    
    /**
     * Expose to global scope for external access
     */
    window.KataShareSocial = KataShareSocial;
    
})(jQuery);
