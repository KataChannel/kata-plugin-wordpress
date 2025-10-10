# ⚡ KATA Dynamic Schema - Complete Guide

## 📝 Tổng Quan

**KATA Dynamic Schema** là tính năng đặc biệt cho phép bạn **tự thêm bất kỳ code JSON-LD nào** vào WordPress mà không cần viết custom code. Đây là công cụ mạnh mẽ nhất trong KATA SEO Manager.

---

## ✨ Tại Sao Cần Dynamic Schema?

### **Vấn Đề:**
- Plugin có sẵn 21+ schema types, nhưng Schema.org có **800+ types**
- Bạn muốn test schema mới mà chưa có trong plugin
- Cần import schema từ Google Structured Data Tool
- Muốn customize schema theo cách riêng của mình

### **Giải Pháp:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"example.com"}']
```

✅ **Đơn giản, linh hoạt, không giới hạn!**

---

## 🎯 Tính Năng Chính

### **1. Universal Schema Support**
```php
// BẤT KỲ @type nào từ Schema.org
"@type": "WebSite"
"@type": "Person"
"@type": "Organization"
"@type": "SoftwareApplication"
"@type": "MusicEvent"
"@type": "Dataset"
// ... 800+ types khác
```

### **2. JSON Validation**
```php
validate="true"  // Auto-check JSON syntax
                 // Helpful error messages
                 // Prevent broken schemas
```

### **3. Multiple Display Modes**
```php
show_schema="true"         // JSON-LD script tag
show_frontend="true"       // Visual display
show_info="true"           // Schema info box
show_raw="true"            // Raw JSON code
show_content_formatted="true"  // Property table
```

### **4. JSON Formatting**
```php
pretty_print="true"   // Beautify JSON với indentation
minify="true"         // Compress JSON (remove whitespace)
auto_context="true"   // Auto-add @context nếu thiếu
```

### **5. Error Handling**
```php
error_display="true"   // Show validation errors
error_display="false"  // Silent mode (production)
```

---

## 📚 Cú Pháp & Attributes

### **Minimal Shortcode:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"Example"}']
```

### **Full Shortcode:**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"Organization","name":"KATA"}' 
    schema_type="Organization" 
    schema_name="KATA Digital Agency" 
    description="Leading digital marketing agency" 
    validate="true" 
    pretty_print="true" 
    minify="false" 
    auto_context="true" 
    escape_quotes="false" 
    show_schema="true" 
    show_frontend="true" 
    show_raw="true" 
    show_info="true" 
    show_content_formatted="true" 
    wrapper_class="my-custom-class" 
    error_display="true"]
```

---

## 📊 Attributes Reference

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| **json_code** | string | *required* | JSON-LD code (bắt buộc) |
| **schema_type** | string | auto-detect | Schema @type (tự động từ JSON) |
| **schema_name** | string | auto | Tên hiển thị |
| **description** | string | '' | Mô tả schema |
| **validate** | boolean | true | Validate JSON syntax |
| **pretty_print** | boolean | false | Format JSON đẹp |
| **minify** | boolean | false | Nén JSON |
| **auto_context** | boolean | true | Tự thêm @context |
| **escape_quotes** | boolean | false | Escape double quotes |
| **show_schema** | boolean | true | Output JSON-LD script |
| **show_frontend** | boolean | false | Hiển thị frontend UI |
| **show_raw** | boolean | false | Hiển thị raw JSON |
| **show_info** | boolean | false | Hiển thị info box |
| **show_content_formatted** | boolean | false | Hiển thị property table |
| **hide_content_info** | boolean | false | Ẩn info box |
| **hide_content_raw** | boolean | false | Ẩn raw JSON |
| **hide_content_formatted** | boolean | false | Ẩn property table |
| **wrapper_class** | string | '' | Custom CSS class |
| **error_display** | boolean | true | Hiển thị errors |

---

## 🚀 Use Cases & Examples

### **Use Case 1: WebSite Schema**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"WebSite","name":"example.com","url":"https://www.example.com/","potentialAction":{"@type":"SearchAction","target":"https://www.example.com/search?q={search_term_string}","query-input":"required name=search_term_string"}}' 
    show_schema="true" 
    show_frontend="false"]
```

**Kết quả:**
```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "example.com",
  "url": "https://www.example.com/",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://www.example.com/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
```

---

