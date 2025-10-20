/**
 * KATA SEO Manager - Schema Attributes Configuration
 * 
 * Centralized configuration for all schema types with validation and helper methods
 * 
 * @package KATA_SEO_Manager
 * @since 2.1.4
 * @version 2.1.4
 */

/**
 * Schema Attributes Configuration Class
 * Manages schema configurations with validation and utility methods
 */
class KataSEOSchemaConfig {
    /**
     * Constructor
     * Initializes schema configurations
     */
    constructor() {
        this.schemas = this.initializeSchemas();
        this.validationRules = this.initializeValidationRules();
    }

    /**
     * Initialize all schema type configurations
     * @returns {Object} Schema configurations
     */
    initializeSchemas() {
        return {
            article: {
                name: 'Article',
                icon: '📝',
                description: 'Bài viết, tin tức, blog post',
                baseAttrs: {
                    title: { 
                        label: 'Tiêu đề', 
                        required: true, 
                        default: 'Tiêu đề bài viết',
                        type: 'text',
                        maxLength: 110
                    },
                    author: { 
                        label: 'Tác giả', 
                        required: true, 
                        default: 'Tên tác giả',
                        type: 'text'
                    },
                    category: { 
                        label: 'Danh mục', 
                        required: false, 
                        default: 'Uncategorized',
                        type: 'text'
                    },
                    tags: { 
                        label: 'Tags', 
                        required: false, 
                        default: '',
                        type: 'text',
                        placeholder: 'Phân cách bằng dấu phẩy'
                    },
                    excerpt: { 
                        label: 'Tóm tắt', 
                        required: false, 
                        default: '',
                        type: 'textarea',
                        maxLength: 300
                    },
                    reading_time: { 
                        label: 'Thời gian đọc (phút)', 
                        required: false, 
                        default: '5',
                        type: 'number',
                        min: 1
                    },
                    word_count: { 
                        label: 'Số từ', 
                        required: false, 
                        default: '1000',
                        type: 'number',
                        min: 0
                    }
                },
                schemaFields: ['headline', 'author', 'datePublished', 'dateModified', 'image', 'publisher'],
                contentFields: ['title', 'author', 'category', 'tags', 'excerpt', 'reading_time', 'word_count']
            },

            faq: {
                name: 'FAQ',
                icon: '❓',
                description: 'Câu hỏi thường gặp',
                baseAttrs: {},
                schemaFields: ['mainEntity'],
                contentFields: ['question', 'answer'],
                supportsNested: true
            },

            howto: {
                name: 'How-To',
                icon: '📋',
                description: 'Hướng dẫn từng bước',
                baseAttrs: {
                    name: { 
                        label: 'Tên hướng dẫn', 
                        required: true, 
                        default: 'Cách làm...',
                        type: 'text'
                    },
                    description: { 
                        label: 'Mô tả', 
                        required: true, 
                        default: '',
                        type: 'textarea'
                    },
                    steps: { 
                        label: 'Các bước', 
                        required: true, 
                        default: 'Bước 1|Bước 2|Bước 3',
                        type: 'textarea',
                        placeholder: 'Phân cách bằng dấu |'
                    },
                    tools: { 
                        label: 'Công cụ cần thiết', 
                        required: false, 
                        default: '',
                        type: 'text',
                        placeholder: 'Phân cách bằng dấu |'
                    },
                    prep_time: { 
                        label: 'Thời gian chuẩn bị', 
                        required: false, 
                        default: 'PT30M',
                        type: 'duration'
                    },
                    perform_time: { 
                        label: 'Thời gian thực hiện', 
                        required: false, 
                        default: 'PT1H',
                        type: 'duration'
                    },
                    difficulty: { 
                        label: 'Độ khó', 
                        required: false, 
                        default: 'Trung bình',
                        type: 'select',
                        options: ['Dễ', 'Trung bình', 'Khó']
                    }
                },
                schemaFields: ['name', 'description', 'step', 'tool', 'totalTime', 'estimatedCost'],
                contentFields: ['name', 'description', 'steps', 'tools', 'time', 'difficulty']
            },

            event: {
                name: 'Event',
                icon: '📅',
                description: 'Sự kiện, hội thảo',
                baseAttrs: {
                    name: { 
                        label: 'Tên sự kiện', 
                        required: true, 
                        default: '',
                        type: 'text'
                    },
                    description: { 
                        label: 'Mô tả', 
                        required: true, 
                        default: '',
                        type: 'textarea'
                    },
                    start_date: { 
                        label: 'Ngày bắt đầu', 
                        required: true, 
                        default: '2025-12-01T10:00',
                        type: 'datetime-local'
                    },
                    end_date: { 
                        label: 'Ngày kết thúc', 
                        required: false, 
                        default: '2025-12-01T18:00',
                        type: 'datetime-local'
                    },
                    location: { 
                        label: 'Địa điểm', 
                        required: true, 
                        default: '',
                        type: 'text'
                    },
                    organizer: { 
                        label: 'Ban tổ chức', 
                        required: false, 
                        default: '',
                        type: 'text'
                    },
                    price: { 
                        label: 'Giá vé', 
                        required: false, 
                        default: '0',
                        type: 'number',
                        min: 0
                    },
                    currency: { 
                        label: 'Đơn vị tiền tệ', 
                        required: false, 
                        default: 'VND',
                        type: 'select',
                        options: ['VND', 'USD', 'EUR']
                    }
                },
                schemaFields: ['name', 'description', 'startDate', 'endDate', 'location', 'organizer', 'offers'],
                contentFields: ['name', 'description', 'date', 'location', 'organizer', 'price']
            },

            product: {
                name: 'Product',
                icon: '🛍️',
                description: 'Sản phẩm, hàng hóa',
                baseAttrs: {
                    name: { label: 'Tên sản phẩm', required: true, default: '', type: 'text' },
                    description: { label: 'Mô tả', required: true, default: '', type: 'textarea' },
                    price: { label: 'Giá', required: true, default: '0', type: 'number', min: 0 },
                    currency: { label: 'Đơn vị tiền tệ', required: false, default: 'VND', type: 'select', options: ['VND', 'USD', 'EUR'] },
                    brand: { label: 'Thương hiệu', required: false, default: '', type: 'text' },
                    availability: { label: 'Tình trạng', required: false, default: 'InStock', type: 'select', options: ['InStock', 'OutOfStock', 'PreOrder'] },
                    rating_value: { label: 'Đánh giá', required: false, default: '5', type: 'number', min: 0, max: 5, step: 0.1 },
                    rating_count: { label: 'Số lượt đánh giá', required: false, default: '1', type: 'number', min: 0 },
                    category: { label: 'Danh mục', required: false, default: '', type: 'text' }
                },
                schemaFields: ['name', 'description', 'image', 'brand', 'offers', 'aggregateRating', 'review'],
                contentFields: ['name', 'description', 'price', 'brand', 'availability', 'rating']
            },

            recipe: {
                name: 'Recipe',
                icon: '🍳',
                description: 'Công thức nấu ăn',
                baseAttrs: {
                    name: { label: 'Tên món ăn', required: true, default: '', type: 'text' },
                    description: { label: 'Mô tả', required: true, default: '', type: 'textarea' },
                    ingredients: { label: 'Nguyên liệu', required: true, default: '', type: 'textarea', placeholder: 'Phân cách bằng |' },
                    instructions: { label: 'Cách làm', required: true, default: '', type: 'textarea', placeholder: 'Phân cách bằng |' },
                    prep_time: { label: 'Thời gian chuẩn bị', required: false, default: 'PT30M', type: 'duration' },
                    cook_time: { label: 'Thời gian nấu', required: false, default: 'PT1H', type: 'duration' },
                    servings: { label: 'Số khẩu phần', required: false, default: '4', type: 'number', min: 1 },
                    calories: { label: 'Calories', required: false, default: '0', type: 'number', min: 0 }
                },
                schemaFields: ['name', 'description', 'recipeIngredient', 'recipeInstructions', 'prepTime', 'cookTime', 'recipeYield', 'nutrition'],
                contentFields: ['name', 'description', 'ingredients', 'instructions', 'time', 'nutrition']
            },

            course: {
                name: 'Course',
                icon: '📚',
                description: 'Khóa học trực tuyến',
                baseAttrs: {
                    name: { label: 'Tên khóa học', required: true, default: '', type: 'text' },
                    description: { label: 'Mô tả', required: true, default: '', type: 'textarea' },
                    provider: { label: 'Nhà cung cấp', required: true, default: '', type: 'text' },
                    instructor: { label: 'Giảng viên', required: false, default: '', type: 'text' },
                    price: { label: 'Học phí', required: false, default: '0', type: 'number', min: 0 },
                    duration: { label: 'Thời lượng', required: false, default: 'PT40H', type: 'duration' },
                    level: { label: 'Cấp độ', required: false, default: 'beginner', type: 'select', options: ['beginner', 'intermediate', 'advanced'] },
                    skills: { label: 'Kỹ năng học được', required: false, default: '', type: 'textarea', placeholder: 'Phân cách bằng |' }
                },
                schemaFields: ['name', 'description', 'provider', 'instructor', 'offers', 'duration', 'educationalLevel', 'teaches'],
                contentFields: ['name', 'description', 'provider', 'instructor', 'price', 'duration', 'level', 'skills']
            },

            book: {
                name: 'Book',
                icon: '📖',
                description: 'Sách, ebook',
                baseAttrs: {
                    name: { label: 'Tên sách', required: true, default: '', type: 'text' },
                    author: { label: 'Tác giả', required: true, default: '', type: 'text' },
                    description: { label: 'Mô tả', required: true, default: '', type: 'textarea' },
                    publisher: { label: 'Nhà xuất bản', required: false, default: '', type: 'text' },
                    publication_date: { label: 'Ngày xuất bản', required: false, default: '2025-01-01', type: 'date' },
                    pages: { label: 'Số trang', required: false, default: '0', type: 'number', min: 0 },
                    genre: { label: 'Thể loại', required: false, default: '', type: 'text' },
                    isbn: { label: 'ISBN', required: false, default: '', type: 'text' }
                },
                schemaFields: ['name', 'author', 'description', 'publisher', 'datePublished', 'numberOfPages', 'genre', 'isbn'],
                contentFields: ['name', 'author', 'description', 'publisher', 'date', 'pages', 'genre', 'isbn']
            },

            local_business: {
                name: 'Local Business',
                icon: '🏢',
                description: 'Doanh nghiệp địa phương',
                baseAttrs: {
                    name: { label: 'Tên doanh nghiệp', required: true, default: '', type: 'text' },
                    address: { label: 'Địa chỉ', required: true, default: '', type: 'text' },
                    phone: { label: 'Số điện thoại', required: true, default: '', type: 'tel' },
                    email: { label: 'Email', required: false, default: '', type: 'email' },
                    website: { label: 'Website', required: false, default: '', type: 'url' },
                    hours: { label: 'Giờ làm việc', required: false, default: '', type: 'text' },
                    description: { label: 'Mô tả', required: false, default: '', type: 'textarea' },
                    services: { label: 'Dịch vụ', required: false, default: '', type: 'text', placeholder: 'Phân cách bằng dấu phẩy' },
                    rating: { label: 'Đánh giá', required: false, default: '5', type: 'number', min: 0, max: 5, step: 0.1 },
                    review_count: { label: 'Số lượt đánh giá', required: false, default: '0', type: 'number', min: 0 },
                    price_range: { label: 'Mức giá', required: false, default: '$$', type: 'select', options: ['$', '$$', '$$$', '$$$$'] }
                },
                schemaFields: ['name', 'address', 'telephone', 'email', 'url', 'openingHours', 'description', 'aggregateRating'],
                contentFields: ['name', 'address', 'contact', 'hours', 'description', 'services', 'rating']
            },

            job_posting: {
                name: 'Job Posting',
                icon: '💼',
                description: 'Thông tin tuyển dụng',
                baseAttrs: {
                    title: { label: 'Tiêu đề công việc', required: true, default: '', type: 'text' },
                    company: { label: 'Tên công ty', required: true, default: '', type: 'text' },
                    location: { label: 'Địa điểm', required: true, default: '', type: 'text' },
                    description: { label: 'Mô tả công việc', required: true, default: '', type: 'textarea' },
                    salary: { label: 'Mức lương', required: false, default: '', type: 'text' },
                    employment_type: { label: 'Loại hình', required: false, default: 'FULL_TIME', type: 'select', options: ['FULL_TIME', 'PART_TIME', 'CONTRACTOR', 'INTERN'] },
                    date_posted: { label: 'Ngày đăng', required: false, default: new Date().toISOString().split('T')[0], type: 'date' },
                    requirements: { label: 'Yêu cầu', required: false, default: '', type: 'textarea', placeholder: 'Phân cách bằng |' },
                    benefits: { label: 'Quyền lợi', required: false, default: '', type: 'textarea', placeholder: 'Phân cách bằng |' }
                },
                schemaFields: ['title', 'hiringOrganization', 'jobLocation', 'description', 'baseSalary', 'employmentType', 'datePosted', 'validThrough'],
                contentFields: ['title', 'company', 'location', 'description', 'salary', 'requirements', 'benefits']
            },

            image_metadata: {
                name: 'Image Metadata',
                icon: '🖼️',
                description: 'Thông tin metadata ảnh',
                baseAttrs: {
                    url: { label: 'URL ảnh', required: true, default: '', type: 'url' },
                    name: { label: 'Tên ảnh', required: true, default: '', type: 'text' },
                    description: { label: 'Mô tả', required: false, default: '', type: 'textarea' },
                    width: { label: 'Chiều rộng (px)', required: false, default: '1200', type: 'number', min: 1 },
                    height: { label: 'Chiều cao (px)', required: false, default: '800', type: 'number', min: 1 },
                    encoding_format: { label: 'Định dạng', required: false, default: 'JPEG', type: 'select', options: ['JPEG', 'PNG', 'GIF', 'WEBP', 'SVG'] },
                    size: { label: 'Dung lượng', required: false, default: '', type: 'text' },
                    creator: { label: 'Người tạo', required: false, default: '', type: 'text' },
                    date_created: { label: 'Ngày tạo', required: false, default: new Date().toISOString().split('T')[0], type: 'date' },
                    keywords: { label: 'Keywords', required: false, default: '', type: 'text', placeholder: 'Phân cách bằng dấu phẩy' },
                    location: { label: 'Vị trí', required: false, default: '', type: 'text' },
                    camera_model: { label: 'Camera', required: false, default: '', type: 'text' }
                },
                schemaFields: ['contentUrl', 'name', 'description', 'width', 'height', 'encodingFormat', 'contentSize', 'creator', 'dateCreated', 'keywords', 'contentLocation'],
                contentFields: ['preview', 'name', 'description', 'technical', 'dimensions', 'size', 'format', 'camera', 'creator', 'date', 'location', 'keywords']
            }
        };
    }

