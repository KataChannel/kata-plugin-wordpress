<?php
/**
 * Admin Settings Template
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get current settings
$settings = get_option('kata_seo_settings', array());
?>

<div class="wrap kata-seo-settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="options.php">
        <?php settings_fields('kata_seo_settings_group'); ?>
        
        <div class="kata-settings-tabs">
            <nav class="kata-tabs-nav">
                <a href="#general" class="kata-tab-link active">General</a>
                <a href="#social" class="kata-tab-link">Social Media</a>
                <a href="#gamification" class="kata-tab-link">Gamification</a>
                <a href="#email" class="kata-tab-link">Email</a>
                <a href="#advanced" class="kata-tab-link">Advanced</a>
            </nav>

            <!-- General Settings -->
            <div id="general" class="kata-tab-content active">
                <h2>General Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_schema">Enable Schema Markup</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[enable_schema]" id="enable_schema" value="1" <?php checked(isset($settings['enable_schema']) ? $settings['enable_schema'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Automatically add Schema.org markup to posts</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="default_schema_type">Default Schema Type</label>
                        </th>
                        <td>
                            <select name="kata_seo_settings[default_schema_type]" id="default_schema_type">
                                <option value="Article" <?php selected(isset($settings['default_schema_type']) ? $settings['default_schema_type'] : 'Article', 'Article'); ?>>Article</option>
                                <option value="BlogPosting" <?php selected(isset($settings['default_schema_type']) ? $settings['default_schema_type'] : 'Article', 'BlogPosting'); ?>>Blog Posting</option>
                                <option value="NewsArticle" <?php selected(isset($settings['default_schema_type']) ? $settings['default_schema_type'] : 'Article', 'NewsArticle'); ?>>News Article</option>
                                <option value="Tutorial" <?php selected(isset($settings['default_schema_type']) ? $settings['default_schema_type'] : 'Article', 'Tutorial'); ?>>Tutorial</option>
                            </select>
                            <p class="description">Default schema type for new posts</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="enable_analytics">Enable Analytics</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[enable_analytics]" id="enable_analytics" value="1" <?php checked(isset($settings['enable_analytics']) ? $settings['enable_analytics'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Track user engagement and interactions</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Social Media Settings -->
            <div id="social" class="kata-tab-content">
                <h2>Social Media Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_og_tags">Enable Open Graph Tags</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[enable_og_tags]" id="enable_og_tags" value="1" <?php checked(isset($settings['enable_og_tags']) ? $settings['enable_og_tags'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Add Open Graph meta tags for Facebook sharing</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="enable_twitter_cards">Enable Twitter Cards</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[enable_twitter_cards]" id="enable_twitter_cards" value="1" <?php checked(isset($settings['enable_twitter_cards']) ? $settings['enable_twitter_cards'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Add Twitter Card meta tags</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="twitter_handle">Twitter Handle</label>
                        </th>
                        <td>
                            <input type="text" name="kata_seo_settings[twitter_handle]" id="twitter_handle" value="<?php echo esc_attr(isset($settings['twitter_handle']) ? $settings['twitter_handle'] : ''); ?>" class="regular-text" placeholder="@yourhandle">
                            <p class="description">Your Twitter username (with @)</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="default_share_buttons">Default Share Buttons</label>
                        </th>
                        <td>
                            <fieldset>
                                <label><input type="checkbox" name="kata_seo_settings[share_facebook]" value="1" <?php checked(isset($settings['share_facebook']) ? $settings['share_facebook'] : 1, 1); ?>> Facebook</label><br>
                                <label><input type="checkbox" name="kata_seo_settings[share_twitter]" value="1" <?php checked(isset($settings['share_twitter']) ? $settings['share_twitter'] : 1, 1); ?>> Twitter</label><br>
                                <label><input type="checkbox" name="kata_seo_settings[share_linkedin]" value="1" <?php checked(isset($settings['share_linkedin']) ? $settings['share_linkedin'] : 1, 1); ?>> LinkedIn</label><br>
                                <label><input type="checkbox" name="kata_seo_settings[share_pinterest]" value="1" <?php checked(isset($settings['share_pinterest']) ? $settings['share_pinterest'] : 1, 1); ?>> Pinterest</label><br>
                                <label><input type="checkbox" name="kata_seo_settings[share_whatsapp]" value="1" <?php checked(isset($settings['share_whatsapp']) ? $settings['share_whatsapp'] : 1, 1); ?>> WhatsApp</label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Gamification Settings -->
            <div id="gamification" class="kata-tab-content">
                <h2>Gamification Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="quiz_pass_percentage">Quiz Pass Percentage</label>
                        </th>
                        <td>
                            <input type="number" name="kata_seo_settings[quiz_pass_percentage]" id="quiz_pass_percentage" value="<?php echo esc_attr(isset($settings['quiz_pass_percentage']) ? $settings['quiz_pass_percentage'] : 70); ?>" min="0" max="100" step="5">
                            <span>%</span>
                            <p class="description">Minimum score to pass a quiz</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="wheel_daily_limit">Wheel Daily Spin Limit</label>
                        </th>
                        <td>
                            <input type="number" name="kata_seo_settings[wheel_daily_limit]" id="wheel_daily_limit" value="<?php echo esc_attr(isset($settings['wheel_daily_limit']) ? $settings['wheel_daily_limit'] : 3); ?>" min="1" max="10">
                            <p class="description">Maximum spins per user per day</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="require_email_wheel">Require Email for Wheel</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[require_email_wheel]" id="require_email_wheel" value="1" <?php checked(isset($settings['require_email_wheel']) ? $settings['require_email_wheel'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Require email before spinning the wheel</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="enable_leaderboard">Enable Quiz Leaderboard</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[enable_leaderboard]" id="enable_leaderboard" value="1" <?php checked(isset($settings['enable_leaderboard']) ? $settings['enable_leaderboard'] : 0, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Show top quiz scores publicly</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Email Settings -->
            <div id="email" class="kata-tab-content">
                <h2>Email Notification Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="admin_email">Admin Email</label>
                        </th>
                        <td>
                            <input type="email" name="kata_seo_settings[admin_email]" id="admin_email" value="<?php echo esc_attr(isset($settings['admin_email']) ? $settings['admin_email'] : get_option('admin_email')); ?>" class="regular-text">
                            <p class="description">Email for receiving form submissions</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="email_from_name">From Name</label>
                        </th>
                        <td>
                            <input type="text" name="kata_seo_settings[email_from_name]" id="email_from_name" value="<?php echo esc_attr(isset($settings['email_from_name']) ? $settings['email_from_name'] : get_bloginfo('name')); ?>" class="regular-text">
                            <p class="description">Name shown in notification emails</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="notify_form_submission">Notify on Form Submission</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[notify_form_submission]" id="notify_form_submission" value="1" <?php checked(isset($settings['notify_form_submission']) ? $settings['notify_form_submission'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Send email when a form is submitted</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="notify_wheel_win">Notify on Wheel Prize</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[notify_wheel_win]" id="notify_wheel_win" value="1" <?php checked(isset($settings['notify_wheel_win']) ? $settings['notify_wheel_win'] : 0, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Send email to user when they win a prize</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Advanced Settings -->
            <div id="advanced" class="kata-tab-content">
                <h2>Advanced Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="load_chartjs">Load Chart.js</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[load_chartjs]" id="load_chartjs" value="1" <?php checked(isset($settings['load_chartjs']) ? $settings['load_chartjs'] : 1, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">Load Chart.js library for analytics</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="cache_duration">Cache Duration</label>
                        </th>
                        <td>
                            <input type="number" name="kata_seo_settings[cache_duration]" id="cache_duration" value="<?php echo esc_attr(isset($settings['cache_duration']) ? $settings['cache_duration'] : 3600); ?>" min="0">
                            <span>seconds</span>
                            <p class="description">How long to cache analytics data (0 = disabled)</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="delete_data_on_uninstall">Delete Data on Uninstall</label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="kata_seo_settings[delete_data_on_uninstall]" id="delete_data_on_uninstall" value="1" <?php checked(isset($settings['delete_data_on_uninstall']) ? $settings['delete_data_on_uninstall'] : 0, 1); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description"><strong>Warning:</strong> This will delete all plugin data when uninstalling</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="custom_css">Custom CSS</label>
                        </th>
                        <td>
                            <textarea name="kata_seo_settings[custom_css]" id="custom_css" rows="10" class="large-text code"><?php echo esc_textarea(isset($settings['custom_css']) ? $settings['custom_css'] : ''); ?></textarea>
                            <p class="description">Add custom CSS to override default styles</p>
                        </td>
                    </tr>
                </table>

                <div class="kata-danger-zone">
                    <h3>🚨 Danger Zone</h3>
                    <p>These actions cannot be undone!</p>
                    
                    <button type="button" class="button" id="reset-settings">Reset All Settings</button>
                    <button type="button" class="button button-danger" id="clear-all-data">Clear All Data</button>
                </div>
            </div>
        </div>

        <?php submit_button('Save Settings', 'primary', 'submit', true); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.kata-tab-link').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        
        $('.kata-tab-link').removeClass('active');
        $(this).addClass('active');
        
        $('.kata-tab-content').removeClass('active');
        $(target).addClass('active');
    });

    // Reset settings
    $('#reset-settings').on('click', function() {
        if (confirm('Are you sure you want to reset all settings to default?')) {
            // AJAX call to reset settings
            $.post(ajaxurl, {
                action: 'kata_seo_reset_settings',
                nonce: '<?php echo wp_create_nonce('kata_seo_reset_settings'); ?>'
            }, function(response) {
                if (response.success) {
                    location.reload();
                }
            });
        }
    });

    // Clear all data
    $('#clear-all-data').on('click', function() {
        if (confirm('⚠️ WARNING: This will delete ALL plugin data including quiz results, ratings, forms, etc. This cannot be undone!\n\nAre you absolutely sure?')) {
            if (confirm('Last confirmation: Delete ALL data?')) {
                $.post(ajaxurl, {
                    action: 'kata_seo_clear_data',
                    nonce: '<?php echo wp_create_nonce('kata_seo_clear_data'); ?>'
                }, function(response) {
                    if (response.success) {
                        alert('All data has been cleared.');
                        location.reload();
                    }
                });
            }
        }
    });
});
</script>
