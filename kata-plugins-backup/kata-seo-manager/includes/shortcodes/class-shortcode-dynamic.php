<?php
/**
 * Dynamic Schema Shortcode
 * 
 * [kata_dynamic]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Dynamic extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_dynamic';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'schema_id' => '',
            'show_output' => 'false'
        ));
        
        if (empty($atts['schema_id'])) {
            $this->log_error('Schema ID is required');
            return '';
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'kata_schemas';
        
        // Get schema from database
        $schema_row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d AND status = 'active'",
            $atts['schema_id']
        ));
        
        if (!$schema_row) {
            $this->log_error('Schema not found or inactive: ' . $atts['schema_id']);
            return '';
        }
        
        // Decode schema data
        $schema_data = json_decode($schema_row->schema_data, true);
        
        if (empty($schema_data)) {
            $this->log_error('Invalid schema data');
            return '';
        }
        
        // Set schema type for generation
        $this->schema_type = $schema_row->schema_type;
        
        // Generate and store schema
        $schema = $this->generate_schema($schema_data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        // Return HTML output if requested
        if ($atts['show_output'] === 'true') {
            return $this->render_schema_output($schema_row->schema_type, $schema_data);
        }
        
        return '';
    }
    
    /**
     * Render HTML output based on schema type
     * 
     * @param string $schema_type Schema type
     * @param array $data Schema data
     * @return string
     */
    private function render_schema_output($schema_type, $data) {
        ob_start();
        
        switch ($schema_type) {
            case 'article':
                $this->render_article_output($data);
                break;
            
            case 'faq':
                $this->render_faq_output($data);
                break;
            
            case 'organization':
                $this->render_organization_output($data);
                break;
            
            case 'product':
                $this->render_product_output($data);
                break;
            
            case 'event':
                $this->render_event_output($data);
                break;
            
            default:
                echo '<div class="kata-schema-output">';
                echo '<p>' . sprintf(__('Schema Type: %s', 'kata-seo-manager'), esc_html($schema_type)) . '</p>';
                echo '</div>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * Render article output
     */
    private function render_article_output($data) {
        ?>
        <article class="kata-article-dynamic">
            <?php if (!empty($data['headline'])): ?>
                <h2><?php echo esc_html($data['headline']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($data['description'])): ?>
                <p><?php echo esc_html($data['description']); ?></p>
            <?php endif; ?>
        </article>
        <?php
    }
    
    /**
     * Render FAQ output
     */
    private function render_faq_output($data) {
        if (empty($data['faq_items'])) {
            return;
        }
        ?>
        <div class="kata-faq-dynamic">
            <?php foreach ($data['faq_items'] as $item): ?>
                <div class="kata-faq-item">
                    <h3><?php echo esc_html($item['question']); ?></h3>
                    <p><?php echo wpautop($item['answer']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * Render organization output
     */
    private function render_organization_output($data) {
        ?>
        <div class="kata-organization-dynamic">
            <?php if (!empty($data['name'])): ?>
                <h2><?php echo esc_html($data['name']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($data['description'])): ?>
                <p><?php echo esc_html($data['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render product output
     */
    private function render_product_output($data) {
        ?>
        <div class="kata-product-dynamic">
            <?php if (!empty($data['name'])): ?>
                <h2><?php echo esc_html($data['name']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($data['description'])): ?>
                <p><?php echo esc_html($data['description']); ?></p>
            <?php endif; ?>
            <?php if (!empty($data['offers']['price'])): ?>
                <p class="price"><?php echo esc_html($data['offers']['priceCurrency'] . ' ' . $data['offers']['price']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Render event output
     */
    private function render_event_output($data) {
        ?>
        <div class="kata-event-dynamic">
            <?php if (!empty($data['name'])): ?>
                <h2><?php echo esc_html($data['name']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($data['startDate'])): ?>
                <p><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($data['startDate']))); ?></p>
            <?php endif; ?>
            <?php if (!empty($data['description'])): ?>
                <p><?php echo esc_html($data['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}
