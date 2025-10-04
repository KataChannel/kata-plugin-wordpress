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
$all_stats = $statistics->get_overview();
?>

<div class="kata-schema-types-wrapper">
    <!-- Page Header -->
    <div class="kata-page-header">
        <div class="kata-page-title">
            <div class="kata-page-title-icon">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319z"/>
                </svg>
            </div>
            <h1><?php _e('Schema Types Management', 'kata-seo-manager'); ?></h1>
        </div>
        <p class="kata-page-subtitle"><?php _e('Manage and configure structured data schema types for your content', 'kata-seo-manager'); ?></p>
        
        <div class="kata-page-actions">
            <button class="kata-btn kata-btn-primary" onclick="kataCreateNewSchema()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                <?php _e('Create Schema', 'kata-seo-manager'); ?>
            </button>
            <button class="kata-btn kata-btn-outline" onclick="kataImportSchemas()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                </svg>
                <?php _e('Import', 'kata-seo-manager'); ?>
            </button>
            <button class="kata-btn kata-btn-outline" onclick="kataExportSchemas()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                <?php _e('Export', 'kata-seo-manager'); ?>
            </button>
        </div>
    </div>

    <!-- Schema Type Filters -->
    <div class="kata-schema-filters">
        <div class="kata-filter-tabs">
            <button class="kata-filter-tab active" data-filter="all"><?php _e('All Types', 'kata-seo-manager'); ?> (<?php echo count($schema_types); ?>)</button>
            <button class="kata-filter-tab" data-filter="popular"><?php _e('Popular', 'kata-seo-manager'); ?> (8)</button>
            <button class="kata-filter-tab" data-filter="business"><?php _e('Business', 'kata-seo-manager'); ?> (5)</button>
            <button class="kata-filter-tab" data-filter="content"><?php _e('Content', 'kata-seo-manager'); ?> (7)</button>
            <button class="kata-filter-tab" data-filter="social"><?php _e('Social', 'kata-seo-manager'); ?> (3)</button>
        </div>
        
        <div class="kata-search-box">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="m13.498 12.05-4.095-4.093a5.5 5.5 0 1 0-.707.707l4.094 4.093a.5.5 0 0 0 .708-.707zM6.5 12A5.5 5.5 0 1 1 12 6.5 5.506 5.506 0 0 1 6.5 12z"/>
            </svg>
            <input type="text" placeholder="<?php _e('Search schema types...', 'kata-seo-manager'); ?>" id="kataSchemaSearch">
        </div>
    </div>

    <!-- Schema Types Grid -->
    <div class="kata-schema-types-grid" id="kataSchemaGrid">
        <?php 
        $type_categories = array(
            'Article' => array('category' => 'content', 'popular' => true),
            'FAQ' => array('category' => 'content', 'popular' => true),
            'Product' => array('category' => 'business', 'popular' => true),
            'Recipe' => array('category' => 'content', 'popular' => false),
            'Event' => array('category' => 'business', 'popular' => true),
            'Video' => array('category' => 'content', 'popular' => true),
            'HowTo' => array('category' => 'content', 'popular' => false),
            'LocalBusiness' => array('category' => 'business', 'popular' => true),
            'Breadcrumb' => array('category' => 'content', 'popular' => false),
            'Organization' => array('category' => 'business', 'popular' => true),
            'Review' => array('category' => 'social', 'popular' => true)
        );
        
        foreach ($schema_types as $type => $label): 
            $stats = $statistics->get_type_stats($type);
            $category = isset($type_categories[$type]) ? $type_categories[$type]['category'] : 'content';
            $is_popular = isset($type_categories[$type]) ? $type_categories[$type]['popular'] : false;
            
            // SVG icons for different schema types
            $svg_icons = array(
                'Article' => '<path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/><path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>',
                'FAQ' => '<path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>',
                'Product' => '<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>',
                'Recipe' => '<path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>',
                'Event' => '<path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>',
                'Video' => '<path d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/>',
                'HowTo' => '<path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z"/><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z"/>',
                'LocalBusiness' => '<path d="M7 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-.5.5h-5a.5.5 0 0 1-.5-.5v-5zM8 3v4h4V3H8z"/><path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm0 1h13a.5.5 0 0 1 .5.5V13H1V3.5a.5.5 0 0 1 .5-.5z"/>',
                'Breadcrumb' => '<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>',
                'Organization' => '<path d="M2.5 14V1.5a.5.5 0 0 1 .5-.5h1V0a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5v1h1a.5.5 0 0 1 .5.5V14a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm2-13v12h5V1H4.5zM6 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/>',
                'Review' => '<path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>'
            );
            
            $icon_svg = isset($svg_icons[$type]) ? $svg_icons[$type] : $svg_icons['Article'];
            ?>
            
            <div class="kata-schema-type-card" data-category="<?php echo esc_attr($category); ?>" data-popular="<?php echo $is_popular ? 'true' : 'false'; ?>" data-name="<?php echo esc_attr(strtolower($label)); ?>">
                <div class="kata-card-header">
                    <div class="kata-schema-icon">
                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                            <?php echo $icon_svg; ?>
                        </svg>
                    </div>
                    <div class="kata-schema-meta">
                        <h3 class="kata-schema-title"><?php echo esc_html($label); ?></h3>
                        <div class="kata-schema-badges">
                            <?php if ($is_popular): ?>
                                <span class="kata-badge kata-badge-popular"><?php _e('Popular', 'kata-seo-manager'); ?></span>
                            <?php endif; ?>
                            <span class="kata-badge kata-badge-category"><?php echo esc_html(ucfirst($category)); ?></span>
                        </div>
                    </div>
                    <div class="kata-card-actions">
                        <button class="kata-action-btn" title="<?php _e('Quick add', 'kata-seo-manager'); ?>" onclick="kataQuickAddSchema('<?php echo esc_js($type); ?>')">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="kata-card-content">
                    <div class="kata-stats-row">
                        <div class="kata-stat-item">
                            <div class="kata-stat-value" data-count="<?php echo esc_attr($stats['total']); ?>"><?php echo esc_html($stats['total']); ?></div>
                            <div class="kata-stat-label"><?php _e('Total Usage', 'kata-seo-manager'); ?></div>
                        </div>
                        <div class="kata-stat-item">
                            <div class="kata-stat-value" data-count="<?php echo esc_attr($stats['active']); ?>"><?php echo esc_html($stats['active']); ?></div>
                            <div class="kata-stat-label"><?php _e('Active', 'kata-seo-manager'); ?></div>
                        </div>
                        <div class="kata-stat-item">
                            <div class="kata-stat-value">
                                <?php 
                                $effectiveness = $stats['total'] > 0 ? min(100, ($stats['active'] / $stats['total']) * 100) : 0;
                                echo number_format($effectiveness, 0) . '%';
                                ?>
                            </div>
                            <div class="kata-stat-label"><?php _e('Effective', 'kata-seo-manager'); ?></div>
                        </div>
                    </div>
                    
                    <?php if ($stats['total'] > 0): ?>
                        <div class="kata-usage-chart">
                            <div class="kata-chart-bar">
                                <div class="kata-chart-fill" style="width: <?php echo min(100, ($stats['total'] / 50) * 100); ?>%"></div>
                            </div>
                            <div class="kata-chart-label"><?php _e('Usage trend', 'kata-seo-manager'); ?></div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="kata-card-footer">
                        <button class="kata-btn kata-btn-outline kata-btn-sm" onclick="kataViewSchemaDetails('<?php echo esc_js($type); ?>')">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.532.532 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                            </svg>
                            <?php _e('View Details', 'kata-seo-manager'); ?>
                        </button>
                        <button class="kata-btn kata-btn-primary kata-btn-sm" onclick="kataConfigureSchema('<?php echo esc_js($type); ?>')">
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
                            </svg>
                            <?php _e('Configure', 'kata-seo-manager'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Empty State for Search -->
    <div class="kata-empty-search" id="kataEmptySearch" style="display: none;">
        <div class="kata-empty-state">
            <svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <h3><?php _e('No schema types found', 'kata-seo-manager'); ?></h3>
            <p><?php _e('Try adjusting your search terms or filters.', 'kata-seo-manager'); ?></p>
            <button class="kata-btn kata-btn-primary" onclick="kataClearSearch()"><?php _e('Clear Search', 'kata-seo-manager'); ?></button>
        </div>
    </div>
</div>

<style>
/* Root Variables */
:root {
    --kata-primary: #667eea;
    --kata-primary-dark: #5a67d8;
    --kata-secondary: #764ba2;
    --kata-success: #10b981;
    --kata-warning: #f59e0b;
    --kata-error: #ef4444;
    --kata-surface: #ffffff;
    --kata-surface-light: #f8fafc;
    --kata-border: #e2e8f0;
    --kata-text: #1e293b;
    --kata-text-muted: #64748b;
    --kata-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --kata-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --kata-radius: 8px;
    --kata-radius-lg: 12px;
    --kata-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Layout */
.kata-schema-types-wrapper {
    margin: 20px 20px 20px 0;
}

/* Page Header */
.kata-page-header {
    background: var(--kata-surface);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius-lg);
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: var(--kata-shadow);
}

.kata-page-title {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 0 0 10px 0;
}

.kata-page-title h1 {
    font-size: 32px;
    font-weight: 700;
    color: var(--kata-text);
    margin: 0;
    line-height: 1.2;
}

.kata-page-title-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
    border-radius: var(--kata-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.kata-page-subtitle {
    color: var(--kata-text-muted);
    font-size: 16px;
    margin: 0 0 20px 0;
}

.kata-page-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* Buttons */
.kata-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    border-radius: var(--kata-radius);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--kata-transition);
    white-space: nowrap;
}

