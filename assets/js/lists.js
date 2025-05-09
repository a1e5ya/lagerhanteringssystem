/**
 * lists.js - JavaScript functionality for the Lists tab
 * Contains filtering, batch operations, and AJAX functionality
 */

// Initialize global selectedItems array
window.selectedItems = [];

$(document).ready(function() {
    // Initialize variables for current state
    let currentFilters = {
        page: 1,
        limit: 15
    };
    
    // Make this function globally accessible
    window.loadProducts = loadProducts;

    // Function to load products with filters
    function loadProducts(filters = {}) {
        // Merge with current filters
        currentFilters = {...currentFilters, ...filters};
        
        // Show loading indicator
        const listsBody = document.querySelector('#lists-body');
        if (!listsBody) {
            return;
        }
        
        listsBody.innerHTML = 
            '<tr><td colspan="10" class="text-center"><div class="spinner-border text-primary" role="status">' +
            '<span class="visually-hidden">Loading...</span></div></td></tr>';
        
        // Perform AJAX request
        $.ajax({
            url: 'admin/list_ajax_handler.php',
            type: 'POST',
            data: {
                action: 'get_filtered_products',
                ...currentFilters
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Update table body
                    $('#lists-body').html(data.html);
                    
                    // Update pagination info
                    $('#current-page').text(data.pagination.currentPage);
                    $('#total-pages').text(data.pagination.totalPages);
                    $('#total-count').text(data.pagination.totalResults);
                    
                    // Enable/disable pagination buttons
                    $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
                    $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
                    
                    // Reset selected items
                    window.selectedItems = [];
                    updateSelectedCount();
                    updateBatchButtons();
                    
                    // Reset select all checkbox
                    $('#select-all').prop('checked', false);
                } else {
                    // Show error
                    $('#lists-body').html('<tr><td colspan="10" class="text-center text-danger">' + (data.message || 'Ett fel inträffade') + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                $('#lists-body').html('<tr><td colspan="10" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>');
            }
        });
    }
    
    // Helper function to reset filter form fields
    function resetFilterFields() {
        $('#list-categories').val('');
        $('#list-genre').val('');
        $('#list-condition').val('');
        $('#list-status').val('all');
        $('#price-min').val('');
        $('#price-max').val('');
        $('#date-min').val('');
        $('#date-max').val('');
        $('#list-search').val('');
    }
    
    // Toggle advanced filter visibility - hide by default
    const filterBody = $('#filter-body');
    const toggleIcon = $('.toggle-icon');
    
    // Hide filter body by default
    filterBody.hide();
    toggleIcon.css('transform', 'rotate(0deg)');
    
    $('#filter-header').on('click', function() {
        // Toggle body display
        const isVisible = filterBody.is(':visible');
        filterBody.slideToggle();
        
        // Rotate icon
        toggleIcon.css('transform', isVisible ? 'rotate(0deg)' : 'rotate(180deg)');
    });
    
    // Add Clear Filter button
    const clearFilterBtn = $('<button class="btn btn-outline-secondary w-100" id="clear-all-filters">Rensa alla filter</button>');
    $('#list-older-than').parent().after($('<div class="col-md-3 mb-2"></div>').append(clearFilterBtn));
    
    // Clear Filter button handler
    $('#clear-all-filters').on('click', function() {
        resetFilterFields();
        loadProducts({
            page: 1,
            category: '',
            genre: '',
            condition: '',
            status: 'all',
            min_price: '',
            max_price: '',
            min_date: '',
            max_date: '',
            search: '',
            no_price: null,
            poor_condition: null,
            shelf: null,
            year_threshold: null
        });
    });
    
    // Apply filter button click
    $('#apply-filter-btn').on('click', function() {
        // Get all filter values
        const filters = {
            category: $('#list-categories').val(),
            genre: $('#list-genre').val(),
            condition: $('#list-condition').val(),
            status: $('#list-status').val(),
            min_price: $('#price-min').val(),
            max_price: $('#price-max').val(),
            min_date: $('#date-min').val(),
            max_date: $('#date-max').val(),
            search: $('#list-search').val(),
            page: 1 // Reset to page 1 for new filter
        };
        
        // Load products with filters
        loadProducts(filters);
    });
    
    // Quick filter buttons
    $('#list-no-price').on('click', function() {
        loadProducts({
            no_price: true,
            page: 1,
            // Clear other filters
            category: '',
            genre: '',
            condition: '',
            status: 'all',
            min_price: '',
            max_price: '',
            min_date: '',
            max_date: '',
            search: ''
        });
        
        resetFilterFields();
    });
    
    $('#list-poor-condition').on('click', function() {
        loadProducts({
            poor_condition: true,
            page: 1,
            // Clear other filters
            category: '',
            genre: '',
            condition: '',
            status: 'all',
            min_price: '',
            max_price: '',
            min_date: '',
            max_date: '',
            search: ''
        });
        
        resetFilterFields();
    });
    
    // Shelf inventory button - handle button and dropdown
    $('#list-shelf-check').on('click', function(e) {
        // Only trigger if click is on the button itself, not the select
        if (e.target === this || $(e.target).is('span')) {
            e.preventDefault();
            const shelfSelector = $('#shelf-selector');
            shelfSelector.focus();
        }
    });
    
    $('#shelf-selector').on('change', function() {
        const shelfName = $(this).val();
        if (shelfName) {
            loadProducts({
                shelf: shelfName,
                page: 1,
                // Clear other filters
                category: '',
                genre: '',
                condition: '',
                status: 'all',
                min_price: '',
                max_price: '',
                min_date: '',
                max_date: '',
                search: ''
            });
            
            resetFilterFields();
        }
    });
    
    // Older than filter - handle button and input
    $('#list-older-than').on('click', function(e) {
        // Only trigger if click is on the button itself, not the input
        if (e.target === this || $(e.target).is('span')) {
            e.preventDefault();
            const yearInput = $('#year-threshold');
            yearInput.focus();
        }
    });
    
    $('#year-threshold').on('change keyup', function(e) {
        // If Enter key is pressed or field loses focus
        if (e.type === 'change' || e.keyCode === 13) {
            const yearThreshold = $(this).val();
            if (yearThreshold) {
                loadProducts({
                    year_threshold: yearThreshold,
                    page: 1,
                    // Clear other filters
                    category: '',
                    genre: '',
                    condition: '',
                    status: 'all',
                    min_price: '',
                    max_price: '',
                    min_date: '',
                    max_date: '',
                    search: ''
                });
                
                resetFilterFields();
            }
        }
    });
    
    // Pagination buttons
    $('#prev-page-btn').on('click', function() {
        if (!$(this).prop('disabled')) {
            loadProducts({ page: currentFilters.page - 1 });
        }
    });
    
    $('#next-page-btn').on('click', function() {
        if (!$(this).prop('disabled')) {
            loadProducts({ page: currentFilters.page + 1 });
        }
    });
    
    // Initialize the page
    updateSelectedItems();
});

// --- BATCH OPERATIONS AND CHECKBOX HANDLING ---

/**
 * Update the selectedItems array from checked boxes
 */
function updateSelectedItems() {
    // Get all checked checkboxes
    const checkedBoxes = document.querySelectorAll('input[name="list-item"]:checked');
    
    // Create a fresh array from the checked boxes
    window.selectedItems = Array.from(checkedBoxes).map(cb => parseInt(cb.value));
    
    // Update UI elements
    updateSelectedCount();
    updateBatchButtons();
    
    return window.selectedItems.length;
}

/**
 * Update the selected count display
 */
function updateSelectedCount() {
    const selectedCountEl = document.getElementById('selected-count');
    if (selectedCountEl) {
        selectedCountEl.textContent = window.selectedItems.length;
    }
}

/**
 * Update batch operation buttons based on selection
 */
function updateBatchButtons() {
    const hasSelection = window.selectedItems && window.selectedItems.length > 0;
    
    // Enable/disable batch action buttons based on selection
    const batchButtons = [
        'batch-update-price',
        'batch-update-status',
        'batch-move-shelf',
        'batch-delete'
    ];
    
    batchButtons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = !hasSelection;
        }
    });
}

