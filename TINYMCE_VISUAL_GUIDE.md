# 🎨 TinyMCE Schema Attributes - Visual Guide

## 📊 Before vs After Comparison

### ❌ BEFORE (11/29 schemas = 37.9%)
```
┌─────────────────────────────────────────────────────┐
│         TinyMCE Modal - Schema Selection            │
├─────────────────────────────────────────────────────┤
│                                                     │
│  FAQ            ✅ HAS customization                │
│  Article        ✅ HAS customization                │
│  Recipe         ✅ HAS customization                │
│  Product        ✅ HAS customization                │
│  Event          ✅ HAS customization                │
│  HowTo          ✅ HAS customization                │
│  Quiz           ❌ NO customization                 │
│  Poll           ❌ NO customization                 │
│  Wheel          ❌ NO customization                 │
│  Form           ❌ NO customization                 │
│  Movie          ❌ NO customization                 │
│  Software       ❌ NO customization                 │
│  LocalBusiness  ✅ HAS customization                │
│  Course         ✅ HAS customization                │
│  JobPosting     ✅ HAS customization                │
│  Book           ✅ HAS customization                │
│  ImageMetadata  ✅ HAS customization                │
│  Carousel       ❌ NO customization                 │
│  Dataset        ❌ NO customization                 │
│  Forum          ❌ NO customization                 │
│  ... (and 9 more without customization)             │
│                                                     │
└─────────────────────────────────────────────────────┘
```

### ✅ AFTER (29/29 schemas = 100%)
```
┌─────────────────────────────────────────────────────┐
│         TinyMCE Modal - Schema Selection            │
├─────────────────────────────────────────────────────┤
│                                                     │
│  FAQ            ✅ FULL customization               │
│  Article        ✅ FULL customization               │
│  Recipe         ✅ FULL customization               │
│  Product        ✅ FULL customization               │
│  Event          ✅ FULL customization               │
│  HowTo          ✅ FULL customization               │
│  Quiz           ✅ FULL customization ⭐            │
│  Poll           ✅ FULL customization ⭐            │
│  Wheel          ✅ FULL customization ⭐            │
│  Form           ✅ FULL customization ⭐            │
│  Movie          ✅ FULL customization ⭐            │
│  Software       ✅ FULL customization ⭐            │
│  LocalBusiness  ✅ FULL customization               │
│  Course         ✅ FULL customization               │
│  JobPosting     ✅ FULL customization               │
│  Book           ✅ FULL customization               │
│  ImageMetadata  ✅ FULL customization               │
│  Carousel       ✅ FULL customization ⭐            │
│  Dataset        ✅ FULL customization ⭐            │
│  Forum          ✅ FULL customization ⭐            │
│  ... (all 29 schemas with full customization)       │
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## 🎯 Customization Panel UI

### Schema Selection → Customization Panel

```
┌─────────────────────────────────────────────────────────────────┐
│  📝 Quiz Schema                                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  [Preview Content Here...]                                      │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │ ⚙️ Tùy chỉnh thuộc tính Schema          [▼ Toggle]        │ │
│  ├───────────────────────────────────────────────────────────┤ │
│  │                                                           │ │
│  │ ┌─────────────────────────────────────────────┐          │ │
│  │ │ 📋 Schema Fields (hide_* / show_*)          │          │ │
│  │ │ Kiểm soát dữ liệu trong JSON-LD schema      │          │ │
│  │ │                           [✓ All] [✗ None]  │          │ │
│  │ ├─────────────────────────────────────────────┤          │ │
│  │ │                                             │          │ │
│  │ │  ☑️ title      ☑️ questions                │          │ │
│  │ │                                             │          │ │
│  │ └─────────────────────────────────────────────┘          │ │
│  │                                                           │ │
│  │ ┌─────────────────────────────────────────────┐          │ │
│  │ │ 🎨 Content Display (hide_content_* / show_*)│          │ │
│  │ │ Kiểm soát hiển thị trên frontend website    │          │ │
│  │ │                           [✓ All] [✗ None]  │          │ │
│  │ ├─────────────────────────────────────────────┤          │ │
│  │ │                                             │          │ │
│  │ │  ☑️ title          ☑️ description           │          │ │
│  │ │  ☑️ questions      ☑️ results               │          │ │
│  │ │  ☑️ statistics                              │          │ │
│  │ │                                             │          │ │
│  │ └─────────────────────────────────────────────┘          │ │
│  │                                                           │ │
│  └───────────────────────────────────────────────────────────┘ │
│                                                                 │
│  ┌───────────────────────────────────────────────────────────┐ │
│  │ 📝 Shortcode:                                             │ │
│  │ ┌─────────────────────────────────────────────────────┐  │ │
│  │ │ [kata_quiz title="Test Quiz"]                       │  │ │
│  │ │ [kata_quiz_question question="..." correct="0"]     │  │ │
│  │ │ ...                                                 │  │ │
│  │ │ [/kata_quiz]                                        │  │ │
│  │ └─────────────────────────────────────────────────────┘  │ │
│  │ 💡 Click vào textarea để select toàn bộ code             │ │
│  └───────────────────────────────────────────────────────────┘ │
│                                                                 │
│                     [📋 Copy]  [✨ Chèn vào Editor]            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔵 Mode 1: Schema Fields (Blue)

### JSON-LD Control Example

**User unchecks "questions":**
```
☑️ title       
☐ questions  ← Unchecked
```

**Generated Shortcode:**
```javascript
[kata_quiz title="Test" hide_questions="true"]
```

**JSON-LD Output:**
```json
{
  "@context": "https://schema.org",
  "@type": "Quiz",
  "name": "Test"
  // ❌ NO "questions" field in JSON-LD
}
```

