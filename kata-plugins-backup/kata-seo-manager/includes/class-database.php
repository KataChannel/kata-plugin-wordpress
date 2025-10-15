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
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            answers longtext NOT NULL,
            user_answers longtext DEFAULT NULL,
            score decimal(5,2) DEFAULT 0.00,
            total_questions int(11) DEFAULT 0,
            correct_answers int(11) DEFAULT 0,
            time_taken int(11) DEFAULT 0,
            completion_rate decimal(5,2) DEFAULT 0.00,
            passed tinyint(1) DEFAULT 0,
            attempt_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY quiz_id (quiz_id),
            KEY user_id (user_id),
            KEY user_ip (user_ip),
            KEY ip_address (ip_address),
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
            total_submissions int(11) DEFAULT 0,
            total_completions int(11) DEFAULT 0,
            average_score decimal(5,2) DEFAULT 0.00,
            avg_score decimal(5,2) DEFAULT 0.00,
            avg_time_taken int(11) DEFAULT 0,
            completion_rate decimal(5,2) DEFAULT 0.00,
            bounce_rate decimal(5,2) DEFAULT 0.00,
            pass_rate decimal(5,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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

        // Table 12.5: Kata Polls - Store poll configurations
        $table_polls = $wpdb->prefix . 'kata_polls';
        $sql_polls = "CREATE TABLE IF NOT EXISTS $table_polls (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(500) NOT NULL,
            description text DEFAULT NULL,
            options longtext NOT NULL,
            allow_multiple tinyint(1) DEFAULT 0,
            show_results tinyint(1) DEFAULT 1,
            require_login tinyint(1) DEFAULT 0,
            active tinyint(1) DEFAULT 1,
            total_votes int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY active (active)
        ) $charset_collate;";
        dbDelta($sql_polls);

        // Table 12.6: Kata Poll Votes - Store individual poll votes
        $table_poll_votes = $wpdb->prefix . 'kata_poll_votes';
        $sql_poll_votes = "CREATE TABLE IF NOT EXISTS $table_poll_votes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            poll_id bigint(20) NOT NULL,
            option_index int(11) NOT NULL,
            voter_user_id bigint(20) DEFAULT NULL,
            voter_ip varchar(100) DEFAULT NULL,
            voter_name varchar(200) DEFAULT NULL,
            voter_email varchar(200) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY poll_id (poll_id),
            KEY voter_user_id (voter_user_id),
            KEY voter_ip (voter_ip),
            KEY created_at (created_at),
            KEY poll_voter (poll_id, voter_user_id),
            KEY poll_ip (poll_id, voter_ip)
        ) $charset_collate;";
        dbDelta($sql_poll_votes);

        // Table 13: Kata Wheels - Store wheel configurations
        $table_wheels = $wpdb->prefix . 'kata_wheels';
        $sql_wheels = "CREATE TABLE IF NOT EXISTS $table_wheels (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wheel_title varchar(500) NOT NULL,
            wheel_description text DEFAULT NULL,
            requirement enum('none', 'email', 'phone', 'both') DEFAULT 'email',
            max_spins_per_user int(11) DEFAULT 1,
            max_spins_per_day int(11) DEFAULT 1,
            start_date datetime DEFAULT NULL,
            end_date datetime DEFAULT NULL,
            status enum('active', 'inactive', 'scheduled', 'expired') DEFAULT 'active',
            total_spins int(11) DEFAULT 0,
            total_prizes_won int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_wheels);

        // Table 13.5: Kata User Interactions - Store user interaction records
        $table_user_interactions = $wpdb->prefix . 'kata_user_interactions';
        $sql_user_interactions = "CREATE TABLE IF NOT EXISTS $table_user_interactions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) DEFAULT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_name varchar(255) DEFAULT NULL,
            user_email varchar(255) DEFAULT NULL,
            user_website varchar(500) DEFAULT NULL,
            interaction_type enum('comment', 'like', 'share', 'follow', 'subscribe', 'review', 'rating', 'other') DEFAULT 'comment',
            review_title varchar(500) DEFAULT NULL,
            review_content text DEFAULT NULL,
            review_pros text DEFAULT NULL,
            review_cons text DEFAULT NULL,
            overall_rating decimal(3,2) DEFAULT NULL,
            quality_rating decimal(3,2) DEFAULT NULL,
            value_rating decimal(3,2) DEFAULT NULL,
            service_rating decimal(3,2) DEFAULT NULL,
            comment_content text DEFAULT NULL,
            parent_id bigint(20) DEFAULT NULL,
            target_id bigint(20) DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            status enum('pending', 'approved', 'rejected', 'spam', 'trash') DEFAULT 'pending',
            is_featured tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY post_id (post_id),
            KEY parent_id (parent_id),
            KEY interaction_type (interaction_type),
            KEY status (status),
            KEY is_featured (is_featured),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_user_interactions);

        // Table 14: Kata Wheel Prizes - Store prize configurations for each wheel
        $table_wheel_prizes = $wpdb->prefix . 'kata_wheel_prizes';
        $sql_wheel_prizes = "CREATE TABLE IF NOT EXISTS $table_wheel_prizes (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wheel_id bigint(20) NOT NULL,
            prize_text varchar(255) NOT NULL,
            prize_value varchar(255) DEFAULT NULL,
            prize_type enum('discount', 'gift', 'service', 'retry', 'nothing', 'custom') DEFAULT 'discount',
            probability decimal(5,2) DEFAULT 0.00,
            color varchar(20) DEFAULT '#ff6b6b',
            total_available int(11) DEFAULT -1,
            total_won int(11) DEFAULT 0,
            is_active tinyint(1) DEFAULT 1,
            position_order int(11) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY wheel_id (wheel_id),
            KEY is_active (is_active)
        ) $charset_collate;";
        dbDelta($sql_wheel_prizes);

        // Table 15: Kata Wheel Spins - Store spin history and results
        $table_wheel_spins = $wpdb->prefix . 'kata_wheel_spins';
        $sql_wheel_spins = "CREATE TABLE IF NOT EXISTS $table_wheel_spins (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wheel_id bigint(20) NOT NULL,
            prize_id bigint(20) DEFAULT NULL,
            user_id bigint(20) DEFAULT NULL,
            user_email varchar(255) DEFAULT NULL,
            user_phone varchar(50) DEFAULT NULL,
            user_name varchar(255) DEFAULT NULL,
            user_ip varchar(45) DEFAULT NULL,
            user_agent text DEFAULT NULL,
            prize_text varchar(255) DEFAULT NULL,
            prize_type varchar(50) DEFAULT NULL,
            prize_value varchar(255) DEFAULT NULL,
            is_claimed tinyint(1) DEFAULT 0,
            claimed_at datetime DEFAULT NULL,
            referrer varchar(500) DEFAULT NULL,
            utm_source varchar(100) DEFAULT NULL,
            utm_medium varchar(100) DEFAULT NULL,
            utm_campaign varchar(100) DEFAULT NULL,
            device_type varchar(50) DEFAULT NULL,
            browser varchar(100) DEFAULT NULL,
            country varchar(100) DEFAULT NULL,
            city varchar(100) DEFAULT NULL,
            spin_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY wheel_id (wheel_id),
            KEY prize_id (prize_id),
            KEY user_id (user_id),
            KEY user_email (user_email),
            KEY user_ip (user_ip),
            KEY spin_date (spin_date),
            KEY created_at (created_at)
        ) $charset_collate;";
        dbDelta($sql_wheel_spins);

        // Table 16: Kata Wheel Analytics - Daily aggregated statistics
        $table_wheel_analytics = $wpdb->prefix . 'kata_wheel_analytics';
        $sql_wheel_analytics = "CREATE TABLE IF NOT EXISTS $table_wheel_analytics (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            wheel_id bigint(20) NOT NULL,
            date date NOT NULL,
            total_views int(11) DEFAULT 0,
            total_spins int(11) DEFAULT 0,
            total_prizes_won int(11) DEFAULT 0,
            total_emails_collected int(11) DEFAULT 0,
            total_phones_collected int(11) DEFAULT 0,
            unique_users int(11) DEFAULT 0,
            conversion_rate decimal(5,2) DEFAULT 0.00,
            avg_spins_per_user decimal(5,2) DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY wheel_id (wheel_id),
            KEY date (date),
            UNIQUE KEY wheel_date (wheel_id, date)
        ) $charset_collate;";
        dbDelta($sql_wheel_analytics);
        
        // Insert default templates
        $this->insert_default_templates();
        
        // Return true on success
        return true;
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
            $wpdb->prefix . 'kata_seo_activity_log',
            $wpdb->prefix . 'kata_polls',
            $wpdb->prefix . 'kata_poll_votes'
        );
        
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }
    }
    
    /**
     * Verify that all required tables exist
     * 
     * @return bool True if all tables exist, false otherwise
     */
    public function verify_tables() {
        global $wpdb;
        
        $required_tables = array(
            $wpdb->prefix . 'kata_seo_schemas',
            $wpdb->prefix . 'kata_seo_schema_stats',
            $wpdb->prefix . 'kata_seo_schema_validation',
            $wpdb->prefix . 'kata_seo_schema_templates',
            $wpdb->prefix . 'kata_seo_schema_tracking',
            $wpdb->prefix . 'kata_polls',
            $wpdb->prefix . 'kata_poll_votes'
        );
        
        foreach ($required_tables as $table) {
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
            if ($table_exists !== $table) {
                return false;
            }
        }
        
        return true;
    }
}
