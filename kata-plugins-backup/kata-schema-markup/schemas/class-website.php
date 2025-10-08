<?php
/**
 * Website Schema Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Website Schema Class
 */
class KataSchema_Website {
    
    /**
     * Generate Website schema
     */
    public function generate($post_id = null) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'url' => home_url('/'),
            'description' => get_bloginfo('description')
        );
        
        // Add alternate name (tagline)
        $tagline = get_bloginfo('description');
        if (!empty($tagline)) {
            $schema['alternateName'] = $tagline;
        }
        
        // Add potential action for search
        $search_action = $this->get_search_action();
        if ($search_action) {
            $schema['potentialAction'] = $search_action;
        }
        
        // Add publisher information
        $publisher = $this->get_publisher();
        if ($publisher) {
            $schema['publisher'] = $publisher;
        }
        
        // Add main entity (usually homepage)
        if (is_front_page()) {
            $schema['mainEntity'] = array(
                '@type' => 'WebPage',
                '@id' => home_url('/'),
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description')
            );
        }
        
        // Add language
        $language = get_locale();
        if ($language) {
            $schema['inLanguage'] = str_replace('_', '-', $language);
        }
        
        // Add same as (social profiles)
        $social_profiles = $this->get_social_profiles();
        if (!empty($social_profiles)) {
            $schema['sameAs'] = $social_profiles;
        }
        
        // Add keywords (from site description or custom field)
        $keywords = $this->get_site_keywords();
        if (!empty($keywords)) {
            $schema['keywords'] = $keywords;
        }
        
        // Add about (what the website is about)
        $about = $this->get_site_about();
        if ($about) {
            $schema['about'] = $about;
        }
        
        // Add audience
        $audience = $this->get_site_audience();
        if ($audience) {
            $schema['audience'] = $audience;
        }
        
        // Add copyright information
        $copyright_year = get_option('kata_schema_website_copyright_year', date('Y'));
        $copyright_holder = get_option('kata_schema_website_copyright_holder', get_bloginfo('name'));
        
        if (!empty($copyright_holder)) {
            $schema['copyrightHolder'] = array(
                '@type' => 'Organization',
                'name' => $copyright_holder
            );
            
            if (!empty($copyright_year)) {
                $schema['copyrightYear'] = intval($copyright_year);
            }
        }
        
        // Add license information
        $license = get_option('kata_schema_website_license', '');
        if (!empty($license)) {
            $schema['license'] = $license;
        }
        
        // Apply filters
        $schema = apply_filters('kata_schema_website', $schema, $post_id);
        
        return $this->clean_schema($schema);
    }
    
    /**
     * Get search action schema
     */
    private function get_search_action() {
        // Check if search is enabled
        if (!get_option('kata_schema_website_search_enabled', true)) {
            return false;
        }
        
        $search_url_template = get_option('kata_schema_website_search_url', home_url('/?s={search_term_string}'));
        
        if (empty($search_url_template)) {
            return false;
        }
        
        return array(
            '@type' => 'SearchAction',
            'target' => array(
                '@type' => 'EntryPoint',
                'urlTemplate' => $search_url_template
            ),
            'query-input' => 'required name=search_term_string'
        );
    }
    
    /**
     * Get publisher information
     */
    private function get_publisher() {
        $organization_name = get_option('kata_schema_organization_name', get_bloginfo('name'));
        
        if (empty($organization_name)) {
            return false;
        }
        
        $publisher = array(
            '@type' => 'Organization',
            'name' => $organization_name,
            'url' => home_url('/')
        );
        
        // Add logo
        $logo_url = get_option('kata_schema_organization_logo', '');
        if (!empty($logo_url)) {
            $publisher['logo'] = array(
                '@type' => 'ImageObject',
                'url' => $logo_url
            );
        }
        
        return $publisher;
    }
    
    /**
     * Get social profiles
     */
    private function get_social_profiles() {
        $profiles = get_option('kata_schema_social_profiles', array());
        
        if (!is_array($profiles)) {
            return array();
        }
        
        return array_filter($profiles, function($url) {
            return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
        });
    }
    
    /**
     * Get site keywords
     */
    private function get_site_keywords() {
        // Try custom keywords first
        $custom_keywords = get_option('kata_schema_website_keywords', '');
        if (!empty($custom_keywords)) {
            return $custom_keywords;
        }
        
        // Generate keywords from categories and tags
        $keywords = array();
        
        // Get top categories
        $categories = get_categories(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 10,
            'hide_empty' => true
        ));
        
        foreach ($categories as $category) {
            $keywords[] = $category->name;
        }
        
        // Get top tags
        $tags = get_tags(array(
            'orderby' => 'count',
            'order' => 'DESC',
            'number' => 10,
            'hide_empty' => true
        ));
        
        foreach ($tags as $tag) {
            $keywords[] = $tag->name;
        }
        
        return !empty($keywords) ? implode(', ', array_unique($keywords)) : '';
    }
    
    /**
     * Get site about information
     */
    private function get_site_about() {
        $about_text = get_option('kata_schema_website_about', '');
        
        if (empty($about_text)) {
            // Use site description as fallback
            $about_text = get_bloginfo('description');
        }
        
        if (empty($about_text)) {
            return false;
        }
        
        return array(
            '@type' => 'Thing',
            'name' => get_bloginfo('name'),
            'description' => $about_text
        );
    }
    
    /**
     * Get site audience
     */
    private function get_site_audience() {
        $audience_type = get_option('kata_schema_website_audience_type', '');
        $audience_name = get_option('kata_schema_website_audience_name', '');
        
        if (empty($audience_type) && empty($audience_name)) {
            return false;
        }
        
        $audience = array(
            '@type' => 'Audience'
        );
        
        if (!empty($audience_type)) {
            $audience['audienceType'] = $audience_type;
        }
        
        if (!empty($audience_name)) {
            $audience['name'] = $audience_name;
        }
        
        // Add geographic area
        $geographic_area = get_option('kata_schema_website_geographic_area', '');
        if (!empty($geographic_area)) {
            $audience['geographicArea'] = array(
                '@type' => 'Place',
                'name' => $geographic_area
            );
        }
        
        return $audience;
    }
    
    /**
     * Generate WebPage schema for specific pages
     */
    public function generate_webpage_schema($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }
        
        $post = get_post($post_id);
        if (!$post) {
            return false;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $this->get_webpage_type($post),
            '@id' => get_permalink($post_id),
            'url' => get_permalink($post_id),
            'name' => get_the_title($post_id),
            'description' => $this->get_page_description($post_id),
            'isPartOf' => array(
                '@type' => 'WebSite',
                '@id' => home_url('/'),
                'name' => get_bloginfo('name'),
                'url' => home_url('/')
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'inLanguage' => str_replace('_', '-', get_locale())
        );
        
        // Add breadcrumb
        $breadcrumb = $this->get_breadcrumb_reference($post_id);
        if ($breadcrumb) {
            $schema['breadcrumb'] = $breadcrumb;
        }
        
        // Add main entity (the content of the page)
        $main_entity = $this->get_main_entity($post_id);
        if ($main_entity) {
            $schema['mainEntity'] = $main_entity;
        }
        
        // Add about
        if ($post->post_type === 'post') {
            $categories = get_the_category($post_id);
            if (!empty($categories)) {
                $schema['about'] = array(
                    '@type' => 'Thing',
                    'name' => $categories[0]->name
                );
            }
        }
        
        return $this->clean_schema($schema);
    }
    
    /**
     * Get webpage type
     */
    private function get_webpage_type($post) {
        // Check for custom type
        $custom_type = get_post_meta($post->ID, '_kata_schema_webpage_type', true);
        if ($custom_type) {
            return $custom_type;
        }
        
        // Determine type based on post type and content
        if ($post->post_type === 'page') {
            if (is_front_page()) {
                return 'WebPage';
            } elseif (is_page('about')) {
                return 'AboutPage';
            } elseif (is_page('contact')) {
                return 'ContactPage';
            } else {
                return 'WebPage';
            }
        } elseif ($post->post_type === 'post') {
            return 'WebPage';
        }
        
        return 'WebPage';
    }
    
    /**
     * Get page description
     */
    private function get_page_description($post_id) {
        // Try excerpt first
        $excerpt = get_the_excerpt($post_id);
        if (!empty($excerpt)) {
            return wp_strip_all_tags($excerpt);
        }
        
        // Fallback to content snippet
        $content = get_the_content(null, false, $post_id);
        if (!empty($content)) {
            $description = wp_strip_all_tags($content);
            return wp_trim_words($description, 25);
        }
        
        return '';
    }
    
    /**
     * Get breadcrumb reference
     */
    private function get_breadcrumb_reference($post_id) {
        return array(
            '@type' => 'BreadcrumbList',
            '@id' => get_permalink($post_id) . '#breadcrumb'
        );
    }
    
    /**
     * Get main entity for the page
     */
    private function get_main_entity($post_id) {
        $post = get_post($post_id);
        
        if ($post->post_type === 'post') {
            // For blog posts, main entity is the article
            return array(
                '@type' => 'Article',
                '@id' => get_permalink($post_id) . '#article',
                'headline' => get_the_title($post_id),
                'description' => $this->get_page_description($post_id)
            );
        }
        
        // For pages, main entity is the page content
        return array(
            '@type' => 'WebPageElement',
            'cssSelector' => 'main, .content, #content, .post-content'
        );
    }
    
    /**
     * Clean schema by removing empty values
     */
    private function clean_schema($schema) {
        foreach ($schema as $key => $value) {
            if (is_array($value)) {
                $schema[$key] = $this->clean_schema($value);
                if (empty($schema[$key])) {
                    unset($schema[$key]);
                }
            } elseif (empty($value) && $value !== 0 && $value !== '0') {
                unset($schema[$key]);
            }
        }
        
        return $schema;
    }
}
