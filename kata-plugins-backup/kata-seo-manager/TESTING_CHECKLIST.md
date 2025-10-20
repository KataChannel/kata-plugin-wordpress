# 🧪 ASSET MANAGER TESTING CHECKLIST

## Date: 20/10/2025
## Version: 2.1.4
## Tester: _______________

---

## ⚙️ PRE-TEST SETUP

- [ ] Backup database
- [ ] Backup plugin files (already done: assets.backup-20251020)
- [ ] Clear all caches (browser, WordPress, CDN)
- [ ] Enable WordPress debug mode
- [ ] Open browser DevTools (Console + Network tabs)

---

## 🔧 1. ADMIN AREA TESTING

### Dashboard Page
- [ ] Navigate to KATA SEO → Bảng điều khiển
- [ ] Page loads without errors
- [ ] CSS styles display correctly
- [ ] No console errors
- [ ] Check Network tab: kata-seo-admin.css loaded
- [ ] Check Network tab: kata-seo-admin.js loaded
- [ ] Check localization: `kataSeoAdmin` object exists in console

### Schema Statistics Page
- [ ] Navigate to KATA SEO → Thống kê Schema
- [ ] Page loads correctly
- [ ] Statistics display properly
- [ ] Charts render correctly
- [ ] No console errors
- [ ] Check Network tab: kata-seo-statistics.css loaded
- [ ] Check Network tab: kata-seo-statistics.js loaded

### Quiz Management Page
- [ ] Navigate to KATA SEO → Quiz Management
- [ ] Page loads correctly
- [ ] Forms work properly
- [ ] Color picker works
- [ ] No console errors

### Poll Management Page
- [ ] Navigate to KATA SEO → Quản lý Poll
- [ ] Page loads correctly
- [ ] Forms work properly
- [ ] No console errors

### Schema Builder (Post Editor)
- [ ] Open any post/page in editor
- [ ] Check console: No errors
- [ ] TinyMCE button appears
- [ ] Click TinyMCE button
- [ ] Schema builder dialog opens
- [ ] Check Network tab: kata-seo-schema-builder.css loaded
- [ ] Check Network tab: kata-seo-schema-dialog.css loaded
- [ ] Check Network tab: kata-seo-schema-builder.js loaded
- [ ] Check Network tab: kata-seo-schema-dialog.js loaded
- [ ] Check Network tab: schema-attributes-config.js loaded

**Admin Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 🌐 2. FRONTEND TESTING

### Base Frontend Assets
- [ ] Visit homepage
- [ ] Check console: No errors
- [ ] Check Network tab: kata-seo-frontend.css loaded
- [ ] Check Network tab: kata-seo-frontend.js loaded
- [ ] Check localization: `kata_ajax` object exists

### Poll Widget Testing
- [ ] Create test page with `[kata_poll id="X"]` shortcode
- [ ] Visit page
- [ ] Check console: No errors
- [ ] Check Network tab: kata-seo-poll.css loaded
- [ ] Check Network tab: kata-seo-poll.js loaded
- [ ] Poll displays correctly
- [ ] Select option
- [ ] Submit vote
- [ ] Results display correctly
- [ ] AJAX voting works

**Visit page WITHOUT poll shortcode:**
- [ ] Visit homepage (no poll)
- [ ] Check Network tab: kata-seo-poll.css NOT loaded ✅
- [ ] Check Network tab: kata-seo-poll.js NOT loaded ✅

### Quiz Widget Testing
- [ ] Create test page with `[kata_quiz id="X"]` shortcode
- [ ] Visit page
- [ ] Check console: No errors
- [ ] Check Network tab: kata-seo-quiz.css loaded
- [ ] Check Network tab: kata-seo-quiz.js loaded
- [ ] Quiz displays correctly
- [ ] Answer questions
- [ ] Submit quiz
- [ ] Results display correctly

