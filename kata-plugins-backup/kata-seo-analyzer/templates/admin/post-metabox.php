<?php
/**
 * Post Edit Metabox Template
 * 
 * @package KataSEOAnalyzer
 * @since 2.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

global $post;
$post_id = isset($post->ID) ? $post->ID : 1;

// Get existing analysis data
$analyzer = KataSEO_Analyzer::get_instance();
$existing_analysis = $analyzer->getPostAnalysis($post_id);

// Get focus keyword
$focus_keyword = get_post_meta($post_id, '_kata_seo_focus_keyword', true);

// Get AI suggestions if available
$ai_suggestions = get_option('kata_seo_ai_enabled', false) ? 
    get_post_meta($post_id, '_kata_seo_ai_suggestions', true) : array();

// Enqueue necessary scripts
wp_enqueue_script('kata-seo-analyzer');
wp_enqueue_style('kata-seo-admin');
?>

<div class="kata-seo-metabox">
    <!-- Header with Score -->
    <div class="kata-metabox-header">
        <div class="score-display">
            <div class="seo-score <?php echo $existing_analysis ? esc_attr($analyzer->get_score_class($existing_analysis['score'])) : 'not-analyzed'; ?>">
                <div class="score-circle">
                    <span class="score-number" id="current-seo-score">
                        <?php echo $existing_analysis ? esc_html($existing_analysis['score']) : '--'; ?>
                    </span>
                </div>
                <div class="score-label">SEO Score</div>
            </div>
        </div>
        
        <div class="header-actions">
            <button type="button" class="kata-btn kata-btn-primary analyze-current-post" 
                    data-post-id="<?php echo esc_attr($post_id); ?>">
                🔍 Analyze Now
            </button>
            
            <?php if (get_option('kata_seo_ai_enabled', false)): ?>
            <button type="button" class="kata-btn kata-btn-secondary get-ai-suggestions-btn" 
                    data-post-id="<?php echo esc_attr($post_id); ?>">
                🤖 Get AI Suggestions
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Focus Keyword Section -->
    <div class="kata-metabox-section">
        <h4 class="section-title">Focus Keyword</h4>
        <div class="focus-keyword-container">
            <input type="text" id="kata-focus-keyword" name="kata_focus_keyword" 
                   class="kata-input focus-keyword-input" 
                   value="<?php echo esc_attr($focus_keyword); ?>"
                   placeholder="Enter your target keyword">
            <button type="button" class="kata-btn kata-btn-small research-keywords">
                Research
            </button>
        </div>
        
        <div id="keyword-suggestions" class="keyword-suggestions" style="display: none;">
            <!-- Keyword suggestions will be populated here -->
        </div>
        
        <?php if ($focus_keyword): ?>
        <div class="keyword-analysis">
            <div class="keyword-metrics">
                <div class="metric">
                    <span class="metric-label">Density:</span>
                    <span class="metric-value" id="keyword-density">--</span>
                </div>
                <div class="metric">
                    <span class="metric-label">Occurrences:</span>
                    <span class="metric-value" id="keyword-count">--</span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Analysis Results -->
    <div class="kata-metabox-section">
        <h4 class="section-title">Content Analysis</h4>
        
        <div id="analysis-results" class="analysis-results">
            <?php if ($existing_analysis && !empty($existing_analysis['analysis'])): ?>
                <?php foreach ($existing_analysis['analysis'] as $factor => $data): ?>
                    <div class="analysis-factor <?php echo $data['status'] ? 'good' : 'needs-improvement'; ?>" 
                         data-factor="<?php echo esc_attr($factor); ?>">
                        <div class="factor-header">
                            <span class="factor-icon"><?php echo $data['status'] ? '✅' : '❌'; ?></span>
                            <span class="factor-name"><?php echo esc_html($data['name']); ?></span>
                            <span class="factor-score"><?php echo esc_html($data['score']); ?>/100</span>
                        </div>
                        
                        <div class="factor-details">
                            <div class="factor-current">
                                Current: <?php echo esc_html($data['current']); ?>
                            </div>
                            <div class="factor-target">
                                Target: <?php echo esc_html($data['target']); ?>
                            </div>
                            
                            <?php if (!$data['status'] && !empty($data['suggestion'])): ?>
                            <div class="factor-suggestion">
                                💡 <?php echo esc_html($data['suggestion']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-analysis">
                    <p>Click "Analyze Now" to see detailed SEO analysis for this content.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Readability Analysis -->
    <div class="kata-metabox-section">
        <h4 class="section-title">Readability</h4>
        
        <div id="readability-results" class="readability-results">
            <div class="readability-score">
                <span class="score-label">Reading Level:</span>
                <span class="score-value" id="readability-level">
                    <?php echo $existing_analysis && isset($existing_analysis['readability']) ? 
                        esc_html($existing_analysis['readability']['level']) : '--'; ?>
                </span>
            </div>
            
            <div class="readability-factors" id="readability-factors">
                <?php if ($existing_analysis && !empty($existing_analysis['readability']['factors'])): ?>
                    <?php foreach ($existing_analysis['readability']['factors'] as $factor => $data): ?>
                        <div class="readability-factor <?php echo $data['status'] ? 'good' : 'needs-improvement'; ?>">
                            <span class="factor-icon"><?php echo $data['status'] ? '✅' : '⚠️'; ?></span>
                            <span class="factor-name"><?php echo esc_html(ucwords(str_replace('_', ' ', $factor))); ?></span>
                            <span class="factor-value"><?php echo esc_html($data['value']); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- AI Suggestions -->
    <?php if (get_option('kata_seo_ai_enabled', false)): ?>
    <div class="kata-metabox-section" id="ai-suggestions-section" 
         style="<?php echo empty($ai_suggestions) ? 'display: none;' : ''; ?>">
        <h4 class="section-title">AI Suggestions</h4>
        
        <div id="ai-suggestions-content" class="ai-suggestions-content">
            <?php if (!empty($ai_suggestions)): ?>
                <?php foreach ($ai_suggestions as $index => $suggestion): ?>
                    <div class="ai-suggestion" data-suggestion-id="<?php echo esc_attr($index); ?>">
                        <div class="suggestion-priority priority-<?php echo esc_attr($suggestion['priority']); ?>">
                            <?php echo esc_html(ucfirst($suggestion['priority'])); ?>
                        </div>
                        <div class="suggestion-content">
                            <h5 class="suggestion-title"><?php echo esc_html($suggestion['title']); ?></h5>
                            <p class="suggestion-description"><?php echo esc_html($suggestion['description']); ?></p>
                            
                            <?php if (!empty($suggestion['action'])): ?>
                            <div class="suggestion-actions">
                                <button type="button" class="kata-btn kata-btn-small apply-suggestion" 
                                        data-action="<?php echo esc_attr($suggestion['action']); ?>"
                                        data-suggestion="<?php echo esc_attr($index); ?>">
                                    Apply
                                </button>
                                <button type="button" class="kata-btn kata-btn-small kata-btn-ghost dismiss-suggestion" 
                                        data-suggestion="<?php echo esc_attr($index); ?>">
                                    Dismiss
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Meta Information -->
    <div class="kata-metabox-section">
        <h4 class="section-title">Meta Information</h4>
        
        <div class="meta-fields">
            <div class="meta-field">
                <label for="kata-meta-title" class="meta-label">Meta Title</label>
                <input type="text" id="kata-meta-title" name="kata_meta_title" 
                       class="kata-input meta-title-input" 
                       value="<?php echo esc_attr(get_post_meta($post_id, '_kata_seo_meta_title', true)); ?>"
                       placeholder="<?php echo esc_attr($post->post_title); ?>">
                <div class="meta-info">
                    <span class="character-count" id="meta-title-count">0</span> / 60 characters
                </div>
            </div>
            
            <div class="meta-field">
                <label for="kata-meta-description" class="meta-label">Meta Description</label>
                <textarea id="kata-meta-description" name="kata_meta_description" 
                          class="kata-textarea meta-description-input" rows="3"
                          placeholder="Enter a compelling description for search results..."><?php 
                    echo esc_textarea(get_post_meta($post_id, '_kata_seo_meta_description', true)); 
                ?></textarea>
                <div class="meta-info">
                    <span class="character-count" id="meta-description-count">0</span> / 160 characters
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="kata-metabox-section">
        <h4 class="section-title">Quick Actions</h4>
        
        <div class="quick-actions">
            <button type="button" class="kata-btn kata-btn-secondary check-competitors">
                🎯 Check Competitors
            </button>
            
            <button type="button" class="kata-btn kata-btn-secondary generate-outline">
                📋 Generate Outline
            </button>
            
            <button type="button" class="kata-btn kata-btn-secondary preview-serp">
                👁️ Preview SERP
            </button>
            
            <?php if ($existing_analysis): ?>
            <button type="button" class="kata-btn kata-btn-secondary export-analysis">
                📊 Export Analysis
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Analysis History -->
    <?php
    $analysis_history = $analyzer->get_analysis_history($post_id, 5);
    if (!empty($analysis_history)):
    ?>
    <div class="kata-metabox-section">
        <h4 class="section-title">Analysis History</h4>
        
        <div class="analysis-history">
            <?php foreach ($analysis_history as $history_item): ?>
                <div class="history-item">
                    <div class="history-score score-<?php echo esc_attr($analyzer->get_score_class($history_item->score)); ?>">
                        <?php echo esc_html($history_item->score); ?>
                    </div>
                    <div class="history-details">
                        <div class="history-date">
                            <?php 
                            $created_at = $history_item->created_at ?? null;
                            if ($created_at) {
                                echo esc_html(date('M j, Y g:i A', strtotime($created_at)));
                            } else {
                                echo esc_html__('Unknown date', 'kata-seo-analyzer');
                            }
                            ?>
                        </div>
                        <div class="history-changes">
                            <?php if ($history_item->changes_count > 0): ?>
                                <?php echo esc_html($history_item->changes_count); ?> changes detected
                            <?php else: ?>
                                No changes
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="kata-btn kata-btn-small view-history-details" 
                            data-analysis-id="<?php echo esc_attr($history_item->id); ?>">
                        View
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Hidden fields for saving data -->
<input type="hidden" name="kata_seo_nonce" value="<?php echo wp_create_nonce('kata_seo_save_meta'); ?>">
<input type="hidden" id="kata-analysis-data" name="kata_analysis_data" value="">

<style>
.kata-seo-metabox {
    background: #f8f9fa;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    overflow: hidden;
}

.kata-metabox-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: white;
    border-bottom: 1px solid #e5e5e5;
}

.score-display .seo-score {
    display: flex;
    align-items: center;
    gap: 15px;
}

.score-display .score-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 18px;
}

.score-display .score-label {
    font-weight: 500;
    color: #333;
}

.seo-score.score-excellent .score-circle { background: #28a745; }
.seo-score.score-good .score-circle { background: #17a2b8; }
.seo-score.score-fair .score-circle { background: #ffc107; color: #333; }
.seo-score.score-poor .score-circle { background: #dc3545; }
.seo-score.not-analyzed .score-circle { background: #6c757d; }

.header-actions {
    display: flex;
    gap: 10px;
}

.kata-metabox-section {
    padding: 20px;
    border-bottom: 1px solid #e5e5e5;
    background: white;
}

.kata-metabox-section:last-child {
    border-bottom: none;
}

.section-title {
    margin: 0 0 15px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.focus-keyword-container {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.focus-keyword-container .kata-input {
    flex: 1;
}

.keyword-suggestions {
    background: #f8f9fa;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
    padding: 10px;
    margin-top: 10px;
}

.keyword-suggestion {
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.keyword-suggestion:hover {
    background: #e9ecef;
}

.suggestion-volume {
    font-size: 12px;
    color: #666;
}

.keyword-analysis {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}

.keyword-metrics {
    display: flex;
    gap: 20px;
}

.metric {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.metric-label {
    font-size: 12px;
    color: #666;
}

.metric-value {
    font-weight: 500;
    color: #333;
}

.analysis-factor {
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 6px;
    border-left: 4px solid #ddd;
}

.analysis-factor.good {
    background: #d4edda;
    border-left-color: #28a745;
}

.analysis-factor.needs-improvement {
    background: #f8d7da;
    border-left-color: #dc3545;
}

.factor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.factor-name {
    font-weight: 500;
    color: #333;
}

.factor-score {
    font-size: 12px;
    color: #666;
}

.factor-details {
    font-size: 13px;
    color: #666;
}

.factor-current,
.factor-target {
    margin-bottom: 4px;
}

.factor-suggestion {
    margin-top: 8px;
    padding: 8px;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 4px;
    font-style: italic;
}

.readability-results {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.readability-score {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.readability-factor {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.readability-factor:last-child {
    border-bottom: none;
}

.ai-suggestion {
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    background: white;
}

.suggestion-priority {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.suggestion-priority.priority-high {
    background: #dc3545;
    color: white;
}

.suggestion-priority.priority-medium {
    background: #ffc107;
    color: #333;
}

.suggestion-priority.priority-low {
    background: #28a745;
    color: white;
}

.suggestion-title {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: 500;
}

.suggestion-description {
    margin: 0 0 12px 0;
    font-size: 13px;
    color: #666;
    line-height: 1.4;
}

.suggestion-actions {
    display: flex;
    gap: 8px;
}

.meta-fields {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.meta-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.meta-label {
    font-weight: 500;
    color: #333;
    font-size: 13px;
}

.meta-info {
    font-size: 12px;
    color: #666;
    text-align: right;
}

.character-count {
    font-weight: 500;
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

.analysis-history {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
}

.history-score {
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

.history-details {
    flex: 1;
}

.history-date {
    font-weight: 500;
    font-size: 13px;
}

.history-changes {
    font-size: 12px;
    color: #666;
}

.no-analysis {
    text-align: center;
    padding: 30px;
    color: #666;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize content analyzer for this post
    if (typeof KataContentAnalyzer !== 'undefined') {
        KataContentAnalyzer.init();
    }

    // Character counters for meta fields
    function updateCharacterCount() {
        var titleLength = $('#kata-meta-title').val().length;
        var descLength = $('#kata-meta-description').val().length;
        
        $('#meta-title-count').text(titleLength);
        $('#meta-description-count').text(descLength);
        
        // Update colors based on length
        if (titleLength > 60) {
            $('#meta-title-count').css('color', '#dc3545');
        } else if (titleLength < 30) {
            $('#meta-title-count').css('color', '#ffc107');
        } else {
            $('#meta-title-count').css('color', '#28a745');
        }
        
        if (descLength > 160) {
            $('#meta-description-count').css('color', '#dc3545');
        } else if (descLength < 120) {
            $('#meta-description-count').css('color', '#ffc107');
        } else {
            $('#meta-description-count').css('color', '#28a745');
        }
    }

    $('#kata-meta-title, #kata-meta-description').on('input', updateCharacterCount);
    updateCharacterCount(); // Initial count

    // Analyze current post
    $('.analyze-current-post').on('click', function() {
        var $button = $(this);
        var postId = $button.data('post-id');
        var originalText = $button.text();

        $button.prop('disabled', true).html('<span class="spinner is-active"></span> Analyzing...');

        // Trigger real-time analysis
        if (typeof KataContentAnalyzer !== 'undefined') {
            KataContentAnalyzer.analyzeRealTime();
        }

        // Also send AJAX request for server-side analysis
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_analyze_content',
                post_id: postId,
                nonce: $('#kata_seo_nonce').val()
            },
            success: function(response) {
                if (response.success) {
                    location.reload(); // Refresh to show updated analysis
                } else {
                    alert('Analysis failed: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('Analysis request failed');
            },
            complete: function() {
                $button.prop('disabled', false).text(originalText);
            }
        });
    });

    // Research keywords
    $('.research-keywords').on('click', function() {
        var keyword = $('#kata-focus-keyword').val().trim();
        
        if (!keyword) {
            alert('Please enter a focus keyword first');
            return;
        }

        if (typeof KataContentAnalyzer !== 'undefined') {
            KataContentAnalyzer.getKeywordSuggestions(keyword);
        }
    });

    // Preview SERP
    $('.preview-serp').on('click', function() {
        var title = $('#kata-meta-title').val() || $('#title').val() || $('input[name="post_title"]').val();
        var description = $('#kata-meta-description').val();
        var url = $('#sample-permalink a').attr('href') || window.location.href;

        // Create SERP preview modal
        var modal = `
            <div class="kata-modal-overlay">
                <div class="kata-modal serp-preview-modal">
                    <div class="kata-modal-header">
                        <h3>Search Result Preview</h3>
                        <button class="kata-modal-close">&times;</button>
                    </div>
                    <div class="kata-modal-body">
                        <div class="serp-preview">
                            <div class="serp-title">${title}</div>
                            <div class="serp-url">${url}</div>
                            <div class="serp-description">${description}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modal);
    });

    // Modal close functionality
    $(document).on('click', '.kata-modal-close, .kata-modal-overlay', function(e) {
        if (e.target === this) {
            $('.kata-modal-overlay').remove();
        }
    });
});
</script>