.kata-btn-primary {
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
    color: white;
}

.kata-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: var(--kata-shadow-lg);
}

.kata-btn-outline {
    background: transparent;
    color: var(--kata-primary);
    border: 2px solid var(--kata-primary);
}

.kata-btn-outline:hover {
    background: var(--kata-primary);
    color: white;
}

.kata-btn-sm {
    padding: 8px 16px;
    font-size: 13px;
    gap: 6px;
}

/* Schema Filters */
.kata-schema-filters {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.kata-filter-tabs {
    display: flex;
    gap: 5px;
    background: var(--kata-surface-light);
    padding: 4px;
    border-radius: var(--kata-radius);
    border: 1px solid var(--kata-border);
}

.kata-filter-tab {
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: var(--kata-text-muted);
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--kata-transition);
    white-space: nowrap;
}

.kata-filter-tab.active,
.kata-filter-tab:hover {
    background: var(--kata-surface);
    color: var(--kata-primary);
    box-shadow: var(--kata-shadow);
}

.kata-search-box {
    position: relative;
    width: 300px;
    max-width: 100%;
}

.kata-search-box svg {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--kata-text-muted);
}

.kata-search-box input {
    width: 100%;
    padding: 12px 12px 12px 40px;
    border: 2px solid var(--kata-border);
    border-radius: var(--kata-radius);
    font-size: 14px;
    background: var(--kata-surface);
    transition: var(--kata-transition);
}

