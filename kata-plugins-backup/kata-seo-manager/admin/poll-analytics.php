<?php
/**
 * Poll Analytics Admin Page
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get specific poll if requested
$selected_poll_id = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;
$selected_poll = null;

// Get all polls for dropdown
$polls = $wpdb->get_results("SELECT id, title FROM {$wpdb->prefix}kata_polls ORDER BY created_at DESC");

if ($selected_poll_id > 0) {
    $selected_poll = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}kata_polls WHERE id = %d",
        $selected_poll_id
    ));
}

// Get poll statistics
function get_poll_stats($poll_id = null) {
    global $wpdb;
    
    $where_clause = $poll_id ? "WHERE poll_id = $poll_id" : "";
    
    $stats = array(
        'total_polls' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_polls"),
        'total_votes' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_poll_votes $where_clause"),
        'active_polls' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kata_polls WHERE active = 1"),
        'unique_voters' => $wpdb->get_var("SELECT COUNT(DISTINCT voter_ip) FROM {$wpdb->prefix}kata_poll_votes $where_clause")
    );
    
    return $stats;
}

$stats = get_poll_stats($selected_poll_id);

// Get vote distribution for selected poll
$vote_distribution = array();
if ($selected_poll) {
    $votes = $wpdb->get_results($wpdb->prepare(
        "SELECT option_index, COUNT(*) as vote_count 
         FROM {$wpdb->prefix}kata_poll_votes 
         WHERE poll_id = %d 
         GROUP BY option_index 
         ORDER BY option_index",
        $selected_poll_id
    ));
    
    $options = json_decode($selected_poll->options, true);
    $total_votes = array_sum(array_column($votes, 'vote_count'));
    
    foreach ($votes as $vote) {
        $percentage = $total_votes > 0 ? round(($vote->vote_count / $total_votes) * 100, 1) : 0;
        $option_text = isset($options[$vote->option_index]) ? $options[$vote->option_index] : 'Unknown Option';
        $vote_distribution[] = array(
            'option_index' => $vote->option_index,
            'option_text' => $option_text,
            'vote_count' => $vote->vote_count,
            'percentage' => $percentage
        );
    }
}

// Get recent votes
$recent_votes = array();
if ($selected_poll_id > 0) {
    $recent_votes = $wpdb->get_results($wpdb->prepare(
        "SELECT option_index, voter_ip, created_at 
         FROM {$wpdb->prefix}kata_poll_votes 
         WHERE poll_id = %d 
         ORDER BY created_at DESC 
         LIMIT 50",
        $selected_poll_id
    ));
}

// Get voting trends (votes per day for last 30 days)
$voting_trends = array();
if ($selected_poll_id > 0) {
    $trends = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(created_at) as vote_date, COUNT(*) as daily_votes 
         FROM {$wpdb->prefix}kata_poll_votes 
         WHERE poll_id = %d AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(created_at) 
         ORDER BY vote_date DESC",
        $selected_poll_id
    ));
    
    foreach ($trends as $trend) {
        $voting_trends[] = array(
            'date' => $trend->vote_date,
            'votes' => $trend->daily_votes
        );
    }
}

?>
<div class="wrap">
    <h1>📈 Phân tích Poll</h1>
    
    <div class="kata-poll-analytics-header">
        <form method="get" action="">
            <input type="hidden" name="page" value="kata-seo-poll-analytics">
            <select name="poll_id" onchange="this.form.submit()">
                <option value="">-- Chọn Poll để phân tích --</option>
                <?php foreach ($polls as $poll): ?>
                    <option value="<?php echo $poll->id; ?>" <?php selected($selected_poll_id, $poll->id); ?>>
                        ID <?php echo $poll->id; ?>: <?php echo esc_html($poll->title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    
    <div class="kata-stats-overview">
        <div class="kata-stat-card">
            <h3>📊 Tổng Poll</h3>
            <div class="stat-number"><?php echo $stats['total_polls']; ?></div>
        </div>
        
        <div class="kata-stat-card">
            <h3>🗳️ Tổng Votes</h3>
            <div class="stat-number"><?php echo $stats['total_votes']; ?></div>
        </div>
        
        <div class="kata-stat-card">
            <h3>✅ Poll Hoạt động</h3>
            <div class="stat-number"><?php echo $stats['active_polls']; ?></div>
        </div>
        
        <div class="kata-stat-card">
            <h3>👥 Người bình chọn</h3>
            <div class="stat-number"><?php echo $stats['unique_voters']; ?></div>
        </div>
    </div>
    
    <?php if ($selected_poll): ?>
        <div class="kata-poll-detailed-analytics">
            <h2>Chi tiết Poll: <?php echo esc_html($selected_poll->title); ?></h2>
            
            <div class="kata-analytics-grid">
                <div class="kata-vote-distribution">
                    <h3>📊 Phân bố Votes</h3>
                    <?php if (empty($vote_distribution)): ?>
                        <p>Chưa có vote nào cho poll này.</p>
                    <?php else: ?>
                        <div class="vote-chart">
                            <?php foreach ($vote_distribution as $vote): ?>
                                <div class="vote-bar">
                                    <div class="vote-option"><?php echo esc_html($vote['option_text']); ?></div>
                                    <div class="vote-progress">
                                        <div class="vote-progress-bar" style="width: <?php echo $vote['percentage']; ?>%"></div>
                                    </div>
                                    <div class="vote-stats">
                                        <?php echo $vote['vote_count']; ?> votes (<?php echo $vote['percentage']; ?>%)
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="kata-poll-info">
                    <h3>ℹ️ Thông tin Poll</h3>
                    <table class="poll-info-table">
                        <tr>
                            <td><strong>ID:</strong></td>
                            <td><?php echo $selected_poll->id; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tiêu đề:</strong></td>
                            <td><?php echo esc_html($selected_poll->title); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Câu hỏi:</strong></td>
                            <td><?php echo esc_html($selected_poll->poll_question); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Trạng thái:</strong></td>
                            <td>
                                <span class="poll-status poll-status-<?php echo $selected_poll->status; ?>">
                                    <?php
                                    switch ($selected_poll->status) {
                                        case 'active': echo '🟢 Hoạt động'; break;
                                        case 'closed': echo '🔴 Đã đóng'; break;
                                        case 'draft': echo '🟡 Nháp'; break;
                                    }
                                    ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tổng votes:</strong></td>
                            <td><?php echo $selected_poll->total_votes; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Ngày tạo:</strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($selected_poll->created_at)); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Shortcode:</strong></td>
                            <td>
                                <code>[kata_poll id="<?php echo $selected_poll->id; ?>"]</code>
                                <button type="button" class="button-link copy-shortcode" 
                                        data-shortcode='[kata_poll id="<?php echo $selected_poll->id; ?>"]'>📋</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (!empty($voting_trends)): ?>
                <div class="kata-voting-trends">
                    <h3>📈 xu hướng Voting (30 ngày qua)</h3>
                    <div class="trend-chart">
                        <?php foreach (array_reverse($voting_trends) as $trend): ?>
                            <div class="trend-bar">
                                <div class="trend-date"><?php echo date('d/m', strtotime($trend['date'])); ?></div>
                                <div class="trend-value"><?php echo $trend['votes']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($recent_votes)): ?>
                <div class="kata-recent-votes">
                    <h3>🕒 Votes Gần đây</h3>
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th>Tùy chọn</th>
                                <th>IP</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Get poll options for display
                            $poll_options = json_decode($selected_poll->options, true);
                            foreach (array_slice($recent_votes, 0, 20) as $vote): 
                                $option_text = isset($poll_options[$vote->option_index]) ? $poll_options[$vote->option_index] : 'Option ' . ($vote->option_index + 1);
                            ?>
                                <tr>
                                    <td><?php echo esc_html($option_text); ?></td>
                                    <td><?php echo esc_html($vote->voter_ip); ?></td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($vote->created_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="kata-no-poll-selected">
            <h3>Chọn một poll để xem phân tích chi tiết</h3>
            <p>Sử dụng dropdown ở trên để chọn poll bạn muốn phân tích.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.kata-poll-analytics-header {
    margin-bottom: 20px;
    padding: 15px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.kata-stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kata-stat-card {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    text-align: center;
}

.kata-stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #666;
}

.stat-number {
    font-size: 36px;
    font-weight: bold;
    color: #0073aa;
}

.kata-poll-detailed-analytics {
    background: #fff;
    padding: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin-bottom: 20px;
}

.kata-analytics-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.vote-chart {
    margin-top: 15px;
}

.vote-bar {
    margin-bottom: 15px;
    display: grid;
    grid-template-columns: 1fr 2fr auto;
    gap: 15px;
    align-items: center;
}

.vote-option {
    font-weight: 500;
}

.vote-progress {
    background: #f1f1f1;
    height: 20px;
    border-radius: 10px;
    overflow: hidden;
}

.vote-progress-bar {
    background: linear-gradient(90deg, #0073aa 0%, #2271b1 100%);
    height: 100%;
    transition: width 0.3s ease;
    min-width: 2px;
}

.vote-stats {
    font-weight: bold;
    color: #0073aa;
    min-width: 120px;
    text-align: right;
}

.poll-info-table {
    width: 100%;
}

.poll-info-table td {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.poll-info-table td:first-child {
    width: 30%;
}

.poll-status {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
}

.poll-status-active {
    background: #d4edda;
    color: #155724;
}

.poll-status-closed {
    background: #f8d7da;
    color: #721c24;
}

.poll-status-draft {
    background: #fff3cd;
    color: #856404;
}

.kata-voting-trends {
    margin-bottom: 30px;
}

.trend-chart {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    overflow-x: auto;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}

.trend-bar {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 60px;
}

.trend-date {
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.trend-value {
    background: #0073aa;
    color: white;
    padding: 5px 8px;
    border-radius: 3px;
    font-weight: bold;
    font-size: 14px;
}

.kata-no-poll-selected {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.copy-shortcode {
    font-size: 12px;
    text-decoration: none;
    margin-left: 5px;
}

@media (max-width: 1024px) {
    .kata-analytics-grid {
        grid-template-columns: 1fr;
    }
    
    .vote-bar {
        grid-template-columns: 1fr;
        gap: 5px;
    }
    
    .vote-stats {
        text-align: left;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Copy shortcode functionality
    $('.copy-shortcode').click(function() {
        var shortcode = $(this).data('shortcode');
        navigator.clipboard.writeText(shortcode).then(function() {
            alert('Đã copy shortcode: ' + shortcode);
        }).catch(function() {
            // Fallback for older browsers
            var textArea = document.createElement('textarea');
            textArea.value = shortcode;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Đã copy shortcode: ' + shortcode);
        });
    });
});
</script>