/**
 * Schema Statistics Page - JavaScript
 * 
 * Handles interactive features:
 * - Search posts by title
 * - Filter by schema type
 * - Highlight recommended schemas
 * - Copy schema shortcode
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

(function($) {
    'use strict';
    
    var KataSchemaStats = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.initTooltips();
            this.animateNumbers();
        },
        
        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Search functionality
            $('#kata-search-posts').on('keyup', this.handleSearch.bind(this));
            
            // Filter by schema type
            $('#kata-filter-schema').on('change', this.handleFilter.bind(this));
            
            // Schema tag click to copy shortcode
            $('.kata-schema-tag.recommended, .kata-schema-tag.suggested').on('click', this.copyShortcode.bind(this));
            
            // Expand/collapse help cards
            $('.kata-help-card h3').on('click', function() {
                $(this).parent().toggleClass('collapsed');
            });
        },
        
        /**
         * Handle search
         */
        handleSearch: function(e) {
            var searchTerm = $(e.target).val().toLowerCase();
            var visibleCount = 0;
            
            $('#kata-posts-tbody tr').each(function() {
                var title = $(this).data('title');
                var postId = $(this).data('post-id');
                
                if (title && (title.includes(searchTerm) || String(postId).includes(searchTerm))) {
                    $(this).fadeIn(200);
                    visibleCount++;
                } else {
                    $(this).fadeOut(200);
                }
            });
            
            // Show/hide empty state
            this.updateEmptyState('#kata-posts-tbody', visibleCount, 'Không tìm thấy bài viết nào phù hợp.');
        },
        
        /**
         * Handle filter
         */
        handleFilter: function(e) {
            var selectedSchema = $(e.target).val();
            var visibleCount = 0;
            
            if (selectedSchema === '') {
                $('#kata-posts-tbody tr').fadeIn(200);
                visibleCount = $('#kata-posts-tbody tr').length;
            } else {
                $('#kata-posts-tbody tr').each(function() {
                    var schemas = $(this).data('schemas');
                    if (schemas) {
                        var schemaArray = String(schemas).split(',');
                        if (schemaArray.includes(selectedSchema)) {
                            $(this).fadeIn(200);
                            visibleCount++;
                        } else {
                            $(this).fadeOut(200);
                        }
                    } else {
                        $(this).fadeOut(200);
                    }
                });
            }
            
            // Update search placeholder
            if (selectedSchema) {
                $('#kata-search-posts').attr('placeholder', 'Tìm trong ' + selectedSchema + ' schemas...');
            } else {
                $('#kata-search-posts').attr('placeholder', 'Tìm kiếm bài viết...');
            }
            
            this.updateEmptyState('#kata-posts-tbody', visibleCount, 'Không có bài viết nào dùng schema này.');
        },
        
        /**
         * Update empty state
         */
        updateEmptyState: function(tableId, visibleCount, message) {
            var $table = $(tableId).closest('.kata-posts-table-wrapper');
            var $emptyState = $table.find('.kata-table-empty-state');
            
            if (visibleCount === 0) {
                if ($emptyState.length === 0) {
                    $table.append(
                        '<div class="kata-table-empty-state">' +
                        '<span class="dashicons dashicons-search"></span>' +
                        '<p>' + message + '</p>' +
                        '</div>'
                    );
                }
                $table.find('table').hide();
            } else {
                $emptyState.remove();
                $table.find('table').show();
            }
        },
        
        /**
         * Copy schema shortcode
         */
        copyShortcode: function(e) {
            var $tag = $(e.currentTarget);
            var schemaType = $tag.text().trim().replace(/^\+\s*/, '').replace(/^\★\s*/, '');
            
            // Convert schema type to shortcode name
            // Article -> kata_article, LocalBusiness -> kata_local_business
            var shortcodeName = 'kata_' + schemaType.replace(/([A-Z])/g, function(match, p1) {
                return '_' + p1.toLowerCase();
            }).replace(/^_/, '');
            
            var shortcode = '[' + shortcodeName + ']';
            
            // Copy to clipboard
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(shortcode).select();
            document.execCommand('copy');
            $temp.remove();
            
            // Show notification
            this.showNotification('✅ Đã copy: ' + shortcode, 'success');
            
            // Visual feedback
            $tag.addClass('copied');
            setTimeout(function() {
                $tag.removeClass('copied');
            }, 1000);
        },
        
        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            // Add tooltips to schema tags
            $('.kata-schema-tag.active').attr('title', 'Schema đang được sử dụng');
            $('.kata-schema-tag.recommended').attr('title', 'Click để copy shortcode');
            $('.kata-schema-tag.suggested').attr('title', 'Click để copy shortcode');
            $('.kata-schema-tag.unused').attr('title', 'Schema chưa được sử dụng');
        },
        
        /**
         * Animate numbers
         */
        animateNumbers: function() {
            $('.kata-stat-number').each(function() {
                var $this = $(this);
                var targetValue = parseInt($this.text()) || 0;
                
                if (targetValue > 0) {
                    $this.text('0');
                    
                    $({ value: 0 }).animate({ value: targetValue }, {
                        duration: 1200,
                        easing: 'swing',
                        step: function() {
                            $this.text(Math.floor(this.value));
                        },
                        complete: function() {
                            $this.text(targetValue);
                        }
                    });
                }
            });
            
            // Animate progress bars
            $('.schema-type-bar-fill').each(function() {
                var $this = $(this);
                var targetWidth = $this.css('width');
                $this.css('width', '0');
                setTimeout(function() {
                    $this.css('width', targetWidth);
                }, 100);
            });
        },
        
        /**
         * Show notification
         */
        showNotification: function(message, type) {
            var $notification = $('<div class="kata-notification kata-notification-' + type + '">' + message + '</div>');
            $('body').append($notification);
            
            setTimeout(function() {
                $notification.addClass('show');
            }, 10);
            
            setTimeout(function() {
                $notification.removeClass('show');
                setTimeout(function() {
                    $notification.remove();
                }, 300);
            }, 2000);
        },
        
        /**
         * Export to CSV
         */
        exportToCSV: function() {
            var csv = 'ID,Title,Type,Schemas,Schema Count,Modified\n';
            
            $('#kata-posts-tbody tr:visible').each(function() {
                var $row = $(this);
                var postId = $row.data('post-id');
                var title = $row.find('.post-title-cell strong').text().replace(/,/g, ';');
                var type = $row.find('.post-type-badge').text();
                var schemas = $row.data('schemas');
                var schemaCount = schemas ? schemas.split(',').length : 0;
                var modified = $row.find('.post-modified').text();
                
                csv += '"' + postId + '","' + title + '","' + type + '","' + schemas + '","' + schemaCount + '","' + modified + '"\n';
            });
            
            // Download CSV
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'kata-schema-statistics-' + Date.now() + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            this.showNotification('✅ Đã xuất file CSV', 'success');
        },
        
        /**
         * Print statistics
         */
        printStatistics: function() {
            window.print();
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        KataSchemaStats.init();
    });
    
    // Expose to global scope for external access
    window.KataSchemaStats = KataSchemaStats;
    
})(jQuery);

