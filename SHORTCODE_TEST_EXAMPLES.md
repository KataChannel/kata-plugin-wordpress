# Shortcode Test Examples for WordPress Pages

## Quick Copy-Paste Tests

### Test 1: Article - MODE 1 (Hide Author from Schema)
```
[kata_article 
    title="Amazing WordPress Tutorial"
    description="Learn WordPress development from scratch"
    author="John Doe"
    date_published="2025-01-10"
    hide_author="true"]
```
**Expected Result:**
- ✅ JSON-LD does NOT have "author" field
- ✅ HTML shows author "John Doe"

---

### Test 2: Article - MODE 2 (Hide Author from HTML)
```
[kata_article 
    title="Advanced PHP Techniques"
    description="Master PHP programming"
    author="Jane Smith"
    date_published="2025-01-15"
    hide_content_author="true"]
```
**Expected Result:**
- ✅ JSON-LD includes "author" field
- ✅ HTML does NOT show author

---

### Test 3: Article - BOTH MODES (Hide Author Everywhere)
```
[kata_article 
    title="Secret Article"
    description="This article has no visible author"
    author="Anonymous"
    date_published="2025-01-20"
    hide_author="true"
    hide_content_author="true"]
```
**Expected Result:**
- ✅ JSON-LD does NOT have "author"
- ✅ HTML does NOT show author

---

### Test 4: FAQ - Schema Fields Only
```
[kata_faq 
    title="Common Questions"
    schema_fields="mainEntity"
    questions="Q1:What is WordPress?|A1:A content management system,Q2:Is it free?|A2:Yes it is open source"]
```
**Expected Result:**
- ✅ JSON-LD only has @context, @type, and mainEntity
- ✅ HTML shows full FAQ with title and all questions

---

### Test 5: Product - Hide Price from Display
```
[kata_product 
    name="Premium WordPress Theme"
    description="Professional business theme"
    price="1500000"
    currency="VND"
    brand="ThemeCo"
    availability="InStock"
    hide_content_price="true"]
```
**Expected Result:**
- ✅ JSON-LD includes price in "offers"
- ✅ HTML does NOT show price

---

### Test 6: Recipe - Show Only Instructions
```
[kata_recipe 
    name="Chocolate Cake"
    description="Delicious homemade cake"
    prep_time="PT30M"
    cook_time="PT45M"
    ingredients="2 cups flour,1 cup sugar,3 eggs,1 cup milk,100g chocolate"
    instructions="Preheat oven to 180C,Mix dry ingredients,Add wet ingredients,Pour into pan,Bake for 45 minutes"
    show_content_instructions="true"
    hide_content_ingredients="true"
    hide_content_title="true"]
```
**Expected Result:**
- ✅ JSON-LD has complete recipe
- ✅ HTML shows ONLY instructions (no title, no ingredients)

---

### Test 7: Event - Hide Organizer from Schema
```
[kata_event 
    name="WordPress Meetup Hanoi"
    description="Monthly WordPress community meetup"
    start_date="2025-02-20T18:00"
    end_date="2025-02-20T21:00"
    location_name="Innovation Hub"
    location_address="123 Nguyễn Trãi, Hà Nội"
    organizer_name="WP Hanoi Community"
    organizer_url="https://wphanoi.com"
    hide_organizer="true"]
```
**Expected Result:**
- ✅ JSON-LD missing "organizer" property
- ✅ HTML shows organizer information

---

### Test 8: Course - Hide Meta Information
```
[kata_course 
    name="Complete WordPress Development"
    description="From beginner to advanced WordPress developer"
    provider="Code Academy Vietnam"
    instructor="Nguyễn Văn A"
    price="3000000"
    currency="VND"
    duration="P12W"
    level="Intermediate"
    mode="Online"
    skills="WordPress,PHP,JavaScript,MySQL"
    hide_content_meta="true"]
```
**Expected Result:**
- ✅ JSON-LD has all course data
- ✅ HTML hides meta info (duration, level, mode, enrollment)

