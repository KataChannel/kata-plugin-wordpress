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
                    <div class="kata-preview-box" style="
                        background: #f8f9fa;
                        border: 1px solid #e5e7eb;
                        border-radius: 8px;
                        padding: 20px;
                        margin-bottom: 16px;
                    ">
                        <h4 style="margin: 0 0 16px 0; font-size: 16px; color: #374151;">🔍 FAQ Schema sẽ tạo rich snippets:</h4>
                        <div class="faq-preview" style="
                            background: white;
                            padding: 16px;
                            border-radius: 6px;
                            border-left: 4px solid #667eea;
                        ">
                            <p style="margin: 8px 0;"><strong>Q:</strong> KATA SEO Manager có miễn phí không?</p>
                            <p style="margin: 8px 0; color: #6b7280;"><strong>A:</strong> KATA SEO Manager hoàn toàn miễn phí...</p>
                            <hr style="margin: 12px 0; border: none; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 8px 0;"><strong>Q:</strong> Plugin hỗ trợ bao nhiều loại Schema?</p>
                            <p style="margin: 8px 0; color: #6b7280;"><strong>A:</strong> Plugin hỗ trợ 26+ loại Schema markup...</p>
                        </div>
                        <small style="display: block; margin-top: 12px; color: #059669;">✅ JSON-LD Schema tự động được tạo cho Google</small>
                        <hr style="margin:12px 0; border:none; border-top:1px solid #e5e7eb;">
                        <small style="color:#6b7280; line-height: 1.6;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_question="true" trong kata_faq_item
                        </small>
                    </div>
                `
            },

            article: {
                title: '📝 Article Schema',
                shortcode: '[kata_article title="5 Bí Quyết Thành Công Trong Kinh Doanh" author="Nguyễn Văn A" category="Kinh Doanh" tags="thành công, kinh doanh, khởi nghiệp" excerpt="Khám phá 5 bí quyết quan trọng giúp bạn thành công trong lĩnh vực kinh doanh từ những chuyên gia hàng đầu." reading_time="5" word_count="1200" show_schema="true" show_frontend="true" hide_content_title="true" hide_content_author="true" hide_content_category="true" hide_content_tags="true" hide_content_excerpt="true" hide_content_reading_time="true" hide_content_word_count="true" hide_content_date="true" hide_content_image="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📝 Article Schema sẽ tạo:</h4>
                        <div class="article-preview">
                            <h3>5 Bí Quyết Thành Công Trong Kinh Doanh</h3>
                            <p><strong>Tác giả:</strong> Nguyễn Văn A | <strong>Danh mục:</strong> Kinh Doanh</p>
                            <p><strong>Thời gian đọc:</strong> 5 phút | <strong>Số từ:</strong> 1200</p>
                            <p>Khám phá 5 bí quyết quan trọng giúp bạn thành công...</p>
                        </div>
                        <small>✅ JSON-LD ArticleSchema tự động cho Google</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_author="true" show_content_excerpt="false"
                        </small>
                    </div>
                `
            },

            recipe: {
                title: '🍳 Recipe Schema',
                shortcode: '[kata_recipe name="Phở Bò Hà Nội" description="Món phở bò truyền thống của Hà Nội với nước dùng trong vắt, thơm ngon" ingredients="500g xương bò|200g thịt bò|1 củ hành tây|Gia vị phở" instructions="Luộc xương bò 2 tiếng|Thái thịt bò mỏng|Trần bánh phở|Múc nước dùng ra tô" prep_time="30M" cook_time="2H" servings="4" calories="450" show_schema="true" show_content="true" hide_content_name="true" hide_content_description="true" hide_content_image="true" hide_content_ingredients="true" hide_content_instructions="true" hide_content_time="true" hide_content_nutrition="true" hide_content_rating="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🍳 Recipe Schema sẽ tạo:</h4>
                        <div class="recipe-preview">
                            <h3>Phở Bò Hà Nội</h3>
                            <p>⏱️ Chuẩn bị: 30 phút | 🔥 Nấu: 2 giờ | 👥 Khẩu phần: 4</p>
                            <p><strong>Nguyên liệu:</strong> Xương bò, thịt bò, hành tây...</p>
                            <p><strong>Cách làm:</strong> Luộc xương bò 2 tiếng...</p>
                        </div>
                        <small>✅ Rich snippets công thức nấu ăn</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_ingredients="true" show_content_instructions="true"
                        </small>
                    </div>
                `
            },

            product: {
                title: '🛍️ Product Schema',
                shortcode: '[kata_product name="iPhone 15 Pro Max" description="Điện thoại thông minh cao cấp với chip A17 Pro và camera 48MP" price="29990000" currency="VND" brand="Apple" availability="InStock" rating_value="4.8" rating_count="1250" category="Điện thoại" show_schema="true" show_content="true" hide_content_name="true" hide_content_description="true" hide_content_image="true" hide_content_price="true" hide_content_brand="true" hide_content_category="true" hide_content_availability="true" hide_content_rating="true" hide_content_features="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🛍️ Product Schema sẽ tạo:</h4>
                        <div class="product-preview">
                            <h3>iPhone 15 Pro Max</h3>
                            <p><strong>Thương hiệu:</strong> Apple | <strong>Giá:</strong> 29.990.000 VND</p>
                            <p>⭐ 4.8/5 (1,250 đánh giá) | ✅ Còn hàng</p>
                            <p>Điện thoại thông minh cao cấp với chip A17 Pro...</p>
                        </div>
                        <small>✅ Rich snippets sản phẩm với giá và đánh giá</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_price="true" show_content_rating="true"
                        </small>
                    </div>
                `
            },

            event: {
                title: '📅 Event Schema',
                shortcode: '[kata_event name="Hội thảo Digital Marketing 2025" description="Hội thảo về xu hướng Digital Marketing mới nhất năm 2025" start_date="2025-11-15T09:00" end_date="2025-11-15T17:00" location="Khách sạn Lotte, Hà Nội" organizer="Marketing Vietnam" price="500000" currency="VND" show_schema="true" show_content="true" hide_content_name="true" hide_content_description="true" hide_content_date="true" hide_content_time="true" hide_content_location="true" hide_content_organizer="true" hide_content_price="true" hide_content_image="true" hide_content_status="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📅 Event Schema sẽ tạo:</h4>
                        <div class="event-preview">
                            <h3>Hội thảo Digital Marketing 2025</h3>
                            <p>📅 15/11/2025, 9:00 - 17:00</p>
                            <p>📍 Khách sạn Lotte, Hà Nội</p>
                            <p>💰 Vé: 500.000 VND | 👥 BTC: Marketing Vietnam</p>
                        </div>
                        <small>✅ Rich snippets sự kiện với thời gian và địa điểm</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_price="true" show_content_location="true"
                        </small>
                    </div>
                `
            },

            howto: {
                title: '📋 HowTo Schema',
                shortcode: '[kata_howto name="Cách Tạo Website WordPress" description="Hướng dẫn chi tiết cách tạo website WordPress từ A đến Z" steps="Mua hosting và domain|Cài đặt WordPress|Chọn theme phù hợp|Tùy chỉnh giao diện|Thêm nội dung" tools="Hosting|Domain|WordPress theme" prep_time="1H" perform_time="3H" difficulty="Trung bình" show_schema="true" show_frontend="true" hide_content_name="true" hide_content_description="true" hide_content_image="true" hide_content_steps="true" hide_content_tools="true" hide_content_time="true" hide_content_difficulty="true" hide_content_cost="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📋 HowTo Schema sẽ tạo:</h4>
                        <div class="howto-preview">
                            <h3>Cách Tạo Website WordPress</h3>
                            <p>⏱️ Chuẩn bị: 1 giờ | 🔧 Thực hiện: 3 giờ | 📊 Độ khó: Trung bình</p>
                            <p><strong>Bước 1:</strong> Mua hosting và domain</p>
                            <p><strong>Bước 2:</strong> Cài đặt WordPress...</p>
                        </div>
                        <small>✅ Rich snippets hướng dẫn từng bước</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_tools="true" show_content_steps="true"
                        </small>
                    </div>
                `
            },

            // review: {
            //     title: '⭐ Review Schema',
            //     shortcode: '[kata_review name="Đánh giá MacBook Pro M3" item_name="MacBook Pro 14 inch M3" item_type="Product" rating_value="4.5" review_body="Laptop có hiệu suất mạnh mẽ, thiết kế đẹp và thời lượng pin ấn tượng. Rất phù hợp cho công việc đồ họa và lập trình." author="Tech Reviewer"]',
            //     preview: `
            //         <div class="kata-preview-box">
            //             <h4>⭐ Review Schema sẽ tạo:</h4>
            //             <div class="review-preview">
            //                 <h3>Đánh giá MacBook Pro M3</h3>
            //                 <p>⭐⭐⭐⭐⭐ 4.5/5 bởi Tech Reviewer</p>
            //                 <p><strong>Sản phẩm:</strong> MacBook Pro 14 inch M3</p>
            //                 <p>Laptop có hiệu suất mạnh mẽ, thiết kế đẹp...</p>
            //             </div>
            //             <small>✅ Rich snippets đánh giá với rating</small>
            //         </div>
            //     `
            // },

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
                title: '📊 Bình Chọn Database',
                shortcode: `[kata_poll id="1"]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>📊 Poll Database: "Tính năng yêu thích nhất?"</h4>
                        <p>�️ Bình chọn một lần mỗi IP/user</p>
                        <p>� Hiển thị kết quả real-time với thanh tiến trình</p>
                        <p>� Lưu trữ trong database với analytics</p>
                        <p>🎨 Giao diện đẹp và responsive</p>
                        <small>✅ Sử dụng poll có sẵn trong database (ID=1 hoặc ID=2)</small>
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

            user_interaction: {
                title: '💬 User Interaction (Reviews & Ratings)',
                shortcode: `[kata_user_interaction type="all" show_form="true" show_list="true" show_ratings="true" style="card" title="Đánh giá & Bình luận" items_per_page="5"]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>💬 User Interaction: "Đánh giá & Bình luận"</h4>
                        <p>⭐ Hệ thống rating đa tiêu chí (1-5 sao)</p>
                        <p>📝 Form đánh giá với title, content, pros/cons</p>
                        <p>💬 Comments và reply system</p>
                        <p>👨‍💼 Admin moderation & approval workflow</p>
                        <p>📊 Statistics dashboard & analytics</p>
                        <p>🎨 3 style options: default, card, minimal</p>
                        <p>📱 Responsive design với AJAX loading</p>
                        <small>✅ Thu thập feedback từ users với database storage</small>
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

            // rating: {
            //     title: '⭐ Đánh Giá',
            //     shortcode: `[kata_rating item_name="KATA SEO Manager Plugin" rating="4.9" review_count="247" review_text="Plugin SEO tuyệt vời! Hỗ trợ 26 loại Schema, dễ sử dụng, tích hợp mượt mà với WordPress. Highly recommended!"]`,
            //     preview: `
            //         <div class="kata-preview-box">
            //             <h4>⭐ Rating: "KATA SEO Manager Plugin"</h4>
            //             <p>🌟 4.9/5 sao (247 đánh giá)</p>
            //             <p>📝 Review: "Plugin SEO tuyệt vời!..."</p>
            //             <p>🔍 Rich snippets trên Google</p>
            //             <small>✅ Tăng CTR và trust của người dùng</small>
            //         </div>
            //     `
            // },

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
            },

            movie: {
                title: '🎬 Movie Schema',
                shortcode: '[kata_movie name="Avengers: Endgame" description="Bộ phim siêu anh hùng kết thúc Infinity Saga của Marvel" director="Anthony Russo, Joe Russo" actor="Robert Downey Jr., Chris Evans, Scarlett Johansson" genre="Hành động, Phiêu lưu, Khoa học viễn tưởng" duration="3H1M" release_date="2019-04-26" rating_value="4.8"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🎬 Movie Schema sẽ tạo:</h4>
                        <div class="movie-preview">
                            <h3>Avengers: Endgame</h3>
                            <p><strong>Đạo diễn:</strong> Anthony Russo, Joe Russo</p>
                            <p><strong>Diễn viên:</strong> Robert Downey Jr., Chris Evans...</p>
                            <p>⏱️ Thời lượng: 3h1m | ⭐ 4.8/5</p>
                            <p>Bộ phim siêu anh hùng kết thúc Infinity Saga...</p>
                        </div>
                        <small>✅ Rich snippets phim với cast và rating</small>
                    </div>
                `
            },

            course: {
                title: '📚 Course Schema',
                shortcode: '[kata_course name="Lập trình Python cơ bản" description="Khóa học Python từ cơ bản đến nâng cao dành cho người mới bắt đầu" provider="CodeGym Vietnam" instructor="Nguyễn Văn B" price="2500000" duration="40H" level="beginner" skills="Python cơ bản|Lập trình hướng đối tượng|Web scraping" show_schema="true" show_frontend="true" show_content_name="true" show_content_description="true" show_content_provider="true" show_content_instructor="true" show_content_price="true" show_content_duration="true" show_content_level="true" show_content_skills="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📚 Course Schema sẽ tạo:</h4>
                        <div class="course-preview">
                            <h3>Lập trình Python cơ bản</h3>
                            <p><strong>Nhà cung cấp:</strong> CodeGym Vietnam</p>
                            <p><strong>Giảng viên:</strong> Nguyễn Văn B | 💰 2.500.000 VND</p>
                            <p>⏱️ 40 giờ | 🟢 Cơ bản</p>
                            <p><strong>Kỹ năng:</strong> Python cơ bản, OOP, Web scraping</p>
                        </div>
                        <small>✅ Rich snippets khóa học với giá và kỹ năng</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_instructor="true" show_content_skills="true"
                        </small>
                    </div>
                `
            },

            software: {
                title: '💻 Software Schema',
                shortcode: '[kata_software name="Adobe Photoshop 2025" description="Phần mềm chỉnh sửa ảnh chuyên nghiệp hàng đầu thế giới" version="2025.1.0" operating_system="Windows 10, macOS 12" application_category="Graphics Software" price="600000" file_size="2.1 GB"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>💻 Software Schema sẽ tạo:</h4>
                        <div class="software-preview">
                            <h3>Adobe Photoshop 2025</h3>
                            <p><strong>Version:</strong> 2025.1.0 | <strong>Dung lượng:</strong> 2.1 GB</p>
                            <p><strong>Hệ điều hành:</strong> Windows 10, macOS 12</p>
                            <p>💰 600.000 VND | 🎨 Graphics Software</p>
                            <p>Phần mềm chỉnh sửa ảnh chuyên nghiệp...</p>
                        </div>
                        <small>✅ Rich snippets phần mềm với yêu cầu hệ thống</small>
                    </div>
                `
            },

            book: {
                title: '📖 Book Schema',
                shortcode: '[kata_book name="Đắc Nhân Tâm" author="Dale Carnegie" description="Cuốn sách kinh điển về nghệ thuật giao tiếp và ứng xử" publisher="Nhà xuất bản Tổng hợp TP.HCM" publication_date="2018-01-15" pages="320" genre="Kỹ năng sống, Tâm lý học" isbn="978-604-2-15234-7" show_schema="true" show_frontend="true" show_content_name="true" show_content_author="true" show_content_description="true" show_content_publisher="true" show_content_date="true" show_content_pages="true" show_content_genre="true" show_content_isbn="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📖 Book Schema sẽ tạo:</h4>
                        <div class="book-preview">
                            <h3>Đắc Nhân Tâm</h3>
                            <p><strong>Tác giả:</strong> Dale Carnegie</p>
                            <p><strong>NXB:</strong> Tổng hợp TP.HCM | 📅 15/01/2018</p>
                            <p>📄 320 trang | 📚 Kỹ năng sống, Tâm lý học</p>
                            <p>Cuốn sách kinh điển về nghệ thuật giao tiếp...</p>
                        </div>
                        <small>✅ Rich snippets sách với ISBN và tác giả</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_isbn="true" show_content_publisher="true"
                        </small>
                    </div>
                `
            },

            webpage: {
                title: '🌐 WebPage Schema',
                shortcode: '[kata_webpage name="Trang chủ KATA SEO" description="Trang chủ của plugin KATA SEO Manager - công cụ SEO WordPress tốt nhất" keywords="SEO, WordPress, plugin, KATA" breadcrumb="Trang chủ > Plugin > KATA SEO"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🌐 WebPage Schema sẽ tạo:</h4>
                        <div class="webpage-preview">
                            <h3>Trang chủ KATA SEO</h3>
                            <p><strong>Breadcrumb:</strong> Trang chủ > Plugin > KATA SEO</p>
                            <p><strong>Keywords:</strong> SEO, WordPress, plugin, KATA</p>
                            <p>Trang chủ của plugin KATA SEO Manager...</p>
                        </div>
                        <small>✅ WebPage schema với breadcrumb navigation</small>
                    </div>
                `
            },

            // === NEW SCHEMA TYPES (October 2025 Update) ===
            
            carousel: {
                title: '🎠 Carousel Gallery',
                shortcode: '[kata_carousel title="Portfolio Showcase 2025" images="https://example.com/project1.jpg,https://example.com/project2.jpg,https://example.com/project3.jpg,https://example.com/project4.jpg" captions="Website E-commerce,Mobile App Design,Brand Identity,Social Media Campaign" links="https://kata.com/project1,https://kata.com/project2,https://kata.com/project3,https://kata.com/project4" height="450px" auto_play="true" show_indicators="true" show_controls="true" transition_speed="4000"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🎠 Carousel Gallery sẽ tạo:</h4>
                        <div class="carousel-preview">
                            <h3>Portfolio Showcase 2025</h3>
                            <p>🖼️ <strong>4 slides:</strong> Website E-commerce, Mobile App Design, Brand Identity, Social Media Campaign</p>
                            <p>⚡ Auto-play: 4s | 🎯 Navigation: Arrows + Indicators</p>
                            <p>📱 Responsive design với JavaScript smooth transitions</p>
                            <p>🔗 Clickable links cho từng project</p>
                        </div>
                        <small>✅ JSON-LD ItemList schema + Interactive gallery</small>
                    </div>
                `
            },

            dataset: {
                title: '📊 Dataset Display',
                shortcode: '[kata_dataset title="Báo Cáo Doanh Thu Q3 2025" description="Phân tích chi tiết doanh thu theo sản phẩm và khu vực trong quý 3 năm 2025" headers="Sản Phẩm|Doanh Thu (VND)|Tăng Trưởng|Khu Vực" data="WordPress Plugin|150,000,000|+25%|Việt Nam,SEO Service|320,000,000|+15%|Đông Nam Á,Web Design|180,000,000|+8%|Toàn Cầu,Training Course|95,000,000|+35%|Việt Nam" creator="KATA Analytics Team" date_published="2025-10-06" license="MIT License" keywords="doanh thu,báo cáo,Q3 2025,phân tích" format="table" show_search="true" show_export="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📊 Dataset Display sẽ tạo:</h4>
                        <div class="dataset-preview">
                            <h3>Báo Cáo Doanh Thu Q3 2025</h3>
                            <p>📋 <strong>Định dạng:</strong> Bảng 4 cột × 4 dòng dữ liệu</p>
                            <p>🔍 <strong>Tính năng:</strong> Real-time search + CSV export</p>
                            <p>👤 <strong>Tác giả:</strong> KATA Analytics Team | 📅 06/10/2025</p>
                            <p>📈 <strong>Nội dung:</strong> WordPress Plugin (+25%), SEO Service (+15%)...</p>
                        </div>
                        <small>✅ Dataset schema + Interactive table với tìm kiếm</small>
                    </div>
                `
            },

            forum: {
                title: '💬 Forum Discussion',
                shortcode: '[kata_forum title="WordPress Developers Vietnam" description="Cộng đồng nhà phát triển WordPress tại Việt Nam - chia sẻ kiến thức, hỗ trợ kỹ thuật" topics="Cách tối ưu WP_Query cho hiệu suất|Minh Developer|42|2 giờ trước,Plugin security best practices 2025|Sarah Nguyen|28|5 giờ trước,Gutenberg custom blocks tutorial|Tech Master|67|1 ngày trước,WooCommerce hooks và filters|E-com Expert|35|3 giờ trước" moderator="KATA Team" category="Web Development" member_count="3,450" post_count="12,890" created_date="2020-03-15" show_stats="true" show_recent="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>💬 Forum Discussion sẽ tạo:</h4>
                        <div class="forum-preview">
                            <h3>WordPress Developers Vietnam</h3>
                            <p>👥 <strong>Cộng đồng:</strong> 3,450 thành viên | 📝 12,890 bài viết</p>
                            <p>🔥 <strong>Topic hot:</strong> WP_Query optimization (42 replies)</p>
                            <p>👨‍💼 <strong>Điều hành:</strong> KATA Team | 🏷️ Web Development</p>
                            <p>📊 <strong>Hoạt động:</strong> 4 chủ đề mới trong 24h</p>
                        </div>
                        <small>✅ DiscussionForumPosting schema + Community stats</small>
                    </div>
                `
            },

            eduqa: {
                title: '🎓 Educational Q&A',
                shortcode: '[kata_eduqa question="Làm thế nào để tối ưu hóa Core Web Vitals cho WordPress?" answer="Để tối ưu Core Web Vitals: 1) Sử dụng caching plugin (WP Rocket, W3 Total Cache), 2) Tối ưu hình ảnh (WebP format, lazy loading), 3) Minify CSS/JS, 4) Sử dụng CDN, 5) Chọn hosting tốt, 6) Tối ưu database, 7) Sử dụng AMP cho mobile" category="WordPress Performance" difficulty="intermediate" author="SEO Expert Pro" votes="89" date_asked="2025-10-01" date_answered="2025-10-02" tags="core web vitals,performance,wordpress,seo,optimization" related_questions="Cách tăng tốc WordPress 2025,Best caching plugins,Tối ưu hình ảnh WordPress" show_voting="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🎓 Educational Q&A sẽ tạo:</h4>
                        <div class="eduqa-preview">
                            <h3>❓ Tối ưu Core Web Vitals cho WordPress?</h3>
                            <p>👨‍🏫 <strong>Expert:</strong> SEO Expert Pro | 🟡 Trung bình</p>
                            <p>👍 <strong>Votes:</strong> 89 upvotes | 📅 01-02/10/2025</p>
                            <p>✅ <strong>Giải pháp:</strong> 7 bước tối ưu từ caching đến CDN</p>
                            <p>🏷️ <strong>Tags:</strong> core web vitals, performance, wordpress...</p>
                        </div>
                        <small>✅ Question schema + Interactive voting system</small>
                    </div>
                `
            },

            employer_rating: {
                title: '⭐ Employer Rating',
                shortcode: '[kata_employer_rating company_name="KATA Digital Agency" overall_rating="4.6" work_life_balance="4.8" salary_benefits="4.2" career_opportunities="4.5" management="4.3" culture="4.7" total_reviews="186" recommend_percentage="92" recent_reviews="Môi trường làm việc tuyệt vời, đồng nghiệp hỗ trợ nhiệt tình|Nhân viên hiện tại|5,Dự án thú vị, học hỏi được nhiều điều mới|Developer Senior|4,Lương competitive, benefit tốt trong ngành|Marketing Executive|4,Leadership team có vision rõ ràng|Product Manager|5" company_size="50-100" industry="Digital Marketing & Web Development" show_breakdown="true" show_reviews="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>⭐ Employer Rating sẽ tạo:</h4>
                        <div class="employer-preview">
                            <h3>KATA Digital Agency</h3>
                            <p>⭐ <strong>4.6/5</strong> (186 đánh giá) | 👍 92% khuyến nghị</p>
                            <p>🏢 <strong>Quy mô:</strong> 50-100 nhân viên | 💼 Digital Marketing</p>
                            <p>📊 <strong>Chi tiết:</strong> Work-life (4.8), Culture (4.7), Salary (4.2)</p>
                            <p>💬 <strong>Reviews:</strong> "Môi trường tuyệt vời", "Dự án thú vị"...</p>
                        </div>
                        <small>✅ Organization schema + Rating breakdown charts</small>
                    </div>
                `
            },

            profile_page: {
                title: '👤 Profile Page',
                shortcode: '[kata_profile_page name="Nguyễn Minh Tuấn" job_title="Senior Full-Stack Developer & SEO Specialist" bio="Full-stack developer với 8+ năm kinh nghiệm phát triển web, chuyên sâu WordPress, React và Node.js. Founder của KATA SEO Tools và diễn giả tại nhiều sự kiện công nghệ." skills="WordPress Development,React.js,Node.js,PHP,JavaScript,SEO Optimization,Database Design,DevOps,UI/UX Design" experience="Senior Developer tại KATA Digital (2022-hiện tại)|Full-Stack Developer tại TechViet Solutions (2019-2022)|WordPress Developer tại Web Studio Pro (2017-2019)|Junior Developer tại StartupXYZ (2015-2017)" education="Thạc sĩ Công nghệ Thông tin - Đại học Bách Khoa TP.HCM (2015)|Cử nhân Khoa học Máy tính - Đại học Công nghệ (2013)" contact_email="tuannm@katadigital.com" contact_phone="+84 901 234 567" website="https://tuannguyen.dev" social_links="LinkedIn|https://linkedin.com/in/tuannguyen-dev,GitHub|https://github.com/tuannm-kata,Twitter|https://twitter.com/tuannm_dev" location="TP. Hồ Chí Minh, Việt Nam" languages="Tiếng Việt (Bản ngữ),English (Fluent),日本語 (Intermediate)" achievements="AWS Certified Solutions Architect|Published 25+ WordPress plugins on wp.org|Speaker tại WordCamp Vietnam 2024|Tech lead cho 15+ dự án enterprise|Contributor cho WordPress Core" show_contact="true" show_social="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>👤 Profile Page sẽ tạo:</h4>
                        <div class="profile-preview">
                            <h3>Nguyễn Minh Tuấn</h3>
                            <p>💼 <strong>Senior Full-Stack Developer & SEO Specialist</strong></p>
                            <p>🏆 <strong>Kinh nghiệm:</strong> 8+ năm | 📍 TP.HCM</p>
                            <p>🛠️ <strong>Skills:</strong> WordPress, React, Node.js, SEO...</p>
                            <p>🎯 <strong>Achievements:</strong> AWS Certified, 25+ plugins, Speaker WordCamp</p>
                            <p>📞 <strong>Contact:</strong> Email, Phone, Website, Social links</p>
                        </div>
                        <small>✅ Person schema + Complete professional profile</small>
                    </div>
                `
            },

            // === ENHANCED EXISTING SCHEMAS ===

            local_business: {
                title: '🏢 Local Business with Departments',
                shortcode: `[kata_local_business 
    name="KATA Digital Marketing Agency - Văn phòng Chính" 
    address="123 Nguyễn Văn Cừ, Quận 1, TP.HCM" 
    phone="+84 28 1234 5678" 
    email="hello@katadigital.com" 
    website="https://katadigital.com" 
    hours="Thứ 2-6: 9:00-18:00, Thứ 7: 9:00-12:00" 
    description="Agency chuyên cung cấp dịch vụ Digital Marketing và phát triển Website chuyên nghiệp tại TP.HCM với 2 chi nhánh" 
    services="SEO Optimization,Google Ads,Social Media Marketing,Web Development" 
    rating="4.8" 
    review_count="127" 
    price_range="$$"
    latitude="10.7769"
    longitude="106.7009"
    department_1_name="KATA Digital - Chi nhánh Quận 3"
    department_1_address="456 Võ Văn Tần, Quận 3, TP.HCM"
    department_1_phone="+84 28 9876 5432"
    department_1_latitude="10.7821"
    department_1_longitude="106.6919"
    department_2_name="KATA Digital - Chi nhánh Thủ Đức"
    department_2_address="789 Võ Văn Ngân, Thủ Đức, TP.HCM"
    department_2_phone="+84 28 5555 6666"
    department_2_latitude="10.8509"
    department_2_longitude="106.7718"
    show_schema="true" 
    show_frontend="true" 
    show_content_name="true" 
    show_content_address="true" 
    show_content_contact="true" 
    show_content_hours="true" 
    show_content_description="true" 
    show_content_services="true" 
    show_content_rating="true"
    show_content_departments="true"]`,
                preview: `
                    <div class="kata-preview-box">
                        <h4>🏢 LocalBusiness với Department Array sẽ tạo:</h4>
                        <div class="business-preview">
                            <h3>KATA Digital Marketing Agency - Văn phòng Chính</h3>
                            <p>📍 123 Nguyễn Văn Cừ, Q1, TP.HCM | ☎️ +84 28 1234 5678</p>
                            <p>⭐ 4.8/5 (127 reviews) | 💰 $$</p>
                            <p>🕒 T2-6: 9:00-18:00, T7: 9:00-12:00</p>
                            <p>🎯 SEO, Google Ads, Social Media, Web Dev</p>
                            <hr style="margin: 8px 0; border-top: 1px dashed #ddd;">
                            <p><strong>🏪 Chi nhánh:</strong></p>
                            <p style="margin-left: 15px;">• Chi nhánh Q3: 456 Võ Văn Tần</p>
                            <p style="margin-left: 15px;">• Chi nhánh Thủ Đức: 789 Võ Văn Ngân</p>
                        </div>
                        <small>✅ LocalBusiness schema với department array + Geo coordinates</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>Schema Structure:</strong><br>
                            <code style="background:#f5f5f5; padding:2px 4px; border-radius:3px;">
                            {<br>
                            &nbsp;&nbsp;"@type": "LocalBusiness",<br>
                            &nbsp;&nbsp;"name": "Văn phòng Chính",<br>
                            &nbsp;&nbsp;"department": [<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;{ "@type": "LocalBusiness", "name": "Chi nhánh 1" },<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;{ "@type": "LocalBusiness", "name": "Chi nhánh 2" }<br>
                            &nbsp;&nbsp;]<br>
                            }
                            </code>
                        </small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> show_content_departments="true" hide_content_hours="true"
                        </small>
                    </div>
                `
            },

            job_posting: {
                title: '💼 Job Posting (Enhanced)',
                shortcode: '[kata_job_posting title="Senior WordPress Developer" company="KATA Digital Agency" location="TP. Hồ Chí Minh, Việt Nam" description="Tìm kiếm Senior WordPress Developer có kinh nghiệm 3+ năm để join team phát triển các dự án enterprise scale. Candidate sẽ làm việc với latest technologies và có cơ hội growth lên Tech Lead." salary="25-40 triệu VND/tháng" employment_type="FULL_TIME" date_posted="2025-10-06" requirements="3+ năm experience WordPress|Thành thạo PHP, JavaScript, MySQL|Kinh nghiệm với WooCommerce, Custom Post Types|Biết Git, Docker là plus" benefits="13th month salary|Premium health insurance|Flexible working time|Learning budget 10 triệu/năm|Team building quarterly|MacBook Pro provided" show_schema="true" show_frontend="true" show_content_title="true" show_content_company="true" show_content_location="true" show_content_description="true" show_content_salary="true" show_content_requirements="true" show_content_benefits="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>💼 Enhanced Job Posting sẽ tạo:</h4>
                        <div class="job-preview">
                            <h3>Senior WordPress Developer</h3>
                            <p>🏢 KATA Digital Agency | 📍 TP.HCM</p>
                            <p>💰 25-40 triệu VND/tháng | ⏰ Full-time</p>
                            <p>📋 Requirements: 3+ năm WordPress, PHP, JavaScript...</p>
                            <p>🎁 Benefits: 13th salary, Insurance, Flexible time...</p>
                        </div>
                        <small>✅ JobPosting schema + Professional job display</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_salary="true" show_content_requirements="true"
                        </small>
                    </div>
                `
            },

            image_metadata: {
                title: '🖼️ Image Metadata',
                shortcode: '[kata_image_metadata url="https://katadigital.com/images/seo-infographic-2025.jpg" name="SEO Best Practices Infographic 2025" description="Infographic tổng hợp các best practices SEO mới nhất năm 2025" width="1200" height="1600" encoding_format="JPEG" size="2.3 MB" creator="KATA Design Team" date_created="2025-10-06" keywords="SEO,infographic,best practices,2025,digital marketing" location="KATA Studio, TP.HCM" camera_model="Canon EOS R5" show_schema="true" show_frontend="true" show_content_preview="true" show_content_name="true" show_content_description="true" show_content_technical="true" show_content_dimensions="true" show_content_size="true" show_content_format="true" show_content_camera="true" show_content_creator="true" show_content_date="true" show_content_location="true" show_content_keywords="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🖼️ Image Metadata sẽ tạo:</h4>
                        <div class="image-preview">
                            <h3>SEO Best Practices Infographic 2025</h3>
                            <p>📐 <strong>Size:</strong> 1200×1600px (2.3 MB) | 📷 Canon EOS R5</p>
                            <p>👨‍🎨 <strong>Creator:</strong> KATA Design Team | 📅 06/10/2025</p>
                            <p>📍 <strong>Location:</strong> KATA Studio, TP.HCM</p>
                            <p>🏷️ <strong>Tags:</strong> SEO, infographic, best practices...</p>
                        </div>
                        <small>✅ ImageObject schema + Technical metadata display</small>
                        <hr style="margin:10px 0; border:none; border-top:1px solid #ddd;">
                        <small style="color:#666;">
                            <strong>MODE 1 (Schema):</strong> schema_fields, hide_*, show_*<br>
                            <strong>MODE 2 (Content):</strong> hide_content_*, show_content_*<br>
                            <strong>Ví dụ:</strong> hide_content_camera="true" hide_keywords="true"
                        </small>
                    </div>
                `
            },

            math_solver: {
                title: '🧮 Math Solver',
                shortcode: '[kata_math_solver problem="Giải phương trình bậc hai: 2x² + 5x - 3 = 0" solution="x₁ = 0.5, x₂ = -3" steps="Áp dụng công thức nghiệm: x = [-b ± √(b²-4ac)] / 2a|Thay a=2, b=5, c=-3: x = [-5 ± √(25+24)] / 4|Tính: x = [-5 ± √49] / 4 = [-5 ± 7] / 4|Nghiệm: x₁ = 2/4 = 0.5 và x₂ = -12/4 = -3" category="Đại số" difficulty="intermediate" explanation="Phương trình bậc hai có 2 nghiệm phân biệt vì Δ = b² - 4ac = 49 > 0" formula="x = [-b ± √(b²-4ac)] / 2a"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🧮 Math Solver sẽ tạo:</h4>
                        <div class="math-preview">
                            <h3>📝 Giải phương trình bậc hai: 2x² + 5x - 3 = 0</h3>
                            <p>🎯 <strong>Chủ đề:</strong> Đại số | 🟡 Trung bình</p>
                            <p>🧮 <strong>Công thức:</strong> x = [-b ± √(b²-4ac)] / 2a</p>
                            <p>✅ <strong>Kết quả:</strong> x₁ = 0.5, x₂ = -3</p>
                            <p>📋 <strong>4 bước giải:</strong> Từ công thức đến kết quả</p>
                        </div>
                        <small>✅ LearningResource schema + Step-by-step solution</small>
                    </div>
                `
            },

            practice_problem: {
                title: '📚 Practice Problem',
                shortcode: '[kata_practice_problem title="Kiểm Tra Kiến Thức WordPress Security" question="Phương pháp nào KHÔNG phải là best practice cho WordPress security?" options="Sử dụng strong passwords|Cập nhật WordPress thường xuyên|Disable WordPress admin|Install security plugins" correct_answer="C" explanation="Disable WordPress admin là không cần thiết và có thể gây khó khăn trong việc quản lý website. Các phương pháp khác đều là best practices quan trọng." category="WordPress Security" difficulty="intermediate" points="15" time_limit="2" hints="Nghĩ về tính thực tế và khả năng quản lý website"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>📚 Practice Problem sẽ tạo:</h4>
                        <div class="practice-preview">
                            <h3>Kiểm Tra Kiến Thức WordPress Security</h3>
                            <p>🎯 <strong>Câu hỏi:</strong> Phương pháp nào KHÔNG phải là best practice?</p>
                            <p>🟡 <strong>Độ khó:</strong> Trung bình | ⭐ 15 điểm | ⏱️ 2 phút</p>
                            <p>📝 <strong>4 lựa chọn:</strong> A, B, C, D với interactive selection</p>
                            <p>💡 <strong>Hints:</strong> Available | ✅ Instant feedback</p>
                        </div>
                        <small>✅ LearningResource schema + Interactive quiz</small>
                    </div>
                `
            },

            sitelinks: {
                title: '🔗 Site Links',
                shortcode: '[kata_sitelinks title="KATA SEO Tools - Site Navigation" description="Điều hướng nhanh đến các trang quan trọng của KATA SEO Tools" links="Trang chủ|https://katadigital.com|Trang chủ chính của website,Hướng dẫn|https://katadigital.com/guide|Hướng dẫn sử dụng chi tiết,Tải plugin|https://katadigital.com/download|Download plugin miễn phí,Hỗ trợ|https://katadigital.com/support|Trung tâm hỗ trợ 24/7,Blog|https://katadigital.com/blog|Bài viết về SEO và WordPress,Liên hệ|https://katadigital.com/contact|Thông tin liên hệ" site_name="KATA SEO Tools" breadcrumb="Home > WordPress > SEO Tools > KATA" style="grid" show_descriptions="true"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🔗 Site Links sẽ tạo:</h4>
                        <div class="sitelinks-preview">
                            <h3>KATA SEO Tools - Site Navigation</h3>
                            <p>🔗 <strong>6 liên kết chính:</strong> Trang chủ, Hướng dẫn, Tải plugin, Hỗ trợ, Blog, Liên hệ</p>
                            <p>🍞 <strong>Breadcrumb:</strong> Home > WordPress > SEO Tools > KATA</p>
                            <p>🎨 <strong>Style:</strong> Grid layout với descriptions</p>
                            <p>🏢 <strong>Site:</strong> KATA SEO Tools</p>
                        </div>
                        <small>✅ SiteNavigationElement schema + Visual navigation</small>
                    </div>
                `
            },

            speakable: {
                title: '🔊 Speakable Content',
                shortcode: '[kata_speakable title="5 Tips SEO Cho Voice Search 2025" content="Voice search đang ngày càng phổ biến. Để tối ưu cho voice search: 1) Tập trung vào long-tail keywords, 2) Tối ưu cho câu hỏi tự nhiên, 3) Cải thiện tốc độ trang, 4) Sử dụng structured data, 5) Tối ưu cho local SEO. Đây là xu hướng quan trọng mà mọi website cần chú ý." summary="5 chiến lược SEO quan trọng để tối ưu website cho voice search trong năm 2025" reading_time="3" language="vi" voice_type="text-to-speech" css_selector=".voice-content,.main-content" xpath="//article//p"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>🔊 Speakable Content sẽ tạo:</h4>
                        <div class="speakable-preview">
                            <h3>5 Tips SEO Cho Voice Search 2025</h3>
                            <p>🗣️ <strong>Content:</strong> Tối ưu cho voice search với 5 tips</p>
                            <p>⏱️ <strong>Reading time:</strong> 3 phút | 🇻🇳 Tiếng Việt</p>
                            <p>🤖 <strong>Voice type:</strong> Text-to-speech</p>
                            <p>🎯 <strong>Selectors:</strong> CSS + XPath cho voice assistants</p>
                        </div>
                        <small>✅ SpeakableSpecification schema + Voice optimization</small>
                    </div>
                `
            }
        };

        // Helper function để tính toán modal size responsive
        function getModalSize(baseWidth, baseHeight) {
            const screenWidth = window.innerWidth || document.documentElement.clientWidth || 800;
            const screenHeight = window.innerHeight || document.documentElement.clientHeight || 600;
            
            const width = Math.min(baseWidth, screenWidth - 40);
            const height = Math.min(baseHeight, screenHeight - 40);
            
            // Ensure minimum size
            return {
                width: Math.max(width, 400),
                height: Math.max(height, 300)
            };
        }

        // Tạo modal selection cho schema templates
        function openSchemaSelector() {
            const schemaOptions = Object.keys(templates).map(key => `
                <div class="kata-schema-option" data-key="${key}" style="
                    border: 1px solid #ddd; 
                    border-radius: 8px; 
                    padding: 16px; 
                    margin: 10px 0; 
                    cursor: pointer; 
                    transition: all 0.3s ease;
                    background: #ffffff;
                    position: relative;
                    overflow: hidden;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                " onmouseover="this.style.borderColor='#0073aa'; this.style.background='#f0f8ff'; this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 8px rgba(0,115,170,0.15)';" 
                   onmouseout="this.style.borderColor='#ddd'; this.style.background='#ffffff'; this.style.transform='translateX(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)';">
                    <h4 style="margin: 0 0 8px 0; color: #23282d; font-size: 15px; font-weight: 600;">${templates[key].title}</h4>
                    <p style="margin: 0; font-size: 12px; color: #666; line-height: 1.4; word-break: break-word;">
                        ${templates[key].shortcode.length > 100 ? templates[key].shortcode.substring(0, 100) + '...' : templates[key].shortcode}
                    </p>
                </div>
            `).join('');

            editor.windowManager.open({
                title: '🏷️ KATA SEO Manager - Chọn Schema Template',
                width: window.innerWidth,
                height: window.innerHeight,
                resizable: false,
                maximizable: false,
                inline: false,
                body: [
                    {
                        type: 'container',
                        html: `
                            <div style="
                                position: fixed;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background: #f5f7fa;
                                display: flex;
                                flex-direction: column;
                                font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;
                                overflow: hidden;
                            ">
                                <!-- Header -->
                                <div style="
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    color: white;
                                    padding: 24px 32px;
                                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                                    flex-shrink: 0;
                                ">
                                    <h2 style="margin: 0 0 8px 0; font-size: 24px; font-weight: 600;">KATA SEO Manager</h2>
                                    <p style="margin: 0; opacity: 0.95; font-size: 14px;">Chọn schema template để chèn vào nội dung - 26+ loại schema hỗ trợ SEO</p>
                                </div>
                                
                                <!-- Main Content Area -->
                                <div style="
                                    flex: 1;
                                    display: flex;
                                    overflow: hidden;
                                    padding: 24px 32px;
                                    gap: 24px;
                                ">
                                    <!-- Left Panel - Schema List -->
                                    <div style="
                                        flex: 0 0 420px;
                                        display: flex;
                                        flex-direction: column;
                                        background: white;
                                        border-radius: 12px;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                                        overflow: hidden;
                                    ">
                                        <!-- Search Box -->
                                        <div style="padding: 20px; border-bottom: 1px solid #e5e7eb; flex-shrink: 0;">
                                            <input 
                                                type="text" 
                                                id="kata-schema-search" 
                                                placeholder="🔍 Tìm kiếm schema (FAQ, Article, Product, LocalBusiness...)" 
                                                style="
                                                    width: 100%;
                                                    padding: 12px 16px;
                                                    border: 2px solid #e5e7eb;
                                                    border-radius: 8px;
                                                    font-size: 14px;
                                                    box-sizing: border-box;
                                                    transition: all 0.3s;
                                                    outline: none;
                                                "
                                                onfocus="this.style.borderColor='#667eea'; this.style.boxShadow='0 0 0 3px rgba(102,126,234,0.1)'"
                                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                                            />
                                            <p style="margin: 8px 0 0 0; font-size: 12px; color: #6b7280;">
                                                💡 Gõ để lọc nhanh - Hỗ trợ 26+ schema types
                                            </p>
                                        </div>
                                        
                                        <!-- Schema List -->
                                        <div id="kata-schema-list" style="
                                            flex: 1;
                                            overflow-y: auto;
                                            padding: 16px;
                                        ">
                                            ${schemaOptions}
                                        </div>
                                        
                                        <!-- Footer Note -->
                                        <div style="
                                            padding: 16px 20px;
                                            background: #fffbeb;
                                            border-top: 1px solid #fef3c7;
                                            flex-shrink: 0;
                                        ">
                                            <p style="margin: 0; font-size: 12px; color: #92400e; line-height: 1.5;">
                                                💡 <strong>Tip:</strong> Click vào schema để xem preview và tùy chọn content
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Right Panel - Preview & Info -->
                                    <div style="
                                        flex: 1;
                                        display: flex;
                                        flex-direction: column;
                                        background: white;
                                        border-radius: 12px;
                                        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                                        overflow: hidden;
                                    ">
                                        <div id="kata-preview-panel" style="
                                            flex: 1;
                                            padding: 32px;
                                            overflow-y: auto;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            text-align: center;
                                            color: #9ca3af;
                                        ">
                                            <div>
                                                <div style="font-size: 64px; margin-bottom: 16px;">📋</div>
                                                <h3 style="margin: 0 0 8px 0; font-size: 18px; color: #6b7280;">Chọn một schema bên trái</h3>
                                                <p style="margin: 0; font-size: 14px;">Preview và tùy chọn sẽ hiển thị ở đây</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `
                    }
                ],
                buttons: [
                    {
                        text: '✖ Đóng',
                        onclick: 'close'
                    }
                ],
                onpostrender: function() {
                    // Add click handlers to schema options
                    const dialog = this;
                    setTimeout(() => {
                        // Add search functionality
                        const searchInput = document.getElementById('kata-schema-search');
                        const schemaList = document.getElementById('kata-schema-list');
                        const allOptions = schemaList.querySelectorAll('.kata-schema-option');
                        
                        if (searchInput) {
                            searchInput.addEventListener('input', function(e) {
                                const searchTerm = e.target.value.toLowerCase().trim();
                                
                                allOptions.forEach(option => {
                                    const title = option.querySelector('h4').textContent.toLowerCase();
                                    const description = option.querySelector('p').textContent.toLowerCase();
                                    
                                    if (searchTerm === '' || title.includes(searchTerm) || description.includes(searchTerm)) {
                                        option.style.display = 'block';
                                    } else {
                                        option.style.display = 'none';
                                    }
                                });
                                
                                // Show "no results" message if all hidden
                                const visibleOptions = Array.from(allOptions).filter(opt => opt.style.display !== 'none');
                                const existingNoResults = schemaList.querySelector('.kata-no-results');
                                
                                if (visibleOptions.length === 0 && !existingNoResults) {
                                    const noResultsDiv = document.createElement('div');
                                    noResultsDiv.className = 'kata-no-results';
                                    noResultsDiv.style.cssText = 'text-align: center; padding: 40px 20px; color: #999;';
                                    noResultsDiv.innerHTML = `
                                        <p style="font-size: 48px; margin: 0;">🔍</p>
                                        <p style="margin: 10px 0 5px 0; font-size: 16px; font-weight: 600;">Không tìm thấy schema</p>
                                        <p style="margin: 0; font-size: 13px;">Thử tìm kiếm với từ khóa khác</p>
                                    `;
                                    schemaList.appendChild(noResultsDiv);
                                } else if (visibleOptions.length > 0 && existingNoResults) {
                                    existingNoResults.remove();
                                }
                            });
                        }
                        
                        const options = document.querySelectorAll('.kata-schema-option');
                        options.forEach(option => {
                            option.addEventListener('click', function() {
                                const key = this.dataset.key;
                                
                                // Remove active state from all options
                                options.forEach(opt => {
                                    opt.style.borderColor = '#ddd';
                                    opt.style.background = '#ffffff';
                                    opt.style.boxShadow = '0 2px 4px rgba(0,0,0,0.05)';
                                });
                                
                                // Add active state to clicked option
                                this.style.borderColor = '#0073aa';
                                this.style.background = '#f0f8ff';
                                this.style.boxShadow = '0 4px 12px rgba(0,115,170,0.25)';
                                
                                // Show preview in right panel
                                const previewPanel = document.getElementById('kata-preview-panel');
                                if (previewPanel && templates[key]) {
                                    const template = templates[key];
                                    previewPanel.style.display = 'block';
                                    previewPanel.style.padding = '24px';
                                    previewPanel.style.textAlign = 'left';
                                    previewPanel.style.alignItems = 'flex-start';
                                    previewPanel.innerHTML = `
                                        <div style="width: 100%; max-width: 900px;">
                                            <!-- Schema Header -->
                                            <div style="
                                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                                color: white;
                                                padding: 20px 24px;
                                                border-radius: 12px;
                                                margin-bottom: 24px;
                                            ">
                                                <h3 style="margin: 0 0 8px 0; font-size: 20px;">${template.title}</h3>
                                                <p style="margin: 0; opacity: 0.95; font-size: 13px;">Schema template với dữ liệu mẫu sẵn sàng sử dụng</p>
                                            </div>
                                            
                                            <!-- Preview Content -->
                                            <div style="margin-bottom: 24px;">
                                                ${template.preview}
                                            </div>
                                            
                                            <!-- Shortcode Display -->
                                            <div style="
                                                background: #f8f9fa;
                                                border: 1px solid #e5e7eb;
                                                border-radius: 8px;
                                                padding: 16px;
                                                margin-bottom: 24px;
                                            ">
                                                <h4 style="margin: 0 0 12px 0; font-size: 14px; color: #374151;">📝 Shortcode:</h4>
                                                <textarea 
                                                    readonly 
                                                    style="
                                                        width: 100%;
                                                        min-height: 120px;
                                                        padding: 12px;
                                                        border: 1px solid #d1d5db;
                                                        border-radius: 6px;
                                                        font-family: 'Courier New', monospace;
                                                        font-size: 12px;
                                                        background: #ffffff;
                                                        resize: vertical;
                                                        line-height: 1.5;
                                                        box-sizing: border-box;
                                                    "
                                                    onclick="this.select()"
                                                >${template.shortcode}</textarea>
                                                <p style="margin: 8px 0 0 0; font-size: 12px; color: #6b7280;">
                                                    💡 Click vào textarea để select toàn bộ code
                                                </p>
                                            </div>
                                            
                                            <!-- Action Buttons -->
                                            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                                                <button 
                                                    onclick="
                                                        const textarea = this.parentElement.previousElementSibling.querySelector('textarea');
                                                        textarea.select();
                                                        document.execCommand('copy');
                                                        this.textContent = '✅ Đã copy!';
                                                        setTimeout(() => { this.textContent = '📋 Copy shortcode'; }, 2000);
                                                    "
                                                    style="
                                                        padding: 10px 20px;
                                                        background: #6b7280;
                                                        color: white;
                                                        border: none;
                                                        border-radius: 6px;
                                                        font-size: 14px;
                                                        cursor: pointer;
                                                        transition: all 0.3s;
                                                    "
                                                    onmouseover="this.style.background='#4b5563'"
                                                    onmouseout="this.style.background='#6b7280'"
                                                >📋 Copy shortcode</button>
                                                
                                                <button 
                                                    onclick="
                                                        var shortcode = \`${template.shortcode.replace(/`/g, '\\`')}\`;
                                                        tinymce.activeEditor.insertContent(shortcode);
                                                        tinymce.activeEditor.windowManager.close();
                                                    "
                                                    style="
                                                        padding: 10px 24px;
                                                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                                        color: white;
                                                        border: none;
                                                        border-radius: 6px;
                                                        font-size: 14px;
                                                        font-weight: 600;
                                                        cursor: pointer;
                                                        transition: all 0.3s;
                                                        box-shadow: 0 4px 12px rgba(102,126,234,0.3);
                                                    "
                                                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102,126,234,0.4)'"
                                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102,126,234,0.3)'"
                                                >✨ Chèn vào Editor</button>
                                            </div>
                                        </div>
                                    `;
                                }
                            });
                        });
                    }, 100);
                }
            });
        }

        // Function to show preview and insert schema
        function showPreviewAndInsert(key) {
            var contentFields = {
                // Schema types with MODE 2 content visibility controls
                article: ['title', 'author', 'category', 'tags', 'excerpt', 'reading_time', 'word_count', 'date', 'image'],
                recipe: ['name', 'description', 'image', 'ingredients', 'instructions', 'time', 'nutrition', 'rating'],
                product: ['name', 'description', 'image', 'price', 'brand', 'category', 'availability', 'rating', 'features'],
                event: ['name', 'description', 'date', 'time', 'location', 'organizer', 'price', 'image', 'status'],
                howto: ['name', 'description', 'image', 'steps', 'tools', 'time', 'difficulty', 'cost'],
                video: ['title', 'description', 'thumbnail', 'video'],
                organization: ['name', 'logo', 'description', 'contact'],
                localbusiness: ['name', 'address', 'phone', 'hours', 'price', 'description', 'image'],
                local_business: ['name', 'address', 'contact', 'hours', 'description', 'services', 'rating'],
                jobposting: ['title', 'company', 'location', 'description', 'salary', 'type', 'date', 'requirements', 'benefits'],
                job_posting: ['title', 'company', 'location', 'description', 'salary', 'requirements', 'benefits'],
                image_metadata: ['preview', 'name', 'description', 'technical', 'size', 'dimensions', 'format', 'camera', 'creator', 'date', 'location', 'keywords'],
                course: ['name', 'description', 'provider', 'instructor', 'price', 'duration', 'level', 'skills'],
                software: ['name', 'description', 'version', 'operating_system', 'category', 'price', 'size'],
                book: ['name', 'author', 'description', 'publisher', 'date', 'pages', 'genre', 'isbn'],
                movie: ['name', 'description', 'director', 'actor', 'genre', 'duration', 'release', 'rating'],
                webpage: ['name', 'description', 'keywords', 'breadcrumb'],
                carousel: ['title', 'images', 'captions', 'links', 'controls', 'indicators'],
                dataset: ['title', 'description', 'headers', 'data', 'creator', 'date', 'license', 'keywords'],
                forum: ['title', 'description', 'topics', 'moderator', 'category', 'stats'],
                eduqa: ['question', 'answer', 'category', 'difficulty', 'author', 'tags', 'related'],
                employer_rating: ['company', 'rating', 'breakdown', 'reviews', 'size', 'industry'],
                profile_page: ['name', 'title', 'bio', 'skills', 'experience', 'education', 'contact', 'social', 'achievements'],
                math_solver: ['problem', 'solution', 'steps', 'category', 'difficulty', 'explanation', 'formula'],
                practice_problem: ['title', 'question', 'options', 'answer', 'explanation', 'category', 'difficulty', 'hints'],
                sitelinks: ['title', 'description', 'links', 'breadcrumb', 'site'],
                speakable: ['title', 'content', 'summary', 'language', 'voice_type']
            };
            
            var fields = contentFields[key] || [];
            var checkboxesHTML = '';
            
            // Define update function globally before dialog opens
            window['kataUpdatePreview_' + key] = function() {
                var baseShortcode = templates[key].shortcode;
                var checkedBoxes = document.querySelectorAll('#show_content_' + key + ' .kata-content-checkbox:checked');
                var checkedFields = [];
                
                checkedBoxes.forEach(function(cb) {
                    checkedFields.push(cb.getAttribute('data-field'));
                });
                
                var modifiedShortcode = baseShortcode;
                
                if (checkedFields.length > 0) {
                    checkedFields.forEach(function(field) {
                        // Remove hide_content_X="true"
                        var hidePattern = new RegExp('hide_content_' + field + '="true"\\s*', 'g');
                        modifiedShortcode = modifiedShortcode.replace(hidePattern, '');
                        
                        // Add show_content_X="true" if not exists
                        if (modifiedShortcode.indexOf('show_content_' + field) === -1) {
                            modifiedShortcode = modifiedShortcode.replace(/]$/, ' show_content_' + field + '="true"]');
                        }
                    });
                }
                
                // Update preview textarea
                var previewEl = document.getElementById('shortcode_preview_' + key);
                if (previewEl) {
                    previewEl.value = modifiedShortcode;
                }
            };
            
            if (fields.length > 0) {
                checkboxesHTML = `
                    <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; padding: 12px; margin: 12px 0;">
                        <h4 style="margin: 0 0 10px 0; color: #856404; font-size: 14px;">
                            🎨 MODE 2: Tùy chỉnh hiển thị Content (hide_content_*, show_content_*)
                        </h4>
                        <p style="margin: 0 0 10px 0; font-size: 12px; color: #856404;">
                            Chọn các phần tử muốn HIỆN trên giao diện. Mặc định: TẤT CẢ BỊ ẨN (hide_content_*="true")
                        </p>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; max-height: 200px; overflow-y: auto; padding: 8px; background: white; border-radius: 3px;">
                            ${fields.map(function(field) {
                                return `
                                    <label style="display: flex; align-items: center; cursor: pointer; padding: 4px; font-size: 12px;">
                                        <input type="checkbox" 
                                               id="show_content_${field}" 
                                               data-field="${field}"
                                               class="kata-content-checkbox"
                                               style="margin-right: 6px;"
                                               onchange="if(window.kataUpdatePreview_${key}) window.kataUpdatePreview_${key}(); this.nextElementSibling.style.fontWeight = this.checked ? 'bold' : 'normal';">
                                        <span style="color: #495057;">Hiện: ${field}</span>
                                    </label>
                                `;
                            }).join('')}
                        </div>
                        <div style="margin-top: 8px; padding: 6px; background: #e7f3ff; border-radius: 3px;">
                            <button type="button" 
                                    onclick="var cbs = document.querySelectorAll('#show_content_${key} .kata-content-checkbox'); cbs.forEach(function(cb){cb.checked=true; cb.nextElementSibling.style.fontWeight='bold';}); if(window.kataUpdatePreview_${key}) window.kataUpdatePreview_${key}();"
                                    style="font-size: 11px; padding: 4px 8px; margin-right: 6px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                ✅ Chọn tất cả
                            </button>
                            <button type="button"
                                    onclick="var cbs = document.querySelectorAll('#show_content_${key} .kata-content-checkbox'); cbs.forEach(function(cb){cb.checked=false; cb.nextElementSibling.style.fontWeight='normal';}); if(window.kataUpdatePreview_${key}) window.kataUpdatePreview_${key}();"
                                    style="font-size: 11px; padding: 4px 8px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                ❌ Bỏ chọn tất cả
                            </button>
                        </div>
                    </div>
                `;
            }
            
            editor.windowManager.open({
                title: templates[key].title + ' - Xem Trước & Chèn',
                width: Math.min(window.innerWidth * 0.95, 1400),
                height: Math.min(window.innerHeight * 0.95, 900),
                resizable: true,
                maximizable: true,
                body: [
                    {
                        type: 'container',
                        html: `
                            <div id="show_content_${key}" style="padding: 15px; height: 80vh !important; max-height: 80vh !important; width: 80vw !important; max-width: 80vw !important; overflow-y: auto; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif; box-sizing: border-box;">
                                <div style="margin-bottom: 15px;">
                                    <h3 style="color: #23282d; margin: 0 0 12px 0; font-size: 18px;">${templates[key].title}</h3>
                                    <div style="max-height: 200px; overflow-y: auto; margin-bottom: 15px;">
                                        ${templates[key].preview}
                                    </div>
                                </div>
                                
                                ${checkboxesHTML}
                                
                                <div style="background: #f8f9fa; border-radius: 4px; padding: 12px; margin: 12px 0;">
                                    <h4 style="margin: 0 0 8px 0; color: #495057; font-size: 14px;">📝 Shortcode sẽ được chèn:</h4>
                                    <textarea id="shortcode_preview_${key}" readonly style="width: 100%; height: 150px; font-family: monospace; font-size: 11px; border: 1px solid #ced4da; border-radius: 3px; padding: 8px; background: white; box-sizing: border-box; resize: vertical;">${templates[key].shortcode}</textarea>
                                </div>
                                
                                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; padding: 8px;">
                                    <p style="margin: 0; font-size: 12px; color: #0c5460; line-height: 1.4;">
                                        ℹ️ <strong>Thông tin:</strong> Sau khi chèn, có thể chỉnh sửa thuộc tính trong editor. JSON-LD schema tự động tạo khi publish.
                                    </p>
                                </div>
                            </div>
                        `
                    }
                ],
                buttons: [
                    {
                        text: '✅ Chèn Shortcode',
                        classes: 'widget btn primary',
                        onclick: function() {
                            // Get base shortcode
                            var shortcode = templates[key].shortcode;
                            
                            // Get checked content fields
                            var checkedFields = [];
                            var checkboxes = document.querySelectorAll('#show_content_' + key + ' .kata-content-checkbox:checked');
                            
                            if (checkboxes.length > 0) {
                                checkboxes.forEach(function(cb) {
                                    checkedFields.push(cb.getAttribute('data-field'));
                                });
                                
                                // Replace hide_content_* with show_content_* for checked fields
                                checkedFields.forEach(function(field) {
                                    // Remove hide_content_X="true"
                                    var hidePattern = new RegExp('hide_content_' + field + '="true"\\s*', 'g');
                                    shortcode = shortcode.replace(hidePattern, '');
                                    
                                    // Add show_content_X="true" if not exists
                                    if (shortcode.indexOf('show_content_' + field) === -1) {
                                        shortcode = shortcode.replace(/]$/, ' show_content_' + field + '="true"]');
                                    }
                                });
                            }
                            
                            // Insert modified shortcode
                            editor.insertContent('\n' + shortcode + '\n');
                            
                            // Close dialog
                            this.parent().parent().close();
                            
                            // Show success notification
                            editor.notificationManager.open({
                                text: `✅ Đã chèn ${templates[key].title} ${checkedFields.length > 0 ? 'với ' + checkedFields.length + ' fields hiển thị' : 'thành công'}!`,
                                type: 'success',
                                timeout: 3000
                            });
                        }
                    },
                    {
                        text: 'Hủy',
                        onclick: 'close'
                    }
                ]
            });
        }

        // Tạo menu dropdown chính  
        editor.addButton('kata_seo_manager', {
            title: 'KATA SEO Manager - Schema Templates',
            type: 'button',
            icon: 'dashicon dashicons-chart-line',
            onclick: function() {
                openSchemaSelector();
            }
        });

        // Tạo menu dropdown phụ cho quick access
        // editor.addButton('kata_seo_quick', {
        //     title: 'KATA Quick Schema',
        //     type: 'menubutton', 
        //     icon: 'dashicon dashicons-performance',
        //     menu: Object.keys(templates).slice(0, 6).map(key => ({
        //         text: templates[key].title,
        //         onclick: function() {
        //             // Chèn shortcode vào editor
        //             editor.insertContent('\n' + templates[key].shortcode + '\n');
                    
        //             // Hiển thị dialog preview
        //             editor.windowManager.open({
        //                 title: templates[key].title + ' - Đã Thêm Thành Công!',
        //                 width: 500,
        //                 height: 400,
        //                 body: [
        //                     {
        //                         type: 'container',
        //                         html: `
        //                             <div style="padding: 20px; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;">
        //                                 <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin-bottom: 15px;">
        //                                     <h3 style="color: #155724; margin: 0 0 10px 0;">✅ Đã thêm thành công!</h3>
        //                                     <p style="margin: 0; color: #155724;">Shortcode đã được chèn vào editor với dữ liệu mẫu.</p>
        //                                 </div>
        //                                 ${templates[key].preview}
        //                                 <div style="background: #e2e3e5; border-radius: 5px; padding: 10px; margin-top: 15px;">
        //                                     <p style="margin: 0; font-size: 12px; color: #6c757d;">
        //                                         💡 <strong>Tip:</strong> Bạn có thể chỉnh sửa nội dung trực tiếp trong editor. 
        //                                         Schema JSON-LD sẽ tự động được tạo khi publish bài viết.
        //                                     </p>
        //                                 </div>
        //                             </div>
        //                         `
        //                     }
        //                 ],
        //                 buttons: [
        //                     {
        //                         text: 'Xem Analytics',
        //                         onclick: function() {
        //                             window.open(ajaxurl.replace('/admin-ajax.php', '/admin.php?page=kata-seo-manager'), '_blank');
        //                             this.parent().close();
        //                         }
        //                     },
        //                     {
        //                         text: 'OK',
        //                         onclick: 'close',
        //                         primary: true
        //                     }
        //                 ]
        //             });
        //         }
        //     }))
        // });

        // Ensure toolbar is visible after init
        editor.on('init', function() {
            // Fix toolbar visibility
            setTimeout(function() {
                var container = editor.getContainer();
                if (container) {
                    var toolbar = jQuery(container).find('.mce-toolbar-grp');
                    toolbar.css({
                        'visibility': 'visible',
                        'display': 'block',
                        'opacity': '1',
                        'z-index': '100'
                    });
                }
            }, 100);
            
            // CSS cho preview styling  
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