    /**
     * Initialize validation rules for different field types
     * @returns {Object} Validation rules
     */
    initializeValidationRules() {
        return {
            text: (value, rules) => {
                if (rules.required && !value) return 'Trường này là bắt buộc';
                if (rules.maxLength && value.length > rules.maxLength) return `Không được vượt quá ${rules.maxLength} ký tự`;
                return null;
            },
            number: (value, rules) => {
                if (rules.required && !value) return 'Trường này là bắt buộc';
                const num = parseFloat(value);
                if (isNaN(num)) return 'Phải là số';
                if (rules.min !== undefined && num < rules.min) return `Giá trị tối thiểu là ${rules.min}`;
                if (rules.max !== undefined && num > rules.max) return `Giá trị tối đa là ${rules.max}`;
                return null;
            },
            email: (value, rules) => {
                if (rules.required && !value) return 'Trường này là bắt buộc';
                if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) return 'Email không hợp lệ';
                return null;
            },
            url: (value, rules) => {
                if (rules.required && !value) return 'Trường này là bắt buộc';
                try {
                    if (value) new URL(value);
                    return null;
                } catch {
                    return 'URL không hợp lệ';
                }
            }
        };
    }

    /**
     * Get schema configuration by type
     * @param {string} type - Schema type (e.g., 'article', 'faq')
     * @returns {Object|null} Schema configuration or null if not found
     */
    getSchema(type) {
        return this.schemas[type] || null;
    }

    /**
     * Get all available schema types
     * @returns {Array<string>} Array of schema type names
     */
    getSchemaTypes() {
        return Object.keys(this.schemas);
    }

    /**
     * Get schema options for dropdowns
     * @returns {Array<Object>} Array of {value, label, icon, description}
     */
    getSchemaOptions() {
        return Object.entries(this.schemas).map(([key, schema]) => ({
            value: key,
            label: schema.name,
            icon: schema.icon,
            description: schema.description || ''
        }));
    }

    /**
     * Validate attribute value
     * @param {string} schemaType - Schema type
     * @param {string} attrName - Attribute name
     * @param {*} value - Value to validate
     * @returns {string|null} Error message or null if valid
     */
    validateAttribute(schemaType, attrName, value) {
        const schema = this.getSchema(schemaType);
        if (!schema || !schema.baseAttrs[attrName]) {
            return 'Thuộc tính không hợp lệ';
        }

        const attr = schema.baseAttrs[attrName];
        const validator = this.validationRules[attr.type] || this.validationRules.text;
        
        return validator(value, attr);
    }

    /**
     * Get default values for schema type
     * @param {string} schemaType - Schema type
     * @returns {Object} Object with default values
     */
    getDefaultValues(schemaType) {
        const schema = this.getSchema(schemaType);
        if (!schema) return {};

        const defaults = {};
        for (const [key, attr] of Object.entries(schema.baseAttrs)) {
            defaults[key] = attr.default;
        }
        return defaults;
    }

    /**
     * Check if schema type supports nested shortcodes
     * @param {string} schemaType - Schema type
     * @returns {boolean} True if supports nested
     */
    supportsNested(schemaType) {
        const schema = this.getSchema(schemaType);
        return schema?.supportsNested || false;
    }

    /**
     * Get required fields for schema type
     * @param {string} schemaType - Schema type
     * @returns {Array<string>} Array of required field names
     */
    getRequiredFields(schemaType) {
        const schema = this.getSchema(schemaType);
        if (!schema) return [];

        return Object.entries(schema.baseAttrs)
            .filter(([_, attr]) => attr.required)
            .map(([key, _]) => key);
    }

    /**
     * Format value for display
     * @param {string} schemaType - Schema type
     * @param {string} attrName - Attribute name
     * @param {*} value - Value to format
     * @returns {string} Formatted value
     */
    formatValue(schemaType, attrName, value) {
        const schema = this.getSchema(schemaType);
        if (!schema || !schema.baseAttrs[attrName]) {
            return String(value);
        }

        const attr = schema.baseAttrs[attrName];
        
        switch (attr.type) {
            case 'number':
                return new Intl.NumberFormat('vi-VN').format(value);
            case 'date':
                return new Date(value).toLocaleDateString('vi-VN');
            case 'datetime-local':
                return new Date(value).toLocaleString('vi-VN');
            default:
                return String(value);
        }
    }

    /**
     * Check if attribute exists in schema
     * @param {string} schemaType - Schema type
     * @param {string} attrName - Attribute name
     * @returns {boolean} True if exists
     */
    hasAttribute(schemaType, attrName) {
        const schema = this.getSchema(schemaType);
        return schema && schema.baseAttrs.hasOwnProperty(attrName);
    }

    /**
     * Get attribute configuration
     * @param {string} schemaType - Schema type
     * @param {string} attrName - Attribute name
     * @returns {Object|null} Attribute config or null
     */
    getAttribute(schemaType, attrName) {
        const schema = this.getSchema(schemaType);
        return schema?.baseAttrs[attrName] || null;
    }
}

// Initialize global instance
const kataSEOSchemaConfig = new KataSEOSchemaConfig();

// Export for different module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = kataSEOSchemaConfig;
}

// Make available globally for WordPress
window.KataSEOSchemaConfig = KataSEOSchemaConfig;
window.kataSEOSchemaConfig = kataSEOSchemaConfig;

// BACKWARD COMPATIBILITY - Keep old variable
window.KATA_SCHEMA_ATTRIBUTES = kataSEOSchemaConfig.schemas;

console.log('✅ KATA SEO Schema Config loaded successfully');
