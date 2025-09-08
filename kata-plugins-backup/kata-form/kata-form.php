<?php
/**
 * Plugin Name: Kata Form Manager
 * Plugin URI: https://kata.vn/kata-form
 * Description: Tích hợp nâng cao với Contact Form 7, lưu trữ cơ sở dữ liệu và quản lý thống kê toàn diện.
 * Version: 1.0.0
 * Author: Kata Team
 * Author URI: https://kata.vn
 * License: GPL v2 or later
 * Text Domain: kata-form
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('KATA_FORM_VERSION', '1.0.0');
define('KATA_FORM_PLUGIN_FILE', __FILE__);
define('KATA_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KATA_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KATA_FORM_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Initialize plugin - simplified approach
add_action('init', function() {
    // Check if Contact Form 7 is active
    if (!class_exists('WPCF7_ContactForm')) {
        add_action('admin_notices', function() {
            echo '<div class="notice notice-error"><p>';
            echo '<strong>Kata Form Manager:</strong> Yêu cầu plugin Contact Form 7 được cài đặt và kích hoạt.';
            echo '</p></div>';
        });
        return;
    }
    
    // Initialize plugin
    KataFormManager::getInstance();
}, 1);

/**
 * Main Plugin Class
 */
class KataFormManager {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Database table name
     */
    private $table_name;
    
    /**
     * Get plugin instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'kata_form_submissions';
        
        // Initialize plugin immediately
        $this->init();
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Load text domain
        load_plugin_textdomain('kata-form', false, dirname(KATA_FORM_PLUGIN_BASENAME) . '/languages');
        
        // Admin interface - ensure hooks are added early
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
        
        // Contact Form 7 hooks - add later to ensure CF7 is loaded
        add_action('init', array($this, 'addCF7Hooks'), 20);
        
        // AJAX handlers
        add_action('wp_ajax_kata_form_get_stats', array($this, 'ajaxGetStats'));
        add_action('wp_ajax_kata_form_export_data', array($this, 'ajaxExportData'));
        add_action('wp_ajax_kata_form_delete_submission', array($this, 'ajaxDeleteSubmission'));
        
        // Add shortcode for frontend display
        add_shortcode('kata_form_stats', array($this, 'displayStatsShortcode'));
    }
    
    /**
     * Add Contact Form 7 hooks after CF7 is loaded
     */
    public function addCF7Hooks() {
        if (class_exists('WPCF7_ContactForm')) {
            add_action('wpcf7_mail_sent', array($this, 'saveFormSubmission'), 10, 1);
            add_action('wpcf7_before_send_mail', array($this, 'beforeSendMail'), 10, 1);
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Ensure we have database access
        global $wpdb;
        if (!$wpdb) {
            return;
        }
        
        $this->createDatabase();
        $this->setDefaultOptions();
        
        // Create upload directory for attachments
        $upload_dir = wp_upload_dir();
        $kata_form_dir = $upload_dir['basedir'] . '/kata-form';
        if (!file_exists($kata_form_dir)) {
            wp_mkdir_p($kata_form_dir);
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up scheduled events if any
        wp_clear_scheduled_hook('kata_form_cleanup');
    }
    
    /**
     * Create database table
     */
    private function createDatabase() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE {$this->table_name} (
            id int(11) NOT NULL AUTO_INCREMENT,
            form_id int(11) NOT NULL,
            form_title varchar(255) NOT NULL,
            submission_data longtext NOT NULL,
            user_agent text,
            ip_address varchar(45),
            referrer text,
            form_page_url text,
            user_id int(11) DEFAULT NULL,
            status varchar(20) DEFAULT 'new',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY form_id (form_id),
            KEY status (status),
            KEY created_at (created_at),
            KEY ip_address (ip_address)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $result = dbDelta($sql);
        
        // Verify table was created and save version
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") == $this->table_name) {
            update_option('kata_form_db_version', '1.0');
        }
    }
    
    /**
     * Set default plugin options
     */
    private function setDefaultOptions() {
        $default_options = array(
            'save_ip_address' => true,
            'save_user_agent' => true,
            'save_referrer' => true,
            'auto_delete_days' => 0, // 0 = never delete
            'email_notifications' => false,
            'notification_email' => get_option('admin_email'),
            'export_format' => 'csv',
            'dashboard_widget' => true
        );
        
        foreach ($default_options as $option => $value) {
            if (!get_option("kata_form_{$option}")) {
                add_option("kata_form_{$option}", $value);
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_menu_page(
            __('Quản Lý Kata Form', 'kata-form'),
            __('Kata Forms', 'kata-form'),
            'manage_options',
            'kata-form',
            array($this, 'adminPage'),
            'dashicons-forms',
            30
        );
        
        add_submenu_page(
            'kata-form',
            __('Dữ Liệu Gửi', 'kata-form'),
            __('Dữ Liệu Gửi', 'kata-form'),
            'manage_options',
            'kata-form-submissions',
            array($this, 'adminPage')
        );
        
        add_submenu_page(
            'kata-form',
            __('Thống Kê', 'kata-form'),
            __('Thống Kê', 'kata-form'),
            'manage_options',
            'kata-form-stats',
            array($this, 'statsPage')
        );
        
        add_submenu_page(
            'kata-form',
            __('Cài Đặt', 'kata-form'),
            __('Cài Đặt', 'kata-form'),
            'manage_options',
            'kata-form-settings',
            array($this, 'settingsPage')
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'kata-form') === false) {
            return;
        }
        
        wp_enqueue_script('kata-form-admin', KATA_FORM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), KATA_FORM_VERSION, true);
        wp_enqueue_style('kata-form-admin', KATA_FORM_PLUGIN_URL . 'assets/css/admin.css', array(), KATA_FORM_VERSION);
        
        // Chart.js for statistics
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
        
        wp_localize_script('kata-form-admin', 'kataForm', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kata_form_nonce'),
            'strings' => array(
                'confirmDelete' => __('Bạn có chắc chắn muốn xóa dữ liệu này?', 'kata-form'),
                'exportSuccess' => __('Xuất dữ liệu thành công!', 'kata-form'),
                'error' => __('Có lỗi xảy ra. Vui lòng thử lại.', 'kata-form')
            )
        ));
    }
    
