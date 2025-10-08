<style>
/* ==================================================
   KATA WHEEL MANAGEMENT - SENIOR UI/UX STYLES
   Modern, Clean, Animated, Responsive
   ================================================== */

/* Base & Reset */
.kata-wheel-management-wrap {
    background: #f5f7fa;
    margin: -20px -20px 0 -22px;
    padding: 0;
    min-height: 100vh;
}

/* ==================================================
   HEADER SECTION
   ================================================== */
.kata-admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 32px 40px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.kata-header-left {
    flex: 1;
}

.kata-page-title {
    color: white;
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 14px;
    line-height: 1.2;
}

.kata-page-title .dashicons {
    font-size: 36px;
    width: 36px;
    height: 36px;
    animation: spin 3s linear infinite;
}

@keyframes spin {
    0%, 90% { transform: rotate(0deg); }
    95%, 100% { transform: rotate(360deg); }
}

.kata-page-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 16px;
    margin: 0;
    font-weight: 400;
}

.kata-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

/* ==================================================
   STATS CARDS
   ================================================== */
.kata-stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    padding: 30px 40px;
    margin: 0;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 18px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    border: 2px solid transparent;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.4s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: #667eea;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-icon {
    font-size: 48px;
    line-height: 1;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 32px;
    font-weight: 800;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 6px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 13px;
    color: #6c757d;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.stat-trend {
    font-size: 11px;
    color: #28a745;
    font-weight: 600;
    background: rgba(40, 167, 69, 0.1);
    padding: 4px 8px;
    border-radius: 12px;
    white-space: nowrap;
}

/* ==================================================
   WHEELS LIST CONTAINER
   ================================================== */
.kata-wheels-list-container {
    padding: 0 40px 40px;
    animation: slideDown 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transform-origin: top;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
        max-height: 0;
    }
    to {
        opacity: 1;
        transform: translateY(0);
        max-height: 5000px;
    }
}

.kata-wheels-list-container.hiding {
    animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

@keyframes slideUp {
    from {
        opacity: 1;
        transform: translateY(0);
        max-height: 5000px;
    }
    to {
        opacity: 0;
        transform: translateY(-20px);
        max-height: 0;
        padding: 0;
        margin: 0;
        overflow: hidden;
    }
}

.kata-wheel-list-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 24px 28px;
    border-bottom: 2px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.card-title {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.kata-search-input,
.kata-filter-select {
    padding: 10px 16px;
    border: 2px solid #e1e8ed;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.kata-search-input {
    min-width: 250px;
}

.kata-search-input:focus,
.kata-filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.card-body {
    padding: 28px;
}

/* ==================================================
   WHEELS GRID
   ================================================== */
.wheels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.wheel-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    border: 2px solid #f1f3f5;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
}

.wheel-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(102, 126, 234, 0.15);
    border-color: #667eea;
}

.wheel-card-header {
    padding: 16px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dee2e6;
}

.wheel-id-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 13px;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
}

.wheel-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.wheel-status-active {
    background: rgba(40, 167, 69, 0.15);
    color: #28a745;
}

.wheel-status-active .status-dot {
    background: #28a745;
}

.wheel-status-inactive {
    background: rgba(220, 53, 69, 0.15);
    color: #dc3545;
}

.wheel-status-inactive .status-dot {
    background: #dc3545;
}

.wheel-status-scheduled {
    background: rgba(255, 193, 7, 0.15);
    color: #ffc107;
}

.wheel-status-scheduled .status-dot {
    background: #ffc107;
}

.wheel-status-expired {
    background: rgba(108, 117, 125, 0.15);
    color: #6c757d;
}

.wheel-status-expired .status-dot {
    background: #6c757d;
}

