<?php
/**
 * Form Handler
 * 
 * Handles custom forms with submissions and analytics
 *
 * @package Kata_SEO_Tools
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Kata_SEO_Form_Handler {
    
    /**
     * Render form shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render($atts) {
        $atts = shortcode_atts(array(
            'id' => 'form-' . uniqid(),
            'title' => '',
            'fields' => '',
            'submit_text' => 'Submit',
            'success_message' => 'Thank you! Your form has been submitted.',
            'redirect_url' => ''
        ), $atts);
        
        if (empty($atts['fields'])) {
            return '<div class="kata-form-error">No form fields provided.</div>';
        }
        
        $fields = json_decode(base64_decode($atts['fields']), true);
        if (!$fields) {
            $fields = json_decode($atts['fields'], true);
        }
        
        if (!$fields || !is_array($fields)) {
            return '<div class="kata-form-error">Invalid form fields format.</div>';
        }
        
        $post_id = get_the_ID();
        
        ob_start();
        ?>
        <div class="kata-form-wrapper" 
             data-form-id="<?php echo esc_attr($atts['id']); ?>"
             data-post-id="<?php echo esc_attr($post_id); ?>">
            
            <?php if (!empty($atts['title'])) : ?>
                <h3 class="kata-form-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <form class="kata-custom-form" onsubmit="kataSEO.submitForm(event, this); return false;">
                <?php wp_nonce_field('kata_seo_form_' . $atts['id'], 'kata_form_nonce'); ?>
                
                <input type="hidden" name="form_id" value="<?php echo esc_attr($atts['id']); ?>">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
                
                <div class="kata-form-fields">
                    <?php foreach ($fields as $field) : 
                        $field_id = 'field-' . sanitize_title($field['name']);
                        $required = isset($field['required']) && $field['required'] ? 'required' : '';
                        $placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
                    ?>
                        <div class="kata-form-field kata-form-field-<?php echo esc_attr($field['type']); ?>">
                            <?php if (!empty($field['label'])) : ?>
                                <label for="<?php echo esc_attr($field_id); ?>">
                                    <?php echo esc_html($field['label']); ?>
                                    <?php if ($required) : ?>
                                        <span class="required">*</span>
                                    <?php endif; ?>
                                </label>
                            <?php endif; ?>
                            
                            <?php if ($field['type'] === 'text' || $field['type'] === 'email' || $field['type'] === 'tel' || $field['type'] === 'url') : ?>
                                <input type="<?php echo esc_attr($field['type']); ?>" 
                                       id="<?php echo esc_attr($field_id); ?>"
                                       name="<?php echo esc_attr($field['name']); ?>" 
                                       placeholder="<?php echo esc_attr($placeholder); ?>"
                                       <?php echo $required; ?>>
                                       
                            <?php elseif ($field['type'] === 'textarea') : ?>
                                <textarea id="<?php echo esc_attr($field_id); ?>"
                                          name="<?php echo esc_attr($field['name']); ?>" 
                                          placeholder="<?php echo esc_attr($placeholder); ?>"
                                          rows="<?php echo isset($field['rows']) ? intval($field['rows']) : 5; ?>"
                                          <?php echo $required; ?>></textarea>
                                          
                            <?php elseif ($field['type'] === 'select') : ?>
                                <select id="<?php echo esc_attr($field_id); ?>"
                                        name="<?php echo esc_attr($field['name']); ?>" 
                                        <?php echo $required; ?>>
                                    <?php if (!empty($placeholder)) : ?>
                                        <option value=""><?php echo esc_html($placeholder); ?></option>
                                    <?php endif; ?>
                                    <?php if (isset($field['options']) && is_array($field['options'])) : ?>
                                        <?php foreach ($field['options'] as $option) : ?>
                                            <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                            <?php elseif ($field['type'] === 'radio' || $field['type'] === 'checkbox') : ?>
                                <div class="kata-form-options">
                                    <?php if (isset($field['options']) && is_array($field['options'])) : ?>
                                        <?php foreach ($field['options'] as $index => $option) : 
                                            $option_id = $field_id . '-' . $index;
                                        ?>
                                            <label class="kata-form-option">
                                                <input type="<?php echo esc_attr($field['type']); ?>" 
                                                       id="<?php echo esc_attr($option_id); ?>"
                                                       name="<?php echo esc_attr($field['name']) . ($field['type'] === 'checkbox' ? '[]' : ''); ?>" 
                                                       value="<?php echo esc_attr($option); ?>"
                                                       <?php echo $required; ?>>
                                                <span><?php echo esc_html($option); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($field['description'])) : ?>
                                <small class="kata-field-description"><?php echo esc_html($field['description']); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="kata-form-actions">
                    <button type="submit" class="kata-form-submit-btn">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                            <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                        </svg>
                        <?php echo esc_html($atts['submit_text']); ?>
                    </button>
                </div>
                
                <div class="kata-form-message" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Submit form
     * 
     * @param array $data Form data
     * @return array Result
     */
    public function submit_form($data) {
        global $wpdb;
        
        $form_id = sanitize_text_field($data['form_id']);
        $post_id = intval($data['post_id']);
        
        // Verify nonce
        if (!isset($data['kata_form_nonce']) || !wp_verify_nonce($data['kata_form_nonce'], 'kata_seo_form_' . $form_id)) {
            return array('error' => 'Security check failed');
        }
        
        // Remove system fields
        unset($data['action']);
        unset($data['nonce']);
        unset($data['kata_form_nonce']);
        unset($data['form_id']);
        unset($data['post_id']);
        
        // Sanitize form data
        $form_data = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $form_data[$key] = array_map('sanitize_text_field', $value);
            } else {
                $form_data[$key] = sanitize_text_field($value);
            }
        }
        
        // Save to database
        $table = $wpdb->prefix . 'kata_seo_form_submissions';
        $result = $wpdb->insert($table, array(
            'post_id' => $post_id,
            'form_id' => $form_id,
            'form_data' => wp_json_encode($form_data),
            'user_id' => get_current_user_id() ?: null,
            'ip_address' => $this->get_client_ip(),
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'], 0, 255)
        ), array('%d', '%s', '%s', '%d', '%s', '%s'));
        
        if (!$result) {
            return array('error' => 'Failed to save form submission');
        }
        
        // Send notification email
        $this->send_notification_email($form_id, $post_id, $form_data);
        
        return array(
            'success' => true,
            'message' => 'Form submitted successfully!',
            'submission_id' => $wpdb->insert_id
        );
    }
    
    /**
     * Send notification email
     * 
     * @param string $form_id Form ID
     * @param int $post_id Post ID
     * @param array $form_data Form data
     */
    private function send_notification_email($form_id, $post_id, $form_data) {
        $admin_email = get_option('admin_email');
        $post_title = get_the_title($post_id);
        
        $subject = sprintf('[%s] New Form Submission: %s', get_bloginfo('name'), $form_id);
        
        $message = "New form submission received:\n\n";
        $message .= "Post: " . $post_title . "\n";
        $message .= "Form ID: " . $form_id . "\n\n";
        $message .= "Submission Data:\n";
        $message .= "================\n\n";
        
        foreach ($form_data as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $message .= $key . ": " . $value . "\n";
        }
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Get form submissions
     * 
     * @param string $form_id Form ID
     * @param int $limit Number of submissions to retrieve
     * @return array Submissions
     */
    public function get_submissions($form_id, $limit = 50) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_form_submissions';
        
        $submissions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE form_id = %s ORDER BY created_at DESC LIMIT %d",
            $form_id, $limit
        ), ARRAY_A);
        
        foreach ($submissions as &$submission) {
            $submission['form_data'] = json_decode($submission['form_data'], true);
        }
        
        return $submissions;
    }
    
    /**
     * Get form statistics
     * 
     * @param string $form_id Form ID
     * @return array Statistics
     */
    public function get_form_stats($form_id) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'kata_seo_form_submissions';
        
        $stats = array(
            'total_submissions' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE form_id = %s",
                $form_id
            )),
            'submissions_today' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE form_id = %s AND DATE(created_at) = CURDATE()",
                $form_id
            )),
            'submissions_this_week' => $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE form_id = %s AND YEARWEEK(created_at) = YEARWEEK(NOW())",
                $form_id
            )),
            'submissions_by_date' => $wpdb->get_results($wpdb->prepare(
                "SELECT DATE(created_at) as date, COUNT(*) as count 
                 FROM $table 
                 WHERE form_id = %s 
                 GROUP BY DATE(created_at) 
                 ORDER BY date DESC 
                 LIMIT 30",
                $form_id
            ), ARRAY_A)
        );
        
        return $stats;
    }
    
    /**
     * Get client IP address
     * 
     * @return string IP address
     */
    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return sanitize_text_field(trim($ip));
    }
    
    /**
     * Export submissions to CSV
     * 
     * @param string $form_id Form ID
     * @return string CSV content
     */
    public function export_to_csv($form_id) {
        $submissions = $this->get_submissions($form_id, 10000);
        
        if (empty($submissions)) {
            return '';
        }
        
        // Get all field names
        $fields = array('ID', 'Submitted At');
        foreach ($submissions[0]['form_data'] as $key => $value) {
            $fields[] = $key;
        }
        
        // Create CSV
        $csv = implode(',', $fields) . "\n";
        
        foreach ($submissions as $submission) {
            $row = array($submission['id'], $submission['created_at']);
            foreach ($submission['form_data'] as $value) {
                if (is_array($value)) {
                    $value = implode('|', $value);
                }
                $row[] = '"' . str_replace('"', '""', $value) . '"';
            }
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }
}
