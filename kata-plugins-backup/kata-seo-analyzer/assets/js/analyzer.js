/**
 * Kata SEO Analyzer - Content Analysis JavaScript
 * 
 * @package KataSEOAnalyzer
 * @version 2.0.0
 */

(function($) {
    'use strict';

    // Content Analyzer object
    window.KataContentAnalyzer = window.KataContentAnalyzer || {};

    /**
     * Initialize content analyzer
     */
    KataContentAnalyzer.init = function() {
        this.bindEvents();
        this.initRealTimeAnalysis();
        this.setupKeywordTracking();
        this.initContentOptimizer();
    };

    /**
     * Bind events
     */
    KataContentAnalyzer.bindEvents = function() {
        // Real-time content analysis
        $(document).on('input', '#title, #content, .wp-editor-area', this.debounce(this.analyzeRealTime, 1000));
        
        // Keyword optimization
        $(document).on('click', '.optimize-keyword-btn', this.optimizeForKeyword);
        $(document).on('input', '.focus-keyword-input', this.debounce(this.updateFocusKeyword, 500));
        
        // Content suggestions
        $(document).on('click', '.apply-content-suggestion', this.applyContentSuggestion);
        $(document).on('click', '.get-content-ideas', this.getContentIdeas);
        
        // Readability improvements
        $(document).on('click', '.improve-readability-btn', this.improveReadability);
        $(document).on('click', '.simplify-sentence', this.simplifySentence);
        
        // Meta optimization
        $(document).on('input', '.meta-title-input', this.debounce(this.analyzeMeta, 500));
        $(document).on('input', '.meta-description-input', this.debounce(this.analyzeMeta, 500));
        
        // Content analysis actions
        $(document).on('click', '.analyze-competitors-content', this.analyzeCompetitorContent);
        $(document).on('click', '.generate-outline', this.generateContentOutline);
        $(document).on('click', '.check-plagiarism', this.checkPlagiarism);
    };

    /**
     * Initialize real-time analysis
     */
    KataContentAnalyzer.initRealTimeAnalysis = function() {
        if (!$('.kata-content-analyzer').length) return;

        // Setup analysis container
        this.setupAnalysisContainer();
        
        // Initial analysis if content exists
        var content = this.getCurrentContent();
        if (content.title || content.content) {
            this.analyzeRealTime();
        }

        // Setup auto-save integration
        this.setupAutoSaveIntegration();
    };

    /**
     * Setup analysis container
     */
    KataContentAnalyzer.setupAnalysisContainer = function() {
        var container = `
            <div id="kata-analysis-container" class="kata-metabox">
                <div class="kata-metabox-header">
                    <h3>SEO Content Analysis</h3>
                    <div class="kata-analysis-score">
                        <span class="score-label">SEO Score:</span>
                        <span class="score-number" id="current-seo-score">--</span>
                        <span class="score-max">/100</span>
                    </div>
                </div>
                
                <div class="kata-metabox-content">
                    <!-- Focus Keyword Section -->
                    <div class="kata-analysis-section">
                        <h4>Focus Keyword</h4>
                        <input type="text" id="focus-keyword" class="kata-input focus-keyword-input" 
                               placeholder="Enter your focus keyword">
                        <div class="keyword-suggestions" id="keyword-suggestions" style="display:none;"></div>
                    </div>

                    <!-- Real-time Analysis -->
                    <div class="kata-analysis-section">
                        <h4>Content Analysis</h4>
                        <div id="analysis-factors" class="analysis-factors">
                            <!-- Analysis factors will be populated here -->
                        </div>
                    </div>

                    <!-- Readability Section -->
                    <div class="kata-analysis-section">
                        <h4>Readability</h4>
                        <div id="readability-analysis" class="readability-analysis">
                            <div class="readability-score">
                                <span class="readability-label">Reading Level:</span>
                                <span class="readability-value" id="readability-score">--</span>
                            </div>
                            <div class="readability-factors" id="readability-factors">
                                <!-- Readability factors will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- AI Suggestions -->
                    <div class="kata-analysis-section" id="ai-suggestions-section" style="display:none;">
                        <h4>AI Suggestions</h4>
                        <div id="ai-content-suggestions" class="ai-suggestions">
                            <!-- AI suggestions will be populated here -->
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="kata-analysis-actions">
                        <button class="kata-btn kata-btn-primary get-ai-suggestions-btn" 
                                data-post-id="<?php echo get_the_ID(); ?>">
                            Get AI Suggestions
                        </button>
                        <button class="kata-btn kata-btn-secondary analyze-competitors-content">
                            Analyze Competitors
                        </button>
                        <button class="kata-btn kata-btn-secondary generate-outline">
                            Generate Outline
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Add to post editor if not already present
        if ($('#kata-analysis-container').length === 0) {
            if ($('#postdivrich').length) {
                $('#postdivrich').after(container);
            } else if ($('.edit-post-layout__metaboxes').length) {
                // Gutenberg editor
                $('.edit-post-layout__metaboxes').prepend(container);
            }
        }
    };

    /**
     * Get current content
     */
    KataContentAnalyzer.getCurrentContent = function() {
        var title = '';
        var content = '';
        var excerpt = '';

        // Get title
        if ($('#title').length) {
            title = $('#title').val();
        } else if ($('.editor-post-title__input').length) {
            title = $('.editor-post-title__input').text();
        }

        // Get content
        if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
            content = tinyMCE.activeEditor.getContent();
        } else if ($('#content').length) {
            content = $('#content').val();
        } else if (typeof wp !== 'undefined' && wp.data) {
            // Gutenberg
            var postContent = wp.data.select('core/editor').getEditedPostContent();
            if (postContent) {
                content = postContent;
            }
        }

        // Get excerpt
        if ($('#excerpt').length) {
            excerpt = $('#excerpt').val();
        }

        return {
            title: title,
            content: content,
            excerpt: excerpt
        };
    };

    /**
     * Analyze content in real-time
     */
    KataContentAnalyzer.analyzeRealTime = function() {
        var content = KataContentAnalyzer.getCurrentContent();
        var focusKeyword = $('#focus-keyword').val();

        if (!content.title && !content.content) {
            return;
        }

        // Show loading
        $('#analysis-factors').html('<div class="analysis-loading">Analyzing content...</div>');

        // Perform analysis
        var analysis = KataContentAnalyzer.performContentAnalysis(content, focusKeyword);
        
        // Update display
        KataContentAnalyzer.displayAnalysisResults(analysis);
        
        // Update score
        KataContentAnalyzer.updateScore(analysis.overall_score);

        // Analyze readability
        var readability = KataContentAnalyzer.analyzeReadability(content.content);
        KataContentAnalyzer.displayReadabilityResults(readability);
    };

    /**
     * Perform content analysis
     */
    KataContentAnalyzer.performContentAnalysis = function(content, focusKeyword) {
        var analysis = {
            factors: {},
            overall_score: 0,
            recommendations: []
        };

        // Title analysis
        analysis.factors.title_length = this.analyzeTitleLength(content.title);
        analysis.factors.title_keywords = this.analyzeTitleKeywords(content.title, focusKeyword);
        
        // Content analysis
        analysis.factors.content_length = this.analyzeContentLength(content.content);
        analysis.factors.keyword_density = this.analyzeKeywordDensity(content.content, focusKeyword);
        analysis.factors.heading_structure = this.analyzeHeadingStructure(content.content);
        analysis.factors.internal_links = this.analyzeInternalLinks(content.content);
        analysis.factors.external_links = this.analyzeExternalLinks(content.content);
        analysis.factors.image_optimization = this.analyzeImages(content.content);
        
        // Meta analysis
        analysis.factors.meta_description = this.analyzeMetaDescription();
        
        // Calculate overall score
        analysis.overall_score = this.calculateOverallScore(analysis.factors);
        
        // Generate recommendations
        analysis.recommendations = this.generateRecommendations(analysis.factors);

        return analysis;
    };

    /**
     * Analyze title length
     */
    KataContentAnalyzer.analyzeTitleLength = function(title) {
        var length = title.length;
        var status = length >= 30 && length <= 60;
        
        return {
            status: status,
            value: length,
            target: '30-60 characters',
            weight: 15,
            score: status ? 100 : Math.max(0, 100 - Math.abs(45 - length) * 2),
            message: status ? 'Title length is optimal' : 'Title should be 30-60 characters'
        };
    };

    /**
     * Analyze title keywords
     */
    KataContentAnalyzer.analyzeTitleKeywords = function(title, keyword) {
        if (!keyword) {
            return {
                status: false,
                value: 0,
                target: 'Include focus keyword',
                weight: 20,
                score: 0,
                message: 'No focus keyword set'
            };
        }

        var titleLower = title.toLowerCase();
        var keywordLower = keyword.toLowerCase();
        var includes = titleLower.includes(keywordLower);
        var position = titleLower.indexOf(keywordLower);
        
        var score = 0;
        if (includes) {
            score = 100;
            if (position <= 30) score += 10; // Bonus for early position
        }

        return {
            status: includes,
            value: includes ? 'Yes' : 'No',
            target: 'Include focus keyword in title',
            weight: 20,
            score: Math.min(100, score),
            message: includes ? 
                (position <= 30 ? 'Focus keyword appears early in title' : 'Focus keyword found in title') :
                'Include focus keyword in title'
        };
    };

    /**
     * Analyze content length
     */
    KataContentAnalyzer.analyzeContentLength = function(content) {
        var text = this.stripHTMLTags(content);
        var wordCount = this.countWords(text);
        var status = wordCount >= 300;
        
        return {
            status: status,
            value: wordCount + ' words',
            target: '300+ words',
            weight: 10,
            score: Math.min(100, (wordCount / 300) * 100),
            message: status ? 'Content length is sufficient' : 'Content should be at least 300 words'
        };
    };

    /**
     * Analyze keyword density
     */
    KataContentAnalyzer.analyzeKeywordDensity = function(content, keyword) {
        if (!keyword) {
            return {
                status: false,
                value: '0%',
                target: '0.5-2.5%',
                weight: 15,
                score: 0,
                message: 'No focus keyword set'
            };
        }

        var text = this.stripHTMLTags(content).toLowerCase();
        var totalWords = this.countWords(text);
        var keywordOccurrences = (text.match(new RegExp(keyword.toLowerCase(), 'g')) || []).length;
        var density = totalWords > 0 ? (keywordOccurrences / totalWords) * 100 : 0;
        
        var status = density >= 0.5 && density <= 2.5;
        var score = 0;
        
        if (density === 0) {
            score = 0;
        } else if (density < 0.5) {
            score = (density / 0.5) * 50;
        } else if (density <= 2.5) {
            score = 100;
        } else {
            score = Math.max(0, 100 - (density - 2.5) * 20);
        }

        return {
            status: status,
            value: density.toFixed(1) + '%',
            target: '0.5-2.5%',
            weight: 15,
            score: score,
            message: status ? 'Keyword density is optimal' : 
                density === 0 ? 'Focus keyword not found in content' :
                density < 0.5 ? 'Keyword density too low' : 'Keyword density too high'
        };
    };

    /**
     * Analyze heading structure
     */
    KataContentAnalyzer.analyzeHeadingStructure = function(content) {
        var headings = content.match(/<h([1-6])[^>]*>.*?<\/h[1-6]>/gi) || [];
        var h1Count = (content.match(/<h1[^>]*>.*?<\/h1>/gi) || []).length;
        var h2Count = (content.match(/<h2[^>]*>.*?<\/h2>/gi) || []).length;
        
        var status = h1Count <= 1 && h2Count >= 1;
        var score = 0;
        
        if (h1Count > 1) {
            score = 30; // Penalty for multiple H1s
        } else if (h1Count === 1 && h2Count >= 1) {
            score = 100;
        } else if (h2Count >= 1) {
            score = 80;
        } else if (headings.length > 0) {
            score = 60;
        } else {
            score = 0;
        }

        return {
            status: status,
            value: headings.length + ' headings',
            target: 'Proper heading structure',
            weight: 10,
            score: score,
            message: status ? 'Good heading structure' : 
                h1Count > 1 ? 'Multiple H1 tags found' :
                h2Count === 0 ? 'Add H2 headings to structure content' : 'Improve heading structure'
        };
    };

    /**
     * Analyze internal links
     */
    KataContentAnalyzer.analyzeInternalLinks = function(content) {
        var internalLinks = (content.match(/<a[^>]*href=["'][^"']*["'][^>]*>/gi) || [])
            .filter(link => !link.includes('http://') && !link.includes('https://') || 
                          link.includes(window.location.hostname));
        
        var count = internalLinks.length;
        var status = count >= 2;
        
        return {
            status: status,
            value: count + ' links',
            target: '2+ internal links',
            weight: 8,
            score: Math.min(100, count * 25),
            message: status ? 'Good internal linking' : 'Add more internal links'
        };
    };

    /**
     * Analyze external links
     */
    KataContentAnalyzer.analyzeExternalLinks = function(content) {
        var externalLinks = (content.match(/<a[^>]*href=["']https?:\/\/[^"']*["'][^>]*>/gi) || [])
            .filter(link => !link.includes(window.location.hostname));
        
        var count = externalLinks.length;
        var status = count >= 1;
        
        return {
            status: status,
            value: count + ' links',
            target: '1+ external links',
            weight: 5,
            score: count > 0 ? 100 : 0,
            message: status ? 'Contains external links' : 'Add external links to authoritative sources'
        };
    };

    /**
     * Analyze images
     */
    KataContentAnalyzer.analyzeImages = function(content) {
        var images = content.match(/<img[^>]*>/gi) || [];
        var imagesWithAlt = images.filter(img => img.includes('alt='));
        
        var totalImages = images.length;
        var optimizedImages = imagesWithAlt.length;
        var status = totalImages === 0 || optimizedImages === totalImages;
        
        return {
            status: status,
            value: optimizedImages + '/' + totalImages + ' optimized',
            target: 'All images with alt text',
            weight: 7,
            score: totalImages === 0 ? 100 : (optimizedImages / totalImages) * 100,
            message: status ? 
                (totalImages === 0 ? 'No images found' : 'All images have alt text') :
                'Add alt text to all images'
        };
    };

    /**
     * Analyze meta description
     */
    KataContentAnalyzer.analyzeMetaDescription = function() {
        var metaDesc = $('#meta-description, .meta-description-input').val() || '';
        var length = metaDesc.length;
        var status = length >= 120 && length <= 160;
        
        return {
            status: status,
            value: length + ' characters',
            target: '120-160 characters',
            weight: 10,
            score: status ? 100 : Math.max(0, 100 - Math.abs(140 - length) * 2),
            message: status ? 'Meta description length is optimal' : 
                length === 0 ? 'Add a meta description' :
                length < 120 ? 'Meta description is too short' : 'Meta description is too long'
        };
    };

    /**
     * Calculate overall score
     */
    KataContentAnalyzer.calculateOverallScore = function(factors) {
        var totalWeight = 0;
        var weightedScore = 0;
        
        Object.values(factors).forEach(factor => {
            totalWeight += factor.weight;
            weightedScore += (factor.score * factor.weight) / 100;
        });
        
        return Math.round((weightedScore / totalWeight) * 100);
    };

    /**
     * Display analysis results
     */
    KataContentAnalyzer.displayAnalysisResults = function(analysis) {
        var html = '';
        
        Object.entries(analysis.factors).forEach(([key, factor]) => {
            var statusClass = factor.status ? 'good' : 'needs-improvement';
            var statusIcon = factor.status ? '✅' : '❌';
            
            html += `
                <div class="analysis-factor ${statusClass}" data-factor="${key}">
                    <div class="factor-header">
                        <span class="factor-status">${statusIcon}</span>
                        <span class="factor-name">${this.getFactorName(key)}</span>
                        <span class="factor-score">${factor.score}/100</span>
                    </div>
                    <div class="factor-details">
                        <div class="factor-value">Current: ${factor.value}</div>
                        <div class="factor-target">Target: ${factor.target}</div>
                        <div class="factor-message">${factor.message}</div>
                    </div>
                </div>
            `;
        });
        
        $('#analysis-factors').html(html);
    };

    /**
     * Get factor display name
     */
    KataContentAnalyzer.getFactorName = function(key) {
        var names = {
            'title_length': 'Title Length',
            'title_keywords': 'Title Keywords',
            'content_length': 'Content Length',
            'keyword_density': 'Keyword Density',
            'heading_structure': 'Heading Structure',
            'internal_links': 'Internal Links',
            'external_links': 'External Links',
            'image_optimization': 'Image Optimization',
            'meta_description': 'Meta Description'
        };
        
        return names[key] || key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    };

    /**
     * Update score display
     */
    KataContentAnalyzer.updateScore = function(score) {
        $('#current-seo-score').text(score);
        
        var $scoreContainer = $('.kata-analysis-score');
        $scoreContainer.removeClass('excellent good fair poor');
        
        if (score >= 90) {
            $scoreContainer.addClass('excellent');
        } else if (score >= 80) {
            $scoreContainer.addClass('good');
        } else if (score >= 60) {
            $scoreContainer.addClass('fair');
        } else {
            $scoreContainer.addClass('poor');
        }
    };

    /**
     * Analyze readability
     */
    KataContentAnalyzer.analyzeReadability = function(content) {
        var text = this.stripHTMLTags(content);
        
        if (!text.trim()) {
            return {
                score: 0,
                level: 'No content',
                factors: {}
            };
        }

        var sentences = this.countSentences(text);
        var words = this.countWords(text);
        var syllables = this.countSyllables(text);
        
        // Flesch Reading Ease
        var fleschScore = 206.835 - (1.015 * (words / sentences)) - (84.6 * (syllables / words));
        fleschScore = Math.max(0, Math.min(100, fleschScore));
        
        var level = this.getReadingLevel(fleschScore);
        
        var factors = {
            sentence_length: this.analyzeSentenceLength(text),
            paragraph_length: this.analyzeParagraphLength(content),
            passive_voice: this.analyzePassiveVoice(text),
            transition_words: this.analyzeTransitionWords(text),
            complex_words: this.analyzeComplexWords(text)
        };

        return {
            score: Math.round(fleschScore),
            level: level,
            factors: factors
        };
    };

    /**
     * Get reading level from Flesch score
     */
    KataContentAnalyzer.getReadingLevel = function(score) {
        if (score >= 90) return 'Very Easy';
        if (score >= 80) return 'Easy';
        if (score >= 70) return 'Fairly Easy';
        if (score >= 60) return 'Standard';
        if (score >= 50) return 'Fairly Difficult';
        if (score >= 30) return 'Difficult';
        return 'Very Difficult';
    };

    /**
     * Display readability results
     */
    KataContentAnalyzer.displayReadabilityResults = function(readability) {
        $('#readability-score').text(readability.level);
        
        var factorsHtml = '';
        Object.entries(readability.factors).forEach(([key, factor]) => {
            var statusClass = factor.status ? 'good' : 'needs-improvement';
            var statusIcon = factor.status ? '✅' : '⚠️';
            
            factorsHtml += `
                <div class="readability-factor ${statusClass}">
                    <span class="factor-status">${statusIcon}</span>
                    <span class="factor-name">${this.getFactorName(key)}</span>
                    <span class="factor-value">${factor.value}</span>
                </div>
            `;
        });
        
        $('#readability-factors').html(factorsHtml);
    };

    /**
     * Utility functions
     */

    /**
     * Strip HTML tags
     */
    KataContentAnalyzer.stripHTMLTags = function(html) {
        var tmp = document.createElement('DIV');
        tmp.innerHTML = html;
        return tmp.textContent || tmp.innerText || '';
    };

    /**
     * Count words
     */
    KataContentAnalyzer.countWords = function(text) {
        return text.trim().split(/\s+/).filter(word => word.length > 0).length;
    };

    /**
     * Count sentences
     */
    KataContentAnalyzer.countSentences = function(text) {
        return text.split(/[.!?]+/).filter(sentence => sentence.trim().length > 0).length;
    };

    /**
     * Count syllables (approximation)
     */
    KataContentAnalyzer.countSyllables = function(text) {
        var words = text.toLowerCase().match(/\b[a-z]+\b/g) || [];
        var syllableCount = 0;
        
        words.forEach(word => {
            var syllables = word.match(/[aeiouy]+/g) || [];
            syllableCount += Math.max(1, syllables.length);
        });
        
        return syllableCount;
    };

    /**
     * Analyze sentence length
     */
    KataContentAnalyzer.analyzeSentenceLength = function(text) {
        var sentences = text.split(/[.!?]+/).filter(s => s.trim().length > 0);
        if (sentences.length === 0) return { status: true, value: '0', message: 'No sentences found' };
        
        var totalWords = 0;
        sentences.forEach(sentence => {
            totalWords += this.countWords(sentence);
        });
        
        var averageLength = totalWords / sentences.length;
        var status = averageLength <= 20;
        
        return {
            status: status,
            value: Math.round(averageLength) + ' words/sentence',
            message: status ? 'Good sentence length' : 'Sentences are too long'
        };
    };

    /**
     * Analyze paragraph length
     */
    KataContentAnalyzer.analyzeParagraphLength = function(content) {
        var paragraphs = content.split(/<\/p>/i).filter(p => this.stripHTMLTags(p).trim().length > 0);
        if (paragraphs.length === 0) return { status: true, value: '0', message: 'No paragraphs found' };
        
        var longParagraphs = paragraphs.filter(p => this.countWords(this.stripHTMLTags(p)) > 150);
        var status = longParagraphs.length === 0;
        
        return {
            status: status,
            value: longParagraphs.length + ' long paragraphs',
            message: status ? 'Good paragraph length' : 'Some paragraphs are too long'
        };
    };

    /**
     * Analyze passive voice (simplified)
     */
    KataContentAnalyzer.analyzePassiveVoice = function(text) {
        var passiveIndicators = /\b(was|were|been|being|is|are|am)\s+\w+ed\b/gi;
        var passiveCount = (text.match(passiveIndicators) || []).length;
        var sentences = this.countSentences(text);
        var percentage = sentences > 0 ? (passiveCount / sentences) * 100 : 0;
        var status = percentage <= 10;
        
        return {
            status: status,
            value: Math.round(percentage) + '%',
            message: status ? 'Good use of active voice' : 'Too much passive voice'
        };
    };

    /**
     * Analyze transition words
     */
    KataContentAnalyzer.analyzeTransitionWords = function(text) {
        var transitions = /\b(however|therefore|furthermore|moreover|consequently|meanwhile|additionally|specifically|finally|firstly|secondly|likewise|similarly|in contrast|on the other hand|for example|for instance|in conclusion)\b/gi;
        var transitionCount = (text.match(transitions) || []).length;
        var sentences = this.countSentences(text);
        var percentage = sentences > 0 ? (transitionCount / sentences) * 100 : 0;
        var status = percentage >= 10;
        
        return {
            status: status,
            value: Math.round(percentage) + '%',
            message: status ? 'Good use of transition words' : 'Add more transition words'
        };
    };

    /**
     * Analyze complex words
     */
    KataContentAnalyzer.analyzeComplexWords = function(text) {
        var words = text.toLowerCase().match(/\b[a-z]+\b/g) || [];
        var complexWords = words.filter(word => word.length > 6 && this.countSyllables(word) > 2);
        var percentage = words.length > 0 ? (complexWords.length / words.length) * 100 : 0;
        var status = percentage <= 15;
        
        return {
            status: status,
            value: Math.round(percentage) + '%',
            message: status ? 'Good word complexity' : 'Too many complex words'
        };
    };

    /**
     * Update focus keyword
     */
    KataContentAnalyzer.updateFocusKeyword = function() {
        var keyword = $(this).val();
        
        // Save keyword
        if (kataSEO && kataSEO.postId) {
            $.ajax({
                url: kataSEO.ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_seo_save_focus_keyword',
                    post_id: kataSEO.postId,
                    keyword: keyword,
                    nonce: kataSEO.nonce
                }
            });
        }
        
        // Re-analyze content
        KataContentAnalyzer.analyzeRealTime();
        
        // Get keyword suggestions
        if (keyword.length > 2) {
            KataContentAnalyzer.getKeywordSuggestions(keyword);
        }
    };

    /**
     * Get keyword suggestions
     */
    KataContentAnalyzer.getKeywordSuggestions = function(keyword) {
        $.ajax({
            url: kataSEO.ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_seo_get_keyword_suggestions',
                keyword: keyword,
                nonce: kataSEO.nonce
            },
            success: function(response) {
                if (response.success && response.data.suggestions) {
                    KataContentAnalyzer.displayKeywordSuggestions(response.data.suggestions);
                }
            }
        });
    };

    /**
     * Display keyword suggestions
     */
    KataContentAnalyzer.displayKeywordSuggestions = function(suggestions) {
        var html = '';
        suggestions.slice(0, 5).forEach(function(suggestion) {
            html += `<div class="keyword-suggestion" data-keyword="${suggestion.keyword}">
                        ${suggestion.keyword} 
                        <span class="suggestion-volume">${suggestion.volume || '--'}</span>
                     </div>`;
        });
        
        $('#keyword-suggestions').html(html).show();
        
        // Handle suggestion clicks
        $('.keyword-suggestion').on('click', function() {
            var keyword = $(this).data('keyword');
            $('#focus-keyword').val(keyword).trigger('input');
            $('#keyword-suggestions').hide();
        });
    };

    /**
     * Setup auto-save integration
     */
    KataContentAnalyzer.setupAutoSaveIntegration = function() {
        // Hook into WordPress autosave
        $(document).on('heartbeat-send', function(event, data) {
            // Trigger analysis on autosave
            setTimeout(function() {
                KataContentAnalyzer.analyzeRealTime();
            }, 1000);
        });
    };

    /**
     * Debounce function
     */
    KataContentAnalyzer.debounce = function(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    // Initialize on document ready
    $(document).ready(function() {
        // Only initialize on post edit pages
        if ($('body').hasClass('post-php') || $('body').hasClass('post-new-php') || 
            $('body').hasClass('wp-admin')) {
            KataContentAnalyzer.init();
        }
    });

    // Gutenberg integration
    if (typeof wp !== 'undefined' && wp.data) {
        // Subscribe to editor changes
        wp.data.subscribe(function() {
            clearTimeout(KataContentAnalyzer.gutenbergTimeout);
            KataContentAnalyzer.gutenbergTimeout = setTimeout(function() {
                KataContentAnalyzer.analyzeRealTime();
            }, 1000);
        });
    }

})(jQuery);
