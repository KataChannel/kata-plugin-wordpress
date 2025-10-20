/**
 * Schema Attributes Configuration
 * Cấu hình chi tiết các thuộc tính cho từng loại Schema
 */

const KATA_SCHEMA_ATTRIBUTES = {
    article: {
        name: 'Article',
        icon: '📝',
        baseAttrs: {
            title: { label: 'Tiêu đề', required: true, default: 'Tiêu đề bài viết' },
            author: { label: 'Tác giả', required: true, default: 'Tên tác giả' },
            category: { label: 'Danh mục', required: false, default: 'Uncategorized' },
            tags: { label: 'Tags', required: false, default: '' },
            excerpt: { label: 'Tóm tắt', required: false, default: '' },
            reading_time: { label: 'Thời gian đọc (phút)', required: false, default: '5' },
            word_count: { label: 'Số từ', required: false, default: '1000' }
        },
        schemaFields: ['headline', 'author', 'datePublished', 'dateModified', 'image', 'publisher'],
        contentFields: ['title', 'author', 'category', 'tags', 'excerpt', 'reading_time', 'word_count']
    },
    
    faq: {
        name: 'FAQ',
        icon: '❓',
        baseAttrs: {
            // FAQ sử dụng nested shortcode
        },
        schemaFields: ['mainEntity'],
        contentFields: ['question', 'answer']
    },
    
    howto: {
        name: 'How-To',
        icon: '📋',
        baseAttrs: {
            name: { label: 'Tên hướng dẫn', required: true, default: 'Cách làm...' },
            description: { label: 'Mô tả', required: true, default: '' },
            steps: { label: 'Các bước (phân cách bằng |)', required: true, default: 'Bước 1|Bước 2|Bước 3' },
            tools: { label: 'Công cụ cần thiết (phân cách bằng |)', required: false, default: '' },
            prep_time: { label: 'Thời gian chuẩn bị', required: false, default: '30M' },
            perform_time: { label: 'Thời gian thực hiện', required: false, default: '1H' },
            difficulty: { label: 'Độ khó', required: false, default: 'Trung bình' }
        },
        schemaFields: ['name', 'description', 'step', 'tool', 'totalTime', 'estimatedCost'],
        contentFields: ['name', 'description', 'steps', 'tools', 'time', 'difficulty']
    },
    
    event: {
        name: 'Event',
        icon: '📅',
        baseAttrs: {
            name: { label: 'Tên sự kiện', required: true, default: '' },
            description: { label: 'Mô tả', required: true, default: '' },
            start_date: { label: 'Ngày bắt đầu', required: true, default: '2025-12-01T10:00' },
            end_date: { label: 'Ngày kết thúc', required: false, default: '2025-12-01T18:00' },
            location: { label: 'Địa điểm', required: true, default: '' },
            organizer: { label: 'Ban tổ chức', required: false, default: '' },
            price: { label: 'Giá vé', required: false, default: '0' },
            currency: { label: 'Đơn vị tiền tệ', required: false, default: 'VND' }
        },
        schemaFields: ['name', 'description', 'startDate', 'endDate', 'location', 'organizer', 'offers'],
        contentFields: ['name', 'description', 'date', 'location', 'organizer', 'price']
    },
    
    product: {
        name: 'Product',
        icon: '🛍️',
        baseAttrs: {
            name: { label: 'Tên sản phẩm', required: true, default: '' },
            description: { label: 'Mô tả', required: true, default: '' },
            price: { label: 'Giá', required: true, default: '0' },
            currency: { label: 'Đơn vị tiền tệ', required: false, default: 'VND' },
            brand: { label: 'Thương hiệu', required: false, default: '' },
            availability: { label: 'Tình trạng', required: false, default: 'InStock' },
            rating_value: { label: 'Đánh giá', required: false, default: '5' },
            rating_count: { label: 'Số lượt đánh giá', required: false, default: '1' },
            category: { label: 'Danh mục', required: false, default: '' }
        },
        schemaFields: ['name', 'description', 'image', 'brand', 'offers', 'aggregateRating', 'review'],
        contentFields: ['name', 'description', 'price', 'brand', 'availability', 'rating']
    },
    
    recipe: {
        name: 'Recipe',
        icon: '🍳',
        baseAttrs: {
            name: { label: 'Tên món ăn', required: true, default: '' },
            description: { label: 'Mô tả', required: true, default: '' },
            ingredients: { label: 'Nguyên liệu (phân cách bằng |)', required: true, default: '' },
            instructions: { label: 'Cách làm (phân cách bằng |)', required: true, default: '' },
            prep_time: { label: 'Thời gian chuẩn bị', required: false, default: '30M' },
            cook_time: { label: 'Thời gian nấu', required: false, default: '1H' },
            servings: { label: 'Số khẩu phần', required: false, default: '4' },
            calories: { label: 'Calories', required: false, default: '0' }
        },
        schemaFields: ['name', 'description', 'recipeIngredient', 'recipeInstructions', 'prepTime', 'cookTime', 'recipeYield', 'nutrition'],
        contentFields: ['name', 'description', 'ingredients', 'instructions', 'time', 'nutrition']
    },
    
    course: {
        name: 'Course',
        icon: '📚',
        baseAttrs: {
            name: { label: 'Tên khóa học', required: true, default: '' },
            description: { label: 'Mô tả', required: true, default: '' },
            provider: { label: 'Nhà cung cấp', required: true, default: '' },
            instructor: { label: 'Giảng viên', required: false, default: '' },
            price: { label: 'Học phí', required: false, default: '0' },
            duration: { label: 'Thời lượng', required: false, default: '40H' },
            level: { label: 'Cấp độ', required: false, default: 'beginner' },
            skills: { label: 'Kỹ năng học được (phân cách bằng |)', required: false, default: '' }
        },
        schemaFields: ['name', 'description', 'provider', 'instructor', 'offers', 'duration', 'educationalLevel', 'teaches'],
        contentFields: ['name', 'description', 'provider', 'instructor', 'price', 'duration', 'level', 'skills']
    },
    
    book: {
        name: 'Book',
        icon: '📖',
        baseAttrs: {
            name: { label: 'Tên sách', required: true, default: '' },
            author: { label: 'Tác giả', required: true, default: '' },
            description: { label: 'Mô tả', required: true, default: '' },
            publisher: { label: 'Nhà xuất bản', required: false, default: '' },
            publication_date: { label: 'Ngày xuất bản', required: false, default: '2025-01-01' },
            pages: { label: 'Số trang', required: false, default: '0' },
            genre: { label: 'Thể loại', required: false, default: '' },
            isbn: { label: 'ISBN', required: false, default: '' }
        },
        schemaFields: ['name', 'author', 'description', 'publisher', 'datePublished', 'numberOfPages', 'genre', 'isbn'],
        contentFields: ['name', 'author', 'description', 'publisher', 'date', 'pages', 'genre', 'isbn']
    },
    
    local_business: {
        name: 'Local Business',
        icon: '🏢',
        baseAttrs: {
            name: { label: 'Tên doanh nghiệp', required: true, default: '' },
            address: { label: 'Địa chỉ', required: true, default: '' },
            phone: { label: 'Số điện thoại', required: true, default: '' },
            email: { label: 'Email', required: false, default: '' },
            website: { label: 'Website', required: false, default: '' },
            hours: { label: 'Giờ làm việc', required: false, default: '' },
            description: { label: 'Mô tả', required: false, default: '' },
            services: { label: 'Dịch vụ (phân cách bằng ,)', required: false, default: '' },
            rating: { label: 'Đánh giá', required: false, default: '5' },
            review_count: { label: 'Số lượt đánh giá', required: false, default: '0' },
            price_range: { label: 'Mức giá', required: false, default: '$$' }
        },
        schemaFields: ['name', 'address', 'telephone', 'email', 'url', 'openingHours', 'description', 'aggregateRating'],
        contentFields: ['name', 'address', 'contact', 'hours', 'description', 'services', 'rating']
    },
    
    job_posting: {
        name: 'Job Posting',
        icon: '💼',
        baseAttrs: {
            title: { label: 'Tiêu đề công việc', required: true, default: '' },
            company: { label: 'Tên công ty', required: true, default: '' },
            location: { label: 'Địa điểm', required: true, default: '' },
            description: { label: 'Mô tả công việc', required: true, default: '' },
            salary: { label: 'Mức lương', required: false, default: '' },
            employment_type: { label: 'Loại hình', required: false, default: 'FULL_TIME' },
            date_posted: { label: 'Ngày đăng', required: false, default: '2025-10-08' },
            requirements: { label: 'Yêu cầu (phân cách bằng |)', required: false, default: '' },
            benefits: { label: 'Quyền lợi (phân cách bằng |)', required: false, default: '' }
        },
        schemaFields: ['title', 'hiringOrganization', 'jobLocation', 'description', 'baseSalary', 'employmentType', 'datePosted', 'validThrough'],
        contentFields: ['title', 'company', 'location', 'description', 'salary', 'requirements', 'benefits']
    },
    
    image_metadata: {
        name: 'Image Metadata',
        icon: '🖼️',
        baseAttrs: {
            url: { label: 'URL ảnh', required: true, default: '' },
            name: { label: 'Tên ảnh', required: true, default: '' },
            description: { label: 'Mô tả', required: false, default: '' },
            width: { label: 'Chiều rộng', required: false, default: '1200' },
            height: { label: 'Chiều cao', required: false, default: '800' },
            encoding_format: { label: 'Định dạng', required: false, default: 'JPEG' },
            size: { label: 'Dung lượng', required: false, default: '' },
            creator: { label: 'Người tạo', required: false, default: '' },
            date_created: { label: 'Ngày tạo', required: false, default: '2025-10-08' },
            keywords: { label: 'Keywords (phân cách bằng ,)', required: false, default: '' },
            location: { label: 'Vị trí', required: false, default: '' },
            camera_model: { label: 'Camera', required: false, default: '' }
        },
        schemaFields: ['contentUrl', 'name', 'description', 'width', 'height', 'encodingFormat', 'contentSize', 'creator', 'dateCreated', 'keywords', 'contentLocation'],
        contentFields: ['preview', 'name', 'description', 'technical', 'dimensions', 'size', 'format', 'camera', 'creator', 'date', 'location', 'keywords']
    }
};
