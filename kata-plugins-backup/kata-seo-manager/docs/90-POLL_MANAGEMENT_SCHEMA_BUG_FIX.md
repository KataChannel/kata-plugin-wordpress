# POLL MANAGEMENT - Database Schema Bug Fix

## ❌ Bug Report

**Date:** October 6, 2025  
**Error:** `Warning: Undefined property: stdClass::$status`  
**Location:** Poll Management Admin Page  
**Files:** `admin/poll-management.php`, `kata-seo-manager.php`

### Error Messages:

```
Warning: Undefined property: stdClass::$status 
in /mnt/chikiet/webseo/timona/wp-content/plugins/kata-seo-manager/admin/poll-management.php 
on line 18

Warning: Undefined property: stdClass::$status 
in /mnt/chikiet/webseo/timona/wp-content/plugins/kata-seo-manager/admin/poll-management.php 
on line 20
```

### Impact:
- ❌ Poll Management page shows PHP warnings
- ❌ Poll status statistics incorrect
- ❌ Poll status display broken
- ❌ Toggle status functionality fails
- ❌ Create/Update poll operations fail with database errors

---

## 🔍 Root Cause Analysis

### Problem: Code vs Database Schema Mismatch

**Current Database Schema:**
```sql
-- Actual kata_polls table structure (11 columns)
kata_polls:
├─ id (bigint)                     ✅ EXISTS
├─ title (varchar(500))            ✅ EXISTS
├─ description (text)              ✅ EXISTS  
├─ options (longtext)              ✅ EXISTS
├─ allow_multiple (tinyint(1))     ✅ EXISTS
├─ show_results (tinyint(1))       ✅ EXISTS
├─ require_login (tinyint(1))      ✅ EXISTS
├─ active (tinyint(1))             ✅ EXISTS (0/1 values)
├─ total_votes (int)               ✅ EXISTS
├─ created_at (datetime)           ✅ EXISTS
└─ updated_at (datetime)           ✅ EXISTS
```

**Code Expected Schema:**
```sql
-- What poll management code tries to access
kata_polls:
├─ status (varchar)                ❌ MISSING (expects 'active'/'closed'/'draft')
├─ post_id (bigint)                ❌ MISSING
├─ poll_title (varchar)            ❌ MISSING (should use 'title')
├─ poll_description (text)         ❌ MISSING (should use 'description')
├─ poll_question (text)            ❌ MISSING (separate field)
├─ poll_options (longtext)         ❌ MISSING (should use 'options')
└─ String values for booleans      ❌ WRONG ('yes'/'no' instead of 1/0)
```

### Column Mapping Issues:

| Code Uses | Database Has | Status | Action Taken |
|-----------|--------------|--------|--------------|
| `$poll->status` | `active` (tinyint) | ❌ MISMATCH | ✅ Map: `active` ? 'active' : 'closed' |
| `poll_title` | `title` | ❌ WRONG NAME | ✅ Fixed: Use `title` |
| `poll_description` | `description` | ❌ WRONG NAME | ✅ Fixed: Use `description` |
| `poll_options` | `options` | ❌ WRONG NAME | ✅ Fixed: Use `options` |
| `poll_question` | N/A | ❌ MISSING | ✅ Fixed: Combine with description |
| `post_id` | N/A | ❌ MISSING | ✅ Fixed: Removed |
| `'yes'/'no'` | 1/0 | ❌ WRONG TYPE | ✅ Fixed: Use 1/0 |

---

## ✅ Solutions Applied

### Fix 1: Status Property Mapping
**File:** `admin/poll-management.php` (Lines 17-19)

**Before:**
```php
// Calculate statistics  
$active_polls = count(array_filter($polls, function($p) { return $p->status === 'active'; }));  // ❌ $p->status doesn't exist
$closed_polls = count(array_filter($polls, function($p) { return $p->status === 'closed'; })); // ❌ $p->status doesn't exist
```

**After:**
```php
// Calculate statistics
$active_polls = count(array_filter($polls, function($p) { return $p->active == 1; }));        // ✅ Use existing 'active' column
$closed_polls = count(array_filter($polls, function($p) { return $p->active == 0; }));        // ✅ Use existing 'active' column
```

