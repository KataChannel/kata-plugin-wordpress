<?php
/**
 * Competitor Analysis class for Kata SEO Analyzer
 *
 * @package KataSEOAnalyzer
 * @subpackage Analyzer
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataSEO_Competitor_Analyzer {
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init();
    }
    
    /**
     * Initialize competitor analysis
     */
    private function init() {
        // Schedule competitor checks
        add_action('kata_seo_competitor_check', array($this, 'checkAllCompetitors'));
    }
    
    /**
     * Add a competitor to track
     */
    public function addCompetitor($domain, $keywords = array()) {
        global $wpdb;
        
        // Validate domain
        if (!$this->isValidDomain($domain)) {
            return new WP_Error('invalid_domain', __('Invalid domain format', 'kata-seo-analyzer'));
        }
        
        // Check if competitor already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}kata_seo_competitors WHERE domain = %s",
            $domain
        ));
        
        if ($existing) {
            return new WP_Error('competitor_exists', __('Competitor already exists', 'kata-seo-analyzer'));
        }
        
        // Add competitor
        $result = $wpdb->insert(
            $wpdb->prefix . 'kata_seo_competitors',
            array(
                'domain' => $domain,
                'keywords' => json_encode($keywords),
                'data' => json_encode(array()),
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('db_error', __('Failed to add competitor', 'kata-seo-analyzer'));
        }
        
        // Initial analysis
        $this->analyzeCompetitor($wpdb->insert_id);
        
        return $wpdb->insert_id;
    }
    
    /**
     * Remove a competitor
     */
    public function removeCompetitor($competitor_id) {
        global $wpdb;
        
        $result = $wpdb->delete(
            $wpdb->prefix . 'kata_seo_competitors',
            array('id' => $competitor_id),
            array('%d')
        );
        
        return $result !== false;
    }
    
    /**
     * Get all competitors
     */
    public function getAllCompetitors() {
        global $wpdb;
        
        $competitors = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}kata_seo_competitors 
             ORDER BY created_at DESC"
        );
        
        foreach ($competitors as &$competitor) {
            $competitor->keywords = json_decode($competitor->keywords, true);
            $competitor->data = json_decode($competitor->data, true);
        }
        
        return $competitors;
    }
    
    /**
     * Get a specific competitor
     */
    public function getCompetitor($competitor_id) {
        global $wpdb;
        
        $competitor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}kata_seo_competitors WHERE id = %d",
            $competitor_id
        ));
        
        if ($competitor) {
            $competitor->keywords = json_decode($competitor->keywords, true);
            $competitor->data = json_decode($competitor->data, true);
        }
        
        return $competitor;
    }
    
    /**
     * Analyze a competitor
     */
    public function analyzeCompetitor($competitor_id) {
        $competitor = $this->getCompetitor($competitor_id);
        
        if (!$competitor) {
            return new WP_Error('competitor_not_found', __('Competitor not found', 'kata-seo-analyzer'));
        }
        
        $analysis_data = array();
        
        // Basic website analysis
        $analysis_data['website_analysis'] = $this->analyzeCompetitorWebsite($competitor->domain);
        
        // Content analysis
        $analysis_data['content_analysis'] = $this->analyzeCompetitorContent($competitor->domain);
        
        // Technical SEO analysis
        $analysis_data['technical_analysis'] = $this->analyzeCompetitorTechnical($competitor->domain);
        
        // Keyword analysis
        if (!empty($competitor->keywords)) {
            $analysis_data['keyword_analysis'] = $this->analyzeCompetitorKeywords($competitor->domain, $competitor->keywords);
        }
        
        // Backlink analysis (simplified)
        $analysis_data['backlink_analysis'] = $this->analyzeCompetitorBacklinks($competitor->domain);
        
        // Social media presence
        $analysis_data['social_analysis'] = $this->analyzeCompetitorSocial($competitor->domain);
        
        // Save analysis results
        $this->saveCompetitorAnalysis($competitor_id, $analysis_data);
        
        return $analysis_data;
    }
    
    /**
     * Analyze competitor website basics
     */
    private function analyzeCompetitorWebsite($domain) {
        $url = 'https://' . $domain;
        
        $analysis = array(
            'url' => $url,
            'status' => 'unknown',
            'response_time' => 0,
            'title' => '',
            'description' => '',
            'h1_count' => 0,
            'image_count' => 0,
            'links_count' => 0,
            'word_count' => 0,
            'has_ssl' => false,
            'mobile_friendly' => false
        );
        
        // Fetch homepage
        $start_time = microtime(true);
        $response = wp_remote_get($url, array(
            'timeout' => 15,
            'user-agent' => 'Kata SEO Analyzer Bot 1.0'
        ));
        $response_time = (microtime(true) - $start_time) * 1000;
        
        if (is_wp_error($response)) {
            $analysis['status'] = 'error';
            $analysis['error'] = $response->get_error_message();
            return $analysis;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $analysis['status'] = $status_code;
        $analysis['response_time'] = round($response_time, 2);
        
        if ($status_code !== 200) {
            return $analysis;
        }
        
        $body = wp_remote_retrieve_body($response);
        $analysis['has_ssl'] = strpos($url, 'https://') === 0;
        
        // Parse HTML content
        if (!empty($body)) {
            // Extract title
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $body, $matches)) {
                $analysis['title'] = trim(strip_tags($matches[1]));
            }
            
            // Extract meta description
            if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $body, $matches)) {
                $analysis['description'] = trim($matches[1]);
            }
            
            // Count H1 tags
            $analysis['h1_count'] = preg_match_all('/<h1[^>]*>/i', $body);
            
            // Count images
            $analysis['image_count'] = preg_match_all('/<img[^>]*>/i', $body);
            
            // Count links
            $analysis['links_count'] = preg_match_all('/<a[^>]+href/i', $body);
            
            // Word count (approximate)
            $text_content = strip_tags($body);
            $analysis['word_count'] = str_word_count($text_content);
            
            // Check for mobile-friendly indicators
            $analysis['mobile_friendly'] = $this->checkMobileFriendly($body);
        }
        
        return $analysis;
    }
    
    /**
     * Analyze competitor content
     */
    private function analyzeCompetitorContent($domain) {
        $analysis = array(
            'blog_detected' => false,
            'recent_posts' => array(),
            'content_types' => array(),
            'posting_frequency' => 'unknown',
            'average_word_count' => 0,
            'content_topics' => array()
        );
        
        // Common blog paths to check
        $blog_paths = array('/blog/', '/news/', '/articles/', '/posts/');
        
        foreach ($blog_paths as $path) {
            $blog_url = 'https://' . $domain . $path;
            $response = wp_remote_get($blog_url, array('timeout' => 10));
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $analysis['blog_detected'] = true;
                $analysis['blog_url'] = $blog_url;
                
                // Try to extract recent posts
                $body = wp_remote_retrieve_body($response);
                $analysis['recent_posts'] = $this->extractRecentPosts($body, $domain);
                break;
            }
        }
        
        // Check for sitemap
        $sitemap_url = 'https://' . $domain . '/sitemap.xml';
        $response = wp_remote_get($sitemap_url, array('timeout' => 10));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $sitemap_content = wp_remote_retrieve_body($response);
            $analysis['content_types'] = $this->extractContentTypesFromSitemap($sitemap_content);
        }
        
        return $analysis;
    }
    
    /**
     * Analyze competitor technical SEO
     */
    private function analyzeCompetitorTechnical($domain) {
        $analysis = array(
            'has_ssl' => false,
            'has_sitemap' => false,
            'has_robots_txt' => false,
            'page_speed_score' => 0,
            'mobile_friendly' => false,
            'structured_data' => false,
            'meta_tags' => array(),
            'canonical_issues' => false,
            'redirect_chains' => false
        );
        
        $base_url = 'https://' . $domain;
        
        // Check SSL
        $analysis['has_ssl'] = $this->checkSSL($domain);
        
        // Check sitemap
        $sitemap_urls = array('/sitemap.xml', '/sitemap_index.xml', '/wp-sitemap.xml');
        foreach ($sitemap_urls as $sitemap_path) {
            $response = wp_remote_head($base_url . $sitemap_path);
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $analysis['has_sitemap'] = true;
                break;
            }
        }
        
        // Check robots.txt
        $robots_response = wp_remote_head($base_url . '/robots.txt');
        $analysis['has_robots_txt'] = !is_wp_error($robots_response) && 
                                     wp_remote_retrieve_response_code($robots_response) === 200;
        
        // Basic page speed check
        $start_time = microtime(true);
        $response = wp_remote_get($base_url, array('timeout' => 10));
        $load_time = (microtime(true) - $start_time) * 1000;
        
        // Simple scoring based on load time
        if ($load_time < 1000) {
            $analysis['page_speed_score'] = 90;
        } elseif ($load_time < 2000) {
            $analysis['page_speed_score'] = 80;
        } elseif ($load_time < 3000) {
            $analysis['page_speed_score'] = 70;
        } elseif ($load_time < 5000) {
            $analysis['page_speed_score'] = 60;
        } else {
            $analysis['page_speed_score'] = 40;
        }
        
        // Analyze homepage for technical elements
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            
            // Check for structured data
            $analysis['structured_data'] = strpos($body, 'application/ld+json') !== false ||
                                         strpos($body, 'itemscope') !== false;
            
            // Extract meta tags
            $analysis['meta_tags'] = $this->extractMetaTags($body);
            
            // Check mobile-friendly
            $analysis['mobile_friendly'] = $this->checkMobileFriendly($body);
        }
        
        return $analysis;
    }
    
    /**
     * Analyze competitor keywords
     */
    private function analyzeCompetitorKeywords($domain, $keywords) {
        $analysis = array(
            'keyword_rankings' => array(),
            'keyword_density' => array(),
            'title_optimization' => array(),
            'meta_optimization' => array(),
            'content_optimization' => array()
        );
        
        $homepage_url = 'https://' . $domain;
        $response = wp_remote_get($homepage_url, array('timeout' => 10));
        
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return $analysis;
        }
        
        $body = wp_remote_retrieve_body($response);
        $text_content = strip_tags($body);
        
        // Extract title and meta description
        $title = '';
        $meta_description = '';
        
        if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $body, $matches)) {
            $title = trim(strip_tags($matches[1]));
        }
        
        if (preg_match('/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']+)["\'][^>]*>/i', $body, $matches)) {
            $meta_description = trim($matches[1]);
        }
        
        foreach ($keywords as $keyword) {
            $keyword_lower = strtolower($keyword);
            
            // Check keyword in title
            $title_optimization = stripos($title, $keyword) !== false;
            
            // Check keyword in meta description
            $meta_optimization = stripos($meta_description, $keyword) !== false;
            
            // Calculate keyword density
            $word_count = str_word_count($text_content);
            $keyword_count = substr_count(strtolower($text_content), $keyword_lower);
            $density = $word_count > 0 ? ($keyword_count / $word_count) * 100 : 0;
            
            $analysis['keyword_density'][$keyword] = round($density, 2);
            $analysis['title_optimization'][$keyword] = $title_optimization;
            $analysis['meta_optimization'][$keyword] = $meta_optimization;
            $analysis['content_optimization'][$keyword] = $keyword_count;
        }
        
        return $analysis;
    }
    
    /**
     * Analyze competitor backlinks (simplified)
     */
    private function analyzeCompetitorBacklinks($domain) {
        // This would typically integrate with backlink analysis APIs
        // For now, provide basic analysis
        
        $analysis = array(
            'estimated_backlinks' => 0,
            'domain_authority' => 0,
            'referring_domains' => 0,
            'link_types' => array(),
            'anchor_text_distribution' => array(),
            'top_referring_domains' => array()
        );
        
        // Simplified analysis - would integrate with services like:
        // - Moz API
        // - Ahrefs API  
        // - SEMrush API
        // - Majestic API
        
        // For demonstration, return mock data
        $analysis['estimated_backlinks'] = rand(100, 10000);
        $analysis['domain_authority'] = rand(20, 80);
        $analysis['referring_domains'] = rand(50, 1000);
        
        return $analysis;
    }
    
    /**
     * Analyze competitor social media presence
     */
    private function analyzeCompetitorSocial($domain) {
        $analysis = array(
            'social_profiles' => array(),
            'social_meta_tags' => false,
            'social_sharing_buttons' => false
        );
        
        // Check homepage for social links and meta tags
        $homepage_url = 'https://' . $domain;
        $response = wp_remote_get($homepage_url, array('timeout' => 10));
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
            $body = wp_remote_retrieve_body($response);
            
            // Check for social meta tags (Open Graph, Twitter Cards)
            $analysis['social_meta_tags'] = strpos($body, 'og:') !== false || 
                                           strpos($body, 'twitter:') !== false;
            
            // Check for social sharing buttons
            $social_indicators = array('share', 'facebook', 'twitter', 'linkedin', 'social');
            foreach ($social_indicators as $indicator) {
                if (stripos($body, $indicator) !== false) {
                    $analysis['social_sharing_buttons'] = true;
                    break;
                }
            }
            
            // Extract social profile links
            $social_patterns = array(
                'facebook' => '/facebook\.com\/[a-zA-Z0-9\.\-_]+/',
                'twitter' => '/twitter\.com\/[a-zA-Z0-9_]+/',
                'linkedin' => '/linkedin\.com\/(company|in)\/[a-zA-Z0-9\-_]+/',
                'instagram' => '/instagram\.com\/[a-zA-Z0-9\.\-_]+/',
                'youtube' => '/youtube\.com\/(channel|user|c)\/[a-zA-Z0-9\-_]+/'
            );
            
            foreach ($social_patterns as $platform => $pattern) {
                if (preg_match($pattern, $body, $matches)) {
                    $analysis['social_profiles'][$platform] = $matches[0];
                }
            }
        }
        
        return $analysis;
    }
    
    /**
     * Check all competitors periodically
     */
    public function checkAllCompetitors() {
        if (!get_option('kata_seo_enable_competitor_tracking', false)) {
            return;
        }
        
        $competitors = $this->getAllCompetitors();
        
        foreach ($competitors as $competitor) {
            // Only check if not analyzed in the last 24 hours
            $last_check = strtotime($competitor->last_checked);
            if ((time() - $last_check) > DAY_IN_SECONDS) {
                $this->analyzeCompetitor($competitor->id);
            }
        }
    }
    
    /**
     * Get keyword analysis comparison
     */
    public function getKeywordAnalysis() {
        $competitors = $this->getAllCompetitors();
        $our_domain = parse_url(home_url(), PHP_URL_HOST);
        
        $analysis = array(
            'keyword_gaps' => array(),
            'keyword_opportunities' => array(),
            'ranking_comparison' => array()
        );
        
        // Collect all competitor keywords
        $all_keywords = array();
        foreach ($competitors as $competitor) {
            if (!empty($competitor->keywords)) {
                $all_keywords = array_merge($all_keywords, $competitor->keywords);
            }
        }
        
        $all_keywords = array_unique($all_keywords);
        
        // Analyze each keyword
        foreach ($all_keywords as $keyword) {
            $keyword_data = array(
                'keyword' => $keyword,
                'our_optimization' => $this->analyzeOurKeywordOptimization($keyword),
                'competitor_performance' => array()
            );
            
            foreach ($competitors as $competitor) {
                if (in_array($keyword, $competitor->keywords) && 
                    isset($competitor->data['keyword_analysis'])) {
                    
                    $keyword_analysis = $competitor->data['keyword_analysis'];
                    if (isset($keyword_analysis['keyword_density'][$keyword])) {
                        $keyword_data['competitor_performance'][$competitor->domain] = array(
                            'density' => $keyword_analysis['keyword_density'][$keyword],
                            'in_title' => $keyword_analysis['title_optimization'][$keyword],
                            'in_meta' => $keyword_analysis['meta_optimization'][$keyword]
                        );
                    }
                }
            }
            
            $analysis['ranking_comparison'][] = $keyword_data;
        }
        
        return $analysis;
    }
    
    /**
     * Get keyword gaps (keywords competitors rank for but we don't)
     */
    public function getKeywordGaps() {
        $competitors = $this->getAllCompetitors();
        $gaps = array();
        
        foreach ($competitors as $competitor) {
            if (!empty($competitor->keywords)) {
                foreach ($competitor->keywords as $keyword) {
                    // Check if we're optimizing for this keyword
                    $our_optimization = $this->analyzeOurKeywordOptimization($keyword);
                    
                    if ($our_optimization['score'] < 50) { // Weak optimization
                        $gaps[] = array(
                            'keyword' => $keyword,
                            'competitor' => $competitor->domain,
                            'our_score' => $our_optimization['score'],
                            'opportunity_score' => $this->calculateOpportunityScore($keyword, $competitor)
                        );
                    }
                }
            }
        }
        
        // Sort by opportunity score
        usort($gaps, function($a, $b) {
            return $b['opportunity_score'] - $a['opportunity_score'];
        });
        
        return array_slice($gaps, 0, 20);
    }
    
    /**
     * Get content opportunities based on competitor analysis
     */
    public function getContentOpportunities() {
        $competitors = $this->getAllCompetitors();
        $opportunities = array();
        
        foreach ($competitors as $competitor) {
            if (isset($competitor->data['content_analysis']['recent_posts'])) {
                foreach ($competitor->data['content_analysis']['recent_posts'] as $post) {
                    $opportunities[] = array(
                        'type' => 'content_gap',
                        'title' => $post['title'],
                        'competitor' => $competitor->domain,
                        'url' => $post['url'],
                        'opportunity' => 'Create similar content with better optimization'
                    );
                }
            }
        }
        
        return array_slice($opportunities, 0, 15);
    }
    
    /**
     * Helper methods
     */
    
    private function isValidDomain($domain) {
        return filter_var('http://' . $domain, FILTER_VALIDATE_URL) !== false;
    }
    
    private function checkSSL($domain) {
        $context = stream_context_create(array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        ));
        
        $result = @file_get_contents('https://' . $domain, false, $context);
        return $result !== false;
    }
    
    private function checkMobileFriendly($html) {
        // Check for mobile-friendly indicators
        $mobile_indicators = array(
            'viewport',
            'responsive',
            'mobile-friendly',
            '@media',
            'max-width'
        );
        
        foreach ($mobile_indicators as $indicator) {
            if (stripos($html, $indicator) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    private function extractMetaTags($html) {
        $meta_tags = array();
        
        // Extract all meta tags
        if (preg_match_all('/<meta[^>]+>/i', $html, $matches)) {
            foreach ($matches[0] as $meta) {
                if (preg_match('/name=["\']([^"\']+)["\']/', $meta, $name_match) &&
                    preg_match('/content=["\']([^"\']+)["\']/', $meta, $content_match)) {
                    $meta_tags[$name_match[1]] = $content_match[1];
                }
            }
        }
        
        return $meta_tags;
    }
    
    private function extractRecentPosts($html, $domain) {
        $posts = array();
        
        // Try to extract post titles and URLs
        if (preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/i', $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $url = $match[1];
                $title = trim(strip_tags($match[2]));
                
                // Make sure URL is absolute
                if (strpos($url, 'http') !== 0) {
                    $url = 'https://' . $domain . $url;
                }
                
                if (strlen($title) > 10 && strlen($title) < 200) {
                    $posts[] = array(
                        'title' => $title,
                        'url' => $url
                    );
                }
            }
        }
        
        return array_slice($posts, 0, 10);
    }
    
    private function extractContentTypesFromSitemap($sitemap_xml) {
        $content_types = array();
        
        // Parse XML and extract URLs
        if (preg_match_all('/<loc>([^<]+)<\/loc>/', $sitemap_xml, $matches)) {
            foreach ($matches[1] as $url) {
                // Analyze URL pattern to determine content type
                if (strpos($url, '/blog/') !== false || strpos($url, '/news/') !== false) {
                    $content_types['blog'] = ($content_types['blog'] ?? 0) + 1;
                } elseif (strpos($url, '/product/') !== false || strpos($url, '/shop/') !== false) {
                    $content_types['product'] = ($content_types['product'] ?? 0) + 1;
                } elseif (strpos($url, '/category/') !== false) {
                    $content_types['category'] = ($content_types['category'] ?? 0) + 1;
                } else {
                    $content_types['page'] = ($content_types['page'] ?? 0) + 1;
                }
            }
        }
        
        return $content_types;
    }
    
    private function analyzeOurKeywordOptimization($keyword) {
        // Analyze how well our site is optimized for this keyword
        $score = 0;
        $factors = array();
        
        // Check homepage optimization
        $homepage_content = $this->getHomepageContent();
        $title = get_bloginfo('name');
        $description = get_bloginfo('description');
        
        // Title optimization
        if (stripos($title, $keyword) !== false) {
            $score += 30;
            $factors['title'] = true;
        }
        
        // Description optimization
        if (stripos($description, $keyword) !== false) {
            $score += 20;
            $factors['description'] = true;
        }
        
        // Content optimization
        if (stripos($homepage_content, $keyword) !== false) {
            $score += 30;
            $factors['content'] = true;
        }
        
        // Check if we have posts optimized for this keyword
        $posts_with_keyword = get_posts(array(
            's' => $keyword,
            'numberposts' => 1
        ));
        
        if (!empty($posts_with_keyword)) {
            $score += 20;
            $factors['posts'] = true;
        }
        
        return array(
            'score' => $score,
            'factors' => $factors
        );
    }
    
    private function getHomepageContent() {
        static $homepage_content = null;
        
        if ($homepage_content === null) {
            $response = wp_remote_get(home_url());
            if (!is_wp_error($response)) {
                $homepage_content = wp_remote_retrieve_body($response);
            } else {
                $homepage_content = '';
            }
        }
        
        return $homepage_content;
    }
    
    private function calculateOpportunityScore($keyword, $competitor) {
        // Calculate opportunity score based on various factors
        $score = 50; // Base score
        
        // Factor in competitor's domain authority (simplified)
        if (isset($competitor->data['backlink_analysis']['domain_authority'])) {
            $da = $competitor->data['backlink_analysis']['domain_authority'];
            $score += (80 - $da) / 2; // Higher score for lower DA competitors
        }
        
        // Factor in keyword competition (simplified)
        $keyword_length = str_word_count($keyword);
        if ($keyword_length > 2) {
            $score += 20; // Long-tail keywords are often easier
        }
        
        // Factor in our current optimization
        $our_optimization = $this->analyzeOurKeywordOptimization($keyword);
        $score += (100 - $our_optimization['score']) / 4;
        
        return min(100, max(0, $score));
    }
    
    private function saveCompetitorAnalysis($competitor_id, $analysis_data) {
        global $wpdb;
        
        $wpdb->update(
            $wpdb->prefix . 'kata_seo_competitors',
            array(
                'data' => json_encode($analysis_data),
                'last_checked' => current_time('mysql')
            ),
            array('id' => $competitor_id),
            array('%s', '%s'),
            array('%d')
        );
    }
}