/**
 * Perform a batch operation via AJAX
 */
function performBatchAction(action, params = {}) {
    // Make sure we have selections
    if (!window.selectedItems || window.selectedItems.length === 0) {
        alert('Inga produkter valda.');
        return;
    }
    
    // Show a loading indicator
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'loading-overlay';
    loadingDiv.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
    loadingDiv.style.position = 'fixed';
    loadingDiv.style.top = '0';
    loadingDiv.style.left = '0';
    loadingDiv.style.width = '100%';
    loadingDiv.style.height = '100%';
    loadingDiv.style.backgroundColor = 'rgba(0,0,0,0.3)';
    loadingDiv.style.display = 'flex';
    loadingDiv.style.justifyContent = 'center';
    loadingDiv.style.alignItems = 'center';
    loadingDiv.style.zIndex = '9999';
    document.body.appendChild(loadingDiv);
    
    // Send the AJAX request
    $.ajax({
        url: 'admin/list_ajax_handler.php',
        type: 'POST',
        data: {
            action: 'batch_action',
            batch_action: action,
            product_ids: JSON.stringify(window.selectedItems),
            ...params
        },
        dataType: 'json',
        success: function(response) {
            // Remove loading indicator
            document.body.removeChild(loadingDiv);
            
            // Hide modals
            $('.modal').modal('hide');
            
            if (response.success) {
                // Show success message
                showMessage(response.message, 'success');
                
                // Reload products to refresh the list
                if (typeof window.loadProducts === 'function') {
                    window.loadProducts();
                } else {
                    // Fallback to page reload if loadProducts not available
                    window.location.reload();
                }
            } else {
                // Show error message
                showMessage(response.message || 'Ett fel inträffade.', 'danger');
            }
        },
        error: function(xhr, status, error) {
            // Remove loading indicator
            document.body.removeChild(loadingDiv);
            
            // Hide modals
            $('.modal').modal('hide');
            
            // Show error message
            showMessage('Ett fel inträffade: ' + error, 'danger');
        }
    });
}

