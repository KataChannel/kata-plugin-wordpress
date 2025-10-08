<?php
/**
 * PracticeProblem Schema Class
 * 
 * Practice problem schema for educational content
 * Subtypes: None
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_PracticeProblem_Schema extends KATA_Base_Schema {
    
    protected $type = 'PracticeProblem';
    
    protected $required_fields = array('name');
    
    /**
     * Generate PracticeProblem schema
     * 
     * @param array $data Schema data
     * @param array $options Additional options
     * @return array Schema array
     */
    public function generate($data, $options = array()) {
        $schema = $this->build_base();
        
        // Replace placeholders if post_id provided
        if (!empty($data['post_id'])) {
            $data = $this->replace_placeholders($data, $data['post_id']);
        }
        
        // Add required fields
        $this->add_field($schema, 'name', $data['name'] ?? $data['title'] ?? '');
        
        // Add optional fields
        $this->add_field($schema, 'description', $data['description'] ?? '');
        $this->add_field($schema, 'url', $data['url'] ?? '');
        
        // Add image if available
        if (!empty($data['image'])) {
            $schema['image'] = $this->build_image($data['image']);
        }
        
        return $schema;
    }
    
    /**
     * Get example data
     * 
     * @return array Example data
     */
    public function get_example() {
        return array(
            'name' => 'Example PracticeProblem',
            'description' => 'This is an example PracticeProblem description',
            'url' => 'https://example.com/{strtolower(PracticeProblem)}',
            'image' => 'https://example.com/image.jpg'
        );
    }
    
    /**
     * Get field definitions
     * 
     * @return array Field definitions
     */
    public function get_fields() {
        return array(
            'name' => array(
                'label' => __('Name', 'kata-seo-manager'),
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Enter PracticeProblem name'
            ),
            'description' => array(
                'label' => __('Description', 'kata-seo-manager'),
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Enter description'
            ),
            'url' => array(
                'label' => __('URL', 'kata-seo-manager'),
                'type' => 'url',
                'required' => false,
                'placeholder' => 'https://example.com'
            ),
            'image' => array(
                'label' => __('Image', 'kata-seo-manager'),
                'type' => 'image',
                'required' => false
            )
        );
    }
}