/**
 * Additional CSS for notifications (injected dynamically)
 */
jQuery(document).ready(function($) {
    var notificationStyles = `
        <style id="kata-notification-styles">
            .kata-notification {
                position: fixed;
                top: 32px;
                right: 20px;
                background: white;
                padding: 16px 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                font-size: 14px;
                font-weight: 500;
                z-index: 999999;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.3s ease;
            }
            
            .kata-notification.show {
                opacity: 1;
                transform: translateX(0);
            }
            
            .kata-notification-success {
                border-left: 4px solid #4caf50;
                color: #2e7d32;
            }
            
            .kata-notification-error {
                border-left: 4px solid #f44336;
                color: #c62828;
            }
            
            .kata-notification-info {
                border-left: 4px solid #2196f3;
                color: #1565c0;
            }
            
            .kata-schema-tag.copied {
                transform: scale(1.1);
                box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
            }
            
            .kata-help-card.collapsed ol,
            .kata-help-card.collapsed ul {
                display: none;
            }
            
            .kata-help-card h3 {
                cursor: pointer;
                user-select: none;
            }
            
            .kata-help-card h3:hover {
                color: #667eea;
            }
            
            .kata-table-empty-state {
                text-align: center;
                padding: 48px 24px;
                color: #94a3b8;
            }
            
            .kata-table-empty-state .dashicons {
                font-size: 48px;
                opacity: 0.5;
                margin-bottom: 12px;
            }
            
            @media print {
                .kata-stats-actions,
                .kata-section-actions,
                .kata-btn-small,
                .kata-notification {
                    display: none !important;
                }
                
                .kata-posts-table {
                    font-size: 11px;
                }
                
                .kata-posts-table td {
                    padding: 8px;
                }
            }
        </style>
    `;
    
    if ($('#kata-notification-styles').length === 0) {
        $('head').append(notificationStyles);
    }
});
