/**
 * KATA SEO Manager - Admin Interface Controller
 * 
 * Modern ES6+ refactored version with:
 * - ES6 Classes and arrow functions
 * - Async/await for AJAX operations
 * - Template literals for HTML generation
 * - Event delegation pattern
 * - Error handling and validation
 * - JSDoc documentation
 * - Debouncing for performance
 * 
 * @package KATA_SEO_Manager
 * @version 2.2.0
 * @since 2.1.4
 */

(function($) {
    'use strict';

    /**
     * Main Admin Controller Class
     * Manages schema modal, meta box, and admin interface
     * 
     * @class KataSEOAdmin
     */
    class KataSEOAdmin {
        /**
         * Initialize the admin controller
         * 
         * @constructor
         */
        constructor() {
            /** @type {string|null} Current selected schema type */
            this.currentType = null;
            
            /** @type {number|null} Current editing schema index */
            this.editingIndex = null;
            
            /** @type {Object} WordPress media frame instance */
            this.mediaFrame = null;
            
            /** @type {number} Debounce timeout ID */
            this.previewDebounceTimer = null;
            
            /** @type {number} Debounce delay in milliseconds */
            this.DEBOUNCE_DELAY = 500;
            
            // Initialize all modules
            this.init();
        }

        /**
         * Initialize all admin modules
         * 
         * @returns {void}
         */
        init() {
            this.initModal();
            this.initMetaBox();
            this.initSchemaBuilder();
            this.initQuickActions();
            this.bindEvents();
            
            console.log('✅ KATA SEO Admin initialized (ES6 version)');
        }

        /**
         * Initialize schema modal handlers
         * 
         * @returns {void}
         */
        initModal() {
            // Open modal - Multiple trigger buttons
            $(document).on('click', '.kata-insert-schema-btn, .kata-add-schema, .kata-add-schema-btn, #kata-seo-insert-schema', (e) => {
                e.preventDefault();
                this.openModal();
                
                const type = $(e.currentTarget).data('type');
                if (type) {
                    this.selectSchemaType(type);
                }
            });

            // Close modal handlers
            $(document).on('click', '.kata-modal-close, .kata-cancel-btn, .kata-modal-overlay', (e) => {
                e.preventDefault();
                this.closeModal();
            });

            // Schema type selection
            $(document).on('click', '.kata-schema-type-btn', (e) => {
                e.preventDefault();
                const type = $(e.currentTarget).data('type');
                this.selectSchemaType(type);
            });

            // Back button
            $(document).on('click', '.kata-back-btn', (e) => {
                e.preventDefault();
                this.showStep(1);
            });

            // Tab switching
            $(document).on('click', '.kata-tab-btn', (e) => {
                e.preventDefault();
                const tab = $(e.currentTarget).data('tab');
                this.switchTab(tab);
            });

            // Insert schema
            $(document).on('click', '.kata-insert-btn', (e) => {
                e.preventDefault();
                this.insertSchema();
            });

            // Validate schema
            $(document).on('click', '.kata-validate-btn', (e) => {
                e.preventDefault();
                this.validateSchema();
            });

            // ESC key to close modal
            $(document).on('keyup', (e) => {
                if (e.key === 'Escape' && $('#kata-schema-modal').is(':visible')) {
                    this.closeModal();
                }
            });
        }

        /**
         * Initialize meta box handlers
         * 
         * @returns {void}
         */
        initMetaBox() {
            // Edit schema
            $(document).on('click', '.kata-edit-schema', (e) => {
                e.preventDefault();
                const index = $(e.currentTarget).data('index');
                this.editSchema(index);
            });

            // Toggle schema active/inactive
            $(document).on('click', '.kata-toggle-schema', (e) => {
                e.preventDefault();
                const index = $(e.currentTarget).data('index');
                this.toggleSchema(index);
            });

            // Delete schema
            $(document).on('click', '.kata-delete-schema', (e) => {
                e.preventDefault();
                const index = $(e.currentTarget).data('index');
                this.confirmAndDeleteSchema(index);
            });

            // Validate all schemas
            $(document).on('click', '.kata-validate-all-schemas', (e) => {
                e.preventDefault();
                this.validateAllSchemas();
            });
        }

        /**
         * Initialize schema builder form handlers
         * 
         * @returns {void}
         */
        initSchemaBuilder() {
            // Form input change with debounce
            $(document).on('change keyup', '#kata-schema-form input, #kata-schema-form textarea, #kata-schema-form select', () => {
                this.debouncedUpdatePreview();
                $('.kata-insert-btn').prop('disabled', false);
            });

            // Template selection
            $(document).on('change', '#kata-template-selector', (e) => {
                const templateId = $(e.currentTarget).val();
                if (templateId) {
                    this.loadTemplate(templateId);
                }
            });
        }

        /**
         * Initialize Quick Actions handlers
         * 
         * @returns {void}
         */
        initQuickActions() {
            // Generate Demo Content
            $('#kata-generate-demo-btn').on('click', (e) => {
                e.preventDefault();
                this.generateDemoContent($(e.currentTarget));
            });

            // Delete Demo Content
            $('#kata-delete-demo-btn').on('click', (e) => {
                e.preventDefault();
                this.deleteDemoContent($(e.currentTarget));
            });
        }

        /**
         * Bind additional events (image upload, etc.)
         * 
         * @returns {void}
         */
        bindEvents() {
            // Image upload via WordPress Media Library
            $(document).on('click', '.kata-upload-image', (e) => {
                e.preventDefault();
                const button = $(e.currentTarget);
                const input = button.prev('input');
                
                this.openMediaLibrary(input, button);
            });
        }

        /**
         * Open WordPress Media Library
         * 
         * @param {jQuery} input - Input field to receive URL
         * @param {jQuery} button - Upload button
         * @returns {void}
         */
        openMediaLibrary(input, button) {
            // Create media frame if not exists
            if (this.mediaFrame) {
                this.mediaFrame.open();
                return;
            }

            this.mediaFrame = wp.media({
                title: kataAdmin.selectImage || 'Select Image',
                button: { text: kataAdmin.useImage || 'Use Image' },
                multiple: false
            });

            this.mediaFrame.on('select', () => {
                const attachment = this.mediaFrame.state().get('selection').first().toJSON();
                input.val(attachment.url).trigger('change');
                
                this.updateImagePreview(button, attachment.url);
            });

            this.mediaFrame.open();
        }

        /**
         * Update image preview after upload
         * 
         * @param {jQuery} button - Upload button
         * @param {string} url - Image URL
         * @returns {void}
         */
        updateImagePreview(button, url) {
            let preview = button.next('.kata-image-preview');
            
            if (preview.length) {
                preview.find('img').attr('src', url);
            } else {
                button.after(`
                    <div class="kata-image-preview">
                        <img src="${url}" style="max-width:200px;height:auto;" alt="Preview">
                    </div>
                `);
            }
        }

        /**
         * Select schema type and load form
         * 
         * @param {string} type - Schema type (article, faq, etc.)
         * @returns {Promise<void>}
         */
        async selectSchemaType(type) {
            try {
                this.currentType = type;
                this.showStep(2);
                this.showLoading('#kata-schema-form');

                const response = await this.ajaxRequest('kata_get_schema_fields', {
                    type: type
                });

                if (response.success) {
                    this.renderForm(response.data.fields, response.data.example);
                    await this.loadTemplates(type);
                } else {
                    this.showError(response.data?.message || kataAdmin.error);
                }
            } catch (error) {
                console.error('Error selecting schema type:', error);
                this.showError('Failed to load schema fields');
            } finally {
                this.hideLoading('#kata-schema-form');
            }
        }

        /**
         * Render schema form from field definitions
         * 
         * @param {Object} fields - Field definitions
         * @param {Object} example - Example data
         * @returns {void}
         */
        renderForm(fields, example = {}) {
            const formHtml = Object.entries(fields).map(([name, field]) => {
                return this.renderField(name, field, example[name]);
            }).join('');

            $('#kata-schema-form').html(formHtml);
        }

        /**
         * Render a single form field
         * 
         * @param {string} name - Field name
         * @param {Object} field - Field configuration
         * @param {*} value - Field value
         * @returns {string} HTML string
         */
        renderField(name, field, value = '') {
            const requiredMark = field.required ? ' <span class="required">*</span>' : '';
            const placeholder = field.placeholder || '';
            const description = field.description ? `<p class="kata-field-description">${field.description}</p>` : '';

            let inputHtml = '';

            switch (field.type) {
                case 'textarea':
                    inputHtml = `<textarea name="${name}" class="kata-field-input" rows="5" placeholder="${placeholder}">${value}</textarea>`;
                    break;

                case 'select':
                    const options = Object.entries(field.options || {})
                        .map(([val, label]) => `<option value="${val}">${label}</option>`)
                        .join('');
                    inputHtml = `<select name="${name}" class="kata-field-input">${options}</select>`;
                    break;

                case 'image':
                    inputHtml = `
                        <input type="url" name="${name}" class="kata-field-input kata-image-url" value="${value}" placeholder="${placeholder}">
                        <button type="button" class="button kata-upload-image">${kataAdmin.uploadImage || 'Upload'}</button>
                    `;
                    break;

                case 'repeater':
                    inputHtml = `
                        <div class="kata-repeater-items" data-field="${name}"></div>
                        <button type="button" class="button kata-repeater-add">${kataAdmin.addItem || 'Add Item'}</button>
                    `;
                    break;

                default:
                    inputHtml = `<input type="${field.type}" name="${name}" class="kata-field-input" value="${value}" placeholder="${placeholder}">`;
            }

            return `
                <div class="kata-field" data-field="${name}">
                    <label class="kata-field-label">
                        ${field.label}${requiredMark}
                    </label>
                    ${inputHtml}
                    ${description}
                </div>
            `;
        }

        /**
         * Load templates for schema type
         * 
         * @param {string} type - Schema type
         * @returns {Promise<void>}
         */
        async loadTemplates(type) {
            try {
                const response = await this.ajaxRequest('kata_get_templates', { type });

                if (response.success) {
                    const options = [
                        `<option value="">${kataAdmin.selectTemplate || 'Select Template'}</option>`,
                        ...response.data.map(template => 
                            `<option value="${template.id}">${template.name}</option>`
                        )
                    ].join('');

                    $('#kata-template-selector').html(options);
                }
            } catch (error) {
                console.error('Error loading templates:', error);
            }
        }

        /**
         * Load template data into form
         * 
         * @param {string} templateId - Template ID
         * @returns {Promise<void>}
         */
        async loadTemplate(templateId) {
            try {
                this.showLoading('#kata-schema-form');

                const response = await this.ajaxRequest('kata_load_template', {
                    template_id: templateId
                });

                if (response.success) {
                    this.populateForm(response.data);
                    this.updatePreview();
                }
            } catch (error) {
                console.error('Error loading template:', error);
            } finally {
                this.hideLoading('#kata-schema-form');
            }
        }

        /**
         * Populate form with data
         * 
         * @param {Object} data - Form data
         * @returns {void}
         */
        populateForm(data) {
            Object.entries(data).forEach(([name, value]) => {
                const field = $(`#kata-schema-form [name="${name}"]`);
                if (field.length) {
                    field.val(value).trigger('change');
                }
            });
        }

        /**
         * Update schema preview with debounce
         * 
         * @returns {void}
         */
        debouncedUpdatePreview() {
            clearTimeout(this.previewDebounceTimer);
            this.previewDebounceTimer = setTimeout(() => {
                this.updatePreview();
            }, this.DEBOUNCE_DELAY);
        }

        /**
         * Update schema preview
         * 
         * @returns {Promise<void>}
         */
        async updatePreview() {
            try {
                const data = this.getFormData();

                const response = await this.ajaxRequest('kata_preview_schema', {
                    type: this.currentType,
                    data: data
                });

                if (response.success) {
                    $('#kata-schema-preview code').text(response.data.json);
                }
            } catch (error) {
                console.error('Error updating preview:', error);
            }
        }

        /**
         * Insert schema into post
         * 
         * @returns {Promise<void>}
         */
        async insertSchema() {
            try {
                const data = this.getFormData();
                const $btn = $('.kata-insert-btn');
                const originalText = $btn.text();

                // Disable button and show loading
                $btn.prop('disabled', true).text(kataAdmin.inserting || 'Inserting...');

                const response = await this.ajaxRequest('kata_insert_schema', {
                    post_id: $('#post_ID').val(),
                    type: this.currentType,
                    data: data,
                    index: this.editingIndex
                });

                if (response.success) {
                    this.showSuccess(response.data.message);
                    this.closeModal();
                    
                    // Reload page to show updated schema list
                    setTimeout(() => location.reload(), 1000);
                } else {
                    this.showError(response.data?.message || kataAdmin.error);
                    $btn.prop('disabled', false).text(originalText);
                }
            } catch (error) {
                console.error('Error inserting schema:', error);
                this.showError('Failed to insert schema');
                $('.kata-insert-btn').prop('disabled', false);
            }
        }

        /**
         * Validate current schema
         * 
         * @returns {Promise<void>}
         */
        async validateSchema() {
            try {
                const data = this.getFormData();

                const response = await this.ajaxRequest('kata_validate_schema', {
                    type: this.currentType,
                    data: data
                });

                if (response.success) {
                    if (response.data.valid) {
                        this.showSuccess('✅ Schema hợp lệ!');
                    } else {
                        this.showError('❌ Schema không hợp lệ:\n' + response.data.errors.join('\n'));
                    }
                }
            } catch (error) {
                console.error('Error validating schema:', error);
                this.showError('Failed to validate schema');
            }
        }

        /**
         * Validate all schemas in post
         * 
         * @returns {Promise<void>}
         */
        async validateAllSchemas() {
            try {
                this.showLoading('.kata-schemas-list');

                const response = await this.ajaxRequest('kata_validate_all_schemas', {
                    post_id: $('#post_ID').val()
                });

                if (response.success) {
                    const results = response.data;
                    const message = `
                        Validation Results:
                        Valid: ${results.valid}
                        Invalid: ${results.invalid}
                        Total: ${results.total}
                    `;
                    alert(message);
                }
            } catch (error) {
                console.error('Error validating all schemas:', error);
            } finally {
                this.hideLoading('.kata-schemas-list');
            }
        }

        /**
         * Edit existing schema
         * 
         * @param {number} index - Schema index
         * @returns {Promise<void>}
         */
        async editSchema(index) {
            try {
                this.editingIndex = index;

                const response = await this.ajaxRequest('kata_get_schema', {
                    post_id: $('#post_ID').val(),
                    index: index
                });

                if (response.success) {
                    const schema = response.data;
                    await this.selectSchemaType(schema.type);
                    this.populateForm(schema.data);
                    this.openModal();
                }
            } catch (error) {
                console.error('Error editing schema:', error);
                this.showError('Failed to load schema');
            }
        }

        /**
         * Toggle schema active/inactive
         * 
         * @param {number} index - Schema index
         * @returns {Promise<void>}
         */
        async toggleSchema(index) {
            try {
                const response = await this.ajaxRequest('kata_toggle_schema', {
                    post_id: $('#post_ID').val(),
                    index: index
                });

                if (response.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error toggling schema:', error);
            }
        }

        /**
         * Confirm and delete schema
         * 
         * @param {number} index - Schema index
         * @returns {void}
         */
        confirmAndDeleteSchema(index) {
            if (confirm(kataAdmin.confirmDelete || 'Are you sure?')) {
                this.deleteSchema(index);
            }
        }

        /**
         * Delete schema
         * 
         * @param {number} index - Schema index
         * @returns {Promise<void>}
         */
        async deleteSchema(index) {
            try {
                const response = await this.ajaxRequest('kata_delete_schema', {
                    post_id: $('#post_ID').val(),
                    index: index
                });

                if (response.success) {
                    this.showSuccess('Schema deleted');
                    location.reload();
                }
            } catch (error) {
                console.error('Error deleting schema:', error);
                this.showError('Failed to delete schema');
            }
        }

        /**
         * Generate demo content
         * 
         * @param {jQuery} $btn - Button element
         * @returns {Promise<void>}
         */
        async generateDemoContent($btn) {
            const confirmMessage = `Bạn có chắc muốn tạo dữ liệu mẫu?\n\nĐiều này sẽ tạo:\n- 26 schema types mẫu\n- 5 polls mẫu\n- 3 wheels mẫu\n- User interactions mẫu`;
            
            if (!confirm(confirmMessage)) {
                return;
            }

            const originalHtml = $btn.html();
            
            try {
                // Show loading state
                $btn.prop('disabled', true).html(`
                    <div class="kata-quick-action-icon"><div class="kata-spinner"></div></div>
                    <div class="kata-quick-action-content">
                        <div class="kata-quick-action-title">Đang tạo...</div>
                        <div class="kata-quick-action-desc">Vui lòng đợi</div>
                    </div>
                `);

                const response = await this.ajaxRequest('kata_generate_demo_content', {});

                if (response.success) {
                    const data = response.data;
                    const message = `✅ Tạo dữ liệu mẫu thành công!\n\n` +
                        `Schemas: ${data.schemas_created || 0}\n` +
                        `Polls: ${data.polls_created || 0}\n` +
                        `Wheels: ${data.wheels_created || 0}\n` +
                        `User Interactions: ${data.interactions_created || 0}`;
                    
                    alert(message);
                    location.reload();
                } else {
                    throw new Error(response.data?.message || 'Failed to generate demo content');
                }
            } catch (error) {
                console.error('Error generating demo content:', error);
                alert('❌ Lỗi: ' + error.message);
                $btn.prop('disabled', false).html(originalHtml);
            }
        }

        /**
         * Delete demo content
         * 
         * @param {jQuery} $btn - Button element
         * @returns {Promise<void>}
         */
        async deleteDemoContent($btn) {
            const confirmMessage1 = `⚠️ CẢNH BÁO: Bạn có chắc muốn XÓA tất cả dữ liệu mẫu?\n\nĐiều này sẽ xóa:\n- Tất cả schemas demo\n- Tất cả polls demo\n- Tất cả wheels demo\n- Tất cả user interactions\n\nHành động này KHÔNG THỂ HOÀN TÁC!`;
            const confirmMessage2 = `Xác nhận lần 2: Bạn THỰC SỰ muốn xóa tất cả dữ liệu mẫu?`;
            
            if (!confirm(confirmMessage1) || !confirm(confirmMessage2)) {
                return;
            }

            const originalHtml = $btn.html();
            
            try {
                // Show loading state
                $btn.prop('disabled', true).html(`
                    <div class="kata-quick-action-icon"><div class="kata-spinner"></div></div>
                    <div class="kata-quick-action-content">
                        <div class="kata-quick-action-title">Đang xóa...</div>
                        <div class="kata-quick-action-desc">Vui lòng đợi</div>
                    </div>
                `);

                const response = await this.ajaxRequest('kata_delete_demo_content', {});

                if (response.success) {
                    const deleted = response.data.deleted || {};
                    const message = `✅ Xóa dữ liệu mẫu thành công!\n\n` +
                        `Schemas: ${deleted.schemas || 0}\n` +
                        `Polls: ${deleted.polls || 0}\n` +
                        `Poll Votes: ${deleted.poll_votes || 0}\n` +
                        `Wheels: ${deleted.wheels || 0}\n` +
                        `Wheel Prizes: ${deleted.wheel_prizes || 0}\n` +
                        `Wheel Spins: ${deleted.wheel_spins || 0}\n` +
                        `User Interactions: ${deleted.user_interactions || 0}\n\n` +
                        `Tổng cộng: ${response.data.total || 0} records`;
                    
                    alert(message);
                    location.reload();
                } else {
                    throw new Error(response.data?.message || 'Failed to delete demo content');
                }
            } catch (error) {
                console.error('Error deleting demo content:', error);
                alert('❌ Lỗi: ' + error.message);
                $btn.prop('disabled', false).html(originalHtml);
            }
        }

        /**
         * Get form data as object
         * 
         * @returns {Object} Form data
         */
        getFormData() {
            const data = {};
            
            $('#kata-schema-form input, #kata-schema-form textarea, #kata-schema-form select').each(function() {
                const name = $(this).attr('name');
                const value = $(this).val();
                
                if (name) {
                    data[name] = value;
                }
            });

            return data;
        }

        /**
         * Show modal step
         * 
         * @param {number} step - Step number (1 or 2)
         * @returns {void}
         */
        showStep(step) {
            $('.kata-modal-step').hide();
            $(`.kata-modal-step[data-step="${step}"]`).show();

            // Show/hide validate button based on step
            $('.kata-validate-btn').toggle(step === 2);
        }

        /**
         * Switch tab in modal
         * 
         * @param {string} tab - Tab name
         * @returns {void}
         */
        switchTab(tab) {
            // Update tab buttons
            $('.kata-tab-btn').removeClass('active');
            $(`.kata-tab-btn[data-tab="${tab}"]`).addClass('active');

            // Update tab content
            $('.kata-tab-content').hide();
            $(`.kata-tab-content[data-tab="${tab}"]`).show();

            // Update preview if switching to preview tab
            if (tab === 'preview') {
                this.updatePreview();
            }
        }

        /**
         * Open modal
         * 
         * @returns {void}
         */
        openModal() {
            $('#kata-schema-modal').fadeIn(300);
            $('body').addClass('kata-modal-open');
        }

        /**
         * Close modal
         * 
         * @returns {void}
         */
        closeModal() {
            $('#kata-schema-modal').fadeOut(300);
            $('body').removeClass('kata-modal-open');
            
            // Reset state
            this.showStep(1);
            this.currentType = null;
            this.editingIndex = null;
            $('#kata-schema-form').html('');
        }

        /**
         * Make AJAX request with Promise
         * 
         * @param {string} action - WordPress AJAX action
         * @param {Object} data - Request data
         * @returns {Promise<Object>} Response data
         */
        async ajaxRequest(action, data = {}) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: kataAdmin?.ajaxUrl || kata_ajax?.url || ajaxurl,
                    type: 'POST',
                    data: {
                        action: action,
                        nonce: kataAdmin?.nonce || kata_ajax?.nonce,
                        ...data
                    },
                    success: (response) => {
                        resolve(response);
                    },
                    error: (xhr, status, error) => {
                        console.error('AJAX Error:', { xhr, status, error });
                        reject(new Error(error || 'AJAX request failed'));
                    }
                });
            });
        }

        /**
         * Show loading indicator
         * 
         * @param {string} selector - jQuery selector
         * @returns {void}
         */
        showLoading(selector) {
            $(selector).addClass('kata-loading');
        }

        /**
         * Hide loading indicator
         * 
         * @param {string} selector - jQuery selector
         * @returns {void}
         */
        hideLoading(selector) {
            $(selector).removeClass('kata-loading');
        }

        /**
         * Show success message
         * 
         * @param {string} message - Success message
         * @returns {void}
         */
        showSuccess(message) {
            alert('✅ ' + message);
        }

        /**
         * Show error message
         * 
         * @param {string} message - Error message
         * @returns {void}
         */
        showError(message) {
            alert('❌ ' + message);
        }
    }

    // Initialize on document ready
    $(document).ready(() => {
        // Create global instance
        window.kataSEOAdmin = new KataSEOAdmin();
        
        // Backward compatibility: expose as KataSEO (old name)
        window.KataSEO = window.kataSEOAdmin;
        
        console.log('✅ KATA SEO Admin ready!');
    });

})(jQuery);
