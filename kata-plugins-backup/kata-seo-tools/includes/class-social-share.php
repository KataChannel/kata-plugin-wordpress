<?php
/**
 * Social Share Class
 * Handle social sharing functionality
 */

class Kata_SEO_Social_Share {
    
    private $platforms = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter (X)',
        'linkedin' => 'LinkedIn',
        'pinterest' => 'Pinterest',
        'telegram' => 'Telegram',
        'whatsapp' => 'WhatsApp',
        'tiktok' => 'TikTok'
    );
    
    /**
     * Generate social meta tags
     */
    public function generate_meta_tags($post_id) {
        $og_title = get_post_meta($post_id, '_kata_seo_og_title', true) ?: get_the_title($post_id);
        $og_description = get_post_meta($post_id, '_kata_seo_og_description', true) ?: get_the_excerpt($post_id);
        $og_image = get_post_meta($post_id, '_kata_seo_og_image', true) ?: get_the_post_thumbnail_url($post_id, 'full');
        $twitter_card = get_post_meta($post_id, '_kata_seo_twitter_card', true) ?: 'summary_large_image';
        
        $output = "\n<!-- Kata SEO Tools - Social Meta Tags -->\n";
        
        // Open Graph
        $output .= '<meta property="og:type" content="article" />' . "\n";
        $output .= '<meta property="og:title" content="' . esc_attr($og_title) . '" />' . "\n";
        $output .= '<meta property="og:description" content="' . esc_attr($og_description) . '" />' . "\n";
        $output .= '<meta property="og:url" content="' . esc_url(get_permalink($post_id)) . '" />' . "\n";
        
        if ($og_image) {
            $output .= '<meta property="og:image" content="' . esc_url($og_image) . '" />' . "\n";
            $output .= '<meta property="og:image:width" content="1200" />' . "\n";
            $output .= '<meta property="og:image:height" content="630" />' . "\n";
        }
        
        $output .= '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '" />' . "\n";
        
        // Twitter Card
        $output .= '<meta name="twitter:card" content="' . esc_attr($twitter_card) . '" />' . "\n";
        $output .= '<meta name="twitter:title" content="' . esc_attr($og_title) . '" />' . "\n";
        $output .= '<meta name="twitter:description" content="' . esc_attr($og_description) . '" />' . "\n";
        
        if ($og_image) {
            $output .= '<meta name="twitter:image" content="' . esc_url($og_image) . '" />' . "\n";
        }
        
        $twitter_site = get_option('kata_seo_twitter_handle');
        if ($twitter_site) {
            $output .= '<meta name="twitter:site" content="' . esc_attr($twitter_site) . '" />' . "\n";
        }
        
        $output .= "<!-- End Kata SEO Tools - Social Meta Tags -->\n\n";
        
        return $output;
    }
    
    /**
     * Render social share buttons
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'platforms' => 'facebook,twitter,linkedin',
            'style' => 'default', // default, circle, square, minimal
            'size' => 'medium', // small, medium, large
            'show_count' => 'no',
            'title' => '',
            'layout' => 'horizontal' // horizontal, vertical
        ), $atts);
        
        $platforms = explode(',', $atts['platforms']);
        $post_id = get_the_ID();
        $url = get_permalink($post_id);
        $title = get_the_title($post_id);
        
        ob_start();
        ?>
        <div class="kata-social-share kata-social-<?php echo esc_attr($atts['style']); ?> kata-social-<?php echo esc_attr($atts['size']); ?> kata-social-<?php echo esc_attr($atts['layout']); ?>">
            <?php if (!empty($atts['title'])) : ?>
                <div class="kata-social-title"><?php echo esc_html($atts['title']); ?></div>
            <?php endif; ?>
            
            <div class="kata-social-buttons">
                <?php foreach ($platforms as $platform) : 
                    $platform = trim($platform);
                    if (!isset($this->platforms[$platform])) continue;
                    
                    $share_url = $this->get_share_url($platform, $url, $title);
                    $share_count = $atts['show_count'] === 'yes' ? $this->get_share_count($post_id, $platform) : 0;
                ?>
                    <a href="<?php echo esc_url($share_url); ?>" 
                       class="kata-social-btn kata-social-<?php echo esc_attr($platform); ?>"
                       data-platform="<?php echo esc_attr($platform); ?>"
                       data-post-id="<?php echo esc_attr($post_id); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       onclick="kataSEO.trackShare('<?php echo esc_js($platform); ?>', <?php echo esc_js($post_id); ?>)">
                        <?php echo $this->get_platform_icon($platform); ?>
                        <span class="kata-social-label"><?php echo esc_html($this->platforms[$platform]); ?></span>
                        <?php if ($share_count > 0) : ?>
                            <span class="kata-social-count"><?php echo esc_html($this->format_count($share_count)); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Get share URL for platform
     */
    private function get_share_url($platform, $url, $title) {
        $urls = array(
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
            'twitter' => 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($title),
            'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($url),
            'pinterest' => 'https://pinterest.com/pin/create/button/?url=' . urlencode($url) . '&description=' . urlencode($title),
            'telegram' => 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($title),
            'whatsapp' => 'https://api.whatsapp.com/send?text=' . urlencode($title . ' ' . $url),
            'tiktok' => 'https://www.tiktok.com/share?url=' . urlencode($url)
        );
        
        return $urls[$platform] ?? '#';
    }
    
    /**
     * Get platform icon SVG
     */
    private function get_platform_icon($platform) {
        $icons = array(
            'facebook' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            'twitter' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
            'linkedin' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
            'pinterest' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z"/></svg>',
            'telegram' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
            'whatsapp' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
            'tiktok' => '<svg viewBox="0 0 24 24" width="20" height="20"><path fill="currentColor" d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>'
        );
        
        return $icons[$platform] ?? '';
    }
    
    /**
     * Get share count
     */
    private function get_share_count($post_id, $platform) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_social_shares';
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT share_count FROM $table WHERE post_id = %d AND platform = %s",
            $post_id, $platform
        ));
        
        return $count ? intval($count) : 0;
    }
    
    /**
     * Track share
     */
    public function track_share($data) {
        global $wpdb;
        
        $post_id = intval($data['post_id']);
        $platform = sanitize_text_field($data['platform']);
        
        $table = $wpdb->prefix . 'kata_seo_social_shares';
        
        // Check if record exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table WHERE post_id = %d AND platform = %s",
            $post_id, $platform
        ));
        
        if ($existing) {
            // Increment count
            $wpdb->query($wpdb->prepare(
                "UPDATE $table SET share_count = share_count + 1, last_shared = NOW() WHERE id = %d",
                $existing
            ));
        } else {
            // Insert new record
            $wpdb->insert($table, array(
                'post_id' => $post_id,
                'platform' => $platform,
                'share_count' => 1
            ));
        }
        
        return array('success' => true);
    }
    
    /**
     * Format share count
     */
    private function format_count($count) {
        if ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        } elseif ($count >= 1000) {
            return round($count / 1000, 1) . 'K';
        }
        return $count;
    }
}
