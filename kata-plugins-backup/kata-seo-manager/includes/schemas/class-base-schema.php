<?php
/**
 * Base Schema Class
 * 
 * Parent class for all schema types
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class KATA_Base_Schema {
    
    /**
     * Schema type
     */
    protected $type = '';
    
    /**
     * Required fields
     */
    protected $required_fields = array();
    
    /**
     * Optional fields
     */
    protected $optional_fields = array();
    
    /**
     * Generate schema
     * 
     * @param array $data Input data
     * @param array $options Additional options
     * @return array|WP_Error Schema array or error
     */
    abstract public function generate($data, $options = array());
    
    /**
     * Get example data
     * 
     * @return array Example data
     */
    abstract public function get_example();
    
    /**
     * Get field definitions
     * 
     * @return array Field definitions
     */
    abstract public function get_fields();
    
    /**
     * Validate schema data
     * 
     * @param array $schema Generated schema
     * @return array Validation result
     */
    public function validate($schema) {
        $errors = array();
        $warnings = array();
        
        // Check required fields
        foreach ($this->required_fields as $field) {
            if (!isset($schema[$field]) || empty($schema[$field])) {
                $errors[] = sprintf(__('Required field "%s" is missing', 'kata-seo-manager'), $field);
            }
        }
        
        return array(
            'errors' => $errors,
            'warnings' => $warnings
        );
    }
    
    /**
     * Build base schema structure
     * 
     * @param string $type Schema @type
     * @return array Base schema
     */
    protected function build_base($type = null) {
        return array(
            '@context' => 'https://schema.org',
            '@type' => $type ?? $this->type
        );
    }
    
    /**
     * Add field if not empty
     * 
     * @param array $schema Schema array
     * @param string $key Field key
     * @param mixed $value Field value
     * @param mixed $default Default value
     * @return array Updated schema
     */
    protected function add_field(&$schema, $key, $value, $default = null) {
        if (!empty($value)) {
            $schema[$key] = $value;
        } elseif ($default !== null) {
            $schema[$key] = $default;
        }
        return $schema;
    }
    
    /**
     * Build Person schema
     * 
     * @param array $data Person data
     * @return array Person schema
     */
    protected function build_person($data) {
        if (is_string($data)) {
            return array(
                '@type' => 'Person',
                'name' => $data
            );
        }
        
        $person = array('@type' => 'Person');
        
        $this->add_field($person, 'name', $data['name'] ?? '');
        $this->add_field($person, 'url', $data['url'] ?? '');
        $this->add_field($person, 'image', $data['image'] ?? '');
        $this->add_field($person, 'jobTitle', $data['jobTitle'] ?? '');
        $this->add_field($person, 'email', $data['email'] ?? '');
        
        return $person;
    }
    
    /**
     * Build Organization schema
     * 
     * @param array $data Organization data
     * @return array Organization schema
     */
    protected function build_organization($data) {
        if (is_string($data)) {
            return array(
                '@type' => 'Organization',
                'name' => $data
            );
        }
        
        $org = array('@type' => 'Organization');
        
        $this->add_field($org, 'name', $data['name'] ?? get_bloginfo('name'));
        $this->add_field($org, 'url', $data['url'] ?? get_bloginfo('url'));
        
        if (!empty($data['logo'])) {
            $org['logo'] = array(
                '@type' => 'ImageObject',
                'url' => $data['logo']
            );
        }
        
        return $org;
    }
    
    /**
     * Build ImageObject schema
     * 
     * @param mixed $image Image URL or array
     * @return array ImageObject schema
     */
    protected function build_image($image) {
        if (is_string($image)) {
            return array(
                '@type' => 'ImageObject',
                'url' => $image
            );
        }
        
        if (is_array($image)) {
            $img = array('@type' => 'ImageObject');
            
            $this->add_field($img, 'url', $image['url'] ?? '');
            $this->add_field($img, 'width', $image['width'] ?? '');
            $this->add_field($img, 'height', $image['height'] ?? '');
            $this->add_field($img, 'caption', $image['caption'] ?? '');
            
            return $img;
        }
        
        return array();
    }
    
    /**
     * Format date to ISO 8601
     * 
     * @param string $date Date string
     * @return string Formatted date
     */
    protected function format_date($date) {
        if (empty($date)) {
            return '';
        }
        
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        
        if (!$timestamp) {
            return $date;
        }
        
        return date('c', $timestamp);
    }
    
    /**
     * Format duration to ISO 8601
     * 
     * @param mixed $duration Duration (minutes or string)
     * @return string ISO 8601 duration
     */
    protected function format_duration($duration) {
        if (empty($duration)) {
            return '';
        }
        
        // If already in ISO format
        if (preg_match('/^PT/', $duration)) {
            return $duration;
        }
        
        // If numeric (minutes)
        if (is_numeric($duration)) {
            $minutes = intval($duration);
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            
            if ($hours > 0 && $mins > 0) {
                return "PT{$hours}H{$mins}M";
            } elseif ($hours > 0) {
                return "PT{$hours}H";
            } else {
                return "PT{$mins}M";
            }
        }
        
        return $duration;
    }
    
    /**
     * Build rating schema
     * 
     * @param array $data Rating data
     * @return array Rating schema
     */
    protected function build_rating($data) {
        if (!is_array($data)) {
            return array();
        }
        
        $rating = array('@type' => 'AggregateRating');
        
        $this->add_field($rating, 'ratingValue', $data['ratingValue'] ?? $data['value'] ?? '');
        $this->add_field($rating, 'bestRating', $data['bestRating'] ?? 5);
        $this->add_field($rating, 'worstRating', $data['worstRating'] ?? 1);
        $this->add_field($rating, 'ratingCount', $data['ratingCount'] ?? $data['count'] ?? '');
        $this->add_field($rating, 'reviewCount', $data['reviewCount'] ?? '');
        
        return $rating;
    }
    
    /**
     * Build offer schema
     * 
     * @param array $data Offer data
     * @return array Offer schema
     */
    protected function build_offer($data) {
        if (!is_array($data)) {
            return array();
        }
        
        $offer = array('@type' => 'Offer');
        
        $this->add_field($offer, 'price', $data['price'] ?? '');
        $this->add_field($offer, 'priceCurrency', $data['priceCurrency'] ?? 'USD');
        $this->add_field($offer, 'availability', $data['availability'] ?? 'https://schema.org/InStock');
        $this->add_field($offer, 'url', $data['url'] ?? '');
        $this->add_field($offer, 'priceValidUntil', $data['priceValidUntil'] ?? '');
        
        if (!empty($data['seller'])) {
            $offer['seller'] = $this->build_organization($data['seller']);
        }
        
        return $offer;
    }
    
    /**
     * Get post data
     * 
     * @param int $post_id Post ID
     * @return array Post data
     */
    protected function get_post_data($post_id = null) {
        global $post;
        
        if (!$post_id && $post) {
            $post_id = $post->ID;
        }
        
        if (!$post_id) {
            return array();
        }
        
        $post_obj = get_post($post_id);
        
        if (!$post_obj) {
            return array();
        }
        
        return array(
            'id' => $post_obj->ID,
            'title' => get_the_title($post_id),
            'content' => $post_obj->post_content,
            'excerpt' => get_the_excerpt($post_id),
            'url' => get_permalink($post_id),
            'author' => get_the_author_meta('display_name', $post_obj->post_author),
            'author_url' => get_author_posts_url($post_obj->post_author),
            'published' => get_the_date('c', $post_id),
            'modified' => get_the_modified_date('c', $post_id),
            'image' => get_the_post_thumbnail_url($post_id, 'full')
        );
    }
    
    /**
     * Replace placeholders with post data
     * 
     * @param array $data Data with placeholders
     * @param int $post_id Post ID
     * @return array Data with replaced values
     */
    protected function replace_placeholders($data, $post_id = null) {
        $post_data = $this->get_post_data($post_id);
        
        $replacements = array(
            '{post_title}' => $post_data['title'] ?? '',
            '{post_content}' => $post_data['content'] ?? '',
            '{post_excerpt}' => $post_data['excerpt'] ?? '',
            '{post_url}' => $post_data['url'] ?? '',
            '{post_author}' => $post_data['author'] ?? '',
            '{post_author_url}' => $post_data['author_url'] ?? '',
            '{post_published}' => $post_data['published'] ?? '',
            '{post_modified}' => $post_data['modified'] ?? '',
            '{featured_image}' => $post_data['image'] ?? '',
            '{site_name}' => get_bloginfo('name'),
            '{site_url}' => get_bloginfo('url'),
            '{site_description}' => get_bloginfo('description')
        );
        
        return $this->array_replace_recursive($data, $replacements);
    }
    
    /**
     * Recursive array replace
     */
    private function array_replace_recursive($data, $replacements) {
        if (is_string($data)) {
            return str_replace(array_keys($replacements), array_values($replacements), $data);
        }
        
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->array_replace_recursive($value, $replacements);
            }
        }
        
        return $data;
    }
}