    /**
     * Save Contact Form 7 submission
     */
    public function saveFormSubmission($contact_form) {
        global $wpdb;
        
        $submission = WPCF7_Submission::get_instance();
        
        if (!$submission) {
            return;
        }
        
        $form_data = $submission->get_posted_data();
        $form_id = $contact_form->id();
        $form_title = $contact_form->title();
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table_name}'") != $this->table_name) {
            $this->createDatabase();
        }
        
        // Prepare submission data
        $submission_data = array(
            'form_id' => $form_id,
            'form_title' => $form_title,
            'submission_data' => json_encode($form_data),
            'ip_address' => $this->getUserIP(),
            'user_agent' => $this->getUserAgent(),
            'referrer' => $this->getReferrer(),
            'form_page_url' => $this->getCurrentPageURL(),
            'user_id' => get_current_user_id() ?: null,
            'status' => 'new',
            'created_at' => current_time('mysql')
        );
        
        // Insert into database
        $result = $wpdb->insert($this->table_name, $submission_data);
        
        if ($result !== false) {
            $submission_id = $wpdb->insert_id;
            
            // Send notification if enabled
            if (get_option('kata_form_email_notifications')) {
                $this->sendNotificationEmail($submission_id, $submission_data);
            }
            
            // Hook for other plugins
            do_action('kata_form_submission_saved', $submission_id, $submission_data);
        }
    }
    
    /**
     * Before send mail hook
     */
    public function beforeSendMail($contact_form) {
        // Add any pre-processing logic here
        do_action('kata_form_before_send_mail', $contact_form);
    }
    
    /**
     * Get user IP address
     */
    private function getUserIP() {
        if (!get_option('kata_form_save_ip_address')) {
            return null;
        }
        
        $ip_keys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                return sanitize_text_field(trim($ip));
            }
        }
        
        return null;
    }
    
    /**
     * Get user agent
     */
    private function getUserAgent() {
        if (!get_option('kata_form_save_user_agent')) {
            return null;
        }
        
        return isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : null;
    }
    
    /**
     * Get referrer
     */
    private function getReferrer() {
        if (!get_option('kata_form_save_referrer')) {
            return null;
        }
        
        return isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : null;
    }
    
    /**
     * Get current page URL
     */
    private function getCurrentPageURL() {
        $protocol = is_ssl() ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Send notification email
     */
    private function sendNotificationEmail($submission_id, $data) {
        $email = get_option('kata_form_notification_email');
        if (!$email) {
            return;
        }
        
        $subject = sprintf(__('[%s] New Form Submission - %s', 'kata-form'), get_bloginfo('name'), $data['form_title']);
        
        $message = sprintf(__('A new form submission has been received:\n\nForm: %s\nSubmission ID: %d\nDate: %s\n\nView details: %s', 'kata-form'),
            $data['form_title'],
            $submission_id,
            $data['created_at'],
            admin_url('admin.php?page=kata-form&submission_id=' . $submission_id)
        );
        
        wp_mail($email, $subject, $message);
    }
    
    /**
     * Admin page
     */
    public function adminPage() {
        $page = isset($_GET['page']) ? $_GET['page'] : 'kata-form';
        
        switch ($page) {
            case 'kata-form-submissions':
                include KATA_FORM_PLUGIN_DIR . 'templates/admin-submissions.php';
                break;
            default:
                // Main dashboard - show submissions by default
                include KATA_FORM_PLUGIN_DIR . 'templates/admin-submissions.php';
                break;
        }
    }
    
    /**
     * Statistics page
     */
    public function statsPage() {
        include KATA_FORM_PLUGIN_DIR . 'templates/admin-statistics.php';
    }
    
    /**
     * Settings page
     */
    public function settingsPage() {
        if (isset($_POST['submit'])) {
            $this->saveSettings();
        }
        include KATA_FORM_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    /**
     * Save settings
     */
    private function saveSettings() {
        if (!check_admin_referer('kata_form_settings', 'kata_form_settings_nonce')) {
            return;
        }
        
        $settings = array(
            'save_ip_address',
            'save_user_agent', 
            'save_referrer',
            'email_notifications',
            'dashboard_widget'
        );
        
        foreach ($settings as $setting) {
            $value = isset($_POST[$setting]) ? 1 : 0;
            update_option("kata_form_{$setting}", $value);
        }
        
        // Text/number settings
        if (isset($_POST['auto_delete_days'])) {
            update_option('kata_form_auto_delete_days', intval($_POST['auto_delete_days']));
        }
        
        if (isset($_POST['notification_email'])) {
            update_option('kata_form_notification_email', sanitize_email($_POST['notification_email']));
        }
        
        if (isset($_POST['export_format'])) {
            update_option('kata_form_export_format', sanitize_text_field($_POST['export_format']));
        }
        
        add_settings_error('kata_form_settings', 'settings_updated', __('Settings saved successfully!', 'kata-form'), 'updated');
    }
    
    /**
     * AJAX: Get statistics
     */
    public function ajaxGetStats() {
        check_ajax_referer('kata_form_nonce', 'nonce');
        
        $stats = $this->getStatistics();
        wp_send_json_success($stats);
    }
    
    /**
     * AJAX: Export data
     */
    public function ajaxExportData() {
        check_ajax_referer('kata_form_nonce', 'nonce');
        
        $format = sanitize_text_field($_POST['format'] ?? 'csv');
        $form_id = intval($_POST['form_id'] ?? 0);
        $date_from = sanitize_text_field($_POST['date_from'] ?? '');
        $date_to = sanitize_text_field($_POST['date_to'] ?? '');
        
        $export_url = $this->exportData($format, $form_id, $date_from, $date_to);
        
        if ($export_url) {
            wp_send_json_success(array('download_url' => $export_url));
        } else {
            wp_send_json_error(__('Export failed', 'kata-form'));
        }
    }
    
    /**
     * AJAX: Delete submission
     */
    public function ajaxDeleteSubmission() {
        check_ajax_referer('kata_form_nonce', 'nonce');
        
        $submission_id = intval($_POST['submission_id']);
        
        global $wpdb;
        $result = $wpdb->delete($this->table_name, array('id' => $submission_id), array('%d'));
        
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Failed to delete submission', 'kata-form'));
        }
    }
    
    /**
     * Get statistics
     */
    public function getStatistics() {
        global $wpdb;
        
        $stats = array();
        
        // Total submissions
        $stats['total_submissions'] = $wpdb->get_var("SELECT COUNT(*) FROM {$this->table_name}");
        
        // Submissions today
        $stats['today_submissions'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE DATE(created_at) = %s",
            current_time('Y-m-d')
        ));
        
        // Submissions this month
        $stats['month_submissions'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->table_name} WHERE YEAR(created_at) = %d AND MONTH(created_at) = %d",
            current_time('Y'),
            current_time('n')
        ));
        
        // Most popular form
        $popular_form = $wpdb->get_row(
            "SELECT form_title, COUNT(*) as count FROM {$this->table_name} GROUP BY form_id ORDER BY count DESC LIMIT 1"
        );
        $stats['popular_form'] = $popular_form ? $popular_form->form_title : __('No submissions yet', 'kata-form');
        
        // Daily submissions for last 30 days
        $daily_stats = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM {$this->table_name} 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY DATE(created_at) 
             ORDER BY date ASC"
        ));
        
        $stats['daily_chart'] = array();
        foreach ($daily_stats as $day) {
            $stats['daily_chart'][] = array(
                'date' => $day->date,
                'count' => intval($day->count)
            );
        }
        
        // Form breakdown
        $form_stats = $wpdb->get_results(
            "SELECT form_title, COUNT(*) as count FROM {$this->table_name} GROUP BY form_id ORDER BY count DESC"
        );
        
        $stats['form_breakdown'] = array();
        foreach ($form_stats as $form) {
            $stats['form_breakdown'][] = array(
                'form' => $form->form_title,
                'count' => intval($form->count)
            );
        }
        
        return $stats;
    }
    
    /**
     * Export data
     */
    public function exportData($format = 'csv', $form_id = 0, $date_from = '', $date_to = '') {
        global $wpdb;
        
        $where_clauses = array();
        $params = array();
        
        if ($form_id > 0) {
            $where_clauses[] = "form_id = %d";
            $params[] = $form_id;
        }
        
        if ($date_from) {
            $where_clauses[] = "DATE(created_at) >= %s";
            $params[] = $date_from;
        }
        
        if ($date_to) {
            $where_clauses[] = "DATE(created_at) <= %s";
            $params[] = $date_to;
        }
        
        $where_sql = empty($where_clauses) ? '' : 'WHERE ' . implode(' AND ', $where_clauses);
        
        $query = "SELECT * FROM {$this->table_name} {$where_sql} ORDER BY created_at DESC";
        
        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }
        
        $results = $wpdb->get_results($query, ARRAY_A);
        
        if (empty($results)) {
            return false;
        }
        
        $upload_dir = wp_upload_dir();
        $filename = 'kata-form-export-' . date('Y-m-d-H-i-s') . '.' . $format;
        $filepath = $upload_dir['path'] . '/' . $filename;
        
        if ($format === 'csv') {
            $this->exportToCSV($results, $filepath);
        } else {
            $this->exportToJSON($results, $filepath);
        }
        
        return $upload_dir['url'] . '/' . $filename;
    }
    
    /**
     * Export to CSV
     */
    private function exportToCSV($data, $filepath) {
        $file = fopen($filepath, 'w');
        
        // Headers
        $headers = array('ID', 'Form Title', 'Submission Date', 'Status', 'IP Address');
        
        // Add dynamic headers from first submission
        if (!empty($data)) {
            $first_submission = json_decode($data[0]['submission_data'], true);
            if ($first_submission) {
                foreach (array_keys($first_submission) as $field) {
                    $headers[] = ucfirst(str_replace('-', ' ', $field));
                }
            }
        }
        
        fputcsv($file, $headers);
        
        // Data rows
        foreach ($data as $row) {
            $csv_row = array(
                $row['id'],
                $row['form_title'],
                $row['created_at'],
                $row['status'],
                $row['ip_address']
            );
            
            // Add submission data
            $submission_data = json_decode($row['submission_data'], true);
            if ($submission_data) {
                foreach (array_keys($first_submission) as $field) {
                    $csv_row[] = isset($submission_data[$field]) ? $submission_data[$field] : '';
                }
            }
            
            fputcsv($file, $csv_row);
        }
        
        fclose($file);
    }
    
    /**
     * Export to JSON
     */
    private function exportToJSON($data, $filepath) {
        // Process submission data
        foreach ($data as &$row) {
            $row['submission_data'] = json_decode($row['submission_data'], true);
        }
        
        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Display statistics shortcode
     */
    public function displayStatsShortcode($atts) {
        $atts = shortcode_atts(array(
            'form_id' => 0,
            'type' => 'summary'
        ), $atts);
        
        ob_start();
        include KATA_FORM_PLUGIN_DIR . 'templates/frontend-stats.php';
        return ob_get_clean();
    }
}

/**
 * Activation and Deactivation Hooks
 */
register_activation_hook(__FILE__, 'kata_form_activate');
register_deactivation_hook(__FILE__, 'kata_form_deactivate');

function kata_form_activate() {
    $instance = KataFormManager::getInstance();
    $instance->activate();
}

function kata_form_deactivate() {
    $instance = KataFormManager::getInstance();
    $instance->deactivate();
}

// Helper function for getting instance
function kata_form() {
    return KataFormManager::getInstance();
}
