# 🎯 Quick Testing Reference Card

## Test URLs
- **Quick Tests:** http://localhost/timona/test_quick_shortcodes.php
- **Full Suite:** http://localhost/timona/test_shortcodes_dual_mode.php
- **WordPress:** http://localhost/timona/wp-admin

## Testing Commands

### Check Plugin Status
```bash
cd /chikiet/webseo/timona
php -r "require('./wp-load.php'); echo class_exists('KATA_Schema_Customizer') ? '✅ Plugin loaded' : '❌ Failed';"
```

### View Error Log
```bash
tail -f /chikiet/webseo/timona/wp-content/debug.log
```

### Check Syntax
```bash
php -l /chikiet/webseo/timona/wp-content/plugins/kata-seo-manager/kata-seo-manager.php
```

## Sample Shortcodes for Quick Copy-Paste

### Article - Hide Author (MODE 1)
```
[kata_article title="Test" description="Testing" author="John Doe" hide_author="true"]
```

### Product - Hide Price Display (MODE 2)
```
[kata_product name="Test Product" price="999000" currency="VND" hide_content_price="true"]
```

### Event - Both Modes
```
[kata_event name="Test Event" start_date="2025-02-15T19:00" organizer_name="Test Org" hide_organizer="true" hide_content_location="true"]
```

## Validation Checklist

### MODE 1 (Schema Filtering)
- [ ] Open page source (Ctrl+U or Cmd+U)
- [ ] Search for `<script type="application/ld+json">`
- [ ] Check filtered property missing
- [ ] Verify @context and @type present
- [ ] Confirm HTML unchanged

### MODE 2 (Content Display)
- [ ] View rendered page
- [ ] Inspect element (F12)
- [ ] Check HTML element hidden/shown
- [ ] Verify JSON-LD complete
- [ ] Test interactive features

## Google Rich Results Test
1. Go to: https://search.google.com/test/rich-results
2. Enter URL or paste code
3. Check for errors
4. Verify rich snippet preview

## Expected Results Matrix

| Attribute | Schema (JSON-LD) | HTML Display |
|-----------|------------------|--------------|
| `hide_author="true"` | ❌ Author removed | ✅ Author shown |
| `hide_content_author="true"` | ✅ Author included | ❌ Author hidden |
| Both attributes | ❌ Author removed | ❌ Author hidden |
| `show_author="true"` | ✅ Author shown (overrides hide) | ✅ Author shown |

## Priority Rules
```
show_* > hide_* > schema_fields > default
show_content_* > hide_content_* > default
```

## Common Issues

**Shortcode as plain text?**
→ Plugin not activated

**No JSON-LD?**
→ Check wp_head() in theme

**Attributes not working?**
→ Check spelling (case-sensitive)

**Quiz/Poll not working?**
→ Check database tables exist

## Test Files Created
1. `test_quick_shortcodes.php` - 6 automated tests
2. `test_shortcodes_dual_mode.php` - Full test suite
3. `SHORTCODE_TEST_EXAMPLES.md` - 16 copy-paste examples
4. `FRONTEND_TESTING_SUMMARY.md` - Complete documentation

## Next Steps
1. ✅ Run quick tests → test_quick_shortcodes.php
2. ⏳ Test Admin UI → WordPress Admin
3. ⏳ Google validation → Rich Results Test
4. ⏳ Document results → Update summary

**Quick Access:** Open Simple Browser to test URLs above!
