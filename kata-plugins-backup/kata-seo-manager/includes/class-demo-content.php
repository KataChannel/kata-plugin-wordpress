<?php
/**
 * Demo Content Generator
 * 
 * Generate demo content with all features for skincare topic
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Demo_Content {
    
    /**
     * Generate all demo content
     */
    public static function generate_all() {
        global $wpdb;
        
        $results = array(
            'success' => false,
            'message' => '',
            'data' => array()
        );
        
        try {
            // Verify required tables exist
            $required_tables = array(
                $wpdb->prefix . 'kata_polls',
                $wpdb->prefix . 'kata_seo_quizzes',
                $wpdb->prefix . 'kata_wheels',
                $wpdb->prefix . 'kata_user_interactions'
            );
            
            foreach ($required_tables as $table) {
                if ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
                    throw new Exception("Bảng $table không tồn tại. Vui lòng kích hoạt lại plugin.");
                }
            }
            
            // 1. Create main post with 26 schema shortcodes
            $post_id = self::create_skincare_guide_post();
            $results['data']['post_id'] = $post_id;
            $results['data']['post_url'] = $post_id ? get_permalink($post_id) : null;
            
            // 2. Create 3 polls
            $polls = self::create_polls();
            $results['data']['polls'] = $polls;
            
            // 3. Create 3 quizzes
            $quizzes = self::create_quizzes();
            $results['data']['quizzes'] = $quizzes;
            
            // 4. Create 3 wheels
            $wheels = self::create_wheels();
            $results['data']['wheels'] = $wheels;
            
            // 5. Create 3 user interactions
            $interactions = self::create_user_interactions();
            $results['data']['interactions'] = $interactions;
            
            $results['success'] = true;
            $results['message'] = 'Đã tạo thành công tất cả dữ liệu mẫu về chăm sóc da!';
            
        } catch (Exception $e) {
            $results['success'] = false;
            $results['message'] = 'Lỗi: ' . $e->getMessage();
        }
        
        return $results;
    }
    
    /**
     * Create main skincare guide post with all 26 schema types
     */
    private static function create_skincare_guide_post() {
        $content = self::get_complete_skincare_content();
        
        $post_data = array(
            'post_title'    => '🌟 Hướng Dẫn Toàn Diện: Chăm Sóc Da Khỏe Đẹp Từ A-Z',
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_author'   => get_current_user_id(),
            'post_category' => array(1), // Uncategorized
            'meta_input'    => array(
                '_kata_seo_demo_content' => true,
                '_kata_seo_demo_date' => current_time('mysql')
            )
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            throw new Exception('Không thể tạo bài viết: ' . $post_id->get_error_message());
        }
        
        return $post_id;
    }
    
    /**
     * Get complete skincare content with all 26 schema shortcodes
     */
    private static function get_complete_skincare_content() {
        $content = '
<h2>Giới Thiệu Về Chăm Sóc Da</h2>

<p>Chào mừng bạn đến với hướng dẫn toàn diện về chăm sóc da! Bài viết này sẽ giúp bạn hiểu rõ từng bước chăm sóc da đúng cách để có làn da khỏe đẹp tự nhiên.</p>

[kata_article 
    headline="Hướng Dẫn Chăm Sóc Da Khỏe Đẹp" 
    author="Chuyên Gia Da Liễu" 
    date_published="2025-10-06"
    description="Hướng dẫn chi tiết về quy trình chăm sóc da đúng cách với các bước cơ bản và nâng cao"]

<h2>📍 Breadcrumb Navigation</h2>

[kata_breadcrumb items="Trang chủ > Blog > Làm đẹp > Chăm sóc da"]

<h2>🎠 Các Sản Phẩm Chăm Sóc Da Được Yêu Thích</h2>

[kata_carousel 
    items="Serum Vitamin C,Kem Chống Nắng SPF50,Toner Cân Bằng Da,Mặt Nạ Dưỡng Ẩm"
    images="https://via.placeholder.com/300x300"]

<h2>📚 Khóa Học Online: Chăm Sóc Da Chuyên Sâu</h2>

[kata_course 
    name="Khóa Học Chăm Sóc Da Từ Cơ Bản Đến Nâng Cao"
    description="Học cách chăm sóc da đúng cách từ chuyên gia"
    provider="KATA Beauty Academy"
    price="1,500,000 VNĐ"]

<h2>📊 Dataset: Nghiên Cứu Về Da</h2>

[kata_dataset 
    name="Dữ liệu Nghiên Cứu Loại Da Người Việt"
    description="Phân tích 1000 mẫu da người Việt Nam"
    creator="Viện Nghiên Cứu Da Liễu Việt Nam"]

<h2>💬 Diễn Đàn Thảo Luận</h2>

[kata_forum 
    name="Cộng Đồng Chăm Sóc Da Việt Nam"
    description="Nơi chia sẻ kinh nghiệm và giải đáp thắc mắc về chăm sóc da"
    url="#forum"]

<h2>🎓 Hỏi Đáp Giáo Dục</h2>

[kata_eduqa 
    question="Làm thế nào để chọn kem dưỡng phù hợp với da?"
    answer="Cần xác định loại da (khô, dầu, hỗn hợp, nhạy cảm) và chọn sản phẩm phù hợp"]

<h2>⭐ Đánh Giá Nhà Tuyển Dụng Ngành Làm Đẹp</h2>

[kata_employer_rating 
    employer="KATA Beauty Clinic"
    rating="4.8"
    review_count="150"]

<h2>📅 Sự Kiện: Workshop Chăm Sóc Da</h2>

[kata_event 
    name="Workshop: Chăm Sóc Da Mùa Hè"
    start_date="2025-11-15 14:00"
    location="KATA Beauty Center, Hà Nội"
    description="Học cách bảo vệ da khỏi tác hại của tia UV"]

<h2>❓ Câu Hỏi Thường Gặp</h2>

[kata_faq]
[faq_item question="Nên rửa mặt mấy lần một ngày?" answer="Nên rửa mặt 2 lần/ngày: sáng và tối để làm sạch da mà không làm khô da."]
[faq_item question="Serum có cần thiết không?" answer="Serum cung cấp dưỡng chất cô đặc, giúp da hấp thụ tốt hơn kem dưỡng thông thường."]
[faq_item question="Kem chống nắng có cần thiết trong nhà?" answer="Có, vì tia UV vẫn xuyên qua cửa kính và gây hại cho da."]
[/kata_faq]

<h2>📖 Hướng Dẫn Từng Bước</h2>

[kata_howto 
    name="Cách Sử Dụng Serum Vitamin C Đúng Cách"
    time="PT10M"
    steps="Rửa mặt sạch,Thoa toner,Nhỏ 3-4 giọt serum lên tay,Massage nhẹ nhàng lên mặt,Đợi 2-3 phút,Thoa kem dưỡng ẩm"]

<h2>🖼️ Metadata Hình Ảnh</h2>

[kata_image_metadata 
    caption="Quy trình chăm sóc da 10 bước"
    license="Creative Commons"
    credit="KATA Beauty"]

<h2>💼 Tuyển Dụng: Chuyên Viên Chăm Sóc Da</h2>

[kata_job_posting 
    title="Chuyên Viên Tư Vấn Chăm Sóc Da"
    company="KATA Beauty Spa"
    location="Hà Nội"
    salary="15-25 triệu VNĐ"
    description="Tư vấn và chăm sóc da cho khách hàng"]

<h2>🏢 Spa Chăm Sóc Da Địa Phương</h2>

[kata_local_business 
    name="KATA Beauty & Spa"
    address="123 Phố Huế, Hai Bà Trưng, Hà Nội"
    phone="024-1234-5678"
    rating="4.9"
    price_range="$$"]

<h2>🔢 Giải Toán: Tính Lượng Sản Phẩm Cần Dùng</h2>

[kata_math_solver 
    problem="Một lọ serum 30ml dùng 3 giọt/ngày thì dùng được bao lâu?"
    solution="30ml ≈ 600 giọt. 600 ÷ 3 = 200 ngày (~6.5 tháng)"]

<h2>🎯 Bài Tập Thực Hành</h2>

[kata_practice_problem 
    name="Xác Định Loại Da Của Bạn"
    question="Sau khi rửa mặt 30 phút, da bạn có cảm giác như thế nào?"
    options="Căng và khô,Dầu ở vùng T,Thoải mái,Ngứa và đỏ"]

<h2>⭐ Đánh Giá Sản Phẩm</h2>

[kata_product 
    name="Serum Vitamin C 20%"
    brand="KATA Skincare"
    price="450,000 VNĐ"
    rating="4.7"
    availability="Còn hàng"]

<h2>👤 Hồ Sơ Chuyên Gia</h2>

[kata_profile_page 
    name="Bác Sĩ Nguyễn Thị Lan"
    job_title="Bác Sĩ Da Liễu"
    description="15 năm kinh nghiệm điều trị và chăm sóc da"]

<h2>🍳 Công Thức Mặt Nạ Tự Nhiên</h2>

[kata_recipe 
    name="Mặt Nạ Mật Ong Chanh Dưỡng Da"
    time="15"
    ingredients="2 thìa mật ong,1/2 thìa nước chanh,1 thìa sữa chua"
    instructions="Trộn đều các nguyên liệu,Thoa đều lên mặt,Để 10-15 phút,Rửa sạch với nước ấm"]

<h2>⭐ Review Sản Phẩm Chi Tiết</h2>

[kata_review 
    item_name="Kem Chống Nắng KATA SPF50"
    rating="5"
    author="Nguyễn Văn A"
    review_body="Sản phẩm rất tốt, không gây bết dính, phù hợp da dầu"]

<h2>🔗 Sitelinks Quan Trọng</h2>

[kata_sitelinks 
    links="Sản phẩm mới,Khuyến mãi,Hướng dẫn,Liên hệ"
    urls="#products,#sales,#guide,#contact"]

<h2>🔊 Nội Dung Đọc To</h2>

[kata_speakable 
    text="3 bước chăm sóc da cơ bản: Làm sạch, Cân bằng, Dưỡng ẩm"]

<h2>🎥 Video Hướng Dẫn</h2>

[kata_video 
    name="Quy Trình Chăm Sóc Da Buổi Sáng"
    description="Hướng dẫn chi tiết 7 bước chăm sóc da buổi sáng"
    thumbnail="https://via.placeholder.com/640x360"
    duration="PT8M30S"
    upload_date="2025-10-06"]

<h2>📄 Thông Tin Trang Web</h2>

[kata_webpage 
    name="Hướng Dẫn Chăm Sóc Da - KATA Beauty"
    description="Trang hướng dẫn toàn diện về chăm sóc da"
    url="current"]

<hr>

<h2>🎮 Tương Tác & Trải Nghiệm</h2>

<h3>📊 Khảo Sát: Loại Da Của Bạn</h3>
[kata_poll id="skincare_poll_1"]

<h3>🧪 Quiz: Kiểm Tra Kiến Thức Chăm Sóc Da</h3>
[kata_quiz id="skincare_quiz_1"]

<h3>🎡 Vòng Quay May Mắn: Nhận Quà Tặng</h3>
[kata_wheel id="skincare_wheel_1"]

<h3>💬 Chia Sẻ Trải Nghiệm</h3>
<p>Hãy để lại đánh giá và chia sẻ kinh nghiệm chăm sóc da của bạn!</p>

<hr>

<h2>📝 Kết Luận</h2>

<p>Chăm sóc da là một quá trình kiên trì và đều đặn. Hy vọng hướng dẫn này sẽ giúp bạn có làn da khỏe đẹp như mong muốn!</p>

<p><strong>Lưu ý:</strong> Mọi thông tin trong bài viết chỉ mang tính chất tham khảo. Hãy tham khảo ý kiến bác sĩ da liễu nếu bạn có vấn đề về da.</p>
';
        
        return $content;
    }
    
    /**
     * Create 3 polls about skincare
     */
    private static function create_polls() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_polls';
        $polls = array();
        
        $poll_data = array(
            array(
                'title' => 'Loại Da Của Bạn Là Gì?',
                'description' => 'Khảo sát để hiểu rõ hơn về loại da của các bạn và đưa ra lời khuyên phù hợp.',
                'options' => json_encode(array(
                    'Da khô' => 0,
                    'Da dầu' => 0,
                    'Da hỗn hợp' => 0,
                    'Da nhạy cảm' => 0,
                    'Da thường' => 0
                )),
                'allow_multiple' => 0,
                'show_results' => 1,
                'require_login' => 0,
                'active' => 1,
                'total_votes' => 0
            ),
            array(
                'title' => 'Bạn Thường Sử Dụng Kem Chống Nắng Bao Nhiêu Lần/Ngày?',
                'description' => 'Tìm hiểu thói quen sử dụng kem chống nắng của mọi người để nâng cao nhận thức về bảo vệ da.',
                'options' => json_encode(array(
                    'Không dùng' => 0,
                    '1 lần' => 0,
                    '2 lần' => 0,
                    '3 lần trở lên' => 0
                )),
                'allow_multiple' => 0,
                'show_results' => 1,
                'require_login' => 0,
                'active' => 1,
                'total_votes' => 0
            ),
            array(
                'title' => 'Thành Phần Nào Bạn Ưu Tiên Trong Sản Phẩm Chăm Sóc Da?',
                'description' => 'Khảo sát về sở thích thành phần skincare để recommend sản phẩm phù hợp.',
                'options' => json_encode(array(
                    'Vitamin C' => 0,
                    'Hyaluronic Acid' => 0,
                    'Niacinamide' => 0,
                    'Retinol' => 0,
                    'AHA/BHA' => 0,
                    'Chiết xuất tự nhiên' => 0
                )),
                'allow_multiple' => 1,
                'show_results' => 1,
                'require_login' => 0,
                'active' => 1,
                'total_votes' => 0
            )
        );
        
        foreach ($poll_data as $poll) {
            $poll['created_at'] = current_time('mysql');
            $poll['updated_at'] = current_time('mysql');
            
            $wpdb->insert($table_name, $poll);
            $polls[] = array(
                'id' => $wpdb->insert_id,
                'title' => $poll['title']
            );
        }
        
        return $polls;
    }
    
    /**
     * Create 3 quizzes about skincare
     */
    private static function create_quizzes() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_seo_quizzes';
        $quizzes = array();
        
        $quiz_data = array(
            array(
                'title' => 'Kiến Thức Cơ Bản Về Chăm Sóc Da',
                'description' => 'Kiểm tra kiến thức của bạn về chăm sóc da cơ bản',
                'questions' => json_encode(array(
                    array(
                        'question' => 'Nên rửa mặt mấy lần một ngày?',
                        'options' => array('1 lần', '2 lần', '3 lần', '4 lần'),
                        'correct' => 1 // Index of correct answer
                    ),
                    array(
                        'question' => 'Thứ tự đúng trong quy trình chăm sóc da là gì?',
                        'options' => array(
                            'Serum - Toner - Kem dưỡng',
                            'Toner - Serum - Kem dưỡng',
                            'Kem dưỡng - Toner - Serum',
                            'Toner - Kem dưỡng - Serum'
                        ),
                        'correct' => 1
                    ),
                    array(
                        'question' => 'SPF là gì?',
                        'options' => array(
                            'Sun Protection Factor',
                            'Skin Protection Formula',
                            'Special Protection Form',
                            'Sun Power Factor'
                        ),
                        'correct' => 0
                    ),
                    array(
                        'question' => 'Khi nào nên thoa kem chống nắng?',
                        'options' => array(
                            'Chỉ khi ra ngoài trời nắng',
                            'Mỗi ngày, kể cả trong nhà',
                            'Chỉ vào mùa hè',
                            'Không cần thiết'
                        ),
                        'correct' => 1
                    ),
                    array(
                        'question' => 'Toner có tác dụng gì?',
                        'options' => array(
                            'Cân bằng độ pH da',
                            'Tẩy trang',
                            'Chống nắng',
                            'Tẩy tế bào chết'
                        ),
                        'correct' => 0
                    )
                )),
                'time_limit' => 300, // 5 minutes
                'pass_score' => 60,
                'active' => 1
            ),
            array(
                'title' => 'Nhận Biết Thành Phần Trong Mỹ Phẩm',
                'description' => 'Test kiến thức về các thành phần quan trọng',
                'questions' => json_encode(array(
                    array(
                        'question' => 'Vitamin C trong skincare có tác dụng gì?',
                        'options' => array(
                            'Dưỡng ẩm',
                            'Làm sáng da và chống oxy hóa',
                            'Tẩy tế bào chết',
                            'Kiểm soát dầu'
                        ),
                        'correct' => 1
                    ),
                    array(
                        'question' => 'Retinol phù hợp với mục đích nào?',
                        'options' => array(
                            'Chống lão hóa',
                            'Dưỡng ẩm',
                            'Làm dịu da',
                            'Chống nắng'
                        ),
                        'correct' => 0
                    ),
                    array(
                        'question' => 'Hyaluronic Acid có khả năng gì?',
                        'options' => array(
                            'Tẩy da chết',
                            'Giữ ẩm cho da',
                            'Làm trắng da',
                            'Chống viêm'
                        ),
                        'correct' => 1
                    ),
                    array(
                        'question' => 'Niacinamide thích hợp cho da nào?',
                        'options' => array(
                            'Chỉ da khô',
                            'Chỉ da dầu',
                            'Mọi loại da',
                            'Chỉ da nhạy cảm'
                        ),
                        'correct' => 2
                    )
                )),
                'time_limit' => 240,
                'pass_score' => 75,
                'active' => 1
            ),
            array(
                'title' => 'Chuyên Sâu: Điều Trị Da Mụn',
                'description' => 'Kiến thức nâng cao về điều trị mụn',
                'questions' => json_encode(array(
                    array(
                        'question' => 'BHA (Beta Hydroxy Acid) là thành phần nào?',
                        'options' => array(
                            'Glycolic Acid',
                            'Salicylic Acid',
                            'Lactic Acid',
                            'Citric Acid'
                        ),
                        'correct' => 1
                    ),
                    array(
                        'question' => 'Nên sử dụng BHA khi nào?',
                        'options' => array(
                            'Buổi sáng',
                            'Buổi tối',
                            'Cả ngày',
                            'Không quan trọng'
                        ),
                        'correct' => 1
                    ),
                    array(
                        'question' => 'Sau khi dùng AHA/BHA cần làm gì?',
                        'options' => array(
                            'Không cần gì thêm',
                            'Bôi kem dưỡng ẩm',
                            'Chống nắng kỹ',
                            'Cả B và C'
                        ),
                        'correct' => 3
                    )
                )),
                'time_limit' => 180,
                'pass_score' => 70,
                'active' => 1
            )
        );
        
        foreach ($quiz_data as $quiz) {
            // Map columns to actual database schema
            $insert_data = array(
                'quiz_title' => $quiz['title'],
                'quiz_data' => json_encode(array(
                    'description' => $quiz['description'],
                    'questions' => json_decode($quiz['questions'], true),
                    'time_limit' => $quiz['time_limit'],
                    'pass_score' => $quiz['pass_score']
                )),
                'total_questions' => count(json_decode($quiz['questions'], true)),
                'is_active' => $quiz['active'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            );
            
            $wpdb->insert($table_name, $insert_data);
            $quizzes[] = array(
                'id' => $wpdb->insert_id,
                'title' => $quiz['title']
            );
        }
        
        return $quizzes;
    }
    
    /**
     * Create 3 wheels about skincare
     */
    private static function create_wheels() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_wheels';
        $wheels = array();
        
        $wheel_data = array(
            array(
                'title' => 'Vòng Quay May Mắn - Nhận Mẫu Thử Miễn Phí',
                'description' => 'Quay để nhận mẫu thử sản phẩm chăm sóc da!',
                'segments' => json_encode(array(
                    array('label' => 'Serum Vitamin C 5ml', 'color' => '#FF6B6B', 'probability' => 15),
                    array('label' => 'Kem Chống Nắng 10ml', 'color' => '#4ECDC4', 'probability' => 20),
                    array('label' => 'Toner 20ml', 'color' => '#45B7D1', 'probability' => 25),
                    array('label' => 'Mặt Nạ Giấy 1 Miếng', 'color' => '#FFA07A', 'probability' => 30),
                    array('label' => 'Voucher 50K', 'color' => '#98D8C8', 'probability' => 5),
                    array('label' => 'Tẩy Tế Bào Chết 15ml', 'color' => '#F7DC6F', 'probability' => 5)
                )),
                'max_spins_per_user' => 1,
                'require_email' => 1,
                'active' => 1
            ),
            array(
                'title' => 'Vòng Quay Giảm Giá - Sale Sốc Hôm Nay',
                'description' => 'Nhận ngay mã giảm giá cho đơn hàng đầu tiên!',
                'segments' => json_encode(array(
                    array('label' => 'Giảm 10%', 'color' => '#E74C3C', 'probability' => 30),
                    array('label' => 'Giảm 15%', 'color' => '#3498DB', 'probability' => 25),
                    array('label' => 'Giảm 20%', 'color' => '#2ECC71', 'probability' => 20),
                    array('label' => 'Giảm 25%', 'color' => '#F39C12', 'probability' => 15),
                    array('label' => 'Giảm 30%', 'color' => '#9B59B6', 'probability' => 8),
                    array('label' => 'Giảm 50%', 'color' => '#E67E22', 'probability' => 2)
                )),
                'max_spins_per_user' => 1,
                'require_email' => 1,
                'active' => 1
            ),
            array(
                'title' => 'Vòng Quay Combo Chăm Sóc Da',
                'description' => 'Quay để nhận combo sản phẩm siêu ưu đãi!',
                'segments' => json_encode(array(
                    array('label' => 'Combo Dưỡng Ẩm', 'color' => '#1ABC9C', 'probability' => 25),
                    array('label' => 'Combo Trị Mụn', 'color' => '#E74C3C', 'probability' => 25),
                    array('label' => 'Combo Làm Sáng', 'color' => '#F1C40F', 'probability' => 20),
                    array('label' => 'Combo Chống Lão Hóa', 'color' => '#9B59B6', 'probability' => 15),
                    array('label' => 'Combo Đặc Biệt', 'color' => '#34495E', 'probability' => 10),
                    array('label' => 'Thử Lại', 'color' => '#95A5A6', 'probability' => 5)
                )),
                'max_spins_per_user' => 2,
                'require_email' => 0,
                'active' => 1
            )
        );
        
        foreach ($wheel_data as $wheel) {
            // Map columns to actual database schema
            $insert_data = array(
                'wheel_title' => $wheel['title'],
                'wheel_description' => $wheel['description'],
                'requirement' => $wheel['require_email'] ? 'email' : 'none',
                'max_spins_per_user' => $wheel['max_spins_per_user'],
                'max_spins_per_day' => 10, // Default value
                'start_date' => current_time('mysql'),
                'end_date' => date('Y-m-d H:i:s', strtotime('+1 year')),
                'status' => $wheel['active'] ? 'active' : 'inactive',
                'total_spins' => 0,
                'total_prizes_won' => 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            );
            
            // Store segments in a separate operation (wheel_data column missing, store in post meta or custom solution)
            $wpdb->insert($table_name, $insert_data);
            
            $wheel_id = $wpdb->insert_id;
            // Store segments data as post meta or in a separate table (for now, skip segments)
            
            $wheels[] = array(
                'id' => $wheel_id,
                'title' => $wheel['title']
            );
        }
        
        return $wheels;
    }
    
    /**
     * Create 3 user interactions
     */
    private static function create_user_interactions() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_user_interactions';
        $interactions = array();
        
        // Sample interactions from different users
        $interaction_data = array(
            array(
                'user_id' => 0,
                'user_name' => 'Nguyễn Thị Hoa',
                'user_email' => 'hoa@example.com',
                'interaction_type' => 'comment',
                'content' => 'Mình đã áp dụng quy trình chăm sóc da 10 bước và thấy da cải thiện rõ rệt sau 2 tuần! Cảm ơn bài viết rất hữu ích.',
                'rating' => 5,
                'post_id' => 0, // Will be updated
                'status' => 'approved'
            ),
            array(
                'user_id' => 0,
                'user_name' => 'Trần Văn Nam',
                'user_email' => 'nam@example.com',
                'interaction_type' => 'review',
                'content' => 'Serum Vitamin C thực sự hiệu quả cho da sạm nám. Sau 1 tháng sử dụng, da sáng hơn hẳn. Recommend!',
                'rating' => 5,
                'product_name' => 'Serum Vitamin C 20%',
                'status' => 'approved'
            ),
            array(
                'user_id' => 0,
                'user_name' => 'Lê Thị Mai',
                'user_email' => 'mai@example.com',
                'interaction_type' => 'feedback',
                'content' => 'Bài viết rất chi tiết và dễ hiểu. Nhưng mình mong có thêm phần về chăm sóc da cho da mụn nữa ạ!',
                'rating' => 4,
                'post_id' => 0,
                'status' => 'approved'
            )
        );
        
        foreach ($interaction_data as $interaction) {
            // Map columns to actual database schema
            $insert_data = array(
                'user_id' => $interaction['user_id'],
                'user_name' => $interaction['user_name'],
                'user_email' => $interaction['user_email'],
                'interaction_type' => $interaction['interaction_type'],
                'content' => $interaction['content'],
                'rating' => $interaction['rating'],
                'post_id' => $interaction['post_id'] ?? 0,
                'target_id' => 0, // Default value
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Demo Content Generator',
                'status' => $interaction['status'],
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            );
            
            // Remove product_name if it exists since the table doesn't have this column
            // Product name info can be included in the content field
            if (isset($interaction['product_name'])) {
                $insert_data['content'] = $interaction['product_name'] . ': ' . $insert_data['content'];
            }
            
            $wpdb->insert($table_name, $insert_data);
            $interactions[] = array(
                'id' => $wpdb->insert_id,
                'type' => $interaction['interaction_type'],
                'user' => $interaction['user_name']
            );
        }
        
        return $interactions;
    }
}
