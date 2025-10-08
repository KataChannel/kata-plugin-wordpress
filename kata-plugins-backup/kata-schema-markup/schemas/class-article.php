<?php
/**
 * Article Schema Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Article Schema Class
 */
class KataSchema_Article {
    
    /**
     * Generate Article schema
     */
    public function generate($post_id) {
        $post = get_post($post_id);
        
        if (!$post || $post->post_status !== 'publish') {
            return false;
        }
        
        // Base schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $this->get_article_type($post),
            'headline' => get_the_title($post_id),
            'description' => $this->get_description($post_id),
            'url' => get_permalink($post_id),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => $this->get_author_schema($post->post_author),
            'publisher' => $this->get_publisher_schema(),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink($post_id)
            )
        );
        
        // Add image if available
        $image = $this->get_image_schema($post_id);
        if ($image) {
            $schema['image'] = $image;
        }
        
        // Add article body
        $content = get_the_content(null, false, $post_id);
        if (!empty($content)) {
            $schema['articleBody'] = wp_strip_all_tags($content);
        }
        
        // Add word count
        $word_count = str_word_count(wp_strip_all_tags($content));
        if ($word_count > 0) {
            $schema['wordCount'] = $word_count;
        }
        
        // Add categories as keywords
        $categories = get_the_category($post_id);
        if (!empty($categories)) {
            $keywords = array();
            foreach ($categories as $category) {
                $keywords[] = $category->name;
            }
            $schema['keywords'] = implode(', ', $keywords);
        }
        
        // Add tags
        $tags = get_the_tags($post_id);
        if (!empty($tags)) {
            $tag_names = array();
            foreach ($tags as $tag) {
                $tag_names[] = $tag->name;
            }
            if (!empty($schema['keywords'])) {
                $schema['keywords'] .= ', ' . implode(', ', $tag_names);
            } else {
                $schema['keywords'] = implode(', ', $tag_names);
            }
        }
        
        // Add reading time
        $reading_time = $this->calculate_reading_time($word_count);
        if ($reading_time) {
            $schema['timeRequired'] = $reading_time;
        }
        
        // Add language
        $language = get_locale();
        if ($language) {
            $schema['inLanguage'] = str_replace('_', '-', $language);
        }
        
        // Custom meta fields
        $custom_fields = $this->get_custom_article_fields($post_id);
        if (!empty($custom_fields)) {
            $schema = array_merge($schema, $custom_fields);
        }
        
        // Apply filters
        $schema = apply_filters('kata_schema_article', $schema, $post_id);
        
        return $this->clean_schema($schema);
    }
    
    /**
     * Get article type based on post type and content
     */
    private function get_article_type($post) {
        // Check custom meta first
        $custom_type = get_post_meta($post->ID, '_kata_schema_article_type', true);
        if ($custom_type) {
            return $custom_type;
        }
        
        // Determine type based on post type and categories
        if ($post->post_type === 'post') {
            $categories = get_the_category($post->ID);
            
            foreach ($categories as $category) {
                $category_name = strtolower($category->name);
                
                if (in_array($category_name, array('news', 'breaking', 'updates'))) {
                    return 'NewsArticle';
                }
                
                if (in_array($category_name, array('tech', 'technology', 'science'))) {
                    return 'TechArticle';
                }
                
                if (in_array($category_name, array('review', 'reviews'))) {
                    return 'Review';
                }
            }
            
            return 'BlogPosting';
        }
        
        return 'Article';
    }
    
    /**
     * Get description
     */
    private function get_description($post_id) {
        // Try excerpt first
        $excerpt = get_the_excerpt($post_id);
        if (!empty($excerpt)) {
            return wp_strip_all_tags($excerpt);
        }
        
        // Fallback to content snippet
        $content = get_the_content(null, false, $post_id);
        if (!empty($content)) {
            $description = wp_strip_all_tags($content);
            return wp_trim_words($description, 30);
        }
        
        return '';
    }
    
    /**
     * Get author schema
     */
    private function get_author_schema($author_id) {
        $author = get_userdata($author_id);
        
        if (!$author) {
            return array(
                '@type' => 'Person',
                'name' => get_bloginfo('name')
            );
        }
        
        $author_schema = array(
            '@type' => 'Person',
            'name' => $author->display_name
        );
        
        // Author URL
        $author_url = get_author_posts_url($author_id);
        if ($author_url) {
            $author_schema['url'] = $author_url;
        }
        
        // Author website
        $website = get_user_meta($author_id, 'user_url', true);
        if (!empty($website)) {
            $author_schema['sameAs'] = array($website);
        }
        
        // Author description
        $description = get_user_meta($author_id, 'description', true);
        if (!empty($description)) {
            $author_schema['description'] = $description;
        }
        
        // Author image
        $avatar_url = get_avatar_url($author_id, array('size' => 300));
        if ($avatar_url) {
            $author_schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $avatar_url
            );
        }
        
        return $author_schema;
    }
    
    /**
     * Get publisher schema
     */
    private function get_publisher_schema() {
        $organization_name = get_option('kata_schema_organization_name', get_bloginfo('name'));
        $organization_logo = get_option('kata_schema_organization_logo', '');
        
        $publisher = array(
            '@type' => 'Organization',
            'name' => $organization_name,
            'url' => home_url('/')
        );
        
        if (!empty($organization_logo)) {
            $publisher['logo'] = array(
                '@type' => 'ImageObject',
                'url' => $organization_logo
            );
        }
        
        // Social profiles
        $social_profiles = get_option('kata_schema_social_profiles', array());
        if (!empty($social_profiles)) {
            $publisher['sameAs'] = array_filter($social_profiles);
        }
        
        return $publisher;
    }
    
    /**
     * Get image schema
     */
    private function get_image_schema($post_id) {
        $image_id = get_post_thumbnail_id($post_id);
        
        if (!$image_id) {
            return false;
        }
        
        $image_url = wp_get_attachment_image_url($image_id, 'large');
        $image_meta = wp_get_attachment_metadata($image_id);
        
        if (!$image_url) {
            return false;
        }
        
        $image_schema = array(
            '@type' => 'ImageObject',
            'url' => $image_url
        );
        
        // Add dimensions if available
        if (isset($image_meta['width']) && isset($image_meta['height'])) {
            $image_schema['width'] = $image_meta['width'];
            $image_schema['height'] = $image_meta['height'];
        }
        
        // Add alt text
        $alt_text = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        if (!empty($alt_text)) {
            $image_schema['alternateName'] = $alt_text;
        }
        
        // Add caption
        $image_post = get_post($image_id);
        if ($image_post && !empty($image_post->post_excerpt)) {
            $image_schema['caption'] = $image_post->post_excerpt;
        }
        
        return $image_schema;
    }
    
    /**
     * Calculate reading time in ISO 8601 duration format
     */
    private function calculate_reading_time($word_count) {
        if ($word_count <= 0) {
            return false;
        }
        
        // Average reading speed: 200 words per minute
        $minutes = ceil($word_count / 200);
        
        // Convert to ISO 8601 duration format (PT5M for 5 minutes)
        return "PT{$minutes}M";
    }
    
    /**
     * Get custom article fields
     */
    private function get_custom_article_fields($post_id) {
        $custom_fields = array();
        
        // Article section
        $section = get_post_meta($post_id, '_kata_schema_article_section', true);
        if (!empty($section)) {
            $custom_fields['articleSection'] = $section;
        }
        
        // About
        $about = get_post_meta($post_id, '_kata_schema_article_about', true);
        if (!empty($about)) {
            $custom_fields['about'] = $about;
        }
        
        // Mentions
        $mentions = get_post_meta($post_id, '_kata_schema_article_mentions', true);
        if (!empty($mentions)) {
            $mentions_array = explode(',', $mentions);
            $custom_fields['mentions'] = array_map('trim', $mentions_array);
        }
        
        // Audience
        $audience = get_post_meta($post_id, '_kata_schema_article_audience', true);
        if (!empty($audience)) {
            $custom_fields['audience'] = array(
                '@type' => 'Audience',
                'audienceType' => $audience
            );
        }
        
        // Citation
        $citation = get_post_meta($post_id, '_kata_schema_article_citation', true);
        if (!empty($citation)) {
            $custom_fields['citation'] = $citation;
        }
        
        // Comment count
        $comment_count = get_comments_number($post_id);
        if ($comment_count > 0) {
            $custom_fields['commentCount'] = $comment_count;
            $custom_fields['interactionStatistic'] = array(
                '@type' => 'InteractionCounter',
                'interactionType' => 'https://schema.org/CommentAction',
                'userInteractionCount' => $comment_count
            );
        }
        
        return $custom_fields;
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