.kata-search-box input:focus {
    outline: none;
    border-color: var(--kata-primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Schema Types Grid */
.kata-schema-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 24px;
}

/* Schema Type Cards */
.kata-schema-type-card {
    background: var(--kata-surface);
    border: 1px solid var(--kata-border);
    border-radius: var(--kata-radius-lg);
    overflow: hidden;
    transition: var(--kata-transition);
    box-shadow: var(--kata-shadow);
}

.kata-schema-type-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--kata-shadow-lg);
    border-color: var(--kata-primary);
}

.kata-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--kata-border);
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.kata-schema-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--kata-primary), var(--kata-secondary));
    border-radius: var(--kata-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.kata-schema-meta {
    flex: 1;
    min-width: 0;
}

.kata-schema-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--kata-text);
    margin: 0 0 8px 0;
}

.kata-schema-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.kata-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kata-badge-popular {
    background: rgba(239, 68, 68, 0.1);
    color: var(--kata-error);
}

.kata-badge-category {
    background: rgba(102, 126, 234, 0.1);
    color: var(--kata-primary);
}

.kata-card-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.kata-action-btn {
    width: 36px;
    height: 36px;
    border: 2px solid var(--kata-border);
    background: var(--kata-surface);
    border-radius: var(--kata-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--kata-transition);
    color: var(--kata-text-muted);
}

.kata-action-btn:hover {
    border-color: var(--kata-primary);
    color: var(--kata-primary);
    background: rgba(102, 126, 234, 0.1);
}

.kata-card-content {
    padding: 24px;
}

.kata-stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.kata-stat-item {
    text-align: center;
    padding: 16px 12px;
    background: var(--kata-surface-light);
    border-radius: var(--kata-radius);
    border: 1px solid var(--kata-border);
}

.kata-stat-value {
    display: block;
    font-size: 24px;
    font-weight: 700;
    color: var(--kata-primary);
    margin-bottom: 4px;
}

