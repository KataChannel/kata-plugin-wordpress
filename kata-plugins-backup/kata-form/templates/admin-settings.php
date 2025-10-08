<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['submit'])) {
    settings_errors();
}
?>

<div class="wrap">
    <h1><?php _e('Cài Đặt Kata Form', 'kata-form'); ?></h1>
    
    <?php settings_errors(); ?>
    
    <form method="post" action="">
        <?php wp_nonce_field('kata_form_settings', 'kata_form_settings_nonce'); ?>
        
        <table class="form-table">
            <tbody>
                <!-- Data Collection Settings -->
                <tr>
                    <th scope="row" colspan="2">
                        <h2><?php _e('Thu Thập Dữ Liệu', 'kata-form'); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Lưu Địa Chỉ IP', 'kata-form'); ?></th>
                    <td>
                        <label for="save_ip_address">
                            <input type="checkbox" id="save_ip_address" name="save_ip_address" value="1" 
                                   <?php checked(get_option('kata_form_save_ip_address'), 1); ?>>
                            <?php _e('Lưu trữ địa chỉ IP của người gửi form', 'kata-form'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Bật tùy chọn này để theo dõi địa chỉ IP của người gửi form cho mục đích bảo mật và phân tích.', 'kata-form'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Lưu User Agent', 'kata-form'); ?></th>
                    <td>
                        <label for="save_user_agent">
                            <input type="checkbox" id="save_user_agent" name="save_user_agent" value="1" 
                                   <?php checked(get_option('kata_form_save_user_agent'), 1); ?>>
                            <?php _e('Lưu trữ thông tin trình duyệt user agent', 'kata-form'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Điều này giúp xác định trình duyệt và thiết bị được sử dụng để gửi form.', 'kata-form'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Lưu Referrer', 'kata-form'); ?></th>
                    <td>
                        <label for="save_referrer">
                            <input type="checkbox" id="save_referrer" name="save_referrer" value="1" 
                                   <?php checked(get_option('kata_form_save_referrer'), 1); ?>>
                            <?php _e('Lưu trữ URL trang giới thiệu', 'kata-form'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Theo dõi khách truy cập đến từ trang nào hoặc trang web bên ngoài nào trước khi gửi form.', 'kata-form'); ?>
                        </p>
                    </td>
                </tr>
                
                <!-- Notification Settings -->
                <tr>
                    <th scope="row" colspan="2">
                        <h2><?php _e('Thông Báo', 'kata-form'); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Thông Báo Email', 'kata-form'); ?></th>
                    <td>
                        <label for="email_notifications">
                            <input type="checkbox" id="email_notifications" name="email_notifications" value="1" 
                                   <?php checked(get_option('kata_form_email_notifications'), 1); ?>>
                            <?php _e('Gửi thông báo email cho dữ liệu mới', 'kata-form'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Email Nhận Thông Báo', 'kata-form'); ?></th>
                    <td>
                        <input type="email" name="notification_email" 
                               value="<?php echo esc_attr(get_option('kata_form_notification_email', get_option('admin_email'))); ?>" 
                               class="regular-text">
                        <p class="description">
                            <?php _e('Địa chỉ email để nhận thông báo dữ liệu mới.', 'kata-form'); ?>
                        </p>
                    </td>
                </tr>
                
                <!-- Data Management -->
                <tr>
                    <th scope="row" colspan="2">
                        <h2><?php _e('Data Management', 'kata-form'); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Auto Delete', 'kata-form'); ?></th>
                    <td>
                        <input type="number" name="auto_delete_days" 
                               value="<?php echo esc_attr(get_option('kata_form_auto_delete_days', 0)); ?>" 
                               min="0" max="365" class="small-text">
                        <?php _e('days', 'kata-form'); ?>
                        <p class="description">
                            <?php _e('Automatically delete submissions older than specified days. Set to 0 to disable.', 'kata-form'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Export Format', 'kata-form'); ?></th>
                    <td>
                        <select name="export_format">
                            <option value="csv" <?php selected(get_option('kata_form_export_format'), 'csv'); ?>>
                                <?php _e('CSV', 'kata-form'); ?>
                            </option>
                            <option value="json" <?php selected(get_option('kata_form_export_format'), 'json'); ?>>
                                <?php _e('JSON', 'kata-form'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e('Default format for data exports.', 'kata-form'); ?>
                        </p>
                    </td>
                </tr>
                
                <!-- Display Settings -->
                <tr>
                    <th scope="row" colspan="2">
                        <h2><?php _e('Display Settings', 'kata-form'); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Dashboard Widget', 'kata-form'); ?></th>
                    <td>
                        <label for="dashboard_widget">
                            <input type="checkbox" id="dashboard_widget" name="dashboard_widget" value="1" 
                                   <?php checked(get_option('kata_form_dashboard_widget'), 1); ?>>
                            <?php _e('Show form statistics on WordPress dashboard', 'kata-form'); ?>
                        </label>
                    </td>
                </tr>
                
                <!-- Database Information -->
                <tr>
                    <th scope="row" colspan="2">
                        <h2><?php _e('Database Information', 'kata-form'); ?></h2>
                    </th>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Database Status', 'kata-form'); ?></th>
                    <td>
                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'kata_form_submissions';
                        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
                        $total_submissions = $table_exists ? $wpdb->get_var("SELECT COUNT(*) FROM $table_name") : 0;
                        ?>
                        
                        <p>
                            <strong><?php _e('Table:', 'kata-form'); ?></strong> 
                            <code><?php echo esc_html($table_name); ?></code>
                            <?php if ($table_exists): ?>
                                <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                            <?php else: ?>
                                <span class="dashicons dashicons-dismiss" style="color: red;"></span>
                            <?php endif; ?>
                        </p>
                        
                        <p>
                            <strong><?php _e('Total Submissions:', 'kata-form'); ?></strong> 
                            <?php echo number_format($total_submissions); ?>
                        </p>
                        
                        <?php if ($table_exists): ?>
                            <p>
                                <button type="button" class="button" id="kata-cleanup-database">
                                    <?php _e('Clean Database', 'kata-form'); ?>
                                </button>
                                <span class="description">
                                    <?php _e('Remove old and orphaned records.', 'kata-form'); ?>
                                </span>
                            </p>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php submit_button(__('Save Settings', 'kata-form')); ?>
    </form>
    
    <!-- Import/Export Section -->
    <div class="kata-import-export-section">
        <h2><?php _e('Import/Export Settings', 'kata-form'); ?></h2>
        
        <div class="kata-settings-grid">
            <div class="kata-settings-card">
                <h3><?php _e('Export Settings', 'kata-form'); ?></h3>
                <p><?php _e('Download your current plugin settings as a backup.', 'kata-form'); ?></p>
                <button type="button" class="button" id="kata-export-settings">
                    <?php _e('Export Settings', 'kata-form'); ?>
                </button>
            </div>
            
            <div class="kata-settings-card">
                <h3><?php _e('Import Settings', 'kata-form'); ?></h3>
                <p><?php _e('Restore plugin settings from a backup file.', 'kata-form'); ?></p>
                <input type="file" id="kata-import-file" accept=".json" style="display: none;">
                <button type="button" class="button" id="kata-import-settings">
                    <?php _e('Import Settings', 'kata-form'); ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- System Information -->
    <div class="kata-system-info">
        <h2><?php _e('System Information', 'kata-form'); ?></h2>
        
        <table class="widefat">
            <tbody>
                <tr>
                    <td><strong><?php _e('Plugin Version', 'kata-form'); ?></strong></td>
                    <td><?php echo KATA_FORM_VERSION; ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('WordPress Version', 'kata-form'); ?></strong></td>
                    <td><?php echo get_bloginfo('version'); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('PHP Version', 'kata-form'); ?></strong></td>
                    <td><?php echo PHP_VERSION; ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('MySQL Version', 'kata-form'); ?></strong></td>
                    <td><?php echo $wpdb->db_version(); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Contact Form 7', 'kata-form'); ?></strong></td>
                    <td>
                        <?php if (class_exists('WPCF7_ContactForm')): ?>
                            <?php echo defined('WPCF7_VERSION') ? WPCF7_VERSION : __('Active', 'kata-form'); ?>
                            <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                        <?php else: ?>
                            <?php _e('Not installed', 'kata-form'); ?>
                            <span class="dashicons dashicons-dismiss" style="color: red;"></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php _e('Server Memory Limit', 'kata-form'); ?></strong></td>
                    <td><?php echo ini_get('memory_limit'); ?></td>
                </tr>
                <tr>
                    <td><strong><?php _e('Max Upload Size', 'kata-form'); ?></strong></td>
                    <td><?php echo size_format(wp_max_upload_size()); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Database cleanup
    document.getElementById('kata-cleanup-database')?.addEventListener('click', function() {
        if (confirm('<?php echo esc_js(__('Are you sure you want to clean the database? This action cannot be undone.', 'kata-form')); ?>')) {
            // Add AJAX call for database cleanup
            console.log('Database cleanup initiated');
        }
    });
    
    // Export settings
    document.getElementById('kata-export-settings')?.addEventListener('click', function() {
        // Create and download settings JSON
        const settings = {
            save_ip_address: document.getElementById('save_ip_address').checked,
            save_user_agent: document.getElementById('save_user_agent').checked,
            save_referrer: document.getElementById('save_referrer').checked,
            email_notifications: document.getElementById('email_notifications').checked,
            notification_email: document.querySelector('input[name="notification_email"]').value,
            auto_delete_days: document.querySelector('input[name="auto_delete_days"]').value,
            export_format: document.querySelector('select[name="export_format"]').value,
            dashboard_widget: document.getElementById('dashboard_widget').checked
        };
        
        const blob = new Blob([JSON.stringify(settings, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'kata-form-settings-' + new Date().toISOString().split('T')[0] + '.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    
    // Import settings
    document.getElementById('kata-import-settings')?.addEventListener('click', function() {
        document.getElementById('kata-import-file').click();
    });
    
    document.getElementById('kata-import-file')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const settings = JSON.parse(e.target.result);
                    
                    // Apply imported settings
                    Object.keys(settings).forEach(key => {
                        const element = document.querySelector(`[name="${key}"]`);
                        if (element) {
                            if (element.type === 'checkbox') {
                                element.checked = settings[key];
                            } else {
                                element.value = settings[key];
                            }
                        }
                    });
                    
                    alert('<?php echo esc_js(__('Settings imported successfully! Please save to apply changes.', 'kata-form')); ?>');
                } catch (error) {
                    alert('<?php echo esc_js(__('Invalid settings file format.', 'kata-form')); ?>');
                }
            };
            reader.readAsText(file);
        }
    });
});
</script>