### **Use Case 2: Person Schema (Author Profile)**
```php
[kata_dynamic 
    json_code='{"@type":"Person","name":"Nguyễn Văn A","jobTitle":"SEO Specialist","email":"contact@example.com","url":"https://example.com/author/nguyen-van-a","image":"https://example.com/avatar.jpg","sameAs":["https://facebook.com/nguyenvana","https://linkedin.com/in/nguyenvana","https://twitter.com/nguyenvana"]}' 
    schema_name="Nguyễn Văn A" 
    description="SEO Specialist với 10+ năm kinh nghiệm" 
    auto_context="true" 
    show_schema="true" 
    show_frontend="true" 
    show_info="true"]
```

**Features:**
- `auto_context="true"` → Tự thêm `"@context": "https://schema.org"`
- `show_frontend="true"` → Hiển thị visual info box
- `show_info="true"` → Schema details

---

### **Use Case 3: SoftwareApplication Schema**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"SoftwareApplication","name":"KATA SEO Manager","applicationCategory":"WebApplication","operatingSystem":"WordPress","offers":{"@type":"Offer","price":"0","priceCurrency":"USD"},"aggregateRating":{"@type":"AggregateRating","ratingValue":"4.9","ratingCount":"500"},"author":{"@type":"Organization","name":"KATA Digital"}}' 
    schema_name="KATA SEO Manager Plugin" 
    show_schema="true" 
    pretty_print="true"]
```

**Output (Pretty Print):**
```json
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "KATA SEO Manager",
  "applicationCategory": "WebApplication",
  "operatingSystem": "WordPress",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.9",
    "ratingCount": "500"
  },
  "author": {
    "@type": "Organization",
    "name": "KATA Digital"
  }
}
```

---

### **Use Case 4: Multiple @type (Union Types)**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":["BlogPosting","NewsArticle"],"headline":"WordPress 6.5 Released","author":{"@type":"Person","name":"John Doe"},"datePublished":"2025-10-09","articleBody":"Major improvements in WordPress 6.5..."}' 
    show_schema="true"]
```

**Schema.org Multiple Types:**
- `["BlogPosting", "NewsArticle"]` → Post vừa là blog vừa là news
- `["LocalBusiness", "Restaurant"]` → Business + specific type
- `["Product", "SoftwareApplication"]` → Software product

---

### **Use Case 5: Import from Google Tool**

**Workflow:**
1. Vào https://search.google.com/test/rich-results
2. Paste URL hoặc code HTML
3. Copy JSON-LD output từ tool
4. Paste vào `json_code` attribute:

```php
[kata_dynamic 
    json_code='... PASTE JSON TỪ GOOGLE TOOL ...' 
    auto_context="true" 
    validate="true" 
    show_schema="true"]
```

✅ **Lợi ích:**
- Không cần viết code
- Google đã validate sẵn
- Import nhanh chóng

---

## 🎨 Frontend Display Modes

### **Mode 1: Schema Only (Production)**
```php
[kata_dynamic 
    json_code='...' 
    show_schema="true" 
    show_frontend="false"]
```
**Output:** Chỉ có `<script type="application/ld+json">` tag

---

### **Mode 2: Frontend Only (Testing UI)**
```php
[kata_dynamic 
    json_code='...' 
    show_schema="false" 
    show_frontend="true" 
    show_info="true" 
    show_raw="true"]
```
**Output:** Visual display (no JSON-LD script) - để test UI

---

### **Mode 3: Both (Development)**
```php
[kata_dynamic 
    json_code='...' 
    show_schema="true" 
    show_frontend="true" 
    show_info="true" 
    show_raw="true" 
    show_content_formatted="true"]
```
**Output:** JSON-LD + Complete visual display

---

## 🔧 Advanced Features

### **1. Pretty Print vs Minify**

**Pretty Print (Development):**
```php
[kata_dynamic json_code='...' pretty_print="true"]
```
```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Example"
}
```

**Minified (Production):**
```php
[kata_dynamic json_code='...' minify="true"]
```
```json
{"@context":"https://schema.org","@type":"WebSite","name":"Example"}
```

✅ **Minify giảm ~30-40% file size**

---

### **2. Auto Context**

**Without auto_context:**
```php
json_code='{"@type":"Person","name":"John"}'  ❌ Missing @context
```

**With auto_context:**
```php
json_code='{"@type":"Person","name":"John"}' auto_context="true"
```
```json
{
  "@context": "https://schema.org",  ← Auto-added
  "@type": "Person",
  "name": "John"
}
```

---

### **3. Custom Wrapper Class**

```php
[kata_dynamic 
    json_code='...' 
    show_frontend="true" 
    wrapper_class="dark-theme compact-view"]
```

