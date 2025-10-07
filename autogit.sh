#!/bin/bash

# Tạo thư mục backup nếu chưa tồn tại
mkdir -p kata-plugins-backup

# Copy tất cả plugin có tiền tố kata sang thư mục backup
cp -r wp-content/plugins/kata* kata-plugins-backup/

echo "Đã copy tất cả plugin có tiền tố 'kata' sang thư mục kata-plugins-backup"

git add .
git commit -m "Auto commit: $(date +"%Y-%m-%d %H:%M:%S")"
git push
echo "Đã tự động commit và push các thay đổi lên nhánh main"