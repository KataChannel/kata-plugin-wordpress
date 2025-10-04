<?php
/**
 * Database Class - Create and manage database tables
 *
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Database {
    
    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Table 1: Schema instances
        $table_schemas = $wpdb->prefix . 'kata_seo_schemas';
        $sql_schemas = "CREATE TABLE IF NOT EXISTS $table_schemas (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            schema_type varchar(100) NOT NULL,
            schema_data longtext NOT NULL,
            schema_json longtext NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY schema_type (schema_type)
        ) $charset_collate;";
        dbDelta($sql_schemas);
        
        // Table 2: Schema usage statistics
        $table_stats = $wpdb->prefix . 'kata_seo_schema_stats';
        $sql_stats = "CREATE TABLE IF NOT EXISTS $table_stats (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            schema_id bigint(20) NOT NULL,
            impressions bigint(20) DEFAULT 0,
            clicks bigint(20) DEFAULT 0,
            ctr decimal(5,2) DEFAULT 0.00,
            position decimal(5,2) DEFAULT 0.00,
            date date NOT NULL,
            PRIMARY KEY  (id),
            KEY schema_id (schema_id),
            KEY date (date)
        ) $charset_collate;";
        dbDelta($sql_stats);
        
        // Table 3: Schema validation results
        $table_validation = $wpdb->prefix . 'kata_seo_schema_validation';
        $sql_validation = "CREATE TABLE IF NOT EXISTS $table_validation (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            schema_id bigint(20) NOT NULL,
            is_valid tinyint(1) DEFAULT 0,
            errors longtext DEFAULT NULL,
            warnings longtext DEFAULT NULL,
            validated_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY schema_id (schema_id)
        ) $charset_collate;";
        dbDelta($sql_validation);
        
        // Table 4: Schema templates
        $table_templates = $wpdb->prefix . 'kata_seo_schema_templates';
        $sql_templates = "CREATE TABLE IF NOT EXISTS $table_templates (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            template_name varchar(255) NOT NULL,
            schema_type varchar(100) NOT NULL,
            template_data longtext NOT NULL,
            is_default tinyint(1) DEFAULT 0,
            created_by bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY schema_type (schema_type)
        ) $charset_collate;";
        dbDelta($sql_templates);
        
        // Table 5: Schema tracking (Google Search Console integration)
        $table_tracking = $wpdb->prefix . 'kata_seo_schema_tracking';
        $sql_tracking = "CREATE TABLE IF NOT EXISTS $table_tracking (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            schema_type varchar(100) NOT NULL,
            url varchar(500) NOT NULL,
            rich_result_type varchar(100) DEFAULT NULL,
            indexed tinyint(1) DEFAULT 0,
            errors_count int(11) DEFAULT 0,
            warnings_count int(11) DEFAULT 0,
            last_checked datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY url (url(191))
        ) $charset_collate;";
        dbDelta($sql_tracking);
        
        // Table 6: Activity log
        $table_log = $wpdb->prefix . 'kata_seo_activity_log';
        $sql_log = "CREATE TABLE IF NOT EXISTS $table_log (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            action varchar(100) NOT NULL,
            object_type varchar(100) NOT NULL,
            object_id bigint(20) DEFAULT NULL,
            description text DEFAULT NULL,
            metadata longtext DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY action (action)
        ) $charset_collate;";
        dbDelta($sql_log);
        
        // Insert default templates
        $this->insert_default_templates();
    }
    
    /**
     * Insert default schema templates
     */
    private function insert_default_templates() {
        global $wpdb;
        $table = $wpdb->prefix . 'kata_seo_schema_templates';
        
        // Check if templates already exist
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }
        
        $templates = array(
            // Article template
            array(
                'template_name' => 'Default Article',
                'schema_type' => 'Article',
                'template_data' => json_encode(array(
                    'headline' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'author' => '{author_name}',
                    'datePublished' => '{post_date}',
                    'dateModified' => '{post_modified}',
                    'image' => '{featured_image}',
                    'publisher' => '{site_name}'
                )),
                'is_default' => 1
            ),
            // FAQ template
            array(
                'template_name' => 'Default FAQ',
                'schema_type' => 'FAQ',
                'template_data' => json_encode(array(
                    'questions' => array(
                        array(
                            'question' => 'Sample question?',
                            'answer' => 'Sample answer'
                        )
                    )
                )),
                'is_default' => 1
            ),
            // Recipe template
            array(
                'template_name' => 'Default Recipe',
                'schema_type' => 'Recipe',
                'template_data' => json_encode(array(
                    'name' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'image' => '{featured_image}',
                    'prepTime' => 'PT30M',
                    'cookTime' => 'PT1H',
                    'recipeYield' => '4 servings',
                    'recipeIngredient' => array(),
                    'recipeInstructions' => array()
                )),
                'is_default' => 1
            ),
            // Product template  
            array(
                'template_name' => 'Default Product',
                'schema_type' => 'Product',
                'template_data' => json_encode(array(
                    'name' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'image' => '{featured_image}',
                    'brand' => '{site_name}',
                    'offers' => array(
                        'price' => '0',
                        'priceCurrency' => 'VND',
                        'availability' => 'InStock'
                    )
                )),
                'is_default' => 1
            ),
            // Event template
            array(
                'template_name' => 'Default Event',
                'schema_type' => 'Event',
                'template_data' => json_encode(array(
                    'name' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'image' => '{featured_image}',
                    'startDate' => '{current_date}',
                    'endDate' => '{current_date}',
                    'location' => array(
                        'name' => 'Venue Name',
                        'address' => 'Address'
                    )
                )),
                'is_default' => 1
            ),
            // Video template
            array(
                'template_name' => 'Default Video',
                'schema_type' => 'Video',
                'template_data' => json_encode(array(
                    'name' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'thumbnailUrl' => '{featured_image}',
                    'uploadDate' => '{post_date}',
                    'duration' => 'PT5M'
                )),
                'is_default' => 1
            ),
            // HowTo template
            array(
                'template_name' => 'Default HowTo',
                'schema_type' => 'HowTo',
                'template_data' => json_encode(array(
                    'name' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'image' => '{featured_image}',
                    'totalTime' => 'PT1H',
                    'step' => array()
                )),
                'is_default' => 1
            ),
            // Course template
            array(
                'template_name' => 'Default Course',
                'schema_type' => 'Course',
                'template_data' => json_encode(array(
                    'name' => '{post_title}',
                    'description' => '{post_excerpt}',
                    'provider' => '{site_name}'
                )),
                'is_default' => 1
            ),
            // Local Business template
            array(
                'template_name' => 'Default Local Business',
                'schema_type' => 'LocalBusiness',
                'template_data' => json_encode(array(
                    'name' => '{site_name}',
                    'image' => '{site_logo}',
                    'address' => array(
                        'streetAddress' => '',
                        'addressLocality' => '',
                        'addressCountry' => 'VN'
                    ),
                    'telephone' => '',
                    'openingHours' => 'Mo-Fr 09:00-17:00'
                )),
                'is_default' => 1
            ),
            // Organization template
            array(
                'template_name' => 'Default Organization',
                'schema_type' => 'Organization',
                'template_data' => json_encode(array(
                    'name' => '{site_name}',
                    'url' => '{site_url}',
                    'logo' => '{site_logo}'
                )),
                'is_default' => 1
            )
        );
        
        foreach ($templates as $template) {
            $wpdb->insert($table, $template);
        }
    }
    
    /**
     * Drop all plugin tables (for uninstall)
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'kata_seo_schemas',
            $wpdb->prefix . 'kata_seo_schema_stats',
            $wpdb->prefix . 'kata_seo_schema_validation',
            $wpdb->prefix . 'kata_seo_schema_templates',
            $wpdb->prefix . 'kata_seo_schema_tracking',
            $wpdb->prefix . 'kata_seo_activity_log'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
}
