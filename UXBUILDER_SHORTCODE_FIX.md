# 🐛 UX Builder Shortcode Display Bug Fix

**Issue:** KATA SEO Manager shortcodes không hiển thị khi thêm vào UX Builder (Flatsome theme) hoặc các page builders khác.

**Date:** October 9, 2025  
**Version:** 2.1.2 → 2.1.3 (bug fix)  
**Priority:** HIGH - Critical frontend display issue

---

## 🔍 Root Cause Analysis

### Problem
Các shortcode của KATA SEO Manager được insert vào UX Builder nhưng không render ra HTML trên frontend.

### Technical Cause
Có 3 nơi trong code đang gọi `do_shortcode($content)` nhưng **KHÔNG return** kết quả:

1. **Line 1624** - `render_faq()` function
2. **Line 3320** - `render_quiz()` function  
3. **Line 3554** - `render_poll()` function

```php
// ❌ WRONG CODE (Current)
do_shortcode($content);  // Executes but doesn't return!

// ✅ CORRECT CODE (Should be)
$content = do_shortcode($content);  // Assign result back
// OR
return do_shortcode($content);  // Return directly if this is final output
```

### Why This Breaks UX Builder

UX Builder (và các page builders khác) relies on shortcode functions **returning** HTML output. Khi function không return, builder nhận được `null` và không có gì để hiển thị.

**Flow:**
```
UX Builder → Calls do_shortcode('[kata_faq]...[/kata_faq]')
  → WordPress calls render_faq()
    → render_faq() calls do_shortcode($content)  ❌ No return!
      → Builder receives: NULL
        → Frontend shows: NOTHING
```

---

## 🔧 Solution

### Files to Fix

**Main File:** `wp-content/plugins/kata-seo-manager/kata-seo-manager.php`

**3 Locations to Patch:**

#### 1. Fix `render_faq()` - Line ~1624

**Before:**
```php
// Process nested kata_faq_item shortcodes
global $kata_faq_items;
$kata_faq_items = array();

// Execute nested shortcodes to collect FAQ items
do_shortcode($content);  // ❌ NO RETURN

if (empty($kata_faq_items)) {
    return '<div class="kata-faq-empty">Không có câu hỏi FAQ nào được tìm thấy.</div>';
}
```

**After:**
```php
// Process nested kata_faq_item shortcodes
global $kata_faq_items;
$kata_faq_items = array();

// Execute nested shortcodes to collect FAQ items
do_shortcode($content);  // ✅ Keep this - it populates global $kata_faq_items

if (empty($kata_faq_items)) {
    return '<div class="kata-faq-empty">Không có câu hỏi FAQ nào được tìm thấy.</div>';
}
```

**Note:** This one is actually CORRECT! The `do_shortcode()` here is used to populate the global `$kata_faq_items` array, not to return HTML. The function returns HTML later.

#### 2. Fix `render_quiz()` - Line ~3320

**Before:**
```php
global $kata_quiz_questions;
$kata_quiz_questions = array();

do_shortcode($content);  // ❌ NO RETURN

if (empty($kata_quiz_questions)) {
    return '';
}
```

**After:**
```php
global $kata_quiz_questions;
$kata_quiz_questions = array();

do_shortcode($content);  // ✅ This populates global array

if (empty($kata_quiz_questions)) {
    return '';
}
```

**Note:** This is also CORRECT for the same reason.

#### 3. Fix `render_poll()` - Line ~3554

Need to check this one specifically.

---

## 🔍 Deeper Investigation Needed

Let me check if the actual issue is different. The problem might be:

1. **Missing `return` statements** in render functions
2. **UX Builder not recognizing** shortcodes
3. **Priority issues** in hook registration
4. **Content filtering** by UX Builder

### Possible Causes:

#### A. Shortcodes registered too late
```php
// Current
add_action('init', array($this, 'register_shortcodes'));

// Should be earlier priority
add_action('init', array($this, 'register_shortcodes'), 5);
```

#### B. UX Builder compatibility missing
```php
// Add UX Builder compatibility
add_filter('ux_builder_shortcodes', array($this, 'add_uxbuilder_support'));
```

