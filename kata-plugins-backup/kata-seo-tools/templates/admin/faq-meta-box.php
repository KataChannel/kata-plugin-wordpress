<?php
/**
 * FAQ Meta Box Template
 * 
 * @package Kata_SEO_Tools
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="kata-meta-box kata-faq-meta-box">
    <div class="kata-faq-intro">
        <p>Add Frequently Asked Questions to generate FAQ Schema markup for better SEO and rich snippets in search results.</p>
    </div>

    <div class="kata-faq-list">
        <?php if (!empty($faqs) && is_array($faqs)) : ?>
            <?php foreach ($faqs as $index => $faq) : ?>
                <div class="kata-faq-item" data-index="<?php echo esc_attr($index); ?>">
                    <div class="kata-faq-header">
                        <span class="kata-faq-handle dashicons dashicons-move"></span>
                        <span class="kata-faq-number">Q<?php echo $index + 1; ?></span>
                        <input type="text" 
                               name="kata_seo_faqs[<?php echo $index; ?>][question]" 
                               value="<?php echo esc_attr($faq['question'] ?? ''); ?>" 
                               placeholder="Enter question..." 
                               class="widefat kata-faq-question">
                        <button type="button" class="button kata-faq-toggle">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </button>
                        <button type="button" class="button kata-faq-remove">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                    <div class="kata-faq-body" style="display: none;">
                        <textarea name="kata_seo_faqs[<?php echo $index; ?>][answer]" 
                                  rows="4" 
                                  placeholder="Enter answer..." 
                                  class="widefat kata-faq-answer"><?php echo esc_textarea($faq['answer'] ?? ''); ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="kata-faq-actions">
        <button type="button" class="button button-primary kata-faq-add">
            <span class="dashicons dashicons-plus-alt"></span> Add FAQ
        </button>
        <p class="description" style="margin-top: 10px;">
            FAQs will be displayed using the <code>[kata_faq]</code> shortcode and will generate FAQ Schema markup automatically.
        </p>
    </div>
</div>

<!-- FAQ Item Template -->
<script type="text/template" id="kata-faq-template">
    <div class="kata-faq-item" data-index="{{INDEX}}">
        <div class="kata-faq-header">
            <span class="kata-faq-handle dashicons dashicons-move"></span>
            <span class="kata-faq-number">Q{{NUMBER}}</span>
            <input type="text" 
                   name="kata_seo_faqs[{{INDEX}}][question]" 
                   value="" 
                   placeholder="Enter question..." 
                   class="widefat kata-faq-question">
            <button type="button" class="button kata-faq-toggle">
                <span class="dashicons dashicons-arrow-down-alt2"></span>
            </button>
            <button type="button" class="button kata-faq-remove">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
        <div class="kata-faq-body" style="display: none;">
            <textarea name="kata_seo_faqs[{{INDEX}}][answer]" 
                      rows="4" 
                      placeholder="Enter answer..." 
                      class="widefat kata-faq-answer"></textarea>
        </div>
    </div>
</script>

<style>
.kata-faq-meta-box {
    padding: 10px 0;
}
.kata-faq-intro {
    margin-bottom: 15px;
    padding: 10px;
    background: #f0f6fc;
    border-left: 4px solid #0073aa;
}
.kata-faq-list {
    margin-bottom: 15px;
}
.kata-faq-item {
    background: #fff;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    border-radius: 4px;
    transition: box-shadow 0.2s;
}
.kata-faq-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.kata-faq-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px;
    background: #f9f9f9;
    border-bottom: 1px solid #ddd;
}
.kata-faq-handle {
    cursor: move;
    color: #999;
}
.kata-faq-number {
    font-weight: 600;
    color: #0073aa;
    min-width: 30px;
}
.kata-faq-question {
    flex: 1;
    font-weight: 500;
}
.kata-faq-toggle .dashicons {
    transition: transform 0.2s;
}
.kata-faq-toggle.active .dashicons {
    transform: rotate(180deg);
}
.kata-faq-body {
    padding: 10px;
}
.kata-faq-answer {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
}
.kata-faq-actions {
    padding-top: 10px;
    border-top: 1px solid #ddd;
}
.kata-faq-add .dashicons {
    vertical-align: middle;
}
.kata-faq-remove {
    color: #dc3232;
}
.kata-faq-remove:hover {
    background: #dc3232;
    color: #fff;
    border-color: #dc3232;
}

/* Sortable placeholder */
.kata-faq-list .ui-sortable-placeholder {
    background: #f0f6fc;
    border: 2px dashed #0073aa;
    visibility: visible !important;
    height: 60px;
    margin-bottom: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    var faqIndex = $('.kata-faq-item').length;
    
    // Add FAQ
    $('.kata-faq-add').on('click', function() {
        var template = $('#kata-faq-template').html();
        var html = template
            .replace(/\{\{INDEX\}\}/g, faqIndex)
            .replace(/\{\{NUMBER\}\}/g, faqIndex + 1);
        
        $('.kata-faq-list').append(html);
        faqIndex++;
        
        // Update numbers
        updateFaqNumbers();
    });
    
    // Remove FAQ
    $(document).on('click', '.kata-faq-remove', function() {
        if (confirm('Are you sure you want to remove this FAQ?')) {
            $(this).closest('.kata-faq-item').fadeOut(300, function() {
                $(this).remove();
                updateFaqNumbers();
            });
        }
    });
    
    // Toggle FAQ body
    $(document).on('click', '.kata-faq-toggle', function() {
        var button = $(this);
        var body = button.closest('.kata-faq-item').find('.kata-faq-body');
        
        body.slideToggle(200);
        button.toggleClass('active');
    });
    
    // Make sortable
    if ($.fn.sortable) {
        $('.kata-faq-list').sortable({
            handle: '.kata-faq-handle',
            placeholder: 'ui-sortable-placeholder',
            axis: 'y',
            update: function() {
                updateFaqNumbers();
            }
        });
    }
    
    // Update FAQ numbers
    function updateFaqNumbers() {
        $('.kata-faq-item').each(function(index) {
            $(this).find('.kata-faq-number').text('Q' + (index + 1));
            $(this).attr('data-index', index);
            
            // Update input names
            $(this).find('.kata-faq-question').attr('name', 'kata_seo_faqs[' + index + '][question]');
            $(this).find('.kata-faq-answer').attr('name', 'kata_seo_faqs[' + index + '][answer]');
        });
    }
});
</script>
