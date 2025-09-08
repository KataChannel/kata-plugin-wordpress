<?php
/**
 * Content Analysis Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current page and filters
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$posts_per_page = 20;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : 'all';
$orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';

// Get admin instance
$admin = KataSEO_Admin::get_instance();

// Get posts with SEO data
$posts_data = $admin->get_posts_with_seo_data(array(
    'posts_per_page' => $posts_per_page,
    'paged' => $current_page,
    'search' => $search,
    'filter' => $filter,
    'orderby' => $orderby,
    'order' => $order
));

$posts = $posts_data['posts'];
$total_posts = isset($posts_data['total_posts']) ? $posts_data['total_posts'] : 0;
$total_pages = ceil($total_posts / $posts_per_page);
?>

<div class="wrap kata-seo-content">
    <div class="kata-header">
        <h1 class="kata-title">
            <span class="kata-logo">📝</span>
            Content Analysis
        </h1>
        <div class="kata-header-actions">
            <button class="kata-btn kata-btn-primary bulk-analyze-btn">
                <span class="dashicons dashicons-search"></span>
                Bulk Analyze
            </button>
            <button class="kata-btn kata-btn-secondary export-csv-btn">
                <span class="dashicons dashicons-download"></span>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="kata-filters">
        <div class="filter-section">
            <div class="search-box">
                <input type="text" class="kata-input search-posts" placeholder="Search posts..." 
                       value="<?php echo esc_attr($search); ?>">
                <span class="search-icon">🔍</span>
            </div>

            <select class="kata-select filter-posts" name="filter">
                <option value="all" <?php selected($filter, 'all'); ?>>All Posts</option>
                <option value="analyzed" <?php selected($filter, 'analyzed'); ?>>Analyzed</option>
                <option value="not_analyzed" <?php selected($filter, 'not_analyzed'); ?>>Not Analyzed</option>
                <option value="excellent" <?php selected($filter, 'excellent'); ?>>Excellent (90+)</option>
                <option value="good" <?php selected($filter, 'good'); ?>>Good (80-89)</option>
                <option value="fair" <?php selected($filter, 'fair'); ?>>Fair (60-79)</option>
                <option value="poor" <?php selected($filter, 'poor'); ?>>Poor (<60)</option>
                <option value="issues" <?php selected($filter, 'issues'); ?>>Has Issues</option>
            </select>

            <select class="kata-select" name="post_type">
                <option value="all">All Types</option>
                <option value="post">Posts</option>
                <option value="page">Pages</option>
                <?php
                $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
                foreach ($post_types as $post_type) {
                    echo '<option value="' . esc_attr($post_type->name) . '">' . esc_html($post_type->label) . '</option>';
                }
                ?>
            </select>

            <select class="kata-select" name="orderby">
                <option value="date" <?php selected($orderby, 'date'); ?>>Date</option>
                <option value="score" <?php selected($orderby, 'score'); ?>>SEO Score</option>
                <option value="title" <?php selected($orderby, 'title'); ?>>Title</option>
                <option value="issues" <?php selected($orderby, 'issues'); ?>>Issues Count</option>
            </select>

            <select class="kata-select" name="order">
                <option value="desc" <?php selected($order, 'desc'); ?>>Descending</option>
                <option value="asc" <?php selected($order, 'asc'); ?>>Ascending</option>
            </select>
        </div>

        <div class="bulk-actions">
            <input type="checkbox" id="select-all-posts" class="select-all-checkbox">
            <label for="select-all-posts">Select All</label>
            
            <select class="kata-select bulk-action-select">
                <option value="">Bulk Actions</option>
                <option value="analyze">Analyze Selected</option>
                <option value="get_ai_suggestions">Get AI Suggestions</option>
                <option value="export">Export Selected</option>
                <option value="delete_analysis">Delete Analysis Data</option>
            </select>
            
            <button class="kata-btn kata-btn-secondary apply-bulk-action">Apply</button>
        </div>
    </div>

    <!-- Posts List Container -->
    <div class="posts-list-container">
        <?php if (!empty($posts)): ?>
            <div class="posts-table-wrapper">
                <table class="kata-table posts-table">
                    <thead>
                        <tr>
                            <th class="check-column">
                                <input type="checkbox" class="select-all-checkbox">
                            </th>
                            <th class="title-column">Title</th>
                            <th class="score-column">SEO Score</th>
                            <th class="issues-column">Issues</th>
                            <th class="keywords-column">Focus Keywords</th>
                            <th class="last-analyzed-column">Last Analyzed</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <?php
                            $post_id = $post['ID'];
                            $seo_data = array(
                                'score' => $post['seo_score'],
                                'last_analyzed' => $post['last_analyzed']
                            );
                            
                            $score = $post['seo_score'] ?: 0;
                            $issues_count = 0; // Placeholder - could be calculated from analysis results
                            $focus_keyword = get_post_meta($post_id, '_kata_seo_focus_keyword', true);
                            $last_analyzed = $post['last_analyzed'] ?: 'Never';
                            ?>
                            <tr class="post-row" data-post-id="<?php echo esc_attr($post_id); ?>">
                                <td class="check-column">
                                    <input type="checkbox" class="post-checkbox" value="<?php echo esc_attr($post_id); ?>">
                                </td>
                                
                                <td class="title-column">
                                    <div class="post-title-section">
                                        <a href="<?php echo esc_url($post['edit_link'] ?: '#'); ?>" class="post-title">
                                            <?php echo esc_html($post['title'] ?: '(No title)'); ?>
                                        </a>
                                        <div class="post-meta">
                                            <span class="post-type"><?php echo esc_html($post['post_type'] ?: 'post'); ?></span>
                                            <span class="post-status status-<?php echo esc_attr($post['status'] ?: 'unknown'); ?>">
                                                <?php echo esc_html(ucfirst($post['status'] ?: 'Unknown')); ?>
                                            </span>
                                            <span class="post-date">
                                                <?php 
                                                $post_obj = get_post($post_id);
                                                $post_date = $post_obj ? $post_obj->post_date : '';
                                                echo esc_html($post_date ? date('M j, Y', strtotime($post_date)) : 'Unknown date'); 
                                                ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="score-column">
                                    <?php if ($seo_data): ?>
                                        <div class="seo-score score-<?php echo esc_attr($admin->get_score_class($score)); ?>">
                                            <div class="score-circle">
                                                <span class="score-number"><?php echo esc_html($score); ?></span>
                                            </div>
                                            <div class="score-label">/100</div>
                                        </div>
                                    <?php else: ?>
                                        <div class="seo-score not-analyzed">
                                            <span class="score-text">Not Analyzed</span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <td class="issues-column">
                                    <?php if ($seo_data && $issues_count > 0): ?>
                                        <div class="issues-indicator">
                                            <span class="issues-count"><?php echo esc_html($issues_count); ?></span>
                                            <span class="issues-label">issues</span>
                                        </div>
                                    <?php else: ?>
                                        <span class="no-issues">✅</span>
                                    <?php endif; ?>
                                </td>

                                <td class="keywords-column">
                                    <?php if ($focus_keyword): ?>
                                        <div class="focus-keyword-display">
                                            <span class="keyword-tag"><?php echo esc_html($focus_keyword); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="no-keyword">-</span>
                                    <?php endif; ?>
                                </td>

                                <td class="last-analyzed-column">
                                    <?php if ($last_analyzed): ?>
                                        <span class="analyzed-time" title="<?php echo esc_attr($last_analyzed); ?>">
                                            <?php echo esc_html(human_time_diff(strtotime($last_analyzed))); ?> ago
                                        </span>
                                    <?php else: ?>
                                        <span class="never-analyzed">Never</span>
                                    <?php endif; ?>
                                </td>

                                <td class="actions-column">
                                    <div class="action-buttons">
                                        <button class="action-btn analyze-post-btn" 
                                                data-post-id="<?php echo esc_attr($post_id); ?>"
                                                title="Analyze this post">
                                            🔍
                                        </button>
                                        
                                        <?php if (get_option('kata_seo_ai_enabled', false)): ?>
                                        <button class="action-btn get-ai-suggestions-btn" 
                                                data-post-id="<?php echo esc_attr($post_id); ?>"
                                                title="Get AI suggestions">
                                            🤖
                                        </button>
                                        <?php endif; ?>
                                        
                                        <a href="<?php echo get_edit_post_link($post_id); ?>" 
                                           class="action-btn edit-btn" 
                                           title="Edit post">
                                            ✏️
                                        </a>
                                        
                                        <a href="<?php echo get_permalink($post_id); ?>" 
                                           class="action-btn view-btn" 
                                           title="View post" 
                                           target="_blank">
                                            👁️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="kata-pagination">
                    <div class="pagination-info">
                        Showing <?php echo (($current_page - 1) * $posts_per_page) + 1; ?> to 
                        <?php echo min($current_page * $posts_per_page, $total_posts); ?> of 
                        <?php echo $total_posts; ?> posts
                    </div>
                    
                    <div class="pagination-links">
                        <?php
                        $base_url = admin_url('admin.php?page=kata-seo-content');
                        $url_params = array_filter(array(
                            's' => $search,
                            'filter' => $filter !== 'all' ? $filter : null,
                            'orderby' => $orderby !== 'date' ? $orderby : null,
                            'order' => $order !== 'desc' ? $order : null
                        ));

                        // Previous page
                        if ($current_page > 1):
                            $prev_url = add_query_arg(array_merge($url_params, array('paged' => $current_page - 1)), $base_url);
                        ?>
                            <a href="<?php echo esc_url($prev_url); ?>" class="pagination-link prev">‹ Previous</a>
                        <?php endif; ?>

                        <?php
                        // Page numbers
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);

                        for ($i = $start_page; $i <= $end_page; $i++):
                            $page_url = add_query_arg(array_merge($url_params, array('paged' => $i)), $base_url);
                            $class = $i === $current_page ? 'pagination-link current' : 'pagination-link';
                        ?>
                            <a href="<?php echo esc_url($page_url); ?>" class="<?php echo esc_attr($class); ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                        <?php endfor; ?>

                        <?php
                        // Next page
                        if ($current_page < $total_pages):
                            $next_url = add_query_arg(array_merge($url_params, array('paged' => $current_page + 1)), $base_url);
                        ?>
                            <a href="<?php echo esc_url($next_url); ?>" class="pagination-link next">Next ›</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">📝</div>
                <h3>No posts found</h3>
                <p>
                    <?php if ($search): ?>
                        No posts match your search criteria. Try adjusting your search terms or filters.
                    <?php else: ?>
                        No posts available for analysis. 
                        <a href="<?php echo admin_url('post-new.php'); ?>">Create your first post</a> to get started.
                    <?php endif; ?>
                </p>
                <?php if ($search || $filter !== 'all'): ?>
                    <a href="<?php echo admin_url('admin.php?page=kata-seo-content'); ?>" class="kata-btn kata-btn-secondary">
                        Clear Filters
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Analysis Progress Modal -->
    <div id="analysis-progress-modal" class="kata-modal-overlay" style="display: none;">
        <div class="kata-modal">
            <div class="kata-modal-header">
                <h3>Analysis in Progress</h3>
            </div>
            <div class="kata-modal-body">
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <div class="progress-text">Starting analysis...</div>
                </div>
                <div class="progress-details">
                    <div class="current-post">Analyzing: <span class="post-title">--</span></div>
                    <div class="progress-stats">
                        <span class="completed">0</span> of <span class="total">0</span> posts completed
                    </div>
                </div>
            </div>
            <div class="kata-modal-footer">
                <button class="kata-btn kata-btn-secondary cancel-analysis">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Content-specific styles -->
<style>
.kata-seo-content .kata-filters {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kata-filters .filter-section {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.kata-filters .search-box {
    position: relative;
    flex: 1;
    min-width: 250px;
}

.kata-filters .search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.kata-filters .bulk-actions {
    display: flex;
    gap: 10px;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.posts-table-wrapper {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.posts-table {
    width: 100%;
    border-collapse: collapse;
}

.posts-table th,
.posts-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.posts-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
}

.posts-table tbody tr:hover {
    background: #f8f9fa;
}

.post-title-section .post-title {
    display: block;
    font-weight: 500;
    color: #0073aa;
    text-decoration: none;
    margin-bottom: 5px;
}

.post-title-section .post-title:hover {
    color: #005a87;
}

.post-title-section .post-meta {
    font-size: 12px;
    color: #666;
}

.post-meta span {
    margin-right: 10px;
}

.post-status {
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.post-status.status-publish {
    background: #d4edda;
    color: #155724;
}

.post-status.status-draft {
    background: #fff3cd;
    color: #856404;
}

.seo-score {
    display: flex;
    align-items: center;
    gap: 5px;
}

.score-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 12px;
}

.seo-score.score-excellent .score-circle {
    background: #28a745;
}

.seo-score.score-good .score-circle {
    background: #17a2b8;
}

.seo-score.score-fair .score-circle {
    background: #ffc107;
    color: #333;
}

.seo-score.score-poor .score-circle {
    background: #dc3545;
}

.seo-score.not-analyzed .score-text {
    color: #666;
    font-size: 12px;
}

.issues-indicator {
    display: flex;
    align-items: center;
    gap: 5px;
}

.issues-count {
    background: #dc3545;
    color: white;
    padding: 2px 6px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.keyword-tag {
    background: #667eea;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 4px;
    background: #f8f9fa;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
}

.action-btn:hover {
    background: #e9ecef;
    transform: translateY(-1px);
}

.kata-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: white;
    border-radius: 8px;
    margin-top: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.pagination-links {
    display: flex;
    gap: 5px;
}

.pagination-link {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    transition: all 0.2s ease;
}

.pagination-link:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
}

.pagination-link.current {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.empty-state-icon {
    font-size: 48px;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #333;
}

.empty-state p {
    color: #666;
    margin-bottom: 20px;
}

#analysis-progress-modal .progress-container {
    margin: 20px 0;
}

#analysis-progress-modal .progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}

#analysis-progress-modal .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

#analysis-progress-modal .progress-text {
    font-weight: 500;
    text-align: center;
}

#analysis-progress-modal .progress-details {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

#analysis-progress-modal .current-post {
    font-weight: 500;
    margin-bottom: 5px;
}

#analysis-progress-modal .progress-stats {
    font-size: 14px;
    color: #666;
}
</style>

<script>
// Content analysis specific JavaScript
jQuery(document).ready(function($) {
    // Select all functionality
    $('.select-all-checkbox').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.post-checkbox').prop('checked', isChecked);
    });

    // Individual checkbox changes
    $(document).on('change', '.post-checkbox', function() {
        var totalCheckboxes = $('.post-checkbox').length;
        var checkedCheckboxes = $('.post-checkbox:checked').length;
        
        $('.select-all-checkbox').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Apply bulk actions
    $('.apply-bulk-action').on('click', function() {
        var action = $('.bulk-action-select').val();
        var selectedPosts = $('.post-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (!action) {
            alert('Please select an action');
            return;
        }

        if (selectedPosts.length === 0) {
            alert('Please select at least one post');
            return;
        }

        switch (action) {
            case 'analyze':
                KataSEO.bulkAnalyzeWithProgress(selectedPosts);
                break;
            case 'get_ai_suggestions':
                KataSEO.bulkGetAISuggestions(selectedPosts);
                break;
            case 'export':
                KataSEO.exportSelectedPosts(selectedPosts);
                break;
            case 'delete_analysis':
                if (confirm('Are you sure you want to delete analysis data for selected posts?')) {
                    KataSEO.bulkDeleteAnalysis(selectedPosts);
                }
                break;
        }
    });

    // Real-time search
    var searchTimeout;
    $('.search-posts').on('input', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val();
        
        searchTimeout = setTimeout(function() {
            var currentUrl = new URL(window.location);
            if (query) {
                currentUrl.searchParams.set('s', query);
            } else {
                currentUrl.searchParams.delete('s');
            }
            currentUrl.searchParams.delete('paged'); // Reset to first page
            window.location.href = currentUrl.toString();
        }, 500);
    });

    // Filter changes
    $('.filter-posts, select[name="post_type"], select[name="orderby"], select[name="order"]').on('change', function() {
        var currentUrl = new URL(window.location);
        
        // Update URL parameters
        $('select[name], input[name]').each(function() {
            var name = $(this).attr('name');
            var value = $(this).val();
            
            if (value && value !== 'all' && value !== '') {
                currentUrl.searchParams.set(name, value);
            } else {
                currentUrl.searchParams.delete(name);
            }
        });
        
        currentUrl.searchParams.delete('paged'); // Reset to first page
        window.location.href = currentUrl.toString();
    });
});

// Extended KataSEO functions for content analysis
if (typeof KataSEO !== 'undefined') {
    KataSEO.bulkAnalyzeWithProgress = function(postIds) {
        this.showProgressModal(postIds.length);
        this.processBulkAnalysisWithProgress(postIds, 0);
    };

    KataSEO.showProgressModal = function(total) {
        $('#analysis-progress-modal').show();
        $('#analysis-progress-modal .total').text(total);
        $('#analysis-progress-modal .completed').text(0);
        $('#analysis-progress-modal .progress-fill').css('width', '0%');
    };

    KataSEO.processBulkAnalysisWithProgress = function(postIds, currentIndex) {
        if (currentIndex >= postIds.length) {
            $('#analysis-progress-modal').hide();
            this.showNotification('Bulk analysis completed!', 'success');
            location.reload();
            return;
        }

        var postId = postIds[currentIndex];
        var progress = ((currentIndex + 1) / postIds.length) * 100;
        
        // Update progress
        $('#analysis-progress-modal .progress-fill').css('width', progress + '%');
        $('#analysis-progress-modal .completed').text(currentIndex + 1);
        $('#analysis-progress-modal .progress-text').text('Analyzing post ' + (currentIndex + 1) + ' of ' + postIds.length);
        
        // Get post title and update display
        var postTitle = $('.post-row[data-post-id="' + postId + '"] .post-title').text() || 'Unknown Post';
        $('#analysis-progress-modal .post-title').text(postTitle);

        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_analyze_content',
                post_id: postId,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                setTimeout(function() {
                    KataSEO.processBulkAnalysisWithProgress(postIds, currentIndex + 1);
                }, 1000);
            },
            error: function() {
                KataSEO.showNotification('Analysis failed for post: ' + postTitle, 'error');
                setTimeout(function() {
                    KataSEO.processBulkAnalysisWithProgress(postIds, currentIndex + 1);
                }, 1000);
            }
        });
    };

    // Cancel analysis
    $(document).on('click', '.cancel-analysis', function() {
        $('#analysis-progress-modal').hide();
        KataSEO.showNotification('Analysis cancelled', 'info');
    });
}
</script>