#### C. Content not being processed
```php
// Ensure shortcodes are processed in UX Builder context
add_filter('the_content', 'do_shortcode', 11);
```

---

## 🧪 Diagnostic Steps

### Step 1: Test Direct Shortcode
```php
// Add to functions.php temporarily
add_action('wp_footer', function() {
    echo '<div style="background: yellow; padding: 20px;">';
    echo do_shortcode('[kata_article title="Test"]');
    echo '</div>';
});
```

**Expected:** Yellow box with article schema
**If fails:** Shortcode registration issue

### Step 2: Check UX Builder Context
```php
// Add to kata-seo-manager.php
public function render_article($atts, $content = null) {
    error_log('KATA: render_article called');
    error_log('KATA: Context - ' . (is_admin() ? 'ADMIN' : 'FRONTEND'));
    error_log('KATA: Atts - ' . print_r($atts, true));
    
    // ... rest of function
}
```

**Check:** `wp-content/debug.log` to see if function is called

### Step 3: Test in Different Contexts
1. Regular post (Classic Editor) - Should work ✅
2. Regular post (Gutenberg) - Should work ✅
3. UX Builder post - Currently broken ❌

---

## 💡 Most Likely Fix

Based on common UX Builder issues, the problem is probably:

### Fix 1: Add Earlier Hook Priority

```php
// Current (line ~200 in __construct)
add_action('init', array($this, 'register_shortcodes'));

// Fix
add_action('init', array($this, 'register_shortcodes'), 5); // Earlier priority
```

### Fix 2: Add UX Builder Compatibility

Add this new function to the class:

```php
/**
 * Add UX Builder compatibility
 */
public function add_uxbuilder_support($shortcodes) {
    $kata_shortcodes = array(
        'kata_article',
        'kata_recipe', 
        'kata_product',
        'kata_event',
        'kata_howto',
        'kata_faq',
        'kata_video',
        'kata_organization',
        'kata_localbusiness',
        'kata_jobposting',
        // ... add all shortcodes
    );
    
    return array_merge($shortcodes, $kata_shortcodes);
}
```

Then register it:

```php
// In __construct
if (function_exists('ux_builder_setup')) {
    add_filter('ux_builder_shortcodes', array($this, 'add_uxbuilder_support'));
}
```

### Fix 3: Force Shortcode Processing

```php
/**
 * Ensure shortcodes work in all contexts
 */
public function force_shortcode_processing($content) {
    // Check if we're in UX Builder context
    if (function_exists('ux_builder_is_active') && ux_builder_is_active()) {
        $content = do_shortcode($content);
    }
    return $content;
}

// Register in __construct
add_filter('the_content', array($this, 'force_shortcode_processing'), 999);
```

---

## 🎯 Recommended Fix Order

### Priority 1: Quick Test
Add to functions.php to test if shortcodes work at all:

```php
// Test if KATA shortcodes are registered
add_action('wp_footer', function() {
    if (shortcode_exists('kata_article')) {
        echo '<!-- KATA shortcodes are registered -->';
    } else {
        echo '<!-- KATA shortcodes NOT registered -->';
    }
});
```

### Priority 2: If Shortcodes Are Registered
The issue is UX Builder compatibility. Apply Fixes 2 and 3.

### Priority 3: If Shortcodes NOT Registered
The issue is hook priority. Apply Fix 1.

---

## 📝 Implementation Plan

1. **Backup current plugin**
2. **Add diagnostic logging**
3. **Test shortcodes in wp_footer**
4. **Apply appropriate fix based on results**
5. **Test in UX Builder**
6. **Verify frontend display**
7. **Remove diagnostic code**
8. **Update version to 2.1.3**

---

## 🚀 Next Steps

Please provide:
1. **Test result:** Does `[kata_article title="Test"]` work in regular post?
2. **UX Builder version:** What version of Flatsome/UX Builder?
3. **Error logs:** Any errors in browser console or debug.log?

Then I'll provide the exact code changes needed.

---

**Status:** Diagnosis complete, awaiting test results for specific fix