.wheel-card-body {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.wheel-title {
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.wheel-desc {
    color: #6c757d;
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.wheel-stats-mini {
    display: flex;
    gap: 12px;
    padding: 14px 0;
    border-top: 1px solid #f1f3f5;
    border-bottom: 1px solid #f1f3f5;
}

.stat-mini {
    flex: 1;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.stat-mini-icon {
    font-size: 20px;
}

.stat-mini-value {
    font-size: 18px;
    font-weight: 700;
    color: #667eea;
}

.stat-mini-label {
    font-size: 11px;
    color: #adb5bd;
    text-transform: uppercase;
    font-weight: 600;
}

.shortcode-display {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.shortcode-code {
    flex: 1;
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #667eea;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.copy-shortcode-btn {
    background: none;
    border: none;
    color: #667eea;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.copy-shortcode-btn:hover {
    color: #764ba2;
    transform: scale(1.15);
}

.wheel-card-footer {
    padding: 16px 20px;
    background: #f8f9fa;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #e9ecef;
}

.wheel-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #adb5bd;
}

.wheel-actions {
    display: flex;
    gap: 8px;
}

/* ==================================================
   MODAL STYLES
   ================================================== */
.kata-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.kata-modal.kata-modal-show {
    display: flex;
    animation: modalFadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.kata-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    animation: overlayFadeIn 0.3s ease;
}

@keyframes overlayFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.kata-modal-container {
    position: relative;
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    z-index: 1;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-30px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.kata-modal-header {
    padding: 24px 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 20px 20px 0 0;
}

.kata-modal-title {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.kata-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.kata-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.kata-modal-body {
    padding: 28px;
    overflow-y: auto;
    flex: 1;
}

/* ==================================================
   FORM STYLES
   ================================================== */
.kata-wheel-form {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group-full {
    grid-column: 1 / -1;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.form-label .required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e1e8ed;
    border-radius: 10px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.field-hint {
    font-size: 12px;
    color: #6c757d;
    margin: 4px 0 0 0;
    font-style: italic;
}

/* ==================================================
   PRIZES CONTAINER
   ================================================== */
.prizes-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px solid #e9ecef;
}

.prize-row {
    display: grid;
    grid-template-columns: 40px 2fr 1fr 1.5fr 90px 60px 40px;
    gap: 10px;
    align-items: center;
    padding: 12px;
    background: white;
    border-radius: 10px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.prize-row:hover {
    border-color: #667eea;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
}

.prize-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 2px 6px rgba(102, 126, 234, 0.3);
}

.prize-color {
    width: 60px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    border: 2px solid #dee2e6;
}

/* ==================================================
   BUTTONS
   ================================================== */
.kata-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    font-family: inherit;
}

.kata-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.kata-button:active {
    transform: translateY(0);
}

.kata-button-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.kata-button-secondary {
    background: #6c757d;
    color: white;
}

.kata-button-small {
    padding: 8px 16px;
    font-size: 13px;
}

.kata-button-lg {
    padding: 14px 28px;
    font-size: 15px;
}

.kata-btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    border: none;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 16px;
}

.kata-btn-edit {
    background: #17a2b8;
    color: white;
}

.kata-btn-analytics {
    background: #28a745;
    color: white;
}

.kata-btn-delete {
    background: #dc3545;
    color: white;
}

.kata-btn-icon:hover {
    transform: scale(1.1);
}

.modal-footer {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 2px solid #f1f3f5;
}

/* ==================================================
   EMPTY STATE
   ================================================== */
.empty-state {
    text-align: center;
    padding: 80px 40px;
    color: #6c757d;
}

.empty-icon {
    font-size: 80px;
    opacity: 0.3;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    color: #2c3e50;
    margin: 0 0 12px 0;
}

.empty-state p {
    font-size: 16px;
    margin: 0 0 24px 0;
}

/* ==================================================
   RESPONSIVE
   ================================================== */
@media (max-width: 1200px) {
    .kata-stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    .wheels-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .kata-admin-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .kata-header-actions {
        width: 100%;
    }
    .kata-button-lg {
        width: 100%;
    }
    .kata-stats-container {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    .kata-wheels-list-container {
        padding: 0 20px 20px;
    }
    .wheels-grid {
        grid-template-columns: 1fr;
    }
    .form-grid {
        grid-template-columns: 1fr;
    }
    .prize-row {
        grid-template-columns: 40px 1fr 40px;
        grid-template-areas:
            "num name delete"
            "num value delete"
            "num type delete"
            "num prob delete"
            "num color delete";
    }
    .prize-number { grid-area: num; }
    .prize-text { grid-area: name; }
    .prize-value { grid-area: value; }
    .prize-type { grid-area: type; }
    .prize-prob { grid-area: prob; }
    .prize-color { grid-area: color; width: 100%; }
    .remove-prize { grid-area: delete; }
}

/* ==================================================
   ANIMATIONS
   ================================================== */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
