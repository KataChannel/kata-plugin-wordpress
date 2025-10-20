#!/bin/bash

# Script to organize test files by moving them to tests/ directory with numbering
# Targets files with patterns: *test*, *_test_*, *-test-*
# Usage: ./testclean.sh

set -e  # Exit on any error

echo "� Starting test files cleanup and organization..."

# Tạo thư mục tests nếu chưa tồn tại
mkdir -p tests

# Lấy số thứ tự cao nhất hiện có trong thư mục tests
max_number=0
if ls tests/*test* 1> /dev/null 2>&1; then
    for file in tests/*test*; do
        if [ -f "$file" ]; then
            # Trích xuất số từ tên file (format: số-tên)
            filename=$(basename "$file")
            if [[ "$filename" =~ ^([0-9]+)- ]]; then
                number="${BASH_REMATCH[1]}"
                if [ "$number" -gt "$max_number" ]; then
                    max_number=$number
                fi
            fi
        fi
    done
fi

# Bắt đầu từ số tiếp theo
next_number=$((max_number + 1))
moved_count=0

echo "📁 Next available number: $next_number"

# Tìm tất cả file test ở root level với các pattern khác nhau
shopt -s nullglob  # Prevent glob expansion if no matches
test_files=()

# Collect files with different test patterns
for pattern in "*test*" "*_test_*" "*-test-*" "*TEST*"; do
    for file in $pattern; do
        if [ -f "$file" ]; then
            test_files+=("$file")
        fi
    done
done

# Remove duplicates
declare -A seen
unique_test_files=()
for file in "${test_files[@]}"; do
    if [ -z "${seen[$file]}" ]; then
        seen[$file]=1
        unique_test_files+=("$file")
    fi
done

shopt -u nullglob

if [ ${#unique_test_files[@]} -eq 0 ]; then
    echo "🧪 No test files found in root directory"
    exit 0
fi

echo "🔍 Found ${#unique_test_files[@]} test files to organize"

# Sắp xếp file theo thời gian sửa đổi (mtime)
declare -A file_times
for file in "${unique_test_files[@]}"; do
    # Skip if already in tests/
    if [ ! -f "$file" ]; then
        continue
    fi
    
    # Get modification time
    file_times["$file"]=$(stat -c %Y "$file" 2>/dev/null || echo "0")
done

# Sort files by filename (simpler and more predictable)
sorted_files=()
for file in "${!file_times[@]}"; do
    sorted_files+=("$file")
done

# Sort by filename
IFS=$'\n' sorted_files=($(sort <<<"${sorted_files[*]}"))
unset IFS

# Process each file
for file in "${sorted_files[@]}"; do
    if [ ! -f "$file" ]; then
        continue
    fi
    
    filename=$(basename "$file")
    
    # Skip cleanup scripts themselves
    if [[ "$filename" == "testclean.sh" || "$filename" == "docsclean.sh" ]]; then
        echo "⏭️  Skipping cleanup script: $filename"
        continue
    fi
    
    # Get file extension
    extension=""
    if [[ "$filename" == *.* ]]; then
        extension=".${filename##*.}"
    fi
    
    # Kiểm tra xem file đã có số thứ tự chưa
    if [[ ! "$filename" =~ ^[0-9]+- ]]; then
        # Thêm số thứ tự vào tên file
        new_filename="${next_number}-${filename}"
        
        # Check if target already exists
        if [ -f "tests/$new_filename" ]; then
            echo "⚠️  Target exists: tests/$new_filename - skipping"
            continue
        fi
        
        mv "$file" "tests/$new_filename"
        echo "🧪 Moved: $filename → tests/$new_filename"
        next_number=$((next_number + 1))
        moved_count=$((moved_count + 1))
    else
        # File đã có số thứ tự, chỉ cần di chuyển nếu chưa trong tests/
        if [ -f "tests/$filename" ]; then
            echo "⚠️  Already exists: tests/$filename - removing duplicate"
            rm "$file"
        else
            mv "$file" "tests/$filename"
            echo "🧪 Moved: $filename → tests/$filename"
            moved_count=$((moved_count + 1))
        fi
    fi
done

echo ""
echo "✅ Done! Moved $moved_count test files to tests/"
echo "📊 Total files in tests/: $(ls -1 tests/ 2>/dev/null | wc -l)"

# List final state
if ls tests/* 1> /dev/null 2>&1; then
    echo ""
    echo "📁 Current tests/ contents:"
    ls -1 tests/ | head -10
    if [ $(ls -1 tests/ | wc -l) -gt 10 ]; then
        echo "   ... and $(($(ls -1 tests/ | wc -l) - 10)) more files"
    fi
fi
