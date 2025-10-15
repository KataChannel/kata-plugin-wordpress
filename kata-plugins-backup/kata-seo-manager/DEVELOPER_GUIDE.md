# KATA SEO Manager - Developer Guide

## 🎯 Quick Start

```bash
# Navigate to plugin directory
cd wp-content/plugins/kata-seo-manager

# View architecture
cat REFACTORING_COMPLETE.md

# Test the plugin
# Open: http://localhost/timona/test-refactored-plugin.php
```

## 📁 Project Structure

```
kata-seo-manager/
├── kata-seo-manager.php          # Main bootstrap file (137 lines)
├── includes/
│   ├── class-core.php            # Main controller (Singleton)
│   ├── class-activator.php       # Plugin lifecycle
│   ├── class-enqueue-handler.php # Asset management
│   ├── class-schema-renderer.php # Schema output
│   ├── ajax/                     # AJAX handlers
│   ├── admin/                    # Admin handlers
│   └── shortcodes/               # Shortcode classes
└── admin/views/                  # Admin templates
```

## 🏗️ Architecture Patterns

### 1. Singleton Pattern (Core)
```php
// Get plugin instance
$core = KATA_SEO_Core::get_instance();

// Store schema from shortcode
$core->store_shortcode_schema($schema_markup);

// Get all stored schemas
$schemas = $core->get_shortcode_schemas();
```

### 2. Template Method (Shortcodes)
```php
// All shortcodes extend base class
class KATA_SEO_Shortcode_Article extends KATA_SEO_Shortcode_Base {
    protected $tag = 'kata_article';
    protected $schema_type = 'article';
    
    public function render($atts, $content = null) {
        // Implementation
    }
}
```

### 3. Strategy Pattern (AJAX)
```php
// Each AJAX handler extends base
class KATA_SEO_Schema_AJAX extends KATA_SEO_AJAX_Handler {
    public function init() {
        add_action('wp_ajax_kata_save_schema', [$this, 'save_schema']);
    }
    
    public function save_schema() {
        $verified = $this->verify_request('kata_seo_nonce');
        // Implementation
    }
}
```

## 🔧 Adding New Features

### Add a New Shortcode

1. Create file: `includes/shortcodes/class-shortcode-mynew.php`

```php
<?php
class KATA_SEO_Shortcode_MyNew extends KATA_SEO_Shortcode_Base {
    
    protected $tag = 'kata_mynew';
    protected $schema_type = 'mynew';
    
    public function render($atts, $content = null) {
        $atts = $this->sanitize_atts($atts, [
            'name' => '',
            'description' => ''
        ]);
        
        $data = [
            'name' => $atts['name'],
            'description' => $atts['description']
        ];
        
        $schema = $this->generate_schema($data);
        
        if (is_wp_error($schema)) {
            $this->log_error($schema->get_error_message());
            return '';
        }
        
        return '<div>' . esc_html($atts['name']) . '</div>';
    }
}
```

2. Auto-loaded by `class-core.php` - No registration needed!

### Add a New AJAX Handler

1. Create file: `includes/ajax/class-mynew-ajax.php`

```php
<?php
class KATA_SEO_MyNew_AJAX extends KATA_SEO_AJAX_Handler {
    
    public function init() {
        add_action('wp_ajax_kata_mynew_action', [$this, 'handle']);
    }
    
    public function handle() {
        $verified = $this->verify_request('kata_seo_nonce');
        
        if (is_wp_error($verified)) {
            $this->error($verified->get_error_message());
        }
        
        // Your logic here
        $data = $this->get_post('data');
        
        $this->success(['result' => $data]);
    }
}
```

2. Register in `class-core.php`:

```php
// In init_components() method
$this->components['mynew_ajax'] = new KATA_SEO_MyNew_AJAX();
$this->components['mynew_ajax']->init();
```

### Add a New Admin Page

1. Create file: `includes/admin/class-mynew-manager.php`

```php
<?php
class KATA_SEO_MyNew_Manager {
    
    public function __construct() {
        add_action('admin_init', [$this, 'handle_actions']);
    }
    
    public function handle_actions() {
        // Handle form submissions
    }
}
```

2. Register in `class-admin-menu.php`:

