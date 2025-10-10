<?php
/**
 * Sample Data Generator - Tạo dữ liệu mẫu cho tất cả tính năng
 *
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Sample_Data {
    
    /**
     * Tạo tất cả dữ liệu mẫu
     */
    public static function generate_all_sample_data() {
        // Tạo sample schemas cho 26 loại
        self::create_sample_schemas();
        
        // Tạo 1 post và 1 page với tất cả schemas
        self::create_demo_posts();
        
        // Tạo sample stats
        self::create_sample_stats();
        
        // Tạo sample templates
        self::create_sample_templates();
        
        return true;
    }
    
    /**
     * Tạo 3 schema mẫu cho mỗi loại (26 types x 3 = 78 schemas)
     */
    private static function create_sample_schemas() {
        global $wpdb;
        $table = $wpdb->prefix . 'kata_seo_schemas';
        
        // Danh sách 26 schema types
        $schema_types = array(
            'Article', 'Breadcrumb', 'Carousel', 'Course', 'Dataset',
            'Forum', 'EduQA', 'EmployerRating', 'Event', 'FAQ',
            'HowTo', 'ImageMetadata', 'JobPosting', 'LocalBusiness', 'MathSolver',
            'Movie', 'Organization', 'PracticeProblem', 'Product', 'ProfilePage',
            'Recipe', 'Review', 'Sitelinks', 'Speakable', 'Video', 'WebPage'
        );
        
        foreach ($schema_types as $type) {
            for ($i = 1; $i <= 3; $i++) {
                $schema_data = self::get_sample_schema_data($type, $i);
                
                $wpdb->insert(
                    $table,
                    array(
                        'post_id' => 0, // Temporary, sẽ update sau khi tạo posts
                        'schema_type' => $type,
                        'schema_data' => json_encode($schema_data['data']),
                        'schema_json' => json_encode($schema_data['json']),
                        'is_active' => 1,
                        'created_at' => current_time('mysql'),
                        'updated_at' => current_time('mysql')
                    ),
                    array('%d', '%s', '%s', '%s', '%d', '%s', '%s')
                );
            }
        }
    }
    
    /**
     * Lấy dữ liệu mẫu cho từng schema type
     */
    private static function get_sample_schema_data($type, $index) {
        $samples = array(
            'Article' => array(
                'data' => array(
                    'headline' => "Hướng Dẫn SEO WordPress Toàn Diện $index",
                    'description' => 'Tìm hiểu cách tối ưu hóa SEO cho WordPress với Schema Markup',
                    'author' => 'KATA Channel',
                    'datePublished' => date('Y-m-d'),
                    'image' => 'https://example.com/article-image.jpg'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Article',
                    'headline' => "Hướng Dẫn SEO WordPress Toàn Diện $index",
                    'description' => 'Tìm hiểu cách tối ưu hóa SEO cho WordPress với Schema Markup',
                    'author' => array(
                        '@type' => 'Person',
                        'name' => 'KATA Channel'
                    ),
                    'datePublished' => date('Y-m-d'),
                    'image' => 'https://example.com/article-image.jpg'
                )
            ),
            
            'FAQ' => array(
                'data' => array(
                    'questions' => array(
                        array(
                            'question' => "Schema Markup là gì? (Mẫu $index)",
                            'answer' => 'Schema Markup là mã cấu trúc giúp Google hiểu nội dung website tốt hơn.'
                        ),
                        array(
                            'question' => "Tại sao cần sử dụng Schema?",
                            'answer' => 'Schema giúp website hiển thị rich snippets trên kết quả tìm kiếm, tăng CTR.'
                        )
                    )
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => array(
                        array(
                            '@type' => 'Question',
                            'name' => "Schema Markup là gì? (Mẫu $index)",
                            'acceptedAnswer' => array(
                                '@type' => 'Answer',
                                'text' => 'Schema Markup là mã cấu trúc giúp Google hiểu nội dung website tốt hơn.'
                            )
                        ),
                        array(
                            '@type' => 'Question',
                            'name' => "Tại sao cần sử dụng Schema?",
                            'acceptedAnswer' => array(
                                '@type' => 'Answer',
                                'text' => 'Schema giúp website hiển thị rich snippets trên kết quả tìm kiếm, tăng CTR.'
                            )
                        )
                    )
                )
            ),
            
            'Product' => array(
                'data' => array(
                    'name' => "KATA SEO Plugin Pro $index",
                    'description' => 'Plugin SEO chuyên nghiệp với 26 loại Schema Markup',
                    'price' => '999000',
                    'currency' => 'VND',
                    'brand' => 'KATA Channel',
                    'rating' => '4.8'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Product',
                    'name' => "KATA SEO Plugin Pro $index",
                    'description' => 'Plugin SEO chuyên nghiệp với 26 loại Schema Markup',
                    'offers' => array(
                        '@type' => 'Offer',
                        'price' => '999000',
                        'priceCurrency' => 'VND'
                    ),
                    'brand' => array(
                        '@type' => 'Brand',
                        'name' => 'KATA Channel'
                    ),
                    'aggregateRating' => array(
                        '@type' => 'AggregateRating',
                        'ratingValue' => '4.8',
                        'reviewCount' => '150'
                    )
                )
            ),
            
            'Recipe' => array(
                'data' => array(
                    'name' => "Công Thức SEO Hoàn Hảo $index",
                    'description' => 'Bí quyết tối ưu SEO cho website WordPress',
                    'prepTime' => 'PT30M',
                    'cookTime' => 'PT1H',
                    'totalTime' => 'PT1H30M',
                    'recipeYield' => '1 website hoàn hảo'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Recipe',
                    'name' => "Công Thức SEO Hoàn Hảo $index",
                    'description' => 'Bí quyết tối ưu SEO cho website WordPress',
                    'prepTime' => 'PT30M',
                    'cookTime' => 'PT1H',
                    'totalTime' => 'PT1H30M',
                    'recipeYield' => '1 website hoàn hảo'
                )
            ),
            
            'HowTo' => array(
                'data' => array(
                    'name' => "Cách Cài Đặt Schema Markup $index",
                    'description' => 'Hướng dẫn từng bước cài đặt Schema',
                    'totalTime' => 'PT15M',
                    'steps' => array(
                        'Bước 1: Cài đặt plugin KATA SEO Manager',
                        'Bước 2: Kích hoạt plugin',
                        'Bước 3: Chọn loại schema cần thêm',
                        'Bước 4: Điền thông tin và lưu'
                    )
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'HowTo',
                    'name' => "Cách Cài Đặt Schema Markup $index",
                    'description' => 'Hướng dẫn từng bước cài đặt Schema',
                    'totalTime' => 'PT15M',
                    'step' => array(
                        array('@type' => 'HowToStep', 'text' => 'Bước 1: Cài đặt plugin KATA SEO Manager'),
                        array('@type' => 'HowToStep', 'text' => 'Bước 2: Kích hoạt plugin'),
                        array('@type' => 'HowToStep', 'text' => 'Bước 3: Chọn loại schema cần thêm'),
                        array('@type' => 'HowToStep', 'text' => 'Bước 4: Điền thông tin và lưu')
                    )
                )
            ),
            
            'Event' => array(
                'data' => array(
                    'name' => "Workshop SEO 2025 - Buổi $index",
                    'description' => 'Workshop hướng dẫn SEO chuyên sâu',
                    'startDate' => date('Y-m-d', strtotime('+7 days')),
                    'endDate' => date('Y-m-d', strtotime('+8 days')),
                    'location' => 'Online',
                    'organizer' => 'KATA Channel'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Event',
                    'name' => "Workshop SEO 2025 - Buổi $index",
                    'description' => 'Workshop hướng dẫn SEO chuyên sâu',
                    'startDate' => date('Y-m-d', strtotime('+7 days')),
                    'endDate' => date('Y-m-d', strtotime('+8 days')),
                    'location' => array(
                        '@type' => 'VirtualLocation',
                        'url' => 'https://katachannel.com/workshop'
                    ),
                    'organizer' => array(
                        '@type' => 'Organization',
                        'name' => 'KATA Channel'
                    )
                )
            ),
            
            'Video' => array(
                'data' => array(
                    'name' => "Video Tutorial SEO $index",
                    'description' => 'Học SEO qua video thực hành',
                    'uploadDate' => date('Y-m-d'),
                    'duration' => 'PT15M30S',
                    'thumbnailUrl' => 'https://example.com/thumb.jpg'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'VideoObject',
                    'name' => "Video Tutorial SEO $index",
                    'description' => 'Học SEO qua video thực hành',
                    'uploadDate' => date('Y-m-d'),
                    'duration' => 'PT15M30S',
                    'thumbnailUrl' => 'https://example.com/thumb.jpg'
                )
            ),
            
            'Review' => array(
                'data' => array(
                    'itemReviewed' => "KATA SEO Manager - Review $index",
                    'reviewRating' => '5',
                    'author' => 'Người dùng WordPress',
                    'reviewBody' => 'Plugin tuyệt vời, dễ sử dụng và hiệu quả!'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Review',
                    'itemReviewed' => array(
                        '@type' => 'Product',
                        'name' => "KATA SEO Manager - Review $index"
                    ),
                    'reviewRating' => array(
                        '@type' => 'Rating',
                        'ratingValue' => '5'
                    ),
                    'author' => array(
                        '@type' => 'Person',
                        'name' => 'Người dùng WordPress'
                    ),
                    'reviewBody' => 'Plugin tuyệt vời, dễ sử dụng và hiệu quả!'
                )
            ),
            
            'LocalBusiness' => array(
                'data' => array(
                    'name' => "KATA Digital Agency $index",
                    'description' => 'Công ty SEO & Digital Marketing',
                    'address' => 'Hà Nội, Việt Nam',
                    'telephone' => '0123456789',
                    'priceRange' => '$$'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'LocalBusiness',
                    'name' => "KATA Digital Agency $index",
                    'description' => 'Công ty SEO & Digital Marketing',
                    'address' => array(
                        '@type' => 'PostalAddress',
                        'addressLocality' => 'Hà Nội',
                        'addressCountry' => 'VN'
                    ),
                    'telephone' => '0123456789',
                    'priceRange' => '$$'
                )
            ),
            
            'JobPosting' => array(
                'data' => array(
                    'title' => "SEO Specialist $index",
                    'description' => 'Tuyển chuyên viên SEO có kinh nghiệm',
                    'datePosted' => date('Y-m-d'),
                    'employmentType' => 'FULL_TIME',
                    'hiringOrganization' => 'KATA Channel'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'JobPosting',
                    'title' => "SEO Specialist $index",
                    'description' => 'Tuyển chuyên viên SEO có kinh nghiệm',
                    'datePosted' => date('Y-m-d'),
                    'employmentType' => 'FULL_TIME',
                    'hiringOrganization' => array(
                        '@type' => 'Organization',
                        'name' => 'KATA Channel'
                    )
                )
            ),
            
            'Course' => array(
                'data' => array(
                    'name' => "Khóa Học SEO Chuyên Sâu $index",
                    'description' => 'Học SEO từ cơ bản đến nâng cao',
                    'provider' => 'KATA Channel',
                    'courseMode' => 'Online'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Course',
                    'name' => "Khóa Học SEO Chuyên Sâu $index",
                    'description' => 'Học SEO từ cơ bản đến nâng cao',
                    'provider' => array(
                        '@type' => 'Organization',
                        'name' => 'KATA Channel'
                    ),
                    'courseMode' => 'Online'
                )
            ),
            
            'Organization' => array(
                'data' => array(
                    'name' => "KATA Channel Organization $index",
                    'description' => 'Tổ chức đào tạo SEO & Marketing',
                    'url' => 'https://katachannel.com',
                    'logo' => 'https://example.com/logo.png'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => "KATA Channel Organization $index",
                    'description' => 'Tổ chức đào tạo SEO & Marketing',
                    'url' => 'https://katachannel.com',
                    'logo' => 'https://example.com/logo.png'
                )
            ),
            
            'Breadcrumb' => array(
                'data' => array(
                    'items' => array(
                        array('name' => 'Trang chủ', 'url' => '/'),
                        array('name' => 'Blog', 'url' => '/blog'),
                        array('name' => "Bài viết $index", 'url' => '/blog/post')
                    )
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => array(
                        array('@type' => 'ListItem', 'position' => 1, 'name' => 'Trang chủ', 'item' => '/'),
                        array('@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => '/blog'),
                        array('@type' => 'ListItem', 'position' => 3, 'name' => "Bài viết $index", 'item' => '/blog/post')
                    )
                )
            ),
            
            'WebPage' => array(
                'data' => array(
                    'name' => "Trang Web Mẫu $index",
                    'description' => 'Trang web demo với Schema Markup',
                    'url' => 'https://example.com/page'
                ),
                'json' => array(
                    '@context' => 'https://schema.org',
                    '@type' => 'WebPage',
                    'name' => "Trang Web Mẫu $index",
                    'description' => 'Trang web demo với Schema Markup',
                    'url' => 'https://example.com/page'
                )
            )
        );
        
        // Các schema types còn lại dùng template đơn giản
        $default_data = array(
            'data' => array(
                'name' => ucfirst($type) . " Sample Data $index",
                'description' => "Dữ liệu mẫu cho schema type: $type"
            ),
            'json' => array(
                '@context' => 'https://schema.org',
                '@type' => $type,
                'name' => ucfirst($type) . " Sample Data $index",
                'description' => "Dữ liệu mẫu cho schema type: $type"
            )
        );
        
        return isset($samples[$type]) ? $samples[$type] : $default_data;
    }
    
    /**
     * Tạo 1 post và 1 page demo với tất cả schemas
     */
    private static function create_demo_posts() {
        global $wpdb;
        
        // Tạo 1 Post demo
        $post_id = wp_insert_post(array(
            'post_title' => 'Bài Viết Demo - KATA SEO Manager với Tất Cả Schema Types',
            'post_content' => self::get_demo_post_content(),
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_author' => 1
        ));
        
        // Tạo 1 Page demo
        $page_id = wp_insert_post(array(
            'post_title' => 'Trang Demo - KATA SEO Manager Schema Showcase',
            'post_content' => self::get_demo_page_content(),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1
        ));
        
        // Cập nhật post_id cho các schemas đã tạo
        if ($post_id && $page_id) {
            $table = $wpdb->prefix . 'kata_seo_schemas';
            
            // Lấy tất cả schemas
            $schemas = $wpdb->get_results("SELECT * FROM $table WHERE post_id = 0", ARRAY_A);
            
            $count = 0;
            foreach ($schemas as $schema) {
                // Chia đều schemas cho post và page
                $target_post_id = ($count % 2 == 0) ? $post_id : $page_id;
                
                $wpdb->update(
                    $table,
                    array('post_id' => $target_post_id),
                    array('id' => $schema['id']),
                    array('%d'),
                    array('%d')
                );
                
                $count++;
            }
        }
        
        return array('post_id' => $post_id, 'page_id' => $page_id);
    }
    
    /**
     * Nội dung cho post demo
     */
    private static function get_demo_post_content() {
        return <<<HTML
<h2>🎉 Chào Mừng Đến Với KATA SEO Manager!</h2>

<p>Đây là <strong>bài viết demo</strong> được tạo tự động khi kích hoạt plugin. Bài viết này chứa <strong>tất cả 26 loại Schema Markup</strong> được hỗ trợ bởi KATA SEO Manager.</p>

<h3>📋 Các Schema Đã Được Thêm:</h3>
<ul>
    <li>✅ Article Schema - Cấu trúc bài viết</li>
    <li>✅ FAQ Schema - Câu hỏi thường gặp</li>
    <li>✅ Product Schema - Thông tin sản phẩm</li>
    <li>✅ Recipe Schema - Công thức nấu ăn</li>
    <li>✅ HowTo Schema - Hướng dẫn từng bước</li>
    <li>✅ Event Schema - Sự kiện</li>
    <li>✅ Video Schema - Video content</li>
    <li>✅ Review Schema - Đánh giá</li>
    <li>✅ LocalBusiness Schema - Doanh nghiệp địa phương</li>
    <li>✅ JobPosting Schema - Tuyển dụng</li>
    <li>✅ Course Schema - Khóa học</li>
    <li>✅ Organization Schema - Tổ chức</li>
    <li>✅ Breadcrumb Schema - Đường dẫn</li>
    <li>✅ WebPage Schema - Trang web</li>
    <li>✅ Và 12 schema types khác...</li>
</ul>

<h3>🚀 Cách Sử Dụng:</h3>
<ol>
    <li>Vào <strong>WordPress Admin → KATA SEO Manager</strong></li>
    <li>Chọn tab <strong>Dashboard</strong> để xem tổng quan</li>
    <li>Chọn tab <strong>Schemas</strong> để quản lý các schema</li>
    <li>Chọn tab <strong>Statistics</strong> để xem thống kê</li>
    <li>Chọn tab <strong>Settings</strong> để cấu hình</li>
</ol>

<h3>💡 Lưu Ý:</h3>
<p>Bài viết này chỉ là <strong>demo</strong>. Bạn có thể:</p>
<ul>
    <li>Chỉnh sửa nội dung</li>
    <li>Thêm/xóa schemas</li>
    <li>Tạo schemas mới cho bài viết khác</li>
    <li>Test với Google Rich Results Test</li>
</ul>

<p><em>Tạo bởi KATA SEO Manager - Plugin SEO chuyên nghiệp cho WordPress</em></p>
HTML;
    }
    
    /**
     * Nội dung cho page demo
     */
    private static function get_demo_page_content() {
        return <<<HTML
<h1>🌟 KATA SEO Manager Schema Showcase</h1>

<p>Đây là <strong>trang demo</strong> hiển thị khả năng của KATA SEO Manager với đầy đủ 26 loại Schema Markup.</p>

<div style="background: #f0f8ff; padding: 20px; border-left: 4px solid #0073aa; margin: 20px 0;">
    <h3>📊 Schema Types Available:</h3>
    <p>Plugin này hỗ trợ <strong>26 loại Schema</strong> được Google công nhận, giúp website của bạn:</p>
    <ul>
        <li>✨ Hiển thị Rich Snippets đẹp mắt</li>
        <li>📈 Tăng CTR (Click-Through Rate)</li>
        <li>🎯 Cải thiện thứ hạng SEO</li>
        <li>🏆 Nổi bật hơn đối thủ</li>
    </ul>
</div>

<h2>🎓 Hướng Dẫn Nhanh</h2>

<h3>1. Thêm Schema Mới:</h3>
<p>Vào bất kỳ bài viết/trang nào → Click nút <strong>"KATA Insert Schema"</strong> trên editor → Chọn loại schema → Điền form → Insert</p>

<h3>2. Quản Lý Schema:</h3>
<p>Vào <strong>KATA SEO Manager → Schemas</strong> → Xem danh sách → Edit/Delete/Toggle Active</p>

<h3>3. Xem Thống Kê:</h3>
<p>Vào <strong>KATA SEO Manager → Statistics</strong> → Xem biểu đồ phân bố schema types</p>

<h3>4. Cấu Hình:</h3>
<p>Vào <strong>KATA SEO Manager → Settings</strong> → Bật/tắt tính năng → Enable/Disable schema types</p>

<hr>

<h2>🔍 Test Schema</h2>
<p>Sau khi thêm schema, hãy test với:</p>
<ul>
    <li><strong>Google Rich Results Test:</strong> <code>https://search.google.com/test/rich-results</code></li>
    <li><strong>Schema Markup Validator:</strong> <code>https://validator.schema.org/</code></li>
</ul>

<div style="background: #e7f3e7; padding: 15px; border-radius: 5px; margin-top: 30px;">
    <p><strong>💚 Cảm ơn bạn đã sử dụng KATA SEO Manager!</strong></p>
    <p>Nếu có câu hỏi, vui lòng truy cập: <a href="https://katachannel.com">https://katachannel.com</a></p>
</div>
HTML;
    }
    
    /**
     * Tạo sample statistics
     */
    private static function create_sample_stats() {
        global $wpdb;
        $table = $wpdb->prefix . 'kata_seo_schema_stats';
        $schema_table = $wpdb->prefix . 'kata_seo_schemas';
        
        // Lấy tất cả schema IDs
        $schema_ids = $wpdb->get_col("SELECT id FROM $schema_table LIMIT 20");
        
        foreach ($schema_ids as $schema_id) {
            // Tạo stats cho 7 ngày gần nhất
            for ($i = 0; $i < 7; $i++) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $impressions = rand(100, 1000);
                $clicks = rand(10, 100);
                $ctr = round(($clicks / $impressions) * 100, 2);
                
                $wpdb->insert(
                    $table,
                    array(
                        'schema_id' => $schema_id,
                        'impressions' => $impressions,
                        'clicks' => $clicks,
                        'ctr' => $ctr,
                        'position' => rand(1, 10),
                        'date' => $date
                    ),
                    array('%d', '%d', '%d', '%f', '%f', '%s')
                );
            }
        }
    }
    
    /**
     * Tạo sample templates
     */
    private static function create_sample_templates() {
        global $wpdb;
        $table = $wpdb->prefix . 'kata_seo_schema_templates';
        
        $templates = array(
            array(
                'template_name' => 'Article Template - Blog Post',
                'schema_type' => 'Article',
                'template_data' => json_encode(array(
                    'headline' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'author' => '{author_name}',
                    'datePublished' => '{post_date}',
                    'image' => '{featured_image}'
                )),
                'is_default' => 1
            ),
            array(
                'template_name' => 'Product Template - WooCommerce',
                'schema_type' => 'Product',
                'template_data' => json_encode(array(
                    'name' => '{product_name}',
                    'description' => '{product_description}',
                    'price' => '{product_price}',
                    'currency' => 'VND',
                    'brand' => '{product_brand}'
                )),
                'is_default' => 1
            ),
            array(
                'template_name' => 'FAQ Template - Support Page',
                'schema_type' => 'FAQ',
                'template_data' => json_encode(array(
                    'questions' => array(
                        array('question' => 'Question 1?', 'answer' => 'Answer 1'),
                        array('question' => 'Question 2?', 'answer' => 'Answer 2')
                    )
                )),
                'is_default' => 1
            )
        );
        
        foreach ($templates as $template) {
            $wpdb->insert(
                $table,
                array(
                    'template_name' => $template['template_name'],
                    'schema_type' => $template['schema_type'],
                    'template_data' => $template['template_data'],
                    'is_default' => $template['is_default'],
                    'created_by' => 1,
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%s', '%d', '%d', '%s')
            );
        }
    }
}