/**
 * Export data to specified format
 */
function exportData(format) {
    // Create a form for POST submission
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'admin/export.php';
    form.target = '_blank';
    
    // Add format parameter
    const formatInput = document.createElement('input');
    formatInput.type = 'hidden';
    formatInput.name = 'format';
    formatInput.value = format;
    form.appendChild(formatInput);
    
    // Add current filters
    const currentFilters = window.currentFilters || {};
    for (const [key, value] of Object.entries(currentFilters)) {
        if (value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
    }
    
    // Add selected items if any
    if (window.selectedItems && window.selectedItems.length > 0) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_items';
        input.value = JSON.stringify(window.selectedItems);
        form.appendChild(input);
    }
    
    // Submit the form
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

/**
 * Print the current list
 */
function printList() {
    // Create a new window for the printable version
    const printWindow = window.open('', '_blank');
    
    if (!printWindow) {
        alert('Popup blockers might be preventing the print window. Please allow popups for this site.');
        return;
    }
    
    // Get the current table
    const table = document.getElementById('lists-table');
    if (!table) {
        return;
    }
    
    // Create a clone of the table to modify for printing
    const tableClone = table.cloneNode(true);
    
    // Remove checkboxes from the cloned table
    tableClone.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        const cell = checkbox.closest('td');
        if (cell) cell.innerHTML = '';
    });
    
    // Create the printable HTML
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Karis Antikvariat - Produktlista</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { text-align: center; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .print-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
                .print-footer { margin-top: 30px; text-align: center; font-size: 12px; }
                .no-print { margin: 20px 0; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h2>Karis Antikvariat</h2>
                <p>Utskriven: ${new Date().toLocaleDateString()} ${new Date().toLocaleTimeString()}</p>
            </div>
            <h1>Produktlista</h1>
            <div class="no-print">
                <button onclick="window.print()">Skriv ut</button>
                <button onclick="window.close()">Stäng</button>
            </div>
            <table>${tableClone.innerHTML}</table>
            <div class="print-footer">
                <p>Karis Antikvariat - Alla rättigheter förbehållna ${new Date().getFullYear()}</p>
            </div>
            <script>
                // Auto-print when loaded
                window.onload = function() { 
                    // Small delay to ensure everything is rendered
                    setTimeout(() => window.print(), 500); 
                };
            </script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}

// Helper function to show messages
function showMessage(message, type) {
    // Find message container or create it
    let container = $('#message-container');
    
    if (container.length === 0) {
        container = $('<div id="message-container"></div>');
        $('#lists').prepend(container);
    }
    
    // Create alert element
    const alertEl = $(`<div class="alert alert-${type} alert-dismissible fade show">${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>`);
    
    // Add the alert to the container
    container.html(alertEl);
    container.show();
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertEl.alert('close');
    }, 5000);
}