.kata-stat-label {
    font-size: 12px;
    color: var(--kata-text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kata-usage-chart {
    margin-bottom: 20px;
}

.kata-chart-bar {
    width: 100%;
    height: 6px;
    background: var(--kata-border);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 8px;
}

.kata-chart-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--kata-primary), var(--kata-secondary));
    border-radius: 3px;
    transition: width 1s ease-out;
}

.kata-chart-label {
    font-size: 12px;
    color: var(--kata-text-muted);
    text-align: center;
}

.kata-card-footer {
    display: flex;
    gap: 10px;
}

.kata-card-footer .kata-btn {
    flex: 1;
}

/* Empty State */
.kata-empty-search {
    grid-column: 1 / -1;
}

.kata-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--kata-text-muted);
}

.kata-empty-state svg {
    margin-bottom: 20px;
    opacity: 0.5;
}

.kata-empty-state h3 {
    font-size: 24px;
    font-weight: 600;
    color: var(--kata-text);
    margin-bottom: 10px;
}

.kata-empty-state p {
    margin-bottom: 30px;
    font-size: 16px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .kata-schema-types-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }
}

@media (max-width: 768px) {
    .kata-schema-types-wrapper {
        margin: 10px;
    }
    
    .kata-schema-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .kata-filter-tabs {
        order: 2;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .kata-search-box {
        order: 1;
        width: 100%;
    }
    
    .kata-schema-types-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-stats-row {
        grid-template-columns: 1fr;
    }
    
    .kata-card-header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .kata-card-actions {
        flex-direction: row;
        align-self: stretch;
    }
    
    .kata-page-actions {
        width: 100%;
    }
    
    .kata-btn {
        flex: 1;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .kata-card-footer {
        flex-direction: column;
    }
    
    .kata-filter-tabs {
        flex-direction: column;
    }
    
    .kata-card-header {
        padding: 16px;
    }
    
    .kata-card-content {
        padding: 16px;
    }
}

/* Animation for counters */
@keyframes countUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kata-stat-value {
    animation: countUp 0.6s ease-out;
}
</style>

<script>
// Initialize schema types page
document.addEventListener('DOMContentLoaded', function() {
    initializeSchemaPage();
});

function initializeSchemaPage() {
    // Filter functionality
    const filterTabs = document.querySelectorAll('.kata-filter-tab');
    const schemaCards = document.querySelectorAll('.kata-schema-type-card');
    const searchInput = document.getElementById('kataSchemaSearch');
    const emptyState = document.getElementById('kataEmptySearch');
    
    // Filter by category
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            filterSchemas(filter, searchInput.value);
        });
    });
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const activeFilter = document.querySelector('.kata-filter-tab.active').dataset.filter;
            filterSchemas(activeFilter, this.value);
        });
    }
    
    function filterSchemas(categoryFilter, searchTerm) {
        let visibleCount = 0;
        
        schemaCards.forEach(card => {
            const category = card.dataset.category;
            const isPopular = card.dataset.popular === 'true';
            const name = card.dataset.name;
            
            let showCard = true;
            
            // Category filter
            if (categoryFilter !== 'all') {
                if (categoryFilter === 'popular' && !isPopular) {
                    showCard = false;
                } else if (categoryFilter !== 'popular' && category !== categoryFilter) {
                    showCard = false;
                }
            }
            
            // Search filter
            if (searchTerm && !name.includes(searchTerm.toLowerCase())) {
                showCard = false;
            }
            
            if (showCard) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide empty state
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
    
    // Animate stat counters
    animateCounters();
}

function animateCounters() {
    const counters = document.querySelectorAll('.kata-stat-value[data-count]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.count);
        const duration = 1500;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, 16);
    });
}

// Schema management functions
function kataCreateNewSchema() {
    alert('<?php _e('Schema creation wizard will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

function kataImportSchemas() {
    alert('<?php _e('Schema import functionality will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

function kataExportSchemas() {
    alert('<?php _e('Schema export functionality will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

function kataQuickAddSchema(type) {
    alert('<?php _e('Quick add feature for', 'kata-seo-manager'); ?> ' + type + ' <?php _e('will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

function kataViewSchemaDetails(type) {
    alert('<?php _e('Schema details for', 'kata-seo-manager'); ?> ' + type + ' <?php _e('will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

function kataConfigureSchema(type) {
    alert('<?php _e('Schema configuration for', 'kata-seo-manager'); ?> ' + type + ' <?php _e('will be implemented in future updates.', 'kata-seo-manager'); ?>');
}

function kataClearSearch() {
    const searchInput = document.getElementById('kataSchemaSearch');
    if (searchInput) {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
    }
}
</script>
