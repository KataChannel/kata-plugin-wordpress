# 🎨 Admin UI Testing Guide

**Test Date:** 08/10/2025  
**Plugin:** kata-seo-manager v1.0.0  
**Feature:** Schema Customization Visual Builder

## Quick Access

**Admin URL:** http://localhost/timona/wp-admin/admin.php?page=kata-schema-customization

**Navigation:** WordPress Admin → kata-seo-manager → Schema Customization

## Pre-Test Checklist

Before testing the admin UI, verify:

- [x] ✅ KATA_Schema_Admin_UI class loaded
- [x] ✅ Admin UI initialized in kata-seo-manager.php (line 121)
- [x] ✅ CSS file exists: `/assets/css/schema-admin-ui.css`
- [x] ✅ JS file exists: `/assets/js/schema-admin-ui.js`
- [ ] ⏳ WordPress admin accessible
- [ ] ⏳ User has admin privileges

## Test Procedure

### Test 1: Menu Access ✓
**Goal:** Verify admin submenu appears correctly

**Steps:**
1. Log into WordPress admin: http://localhost/timona/wp-admin
2. Look for "kata-seo-manager" in left sidebar menu
3. Hover/click to expand submenu
4. Look for "Schema Customization" submenu item
5. Click "Schema Customization"

**Expected Results:**
- ✅ "kata-seo-manager" menu visible in sidebar
- ✅ "Schema Customization" submenu item appears
- ✅ Clicking loads the customization page
- ✅ URL changes to: `admin.php?page=kata-schema-customization`

**Pass/Fail:** ☐

---

### Test 2: Page Load ✓
**Goal:** Verify page loads without errors

**Steps:**
1. On Schema Customization page
2. Open browser console (F12)
3. Check Console tab for errors
4. Check Network tab for failed requests

**Expected Results:**
- ✅ Page title: "Schema Customization"
- ✅ No PHP errors displayed
- ✅ No JavaScript console errors
- ✅ CSS file loaded (200 status)
- ✅ JS file loaded (200 status)
- ✅ Page renders completely

**Pass/Fail:** ☐

**Common Issues:**
- **404 on CSS/JS:** Check file paths in `enqueue_scripts()`
- **White screen:** Check PHP error log
- **Blank page:** Check JavaScript console

---

### Test 3: Schema Type Dropdown ✓
**Goal:** Verify all 15 schema types available

**Steps:**
1. Locate "Select Schema Type" dropdown at top of page
2. Click dropdown to open
3. Count available options
4. Select different types

**Expected Results:**
- ✅ Dropdown contains 15 options
- ✅ Options include: Article, FAQ, Recipe, Product, Event, HowTo, Course, LocalBusiness, JobPosting, Video, Organization, Rating, Quiz, Poll, Wheel
- ✅ Selecting type changes properties panel
- ✅ No JavaScript errors when switching

**Schema Types Checklist:**
- [ ] Article
- [ ] FAQ
- [ ] Recipe
- [ ] Product
- [ ] Event
- [ ] HowTo
- [ ] Course
- [ ] LocalBusiness
- [ ] JobPosting
- [ ] Video
- [ ] Organization
- [ ] Rating
- [ ] Quiz
- [ ] Poll
- [ ] Wheel

**Pass/Fail:** ☐

---

### Test 4: Mode Tabs ✓
**Goal:** Verify 3 mode tabs work correctly

**Steps:**
1. Select any schema type (e.g., Article)
2. Observe the 3 mode tabs
3. Click each tab in sequence
4. Check that panels switch

**Expected Results:**
- ✅ 3 tabs visible: "Schema Filtering (MODE 1)", "Content Display (MODE 2)", "Both Modes"
- ✅ Default tab is active (highlighted)
- ✅ Clicking tab switches active state
- ✅ Clicking tab shows/hides corresponding panels
- ✅ Smooth visual transition

**Mode Tabs Checklist:**
- [ ] Schema Filtering tab (MODE 1)
- [ ] Content Display tab (MODE 2)
- [ ] Both Modes tab

**Pass/Fail:** ☐

---

### Test 5: Properties Grid - MODE 1 ✓
**Goal:** Verify schema property checkboxes work

**Steps:**
1. Select schema type: Article
2. Click "Schema Filtering (MODE 1)" tab
3. Observe properties grid
4. Check/uncheck several properties
5. Observe shortcode output updates

**Expected Results:**
- ✅ Properties displayed in grid layout
- ✅ Each property has checkbox
- ✅ Property names formatted nicely (e.g., "Date Published")
- ✅ Checking property adds to selected list
- ✅ Unchecking removes from list
- ✅ Required properties marked (@context, @type)
- ✅ Shortcode updates in real-time

**Test Properties (Article):**
- [ ] headline
- [ ] description
- [ ] author
- [ ] datePublished
- [ ] dateModified
- [ ] image

**Pass/Fail:** ☐

---

### Test 6: Properties Grid - MODE 2 ✓
**Goal:** Verify content display checkboxes work

**Steps:**
1. Click "Content Display (MODE 2)" tab
2. Observe different properties grid
3. Check/uncheck content elements
4. Observe shortcode updates

