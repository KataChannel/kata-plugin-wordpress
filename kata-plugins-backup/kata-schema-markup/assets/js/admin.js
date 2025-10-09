/**
 * Admin JavaScript for Kata Schema Markup
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

(function($) {
    'use strict';
    
    // Main plugin object
    var KataSchemaAdmin = {
        
        /**
         * Initialize the admin interface
         */
        init: function() {
            this.bindEvents();
            this.initializeComponents();
        },
        
        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Tab navigation
            $(document).on('click', '.nav-tab', this.handleTabClick);
            
            // Schema type selection
            $(document).on('change', '#kata_schema_type', this.handleSchemaTypeChange);
            
            // Template management
            $(document).on('click', '.kata-schema-template-card', this.handleTemplateSelect);
            $(document).on('click', '#kata-schema-save-template', this.handleTemplateSave);
            $(document).on('click', '.kata-schema-delete-template', this.handleTemplateDelete);
            
            // Validation
            $(document).on('click', '#kata-schema-validate', this.handleSchemaValidation);
            $(document).on('click', '#kata-schema-test-google', this.handleGoogleTest);
            
            // JSON editor tools
            $(document).on('click', '#kata-schema-format-json', this.handleJsonFormat);
            $(document).on('click', '#kata-schema-validate-json', this.handleJsonValidation);
            $(document).on('click', '#kata-schema-reset-json', this.handleJsonReset);
            
            // Preview generation
            $(document).on('click', '#kata-schema-generate-preview', this.handlePreviewGeneration);
            $(document).on('click', '#kata-schema-copy-preview', this.handlePreviewCopy);
            
            // Template loading
            $(document).on('click', '#kata-schema-load-template', this.handleTemplateLoad);
            
            // Cache management
            $(document).on('click', '#kata-clear-cache', this.handleCacheClear);
            
            // Form submissions
            $(document).on('submit', '.kata-schema-form', this.handleFormSubmit);
            
            // Toggle switches
            $(document).on('change', '.kata-schema-toggle input', this.handleToggleChange);
        },
        
        /**
         * Initialize components
         */
        initializeComponents: function() {
            this.initializeCodeEditors();
            this.initializeTooltips();
            this.initializeValidation();
        },
        
        /**
         * Handle tab clicks
         */
        handleTabClick: function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var target = $tab.attr('href');
            
            // Update tab states
            $tab.siblings().removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Update content
            $('.tab-content').removeClass('active');
            $(target).addClass('active');
            
            // Refresh code editor if switching to custom tab
            if (target === '#tab-custom' && window.kataSchemaEditor) {
                setTimeout(function() {
                    window.kataSchemaEditor.codemirror.refresh();
                }, 100);
            }
        },
        
        /**
         * Handle schema type changes
         */
        handleSchemaTypeChange: function() {
            var $select = $(this);
            var selectedType = $select.val();
            var description = $select.find('option:selected').data('description');
            
            // Update description
            var $description = $('#schema-type-description');
            if (description) {
                $description.html('<p>' + description + '</p>').show();
            } else {
                $description.hide();
            }
            
            // Show/hide additional fields
            $('.additional-fields').hide();
            if (selectedType) {
                $('.' + selectedType + '-fields').show();
            }
            
            // Load default template for type
            KataSchemaAdmin.loadDefaultTemplate(selectedType);
        },
        
        /**
         * Handle template selection
         */
        handleTemplateSelect: function() {
            var $card = $(this);
            
            // Update visual state
            $card.siblings().removeClass('active');
            $card.addClass('active');
            
            // Load template data
            var templateId = $card.data('template-id');
            if (templateId) {
                KataSchemaAdmin.loadTemplate(templateId);
            }
        },
        
        /**
         * Handle template save
         */
        handleTemplateSave: function() {
            var templateData = KataSchemaAdmin.getTemplateFormData();
            
            if (!KataSchemaAdmin.validateTemplateData(templateData)) {
                return;
            }
            
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_save_schema_template',
                    nonce: kataSchemaAdmin.nonce,
                    ...templateData
                },
                beforeSend: function() {
                    KataSchemaAdmin.showSpinner('#kata-schema-save-template');
                },
                success: function(response) {
                    KataSchemaAdmin.hideSpinner('#kata-schema-save-template');
                    
                    if (response.success) {
                        KataSchemaAdmin.showNotice(response.data.message, 'success');
                        KataSchemaAdmin.refreshTemplatesList();
                    } else {
                        KataSchemaAdmin.showNotice(response.data.message, 'error');
                    }
                },
                error: function() {
                    KataSchemaAdmin.hideSpinner('#kata-schema-save-template');
                    KataSchemaAdmin.showNotice(kataSchemaAdmin.i18n.error, 'error');
                }
            });
        },
        
        /**
         * Handle template deletion
         */
        handleTemplateDelete: function() {
            if (!confirm(kataSchemaAdmin.i18n.confirmDelete)) {
                return;
            }
            
            var templateId = $(this).data('template-id');
            
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_delete_schema_template',
                    nonce: kataSchemaAdmin.nonce,
                    template_id: templateId
                },
                success: function(response) {
                    if (response.success) {
                        KataSchemaAdmin.showNotice(response.data.message, 'success');
                        KataSchemaAdmin.refreshTemplatesList();
                    } else {
                        KataSchemaAdmin.showNotice(response.data.message, 'error');
                    }
                },
                error: function() {
                    KataSchemaAdmin.showNotice(kataSchemaAdmin.i18n.error, 'error');
                }
            });
        },
        
        /**
         * Handle schema validation
         */
        handleSchemaValidation: function() {
            var schemaData = KataSchemaAdmin.getCurrentSchemaData();
            
            if (!schemaData) {
                KataSchemaAdmin.showNotice('Please enter schema JSON to validate.', 'error');
                return;
            }
            
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_test_schema',
                    nonce: kataSchemaAdmin.nonce,
                    schema_json: schemaData
                },
                beforeSend: function() {
                    $('#kata-schema-validation-results').html('<p>' + kataSchemaAdmin.i18n.validating + '</p>');
                },
                success: function(response) {
                    if (response.success) {
                        KataSchemaAdmin.displayValidationResults(response.data);
                    } else {
                        KataSchemaAdmin.showNotice('Validation failed', 'error');
                    }
                },
                error: function() {
                    KataSchemaAdmin.showNotice(kataSchemaAdmin.i18n.error, 'error');
                }
            });
        },
        
        /**
         * Handle Google testing
         */
        handleGoogleTest: function() {
            var schemaData = KataSchemaAdmin.getCurrentSchemaData();
            
            if (!schemaData) {
                KataSchemaAdmin.showNotice('Please enter schema JSON to test.', 'error');
                return;
            }
            
            // Open Google's Structured Data Testing Tool
            var testUrl = 'https://search.google.com/test/rich-results';
            var popup = window.open(testUrl, '_blank', 'width=1200,height=800');
            
            if (popup) {
                KataSchemaAdmin.showNotice('Google Rich Results Test opened in new window. Copy and paste your schema there.', 'info');
            }
        },
        
        /**
         * Handle JSON formatting
         */
        handleJsonFormat: function() {
            try {
                var editor = KataSchemaAdmin.getCodeEditor();
                var jsonString = editor ? editor.codemirror.getValue() : $('#kata_schema_data').val();
                
                var parsed = JSON.parse(jsonString);
                var formatted = JSON.stringify(parsed, null, 2);
                
                if (editor) {
                    editor.codemirror.setValue(formatted);
                } else {
                    $('#kata_schema_data').val(formatted);
                }
                
                KataSchemaAdmin.showNotice('JSON formatted successfully!', 'success');
            } catch (e) {
                KataSchemaAdmin.showNotice('Invalid JSON: ' + e.message, 'error');
            }
        },
        
        /**
         * Handle JSON validation
         */
        handleJsonValidation: function() {
            try {
                var jsonString = KataSchemaAdmin.getCurrentSchemaData();
                JSON.parse(jsonString);
                KataSchemaAdmin.showNotice('JSON is valid!', 'success');
            } catch (e) {
                KataSchemaAdmin.showNotice('Invalid JSON: ' + e.message, 'error');
            }
        },
        
        /**
         * Handle JSON reset
         */
        handleJsonReset: function() {
            if (!confirm('Are you sure you want to reset the JSON? This will clear all custom schema data.')) {
                return;
            }
            
            var editor = KataSchemaAdmin.getCodeEditor();
            if (editor) {
                editor.codemirror.setValue('');
            } else {
                $('#kata_schema_data').val('');
            }
            
            KataSchemaAdmin.showNotice('JSON reset successfully.', 'info');
        },
        
        /**
         * Handle preview generation
         */
        handlePreviewGeneration: function() {
            var postId = $('#post_ID').val() || 0;
            var schemaType = $('#kata_schema_type').val() || 'article';
            
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_generate_schema_preview',
                    nonce: kataSchemaAdmin.nonce,
                    post_id: postId,
                    schema_type: schemaType
                },
                beforeSend: function() {
                    $('#kata-schema-preview-content').html('<p>Generating preview...</p>');
                },
                success: function(response) {
                    if (response.success) {
                        var previewHtml = '<pre><code>' + KataSchemaAdmin.escapeHtml(response.data.schema) + '</code></pre>';
                        $('#kata-schema-preview-content').html(previewHtml);
                    } else {
                        $('#kata-schema-preview-content').html('<p class="error">Failed to generate preview: ' + response.data.message + '</p>');
                    }
                },
                error: function() {
                    $('#kata-schema-preview-content').html('<p class="error">Error occurred while generating preview.</p>');
                }
            });
        },
        
        /**
         * Handle preview copy
         */
        handlePreviewCopy: function() {
            var previewText = $('#kata-schema-preview-content code').text();
            
            if (previewText) {
                KataSchemaAdmin.copyToClipboard(previewText);
                KataSchemaAdmin.showNotice('Schema copied to clipboard!', 'success');
            } else {
                KataSchemaAdmin.showNotice('No preview content to copy.', 'error');
            }
        },
        
        /**
         * Handle template loading
         */
        handleTemplateLoad: function() {
            var selectedType = $('#kata-schema-template-select').val();
            
            if (!selectedType) {
                KataSchemaAdmin.showNotice('Please select a template type.', 'error');
                return;
            }
            
            KataSchemaAdmin.loadDefaultTemplate(selectedType);
        },
        
        /**
         * Handle cache clearing
         */
        handleCacheClear: function() {
            var $button = $(this);
            var originalText = $button.text();
            
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_clear_schema_cache',
                    nonce: kataSchemaAdmin.nonce
                },
                beforeSend: function() {
                    $button.text('Clearing...').prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        $button.text('Cleared!');
                        KataSchemaAdmin.showNotice(response.data.message, 'success');
                        
                        setTimeout(function() {
                            $button.text(originalText).prop('disabled', false);
                        }, 2000);
                    } else {
                        $button.text(originalText).prop('disabled', false);
                        KataSchemaAdmin.showNotice('Failed to clear cache', 'error');
                    }
                },
                error: function() {
                    $button.text(originalText).prop('disabled', false);
                    KataSchemaAdmin.showNotice(kataSchemaAdmin.i18n.error, 'error');
                }
            });
        },
        
        /**
         * Handle form submissions
         */
        handleFormSubmit: function(e) {
            var $form = $(this);
            
            // Validate form before submission
            if (!KataSchemaAdmin.validateForm($form)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            KataSchemaAdmin.showFormLoading($form);
        },
        
        /**
         * Handle toggle changes
         */
        handleToggleChange: function() {
            var $toggle = $(this);
            var isChecked = $toggle.is(':checked');
            var relatedFields = $toggle.data('related-fields');
            
            if (relatedFields) {
                $(relatedFields).toggle(isChecked);
            }
        },
        
        /**
         * Initialize code editors
         */
        initializeCodeEditors: function() {
            if (typeof wp !== 'undefined' && wp.codeEditor) {
                var $textarea = $('#kata_schema_data');
                
                if ($textarea.length) {
                    var editorSettings = wp.codeEditor.defaultSettings ? 
                        _.clone(wp.codeEditor.defaultSettings) : {};
                    
                    editorSettings.codemirror = _.extend({}, editorSettings.codemirror, {
                        mode: 'application/json',
                        lineNumbers: true,
                        theme: 'default',
                        indentUnit: 2,
                        tabSize: 2,
                        lineWrapping: true,
                        autoCloseBrackets: true,
                        matchBrackets: true,
                        foldGutter: true,
                        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter']
                    });
                    
                    window.kataSchemaEditor = wp.codeEditor.initialize($textarea, editorSettings);
                }
            }
        },
        
        /**
         * Initialize tooltips
         */
        initializeTooltips: function() {
            if ($.fn.tooltip) {
                $('.kata-schema-tooltip').tooltip({
                    position: { my: 'left+15 center', at: 'right center' },
                    content: function() {
                        return $(this).attr('title') || $(this).data('tooltip');
                    }
                });
            }
        },
        
        /**
         * Initialize validation
         */
        initializeValidation: function() {
            // Add custom validation rules
            if ($.validator) {
                $.validator.addMethod('validJson', function(value, element) {
                    if (!value) return true;
                    try {
                        JSON.parse(value);
                        return true;
                    } catch (e) {
                        return false;
                    }
                }, 'Please enter valid JSON.');
            }
        },
        
        /**
         * Utility functions
         */
        
        /**
         * Get current schema data
         */
        getCurrentSchemaData: function() {
            var editor = this.getCodeEditor();
            return editor ? editor.codemirror.getValue() : $('#kata_schema_data').val();
        },
        
        /**
         * Get code editor instance
         */
        getCodeEditor: function() {
            return window.kataSchemaEditor || null;
        },
        
        /**
         * Load default template for schema type
         */
        loadDefaultTemplate: function(schemaType) {
            if (!schemaType || !kataSchemaAdmin.schemaTypes[schemaType]) {
                return;
            }
            
            var template = this.getDefaultTemplate(schemaType);
            var editor = this.getCodeEditor();
            
            if (editor) {
                editor.codemirror.setValue(template);
            } else {
                $('#kata_schema_data').val(template);
            }
        },
        
        /**
         * Get default template for schema type
         */
        getDefaultTemplate: function(schemaType) {
            var templates = {
                article: {
                    '@context': 'https://schema.org',
                    '@type': 'Article',
                    'headline': '{{post_title}}',
                    'description': '{{post_excerpt}}',
                    'image': '{{featured_image}}',
                    'author': {
                        '@type': 'Person',
                        'name': '{{author_name}}'
                    },
                    'publisher': {
                        '@type': 'Organization',
                        'name': '{{organization_name}}',
                        'logo': '{{organization_logo}}'
                    },
                    'datePublished': '{{publish_date}}',
                    'dateModified': '{{modified_date}}'
                },
                organization: {
                    '@context': 'https://schema.org',
                    '@type': 'Organization',
                    'name': '{{organization_name}}',
                    'url': '{{site_url}}',
                    'logo': '{{organization_logo}}',
                    'sameAs': '{{social_profiles}}'
                },
                website: {
                    '@context': 'https://schema.org',
                    '@type': 'WebSite',
                    'name': '{{site_name}}',
                    'url': '{{site_url}}',
                    'potentialAction': {
                        '@type': 'SearchAction',
                        'target': '{{search_url}}',
                        'query-input': 'required name=search_term_string'
                    }
                },
                breadcrumb: {
                    '@context': 'https://schema.org',
                    '@type': 'BreadcrumbList',
                    'itemListElement': '{{breadcrumb_items}}'
                }
            };
            
            return JSON.stringify(templates[schemaType] || {}, null, 2);
        },
        
        /**
         * Display validation results
         */
        displayValidationResults: function(data) {
            var html = '';
            
            if (data.validation) {
                var validation = data.validation;
                var scoreClass = this.getScoreClass(validation.score);
                
                html += '<div class="kata-schema-validation-score ' + scoreClass + '">';
                html += 'Validation Score: ' + validation.score + '/100';
                html += '</div>';
                
                if (validation.errors && validation.errors.length > 0) {
                    html += '<div class="kata-schema-validation-errors">';
                    html += '<h4>Errors:</h4>';
                    html += '<ul class="kata-schema-validation-list">';
                    validation.errors.forEach(function(error) {
                        html += '<li class="error"><span class="dashicons dashicons-dismiss"></span>' + error + '</li>';
                    });
                    html += '</ul></div>';
                }
                
                if (validation.warnings && validation.warnings.length > 0) {
                    html += '<div class="kata-schema-validation-warnings">';
                    html += '<h4>Warnings:</h4>';
                    html += '<ul class="kata-schema-validation-list">';
                    validation.warnings.forEach(function(warning) {
                        html += '<li class="warning"><span class="dashicons dashicons-warning"></span>' + warning + '</li>';
                    });
                    html += '</ul></div>';
                }
                
                if (validation.valid && validation.errors.length === 0) {
                    html += '<div class="kata-schema-notice success">';
                    html += '<span class="dashicons dashicons-yes-alt"></span>';
                    html += '<p>Schema is valid!</p>';
                    html += '</div>';
                }
            }
            
            $('#kata-schema-validation-results').html(html);
            
            // Switch to validation tab
            if (!$('#tab-validation').hasClass('active')) {
                $('a[href="#tab-validation"]').click();
            }
        },
        
        /**
         * Get score class for validation
         */
        getScoreClass: function(score) {
            if (score >= 90) return 'excellent';
            if (score >= 80) return 'good';
            if (score >= 60) return 'warning';
            return 'poor';
        },
        
        /**
         * Show notice message
         */
        showNotice: function(message, type) {
            type = type || 'info';
            
            var iconClass = {
                success: 'dashicons-yes-alt',
                error: 'dashicons-dismiss',
                warning: 'dashicons-warning',
                info: 'dashicons-info'
            };
            
            var noticeHtml = '<div class="kata-schema-notice ' + type + ' kata-schema-fade-in">';
            noticeHtml += '<span class="dashicons ' + iconClass[type] + '"></span>';
            noticeHtml += '<p>' + message + '</p>';
            noticeHtml += '</div>';
            
            // Remove existing notices
            $('.kata-schema-notice').remove();
            
            // Add new notice
            if ($('#kata-schema-json-messages').length) {
                $('#kata-schema-json-messages').html(noticeHtml);
            } else {
                $('.kata-schema-dashboard').prepend(noticeHtml);
            }
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('.kata-schema-notice').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
        },
        
        /**
         * Show spinner
         */
        showSpinner: function(selector) {
            var $element = $(selector);
            $element.data('original-html', $element.html());
            $element.html('<span class="kata-schema-spinner"></span> ' + kataSchemaAdmin.i18n.saving);
            $element.prop('disabled', true);
        },
        
        /**
         * Hide spinner
         */
        hideSpinner: function(selector) {
            var $element = $(selector);
            var originalHtml = $element.data('original-html');
            if (originalHtml) {
                $element.html(originalHtml);
            }
            $element.prop('disabled', false);
        },
        
        /**
         * Copy text to clipboard
         */
        copyToClipboard: function(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text);
            } else {
                // Fallback for older browsers
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
        },
        
        /**
         * Escape HTML
         */
        escapeHtml: function(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        },
        
        /**
         * Validate form
         */
        validateForm: function($form) {
            var isValid = true;
            
            // Check required fields
            $form.find('[required]').each(function() {
                var $field = $(this);
                if (!$field.val().trim()) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            return isValid;
        },
        
        /**
         * Show form loading state
         */
        showFormLoading: function($form) {
            $form.find('.button-primary').each(function() {
                KataSchemaAdmin.showSpinner(this);
            });
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        KataSchemaAdmin.init();
    });
    
    // Make available globally
    window.KataSchemaAdmin = KataSchemaAdmin;
    
})(jQuery);
