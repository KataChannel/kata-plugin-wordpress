<?php
/**
 * Breadcrumb Schema Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Breadcrumb Schema Class
 */
class KataSchema_Breadcrumb {
    
    /**
     * Generate Breadcrumb schema
     */
    public function generate($post_id = null) {
        $breadcrumb_items = $this->get_breadcrumb_items($post_id);
        
        if (empty($breadcrumb_items)) {
            return false;
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumb_items
        );
        
        // Apply filters
        $schema = apply_filters('kata_schema_breadcrumb', $schema, $post_id);
        
        return $schema;
    }
    
    /**
     * Get breadcrumb items
     */
    private function get_breadcrumb_items($post_id = null) {
        $items = array();
        $position = 1;
        
        // Home page
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => get_bloginfo('name'),
            'item' => home_url('/')
        );
        
        if (is_front_page()) {
            return $items;
        }
        
        // Single post or page
        if (is_singular()) {
            $post = $post_id ? get_post($post_id) : get_queried_object();
            
            if (!$post) {
                return $items;
            }
            
            // Add custom post type archive
            if ($post->post_type !== 'post' && $post->post_type !== 'page') {
                $post_type_object = get_post_type_object($post->post_type);
                if ($post_type_object && $post_type_object->has_archive) {
                    $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $post_type_object->labels->name,
                        'item' => get_post_type_archive_link($post->post_type)
                    );
                }
            }
            
            // Add category for posts
            if ($post->post_type === 'post') {
                $categories = get_the_category($post->ID);
                if (!empty($categories)) {
                    $category = $categories[0];
                    
                    // Add parent categories
                    $parent_categories = $this->get_parent_categories($category);
                    foreach ($parent_categories as $parent_cat) {
                        $items[] = array(
                            '@type' => 'ListItem',
                            'position' => $position++,
                            'name' => $parent_cat->name,
                            'item' => get_category_link($parent_cat->term_id)
                        );
                    }
                    
                    // Add current category
                    $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $category->name,
                        'item' => get_category_link($category->term_id)
                    );
                }
            }
            
            // Add parent pages
            if ($post->post_parent) {
                $parents = $this->get_parent_pages($post->post_parent);
                foreach ($parents as $parent) {
                    $items[] = array(
                        '@type' => 'ListItem',
                        'position' => $position++,
                        'name' => $parent->post_title,
                        'item' => get_permalink($parent->ID)
                    );
                }
            }
            
            // Add current page/post
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => get_the_title($post->ID),
                'item' => get_permalink($post->ID)
            );
            
        } elseif (is_category()) {
            $category = get_queried_object();
            
            // Add parent categories
            $parent_categories = $this->get_parent_categories($category);
            foreach ($parent_categories as $parent_cat) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $parent_cat->name,
                    'item' => get_category_link($parent_cat->term_id)
                );
            }
            
            // Add current category
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $category->name,
                'item' => get_category_link($category->term_id)
            );
            
        } elseif (is_tag()) {
            $tag = get_queried_object();
            
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $tag->name,
                'item' => get_tag_link($tag->term_id)
            );
            
        } elseif (is_tax()) {
            $term = get_queried_object();
            $taxonomy = get_taxonomy($term->taxonomy);
            
            // Add taxonomy archive if it has one
            if ($taxonomy->public) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $taxonomy->labels->name,
                    'item' => get_term_link($term)
                );
            }
            
            // Add parent terms
            $parent_terms = $this->get_parent_terms($term);
            foreach ($parent_terms as $parent_term) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $parent_term->name,
                    'item' => get_term_link($parent_term)
                );
            }
            
            // Add current term
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $term->name,
                'item' => get_term_link($term)
            );
            
        } elseif (is_post_type_archive()) {
            $post_type = get_query_var('post_type');
            $post_type_object = get_post_type_object($post_type);
            
            if ($post_type_object) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $post_type_object->labels->name,
                    'item' => get_post_type_archive_link($post_type)
                );
            }
            
        } elseif (is_author()) {
            $author = get_queried_object();
            
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => sprintf(__('Author: %s', 'kata-schema-markup'), $author->display_name),
                'item' => get_author_posts_url($author->ID)
            );
            
        } elseif (is_date()) {
            if (is_year()) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => get_query_var('year'),
                    'item' => get_year_link(get_query_var('year'))
                );
            } elseif (is_month()) {
                $year = get_query_var('year');
                $month = get_query_var('monthnum');
                
                // Add year
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $year,
                    'item' => get_year_link($year)
                );
                
                // Add month
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => date_i18n('F', mktime(0, 0, 0, $month, 1)),
                    'item' => get_month_link($year, $month)
                );
            } elseif (is_day()) {
                $year = get_query_var('year');
                $month = get_query_var('monthnum');
                $day = get_query_var('day');
                
                // Add year
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $year,
                    'item' => get_year_link($year)
                );
                
                // Add month
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => date_i18n('F', mktime(0, 0, 0, $month, 1)),
                    'item' => get_month_link($year, $month)
                );
                
                // Add day
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $day,
                    'item' => get_day_link($year, $month, $day)
                );
            }
            
        } elseif (is_search()) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => sprintf(__('Search Results for: %s', 'kata-schema-markup'), get_search_query()),
                'item' => get_search_link()
            );
            
        } elseif (is_404()) {
            $items[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => __('Page Not Found', 'kata-schema-markup')
                // No item URL for 404 pages
            );
        }
        
        return $items;
    }
    
    /**
     * Get parent categories
     */
    private function get_parent_categories($category) {
        $parents = array();
        
        if ($category->parent) {
            $parent = get_category($category->parent);
            if ($parent && !is_wp_error($parent)) {
                $parents = array_merge($this->get_parent_categories($parent), array($parent));
            }
        }
        
        return $parents;
    }
    
    /**
     * Get parent pages
     */
    private function get_parent_pages($parent_id) {
        $parents = array();
        
        while ($parent_id) {
            $parent = get_post($parent_id);
            if ($parent) {
                array_unshift($parents, $parent);
                $parent_id = $parent->post_parent;
            } else {
                break;
            }
        }
        
        return $parents;
    }
    
    /**
     * Get parent terms
     */
    private function get_parent_terms($term) {
        $parents = array();
        
        if ($term->parent) {
            $parent = get_term($term->parent, $term->taxonomy);
            if ($parent && !is_wp_error($parent)) {
                $parents = array_merge($this->get_parent_terms($parent), array($parent));
            }
        }
        
        return $parents;
    }
}
