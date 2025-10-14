# 🔄 Database Restore Scripts - User Guide

**Created:** October 13, 2025  
**Purpose:** Automated WordPress database restore with URL replacement

---

## 📁 Available Scripts

### 1️⃣ `restore_sql.sh` - Quick & Simple ⭐ RECOMMENDED

**Fastest way to restore a specific SQL file**

**Usage:**
```bash
./restore_sql.sh <filename.sql>
```

**Examples:**
```bash
# Restore using filename only
./restore_sql.sh tazaspac_wp_timona_13102025.sql

# Restore using full path
./restore_sql.sh /path/to/backup.sql

# List available files
./restore_sql.sh
```

**Features:**
- ✅ Auto-creates safety backup
- ✅ Drops and recreates database
- ✅ Restores from SQL file
- ✅ Updates URLs automatically
- ✅ Shows statistics
- ⚡ Fast execution (~1-2 minutes)

---

### 2️⃣ `quick_restore.sh` - Interactive Menu

**User-friendly menu for common restore scenarios**

**Usage:**
```bash
./quick_restore.sh
```

**Menu Options:**
1. **Restore latest backup** - Auto-selects newest .sql file
2. **Restore specific backup** - Choose from list
3. **Restore with custom file path** - Enter any path
4. **Fast mode** - Skip creating backup (faster)
5. **Keep original URLs** - Skip URL replacement
6. **Show available backups** - List all .sql files
7. **Advanced options** - Custom old/new URLs

**Best for:**
- First-time users
- When you're not sure which file to use
- Testing different restore options

---

### 3️⃣ `auto_restore_db.sh` - Full-Featured

**Complete restore script with all options**

**Usage:**
```bash
./auto_restore_db.sh [OPTIONS]
```

**Command Line Options:**
```bash
-f, --file FILE         Specify SQL file to restore
--skip-backup           Skip creating safety backup
--skip-url-update       Skip URL replacement
--old-url URL           Old URL to replace
--new-url URL           New URL to use
-y, --yes               Auto-confirm all prompts
-h, --help              Show help message
```

**Examples:**
```bash
# Basic restore with interactive file selection
./auto_restore_db.sh

# Restore specific file
./auto_restore_db.sh -f backup.sql

# Restore with custom URLs
./auto_restore_db.sh -f backup.sql \
  --old-url https://example.com \
  --new-url http://localhost/mysite

# Fast restore (skip backup, auto-confirm)
./auto_restore_db.sh -f backup.sql --skip-backup -y

# Restore without URL update
./auto_restore_db.sh -f backup.sql --skip-url-update
```

**Best for:**
- Advanced users
- Automated scripts
- Custom restore scenarios
- Production deployments

---

## 🚀 Quick Start Guide

### Step 1: Make Scripts Executable

```bash
cd /mnt/chikiet/webseo/timona
chmod +x restore_sql.sh quick_restore.sh auto_restore_db.sh
```

### Step 2: Choose Your Method

**For Beginners:**
```bash
./quick_restore.sh
# Then select option 1 or 2
```

**For Quick Restore:**
```bash
./restore_sql.sh tazaspac_wp_timona_13102025.sql
```

**For Advanced Use:**
```bash
./auto_restore_db.sh -f mybackup.sql -y
```

---

## 📊 What Each Script Does

### Restore Process (All Scripts):

1. **✅ Check MySQL Connection**
   - Verifies database credentials
   - Tests connectivity

2. **✅ Create Safety Backup**
   - Backs up current database
   - Saved as: `backup_before_restore_YYYYMMDD_HHMMSS.sql`

3. **✅ Drop & Recreate Database**
   - Removes old database
   - Creates fresh database with utf8mb4

4. **✅ Restore from SQL File**
   - Imports SQL file
   - Shows progress indicator

5. **✅ Update URLs**
   - Replaces old domain with localhost
   - Updates in:
     - Options table (siteurl, home)
     - Posts (content, excerpts, GUIDs)
     - Postmeta
     - Comments
     - Usermeta
     - Termmeta

6. **✅ Verify & Report**
   - Shows database statistics
   - Generates restore report
   - Displays access URLs

---

## ⚙️ Default Configuration

**Database:**
- Name: `tazaspac_wp_timona`
- User: `timona_user`
- Password: `Timona@2025!Strong`
- Host: `localhost`

**URLs:**
- Old: `https://timona.alodigital.edu.vn`
- New: `http://localhost/timona`

**To customize:** Edit the script files directly or use `--old-url` and `--new-url` flags.

---

## 📋 Common Use Cases

### Case 1: Daily Development Restore
```bash
# Quick restore of latest backup
./quick_restore.sh
# Select option 1
```

### Case 2: Restore Specific Date
```bash
# Restore backup from specific date
./restore_sql.sh tazaspac_wp_timona_13102025.sql
```

### Case 3: Fast Restore (No Backup)
```bash
# Skip backup creation for speed
./auto_restore_db.sh -f myfile.sql --skip-backup -y
```

### Case 4: Migrate from Production
```bash
# Custom URLs for site migration
./auto_restore_db.sh -f production_backup.sql \
  --old-url https://production-site.com \
  --new-url http://localhost/dev-site
```

### Case 5: Test Different Backups
```bash
# Interactive menu to try different files
./quick_restore.sh
# Select option 2 for file selection
```

---

## 🛡️ Safety Features

### Automatic Backups
- Safety backup created before every restore
- Named: `backup_before_restore_YYYYMMDD_HHMMSS.sql`
- Can be used to rollback

### Rollback Procedure
```bash
# Find your backup
ls -lt backup_before_restore_*.sql | head -1

# Restore it
./restore_sql.sh backup_before_restore_20251013_111521.sql
```

