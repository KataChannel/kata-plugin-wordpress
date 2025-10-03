<?php
/**
 * Email Template: Wheel Prize Notification
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
    <title>Congratulations! You Won a Prize!</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 28px;
        }
        .email-header .trophy {
            font-size: 60px;
            margin-bottom: 10px;
        }
        .email-body {
            padding: 40px 30px;
            text-align: center;
        }
        .prize-box {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
        }
        .prize-name {
            font-size: 32px;
            font-weight: bold;
            color: #d63031;
            margin: 10px 0;
        }
        .prize-details {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            margin: 20px 0;
            font-weight: 600;
            font-size: 16px;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .instructions {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .instructions h3 {
            margin-top: 0;
            color: #2980b9;
        }
        .confetti {
            font-size: 30px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="trophy">🏆</div>
            <h1>Congratulations!</h1>
            <p style="font-size: 18px; margin: 10px 0 0 0;">You Won a Prize!</p>
        </div>
        
        <div class="email-body">
            <p style="font-size: 20px;">
                <span class="confetti">🎉</span>
                <strong>Great news, <?php echo esc_html($user_name); ?>!</strong>
                <span class="confetti">🎉</span>
            </p>
            
            <p>You just won an amazing prize by spinning our Lucky Wheel!</p>
            
            <div class="prize-box">
                <div class="prize-name">
                    <?php echo esc_html($prize_name); ?>
                </div>
                <?php if (!empty($prize_description)) : ?>
                    <div class="prize-details">
                        <?php echo esc_html($prize_description); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="instructions">
                <h3>📋 How to Claim Your Prize:</h3>
                <ol>
                    <li>Click the button below to visit our website</li>
                    <li>Show this email to our team</li>
                    <li>Provide your prize code: <strong><?php echo esc_html($prize_code); ?></strong></li>
                    <li>Enjoy your prize! 🎁</li>
                </ol>
            </div>
            
            <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                Claim Your Prize Now
            </a>
            
            <p style="font-size: 14px; color: #999; margin-top: 30px;">
                This prize is valid until: <strong><?php echo date('F j, Y', strtotime('+30 days')); ?></strong>
            </p>
        </div>
        
        <div class="email-footer">
            <p><strong>Prize Details:</strong></p>
            <p>
                Won on: <?php echo date('F j, Y \a\t g:i a'); ?><br>
                Prize Code: <?php echo esc_html($prize_code); ?><br>
                Spin ID: #<?php echo esc_html($spin_id); ?>
            </p>
            <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
            <p>This email was sent from <strong><?php echo esc_html(get_bloginfo('name')); ?></strong></p>
            <p>Powered by Kata SEO Tools</p>
        </div>
    </div>
</body>
</html>
