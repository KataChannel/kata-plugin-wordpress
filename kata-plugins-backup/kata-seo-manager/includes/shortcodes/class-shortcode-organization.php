<?php
/**
 * Organization Shortcode
 * 
 * [kata_organization]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_Organization extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_organization';
    protected $schema_type = 'organization';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'logo' => '',
            'description' => get_bloginfo('description'),
            'email' => '',
            'phone' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'country' => '',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_instagram' => '',
            'social_linkedin' => '',
            'show_output' => 'false'
        ));
        
        // Build schema data
        $data = array(
            'name' => $atts['name'],
            'url' => $atts['url'],
            'description' => $atts['description']
        );
        
        // Add logo if available
        if (!empty($atts['logo'])) {
            $data['logo'] = $atts['logo'];
        }
        
        // Add contact info
        if (!empty($atts['email']) || !empty($atts['phone'])) {
            $data['contactPoint'] = array(
                '@type' => 'ContactPoint',
                'contactType' => 'customer service'
            );
            
            if (!empty($atts['email'])) {
                $data['contactPoint']['email'] = $atts['email'];
            }
            
            if (!empty($atts['phone'])) {
                $data['contactPoint']['telephone'] = $atts['phone'];
            }
        }
        
        // Add address
        if (!empty($atts['address'])) {
            $data['address'] = array(
                '@type' => 'PostalAddress',
                'streetAddress' => $atts['address']
            );
            
            if (!empty($atts['city'])) {
                $data['address']['addressLocality'] = $atts['city'];
            }
            
            if (!empty($atts['state'])) {
                $data['address']['addressRegion'] = $atts['state'];
            }
            
            if (!empty($atts['postal_code'])) {
                $data['address']['postalCode'] = $atts['postal_code'];
            }
            
            if (!empty($atts['country'])) {
                $data['address']['addressCountry'] = $atts['country'];
            }
        }
        
        // Add social media
        $social_profiles = array();
        if (!empty($atts['social_facebook'])) {
            $social_profiles[] = $atts['social_facebook'];
        }
        if (!empty($atts['social_twitter'])) {
            $social_profiles[] = $atts['social_twitter'];
        }
        if (!empty($atts['social_instagram'])) {
            $social_profiles[] = $atts['social_instagram'];
        }
        if (!empty($atts['social_linkedin'])) {
            $social_profiles[] = $atts['social_linkedin'];
        }
        
        if (!empty($social_profiles)) {
            $data['sameAs'] = $social_profiles;
        }
        
        // Generate and store schema
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        // Return HTML output if requested
        if ($atts['show_output'] === 'true') {
            ob_start();
            ?>
            <div class="kata-organization" itemscope itemtype="https://schema.org/Organization">
                <?php if (!empty($atts['logo'])): ?>
                    <img itemprop="logo" src="<?php echo esc_url($atts['logo']); ?>" alt="<?php echo esc_attr($atts['name']); ?>">
                <?php endif; ?>
                <h2 itemprop="name"><?php echo esc_html($atts['name']); ?></h2>
                <p itemprop="description"><?php echo esc_html($atts['description']); ?></p>
                
                <?php if (!empty($atts['phone']) || !empty($atts['email'])): ?>
                    <div class="kata-organization-contact">
                        <?php if (!empty($atts['phone'])): ?>
                            <p><strong><?php _e('Phone:', 'kata-seo-manager'); ?></strong> <span itemprop="telephone"><?php echo esc_html($atts['phone']); ?></span></p>
                        <?php endif; ?>
                        <?php if (!empty($atts['email'])): ?>
                            <p><strong><?php _e('Email:', 'kata-seo-manager'); ?></strong> <span itemprop="email"><?php echo esc_html($atts['email']); ?></span></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($atts['address'])): ?>
                    <div class="kata-organization-address" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                        <p>
                            <span itemprop="streetAddress"><?php echo esc_html($atts['address']); ?></span><br>
                            <span itemprop="addressLocality"><?php echo esc_html($atts['city']); ?></span>,
                            <span itemprop="addressRegion"><?php echo esc_html($atts['state']); ?></span>
                            <span itemprop="postalCode"><?php echo esc_html($atts['postal_code']); ?></span><br>
                            <span itemprop="addressCountry"><?php echo esc_html($atts['country']); ?></span>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
