/**
 * KATA Schema Builder Dialog
 * Professional modal interface for schema attribute selection
 * Optimized for responsive design and better UX
 * 
 * @package KATA SEO Manager
 * @version 2.0.4
 * @author KATA Team
 * @license MIT
 */

(function(window, tinymce) {
    'use strict';

    // Constants - Responsive sizing
    var DIALOG_WIDTH = Math.min(window.innerWidth * 0.9, 900);
    var DIALOG_HEIGHT = Math.min(window.innerHeight * 0.85, 650);
    
    /**
     * KATA Schema Builder - Main Class
     */
    var KataSchemaBuilder = {
        
        /**
         * Current editor instance
         * @private
         */
        _currentEditor: null,
        
        /**
         * Dialog state cache
         * @private
         */
        _dialogState: {},
        
        /**
         * Current window reference
         * @private
         */
        _currentWindow: null,

        /**
         * Open schema builder dialog
         * @param {Object} editor - TinyMCE editor instance
         * @param {string} schemaType - Type of schema (article, event, etc.)
         * @public
         */
        openDialog: function(editor, schemaType) {
            // Validate inputs
            if (!this._validateDialog(editor, schemaType)) {
                return;
            }

            this._currentEditor = editor;
            var config = KATA_SCHEMA_ATTRIBUTES[schemaType];
            
            // Build and open dialog
            var dialogConfig = this._buildDialogConfig(schemaType, config);
            this._currentWindow = editor.windowManager.open(dialogConfig);
            
            // Add responsive class after dialog opens
            this._makeDialogResponsive();
            
            // Log for debugging
            this._logDialogOpen(schemaType);
        },

        /**
         * Make dialog responsive by adding custom classes
         * @private
         */
        _makeDialogResponsive: function() {
            var self = this;
            setTimeout(function() {
                try {
                    var dialog = document.querySelector('.mce-window');
                    if (dialog) {
                        dialog.classList.add('kata-schema-dialog');
                        
                        // Add responsive wrapper to checkbox sections
                        var checkboxes = dialog.querySelectorAll('.mce-checkbox');
                        self._wrapCheckboxesInGrid(checkboxes);
                        
                        // Add scroll wrapper if content is too tall
                        self._addScrollWrapper(dialog);
                    }
                } catch (e) {
                    console.warn('[KATA] Could not apply responsive styling:', e);
                }
            }, 100);
        },
        
        /**
         * Wrap checkboxes in grid layout for better organization
         * @private
         */
        _wrapCheckboxesInGrid: function(checkboxes) {
            if (!checkboxes || checkboxes.length === 0) return;
            
            var checkboxArray = Array.prototype.slice.call(checkboxes);
            var currentGroup = [];
            
            for (var i = 0; i < checkboxArray.length; i++) {
                var checkbox = checkboxArray[i];
                var prevSibling = checkbox.previousElementSibling;
                
                // Check if previous element is a section separator
                if (prevSibling && prevSibling.classList.contains('mce-label')) {
                    var text = prevSibling.textContent || '';
                    if (text.indexOf('THUỘC TÍNH') > -1 || text.indexOf('HIỂN THỊ') > -1) {
                        // Wrap previous group if exists
                        if (currentGroup.length > 0) {
                            this._createCheckboxGrid(currentGroup);
                        }
                        
                        // Start new group
                        currentGroup = [checkbox];
                    } else {
                        currentGroup.push(checkbox);
                    }
                } else {
                    currentGroup.push(checkbox);
                }
            }
            
            // Wrap final group
            if (currentGroup.length > 0) {
                this._createCheckboxGrid(currentGroup);
            }
        },
        
        /**
         * Create grid layout for checkbox group
         * @private
         */
        _createCheckboxGrid: function(checkboxes) {
            if (!checkboxes || checkboxes.length === 0) return;
            
            var parent = checkboxes[0].parentElement;
            if (!parent) return;
            
            // Create wrapper
            var wrapper = document.createElement('div');
            wrapper.className = 'kata-checkbox-grid';
            
            // Insert wrapper before first checkbox
            parent.insertBefore(wrapper, checkboxes[0]);
            
            // Move all checkboxes into wrapper
            for (var i = 0; i < checkboxes.length; i++) {
                wrapper.appendChild(checkboxes[i]);
            }
        },
        
        /**
         * Add scroll wrapper for long content
         * @private
         */
        _addScrollWrapper: function(dialog) {
            var body = dialog.querySelector('.mce-container-body.mce-abs-layout');
            if (body && body.scrollHeight > DIALOG_HEIGHT - 100) {
                body.style.overflowY = 'auto';
                body.style.maxHeight = (DIALOG_HEIGHT - 100) + 'px';
            }
        },

        /**
         * Validate dialog prerequisites
         * @private
         */
        _validateDialog: function(editor, schemaType) {
            if (!editor) {
                console.error('[KATA] Editor instance is required');
                return false;
            }

            if (!KATA_SCHEMA_ATTRIBUTES || !KATA_SCHEMA_ATTRIBUTES[schemaType]) {
                this._showError('Schema type "' + schemaType + '" không hợp lệ hoặc chưa được cấu hình.');
                return false;
            }

            return true;
        },

        /**
         * Build complete dialog configuration
         * @private
         */
        _buildDialogConfig: function(schemaType, config) {
            var self = this;
            
            return {
                title: this._buildDialogTitle(config),
                width: DIALOG_WIDTH,
                height: DIALOG_HEIGHT,
                body: this._buildDialogBody(schemaType, config),
                buttons: this._buildDialogButtons(),
                onsubmit: function(e) {
                    self._handleSubmit(e, schemaType);
                },
                onclose: function() {
                    self._handleClose();
                }
            };
        },

        /**
         * Build dialog title with icon and schema name
         * @private
         */
        _buildDialogTitle: function(config) {
            return config.icon + ' ' + config.name + ' Schema Builder';
        },

        /**
         * Build dialog body with all fields
         * @private
         */
        _buildDialogBody: function(schemaType, config) {
            var items = [];

            // Header info
            items.push(this._createHeaderLabel(config));

            // Basic information section
            this._addSection(items, '📝 THÔNG TIN CƠ BẢN', this._buildBasicFields(config));

            // Schema properties section
            this._addSection(items, '🔍 THUỘC TÍNH SCHEMA', this._buildSchemaFields(schemaType, config), 
                'Chọn các thuộc tính xuất hiện trong JSON-LD (mặc định: tất cả)');

            // Content display section
            this._addSection(items, '🎨 HIỂN THỊ NỘI DUNG', this._buildContentFields(schemaType, config),
                'Chọn các phần tử hiển thị trên giao diện (mặc định: tất cả)');

            // Quick actions
            items.push(this._createQuickActions());

            // Footer info
            items.push(this._createFooterLabel());

            return items;
        },

        /**
         * Create header label with instructions
         * @private
         */
        _createHeaderLabel: function(config) {
            return {
                type: 'label',
                text: '💡 Điền thông tin và chọn thuộc tính. Các trường (*) là bắt buộc.',
                style: 'background: #fff9e6; border: 1px solid #ffde8a; border-radius: 4px; padding: 8px 10px; margin-bottom: 12px; color: #856404; font-size: 13px; display: block;'
            };
        },

        /**
         * Create quick actions label
         * @private
         */
        _createQuickActions: function() {
            return {
                type: 'label',
                text: '⚡ Mẹo: Bỏ chọn checkbox để ẩn thuộc tính trong schema hoặc giao diện',
                style: 'background: #e7f3f8; padding: 6px 10px; margin: 12px 0 8px 0; border-left: 3px solid #0073aa; font-size: 12px; font-style: italic; color: #004a5d; display: block;'
            };
        },

        /**
         * Create footer label with tips
         * @private
         */
        _createFooterLabel: function() {
            return {
                type: 'label',
                text: '✨ Bỏ chọn checkbox = thêm thuộc tính hide_* vào shortcode',
                style: 'background: #e7f3f8; border: 1px solid #b3d9ea; border-radius: 4px; padding: 6px 10px; margin-top: 12px; color: #004a5d; font-size: 12px; display: block;'
            };
        },

        /**
         * Add section with separator and header
         * @private
         */
        _addSection: function(items, title, fields, description) {
            // Section title
            items.push({
                type: 'label',
                text: title,
                style: 'background: linear-gradient(135deg, #0073aa 0%, #005a87 100%); color: white; padding: 8px 12px; margin: 16px 0 8px 0; border-radius: 4px; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block;'
            });

            // Section description (optional)
            if (description) {
                items.push({
                    type: 'label',
                    text: description,
                    style: 'font-size: 12px; color: #666; font-style: italic; margin-bottom: 8px; display: block; padding-left: 4px;'
                });
            }

            // Add fields
            for (var i = 0; i < fields.length; i++) {
                items.push(fields[i]);
            }
        },

        /**
         * Build basic input fields
         * @private
         */
        _buildBasicFields: function(config) {
            var fields = [];
            var entries = Object.keys(config.baseAttrs);

            for (var i = 0; i < entries.length; i++) {
                var key = entries[i];
                var attr = config.baseAttrs[key];
                
                fields.push({
                    type: 'textbox',
                    name: key,
                    label: attr.label + (attr.required ? ' *' : ''),
                    value: attr.default || '',
                    placeholder: attr.placeholder || attr.label,
                    multiline: attr.multiline || false,
                    minHeight: attr.multiline ? 60 : undefined,
                    style: 'width: 100%;'
                });
            }

            return fields;
        },

        /**
         * Build schema property checkboxes
         * @private
         */
        _buildSchemaFields: function(schemaType, config) {
            return this._buildCheckboxGroup(config.schemaFields, 'schema_', '');
        },

        /**
         * Build content display checkboxes
         * @private
         */
        _buildContentFields: function(schemaType, config) {
            return this._buildCheckboxGroup(config.contentFields, 'content_', '');
        },

        /**
         * Build checkbox group with common pattern
         * @private
         */
        _buildCheckboxGroup: function(fields, prefix, indent) {
            var checkboxes = [];

            for (var i = 0; i < fields.length; i++) {
                var field = fields[i];
                checkboxes.push({
                    type: 'checkbox',
                    name: prefix + field,
                    label: indent + field,
                    checked: true,
                    style: 'margin: 4px 0;'
                });
            }

            return checkboxes;
        },

        /**
         * Build dialog buttons
         * @private
         */
        _buildDialogButtons: function() {
            return [
                {
                    text: '✅ Chèn Shortcode',
                    subtype: 'primary',
                    onclick: 'submit'
                },
                {
                    text: '❌ Hủy',
                    onclick: 'close'
                }
            ];
        },

        /**
         * Handle form submission
         * @private
         */
        _handleSubmit: function(e, schemaType) {
            try {
                var formData = e.data;
                
                // Validate required fields
                if (!this._validateRequiredFields(schemaType, formData)) {
                    return; // Validation will show error
                }
                
                var shortcode = this._buildShortcode(schemaType, formData);
                
                // Insert shortcode
                this._currentEditor.insertContent('\n' + shortcode + '\n');
                
                // Show success notification
                this._showSuccess('✅ Đã chèn ' + schemaType + ' schema thành công!');
                
                // Cache state
                this._dialogState[schemaType] = formData;
                
            } catch (error) {
                this._showError('❌ Có lỗi xảy ra: ' + error.message);
                console.error('[KATA] Submit error:', error);
            }
        },
        
        /**
         * Validate required fields
         * @private
         */
        _validateRequiredFields: function(schemaType, formData) {
            var config = KATA_SCHEMA_ATTRIBUTES[schemaType];
            if (!config) return true;
            
            var missingFields = [];
            var entries = Object.keys(config.baseAttrs);
            
            for (var i = 0; i < entries.length; i++) {
                var key = entries[i];
                var attr = config.baseAttrs[key];
                
                if (attr.required && (!formData[key] || String(formData[key]).trim() === '')) {
                    missingFields.push(attr.label);
                }
            }
            
            if (missingFields.length > 0) {
                this._showError('⚠️ Vui lòng điền: ' + missingFields.join(', '));
                return false;
            }
            
            return true;
        },

        /**
         * Handle dialog close
         * @private
         */
        _handleClose: function() {
            this._currentEditor = null;
            this._currentWindow = null;
        },

        /**
         * Build shortcode from form data
         * @private
         */
        _buildShortcode: function(schemaType, data) {
            var config = KATA_SCHEMA_ATTRIBUTES[schemaType];
            var parts = ['[kata_' + schemaType];

            // Add basic attributes
            this._addBasicAttributes(parts, config, data);

            // Add control attributes
            parts.push('show_schema="true"');
            parts.push('show_frontend="true"');

            // Add schema properties
            this._addSchemaAttributes(parts, config, data);

            // Add content display properties
            this._addContentAttributes(parts, config, data);

            parts.push(']');
            return parts.join(' ');
        },

        /**
         * Add basic attributes to shortcode
         * @private
         */
        _addBasicAttributes: function(parts, config, data) {
            var entries = Object.keys(config.baseAttrs);
            
            for (var i = 0; i < entries.length; i++) {
                var key = entries[i];
                var value = data[key];
                
                if (value && String(value).trim() !== '') {
                    parts.push(key + '="' + this._escapeAttr(value) + '"');
                }
            }
        },

        /**
         * Add schema attributes to shortcode
         * @private
         */
        _addSchemaAttributes: function(parts, config, data) {
            for (var i = 0; i < config.schemaFields.length; i++) {
                var field = config.schemaFields[i];
                var key = 'schema_' + field;
                var attribute = data[key] === true ? 'show_' : 'hide_';
                parts.push(attribute + field + '="true"');
            }
        },

        /**
         * Add content attributes to shortcode
         * @private
         */
        _addContentAttributes: function(parts, config, data) {
            for (var i = 0; i < config.contentFields.length; i++) {
                var field = config.contentFields[i];
                var key = 'content_' + field;
                var attribute = data[key] === true ? 'show_content_' : 'hide_content_';
                parts.push(attribute + field + '="true"');
            }
        },

        /**
         * Escape attribute value for safe HTML output
         * @private
         */
        _escapeAttr: function(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        },

        /**
         * Show error notification
         * @private
         */
        _showError: function(message) {
            if (this._currentEditor && this._currentEditor.notificationManager) {
                this._currentEditor.notificationManager.open({
                    text: message,
                    type: 'error',
                    timeout: 5000
                });
            } else {
                alert('Error: ' + message);
            }
        },

        /**
         * Show success notification
         * @private
         */
        _showSuccess: function(message) {
            if (this._currentEditor && this._currentEditor.notificationManager) {
                this._currentEditor.notificationManager.open({
                    text: message,
                    type: 'success',
                    timeout: 3000
                });
            }
        },

        /**
         * Log dialog open event
         * @private
         */
        _logDialogOpen: function(schemaType) {
            if (window.console && console.log) {
                console.log('[KATA] Schema Builder opened for type:', schemaType);
            }
        },

        /**
         * Get cached dialog state
         * @public
         */
        getDialogState: function(schemaType) {
            return this._dialogState[schemaType] || null;
        },

        /**
         * Clear cached dialog state
         * @public
         */
        clearDialogState: function(schemaType) {
            if (schemaType) {
                delete this._dialogState[schemaType];
            } else {
                this._dialogState = {};
            }
        }
    };

    // Export to global scope
    window.KataSchemaBuilder = KataSchemaBuilder;

})(window, window.tinymce || {});
