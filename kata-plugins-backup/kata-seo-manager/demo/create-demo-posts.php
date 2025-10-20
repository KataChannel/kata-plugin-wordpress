<?php
/**
 * KATA SEO Manager Demo Posts Creator
 * Script tạo bài viết demo cho tất cả 27 schema types
 */

// Include WordPress
require_once('wp-config.php');
require_once('wp-load.php');
require_once('wp-admin/includes/post.php');

class KATA_Demo_Posts_Creator {
    
    private $demo_posts = array();
    
    public function __construct() {
        $this->init_demo_posts();
    }
    
    private function init_demo_posts() {
        $this->demo_posts = array(
            
            // 1. Article Schema
            'article' => array(
                'title' => 'Bí Quyết Thành Công Trong Digital Marketing 2025',
                'content' => '[kata_article title="Bí Quyết Thành Công Trong Digital Marketing 2025" author="Nguyễn Minh Anh" category="Digital Marketing" tags="marketing, SEO, social media, content marketing" excerpt="Khám phá 10 bí quyết quan trọng giúp bạn thành công trong lĩnh vực Digital Marketing năm 2025 từ những chuyên gia hàng đầu." reading_time="8" word_count="2500" image="https://example.com/digital-marketing-2025.jpg"]

<h2>10 Bí Quyết Digital Marketing Hiệu Quả</h2>

<p>Digital Marketing đang phát triển với tốc độ chóng mặt. Để thành công trong năm 2025, bạn cần nắm vững những bí quyết sau:</p>

<h3>1. Tối ưu SEO với AI</h3>
<p>Sử dụng các công cụ AI để phân tích từ khóa và tối ưu nội dung theo intent của người dùng.</p>

<h3>2. Video Marketing</h3>
<p>Video content chiếm 80% lưu lượng internet. Đầu tư vào video chất lượng cao sẽ mang lại ROI tốt nhất.</p>

<h3>3. Personalization</h3>
<p>Cá nhân hóa trải nghiệm người dùng dựa trên data và behavior analysis.</p>

<blockquote>
"Content is king, but context is god" - Gary Vaynerchuk
</blockquote>

<p>Áp dụng những bí quyết này sẽ giúp business của bạn tăng trưởng mạnh mẽ trong năm 2025.</p>',
                'category' => 'Marketing',
                'tags' => array('digital marketing', 'SEO', '2025')
            ),

            // 2. Recipe Schema  
            'recipe' => array(
                'title' => 'Công Thức Phở Bò Hà Nội Chuẩn Vị',
                'content' => '[kata_recipe name="Phở Bò Hà Nội Truyền Thống" description="Công thức phở bò Hà Nội authentic với nước dùng ninh 12 tiếng, thơm ngon đậm đà" ingredients="1kg xương bò|500g thịt bò tái|200g bánh phở|1 củ hành tây lớn|1 miếng gừng 50g|2 quả hồi|1 thanh quế|1 thìa cà phê hạt tiêu|Muối|Nước mắm|Đường phèn" instructions="Sơ chế xương bò, rửa sạch và chần qua nước sôi|Nướng hành tây và gừng trên bếp gas|Ninh xương bò với hành tây, gừng trong 12 tiếng|Rang các gia vị (hồi, quế, tiêu) cho thơm|Cho gia vị vào nước dùng, nêm nếm|Thái thịt bò mỏng, trần bánh phở|Bày thịt, bánh phở vào tô, chan nước dùng nóng|Ăn kèm với rau thơm, chanh, ớt" prep_time="45M" cook_time="12H" total_time="12H45M" servings="6" calories="420" cuisine="Vietnamese" category="Main Course" difficulty="Medium" rating_value="4.8" rating_count="156"]

<h2>Phở Bò Hà Nội - Tinh Hoa Ẩm Thực Việt</h2>

<p>Phở là món ăn đặc trưng nhất của ẩm thực Việt Nam, đặc biệt là phở bò Hà Nội với hương vị đậm đà, nước dùng trong vắt.</p>

<h3>Nguyên liệu chính:</h3>
<ul>
<li><strong>Xương bò:</strong> 1kg (xương ống, xương sườn)</li>
<li><strong>Thịt bò:</strong> 500g (nạm, gầu bò)</li>
<li><strong>Bánh phở:</strong> 200g (bánh phở tươi)</li>
<li><strong>Gia vị:</strong> Hành tây, gừng, hồi, quế, tiêu</li>
</ul>

<h3>Cách làm chi tiết:</h3>

<h4>Bước 1: Chuẩn bị nguyên liệu</h4>
<p>Xương bò rửa sạch, chần qua nước sôi để loại bỏ tạp chất. Hành tây và gừng nướng thơm trên bếp gas.</p>

<h4>Bước 2: Ninh nước dùng</h4>
<p>Cho xương bò vào nồi lớn, đổ nước ngập. Ninh trong 12 tiếng với lửa nhỏ để có nước dùng trong vắt.</p>

<h4>Bước 3: Gia vị</h4>
<p>Rang thơm hồi, quế, tiêu rồi bỏ vào túi vải cho vào nồi nước dùng.</p>

<h4>Bước 4: Hoàn thiện</h4>
<p>Thái thịt bò mỏng, trần bánh phở. Bày vào tô, chan nước dùng nóng.</p>

<blockquote>
<p><em>"Phở ngon nhất là phở có nước dùng trong vắt, thịt bò tươi ngon và bánh phở dai, không bở."</em></p>
</blockquote>

<p>Thưởng thức phở bò nóng hổi cùng rau thơm, chanh và ớt sẽ mang lại trải nghiệm ẩm thực tuyệt vời!</p>',
                'category' => 'Ẩm Thực',
                'tags' => array('phở bò', 'công thức', 'ẩm thực Việt')
            ),

            // 3. Product Schema
            'product' => array(
                'title' => 'iPhone 15 Pro Max - Đánh Giá Chi Tiết',
                'content' => '[kata_product name="iPhone 15 Pro Max 256GB" description="Điện thoại thông minh cao cấp với chip A17 Pro, camera 48MP và màn hình Super Retina XDR 6.7 inch" price="29990000" currency="VND" brand="Apple" category="Điện thoại thông minh" sku="IPHONE15PM256" availability="InStock" condition="New" rating_value="4.7" rating_count="2847" image="https://example.com/iphone15promax.jpg" url="https://example.com/iphone-15-pro-max"]

<h2>iPhone 15 Pro Max - Đỉnh Cao Công Nghệ</h2>

<p>iPhone 15 Pro Max là flagship mới nhất của Apple với nhiều cải tiến đáng kể về hiệu năng, camera và thiết kế.</p>

<h3>⭐ Điểm nổi bật:</h3>

<h4>🔥 Hiệu năng vượt trội</h4>
<ul>
<li><strong>Chip A17 Pro:</strong> Hiệu năng CPU nhanh hơn 10%, GPU nhanh hơn 20%</li>
<li><strong>RAM:</strong> 8GB cho đa nhiệm mượt mà</li>
<li><strong>Dung lượng:</strong> 256GB/512GB/1TB</li>
</ul>

<h4>📸 Camera chuyên nghiệp</h4>
<ul>
<li><strong>Camera chính:</strong> 48MP với sensor lớn</li>
<li><strong>Zoom quang học:</strong> Lên đến 5x</li>
<li><strong>Chế độ Portrait:</strong> Tự động phát hiện người</li>
<li><strong>Video 4K:</strong> ProRes và Cinematic mode</li>
</ul>

<h4>🖥️ Màn hình đẳng cấp</h4>
<ul>
<li><strong>Kích thước:</strong> 6.7 inch Super Retina XDR</li>
<li><strong>Độ phân giải:</strong> 2796 x 1290 pixels</li>
<li><strong>Tần số quét:</strong> 120Hz ProMotion</li>
<li><strong>Độ sáng:</strong> Lên đến 2000 nits</li>
</ul>

<h3>💰 Giá bán và khuyến mãi:</h3>

<div style="background: #f0f8ff; padding: 20px; border-radius: 10px; margin: 20px 0;">
<h4>🏷️ Giá hiện tại: 29.990.000 VND</h4>
<p><strong>✅ Còn hàng</strong> - Giao hàng trong 24h</p>
<p><strong>🎁 Khuyến mãi:</strong></p>
<ul>
<li>Tặng ốp lưng chính hãng Apple (1.490.000 VND)</li>
<li>Bảo hành 12 tháng toàn cầu</li>
<li>Trả góp 0% lãi suất</li>
</ul>
</div>

<h3>📊 Đánh giá từ người dùng:</h3>
<p><strong>⭐⭐⭐⭐⭐ 4.7/5</strong> (2,847 reviews)</p>

<blockquote>
<p><em>"Camera cực kỳ ấn tượng, chụp ảnh đêm rất đẹp. Hiệu năng mượt mà, pin trâu. Đáng đồng tiền bát gạo!"</em> - Nguyễn Văn A</p>
</blockquote>

<p>iPhone 15 Pro Max xứng đáng là chiếc smartphone flagship tốt nhất hiện tại với sự kết hợp hoàn hảo giữa thiết kế, hiệu năng và tính năng.</p>',
                'category' => 'Công Nghệ',
                'tags' => array('iPhone', 'Apple', 'smartphone')
            ),

            // 4. Event Schema
            'event' => array(
                'title' => 'Hội Thảo Digital Marketing Vietnam 2025',
                'content' => '[kata_event name="Digital Marketing Vietnam Summit 2025" description="Hội thảo lớn nhất về Digital Marketing tại Việt Nam với sự tham gia của 50+ diễn giả hàng đầu" start_date="2025-03-15T08:00" end_date="2025-03-16T18:00" location="Lotte Center Hanoi, 54 Liễu Giai, Ba Đình, Hà Nội" venue="Lotte Center Hanoi" organizer="Vietnam Digital Marketing Association" price="1500000" currency="VND" image="https://example.com/dmvn2025.jpg" url="https://dmvietnam2025.com"]

<h2>🚀 Digital Marketing Vietnam Summit 2025</h2>
<h3>Sự kiện Digital Marketing lớn nhất năm!</h3>

<p>Tham gia cùng 2000+ marketer hàng đầu Việt Nam trong 2 ngày học hỏi và networking đầy giá trị.</p>

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 15px; margin: 30px 0; text-align: center;">
<h3>📅 THÔNG TIN SỰ KIỆN</h3>
<p><strong>🗓️ Thời gian:</strong> 15-16/03/2025 (8:00 - 18:00)</p>
<p><strong>📍 Địa điểm:</strong> Lotte Center Hanoi</p>
<p><strong>🎫 Giá vé:</strong> 1.500.000 VND</p>
<p><strong>👥 Dự kiến:</strong> 2000+ tham dự</p>
</div>

<h3>🎯 Chương trình chính:</h3>

<h4>Ngày 1 - 15/03/2025:</h4>
<ul>
<li><strong>08:00-09:00:</strong> Đăng ký & Networking Coffee</li>
<li><strong>09:00-10:30:</strong> Keynote "Future of Digital Marketing in AI Era"</li>
<li><strong>10:45-12:15:</strong> Panel Discussion "E-commerce Trends 2025"</li>
<li><strong>13:30-15:00:</strong> Workshop "Advanced Facebook Ads"</li>
<li><strong>15:15-16:45:</strong> "Content Marketing That Converts"</li>
<li><strong>17:00-18:00:</strong> Networking & Startup Pitch</li>
</ul>

<h4>Ngày 2 - 16/03/2025:</h4>
<ul>
<li><strong>09:00-10:30:</strong> "SEO & Technical Optimization"</li>
<li><strong>10:45-12:15:</strong> "Influencer Marketing ROI"</li>  
<li><strong>13:30-15:00:</strong> "Marketing Automation"</li>
<li><strong>15:15-16:45:</strong> "Data Analytics & Attribution"</li>
<li><strong>17:00-18:00:</strong> Awards Ceremony & Closing</li>
</ul>

<h3>🌟 Diễn giả đáng chú ý:</h3>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;">

<div style="border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
<h4>👨‍💼 Nguyễn Minh Anh</h4>
<p><strong>CEO - Digital Agency ABC</strong></p>
<p>15+ năm kinh nghiệm, từng làm việc tại Google APAC</p>
</div>

<div style="border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
<h4>👩‍💼 Trần Thị Lan</h4>
<p><strong>Head of Marketing - Shopee Vietnam</strong></p>
<p>Chuyên gia E-commerce Marketing hàng đầu</p>
</div>

<div style="border: 1px solid #ddd; border-radius: 10px; padding: 20px;">
<h4>👨‍🏫 John Smith</h4>
<p><strong>International Speaker</strong></p>
<p>Marketing Automation Expert from Silicon Valley</p>
</div>

</div>

<h3>🎁 Quyền lợi tham dự:</h3>
<ul>
<li>✅ Tham dự tất cả sessions trong 2 ngày</li>
<li>✅ Tài liệu học tập đầy đủ (slides, templates)</li>
<li>✅ Networking với 2000+ professionals</li>
<li>✅ Certificate of Completion</li>
<li>✅ Voucher giảm giá các khóa học</li>
<li>✅ Buffet trưa 2 ngày</li>
<li>✅ Welcome kit (áo, notebook, pen)</li>
</ul>

<div style="background: #fffbf0; border: 2px solid #f59e0b; border-radius: 10px; padding: 20px; margin: 30px 0;">
<h3>🔥 EARLY BIRD - Chỉ còn 3 ngày!</h3>
<p><strong>Giá Early Bird:</strong> <del>1.500.000 VND</del> <span style="color: #dc2626; font-size: 24px; font-weight: bold;">1.200.000 VND</span></p>
<p><strong>Tiết kiệm:</strong> 300.000 VND (20%)</p>
<p><em>⏰ Ưu đãi kết thúc: 31/12/2024</em></p>
</div>

<h3>📞 Thông tin liên hệ:</h3>
<ul>
<li><strong>📧 Email:</strong> info@dmvietnam2025.com</li>
<li><strong>📱 Hotline:</strong> 0901 234 567</li>
<li><strong>🌐 Website:</strong> dmvietnam2025.com</li>
<li><strong>📘 Fanpage:</strong> Facebook.com/DMVietnam2025</li>
</ul>

<blockquote>
<p><em>"Đây là cơ hội tuyệt vời để học hỏi từ những chuyên gia hàng đầu và mở rộng network trong ngành Digital Marketing!"</em></p>
</blockquote>',
                'category' => 'Sự Kiện',
                'tags' => array('digital marketing', 'hội thảo', 'networking')
            ),

            // 5. HowTo Schema
            'howto' => array(
                'title' => 'Cách Tạo Website WordPress Từ A Đến Z',
                'content' => '[kata_howto name="Hướng Dẫn Tạo Website WordPress Chuyên Nghiệp" description="Hướng dẫn chi tiết cách tạo website WordPress từ A đến Z dành cho người mới bắt đầu, bao gồm hosting, domain, cài đặt và tối ưu" steps="Chọn và mua hosting phù hợp|Đăng ký domain name|Cài đặt WordPress|Chọn theme phù hợp|Cài đặt plugin cần thiết|Tạo nội dung cơ bản|Tối ưu SEO|Backup và bảo mật" tools="Hosting|Domain|WordPress|Theme|Plugin|FTP Client" materials="Máy tính|Kết nối internet|Thẻ tín dụng/Paypal" prep_time="2H" perform_time="8H" total_time="10H" difficulty="Trung bình" cost="1500000" currency="VND"]

<h2>🌐 Tạo Website WordPress - Hướng Dẫn Toàn Diện</h2>

<p>WordPress là nền tảng tạo website phổ biến nhất thế giới với 40% websites sử dụng. Hướng dẫn này sẽ giúp bạn tạo một website chuyên nghiệp từ con số 0.</p>

<div style="background: #e6f3ff; border-left: 5px solid #0073aa; padding: 20px; margin: 20px 0;">
<h3>⏱️ Thông tin tổng quan:</h3>
<ul>
<li><strong>Thời gian chuẩn bị:</strong> 2 giờ</li>
<li><strong>Thời gian thực hiện:</strong> 8 giờ</li>
<li><strong>Độ khó:</strong> Trung bình</li>
<li><strong>Chi phí ước tính:</strong> 1,500,000 VND/năm</li>
</ul>
</div>

<h3>🛠️ Công cụ và vật liệu cần thiết:</h3>

<h4>Công cụ:</h4>
<ul>
<li>✅ <strong>Hosting</strong> (VPS/Shared hosting)</li>
<li>✅ <strong>Domain name</strong> (.com/.vn/.org)</li>
<li>✅ <strong>WordPress CMS</strong> (miễn phí)</li>
<li>✅ <strong>Theme</strong> (miễn phí hoặc premium)</li>
<li>✅ <strong>Plugin</strong> (SEO, Security, Backup)</li>
<li>✅ <strong>FTP Client</strong> (FileZilla)</li>
</ul>

<h4>Vật liệu:</h4>
<ul>
<li>💻 Máy tính/laptop</li>
<li>🌐 Kết nối internet ổn định</li>
<li>💳 Phương thức thanh toán (thẻ tín dụng/Paypal)</li>
</ul>

<h3>📋 Các bước thực hiện chi tiết:</h3>

<div style="counter-reset: step-counter;">

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 1: Chọn và mua hosting phù hợp</h4>
<p><strong>🎯 Mục tiêu:</strong> Có server để chạy website</p>
<p><strong>⏰ Thời gian:</strong> 30 phút</p>

<h5>Lựa chọn hosting:</h5>
<ul>
<li><strong>Shared hosting:</strong> 100-300k/tháng (phù hợp mới bắt đầu)</li>
<li><strong>VPS:</strong> 500k-2tr/tháng (website lớn)</li>
<li><strong>Cloud hosting:</strong> Linh hoạt theo usage</li>
</ul>

<h5>Nhà cung cấp uy tín:</h5>
<ul>
<li>🇻🇳 <strong>Việt Nam:</strong> Inet, BKHOST, Viettel IDC</li>
<li>🌍 <strong>Quốc tế:</strong> SiteGround, Bluehost, HostGator</li>
</ul>

<p><strong>💡 Tip:</strong> Chọn hosting có hỗ trợ 1-click WordPress install để dễ dàng hơn.</p>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 2: Đăng ký domain name</h4>
<p><strong>🎯 Mục tiêu:</strong> Có địa chỉ website (tenwebsite.com)</p>
<p><strong>⏰ Thời gian:</strong> 15 phút</p>

<h5>Tips chọn domain:</h5>
<ul>
<li>✅ Ngắn gọn, dễ nhớ</li>
<li>✅ Liên quan đến nội dung website</li>
<li>✅ Tránh dấu gạch ngang, số</li>
<li>✅ Ưu tiên .com trước</li>
</ul>

<h5>Giá domain phổ biến:</h5>
<ul>
<li><strong>.com:</strong> ~400k/năm</li>
<li><strong>.vn:</strong> ~600k/năm</li>
<li><strong>.org/.net:</strong> ~350k/năm</li>
</ul>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 3: Cài đặt WordPress</h4>
<p><strong>🎯 Mục tiêu:</strong> Cài đặt WordPress trên hosting</p>
<p><strong>⏰ Thời gian:</strong> 30 phút</p>

<h5>Cách 1: 1-Click Install (Khuyên dùng)</h5>
<ol>
<li>Đăng nhập cPanel hosting</li>
<li>Tìm "WordPress" hoặc "Softaculous"</li>
<li>Click "Install Now"</li>
<li>Điền thông tin domain, admin</li>
<li>Chờ 5-10 phút hoàn thành</li>
</ol>

<h5>Cách 2: Upload thủ công</h5>
<ol>
<li>Download WordPress từ wordpress.org</li>
<li>Upload qua FTP lên thư mục public_html</li>
<li>Tạo database MySQL</li>
<li>Chạy file install.php</li>
</ol>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 4: Chọn theme phù hợp</h4>
<p><strong>🎯 Mục tiêu:</strong> Có giao diện đẹp, phù hợp mục đích</p>
<p><strong>⏰ Thời gian:</strong> 1 giờ</p>

<h5>Loại theme:</h5>
<ul>
<li><strong>Free themes:</strong> Miễn phí từ WordPress.org</li>
<li><strong>Premium themes:</strong> $30-100, nhiều tính năng</li>
<li><strong>Custom theme:</strong> Thiết kế riêng, 10-50 triệu</li>
</ul>

<h5>Theme phổ biến:</h5>
<ul>
<li>🎨 <strong>Astra:</strong> Nhanh, đa mục đích</li>
<li>🛒 <strong>Storefront:</strong> Cho WooCommerce</li>
<li>📰 <strong>Newspaper:</strong> Cho blog/tin tức</li>
<li>🏢 <strong>Avada:</strong> Đa năng, nhiều demo</li>
</ul>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 5: Cài đặt plugin cần thiết</h4>
<p><strong>🎯 Mục tiêu:</strong> Bổ sung tính năng cho website</p>
<p><strong>⏰ Thời gian:</strong> 1 giờ</p>

<h5>Plugin thiết yếu:</h5>
<ul>
<li>🔍 <strong>Yoast SEO:</strong> Tối ưu SEO</li>
<li>🛡️ <strong>Wordfence:</strong> Bảo mật</li>
<li>💾 <strong>UpdraftPlus:</strong> Backup tự động</li>
<li>⚡ <strong>W3 Total Cache:</strong> Tăng tốc website</li>
<li>📞 <strong>Contact Form 7:</strong> Form liên hệ</li>
</ul>

<h5>Cách cài plugin:</h5>
<ol>
<li>Vào Admin → Plugins → Add New</li>
<li>Search tên plugin</li>
<li>Click "Install Now" → "Activate"</li>
<li>Cấu hình theo hướng dẫn plugin</li>
</ol>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 6: Tạo nội dung cơ bản</h4>
<p><strong>🎯 Mục tiêu:</strong> Có nội dung cần thiết cho website</p>
<p><strong>⏰ Thời gian:</strong> 3 giờ</p>

<h5>Pages cần tạo:</h5>
<ul>
<li>🏠 <strong>Homepage:</strong> Trang chủ giới thiệu</li>
<li>ℹ️ <strong>About:</strong> Giới thiệu về công ty/cá nhân</li>
<li>📞 <strong>Contact:</strong> Thông tin liên hệ</li>
<li>🔒 <strong>Privacy Policy:</strong> Chính sách bảo mật</li>
<li>📋 <strong>Terms:</strong> Điều khoản sử dụng</li>
</ul>

<h5>Tạo menu navigation:</h5>
<ol>
<li>Vào Appearance → Menus</li>
<li>Create new menu</li>
<li>Add pages vào menu</li>
<li>Assign to Header location</li>
</ol>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 7: Tối ưu SEO cơ bản</h4>
<p><strong>🎯 Mục tiêu:</strong> Website thân thiện với search engine</p>
<p><strong>⏰ Thời gian:</strong> 1.5 giờ</p>

<h5>Cấu hình Yoast SEO:</h5>
<ul>
<li>✅ Setup wizard configuration</li>
<li>✅ Set focus keywords cho pages</li>
<li>✅ Tối ưu meta title, description</li>
<li>✅ Enable XML sitemap</li>
<li>✅ Connect Google Search Console</li>
</ul>

<h5>Technical SEO:</h5>
<ul>
<li>⚡ Optimize images (compress, alt text)</li>
<li>🔗 Internal linking structure</li>
<li>📱 Mobile responsive check</li>
<li>🚀 Page speed optimization</li>
</ul>
</div>

<div style="counter-increment: step-counter; border: 1px solid #ddd; border-radius: 10px; padding: 20px; margin: 20px 0;">
<h4 style="color: #0073aa;">Bước 8: Backup và bảo mật</h4>
<p><strong>🎯 Mục tiêu:</strong> Bảo vệ website khỏi rủi ro</p>
<p><strong>⏰ Thời gian:</strong> 45 phút</p>

<h5>Setup Backup:</h5>
<ul>
<li>📅 Schedule automatic daily backup</li>
<li>☁️ Store backup to cloud (Google Drive, Dropbox)</li>
<li>✅ Test restore process</li>
</ul>

<h5>Security measures:</h5>
<ul>
<li>🔐 Strong admin passwords</li>
<li>🛡️ Firewall configuration</li>
<li>🚫 Hide wp-admin from bots</li>
<li>🔄 Regular updates</li>
</ul>
</div>

</div>

<h3>💰 Chi phí ước tính:</h3>

<table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
<tr style="background: #f8f9fa;">
<th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Hạng mục</th>
<th style="border: 1px solid #ddd; padding: 12px; text-align: right;">Giá (VND)</th>
<th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Chu kỳ</th>
</tr>
<tr>
<td style="border: 1px solid #ddd; padding: 12px;">Hosting Shared</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">1,200,000</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">1 năm</td>
</tr>
<tr>
<td style="border: 1px solid #ddd; padding: 12px;">Domain .com</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">400,000</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">1 năm</td>
</tr>
<tr>
<td style="border: 1px solid #ddd; padding: 12px;">Premium Theme</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">2,000,000</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">1 lần</td>
</tr>
<tr>
<td style="border: 1px solid #ddd; padding: 12px;">Premium Plugins</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">1,500,000</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">1 năm</td>
</tr>
<tr style="background: #e8f5e8; font-weight: bold;">
<td style="border: 1px solid #ddd; padding: 12px;">TỔNG CỘNG</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: right;">5,100,000</td>
<td style="border: 1px solid #ddd; padding: 12px; text-align: center;">Năm đầu</td>
</tr>
</table>

<div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 10px; padding: 20px; margin: 30px 0;">
<h3>⚠️ Lưu ý quan trọng:</h3>
<ul>
<li>🔄 <strong>Backup thường xuyên:</strong> Tránh mất dữ liệu</li>
<li>🔐 <strong>Bảo mật:</strong> Cập nhật WordPress, plugin định kỳ</li>
<li>📈 <strong>SEO:</strong> Cần thời gian 3-6 tháng mới có hiệu quả</li>
<li>📱 <strong>Mobile:</strong> 60% traffic từ mobile, phải responsive</li>
<li>⚡ <strong>Speed:</strong> Website chậm = mất khách hàng</li>
</ul>
</div>

<h3>🎯 Kết quả đạt được:</h3>
<ul>
<li>✅ Website WordPress hoàn chỉnh, chuyên nghiệp</li>
<li>✅ Tối ưu SEO cơ bản</li>
<li>✅ Bảo mật và backup tự động</li>
<li>✅ Ready để phát triển nội dung</li>
<li>✅ Kiến thức quản trị website cơ bản</li>
</ul>

<blockquote>
<p><em>"Một website tốt không chỉ đẹp mà còn phải nhanh, an toàn và thân thiện với SEO. Đầu tư thời gian làm đúng từ đầu sẽ tiết kiệm rất nhiều công sức sau này!"</em></p>
</blockquote>

<p>Chúc bạn thành công với website WordPress mới! 🎉</p>',
                'category' => 'Hướng Dẫn',
                'tags' => array('WordPress', 'website', 'hướng dẫn')
            ),

            // 6. Review Schema
            'review' => array(
                'title' => 'Review iPhone 15 Pro Max - Flagship Đáng Đồng Tiền',
                'content' => '[kata_review item_name="iPhone 15 Pro Max" item_type="Product" rating_value="9.2" rating_scale="10" author="Tech Reviewer VN" publisher="TechVN Magazine" review_body="iPhone 15 Pro Max là một chiếc smartphone xuất sắc với nhiều nâng cấp đáng kể so với thế hệ trước. Camera, hiệu năng và thiết kế đều ở mức top tier." pros="Camera xuất sắc|Hiệu năng mạnh mẽ|Thiết kế premium|Pin trâu" cons="Giá cao|Nặng|Sạc chậm" image="https://example.com/iphone15pm-review.jpg"]

<h2>📱 Đánh Giá Chi Tiết iPhone 15 Pro Max</h2>

<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 15px; text-align: center; margin: 20px 0;">
<h3>⭐ ĐIỂM TỔNG QUAN: 9.2/10</h3>
<p>Flagship xuất sắc, đáng đầu tư</p>
</div>

<p>Sau 2 tuần sử dụng iPhone 15 Pro Max, đây là đánh giá chi tiết và trung thực nhất về chiếc flagship mới nhất của Apple.</p>',
                'category' => 'Review',
                'tags' => array('review', 'iPhone', 'đánh giá')
            ),

            // 7. Movie Schema
            'movie' => array(
                'title' => 'Bom Tấn Avatar 3 - Thông Tin Chi Tiết',
                'content' => '[kata_movie name="Avatar: The Seed Bearer" director="James Cameron" actors="Sam Worthington, Zoe Saldana, Sigourney Weaver" genre="Sci-Fi, Adventure, Drama" duration="PT162M" release_date="2025-12-20" rating_value="8.9" rating_count="15670" description="Phần tiếp theo của series Avatar, Jake và Neytiri tiếp tục cuộc phiêu lưu trên hành tinh Pandora với những thử thách mới." image="https://example.com/avatar3.jpg" trailer="https://youtube.com/watch?v=avatar3"]

<h2>🎬 Avatar 3: The Seed Bearer - Siêu Phẩm Sci-Fi 2025</h2>

<p>James Cameron tiếp tục mang đến màn ảnh rộng một tác phẩm điện ảnh đỉnh cao với Avatar 3, hứa hẹn sẽ là bom tấn lớn nhất năm 2025.</p>',
                'category' => 'Phim Ảnh',
                'tags' => array('Avatar', 'James Cameron', 'sci-fi')
            ),

            // 8. Course Schema
            'course' => array(
                'title' => 'Khóa Học Digital Marketing Toàn Diện',
                'content' => '[kata_course name="Digital Marketing Mastery 2025" provider="Marketing Academy VN" instructor="Nguyễn Digital Expert" description="Khóa học Digital Marketing từ cơ bản đến nâng cao, bao gồm SEO, SEM, Social Media, Content Marketing" duration="P12W" level="Intermediate" price="4999000" currency="VND" language="Vietnamese" category="Marketing" skills="SEO|SEM|Social Media Marketing|Content Marketing|Analytics" certificate="yes" online="yes"]

<h2>🎓 Digital Marketing Mastery 2025</h2>

<p>Khóa học Digital Marketing toàn diện nhất tại Việt Nam, được thiết kế để biến bạn thành một Digital Marketer chuyên nghiệp.</p>',
                'category' => 'Giáo Dục',
                'tags' => array('khóa học', 'digital marketing', 'online')
            ),

            // 9. Software Schema
            'software' => array(
                'title' => 'Phần Mềm Quản Lý Bán Hàng SalesPro',
                'content' => '[kata_software name="SalesPro CRM" version="3.2.1" operating_system="Windows, macOS, Linux" category="CRM Software" price="299" currency="USD" developer="SalesTech Solutions" release_date="2024-09-15" description="Phần mềm CRM chuyên nghiệp giúp quản lý khách hàng, bán hàng và marketing hiệu quả" features="Contact Management|Sales Pipeline|Email Marketing|Reports & Analytics|Mobile App" requirements="4GB RAM, 2GB Storage" rating_value="4.6" rating_count="892"]

<h2>💼 SalesPro CRM - Giải Pháp Quản Lý Bán Hàng</h2>

<p>SalesPro là phần mềm CRM hàng đầu giúp doanh nghiệp tối ưu hóa quy trình bán hàng và chăm sóc khách hàng.</p>',
                'category' => 'Phần Mềm',
                'tags' => array('CRM', 'phần mềm', 'bán hàng')
            ),

            // 10. Book Schema
            'book' => array(
                'title' => 'Cuốn Sách "Thành Công Trong Kinh Doanh Online"',
                'content' => '[kata_book name="Thành Công Trong Kinh Doanh Online" author="Trần Doanh Nhân" isbn="978-604-777-888-9" publisher="NXB Tri Thức" pages="320" language="Vietnamese" genre="Business, E-commerce" price="199000" currency="VND" publication_date="2024-08-20" description="Hướng dẫn chi tiết cách xây dựng và phát triển business online thành công từ con số 0" rating_value="4.7" rating_count="234"]

<h2>📚 "Thành Công Trong Kinh Doanh Online"</h2>

<p>Cuốn sách cẩm nang hoàn chỉnh cho những ai muốn bắt đầu hoặc phát triển kinh doanh online hiệu quả.</p>',
                'category' => 'Sách',
                'tags' => array('sách', 'kinh doanh', 'online')
            ),

            // 11. WebPage Schema
            'webpage' => array(
                'title' => 'Trang Chủ - Dịch Vụ SEO Chuyên Nghiệp',
                'content' => '[kata_webpage name="SEO Services Vietnam - Trang Chủ" description="Dịch vụ SEO chuyên nghiệp giúp website lên TOP Google, tăng traffic và doanh số bán hàng hiệu quả" url="https://seoservices.vn" primary_image="https://seoservices.vn/images/hero.jpg" breadcrumb="Trang chủ" last_reviewed="2024-10-04" specialty="SEO Services" audience="Small Business Owners"]

<h1>🚀 Dịch Vụ SEO Chuyên Nghiệp #1 Việt Nam</h1>

<p>Giúp website của bạn lên TOP Google, tăng traffic organic và doanh số bán hàng với dịch vụ SEO chuyên nghiệp.</p>',
                'category' => 'Dịch Vụ',
                'tags' => array('SEO', 'dịch vụ', 'website')
            ),

            // 12. Organization Schema
            'organization' => array(
                'title' => 'Giới Thiệu Công Ty Digital Marketing ABC',
                'content' => '[kata_organization name="Digital Marketing ABC Company" type="Corporation" description="Công ty chuyên cung cấp dịch vụ Digital Marketing, SEO, Social Media Marketing cho doanh nghiệp Việt Nam" url="https://dmabc.vn" logo="https://dmABC.vn/logo.png" address="123 Nguyễn Huệ, Q1, TP.HCM" phone="028-1234-5678" email="info@dmABC.vn" founding_date="2020-01-15" employees="50-100" industry="Digital Marketing" services="SEO|SEM|Social Media|Content Marketing"]

<h2>🏢 Digital Marketing ABC - Đối Tác Tin Cậy</h2>

<p>Với 5 năm kinh nghiệm, chúng tôi đã giúp hơn 500 doanh nghiệp phát triển online thành công.</p>',
                'category' => 'Công Ty',
                'tags' => array('công ty', 'digital marketing', 'dịch vụ')
            ),

            // 13. Person Schema
            'person' => array(
                'title' => 'CEO Nguyễn Minh Anh - Chuyên Gia Digital Marketing',
                'content' => '[kata_person name="Nguyễn Minh Anh" job_title="CEO & Digital Marketing Expert" company="Digital Marketing ABC" description="Chuyên gia Digital Marketing với 10+ năm kinh nghiệm, từng làm việc tại Google APAC và các agency hàng đầu" url="https://nguyenminhanh.com" image="https://nguyenminhanh.com/avatar.jpg" email="minh@dmABC.vn" address="TP.HCM, Việt Nam" birth_date="1985-03-15" nationality="Vietnamese" skills="SEO|SEM|Social Media Marketing|Growth Hacking"]

<h2>👨‍💼 Nguyễn Minh Anh - Digital Marketing Expert</h2>

<p>CEO và founder của Digital Marketing ABC, một trong những chuyên gia Digital Marketing hàng đầu Việt Nam.</p>',
                'category' => 'Chuyên Gia',
                'tags' => array('CEO', 'chuyên gia', 'digital marketing')
            ),

            // 14. LocalBusiness Schema
            'localbusiness' => array(
                'title' => 'Nhà Hàng Phở Hà Nội - Quận 1',
                'content' => '[kata_localbusiness name="Nhà Hàng Phở Hà Nội" type="Restaurant" description="Nhà hàng phở bò Hà Nội authentic, phục vụ từ 1995 với hương vị truyền thống" address="456 Lê Lợi, Q1, TP.HCM" phone="028-3827-5555" website="https://phohanoi.vn" email="info@phohanoi.vn" opening_hours="Mo-Su 06:00-22:00" price_range="$$" cuisine="Vietnamese" accepts_reservations="yes" delivery="yes" takeout="yes" rating_value="4.5" rating_count="1250" image="https://phohanoi.vn/restaurant.jpg"]

<h2>🍜 Nhà Hàng Phở Hà Nội - Hương Vị Truyền Thống</h2>

<p>Phục vụ món phở bò Hà Nội chuẩn vị từ năm 1995, được yêu thích bởi hàng ngàn thực khách mỗi ngày.</p>',
                'category' => 'Nhà Hàng',
                'tags' => array('nhà hàng', 'phở', 'Hà Nội')
            ),

            // 15. FAQ Schema
            'faq' => array(
                'title' => 'Câu Hỏi Thường Gặp Về SEO',
                'content' => '[kata_faq questions="SEO là gì?|Tại sao cần làm SEO?|SEO mất bao lâu có hiệu quả?|Chi phí SEO là bao nhiêu?|Có nên thuê agency SEO?" answers="SEO (Search Engine Optimization) là quá trình tối ưu website để tăng thứ hạng trên kết quả tìm kiếm Google|SEO giúp website tăng traffic organic miễn phí, tiếp cận đúng khách hàng tiềm năng và tăng doanh số bán hàng|SEO thường mất 3-6 tháng để có hiệu quả rõ rệt, tùy thuộc vào độ c经争 của từ khóa và chất lượng website|Chi phí SEO dao động từ 5-50 triệu/tháng tùy theo quy mô dự án và mục tiêu kinh doanh|Nên thuê agency SEO uy tín có kinh nghiệm để đảm bảo hiệu quả và tránh rủi ro bị phạt"]

<h2>❓ Câu Hỏi Thường Gặp Về SEO</h2>

<p>Tổng hợp những câu hỏi phổ biến nhất về SEO và câu trả lời chi tiết từ chuyên gia.</p>',
                'category' => 'FAQ',
                'tags' => array('FAQ', 'SEO', 'câu hỏi')
            ),

            // 16. JobPosting Schema
            'jobposting' => array(
                'title' => 'Tuyển Dụng SEO Specialist - Lương 15-25 Triệu',
                'content' => '[kata_jobposting title="SEO Specialist" company="Digital Marketing ABC" location="TP.HCM" employment_type="FULL_TIME" salary_min="15000000" salary_max="25000000" currency="VND" description="Tìm kiếm SEO Specialist có kinh nghiệm để join team marketing của chúng tôi" requirements="2+ năm kinh nghiệm SEO|Thành thạo Google Analytics, Search Console|Kinh nghiệm content marketing|Kỹ năng phân tích dữ liệu tốt" benefits="Lương cạnh tranh|Bonus theo KPI|Bảo hiểm full|Team building hàng quý" posted_date="2024-10-01" valid_through="2024-11-30" contact_email="hr@dmABC.vn"]

<h2>💼 Tuyển Dụng SEO Specialist</h2>

<p>Cơ hội tuyệt vời để phát triển sự nghiệp trong lĩnh vực SEO tại một trong những agency hàng đầu Việt Nam.</p>',
                'category' => 'Tuyển Dụng',
                'tags' => array('tuyển dụng', 'SEO', 'việc làm')
            ),

            // 17. Service Schema
            'service' => array(
                'title' => 'Dịch Vụ Thiết Kế Website Chuyên Nghiệp',
                'content' => '[kata_service name="Thiết Kế Website Chuyên Nghiệp" provider="WebDesign Pro" type="WebDesign" description="Dịch vụ thiết kế website responsive, SEO-friendly với UX/UI đẹp mắt và chuyển đổi cao" area_served="Việt Nam" price_range="10000000-50000000" currency="VND" duration="P4W" category="Web Development" features="Responsive Design|SEO Optimized|Fast Loading|Mobile Friendly|CMS Integration" guarantee="6 tháng bảo hành miễn phí" contact_phone="0901-234-567"]

<h2>🎨 Thiết Kế Website Chuyên Nghiệp</h2>

<p>Tạo ra những website đẹp mắt, chuyên nghiệp và hiệu quả chuyển đổi khách hàng cho doanh nghiệp của bạn.</p>',
                'category' => 'Dịch Vụ',
                'tags' => array('thiết kế web', 'website', 'dịch vụ')
            ),

            // 18. Vehicle Schema
            'vehicle' => array(
                'title' => 'Honda City 2025 - Sedan Hạng B Đáng Mua',
                'content' => '[kata_vehicle name="Honda City 2025" brand="Honda" model="City" year="2025" type="Sedan" fuel_type="Gasoline" engine="1.5L i-VTEC" transmission="CVT" doors="4" seats="5" price="599000000" currency="VND" color="White Pearl" mileage="0" condition="New" description="Sedan hạng B với thiết kế hiện đại, động cơ tiết kiệm nhiên liệu và trang bị an toàn cao cấp" features="Honda SENSING|LED Headlights|Touchscreen 8 inch|Cruise Control|6 Airbags"]

<h2>🚗 Honda City 2025 - Lựa Chọn Thông Minh</h2>

<p>Honda City 2025 với nhiều nâng cấp đáng kể, xứng đáng là lựa chọn hàng đầu trong phân khúc sedan hạng B.</p>',
                'category' => 'Ô Tô',
                'tags' => array('Honda City', 'sedan', 'ô tô')
            ),

            // 19. RealEstate Schema
            'realestate' => array(
                'title' => 'Bán Căn Hộ Vinhomes Central Park - 2PN View Sông',
                'content' => '[kata_realestate name="Căn Hộ Vinhomes Central Park 2PN" type="Apartment" price="7500000000" currency="VND" address="208 Nguyễn Hữu Cảnh, Bình Thạnh, TP.HCM" bedrooms="2" bathrooms="2" area="75" area_unit="m2" floor="15" total_floors="40" year_built="2020" description="Căn hộ cao cấp view sông Sài Gòn, full nội thất, tiện ích đầy đủ" amenities="Hồ bơi|Gym|Spa|Công viên|Trường học|Bệnh viện" direction="Đông Nam" legal="Sổ hồng riêng" contact_phone="0912-345-678"]

<h2>🏠 Căn Hộ Vinhomes Central Park - Đẳng Cấp Thượng Lưu</h2>

<p>Căn hộ 2 phòng ngủ với view sông tuyệt đẹp, nằm tại vị trí vàng quận Bình Thạnh, đầy đủ tiện ích cao cấp.</p>',
                'category' => 'Bất Động Sản',
                'tags' => array('căn hộ', 'Vinhomes', 'bán nhà')
            ),

            // 20. Restaurant Schema
            'restaurant' => array(
                'title' => 'Nhà Hàng Sushi Tokyo - Quận 3',
                'content' => '[kata_restaurant name="Sushi Tokyo Restaurant" cuisine="Japanese" address="789 Võ Văn Tần, Q3, TP.HCM" phone="028-3930-7777" website="https://sushitokyo.vn" price_range="$$$" opening_hours="Mo-Su 11:00-23:00" accepts_reservations="yes" delivery="yes" description="Nhà hàng sushi Nhật Bản authentic với đầu bếp người Nhật và nguyên liệu tươi sống nhập khẩu" specialties="Sashimi|Sushi|Ramen|Tempura|Sake" atmosphere="Elegant" rating_value="4.6" rating_count="890" image="https://sushitokyo.vn/restaurant.jpg"]

<h2>🍣 Sushi Tokyo - Tinh Hoa Ẩm Thực Nhật Bản</h2>

<p>Trải nghiệm hương vị sushi authentic tại nhà hàng Nhật Bản cao cấp với không gian elegant và dịch vụ chuyên nghiệp.</p>',
                'category' => 'Nhà Hàng',
                'tags' => array('sushi', 'nhật bản', 'nhà hàng')
            ),

            // 21. MedicalOrganization Schema
            'medicalorganization' => array(
                'title' => 'Bệnh Viện Đa Khoa Thành Đô',
                'content' => '[kata_medicalorganization name="Bệnh Viện Đa Khoa Thành Đô" type="Hospital" description="Bệnh viện đa khoa hàng đầu với đội ngũ bác sĩ giỏi và trang thiết bị hiện đại" address="321 Cách Mạng Tháng Tám, Q10, TP.HCM" phone="028-3865-4321" website="https://bvthanhdo.vn" specialties="Tim mạch|Thần kinh|Ung bướu|Nhi khoa|Sản phụ khoa" services="Khám bệnh|Cấp cứu 24/7|Phẫu thuật|Xét nghiệm|Chẩn đoán hình ảnh" insurance_accepted="BHYT|Bảo hiểm tư nhân" opening_hours="24/7" rating_value="4.3" rating_count="567"]

<h2>🏥 Bệnh Viện Đa Khoa Thành Đô</h2>

<p>Cung cấp dịch vụ y tế chất lượng cao với đội ngũ chuyên gia đầu ngành và công nghệ y tế tiên tiến.</p>',
                'category' => 'Y Tế',
                'tags' => array('bệnh viện', 'y tế', 'sức khỏe')
            ),

            // 22. CreativeWork Schema
            'creativework' => array(
                'title' => 'Artwork "Hà Nội Mùa Thu" - Tranh Sơn Dầu',
                'content' => '[kata_creativework name="Hà Nội Mùa Thu" creator="Họa Sĩ Nguyễn Văn Nghệ" type="Painting" description="Bức tranh sơn dầu miêu tả vẻ đẹp thơ mộng của Hà Nội trong mùa lá vàng rơi" medium="Oil on Canvas" dimensions="80x60cm" creation_date="2024-09-15" genre="Landscape" style="Impressionism" price="15000000" currency="VND" copyright="© 2024 Nguyễn Văn Nghệ" exhibition="Triển Lãm Mùa Thu 2024" location="Bảo Tàng Mỹ Thuật TP.HCM"]

<h2>🎨 "Hà Nội Mùa Thu" - Tác Phẩm Nghệ Thuật</h2>

<p>Bức tranh sơn dầu tuyệt đẹp thể hiện nét đẹp đặc trưng của thủ đô Hà Nội trong mùa thu lá vàng.</p>',
                'category' => 'Nghệ Thuật',
                'tags' => array('tranh', 'nghệ thuật', 'Hà Nội')
            ),

            // 23. VideoObject Schema
            'videoobject' => array(
                'title' => 'Video: Hướng Dẫn SEO Từ A-Z Cho Người Mới',
                'content' => '[kata_videoobject name="SEO Từ A-Z Cho Người Mới Bắt Đầu" description="Video hướng dẫn chi tiết cách làm SEO từ cơ bản đến nâng cao, phù hợp cho người mới bắt đầu" duration="PT45M30S" upload_date="2024-10-01" thumbnail="https://youtube.com/thumbnail-seo-guide.jpg" embed_url="https://youtube.com/embed/seo-guide-123" content_url="https://youtube.com/watch?v=seo-guide-123" creator="SEO Expert VN" publisher="YouTube" category="Education" language="Vietnamese" quality="HD" view_count="15670" like_count="892" comment_count="156"]

<h2>📹 Video: SEO Từ A-Z Cho Người Mới</h2>

<p>Video tutorial chi tiết và dễ hiểu giúp bạn nắm vững kiến thức SEO từ cơ bản đến nâng cao.</p>',
                'category' => 'Video',
                'tags' => array('video', 'SEO', 'hướng dẫn')
            ),

            // 24. NewsArticle Schema
            'newsarticle' => array(
                'title' => 'Tin Tức: Google Cập Nhật Thuật Toán Core Tháng 10/2024',
                'content' => '[kata_newsarticle headline="Google Phát Hành Core Algorithm Update Tháng 10/2024" description="Google vừa công bố bản cập nhật thuật toán core mới nhất, tác động đến thứ hạng tìm kiếm của nhiều website" author="Biên Tập Viên SEO News" publisher="SEO Vietnam Magazine" publication_date="2024-10-04T08:30:00" location="TP.HCM" category="Technology" keywords="Google algorithm, SEO, core update, search ranking" image="https://seonews.vn/google-update-oct2024.jpg"]

<h2>📰 Google Core Algorithm Update Tháng 10/2024</h2>

<p>Thông tin mới nhất về bản cập nhật thuật toán của Google và những tác động đến community SEO Việt Nam.</p>',
                'category' => 'Tin Tức',
                'tags' => array('tin tức', 'Google', 'SEO')
            ),

            // 25. BlogPosting Schema
            'blogposting' => array(
                'title' => 'Blog: 10 Xu Hướng Digital Marketing 2025',
                'content' => '[kata_blogposting headline="10 Xu Hướng Digital Marketing Không Thể Bỏ Qua Năm 2025" description="Phân tích chi tiết 10 xu hướng Digital Marketing sẽ định hình ngành marketing trong năm 2025" author="Marketing Expert" publisher="Digital Marketing Blog" publication_date="2024-10-01" category="Marketing" tags="digital marketing, trends, 2025, marketing strategy" word_count="2500" reading_time="PT10M" comment_count="45" share_count="230"]

<h2>📝 10 Xu Hướng Digital Marketing 2025</h2>

<p>Cùng khám phá những xu hướng marketing sẽ thay đổi cách chúng ta tiếp cận khách hàng trong năm tới.</p>',
                'category' => 'Blog',
                'tags' => array('blog', 'marketing trends', '2025')
            ),

            // 26. WebSite Schema
            'website' => array(
                'title' => 'Website Chính Thức - KATA SEO Manager',
                'content' => '[kata_website name="KATA SEO Manager Official Website" url="https://kata-seo.com" description="Plugin WordPress chuyên nghiệp cho việc tối ưu SEO và Schema Markup, giúp website đạt thứ hạng cao trên Google" publisher="KATA Channel" category="Software" language="Vietnamese" search_action="Search KATA SEO" search_url="https://kata-seo.com/search?q={search_term_string}" about="WordPress SEO Plugin" keywords="WordPress, SEO, Schema Markup, Plugin"]

<h2>🌐 KATA SEO Manager - Website Chính Thức</h2>

<p>Trang chủ chính thức của plugin WordPress KATA SEO Manager - công cụ SEO và Schema Markup hàng đầu.</p>',
                'category' => 'Website',
                'tags' => array('website', 'KATA SEO', 'plugin')
            ),

            // 27. BreadcrumbList Schema
            'breadcrumblist' => array(
                'title' => 'Breadcrumb: Trang Chủ > Blog > SEO Tips',
                'content' => '[kata_breadcrumblist items="Trang Chủ>https://example.com|Blog>https://example.com/blog|SEO Tips>https://example.com/blog/seo-tips|Bài Viết Hiện Tại>#"]

<h2>🧭 Điều Hướng Breadcrumb</h2>

<p>Breadcrumb giúp người dùng và search engine hiểu rõ cấu trúc và vị trí của trang web trong website.</p>',
                'category' => 'Navigation',
                'tags' => array('breadcrumb', 'navigation', 'SEO')
            )
            
        );
    }
    