**Expected Results:**
- ✅ Different properties from MODE 1
- ✅ Content-focused properties (title, description, image, etc.)
- ✅ Checkboxes functional
- ✅ Shortcode attributes update with `hide_content_*` or `show_content_*`

**Test Properties (Article MODE 2):**
- [ ] title
- [ ] description
- [ ] author
- [ ] date
- [ ] image
- [ ] reading_time

**Pass/Fail:** ☐

---

### Test 7: Shortcode Generation ✓
**Goal:** Verify shortcode is generated correctly

**Steps:**
1. Select schema: Product
2. MODE 1: Check "name", "description", uncheck "brand"
3. Observe "Generated Shortcode" output box
4. Verify attributes present

**Expected Results:**
- ✅ Shortcode starts with `[kata_product`
- ✅ Contains `hide_brand="true"` (unchecked property)
- ✅ Updates in real-time as checkboxes change
- ✅ Properly formatted with line breaks
- ✅ Copy button visible

**Example Expected Output:**
```
[kata_product 
    name="Product Name"
    description="Product Description"
    hide_brand="true"]
```

**Pass/Fail:** ☐

---

### Test 8: Copy to Clipboard ✓
**Goal:** Verify copy button works

**Steps:**
1. Generate a shortcode (any type)
2. Click "Copy Shortcode" button
3. Paste into a text editor (Ctrl+V)

**Expected Results:**
- ✅ Button exists and is clickable
- ✅ Click shows success message (e.g., "Copied!")
- ✅ Pasted content matches displayed shortcode
- ✅ Shortcode is properly formatted

**Pass/Fail:** ☐

---

### Test 9: Preview Panels ✓
**Goal:** Verify JSON-LD and HTML previews work

**Steps:**
1. Select schema: Event
2. Check/uncheck some properties
3. Look at "Schema Preview (JSON-LD)" panel
4. Look at "HTML Preview" panel (if present)

**Expected Results:**
- ✅ JSON-LD preview displays valid JSON
- ✅ JSON updates when properties change
- ✅ Syntax highlighting applied (dark theme)
- ✅ @context and @type always present
- ✅ Filtered properties missing from JSON
- ✅ HTML preview shows expected output (if applicable)

**Pass/Fail:** ☐

---

### Test 10: Preset Save ✓
**Goal:** Verify preset save functionality

**Steps:**
1. Configure a schema (e.g., Article with specific properties)
2. Look for "Save Preset" button
3. Click it
4. Enter preset name in modal: "Test Article 1"
5. Click Save

**Expected Results:**
- ✅ Modal/dialog appears
- ✅ Input field for preset name
- ✅ Save button functional
- ✅ Success message appears
- ✅ Preset saved to database (wp_options)

**Pass/Fail:** ☐

---

### Test 11: Preset Load ✓
**Goal:** Verify preset load functionality

**Steps:**
1. Click "Load Preset" button
2. Modal shows with saved presets list
3. Select "Test Article 1" (from Test 10)
4. Click Load

**Expected Results:**
- ✅ Modal appears with preset list
- ✅ Previously saved preset visible
- ✅ Clicking preset loads configuration
- ✅ Properties checkboxes update
- ✅ Shortcode updates
- ✅ Modal closes

**Pass/Fail:** ☐

---

### Test 12: Preset Delete ✓
**Goal:** Verify preset delete functionality

**Steps:**
1. Open Load Preset modal
2. Find "Test Article 1" preset
3. Click delete/trash icon
4. Confirm deletion

**Expected Results:**
- ✅ Delete button/icon present
- ✅ Confirmation dialog appears (optional)
- ✅ Preset removed from list
- ✅ Database updated
- ✅ Success message shown

**Pass/Fail:** ☐

---

### Test 13: Multiple Schema Types ✓
**Goal:** Verify functionality across different types

**Steps:**
1. Test with FAQ schema
2. Test with Recipe schema
3. Test with Course schema
4. Verify properties are different for each

**Expected Results:**
- ✅ Properties change based on schema type
- ✅ Article: headline, author, datePublished
- ✅ FAQ: questions, answers
- ✅ Recipe: ingredients, instructions, cookTime
- ✅ Course: provider, instructor, duration
- ✅ Each generates correct shortcode

**Schema Type Tests:**
- [ ] Article - has author, headline
- [ ] FAQ - has mainEntity
- [ ] Recipe - has ingredients, instructions
- [ ] Product - has offers, brand
- [ ] Event - has location, organizer
- [ ] Course - has provider, duration

**Pass/Fail:** ☐

---

### Test 14: Both Modes Tab ✓
**Goal:** Verify "Both Modes" tab combines both panels

**Steps:**
1. Select schema: Product
2. Click "Both Modes" tab
3. Observe page layout

**Expected Results:**
- ✅ Both MODE 1 and MODE 2 panels visible
- ✅ Can check properties in MODE 1
- ✅ Can check properties in MODE 2
- ✅ Shortcode includes both types of attributes
- ✅ Example: `hide_brand="true" hide_content_price="true"`

**Pass/Fail:** ☐

