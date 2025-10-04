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

        // Table 7: Quiz instances
        $table_quizzes = $wpdb->prefix . 'kata_seo_quizzes';
        $sql_quizzes = "CREATE TABLE IF NOT EXISTS $table_quizzes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            quiz_title varchar(500) NOT NULL,
            quiz_data longtext NOT NULL,
            quiz_config longtext DEFAULT NULL,
            total_questions int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY is_active (is_active)
        ) $charset_collate;";
        dbDelta($sql_quizzes);

        // Table 8: Quiz attempts/submissions
        $table_quiz_attempts = $wpdb->prefix . 'kata_seo_quiz_attempts';
        $sql_quiz_attempts = "CREATE TABLE IF NOT EXISTS $table_quiz_attempts (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quiz_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_ip varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            answers longtext NOT NULL,
            score decimal(5,2) DEFAULT 0.00,
            total_questions int(11) DEFAULT 0,
            correct_answers int(11) DEFAULT 0,
            time_taken int(11) DEFAULT 0,
            completion_rate decimal(5,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id),
            KEY user_id (user_id),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_quiz_attempts);

        // Table 9: Quiz analytics
        $table_quiz_analytics = $wpdb->prefix . 'kata_seo_quiz_analytics';
        $sql_quiz_analytics = "CREATE TABLE IF NOT EXISTS $table_quiz_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quiz_id bigint(20) NOT NULL,
            date date NOT NULL,
            total_views int(11) DEFAULT 0,
            total_attempts int(11) DEFAULT 0,
            total_completions int(11) DEFAULT 0,
            avg_score decimal(5,2) DEFAULT 0.00,
            avg_time_taken int(11) DEFAULT 0,
            completion_rate decimal(5,2) DEFAULT 0.00,
            bounce_rate decimal(5,2) DEFAULT 0.00,
            pass_rate decimal(5,2) DEFAULT 0.00,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id),
            KEY date (date),
            UNIQUE KEY quiz_date (quiz_id, date)
        ) $charset_collate;";
        dbDelta($sql_quiz_analytics);

        // Table 10: Quiz sessions - Track user sessions and behavior
        $table_quiz_sessions = $wpdb->prefix . 'kata_seo_quiz_sessions';
        $sql_quiz_sessions = "CREATE TABLE IF NOT EXISTS $table_quiz_sessions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            session_id varchar(128) NOT NULL,
            quiz_id bigint(20) NOT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_ip varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            started_at datetime NOT NULL,
            last_activity datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            completed_at datetime DEFAULT NULL,
            questions_viewed int(11) DEFAULT 0,
            questions_answered int(11) DEFAULT 0,
            time_spent int(11) DEFAULT 0,
            status enum('started', 'in_progress', 'completed', 'abandoned') DEFAULT 'started',
            referrer varchar(500) DEFAULT NULL,
            utm_source varchar(100) DEFAULT NULL,
            utm_medium varchar(100) DEFAULT NULL,
            utm_campaign varchar(100) DEFAULT NULL,
            device_type varchar(50) DEFAULT NULL,
            browser varchar(100) DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY session_id (session_id),
            KEY quiz_id (quiz_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";
        dbDelta($sql_quiz_sessions);

        // Table 11: Quiz question analytics - Track individual question performance
        $table_question_analytics = $wpdb->prefix . 'kata_seo_quiz_question_analytics';
        $sql_question_analytics = "CREATE TABLE IF NOT EXISTS $table_question_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quiz_id bigint(20) NOT NULL,
            question_index int(11) NOT NULL,
            question_text text NOT NULL,
            date date NOT NULL,
            total_views int(11) DEFAULT 0,
            total_answers int(11) DEFAULT 0,
            correct_answers int(11) DEFAULT 0,
            option_0_count int(11) DEFAULT 0,
            option_1_count int(11) DEFAULT 0,
            option_2_count int(11) DEFAULT 0,
            option_3_count int(11) DEFAULT 0,
            option_4_count int(11) DEFAULT 0,
            avg_time_spent int(11) DEFAULT 0,
            difficulty_score decimal(5,2) DEFAULT 0.00,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id),
            KEY question_index (question_index),
            KEY date (date),
            UNIQUE KEY quiz_question_date (quiz_id, question_index, date)
        ) $charset_collate;";
        dbDelta($sql_question_analytics);

        // Table 12: Quiz performance trends - For advanced analytics
        $table_quiz_trends = $wpdb->prefix . 'kata_seo_quiz_trends';
        $sql_quiz_trends = "CREATE TABLE IF NOT EXISTS $table_quiz_trends (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            quiz_id bigint(20) NOT NULL,
            period enum('hourly', 'daily', 'weekly', 'monthly') NOT NULL,
            period_start datetime NOT NULL,
            period_end datetime NOT NULL,
            total_views int(11) DEFAULT 0,
            total_attempts int(11) DEFAULT 0,
            total_completions int(11) DEFAULT 0,
            avg_score decimal(5,2) DEFAULT 0.00,
            completion_rate decimal(5,2) DEFAULT 0.00,
            engagement_rate decimal(5,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id),
            KEY period (period),
            KEY period_start (period_start),
            UNIQUE KEY quiz_period (quiz_id, period, period_start)
        ) $charset_collate;";
        dbDelta($sql_quiz_trends);
        
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
