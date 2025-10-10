/**
 * KATA SEO Manager - Dashboard JavaScript
 * Advanced interactions and animations
 */

class KataDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.initializeAnimations();
        this.initializeChart();
        this.initializeCounters();
        this.initializeFilters();
        this.initializeTooltips();
        this.initializeAutoRefresh();
    }

    // Initialize entrance animations
    initializeAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        // Animate cards on scroll
        document.querySelectorAll('.kata-card, .kata-stat-card-v2').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    }

    // Initialize animated counters
    initializeCounters() {
        const counters = document.querySelectorAll('.kata-stat-number[data-count]');
        
        const animateCounter = (counter) => {
            const target = parseInt(counter.dataset.count);
            const duration = 2000; // 2 seconds
            const increment = target / (duration / 16); // 60fps
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                counter.textContent = Math.floor(current);
            }, 16);
        };

        // Trigger animation when element is visible
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateCounter(entry.target);
                }
            });
        });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    // Initialize chart with animation
    initializeChart() {
        const chartCanvas = document.getElementById('kata-schema-usage-chart');
        if (!chartCanvas || typeof Chart === 'undefined') {
            // Fallback: Create a simple visual representation
            this.createFallbackChart(chartCanvas);
            return;
        }

        const ctx = chartCanvas.getContext('2d');
        const data = window.schemaUsageData || [];
        
        // Prepare chart data
        const chartData = {
            labels: data.slice(0, 6).map(item => item.schema_type.charAt(0).toUpperCase() + item.schema_type.slice(1)),
            datasets: [{
                data: data.slice(0, 6).map(item => item.count),
                backgroundColor: [
                    '#2563eb', '#7c3aed', '#059669', 
                    '#dc2626', '#ea580c', '#ca8a04'
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#ffffff'
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // Fallback chart for when Chart.js is not available
    createFallbackChart(canvas) {
        if (!canvas) return;
        
        const container = canvas.parentElement;
        container.innerHTML = `
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 8px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; width: 100%; max-width: 400px; padding: 2rem;">
                    ${window.schemaUsageData ? window.schemaUsageData.slice(0, 6).map((item, index) => {
                        const colors = ['#2563eb', '#7c3aed', '#059669', '#dc2626', '#ea580c', '#ca8a04'];
                        return `
                            <div style="text-align: center;">
                                <div style="width: 40px; height: 40px; background: ${colors[index]}; border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.875rem;">
                                    ${item.count}
                                </div>
                                <div style="font-size: 0.75rem; color: #6b7280; text-transform: capitalize;">
                                    ${item.schema_type}
                                </div>
                            </div>
                        `;
                    }).join('') : '<div style="color: #6b7280;">No data available</div>'}
                </div>
            </div>
        `;
    }

    // Initialize activity filters
    initializeFilters() {
        const filterSelect = document.querySelector('.kata-filter-select');
        const activityItems = document.querySelectorAll('.kata-activity-item');
        
        if (!filterSelect || !activityItems.length) return;

        filterSelect.addEventListener('change', (e) => {
            const filterValue = e.target.value.toLowerCase();
            
            activityItems.forEach((item, index) => {
                const schemaType = item.querySelector('.kata-schema-type');
                const shouldShow = filterValue === 'all' || 
                    (schemaType && schemaType.textContent.toLowerCase().includes(filterValue));
                
                if (shouldShow) {
                    item.style.display = 'flex';
                    item.style.animation = `slideInLeft 0.3s ease-out ${index * 0.1}s forwards`;
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Initialize tooltips
    initializeTooltips() {
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.addEventListener('mouseenter', this.showTooltip);
            element.addEventListener('mouseleave', this.hideTooltip);
        });
    }

    showTooltip(e) {
        const tooltip = document.createElement('div');
        tooltip.className = 'kata-tooltip';
        tooltip.textContent = e.target.dataset.tooltip;
        document.body.appendChild(tooltip);
        
        const rect = e.target.getBoundingClientRect();
        tooltip.style.cssText = `
            position: fixed;
            top: ${rect.top - tooltip.offsetHeight - 8}px;
            left: ${rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)}px;
            background: #1f2937;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            z-index: 1000;
            pointer-events: none;
            animation: fadeInUp 0.2s ease-out;
        `;
    }

    hideTooltip() {
        const tooltip = document.querySelector('.kata-tooltip');
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Auto-refresh functionality
    initializeAutoRefresh() {
        let refreshInterval;
        
        const startAutoRefresh = () => {
            refreshInterval = setInterval(() => {
                this.refreshData();
            }, 300000); // 5 minutes
        };

        const stopAutoRefresh = () => {
            if (refreshInterval) {
                clearInterval(refreshInterval);
            }
        };

        // Start auto-refresh
        startAutoRefresh();

        // Stop auto-refresh when tab is not visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoRefresh();
            } else {
                startAutoRefresh();
            }
        });
    }

    // Refresh dashboard data
    async refreshData() {
        try {
            const response = await fetch(ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'kata_refresh_dashboard_data',
                    nonce: kataAdmin.nonce
                })
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateDashboardData(data.data);
                }
            }
        } catch (error) {
            console.warn('Failed to refresh dashboard data:', error);
        }
    }

    // Update dashboard with new data
    updateDashboardData(data) {
        // Update stat numbers
        Object.keys(data.stats || {}).forEach(key => {
            const element = document.querySelector(`[data-stat="${key}"]`);
            if (element) {
                element.textContent = data.stats[key];
                element.classList.add('kata-updated');
                setTimeout(() => element.classList.remove('kata-updated'), 1000);
            }
        });

        // Update activity list if provided
        if (data.recent_activity) {
            this.updateActivityList(data.recent_activity);
        }
    }

    // Update activity list
    updateActivityList(activities) {
        const activityContainer = document.querySelector('.kata-activity-list');
        if (!activityContainer) return;

        activityContainer.innerHTML = activities.map(activity => `
            <div class="kata-activity-item">
                <div class="kata-activity-icon">
                    <div class="kata-schema-badge ${activity.schema_type.toLowerCase()}">
                        ${activity.schema_type.substring(0, 2).toUpperCase()}
                    </div>
                </div>
                <div class="kata-activity-content">
                    <div class="kata-activity-title">
                        <a href="${activity.edit_link}">${activity.post_title}</a>
                    </div>
                    <div class="kata-activity-meta">
                        <span class="kata-schema-type">${activity.schema_type}</span>
                        <span class="kata-activity-time">${activity.time_ago}</span>
                    </div>
                </div>
                <div class="kata-activity-status">
                    <span class="kata-status-dot active" title="Active & Valid"></span>
                </div>
            </div>
        `).join('');
    }
}

// Export functions for global access
window.kataExportSchemas = function() {
    const data = window.schemaUsageData || [];
    if (!data.length) {
        alert('No data available to export.');
        return;
    }

    const csvContent = "data:text/csv;charset=utf-8," + 
        "Schema Type,Count,Status,Percentage\n" +
        data.map(item => {
            const total = data.reduce((sum, i) => sum + parseInt(i.count), 0);
            const percentage = ((parseInt(item.count) / total) * 100).toFixed(1);
            return `${item.schema_type},${item.count},Active,${percentage}%`;
        }).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `kata-schema-export-${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

window.kataHelp = function() {
    const helpModal = document.createElement('div');
    helpModal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
                <h3 style="margin-top: 0; color: #1f2937;">KATA SEO Manager Help</h3>
                <div style="color: #6b7280; line-height: 1.6;">
                    <p><strong>Schema Types:</strong> Manage and configure different schema types for your content.</p>
                    <p><strong>Analytics:</strong> View detailed statistics about your schema implementation.</p>
                    <p><strong>Validation:</strong> Check the health and validity of your schemas.</p>
                    <p><strong>Export:</strong> Download your schema data as CSV for analysis.</p>
                    <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e5e7eb;">
                    <p><strong>Need more help?</strong></p>
                    <p>Visit <a href="https://schema.org" target="_blank" style="color: #2563eb;">Schema.org</a> for schema documentation.</p>
                    <p>Check <a href="https://developers.google.com/search/docs/appearance/structured-data" target="_blank" style="color: #2563eb;">Google's Guide</a> for best practices.</p>
                </div>
                <div style="margin-top: 2rem; text-align: right;">
                    <button onclick="this.closest('div').remove()" style="background: #2563eb; color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">Close</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(helpModal);
};

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new KataDashboard();
});

// Add some utility CSS for dynamic elements
const style = document.createElement('style');
style.textContent = `
    .kata-updated {
        animation: pulse 1s ease-in-out;
        color: #059669 !important;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);