### Error Handling
- Scripts check MySQL connectivity
- Validate SQL file exists
- Confirm before destructive operations
- Clear error messages

---

## 📝 Output & Reports

### Console Output
```
════════════════════════════════════════════════
  Quick Database Restore
════════════════════════════════════════════════

📦 File: tazaspac_wp_timona_13102025.sql (84M)
🗄️  Database: tazaspac_wp_timona
🔄 URL: https://timona.alodigital.edu.vn → http://localhost/timona

Creating backup... ✓
Recreating database... ✓
Restoring from SQL file... ✓
Updating URLs... ✓

════════════════════════════════════════════════
✅ Restore Complete!
════════════════════════════════════════════════

📊 Stats: 65 tables, 10882 posts, 3 users
💾 Backup: backup_before_restore_20251013_120000.sql

🌐 Access: http://localhost/timona
🔐 Admin: http://localhost/timona/wp-admin
```

### Restore Report File
Generated automatically: `restore_report_YYYYMMDD_HHMMSS.txt`

Contains:
- Source file details
- Database statistics
- URL migration info
- Safety backup location
- Access URLs
- Next steps checklist

---

## 🔧 Troubleshooting

### Error: "Cannot connect to MySQL"
**Solution:**
```bash
# Test MySQL connection
mysql -u timona_user -p'Timona@2025!Strong' -e "SELECT 1;"

# Check credentials in wp-config.php
cat wp-config.php | grep DB_
```

### Error: "SQL file not found"
**Solution:**
```bash
# List available files
ls -lh *.sql

# Use full path
./restore_sql.sh /full/path/to/file.sql
```

### Error: "Permission denied"
**Solution:**
```bash
# Make scripts executable
chmod +x restore_sql.sh quick_restore.sh auto_restore_db.sh

# Or run with bash
bash restore_sql.sh myfile.sql
```

### Database restored but site shows errors
**Solution:**
```bash
# Flush permalinks
wp rewrite flush --path=/mnt/chikiet/webseo/timona

# Or visit in browser:
# http://localhost/timona/wp-admin/options-permalink.php
# Click "Save Changes"
```

### URLs not updated correctly
**Solution:**
```bash
# Manual URL update
./auto_restore_db.sh -f backup.sql \
  --old-url "https://old-site.com" \
  --new-url "http://localhost/timona"
```

---

## 📚 Advanced Examples

### Example 1: Scheduled Daily Restore
```bash
# Add to crontab for daily dev environment refresh
0 9 * * * cd /mnt/chikiet/webseo/timona && ./restore_sql.sh latest_backup.sql
```

### Example 2: Restore Multiple Sites
```bash
# Script to restore multiple WordPress databases
for site in site1 site2 site3; do
  ./auto_restore_db.sh \
    -f "${site}_backup.sql" \
    --old-url "https://${site}.com" \
    --new-url "http://localhost/${site}" \
    -y
done
```

### Example 3: Restore with Custom MySQL Credentials
Edit the script and change:
```bash
DB_NAME="your_database"
DB_USER="your_user"
DB_PASS="your_password"
```

---

## ✅ Post-Restore Checklist

After restore completes, do these steps:

1. **✅ Visit Frontend**
   - URL: http://localhost/timona
   - Check homepage loads

2. **✅ Login to Admin**
   - URL: http://localhost/timona/wp-admin
   - Use production credentials

3. **✅ Flush Permalinks**
   - Settings > Permalinks
   - Click "Save Changes"

4. **✅ Clear Cache**
   - Browser cache (Ctrl + Shift + R)
   - WordPress cache plugin (if any)

5. **✅ Test Features**
   - FAQ toggle functionality
   - Wheel spinner
   - Chatbot widget
   - Search functionality

6. **✅ Check for Errors**
   - Browser console (F12)
   - WordPress debug log
   - PHP error logs

---

## 🎯 Performance Tips

### Faster Restores
```bash
# Skip backup creation (saves 30-60 seconds)
./auto_restore_db.sh -f backup.sql --skip-backup -y

# Skip URL updates (saves 10-20 seconds)
./auto_restore_db.sh -f backup.sql --skip-url-update -y
```

### Smaller Backup Files
```bash
# Export without auto-increment values (smaller file)
mysqldump --skip-opt --add-drop-table --create-options \
  -u timona_user -p'Timona@2025!Strong' \
  tazaspac_wp_timona > compact_backup.sql
```

---

## 📞 Support & Help

### Get Help
```bash
# Show help for auto_restore_db.sh
./auto_restore_db.sh --help

# List available backups
./quick_restore.sh
# Select option 6
```

### Common Issues
- Database connection errors → Check wp-config.php
- File not found → Use full file path
- Permission denied → Run `chmod +x script.sh`
- Site broken after restore → Flush permalinks

---

## 🔐 Security Notes

- **Never commit passwords to git**
- Scripts contain database password (keep secure)
- Use `.gitignore` to exclude `*.sh` files if needed
- Backup files contain sensitive data (protect them)

---

## 📅 Maintenance

### Cleanup Old Backups
```bash
# Delete backups older than 30 days
find /mnt/chikiet/webseo/timona -name "backup_before_restore_*.sql" -mtime +30 -delete
```

### Verify Backup Integrity
```bash
# Test if SQL file is valid
mysql -u timona_user -p'Timona@2025!Strong' \
  --execute="SOURCE /path/to/backup.sql;" test_db
```

---

**Scripts Version:** 1.0  
**Last Updated:** October 13, 2025  
**Author:** GitHub Copilot  
**License:** MIT

---

**Happy Restoring! 🚀**
