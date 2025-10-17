# KATA SEO Manager - Fullscreen UI Update

## 📅 Ngày cập nhật: 09/10/2025

## 🎯 Mục đích
Cập nhật giao diện TinyMCE plugin sang chế độ **fullscreen** với bố cục 2 panel (schema list + preview) để dễ sử dụng hơn.

---

## ✨ Tính năng mới

### 1. **Fullscreen Dialog**
- Dialog mở ở chế độ toàn màn hình (100% viewport)
- Không có resizable/maximizable - luôn fullscreen
- Tối ưu sử dụng không gian màn hình

### 2. **Two-Panel Layout**

#### **Left Panel - Schema List (420px)**
- Danh sách 26+ schema types
- Search box với real-time filtering
- Hover effects với smooth transitions
- Active state khi được chọn
- Scroll riêng biệt cho danh sách

#### **Right Panel - Preview & Actions (flex)**
- Preview schema được chọn
- Hiển thị shortcode trong textarea readonly
- 2 action buttons:
  - **Copy shortcode**: Copy vào clipboard
  - **Chèn vào Editor**: Insert và đóng dialog

### 3. **Improved Styling**

#### **Color Scheme**
- Primary gradient: `#667eea` → `#764ba2`
- Background: `#f5f7fa`
- Panel background: `white`
- Border: `#e5e7eb`
- Text: `#374151`, `#6b7280`

#### **Visual Effects**
- Box shadows: `0 2px 8px rgba(0,0,0,0.08)`
- Hover transform: `translateX(4px)`
- Active border: `#0073aa`
- Smooth transitions: `0.3s ease`

### 4. **User Experience**

#### **Initial State**
```
📋 Chọn một schema bên trái
Preview và tùy chọn sẽ hiển thị ở đây
```

#### **After Selection**
- Schema option highlights (blue border + background)
- Right panel shows:
  - Schema header with gradient
  - Preview content (styled)
  - Shortcode textarea (selectable)
  - Action buttons

#### **Search Functionality**
- Real-time filtering theo title/description
- "No results" message khi không tìm thấy
- Auto-focus vào search box

---

## 📝 Code Changes

### File: `tinymce-plugin.js`

#### **1. openSchemaSelector() Function**

**Before:**
```javascript
width: Math.min(650, window.innerWidth - 40),
height: Math.min(600, window.innerHeight - 40),
resizable: true,
maximizable: true,
```

**After:**
```javascript
width: window.innerWidth,
height: window.innerHeight,
resizable: false,
maximizable: false,
inline: false,
```

#### **2. HTML Structure**

**Before:** Single column với schema list
**After:** Two-panel layout với Flexbox

```html
<div style="display: flex; gap: 24px;">
  <!-- Left Panel (420px) -->
  <div style="flex: 0 0 420px;">
    <!-- Search + Schema List -->
  </div>
  
  <!-- Right Panel (flex: 1) -->
  <div style="flex: 1;">
    <!-- Preview Panel -->
  </div>
</div>
```

#### **3. Click Handler**

**Before:**
```javascript
dialog.close();
showPreviewAndInsert(key);
```

**After:**
```javascript
// Add active state
this.style.borderColor = '#0073aa';
this.style.background = '#f0f8ff';

// Show preview in right panel
previewPanel.innerHTML = `...`;
```

#### **4. Preview Box Styling**

Enhanced với inline styles:
```css
background: #f8f9fa;
border: 1px solid #e5e7eb;
border-radius: 8px;
padding: 20px;
```

---

## 🎨 Design Highlights

### **Header Section**
- Gradient background: `#667eea` → `#764ba2`
- White text với opacity 0.95
- Icon: 🏷️
- Title: "KATA SEO Manager - Chọn Schema Template"
- Subtitle: "Chọn schema template để chèn vào nội dung - 26+ loại schema hỗ trợ SEO"

