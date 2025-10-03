/**
 * Kata SEO Tools - Admin JavaScript
 * 
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    /**
     * Admin object
     */
    window.kataSEOAdmin = {
        
        /**
         * Initialize
         */
        init: function() {
            this.initMetaBoxes();
            this.initFormBuilder();
            this.initQuizBuilder();
            this.initAnalytics();
            this.initColorPickers();
            this.initMediaUploaders();
        },
        
        // ===================================
        // META BOXES
        // ===================================
        
        /**
         * Initialize meta boxes
         */
        initMetaBoxes: function() {
            this.initSchemaMetaBox();
            this.initSocialMetaBox();
            this.initFAQMetaBox();
        },
        
        /**
         * Schema meta box
         */
        initSchemaMetaBox: function() {
            $('#kata_schema_type').on('change', function() {
                var type = $(this).val();
                $('.kata-schema-options').hide();
                $('.kata-schema-' + type + '-options').show();
            }).trigger('change');
        },
        
        /**
         * Social meta box
         */
        initSocialMetaBox: function() {
            var self = this;
            
            // Update preview on input change
            $('#kata_og_title, #kata_og_description, #kata_og_image').on('input', function() {
                self.updateSocialPreview();
            });
        },
        
        /**
         * Update social preview
         */
        updateSocialPreview: function() {
            var title = $('#kata_og_title').val() || $('#title').val();
            var description = $('#kata_og_description').val();
            var image = $('#kata_og_image').val();
            
            $('.kata-og-preview h4').text(title);
            $('.kata-og-preview p').text(description);
            
            if (image) {
                $('.kata-og-preview img').attr('src', image).show();
            } else {
                $('.kata-og-preview img').hide();
            }
        },
        
        /**
         * FAQ meta box
         */
        initFAQMetaBox: function() {
            var self = this;
            
            // Add FAQ item
            $('#kata-faq-add-btn').on('click', function() {
                self.addFAQItem();
            });
            
            // Remove FAQ item
            $(document).on('click', '.kata-faq-remove', function() {
                $(this).closest('.kata-faq-item-editor').remove();
                self.updateFAQIndices();
            });
            
            // Make FAQ items sortable
            if ($.fn.sortable) {
                $('.kata-faq-items').sortable({
                    handle: '.kata-faq-item-editor',
                    update: function() {
                        self.updateFAQIndices();
                    }
                });
            }
        },
        
        /**
         * Add FAQ item
         */
        addFAQItem: function() {
            var index = $('.kata-faq-item-editor').length;
            var template = `
                <div class="kata-faq-item-editor">
                    <button type="button" class="kata-faq-remove">✕ Remove</button>
                    <div class="kata-meta-field">
                        <label>Question</label>
                        <input type="text" name="kata_faq_items[${index}][question]" required>
                    </div>
                    <div class="kata-meta-field">
                        <label>Answer</label>
                        <textarea name="kata_faq_items[${index}][answer]" rows="4" required></textarea>
                    </div>
                </div>
            `;
            
            $('.kata-faq-items').append(template);
        },
        
        /**
         * Update FAQ indices
         */
        updateFAQIndices: function() {
            $('.kata-faq-item-editor').each(function(index) {
                $(this).find('input, textarea').each(function() {
                    var name = $(this).attr('name');
                    name = name.replace(/\[\d+\]/, '[' + index + ']');
                    $(this).attr('name', name);
                });
            });
        },
        
        // ===================================
        // FORM BUILDER
        // ===================================
        
        /**
         * Initialize form builder
         */
        initFormBuilder: function() {
            var self = this;
            
            // Add field buttons
            $('.kata-field-type-btn').on('click', function() {
                var fieldType = $(this).data('field-type');
                self.addFormField(fieldType);
            });
            
            // Remove field
            $(document).on('click', '.kata-field-remove', function() {
                $(this).closest('.kata-form-field-item').remove();
            });
            
            // Edit field
            $(document).on('click', '.kata-field-edit', function() {
                var fieldItem = $(this).closest('.kata-form-field-item');
                fieldItem.toggleClass('active');
            });
            
            // Make fields sortable
            if ($.fn.sortable) {
                $('.kata-form-builder-canvas').sortable({
                    handle: '.kata-form-field-item',
                    placeholder: 'kata-field-placeholder'
                });
            }
        },
        
        /**
         * Add form field
         */
        addFormField: function(type) {
            var timestamp = Date.now();
            var template = `
                <div class="kata-form-field-item" data-field-type="${type}">
                    <div class="kata-field-item-controls">
                        <button type="button" class="kata-field-edit">Edit</button>
                        <button type="button" class="kata-field-remove delete-btn">Delete</button>
                    </div>
                    
                    <div class="kata-meta-field">
                        <label>Field Label</label>
                        <input type="text" name="form_fields[${timestamp}][label]" value="${this.getFieldTypeLabel(type)}">
                    </div>
                    
                    <div class="kata-meta-field">
                        <label>Field Name</label>
                        <input type="text" name="form_fields[${timestamp}][name]" value="${type}_${timestamp}">
                    </div>
                    
                    <div class="kata-meta-field">
                        <label>Field Type</label>
                        <input type="text" name="form_fields[${timestamp}][type]" value="${type}" readonly>
                    </div>
                    
                    <div class="kata-meta-field-row">
                        <div>
                            <label>
                                <input type="checkbox" name="form_fields[${timestamp}][required]" value="1">
                                Required
                            </label>
                        </div>
                    </div>
                    
                    ${type === 'select' || type === 'radio' || type === 'checkbox' ? `
                        <div class="kata-meta-field">
                            <label>Options (one per line)</label>
                            <textarea name="form_fields[${timestamp}][options]" rows="4"></textarea>
                        </div>
                    ` : ''}
                </div>
            `;
            
            $('.kata-form-builder-canvas').append(template);
        },
        
        /**
         * Get field type label
         */
        getFieldTypeLabel: function(type) {
            var labels = {
                'text': 'Text Field',
                'email': 'Email Address',
                'tel': 'Phone Number',
                'textarea': 'Message',
                'select': 'Dropdown',
                'radio': 'Radio Buttons',
                'checkbox': 'Checkboxes'
            };
            
            return labels[type] || type.charAt(0).toUpperCase() + type.slice(1);
        },
        
        // ===================================
        // QUIZ BUILDER
        // ===================================
        
        /**
         * Initialize quiz builder
         */
        initQuizBuilder: function() {
            var self = this;
            
            // Add question
            $('#kata-quiz-add-question').on('click', function() {
                self.addQuizQuestion();
            });
            
            // Remove question
            $(document).on('click', '.kata-quiz-remove-question', function() {
                $(this).closest('.kata-quiz-question-item').remove();
                self.updateQuizQuestionNumbers();
            });
            
            // Add option
            $(document).on('click', '.kata-quiz-add-option', function() {
                var questionItem = $(this).closest('.kata-quiz-question-item');
                self.addQuizOption(questionItem);
            });
            
            // Remove option
            $(document).on('click', '.kata-quiz-remove-option', function() {
                $(this).closest('.kata-quiz-option-item').remove();
            });
        },
        
        /**
         * Add quiz question
         */
        addQuizQuestion: function() {
            var questionIndex = $('.kata-quiz-question-item').length;
            var template = `
                <div class="kata-quiz-question-item">
                    <div class="kata-quiz-question-header">
                        <span class="kata-quiz-question-number">Question ${questionIndex + 1}</span>
                        <button type="button" class="kata-quiz-remove-question kata-btn kata-btn-danger">Remove</button>
                    </div>
                    
                    <div class="kata-meta-field">
                        <label>Question Text</label>
                        <input type="text" name="quiz_questions[${questionIndex}][question]" required>
                    </div>
                    
                    <div class="kata-quiz-options-list">
                        <label>Answer Options</label>
                        <div class="kata-quiz-option-item">
                            <input type="radio" name="quiz_questions[${questionIndex}][correct]" value="0">
                            <input type="text" name="quiz_questions[${questionIndex}][options][]" placeholder="Option 1" required>
                            <button type="button" class="kata-quiz-remove-option">✕</button>
                        </div>
                        <div class="kata-quiz-option-item">
                            <input type="radio" name="quiz_questions[${questionIndex}][correct]" value="1">
                            <input type="text" name="quiz_questions[${questionIndex}][options][]" placeholder="Option 2" required>
                            <button type="button" class="kata-quiz-remove-option">✕</button>
                        </div>
                    </div>
                    
                    <button type="button" class="kata-quiz-add-option">+ Add Option</button>
                    
                    <div class="kata-meta-field" style="margin-top: 15px;">
                        <label>Explanation (optional)</label>
                        <textarea name="quiz_questions[${questionIndex}][explanation]" rows="2"></textarea>
                    </div>
                </div>
            `;
            
            $('.kata-quiz-builder').append(template);
        },
        
        /**
         * Add quiz option
         */
        addQuizOption: function(questionItem) {
            var questionIndex = $('.kata-quiz-question-item').index(questionItem);
            var optionIndex = questionItem.find('.kata-quiz-option-item').length;
            
            var template = `
                <div class="kata-quiz-option-item">
                    <input type="radio" name="quiz_questions[${questionIndex}][correct]" value="${optionIndex}">
                    <input type="text" name="quiz_questions[${questionIndex}][options][]" placeholder="Option ${optionIndex + 1}" required>
                    <button type="button" class="kata-quiz-remove-option">✕</button>
                </div>
            `;
            
            questionItem.find('.kata-quiz-options-list').append(template);
        },
        
        /**
         * Update quiz question numbers
         */
        updateQuizQuestionNumbers: function() {
            $('.kata-quiz-question-item').each(function(index) {
                $(this).find('.kata-quiz-question-number').text('Question ' + (index + 1));
                
                // Update field names
                $(this).find('input, textarea').each(function() {
                    var name = $(this).attr('name');
                    if (name) {
                        name = name.replace(/quiz_questions\[\d+\]/, 'quiz_questions[' + index + ']');
                        $(this).attr('name', name);
                    }
                });
            });
        },
        
        // ===================================
        // ANALYTICS
        // ===================================
        
        /**
         * Initialize analytics
         */
        initAnalytics: function() {
            var self = this;
            
            // Date range filter
            $('#kata-analytics-date-from, #kata-analytics-date-to').on('change', function() {
                self.loadAnalyticsData();
            });
            
            // Feature filter
            $('#kata-analytics-feature').on('change', function() {
                self.loadAnalyticsData();
            });
            
            // Load initial data
            this.loadAnalyticsData();
        },
        
        /**
         * Load analytics data
         */
        loadAnalyticsData: function() {
            var dateFrom = $('#kata-analytics-date-from').val();
            var dateTo = $('#kata-analytics-date-to').val();
            var feature = $('#kata-analytics-feature').val();
            
            // Show loading
            $('.kata-chart-container').append('<div class="kata-loading">Loading...</div>');
            
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'kata_seo_load_analytics',
                    nonce: kata_seo_admin.nonce,
                    date_from: dateFrom,
                    date_to: dateTo,
                    feature: feature
                },
                success: function(response) {
                    if (response.success) {
                        kataSEOAdmin.renderAnalyticsCharts(response.data);
                    }
                    $('.kata-loading').remove();
                },
                error: function() {
                    $('.kata-loading').remove();
                    alert('Failed to load analytics data');
                }
            });
        },
        
        /**
         * Render analytics charts
         */
        renderAnalyticsCharts: function(data) {
            // Update stat cards
            $('#total-interactions').text(data.total_interactions || 0);
            $('#total-quiz-submissions').text(data.total_quiz_submissions || 0);
            $('#total-poll-votes').text(data.total_poll_votes || 0);
            $('#total-ratings').text(data.total_ratings || 0);
            
            // Render charts using Chart.js (if available)
            if (typeof Chart !== 'undefined') {
                this.renderInteractionsChart(data.interactions_over_time);
                this.renderFeatureUsageChart(data.feature_usage);
            }
        },
        
        /**
         * Render interactions chart
         */
        renderInteractionsChart: function(data) {
            var ctx = document.getElementById('interactions-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Interactions',
                        data: data.values,
                        borderColor: '#4A90E2',
                        backgroundColor: 'rgba(74, 144, 226, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        },
        
        /**
         * Render feature usage chart
         */
        renderFeatureUsageChart: function(data) {
            var ctx = document.getElementById('feature-usage-chart');
            if (!ctx) return;
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: [
                            '#4A90E2',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#17a2b8',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        },
        
        // ===================================
        // UTILITIES
        // ===================================
        
        /**
         * Initialize color pickers
         */
        initColorPickers: function() {
            if ($.fn.wpColorPicker) {
                $('.kata-color-input').wpColorPicker();
            }
        },
        
        /**
         * Initialize media uploaders
         */
        initMediaUploaders: function() {
            var self = this;
            
            $('.kata-upload-image-btn').on('click', function(e) {
                e.preventDefault();
                
                var button = $(this);
                var targetInput = button.data('target');
                
                var frame = wp.media({
                    title: 'Select Image',
                    button: {
                        text: 'Use Image'
                    },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $(targetInput).val(attachment.url);
                    button.siblings('.kata-image-preview').html('<img src="' + attachment.url + '" style="max-width: 200px;">');
                });
                
                frame.open();
            });
            
            // Remove image
            $('.kata-remove-image-btn').on('click', function(e) {
                e.preventDefault();
                var targetInput = $(this).data('target');
                $(targetInput).val('');
                $(this).siblings('.kata-image-preview').html('');
            });
        },
        
        /**
         * Show notification
         */
        showNotification: function(message, type) {
            type = type || 'success';
            
            var notification = $('<div class="kata-notification kata-notification-' + type + '">' + message + '</div>');
            $('body').append(notification);
            
            setTimeout(function() {
                notification.addClass('show');
            }, 100);
            
            setTimeout(function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 3000);
        },
        
        /**
         * Confirm dialog
         */
        confirm: function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        kataSEOAdmin.init();
    });
    
})(jQuery);