    public function create_demo_posts() {
        echo "🚀 Bắt đầu tạo demo posts cho KATA SEO Manager...\n\n";
        
        $created_count = 0;
        $total_count = count($this->demo_posts);
        
        foreach ($this->demo_posts as $schema_type => $post_data) {
            echo "📝 Tạo bài viết demo cho schema: {$schema_type}...\n";
            
            $post_args = array(
                'post_title'    => $post_data['title'],
                'post_content'  => $post_data['content'],
                'post_status'   => 'publish',
                'post_type'     => 'post',
                'post_category' => array($this->get_or_create_category($post_data['category']))
            );
            
            $post_id = wp_insert_post($post_args);
            
            if ($post_id && !is_wp_error($post_id)) {
                // Add tags
                if (isset($post_data['tags'])) {
                    wp_set_post_tags($post_id, $post_data['tags']);
                }
                
                // Add custom meta for tracking
                update_post_meta($post_id, '_kata_seo_demo_post', true);
                update_post_meta($post_id, '_kata_seo_schema_type', $schema_type);
                update_post_meta($post_id, '_kata_seo_created_at', current_time('mysql'));
                
                $created_count++;
                echo "✅ Tạo thành công: {$post_data['title']} (ID: {$post_id})\n";
            } else {
                echo "❌ Lỗi tạo bài viết: {$post_data['title']}\n";
            }
            
            // Sleep để tránh overload
            sleep(1);
        }
        
        echo "\n🎉 Hoàn thành! Đã tạo {$created_count}/{$total_count} bài viết demo.\n";
        echo "📊 Kiểm tra trong Admin → Posts để xem các bài viết.\n";
        echo "📈 Kiểm tra KATA SEO Manager Dashboard để xem statistics.\n\n";
        
        return $created_count;
    }
    
