#!/bin/bash

# Script to organize markdown files by moving them to docs/ directory with numbering
# Enhanced version with batch processing and categorization
#
# Usage: 
#   ./docsclean.sh [options]
#
# Options:
#   --dry-run          Show what would be done without making changes
#   --archive          Archive old docs to docs/archive/
#   --category PREFIX  Only process files starting with PREFIX
#   --help             Show this help message
#
# Examples:
#   ./docsclean.sh                    # Normal operation
#   ./docsclean.sh --dry-run          # Preview changes
#   ./docsclean.sh --archive          # Archive old docs
#   ./docsclean.sh --category FIX     # Only process FIX-*.md files

set -e  # Exit on any error

# Parse command line arguments
DRY_RUN=false
ARCHIVE_OLD=false
CATEGORY=""

while [[ $# -gt 0 ]]; do
    case $1 in
        --dry-run)
            DRY_RUN=true
            shift
            ;;
        --archive)
            ARCHIVE_OLD=true
            shift
            ;;
        --category)
            CATEGORY="$2"
            shift 2
            ;;
        --help)
            head -n 20 "$0" | tail -n +3
            exit 0
            ;;
        *)
            echo "❌ Unknown option: $1"
            echo "Use --help for usage information"
            exit 1
            ;;
    esac
done

echo "🧹 Starting docs cleanup and organization..."
if [ "$DRY_RUN" = true ]; then
    echo "🔍 DRY RUN MODE - No changes will be made"
fi
if [ "$ARCHIVE_OLD" = true ]; then
    echo "📦 Archive mode enabled"
fi
if [ -n "$CATEGORY" ]; then
    echo "🏷️  Category filter: $CATEGORY"
fi
echo ""

# Tạo thư mục docs nếu chưa tồn tại
if [ "$DRY_RUN" = false ]; then
    mkdir -p docs
    if [ "$ARCHIVE_OLD" = true ]; then
        mkdir -p docs/archive
    fi
fi

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local color=$1
    local message=$2
    echo -e "${color}${message}${NC}"
}

# Function to show progress
show_progress() {
    local current=$1
    local total=$2
    local percentage=$((current * 100 / total))
    local bar_length=50
    local filled=$((percentage * bar_length / 100))
    
    printf "\r["
    printf "%${filled}s" | tr ' ' '='
    printf "%$((bar_length - filled))s" | tr ' ' ' '
    printf "] %3d%% (%d/%d)" "$percentage" "$current" "$total"
}