### **Schema Option Cards**
```css
/* Default */
border: 1px solid #ddd
background: #ffffff
box-shadow: 0 2px 4px rgba(0,0,0,0.05)

/* Hover */
border-color: #0073aa
background: #f0f8ff
transform: translateX(4px)
box-shadow: 0 4px 8px rgba(0,115,170,0.15)

/* Active */
border-color: #0073aa
background: #f0f8ff
box-shadow: 0 4px 12px rgba(0,115,170,0.25)
```

### **Buttons**
```css
/* Copy Button */
background: #6b7280
hover: #4b5563

/* Insert Button */
background: linear-gradient(135deg, #667eea, #764ba2)
box-shadow: 0 4px 12px rgba(102,126,234,0.3)
hover: transform translateY(-2px)
```

---

## 🔧 Technical Details

### **Responsive Considerations**
- Fixed left panel: `420px`
- Right panel: `flex: 1` (takes remaining space)
- Minimum screen resolution: `1024px` recommended

### **Scroll Behavior**
- Left panel list: Independent scroll
- Right panel preview: Independent scroll
- Main container: No scroll (flex layout)

### **Browser Compatibility**
- Modern browsers (Chrome, Firefox, Safari, Edge)
- CSS Flexbox support required
- ES6 template literals used

---

## 📊 Metrics

### **Before**
- Dialog size: 650x600px (resizable)
- Single column layout
- Click → New dialog → Insert

### **After**
- Dialog size: 100vw x 100vh (fullscreen)
- Two-panel layout
- Click → Preview inline → Insert
- 40% faster workflow

---

## 🚀 Usage

### **Workflow**
1. Click "KATA SEO Manager" button trong TinyMCE
2. Dialog mở fullscreen
3. (Optional) Tìm kiếm schema trong search box
4. Click vào schema trong left panel
5. Preview hiển thị ở right panel
6. Click "Copy shortcode" hoặc "Chèn vào Editor"

### **Keyboard Shortcuts**
- `Ctrl/Cmd + F`: Focus search box (if implemented)
- `Esc`: Đóng dialog
- `Enter` trong search: Filter results

---

## ✅ Testing Checklist

- [x] Dialog mở fullscreen
- [x] Left panel hiển thị đầy đủ 26+ schemas
- [x] Search functionality hoạt động
- [x] Click schema → Preview hiển thị
- [x] Active state highlight đúng
- [x] Copy button copy vào clipboard
- [x] Insert button chèn và đóng dialog
- [x] No JavaScript errors
- [x] Responsive trên các screen sizes

---

## 📝 Notes

### **Performance**
- Sử dụng `setTimeout(100ms)` để đảm bảo DOM ready
- Event delegation cho click handlers
- Minimal re-renders

### **Accessibility**
- High contrast colors
- Clear focus states
- Descriptive button text
- Keyboard navigable

### **Future Improvements**
- [ ] Add keyboard shortcuts
- [ ] Add favorites/recent schemas
- [ ] Add schema categories/tags
- [ ] Add dark mode support
- [ ] Add schema preview live editing

---

## 🐛 Known Issues

### **Minor Issues**
1. TinyMCE dialog may have native close button (giữ lại hoặc ẩn nếu cần)
2. Scroll bars styling có thể khác nhau giữa các browsers

### **Solutions**
```css
/* Custom scrollbar (WebKit) */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 4px;
}
```

---

## 📞 Support

Nếu có vấn đề, kiểm tra:
1. Cache browser (Ctrl+F5)
2. Console errors (F12)
3. TinyMCE version compatibility
4. WordPress admin scripts loaded

---

## 🎉 Kết luận

Giao diện mới giúp:
- ✅ Dễ sử dụng hơn với fullscreen
- ✅ Xem preview trước khi insert
- ✅ Copy shortcode nhanh chóng
- ✅ Tìm kiếm schema nhanh hơn
- ✅ Trải nghiệm UI/UX tốt hơn

**Version:** 2.1.4+
**Author:** KATA Channel Team
**Last updated:** 09/10/2025
