/**
 * KATA Schema Builder Dialog
 * Professional fullscreen modal with collapsible sections
 * Default: Hide all attributes, show sample values
 * 
 * @package KATA SEO Manager
 * @version 2.1.0
 * @author KATA Team
 * @license MIT
 */

(function(window, tinymce) {
    'use strict';

    /**
     * KATA Schema Builder - Main Class
     * Manages TinyMCE dialogs for schema markup creation
     * @class KataSchemaBuilder
     */
    class KataSchemaBuilder {
        
        /**
         * Constructor
         */
        constructor() {
            // Constants - Fullscreen sizing
            this.DIALOG_WIDTH = Math.min(window.innerWidth * 0.95, 1200);
            this.DIALOG_HEIGHT = Math.min(window.innerHeight * 0.95, 800);
            
            // Current editor instance
            this._currentEditor = null;
            
            // Dialog state cache
            this._dialogState = {};
            
            // Current window reference
            this._currentWindow = null;
        }

        /**
         * Open schema builder dialog
         * @param {Object} editor - TinyMCE editor instance
         * @param {string} schemaType - Type of schema (article, event, etc.)
         * @public
         * @returns {void}
         */
        openDialog(editor, schemaType) {
            // Validate inputs
            if (!this._validateDialog(editor, schemaType)) {
                return;
            }

            this._currentEditor = editor;
            const config = KATA_SCHEMA_ATTRIBUTES[schemaType];
            
            // Build and open dialog
            const dialogConfig = this._buildDialogConfig(schemaType, config);
            this._currentWindow = editor.windowManager.open(dialogConfig);
            
            // Add responsive class after dialog opens
            this._makeDialogResponsive();
            
            // Log for debugging
            this._logDialogOpen(schemaType);
        }

        /**
         * Make dialog responsive by adding custom classes and interactions
         * @private
         * @returns {void}
         */
        _makeDialogResponsive() {
            setTimeout(() => {
                try {
                    const dialog = document.querySelector('.mce-window');
                    if (dialog) {
                        dialog.classList.add('kata-schema-dialog');
                        dialog.classList.add('kata-fullscreen-mode');
                        
                        // Add responsive wrapper to checkbox sections
                        const checkboxes = dialog.querySelectorAll('.mce-checkbox');
                        this._wrapCheckboxesInGrid(checkboxes);
                        
                        // Add scroll wrapper if content is too tall
                        this._addScrollWrapper(dialog);
                        
                        // Setup collapsible sections
                        this._setupCollapsibleSections(dialog);
                        
                        // Setup quick select buttons
                        this._setupQuickSelectButtons(dialog);
                    }
                } catch (e) {
                    console.warn('[KATA] Could not apply responsive styling:', e);
                }
            }, 100);
        }
        
        /**
         * Setup collapsible sections functionality
         * @private
         * @param {HTMLElement} dialog - Dialog element
         * @returns {void}
         */
        _setupCollapsibleSections(dialog) {
            const headers = dialog.querySelectorAll('.kata-section-header');
            
            headers.forEach((header) => {
                header.addEventListener('click', () => {
                    const parent = header.parentElement;
                    const contents = [];
                    let sibling = header.nextElementSibling;
                    
                    // Collect all content elements until next header
                    while (sibling && !sibling.classList.contains('kata-section-header')) {
                        if (sibling.classList.contains('kata-section-content') || 
                            sibling.classList.contains('mce-formitem')) {
                            contents.push(sibling);
                        }
                        sibling = sibling.nextElementSibling;
                    }
                    
                    // Toggle visibility
                    const isCollapsed = header.classList.contains('kata-collapsed');
                    
                    if (isCollapsed) {
                        header.classList.remove('kata-collapsed');
                        header.textContent = header.textContent.replace('▶', '▼');
                        contents.forEach((el) => {
                            el.style.display = '';
                        });
                    } else {
                        header.classList.add('kata-collapsed');
                        header.textContent = header.textContent.replace('▼', '▶');
                        contents.forEach((el) => {
                            el.style.display = 'none';
                        });
                    }
                });
            });
        }
        
        /**
         * Setup quick select buttons functionality
         * @private
         * @param {HTMLElement} dialog - Dialog element
         * @returns {void}
         */
        _setupQuickSelectButtons(dialog) {
            const quickButtons = dialog.querySelectorAll('.kata-quick-select');
            
            quickButtons.forEach((button) => {
                button.addEventListener('click', (e) => {
                    const clickX = e.offsetX;
                    const buttonWidth = button.offsetWidth;
                    
                    // Determine which action based on click position
                    let action = '';
                    if (clickX < buttonWidth / 3) {
                        action = 'all';
                    } else if (clickX < buttonWidth * 2 / 3) {
                        action = 'none';
                    } else {
                        action = 'toggle';
                    }
                    
                    // Find checkboxes in this section
                    const checkboxes = [];
                    let sibling = button.nextElementSibling;
                    
                    while (sibling && !sibling.classList.contains('kata-section-header')) {
                        const checkbox = sibling.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            checkboxes.push(checkbox);
                        }
                        sibling = sibling.nextElementSibling;
                    }
                    
                    // Apply action
                    checkboxes.forEach((checkbox) => {
                        if (action === 'all') {
                            checkbox.checked = true;
                        } else if (action === 'none') {
                            checkbox.checked = false;
                        } else if (action === 'toggle') {
                            checkbox.checked = !checkbox.checked;
                        }
                    });
                });
            });
        }
        
        /**
         * Wrap checkboxes in grid layout for better organization
         * @private
        
        /**
         * Wrap checkboxes in grid layout for better organization
         * @private
         * @param {NodeList} checkboxes - Checkboxes to wrap
         * @returns {void}
         */
        _wrapCheckboxesInGrid(checkboxes) {
            if (!checkboxes || checkboxes.length === 0) return;
            
            const checkboxArray = Array.from(checkboxes);
            let currentGroup = [];
            
            checkboxArray.forEach((checkbox, i) => {
                const prevSibling = checkbox.previousElementSibling;
                
                // Check if previous element is a section separator
                if (prevSibling && prevSibling.classList.contains('mce-label')) {
                    const text = prevSibling.textContent || '';
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
            });
            
            // Wrap final group
            if (currentGroup.length > 0) {
                this._createCheckboxGrid(currentGroup);
            }
        }
        
        /**
         * Create grid layout for checkbox group
         * @private
         * @param {Array} checkboxes - Array of checkbox elements
         * @returns {void}
         */
        _createCheckboxGrid(checkboxes) {
            if (!checkboxes || checkboxes.length === 0) return;
            
            const parent = checkboxes[0].parentElement;
            if (!parent) return;
            
            // Create wrapper
            const wrapper = document.createElement('div');
            wrapper.className = 'kata-checkbox-grid';
            
            // Insert wrapper before first checkbox
            parent.insertBefore(wrapper, checkboxes[0]);
            
            // Move all checkboxes into wrapper
            checkboxes.forEach((checkbox) => {
                wrapper.appendChild(checkbox);
            });
        }
        
        /**
         * Add scroll wrapper for long content
         * @private
         * @param {HTMLElement} dialog - Dialog element
         * @returns {void}
         */
        _addScrollWrapper(dialog) {
            const body = dialog.querySelector('.mce-container-body.mce-abs-layout');
            if (body && body.scrollHeight > this.DIALOG_HEIGHT - 100) {
                body.style.overflowY = 'auto';
                body.style.maxHeight = `${this.DIALOG_HEIGHT - 100}px`;
            }
        }

        /**
         * Validate dialog prerequisites
         * @private
         * @param {Object} editor - Editor instance
         * @param {string} schemaType - Schema type
         * @returns {boolean} True if valid
         */
        _validateDialog(editor, schemaType) {
            if (!editor) {
                console.error('[KATA] Editor instance is required');
                return false;
            }

            if (!KATA_SCHEMA_ATTRIBUTES || !KATA_SCHEMA_ATTRIBUTES[schemaType]) {
                this._showError(`Schema type "${schemaType}" không hợp lệ hoặc chưa được cấu hình.`);
                return false;
            }

            return true;
        }

        /**
         * Build complete dialog configuration
         * @private
         * @param {string} schemaType - Schema type
         * @param {Object} config - Schema configuration
         * @returns {Object} Dialog configuration
         */
        _buildDialogConfig(schemaType, config) {
            return {
                title: this._buildDialogTitle(config),
                width: this.DIALOG_WIDTH,
                height: this.DIALOG_HEIGHT,
                body: this._buildDialogBody(schemaType, config),
                buttons: this._buildDialogButtons(),
                onsubmit: (e) => {
                    this._handleSubmit(e, schemaType);
                },
                onclose: () => {
                    this._handleClose();
                }
            };
        }

        /**
         * Build dialog title with icon and schema name
         * @private
         * @param {Object} config - Schema configuration
         * @returns {string} Dialog title
         */
        _buildDialogTitle(config) {
            return `${config.icon} ${config.name} Schema Builder`;
        }

        /**
         * Build dialog body with all fields
         * @private
         * @param {string} schemaType - Schema type
         * @param {Object} config - Schema configuration
         * @returns {Array} Array of form items
         */
        _buildDialogBody(schemaType, config) {
            const items = [];

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
        }

        /**
         * Create header label with instructions
         * @private
         * @param {Object} config - Schema configuration
         * @returns {Object} Label configuration
         */
        _createHeaderLabel(config) {
            return {
                type: 'label',
                text: '💡 Mặc định: TẤT CẢ thuộc tính bị ẨN. Chọn checkbox để HIỂN THỊ. Click tiêu đề section để thu gọn/mở rộng.',
                style: 'background: linear-gradient(135deg, #fff9e6 0%, #fff5d6 100%); border: 1px solid #ffde8a; border-left: 4px solid #f0b429; border-radius: 6px; padding: 12px 14px; margin-bottom: 16px; color: #856404; font-size: 13px; display: block; font-weight: 600; line-height: 1.6;'
            };
        }

        /**
         * Create quick actions label
         * @private
         * @returns {Object} Label configuration
         */
        _createQuickActions() {
            return {
                type: 'label',
                text: '⚡ Mẹo: Bỏ chọn checkbox để ẩn thuộc tính trong schema hoặc giao diện',
                style: 'background: #e7f3f8; padding: 6px 10px; margin: 12px 0 8px 0; border-left: 3px solid #0073aa; font-size: 12px; font-style: italic; color: #004a5d; display: block;'
            };
        }

        /**
         * Create footer label with tips
         * @private
         * @returns {Object} Label configuration
         */
        _createFooterLabel() {
            return {
                type: 'label',
                text: '✨ Mặc định: Tất cả ẨN (hide_* = true). Chọn checkbox = HIỆN (show_* = true). Giá trị mẫu được điền sẵn.',
                style: 'background: linear-gradient(135deg, #e7f3f8 0%, #d6ebf5 100%); border: 1px solid #b3d9ea; border-radius: 6px; padding: 10px 14px; margin-top: 16px; color: #004a5d; font-size: 12px; display: block; font-weight: 500;'
            };
        }

        /**
         * Add section with separator, header and collapsible functionality
         * @private
         * @param {Array} items - Items array
         * @param {string} title - Section title
         * @param {Array} fields - Section fields
         * @param {string} description - Optional description
         * @returns {void}
         */
        _addSection(items, title, fields, description) {
            // Section title with collapse indicator
            items.push({
                type: 'label',
                text: `${title} ▼`,
                style: 'background: linear-gradient(135deg, #0073aa 0%, #005a87 100%); color: white; padding: 10px 14px; margin: 16px 0 8px 0; border-radius: 6px; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: block; cursor: pointer; user-select: none;',
                classes: 'kata-section-header kata-collapsible'
            });

            // Section description (optional)
            if (description) {
                items.push({
                    type: 'label',
                    text: description,
                    style: 'font-size: 12px; color: #666; font-style: italic; margin-bottom: 10px; display: block; padding-left: 4px;',
                    classes: 'kata-section-content'
                });
            }

            // Quick select buttons
            items.push({
                type: 'label',
                text: '⚡ Nhanh: [Chọn tất cả] [Bỏ chọn tất cả] [Đảo ngược]',
                style: 'background: #f0f7fc; padding: 8px 12px; margin-bottom: 8px; border-radius: 4px; font-size: 12px; color: #0073aa; display: block; cursor: pointer;',
                classes: 'kata-quick-select kata-section-content'
            });

            // Add fields with section-content class
            fields.forEach((field) => {
                if (!field.classes) field.classes = '';
                field.classes += ' kata-section-content';
                items.push(field);
            });
        }

        /**
         * Build basic input fields
         * @private
         * @param {Object} config - Schema configuration
         * @returns {Array} Array of field configurations
         */
        _buildBasicFields(config) {
            const fields = [];
            const entries = Object.keys(config.baseAttrs);

            entries.forEach((key) => {
                const attr = config.baseAttrs[key];
                
                fields.push({
                    type: 'textbox',
                    name: key,
                    label: `${attr.label}${attr.required ? ' *' : ''}`,
                    value: attr.default || '',
                    placeholder: attr.placeholder || attr.label,
                    multiline: attr.multiline || false,
                    minHeight: attr.multiline ? 60 : undefined,
                    style: 'width: 100%;'
                });
            });

            return fields;
        }

        /**
         * Build schema property checkboxes
         * @private
         * @param {string} schemaType - Schema type
         * @param {Object} config - Schema configuration
         * @returns {Array} Array of checkbox configurations
         */
        _buildSchemaFields(schemaType, config) {
            return this._buildCheckboxGroup(config.schemaFields, 'schema_', '');
        }

        /**
         * Build content display checkboxes
         * @private
         * @param {string} schemaType - Schema type
         * @param {Object} config - Schema configuration
         * @returns {Array} Array of checkbox configurations
         */
        _buildContentFields(schemaType, config) {
            return this._buildCheckboxGroup(config.contentFields, 'content_', '');
        }

        /**
         * Build checkbox group with common pattern
         * DEFAULT: All checkboxes UNCHECKED (hide by default)
         * @private
         * @param {Array} fields - Field names
         * @param {string} prefix - Field name prefix
         * @param {string} indent - Label indentation
         * @returns {Array} Array of checkbox configurations
         */
        _buildCheckboxGroup(fields, prefix, indent) {
            const checkboxes = [];

            fields.forEach((field) => {
                checkboxes.push({
                    type: 'checkbox',
                    name: prefix + field,
                    label: indent + field,
                    checked: false, // DEFAULT: UNCHECKED = HIDE
                    style: 'margin: 4px 0;'
                });
            });

            return checkboxes;
        }
        
        /**
         * Build dialog buttons
         * @private
         * @returns {Array} Array of button configurations
         */
        _buildDialogButtons() {
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
        }

        /**
         * Handle form submission
         * @private
         * @param {Object} e - Submit event
         * @param {string} schemaType - Schema type
         * @returns {void}
         */
        _handleSubmit(e, schemaType) {
            try {
                const formData = e.data;
                
                // Validate required fields
                if (!this._validateRequiredFields(schemaType, formData)) {
                    return; // Validation will show error
                }
                
                const shortcode = this._buildShortcode(schemaType, formData);
                
                // Insert shortcode
                this._currentEditor.insertContent(`\n${shortcode}\n`);
                
                // Show success notification
                this._showSuccess(`✅ Đã chèn ${schemaType} schema thành công!`);
                
                // Cache state
                this._dialogState[schemaType] = formData;
                
            } catch (error) {
                this._showError(`❌ Có lỗi xảy ra: ${error.message}`);
                console.error('[KATA] Submit error:', error);
            }
        }
        
        /**
         * Validate required fields
         * @private
         * @param {string} schemaType - Schema type
         * @param {Object} formData - Form data
         * @returns {boolean} True if valid
         */
        _validateRequiredFields(schemaType, formData) {
            const config = KATA_SCHEMA_ATTRIBUTES[schemaType];
            if (!config) return true;
            
            const missingFields = [];
            const entries = Object.keys(config.baseAttrs);
            
            entries.forEach((key) => {
                const attr = config.baseAttrs[key];
                
                if (attr.required && (!formData[key] || String(formData[key]).trim() === '')) {
                    missingFields.push(attr.label);
                }
            });
            
            if (missingFields.length > 0) {
                this._showError(`⚠️ Vui lòng điền: ${missingFields.join(', ')}`);
                return false;
            }
            
            return true;
        }

        /**
         * Handle dialog close
         * @private
         * @returns {void}
         */
        _handleClose() {
            this._currentEditor = null;
            this._currentWindow = null;
        }

        /**
         * Build shortcode from form data
         * @private
         * @param {string} schemaType - Schema type
         * @param {Object} data - Form data
         * @returns {string} Shortcode string
         */
        _buildShortcode(schemaType, data) {
            const config = KATA_SCHEMA_ATTRIBUTES[schemaType];
            const parts = [`[kata_${schemaType}`];

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
        }

        /**
         * Add basic attributes to shortcode
         * @private
         * @param {Array} parts - Shortcode parts array
         * @param {Object} config - Schema configuration
         * @param {Object} data - Form data
         * @returns {void}
         */
        _addBasicAttributes(parts, config, data) {
            const entries = Object.keys(config.baseAttrs);
            
            entries.forEach((key) => {
                const value = data[key];
                
                if (value && String(value).trim() !== '') {
                    parts.push(`${key}="${this._escapeAttr(value)}"`);
                }
            });
        }

        /**
         * Add schema attributes to shortcode
         * @private
         * @param {Array} parts - Shortcode parts array
         * @param {Object} config - Schema configuration
         * @param {Object} data - Form data
         * @returns {void}
         */
        _addSchemaAttributes(parts, config, data) {
            config.schemaFields.forEach((field) => {
                const key = `schema_${field}`;
                const attribute = data[key] === true ? 'show_' : 'hide_';
                parts.push(`${attribute}${field}="true"`);
            });
        }

        /**
         * Add content attributes to shortcode
         * @private
         * @param {Array} parts - Shortcode parts array
         * @param {Object} config - Schema configuration
         * @param {Object} data - Form data
         * @returns {void}
         */
        _addContentAttributes(parts, config, data) {
            config.contentFields.forEach((field) => {
                const key = `content_${field}`;
                const attribute = data[key] === true ? 'show_content_' : 'hide_content_';
                parts.push(`${attribute}${field}="true"`);
            });
        }

        /**
         * Escape attribute value for safe HTML output
         * @private
         * @param {string} value - Value to escape
         * @returns {string} Escaped value
         */
        _escapeAttr(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        /**
         * Show error notification
         * @private
         * @param {string} message - Error message
         * @returns {void}
         */
        _showError(message) {
            if (this._currentEditor && this._currentEditor.notificationManager) {
                this._currentEditor.notificationManager.open({
                    text: message,
                    type: 'error',
                    timeout: 5000
                });
            } else {
                alert(`Error: ${message}`);
            }
        }

        /**
         * Show success notification
         * @private
         * @param {string} message - Success message
         * @returns {void}
         */
        _showSuccess(message) {
            if (this._currentEditor && this._currentEditor.notificationManager) {
                this._currentEditor.notificationManager.open({
                    text: message,
                    type: 'success',
                    timeout: 3000
                });
            }
        }

        /**
         * Log dialog open event
         * @private
         * @param {string} schemaType - Schema type
         * @returns {void}
         */
        _logDialogOpen(schemaType) {
            if (window.console && console.log) {
                console.log('[KATA] Schema Builder opened for type:', schemaType);
            }
        }

        /**
         * Get cached dialog state
         * @public
         * @param {string} schemaType - Schema type
         * @returns {Object|null} Cached state or null
         */
        getDialogState(schemaType) {
            return this._dialogState[schemaType] || null;
        }

        /**
         * Clear cached dialog state
         * @public
         * @param {string} schemaType - Schema type (optional, clears all if omitted)
         * @returns {void}
         */
        clearDialogState(schemaType) {
            if (schemaType) {
                delete this._dialogState[schemaType];
            } else {
                this._dialogState = {};
            }
        }
    }

    // Export to global scope
    window.KataSchemaBuilder = new KataSchemaBuilder();

})(window, window.tinymce || {});
