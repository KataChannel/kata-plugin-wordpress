/**
 * Schema Statistics Page - JavaScript - ES6 Refactored
 * 
 * Handles interactive features:
 * - Search posts by title
 * - Filter by schema type
 * - Highlight recommended schemas
 * - Copy schema shortcode
 * - Export to CSV
 * - Print statistics
 * 
 * @package KATA_SEO_Manager
 * @since 2.2.0
 * @version 2.2.0
 */

(function($) {
    'use strict';
    
    /**
     * Schema Statistics Controller Class
     * @class KataSchemaStats
     */
    class KataSchemaStats {
        /**
         * Initialize Statistics Controller
         * @constructor
         */
        constructor() {
            this.config = {
                searchDelay: 300,
                animationDuration: 1200,
                notificationDuration: 2000,
                fadeSpeed: 200
            };
            this.searchTimeout = null;
            this.currentFilter = '';
            this.init();
        }
        
        /**
         * Initialize component
         * @returns {void}
         */
        init() {
            this.bindEvents();
            this.initTooltips();
            this.animateNumbers();
            this.injectStyles();
        }
        
        /**
         * Bind event listeners
         * @returns {void}
         */
        bindEvents() {
            $('#kata-search-posts').on('keyup', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => this.handleSearch(e), this.config.searchDelay);
            });
            $('#kata-filter-schema').on('change', (e) => this.handleFilter(e));
            $('.kata-schema-tag.recommended, .kata-schema-tag.suggested').on('click', (e) => this.copyShortcode(e));
            $('.kata-help-card h3').on('click', function() {
                $(this).parent().toggleClass('collapsed');
            });
            $(document).on('click', '[data-action="export-csv"]', () => this.exportToCSV());
            $(document).on('click', '[data-action="print-stats"]', () => this.printStatistics());
        }
        
        /**
         * Handle search with filtering
         * @param {Event} e - Input event
         * @returns {void}
         */
        handleSearch(e) {
            const searchTerm = $(e.target).val().toLowerCase().trim();
            let visibleCount = 0;
            
            $('#kata-posts-tbody tr').each((index, element) => {
                const $row = $(element);
                const title = $row.data('title') || '';
                const postId = String($row.data('post-id') || '');
                
                const isVisible = !searchTerm || 
                    title.toLowerCase().includes(searchTerm) || 
                    postId.includes(searchTerm);

                if (isVisible) {
                    $row.fadeIn(this.config.fadeSpeed);
                    visibleCount++;
                } else {
                    $row.fadeOut(this.config.fadeSpeed);
                }
            });
            
            this.updateEmptyState('#kata-posts-tbody', visibleCount, 
                searchTerm ? 'Không tìm thấy bài viết nào phù hợp.' : 'Không có bài viết nào.');
        }
        
        /**
         * Handle schema type filter
         * @param {Event} e - Change event
         * @returns {void}
         */
        handleFilter(e) {
            const selectedSchema = $(e.target).val();
            this.currentFilter = selectedSchema;
            let visibleCount = 0;
            
            if (selectedSchema === '') {
                $('#kata-posts-tbody tr').fadeIn(this.config.fadeSpeed);
                visibleCount = $('#kata-posts-tbody tr').length;
            } else {
                $('#kata-posts-tbody tr').each((index, element) => {
                    const $row = $(element);
                    const schemas = $row.data('schemas');
                    
                    if (schemas) {
                        const schemaArray = String(schemas).split(',').map(s => s.trim());
                        if (schemaArray.includes(selectedSchema)) {
                            $row.fadeIn(this.config.fadeSpeed);
                            visibleCount++;
                        } else {
                            $row.fadeOut(this.config.fadeSpeed);
                        }
                    } else {
                        $row.fadeOut(this.config.fadeSpeed);
                    }
                });
            }
            
            const placeholder = selectedSchema 
                ? `Tìm trong ${selectedSchema} schemas...` 
                : 'Tìm kiếm bài viết...';
            $('#kata-search-posts').attr('placeholder', placeholder);
            
            this.updateEmptyState('#kata-posts-tbody', visibleCount, 
                `Không có bài viết nào dùng schema ${selectedSchema || 'này'}.`);
        }
        
        /**
         * Update empty state display
         * @param {string} tableId - Table selector
         * @param {number} visibleCount - Number of visible rows
         * @param {string} message - Empty state message
         * @returns {void}
         */
        updateEmptyState(tableId, visibleCount, message) {
            const $table = $(tableId).closest('.kata-posts-table-wrapper');
            const $emptyState = $table.find('.kata-table-empty-state');
            
            if (visibleCount === 0) {
                if ($emptyState.length === 0) {
                    $table.append(`
                        <div class="kata-table-empty-state">
                            <span class="dashicons dashicons-search"></span>
                            <p>${this.escapeHtml(message)}</p>
                        </div>
                    `);
                }
                $table.find('table').hide();
            } else {
                $emptyState.remove();
                $table.find('table').show();
            }
        }
        
        /**
         * Copy schema shortcode to clipboard
         * @param {Event} e - Click event
         * @returns {void}
         */
        copyShortcode(e) {
            const $tag = $(e.currentTarget);
            const schemaType = $tag.text().trim()
                .replace(/^\+\s*/, '')
                .replace(/^\★\s*/, '');
            
            const shortcodeName = 'kata_' + schemaType
                .replace(/([A-Z])/g, (match, p1) => '_' + p1.toLowerCase())
                .replace(/^_/, '');
            
            const shortcode = `[${shortcodeName}]`;
            
            this.copyToClipboard(shortcode);
            this.showNotification(`✅ Đã copy: ${shortcode}`, 'success');
            
            $tag.addClass('copied');
            setTimeout(() => $tag.removeClass('copied'), 1000);
        }
        
        /**
         * Initialize tooltips
         * @returns {void}
         */
        initTooltips() {
            const tooltips = {
                '.kata-schema-tag.active': 'Schema đang được sử dụng',
                '.kata-schema-tag.recommended': 'Click để copy shortcode - Schema được khuyên dùng',
                '.kata-schema-tag.suggested': 'Click để copy shortcode - Schema gợi ý',
                '.kata-schema-tag.unused': 'Schema chưa được sử dụng'
            };
            Object.entries(tooltips).forEach(([selector, title]) => {
                $(selector).attr('title', title);
            });
        }
        
        /**
         * Animate numbers and progress bars
         * @returns {void}
         */
        animateNumbers() {
            $('.kata-stat-number').each((index, element) => {
                const $element = $(element);
                const targetValue = parseInt($element.text()) || 0;
                
                if (targetValue > 0) {
                    $element.text('0');
                    
                    $({ value: 0 }).animate({ value: targetValue }, {
                        duration: this.config.animationDuration,
                        easing: 'swing',
                        step: function() {
                            $element.text(Math.floor(this.value));
                        },
                        complete: () => {
                            $element.text(targetValue);
                        }
                    });
                }
            });
            
            $('.schema-type-bar-fill').each((index, element) => {
                const $bar = $(element);
                const targetWidth = $bar.css('width');
                $bar.css('width', '0');
                
                setTimeout(() => {
                    $bar.css({
                        width: targetWidth,
                        transition: 'width 0.8s cubic-bezier(0.4, 0, 0.2, 1)'
                    });
                }, 100 + (index * 50));
            });
        }
        
        /**
         * Show notification toast
         * @param {string} message - Notification message
         * @param {string} type - Notification type (success|error|info)
         * @returns {void}
         */
        showNotification(message, type = 'info') {
            const $notification = $(`
                <div class="kata-notification kata-notification-${type}">
                    ${this.escapeHtml(message)}
                </div>
            `);
            
            $('body').append($notification);
            setTimeout(() => $notification.addClass('show'), 10);
            setTimeout(() => {
                $notification.removeClass('show');
                setTimeout(() => $notification.remove(), 300);
            }, this.config.notificationDuration);
        }
        
        /**
         * Export statistics to CSV
         * @returns {void}
         */
        exportToCSV() {
            try {
                const headers = ['ID', 'Title', 'Type', 'Schemas', 'Schema Count', 'Modified'];
                const rows = [headers.join(',')];
                
                $('#kata-posts-tbody tr:visible').each((index, element) => {
                    const $row = $(element);
                    const postId = $row.data('post-id') || '';
                    const title = ($row.find('.post-title-cell strong').text() || '')
                        .replace(/,/g, ';').replace(/"/g, '""');
                    const type = $row.find('.post-type-badge').text() || '';
                    const schemas = $row.data('schemas') || '';
                    const schemaCount = schemas ? schemas.split(',').length : 0;
                    const modified = $row.find('.post-modified').text() || '';
                    
                    rows.push(`"${postId}","${title}","${type}","${schemas}","${schemaCount}","${modified}"`);
                });
                
                const csv = rows.join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                const filename = `kata-schema-statistics-${Date.now()}.csv`;
                
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
                
                this.showNotification('✅ Đã xuất file CSV', 'success');
            } catch (error) {
                console.error('CSV export error:', error);
                this.showNotification('❌ Lỗi khi xuất file CSV', 'error');
            }
        }
        
        /**
         * Print statistics page
         * @returns {void}
         */
        printStatistics() {
            window.print();
        }
        
        /**
         * Copy text to clipboard with fallback
         * @param {string} text - Text to copy
         * @returns {void}
         */
        copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).catch(() => this.fallbackCopy(text));
            } else {
                this.fallbackCopy(text);
            }
        }
        
        /**
         * Fallback clipboard copy method
         * @param {string} text - Text to copy
         * @returns {void}
         */
        fallbackCopy(text) {
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
        }
        
        /**
         * Escape HTML to prevent XSS
         * @param {string} text - Text to escape
         * @returns {string} Escaped text
         */
        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, m => map[m]);
        }
        
        /**
         * Inject dynamic styles
         * @returns {void}
         */
        injectStyles() {
            if ($('#kata-notification-styles').length > 0) return;
            
            const styles = `
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
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    }
                    .kata-notification.show {
                        opacity: 1;
                        transform: translateX(0);
                    }
                    .kata-notification-success {
                        border-left: 4px solid var(--kata-success-color, #4caf50);
                        color: var(--kata-success-text, #2e7d32);
                    }
                    .kata-notification-error {
                        border-left: 4px solid var(--kata-error-color, #f44336);
                        color: var(--kata-error-text, #c62828);
                    }
                    .kata-notification-info {
                        border-left: 4px solid var(--kata-info-color, #2196f3);
                        color: var(--kata-info-text, #1565c0);
                    }
                    .kata-schema-tag.copied {
                        transform: scale(1.1);
                        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
                        transition: all 0.2s ease;
                    }
                    .kata-help-card.collapsed ol,
                    .kata-help-card.collapsed ul,
                    .kata-help-card.collapsed p {
                        display: none;
                    }
                    .kata-help-card h3 {
                        cursor: pointer;
                        user-select: none;
                        transition: color 0.2s ease;
                    }
                    .kata-help-card h3:hover {
                        color: var(--kata-primary-color, #667eea);
                    }
                    .kata-table-empty-state {
                        text-align: center;
                        padding: 48px 24px;
                        color: #94a3b8;
                        animation: fadeIn 0.3s ease;
                    }
                    .kata-table-empty-state .dashicons {
                        font-size: 48px;
                        opacity: 0.5;
                        margin-bottom: 12px;
                    }
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(-10px); }
                        to { opacity: 1; transform: translateY(0); }
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
                        .kata-help-card.collapsed ol,
                        .kata-help-card.collapsed ul,
                        .kata-help-card.collapsed p {
                            display: block !important;
                        }
                    }
                </style>
            `;
            $('head').append(styles);
        }
    }

    // Create and expose global instance
    const kataSchemaStatsInstance = new KataSchemaStats();
    window.KataSchemaStats = kataSchemaStatsInstance;

})(jQuery);
