<?php
/**
 * FAQ Shortcode
 * 
 * [kata_faq]
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.3
 */

if (!defined('ABSPATH')) {
    exit;
}

class KATA_SEO_Shortcode_FAQ extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_faq';
    protected $schema_type = 'faq';
    
    /**
     * Render shortcode
     */
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, array(
            'questions' => '',
            'answers' => '',
            'show_output' => 'true',
            'accordion' => 'true'
        ));
        
        // Parse questions and answers
        $questions = array_filter(array_map('trim', explode('|', $atts['questions'])));
        $answers = array_filter(array_map('trim', explode('|', $atts['answers'])));
        
        if (empty($questions) || empty($answers)) {
            $this->log_error('Questions or answers are missing');
            return '';
        }
        
        // Build FAQ items
        $faq_items = array();
        $count = min(count($questions), count($answers));
        
        for ($i = 0; $i < $count; $i++) {
            $faq_items[] = array(
                'question' => $questions[$i],
                'answer' => $answers[$i]
            );
        }
        
        // Generate and store schema
        $schema = $this->generate_schema(array('faq_items' => $faq_items));
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        // Return HTML output if requested
        if ($atts['show_output'] === 'true') {
            $accordion = ($atts['accordion'] === 'true');
            
            ob_start();
            ?>
            <div class="kata-faq <?php echo $accordion ? 'kata-faq-accordion' : ''; ?>" itemscope itemtype="https://schema.org/FAQPage">
                <?php foreach ($faq_items as $index => $item): ?>
                    <div class="kata-faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                        <h3 class="kata-faq-question" itemprop="name">
                            <?php echo esc_html($item['question']); ?>
                            <?php if ($accordion): ?>
                                <span class="kata-faq-toggle">▼</span>
                            <?php endif; ?>
                        </h3>
                        <div class="kata-faq-answer <?php echo $accordion ? 'kata-faq-collapse' : ''; ?>" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                            <div itemprop="text">
                                <?php echo wpautop($item['answer']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ($accordion): ?>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.kata-faq-question').forEach(function(question) {
                        question.addEventListener('click', function() {
                            var answer = this.nextElementSibling;
                            var toggle = this.querySelector('.kata-faq-toggle');
                            
                            if (answer.classList.contains('kata-faq-collapse')) {
                                answer.classList.remove('kata-faq-collapse');
                                toggle.textContent = '▲';
                            } else {
                                answer.classList.add('kata-faq-collapse');
                                toggle.textContent = '▼';
                            }
                        });
                    });
                });
                </script>
            <?php endif; ?>
            <?php
            return ob_get_clean();
        }
        
        return '';
    }
}