### Fix 2: Status Display Logic
**File:** `admin/poll-management.php` (Lines 125-145)

**Before:**
```php
foreach ($polls as $poll): 
    $options = json_decode($poll->poll_options, true);  // ❌ Column doesn't exist
    
    // Calculate status class
    $status_class = 'status-' . $poll->status;          // ❌ Property doesn't exist
    $status_text = '';
    $status_icon = '';
    
    switch ($poll->status) {                            // ❌ Property doesn't exist
        case 'active':
            $status_text = 'Hoạt động';
            break;
        // ...
    }
```

**After:**
```php
foreach ($polls as $poll): 
    $options = json_decode($poll->options, true);       // ✅ Use existing 'options' column
    
    // Map active column to status strings
    $status = $poll->active ? 'active' : 'closed';     // ✅ Map tinyint to string
    
    // Calculate status class
    $status_class = 'status-' . $status;                // ✅ Use mapped status
    $status_text = '';
    $status_icon = '';
    
    switch ($status) {                                  // ✅ Use mapped status
        case 'active':
            $status_text = 'Hoạt động';
            break;
        case 'closed':
            $status_text = 'Đã đóng';
            break;
    }
```

### Fix 3: Status Toggle Buttons
**File:** `admin/poll-management.php` (Lines 149, 200-201)

**Before:**
```php
<div class="poll-card" data-poll-id="<?php echo $poll->id; ?>" data-status="<?php echo $poll->status; ?>">  // ❌ Property doesn't exist

<button type="button" class="action-btn toggle-status-btn" 
        data-poll-id="<?php echo $poll->id; ?>" 
        data-status="<?php echo $poll->status; ?>"                                                        // ❌ Property doesn't exist
        title="<?php echo $poll->status === 'active' ? 'Tạm dừng' : 'Kích hoạt'; ?>">                   // ❌ Property doesn't exist
    <span class="dashicons dashicons-controls-<?php echo $poll->status === 'active' ? 'pause' : 'play'; ?>"></span>  // ❌ Property doesn't exist
</button>
```

**After:**
```php
<div class="poll-card" data-poll-id="<?php echo $poll->id; ?>" data-status="<?php echo $status; ?>">     // ✅ Use mapped status

<button type="button" class="action-btn toggle-status-btn" 
        data-poll-id="<?php echo $poll->id; ?>" 
        data-status="<?php echo $status; ?>"                                                             // ✅ Use mapped status
        title="<?php echo $status === 'active' ? 'Tạm dừng' : 'Kích hoạt'; ?>">                        // ✅ Use mapped status
    <span class="dashicons dashicons-controls-<?php echo $status === 'active' ? 'pause' : 'play'; ?>"></span>       // ✅ Use mapped status
</button>
```

### Fix 4: Create Poll Database Insert
**File:** `kata-seo-manager.php` (Lines 835-850)

**Before:**
```php
$result = $wpdb->insert(
    $wpdb->prefix . 'kata_polls',
    array(
        'post_id' => 0,                           // ❌ Column doesn't exist
        'poll_title' => $title,                   // ❌ Column doesn't exist (should be 'title')
        'poll_description' => $description,       // ❌ Column doesn't exist (should be 'description')
        'poll_question' => $question,             // ❌ Column doesn't exist
        'poll_options' => json_encode($options),  // ❌ Column doesn't exist (should be 'options')
        'total_votes' => 0,                       // ✅ OK
        'status' => 'active',                     // ❌ Column doesn't exist (should be 'active' => 1)
        'show_results' => 'after_vote',           // ❌ Wrong type (should be 1/0, not string)
        'allow_multiple' => 'no',                 // ❌ Wrong type (should be 1/0, not string)
        'require_login' => 'no',                  // ❌ Wrong type (should be 1/0, not string)
        'created_at' => current_time('mysql'),    // ✅ OK
        'updated_at' => current_time('mysql')     // ✅ OK
    ),
    array('%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s')  // ❌ Wrong format strings
);
```

