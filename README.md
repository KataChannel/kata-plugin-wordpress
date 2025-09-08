# 🔌 Kata Plugin WordPress Repository

Repository chuyên dụng cho các plugin WordPress của Kata Channel.

## 📦 Plugins Included

Repository này chứa tất cả các plugin WordPress với prefix "kata" được phát triển bởi Kata Channel:

### 🤖 Kata Chatbot
- **Mô tả**: Plugin chatbot AI tích hợp Google AI Studio
- **Tính năng**: Chat tự động, AI response, analytics, admin dashboard
- **Version**: 1.0.0
- **Status**: ✅ Production Ready

### 📊 Kata SEO Analyzer  
- **Mô tả**: Plugin phân tích và tối ưu SEO
- **Tính năng**: SEO analysis, content optimization, meta management
- **Version**: Latest
- **Status**: ✅ Active Development

### 📝 Kata Schema Markup
- **Mô tả**: Plugin tạo schema markup tự động
- **Tính năng**: JSON-LD schema, rich snippets, SEO enhancement
- **Version**: Latest
- **Status**: ✅ Active Development

### 📱 Kata ShareSocial
- **Mô tả**: Plugin chia sẻ mạng xã hội
- **Tính năng**: Social sharing buttons, analytics, customization
- **Version**: Latest
- **Status**: ✅ Active Development

### 📋 Kata Form
- **Mô tả**: Plugin tạo form nâng cao
- **Tính năng**: Form builder, validation, submissions management
- **Version**: Latest
- **Status**: ✅ Active Development

## 🚀 Auto-Deployment

Repository này được cập nhật tự động thông qua script `autogit.sh` từ môi trường development:

```bash
# Backup và push plugins tự động
./autogit.sh

# Chỉ backup plugins
./autogit.sh --backup-only

# Kiểm tra status
./autogit.sh --status
```

## 📁 Repository Structure

```
kata-plugin-wordpress/
├── .gitignore              # Chỉ cho phép kata-plugins-backup
├── README.md              # File này
└── kata-plugins-backup/   # Thư mục chứa tất cả kata plugins
    ├── kata-chatbot/      # Plugin chatbot AI
    ├── kata-seo-analyzer/ # Plugin SEO analyzer
    ├── kata-schema-markup/# Plugin schema markup
    ├── kata-sharesocial/  # Plugin social sharing
    └── kata-form/         # Plugin form builder
```

## 🔧 Development Workflow

### Branch Strategy:
- **main**: Stable releases
- **dev1.1**: Bug fixes và minor updates
- **dev1.2**: New features và major updates

### Update Process:
1. Development environment auto-backup plugins to `kata-plugins-backup/`
2. Auto-commit changes to appropriate branch
3. Auto-push to GitHub repository
4. Manual review and merge if needed

## 📋 Installation

### Manual Installation:
1. Download plugin từ repository
2. Upload to `/wp-content/plugins/`
3. Activate trong WordPress admin

### Git Clone:
```bash
git clone https://github.com/KataChannel/kata-plugin-wordpress.git
cd kata-plugin-wordpress
cp -r kata-plugins-backup/* /path/to/wordpress/wp-content/plugins/
```

## 🛠️ Requirements

- **WordPress**: 5.0+
- **PHP**: 7.4+
- **MySQL**: 5.6+

### Plugin-specific Requirements:
- **Kata Chatbot**: Google AI Studio API Key
- **Kata SEO Analyzer**: WordPress REST API enabled
- **Kata Schema Markup**: JSON-LD support
- **Kata ShareSocial**: Social media API keys (optional)

## 📊 Plugin Status

| Plugin | Version | Status | Last Update |
|--------|---------|--------|-------------|
| kata-chatbot | 1.0.0 | ✅ Stable | 2025-09-08 |
| kata-seo-analyzer | Latest | 🔄 Development | 2025-09-08 |
| kata-schema-markup | Latest | 🔄 Development | 2025-09-08 |
| kata-sharesocial | Latest | 🔄 Development | 2025-09-08 |
| kata-form | Latest | 🔄 Development | 2025-09-08 |

## 🔒 Security

- Tất cả plugins tuân thủ WordPress Coding Standards
- Input sanitization và output escaping
- Nonce verification cho AJAX requests
- SQL injection protection với prepared statements

## 📞 Support

- **Repository**: [KataChannel/kata-plugin-wordpress](https://github.com/KataChannel/kata-plugin-wordpress)
- **Issues**: [GitHub Issues](https://github.com/KataChannel/kata-plugin-wordpress/issues)
- **Documentation**: Available in each plugin folder

## 📄 License

GPL v2 or later - General Public License

---

**🔌 Powered by Kata Channel** | **📅 Last Updated**: September 8, 2025
