<style>
/* ================================================
   KATA POLL MANAGEMENT - MODERN SENIOR UI STYLES
   ================================================ */

/* Base & Reset */
.kata-poll-management-wrap {
    background: #f5f7fa;
    margin: -20px -20px 0 -22px;
    padding: 0;
    min-height: 100vh;
}

.kata-poll-management-wrap * {
    box-sizing: border-box;
}

/* ================================================
   HEADER SECTION
   ================================================ */
.kata-poll-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 40px;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    overflow: hidden;
}

.kata-poll-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.kata-page-title {
    color: white;
    font-size: 32px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    animation: fadeInDown 0.6s ease;
}

.kata-page-title .dashicons {
    font-size: 36px;
    width: 36px;
    height: 36px;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.kata-page-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 15px;
    margin: 0;
    animation: fadeIn 0.8s ease 0.2s backwards;
}

.header-actions {
    display: flex;
    gap: 12px;
    animation: fadeInRight 0.6s ease 0.3s backwards;
}

/* ================================================
   STATISTICS CARDS
   ================================================ */
.kata-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    padding: 30px 40px;
    animation: fadeInUp 0.6s ease 0.4s backwards;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: scaleY(0);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-card:hover::before {
    transform: scaleY(1);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    animation: pulse 2s ease-in-out infinite;
}

.stat-card-success .stat-icon {
    background: linear-gradient(135deg, #06D6A0 0%, #1B9AAA 100%);
}

.stat-card-info .stat-icon {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
}

.stat-card-warning .stat-icon {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ================================================
   POLLS LIST CONTAINER
   ================================================ */
.kata-polls-list-container {
    margin: 0 40px 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: fadeInUp 0.6s ease 0.5s backwards;
}

.kata-polls-list-container.hidden {
    max-height: 0;
    margin-bottom: 0;
    opacity: 0;
    transform: translateY(-20px);
}

.list-header {
    padding: 24px 30px;
    border-bottom: 2px solid #f0f2f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafbfc;
}

.list-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.list-controls {
    display: flex;
    gap: 12px;
}

.search-input {
    padding: 10px 16px;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    font-size: 14px;
    width: 250px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-select {
    padding: 10px 16px;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 180px;
}

.filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* ================================================
   POLLS GRID
   ================================================ */
.polls-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 24px;
    padding: 30px;
}

.poll-card {
    background: white;
    border: 2px solid #e0e6ed;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    animation: fadeInUp 0.5s ease backwards;
}

.poll-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-color: #667eea;
}

.poll-card.hidden {
    display: none;
}

.poll-card-header {
    padding: 16px 20px;
    background: #fafbfc;
    border-bottom: 1px solid #e0e6ed;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.poll-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.poll-status.status-active {
    background: #d4edda;
    color: #155724;
}

.poll-status.status-closed {
    background: #f8d7da;
    color: #721c24;
}

.poll-status.status-draft {
    background: #fff3cd;
    color: #856404;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    animation: pulse 2s ease-in-out infinite;
}

.poll-id {
    font-size: 12px;
    font-weight: 600;
    color: #7f8c8d;
}

.poll-card-body {
    padding: 20px;
}

.poll-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.poll-question {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0 0 16px 0;
    line-height: 1.5;
}

.poll-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 16px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #7f8c8d;
}

.meta-item .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

.poll-shortcode {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: #f8f9fa;
    border: 1px solid #e0e6ed;
    border-radius: 6px;
}

.poll-shortcode code {
    flex: 1;
    font-size: 12px;
    color: #667eea;
    font-weight: 600;
    background: none;
    padding: 0;
}

.copy-shortcode-btn {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    color: #7f8c8d;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.copy-shortcode-btn:hover {
    color: #667eea;
    background: rgba(102, 126, 234, 0.1);
}

.poll-card-footer {
    padding: 16px 20px;
    background: #fafbfc;
    border-top: 1px solid #e0e6ed;
    display: flex;
    gap: 8px;
}

.action-btn {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #e0e6ed;
    background: white;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    color: #2c3e50;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none;
}

.action-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.action-btn .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
}

/* ================================================
   MODAL SYSTEM
   ================================================ */
.kata-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 100000;
    animation: fadeIn 0.3s ease;
}

.kata-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.kata-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
}

.kata-modal-container {
    position: relative;
    background: white;
    border-radius: 16px;
    width: 90%;
    max-width: 900px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideInUp 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.kata-modal-header {
    padding: 24px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-modal-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kata-modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    color: white;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kata-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.kata-modal-body {
    padding: 30px;
    max-height: calc(90vh - 180px);
    overflow-y: auto;
}

/* Custom scrollbar */
.kata-modal-body::-webkit-scrollbar {
    width: 8px;
}

.kata-modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.kata-modal-body::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 4px;
}

/* ================================================
   FORM STYLES
   ================================================ */
.kata-poll-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-label .required {
    color: #e74c3c;
}

.form-control {
    padding: 12px 16px;
    border: 2px solid #e0e6ed;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s ease;
    width: 100%;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.field-hint {
    font-size: 12px;
    color: #7f8c8d;
    margin: 0;
}

/* ================================================
   OPTIONS CONTAINER
   ================================================ */
.options-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 12px;
}

.option-row {
    display: grid;
    grid-template-columns: 40px 1fr 40px;
    gap: 12px;
    align-items: center;
    animation: fadeInDown 0.3s ease;
}

.option-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.option-input {
    flex: 1;
}

.remove-option-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fee;
    border: none;
    color: #e74c3c;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-option-btn:hover {
    background: #e74c3c;
    color: white;
    transform: scale(1.1);
}

/* ================================================
   BUTTONS
   ================================================ */
.kata-button {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    font-family: inherit;
}

.kata-button-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.kata-button-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
}

.kata-button-secondary {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.kata-button-secondary:hover {
    background: #667eea;
    color: white;
}

.kata-button-small {
    padding: 8px 16px;
    font-size: 13px;
}

.kata-modal-footer {
    padding: 20px 30px;
    background: #fafbfc;
    border-top: 2px solid #e0e6ed;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

/* ================================================
   EMPTY STATE
   ================================================ */
.empty-state {
    text-align: center;
    padding: 60px 30px;
}

.empty-icon {
    font-size: 64px;
    color: #e0e6ed;
    margin-bottom: 20px;
}

.empty-icon .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
}

.empty-state h3 {
    font-size: 20px;
    color: #2c3e50;
    margin: 0 0 8px 0;
}

.empty-state p {
    font-size: 14px;
    color: #7f8c8d;
    margin: 0 0 24px 0;
}

/* ================================================
   ANIMATIONS
   ================================================ */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* ================================================
   RESPONSIVE DESIGN
   ================================================ */
@media (max-width: 1200px) {
    .kata-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .polls-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 768px) {
    .kata-poll-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
        padding: 24px;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .kata-stats-grid {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .kata-polls-list-container {
        margin: 0 20px 20px;
    }
    
    .list-header {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }
    
    .list-controls {
        width: 100%;
        flex-direction: column;
    }
    
    .search-input,
    .filter-select {
        width: 100%;
    }
    
    .polls-grid {
        grid-template-columns: 1fr;
        padding: 20px;
    }
    
    .kata-modal-container {
        width: 95%;
        max-height: 95vh;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .option-row {
        grid-template-columns: 32px 1fr 32px;
    }
}
</style>
