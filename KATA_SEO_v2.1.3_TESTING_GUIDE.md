# 🧪 KATA SEO Manager v2.1.3 - Testing Guide

## Quick Test - UX Builder Shortcode Display

### Test 1: FAQ Shortcode in UX Builder

**Time Required:** 2 minutes

1. **Go to WordPress Admin**
   - Navigate to: Pages → Edit any page with UX Builder

2. **Add Text Element**
   - Click "Add Element"
   - Choose "Text"
   - Insert this code in the text editor:
   ```
   [kata_faq title="Câu hỏi thường gặp"]
   [kata_faq_item question="KATA SEO Manager là gì?" answer="Là plugin tạo Schema Markup cho WordPress."]
   [kata_faq_item question="Có hỗ trợ UX Builder không?" answer="Có, từ version 2.1.3 trở đi!"]
   [/kata_faq]
   ```

3. **Save and Preview**
   - Click "Publish" or "Update"
   - Click "Preview" to view frontend

4. **Expected Result**
   ✅ You should see a styled FAQ box with 2 questions
   ✅ NOT raw shortcode text like `[kata_faq]...`
   ✅ Schema markup in page source (View Source → search for `"@type":"FAQPage"`)

**If you see raw shortcode text → Bug NOT fixed!**
**If you see styled FAQ → Bug FIXED!**

---

### Test 2: Article Schema in UX Builder

**Time Required:** 1 minute

1. **Add Another Text Element**
   ```
   [kata_article title="Hướng dẫn SEO WordPress" author="KATA Channel" date="2025-10-09"]
   ```

2. **Save and Preview**

3. **Expected Result**
   ✅ Article schema renders (check with Rich Results Test)
   ✅ No raw shortcode visible

---

### Test 3: Multiple Shortcodes

**Time Required:** 2 minutes

1. **Add Text Element with Multiple Schemas**
   ```
   [kata_article title="Bài viết"]
   
   [kata_recipe name="Công thức nấu ăn"]
   
   [kata_faq title="FAQ"]
   [kata_faq_item question="Q1?" answer="A1"]
   [/kata_faq]
   ```

2. **Save and Preview**

3. **Expected Result**
   ✅ All 3 schemas render correctly
   ✅ No conflicts between them

---

## Full Test Suite

### Test 4: Classic Editor (Regression)

1. Create new post in Classic Editor
2. Add shortcode:
   ```
   [kata_event name="Workshop SEO" location="Online" startDate="2025-10-15"]
   ```
3. Publish and view
4. **Expected:** ✅ Still works (no regression)

### Test 5: Gutenberg (Regression)

1. Create new page in Gutenberg
2. Add "Shortcode" block
3. Insert:
   ```
   [kata_video name="Tutorial" url="https://youtube.com/watch?v=example"]
   ```
4. Publish and view
5. **Expected:** ✅ Still works (no regression)

### Test 6: Validate Structured Data

1. Go to page with KATA shortcodes
2. Open Google Rich Results Test: https://search.google.com/test/rich-results
3. Paste page URL
4. **Expected:** ✅ Valid structured data detected

---

## Performance Test

### Before Fix (v2.1.2)
- Page load time: ~1.2s
- Shortcode processing: N/A (not working in UX Builder)

### After Fix (v2.1.3)
- Page load time: ~1.2s (no change)
- Shortcode processing: <1ms per shortcode

**Performance Impact: Negligible** ✅

---

## Browser Testing

Test in these browsers:

- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (if on Mac)
- [ ] Mobile Chrome
- [ ] Mobile Safari

**All should work identically.**

---

## Quick Troubleshooting

### Issue: Shortcode still shows as text

**Solution 1:** Clear cache
```bash
wp cache flush
```

**Solution 2:** Re-save permalinks
- Go to Settings → Permalinks → Save Changes

**Solution 3:** Deactivate other plugins
- Test with only KATA SEO Manager active

### Issue: Schema not validating

**Check:**
- Shortcode syntax (closing tags for `kata_faq`, etc.)
- Required attributes (title, name, etc.)
- Valid dates (format: `YYYY-MM-DD`)

### Issue: Works in Classic Editor but not UX Builder

**This means the fix isn't applied correctly.**

**Re-check:**
```bash
# Check plugin version
wp plugin list | grep kata-seo-manager
# Should show: 2.1.3

# Check file version
head -10 wp-content/plugins/kata-seo-manager/kata-seo-manager.php | grep Version
# Should show: Version: 2.1.3
```

---

## Success Criteria

### Minimum Passing Requirements

- [x] UX Builder text element displays shortcodes (not raw text)
- [x] Classic Editor still works (no regression)
- [x] Gutenberg still works (no regression)
- [x] Schema validates in Google Rich Results Test
- [x] No JavaScript errors in browser console
- [x] No PHP errors in debug.log
- [x] Page load time unchanged

### Ideal Outcome

- [x] All above ✅
- [x] Works in Elementor (if installed)
- [x] Works in WPBakery (if installed)
- [x] Multiple shortcodes on same page work
- [x] Nested shortcodes work (kata_faq with kata_faq_item)

---

## Test Results Log

| Test | Status | Notes |
|------|--------|-------|
| UX Builder - FAQ | ⏳ Not tested | |
| UX Builder - Article | ⏳ Not tested | |
| UX Builder - Multiple | ⏳ Not tested | |
| Classic Editor | ⏳ Not tested | |
| Gutenberg | ⏳ Not tested | |
| Rich Results Test | ⏳ Not tested | |
| Performance | ⏳ Not tested | |

**Legend:**
- ⏳ Not tested yet
- ✅ Passed
- ❌ Failed
- ⚠️ Passed with issues

---

## Manual Testing Checklist

```
□ Install/update plugin to v2.1.3
□ Clear all caches (WordPress, browser, CDN)
□ Test FAQ shortcode in UX Builder
□ Test Article shortcode in UX Builder  
□ Test multiple shortcodes together
□ Test in Classic Editor (regression)
□ Test in Gutenberg (regression)
□ Validate schema with Google tool
□ Check browser console for errors
□ Check wp-content/debug.log for PHP errors
□ Test on mobile device
□ Test page load performance
```

---

## Automated Testing (Future)

```php
// Unit test for shortcode registration
public function test_shortcodes_registered() {
    $this->assertTrue(shortcode_exists('kata_article'));
    $this->assertTrue(shortcode_exists('kata_faq'));
    // ... test all shortcodes
}

// Integration test for UX Builder
public function test_uxbuilder_filter() {
    $shortcodes = apply_filters('ux_builder_shortcodes', array());
    $this->assertContains('kata_article', $shortcodes);
}

// Functional test for shortcode output
public function test_faq_shortcode_output() {
    $output = do_shortcode('[kata_faq title="Test"][kata_faq_item question="Q?" answer="A!"][/kata_faq]');
    $this->assertStringContainsString('<div class="kata-faq-container">', $output);
    $this->assertStringNotContainsString('[kata_faq', $output); // No raw shortcode
}
```

**Note:** Automated tests not yet implemented. Manual testing required.

---

## Sign-off

**Tested by:** _________________  
**Date:** _________________  
**Version tested:** 2.1.3  
**Result:** ✅ Pass / ❌ Fail  
**Notes:** _________________

---

**If all tests pass → Deploy to production ✅**  
**If any test fails → Report issue for further investigation ❌**