    private function get_or_create_category($category_name) {
        $category = get_category_by_slug(sanitize_title($category_name));
        
        if (!$category) {
            $category_data = wp_insert_term($category_name, 'category');
            if (!is_wp_error($category_data)) {
                return $category_data['term_id'];
            }
            return 1; // Default category if error
        }
        
        return $category->term_id;
    }
    
    public function cleanup_demo_posts() {
        echo "🧹 Xóa các bài viết demo...\n";
        
        $demo_posts = get_posts(array(
            'posts_per_page' => -1,
            'meta_key' => '_kata_seo_demo_post',
            'meta_value' => true,
            'post_status' => 'any'
        ));
        
        $deleted_count = 0;
        foreach ($demo_posts as $post) {
            if (wp_delete_post($post->ID, true)) {
                $deleted_count++;
                echo "🗑️ Đã xóa: {$post->post_title}\n";
            }
        }
        
        echo "✅ Đã xóa {$deleted_count} bài viết demo.\n";
        return $deleted_count;
    }
}

// Command line interface
if (php_sapi_name() === 'cli') {
    $creator = new KATA_Demo_Posts_Creator();
    
    $command = isset($argv[1]) ? $argv[1] : 'create';
    
    switch ($command) {
        case 'create':
            $creator->create_demo_posts();
            break;
            
        case 'cleanup':
            $creator->cleanup_demo_posts();
            break;
            
        default:
            echo "Usage: php create-demo-posts.php [create|cleanup]\n";
            echo "  create  - Tạo các bài viết demo\n";
            echo "  cleanup - Xóa các bài viết demo\n";
            break;
    }
} else {
    // Web interface
    $creator = new KATA_Demo_Posts_Creator();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if ($action === 'create') {
        $count = $creator->create_demo_posts();
        echo "<h2>✅ Đã tạo {$count} bài viết demo thành công!</h2>";
        echo "<p><a href='/wp-admin/edit.php'>Xem bài viết</a> | <a href='/wp-admin/admin.php?page=kata-seo-manager'>KATA SEO Dashboard</a></p>";
    } elseif ($action === 'cleanup') {
        $count = $creator->cleanup_demo_posts();
        echo "<h2>🗑️ Đã xóa {$count} bài viết demo!</h2>";
    } else {
        echo "<h2>KATA SEO Manager Demo Posts Creator</h2>";
        echo "<p><a href='?action=create' style='background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Tạo Demo Posts</a></p>";
        echo "<p><a href='?action=cleanup' style='background: #dc3232; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Xóa Demo Posts</a></p>";
    }
}
?>