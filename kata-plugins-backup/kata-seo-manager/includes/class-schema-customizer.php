<?php
/**
 * Schema Customizer Class
 * 
 * Cho phép tùy chỉnh các thuộc tính hiển thị trong JSON-LD schema output
 * 
 * @package KATA_SEO_Manager
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_Schema_Customizer {
    
    /**
     * Default schema properties cho từng loại schema
     */
    private static $default_properties = array(
        'faq' => array(
            '@context' => true,
            '@type' => true,
            'mainEntity' => true,
            'name' => true,
            'description' => false,
            'author' => false,
            'datePublished' => false,
            'dateModified' => false
        ),
        'article' => array(
            '@context' => true,
            '@type' => true,
            'headline' => true,
            'author' => true,
            'datePublished' => true,
            'dateModified' => true,
            'image' => true,
            'publisher' => true,
            'description' => true,
            'articleBody' => false,
            'wordCount' => false,
            'timeRequired' => false,
            'articleSection' => false,
            'keywords' => false
        ),
        'recipe' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'image' => true,
            'author' => true,
            'datePublished' => true,
            'prepTime' => true,
            'cookTime' => true,
            'totalTime' => true,
            'recipeYield' => true,
            'recipeIngredient' => true,
            'recipeInstructions' => true,
            'nutrition' => false,
            'recipeCuisine' => false,
            'recipeCategory' => false,
            'keywords' => false,
            'suitableForDiet' => false,
            'aggregateRating' => false
        ),
        'product' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'image' => true,
            'description' => true,
            'brand' => true,
            'offers' => true,
            'sku' => false,
            'gtin' => false,
            'mpn' => false,
            'aggregateRating' => false,
            'review' => false,
            'material' => false,
            'color' => false,
            'weight' => false,
            'width' => false,
            'height' => false,
            'depth' => false
        ),
        'event' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'startDate' => true,
            'endDate' => true,
            'location' => true,
            'image' => true,
            'organizer' => true,
            'offers' => false,
            'performer' => false,
            'eventStatus' => false,
            'eventAttendanceMode' => false,
            'previousStartDate' => false,
            'typicalAgeRange' => false
        ),
        'howto' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'image' => true,
            'step' => true,
            'totalTime' => true,
            'estimatedCost' => false,
            'supply' => false,
            'tool' => false,
            'performTime' => false,
            'prepTime' => false,
            'yield' => false
        ),
        'quiz' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'author' => true,
            'datePublished' => true,
            'educationalLevel' => false,
            'typicalAgeRange' => false,
            'learningResourceType' => true,
            'interactivityType' => true,
            'assesses' => false,
            'teaches' => false,
            'competencyRequired' => false,
            'educationalUse' => false
        ),
        'poll' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'author' => true,
            'datePublished' => true,
            'interactionStatistic' => true,
            'interactivityType' => true,
            'potentialAction' => true
        ),
        'wheel' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'image' => false,
            'offers' => true,
            'potentialAction' => true,
            'interactivityType' => true
        ),
        'course' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'provider' => true,
            'offers' => true,
            'hasCourseInstance' => false,
            'courseCode' => false,
            'educationalLevel' => false,
            'timeRequired' => false,
            'numberOfCredits' => false,
            'coursePrerequisites' => false
        ),
        'localbusiness' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'address' => true,
            'telephone' => true,
            'openingHoursSpecification' => true,
            'image' => true,
            'priceRange' => false,
            'geo' => false,
            'url' => false,
            'email' => false,
            'aggregateRating' => false,
            'review' => false,
            'paymentAccepted' => false,
            'currenciesAccepted' => false
        ),
        'jobposting' => array(
            '@context' => true,
            '@type' => true,
            'title' => true,
            'description' => true,
            'datePosted' => true,
            'hiringOrganization' => true,
            'jobLocation' => true,
            'baseSalary' => true,
            'employmentType' => true,
            'validThrough' => false,
            'qualifications' => false,
            'responsibilities' => false,
            'skills' => false,
            'experienceRequirements' => false,
            'educationRequirements' => false,
            'benefits' => false,
            'workHours' => false,
            'incentiveCompensation' => false
        ),
        'video' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'contentUrl' => true,
            'description' => true,
            'thumbnailUrl' => true,
            'duration' => true,
            'uploadDate' => true,
            'embedUrl' => false,
            'transcript' => false,
            'videoQuality' => false,
            'publisher' => false,
            'contentSize' => false,
            'encodingFormat' => false,
            'interactionStatistic' => false,
            'regionsAllowed' => false
        ),
        'organization' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'url' => true,
            'logo' => true,
            'description' => true,
            'address' => true,
            'telephone' => true,
            'email' => true,
            'foundingDate' => false,
            'founder' => false,
            'numberOfEmployees' => false,
            'slogan' => false,
            'contactPoint' => false,
            'sameAs' => false,
            'areaServed' => false,
            'award' => false
        ),
        'rating' => array(
            '@context' => true,
            '@type' => true,
            'ratingValue' => true,
            'bestRating' => true,
            'worstRating' => false,
            'ratingCount' => false,
            'reviewCount' => false,
            'itemReviewed' => false,
            'author' => false,
            'reviewBody' => false,
            'datePublished' => false
        ),
        'breadcrumb' => array(
            '@context' => true,
            '@type' => true,
            'itemListElement' => true,
            'numberOfItems' => false,
            'name' => false,
            'description' => false
        ),
        'book' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'author' => true,
            'description' => true,
            'isbn' => true,
            'publisher' => true,
            'datePublished' => true,
            'numberOfPages' => false,
            'genre' => false,
            'inLanguage' => false,
            'bookFormat' => false,
            'image' => false,
            'url' => false,
            'offers' => false,
            'aggregateRating' => false,
            'review' => false
        ),
        'movie' => array(
            '@context' => true,
            '@type' => true,
            'name' => true,
            'description' => true,
            'director' => true,
            'actor' => true,
            'image' => true,
            'datePublished' => true,
            'duration' => false,
            'genre' => false,
            'contentRating' => false,
            'trailer' => false,
            'countryOfOrigin' => false,
            'inLanguage' => false,
            'productionCompany' => false,
            'aggregateRating' => false,
            'review' => false
        ),
        'review' => array(
            '@context' => true,
            '@type' => true,
            'itemReviewed' => true,
            'author' => true,
            'reviewRating' => true,
            'reviewBody' => true,
            'datePublished' => true,
            'publisher' => false,
            'positiveNotes' => false,
            'negativeNotes' => false,
            'reviewAspect' => false
        ),
        'newsarticle' => array(
            '@context' => true,
            '@type' => true,
            'headline' => true,
            'author' => true,
            'datePublished' => true,
            'dateModified' => true,
            'image' => true,
            'publisher' => true,
            'description' => true,
            'articleBody' => false,
            'dateline' => false,
            'printColumn' => false,
            'printEdition' => false,
            'printPage' => false,
            'printSection' => false,
            'speakable' => false
        ),
        'blogposting' => array(
            '@context' => true,
            '@type' => true,
            'headline' => true,
            'author' => true,
            'datePublished' => true,
            'dateModified' => true,
            'image' => true,
            'publisher' => true,
            'description' => true,
            'articleBody' => false,
            'wordCount' => false,
            'commentCount' => false,
            'comment' => false,
            'blogSection' => false,
            'keywords' => false
        )
    );
    
    /**
     * Parse shortcode attributes để lấy schema customization settings
     * 
     * @param array $atts Shortcode attributes
     * @param string $schema_type Loại schema
     * @return array Customized schema properties
     */
    public static function parse_schema_attributes($atts, $schema_type) {
        $schema_type = strtolower($schema_type);
        
        // Get default properties cho schema type này
        $default_props = isset(self::$default_properties[$schema_type]) 
            ? self::$default_properties[$schema_type] 
            : array();
        
        $custom_props = $default_props;
        
        // Parse schema_fields attribute
        if (isset($atts['schema_fields'])) {
            $custom_props = self::parse_fields_attribute($atts['schema_fields'], $default_props);
        }
        
        // Parse individual hide_* attributes
        foreach ($atts as $key => $value) {
            if (strpos($key, 'hide_') === 0) {
                $field_name = str_replace('hide_', '', $key);
                if ($value === 'true' || $value === '1' || $value === true) {
                    $custom_props[$field_name] = false;
                }
            } elseif (strpos($key, 'show_') === 0) {
                $field_name = str_replace('show_', '', $key);
                if ($value === 'true' || $value === '1' || $value === true) {
                    $custom_props[$field_name] = true;
                }
            }
        }
        
        return $custom_props;
    }
    
    /**
     * Parse schema_fields attribute
     * 
     * Format: schema_fields="field1,field2,-field3,field4"
     * - field1: include field1
     * - -field3: exclude field3
     * 
     * @param string $fields_string
     * @param array $default_props
     * @return array
     */
    private static function parse_fields_attribute($fields_string, $default_props) {
        $props = $default_props;
        
        // Split by comma
        $fields = array_map('trim', explode(',', $fields_string));
        
        foreach ($fields as $field) {
            if (empty($field)) continue;
            
            // Check if exclude (starts with -)
            if (substr($field, 0, 1) === '-') {
                $field_name = substr($field, 1);
                $props[$field_name] = false;
            } else {
                $props[$field] = true;
            }
        }
        
        return $props;
    }
    
    /**
     * Filter schema output dựa trên customization settings
     * 
     * @param array $schema Original schema data
     * @param array $custom_props Customization properties
     * @return array Filtered schema
     */
    public static function filter_schema_output($schema, $custom_props) {
        if (empty($custom_props)) {
            return $schema;
        }
        
        $filtered = array();
        
        foreach ($schema as $key => $value) {
            // Always include @context and @type
            if ($key === '@context' || $key === '@type') {
                $filtered[$key] = $value;
                continue;
            }
            
            // Check if field should be included
            if (isset($custom_props[$key]) && $custom_props[$key] === true) {
                $filtered[$key] = $value;
            } elseif (!isset($custom_props[$key])) {
                // If not specified in custom_props, include by default
                $filtered[$key] = $value;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Get default properties cho một schema type
     * 
     * @param string $schema_type
     * @return array
     */
    public static function get_default_properties($schema_type) {
        $schema_type = strtolower($schema_type);
        return isset(self::$default_properties[$schema_type]) 
            ? self::$default_properties[$schema_type] 
            : array();
    }
    
    /**
     * Get all default properties cho tất cả schema types
     * Dùng cho Admin UI để hiển thị checkboxes
     * 
     * @return array
     */
    public static function get_all_default_properties() {
        return self::$default_properties;
    }
    
    /**
     * Get danh sách tất cả schema types được hỗ trợ
     * 
     * @return array
     */
    public static function get_supported_schema_types() {
        return array_keys(self::$default_properties);
    }
    
    /**
     * Kiểm tra xem một property có được phép hiển thị không
     * 
     * @param string $property Property name
     * @param array $custom_props Custom properties settings
     * @return bool
     */
    public static function is_property_visible($property, $custom_props) {
        // Always show @context and @type
        if ($property === '@context' || $property === '@type') {
            return true;
        }
        
        if (isset($custom_props[$property])) {
            return $custom_props[$property] === true;
        }
        
        return true; // Default to visible
    }
    
    /**
     * Generate documentation cho schema customization
     * 
     * @param string $schema_type
     * @return string HTML documentation
     */
    public static function get_documentation($schema_type = null) {
        $doc = '<div class="kata-schema-customizer-docs">';
        $doc .= '<h3>📝 Hướng Dẫn Tùy Chỉnh Schema Output</h3>';
        
        $doc .= '<h4>Cách 1: Sử dụng thuộc tính schema_fields</h4>';
        $doc .= '<pre>[kata_article schema_fields="headline,author,datePublished,-articleBody,-keywords"]</pre>';
        $doc .= '<p>- Liệt kê các field muốn hiển thị, phân cách bằng dấu phẩy</p>';
        $doc .= '<p>- Thêm dấu "-" trước field để ẩn field đó</p>';
        
        $doc .= '<h4>Cách 2: Sử dụng thuộc tính hide_* hoặc show_*</h4>';
        $doc .= '<pre>[kata_article hide_articleBody="true" hide_keywords="true"]</pre>';
        $doc .= '<pre>[kata_recipe show_nutrition="true" show_suitableForDiet="true"]</pre>';
        
        if ($schema_type) {
            $props = self::get_default_properties($schema_type);
            if (!empty($props)) {
                $doc .= '<h4>Thuộc tính có sẵn cho ' . esc_html($schema_type) . ':</h4>';
                $doc .= '<ul>';
                foreach ($props as $prop => $default) {
                    $status = $default ? '✅ Hiển thị mặc định' : '❌ Ẩn mặc định';
                    $doc .= '<li><code>' . esc_html($prop) . '</code> - ' . $status . '</li>';
                }
                $doc .= '</ul>';
            }
        }
        
        $doc .= '</div>';
        
        return $doc;
    }
}
