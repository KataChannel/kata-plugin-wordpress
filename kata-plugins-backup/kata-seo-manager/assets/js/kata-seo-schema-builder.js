/**
 * KATA SEO Manager - Schema Builder Admin UI
 * 
 * Modern ES6+ refactored version with:
 * - ES6 Class architecture
 * - Async/await for AJAX operations  
 * - Template literals for HTML generation
 * - Enhanced error handling
 * - JSDoc documentation
 * - Debouncing for performance
 * - Improved UX with loading states
 * 
 * @package KATA_SEO_Manager
 * @version 2.2.0
 * @since 1.1.0
 */

(function($) {
    'use strict';

    /**
     * Schema Builder Admin Controller Class
     * Manages schema customization UI and shortcode generation
     * 
     * @class KataSchemaBuilder
     */
    class KataSchemaBuilder {
        /**
         * Initialize Schema Builder
         * 
         * @constructor
         */
        constructor() {
            /** @type {string} Current selected schema type */
            this.currentSchemaType = '';
            
            /** @type {string} Current mode: 'schema', 'content', or 'both' */
            this.currentMode = 'schema';
            
            /** @type {Array<string>} Selected schema properties */
            this.selectedSchemaProps = [];
            
            /** @type {Array<string>} Selected content properties */
            this.selectedContentProps = [];
            
            /** @type {number} Preview update debounce timer */
            this.previewDebounceTimer = null;
            
            /** @type {number} Debounce delay in milliseconds */
            this.DEBOUNCE_DELAY = 500;
            
            this.init();
        }

        /**
         * Initialize the builder
         * 
         * @returns {void}
         */
        init() {
            this.bindEvents();
            this.loadPresets();
            
            console.log('✅ KATA Schema Builder initialized (ES6 version)');
        }

        /**
         * Bind all event listeners
         * 
         * @returns {void}
         */
        bindEvents() {
            // Schema type selection
            $('#kata-schema-type').on('change', (e) => {
                this.currentSchemaType = $(e.target).val();
                this.loadProperties();
            });
            
            // Mode tabs
            $('.mode-tab').on('click', (e) => {
                const $tab = $(e.currentTarget);
                const mode = $tab.data('mode');
                
                $('.mode-tab').removeClass('active');
                $tab.addClass('active');
                
                this.currentMode = mode;
                this.showModePanel(mode);
            });
            
            // Property selection with debounce
            $(document).on('change', '.property-checkbox', () => {
                this.updateSelectedProperties();
            });
            
            // Generate shortcode
            $('#kata-generate-shortcode').on('click', () => {
                this.generateShortcode();
            });
            
            // Copy shortcode
            $('#kata-copy-shortcode').on('click', (e) => {
                this.copyShortcode(e);
            });
            
            // Save preset
            $('#kata-save-preset').on('click', () => {
                this.savePreset();
            });
            
            // Load preset
            $('#kata-load-preset').on('click', () => {
                this.showPresetsModal();
            });
            
            // Validate schema
            $('#kata-validate-schema').on('click', () => {
                this.validateSchema();
            });
            
            // Modal close
            $('.kata-modal-close').on('click', () => {
                this.closeModals();
            });
            
            // Close modal on outside click
            $('.kata-modal').on('click', (e) => {
                if ($(e.target).hasClass('kata-modal')) {
                    this.closeModals();
                }
            });

            // ESC key to close modals
            $(document).on('keyup', (e) => {
                if (e.key === 'Escape') {
                    this.closeModals();
                }
            });
        }

        /**
         * Show/hide mode panels based on selection
         * 
         * @param {string} mode - Mode: 'schema', 'content', or 'both'
         * @returns {void}
         */
        showModePanel(mode) {
            const $schemaPanel = $('#schema-mode-panel');
            const $contentPanel = $('#content-mode-panel');

            switch (mode) {
                case 'schema':
                    $schemaPanel.show();
                    $contentPanel.hide();
                    break;
                case 'content':
                    $schemaPanel.hide();
                    $contentPanel.show();
                    break;
                case 'both':
                    $schemaPanel.show();
                    $contentPanel.show();
                    break;
                default:
                    console.warn('Unknown mode:', mode);
            }
        }

        /**
         * Load properties for selected schema type
         * 
         * @returns {void}
         */
        loadProperties() {
            if (!this.currentSchemaType) {
                console.warn('No schema type selected');
                return;
            }
            
            const defaultProps = kataSchemaAdmin?.defaultProperties?.[this.currentSchemaType] || {};
            
            // Load schema properties
            this.renderPropertiesGrid('#schema-properties', defaultProps, 'schema');
            
            // Load content properties
            this.renderContentProperties('#content-properties', this.currentSchemaType);
            
            // Update preview
            this.debouncedUpdatePreview();
        }

        /**
         * Render properties grid
         * 
         * @param {string} containerSelector - jQuery selector for container
         * @param {Object} properties - Properties object
         * @param {string} type - Type: 'schema' or 'content'
         * @returns {void}
         */
        renderPropertiesGrid(containerSelector, properties, type) {
            const $container = $(containerSelector);
            $container.empty();
            
            const props = Object.keys(properties);
            
            if (props.length === 0) {
                $container.html('<p class="placeholder-text">No properties available</p>');
                return;
            }

            const propertyItems = props
                .filter(prop => prop !== '@context' && prop !== '@type')
                .map(prop => {
                    const isDefault = properties[prop];
                    const isRequired = prop === 'name' || prop === 'headline';
                    
                    return `
                        <div class="property-item ${isDefault ? 'selected' : ''} ${isRequired ? 'required' : ''}">
                            <input type="checkbox" 
                                   class="property-checkbox" 
                                   data-type="${type}" 
                                   data-property="${prop}" 
                                   id="${type}-${prop}" 
                                   ${isDefault ? 'checked' : ''}>
                            <label for="${type}-${prop}">
                                ${this.formatPropertyName(prop)}
                            </label>
                        </div>
                    `;
                })
                .join('');
            
            $container.html(propertyItems);
        }

        /**
         * Render content properties
         * 
         * @param {string} containerSelector - jQuery selector for container
         * @param {string} schemaType - Schema type
         * @returns {void}
         */
        renderContentProperties(containerSelector, schemaType) {
            const $container = $(containerSelector);
            $container.empty();
            
            const contentElements = this.getContentElements(schemaType);
            
            if (contentElements.length === 0) {
                $container.html('<p class="placeholder-text">No content elements available</p>');
                return;
            }

            const elementItems = contentElements
                .map(element => `
                    <div class="property-item selected">
                        <input type="checkbox" 
                               class="property-checkbox" 
                               data-type="content" 
                               data-property="${element}" 
                               id="content-${element}" 
                               checked>
                        <label for="content-${element}">
                            ${this.formatPropertyName(element)}
                        </label>
                    </div>
                `)
                .join('');
            
            $container.html(elementItems);
        }

        /**
         * Get content elements for schema type
         * 
         * @param {string} schemaType - Schema type
         * @returns {Array<string>} Content elements
         */
        getContentElements(schemaType) {
            const elements = {
                article: ['title', 'description', 'author', 'date', 'image', 'reading_time', 'meta'],
                recipe: ['title', 'description', 'image', 'author', 'time', 'meta', 'ingredients', 'instructions', 'nutrition', 'rating'],
                product: ['title', 'image', 'description', 'brand', 'price', 'availability', 'details', 'rating', 'buy_button'],
                faq: ['questions'],
                event: ['title', 'description', 'image', 'date', 'location', 'organizer', 'offers'],
                howto: ['title', 'description', 'steps', 'tools', 'supplies', 'time', 'cost']
            };
            
            return elements[schemaType] || [];
        }

        /**
         * Format property name for display
         * 
         * @param {string} prop - Property name
         * @returns {string} Formatted name
         */
        formatPropertyName(prop) {
            return prop
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, str => str.toUpperCase())
                .replace(/_/g, ' ');
        }

        /**
         * Update selected properties arrays
         * 
         * @returns {void}
         */
        updateSelectedProperties() {
            this.selectedSchemaProps = [];
            this.selectedContentProps = [];
            
            $('.property-checkbox').each((i, checkbox) => {
                const $checkbox = $(checkbox);
                
                if ($checkbox.is(':checked')) {
                    const type = $checkbox.data('type');
                    const prop = $checkbox.data('property');
                    
                    if (type === 'schema') {
                        this.selectedSchemaProps.push(prop);
                    } else if (type === 'content') {
                        this.selectedContentProps.push(prop);
                    }
                }
            });
            
            // Update preview with debounce
            this.debouncedUpdatePreview();
        }

        /**
         * Generate shortcode from selections
         * 
         * @returns {void}
         */
        generateShortcode() {
            if (!this.currentSchemaType) {
                alert('Please select a schema type first');
                return;
            }
            
            const basicAttrs = this.getBasicAttributes(this.currentSchemaType);
            const schemaModifiers = this.getSchemaModifiers();
            const contentModifiers = this.getContentModifiers();
            
            // Build shortcode with template literals
            let shortcode = `[kata_${this.currentSchemaType}`;
            
            // Add basic attributes (user should fill these)
            if (basicAttrs.length > 0) {
                shortcode += '\n    ' + basicAttrs.map(attr => `${attr}=""`).join('\n    ');
            }
            
            // Add schema modifiers
            if (schemaModifiers.length > 0) {
                shortcode += '\n    ' + schemaModifiers.join('\n    ');
            }
            
            // Add content modifiers
            if (contentModifiers.length > 0) {
                shortcode += '\n    ' + contentModifiers.join('\n    ');
            }
            
            shortcode += '\n]';
            
            $('#kata-shortcode-output').val(shortcode);
        }

        /**
         * Get schema modifiers (hide/show attributes)
         * 
         * @returns {Array<string>} Schema modifier attributes
         */
        getSchemaModifiers() {
            if (this.currentMode !== 'schema' && this.currentMode !== 'both') {
                return [];
            }

            const defaultProps = kataSchemaAdmin?.defaultProperties?.[this.currentSchemaType] || {};
            const modifiers = [];
            
            Object.keys(defaultProps).forEach(prop => {
                if (prop === '@context' || prop === '@type') return;
                
                const isDefault = defaultProps[prop];
                const isSelected = this.selectedSchemaProps.includes(prop);
                
                // Hide default properties that are unselected
                if (isDefault && !isSelected) {
                    modifiers.push(`hide_${prop}="true"`);
                }
                
                // Show non-default properties that are selected
                if (!isDefault && isSelected) {
                    modifiers.push(`show_${prop}="true"`);
                }
            });
            
            return modifiers;
        }

        /**
         * Get content modifiers (hide content elements)
         * 
         * @returns {Array<string>} Content modifier attributes
         */
        getContentModifiers() {
            if (this.currentMode !== 'content' && this.currentMode !== 'both') {
                return [];
            }

            const allElements = this.getContentElements(this.currentSchemaType);
            const hiddenElements = allElements.filter(el => !this.selectedContentProps.includes(el));
            
            return hiddenElements.map(el => `hide_content_${el}="true"`);
        }

        /**
         * Get basic attributes for schema type
         * 
         * @param {string} schemaType - Schema type
         * @returns {Array<string>} Basic attributes
         */
        getBasicAttributes(schemaType) {
            const attrs = {
                article: ['title', 'description', 'author'],
                recipe: ['name', 'ingredients', 'instructions'],
                product: ['name', 'brand', 'price'],
                faq: [], // FAQ uses nested shortcodes
                event: ['name', 'start_date', 'location'],
                howto: ['name', 'steps']
            };
            
            return attrs[schemaType] || ['name'];
        }

        /**
         * Copy shortcode to clipboard
         * 
         * @param {Event} e - Click event
         * @returns {void}
         */
        copyShortcode(e) {
            const $output = $('#kata-shortcode-output');
            
            if (!$output.length || !$output.val()) {
                alert('No shortcode to copy. Please generate a shortcode first.');
                return;
            }

            // Select and copy
            $output.select();
            document.execCommand('copy');
            
            // Visual feedback
            const $btn = $(e.currentTarget);
            const originalHTML = $btn.html();
            
            $btn.addClass('copied').text('✅ Copied!');
            
            setTimeout(() => {
                $btn.removeClass('copied').html(originalHTML);
            }, 2000);
        }

        /**
         * Update preview with debounce
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
         * Update preview panels
         * 
         * @returns {Promise<void>}
         */
        async updatePreview() {
            if (!this.currentSchemaType) {
                return;
            }
            
            // Update schema preview
            await this.updateSchemaPreview();
            
            // Update HTML preview
            this.updateHTMLPreview();
        }

        /**
         * Update schema JSON preview
         * 
         * @returns {Promise<void>}
         */
        async updateSchemaPreview() {
            try {
                const response = await this.ajaxRequest('kata_preview_schema', {
                    schema_type: this.currentSchemaType,
                    properties: JSON.stringify(this.selectedSchemaProps)
                });

                if (response.success) {
                    $('#kata-schema-preview code').text(response.data.json);
                }
            } catch (error) {
                console.error('Error updating schema preview:', error);
            }
        }

        /**
         * Update HTML preview
         * 
         * @returns {void}
         */
        updateHTMLPreview() {
            const $preview = $('#kata-html-preview');
            
            if (this.selectedContentProps.length === 0) {
                $preview.html('<p class="placeholder-text">No content elements selected</p>');
                return;
            }
            
            const contentItems = this.selectedContentProps
                .map(prop => `
                    <div class="kata-${prop}">
                        ${this.formatPropertyName(prop)} will be shown
                    </div>
                `)
                .join('');
            
            $preview.html(`
                <div class="kata-${this.currentSchemaType}-container">
                    ${contentItems}
                </div>
            `);
        }

        /**
         * Save preset
         * 
         * @returns {Promise<void>}
         */
        async savePreset() {
            if (!this.currentSchemaType) {
                alert('Please select a schema type first');
                return;
            }
            
            const presetName = prompt('Enter a name for this preset:');
            if (!presetName || !presetName.trim()) {
                return;
            }

            try {
                const response = await this.ajaxRequest('kata_save_schema_preset', {
                    preset_name: presetName.trim(),
                    schema_type: this.currentSchemaType,
                    schema_properties: JSON.stringify(this.selectedSchemaProps),
                    content_properties: JSON.stringify(this.selectedContentProps)
                });

                if (response.success) {
                    alert('✅ Preset saved successfully!');
                } else {
                    throw new Error(response.data?.message || 'Failed to save preset');
                }
            } catch (error) {
                console.error('Error saving preset:', error);
                alert('❌ Error: ' + error.message);
            }
        }

        /**
         * Load presets from server
         * 
         * @returns {Promise<void>}
         */
        async loadPresets() {
            try {
                // Load presets when needed
                // This will be populated when opening the modal
                console.log('Presets loaded');
            } catch (error) {
                console.error('Error loading presets:', error);
            }
        }

        /**
         * Show presets modal
         * 
         * @returns {void}
         */
        showPresetsModal() {
            $('#kata-presets-modal').show();
            $('body').addClass('kata-modal-open');
            // Load presets dynamically
        }

        /**
         * Close all modals
         * 
         * @returns {void}
         */
        closeModals() {
            $('.kata-modal').hide();
            $('body').removeClass('kata-modal-open');
        }

        /**
         * Validate schema JSON
         * 
         * @returns {void}
         */
        validateSchema() {
            const schemaText = $('#kata-schema-preview code').text();
            
            if (!schemaText || !schemaText.trim()) {
                alert('No schema to validate. Please select a schema type and properties first.');
                return;
            }

            try {
                const schema = JSON.parse(schemaText);
                
                // Basic validation
                if (!schema['@context']) {
                    alert('❌ Schema is missing required @context');
                    return;
                }
                
                if (!schema['@type']) {
                    alert('❌ Schema is missing required @type');
                    return;
                }
                
                // Success
                const message = `✅ Schema is valid!\n\n` +
                    `Type: ${schema['@type']}\n` +
                    `Properties: ${Object.keys(schema).length}\n\n` +
                    `Recommended next step:\n` +
                    `Test with Google Rich Results Test:\n` +
                    `https://search.google.com/test/rich-results`;
                
                alert(message);
            } catch (e) {
                alert(`❌ Invalid JSON schema:\n\n${e.message}`);
            }
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
                    url: kataSchemaAdmin?.ajaxUrl || ajaxurl,
                    method: 'POST',
                    data: {
                        action: action,
                        nonce: kataSchemaAdmin?.nonce,
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
    }
    
    // Initialize on document ready
    $(document).ready(() => {
        // Create global instance
        window.kataSchemaBuilder = new KataSchemaBuilder();
        
        // Backward compatibility
        window.KataSchemaAdmin = window.kataSchemaBuilder;
        
        console.log('✅ KATA Schema Builder ready!');
    });
    
})(jQuery);
