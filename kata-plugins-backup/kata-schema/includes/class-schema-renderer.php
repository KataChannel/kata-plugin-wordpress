<?php
/**
 * KATA Schema Renderer Class
 * 
 * Handles frontend rendering of schemas
 * 
 * @package KATA_Schema
 * @since 1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class KATA_Schema_Renderer {
    
    /**
     * Render schema for frontend display (optional visual representation)
     */
    public static function render_frontend($schema_data, $show_frontend = false) {
        if (!$show_frontend) {
            return '';
        }
        
        $schema = json_decode($schema_data, true);
        
        if (!$schema || !isset($schema['@type'])) {
            return '';
        }
        
        $output = '<div class="kata-schema-display">';
        
        switch ($schema['@type']) {
            case 'Article':
                $output .= self::render_article($schema);
                break;
            case 'Product':
                $output .= self::render_product($schema);
                break;
            case 'FAQ':
                $output .= self::render_faq($schema);
                break;
            case 'HowTo':
                $output .= self::render_howto($schema);
                break;
            case 'Event':
                $output .= self::render_event($schema);
                break;
            case 'Recipe':
                $output .= self::render_recipe($schema);
                break;
            default:
                $output .= self::render_generic($schema);
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render Article schema
     */
    private static function render_article($schema) {
        $output = '<div class="kata-article-schema">';
        
        if (isset($schema['headline'])) {
            $output .= '<h3>' . esc_html($schema['headline']) . '</h3>';
        }
        
        if (isset($schema['author']['name'])) {
            $output .= '<p class="author">Tác giả: ' . esc_html($schema['author']['name']) . '</p>';
        }
        
        if (isset($schema['datePublished'])) {
            $output .= '<p class="date">Ngày xuất bản: ' . esc_html($schema['datePublished']) . '</p>';
        }
        
        if (isset($schema['description'])) {
            $output .= '<p class="description">' . esc_html($schema['description']) . '</p>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render Product schema
     */
    private static function render_product($schema) {
        $output = '<div class="kata-product-schema">';
        
        if (isset($schema['name'])) {
            $output .= '<h3>' . esc_html($schema['name']) . '</h3>';
        }
        
        if (isset($schema['image'])) {
            $output .= '<img src="' . esc_url($schema['image']) . '" alt="' . esc_attr($schema['name']) . '" />';
        }
        
        if (isset($schema['description'])) {
            $output .= '<p class="description">' . esc_html($schema['description']) . '</p>';
        }
        
        if (isset($schema['offers']['price'])) {
            $currency = isset($schema['offers']['priceCurrency']) ? $schema['offers']['priceCurrency'] : 'VND';
            $output .= '<p class="price">Giá: ' . number_format($schema['offers']['price']) . ' ' . esc_html($currency) . '</p>';
        }
        
        if (isset($schema['aggregateRating'])) {
            $output .= '<p class="rating">Đánh giá: ' . esc_html($schema['aggregateRating']['ratingValue']) . '/5 ';
            $output .= '(' . esc_html($schema['aggregateRating']['reviewCount']) . ' reviews)</p>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render FAQ schema
     */
    private static function render_faq($schema) {
        $output = '<div class="kata-faq-schema">';
        
        if (isset($schema['mainEntity']) && is_array($schema['mainEntity'])) {
            $output .= '<div class="faq-list">';
            
            foreach ($schema['mainEntity'] as $index => $item) {
                if (isset($item['name']) && isset($item['acceptedAnswer']['text'])) {
                    $output .= '<div class="faq-item">';
                    $output .= '<h4 class="question">' . esc_html($item['name']) . '</h4>';
                    $output .= '<p class="answer">' . esc_html($item['acceptedAnswer']['text']) . '</p>';
                    $output .= '</div>';
                }
            }
            
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render HowTo schema
     */
    private static function render_howto($schema) {
        $output = '<div class="kata-howto-schema">';
        
        if (isset($schema['name'])) {
            $output .= '<h3>' . esc_html($schema['name']) . '</h3>';
        }
        
        if (isset($schema['description'])) {
            $output .= '<p class="description">' . esc_html($schema['description']) . '</p>';
        }
        
        if (isset($schema['totalTime'])) {
            $output .= '<p class="time">Thời gian: ' . esc_html($schema['totalTime']) . '</p>';
        }
        
        if (isset($schema['step']) && is_array($schema['step'])) {
            $output .= '<ol class="steps">';
            
            foreach ($schema['step'] as $index => $step) {
                if (isset($step['text'])) {
                    $output .= '<li>';
                    if (isset($step['name'])) {
                        $output .= '<strong>' . esc_html($step['name']) . '</strong>: ';
                    }
                    $output .= esc_html($step['text']);
                    $output .= '</li>';
                }
            }
            
            $output .= '</ol>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render Event schema
     */
    private static function render_event($schema) {
        $output = '<div class="kata-event-schema">';
        
        if (isset($schema['name'])) {
            $output .= '<h3>' . esc_html($schema['name']) . '</h3>';
        }
        
        if (isset($schema['description'])) {
            $output .= '<p class="description">' . esc_html($schema['description']) . '</p>';
        }
        
        if (isset($schema['startDate'])) {
            $output .= '<p class="date">Ngày bắt đầu: ' . esc_html(date('d/m/Y H:i', strtotime($schema['startDate']))) . '</p>';
        }
        
        if (isset($schema['location']['name'])) {
            $output .= '<p class="location">Địa điểm: ' . esc_html($schema['location']['name']) . '</p>';
        }
        
        if (isset($schema['offers']['price'])) {
            $currency = isset($schema['offers']['priceCurrency']) ? $schema['offers']['priceCurrency'] : 'VND';
            $output .= '<p class="price">Giá vé: ' . number_format($schema['offers']['price']) . ' ' . esc_html($currency) . '</p>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render Recipe schema
     */
    private static function render_recipe($schema) {
        $output = '<div class="kata-recipe-schema">';
        
        if (isset($schema['name'])) {
            $output .= '<h3>' . esc_html($schema['name']) . '</h3>';
        }
        
        if (isset($schema['description'])) {
            $output .= '<p class="description">' . esc_html($schema['description']) . '</p>';
        }
        
        if (isset($schema['prepTime']) || isset($schema['cookTime']) || isset($schema['totalTime'])) {
            $output .= '<div class="times">';
            if (isset($schema['prepTime'])) {
                $output .= '<span>Chuẩn bị: ' . esc_html($schema['prepTime']) . '</span> ';
            }
            if (isset($schema['cookTime'])) {
                $output .= '<span>Nấu: ' . esc_html($schema['cookTime']) . '</span> ';
            }
            if (isset($schema['totalTime'])) {
                $output .= '<span>Tổng: ' . esc_html($schema['totalTime']) . '</span>';
            }
            $output .= '</div>';
        }
        
        if (isset($schema['recipeIngredient']) && is_array($schema['recipeIngredient'])) {
            $output .= '<h4>Nguyên liệu:</h4>';
            $output .= '<ul class="ingredients">';
            foreach ($schema['recipeIngredient'] as $ingredient) {
                $output .= '<li>' . esc_html($ingredient) . '</li>';
            }
            $output .= '</ul>';
        }
        
        if (isset($schema['recipeInstructions']) && is_array($schema['recipeInstructions'])) {
            $output .= '<h4>Cách làm:</h4>';
            $output .= '<ol class="instructions">';
            foreach ($schema['recipeInstructions'] as $step) {
                if (isset($step['text'])) {
                    $output .= '<li>' . esc_html($step['text']) . '</li>';
                }
            }
            $output .= '</ol>';
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Render generic schema (fallback)
     */
    private static function render_generic($schema) {
        $output = '<div class="kata-generic-schema">';
        
        if (isset($schema['name'])) {
            $output .= '<h3>' . esc_html($schema['name']) . '</h3>';
        }
        
        if (isset($schema['description'])) {
            $output .= '<p class="description">' . esc_html($schema['description']) . '</p>';
        }
        
        $output .= '<p class="schema-type">Schema Type: ' . esc_html($schema['@type']) . '</p>';
        
        $output .= '</div>';
        
        return $output;
    }
}