**Output:**
```html
<div class="kata-dynamic-container dark-theme compact-view">
    <!-- Content -->
</div>
```

**Custom CSS:**
```css
.dark-theme {
    background: #1a1a1a;
    color: #fff;
}
.compact-view .kata-dynamic-info {
    padding: 10px;
}
```

---

## ⚠️ Error Handling

### **Error 1: Missing json_code**
```php
[kata_dynamic show_schema="true"]  ❌ No json_code
```

**Output:**
```
⚠️ Dynamic Schema Error: Attribute json_code is required.
Example: [kata_dynamic json_code='{"@context":"https://schema.org",...}']
```

---

### **Error 2: Invalid JSON Syntax**
```php
[kata_dynamic 
    json_code='{"@type":"WebSite","name":"Example"'  ❌ Missing closing }
    validate="true" 
    error_display="true"]
```

**Output:**
```
⚠️ JSON Validation Error: Syntax error, malformed JSON
Please check your JSON syntax. Use a JSON validator tool if needed.

{"@type":"WebSite","name":"Example"  ← Missing }
```

---

### **Error 3: Silent Mode (Production)**
```php
[kata_dynamic 
    json_code='... INVALID JSON ...' 
    validate="true" 
    error_display="false"]
```

**Output:** Empty string (no error display)

✅ **Use in production để tránh expose errors**

---

## 💡 Best Practices

### ✅ **DO's:**

1. **Always Validate JSON**
   ```php
   validate="true"  // Catch errors early
   ```

2. **Use Pretty Print in Development**
   ```php
   pretty_print="true"  // Easy debugging
   ```

3. **Use Minify in Production**
   ```php
   minify="true"  // Reduce file size
   ```

4. **Enable Auto Context**
   ```php
   auto_context="true"  // Safe when copying from external sources
   ```

5. **Test with Google Tool**
   ```
   https://search.google.com/test/rich-results
   ```

6. **Use Error Display in Development**
   ```php
   error_display="true"  // Development
   error_display="false"  // Production
   ```

---

### ❌ **DON'Ts:**

1. **Don't Use Single Quotes in JSON**
   ```json
   ❌ {'name': 'John'}  // Wrong
   ✅ {"name": "John"}  // Correct
   ```

2. **Don't Forget Escape Special Characters**
   ```json
   ❌ "description": "It's great"  // Wrong
   ✅ "description": "It's great"  // Correct (curly apostrophe)
   ✅ "description": "It\\'s great"  // Or escape
   ```

3. **Don't Output Duplicate Schemas**
   ```php
   // Check if schema already exists before adding
   // Use show_frontend="false" nếu chỉ cần JSON-LD
   ```

4. **Don't Enable Frontend in Production (Unless Needed)**
   ```php
   show_frontend="false"  // Save resources
   ```

5. **Don't Hardcode Dates**
   ```php
   ❌ "datePublished": "2025-10-09"  // Static
   ✅ Use dynamic values (PHP date())
   ```

---

## 🎯 Common Scenarios

### **Scenario 1: Test New Schema Type**
```php
// Schema.org vừa release new type: "HealthInsurancePlan"
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"HealthInsurancePlan","name":"Premium Health Plan","benefitsSummaryUrl":"https://example.com/benefits"}' 
    schema_name="Health Insurance Plan" 
    show_schema="true" 
    show_frontend="true" 
    show_info="true"]
```

---

### **Scenario 2: Override Plugin Schema**
```php
// Plugin có Article schema, nhưng bạn muốn customize
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"Article","headline":"My Custom Article","custom_field":"Custom Value"}' 
    show_schema="true"]
```

---

### **Scenario 3: Combine Multiple Types**
```php
// Restaurant + LocalBusiness
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":["Restaurant","LocalBusiness"],"name":"Phở Hà Nội","servesCuisine":"Vietnamese","priceRange":"$$"}' 
    show_schema="true"]
```

---

### **Scenario 4: Import from Competitor**
```php
// View page source của competitor → copy JSON-LD
[kata_dynamic 
    json_code='... PASTE FROM COMPETITOR ...' 
    auto_context="true" 
    validate="true" 
    show_schema="true"]
```

---

### **Scenario 5: A/B Testing Schemas**
```php
// Version A
[kata_dynamic json_code='{"@type":"Article",...}' show_schema="true"]

// Version B (test on different page)
[kata_dynamic json_code='{"@type":"NewsArticle",...}' show_schema="true"]

// Track performance in Google Search Console
```

---

## 📊 Performance Considerations

