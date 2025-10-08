<?php
/**
 * Database Handler Class
 * 
 * Handles all database operations for chatbot
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class KataChatbot_DB_Handler {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Constructor can be used for initialization if needed
    }
    
    /**
     * Create or get conversation
     */
    public function get_or_create_conversation($session_id, $user_id = null) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        // Try to get existing conversation
        $conversation = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $conversations_table WHERE session_id = %s AND status = 'active'",
            $session_id
        ));
        
        if ($conversation) {
            // Update last activity
            $wpdb->update(
                $conversations_table,
                array('last_activity' => current_time('mysql')),
                array('id' => $conversation->id),
                array('%s'),
                array('%d')
            );
            
            return $conversation;
        }
        
        // Create new conversation
        $conversation_data = array(
            'session_id' => $session_id,
            'user_id' => $user_id,
            'user_ip' => $this->get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'started_at' => current_time('mysql'),
            'last_activity' => current_time('mysql'),
            'status' => 'active',
            'total_messages' => 0
        );
        
        $inserted = $wpdb->insert(
            $conversations_table,
            $conversation_data,
            array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d')
        );
        
        if ($inserted === false) {
            throw new Exception('Failed to create conversation');
        }
        
        $conversation_id = $wpdb->insert_id;
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $conversations_table WHERE id = %d",
            $conversation_id
        ));
    }
    
    /**
     * Save message to database
     */
    public function save_message($conversation_id, $sender_type, $message_text, $ai_response_data = null) {
        global $wpdb;
        
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        $message_data = array(
            'conversation_id' => $conversation_id,
            'sender_type' => $sender_type,
            'message_text' => $message_text,
            'created_at' => current_time('mysql')
        );
        
        // Add AI response data if provided
        if ($ai_response_data && $sender_type === 'bot') {
            $message_data['ai_response'] = $ai_response_data['message'] ?? '';
            $message_data['ai_model'] = $ai_response_data['model'] ?? '';
            $message_data['response_time'] = $ai_response_data['response_time'] ?? 0;
            $message_data['metadata'] = wp_json_encode($ai_response_data['metadata'] ?? array());
        }
        
        $format = array('%d', '%s', '%s', '%s');
        if ($sender_type === 'bot') {
            $format = array_merge($format, array('%s', '%s', '%f', '%s'));
        }
        
        $inserted = $wpdb->insert($messages_table, $message_data, $format);
        
        if ($inserted === false) {
            throw new Exception('Failed to save message');
        }
        
        // Update conversation message count
        $wpdb->query($wpdb->prepare(
            "UPDATE $conversations_table 
             SET total_messages = total_messages + 1, last_activity = %s 
             WHERE id = %d",
            current_time('mysql'),
            $conversation_id
        ));
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get conversation history
     */
    public function get_conversation_history($session_id, $limit = 50) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $query = $wpdb->prepare(
            "SELECT m.* FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE c.session_id = %s AND c.status = 'active'
             ORDER BY m.created_at ASC
             LIMIT %d",
            $session_id,
            $limit
        );
        
        $messages = $wpdb->get_results($query);
        
        // Format messages for frontend
        $formatted_messages = array();
        foreach ($messages as $message) {
            $formatted_messages[] = array(
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'message' => $message->message_text,
                'timestamp' => $message->created_at,
                'response_time' => $message->response_time,
                'metadata' => $message->metadata ? json_decode($message->metadata, true) : array()
            );
        }
        
        return $formatted_messages;
    }
    
    /**
     * Get conversation context for AI
     */
    public function get_conversation_context($session_id, $limit = 5) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $query = $wpdb->prepare(
            "SELECT m.sender_type, m.message_text FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE c.session_id = %s AND c.status = 'active'
             ORDER BY m.created_at DESC
             LIMIT %d",
            $session_id,
            $limit
        );
        
        $messages = $wpdb->get_results($query);
        
        // Reverse to get chronological order
        return array_reverse($messages);
    }
    
    /**
     * Close conversation
     */
    public function close_conversation($session_id) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        return $wpdb->update(
            $conversations_table,
            array(
                'status' => 'closed',
                'last_activity' => current_time('mysql')
            ),
            array('session_id' => $session_id),
            array('%s', '%s'),
            array('%s')
        );
    }
    
    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats($days = 30) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $date_from = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        $stats = array();
        
        // Total conversations
        $stats['total_conversations'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $conversations_table WHERE started_at >= %s",
            $date_from
        ));
        
        // Total messages
        $stats['total_messages'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE c.started_at >= %s",
            $date_from
        ));
        
        // Average response time
        $stats['avg_response_time'] = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(response_time) FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE m.sender_type = 'bot' AND c.started_at >= %s AND m.response_time > 0",
            $date_from
        ));
        
        // Active users (unique IPs in the period)
        $stats['active_users'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_ip) FROM $conversations_table 
             WHERE started_at >= %s",
            $date_from
        ));
        
        // Average rating
        $avg_rating = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(rating) FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE m.rating > 0 AND c.started_at >= %s",
            $date_from
        ));
        $stats['avg_rating'] = $avg_rating ? round((float) $avg_rating, 2) : 0;
        
        // Satisfaction rate (percentage of ratings >= 4 out of 5)
        $satisfaction_rate = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) * 100.0 / NULLIF((SELECT COUNT(*) FROM $messages_table m2 
             INNER JOIN $conversations_table c2 ON m2.conversation_id = c2.id 
             WHERE m2.rating > 0 AND c2.started_at >= %s), 0)
             FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE m.rating >= 4 AND c.started_at >= %s",
            $date_from,
            $date_from
        ));
        $stats['satisfaction_rate'] = $satisfaction_rate ? round((float) $satisfaction_rate, 1) : 0;
        
        // Daily conversation counts
        $stats['daily_conversations'] = $wpdb->get_results($wpdb->prepare(
            "SELECT DATE(started_at) as date, COUNT(*) as count
             FROM $conversations_table
             WHERE started_at >= %s
             GROUP BY DATE(started_at)
             ORDER BY date ASC",
            $date_from
        ));
        
        // Popular topics (simplified - based on message keywords)
        $stats['popular_keywords'] = $this->get_popular_keywords($date_from);
        
        // Active conversations
        $stats['active_conversations'] = $wpdb->get_var(
            "SELECT COUNT(*) FROM $conversations_table WHERE status = 'active'"
        );
        
        return $stats;
    }
    
    /**
     * Get popular keywords from conversations
     */
    private function get_popular_keywords($date_from) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $messages = $wpdb->get_results($wpdb->prepare(
            "SELECT m.message_text FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE m.sender_type = 'user' AND c.started_at >= %s",
            $date_from
        ));
        
        $keywords = array();
        foreach ($messages as $message) {
            $words = $this->extract_keywords($message->message_text);
            foreach ($words as $word) {
                if (isset($keywords[$word])) {
                    $keywords[$word]++;
                } else {
                    $keywords[$word] = 1;
                }
            }
        }
        
        // Sort by frequency and return top 10
        arsort($keywords);
        return array_slice($keywords, 0, 10, true);
    }
    
    /**
     * Extract keywords from text
     */
    private function extract_keywords($text) {
        // Vietnamese stop words
        $stop_words = array(
            'và', 'của', 'cho', 'với', 'từ', 'trong', 'trên', 'dưới', 'này', 'đó', 
            'những', 'các', 'một', 'có', 'là', 'được', 'sẽ', 'đã', 'bạn', 'tôi', 
            'chúng', 'họ', 'em', 'anh', 'chị', 'cô', 'chú', 'bác'
        );
        
        // Convert to lowercase and extract words
        $words = preg_split('/\s+/', mb_strtolower($text, 'UTF-8'));
        
        // Filter meaningful words
        $keywords = array_filter($words, function($word) use ($stop_words) {
            return strlen($word) > 2 && !in_array($word, $stop_words) && !is_numeric($word);
        });
        
        return array_unique($keywords);
    }
    
    /**
     * Get conversations for admin
     */
    public function get_conversations_for_admin($page = 1, $per_page = 20, $filters = array()) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $offset = ($page - 1) * $per_page;
        
        $where_conditions = array('1=1');
        $where_values = array();
        
        // Apply filters
        if (!empty($filters['status'])) {
            $where_conditions[] = 'c.status = %s';
            $where_values[] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $where_conditions[] = 'c.started_at >= %s';
            $where_values[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where_conditions[] = 'c.started_at <= %s';
            $where_values[] = $filters['date_to'];
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        // Get conversations with message counts
        $query = "SELECT c.*, 
                         COUNT(m.id) as message_count,
                         MAX(m.created_at) as last_message_at,
                         (SELECT m2.message_text 
                          FROM $messages_table m2 
                          WHERE m2.conversation_id = c.id 
                          ORDER BY m2.created_at DESC 
                          LIMIT 1) as last_message
                  FROM $conversations_table c
                  LEFT JOIN $messages_table m ON c.id = m.conversation_id
                  WHERE $where_clause
                  GROUP BY c.id
                  ORDER BY c.last_activity DESC
                  LIMIT %d OFFSET %d";
        
        $where_values[] = $per_page;
        $where_values[] = $offset;
        
        if (count($where_values) > 2) {
            $query = $wpdb->prepare($query, $where_values);
        } else {
            // Only LIMIT and OFFSET, prepare them directly
            $query = $wpdb->prepare($query, $per_page, $offset);
        }
        
        $conversations = $wpdb->get_results($query);
        
        // Get total count
        $count_query = "SELECT COUNT(DISTINCT c.id) FROM $conversations_table c WHERE $where_clause";
        if (count($where_values) > 2) {
            $count_values = array_slice($where_values, 0, -2); // Remove LIMIT and OFFSET
            if (!empty($count_values)) {
                $count_query = $wpdb->prepare($count_query, $count_values);
            }
        }
        $total_count = $wpdb->get_var($count_query);
        
        return array(
            'conversations' => $conversations,
            'total' => $total_count,
            'pages' => ceil($total_count / $per_page),
            'current_page' => $page
        );
    }
    
    /**
     * Get knowledge base entries
     */
    public function get_knowledge_entries($page = 1, $per_page = 20, $search = '') {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        $offset = ($page - 1) * $per_page;
        
        $where_conditions = array("status = 'active'");
        $where_values = array();
        
        if (!empty($search)) {
            $where_conditions[] = '(question LIKE %s OR answer LIKE %s OR keywords LIKE %s)';
            $search_term = '%' . $search . '%';
            $where_values = array($search_term, $search_term, $search_term);
        }
        
        $where_clause = implode(' AND ', $where_conditions);
        
        $query = "SELECT * FROM $knowledge_table 
                  WHERE $where_clause 
                  ORDER BY priority DESC, created_at DESC 
                  LIMIT %d OFFSET %d";
        
        $where_values[] = $per_page;
        $where_values[] = $offset;
        
        if (!empty($where_values)) {
            $query = $wpdb->prepare($query, $where_values);
        }
        
        $entries = $wpdb->get_results($query);
        
        // Get total count
        $count_query = "SELECT COUNT(*) FROM $knowledge_table WHERE $where_clause";
        if (!empty($search)) {
            $count_query = $wpdb->prepare($count_query, array($search_term, $search_term, $search_term));
        }
        $total_count = $wpdb->get_var($count_query);
        
        return array(
            'entries' => $entries,
            'total' => $total_count,
            'pages' => ceil($total_count / $per_page),
            'current_page' => $page
        );
    }
    
    /**
     * Save knowledge entry
     */
    public function save_knowledge_entry($data) {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        $entry_data = array(
            'category' => sanitize_text_field($data['category']),
            'question' => sanitize_textarea_field($data['question']),
            'answer' => sanitize_textarea_field($data['answer']),
            'keywords' => sanitize_text_field($data['keywords']),
            'priority' => intval($data['priority'] ?? 1),
            'status' => sanitize_text_field($data['status'] ?? 'active')
        );
        
        if (!empty($data['id'])) {
            // Update existing entry
            $entry_data['updated_at'] = current_time('mysql');
            return $wpdb->update(
                $knowledge_table,
                $entry_data,
                array('id' => intval($data['id'])),
                array('%s', '%s', '%s', '%s', '%d', '%s', '%s'),
                array('%d')
            );
        } else {
            // Insert new entry
            $entry_data['created_at'] = current_time('mysql');
            return $wpdb->insert(
                $knowledge_table,
                $entry_data,
                array('%s', '%s', '%s', '%s', '%d', '%s', '%s')
            );
        }
    }
    
    /**
     * Delete knowledge entry
     */
    public function delete_knowledge_entry($id) {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        return $wpdb->delete(
            $knowledge_table,
            array('id' => intval($id)),
            array('%d')
        );
    }
    
    /**
     * Update analytics
     */
    public function update_analytics($date = null) {
        global $wpdb;
        
        if (!$date) {
            $date = current_time('Y-m-d');
        }
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        $analytics_table = $wpdb->prefix . 'kata_chatbot_analytics';
        
        // Get stats for the date
        $stats = array();
        
        $stats['total_conversations'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $conversations_table WHERE DATE(started_at) = %s",
            $date
        ));
        
        $stats['total_messages'] = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE DATE(c.started_at) = %s",
            $date
        ));
        
        $stats['avg_response_time'] = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(response_time) FROM $messages_table m
             INNER JOIN $conversations_table c ON m.conversation_id = c.id
             WHERE m.sender_type = 'bot' AND DATE(c.started_at) = %s AND m.response_time > 0",
            $date
        ));
        
        // Insert or update analytics
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $analytics_table WHERE date = %s",
            $date
        ));
        
        $analytics_data = array(
            'date' => $date,
            'total_conversations' => $stats['total_conversations'],
            'total_messages' => $stats['total_messages'],
            'avg_response_time' => $stats['avg_response_time'] ?: 0,
        );
        
        if ($existing) {
            return $wpdb->update(
                $analytics_table,
                $analytics_data,
                array('id' => $existing->id),
                array('%s', '%d', '%d', '%f'),
                array('%d')
            );
        } else {
            return $wpdb->insert(
                $analytics_table,
                $analytics_data,
                array('%s', '%d', '%d', '%f')
            );
        }
    }
    
    /**
     * Clean old conversations
     */
    public function clean_old_conversations($days = 90) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        // Delete old conversations and their messages (cascade will handle messages)
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $conversations_table WHERE last_activity < %s",
            $cutoff_date
        ));
        
        return $deleted;
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR');
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Get conversations for admin
     */
    public function get_admin_conversations($limit = 20, $offset = 0) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $conversations_table 
                 ORDER BY last_activity DESC 
                 LIMIT %d OFFSET %d",
                $limit,
                $offset
            )
        );
        
        return $results ? $results : array();
    }
    
    /**
     * Get total conversations count
     */
    public function get_total_conversations() {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM $conversations_table");
    }
    
    /**
     * Get knowledge base items
     */
    public function get_knowledge_base_items($category = '', $status = 'active') {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        $where_clauses = array();
        $where_values = array();
        
        if (!empty($status)) {
            $where_clauses[] = 'status = %s';
            $where_values[] = $status;
        }
        
        if (!empty($category)) {
            $where_clauses[] = 'category = %s';
            $where_values[] = $category;
        }
        
        $where_sql = !empty($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';
        
        $sql = "SELECT * FROM $knowledge_table $where_sql ORDER BY priority DESC, created_at DESC";
        
        if (!empty($where_values)) {
            $results = $wpdb->get_results($wpdb->prepare($sql, $where_values));
        } else {
            $results = $wpdb->get_results($sql);
        }
        
        return $results ? $results : array();
    }
    
    /**
     * Get analytics data
     */
    public function get_analytics_data($days = 30) {
        global $wpdb;
        
        $conversations_table = $wpdb->prefix . 'kata_chatbot_conversations';
        $messages_table = $wpdb->prefix . 'kata_chatbot_messages';
        
        $date_from = date('Y-m-d', strtotime("-$days days"));
        
        // Get daily conversation counts
        $daily_conversations = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE(started_at) as date, COUNT(*) as count 
                 FROM $conversations_table 
                 WHERE DATE(started_at) >= %s 
                 GROUP BY DATE(started_at) 
                 ORDER BY date ASC",
                $date_from
            )
        );
        
        // Get daily message counts
        $daily_messages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT DATE(created_at) as date, COUNT(*) as count 
                 FROM $messages_table 
                 WHERE DATE(created_at) >= %s 
                 GROUP BY DATE(created_at) 
                 ORDER BY date ASC",
                $date_from
            )
        );
        
        return array(
            'daily_conversations' => $daily_conversations,
            'daily_messages' => $daily_messages
        );
    }
    
    /**
     * Save knowledge base item
     */
    public function save_knowledge_item($data) {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        $item_data = array(
            'category' => sanitize_text_field($data['category']),
            'question' => sanitize_textarea_field($data['question']),
            'answer' => wp_kses_post($data['answer']),
            'keywords' => sanitize_text_field($data['keywords']),
            'priority' => intval($data['priority']),
            'status' => sanitize_text_field($data['status'])
        );
        
        $format = array('%s', '%s', '%s', '%s', '%d', '%s');
        
        if (isset($data['id']) && $data['id'] > 0) {
            // Update existing item
            $result = $wpdb->update(
                $knowledge_table,
                $item_data,
                array('id' => intval($data['id'])),
                $format,
                array('%d')
            );
            
            return $result !== false ? intval($data['id']) : false;
        } else {
            // Create new item
            $result = $wpdb->insert($knowledge_table, $item_data, $format);
            
            return $result ? $wpdb->insert_id : false;
        }
    }
    
    /**
     * Delete knowledge base item
     */
    public function delete_knowledge_item($id) {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        return $wpdb->delete(
            $knowledge_table,
            array('id' => intval($id)),
            array('%d')
        );
    }
    
    /**
     * Search knowledge base
     */
    public function search_knowledge($query) {
        global $wpdb;
        
        $knowledge_table = $wpdb->prefix . 'kata_chatbot_knowledge';
        
        $search_terms = explode(' ', sanitize_text_field($query));
        $where_clauses = array();
        $where_values = array();
        
        foreach ($search_terms as $term) {
            if (strlen($term) > 2) {
                $where_clauses[] = '(question LIKE %s OR answer LIKE %s OR keywords LIKE %s)';
                $like_term = '%' . $wpdb->esc_like($term) . '%';
                $where_values[] = $like_term;
                $where_values[] = $like_term;
                $where_values[] = $like_term;
            }
        }
        
        if (empty($where_clauses)) {
            return array();
        }
        
        $where_sql = 'WHERE status = "active" AND (' . implode(' AND ', $where_clauses) . ')';
        
        $sql = "SELECT * FROM $knowledge_table $where_sql ORDER BY priority DESC LIMIT 10";
        
        $results = $wpdb->get_results($wpdb->prepare($sql, $where_values));
        
        return $results ? $results : array();
    }
    
    /**
     * Add knowledge base item (compatibility method)
     */
    public function add_knowledge_item($question, $answer, $category = 'general', $keywords = '', $priority = 1) {
        $data = array(
            'category' => $category,
            'question' => $question,
            'answer' => $answer,
            'keywords' => $keywords,
            'priority' => $priority,
            'status' => 'active'
        );
        
        return $this->save_knowledge_item($data);
    }
}
