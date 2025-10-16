/**
 * KATA Schema TinyMCE Button Plugin
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

(function() {
    'use strict';
    
    // Prevent duplicate initialization
    if (typeof tinymce === 'undefined') {
        console.warn('TinyMCE not loaded');
        return;
    }
    
    tinymce.PluginManager.add('kata_schema', function(editor, url) {
        
        // Prevent duplicate initialization (learned from kata-seo-manager bugfix)
        if (editor.kata_schema_initialized) {
            return;
        }
        editor.kata_schema_initialized = true;
        
        // Add button
        editor.addButton('kata_schema', {
            text: 'KATA Schema',
            icon: 'code',
            tooltip: 'Chèn Schema Markup',
            onclick: function() {
                openSchemaModal();
            }
        });
        
        /**
         * Open schema selection modal
         */
        function openSchemaModal() {
            
            // Check if data is available
            if (typeof kataSchemaData === 'undefined') {
                alert('Không thể tải danh sách schema. Vui lòng thử lại.');
                return;
            }
            
            var schemas = kataSchemaData.schemas || {};
            var schemaTypes = kataSchemaData.schemaTypes || {};
            
            // Build schema list HTML
            var schemaListHTML = '';
            
            if (Object.keys(schemas).length === 0) {
                schemaListHTML = '<p style="padding: 20px; text-align: center; color: #999;">Chưa có schema nào. <a href="' + 
                    (typeof adminUrl !== 'undefined' ? adminUrl : '/wp-admin/') + 
                    'admin.php?page=kata-schema-add" target="_blank">Tạo schema mới</a></p>';
            } else {
                schemaListHTML = '<div style="max-height: 400px; overflow-y: auto;">';
                
                for (var type in schemas) {
                    if (schemas.hasOwnProperty(type)) {
                        var typeLabel = schemaTypes[type] || type;
                        schemaListHTML += '<div style="margin-bottom: 20px;">';
                        schemaListHTML += '<h3 style="margin: 0 0 10px 0; padding: 10px; background: #042277; color: white; border-radius: 4px;">' + 
                            typeLabel + '</h3>';
                        schemaListHTML += '<div style="padding: 0 10px;">';
                        
                        schemas[type].forEach(function(schema) {
                            schemaListHTML += '<div style="padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; transition: all 0.2s;" ' +
                                'class="kata-schema-item" data-id="' + schema.id + '" data-name="' + escapeHtml(schema.name) + '">';
                            schemaListHTML += '<strong>' + escapeHtml(schema.name) + '</strong>';
                            schemaListHTML += '<div style="margin-top: 5px; font-size: 11px; color: #666;">ID: ' + schema.id + '</div>';
                            schemaListHTML += '</div>';
                        });
                        
                        schemaListHTML += '</div></div>';
                    }
                }
                
                schemaListHTML += '</div>';
            }
            
            // Open modal
            editor.windowManager.open({
                title: 'Chọn Schema',
                width: 600,
                height: 500,
                body: [
                    {
                        type: 'container',
                        html: schemaListHTML
                    }
                ],
                buttons: [
                    {
                        text: 'Đóng',
                        onclick: 'close'
                    }
                ]
            });
            
            // Add click handlers to schema items
            setTimeout(function() {
                var items = document.querySelectorAll('.kata-schema-item');
                items.forEach(function(item) {
                    item.addEventListener('click', function() {
                        var schemaId = this.getAttribute('data-id');
                        var schemaName = this.getAttribute('data-name');
                        insertSchema(schemaId, schemaName);
                        editor.windowManager.close();
                    });
                    
                    // Hover effect
                    item.addEventListener('mouseenter', function() {
                        this.style.background = '#f0f0f1';
                        this.style.borderColor = '#042277';
                    });
                    item.addEventListener('mouseleave', function() {
                        this.style.background = '';
                        this.style.borderColor = '#ddd';
                    });
                });
            }, 100);
        }
        
        /**
         * Insert schema shortcode
         */
        function insertSchema(schemaId, schemaName) {
            var shortcode = '[kata_schema id="' + schemaId + '"]';
            
            // Insert at cursor position
            editor.insertContent(shortcode);
            
            // Show success message
            if (typeof editor.notificationManager !== 'undefined') {
                editor.notificationManager.open({
                    text: 'Đã chèn schema: ' + schemaName,
                    type: 'success',
                    timeout: 3000
                });
            }
        }
        
        /**
         * Escape HTML
         */
        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
    });
    
})();
