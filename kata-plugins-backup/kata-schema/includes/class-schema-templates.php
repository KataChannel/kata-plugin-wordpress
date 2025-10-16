<?php
/**
 * KATA Schema Templates Class
 * 
 * Provides default schema templates with sample data
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class KATA_Schema_Templates {
    
    /**
     * Available schema types
     */
    private static $schema_types = array(
        'Article' => 'Bài viết',
        'LocalBusiness' => 'Doanh nghiệp địa phương',
        'Product' => 'Sản phẩm',
        'FAQ' => 'Câu hỏi thường gặp',
        'HowTo' => 'Hướng dẫn',
        'Organization' => 'Tổ chức',
        'Person' => 'Nhân vật',
        'Event' => 'Sự kiện',
        'Recipe' => 'Công thức nấu ăn',
        'Course' => 'Khóa học'
    );
    
    /**
     * Get all schema types
     */
    public static function get_schema_types() {
        return self::$schema_types;
    }
    
    /**
     * Create default templates
     */
    public static function create_default_templates() {
        $database = new KATA_Schema_Database();
        
        // Only create if no templates exist
        if ($database->get_total_count() > 0) {
            return;
        }
        
        $templates = array(
            self::get_article_template(),
            self::get_local_business_template(),
            self::get_product_template(),
            self::get_faq_template(),
            self::get_howto_template(),
            self::get_organization_template(),
            self::get_person_template(),
            self::get_event_template(),
            self::get_recipe_template(),
            self::get_course_template()
        );
        
        foreach ($templates as $template) {
            $database->save_schema($template);
        }
    }
    
    /**
     * Article Template
     */
    private static function get_article_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => 'Tiêu đề bài viết của bạn',
            'image' => get_site_url() . '/wp-content/uploads/default-image.jpg',
            'author' => array(
                '@type' => 'Person',
                'name' => 'Tên tác giả'
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            ),
            'datePublished' => date('Y-m-d'),
            'dateModified' => date('Y-m-d'),
            'description' => 'Mô tả ngắn gọn về bài viết'
        );
        
        return array(
            'schema_name' => 'Article - Mẫu bài viết mặc định',
            'schema_type' => 'Article',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * LocalBusiness Template
     */
    private static function get_local_business_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'Tên doanh nghiệp',
            'image' => get_site_url() . '/wp-content/uploads/business-image.jpg',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '123 Đường ABC',
                'addressLocality' => 'Quận XYZ',
                'addressRegion' => 'TP. Hồ Chí Minh',
                'postalCode' => '700000',
                'addressCountry' => 'VN'
            ),
            'geo' => array(
                '@type' => 'GeoCoordinates',
                'latitude' => 10.762622,
                'longitude' => 106.660172
            ),
            'url' => get_site_url(),
            'telephone' => '+84-xxx-xxx-xxx',
            'openingHoursSpecification' => array(
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
                'opens' => '08:00',
                'closes' => '17:00'
            ),
            'priceRange' => '$$'
        );
        
        return array(
            'schema_name' => 'LocalBusiness - Mẫu doanh nghiệp địa phương',
            'schema_type' => 'LocalBusiness',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * Product Template
     */
    private static function get_product_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => 'Tên sản phẩm',
            'image' => get_site_url() . '/wp-content/uploads/product-image.jpg',
            'description' => 'Mô tả chi tiết về sản phẩm',
            'brand' => array(
                '@type' => 'Brand',
                'name' => 'Tên thương hiệu'
            ),
            'offers' => array(
                '@type' => 'Offer',
                'url' => get_site_url() . '/san-pham',
                'priceCurrency' => 'VND',
                'price' => '1000000',
                'availability' => 'https://schema.org/InStock',
                'priceValidUntil' => date('Y-m-d', strtotime('+1 year'))
            ),
            'aggregateRating' => array(
                '@type' => 'AggregateRating',
                'ratingValue' => '4.5',
                'reviewCount' => '100'
            )
        );
        
        return array(
            'schema_name' => 'Product - Mẫu sản phẩm',
            'schema_type' => 'Product',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * FAQ Template
     */
    private static function get_faq_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array(
                array(
                    '@type' => 'Question',
                    'name' => 'Câu hỏi 1?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Câu trả lời cho câu hỏi 1'
                    )
                ),
                array(
                    '@type' => 'Question',
                    'name' => 'Câu hỏi 2?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Câu trả lời cho câu hỏi 2'
                    )
                ),
                array(
                    '@type' => 'Question',
                    'name' => 'Câu hỏi 3?',
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => 'Câu trả lời cho câu hỏi 3'
                    )
                )
            )
        );
        
        return array(
            'schema_name' => 'FAQ - Mẫu câu hỏi thường gặp',
            'schema_type' => 'FAQ',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * HowTo Template
     */
    private static function get_howto_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => 'Hướng dẫn làm gì đó',
            'description' => 'Mô tả ngắn gọn về hướng dẫn',
            'image' => get_site_url() . '/wp-content/uploads/howto-image.jpg',
            'totalTime' => 'PT30M',
            'estimatedCost' => array(
                '@type' => 'MonetaryAmount',
                'currency' => 'VND',
                'value' => '100000'
            ),
            'step' => array(
                array(
                    '@type' => 'HowToStep',
                    'name' => 'Bước 1',
                    'text' => 'Mô tả chi tiết bước 1',
                    'image' => get_site_url() . '/wp-content/uploads/step1.jpg'
                ),
                array(
                    '@type' => 'HowToStep',
                    'name' => 'Bước 2',
                    'text' => 'Mô tả chi tiết bước 2',
                    'image' => get_site_url() . '/wp-content/uploads/step2.jpg'
                ),
                array(
                    '@type' => 'HowToStep',
                    'name' => 'Bước 3',
                    'text' => 'Mô tả chi tiết bước 3',
                    'image' => get_site_url() . '/wp-content/uploads/step3.jpg'
                )
            )
        );
        
        return array(
            'schema_name' => 'HowTo - Mẫu hướng dẫn',
            'schema_type' => 'HowTo',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * Organization Template
     */
    private static function get_organization_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => get_site_url(),
            'logo' => get_site_icon_url(),
            'description' => get_bloginfo('description'),
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '123 Đường ABC',
                'addressLocality' => 'Quận XYZ',
                'addressRegion' => 'TP. Hồ Chí Minh',
                'postalCode' => '700000',
                'addressCountry' => 'VN'
            ),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'telephone' => '+84-xxx-xxx-xxx',
                'contactType' => 'Customer Service'
            ),
            'sameAs' => array(
                'https://www.facebook.com/yourpage',
                'https://www.linkedin.com/company/yourcompany'
            )
        );
        
        return array(
            'schema_name' => 'Organization - Mẫu tổ chức',
            'schema_type' => 'Organization',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * Person Template
     */
    private static function get_person_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => 'Tên người',
            'jobTitle' => 'Vị trí công việc',
            'url' => get_site_url(),
            'image' => get_site_url() . '/wp-content/uploads/person-image.jpg',
            'sameAs' => array(
                'https://www.facebook.com/yourprofile',
                'https://www.linkedin.com/in/yourprofile'
            ),
            'worksFor' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name')
            )
        );
        
        return array(
            'schema_name' => 'Person - Mẫu nhân vật',
            'schema_type' => 'Person',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * Event Template
     */
    private static function get_event_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => 'Tên sự kiện',
            'startDate' => date('Y-m-d\TH:i:s', strtotime('+1 week')),
            'endDate' => date('Y-m-d\TH:i:s', strtotime('+1 week +3 hours')),
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'eventStatus' => 'https://schema.org/EventScheduled',
            'location' => array(
                '@type' => 'Place',
                'name' => 'Địa điểm sự kiện',
                'address' => array(
                    '@type' => 'PostalAddress',
                    'streetAddress' => '123 Đường ABC',
                    'addressLocality' => 'TP. Hồ Chí Minh',
                    'addressCountry' => 'VN'
                )
            ),
            'image' => get_site_url() . '/wp-content/uploads/event-image.jpg',
            'description' => 'Mô tả về sự kiện',
            'offers' => array(
                '@type' => 'Offer',
                'url' => get_site_url() . '/su-kien',
                'price' => '500000',
                'priceCurrency' => 'VND',
                'availability' => 'https://schema.org/InStock',
                'validFrom' => date('Y-m-d')
            ),
            'performer' => array(
                '@type' => 'PerformingGroup',
                'name' => 'Người biểu diễn'
            ),
            'organizer' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => get_site_url()
            )
        );
        
        return array(
            'schema_name' => 'Event - Mẫu sự kiện',
            'schema_type' => 'Event',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * Recipe Template
     */
    private static function get_recipe_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Recipe',
            'name' => 'Tên món ăn',
            'image' => get_site_url() . '/wp-content/uploads/recipe-image.jpg',
            'author' => array(
                '@type' => 'Person',
                'name' => 'Tên đầu bếp'
            ),
            'datePublished' => date('Y-m-d'),
            'description' => 'Mô tả món ăn',
            'prepTime' => 'PT30M',
            'cookTime' => 'PT1H',
            'totalTime' => 'PT1H30M',
            'recipeYield' => '4 người',
            'recipeCategory' => 'Món chính',
            'recipeCuisine' => 'Việt Nam',
            'recipeIngredient' => array(
                '500g thịt',
                '2 củ hành',
                '1 muỗng canh nước mắm'
            ),
            'recipeInstructions' => array(
                array(
                    '@type' => 'HowToStep',
                    'text' => 'Chuẩn bị nguyên liệu'
                ),
                array(
                    '@type' => 'HowToStep',
                    'text' => 'Chế biến món ăn'
                ),
                array(
                    '@type' => 'HowToStep',
                    'text' => 'Trang trí và thưởng thức'
                )
            ),
            'nutrition' => array(
                '@type' => 'NutritionInformation',
                'calories' => '400 calories'
            )
        );
        
        return array(
            'schema_name' => 'Recipe - Mẫu công thức nấu ăn',
            'schema_type' => 'Recipe',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
    
    /**
     * Course Template
     */
    private static function get_course_template() {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => 'Tên khóa học',
            'description' => 'Mô tả về khóa học',
            'provider' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'sameAs' => get_site_url()
            ),
            'offers' => array(
                '@type' => 'Offer',
                'category' => 'Paid',
                'price' => '2000000',
                'priceCurrency' => 'VND'
            ),
            'hasCourseInstance' => array(
                '@type' => 'CourseInstance',
                'courseMode' => 'online',
                'courseWorkload' => 'PT40H',
                'instructor' => array(
                    '@type' => 'Person',
                    'name' => 'Tên giảng viên'
                )
            )
        );
        
        return array(
            'schema_name' => 'Course - Mẫu khóa học',
            'schema_type' => 'Course',
            'schema_data' => json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            'status' => 'active'
        );
    }
}