---

## 🟠 Mode 2: Content Display (Orange)

### Frontend Display Control Example

**User unchecks "results" and "statistics":**
```
☑️ title          
☑️ description    
☑️ questions      
☐ results        ← Unchecked
☐ statistics     ← Unchecked
```

**Generated Shortcode:**
```javascript
[kata_quiz 
    title="Test" 
    hide_content_results="true" 
    hide_content_statistics="true"]
```

**Frontend Display:**
```html
<div class="kata-quiz">
    <h3>Test</h3>                    ✅ Shown
    <p class="description">...</p>   ✅ Shown
    <div class="questions">...</div> ✅ Shown
    <!-- ❌ NO results display -->
    <!-- ❌ NO statistics display -->
</div>
```

---

## ⚡ Real-time Update Flow

```
User Action                JavaScript Function              Result
═══════════════════════════════════════════════════════════════════════

1. Click schema          →  Show preview panel          →  Schema preview
   "Quiz"                                                   + attributes

2. Uncheck               →  window.updateShortcode_     →  Shortcode updates
   "questions"               quiz()                          with hide_*

3. Uncheck               →  window.updateShortcode_     →  Shortcode updates
   "results"                 quiz()                          with hide_content_*

4. Click                 →  Copy to clipboard          →  Success message
   "Copy"

5. Click                 →  Insert to editor           →  Modal closes
   "Insert"                  + close modal                  + shortcode added
```

---

## 📋 All 29 Schemas with Attributes

### Original 11 Schemas
```
1.  FAQ             → 2 schema + 3 content = 5 attributes
2.  Article         → 5 schema + 9 content = 14 attributes
3.  Recipe          → 5 schema + 8 content = 13 attributes
4.  Product         → 4 schema + 9 content = 13 attributes
5.  Event           → 4 schema + 9 content = 13 attributes
6.  HowTo           → 3 schema + 8 content = 11 attributes
7.  LocalBusiness   → 4 schema + 8 content = 12 attributes
8.  Course          → 4 schema + 8 content = 12 attributes
9.  JobPosting      → 4 schema + 7 content = 11 attributes
10. Book            → 3 schema + 8 content = 11 attributes
11. ImageMetadata   → 3 schema + 12 content = 15 attributes
```

### New 18 Schemas ⭐
```
12. Quiz            → 2 schema + 5 content = 7 attributes
13. Poll            → 3 schema + 5 content = 8 attributes
14. Wheel           → 2 schema + 5 content = 7 attributes
15. Form            → 2 schema + 5 content = 7 attributes
16. UserInteraction → 2 schema + 5 content = 7 attributes
17. Movie           → 5 schema + 9 content = 14 attributes
18. Software        → 4 schema + 8 content = 12 attributes
19. WebPage         → 3 schema + 5 content = 8 attributes
20. Carousel        → 2 schema + 5 content = 7 attributes
21. Dataset         → 3 schema + 6 content = 9 attributes
22. Forum           → 2 schema + 6 content = 8 attributes
23. EduQA           → 3 schema + 7 content = 10 attributes
24. EmployerRating  → 3 schema + 6 content = 9 attributes
25. ProfilePage     → 3 schema + 9 content = 12 attributes
26. MathSolver      → 2 schema + 6 content = 8 attributes
27. PracticeProblem → 2 schema + 7 content = 9 attributes
28. SiteLinks       → 2 schema + 5 content = 7 attributes
29. Speakable       → 2 schema + 5 content = 7 attributes
```

**Grand Total: 96 schema fields + 216 content fields = 312 controls!**

---

## 🎨 Color Coding System

```
┌────────────────────────────────────────────────────┐
│ 🔵 Schema Fields (JSON-LD Control)                │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Color: #0073aa (Blue)                              │
│ Border: 2px solid #0073aa                          │
│ Background: #f0f8ff (Light blue)                   │
│ Icon: 📋                                           │
└────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────┐
│ 🟠 Content Display (Frontend Control)             │
│ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ │
│ Color: #f59e0b (Orange)                            │
│ Border: 2px solid #f59e0b                          │
│ Background: #fffbeb (Light orange)                 │
│ Icon: 🎨                                           │
└────────────────────────────────────────────────────┘
```

---

## 🚀 Usage Examples

### Example 1: Minimal Quiz (Only show questions)
```javascript
[kata_quiz 
    title="Quick Test" 
    hide_content_description="true" 
    hide_content_results="true" 
    hide_content_statistics="true"]
```

### Example 2: Movie without spoilers
```javascript
[kata_movie 
    name="Avengers" 
    hide_content_trailer="true" 
    hide_duration="true"]
```

### Example 3: Dataset with export only
```javascript
[kata_dataset 
    title="Sales Data" 
    hide_content_search="true" 
    hide_content_statistics="true"]
```

---

## ✅ Testing Checklist

- [ ] Open TinyMCE modal
- [ ] Select "Quiz" schema
- [ ] Verify "⚙️ Tùy chỉnh thuộc tính Schema" panel appears
- [ ] Verify "📋 Schema Fields" section (blue)
- [ ] Verify "🎨 Content Display" section (orange)
- [ ] Uncheck 1 schema attribute → shortcode updates
- [ ] Uncheck 1 content attribute → shortcode updates
- [ ] Click "✓ All" → all checkboxes checked
- [ ] Click "✗ None" → all checkboxes unchecked
- [ ] Click "📋 Copy" → shortcode copied
- [ ] Click "✨ Insert" → shortcode inserted to editor
- [ ] Repeat for all 29 schemas

---

*Visualized on: 9 tháng 10, 2025*  
*KATA SEO Manager v1.0.0 - Branch: dev1.2*
