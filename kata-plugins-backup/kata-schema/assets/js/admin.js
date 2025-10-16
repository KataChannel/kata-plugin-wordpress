/**
 * KATA Schema Admin JavaScript
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Admin page initialization
        if ($('.kata-schema-dashboard').length || $('.kata-schema-add').length || $('.kata-schema-templates').length) {
            initAdmin();
        }
        
    });
    
    /**
     * Initialize admin functionality
     */
    function initAdmin() {
        console.log('KATA Schema Admin initialized');
        
        // Add smooth scrolling
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 32
                }, 500);
            }
        });
        
        // Show loading on AJAX requests
        $(document).ajaxStart(function() {
            showLoading();
        }).ajaxStop(function() {
            hideLoading();
        });
    }
    
    /**
     * Show loading indicator
     */
    function showLoading() {
        if ($('#kata-loading-overlay').length === 0) {
            $('body').append('<div id="kata-loading-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); z-index: 99999; display: flex; align-items: center; justify-content: center;"><div class="spinner is-active" style="float: none; width: 40px; height: 40px; margin: 0;"></div></div>');
        }
    }
    
    /**
     * Hide loading indicator
     */
    function hideLoading() {
        $('#kata-loading-overlay').fadeOut(200, function() {
            $(this).remove();
        });
    }
    
    /**
     * Copy to clipboard utility
     */
    window.kataSchemaClipboard = function(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                showNotice('Đã copy vào clipboard!', 'success');
            }).catch(function() {
                showNotice('Không thể copy. Vui lòng copy thủ công.', 'error');
            });
        } else {
            // Fallback for older browsers
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showNotice('Đã copy vào clipboard!', 'success');
            } catch (err) {
                showNotice('Không thể copy. Vui lòng copy thủ công.', 'error');
            }
            document.body.removeChild(textarea);
        }
    };
    
    /**
     * Show notice
     */
    function showNotice(message, type) {
        type = type || 'info';
        
        var notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        
        if ($('.wrap > h1').length) {
            $('.wrap > h1').after(notice);
        } else {
            $('.wrap').prepend(notice);
        }
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            notice.fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
})(jQuery);
