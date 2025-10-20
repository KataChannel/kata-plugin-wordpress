<?php
/**
 * Product Schema Class
 * 
 * Product schema for e-commerce
 * Subtypes: None
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_Product_Schema extends KATA_Base_Schema {
    
    protected $type = 'Product';
    
    protected $required_fields = array('name');
    
    /**
     * Generate Product schema
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
            'name' => 'Example Product',
            'description' => 'This is an example Product description',
            'url' => 'https://example.com/{strtolower(Product)}',
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
                'placeholder' => 'Enter Product name'
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