**Visit page WITHOUT quiz shortcode:**
- [ ] Visit homepage (no quiz)
- [ ] Check Network tab: kata-seo-quiz.css NOT loaded ✅
- [ ] Check Network tab: kata-seo-quiz.js NOT loaded ✅

### Wheel Widget Testing
- [ ] Create test page with `[kata_wheel id="X"]` shortcode
- [ ] Visit page
- [ ] Check console: No errors
- [ ] Check Network tab: kata-seo-wheel.css loaded
- [ ] Check Network tab: kata-seo-wheel.js loaded
- [ ] Wheel displays correctly
- [ ] Fill form
- [ ] Spin wheel
- [ ] Animation works
- [ ] Result modal displays

**Visit page WITHOUT wheel shortcode:**
- [ ] Visit homepage (no wheel)
- [ ] Check Network tab: kata-seo-wheel.css NOT loaded ✅
- [ ] Check Network tab: kata-seo-wheel.js NOT loaded ✅

### Schema Markup Display
- [ ] Visit post with schema markup
- [ ] Schema displays correctly on frontend
- [ ] Check Network tab: schema-frontend.css loaded
- [ ] View page source
- [ ] Schema JSON-LD present in `<head>`

**Frontend Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## ⚡ 3. PERFORMANCE TESTING

### Homepage (No Widgets)
- [ ] Clear cache
- [ ] Visit homepage
- [ ] Count CSS files loaded: _____ (should be ~3-4)
- [ ] Count JS files loaded: _____ (should be ~3-4)
- [ ] Total CSS size: _____ KB
- [ ] Total JS size: _____ KB
- [ ] Page load time: _____ ms

### Page with Poll Only
- [ ] Clear cache
- [ ] Visit page with poll
- [ ] kata-seo-poll.css loaded: ✅
- [ ] kata-seo-poll.js loaded: ✅
- [ ] kata-seo-quiz.css NOT loaded: ✅
- [ ] kata-seo-wheel.css NOT loaded: ✅

### Page with All Widgets
- [ ] Clear cache
- [ ] Visit page with poll + quiz + wheel
- [ ] All widget CSS loaded: ✅
- [ ] All widget JS loaded: ✅
- [ ] Total page load time: _____ ms

**Performance Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 🔒 4. SECURITY TESTING

### AJAX Nonce Verification
- [ ] Open DevTools Console
- [ ] Type: `kataSeoAdmin.nonce` (admin)
- [ ] Nonce displays: ✅
- [ ] Type: `kata_ajax.nonce` (frontend)
- [ ] Nonce displays: ✅

### AJAX Calls
- [ ] Submit poll vote
- [ ] Check Network tab
- [ ] Verify nonce sent in POST data
- [ ] Response successful: ✅

**Security Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 🌍 5. COMPATIBILITY TESTING

### Browser Testing
- [ ] Chrome (latest): ✅ / ❌
- [ ] Firefox (latest): ✅ / ❌
- [ ] Safari (latest): ✅ / ❌
- [ ] Edge (latest): ✅ / ❌

### Mobile Testing
- [ ] iOS Safari: ✅ / ❌
- [ ] Android Chrome: ✅ / ❌
- [ ] Responsive design: ✅ / ❌

### Theme Testing
- [ ] Flatsome theme: ✅ / ❌
- [ ] Default WP theme: ✅ / ❌
- [ ] CSS conflicts: None / List: ___________

### Plugin Compatibility
- [ ] WooCommerce active: ✅ / ❌
- [ ] Contact Form 7 active: ✅ / ❌
- [ ] Yoast SEO active: ✅ / ❌
- [ ] Other plugins: _______________

**Compatibility Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 📱 6. RESPONSIVE TESTING

### Desktop (1920x1080)
- [ ] Admin pages display correctly
- [ ] Widgets display correctly
- [ ] No layout issues

### Tablet (768x1024)
- [ ] Admin pages responsive
- [ ] Widgets responsive
- [ ] Touch interactions work

