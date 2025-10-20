<?php
/**
 * URL Redirect Helper for Smart Chatbot Admin Pages
 * 
 * This file handles incorrect URL formats and redirects to correct WordPress admin URLs
 * 
 * @package KataChatbot
 * @since 1.1.1
 */

// Load WordPress
require_once('../../../../wp-load.php');

// Check if user is logged in and has permission
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('You do not have permission to access this page.');
}

// Get the current URL path
$request_uri = $_SERVER['REQUEST_URI'];

// Determine which page was requested
$page_slug = '';

if (strpos($request_uri, 'kata-smart-chatbot') !== false) {
    $page_slug = 'kata-smart-chatbot';
} elseif (strpos($request_uri, 'kata-chatbot-logs') !== false) {
    $page_slug = 'kata-chatbot-logs';
} elseif (strpos($request_uri, 'kata-chatbot-leads') !== false) {
    $page_slug = 'kata-chatbot-leads';
}

// If we found a matching page, redirect to correct URL
if (!empty($page_slug)) {
    $correct_url = admin_url('admin.php?page=' . $page_slug);
    
    // Show redirect message
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Redirecting...</title>
        <meta http-equiv="refresh" content="3;url=<?php echo esc_url($correct_url); ?>">
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                background: #f0f0f1;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 100px auto;
                background: white;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.13);
            }
            h1 {
                color: #042277;
                margin-top: 0;
            }
            .notice {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 15px;
                margin: 20px 0;
            }
            .success {
                background: #d4edda;
                border-left: 4px solid #28a745;
                padding: 15px;
                margin: 20px 0;
            }
            .url-box {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                font-family: monospace;
                word-break: break-all;
                margin: 10px 0;
            }
            .wrong {
                color: #dc3545;
            }
            .correct {
                color: #28a745;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background: #042277;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
            }
            .btn:hover {
                background: #031a5c;
            }
            .spinner {
                border: 3px solid #f3f3f3;
                border-top: 3px solid #042277;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                animation: spin 1s linear infinite;
                display: inline-block;
                vertical-align: middle;
                margin-right: 10px;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔄 URL Redirect</h1>
            
            <div class="notice">
                <strong>⚠️ Incorrect URL Format</strong>
                <p>You are using an incorrect URL format for WordPress admin pages.</p>
            </div>
            
            <p><strong>Your URL (Wrong):</strong></p>
            <div class="url-box wrong">
                <?php echo esc_html($request_uri); ?>
            </div>
            
            <p><strong>Correct URL:</strong></p>
            <div class="url-box correct">
                <?php echo esc_html(str_replace(home_url(), '', $correct_url)); ?>
            </div>
            
            <div class="success">
                <div class="spinner"></div>
                <strong>Redirecting in 3 seconds...</strong>
            </div>
            
            <p>If you are not redirected automatically, please click the button below:</p>
            
            <a href="<?php echo esc_url($correct_url); ?>" class="btn">
                Go to <?php echo ucwords(str_replace('-', ' ', $page_slug)); ?>
            </a>
            
            <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
            
            <h3>📌 How to Access Admin Pages Correctly</h3>
            
            <p><strong>Method 1: Via Admin Menu (Recommended)</strong></p>
            <ol>
                <li>Go to WordPress Admin Dashboard</li>
                <li>Click on <strong>KATA Chatbot</strong> in the sidebar</li>
                <li>Select the submenu item you need</li>
            </ol>
            
            <p><strong>Method 2: Direct URL</strong></p>
            <p>Always use this format:</p>
            <div class="url-box">
                /wp-admin/admin.php?page=PAGE_SLUG
            </div>
            
            <p><strong>Examples:</strong></p>
            <ul>
                <li>Smart Chatbot: <code>/wp-admin/admin.php?page=kata-smart-chatbot</code></li>
                <li>Chat Logs: <code>/wp-admin/admin.php?page=kata-chatbot-logs</code></li>
                <li>Leads: <code>/wp-admin/admin.php?page=kata-chatbot-leads</code></li>
            </ul>
            
            <p style="color: #666; font-size: 14px; margin-top: 30px;">
                For more information, see: <strong>HOW_TO_ACCESS_ADMIN_PAGES.md</strong>
            </p>
        </div>
        
        <script>
            // Auto redirect after 3 seconds
            setTimeout(function() {
                window.location.href = '<?php echo esc_js($correct_url); ?>';
            }, 3000);
        </script>
    </body>
    </html>
    <?php
    exit;
} else {
    // Unknown page
    wp_die('Unknown admin page requested. Please use WordPress admin menu to navigate.');
}
