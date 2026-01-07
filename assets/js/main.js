/**
 * Stock Management System - Main JavaScript
 * Client-side functionality and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-hide flash messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Confirm delete actions
    const deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
    
    // Highlight low stock rows
    const tableRows = document.querySelectorAll('.row-warning');
    tableRows.forEach(row => {
        row.style.backgroundColor = 'rgba(245, 158, 11, 0.05)';
    });
    
    // Search form auto-submit on Enter
    const searchInputs = document.querySelectorAll('input[name="search"]');
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    });
    
    // Number input validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
            
            if (this.hasAttribute('max')) {
                const max = parseFloat(this.getAttribute('max'));
                if (parseFloat(this.value) > max) {
                    this.value = max;
                }
            }
        });
    });
    
    // SKU auto-uppercase
    const skuInput = document.getElementById('sku');
    if (skuInput) {
        skuInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Responsive table scroll indicator
    const tableContainers = document.querySelectorAll('.table-responsive');
    tableContainers.forEach(container => {
        const table = container.querySelector('table');
        if (table && table.offsetWidth > container.offsetWidth) {
            container.style.boxShadow = 'inset -10px 0 10px -10px rgba(0,0,0,0.1)';
        }
    });
    
    // Print functionality (if needed)
    window.printPage = function() {
        window.print();
    };
    
    // Export confirmation
    const exportLinks = document.querySelectorAll('a[href*="export"]');
    exportLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Show loading indicator
            const originalText = this.textContent;
            this.textContent = 'Exporting...';
            this.style.pointerEvents = 'none';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.pointerEvents = '';
            }, 2000);
        });
    });
});

// Utility functions
function formatCurrency(amount) {
    return '$' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// AJAX helper (for future enhancements)
function ajaxRequest(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            callback(null, JSON.parse(xhr.responseText));
        } else {
            callback(new Error('Request failed'), null);
        }
    };
    
    xhr.onerror = function() {
        callback(new Error('Network error'), null);
    };
    
    xhr.send(JSON.stringify(data));
}
