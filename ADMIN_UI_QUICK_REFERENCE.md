# 🎯 Admin UI Testing - Quick Reference

## Access Information

**WordPress Admin:** http://localhost/timona/wp-admin  
**Direct Access:** http://localhost/timona/wp-admin/admin.php?page=kata-schema-customization

**Navigation:** kata-seo-manager → Schema Customization

## ✅ Pre-Test Verification (COMPLETE)

- ✅ class-schema-admin-ui.php exists (601 lines)
- ✅ schema-admin-ui.css exists (509 lines)
- ✅ schema-admin-ui.js exists (413 lines)
- ✅ Admin UI initialized (line 122)
- ✅ Submenu registered
- ✅ 4 AJAX handlers ready
- ✅ All classes loaded

## Quick Test Checklist

### Essential Tests (Must Pass)
1. ☐ Menu appears in admin sidebar
2. ☐ Page loads without errors
3. ☐ Schema type dropdown shows 15 types
4. ☐ Mode tabs (3) switch correctly
5. ☐ Properties checkboxes work
6. ☐ Shortcode generates correctly
7. ☐ Copy button works
8. ☐ No JavaScript console errors

### Feature Tests (Should Pass)
9. ☐ Preview panels update
10. ☐ Preset save works
11. ☐ Preset load works
12. ☐ Preset delete works
13. ☐ Multiple schema types work
14. ☐ Both modes tab works

### Polish Tests (Nice to Have)
15. ☐ Responsive on mobile
16. ☐ Tips section visible
17. ☐ Keyboard navigation
18. ☐ Fast performance

## Expected UI Elements

### Top Section
- Schema Type Dropdown (15 options)
- Mode Tabs (3 tabs)

### Left Panel - Schema Builder
- Properties Grid (checkboxes)
- Selected Properties List
- Tips & Best Practices

### Right Panel - Output
- Generated Shortcode Box
- Copy Button
- Schema Preview (JSON-LD)
- HTML Preview

### Bottom Section
- Save Preset Button
- Load Preset Button
- Documentation Links

## 15 Schema Types

1. Article
2. FAQ
3. Recipe
4. Product
5. Event
6. HowTo
7. Course
8. LocalBusiness
9. JobPosting
10. Video
11. Organization
12. Rating
13. Quiz
14. Poll
15. Wheel

## 3 Mode Tabs

1. **Schema Filtering (MODE 1)**
   - Controls JSON-LD properties
   - Uses hide_* and show_* attributes

2. **Content Display (MODE 2)**
   - Controls HTML visibility
   - Uses hide_content_* and show_content_* attributes

3. **Both Modes**
   - Shows both panels
   - Combines both attribute types

## Sample Test Flow

### Test 1: Basic Functionality
```
1. Login to WP admin
2. Click kata-seo-manager
3. Click Schema Customization
4. Select: Article
5. MODE 1: Uncheck "author"
6. Verify shortcode shows: hide_author="true"
7. Copy shortcode
8. ✅ PASS if all steps work
```

### Test 2: MODE 2
```
1. Select: Product
2. Click "Content Display (MODE 2)" tab
3. Uncheck "price"
4. Verify shortcode shows: hide_content_price="true"
5. ✅ PASS if attribute correct
```

### Test 3: Presets
```
1. Configure Article schema
2. Click "Save Preset"
3. Name: "My Article"
4. Save
5. Reload page
6. Click "Load Preset"
7. Select "My Article"
8. ✅ PASS if configuration restored
```

## Common Issues & Solutions

### Issue: Menu not visible
**Check:** Is user logged in as admin?  
**Check:** Plugin activated?

### Issue: Page blank
**Check:** Browser console for errors  
**Check:** wp-content/debug.log for PHP errors

### Issue: Shortcode not generating
**Check:** Browser console for JS errors  
**Check:** Properties selected?

### Issue: Properties not loading
**Check:** Schema type selected?  
**Check:** kataSchemaAdmin object in console

### Issue: Copy button not working
**Check:** Browser clipboard permissions  
**Check:** HTTPS vs HTTP (some browsers)

## Browser Console Commands

### Check if JavaScript loaded
```javascript
console.log(typeof KataSchemaAdmin);
// Should output: "object"
```

### Check default properties
```javascript
console.log(kataSchemaAdmin.defaultProperties);
// Should show object with all schema types
```

### Check current selection
```javascript
console.log(KataSchemaAdmin.selectedProperties);
// Shows currently selected properties
```

## Debugging Tips

1. **Open DevTools:** F12 or Right-click → Inspect
2. **Console Tab:** Check for errors (red text)
3. **Network Tab:** Check CSS/JS loaded (200 status)
4. **Elements Tab:** Inspect HTML structure

## Success Criteria

**Minimum (Must Pass):**
- Menu accessible ✓
- Page loads ✓
- Shortcode generates ✓
- No critical errors ✓

**Standard (Should Pass):**
- All 15 schema types work ✓
- All 3 modes work ✓
- Presets functional ✓
- UI responsive ✓

**Excellent (Nice to Have):**
- Fast performance ✓
- Great UX ✓
- Accessible ✓
- Well documented ✓

## Next Steps After Testing

1. ✅ Document test results
2. ✅ Take screenshots
3. ✅ Note any bugs
4. ✅ Test generated shortcodes on frontend
5. ✅ Validate with Google Rich Results

## Files Reference

**Testing Guide:** ADMIN_UI_TESTING_GUIDE.md (detailed tests)  
**Check Script:** check_admin_ui.sh (verification)  
**This File:** ADMIN_UI_QUICK_REFERENCE.md (quick ref)

---

**Last Updated:** 08/10/2025  
**Status:** ✅ Ready for Testing  
**All Files:** ✅ Verified
