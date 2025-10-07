/**
 * Admin JavaScript
 * 
 * @package KATA_SEO_Manager
 */

(function($) {
    'use strict';
    
    var KataSEO = {
        init: function() {
            this.initModal();
            this.initMetaBox();
            this.initSchemaBuilder();
            this.bindEvents();
        },
        
        initModal: function() {
            var self = this;
            
            // Open modal - Including editor button
            $(document).on('click', '.kata-insert-schema-btn, .kata-add-schema, .kata-add-schema-btn, #kata-seo-insert-schema', function(e) {
                e.preventDefault();
                $('#kata-schema-modal').fadeIn(300);
                
                var type = $(this).data('type');
                if (type) {
                    self.selectSchemaType(type);
                }
            });
            
            // Close modal
            $(document).on('click', '.kata-modal-close, .kata-cancel-btn, .kata-modal-overlay', function(e) {
                e.preventDefault();
                self.closeModal();
            });
            
            // Schema type selection
            $(document).on('click', '.kata-schema-type-btn', function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                self.selectSchemaType(type);
            });
            
            // Back button
            $(document).on('click', '.kata-back-btn', function(e) {
                e.preventDefault();
                self.showStep(1);
            });
            
            // Tab switching
            $(document).on('click', '.kata-tab-btn', function(e) {
                e.preventDefault();
                var tab = $(this).data('tab');
                self.switchTab(tab);
            });
            
            // Insert schema
            $(document).on('click', '.kata-insert-btn', function(e) {
                e.preventDefault();
                self.insertSchema();
            });
            
            // Validate schema
            $(document).on('click', '.kata-validate-btn', function(e) {
                e.preventDefault();
                self.validateSchema();
            });
        },
        
        initMetaBox: function() {
            var self = this;
            
            // Edit schema
            $(document).on('click', '.kata-edit-schema', function(e) {
                e.preventDefault();
                var index = $(this).data('index');
                self.editSchema(index);
            });
            
            // Toggle schema
            $(document).on('click', '.kata-toggle-schema', function(e) {
                e.preventDefault();
                var index = $(this).data('index');
                self.toggleSchema(index);
            });
            
            // Delete schema
            $(document).on('click', '.kata-delete-schema', function(e) {
                e.preventDefault();
                if (confirm(kataAdmin.confirmDelete)) {
                    var index = $(this).data('index');
                    self.deleteSchema(index);
                }
            });
            
            // Validate all
            $(document).on('click', '.kata-validate-all-schemas', function(e) {
                e.preventDefault();
                self.validateAllSchemas();
            });
        },
        
        initSchemaBuilder: function() {
            var self = this;
            
            // Form input change
            $(document).on('change keyup', '#kata-schema-form input, #kata-schema-form textarea, #kata-schema-form select', function() {
                self.updatePreview();
                $('.kata-insert-btn').prop('disabled', false);
            });
            
            // Template selection
            $(document).on('change', '#kata-template-selector', function() {
                var templateId = $(this).val();
                if (templateId) {
                    self.loadTemplate(templateId);
                }
            });
        },
        
        bindEvents: function() {
            // Image upload
            $(document).on('click', '.kata-upload-image', function(e) {
                e.preventDefault();
                var button = $(this);
                var input = button.prev('input');
                
                var frame = wp.media({
                    title: kataAdmin.selectImage,
                    button: { text: kataAdmin.useImage },
                    multiple: false
                });
                
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    input.val(attachment.url);
                    
                    var preview = button.next('.kata-image-preview');
                    if (preview.length) {
                        preview.find('img').attr('src', attachment.url);
                    } else {
                        button.after('<div class="kata-image-preview"><img src="' + attachment.url + '" style="max-width:200px;height:auto;"></div>');
                    }
                });
                
                frame.open();
            });
        },
        
        selectSchemaType: function(type) {
            var self = this;
            
            this.currentType = type;
            this.showStep(2);
            
            // Load schema fields
            $.ajax({
                url: kataAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_get_schema_fields',
                    nonce: kataAdmin.nonce,
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        self.renderForm(response.data.fields, response.data.example);
                        self.loadTemplates(type);
                    } else {
                        alert(response.data.message || kataAdmin.error);
                    }
                }
            });
        },
        
        renderForm: function(fields, example) {
            var html = '';
            
            $.each(fields, function(name, field) {
                html += '<div class="kata-field">';
                html += '<label class="kata-field-label">';
                html += field.label;
                if (field.required) {
                    html += ' <span class="required">*</span>';
                }
                html += '</label>';
                
                if (field.type === 'textarea') {
                    html += '<textarea name="' + name + '" class="kata-field-input" rows="5" placeholder="' + (field.placeholder || '') + '">' + (example[name] || '') + '</textarea>';
                } else if (field.type === 'select') {
                    html += '<select name="' + name + '" class="kata-field-input">';
                    $.each(field.options, function(value, label) {
                        html += '<option value="' + value + '">' + label + '</option>';
                    });
                    html += '</select>';
                } else if (field.type === 'image') {
                    html += '<input type="url" name="' + name + '" class="kata-field-input kata-image-url" value="' + (example[name] || '') + '" placeholder="' + (field.placeholder || '') + '">';
                    html += '<button type="button" class="button kata-upload-image">' + kataAdmin.uploadImage + '</button>';
                } else if (field.type === 'repeater') {
                    html += '<div class="kata-repeater-items" data-field="' + name + '"></div>';
                    html += '<button type="button" class="button kata-repeater-add">' + kataAdmin.addItem + '</button>';
                } else {
                    html += '<input type="' + field.type + '" name="' + name + '" class="kata-field-input" value="' + (example[name] || '') + '" placeholder="' + (field.placeholder || '') + '">';
                }
                
                if (field.description) {
                    html += '<p class="kata-field-description">' + field.description + '</p>';
                }
                
                html += '</div>';
            });
            
            $('#kata-schema-form').html(html);
        },
        
        loadTemplates: function(type) {
            $.ajax({
                url: kataAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_get_templates',
                    nonce: kataAdmin.nonce,
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        var html = '<option value="">' + kataAdmin.selectTemplate + '</option>';
                        $.each(response.data, function(i, template) {
                            html += '<option value="' + template.id + '">' + template.name + '</option>';
                        });
                        $('#kata-template-selector').html(html);
                    }
                }
            });
        },
        
        updatePreview: function() {
            var self = this;
            var data = this.getFormData();
            
            $.ajax({
                url: kataAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_preview_schema',
                    nonce: kataAdmin.nonce,
                    type: this.currentType,
                    data: data
                },
                success: function(response) {
                    if (response.success) {
                        $('#kata-schema-preview code').text(response.data.json);
                    }
                }
            });
        },
        
        insertSchema: function() {
            var self = this;
            var data = this.getFormData();
            
            $('.kata-insert-btn').prop('disabled', true).text(kataAdmin.inserting);
            
            $.ajax({
                url: kataAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'kata_insert_schema',
                    nonce: kataAdmin.nonce,
                    post_id: $('#post_ID').val(),
                    type: this.currentType,
                    data: data
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        self.closeModal();
                        location.reload();
                    } else {
                        alert(response.data.message || kataAdmin.error);
                        $('.kata-insert-btn').prop('disabled', false).text(kataAdmin.insertSchema);
                    }
                }
            });
        },
        
        getFormData: function() {
            var data = {};
            $('#kata-schema-form input, #kata-schema-form textarea, #kata-schema-form select').each(function() {
                var name = $(this).attr('name');
                var value = $(this).val();
                if (name) {
                    data[name] = value;
                }
            });
            return data;
        },
        
        showStep: function(step) {
            $('.kata-modal-step').hide();
            $('.kata-modal-step[data-step="' + step + '"]').show();
            
            if (step === 2) {
                $('.kata-validate-btn').show();
            } else {
                $('.kata-validate-btn').hide();
            }
        },
        
        switchTab: function(tab) {
            $('.kata-tab-btn').removeClass('active');
            $('.kata-tab-btn[data-tab="' + tab + '"]').addClass('active');
            
            $('.kata-tab-content').hide();
            $('.kata-tab-content[data-tab="' + tab + '"]').show();
            
            if (tab === 'preview') {
                this.updatePreview();
            }
        },
        
        closeModal: function() {
            $('#kata-schema-modal').fadeOut(300);
            this.showStep(1);
            $('#kata-schema-form').html('');
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        KataSEO.init();
        
        // Quick Actions: Generate Demo Content
        $('#kata-generate-demo-btn').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var originalHtml = $btn.html();
            
            if (!confirm('Bạn có chắc muốn tạo dữ liệu mẫu?\n\nĐiều này sẽ tạo:\n- 26 schema types mẫu\n- 5 polls mẫu\n- 3 wheels mẫu\n- User interactions mẫu')) {
                return;
            }
            
            // Disable button and show loading
            $btn.prop('disabled', true).html(
                '<div class="kata-quick-action-icon"><div class="kata-spinner"></div></div>' +
                '<div class="kata-quick-action-content">' +
                '<div class="kata-quick-action-title">Đang tạo...</div>' +
                '<div class="kata-quick-action-desc">Vui lòng đợi</div>' +
                '</div>'
            );
            
            $.ajax({
                url: kata_ajax.url,
                type: 'POST',
                data: {
                    action: 'kata_generate_demo_content',
                    nonce: kata_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('✅ Tạo dữ liệu mẫu thành công!\n\n' +
                              'Schemas: ' + (response.data.schemas_created || 0) + '\n' +
                              'Polls: ' + (response.data.polls_created || 0) + '\n' +
                              'Wheels: ' + (response.data.wheels_created || 0) + '\n' +
                              'User Interactions: ' + (response.data.interactions_created || 0));
                        location.reload();
                    } else {
                        alert('❌ Lỗi: ' + (response.data.message || 'Không thể tạo dữ liệu mẫu'));
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    var errorMsg = 'Lỗi kết nối';
                    try {
                        var response = JSON.parse(xhr.responseText);
                        errorMsg = response.data?.message || errorMsg;
                    } catch(e) {
                        errorMsg = 'Parse error: ' + xhr.responseText.substring(0, 200);
                    }
                    alert('❌ ' + errorMsg);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
        
        // Quick Actions: Delete Demo Content
        $('#kata-delete-demo-btn').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var originalHtml = $btn.html();
            
            if (!confirm('⚠️ CẢNH BÁO: Bạn có chắc muốn XÓA tất cả dữ liệu mẫu?\n\nĐiều này sẽ xóa:\n- Tất cả schemas demo\n- Tất cả polls demo\n- Tất cả wheels demo\n- Tất cả user interactions\n\nHành động này KHÔNG THỂ HOÀN TÁC!')) {
                return;
            }
            
            // Double confirm
            if (!confirm('Xác nhận lần 2: Bạn THỰC SỰ muốn xóa tất cả dữ liệu mẫu?')) {
                return;
            }
            
            // Disable button and show loading
            $btn.prop('disabled', true).html(
                '<div class="kata-quick-action-icon"><div class="kata-spinner"></div></div>' +
                '<div class="kata-quick-action-content">' +
                '<div class="kata-quick-action-title">Đang xóa...</div>' +
                '<div class="kata-quick-action-desc">Vui lòng đợi</div>' +
                '</div>'
            );
            
            $.ajax({
                url: kata_ajax.url,
                type: 'POST',
                data: {
                    action: 'kata_delete_demo_content',
                    nonce: kata_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var deleted = response.data.deleted || {};
                        alert('✅ Xóa dữ liệu mẫu thành công!\n\n' +
                              'Schemas: ' + (deleted.schemas || 0) + '\n' +
                              'Polls: ' + (deleted.polls || 0) + '\n' +
                              'Poll Votes: ' + (deleted.poll_votes || 0) + '\n' +
                              'Wheels: ' + (deleted.wheels || 0) + '\n' +
                              'Wheel Prizes: ' + (deleted.wheel_prizes || 0) + '\n' +
                              'Wheel Spins: ' + (deleted.wheel_spins || 0) + '\n' +
                              'User Interactions: ' + (deleted.user_interactions || 0) + '\n\n' +
                              'Tổng cộng: ' + (response.data.total || 0) + ' records');
                        location.reload();
                    } else {
                        alert('❌ Lỗi: ' + (response.data.message || 'Không thể xóa dữ liệu mẫu'));
                        $btn.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    alert('❌ Lỗi kết nối: ' + error);
                    $btn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    });
    
})(jQuery);