// --- EVENT HANDLERS (outside document.ready for global scope) ---

// Select All checkbox
$(document).on('change', '#select-all', function() {
    // Update all checkboxes
    $('input[name="list-item"]').prop('checked', this.checked);
    
    // Update the selectedItems array
    if (this.checked) {
        // If checked, add all product IDs
        window.selectedItems = Array.from(
            document.querySelectorAll('input[name="list-item"]')
        ).map(cb => parseInt(cb.value));
    } else {
        // If unchecked, empty the array
        window.selectedItems = [];
    }
    
    // Update count and buttons
    updateSelectedCount();
    updateBatchButtons();
});

// Individual checkboxes
$(document).on('change', 'input[name="list-item"]', function() {
    const productId = parseInt(this.value);
    const isChecked = this.checked;
    
    // Update the array based on checkbox state
    if (isChecked) {
        // Add to array if not already present
        if (!window.selectedItems.includes(productId)) {
            window.selectedItems.push(productId);
        }
    } else {
        // Remove from array
        const index = window.selectedItems.indexOf(productId);
        if (index !== -1) {
            window.selectedItems.splice(index, 1);
        }
    }
    
    // Update the "select all" checkbox state
    const allCheckboxes = document.querySelectorAll('input[name="list-item"]');
    const checkedCount = document.querySelectorAll('input[name="list-item"]:checked').length;
    $('#select-all').prop('checked', allCheckboxes.length > 0 && checkedCount === allCheckboxes.length);
    
    // Update count and buttons
    updateSelectedCount();
    updateBatchButtons();
});

// Batch buttons click handlers
// Update Price button
$(document).on('click', '#batch-update-price', function() {
    if (window.selectedItems && window.selectedItems.length > 0) {
        $('#updatePriceModal').modal('show');
    } else {
        alert('Välj minst en produkt först.');
    }
});

// Confirm update price
$(document).on('click', '#confirm-update-price', function() {
    const newPrice = $('#new-price').val();
    if (newPrice && parseFloat(newPrice) > 0) {
        performBatchAction('update_price', { new_price: newPrice });
    } else {
        alert('Vänligen ange ett giltigt pris.');
    }
});

// Update Status button (direct action without modal)
$(document).on('click', '#batch-update-status', function() {
    if (window.selectedItems && window.selectedItems.length > 0) {
        // Immediately change status to Sold (status=2)
        performBatchAction('update_status', { new_status: 2 });
    } else {
        alert('Välj minst en produkt först.');
    }
});

// Move Shelf button
$(document).on('click', '#batch-move-shelf', function() {
    if (window.selectedItems && window.selectedItems.length > 0) {
        $('#moveShelfModal').modal('show');
    } else {
        alert('Välj minst en produkt först.');
    }
});

// Confirm move shelf
$(document).on('click', '#confirm-move-shelf', function() {
    const newShelf = $('#new-shelf').val();
    if (newShelf) {
        performBatchAction('move_shelf', { new_shelf: newShelf });
    } else {
        alert('Vänligen välj en hylla.');
    }
});

// Delete button
$(document).on('click', '#batch-delete', function() {
    if (window.selectedItems && window.selectedItems.length > 0) {
        // Update the counter in the modal
        $('#delete-count').text(window.selectedItems.length);
        $('#deleteConfirmModal').modal('show');
    } else {
        alert('Välj minst en produkt först.');
    }
});

// Confirm delete
$(document).on('click', '#confirm-delete', function() {
    performBatchAction('delete');
});

// Export and print buttons
$(document).on('click', '#export-csv-btn', function() {
    exportData('csv');
});

$(document).on('click', '#print-list-btn', function() {
    printList();
});