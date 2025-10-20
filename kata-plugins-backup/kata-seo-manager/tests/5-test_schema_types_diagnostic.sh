#!/bin/bash

# KATA SEO Manager - Schema Types Page Test
# This script checks if all required files and configurations are in place

echo "================================================"
echo "KATA SEO Manager - Schema Types Page Diagnostic"
echo "================================================"
echo ""

PLUGIN_DIR="/mnt/chikiet/webseo/timona/wp-content/plugins/kata-seo-manager"

# Test 1: Check if main plugin file exists
echo "Test 1: Main Plugin File"
if [ -f "$PLUGIN_DIR/kata-seo-manager.php" ]; then
    echo "✅ kata-seo-manager.php exists"
    SIZE=$(stat -f%z "$PLUGIN_DIR/kata-seo-manager.php" 2>/dev/null || stat -c%s "$PLUGIN_DIR/kata-seo-manager.php" 2>/dev/null)
    echo "   Size: $SIZE bytes"
else
    echo "❌ kata-seo-manager.php NOT found"
fi
echo ""

# Test 2: Check if schema-types.php exists
echo "Test 2: Schema Types Page File"
if [ -f "$PLUGIN_DIR/admin/schema-types.php" ]; then
    echo "✅ admin/schema-types.php exists"
    SIZE=$(stat -f%z "$PLUGIN_DIR/admin/schema-types.php" 2>/dev/null || stat -c%s "$PLUGIN_DIR/admin/schema-types.php" 2>/dev/null)
    echo "   Size: $SIZE bytes"
    LINES=$(wc -l < "$PLUGIN_DIR/admin/schema-types.php")
    echo "   Lines: $LINES"
else
    echo "❌ admin/schema-types.php NOT found"
fi
echo ""

# Test 3: Check if CSS file exists
echo "Test 3: Schema Types CSS File"
if [ -f "$PLUGIN_DIR/assets/css/schema-types.css" ]; then
    echo "✅ assets/css/schema-types.css exists"
    SIZE=$(stat -f%z "$PLUGIN_DIR/assets/css/schema-types.css" 2>/dev/null || stat -c%s "$PLUGIN_DIR/assets/css/schema-types.css" 2>/dev/null)
    echo "   Size: $SIZE bytes"
else
    echo "❌ assets/css/schema-types.css NOT found"
fi
echo ""

# Test 4: Check if method exists in main file
echo "Test 4: Callback Method 'admin_schema_types_page'"
if grep -q "function admin_schema_types_page" "$PLUGIN_DIR/kata-seo-manager.php"; then
    echo "✅ Method 'admin_schema_types_page' found"
    LINE=$(grep -n "function admin_schema_types_page" "$PLUGIN_DIR/kata-seo-manager.php" | cut -d: -f1)
    echo "   Line: $LINE"
else
    echo "❌ Method 'admin_schema_types_page' NOT found"
fi
echo ""

# Test 5: Check if menu is registered
echo "Test 5: Menu Registration 'kata-seo-schema-types'"
if grep -q "kata-seo-schema-types" "$PLUGIN_DIR/kata-seo-manager.php"; then
    echo "✅ Menu slug 'kata-seo-schema-types' found"
    COUNT=$(grep -c "kata-seo-schema-types" "$PLUGIN_DIR/kata-seo-manager.php")
    echo "   Occurrences: $COUNT"
else
    echo "❌ Menu slug 'kata-seo-schema-types' NOT found"
fi
echo ""

# Test 6: Check if CSS is enqueued
echo "Test 6: CSS Enqueue for schema-types.css"
if grep -q "schema-types.css" "$PLUGIN_DIR/kata-seo-manager.php"; then
    echo "✅ CSS enqueue found"
    LINE=$(grep -n "schema-types.css" "$PLUGIN_DIR/kata-seo-manager.php" | cut -d: -f1)
    echo "   Line: $LINE"
else
    echo "❌ CSS enqueue NOT found"
fi
echo ""

# Test 7: Check for wrapper class in PHP file
echo "Test 7: Main Wrapper Class 'kata-schema-types-wrapper'"
if grep -q "kata-schema-types-wrapper" "$PLUGIN_DIR/admin/schema-types.php"; then
    echo "✅ Wrapper class found in schema-types.php"
else
    echo "❌ Wrapper class NOT found"
fi
echo ""

# Test 8: Check for wrapper class in CSS file
echo "Test 8: CSS Styling for 'kata-schema-types-wrapper'"
if grep -q "kata-schema-types-wrapper" "$PLUGIN_DIR/assets/css/schema-types.css"; then
    echo "✅ Wrapper class styled in CSS"
else
    echo "❌ Wrapper class NOT styled in CSS"
fi
echo ""

# Test 9: Show the include statement
echo "Test 9: Include Statement in Callback Method"
if grep -A 2 "function admin_schema_types_page" "$PLUGIN_DIR/kata-seo-manager.php" | grep -q "include.*schema-types.php"; then
    echo "✅ Include statement found"
    grep -A 2 "function admin_schema_types_page" "$PLUGIN_DIR/kata-seo-manager.php" | grep "include"
else
    echo "❌ Include statement NOT found"
fi
echo ""

# Summary
echo "================================================"
echo "Test Summary"
echo "================================================"
echo ""
echo "If all tests show ✅, the Schema Types page should work."
echo ""
echo "Access the page at:"
echo "http://your-site/wp-admin/admin.php?page=kata-seo-schema-types"
echo ""
echo "Test file location:"
echo "$PLUGIN_DIR/test_schema_types_page.php"
echo ""
