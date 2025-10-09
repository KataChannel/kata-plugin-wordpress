# Website Restoration Checklist ✅

## ✅ Database Connection
- [x] Created new MySQL user: `timona_user`
- [x] Granted all privileges on `tazaspac_wp_timona`
- [x] Updated wp-config.php with new credentials
- [x] Verified connection: 65 tables accessible

## ✅ URL Configuration
- [x] Updated WP_HOME to `http://localhost/timona`
- [x] Updated WP_SITEURL to `http://localhost/timona`
- [x] Disabled SSL session cookies for localhost
- [x] Updated database URLs (14,454 rows)

## ✅ SSL & Redirects
- [x] Deactivated Really Simple SSL plugin
- [x] Removed HTTPS redirect from wp-config.php
- [x] Cleared all transients and cache
- [x] Verified no HTTPS redirects in .htaccess

## ✅ Permalinks & Rewrite Rules
- [x] Flushed WordPress rewrite rules
- [x] Verified .htaccess configuration
- [x] Confirmed mod_rewrite is enabled
- [x] Tested permalink structure

## ✅ Verification Tests
- [x] Homepage: HTTP 200 OK ✅
- [x] Page title: "Học viện đào tạo thẩm mỹ Quốc tế Timona Academy" ✅
- [x] wp-admin: Login page accessible ✅
- [x] WordPress API: Responding correctly ✅
- [x] Root redirect: Correctly redirects to /timona/ ✅

## ✅ Helper Scripts Created
- [x] flush_rewrite.php - Flush rewrite rules
- [x] search_replace_urls.php - Replace URLs in database
- [x] deactivate_ssl.php - Deactivate SSL for local dev
- [x] test_php.php - PHP diagnostics

## ✅ Documentation
- [x] WEBSITE_FIX_SUMMARY.md - Complete fix documentation
- [x] WEBSITE_RESTORATION_CHECKLIST.md - This checklist

---

## Final Status: WEBSITE FULLY OPERATIONAL ✅

**Website URL:** http://localhost/timona/  
**Database:** Connected and working  
**WordPress:** Fully functional  
**Admin Panel:** Accessible  

**Date Completed:** October 9, 2025  
**Time to Fix:** ~45 minutes  
**Issues Resolved:** 7 critical issues  

---

## Quick Access

### Start Website
```bash
# Apache should already be running
sudo systemctl status apache2

# If not, start it:
sudo systemctl start apache2
```

### Access Website
- **Homepage:** http://localhost/timona/
- **Admin:** http://localhost/timona/wp-admin/

### Database Access
```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona
```

### Restart Services (if needed)
```bash
sudo systemctl restart apache2
sudo systemctl restart mysql
```

---

## Common Commands

### Flush Rewrite Rules
```bash
cd /mnt/chikiet/webseo/timona
php flush_rewrite.php
```

### Clear Cache
```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "DELETE FROM gt_options WHERE option_name LIKE '_transient%';"
```

### Check Apache Status
```bash
systemctl status apache2
curl -I http://localhost/timona/
```

### Check Database Connection
```bash
mysql -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona -e "SELECT COUNT(*) FROM gt_posts;"
```

---

## Maintenance Tasks

### Daily
- [ ] Check website is accessible
- [ ] Verify database connection

### Weekly  
- [ ] Backup database
- [ ] Backup WordPress files
- [ ] Check for WordPress updates

### Monthly
- [ ] Review error logs
- [ ] Update plugins (if any)
- [ ] Test all major functionality

---

## Backup Commands

### Database Backup
```bash
mysqldump -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Files Backup
```bash
tar -czf timona_files_$(date +%Y%m%d_%H%M%S).tar.gz /mnt/chikiet/webseo/timona/
```

### Full Backup (Database + Files)
```bash
# Backup database
mysqldump -u timona_user -p'Timona@2025!Strong' tazaspac_wp_timona > /tmp/db_backup.sql

# Backup everything
tar -czf timona_full_backup_$(date +%Y%m%d_%H%M%S).tar.gz \
  /mnt/chikiet/webseo/timona/ \
  /tmp/db_backup.sql

# Clean up
rm /tmp/db_backup.sql
```

---

**All tasks completed successfully! ✅**
