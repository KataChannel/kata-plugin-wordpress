/**
 * Kata SEO Analyzer - Admin JavaScript
 * 
 * @package KataSEOAnalyzer
 * @version 2.0.0
 */

(function($) {
    'use strict';

    // Global object for Kata SEO functionality
    window.KataSEO = window.KataSEO || {};

    /**
     * Main initialization function
     */
    KataSEO.init = function() {
        this.bindEvents();
        this.initComponents();
        this.loadDashboardData();
        this.initCharts();
        this.initTooltips();
        this.setupAutoRefresh();
    };

    /**
     * Bind event handlers
     */
    KataSEO.bindEvents = function() {
        // Analysis buttons
        $(document).on('click', '.analyze-post-btn', this.analyzePost);
        $(document).on('click', '.bulk-analyze-btn', this.bulkAnalyze);
        $(document).on('click', '.refresh-analysis-btn', this.refreshAnalysis);

        // AI suggestions
        $(document).on('click', '.get-ai-suggestions-btn', this.getAISuggestions);
        $(document).on('click', '.apply-suggestion-btn', this.applySuggestion);
        $(document).on('click', '.dismiss-suggestion-btn', this.dismissSuggestion);

        // Competitor analysis
        $(document).on('click', '.add-competitor-btn', this.showAddCompetitorModal);
        $(document).on('click', '.analyze-competitor-btn', this.analyzeCompetitor);
        $(document).on('click', '.remove-competitor-btn', this.removeCompetitor);

        // Export functionality
        $(document).on('click', '.export-report-btn', this.exportReport);
        $(document).on('click', '.export-csv-btn', this.exportCSV);

        // Settings
        $(document).on('click', '.save-settings-btn', this.saveSettings);
        $(document).on('click', '.test-api-btn', this.testAPIConnection);

        // Chart interactions
        $(document).on('click', '.chart-filter', this.filterChart);
        $(document).on('change', '.chart-period-select', this.updateChartPeriod);

        // Modal controls
        $(document).on('click', '.kata-modal-close', this.closeModal);
        $(document).on('click', '.kata-modal-overlay', this.closeModalOnOverlay);

        // Search and filters
        $(document).on('input', '.search-posts', this.debounce(this.searchPosts, 300));
        $(document).on('change', '.filter-posts', this.filterPosts);

        // Pagination
        $(document).on('click', '.pagination-link', this.loadPage);

        // Real-time updates
        $(document).on('change', '#auto-analyze-checkbox', this.toggleAutoAnalyze);
        $(document).on('change', '#enable-ai-checkbox', this.toggleAI);
    };

    /**
     * Initialize components
     */
    KataSEO.initComponents = function() {
        // Initialize select2 for better dropdowns
        if ($.fn.select2) {
            $('.kata-select2').select2({
                theme: 'default',
                width: '100%'
            });
        }

        // Initialize date pickers
        if ($.fn.datepicker) {
            $('.kata-datepicker').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });
        }

        // Initialize progress bars
        this.initProgressBars();

        // Initialize counters
        this.initCounters();

        // Initialize tabs
        this.initTabs();
    };

    /**
     * Load dashboard data
     */
    KataSEO.loadDashboardData = function() {
        if (!$('.kata-seo-dashboard').length) return;

        this.showLoading('.stats-grid');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_get_dashboard_data',
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    KataSEO.updateDashboardStats(response.data);
                } else {
                    KataSEO.showNotification('Error loading dashboard data', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('Failed to load dashboard data', 'error');
            },
            complete: function() {
                KataSEO.hideLoading('.stats-grid');
            }
        });
    };

    /**
     * Update dashboard statistics
     */
    KataSEO.updateDashboardStats = function(data) {
        // Update stat cards
        $.each(data.stats, function(key, value) {
            var $statCard = $('.stat-card[data-stat="' + key + '"]');
            if ($statCard.length) {
                $statCard.find('.stat-number').text(value);
                $statCard.addClass('fade-in');
            }
        });

        // Update charts if data is available
        if (data.charts) {
            this.updateCharts(data.charts);
        }

        // Update recent activity
        if (data.recent_activity) {
            this.updateRecentActivity(data.recent_activity);
        }
    };

    /**
     * Analyze a single post
     */
    KataSEO.analyzePost = function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var postId = $button.data('post-id');
        var originalText = $button.text();

        if (!postId) {
            KataSEO.showNotification('Invalid post ID', 'error');
            return;
        }

        $button.prop('disabled', true).html('<span class="loading-spinner"></span> ' + kataSEO.strings.analyzing);

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_analyze_content',
                post_id: postId,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    KataSEO.showNotification('Analysis completed successfully!', 'success');
                    KataSEO.updatePostAnalysis(postId, response.data);
                } else {
                    KataSEO.showNotification(response.data || 'Analysis failed', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('Analysis request failed', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    };

    /**
     * Bulk analyze posts
     */
    KataSEO.bulkAnalyze = function(e) {
        e.preventDefault();

        var selectedPosts = [];
        $('.post-checkbox:checked').each(function() {
            selectedPosts.push($(this).val());
        });

        if (selectedPosts.length === 0) {
            KataSEO.showNotification('Please select at least one post', 'warning');
            return;
        }

        var $button = $(this);
        var originalText = $button.text();

        $button.prop('disabled', true).html('<span class="loading-spinner"></span> Analyzing...');

        // Process posts in batches to avoid timeouts
        KataSEO.processBulkAnalysis(selectedPosts, 0, $button, originalText);
    };

    /**
     * Process bulk analysis in batches
     */
    KataSEO.processBulkAnalysis = function(posts, currentIndex, $button, originalText) {
        var batchSize = 5;
        var batch = posts.slice(currentIndex, currentIndex + batchSize);

        if (batch.length === 0) {
            // All done
            $button.prop('disabled', false).text(originalText);
            KataSEO.showNotification('Bulk analysis completed!', 'success');
            location.reload(); // Refresh to show updated data
            return;
        }

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_bulk_analyze',
                post_ids: batch,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    var progress = ((currentIndex + batch.length) / posts.length) * 100;
                    $button.html('<span class="loading-spinner"></span> Analyzing... ' + Math.round(progress) + '%');
                    
                    // Process next batch
                    setTimeout(function() {
                        KataSEO.processBulkAnalysis(posts, currentIndex + batchSize, $button, originalText);
                    }, 1000);
                } else {
                    KataSEO.showNotification('Bulk analysis failed: ' + response.data, 'error');
                    $button.prop('disabled', false).text(originalText);
                }
            },
            error: function() {
                KataSEO.showNotification('Bulk analysis request failed', 'error');
                $button.prop('disabled', false).text(originalText);
            }
        });
    };

    /**
     * Get AI suggestions for a post
     */
    KataSEO.getAISuggestions = function(e) {
        e.preventDefault();

        var $button = $(this);
        var postId = $button.data('post-id');
        var originalText = $button.text();

        if (!postId) {
            KataSEO.showNotification('Invalid post ID', 'error');
            return;
        }

        $button.prop('disabled', true).html('<span class="loading-spinner"></span> Generating...');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_get_ai_suggestions',
                post_id: postId,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    KataSEO.displayAISuggestions(postId, response.data);
                    KataSEO.showNotification('AI suggestions generated!', 'success');
                } else {
                    KataSEO.showNotification(response.data || 'Failed to generate suggestions', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('AI suggestion request failed', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    };

    /**
     * Display AI suggestions
     */
    KataSEO.displayAISuggestions = function(postId, suggestions) {
        var $container = $('.ai-suggestions-container[data-post-id="' + postId + '"]');
        
        if (!$container.length) {
            $container = $('.ai-suggestions-container');
        }

        if (!$container.length) return;

        var html = '';
        
        if (suggestions.enabled && suggestions.suggestions) {
            $.each(suggestions.suggestions, function(index, suggestion) {
                html += '<div class="suggestion-item" data-suggestion-id="' + index + '">';
                html += '<div class="suggestion-priority priority-' + suggestion.priority + '">' + suggestion.priority + '</div>';
                html += '<div class="suggestion-title">' + suggestion.title + '</div>';
                html += '<div class="suggestion-description">' + suggestion.description + '</div>';
                html += '<div class="suggestion-actions">';
                html += '<button class="suggestion-btn apply-suggestion-btn" data-suggestion="' + index + '">Apply</button>';
                html += '<button class="suggestion-btn secondary dismiss-suggestion-btn" data-suggestion="' + index + '">Dismiss</button>';
                html += '</div>';
                html += '</div>';
            });
        } else {
            html = '<div class="suggestion-item"><div class="suggestion-description">' + 
                   (suggestions.message || 'No suggestions available') + '</div></div>';
        }

        $container.find('.ai-suggestions-body').html(html);
        $container.addClass('fade-in');
    };

    /**
     * Initialize charts
     */
    KataSEO.initCharts = function() {
        if (!window.Chart || !$('.chart-canvas').length) return;

        // SEO Score Trend Chart
        this.initScoreTrendChart();

        // Score Distribution Chart
        this.initScoreDistributionChart();

        // Issue Frequency Chart
        this.initIssueFrequencyChart();

        // Performance Over Time Chart
        this.initPerformanceChart();
    };

    /**
     * Initialize score trend chart
     */
    KataSEO.initScoreTrendChart = function() {
        var $canvas = $('#score-trend-chart');
        if (!$canvas.length) return;

        var ctx = $canvas[0].getContext('2d');
        
        // Sample data - replace with actual data from server
        var data = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Average SEO Score',
                data: [65, 70, 75, 80, 78, 85],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    };

    /**
     * Initialize score distribution chart
     */
    KataSEO.initScoreDistributionChart = function() {
        var $canvas = $('#score-distribution-chart');
        if (!$canvas.length) return;

        var ctx = $canvas[0].getContext('2d');
        
        var data = {
            labels: ['Excellent (90+)', 'Good (80-89)', 'Fair (60-79)', 'Poor (<60)'],
            datasets: [{
                data: [15, 35, 40, 10],
                backgroundColor: [
                    '#48bb78',
                    '#ed8936',
                    '#ecc94b',
                    '#f56565'
                ]
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    };

    /**
     * Show add competitor modal
     */
    KataSEO.showAddCompetitorModal = function(e) {
        e.preventDefault();

        var modal = `
            <div class="kata-modal-overlay">
                <div class="kata-modal">
                    <div class="kata-modal-header">
                        <h3 class="kata-modal-title">Add Competitor</h3>
                        <button class="kata-modal-close">&times;</button>
                    </div>
                    <div class="kata-modal-body">
                        <form id="add-competitor-form">
                            <div class="kata-form-group">
                                <label class="kata-form-label">Domain</label>
                                <input type="text" class="kata-form-input" name="domain" placeholder="example.com" required>
                                <div class="kata-form-help">Enter domain without http:// or www</div>
                            </div>
                            <div class="kata-form-group">
                                <label class="kata-form-label">Keywords (comma-separated)</label>
                                <textarea class="kata-form-input kata-form-textarea" name="keywords" placeholder="keyword1, keyword2, keyword3"></textarea>
                                <div class="kata-form-help">Enter keywords you want to track for this competitor</div>
                            </div>
                        </form>
                    </div>
                    <div class="kata-modal-footer">
                        <button class="kata-btn kata-btn-secondary kata-modal-close">Cancel</button>
                        <button class="kata-btn kata-btn-primary" id="save-competitor-btn">Add Competitor</button>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modal);

        // Handle form submission
        $('#save-competitor-btn').on('click', function() {
            var $form = $('#add-competitor-form');
            var domain = $form.find('[name="domain"]').val().trim();
            var keywords = $form.find('[name="keywords"]').val().trim();

            if (!domain) {
                KataSEO.showNotification('Please enter a domain', 'error');
                return;
            }

            var keywordArray = keywords ? keywords.split(',').map(k => k.trim()).filter(k => k) : [];

            KataSEO.addCompetitor(domain, keywordArray);
        });
    };

    /**
     * Add competitor
     */
    KataSEO.addCompetitor = function(domain, keywords) {
        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_add_competitor',
                domain: domain,
                keywords: keywords,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    KataSEO.showNotification('Competitor added successfully!', 'success');
                    KataSEO.closeModal();
                    location.reload(); // Refresh to show new competitor
                } else {
                    KataSEO.showNotification(response.data || 'Failed to add competitor', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('Request failed', 'error');
            }
        });
    };

    /**
     * Export report
     */
    KataSEO.exportReport = function(e) {
        e.preventDefault();

        var reportType = $(this).data('report-type') || 'overview';
        var format = $(this).data('format') || 'pdf';

        KataSEO.showNotification('Generating report...', 'info');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_export_report',
                report_type: reportType,
                format: format,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Download the file
                    var link = document.createElement('a');
                    link.href = response.data.download_url;
                    link.download = response.data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    KataSEO.showNotification('Report generated successfully!', 'success');
                } else {
                    KataSEO.showNotification(response.data || 'Export failed', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('Export request failed', 'error');
            }
        });
    };

    /**
     * Save settings
     */
    KataSEO.saveSettings = function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        var formData = $form.serialize();

        var $button = $(this);
        var originalText = $button.text();

        $button.prop('disabled', true).text('Saving...');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: formData + '&action=kata_seo_save_settings&nonce=' + kataSEO.nonce,
            success: function(response) {
                if (response.success) {
                    KataSEO.showNotification('Settings saved successfully!', 'success');
                } else {
                    KataSEO.showNotification(response.data || 'Failed to save settings', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('Save request failed', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    };

    /**
     * Test API connection
     */
    KataSEO.testAPIConnection = function(e) {
        e.preventDefault();

        var apiType = $(this).data('api-type');
        var $button = $(this);
        var originalText = $button.text();

        $button.prop('disabled', true).text('Testing...');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_test_api',
                api_type: apiType,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    KataSEO.showNotification('API connection successful!', 'success');
                } else {
                    KataSEO.showNotification(response.data || 'API connection failed', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('API test request failed', 'error');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    };

    /**
     * Utility functions
     */

    /**
     * Show notification
     */
    KataSEO.showNotification = function(message, type) {
        type = type || 'info';

        var notification = $('<div class="kata-notification kata-notification-' + type + '">' + message + '</div>');
        
        // Remove existing notifications
        $('.kata-notification').remove();

        // Add new notification
        $('body').append(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);

        // Add close button
        notification.append('<button class="kata-notification-close">&times;</button>');
        notification.find('.kata-notification-close').on('click', function() {
            notification.fadeOut(function() {
                $(this).remove();
            });
        });
    };

    /**
     * Show loading indicator
     */
    KataSEO.showLoading = function(selector) {
        var $element = $(selector);
        if (!$element.length) return;

        var $loading = $('<div class="kata-loading-overlay"><div class="loading-spinner"></div></div>');
        $element.css('position', 'relative').append($loading);
    };

    /**
     * Hide loading indicator
     */
    KataSEO.hideLoading = function(selector) {
        $(selector).find('.kata-loading-overlay').remove();
    };

    /**
     * Close modal
     */
    KataSEO.closeModal = function() {
        $('.kata-modal-overlay').fadeOut(function() {
            $(this).remove();
        });
    };

    /**
     * Close modal when clicking on overlay
     */
    KataSEO.closeModalOnOverlay = function(e) {
        if (e.target === this) {
            KataSEO.closeModal();
        }
    };

    /**
     * Debounce function
     */
    KataSEO.debounce = function(func, wait, immediate) {
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
    };

    /**
     * Initialize progress bars
     */
    KataSEO.initProgressBars = function() {
        $('.progress-bar').each(function() {
            var $progressBar = $(this);
            var $fill = $progressBar.find('.progress-fill');
            var targetWidth = $fill.data('width') || 0;

            // Animate to target width
            setTimeout(function() {
                $fill.css('width', targetWidth + '%');
            }, 500);
        });
    };

    /**
     * Initialize counters
     */
    KataSEO.initCounters = function() {
        $('.stat-number[data-count]').each(function() {
            var $counter = $(this);
            var targetCount = parseInt($counter.data('count'), 10);
            var currentCount = 0;
            var increment = targetCount / 100;

            var timer = setInterval(function() {
                currentCount += increment;
                if (currentCount >= targetCount) {
                    currentCount = targetCount;
                    clearInterval(timer);
                }
                $counter.text(Math.floor(currentCount));
            }, 20);
        });
    };

    /**
     * Initialize tabs
     */
    KataSEO.initTabs = function() {
        $('.kata-tabs').each(function() {
            var $tabContainer = $(this);
            var $tabs = $tabContainer.find('.kata-tab');
            var $panels = $tabContainer.find('.kata-tab-panel');

            $tabs.on('click', function(e) {
                e.preventDefault();
                
                var targetPanel = $(this).attr('href');
                
                // Update active states
                $tabs.removeClass('active');
                $(this).addClass('active');
                
                $panels.removeClass('active');
                $(targetPanel).addClass('active');
            });
        });
    };

    /**
     * Initialize tooltips
     */
    KataSEO.initTooltips = function() {
        if ($.fn.tooltip) {
            $('.kata-tooltip').tooltip({
                placement: 'top',
                trigger: 'hover'
            });
        }
    };

    /**
     * Setup auto-refresh for dashboard
     */
    KataSEO.setupAutoRefresh = function() {
        if ($('.kata-seo-dashboard').length && kataSEO.autoRefresh) {
            setInterval(function() {
                KataSEO.loadDashboardData();
            }, 60000); // Refresh every minute
        }
    };

    /**
     * Search posts
     */
    KataSEO.searchPosts = function() {
        var query = $(this).val();
        var $container = $('.posts-list-container');
        
        if (query.length < 3 && query.length > 0) {
            return;
        }

        KataSEO.showLoading('.posts-list-container');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_search_posts',
                query: query,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    $container.html(response.data);
                }
            },
            complete: function() {
                KataSEO.hideLoading('.posts-list-container');
            }
        });
    };

    /**
     * Filter posts
     */
    KataSEO.filterPosts = function() {
        var filter = $(this).val();
        var $container = $('.posts-list-container');
        
        KataSEO.showLoading('.posts-list-container');

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_filter_posts',
                filter: filter,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    $container.html(response.data);
                }
            },
            complete: function() {
                KataSEO.hideLoading('.posts-list-container');
            }
        });
    };

    /**
     * Load page
     */
    KataSEO.loadPage = function(e) {
        e.preventDefault();
        
        var url = $(this).attr('href');
        var page = new URL(url).searchParams.get('paged') || 1;
        
        KataSEO.showLoading('.posts-list-container');
        
        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_load_page',
                page: page,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.posts-list-container').html(response.data);
                    // Update URL without page reload
                    history.pushState(null, null, url);
                }
            },
            complete: function() {
                KataSEO.hideLoading('.posts-list-container');
            }
        });
    };

    /**
     * Update post analysis display
     */
    KataSEO.updatePostAnalysis = function(postId, data) {
        var $container = $('.post-analysis-container[data-post-id="' + postId + '"]');
        
        if ($container.length) {
            // Update score
            $container.find('.seo-score-number').text(data.score);
            $container.find('.seo-score').removeClass('excellent good fair poor')
                     .addClass(KataSEO.getScoreClass(data.score));
            
            // Update factors
            if (data.analysis) {
                $.each(data.analysis, function(factor, info) {
                    var $factor = $container.find('.seo-factor[data-factor="' + factor + '"]');
                    if ($factor.length) {
                        $factor.find('.factor-status').text(info.status ? '✅' : '❌');
                        $factor.find('.factor-value').text(info.current || '');
                        
                        if (!info.status && info.suggestion) {
                            $factor.find('.factor-suggestion').text(info.suggestion).show();
                        } else {
                            $factor.find('.factor-suggestion').hide();
                        }
                    }
                });
            }
        }
        
        // Update any score displays in lists
        $('.post-row[data-post-id="' + postId + '"] .seo-score').text(data.score + '/100')
            .removeClass('excellent good fair poor').addClass(KataSEO.getScoreClass(data.score));
    };

    /**
     * Get score CSS class
     */
    KataSEO.getScoreClass = function(score) {
        if (score >= 90) return 'excellent';
        if (score >= 80) return 'good';
        if (score >= 60) return 'fair';
        return 'poor';
    };

    // Initialize when document is ready
    $(document).ready(function() {
        KataSEO.init();
    });

    // Admin bar integration
    window.kataSEOAnalyzeFromBar = function(postId) {
        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_analyze_content',
                post_id: postId,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update admin bar score
                    $('#wp-admin-bar-kata-seo .ab-item').html(
                        '<span style="color: ' + KataSEO.getScoreColor(response.data.score) + ';">SEO: ' + 
                        response.data.score + '/100</span>'
                    );
                    
                    KataSEO.showNotification('Analysis completed! Score: ' + response.data.score, 'success');
                } else {
                    KataSEO.showNotification('Analysis failed', 'error');
                }
            },
            error: function() {
                KataSEO.showNotification('Analysis request failed', 'error');
            }
        });
    };

    /**
     * Get score color
     */
    KataSEO.getScoreColor = function(score) {
        if (score >= 90) return '#0073aa';
        if (score >= 80) return '#46b450';
        if (score >= 60) return '#ffb900';
        if (score >= 40) return '#f56e28';
        return '#dc3232';
    };

})(jQuery);

// Add notification styles if not already present
jQuery(document).ready(function($) {
    if (!$('#kata-notification-styles').length) {
        var styles = `
            <style id="kata-notification-styles">
            .kata-notification {
                position: fixed;
                top: 32px;
                right: 20px;
                z-index: 999999;
                padding: 15px 20px;
                border-radius: 4px;
                color: white;
                font-weight: 500;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                max-width: 400px;
                animation: slideInRight 0.3s ease-out;
            }
            
            .kata-notification-success {
                background: #46b450;
            }
            
            .kata-notification-error {
                background: #dc3232;
            }
            
            .kata-notification-warning {
                background: #ffb900;
            }
            
            .kata-notification-info {
                background: #0073aa;
            }
            
            .kata-notification-close {
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                cursor: pointer;
                float: right;
                margin-left: 10px;
                opacity: 0.8;
                padding: 0;
                line-height: 1;
            }
            
            .kata-notification-close:hover {
                opacity: 1;
            }
            
            .kata-loading-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(255,255,255,0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            </style>
        `;
        $('head').append(styles);
    }
});
