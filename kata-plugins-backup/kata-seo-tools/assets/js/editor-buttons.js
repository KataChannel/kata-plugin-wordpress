/**
 * KATA SEO Tools - Editor Quick Insert Buttons
 * Add quick insert buttons for shortcode samples
 */

(function($) {
    'use strict';

    // Template samples cho từng loại shortcode
    const kataTemplates = {
        faq: `<!-- FAQ Sample - Copy và paste vào post -->
<h2>Câu hỏi thường gặp</h2>

[kata_faq]

<!-- Sau khi paste, vào phần FAQ Meta Box bên dưới editor để thêm câu hỏi -->
<!-- Ví dụ: 
Câu hỏi 1: KATA SEO Tools là gì?
Trả lời 1: KATA SEO Tools là plugin WordPress giúp tối ưu SEO...
-->`,

        quiz: `<!-- Quiz Sample - Interactive Quiz -->
[kata_quiz 
    title="Kiểm tra kiến thức SEO của bạn"
    questions="3"
    show_results="yes"
    pass_score="70"
]

<!-- Câu hỏi sẽ được tự động load từ database -->
<!-- Vào Dashboard > KATA SEO > Quizzes để tạo câu hỏi -->`,

        poll: `<!-- Poll Sample - Vote Poll -->
[kata_poll 
    question="Bạn thích tính năng nào của KATA SEO Tools nhất?"
    option1="Schema Markup"
    option2="FAQ Generator"
    option3="Social Share"
    option4="Analytics Dashboard"
    show_results="after_vote"
]

<!-- Poll sẽ lưu votes vào database và hiển thị kết quả real-time -->`,

        wheel: `<!-- Wheel Sample - Lucky Wheel -->
[kata_wheel 
    prizes="Giảm 10%, Giảm 20%, Giảm 30%, Gift Free, Try Again, Gift VIP"
    button_text="Quay thưởng ngay!"
    colors="#FF6B6B, #4ECDC4, #45B7D1, #FFA07A, #98D8C8, #F7DC6F"
    require_email="yes"
]

<!-- Wheel với email collection cho marketing campaign -->`,

        rating: `<!-- Rating Sample - Star Rating -->
<h3>Đánh giá plugin KATA SEO Tools</h3>

[kata_rating 
    type="stars"
    max="5"
    allow_half="yes"
    show_count="yes"
    item_id="kata-seo-tools"
]

<!-- Rating được lưu và tính trung bình tự động -->`,

        form: `<!-- Form Sample - Contact/Lead Form -->
[kata_form 
    form_title="Đăng ký nhận tư vấn SEO miễn phí"
    fields="name,email,phone,message"
    required_fields="name,email"
    submit_text="Gửi đăng ký"
    success_message="Cảm ơn! Chúng tôi sẽ liên hệ bạn sớm."
    send_email="admin@example.com"
]

<!-- Form data được lưu vào database và gửi email notification -->`,

        social: `<!-- Social Share Sample -->
<div class="kata-social-share-container">
    <h4>Chia sẻ bài viết này:</h4>
    [kata_social_share 
        platforms="facebook,twitter,linkedin,pinterest,whatsapp"
        style="icons"
        size="medium"
        show_count="yes"
    ]
</div>

<!-- Social share buttons với counter -->`,

        cta: `<!-- CTA Sample - Call to Action -->
[kata_cta 
    title="Tăng traffic website lên 300% với KATA SEO Tools!"
    description="Plugin SEO All-in-One cho WordPress - Dễ dùng, Hiệu quả, Miễn phí"
    button_text="Tải plugin ngay"
    button_url="https://example.com/download"
    style="gradient"
    color="blue"
]

<!-- CTA box với style đẹp và conversion-focused -->`,

        landing: `<!-- Complete Landing Page Sample -->
<div class="kata-landing-page">

    <!-- Hero Section với CTA -->
    [kata_cta 
        title="🚀 KATA SEO Tools - Plugin SEO #1 cho WordPress"
        description="Tối ưu website, tăng traffic tự nhiên, boost ranking Google"
        button_text="Dùng thử miễn phí"
        button_url="#quiz"
        style="gradient"
        color="blue"
    ]

    <!-- Social Proof -->
    <div class="social-proof">
        <h3>⭐ Được tin dùng bởi 10,000+ websites</h3>
        [kata_rating type="stars" max="5" show_count="yes" item_id="kata-landing"]
    </div>

    <!-- Interactive Quiz -->
    <div id="quiz" class="quiz-section">
        <h2>📊 Kiểm tra SEO Score website của bạn</h2>
        [kata_quiz title="SEO Health Check" questions="5" show_results="yes"]
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <h2>❓ Câu hỏi thường gặp</h2>
        [kata_faq]
    </div>

    <!-- Lucky Wheel Lead Gen -->
    <div class="wheel-section">
        <h2>🎁 Quay thưởng - Nhận ưu đãi đặc biệt</h2>
        [kata_wheel 
            prizes="Giảm 50%, Giảm 30%, Giảm 20%, Gift Pro, Try Again, Gift VIP"
            require_email="yes"
        ]
    </div>

    <!-- Poll for Engagement -->
    <div class="poll-section">
        <h2>📈 Bạn đang gặp vấn đề SEO nào?</h2>
        [kata_poll 
            question="Vấn đề SEO lớn nhất của bạn?"
            option1="Traffic thấp"
            option2="Ranking kém"
            option3="Chưa biết SEO"
            option4="Cần tối ưu content"
        ]
    </div>

    <!-- Lead Form -->
    <div class="form-section">
        <h2>📧 Nhận tư vấn SEO miễn phí</h2>
        [kata_form 
            form_title="Để lại thông tin - Chuyên gia sẽ tư vấn trong 24h"
            fields="name,email,phone,website,message"
            required_fields="name,email"
        ]
    </div>

    <!-- Social Share -->
    <div class="share-section">
        <h3>Chia sẻ với bạn bè:</h3>
        [kata_social_share platforms="facebook,twitter,linkedin,pinterest"]
    </div>

</div>

<!-- Thêm CSS tùy chỉnh -->
<style>
.kata-landing-page { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
.kata-landing-page > div { margin: 60px 0; padding: 40px; background: #f9f9f9; border-radius: 10px; }
.social-proof { text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.quiz-section { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.faq-section { background: white; }
.wheel-section { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.poll-section { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.form-section { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.share-section { text-align: center; background: #667eea; color: white; }
</style>`
    };

    // Gutenberg Block Integration
    if (wp && wp.blocks) {
        registerGutenbergBlocks();
    }

    // TinyMCE (Classic Editor) Integration
    if (typeof tinymce !== 'undefined') {
        tinymce.PluginManager.add('kata_seo_buttons', function(editor) {
            
            // Tạo menu dropdown với tất cả templates
            editor.addButton('kata_seo_insert', {
                text: '⚡ KATA Quick Insert',
                icon: false,
                type: 'menubutton',
                menu: [
                    {
                        text: '❓ FAQ Sample',
                        onclick: function() {
                            insertTemplate(editor, 'faq');
                        }
                    },
                    {
                        text: '📝 Quiz Sample',
                        onclick: function() {
                            insertTemplate(editor, 'quiz');
                        }
                    },
                    {
                        text: '📊 Poll Sample',
                        onclick: function() {
                            insertTemplate(editor, 'poll');
                        }
                    },
                    {
                        text: '🎡 Wheel Sample',
                        onclick: function() {
                            insertTemplate(editor, 'wheel');
                        }
                    },
                    {
                        text: '⭐ Rating Sample',
                        onclick: function() {
                            insertTemplate(editor, 'rating');
                        }
                    },
                    {
                        text: '📧 Form Sample',
                        onclick: function() {
                            insertTemplate(editor, 'form');
                        }
                    },
                    {
                        text: '🔗 Social Share Sample',
                        onclick: function() {
                            insertTemplate(editor, 'social');
                        }
                    },
                    {
                        text: '🎯 CTA Sample',
                        onclick: function() {
                            insertTemplate(editor, 'cta');
                        }
                    },
                    {
                        text: '🚀 Complete Landing Page',
                        onclick: function() {
                            if (confirm('Insert complete landing page template?\n\nĐây là template đầy đủ, sẽ thay thế toàn bộ nội dung hiện tại.')) {
                                editor.setContent(kataTemplates.landing);
                            }
                        }
                    }
                ]
            });

            // Helper function để insert template
            function insertTemplate(editor, type) {
                const template = kataTemplates[type];
                if (template) {
                    editor.insertContent(template + '\n\n');
                    
                    // Show notification
                    editor.notificationManager.open({
                        text: '✅ Template inserted! Scroll xuống để xem và edit.',
                        type: 'success',
                        timeout: 3000
                    });
                }
            }
        });
    }

    // Hàm đăng ký Gutenberg Blocks
    function registerGutenbergBlocks() {
        const { registerBlockType } = wp.blocks;
        const { __ } = wp.i18n;
        const { TextControl, SelectControl, ToggleControl } = wp.components;
        const { InspectorControls } = wp.blockEditor;

        // Block 1: FAQ Block
        registerBlockType('kata-seo/faq-block', {
            title: __('KATA FAQ', 'kata-seo-tools'),
            icon: 'editor-help',
            category: 'widgets',
            attributes: {
                postId: { type: 'number' }
            },
            edit: function(props) {
                return wp.element.createElement(
                    'div',
                    { className: 'kata-block-placeholder' },
                    wp.element.createElement('span', { className: 'dashicons dashicons-editor-help' }),
                    wp.element.createElement('p', {}, 'KATA FAQ Block'),
                    wp.element.createElement('small', {}, 'Configure trong FAQ meta box')
                );
            },
            save: function() {
                return wp.element.createElement(
                    wp.element.RawHTML,
                    {},
                    '[kata_faq]'
                );
            }
        });

        // Block 2: Quiz Block
        registerBlockType('kata-seo/quiz-block', {
            title: __('KATA Quiz', 'kata-seo-tools'),
            icon: 'welcome-learn-more',
            category: 'widgets',
            attributes: {
                title: { type: 'string', default: 'Quiz' },
                questions: { type: 'string', default: '5' },
                showResults: { type: 'boolean', default: true }
            },
            edit: function(props) {
                const { attributes, setAttributes } = props;
                
                return wp.element.createElement(
                    wp.element.Fragment,
                    {},
                    wp.element.createElement(
                        InspectorControls,
                        {},
                        wp.element.createElement(
                            TextControl,
                            {
                                label: 'Quiz Title',
                                value: attributes.title,
                                onChange: (value) => setAttributes({ title: value })
                            }
                        ),
                        wp.element.createElement(
                            TextControl,
                            {
                                label: 'Number of Questions',
                                value: attributes.questions,
                                onChange: (value) => setAttributes({ questions: value })
                            }
                        ),
                        wp.element.createElement(
                            ToggleControl,
                            {
                                label: 'Show Results',
                                checked: attributes.showResults,
                                onChange: (value) => setAttributes({ showResults: value })
                            }
                        )
                    ),
                    wp.element.createElement(
                        'div',
                        { className: 'kata-block-placeholder kata-quiz-block' },
                        wp.element.createElement('span', { className: 'dashicons dashicons-welcome-learn-more' }),
                        wp.element.createElement('p', {}, 'KATA Quiz: ' + attributes.title),
                        wp.element.createElement('small', {}, attributes.questions + ' questions')
                    )
                );
            },
            save: function(props) {
                const { attributes } = props;
                const shortcode = `[kata_quiz title="${attributes.title}" questions="${attributes.questions}" show_results="${attributes.showResults ? 'yes' : 'no'}"]`;
                return wp.element.createElement(wp.element.RawHTML, {}, shortcode);
            }
        });

        // Block 3: Poll Block
        registerBlockType('kata-seo/poll-block', {
            title: __('KATA Poll', 'kata-seo-tools'),
            icon: 'chart-bar',
            category: 'widgets',
            attributes: {
                question: { type: 'string', default: 'Your question?' },
                option1: { type: 'string', default: 'Option 1' },
                option2: { type: 'string', default: 'Option 2' },
                option3: { type: 'string', default: 'Option 3' },
                option4: { type: 'string', default: 'Option 4' }
            },
            edit: function(props) {
                const { attributes, setAttributes } = props;
                
                return wp.element.createElement(
                    wp.element.Fragment,
                    {},
                    wp.element.createElement(
                        InspectorControls,
                        {},
                        wp.element.createElement(TextControl, {
                            label: 'Question',
                            value: attributes.question,
                            onChange: (val) => setAttributes({ question: val })
                        }),
                        wp.element.createElement(TextControl, {
                            label: 'Option 1',
                            value: attributes.option1,
                            onChange: (val) => setAttributes({ option1: val })
                        }),
                        wp.element.createElement(TextControl, {
                            label: 'Option 2',
                            value: attributes.option2,
                            onChange: (val) => setAttributes({ option2: val })
                        }),
                        wp.element.createElement(TextControl, {
                            label: 'Option 3',
                            value: attributes.option3,
                            onChange: (val) => setAttributes({ option3: val })
                        }),
                        wp.element.createElement(TextControl, {
                            label: 'Option 4',
                            value: attributes.option4,
                            onChange: (val) => setAttributes({ option4: val })
                        })
                    ),
                    wp.element.createElement(
                        'div',
                        { className: 'kata-block-placeholder kata-poll-block' },
                        wp.element.createElement('span', { className: 'dashicons dashicons-chart-bar' }),
                        wp.element.createElement('p', {}, 'KATA Poll: ' + attributes.question)
                    )
                );
            },
            save: function(props) {
                const { attributes } = props;
                const shortcode = `[kata_poll question="${attributes.question}" option1="${attributes.option1}" option2="${attributes.option2}" option3="${attributes.option3}" option4="${attributes.option4}"]`;
                return wp.element.createElement(wp.element.RawHTML, {}, shortcode);
            }
        });

        // Block 4: Wheel Block
        registerBlockType('kata-seo/wheel-block', {
            title: __('KATA Wheel', 'kata-seo-tools'),
            icon: 'image-rotate',
            category: 'widgets',
            attributes: {
                prizes: { type: 'string', default: 'Prize 1, Prize 2, Prize 3, Prize 4' }
            },
            edit: function(props) {
                return wp.element.createElement(
                    'div',
                    { className: 'kata-block-placeholder kata-wheel-block' },
                    wp.element.createElement('span', { className: 'dashicons dashicons-image-rotate' }),
                    wp.element.createElement('p', {}, 'KATA Lucky Wheel'),
                    wp.element.createElement('small', {}, 'Spin to win!')
                );
            },
            save: function(props) {
                const { attributes } = props;
                return wp.element.createElement(
                    wp.element.RawHTML,
                    {},
                    `[kata_wheel prizes="${attributes.prizes}"]`
                );
            }
        });

        // Block 5: Social Share Block
        registerBlockType('kata-seo/social-block', {
            title: __('KATA Social Share', 'kata-seo-tools'),
            icon: 'share',
            category: 'widgets',
            edit: function() {
                return wp.element.createElement(
                    'div',
                    { className: 'kata-block-placeholder kata-social-block' },
                    wp.element.createElement('span', { className: 'dashicons dashicons-share' }),
                    wp.element.createElement('p', {}, 'KATA Social Share Buttons')
                );
            },
            save: function() {
                return wp.element.createElement(
                    wp.element.RawHTML,
                    {},
                    '[kata_social_share]'
                );
            }
        });
    }

    // Quick Insert Sidebar (cho Gutenberg)
    $(document).ready(function() {
        if ($('.edit-post-sidebar').length > 0) {
            addGutenbergQuickInsert();
        }
    });

    function addGutenbergQuickInsert() {
        const quickInsertHTML = `
            <div class="kata-quick-insert-panel">
                <h3>⚡ KATA Quick Insert</h3>
                <div class="kata-quick-buttons">
                    <button class="button button-secondary" data-template="faq">❓ FAQ</button>
                    <button class="button button-secondary" data-template="quiz">📝 Quiz</button>
                    <button class="button button-secondary" data-template="poll">📊 Poll</button>
                    <button class="button button-secondary" data-template="wheel">🎡 Wheel</button>
                    <button class="button button-secondary" data-template="rating">⭐ Rating</button>
                    <button class="button button-secondary" data-template="form">📧 Form</button>
                    <button class="button button-secondary" data-template="social">🔗 Social</button>
                    <button class="button button-secondary" data-template="cta">🎯 CTA</button>
                    <button class="button button-primary" data-template="landing">🚀 Landing Page</button>
                </div>
            </div>
        `;

        // Add CSS
        $('<style>')
            .text(`
                .kata-quick-insert-panel {
                    padding: 16px;
                    background: #f0f0f1;
                    border-radius: 4px;
                    margin: 16px 0;
                }
                .kata-quick-insert-panel h3 {
                    margin: 0 0 12px 0;
                    font-size: 14px;
                }
                .kata-quick-buttons {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 8px;
                }
                .kata-quick-buttons button {
                    padding: 8px 12px;
                    font-size: 12px;
                    text-align: left;
                }
                .kata-block-placeholder {
                    padding: 40px;
                    text-align: center;
                    background: #f0f0f1;
                    border: 2px dashed #ccc;
                    border-radius: 4px;
                }
                .kata-block-placeholder .dashicons {
                    font-size: 48px;
                    width: 48px;
                    height: 48px;
                    color: #666;
                }
                .kata-block-placeholder p {
                    margin: 12px 0 4px;
                    font-weight: 600;
                }
            `)
            .appendTo('head');

        // Insert panel into Gutenberg sidebar
        setTimeout(function() {
            if ($('.edit-post-sidebar .components-panel').length > 0) {
                $('.edit-post-sidebar .components-panel').first().before(quickInsertHTML);
                
                // Button click handlers
                $('.kata-quick-buttons button').on('click', function() {
                    const template = $(this).data('template');
                    insertIntoGutenberg(template);
                });
            }
        }, 1000);
    }

    function insertIntoGutenberg(template) {
        const templateContent = kataTemplates[template];
        
        if (!templateContent) return;

        // Copy to clipboard
        const textarea = document.createElement('textarea');
        textarea.value = templateContent;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);

        // Show notification
        if (wp.data && wp.data.dispatch) {
            wp.data.dispatch('core/notices').createNotice(
                'success',
                '✅ Template copied to clipboard! Paste (Ctrl+V) vào editor.',
                { isDismissible: true, type: 'snackbar' }
            );
        } else {
            alert('✅ Template copied to clipboard!\n\nPaste (Ctrl+V) vào editor để sử dụng.');
        }
    }

})(jQuery);
