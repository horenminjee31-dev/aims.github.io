/**
 * School Management System - Main JavaScript
 */

// Document Ready
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // DataTable initialization
    if ($('.datatable').length) {
        $('.datatable').DataTable({
            responsive: true,
            pageLength: 25,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    }
    
    // Confirm delete
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        if (confirm('Are you sure you want to delete this record?')) {
            window.location.href = url;
        }
    });
    
    // Dynamic row addition for forms
    $('.add-row').on('click', function() {
        var template = $(this).data('template');
        var container = $(this).data('container');
        var clone = $(template).clone().removeClass('d-none').removeAttr('id');
        $(container).append(clone);
    });
    
    // Remove dynamic row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.dynamic-row').remove();
    });
    
    // Calculate total marks
    $('.calc-total').on('input', function() {
        var row = $(this).closest('tr');
        var written = parseFloat(row.find('.written-mark').val()) || 0;
        var mcq = parseFloat(row.find('.mcq-mark').val()) || 0;
        var practical = parseFloat(row.find('.practical-mark').val()) || 0;
        var total = written + mcq + practical;
        
        row.find('.total-mark').val(total.toFixed(2));
    });
    
    // Print functionality
    $('.btn-print').on('click', function() {
        window.print();
    });
    
    // SMS character counter
    $('#sms-message').on('input', function() {
        var length = $(this).val().length;
        var sms = Math.ceil(length / 160);
        $('#sms-count').text(length + ' characters / ' + sms + ' SMS');
    });
    
    // Select all checkbox
    $('#select-all').on('change', function() {
        $('.select-item').prop('checked', $(this).prop('checked'));
    });
    
    // Class change - load sections and subjects
    $('#class_id').on('change', function() {
        var classId = $(this).val();
        if (classId) {
            loadSections(classId);
            loadSubjects(classId);
        }
    });
    
    // Date picker initialization
    if ($('.datepicker').length) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }
    
    // Image preview
    $('.image-upload').on('change', function() {
        var input = this;
        var preview = $(this).data('preview');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $(preview).attr('src', e.target.result).removeClass('d-none');
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // AJAX form submission
    $('.ajax-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method') || 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),
            beforeSend: function() {
                form.find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    if (response.redirect) {
                        setTimeout(function() {
                            window.location.href = response.redirect;
                        }, 1500);
                    }
                } else {
                    showAlert('danger', response.message || 'An error occurred');
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred while processing your request');
            },
            complete: function() {
                form.find('button[type="submit"]').prop('disabled', false).html('Submit');
            }
        });
    });
    
    // Show attendance status colors
    $('.attendance-status').on('change', function() {
        var status = $(this).val();
        var row = $(this).closest('tr');
        
        row.removeClass('table-success table-danger table-warning table-info');
        
        switch(status) {
            case 'present':
                row.addClass('table-success');
                break;
            case 'absent':
                row.addClass('table-danger');
                break;
            case 'late':
                row.addClass('table-warning');
                break;
            case 'leave':
                row.addClass('table-info');
                break;
        }
    });
    
    // Quick search in tables
    $('#quick-search').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.search-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Fee calculation
    $('.fee-amount, .fee-discount, .fee-fine').on('input', function() {
        var row = $(this).closest('.fee-row');
        var amount = parseFloat(row.find('.fee-amount').val()) || 0;
        var discount = parseFloat(row.find('.fee-discount').val()) || 0;
        var fine = parseFloat(row.find('.fee-fine').val()) || 0;
        var total = amount - discount + fine;
        
        row.find('.fee-total').val(total.toFixed(2));
        calculateGrandTotal();
    });
    
    // Export table to Excel
    $('.export-excel').on('click', function() {
        var table = $(this).data('table');
        exportTableToExcel(table, 'export.xlsx');
    });
    
    // Export table to PDF
    $('.export-pdf').on('click', function() {
        var element = $(this).data('element');
        exportToPDF(element);
    });
});

// Helper Functions

function loadSections(classId) {
    $.ajax({
        url: '/ajax/get_sections.php',
        type: 'GET',
        data: { class_id: classId },
        success: function(response) {
            $('#section_id').html(response);
        }
    });
}

function loadSubjects(classId) {
    $.ajax({
        url: '/ajax/get_subjects.php',
        type: 'GET',
        data: { class_id: classId },
        success: function(response) {
            $('#subject_id').html(response);
        }
    });
}

function showAlert(type, message) {
    var alert = '<div class="alert alert-' + type + ' alert-dismissible fade show">' +
                message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                '</div>';
    
    $('#alert-container').html(alert);
    
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
}

function calculateGrandTotal() {
    var total = 0;
    $('.fee-total').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#grand-total').text(total.toFixed(2));
}

function exportTableToExcel(tableID, filename = '') {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    filename = filename ? filename + '.xls' : 'excel_data.xls';
    
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    
    if (navigator.msSaveOrOpenBlob) {
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}

function exportToPDF(elementId) {
    window.print();
}

// Confirm action with custom message
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,');
}

// Validate Bangladeshi mobile number
function validateMobile(number) {
    var regex = /^01[3-9]\d{8}$/;
    return regex.test(number);
}

// Validate email
function validateEmail(email) {
    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// Generate random password
function generatePassword(length = 8) {
    var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    var retVal = "";
    for (var i = 0, n = charset.length; i < length; ++i) {
        retVal += charset.charAt(Math.floor(Math.random() * n));
    }
    return retVal;
}

// Copy to clipboard
function copyToClipboard(text) {
    var tempInput = document.createElement("input");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    showAlert('success', 'Copied to clipboard!');
}

// Live clock
function updateClock() {
    var now = new Date();
    var timeString = now.toLocaleTimeString('en-US', { hour12: true });
    var dateString = now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    
    if ($('#live-clock').length) {
        $('#live-clock').text(timeString);
    }
    if ($('#live-date').length) {
        $('#live-date').text(dateString);
    }
}

setInterval(updateClock, 1000);
updateClock();