**After:**
```php
$result = $wpdb->insert(
    $wpdb->prefix . 'kata_polls',
    array(
        'title' => $title,                        // ✅ Correct column name
        'description' => $description . "\n\nCâu hỏi: " . $question,  // ✅ Combine description + question
        'options' => json_encode($options),       // ✅ Correct column name
        'total_votes' => 0,                       // ✅ OK
        'active' => 1,                            // ✅ Correct column name and type
        'show_results' => 1,                      // ✅ Correct type (1/0 instead of string)
        'allow_multiple' => 0,                    // ✅ Correct type (1/0 instead of string)
        'require_login' => 0,                     // ✅ Correct type (1/0 instead of string)
        'created_at' => current_time('mysql'),    // ✅ OK
        'updated_at' => current_time('mysql')     // ✅ OK
    ),
    array('%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%s', '%s')  // ✅ Correct format strings
);
```

### Fix 5: Update Poll Database Update
**File:** `kata-seo-manager.php` (Lines 883-895)

**Before:**
```php
$result = $wpdb->update(
    $wpdb->prefix . 'kata_polls',
    array(
        'poll_title' => $title,                   // ❌ Column doesn't exist
        'poll_description' => $description,       // ❌ Column doesn't exist
        'poll_question' => $question,             // ❌ Column doesn't exist
        'poll_options' => json_encode($options),  // ❌ Column doesn't exist
        'status' => $status,                      // ❌ Column doesn't exist
        'updated_at' => current_time('mysql')
    ),
    array('id' => $poll_id),
    array('%s', '%s', '%s', '%s', '%s', '%s'),    // ❌ Wrong format strings
    array('%d')
);
```

**After:**
```php
// Map status to active column
$active_value = ($status === 'active') ? 1 : 0;

$result = $wpdb->update(
    $wpdb->prefix . 'kata_polls',
    array(
        'title' => $title,                        // ✅ Correct column name
        'description' => $description . "\n\nCâu hỏi: " . $question,  // ✅ Combine fields
        'options' => json_encode($options),       // ✅ Correct column name
        'active' => $active_value,                // ✅ Correct column name and type
        'updated_at' => current_time('mysql')
    ),
    array('id' => $poll_id),
    array('%s', '%s', '%s', '%d', '%s'),          // ✅ Correct format strings
    array('%d')
);
```

### Fix 6: Toggle Status Database Update
**File:** `kata-seo-manager.php` (Lines 942-950)

**Before:**
```php
$result = $wpdb->update(
    $wpdb->prefix . 'kata_polls',
    array('status' => $new_status, 'updated_at' => current_time('mysql')),  // ❌ 'status' column doesn't exist
    array('id' => $poll_id),
    array('%s', '%s'),                            // ❌ Wrong format for 'status'
    array('%d')
);
```

**After:**
```php
// Map status strings to active column values
$active_value = ($new_status === 'active') ? 1 : 0;

$result = $wpdb->update(
    $wpdb->prefix . 'kata_polls',
    array('active' => $active_value, 'updated_at' => current_time('mysql')),  // ✅ Use 'active' column with 1/0
    array('id' => $poll_id),
    array('%d', '%s'),                            // ✅ Correct format strings
    array('%d')
);
```

---

## 📊 Changes Summary

### Files Modified: 2

| File | Changes | Lines Modified |
|------|---------|----------------|
| `admin/poll-management.php` | Status property mapping | ~8 locations |
| `kata-seo-manager.php` | Database operations fix | 3 methods |

### Database Operation Fixes:

| Method | Old Columns | New Columns | Status |
|--------|-------------|-------------|--------|
| `handle_create_poll()` | 12 wrong columns | 10 correct columns | ✅ Fixed |
| `handle_update_poll()` | 6 wrong columns | 5 correct columns | ✅ Fixed |
| `handle_toggle_poll_status()` | 1 wrong column | 1 correct column | ✅ Fixed |

### Status Mapping Logic:

```php
// Database: active (tinyint) 0 or 1
// Display: status (string) 'active' or 'closed'

$status = $poll->active ? 'active' : 'closed';  // Display mapping
$active_value = ($status === 'active') ? 1 : 0; // Database mapping
```

---

## 🛠️ Current Status

### ✅ What Works Now:
- ✅ Poll Management page loads without warnings
- ✅ Poll statistics calculate correctly
- ✅ Poll status displays properly (Active/Closed)
- ✅ Status toggle buttons work
- ✅ Create new polls saves to database
- ✅ Update existing polls works
- ✅ Toggle poll status works
- ✅ Poll options display correctly
- ✅ No database column errors

### 🎯 Result Summary:

**Before Fix:**
```
❌ Undefined property: stdClass::$status warnings
❌ Poll creation fails with database errors
❌ Poll updates fail with wrong column names
❌ Status toggle doesn't work
❌ Statistics show wrong counts
❌ Frontend displays broken
```

**After Fix:**
```
✅ No PHP warnings or errors
✅ Poll management page fully functional
✅ Create polls works with proper column mapping
✅ Update polls saves correctly
✅ Status toggle updates database properly
✅ Statistics display accurate counts
✅ All poll operations work correctly
```

---

## 🧪 Testing

### Test 1: Poll Management Page Load
```
1. Go to: KATA SEO Manager → Poll Management
2. Verify: Page loads without PHP warnings ✅
3. Verify: Statistics show correct numbers ✅
4. Verify: Poll cards display with proper status ✅
```

### Test 2: Create New Poll
```
1. Click "Tạo Poll Mới" button
2. Fill in: Title, Description, Question, Options
3. Click "Tạo Poll"
4. Verify: Poll saves successfully ✅
5. Verify: Appears in poll list ✅
6. Verify: Status shows as "Hoạt động" ✅
```

### Test 3: Toggle Poll Status
```
1. Find active poll with play/pause button
2. Click toggle button
3. Verify: Status changes from "Hoạt động" to "Đã đóng" ✅
4. Verify: Database active column updates ✅
5. Verify: Button icon changes ✅
```

### Test 4: Update Existing Poll
```
1. Click "Sửa" button on a poll
2. Modify title, description, options
3. Click "Cập Nhật"
4. Verify: Changes save successfully ✅
5. Verify: Updated data displays correctly ✅
```

---

## 📝 Notes

### Schema Design Choices

**Why use `active` (tinyint) instead of `status` (varchar)?**
1. ✅ **Performance:** Integer comparison faster than string
2. ✅ **Storage:** 1 byte vs variable length
3. ✅ **Validation:** Only 2 possible values (0/1)
4. ✅ **Database consistency:** Matches other boolean columns

**Column Mapping Strategy:**
```
Database Storage (Efficient) → Display Layer (User-friendly)
active = 1                   → status = 'active' → "Hoạt động"
active = 0                   → status = 'closed' → "Đã đóng"
```

### Future Enhancements

**Potential Status Extensions:**
```sql
-- If more status types needed later:
ALTER TABLE kata_polls 
ADD COLUMN status ENUM('draft', 'active', 'closed', 'archived') DEFAULT 'draft';

-- Migration plan:
UPDATE kata_polls SET status = CASE 
    WHEN active = 1 THEN 'active' 
    ELSE 'closed' 
END;
```

### Data Migration Notes

**For existing installations:**
- ✅ Current fix maintains backward compatibility
- ✅ No data loss - all polls preserved
- ✅ Status mapping works with existing `active` column
- ✅ No manual database changes required

---

## 🔗 Related Issues Fixed

1. **Poll Options Display:** Fixed `poll_options` → `options` column name
2. **Question Field:** Combined separate question into description field
3. **Boolean Fields:** Fixed string values to proper 1/0 integers
4. **Status Display:** Proper active/closed mapping with icons
5. **Form Validation:** Maintained validation with correct field names

---

**Bug Status:** ✅ RESOLVED  
**Fix Applied:** October 6, 2025  
**Tested:** ✅ YES  
**Production Ready:** ✅ YES  

*KATA SEO Manager - Poll Management System*