---

### Test 9: Video - Schema Only Mode
```
[kata_video 
    name="WordPress Tutorial - Part 1"
    description="Introduction to WordPress"
    url="https://example.com/videos/wp-tutorial.mp4"
    thumbnail_url="https://example.com/images/wp-thumb.jpg"
    upload_date="2025-01-01"
    duration="PT15M30S"
    hide_content_title="true"
    hide_content_description="true"
    hide_content_video="true"]
```
**Expected Result:**
- ✅ JSON-LD has VideoObject schema
- ✅ HTML shows nothing (pure schema mode)

---

### Test 10: Organization - Enable Content Display
```
[kata_organization 
    name="Kata Digital Solutions"
    url="https://katadigital.com"
    logo="https://katadigital.com/logo.png"
    description="Leading digital marketing agency"
    telephone="+84-24-1234-5678"
    email="info@katadigital.com"
    address="456 Láng Hạ, Đống Đa, Hà Nội"
    show_content="true"]
```
**Expected Result:**
- ✅ JSON-LD has Organization schema
- ✅ HTML shows company card (this was schema-only before)

---

### Test 11: Local Business - Full Display
```
[kata_localbusiness 
    name="Kata Coffee Shop"
    type="CoffeeShop"
    description="Artisan coffee in the heart of Hanoi"
    image="https://example.com/coffee-shop.jpg"
    address="789 Tràng Tiền, Hoàn Kiếm, Hà Nội"
    telephone="+84-98-765-4321"
    url="https://katacoffee.com"
    price_range="$$"
    opening_hours="Mo-Su 07:00-22:00"]
```
**Expected Result:**
- ✅ JSON-LD has LocalBusiness schema
- ✅ HTML shows business card with all details

---

### Test 12: Job Posting - Hide Requirements
```
[kata_jobposting 
    title="Senior WordPress Developer"
    description="Join our team as a WordPress expert"
    company="Tech Startup VN"
    location="Hà Nội, Vietnam"
    employment_type="FULL_TIME"
    salary_value="2000"
    salary_currency="USD"
    date_posted="2025-01-10"
    requirements="5+ years WordPress,PHP expert,Team leader experience"
    benefits="Remote work,Health insurance,Learning budget"
    hide_content_requirements="true"]
```
**Expected Result:**
- ✅ JSON-LD has complete job posting
- ✅ HTML shows job but hides requirements section

---

### Test 13: HowTo - Step-by-Step Guide
```
[kata_howto 
    name="How to Install WordPress"
    description="Complete guide to WordPress installation"
    total_time="PT30M"
    tools="FTP Client,Text Editor,Web Browser"
    materials="Domain name,Web hosting"
    steps="Download WordPress,Upload files via FTP,Create database,Run installation,Configure settings"
    hide_content_tools="true"]
```
**Expected Result:**
- ✅ JSON-LD has HowTo schema with all steps
- ✅ HTML shows guide but hides tools section

---

### Test 14: Quiz - Hide Title
```
[kata_quiz 
    title="WordPress Knowledge Test"
    description="Test your WordPress knowledge"
    questions="Q1:What does WP stand for?|A:Web Page,B:WordPress,C:Web Portal|B,Q2:What language is WordPress written in?|A:Python,B:Java,C:PHP|C"]
```
**Expected Result:**
- ✅ Quiz is interactive and functional
- ✅ All content visible by default

Now test with hidden title:
```
[kata_quiz 
    title="WordPress Knowledge Test"
    description="Test your WordPress knowledge"
    questions="Q1:What does WP stand for?|A:Web Page,B:WordPress,C:Web Portal|B"
    hide_content_title="true"]
```
**Expected Result:**
- ✅ Quiz works
- ✅ Title is hidden

---

### Test 15: Poll - Interactive Voting
```
[kata_poll 
    title="Favorite CMS"
    description="Vote for your favorite content management system"
    options="WordPress,Drupal,Joomla,Other"]
```
**Expected Result:**
- ✅ Poll displays and accepts votes
- ✅ Results show after voting

