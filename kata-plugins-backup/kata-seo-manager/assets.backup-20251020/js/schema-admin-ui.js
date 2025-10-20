/**
 * KATA Schema Customization Admin UI JavaScript
 * Version: 1.1.0
 */

(function($) {
    'use strict';
    
    const KataSchemaAdmin = {
        currentSchemaType: '',
        currentMode: 'schema',
        selectedSchemaProps: [],
        selectedContentProps: [],
        
        init() {
            this.bindEvents();
            this.loadPresets();
        },
        
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
            
            // Property selection
            $(document).on('change', '.property-checkbox', (e) => {
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
                $('.kata-modal').hide();
            });
            
            // Close modal on outside click
            $('.kata-modal').on('click', (e) => {
                if ($(e.target).hasClass('kata-modal')) {
                    $('.kata-modal').hide();
                }
            });
        },
        
        showModePanel(mode) {
            if (mode === 'schema') {
                $('#schema-mode-panel').show();
                $('#content-mode-panel').hide();
            } else if (mode === 'content') {
                $('#schema-mode-panel').hide();
                $('#content-mode-panel').show();
            } else if (mode === 'both') {
                $('#schema-mode-panel').show();
                $('#content-mode-panel').show();
            }
        },
        
        loadProperties() {
            if (!this.currentSchemaType) {
                return;
            }
            
            const defaultProps = kataSchemaAdmin.defaultProperties[this.currentSchemaType] || {};
            
            // Load schema properties
            this.renderPropertiesGrid('#schema-properties', defaultProps, 'schema');
            
            // Load content properties
            this.renderContentProperties('#content-properties', this.currentSchemaType);
        },
        
        renderPropertiesGrid(container, properties, type) {
            const $container = $(container);
            $container.empty();
            
            if (Object.keys(properties).length === 0) {
                $container.html('<p class="placeholder-text">No properties available</p>');
                return;
            }
            
            Object.keys(properties).forEach(prop => {
                if (prop === '@context' || prop === '@type') {
                    return; // Skip required fields
                }
                
                const isDefault = properties[prop];
                const isRequired = prop === 'name' || prop === 'headline';
                
                const $item = $(`
                    <div class="property-item ${isDefault ? 'selected' : ''} ${isRequired ? 'required' : ''}">
                        <input type="checkbox" 
                               class="property-checkbox" 
                               data-type="${type}" 
                               data-property="${prop}" 
                               id="${type}-${prop}" 
                               ${isDefault ? 'checked' : ''}>
                        <label for="${type}-${prop}">${this.formatPropertyName(prop)}</label>
                    </div>
                `);
                
                $container.append($item);
            });
        },
        
        renderContentProperties(container, schemaType) {
            const $container = $(container);
            $container.empty();
            
            const contentElements = this.getContentElements(schemaType);
            
            if (contentElements.length === 0) {
                $container.html('<p class="placeholder-text">No content elements available</p>');
                return;
            }
            
            contentElements.forEach(element => {
                const $item = $(`
                    <div class="property-item selected">
                        <input type="checkbox" 
                               class="property-checkbox" 
                               data-type="content" 
                               data-property="${element}" 
                               id="content-${element}" 
                               checked>
                        <label for="content-${element}">${this.formatPropertyName(element)}</label>
                    </div>
                `);
                
                $container.append($item);
            });
        },
        
        getContentElements(schemaType) {
            const elements = {
                'article': ['title', 'description', 'author', 'date', 'image', 'reading_time', 'meta'],
                'recipe': ['title', 'description', 'image', 'author', 'time', 'meta', 'ingredients', 'instructions', 'nutrition', 'rating'],
                'product': ['title', 'image', 'description', 'brand', 'price', 'availability', 'details', 'rating', 'buy_button'],
                'faq': ['questions'],
                'event': ['title', 'description', 'image', 'date', 'location', 'organizer', 'offers'],
                'howto': ['title', 'description', 'steps', 'tools', 'supplies', 'time', 'cost']
            };
            
            return elements[schemaType] || [];
        },
        
        formatPropertyName(prop) {
            return prop
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, str => str.toUpperCase())
                .replace(/_/g, ' ');
        },
        
        updateSelectedProperties() {
            this.selectedSchemaProps = [];
            this.selectedContentProps = [];
            
            $('.property-checkbox').each((i, checkbox) => {
                const $checkbox = $(checkbox);
                const type = $checkbox.data('type');
                const prop = $checkbox.data('property');
                
                if ($checkbox.is(':checked')) {
                    if (type === 'schema') {
                        this.selectedSchemaProps.push(prop);
                    } else if (type === 'content') {
                        this.selectedContentProps.push(prop);
                    }
                }
            });
            
            // Update preview
            this.updatePreview();
        },
        
        generateShortcode() {
            if (!this.currentSchemaType) {
                alert('Please select a schema type first');
                return;
            }
            
            let shortcode = `[kata_${this.currentSchemaType}`;
            
            // Add basic attributes (user should fill these)
            const basicAttrs = this.getBasicAttributes(this.currentSchemaType);
            basicAttrs.forEach(attr => {
                shortcode += `\n    ${attr}=""`;
            });
            
            // MODE 1: Schema filtering
            if (this.currentMode === 'schema' || this.currentMode === 'both') {
                const defaultProps = kataSchemaAdmin.defaultProperties[this.currentSchemaType] || {};
                const hiddenProps = [];
                const shownProps = [];
                
                Object.keys(defaultProps).forEach(prop => {
                    if (prop === '@context' || prop === '@type') return;
                    
                    const isDefault = defaultProps[prop];
                    const isSelected = this.selectedSchemaProps.includes(prop);
                    
                    if (isDefault && !isSelected) {
                        hiddenProps.push(prop);
                    } else if (!isDefault && isSelected) {
                        shownProps.push(prop);
                    }
                });
                
                if (hiddenProps.length > 0) {
                    hiddenProps.forEach(prop => {
                        shortcode += `\n    hide_${prop}="true"`;
                    });
                }
                
                if (shownProps.length > 0) {
                    shownProps.forEach(prop => {
                        shortcode += `\n    show_${prop}="true"`;
                    });
                }
            }
            
            // MODE 2: Content display
            if (this.currentMode === 'content' || this.currentMode === 'both') {
                const allElements = this.getContentElements(this.currentSchemaType);
                const hiddenElements = allElements.filter(el => !this.selectedContentProps.includes(el));
                
                if (hiddenElements.length > 0) {
                    hiddenElements.forEach(el => {
                        shortcode += `\n    hide_content_${el}="true"`;
                    });
                }
            }
            
            shortcode += '\n]';
            
            $('#kata-shortcode-output').val(shortcode);
        },
        
        getBasicAttributes(schemaType) {
            const attrs = {
                'article': ['title', 'description', 'author'],
                'recipe': ['name', 'ingredients', 'instructions'],
                'product': ['name', 'brand', 'price'],
                'faq': [], // FAQ uses nested shortcodes
                'event': ['name', 'start_date', 'location'],
                'howto': ['name', 'steps']
            };
            
            return attrs[schemaType] || ['name'];
        },
        
        copyShortcode(e) {
            const $output = $('#kata-shortcode-output');
            $output.select();
            document.execCommand('copy');
            
            const $btn = $(e.currentTarget);
            $btn.addClass('copied').text('Copied!');
            
            setTimeout(() => {
                $btn.removeClass('copied').html('<span class="dashicons dashicons-clipboard"></span> Copy');
            }, 2000);
        },
        
        updatePreview() {
            if (!this.currentSchemaType) {
                return;
            }
            
            // Update schema preview
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'kata_preview_schema',
                    nonce: kataSchemaAdmin.nonce,
                    schema_type: this.currentSchemaType,
                    properties: JSON.stringify(this.selectedSchemaProps)
                },
                success: (response) => {
                    if (response.success) {
                        $('#kata-schema-preview code').text(response.data.json);
                    }
                }
            });
            
            // Update HTML preview
            this.updateHTMLPreview();
        },
        
        updateHTMLPreview() {
            const $preview = $('#kata-html-preview');
            $preview.empty();
            
            if (this.selectedContentProps.length === 0) {
                $preview.html('<p class="placeholder-text">No content elements selected</p>');
                return;
            }
            
            let html = `<div class="kata-${this.currentSchemaType}-container">`;
            
            this.selectedContentProps.forEach(prop => {
                html += `<div class="kata-${prop}">${this.formatPropertyName(prop)} will be shown</div>`;
            });
            
            html += '</div>';
            
            $preview.html(html);
        },
        
        savePreset() {
            if (!this.currentSchemaType) {
                alert('Please select a schema type first');
                return;
            }
            
            const presetName = prompt('Enter a name for this preset:');
            if (!presetName) {
                return;
            }
            
            $.ajax({
                url: kataSchemaAdmin.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'kata_save_schema_preset',
                    nonce: kataSchemaAdmin.nonce,
                    preset_name: presetName,
                    schema_type: this.currentSchemaType,
                    schema_properties: JSON.stringify(this.selectedSchemaProps),
                    content_properties: JSON.stringify(this.selectedContentProps)
                },
                success: (response) => {
                    if (response.success) {
                        alert('Preset saved successfully!');
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }
            });
        },
        
        loadPresets() {
            // Load from WordPress options
            // This will be populated when opening the modal
        },
        
        showPresetsModal() {
            $('#kata-presets-modal').show();
            // Load presets dynamically
        },
        
        validateSchema() {
            const schemaText = $('#kata-schema-preview code').text();
            
            try {
                const schema = JSON.parse(schemaText);
                
                // Basic validation
                if (!schema['@context'] || !schema['@type']) {
                    alert('Schema is missing required @context or @type');
                    return;
                }
                
                alert('Schema is valid! ✅\n\nRecommended: Test with Google Rich Results Test:\nhttps://search.google.com/test/rich-results');
            } catch (e) {
                alert('Invalid JSON schema: ' + e.message);
            }
        }
    };
    
    // Initialize on document ready
    $(document).ready(() => {
        KataSchemaAdmin.init();
    });
    
})(jQuery);