### **File Size:**
- **Pretty Print:** ~40% larger (development only)
- **Minify:** ~30-40% smaller (production recommended)
- **No validation:** Slightly faster (skip JSON parsing)

### **Recommendations:**

**Development:**
```php
pretty_print="true" 
validate="true" 
error_display="true" 
show_frontend="true"
```

**Production:**
```php
minify="true" 
validate="true" 
error_display="false" 
show_frontend="false"
```

---

## 🧪 Testing Workflow

### **Step 1: Create Schema**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"WebSite","name":"Test"}' 
    show_frontend="true" 
    show_raw="true" 
    validate="true"]
```

### **Step 2: Visual Inspection**
- Check frontend display
- Verify JSON syntax
- Review property table

### **Step 3: Google Validation**
1. Publish page
2. Go to https://search.google.com/test/rich-results
3. Enter page URL
4. Check for errors/warnings

### **Step 4: Production**
```php
[kata_dynamic 
    json_code='...' 
    show_schema="true" 
    show_frontend="false" 
    minify="true" 
    error_display="false"]
```

---

## 🎓 Learning Resources

### **Schema.org Documentation:**
- https://schema.org/docs/schemas.html
- https://schema.org/docs/full.html (all types)

### **Google Rich Results:**
- https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data
- https://search.google.com/test/rich-results

### **JSON-LD Playground:**
- https://json-ld.org/playground/
- Validate and visualize JSON-LD

### **KATA Plugin Docs:**
- See `ORGANIZATION_SCHEMA_ENHANCEMENT.md`
- See `test_dynamic_schema.html` (12 test cases)

---

## 📝 Examples Collection

### **1. Simple WebSite:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"example.com","url":"https://example.com"}' show_schema="true"]
```

### **2. Organization with Logo:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Organization","name":"KATA","url":"https://katadigital.com","logo":"https://katadigital.com/logo.png"}' show_schema="true"]
```

### **3. Person (Author):**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Person","name":"John Doe","jobTitle":"Developer"}' show_schema="true"]
```

### **4. Product:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Product","name":"iPhone 15","brand":"Apple","offers":{"@type":"Offer","price":"999","priceCurrency":"USD"}}' show_schema="true"]
```

### **5. Event:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"Event","name":"WordPress Meetup","startDate":"2025-10-15T18:00","location":{"@type":"Place","name":"KATA Office"}}' show_schema="true"]
```

### **6. BreadcrumbList:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[{"@type":"ListItem","position":1,"name":"Home","item":"https://example.com"},{"@type":"ListItem","position":2,"name":"Blog"}]}' show_schema="true"]
```

---

## 🚀 Quick Start

### **Minimal Example:**
```php
[kata_dynamic json_code='{"@context":"https://schema.org","@type":"WebSite","name":"example.com","url":"https://www.example.com/"}']
```

### **Recommended Example:**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"Organization","name":"KATA Digital","url":"https://katadigital.com"}' 
    schema_name="KATA Digital Agency" 
    validate="true" 
    show_schema="true"]
```

### **Full-Featured Example:**
```php
[kata_dynamic 
    json_code='{"@context":"https://schema.org","@type":"Organization","name":"KATA Digital","url":"https://katadigital.com","logo":"https://katadigital.com/logo.png","description":"Digital Marketing Agency"}' 
    schema_name="KATA Digital Agency" 
    description="Leading digital marketing agency in Vietnam" 
    validate="true" 
    pretty_print="true" 
    show_schema="true" 
    show_frontend="true" 
    show_info="true" 
    show_raw="true"]
```

---

## ✅ Summary

**KATA Dynamic Schema** = Universal Schema Tool

✅ Support **bất kỳ Schema.org type nào** (800+ types)  
✅ **JSON validation** với helpful error messages  
✅ **Multiple display modes** (schema only, frontend, both)  
✅ **Formatting options** (pretty print, minify)  
✅ **Auto features** (auto @context, auto type detection)  
✅ **Error handling** (development vs production modes)  
✅ **Custom styling** (wrapper_class attribute)  
✅ **Testing tools** (show_raw, show_info, property table)  

**Perfect for:**
- Testing new schema types
- Importing from external sources
- Prototyping before coding
- Overriding default schemas
- Learning Schema.org

---

*Tạo ngày: 9 tháng 10, 2025*  
*Plugin: KATA SEO Manager v1.0.0*  
*Feature: Dynamic Schema (Custom JSON-LD)*  
*Test File: test_dynamic_schema.html (12 scenarios)*
