# 🏗️ KATA SEO Manager - Plugin Refactoring Plan

## 📊 Current State Analysis
- **Main File:** `kata-seo-manager.php` (11,274 lines)
- **Problem:** Monolithic structure, hard to maintain
- **Goal:** Modular architecture following WordPress best practices

## 🎯 New Architecture

```
kata-seo-manager/
├── kata-seo-manager.php (Bootstrap ~150 lines)
├── includes/
│   ├── class-core.php (Core singleton & initialization)
│   ├── class-activator.php (Activation/Deactivation logic)
│   ├── class-schema-renderer.php (Schema output logic)
│   ├── class-enqueue-handler.php (Scripts & styles)
│   │
│   ├── ajax/
│   │   ├── class-ajax-handler.php (Base AJAX handler)
│   │   ├── class-schema-ajax.php (Schema AJAX endpoints)
│   │   ├── class-poll-ajax.php (Poll AJAX endpoints)
│   │   ├── class-wheel-ajax.php (Wheel AJAX endpoints)
│   │   └── class-interaction-ajax.php (User interaction AJAX)
│   │
│   ├── admin/
│   │   ├── class-admin-menu.php (Admin menu registration)
│   │   ├── class-poll-manager.php (Poll management)
│   │   ├── class-wheel-manager.php (Wheel management)
│   │   └── class-admin-notices.php (Admin notifications)
│   │
│   └── shortcodes/
│       ├── class-shortcode-base.php (Abstract base class)
│       ├── class-article-shortcode.php
│       ├── class-faq-shortcode.php
│       ├── class-dynamic-shortcode.php
│       ├── class-organization-shortcode.php
│       ├── class-localbusiness-shortcode.php
│       ├── class-poll-shortcode.php
│       ├── class-wheel-shortcode.php
│       └── ... (26 total shortcode classes)
```

## 📝 Refactoring Steps

### Step 1: Core Infrastructure ✅
- [x] Create REFACTORING_PLAN.md
- [ ] Create class-core.php (singleton pattern)
- [ ] Create class-activator.php
- [ ] Create class-enqueue-handler.php
- [ ] Create class-schema-renderer.php

### Step 2: AJAX Handlers
- [ ] Create ajax/class-ajax-handler.php (base)
- [ ] Create ajax/class-schema-ajax.php
- [ ] Create ajax/class-poll-ajax.php
- [ ] Create ajax/class-wheel-ajax.php
- [ ] Create ajax/class-interaction-ajax.php

### Step 3: Admin Handlers
- [ ] Create admin/class-admin-menu.php
- [ ] Create admin/class-poll-manager.php
- [ ] Create admin/class-wheel-manager.php
- [ ] Create admin/class-admin-notices.php

### Step 4: Shortcode Refactoring
- [ ] Create shortcodes/class-shortcode-base.php
- [ ] Create shortcodes/class-article-shortcode.php
- [ ] Create shortcodes/class-faq-shortcode.php
- [ ] Create shortcodes/class-dynamic-shortcode.php
- [ ] ... (Extract all 26+ shortcodes)

### Step 5: Main File Refactoring
- [ ] Refactor kata-seo-manager.php (keep only bootstrap)
- [ ] Update includes() to load new structure
- [ ] Remove old monolithic code
- [ ] Test all functionality

## 🎨 Design Patterns Used

1. **Singleton Pattern** - Core class instance management
2. **Factory Pattern** - Shortcode registration
3. **Strategy Pattern** - Different AJAX handlers
4. **Template Method Pattern** - Base shortcode class
5. **Dependency Injection** - Pass dependencies via constructor

## ✅ Benefits

- **Maintainability:** Each file has single responsibility
- **Testability:** Easy to unit test individual classes
- **Scalability:** Easy to add new features
- **Readability:** Clear code organization
- **Performance:** Lazy loading where possible

## 📅 Timeline

- Phase 1: Core Infrastructure (30 mins)
- Phase 2: AJAX Handlers (20 mins)
- Phase 3: Admin Handlers (20 mins)
- Phase 4: Shortcode Refactoring (60 mins)
- Phase 5: Main File & Testing (30 mins)

**Total Estimate:** 2.5 hours

## 🔍 Testing Checklist

- [ ] Plugin activation/deactivation works
- [ ] All shortcodes render correctly
- [ ] AJAX endpoints respond properly
- [ ] Admin pages load without errors
- [ ] Schema output in wp_head
- [ ] No PHP errors in debug.log
- [ ] Frontend features work (polls, wheels, etc.)

---

**Status:** 🚧 IN PROGRESS
**Started:** 15/10/2025
**Last Updated:** 15/10/2025
