<?php
/**
 * Schema Generator Class
 * Generate dynamic schema markup for posts and pages
 */

class Kata_SEO_Schema_Generator {
    
    /**
     * Generate schema markup for a post
     */
    public function generate_schema($post_id) {
        $schema_type = get_post_meta($post_id, '_kata_seo_schema_type', true) ?: 'Article';
        
        $schemas = array();
        
        // Main content schema
        $schemas[] = $this->generate_article_schema($post_id, $schema_type);
        
        // FAQ schema if exists
        $faqs = get_post_meta($post_id, '_kata_seo_faqs', true);
        if (!empty($faqs)) {
            $schemas[] = $this->generate_faq_schema($faqs);
        }
        
        // Breadcrumb schema
        $schemas[] = $this->generate_breadcrumb_schema($post_id);
        
        // Organization schema (for homepage)
        if (is_front_page()) {
            $schemas[] = $this->generate_organization_schema();
        }
        
        $output = '';
        foreach ($schemas as $schema) {
            if ($schema) {
                $output .= '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
            }
        }
        
        return $output;
    }
    
    /**
     * Generate Article schema
     */
    private function generate_article_schema($post_id, $type = 'Article') {
        $post = get_post($post_id);
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => $type,
            'headline' => get_the_title($post_id),
            'description' => get_the_excerpt($post_id),
            'image' => $this->get_image_schema($post_id),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => $this->get_author_schema($post->post_author),
            'publisher' => $this->get_publisher_schema(),
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => get_permalink($post_id)
            )
        );
        
        // Add rating if exists
        $rating_data = $this->get_aggregate_rating($post_id);
        if ($rating_data) {
            $schema['aggregateRating'] = $rating_data;
        }
        
        return $schema;
    }
    
    /**
     * Generate FAQ schema
     */
    public function generate_faq_schema($faqs) {
        if (empty($faqs)) {
            return null;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );
        
        foreach ($faqs as $faq) {
            $schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                )
            );
        }
        
        return $schema;
    }
    
    /**
     * Generate Breadcrumb schema
     */
    private function generate_breadcrumb_schema($post_id) {
        $breadcrumbs = array(
            array(
                '@type' => 'ListItem',
                'position' => 1,
                'name' => get_bloginfo('name'),
                'item' => home_url()
            )
        );
        
        $post_type = get_post_type($post_id);
        $position = 2;
        
        // Add category for posts
        if ($post_type === 'post') {
            $categories = get_the_category($post_id);
            if (!empty($categories)) {
                $category = $categories[0];
                $breadcrumbs[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $category->name,
                    'item' => get_category_link($category->term_id)
                );
            }
        }
        
        // Add current page
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title($post_id),
            'item' => get_permalink($post_id)
        );
        
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );
    }
    
    /**
     * Generate Organization schema
     */
    private function generate_organization_schema() {
        $company_name = get_option('kata_seo_company_name', get_bloginfo('name'));
        $company_logo = get_option('kata_seo_company_logo', get_site_icon_url());
        
        return array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $company_name,
            'url' => home_url(),
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => $company_logo
            ),
            'sameAs' => $this->get_social_profiles()
        );
    }
    
    /**
     * Get image schema
     */
    private function get_image_schema($post_id) {
        $image_url = get_the_post_thumbnail_url($post_id, 'full');
        
        if (!$image_url) {
            return null;
        }
        
        $image_id = get_post_thumbnail_id($post_id);
        $image_data = wp_get_attachment_image_src($image_id, 'full');
        
        return array(
            '@type' => 'ImageObject',
            'url' => $image_url,
            'width' => $image_data[1] ?? 1200,
            'height' => $image_data[2] ?? 630
        );
    }
    
    /**
     * Get author schema
     */
    private function get_author_schema($author_id) {
        return array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name', $author_id),
            'url' => get_author_posts_url($author_id),
            'description' => get_the_author_meta('description', $author_id)
        );
    }
    
    /**
     * Get publisher schema
     */
    private function get_publisher_schema() {
        $company_name = get_option('kata_seo_company_name', get_bloginfo('name'));
        $company_logo = get_option('kata_seo_company_logo', get_site_icon_url());
        
        return array(
            '@type' => 'Organization',
            'name' => $company_name,
            'logo' => array(
                '@type' => 'ImageObject',
                'url' => $company_logo
            )
        );
    }
    
    /**
     * Get aggregate rating
     */
    private function get_aggregate_rating($post_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_ratings';
        $stats = $wpdb->get_row($wpdb->prepare(
            "SELECT COUNT(*) as count, AVG(rating) as average FROM $table WHERE post_id = %d",
            $post_id
        ));
        
        if ($stats && $stats->count > 0) {
            return array(
                '@type' => 'AggregateRating',
                'ratingValue' => round($stats->average, 1),
                'reviewCount' => $stats->count,
                'bestRating' => 5,
                'worstRating' => 1
            );
        }
        
        return null;
    }
    
    /**
     * Get social profiles
     */
    private function get_social_profiles() {
        $profiles = array();
        
        $social_links = array(
            'facebook' => get_option('kata_seo_facebook_url'),
            'twitter' => get_option('kata_seo_twitter_url'),
            'linkedin' => get_option('kata_seo_linkedin_url'),
            'instagram' => get_option('kata_seo_instagram_url'),
            'youtube' => get_option('kata_seo_youtube_url')
        );
        
        foreach ($social_links as $link) {
            if (!empty($link)) {
                $profiles[] = $link;
            }
        }
        
        return $profiles;
    }
}
