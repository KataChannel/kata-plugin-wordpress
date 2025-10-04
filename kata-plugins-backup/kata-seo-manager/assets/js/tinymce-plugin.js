(function() {
    'use strict';

    // TinyMCE Plugin for KATA SEO Manager
    tinymce.PluginManager.add('kata_seo_manager', function(editor, url) {
        
        // Template dữ liệu mẫu cho từng tính năng
        const templates = {
            faq: {
                title: '📝 FAQ Schema',
                shortcode: `[kata_faq]
[kata_faq_item question="KATA SEO Manager có miễn phí không?" answer="KATA SEO Manager hoàn toàn miễn phí và mã nguồn mở. Bạn có thể sử dụng cho bất kỳ dự án WordPress nào."]
[kata_faq_item question="Plugin hỗ trợ bao nhiều loại Schema?" answer="Plugin hỗ trợ 26+ loại Schema markup bao gồm Article, Product, Recipe, Event, FAQ, LocalBusiness và nhiều loại khác."]
[kata_faq_item question="Có tương thích với Gutenberg không?" answer="Có, plugin hoạt động tốt với cả Classic Editor và Gutenberg Block Editor."]
[/kata_faq]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>🔍 FAQ Schema sẽ tạo rich snippets:</h4>
                        <div class="faq-preview">
                            <p><strong>Q:</strong> KATA SEO Manager có miễn phí không?</p>
                            <p><strong>A:</strong> KATA SEO Manager hoàn toàn miễn phí...</p>
                            <hr>
                            <p><strong>Q:</strong> Plugin hỗ trợ bao nhiều loại Schema?</p>
                            <p><strong>A:</strong> Plugin hỗ trợ 26+ loại Schema markup...</p>
                        </div>
                        <small>✅ JSON-LD Schema tự động được tạo cho Google</small>
                    </div>
                `
            },

            quiz: {
                title: '🎯 Quiz Tương Tác',
                shortcode: `[kata_quiz title="Kiểm Tra Kiến Thức SEO với KATA Manager"]
[kata_quiz_question question="SEO là viết tắt của gì?" correct="0"]
[kata_quiz_option]Search Engine Optimization[/kata_quiz_option]
[kata_quiz_option]Social Engine Optimization[/kata_quiz_option]
[kata_quiz_option]Site Engine Optimization[/kata_quiz_option]
[/kata_quiz_question]

[kata_quiz_question question="Schema.org được tạo ra bởi những công ty nào?" correct="1"]
[kata_quiz_option]Google và Facebook[/kata_quiz_option]
[kata_quiz_option]Google, Bing, Yahoo, Yandex[/kata_quiz_option]
[kata_quiz_option]Chỉ có Google[/kata_quiz_option]
[/kata_quiz_question]

[kata_quiz_question question="Loại Schema nào phù hợp cho trang bán hàng?" correct="2"]
[kata_quiz_option]Article Schema[/kata_quiz_option]
[kata_quiz_option]FAQ Schema[/kata_quiz_option]
[kata_quiz_option]Product Schema[/kata_quiz_option]
[/kata_quiz_question]
[/kata_quiz]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>🎯 Quiz: "Kiểm Tra Kiến Thức SEO"</h4>
                        <p>📊 3 câu hỏi trắc nghiệm</p>
                        <p>⏱️ Hiển thị kết quả ngay lập tức</p>
                        <p>📈 Thống kê trong admin dashboard</p>
                        <small>✅ Tăng engagement và thu thập data người dùng</small>
                    </div>
                `
            },

            poll: {
                title: '📊 Bình Chọn',
                shortcode: `[kata_poll question="Tính năng KATA SEO Manager nào bạn thích nhất?"]
[kata_poll_option]26 loại Schema markup[/kata_poll_option]
[kata_poll_option]Editor buttons tiện lợi[/kata_poll_option]
[kata_poll_option]Analytics dashboard[/kata_poll_option]
[kata_poll_option]Tích hợp Gutenberg[/kata_poll_option]
[kata_poll_option]Tối ưu performance[/kata_poll_option]
[/kata_poll]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>📊 Poll: "Tính năng yêu thích nhất?"</h4>
                        <p>📝 5 lựa chọn voting</p>
                        <p>📈 Kết quả real-time</p>
                        <p>📊 Biểu đồ % cho từng option</p>
                        <small>✅ Thu thập feedback từ audience</small>
                    </div>
                `
            },

            form: {
                title: '📋 Form Lead',
                shortcode: `[kata_form title="Đăng Ký Nhận Tài Liệu SEO Miễn Phí" success_message="Cảm ơn! Tài liệu sẽ được gửi trong 24h."]
[kata_form_field type="text" label="Họ và tên" placeholder="Nguyễn Văn A" required="true"]
[kata_form_field type="email" label="Email địa chỉ" placeholder="email@domain.com" required="true"]
[kata_form_field type="tel" label="Số điện thoại" placeholder="0123456789" required="false"]
[kata_form_field type="select" label="Lĩnh vực quan tâm" options="SEO,Content Marketing,Social Media,E-commerce,Blog cá nhân" required="true"]
[kata_form_field type="textarea" label="Mô tả dự án" placeholder="Mô tả ngắn về website/dự án của bạn..." required="false"]
[/kata_form]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>📋 Form: "Đăng Ký Tài Liệu SEO"</h4>
                        <p>📝 5 trường dữ liệu (3 bắt buộc)</p>
                        <p>📧 Auto-response email</p>
                        <p>📊 Lead tracking trong admin</p>
                        <small>✅ Thu thập leads chất lượng cao</small>
                    </div>
                `
            },

            wheel: {
                title: '🎡 Vòng Quay',
                shortcode: `[kata_wheel title="Vòng Quay Ưu Đãi KATA SEO" requirement="email"]
[kata_wheel_prize text="Giảm 50%" probability="5" color="#ff6b6b" type="discount" value="50"]
[kata_wheel_prize text="Giảm 30%" probability="10" color="#4ecdc4" type="discount" value="30"]
[kata_wheel_prize text="Giảm 20%" probability="15" color="#45b7d1" type="discount" value="20"]
[kata_wheel_prize text="Giảm 15%" probability="25" color="#96ceb4" type="discount" value="15"]
[kata_wheel_prize text="Free Ebook" probability="20" color="#feca57" type="gift" value="seo-guide-2025"]
[kata_wheel_prize text="Free Support" probability="15" color="#ff9ff3" type="service" value="consultation"]
[kata_wheel_prize text="Thử lại" probability="10" color="#a29bfe" type="retry" value="0"]
[/kata_wheel]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>🎡 Vòng Quay: "Ưu Đãi KATA SEO"</h4>
                        <p>🎁 7 phần thưởng hấp dẫn</p>
                        <p>📧 Yêu cầu email để tham gia</p>
                        <p>🎯 Tỷ lệ trúng thưởng: 50-5%</p>
                        <small>✅ Gamification tăng conversion rate</small>
                    </div>
                `
            },

            rating: {
                title: '⭐ Đánh Giá',
                shortcode: `[kata_rating item_name="KATA SEO Manager Plugin" rating="4.9" review_count="247" review_text="Plugin SEO tuyệt vời! Hỗ trợ 26 loại Schema, dễ sử dụng, tích hợp mượt mà với WordPress. Highly recommended!"]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>⭐ Rating: "KATA SEO Manager Plugin"</h4>
                        <p>🌟 4.9/5 sao (247 đánh giá)</p>
                        <p>📝 Review: "Plugin SEO tuyệt vời!..."</p>
                        <p>🔍 Rich snippets trên Google</p>
                        <small>✅ Tăng CTR và trust của người dùng</small>
                    </div>
                `
            },

            product: {
                title: '🛒 Sản Phẩm',
                shortcode: `[kata_product 
    name="Khóa Học SEO Master Class 2025" 
    brand="KATA Academy"
    price="2990000" 
    currency="VND"
    availability="InStock"
    condition="New"
    sku="KATA-SEO-MASTER-2025"
    description="Khóa học SEO từ cơ bản đến nâng cao với 60+ bài học video, 100+ case study thực tế, hỗ trợ 1-1 với mentor."
    image="https://katachannel.com/images/seo-course-cover.jpg"
    category="Education > Online Courses > SEO"
]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>🛒 Product: "Khóa Học SEO Master Class"</h4>
                        <p>💰 ₫2,990,000 - Còn hàng</p>
                        <p>🏷️ KATA Academy</p>
                        <p>📦 SKU: KATA-SEO-MASTER-2025</p>
                        <small>✅ Schema hiển thị giá & thông tin trên Google</small>
                    </div>
                `
            },

            recipe: {
                title: '🍳 Công Thức',
                shortcode: `[kata_recipe 
    name="Phở Bò Hà Nội Truyền Thống"
    description="Công thức phở bò Hà Nội authentic với nước dùng ninh 8 tiếng"
    prep_time="PT45M"
    cook_time="PT8H"
    total_time="PT8H45M"
    servings="6"
    calories="450"
    cuisine="Vietnamese"
    category="Main Course"
    difficulty="Medium"
]

[kata_recipe_ingredients]
Xương ống bò: 1kg
Thịt nạm bò: 500g  
Thịt thăn bò: 300g
Bánh phở tươi: 600g
Hành tây: 2 củ lớn
Gừng: 100g
Quế, hoa hồi, đinh hương
[/kata_recipe_ingredients]

[kata_recipe_instructions]
Sơ chế xương và thịt bò, rửa sạch và chần qua nước sôi
Ninh xương bò với hành tây, gừng nướng trong 8 tiếng
Rang các loại gia vị cho thơm, cho vào nồi ninh
Thái thịt bò mỏng, trần bánh phở
Bày thịt, bánh phở vào tô, chan nước dùng nóng
[/kata_recipe_instructions]
[/kata_recipe]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>🍳 Recipe: "Phở Bò Hà Nội Truyền Thống"</h4>
                        <p>⏱️ Tổng thời gian: 8h45m (6 người ăn)</p>
                        <p>🔥 450 calories/portion</p>
                        <p>🇻🇳 Vietnamese cuisine</p>
                        <small>✅ Rich snippets với ảnh, rating, thời gian</small>
                    </div>
                `
            }
        };

        // Tạo menu dropdown chính
        editor.addButton('kata_seo_manager', {
            title: 'KATA SEO Manager',
            type: 'menubutton',
            icon: 'dashicon dashicons-chart-line',
            menu: Object.keys(templates).map(key => ({
                text: templates[key].title,
                onclick: function() {
                    // Chèn shortcode vào editor
                    editor.insertContent('\n' + templates[key].shortcode + '\n');
                    
                    // Hiển thị dialog preview
                    editor.windowManager.open({
                        title: templates[key].title + ' - Đã Thêm Thành Công!',
                        width: 500,
                        height: 400,
                        body: [
                            {
                                type: 'container',
                                html: `
                                    <div style="padding: 20px; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;">
                                        <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin-bottom: 15px;">
                                            <h3 style="color: #155724; margin: 0 0 10px 0;">✅ Đã thêm thành công!</h3>
                                            <p style="margin: 0; color: #155724;">Shortcode đã được chèn vào editor với dữ liệu mẫu.</p>
                                        </div>
                                        ${templates[key].preview}
                                        <div style="background: #e2e3e5; border-radius: 5px; padding: 10px; margin-top: 15px;">
                                            <p style="margin: 0; font-size: 12px; color: #6c757d;">
                                                💡 <strong>Tip:</strong> Bạn có thể chỉnh sửa nội dung trực tiếp trong editor. 
                                                Schema JSON-LD sẽ tự động được tạo khi publish bài viết.
                                            </p>
                                        </div>
                                    </div>
                                `
                            }
                        ],
                        buttons: [
                            {
                                text: 'Xem Analytics',
                                onclick: function() {
                                    window.open(ajaxurl.replace('/admin-ajax.php', '/admin.php?page=kata-seo-manager'), '_blank');
                                    this.parent().close();
                                }
                            },
                            {
                                text: 'OK',
                                onclick: 'close',
                                primary: true
                            }
                        ]
                    });
                }
            }))
        });

        // CSS cho preview styling  
        editor.on('init', function() {
            editor.dom.addStyle(`
                .kata-preview-box {
                    border: 2px dashed #007cba;
                    border-radius: 8px;
                    padding: 15px;
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    margin: 10px 0;
                }
                .kata-preview-box h4 {
                    color: #007cba;
                    margin: 0 0 10px 0;
                    font-size: 16px;
                    font-weight: 600;
                }
                .kata-preview-box p {
                    margin: 5px 0;
                    color: #495057;
                    font-size: 14px;
                }
                .kata-preview-box small {
                    display: block;
                    margin-top: 10px;
                    color: #28a745;
                    font-weight: 500;
                }
                .faq-preview {
                    background: white;
                    border-radius: 5px;
                    padding: 10px;
                    font-size: 13px;
                    margin: 10px 0;
                }
                .faq-preview hr {
                    border: none;
                    border-top: 1px solid #dee2e6;
                    margin: 8px 0;
                }
            `);
        });

        // Quick insert buttons (optional)
        editor.addButton('kata_quick_faq', {
            title: 'Quick FAQ',
            icon: 'help',
            onclick: function() {
                editor.insertContent(templates.faq.shortcode);
            }
        });

        // Context menu integration
        editor.on('contextmenu', function(e) {
            // Add KATA SEO options to right-click menu if needed
        });

        // Auto-complete for shortcodes (advanced feature)
        editor.on('keyup', function(e) {
            var content = editor.getContent();
            // Could add auto-suggestions for shortcode parameters
        });
    });

    // Custom CSS for TinyMCE editor
    tinymce.DOM.loadCSS(tinymce.baseURL + '/plugins/kata_seo_manager/editor-styles.css');

})();

// jQuery helper functions for enhanced functionality
jQuery(document).ready(function($) {
    // Add preview functionality
    $('body').on('click', '.kata-preview-shortcode', function(e) {
        e.preventDefault();
        var shortcode = $(this).data('shortcode');
        // AJAX preview functionality could be added here
    });
});

// Notification system
window.KataSEONotifications = {
    show: function(message, type = 'success') {
        var notification = jQuery('<div class="kata-notification kata-notification-' + type + '">' + message + '</div>');
        jQuery('body').append(notification);
        
        setTimeout(function() {
            notification.fadeOut(function() {
                notification.remove();
            });
        }, 3000);
    }
};