### Mobile (375x667)
- [ ] Admin pages usable
- [ ] Widgets fully functional
- [ ] Wheel spins correctly
- [ ] Poll selects work
- [ ] Forms submit correctly

**Responsive Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 🐛 7. ERROR TESTING

### PHP Errors
- [ ] Check `wp-content/debug.log`
- [ ] No PHP errors: ✅ / ❌
- [ ] No PHP warnings: ✅ / ❌
- [ ] No deprecation notices: ✅ / ❌

### JavaScript Errors
- [ ] Check browser console
- [ ] No JS errors: ✅ / ❌
- [ ] No JS warnings: ✅ / ❌

### Network Errors
- [ ] Check Network tab
- [ ] All assets load (no 404): ✅ / ❌
- [ ] All AJAX calls succeed: ✅ / ❌

**Error Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 📊 8. FUNCTIONAL TESTING

### Poll Functionality
- [ ] Vote on poll: ✅ / ❌
- [ ] See results: ✅ / ❌
- [ ] Prevent double voting: ✅ / ❌
- [ ] Animation smooth: ✅ / ❌

### Quiz Functionality
- [ ] Take quiz: ✅ / ❌
- [ ] Submit answers: ✅ / ❌
- [ ] See results: ✅ / ❌
- [ ] Score calculation correct: ✅ / ❌

### Wheel Functionality
- [ ] Fill user info: ✅ / ❌
- [ ] Spin wheel: ✅ / ❌
- [ ] Animation smooth: ✅ / ❌
- [ ] Result displays: ✅ / ❌
- [ ] Spin limit enforced: ✅ / ❌

### Schema Functionality
- [ ] Add schema to post: ✅ / ❌
- [ ] Edit schema: ✅ / ❌
- [ ] Delete schema: ✅ / ❌
- [ ] Schema outputs correctly: ✅ / ❌
- [ ] Valid JSON-LD: ✅ / ❌

**Functional Test Result:** ✅ PASS / ❌ FAIL  
**Notes:** ________________________________

---

## 🎯 FINAL ASSESSMENT

### Overall Test Results

| Category | Status | Notes |
|----------|--------|-------|
| Admin Testing | ☐ PASS ☐ FAIL | |
| Frontend Testing | ☐ PASS ☐ FAIL | |
| Performance Testing | ☐ PASS ☐ FAIL | |
| Security Testing | ☐ PASS ☐ FAIL | |
| Compatibility Testing | ☐ PASS ☐ FAIL | |
| Responsive Testing | ☐ PASS ☐ FAIL | |
| Error Testing | ☐ PASS ☐ FAIL | |
| Functional Testing | ☐ PASS ☐ FAIL | |

### Issues Found

1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

### Recommendations

1. _______________________________________________
2. _______________________________________________
3. _______________________________________________

### Final Verdict

- ☐ **APPROVED** - Ready for production
- ☐ **APPROVED WITH MINOR ISSUES** - Deploy with monitoring
- ☐ **NEEDS WORK** - Fix issues before deployment
- ☐ **REJECTED** - Major issues, rollback required

---

## 📝 SIGN-OFF

**Tester Name:** _______________  
**Date Tested:** _______________  
**Time Spent:** _______________  
**Signature:** _______________

**Developer Review:** _______________  
**Date Reviewed:** _______________  
**Signature:** _______________

---

## 🔄 ROLLBACK INSTRUCTIONS

If tests fail and rollback is needed:

```bash
cd /chikiet/webseo/timona/wp-content/plugins/kata-seo-manager

# Option 1: Restore from backup
rm -rf assets
cp -r assets.backup-20251020 assets

# Option 2: Git revert
git revert HEAD

# Option 3: Manual fix
# Edit kata-seo-manager.php
# Remove Asset Manager initialization
# Restore old enqueue methods
```

**Rollback Contact:** support@katachannel.com
