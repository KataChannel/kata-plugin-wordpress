<?php
/**
 * Main SEO Analyzer class for Kata SEO Analyzer
 *
 * @package KataSEOAnalyzer
 * @subpackage Analyzer
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataSEO_Analyzer {
    
    /**
     * Single instance
     */
    private static $instance = null;
    
    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Analysis factors and their weights
     */
    private $analysis_factors = array(
        'title_length' => 15,
        'description_length' => 15,
        'focus_keyword' => 20,
        'keyword_in_title' => 20,
        'keyword_in_description' => 15,
        'keyword_density' => 10,
        'content_length' => 10,
        'headings_structure' => 10,
        'images_alt' => 8,
        'internal_links' => 7,
        'external_links' => 5,
        'readability' => 10,
        'url_structure' => 8,
        'social_meta' => 5,
        'schema_markup' => 7
    );
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize analyzer
     */
    private function init() {
        // Hook into post save to auto-analyze
        add_action('save_post', array($this, 'maybeAutoAnalyze'), 20, 2);
    }
    
    /**
     * Analyze a post comprehensively
     */
    public function analyzePost($post_id, $analysis_type = 'content') {
        // Multiple layers of recursion prevention
        static $analyzing_posts = array();
        
        // Check if we're already analyzing this specific post
        if (isset($analyzing_posts[$post_id])) {
            return false;
        }
        
        // Prevent infinite loops during analysis
        if (defined('KATA_SEO_ANALYZING') && KATA_SEO_ANALYZING) {
            return false;
        }
        
        // Set flags to prevent recursion
        define('KATA_SEO_ANALYZING', true);
        $analyzing_posts[$post_id] = true;
        
        $post = get_post($post_id);
        if (!$post) {
            unset($analyzing_posts[$post_id]);
            return new WP_Error('invalid_post', __('Invalid post ID', 'kata-seo-analyzer'));
        }
        
        $results = array();
        
        switch ($analysis_type) {
            case 'content':
                $results = $this->analyzeContent($post);
                break;
            case 'technical':
                $results = $this->analyzeTechnical($post);
                break;
            case 'full':
                $results = array_merge(
                    $this->analyzeContent($post),
                    $this->analyzeTechnical($post)
                );
                break;
            default:
                $results = $this->analyzeContent($post);
        }
        
        // Calculate overall score
        $score = $this->calculateScoreFromResults($results);
        
        // Save results to database
        $this->saveAnalysisResults($post_id, $analysis_type, $score, $results);
        
        // Update post meta (remove actions temporarily to prevent recursion)
        $removed_actions = array();
        
        // Remove save_post actions to prevent infinite loops
        global $wp_filter;
        if (isset($wp_filter['save_post'])) {
            $removed_actions = $wp_filter['save_post']->callbacks;
            $wp_filter['save_post']->callbacks = array();
        }
        
        update_post_meta($post_id, '_kata_seo_score', $score);
        update_post_meta($post_id, '_kata_seo_last_analysis', current_time('mysql'));
        
        // Restore the actions
        if (isset($wp_filter['save_post']) && !empty($removed_actions)) {
            $wp_filter['save_post']->callbacks = $removed_actions;
        }
        
        // Clean up flags
        unset($analyzing_posts[$post_id]);
        
        return array(
            'score' => $score,
            'analysis' => $results,
            'suggestions' => $this->generateSuggestions($results)
        );
    }
    
    /**
     * Analyze content factors
     */
    private function analyzeContent($post) {
        $analysis = array();
        
        // Store current shortcodes and temporarily remove them to prevent recursion
        global $shortcode_tags;
        $original_shortcodes = $shortcode_tags;
        $shortcode_tags = array(); // Temporarily disable all shortcodes
        
        $content = $post->post_content;
        $title = $post->post_title;
        $excerpt = $post->post_excerpt;
        
        // Strip shortcodes from content safely without processing them
        $content = strip_shortcodes($content);
        
        // Get SEO meta data (support for popular SEO plugins)
        $seo_title = $this->getSEOTitle($post);
        $seo_description = $this->getSEODescription($post);
        $focus_keyword = $this->getFocusKeyword($post);
        
        // 1. Title Length Analysis
        $analysis['title_length'] = $this->analyzeTitleLength($seo_title);
        
        // 2. Meta Description Analysis
        $analysis['description_length'] = $this->analyzeDescriptionLength($seo_description);
        
        // 3. Focus Keyword Analysis
        $analysis['focus_keyword'] = $this->analyzeFocusKeyword($focus_keyword);
        
        // 4. Keyword in Title
        $analysis['keyword_in_title'] = $this->analyzeKeywordInTitle($seo_title, $focus_keyword);
        
        // 5. Keyword in Description
        $analysis['keyword_in_description'] = $this->analyzeKeywordInDescription($seo_description, $focus_keyword);
        
        // 6. Keyword Density
        $analysis['keyword_density'] = $this->analyzeKeywordDensity($content, $focus_keyword);
        
        // 7. Content Length
        $analysis['content_length'] = $this->analyzeContentLength($content);
        
        // 8. Headings Structure
        $analysis['headings_structure'] = $this->analyzeHeadingsStructure($content);
        
        // 9. Images with Alt Text
        $analysis['images_alt'] = $this->analyzeImagesAlt($content);
        
        // 10. Internal Links
        $analysis['internal_links'] = $this->analyzeInternalLinks($content);
        
        // 11. External Links
        $analysis['external_links'] = $this->analyzeExternalLinks($content);
        
        // 12. Readability
        $analysis['readability'] = $this->analyzeReadability($content);
        
        // 13. URL Structure
        $analysis['url_structure'] = $this->analyzeURLStructure($post);
        
        // 14. Social Meta Tags
        $analysis['social_meta'] = $this->analyzeSocialMeta($post);
        
        // 15. Schema Markup
        $analysis['schema_markup'] = $this->analyzeSchemaMarkup($post);
        
        // Restore original shortcodes
        $shortcode_tags = $original_shortcodes;
        
        return $analysis;
    }
    
    /**
     * Analyze technical SEO factors
     */
    private function analyzeTechnical($post) {
        $analysis = array();
        
        // Technical SEO analysis
        $analysis['page_speed'] = $this->analyzePageSpeed($post);
        $analysis['mobile_friendly'] = $this->analyzeMobileFriendly($post);
        $analysis['ssl_certificate'] = $this->analyzeSSL();
        $analysis['xml_sitemap'] = $this->analyzeXMLSitemap();
        $analysis['robots_txt'] = $this->analyzeRobotsTxt();
        $analysis['canonical_url'] = $this->analyzeCanonicalURL($post);
        $analysis['duplicate_content'] = $this->analyzeDuplicateContent($post);
        
        return $analysis;
    }
    
    /**
     * Calculate SEO score for a post
     */
    public function calculateSEOScore($post_id) {
        global $wpdb;
        
        // Get latest analysis
        $latest_analysis = $wpdb->get_row($wpdb->prepare(
            "SELECT score, results FROM {$wpdb->prefix}kata_seo_analysis 
             WHERE post_id = %d AND analysis_type = 'content'
             ORDER BY created_at DESC LIMIT 1",
            $post_id
        ));
        
        if ($latest_analysis) {
            return intval($latest_analysis->score);
        }
        
        // If no analysis exists, perform one
        $results = $this->analyzePost($post_id);
        return isset($results['score']) ? $results['score'] : 0;
    }
    
    /**
     * Get post analysis results
     */
    public function getPostAnalysis($post_id) {
        global $wpdb;
        
        $analysis = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_seo_analysis 
             WHERE post_id = %d AND analysis_type = 'content'
             ORDER BY created_at DESC LIMIT 1",
            $post_id
        ));
        
        if ($analysis) {
            $results = json_decode($analysis->results, true);
            return $results;
        }
        
        return null;
    }
    
    /**
     * Get top issues for a post
     */
    public function getTopIssues($post_id, $limit = 3) {
        $analysis = $this->getPostAnalysis($post_id);
        
        if (!$analysis || !isset($analysis['analysis'])) {
            return array();
        }
        
        $issues = array();
        foreach ($analysis['analysis'] as $factor => $data) {
            if (!$data['status'] && isset($data['name'])) {
                $issues[] = $data['name'];
            }
        }
        
        return array_slice($issues, 0, $limit);
    }
    
    /**
     * Get technical issues
     */
    public function getTechnicalIssues() {
        // Analyze site-wide technical issues
        $issues = array();
        
        // Check for common technical issues
        $issues['ssl'] = $this->analyzeSSL();
        $issues['sitemap'] = $this->analyzeXMLSitemap();
        $issues['robots'] = $this->analyzeRobotsTxt();
        $issues['speed'] = $this->analyzeSiteSpeed();
        $issues['mobile'] = $this->analyzeMobileFriendly();
        
        return $issues;
    }
    
    /**
     * Individual analysis methods
     */
    
    private function analyzeTitleLength($title) {
        $length = mb_strlen($title);
        $optimal_min = 30;
        $optimal_max = 60;
        
        $status = ($length >= $optimal_min && $length <= $optimal_max);
        
        return array(
            'name' => __('Title Length (30-60 characters)', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['title_length'],
            'current' => $length,
            'optimal' => "{$optimal_min}-{$optimal_max}",
            'suggestion' => $this->getTitleLengthSuggestion($length, $optimal_min, $optimal_max)
        );
    }
    
    private function analyzeDescriptionLength($description) {
        $length = mb_strlen($description);
        $optimal_min = 120;
        $optimal_max = 160;
        
        $status = ($length >= $optimal_min && $length <= $optimal_max);
        
        return array(
            'name' => __('Meta Description Length (120-160 characters)', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['description_length'],
            'current' => $length,
            'optimal' => "{$optimal_min}-{$optimal_max}",
            'suggestion' => $this->getDescriptionLengthSuggestion($length, $optimal_min, $optimal_max)
        );
    }
    
    private function analyzeFocusKeyword($keyword) {
        $has_keyword = !empty(trim($keyword));
        
        return array(
            'name' => __('Focus Keyword Set', 'kata-seo-analyzer'),
            'status' => $has_keyword,
            'weight' => $this->analysis_factors['focus_keyword'],
            'current' => $keyword ?: __('Not set', 'kata-seo-analyzer'),
            'suggestion' => $has_keyword ? '' : __('Set a focus keyword to optimize your content', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeKeywordInTitle($title, $keyword) {
        if (empty($keyword)) {
            return array(
                'name' => __('Keyword in Title', 'kata-seo-analyzer'),
                'status' => false,
                'weight' => $this->analysis_factors['keyword_in_title'],
                'suggestion' => __('Set a focus keyword first', 'kata-seo-analyzer')
            );
        }
        
        $keyword_in_title = stripos($title, $keyword) !== false;
        
        return array(
            'name' => __('Keyword in Title', 'kata-seo-analyzer'),
            'status' => $keyword_in_title,
            'weight' => $this->analysis_factors['keyword_in_title'],
            'current' => $keyword_in_title ? __('Found', 'kata-seo-analyzer') : __('Not found', 'kata-seo-analyzer'),
            'suggestion' => $keyword_in_title ? '' : __('Include your focus keyword in the title', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeKeywordInDescription($description, $keyword) {
        if (empty($keyword)) {
            return array(
                'name' => __('Keyword in Description', 'kata-seo-analyzer'),
                'status' => false,
                'weight' => $this->analysis_factors['keyword_in_description'],
                'suggestion' => __('Set a focus keyword first', 'kata-seo-analyzer')
            );
        }
        
        $keyword_in_desc = stripos($description, $keyword) !== false;
        
        return array(
            'name' => __('Keyword in Description', 'kata-seo-analyzer'),
            'status' => $keyword_in_desc,
            'weight' => $this->analysis_factors['keyword_in_description'],
            'current' => $keyword_in_desc ? __('Found', 'kata-seo-analyzer') : __('Not found', 'kata-seo-analyzer'),
            'suggestion' => $keyword_in_desc ? '' : __('Include your focus keyword in the meta description', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeKeywordDensity($content, $keyword) {
        if (empty($keyword)) {
            return array(
                'name' => __('Keyword Density', 'kata-seo-analyzer'),
                'status' => false,
                'weight' => $this->analysis_factors['keyword_density'],
                'suggestion' => __('Set a focus keyword first', 'kata-seo-analyzer')
            );
        }
        
        $content_text = wp_strip_all_tags($content);
        $word_count = str_word_count($content_text);
        $keyword_count = substr_count(strtolower($content_text), strtolower($keyword));
        
        $density = $word_count > 0 ? ($keyword_count / $word_count) * 100 : 0;
        $optimal_min = 0.5;
        $optimal_max = 2.5;
        
        $status = ($density >= $optimal_min && $density <= $optimal_max);
        
        return array(
            'name' => __('Keyword Density (0.5-2.5%)', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['keyword_density'],
            'current' => round($density, 2) . '%',
            'optimal' => "{$optimal_min}%-{$optimal_max}%",
            'suggestion' => $this->getKeywordDensitySuggestion($density, $optimal_min, $optimal_max)
        );
    }
    
    private function analyzeContentLength($content) {
        $word_count = str_word_count(wp_strip_all_tags($content));
        $min_words = 300;
        
        $status = ($word_count >= $min_words);
        
        return array(
            'name' => sprintf(__('Content Length (min %d words)', 'kata-seo-analyzer'), $min_words),
            'status' => $status,
            'weight' => $this->analysis_factors['content_length'],
            'current' => $word_count,
            'optimal' => "≥{$min_words}",
            'suggestion' => $status ? '' : sprintf(__('Add more content. Aim for at least %d words.', 'kata-seo-analyzer'), $min_words)
        );
    }
    
    private function analyzeHeadingsStructure($content) {
        // Check for proper heading hierarchy
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $headings);
        
        $has_h1 = in_array('1', $headings[1]);
        $has_subheadings = count(array_filter($headings[1], function($h) { return $h > 1; })) > 0;
        
        $status = $has_h1 && $has_subheadings;
        
        return array(
            'name' => __('Proper Heading Structure', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['headings_structure'],
            'current' => sprintf(__('H1: %s, Subheadings: %s', 'kata-seo-analyzer'), 
                        $has_h1 ? __('Yes', 'kata-seo-analyzer') : __('No', 'kata-seo-analyzer'),
                        $has_subheadings ? __('Yes', 'kata-seo-analyzer') : __('No', 'kata-seo-analyzer')),
            'suggestion' => $this->getHeadingStructureSuggestion($has_h1, $has_subheadings)
        );
    }
    
    private function analyzeImagesAlt($content) {
        preg_match_all('/<img[^>]+>/i', $content, $images);
        $total_images = count($images[0]);
        $images_with_alt = 0;
        
        foreach ($images[0] as $img) {
            if (preg_match('/alt=["\'][^"\']*["\']/', $img)) {
                $images_with_alt++;
            }
        }
        
        $status = ($total_images === 0) || ($images_with_alt === $total_images);
        
        return array(
            'name' => __('Images with Alt Text', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['images_alt'],
            'current' => "{$images_with_alt}/{$total_images}",
            'suggestion' => $status ? '' : __('Add alt text to all images for better accessibility and SEO', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeInternalLinks($content) {
        $home_url = home_url();
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $links);
        
        $internal_links = 0;
        foreach ($links[1] as $link) {
            if (strpos($link, $home_url) === 0 || strpos($link, '/') === 0) {
                $internal_links++;
            }
        }
        
        $status = ($internal_links >= 2);
        
        return array(
            'name' => __('Internal Links (min 2)', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['internal_links'],
            'current' => $internal_links,
            'optimal' => '≥2',
            'suggestion' => $status ? '' : __('Add internal links to related content on your site', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeExternalLinks($content) {
        $home_url = home_url();
        preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i', $content, $links);
        
        $external_links = 0;
        foreach ($links[1] as $link) {
            if (strpos($link, 'http') === 0 && strpos($link, $home_url) !== 0) {
                $external_links++;
            }
        }
        
        $status = ($external_links >= 1);
        
        return array(
            'name' => __('External Links (min 1)', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['external_links'],
            'current' => $external_links,
            'optimal' => '≥1',
            'suggestion' => $status ? '' : __('Add at least one external link to authoritative sources', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeReadability($content) {
        // Simplified readability analysis
        $text = wp_strip_all_tags($content);
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        
        $avg_sentence_length = count($sentences) > 0 ? $words / count($sentences) : 0;
        $status = ($avg_sentence_length <= 20); // Target: max 20 words per sentence
        
        return array(
            'name' => __('Readability (avg sentence ≤20 words)', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['readability'],
            'current' => round($avg_sentence_length, 1),
            'optimal' => '≤20',
            'suggestion' => $status ? '' : __('Use shorter sentences to improve readability', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeURLStructure($post) {
        $permalink = get_permalink($post->ID);
        $url_path = parse_url($permalink, PHP_URL_PATH);
        
        // Check for SEO-friendly URL
        $has_numbers = preg_match('/\d/', $url_path);
        $has_special_chars = preg_match('/[^a-zA-Z0-9\-\/]/', $url_path);
        $is_too_long = strlen($url_path) > 100;
        
        $status = !$has_special_chars && !$is_too_long;
        
        return array(
            'name' => __('SEO-friendly URL Structure', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['url_structure'],
            'current' => $url_path,
            'suggestion' => $this->getURLStructureSuggestion($has_special_chars, $is_too_long)
        );
    }
    
    private function analyzeSocialMeta($post) {
        // Check for Open Graph and Twitter Card meta tags
        $has_og_title = !empty(get_post_meta($post->ID, '_yoast_wpseo_opengraph-title', true)) ||
                       !empty(get_post_meta($post->ID, 'rank_math_facebook_title', true));
        
        $has_og_description = !empty(get_post_meta($post->ID, '_yoast_wpseo_opengraph-description', true)) ||
                            !empty(get_post_meta($post->ID, 'rank_math_facebook_description', true));
        
        $status = $has_og_title && $has_og_description;
        
        return array(
            'name' => __('Social Media Meta Tags', 'kata-seo-analyzer'),
            'status' => $status,
            'weight' => $this->analysis_factors['social_meta'],
            'suggestion' => $status ? '' : __('Add Open Graph and Twitter Card meta tags for better social sharing', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeSchemaMarkup($post) {
        // Basic schema markup detection
        $content = $post->post_content;
        $has_schema = strpos($content, 'application/ld+json') !== false ||
                     strpos($content, 'itemscope') !== false;
        
        return array(
            'name' => __('Schema Markup', 'kata-seo-analyzer'),
            'status' => $has_schema,
            'weight' => $this->analysis_factors['schema_markup'],
            'suggestion' => $has_schema ? '' : __('Add structured data markup to help search engines understand your content', 'kata-seo-analyzer')
        );
    }
    
    /**
     * Technical analysis methods
     */
    
    private function analyzePageSpeed($post) {
        // Simplified page speed check
        $url = get_permalink($post->ID);
        $start_time = microtime(true);
        
        $response = wp_remote_get($url, array('timeout' => 10));
        $load_time = (microtime(true) - $start_time) * 1000;
        
        $status = $load_time < 3000; // Less than 3 seconds
        
        return array(
            'name' => __('Page Load Speed (<3s)', 'kata-seo-analyzer'),
            'status' => $status,
            'current' => round($load_time, 0) . 'ms',
            'optimal' => '<3000ms',
            'suggestion' => $status ? '' : __('Optimize images and use caching to improve page speed', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeMobileFriendly($post = null) {
        // Check for mobile-friendly indicators
        $has_viewport = false;
        $has_responsive_theme = current_theme_supports('responsive-layout');
        
        // Check for viewport meta tag
        ob_start();
        wp_head();
        $head_content = ob_get_clean();
        
        if (strpos($head_content, 'viewport') !== false) {
            $has_viewport = true;
        }
        
        $status = $has_viewport || $has_responsive_theme;
        
        return array(
            'name' => __('Mobile Friendly', 'kata-seo-analyzer'),
            'status' => $status,
            'suggestion' => $status ? '' : __('Ensure your theme is responsive and includes viewport meta tag', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeSSL() {
        $is_ssl = is_ssl();
        
        return array(
            'name' => __('SSL Certificate', 'kata-seo-analyzer'),
            'status' => $is_ssl,
            'suggestion' => $is_ssl ? '' : __('Install SSL certificate to secure your website', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeXMLSitemap() {
        // Check for common sitemap locations
        $sitemap_urls = array(
            home_url('sitemap.xml'),
            home_url('sitemap_index.xml'),
            home_url('wp-sitemap.xml')
        );
        
        $has_sitemap = false;
        foreach ($sitemap_urls as $url) {
            $response = wp_remote_head($url);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $has_sitemap = true;
                break;
            }
        }
        
        return array(
            'name' => __('XML Sitemap', 'kata-seo-analyzer'),
            'status' => $has_sitemap,
            'suggestion' => $has_sitemap ? '' : __('Generate and submit an XML sitemap to search engines', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeRobotsTxt() {
        $robots_url = home_url('robots.txt');
        $response = wp_remote_get($robots_url);
        
        $has_robots = !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
        
        return array(
            'name' => __('Robots.txt File', 'kata-seo-analyzer'),
            'status' => $has_robots,
            'suggestion' => $has_robots ? '' : __('Create a robots.txt file to guide search engine crawlers', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeCanonicalURL($post) {
        $canonical = get_post_meta($post->ID, '_yoast_wpseo_canonical', true) ?: 
                    get_post_meta($post->ID, 'rank_math_canonical_url', true);
        
        $has_canonical = !empty($canonical) || !empty(wp_get_canonical_url($post->ID));
        
        return array(
            'name' => __('Canonical URL', 'kata-seo-analyzer'),
            'status' => $has_canonical,
            'suggestion' => $has_canonical ? '' : __('Set canonical URL to prevent duplicate content issues', 'kata-seo-analyzer')
        );
    }
    
    private function analyzeDuplicateContent($post) {
        // Basic duplicate content check (simplified)
        $content = $post->post_content;
        $content_hash = md5(wp_strip_all_tags($content));
        
        global $wpdb;
        $duplicate = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} 
             WHERE meta_key = '_kata_seo_content_hash' 
             AND meta_value = %s 
             AND post_id != %d",
            $content_hash,
            $post->ID
        ));
        
        $has_duplicate = !empty($duplicate);
        
        // Store content hash for future checks
        update_post_meta($post->ID, '_kata_seo_content_hash', $content_hash);
        
        return array(
            'name' => __('No Duplicate Content', 'kata-seo-analyzer'),
            'status' => !$has_duplicate,
            'suggestion' => $has_duplicate ? __('This content appears to be duplicated on another page', 'kata-seo-analyzer') : ''
        );
    }
    
    private function analyzeSiteSpeed() {
        // Site-wide speed analysis
        $home_url = home_url();
        $start_time = microtime(true);
        
        $response = wp_remote_get($home_url, array('timeout' => 10));
        $load_time = (microtime(true) - $start_time) * 1000;
        
        $status = $load_time < 3000;
        
        return array(
            'name' => __('Site Speed', 'kata-seo-analyzer'),
            'status' => $status,
            'current' => round($load_time, 0) . 'ms',
            'suggestion' => $status ? '' : __('Optimize your site speed with caching, image optimization, and CDN', 'kata-seo-analyzer')
        );
    }
    
    /**
     * Helper methods
     */
    
    private function getSEOTitle($post) {
        // Support multiple SEO plugins
        $seo_title = get_post_meta($post->ID, '_yoast_wpseo_title', true) ?:
                    get_post_meta($post->ID, 'rank_math_title', true) ?:
                    get_post_meta($post->ID, '_aioseo_title', true) ?:
                    $post->post_title;
        
        return $seo_title;
    }
    
    private function getSEODescription($post) {
        $seo_description = get_post_meta($post->ID, '_yoast_wpseo_metadesc', true) ?:
                          get_post_meta($post->ID, 'rank_math_description', true) ?:
                          get_post_meta($post->ID, '_aioseo_description', true) ?:
                          $post->post_excerpt;
        
        return $seo_description;
    }
    
    private function getFocusKeyword($post) {
        $focus_keyword = get_post_meta($post->ID, '_yoast_wpseo_focuskw', true) ?:
                        get_post_meta($post->ID, 'rank_math_focus_keyword', true) ?:
                        get_post_meta($post->ID, '_aioseo_keyphrases', true);
        
        return $focus_keyword;
    }
    
    private function calculateScoreFromResults($results) {
        $total_weight = 0;
        $achieved_weight = 0;
        
        foreach ($results as $factor => $data) {
            if (isset($data['weight'])) {
                $total_weight += $data['weight'];
                if ($data['status']) {
                    $achieved_weight += $data['weight'];
                }
            }
        }
        
        return $total_weight > 0 ? round(($achieved_weight / $total_weight) * 100) : 0;
    }
    
    private function saveAnalysisResults($post_id, $analysis_type, $score, $results) {
        global $wpdb;
        
        $wpdb->replace(
            $wpdb->prefix . 'kata_seo_analysis',
            array(
                'post_id' => $post_id,
                'analysis_type' => $analysis_type,
                'score' => $score,
                'results' => json_encode(array(
                    'score' => $score,
                    'analysis' => $results,
                    'timestamp' => current_time('mysql')
                )),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array('%d', '%s', '%d', '%s', '%s', '%s')
        );
    }
    
    private function generateSuggestions($results) {
        $suggestions = array();
        
        foreach ($results as $factor => $data) {
            if (!$data['status'] && !empty($data['suggestion'])) {
                $priority = 'medium';
                if (isset($data['weight'])) {
                    $priority = $data['weight'] >= 15 ? 'high' : ($data['weight'] >= 10 ? 'medium' : 'low');
                }
                
                $suggestions[] = array(
                    'factor' => $data['name'],
                    'suggestion' => $data['suggestion'],
                    'priority' => $priority,
                    'weight' => isset($data['weight']) ? $data['weight'] : 0
                );
            }
        }
        
        // Sort by priority and weight
        usort($suggestions, function($a, $b) {
            $priority_order = array('high' => 3, 'medium' => 2, 'low' => 1);
            $priority_diff = $priority_order[$b['priority']] - $priority_order[$a['priority']];
            return $priority_diff !== 0 ? $priority_diff : $b['weight'] - $a['weight'];
        });
        
        return $suggestions;
    }
    
    /**
     * Suggestion helper methods
     */
    
    private function getTitleLengthSuggestion($length, $min, $max) {
        if ($length < $min) {
            return sprintf(__('Title is too short. Add %d more characters.', 'kata-seo-analyzer'), $min - $length);
        } elseif ($length > $max) {
            return sprintf(__('Title is too long. Remove %d characters.', 'kata-seo-analyzer'), $length - $max);
        }
        return '';
    }
    
    private function getDescriptionLengthSuggestion($length, $min, $max) {
        if ($length < $min) {
            return sprintf(__('Description is too short. Add %d more characters.', 'kata-seo-analyzer'), $min - $length);
        } elseif ($length > $max) {
            return sprintf(__('Description is too long. Remove %d characters.', 'kata-seo-analyzer'), $length - $max);
        }
        return '';
    }
    
    private function getKeywordDensitySuggestion($density, $min, $max) {
        if ($density < $min) {
            return __('Keyword density is too low. Use your focus keyword more naturally in the content.', 'kata-seo-analyzer');
        } elseif ($density > $max) {
            return __('Keyword density is too high. Reduce keyword usage to avoid over-optimization.', 'kata-seo-analyzer');
        }
        return '';
    }
    
    private function getHeadingStructureSuggestion($has_h1, $has_subheadings) {
        if (!$has_h1 && !$has_subheadings) {
            return __('Add proper heading structure with H1 and subheadings (H2, H3, etc.)', 'kata-seo-analyzer');
        } elseif (!$has_h1) {
            return __('Add an H1 heading to your content', 'kata-seo-analyzer');
        } elseif (!$has_subheadings) {
            return __('Add subheadings (H2, H3, etc.) to organize your content', 'kata-seo-analyzer');
        }
        return '';
    }
    
    private function getURLStructureSuggestion($has_special_chars, $is_too_long) {
        $suggestions = array();
        if ($has_special_chars) {
            $suggestions[] = __('Remove special characters from URL', 'kata-seo-analyzer');
        }
        if ($is_too_long) {
            $suggestions[] = __('Shorten the URL length', 'kata-seo-analyzer');
        }
        return implode('. ', $suggestions);
    }
    
    /**
     * Auto-analyze on post save
     */
    public function maybeAutoAnalyze($post_id, $post) {
        // Skip for autosave, revisions, etc.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (wp_is_post_revision($post_id)) {
            return;
        }
        
        // Only analyze published posts/pages
        if (!in_array($post->post_status, array('publish', 'private'))) {
            return;
        }
        
        // Check if auto-analyze is enabled
        if (!get_option('kata_seo_auto_analyze', true)) {
            return;
        }
        
        // Schedule analysis (to avoid slowing down post save)
        wp_schedule_single_event(time() + 10, 'kata_seo_analyze_post', array($post_id));
    }
}

// Hook for scheduled analysis
add_action('kata_seo_analyze_post', function($post_id) {
    kata_seo()->analyzer->analyzePost($post_id);
});