```php
add_submenu_page(
    'kata-seo-manager',
    __('My New Feature', 'kata-seo-manager'),
    __('My Feature', 'kata-seo-manager'),
    'manage_options',
    'kata-seo-mynew',
    [$this, 'render_mynew']
);
```

## 🧪 Testing

### Run PHP Syntax Check
```bash
php -l includes/class-core.php
```

### Check All Files
```bash
find includes -name "*.php" -type f | xargs -I {} php -l {}
```

### Test Plugin in Browser
```
http://localhost/timona/test-refactored-plugin.php
```

## 📊 Code Metrics

### Before Refactoring
- Main file: 11,276 lines
- Structure: Monolithic
- Maintainability: Low

### After Refactoring
- Main file: 137 lines (98.8% reduction)
- Structure: Modular (42 files)
- Maintainability: High
- Design Patterns: 5
- Average Class Size: 155 lines

## 🔍 Common Tasks

### Get Plugin Instance
```php
// New way (recommended)
$core = kata_seo_init();

// Legacy way (still works)
$manager = kata_seo_manager();
```

### Register Custom Schema Type
```php
// In your schema handler class
$generator = new KATA_SEO_Schema_Generator();
$schema = $generator->generate('custom_type', $data);
```

### Store Schema from Code
```php
$core = KATA_SEO_Core::get_instance();
$core->store_shortcode_schema([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => 'My Article'
]);
```

### Get All Schemas
```php
$core = KATA_SEO_Core::get_instance();
$schemas = $core->get_shortcode_schemas();
```

## 🐛 Debugging

### Enable WordPress Debug Mode
```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Check Debug Log
```bash
tail -f wp-content/debug.log
```

### Test AJAX Endpoint
```javascript
jQuery.ajax({
    url: ajaxurl,
    type: 'POST',
    data: {
        action: 'kata_save_schema',
        nonce: kata_seo.nonce,
        schema_name: 'Test Schema'
    },
    success: function(response) {
        console.log(response);
    }
});
```

## 📝 Code Style

### Naming Conventions
- Classes: `KATA_SEO_ClassName`
- Files: `class-classname.php`
- Functions: `kata_seo_function_name()`
- Constants: `KATA_SEO_CONSTANT_NAME`

### Documentation
```php
/**
 * Short description
 * 
 * Long description
 * 
 * @param string $param Description
 * @return mixed Description
 */
public function method_name($param) {
    // Implementation
}
```

## 🚀 Performance

### Opcode Caching
- Smaller files = better opcode caching
- Each class cached independently
- Faster subsequent loads

### Lazy Loading
```php
// Load only when needed
if (is_admin()) {
    $this->components['admin_menu'] = new KATA_SEO_Admin_Menu();
}
```

## 📚 Resources

### Documentation Files
- `REFACTORING_PLAN.md` - Original plan
- `REFACTORING_PROGRESS.md` - Progress tracking
- `REFACTORING_COMPLETE.md` - Final report
- `DEVELOPER_GUIDE.md` - This file

### Test Files
- `test-refactored-plugin.php` - Comprehensive test suite

### Backup Files
- `kata-seo-manager.php.backup-refactor` - Original code
- `kata-seo-manager.php.old` - Pre-replacement backup

## 🤝 Contributing

### Before Making Changes
1. Create a backup
2. Test in local environment
3. Check for syntax errors
4. Verify backward compatibility

### After Making Changes
1. Run syntax checker
2. Test all affected features
3. Update documentation
4. Create pull request

## 📞 Support

### Common Issues

**Issue: Class not found**
```
Solution: Check file exists and is loaded in class-core.php
```

**Issue: AJAX not working**
```
Solution: Verify nonce and action name match
```

**Issue: Schema not appearing**
```
Solution: Check schema is stored via store_shortcode_schema()
```

## 🎓 Learn More

### WordPress Coding Standards
- https://developer.wordpress.org/coding-standards/

### Schema.org Documentation
- https://schema.org/

### Design Patterns in PHP
- https://refactoring.guru/design-patterns/php

---

**Version:** 2.1.3  
**Last Updated:** October 15, 2025  
**Status:** Production Ready (pending testing)
