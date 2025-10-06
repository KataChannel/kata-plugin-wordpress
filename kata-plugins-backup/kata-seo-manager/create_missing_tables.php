<?php
/**
 * Create Missing Tables for Demo Content Feature
 * 
 * Run this script to create kata_polls table
 */

// Load WordPress
require_once('../../../wp-load.php');

// Must be admin
if (!current_user_can('manage_options')) {
    die('You must be an administrator to run this script.');
}

echo "<h1>KATA SEO Manager - Create Missing Tables</h1>";
echo "<hr>";

global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

// Create kata_polls table
echo "<h2>Creating kata_polls table...</h2>";
$table_polls = $wpdb->prefix . 'kata_polls';
$sql_polls = "CREATE TABLE IF NOT EXISTS $table_polls (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    title varchar(500) NOT NULL,
    description text DEFAULT NULL,
    options longtext NOT NULL,
    allow_multiple tinyint(1) DEFAULT 0,
    show_results tinyint(1) DEFAULT 1,
    require_login tinyint(1) DEFAULT 0,
    active tinyint(1) DEFAULT 1,
    total_votes int(11) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY active (active)
) $charset_collate;";

dbDelta($sql_polls);

// Check if table was created
if ($wpdb->get_var("SHOW TABLES LIKE '$table_polls'") == $table_polls) {
    echo "✅ Table <strong>$table_polls</strong> created successfully!<br>";
    
    // Show table structure
    $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_polls");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column->Field}</td>";
        echo "<td>{$column->Type}</td>";
        echo "<td>{$column->Null}</td>";
        echo "<td>{$column->Key}</td>";
        echo "<td>{$column->Default}</td>";
        echo "<td>{$column->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Failed to create table <strong>$table_polls</strong><br>";
}

echo "<hr>";

// Create kata_user_interactions table
echo "<h2>Creating kata_user_interactions table...</h2>";
$table_user_interactions = $wpdb->prefix . 'kata_user_interactions';
$sql_user_interactions = "CREATE TABLE IF NOT EXISTS $table_user_interactions (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    post_id bigint(20) DEFAULT NULL,
    user_id bigint(20) DEFAULT NULL,
    user_name varchar(255) DEFAULT NULL,
    user_email varchar(255) DEFAULT NULL,
    user_website varchar(500) DEFAULT NULL,
    interaction_type enum('comment', 'like', 'share', 'follow', 'subscribe', 'review', 'rating', 'other') DEFAULT 'comment',
    review_title varchar(500) DEFAULT NULL,
    review_content text DEFAULT NULL,
    review_pros text DEFAULT NULL,
    review_cons text DEFAULT NULL,
    overall_rating decimal(3,2) DEFAULT NULL,
    quality_rating decimal(3,2) DEFAULT NULL,
    value_rating decimal(3,2) DEFAULT NULL,
    service_rating decimal(3,2) DEFAULT NULL,
    comment_content text DEFAULT NULL,
    parent_id bigint(20) DEFAULT NULL,
    target_id bigint(20) DEFAULT NULL,
    ip_address varchar(45) DEFAULT NULL,
    user_agent text DEFAULT NULL,
    status enum('pending', 'approved', 'rejected', 'spam', 'trash') DEFAULT 'pending',
    is_featured tinyint(1) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY  (id),
    KEY user_id (user_id),
    KEY post_id (post_id),
    KEY parent_id (parent_id),
    KEY interaction_type (interaction_type),
    KEY status (status),
    KEY is_featured (is_featured),
    KEY created_at (created_at)
) $charset_collate;";

dbDelta($sql_user_interactions);

// Check if table was created
if ($wpdb->get_var("SHOW TABLES LIKE '$table_user_interactions'") == $table_user_interactions) {
    echo "✅ Table <strong>$table_user_interactions</strong> created successfully!<br>";
    
    // Show table structure
    $columns = $wpdb->get_results("SHOW COLUMNS FROM $table_user_interactions");
    echo "<h3>Table Structure:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>{$column->Field}</td>";
        echo "<td>{$column->Type}</td>";
        echo "<td>{$column->Null}</td>";
        echo "<td>{$column->Key}</td>";
        echo "<td>{$column->Default}</td>";
        echo "<td>{$column->Extra}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Failed to create table <strong>$table_user_interactions</strong><br>";
}

echo "<hr>";

// Verify all required tables exist
echo "\n=== Verifying All Required Tables ===\n";
$required_tables = array(
    'kata_polls',
    'kata_seo_quizzes',
    'kata_wheels',
    'kata_user_interactions'
);

foreach ($required_tables as $table_name) {
    $full_table = $wpdb->prefix . $table_name;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table'");
    echo $table_name . ': ' . ($exists ? '✅ EXISTS' : '❌ MISSING') . "\n";
}
echo "<h2>Checking All Required Tables:</h2>";
$required_tables = array(
    'kata_polls',
    'kata_seo_quizzes',
    'kata_wheels',
    'kata_user_interactions'
);

echo "<ul>";
foreach ($required_tables as $table_name) {
    $full_table_name = $wpdb->prefix . $table_name;
    if ($wpdb->get_var("SHOW TABLES LIKE '$full_table_name'") == $full_table_name) {
        echo "<li>✅ <strong>$full_table_name</strong> - EXISTS</li>";
    } else {
        echo "<li>❌ <strong>$full_table_name</strong> - MISSING</li>";
    }
}
echo "</ul>";

echo "<hr>";
echo "<h2>✅ Setup Complete!</h2>";
echo "<p>You can now use the Demo Content Generator feature.</p>";
echo "<p><a href='/wp-admin/admin.php?page=kata-seo-settings' class='button button-primary'>Go to Settings</a></p>";
