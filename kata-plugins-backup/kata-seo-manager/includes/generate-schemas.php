<?php
/**
 * Schema Generator Script
 * Run this file once to generate all 26 schema class files
 * 
 * Usage: php generate-schemas.php
 */

// Schema definitions from kataseotool.md
$schemas = array(
    'Article' => array(
        'subtypes' => array('NewsArticle', 'BlogPosting'),
        'description' => 'Article schema for news, blog posts, and articles'
    ),
    'Breadcrumb' => array(
        'subtypes' => array(),
        'description' => 'Breadcrumb navigation schema'
    ),
    'Carousel' => array(
        'subtypes' => array(),
        'description' => 'Carousel schema for lists and galleries'
    ),
    'Course' => array(
        'subtypes' => array(),
        'description' => 'Educational course schema'
    ),
    'Dataset' => array(
        'subtypes' => array(),
        'description' => 'Dataset schema for large data collections'
    ),
    'Forum' => array(
        'subtypes' => array(),
        'description' => 'Discussion forum schema'
    ),
    'EduQA' => array(
        'subtypes' => array(),
        'description' => 'Educational Q&A schema'
    ),
    'EmployerRating' => array(
        'subtypes' => array(),
        'description' => 'Employer aggregate rating schema'
    ),
    'Event' => array(
        'subtypes' => array('Concert', 'Festival'),
        'description' => 'Event schema for concerts, festivals, etc.'
    ),
    'FAQ' => array(
        'subtypes' => array(),
        'description' => 'Frequently Asked Questions schema'
    ),
    'HowTo' => array(
        'subtypes' => array(),
        'description' => 'How-to guide schema with step-by-step instructions'
    ),
    'ImageMetadata' => array(
        'subtypes' => array(),
        'description' => 'Image metadata schema for Google Images'
    ),
    'JobPosting' => array(
        'subtypes' => array(),
        'description' => 'Job posting schema'
    ),
    'LocalBusiness' => array(
        'subtypes' => array('Restaurant', 'Store'),
        'description' => 'Local business schema'
    ),
    'MathSolver' => array(
        'subtypes' => array(),
        'description' => 'Math solver schema for educational content'
    ),
    'Movie' => array(
        'subtypes' => array(),
        'description' => 'Movie schema'
    ),
    'Organization' => array(
        'subtypes' => array(),
        'description' => 'Organization schema'
    ),
    'PracticeProblem' => array(
        'subtypes' => array(),
        'description' => 'Practice problem schema for educational content'
    ),
    'Product' => array(
        'subtypes' => array(),
        'description' => 'Product schema for e-commerce'
    ),
    'ProfilePage' => array(
        'subtypes' => array(),
        'description' => 'Profile page schema'
    ),
    'Recipe' => array(
        'subtypes' => array(),
        'description' => 'Recipe schema for cooking instructions'
    ),
    'Review' => array(
        'subtypes' => array(),
        'description' => 'Review schema'
    ),
    'Sitelinks' => array(
        'subtypes' => array(),
        'description' => 'Sitelinks searchbox schema'
    ),
    'Speakable' => array(
        'subtypes' => array(),
        'description' => 'Speakable schema for voice search'
    ),
    'Video' => array(
        'subtypes' => array(),
        'description' => 'Video schema'
    ),
    'WebPage' => array(
        'subtypes' => array(),
        'description' => 'WebPage schema'
    ),
);

// Generate class files
foreach ($schemas as $type => $info) {
    $class_name = 'KATA_' . $type . '_Schema';
    $file_name = 'class-' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $type)) . '-schema.php';
    
    $template = generate_schema_class($type, $class_name, $info);
    
    file_put_contents(__DIR__ . '/schemas/' . $file_name, $template);
    echo "Created: {$file_name}\n";
}

echo "\nAll 26 schema classes generated successfully!\n";

function generate_schema_class($type, $class_name, $info) {
    $description = $info['description'];
    $subtypes = !empty($info['subtypes']) ? implode(', ', $info['subtypes']) : 'None';
    
    return <<<PHP
<?php
/**
 * {$type} Schema Class
 * 
 * {$description}
 * Subtypes: {$subtypes}
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class {$class_name} extends KATA_Base_Schema {
    
    protected \$type = '{$type}';
    
    protected \$required_fields = array('name');
    
    /**
     * Generate {$type} schema
     * 
     * @param array \$data Schema data
     * @param array \$options Additional options
     * @return array Schema array
     */
    public function generate(\$data, \$options = array()) {
        \$schema = \$this->build_base();
        
        // Replace placeholders if post_id provided
        if (!empty(\$data['post_id'])) {
            \$data = \$this->replace_placeholders(\$data, \$data['post_id']);
        }
        
        // Add required fields
        \$this->add_field(\$schema, 'name', \$data['name'] ?? \$data['title'] ?? '');
        
        // Add optional fields
        \$this->add_field(\$schema, 'description', \$data['description'] ?? '');
        \$this->add_field(\$schema, 'url', \$data['url'] ?? '');
        
        // Add image if available
        if (!empty(\$data['image'])) {
            \$schema['image'] = \$this->build_image(\$data['image']);
        }
        
        return \$schema;
    }
    
    /**
     * Get example data
     * 
     * @return array Example data
     */
    public function get_example() {
        return array(
            'name' => 'Example {$type}',
            'description' => 'This is an example {$type} description',
            'url' => 'https://example.com/{strtolower($type)}',
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
                'placeholder' => 'Enter {$type} name'
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

PHP;
}
