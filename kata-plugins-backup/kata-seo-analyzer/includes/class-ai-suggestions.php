<?php
/**
 * AI Suggestions Class
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KataSEO_AI_Suggestions {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Initialize AI suggestions
    }
    
    /**
     * Get AI-powered content suggestions
     */
    public function get_content_suggestions($content, $focus_keyword = '') {
        return array(
            'keyword_density' => $this->analyze_keyword_density($content, $focus_keyword),
            'readability' => $this->analyze_readability($content),
            'structure' => $this->analyze_structure($content),
            'improvements' => $this->generate_improvements($content, $focus_keyword)
        );
    }
    
    /**
     * Analyze keyword density
     */
    private function analyze_keyword_density($content, $keyword) {
        if (empty($keyword)) {
            return array('density' => 0, 'suggestion' => 'No focus keyword set');
        }
        
        $word_count = str_word_count(strip_tags($content));
        $keyword_count = substr_count(strtolower($content), strtolower($keyword));
        $density = $word_count > 0 ? ($keyword_count / $word_count) * 100 : 0;
        
        return array(
            'density' => round($density, 2),
            'suggestion' => $this->get_density_suggestion($density)
        );
    }
    
    /**
     * Analyze readability
     */
    private function analyze_readability($content) {
        $text = strip_tags($content);
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        $avg_sentence_length = count($sentences) > 0 ? $words / count($sentences) : 0;
        
        return array(
            'avg_sentence_length' => round($avg_sentence_length, 1),
            'suggestion' => $avg_sentence_length > 20 ? 'Consider shorter sentences for better readability' : 'Good sentence length'
        );
    }
    
    /**
     * Analyze content structure
     */
    private function analyze_structure($content) {
        $h1_count = substr_count($content, '<h1');
        $h2_count = substr_count($content, '<h2');
        $h3_count = substr_count($content, '<h3');
        
        return array(
            'h1_count' => $h1_count,
            'h2_count' => $h2_count,
            'h3_count' => $h3_count,
            'suggestion' => $this->get_structure_suggestion($h1_count, $h2_count, $h3_count)
        );
    }
    
    /**
     * Generate improvement suggestions
     */
    private function generate_improvements($content, $keyword) {
        $suggestions = array();
        
        if (str_word_count(strip_tags($content)) < 300) {
            $suggestions[] = 'Consider adding more content (aim for 300+ words)';
        }
        
        if (!empty($keyword) && strpos(strtolower($content), strtolower($keyword)) === false) {
            $suggestions[] = 'Include your focus keyword in the content';
        }
        
        if (!preg_match('/<h[1-6]/', $content)) {
            $suggestions[] = 'Add headings to improve content structure';
        }
        
        return $suggestions;
    }
    
    /**
     * Get all AI suggestions
     */
    public function getAllSuggestions() {
        global $wpdb;
        
        // Return sample suggestions for now
        return array(
            array(
                'id' => 1,
                'post_id' => 0,
                'type' => 'content',
                'suggestion' => 'Add more internal links to improve site navigation and SEO',
                'priority' => 'high',
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array(
                'id' => 2,
                'post_id' => 0,
                'type' => 'technical',
                'suggestion' => 'Optimize images by adding alt text for better accessibility',
                'priority' => 'medium',
                'status' => 'pending',
                'created_at' => current_time('mysql')
            ),
            array(
                'id' => 3,
                'post_id' => 0,
                'type' => 'keyword',
                'suggestion' => 'Consider targeting long-tail keywords for better ranking opportunities',
                'priority' => 'low',
                'status' => 'completed',
                'created_at' => current_time('mysql')
            )
        );
    }
    
    /**
     * Get AI suggestions statistics
     */
    public function getStats() {
        return array(
            'total_suggestions' => 3,
            'pending' => 2,
            'completed' => 1,
            'high_priority' => 1,
            'medium_priority' => 1,
            'low_priority' => 1
        );
    }
    
    /**
     * Get density suggestion
     */
    private function get_density_suggestion($density) {
        if ($density < 0.5) {
            return 'Keyword density is too low. Consider using the keyword more frequently.';
        } elseif ($density > 3) {
            return 'Keyword density is too high. Reduce keyword usage to avoid over-optimization.';
        }
        return 'Good keyword density.';
    }
    
    /**
     * Get structure suggestion
     */
    private function get_structure_suggestion($h1, $h2, $h3) {
        if ($h1 > 1) {
            return 'Use only one H1 tag per page';
        } elseif ($h1 == 0) {
            return 'Add an H1 tag for better SEO';
        } elseif ($h2 == 0) {
            return 'Consider adding H2 tags to structure your content';
        }
        return 'Good heading structure';
    }
}