---

### Test 15: Responsive Design ✓
**Goal:** Verify UI works on different screen sizes

**Steps:**
1. Resize browser window to narrow width (~768px)
2. Check layout adapts
3. Resize to mobile size (~480px)

**Expected Results:**
- ✅ Grid becomes single column on narrow screens
- ✅ Buttons remain accessible
- ✅ No horizontal scrolling
- ✅ Touch-friendly on mobile
- ✅ All features still functional

**Breakpoint Tests:**
- [ ] Desktop (> 1400px) - 2 columns
- [ ] Tablet (768-1400px) - 1 column
- [ ] Mobile (< 768px) - stacked layout

**Pass/Fail:** ☐

---

### Test 16: Tips & Documentation ✓
**Goal:** Verify help content is present

**Steps:**
1. Scroll to bottom of page
2. Look for "Tips & Best Practices" section
3. Read content

**Expected Results:**
- ✅ Tips section visible
- ✅ Clear usage instructions
- ✅ Examples provided
- ✅ Best practices listed
- ✅ Links to full documentation (optional)

**Pass/Fail:** ☐

---

## Browser Compatibility Tests

Test the admin UI in multiple browsers:

### Chrome/Edge
- [ ] Page loads correctly
- [ ] All features functional
- [ ] No console errors

### Firefox
- [ ] Page loads correctly
- [ ] All features functional
- [ ] No console errors

### Safari (if available)
- [ ] Page loads correctly
- [ ] All features functional
- [ ] No console errors

---

## Error Testing

### Test: Missing Required Fields
**Steps:**
1. Generate shortcode without filling required fields
2. Try to copy

**Expected:** Warning or validation message

**Pass/Fail:** ☐

---

### Test: AJAX Failures
**Steps:**
1. Disconnect from network (simulate)
2. Try to save preset
3. Check error handling

**Expected:** Error message displayed gracefully

**Pass/Fail:** ☐

---

### Test: Invalid JSON
**Steps:**
1. Check preview panel with complex schema
2. Verify JSON is valid

**Expected:** No JSON parse errors, pretty formatting

**Pass/Fail:** ☐

---

## Performance Tests

### Load Time
- [ ] Page loads in < 2 seconds
- [ ] Assets load efficiently
- [ ] No blocking resources

### Interactivity
- [ ] Checkbox changes instant (< 100ms)
- [ ] Tab switching smooth
- [ ] Shortcode updates real-time
- [ ] No lag when typing

### Memory
- [ ] No memory leaks (check dev tools)
- [ ] Smooth scrolling
- [ ] No freezing

---

## Accessibility Tests

### Keyboard Navigation
- [ ] Can tab through form elements
- [ ] Can select options with keyboard
- [ ] Can activate buttons with Enter/Space

### Screen Reader
- [ ] Labels properly associated
- [ ] ARIA attributes present (optional)
- [ ] Semantic HTML used

### Color Contrast
- [ ] Text readable on background
- [ ] Active states clearly visible
- [ ] Meets WCAG standards (optional)

---

## Final Checklist

Before marking complete:

- [ ] All 16 main tests passed
- [ ] Tested in at least 2 browsers
- [ ] No JavaScript console errors
- [ ] No PHP errors in debug.log
- [ ] Presets save/load/delete work
- [ ] Shortcode generation accurate
- [ ] Copy to clipboard works
- [ ] Preview panels update correctly
- [ ] All 15 schema types work
- [ ] MODE 1, MODE 2, Both Modes tabs functional

---

## Troubleshooting

### Issue: Menu not appearing
**Solution:**
```php
// Check kata-seo-manager.php line 121
KATA_Schema_Admin_UI::init();
```

### Issue: 404 on CSS/JS files
**Solution:**
```php
// Check file paths in class-schema-admin-ui.php
wp_enqueue_style('kata-schema-admin-ui', 
    KATA_SEO_MANAGER_PLUGIN_URL . 'assets/css/schema-admin-ui.css'
);
```

### Issue: Properties not loading
**Solution:** Check browser console, verify `kataSchemaAdmin` object exists

### Issue: AJAX not working
**Solution:** Check `wp_ajax_*` hooks registered in `init()` method

### Issue: Shortcode not generating
**Solution:** Check JavaScript `generateShortcode()` function

---

## Test Results Summary

**Date Tested:** ______________  
**Tester:** ______________  
**Browser:** ______________  
**OS:** ______________

**Tests Passed:** ___ / 16  
**Critical Issues:** ___  
**Minor Issues:** ___

**Overall Status:** ☐ Pass ☐ Fail ☐ Needs Work

**Notes:**
_____________________________________________
_____________________________________________
_____________________________________________

---

## Next Steps After Testing

1. ✅ Document any bugs found
2. ✅ Create bug fix tickets if needed
3. ✅ Take screenshots for documentation
4. ✅ Update user guide with screenshots
5. ✅ Test shortcodes generated by UI on frontend
6. ✅ Validate with Google Rich Results Test

---

**Generated:** 08/10/2025  
**Plugin Version:** 1.0.0  
**Admin UI Version:** 1.1.0