Now test with hidden description:
```
[kata_poll 
    title="Favorite CMS"
    description="Vote for your favorite content management system"
    options="WordPress,Drupal,Joomla,Other"
    hide_content_description="true"]
```
**Expected Result:**
- ✅ Poll works
- ✅ Description is hidden

---

### Test 16: Rating Widget
```
[kata_rating 
    title="Rate this Article"
    max_rating="5"
    show_average="true"]
```
**Expected Result:**
- ✅ Star rating widget displays
- ✅ Interactive (clickable stars)

---

## How to Test in WordPress

### Method 1: Create Test Page
1. Go to WordPress Admin → Pages → Add New
2. Title: "Dual-Mode Shortcode Tests"
3. Copy ANY test from above
4. Paste into page content (can use Classic Editor or Code view in Gutenberg)
5. Click "Preview" or "Publish"
6. View the page

### Method 2: Check JSON-LD Output
1. View the page in browser
2. Right-click → "View Page Source"
3. Search for `<script type="application/ld+json">`
4. Verify schema has/doesn't have fields based on MODE 1 attributes

### Method 3: Check HTML Output
1. View the page
2. Right-click → "Inspect Element"
3. Find the shortcode output HTML
4. Verify elements are shown/hidden based on MODE 2 attributes

---

## Validation Checklist

Use this checklist for EACH test:

**MODE 1 Tests (Schema Filtering):**
- [ ] `hide_*="true"` removes property from JSON-LD
- [ ] `show_*="true"` includes property in JSON-LD
- [ ] `schema_fields="field1,field2"` includes only those fields
- [ ] @context and @type always present
- [ ] HTML content unaffected by MODE 1

**MODE 2 Tests (Content Display):**
- [ ] `hide_content_*="true"` hides HTML element
- [ ] `show_content_*="true"` shows HTML element
- [ ] JSON-LD unaffected by MODE 2
- [ ] Interactive features still work

**Combined Tests:**
- [ ] Both modes work together
- [ ] No conflicts between modes
- [ ] Priority: show_* > hide_* > default

**Priority Tests:**
- [ ] `show_author="true"` overrides `hide_author="true"`
- [ ] `show_content_title="true"` overrides `hide_content_title="true"`

---

## Expected Schema Structures

### Article Schema
```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "...",
  "description": "...",
  "author": { ... },  // ← Can be filtered with hide_author
  "datePublished": "...",
  "dateModified": "..."
}
```

### Product Schema
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "...",
  "description": "...",
  "offers": {
    "price": "...",  // ← Can be filtered with hide_offers
    "priceCurrency": "..."
  }
}
```

### Event Schema
```json
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "...",
  "startDate": "...",
  "location": { ... },
  "organizer": { ... }  // ← Can be filtered with hide_organizer
}
```

---

## Troubleshooting

**Issue: Shortcode shows as plain text**
- Solution: Plugin not activated. Go to Plugins → Activate kata-seo-manager

**Issue: No JSON-LD in page source**
- Solution: Check wp_head() is in theme header.php

**Issue: Attributes not working**
- Solution: Check attribute names match exactly (case-sensitive)

**Issue: Interactive widgets not working**
- Solution: Check JavaScript console for errors, ensure jQuery loaded

**Issue: Database-dependent features (quiz/poll) error**
- Solution: Check database tables exist, run plugin activation

---

## Next Steps After Testing

1. ✅ Verify all 15 shortcode types work
2. ✅ Test MODE 1, MODE 2, and combined
3. ✅ Validate JSON-LD with Google Rich Results Test
4. ✅ Check WordPress debug.log for errors
5. ✅ Test on different browsers
6. ✅ Document any bugs found
7. ✅ Commit working code to git

---

**Test Suite Created:** <?php echo date('d/m/Y H:i:s'); ?>
