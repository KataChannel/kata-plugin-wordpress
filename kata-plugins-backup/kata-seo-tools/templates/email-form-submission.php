<?php
/**
 * Email Template: Form Submission Notification
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .submission-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .field-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .field-row:last-child {
            border-bottom: none;
        }
        .field-label {
            font-weight: 600;
            color: #555;
            display: inline-block;
            width: 150px;
        }
        .field-value {
            color: #333;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .metadata {
            font-size: 12px;
            color: #999;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📝 New Form Submission</h1>
        </div>
        
        <div class="email-body">
            <p>You have received a new form submission from your website.</p>
            
            <div class="submission-info">
                <h3 style="margin-top: 0;">Submission Details</h3>
                
                <?php foreach ($form_data as $field_name => $field_value) : ?>
                    <div class="field-row">
                        <span class="field-label"><?php echo esc_html(ucfirst(str_replace('_', ' ', $field_name))); ?>:</span>
                        <span class="field-value"><?php echo esc_html($field_value); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center;">
                <a href="<?php echo admin_url('admin.php?page=kata-seo-dashboard'); ?>" class="button">
                    View in Dashboard
                </a>
            </div>
            
            <div class="metadata">
                <strong>Submitted:</strong> <?php echo date('F j, Y \a\t g:i a'); ?><br>
                <strong>IP Address:</strong> <?php echo esc_html($user_ip); ?><br>
                <strong>Form ID:</strong> #<?php echo esc_html($form_id); ?>
            </div>
        </div>
        
        <div class="email-footer">
            <p>This email was sent from <strong><?php echo esc_html(get_bloginfo('name')); ?></strong></p>
            <p>Powered by Kata SEO Tools</p>
        </div>
    </div>
</body>
</html>
