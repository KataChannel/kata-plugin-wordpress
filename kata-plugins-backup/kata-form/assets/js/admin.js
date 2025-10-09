/**
 * Kata Form Admin JavaScript
 * Handles admin interface interactions, AJAX requests, and chart rendering
 */

(function($) {
    'use strict';

    // Main Admin Object
    window.KataFormAdmin = {
        init: function() {
            this.bindEvents();
            this.initCharts();
            this.initModals();
            this.initDatePickers();
        },

        bindEvents: function() {
            // Status change handler
            $(document).on('change', '.status-select', this.handleStatusChange);
            
            // Export handlers
            $('#export-submissions').on('click', this.showExportModal);
            $('#export-confirm').on('click', this.exportSubmissions);
            
            // Import handler
            $('#import-file').on('change', this.handleFileSelect);
            $('#import-confirm').on('click', this.importSubmissions);
            
            // Bulk actions
            $('#bulk-action-submit').on('click', this.handleBulkAction);
            
            // Settings form
            $('#kata-settings-form').on('submit', this.saveSettings);
            
            // Filter form
            $('#submission-filters').on('submit', this.filterSubmissions);
            
            // Modal close events
            $(document).on('click', '.kata-modal-close, .kata-modal', this.closeModal);
            $(document).on('click', '.kata-modal-content', function(e) {
                e.stopPropagation();
            });
            
            // ESC key to close modal
            $(document).on('keyup', function(e) {
                if (e.keyCode === 27) {
                    KataFormAdmin.closeModal();
                }
            });

            // Refresh charts button
            $('#refresh-charts').on('click', this.refreshCharts);

            // Delete data confirmation
            $('#delete-all-data').on('click', this.confirmDeleteData);
        },

        handleStatusChange: function() {
            var $select = $(this);
            var submissionId = $select.data('submission-id');
            var newStatus = $select.val();
            var oldStatus = $select.data('original-status');

            // Show loading
            $select.after('<span class="kata-loading"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_update_submission_status',
                    submission_id: submissionId,
                    status: newStatus,
                    nonce: kata_admin_vars.nonce
                },
                success: function(response) {
                    $('.kata-loading').remove();
                    if (response.success) {
                        $select.data('original-status', newStatus);
                        KataFormAdmin.showMessage('Status updated successfully', 'success');
                        // Update status badge in table
                        var $badge = $select.closest('tr').find('.status-badge');
                        $badge.removeClass('status-new status-read status-archived')
                               .addClass('status-' + newStatus)
                               .text(newStatus.charAt(0).toUpperCase() + newStatus.slice(1));
                    } else {
                        $select.val(oldStatus);
                        KataFormAdmin.showMessage('Failed to update status', 'error');
                    }
                },
                error: function() {
                    $('.kata-loading').remove();
                    $select.val(oldStatus);
                    KataFormAdmin.showMessage('Error updating status', 'error');
                }
            });
        },

        showExportModal: function(e) {
            e.preventDefault();
            $('#export-modal').fadeIn(300);
        },

        exportSubmissions: function(e) {
            e.preventDefault();
            
            var format = $('#export-format').val();
            var dateFrom = $('#export-date-from').val();
            var dateTo = $('#export-date-to').val();
            var formId = $('#export-form-id').val();
            var status = $('#export-status').val();

            // Show loading
            $('#export-confirm').prop('disabled', true).append('<span class="kata-loading"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_export_submissions',
                    format: format,
                    date_from: dateFrom,
                    date_to: dateTo,
                    form_id: formId,
                    status: status,
                    nonce: kata_admin_vars.nonce
                },
                success: function(response) {
                    $('#export-confirm').prop('disabled', false).find('.kata-loading').remove();
                    
                    if (response.success) {
                        // Create download link
                        var link = document.createElement('a');
                        link.href = 'data:text/csv;charset=utf-8,' + encodeURI(response.data.content);
                        link.download = response.data.filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        
                        KataFormAdmin.closeModal();
                        KataFormAdmin.showMessage('Export completed successfully', 'success');
                    } else {
                        KataFormAdmin.showMessage(response.data.message || 'Export failed', 'error');
                    }
                },
                error: function() {
                    $('#export-confirm').prop('disabled', false).find('.kata-loading').remove();
                    KataFormAdmin.showMessage('Error during export', 'error');
                }
            });
        },

        handleFileSelect: function() {
            var file = this.files[0];
            if (file) {
                $('#import-file-name').text(file.name);
                $('#import-confirm').prop('disabled', false);
            }
        },

        importSubmissions: function(e) {
            e.preventDefault();
            
            var fileInput = $('#import-file')[0];
            if (!fileInput.files.length) {
                KataFormAdmin.showMessage('Please select a file to import', 'error');
                return;
            }

            var formData = new FormData();
            formData.append('action', 'kata_import_submissions');
            formData.append('file', fileInput.files[0]);
            formData.append('nonce', kata_admin_vars.nonce);

            // Show loading
            $('#import-confirm').prop('disabled', true).append('<span class="kata-loading"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#import-confirm').prop('disabled', false).find('.kata-loading').remove();
                    
                    if (response.success) {
                        KataFormAdmin.closeModal();
                        KataFormAdmin.showMessage(response.data.message, 'success');
                        location.reload(); // Reload to show imported data
                    } else {
                        KataFormAdmin.showMessage(response.data.message || 'Import failed', 'error');
                    }
                },
                error: function() {
                    $('#import-confirm').prop('disabled', false).find('.kata-loading').remove();
                    KataFormAdmin.showMessage('Error during import', 'error');
                }
            });
        },

        handleBulkAction: function(e) {
            e.preventDefault();
            
            var action = $('#bulk-action').val();
            if (!action) {
                KataFormAdmin.showMessage('Please select an action', 'error');
                return;
            }

            var checkedItems = $('input[name="submission_ids[]"]:checked');
            if (checkedItems.length === 0) {
                KataFormAdmin.showMessage('Please select items to perform bulk action', 'error');
                return;
            }

            var ids = [];
            checkedItems.each(function() {
                ids.push($(this).val());
            });

            if (action === 'delete' && !confirm('Bạn có chắc chắn muốn xóa các dữ liệu đã chọn?')) {
                return;
            }

            // Show loading
            $('#bulk-action-submit').prop('disabled', true).append('<span class="kata-loading"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_bulk_action',
                    bulk_action: action,
                    submission_ids: ids,
                    nonce: kata_admin_vars.nonce
                },
                success: function(response) {
                    $('#bulk-action-submit').prop('disabled', false).find('.kata-loading').remove();
                    
                    if (response.success) {
                        KataFormAdmin.showMessage(response.data.message, 'success');
                        location.reload();
                    } else {
                        KataFormAdmin.showMessage(response.data.message || 'Action failed', 'error');
                    }
                },
                error: function() {
                    $('#bulk-action-submit').prop('disabled', false).find('.kata-loading').remove();
                    KataFormAdmin.showMessage('Error performing bulk action', 'error');
                }
            });
        },

        saveSettings: function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            formData += '&action=kata_save_settings&nonce=' + kata_admin_vars.nonce;

            // Show loading
            $('#submit').prop('disabled', true).append('<span class="kata-loading"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#submit').prop('disabled', false).find('.kata-loading').remove();
                    
                    if (response.success) {
                        KataFormAdmin.showMessage('Settings saved successfully', 'success');
                    } else {
                        KataFormAdmin.showMessage(response.data.message || 'Failed to save settings', 'error');
                    }
                },
                error: function() {
                    $('#submit').prop('disabled', false).find('.kata-loading').remove();
                    KataFormAdmin.showMessage('Error saving settings', 'error');
                }
            });
        },

        filterSubmissions: function(e) {
            e.preventDefault();
            // Form will submit normally to reload page with filters
        },

        closeModal: function() {
            $('.kata-modal').fadeOut(300);
        },

        initModals: function() {
            // Pre-populate export form options
            if ($('#export-modal').length) {
                // Populate form options from global data if available
                if (typeof kata_admin_vars.forms !== 'undefined') {
                    var $formSelect = $('#export-form-id');
                    $.each(kata_admin_vars.forms, function(id, title) {
                        $formSelect.append('<option value="' + id + '">' + title + '</option>');
                    });
                }
            }
        },

        initDatePickers: function() {
            // Initialize date pickers if jQuery UI is available
            if ($.fn.datepicker) {
                $('.date-picker').datepicker({
                    dateFormat: 'yy-mm-dd'
                });
            } else {
                // Fallback to HTML5 date input
                $('.date-picker').attr('type', 'date');
            }
        },

        initCharts: function() {
            // Initialize daily submissions chart
            if ($('#daily-chart').length && typeof Chart !== 'undefined') {
                this.initDailyChart();
            }

            // Initialize form breakdown chart
            if ($('#form-chart').length && typeof Chart !== 'undefined') {
                this.initFormChart();
            }
        },

        initDailyChart: function() {
            var ctx = document.getElementById('daily-chart').getContext('2d');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_get_chart_data',
                    chart: 'daily',
                    nonce: kata_admin_vars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: response.data.labels,
                                datasets: [{
                                    label: 'Submissions',
                                    data: response.data.data,
                                    borderColor: '#0073aa',
                                    backgroundColor: 'rgba(0, 115, 170, 0.1)',
                                    tension: 0.1,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            });
        },

        initFormChart: function() {
            var ctx = document.getElementById('form-chart').getContext('2d');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_get_chart_data',
                    chart: 'forms',
                    nonce: kata_admin_vars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: response.data.labels,
                                datasets: [{
                                    data: response.data.data,
                                    backgroundColor: [
                                        '#0073aa',
                                        '#00a32a',
                                        '#d63638',
                                        '#ff6900',
                                        '#f0b849',
                                        '#826eb4',
                                        '#ea4aaa'
                                    ]
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                    }
                }
            });
        },

        refreshCharts: function(e) {
            e.preventDefault();
            
            // Show loading
            $(this).prop('disabled', true).append('<span class="kata-loading"></span>');
            
            // Reinitialize charts
            KataFormAdmin.initCharts();
            
            // Remove loading
            setTimeout(function() {
                $('#refresh-charts').prop('disabled', false).find('.kata-loading').remove();
                KataFormAdmin.showMessage('Charts refreshed', 'success');
            }, 1000);
        },

        confirmDeleteData: function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete ALL submission data? This action cannot be undone!')) {
                return;
            }

            if (!confirm('This will permanently delete all submissions and statistics. Type "DELETE" to confirm:')) {
                return;
            }

            var confirmation = prompt('Type "DELETE" to confirm:');
            if (confirmation !== 'DELETE') {
                KataFormAdmin.showMessage('Operation cancelled', 'warning');
                return;
            }

            // Show loading
            $(this).prop('disabled', true).append('<span class="kata-loading"></span>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'kata_delete_all_data',
                    nonce: kata_admin_vars.nonce
                },
                success: function(response) {
                    $('#delete-all-data').prop('disabled', false).find('.kata-loading').remove();
                    
                    if (response.success) {
                        KataFormAdmin.showMessage('All data deleted successfully', 'success');
                        location.reload();
                    } else {
                        KataFormAdmin.showMessage(response.data.message || 'Failed to delete data', 'error');
                    }
                },
                error: function() {
                    $('#delete-all-data').prop('disabled', false).find('.kata-loading').remove();
                    KataFormAdmin.showMessage('Error deleting data', 'error');
                }
            });
        },

        showMessage: function(message, type) {
            var $message = $('<div class="kata-message ' + type + '">' + 
                            '<span class="dashicons dashicons-' + (type === 'success' ? 'yes' : 'warning') + '"></span>' +
                            message + '</div>');
            
            $('.wrap h1').after($message);
            
            setTimeout(function() {
                $message.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        },

        // Utility function for loading states
        showLoading: function($element) {
            $element.prop('disabled', true).append('<span class="kata-loading"></span>');
        },

        hideLoading: function($element) {
            $element.prop('disabled', false).find('.kata-loading').remove();
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        KataFormAdmin.init();
    });

    // Frontend shortcode support
    window.KataFormFrontend = {
        init: function() {
            this.initFrontendCharts();
        },

        initFrontendCharts: function() {
            $('.kata-frontend-chart').each(function() {
                var $chart = $(this);
                var formId = $chart.data('form-id');
                var chartType = $chart.data('chart-type') || 'line';
                
                if (typeof Chart !== 'undefined') {
                    KataFormFrontend.loadChart($chart, formId, chartType);
                }
            });
        },

        loadChart: function($container, formId, type) {
            var canvas = $container.find('canvas')[0];
            if (!canvas) return;

            var ctx = canvas.getContext('2d');
            
            $.ajax({
                url: kata_frontend_vars.ajax_url,
                type: 'POST',
                data: {
                    action: 'kata_get_frontend_chart_data',
                    form_id: formId,
                    nonce: kata_frontend_vars.nonce
                },
                success: function(response) {
                    if (response.success) {
                        new Chart(ctx, {
                            type: type,
                            data: response.data,
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    }
                }
            });
        }
    };

    // Initialize frontend when document is ready
    if (typeof kata_frontend_vars !== 'undefined') {
        $(document).ready(function() {
            KataFormFrontend.init();
        });
    }

})(jQuery);
