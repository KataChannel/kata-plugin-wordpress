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

            article: {
                title: '📝 Article Schema',
                shortcode: '[kata_article title="5 Bí Quyết Thành Công Trong Kinh Doanh" author="Nguyễn Văn A" category="Kinh Doanh" tags="thành công, kinh doanh, khởi nghiệp" excerpt="Khám phá 5 bí quyết quan trọng giúp bạn thành công trong lĩnh vực kinh doanh từ những chuyên gia hàng đầu." reading_time="5" word_count="1200"]',
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
                    </div>
                `
            },

            recipe: {
                title: '🍳 Recipe Schema',
                shortcode: '[kata_recipe name="Phở Bò Hà Nội" description="Món phở bò truyền thống của Hà Nội với nước dùng trong vắt, thơm ngon" ingredients="500g xương bò|200g thịt bò|1 củ hành tây|Gia vị phở" instructions="Luộc xương bò 2 tiếng|Thái thịt bò mỏng|Trần bánh phở|Múc nước dùng ra tô" prep_time="30M" cook_time="2H" servings="4" calories="450"]',
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
                    </div>
                `
            },

            product: {
                title: '🛍️ Product Schema',
                shortcode: '[kata_product name="iPhone 15 Pro Max" description="Điện thoại thông minh cao cấp với chip A17 Pro và camera 48MP" price="29990000" currency="VND" brand="Apple" availability="InStock" rating_value="4.8" rating_count="1250" category="Điện thoại"]',
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
                    </div>
                `
            },

            event: {
                title: '📅 Event Schema',
                shortcode: '[kata_event name="Hội thảo Digital Marketing 2025" description="Hội thảo về xu hướng Digital Marketing mới nhất năm 2025" start_date="2025-11-15T09:00" end_date="2025-11-15T17:00" location="Khách sạn Lotte, Hà Nội" organizer="Marketing Vietnam" price="500000" currency="VND"]',
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
                    </div>
                `
            },

            howto: {
                title: '📋 HowTo Schema',
                shortcode: '[kata_howto name="Cách Tạo Website WordPress" description="Hướng dẫn chi tiết cách tạo website WordPress từ A đến Z" steps="Mua hosting và domain|Cài đặt WordPress|Chọn theme phù hợp|Tùy chỉnh giao diện|Thêm nội dung" tools="Hosting|Domain|WordPress theme" prep_time="1H" perform_time="3H" difficulty="Trung bình"]',
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
                    </div>
                `
            },

            review: {
                title: '⭐ Review Schema',
                shortcode: '[kata_review name="Đánh giá MacBook Pro M3" item_name="MacBook Pro 14 inch M3" item_type="Product" rating_value="4.5" review_body="Laptop có hiệu suất mạnh mẽ, thiết kế đẹp và thời lượng pin ấn tượng. Rất phù hợp cho công việc đồ họa và lập trình." author="Tech Reviewer"]',
                preview: `
                    <div class="kata-preview-box">
                        <h4>⭐ Review Schema sẽ tạo:</h4>
                        <div class="review-preview">
                            <h3>Đánh giá MacBook Pro M3</h3>
                            <p>⭐⭐⭐⭐⭐ 4.5/5 bởi Tech Reviewer</p>
                            <p><strong>Sản phẩm:</strong> MacBook Pro 14 inch M3</p>
                            <p>Laptop có hiệu suất mạnh mẽ, thiết kế đẹp...</p>
                        </div>
                        <small>✅ Rich snippets đánh giá với rating</small>
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
                shortcode: '[kata_course name="Lập trình Python cơ bản" description="Khóa học Python từ cơ bản đến nâng cao dành cho người mới bắt đầu" provider="CodeGym Vietnam" instructor="Nguyễn Văn B" price="2500000" duration="40H" level="beginner" skills="Python cơ bản|Lập trình hướng đối tượng|Web scraping"]',
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
                shortcode: '[kata_book name="Đắc Nhân Tâm" author="Dale Carnegie" description="Cuốn sách kinh điển về nghệ thuật giao tiếp và ứng xử" publisher="Nhà xuất bản Tổng hợp TP.HCM" publication_date="2018-01-15" pages="320" genre="Kỹ năng sống, Tâm lý học" isbn="978-604-2-15234-7"]',
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
            }
        };

        // Tạo modal selection cho schema templates
        function openSchemaSelector() {
            const schemaOptions = Object.keys(templates).map(key => `
                <div class="kata-schema-option" data-key="${key}" style="
                    border: 1px solid #ddd; 
                    border-radius: 8px; 
                    padding: 15px; 
                    margin: 10px 0; 
                    cursor: pointer; 
                    transition: all 0.3s ease;
                    background: #f8f9fa;
                " onmouseover="this.style.borderColor='#0073aa'; this.style.background='#e6f3ff';" 
                   onmouseout="this.style.borderColor='#ddd'; this.style.background='#f8f9fa';">
                    <h4 style="margin: 0 0 8px 0; color: #23282d;">${templates[key].title}</h4>
                    <p style="margin: 0; font-size: 13px; color: #666;">
                        ${templates[key].shortcode.length > 100 ? templates[key].shortcode.substring(0, 100) + '...' : templates[key].shortcode}
                    </p>
                </div>
            `).join('');

            editor.windowManager.open({
                title: '🏷️ Chọn Schema Template để Chèn',
                width: 600,
                height: 500,
                body: [
                    {
                        type: 'container',
                        html: `
                            <div style="padding: 20px; max-height: 400px; overflow-y: auto; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;">
                                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                                    <h3 style="margin: 0 0 5px 0;">KATA SEO Manager</h3>
                                    <p style="margin: 0; opacity: 0.9;">Chọn một template để chèn dữ liệu mẫu vào editor</p>
                                </div>
                                <div id="kata-schema-list">
                                    ${schemaOptions}
                                </div>
                                <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 10px; margin-top: 15px;">
                                    <p style="margin: 0; font-size: 12px; color: #856404;">
                                        💡 <strong>Lưu ý:</strong> Sau khi chọn, bạn có thể chỉnh sửa nội dung trực tiếp trong editor.
                                    </p>
                                </div>
                            </div>
                        `
                    }
                ],
                buttons: [
                    {
                        text: 'Hủy',
                        onclick: 'close'
                    }
                ],
                onpostrender: function() {
                    // Add click handlers to schema options
                    const dialog = this;
                    setTimeout(() => {
                        const options = document.querySelectorAll('.kata-schema-option');
                        options.forEach(option => {
                            option.addEventListener('click', function() {
                                const key = this.dataset.key;
                                
                                // Close current dialog
                                dialog.close();
                                
                                // Show preview and insert
                                showPreviewAndInsert(key);
                            });
                        });
                    }, 100);
                }
            });
        }

        // Function to show preview and insert schema
        function showPreviewAndInsert(key) {
            editor.windowManager.open({
                title: templates[key].title + ' - Xem Trước & Chèn',
                width: 650,
                height: 500,
                body: [
                    {
                        type: 'container',
                        html: `
                            <div style="padding: 20px; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif;">
                                <div style="margin-bottom: 20px;">
                                    <h3 style="color: #23282d; margin: 0 0 15px 0;">${templates[key].title}</h3>
                                    ${templates[key].preview}
                                </div>
                                
                                <div style="background: #f8f9fa; border-radius: 5px; padding: 15px; margin: 15px 0;">
                                    <h4 style="margin: 0 0 10px 0; color: #495057;">📝 Shortcode sẽ được chèn:</h4>
                                    <textarea readonly style="width: 100%; height: 120px; font-family: monospace; font-size: 12px; border: 1px solid #ced4da; border-radius: 4px; padding: 10px; background: white;">${templates[key].shortcode}</textarea>
                                </div>
                                
                                <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; padding: 10px;">
                                    <p style="margin: 0; font-size: 13px; color: #0c5460;">
                                        ℹ️ <strong>Thông tin:</strong> Sau khi chèn, bạn có thể chỉnh sửa các thuộc tính trực tiếp trong editor. JSON-LD schema sẽ tự động được tạo khi xuất bản.
                                    </p>
                                </div>
                            </div>
                        `
                    }
                ],
                buttons: [
                    {
                        text: 'Chèn vào Editor',
                        classes: 'widget btn primary',
                        onclick: function() {
                            // Insert shortcode
                            editor.insertContent('\n' + templates[key].shortcode + '\n');
                            
                            // Close dialog
                            this.parent().parent().close();
                            
                            // Show success notification
                            editor.notificationManager.open({
                                text: `✅ Đã chèn ${templates[key].title} thành công!`,
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
        editor.addButton('kata_seo_quick', {
            title: 'KATA Quick Schema',
            type: 'menubutton', 
            icon: 'dashicon dashicons-performance',
            menu: Object.keys(templates).slice(0, 6).map(key => ({
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