# Lấy số thứ tự cao nhất hiện có trong thư mục docs
max_number=0
if ls docs/*.md 1> /dev/null 2>&1; then
    for file in docs/*.md; do
        if [ -f "$file" ]; then
            # Trích xuất số từ tên file (format: số-tên.md)
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
skipped_count=0
archived_count=0
error_count=0

print_status "$BLUE" "📁 Next available number: $next_number"
echo ""

# Statistics arrays
declare -a moved_files
declare -a skipped_files
declare -a archived_files
declare -a error_files

# Tìm tất cả file .md ở root level, trừ README.md
shopt -s nullglob  # Prevent glob expansion if no matches
md_files=(*.md)
shopt -u nullglob

# Apply category filter if specified
if [ -n "$CATEGORY" ]; then
    filtered_files=()
    for file in "${md_files[@]}"; do
        if [[ "$file" =~ ^${CATEGORY} ]]; then
            filtered_files+=("$file")
        fi
    done
    md_files=("${filtered_files[@]}")
    print_status "$YELLOW" "🏷️  Filtered to ${#md_files[@]} files matching category: $CATEGORY"
fi

if [ ${#md_files[@]} -eq 0 ]; then
    print_status "$YELLOW" "📄 No .md files found in root directory"
    exit 0
fi

total_files=${#md_files[@]}
print_status "$GREEN" "📊 Found $total_files .md files to process"
echo ""

# Sắp xếp file theo thời gian sửa đổi (mtime)
declare -A file_times
for file in "${md_files[@]}"; do
    # Skip README.md (case insensitive)
    if [[ "${file,,}" == "readme.md" ]]; then
        continue
    fi
    
    # Skip if already in docs/
    if [ ! -f "$file" ]; then
        continue
    fi
    
    # Get modification time
    file_times["$file"]=$(stat -c %Y "$file" 2>/dev/null || echo "0")
done

# Sort files by modification time
sorted_files=()
while IFS= read -r file_entry; do
    # Split time and filename
    file_name="${file_entry#*:}"
    if [ -n "$file_name" ]; then
        sorted_files+=("$file_name")
    fi
done < <(for file in "${!file_times[@]}"; do
    printf '%s:%s\n' "${file_times[$file]}" "$file"
done | sort -n | cut -d: -f2-)

# Process each file
current_file=0
for file in "${sorted_files[@]}"; do
    current_file=$((current_file + 1))
    
    # Show progress bar
    if [ "$DRY_RUN" = false ]; then
        show_progress "$current_file" "$total_files"
    fi
    
    if [ ! -f "$file" ]; then
        continue
    fi
    
    filename=$(basename "$file")
    
    # Skip README.md (case insensitive)
    if [[ "${filename,,}" == "readme.md" ]]; then
        skipped_files+=("$filename (README)")
        skipped_count=$((skipped_count + 1))
        continue
    fi
    
    # Process based on file state
    if [[ ! "$filename" =~ ^[0-9]+-.*\.md$ ]]; then
        # File without number - add number and move
        new_filename="${next_number}-${filename}"
        
        # Check if target already exists
        if [ -f "docs/$new_filename" ]; then
            if [ "$ARCHIVE_OLD" = true ]; then
                # Archive the existing file
                if [ "$DRY_RUN" = false ]; then
                    mv "docs/$new_filename" "docs/archive/$new_filename"
                fi
                archived_files+=("$new_filename")
                archived_count=$((archived_count + 1))
            else
                skipped_files+=("$filename (target exists: $new_filename)")
                skipped_count=$((skipped_count + 1))
                continue
            fi
        fi
        
        # Move file
        if [ "$DRY_RUN" = false ]; then
            if mv "$file" "docs/$new_filename" 2>/dev/null; then
                moved_files+=("$filename → docs/$new_filename")
                moved_count=$((moved_count + 1))
                next_number=$((next_number + 1))
            else
                error_files+=("$filename (move failed)")
                error_count=$((error_count + 1))
            fi
        else
            echo "  [DRY RUN] Would move: $filename → docs/$new_filename"
            moved_count=$((moved_count + 1))
            next_number=$((next_number + 1))
        fi
    else
        # File already has number - check if needs to move
        if [ -f "docs/$filename" ]; then
            # Duplicate - handle based on mode
            if [ "$ARCHIVE_OLD" = true ] && [ "$DRY_RUN" = false ]; then
                mv "docs/$filename" "docs/archive/$filename"
                archived_files+=("$filename (duplicate)")
                archived_count=$((archived_count + 1))
                
                mv "$file" "docs/$filename"
                moved_files+=("$filename → docs/$filename (replaced)")
                moved_count=$((moved_count + 1))
            else
                if [ "$DRY_RUN" = false ]; then
                    rm "$file"
                fi
                skipped_files+=("$filename (duplicate removed)")
                skipped_count=$((skipped_count + 1))
            fi
        else
            # Just move it
            if [ "$DRY_RUN" = false ]; then
                if mv "$file" "docs/$filename" 2>/dev/null; then
                    moved_files+=("$filename → docs/$filename")
                    moved_count=$((moved_count + 1))
                else
                    error_files+=("$filename (move failed)")
                    error_count=$((error_count + 1))
                fi
            else
                echo "  [DRY RUN] Would move: $filename → docs/$filename"
                moved_count=$((moved_count + 1))
            fi
        fi
    fi
done

# Clear progress bar line
if [ "$DRY_RUN" = false ]; then
    echo -e "\n"
fi

# Print summary report
echo "════════════════════════════════════════════════════════"
print_status "$GREEN" "✅ Operation Complete!"
echo "════════════════════════════════════════════════════════"
echo ""

# Summary statistics
print_status "$BLUE" "📊 Summary:"
echo "  • Processed: $total_files files"
echo "  • Moved:     $moved_count files"
echo "  • Skipped:   $skipped_count files"
if [ "$ARCHIVE_OLD" = true ]; then
    echo "  • Archived:  $archived_count files"
fi
if [ $error_count -gt 0 ]; then
    print_status "$RED" "  • Errors:    $error_count files"
fi
echo ""

# Total files in docs
if [ "$DRY_RUN" = false ]; then
    total_docs=$(ls -1 docs/*.md 2>/dev/null | wc -l)
    print_status "$GREEN" "📁 Total files in docs/: $total_docs"
    
    if [ "$ARCHIVE_OLD" = true ]; then
        total_archived=$(ls -1 docs/archive/*.md 2>/dev/null | wc -l)
        if [ $total_archived -gt 0 ]; then
            print_status "$YELLOW" "📦 Total files in archive/: $total_archived"
        fi
    fi
fi
echo ""

# Detailed lists (if not too many)
if [ ${#moved_files[@]} -gt 0 ] && [ ${#moved_files[@]} -le 20 ]; then
    print_status "$GREEN" "📝 Moved files:"
    for item in "${moved_files[@]}"; do
        echo "  ✓ $item"
    done
    echo ""
elif [ ${#moved_files[@]} -gt 20 ]; then
    print_status "$GREEN" "📝 Moved files (first 10):"
    for i in {0..9}; do
        if [ $i -lt ${#moved_files[@]} ]; then
            echo "  ✓ ${moved_files[$i]}"
        fi
    done
    echo "  ... and $((${#moved_files[@]} - 10)) more"
    echo ""
fi

if [ ${#skipped_files[@]} -gt 0 ] && [ ${#skipped_files[@]} -le 10 ]; then
    print_status "$YELLOW" "⏭️  Skipped files:"
    for item in "${skipped_files[@]}"; do
        echo "  ⊘ $item"
    done
    echo ""
fi

if [ ${#archived_files[@]} -gt 0 ] && [ ${#archived_files[@]} -le 10 ]; then
    print_status "$YELLOW" "📦 Archived files:"
    for item in "${archived_files[@]}"; do
        echo "  📦 $item"
    done
    echo ""
fi

if [ ${#error_files[@]} -gt 0 ]; then
    print_status "$RED" "❌ Errors:"
    for item in "${error_files[@]}"; do
        echo "  ✗ $item"
    done
    echo ""
fi

# List final state (first 15 files)
if [ "$DRY_RUN" = false ] && ls docs/*.md 1> /dev/null 2>&1; then
    print_status "$BLUE" "📁 Current docs/ contents:"
    ls -1 docs/*.md | head -15 | while read -r file; do
        basename "$file"
    done | nl -w2 -s'. '
    
    remaining=$(($(ls -1 docs/*.md | wc -l) - 15))
    if [ $remaining -gt 0 ]; then
        echo "   ... and $remaining more files"
    fi
fi

echo ""
print_status "$GREEN" "════════════════════════════════════════════════════════"
