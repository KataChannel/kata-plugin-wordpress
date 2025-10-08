<?php
/**
 * Schema Validator Class
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Schema Validator Class
 */
class KataSchema_Validator {
    
    /**
     * Validation rules for different schema types
     */
    private $validation_rules = array();
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->init_validation_rules();
    }
    
    /**
     * Initialize validation rules
     */
    private function init_validation_rules() {
        $this->validation_rules = array(
            'Article' => array(
                'required' => array('@context', '@type', 'headline'),
                'recommended' => array('author', 'datePublished', 'dateModified', 'image', 'publisher'),
                'properties' => array(
                    'headline' => 'string',
                    'author' => 'object|string',
                    'datePublished' => 'date',
                    'dateModified' => 'date',
                    'image' => 'url|object',
                    'publisher' => 'object',
                    'articleBody' => 'string',
                    'description' => 'string'
                )
            ),
            'Product' => array(
                'required' => array('@context', '@type', 'name'),
                'recommended' => array('image', 'description', 'brand', 'offers'),
                'properties' => array(
                    'name' => 'string',
                    'image' => 'url|object',
                    'description' => 'string',
                    'brand' => 'object|string',
                    'offers' => 'object',
                    'aggregateRating' => 'object',
                    'review' => 'object'
                )
            ),
            'Organization' => array(
                'required' => array('@context', '@type', 'name'),
                'recommended' => array('url', 'logo', 'sameAs'),
                'properties' => array(
                    'name' => 'string',
                    'url' => 'url',
                    'logo' => 'url|object',
                    'sameAs' => 'array',
                    'contactPoint' => 'object',
                    'address' => 'object'
                )
            ),
            'Website' => array(
                'required' => array('@context', '@type', 'name', 'url'),
                'recommended' => array('potentialAction'),
                'properties' => array(
                    'name' => 'string',
                    'url' => 'url',
                    'potentialAction' => 'object'
                )
            ),
            'BreadcrumbList' => array(
                'required' => array('@context', '@type', 'itemListElement'),
                'recommended' => array(),
                'properties' => array(
                    'itemListElement' => 'array'
                )
            ),
            'Review' => array(
                'required' => array('@context', '@type', 'itemReviewed', 'reviewRating', 'author'),
                'recommended' => array('reviewBody', 'datePublished'),
                'properties' => array(
                    'itemReviewed' => 'object',
                    'reviewRating' => 'object',
                    'author' => 'object|string',
                    'reviewBody' => 'string',
                    'datePublished' => 'date'
                )
            ),
            'FAQPage' => array(
                'required' => array('@context', '@type', 'mainEntity'),
                'recommended' => array(),
                'properties' => array(
                    'mainEntity' => 'array'
                )
            ),
            'HowTo' => array(
                'required' => array('@context', '@type', 'name', 'step'),
                'recommended' => array('description', 'image', 'totalTime'),
                'properties' => array(
                    'name' => 'string',
                    'description' => 'string',
                    'image' => 'url|object',
                    'step' => 'array',
                    'totalTime' => 'string',
                    'supply' => 'array',
                    'tool' => 'array'
                )
            ),
            'Event' => array(
                'required' => array('@context', '@type', 'name', 'startDate'),
                'recommended' => array('location', 'description', 'endDate'),
                'properties' => array(
                    'name' => 'string',
                    'startDate' => 'date',
                    'endDate' => 'date',
                    'location' => 'object',
                    'description' => 'string',
                    'organizer' => 'object',
                    'offers' => 'object'
                )
            ),
            'Recipe' => array(
                'required' => array('@context', '@type', 'name', 'recipeIngredient', 'recipeInstructions'),
                'recommended' => array('image', 'description', 'nutrition', 'cookTime', 'prepTime'),
                'properties' => array(
                    'name' => 'string',
                    'image' => 'url|object',
                    'description' => 'string',
                    'recipeIngredient' => 'array',
                    'recipeInstructions' => 'array',
                    'nutrition' => 'object',
                    'cookTime' => 'string',
                    'prepTime' => 'string',
                    'recipeYield' => 'string',
                    'recipeCategory' => 'string',
                    'recipeCuisine' => 'string'
                )
            )
        );
        
        // Allow custom validation rules
        $this->validation_rules = apply_filters('kata_schema_validation_rules', $this->validation_rules);
    }
    
    /**
     * Validate schema structure
     */
    public function validate_schema($schema) {
        $result = array(
            'valid' => true,
            'errors' => array(),
            'warnings' => array(),
            'score' => 100
        );
        
        // Check if schema is array
        if (!is_array($schema)) {
            $result['valid'] = false;
            $result['errors'][] = __('Schema must be a valid JSON object', 'kata-schema-markup');
            $result['score'] = 0;
            return $result;
        }
        
        // Basic structure validation
        $basic_validation = $this->validate_basic_structure($schema);
        $result = $this->merge_validation_results($result, $basic_validation);
        
        // Type-specific validation
        if (isset($schema['@type'])) {
            $type_validation = $this->validate_schema_type($schema, $schema['@type']);
            $result = $this->merge_validation_results($result, $type_validation);
        }
        
        // Calculate final score
        $result['score'] = $this->calculate_validation_score($result);
        
        return $result;
    }
    
    /**
     * Validate basic schema structure
     */
    private function validate_basic_structure($schema) {
        $result = array(
            'valid' => true,
            'errors' => array(),
            'warnings' => array()
        );
        
        // Check @context
        if (!isset($schema['@context'])) {
            $result['valid'] = false;
            $result['errors'][] = __('@context is required', 'kata-schema-markup');
        } elseif ($schema['@context'] !== 'https://schema.org') {
            $result['warnings'][] = __('@context should be "https://schema.org"', 'kata-schema-markup');
        }
        
        // Check @type
        if (!isset($schema['@type'])) {
            $result['valid'] = false;
            $result['errors'][] = __('@type is required', 'kata-schema-markup');
        } elseif (!is_string($schema['@type'])) {
            $result['valid'] = false;
            $result['errors'][] = __('@type must be a string', 'kata-schema-markup');
        }
        
        return $result;
    }
    
    /**
     * Validate specific schema type
     */
    private function validate_schema_type($schema, $type) {
        $result = array(
            'valid' => true,
            'errors' => array(),
            'warnings' => array()
        );
        
        if (!isset($this->validation_rules[$type])) {
            $result['warnings'][] = sprintf(__('No validation rules found for type: %s', 'kata-schema-markup'), $type);
            return $result;
        }
        
        $rules = $this->validation_rules[$type];
        
        // Check required fields
        foreach ($rules['required'] as $field) {
            if (!isset($schema[$field]) || empty($schema[$field])) {
                $result['valid'] = false;
                $result['errors'][] = sprintf(__('Required field "%s" is missing or empty', 'kata-schema-markup'), $field);
            }
        }
        
        // Check recommended fields
        foreach ($rules['recommended'] as $field) {
            if (!isset($schema[$field]) || empty($schema[$field])) {
                $result['warnings'][] = sprintf(__('Recommended field "%s" is missing', 'kata-schema-markup'), $field);
            }
        }
        
        // Validate property types
        if (isset($rules['properties'])) {
            foreach ($rules['properties'] as $property => $expected_type) {
                if (isset($schema[$property])) {
                    $type_validation = $this->validate_property_type($schema[$property], $expected_type, $property);
                    $result = $this->merge_validation_results($result, $type_validation);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Validate property type
     */
    private function validate_property_type($value, $expected_type, $property_name) {
        $result = array(
            'valid' => true,
            'errors' => array(),
            'warnings' => array()
        );
        
        $types = explode('|', $expected_type);
        $is_valid = false;
        
        foreach ($types as $type) {
            switch (trim($type)) {
                case 'string':
                    if (is_string($value)) {
                        $is_valid = true;
                    }
                    break;
                    
                case 'array':
                    if (is_array($value)) {
                        $is_valid = true;
                    }
                    break;
                    
                case 'object':
                    if (is_array($value) && $this->is_associative_array($value)) {
                        $is_valid = true;
                    }
                    break;
                    
                case 'url':
                    if (is_string($value) && filter_var($value, FILTER_VALIDATE_URL)) {
                        $is_valid = true;
                    }
                    break;
                    
                case 'date':
                    if (is_string($value) && $this->is_valid_date($value)) {
                        $is_valid = true;
                    }
                    break;
                    
                case 'number':
                    if (is_numeric($value)) {
                        $is_valid = true;
                    }
                    break;
                    
                case 'boolean':
                    if (is_bool($value)) {
                        $is_valid = true;
                    }
                    break;
            }
            
            if ($is_valid) {
                break;
            }
        }
        
        if (!$is_valid) {
            $result['warnings'][] = sprintf(
                __('Property "%s" should be of type: %s', 'kata-schema-markup'),
                $property_name,
                $expected_type
            );
        }
        
        return $result;
    }
    
    /**
     * Check if array is associative
     */
    private function is_associative_array($array) {
        if (!is_array($array)) {
            return false;
        }
        
        return array_keys($array) !== range(0, count($array) - 1);
    }
    
    /**
     * Validate date format
     */
    private function is_valid_date($date) {
        // Check ISO 8601 format
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $date)) {
            return true;
        }
        
        // Check simple date format
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $date)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Merge validation results
     */
    private function merge_validation_results($result1, $result2) {
        return array(
            'valid' => $result1['valid'] && $result2['valid'],
            'errors' => array_merge($result1['errors'], $result2['errors']),
            'warnings' => array_merge($result1['warnings'], $result2['warnings'])
        );
    }
    
    /**
     * Calculate validation score
     */
    private function calculate_validation_score($result) {
        $score = 100;
        
        // Deduct points for errors
        $error_penalty = count($result['errors']) * 20;
        $score -= $error_penalty;
        
        // Deduct points for warnings
        $warning_penalty = count($result['warnings']) * 5;
        $score -= $warning_penalty;
        
        return max(0, $score);
    }
    
    /**
     * Test schema with Google's Structured Data Testing Tool
     */
    public function test_with_google($schema_json, $url = null) {
        $result = array(
            'success' => false,
            'message' => '',
            'data' => array()
        );
        
        // Note: Google's Structured Data Testing Tool API is not public
        // This is a placeholder for future implementation
        
        $result['message'] = __('Google testing feature is not available in this version', 'kata-schema-markup');
        
        return $result;
    }
    
    /**
     * Validate schema against Schema.org vocabulary
     */
    public function validate_against_vocabulary($schema) {
        $result = array(
            'valid' => true,
            'errors' => array(),
            'warnings' => array()
        );
        
        // Known Schema.org types
        $known_types = array(
            'Article', 'NewsArticle', 'BlogPosting', 'Product', 'Organization',
            'Person', 'Place', 'Event', 'Recipe', 'Review', 'Rating',
            'AggregateRating', 'Offer', 'PriceSpecification', 'Website',
            'WebPage', 'BreadcrumbList', 'ListItem', 'ImageObject',
            'VideoObject', 'AudioObject', 'CreativeWork', 'Thing',
            'FAQPage', 'Question', 'Answer', 'HowTo', 'HowToStep',
            'HowToDirection', 'ItemList', 'Service', 'LocalBusiness'
        );
        
        // Check if @type is known
        if (isset($schema['@type'])) {
            if (!in_array($schema['@type'], $known_types)) {
                $result['warnings'][] = sprintf(
                    __('Type "%s" may not be a standard Schema.org type', 'kata-schema-markup'),
                    $schema['@type']
                );
            }
        }
        
        // Validate nested objects
        foreach ($schema as $property => $value) {
            if (is_array($value) && isset($value['@type'])) {
                $nested_validation = $this->validate_against_vocabulary($value);
                $result = $this->merge_validation_results($result, $nested_validation);
            } elseif (is_array($value) && !isset($value['@type'])) {
                // Check if it's an array of objects
                foreach ($value as $item) {
                    if (is_array($item) && isset($item['@type'])) {
                        $item_validation = $this->validate_against_vocabulary($item);
                        $result = $this->merge_validation_results($result, $item_validation);
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Get validation suggestions
     */
    public function get_suggestions($schema) {
        $suggestions = array();
        
        if (!isset($schema['@type'])) {
            return $suggestions;
        }
        
        $type = $schema['@type'];
        
        switch ($type) {
            case 'Article':
                if (!isset($schema['author'])) {
                    $suggestions[] = __('Consider adding author information for better SEO', 'kata-schema-markup');
                }
                if (!isset($schema['image'])) {
                    $suggestions[] = __('Adding an image can improve rich snippet appearance', 'kata-schema-markup');
                }
                if (!isset($schema['publisher'])) {
                    $suggestions[] = __('Publisher information helps establish authority', 'kata-schema-markup');
                }
                break;
                
            case 'Product':
                if (!isset($schema['aggregateRating'])) {
                    $suggestions[] = __('Aggregate rating can improve product visibility', 'kata-schema-markup');
                }
                if (!isset($schema['offers'])) {
                    $suggestions[] = __('Offer information is crucial for e-commerce', 'kata-schema-markup');
                }
                break;
                
            case 'Organization':
                if (!isset($schema['sameAs'])) {
                    $suggestions[] = __('Social media profiles help establish credibility', 'kata-schema-markup');
                }
                if (!isset($schema['logo'])) {
                    $suggestions[] = __('Logo helps with brand recognition in search results', 'kata-schema-markup');
                }
                break;
        }
        
        return $suggestions;
    }
    
    /**
     * Generate validation report
     */
    public function generate_report($schema) {
        $validation = $this->validate_schema($schema);
        $vocabulary_check = $this->validate_against_vocabulary($schema);
        $suggestions = $this->get_suggestions($schema);
        
        return array(
            'overall_score' => $validation['score'],
            'is_valid' => $validation['valid'],
            'errors' => array_unique(array_merge($validation['errors'], $vocabulary_check['errors'])),
            'warnings' => array_unique(array_merge($validation['warnings'], $vocabulary_check['warnings'])),
            'suggestions' => $suggestions,
            'schema_type' => isset($schema['@type']) ? $schema['@type'] : 'Unknown',
            'properties_count' => count($schema),
            'generated_at' => current_time('c')
        );
    }
}
