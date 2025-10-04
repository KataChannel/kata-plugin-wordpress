<?php
/**
 * Schema Types Page
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$schema_types = KATA_SEO_Manager::get_instance()->get_schema_types();
$statistics = new KATA_SEO_Statistics();
?>

<div class="wrap kata-seo-schema-types">
    <h1><?php _e('Schema Types', 'kata-seo-manager'); ?></h1>
    
    <div class="kata-schema-types-grid">
        <?php foreach ($schema_types as $type => $label): ?>
            <?php
            $stats = $statistics->get_type_stats($type);
            $icon = 'dashicons-code-standards';
            
            // Custom icons for specific types
            $icons = array(
                'Article' => 'dashicons-media-document',
                'FAQ' => 'dashicons-format-chat',
                'Product' => 'dashicons-cart',
                'Recipe' => 'dashicons-food',
                'Event' => 'dashicons-calendar',
                'Video' => 'dashicons-video-alt3',
                'HowTo' => 'dashicons-list-view',
                'LocalBusiness' => 'dashicons-store',
                'Breadcrumb' => 'dashicons-arrow-right-alt2',
                'Organization' => 'dashicons-building',
                'Review' => 'dashicons-star-filled'
            );
            
            if (isset($icons[$type])) {
                $icon = $icons[$type];
            }
            ?>
            
            <div class="kata-schema-type-card">
                <div class="card-header">
                    <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
                    <h3><?php echo esc_html($label); ?></h3>
                </div>
                
                <div class="card-stats">
                    <div class="stat">
                        <span class="stat-value"><?php echo esc_html($stats['total']); ?></span>
                        <span class="stat-label"><?php _e('Total', 'kata-seo-manager'); ?></span>
                    </div>
                    <div class="stat">
                        <span class="stat-value"><?php echo esc_html($stats['active']); ?></span>
                        <span class="stat-label"><?php _e('Active', 'kata-seo-manager'); ?></span>
                    </div>
                </div>
                
                <?php if ($stats['total'] > 0 && !empty($stats['performance'])): ?>
                    <div class="card-performance">
                        <small>
                            <?php printf(
                                __('Impressions: %s | Clicks: %s', 'kata-seo-manager'),
                                number_format($stats['performance']['impressions']),
                                number_format($stats['performance']['clicks'])
                            ); ?>
                        </small>
                    </div>
                <?php endif; ?>
                
                <div class="card-actions">
                    <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="button button-small">
                        <?php _e('View Posts', 'kata-seo-manager'); ?>
                    </a>
                    <button type="button" class="button button-small kata-add-schema-btn" data-type="<?php echo esc_attr($type); ?>">
                        <?php _e('Add New', 'kata-seo-manager'); ?>
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.kata-seo-schema-types {
    margin: 20px 20px 0 0;
}
.kata-schema-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.kata-schema-type-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    transition: box-shadow 0.3s;
}
.kata-schema-type-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}
.card-header .dashicons {
    font-size: 30px;
    color: #2271b1;
}
.card-header h3 {
    margin: 0;
    font-size: 16px;
}
.card-stats {
    display: flex;
    gap: 20px;
    padding: 15px 0;
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
}
.stat {
    display: flex;
    flex-direction: column;
}
.stat-value {
    font-size: 24px;
    font-weight: bold;
    color: #2271b1;
}
.stat-label {
    font-size: 12px;
    color: #646970;
}
.card-performance {
    padding: 10px 0;
    color: #646970;
}
.card-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}
.card-actions .button {
    flex: 1;
}
</style>
