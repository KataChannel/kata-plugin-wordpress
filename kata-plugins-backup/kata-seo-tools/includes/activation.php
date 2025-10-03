<?php
/**
 * Activation Hook Handler
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin activation callback
 */
function kata_seo_tools_activate() {
    global $wpdb;
    
    // Create database tables
    Kata_SEO_Tools::get_instance()->create_database_tables();
    
    // Set default options
    $default_settings = array(
        'enable_schema' => 1,
        'default_schema_type' => 'Article',
        'enable_analytics' => 1,
        'enable_og_tags' => 1,
        'enable_twitter_cards' => 1,
        'share_facebook' => 1,
        'share_twitter' => 1,
        'share_linkedin' => 1,
        'share_pinterest' => 1,
        'share_whatsapp' => 1,
        'quiz_pass_percentage' => 70,
        'wheel_daily_limit' => 3,
        'require_email_wheel' => 1,
        'enable_leaderboard' => 0,
        'admin_email' => get_option('admin_email'),
        'email_from_name' => get_bloginfo('name'),
        'notify_form_submission' => 1,
        'notify_wheel_win' => 0,
        'load_chartjs' => 1,
        'cache_duration' => 3600,
        'delete_data_on_uninstall' => 0
    );
    
    add_option('kata_seo_settings', $default_settings);
    
    // Set plugin version
    add_option('kata_seo_tools_version', KATA_SEO_TOOLS_VERSION);
    
    // Set activation timestamp
    add_option('kata_seo_tools_activated', current_time('timestamp'));
    
    // Create default quiz if doesn't exist
    $quiz_exists = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'page' AND post_content LIKE '%[kata_quiz%'");
    
    if (!$quiz_exists) {
        // Create welcome page with demo content
        $welcome_page = array(
            'post_title'    => 'Kata SEO Tools - Demo Page',
            'post_content'  => kata_seo_tools_get_demo_content(),
            'post_status'   => 'draft',
            'post_author'   => get_current_user_id(),
            'post_type'     => 'page'
        );
        
        wp_insert_post($welcome_page);
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set transient for activation redirect
    set_transient('kata_seo_tools_activation_redirect', true, 30);
    
    // Log activation
    error_log('Kata SEO Tools activated successfully at ' . current_time('mysql'));
}

/**
 * Get demo content for welcome page
 */
function kata_seo_tools_get_demo_content() {
    return <<<HTML
<h2>Welcome to Kata SEO Tools! 🎉</h2>

<p>Thank you for installing Kata SEO Tools. Below are examples of all available features:</p>

<hr>

<h3>1. Social Share Buttons</h3>
[kata_social_share style="circle" platforms="facebook,twitter,linkedin,pinterest"]

<hr>

<h3>2. Quote Generator</h3>
[kata_quote text="Success is not final, failure is not fatal: it is the courage to continue that counts." author="Winston Churchill" style="modern" hashtags="motivation,success,inspiration"]

<hr>

<h3>3. Quiz</h3>
[kata_quiz id="demo-quiz" title="SEO Knowledge Quiz" 
    questions='[
        {"question": "What does SEO stand for?", "options": ["Search Engine Optimization", "Social Engine Optimization", "Site Engine Optimization"], "correct": 0},
        {"question": "Which is most important for SEO?", "options": ["Keywords", "Quality Content", "Backlinks", "All of the above"], "correct": 3}
    ]'
]

<hr>

<h3>4. Poll</h3>
[kata_poll id="favorite-feature" question="What's your favorite Kata SEO Tools feature?" 
    options="Social Share,Quiz,Rating,Lucky Wheel"
]

<hr>

<h3>5. Rating System</h3>
[kata_rating show_form="true"]

<hr>

<h3>6. FAQ</h3>
[kata_faq style="accordion" 
    items='[
        {"question": "How do I install Kata SEO Tools?", "answer": "Simply upload and activate the plugin from WordPress admin."},
        {"question": "Is it free?", "answer": "Yes, Kata SEO Tools is completely free to use."}
    ]'
]

<hr>

<h3>7. Call-to-Action</h3>
[kata_cta title="Ready to boost your SEO?" 
    description="Start using Kata SEO Tools today and see the difference!" 
    button_text="Get Started" 
    button_url="#" 
    style="card"
]

<hr>

<h3>8. Custom Form</h3>
[kata_form id="contact-form" 
    fields='[
        {"type": "text", "name": "name", "label": "Name", "required": true},
        {"type": "email", "name": "email", "label": "Email", "required": true},
        {"type": "textarea", "name": "message", "label": "Message", "required": true}
    ]'
    submit_text="Send Message"
]

<hr>

<h3>9. Lucky Wheel</h3>
[kata_wheel 
    prizes='[
        {"label": "10% Off", "probability": 30},
        {"label": "Free Shipping", "probability": 25},
        {"label": "20% Off", "probability": 20},
        {"label": "Free Gift", "probability": 15},
        {"label": "Try Again", "probability": 10}
    ]'
]

<hr>

<p><strong>Next Steps:</strong></p>
<ul>
    <li>Go to Settings > Kata SEO Tools to configure the plugin</li>
    <li>Add shortcodes to your posts and pages</li>
    <li>Check the Dashboard for analytics</li>
    <li>Read the documentation for more information</li>
</ul>
HTML;
}

/**
 * Activation redirect
 */
function kata_seo_tools_activation_redirect() {
    if (get_transient('kata_seo_tools_activation_redirect')) {
        delete_transient('kata_seo_tools_activation_redirect');
        
        if (!isset($_GET['activate-multi'])) {
            wp_redirect(admin_url('admin.php?page=kata-seo-dashboard&welcome=1'));
            exit;
        }
    }
}
add_action('admin_init', 'kata_seo_tools_activation_redirect');

register_activation_hook(KATA_SEO_TOOLS_FILE, 'kata_seo_tools_activate');
