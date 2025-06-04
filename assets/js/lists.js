/**
 * FIXED lists.js - Consolidated and Secure
 * - CSRF tokens automatically handled by admin_header.php
 * - Removed duplicate event handlers
 * - Consolidated batch operations
 * - Advanced filters collapsed by default
 * - Improved error handling and security
 */

// Global variables
window.selectedItems = [];
window.currentFilters = {};

$(document).ready(function() {
    initializeListsPage();
});

$(document).on('DOMContentLoaded', function() {
    if ($('#lists').length > 0) {
        initializeListsPage();
    }
});

$(document).on('shown.bs.tab', 'a[data-tab="lists"]', function() {
    setTimeout(() => {
        initializeListsPage();
    }, 100);
});

function initializeListsPage() {
    if ($('#list-status').length === 0) {
        setTimeout(initializeListsPage, 200);
        return;
    }
    
    // Set default status
    $('#list-status').val('');
    
    // Collapse advanced filters by default
    $('#filter-body').hide();
    $('.toggle-icon').addClass('rotated');
    
    // Initialize all event handlers
    attachListsEventHandlers();
    attachBatchOperationHandlers();
    attachCheckboxHandlers();
    
    // Load initial data
    loadListsProducts();
    
    // Page size selector
    $('#page-size-selector').off('change').on('change', function() {
        const pageSize = $(this).val();
        loadListsProducts('', '', 1, pageSize);
    });
}

function loadListsProducts(searchTerm = '', category = '', page = 1, limit = 20) {
    $('#inventory-body').html(`
        <tr>
            <td colspan="10" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    const genre = $('#list-genre').val() || '';
    const condition = $('#list-condition').val() || '';
    const status = $('#list-status').val() || '';
    const shelf = $('#shelf-filter').val() || $('#shelf-selector').val() || '';
    const priceMin = $('#price-min').val() || '';
    const priceMax = $('#price-max').val() || '';
    const dateMin = $('#date-min').val() || '';
    const dateMax = $('#date-max').val() || '';
    const yearThreshold = $('#year-threshold').val() || '';
    
    window.currentFilters = {
        search: searchTerm,
        category: category,
        genre: genre,
        condition: condition,
        status: status,
        shelf: shelf,
        price_min: priceMin,
        price_max: priceMax,
        date_min: dateMin,
        date_max: dateMax,
        year_threshold: yearThreshold,
        page: page,
        limit: limit
    };
    
    let requestData = {
        search: searchTerm,
        category: category !== '' ? category : '',
        genre: genre,
        condition: condition, 
        status: status === '' ? '' : (status === 'all' ? 'all' : status),
        shelf: shelf,
        price_min: priceMin,
        price_max: priceMax,
        date_min: dateMin,
        date_max: dateMax,
        page: page,
        limit: limit,
        show_all_statuses: status === 'all',
        view_type: 'lists'
    };
    
    if (yearThreshold) {
        requestData.year_threshold = yearThreshold;
    }
    
    // CSRF token will be automatically added by admin_header.php setup
    $.ajax({
        url: BASE_URL + '/admin/get_products.php',
        type: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                if (data.items && data.items.length > 0) {
                    renderListsProducts(data.items);
                } else {
                    $('#inventory-body').html('<tr><td colspan="10" class="text-center">Inga produkter hittades</td></tr>');
                    updateSelectedCount();
                    updateBatchButtons();
                    $('#select-all').prop('checked', false);
                }
                
                if (data.pagination) {
                    updateListsPagination(data.pagination, searchTerm, category, limit);
                }
            } else {
                showMessage(data.message || 'Ett fel inträffade vid hämtning av data', 'danger');
                $('#inventory-body').html(`<tr><td colspan="10" class="text-center text-danger">${data.message || 'Ett fel inträffade'}</td></tr>`);
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Ett fel inträffade vid hämtning av data';
            
            // Handle specific error cases
            if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet att komma åt denna data';
            } else if (xhr.status === 419) {
                errorMessage = 'Säkerhetstoken har gått ut';
                // Refresh page to get new token
                setTimeout(() => window.location.reload(), 2000);
            } else if (xhr.status >= 500) {
                errorMessage = 'Serverfel - försök igen senare';
            }
            
            showMessage(errorMessage, 'danger');
            $('#inventory-body').html(`<tr><td colspan="10" class="text-center text-danger">${errorMessage}</td></tr>`);
        }
    });
}

function loadListsProductsWithSpecialFilter(filterType, value = null) {
    $('#inventory-body').html(`
        <tr>
            <td colspan="10" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            </td>
        </tr>
    `);
    
    let requestData = {
        page: 1,
        limit: $('#page-size-selector').val() || 20,
        view_type: 'lists'
    };
    
    switch(filterType) {
        case 'special_price':
            requestData.special_price = 1;
            break;
        case 'rare':
            requestData.rare = 1;
            break;
        case 'recommended':
            requestData.recommended = 1;
            break;
        case 'no_price':
            requestData.no_price = true;
            break;
        case 'poor_condition':
            requestData.poor_condition = true;
            break;
        case 'year_threshold':
            requestData.year_threshold = value;
            break;
    }
    
    window.currentFilters = requestData;
    
    $.ajax({
        url: BASE_URL + '/admin/get_products.php',
        type: 'GET',
        data: requestData,
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                if (data.items && data.items.length > 0) {
                    renderListsProducts(data.items);
                } else {
                    $('#inventory-body').html('<tr><td colspan="10" class="text-center">Inga produkter hittades</td></tr>');
                }
                
                if (data.pagination) {
                    updateListsPagination(data.pagination, '', '', $('#page-size-selector').val() || 20);
                }
                
                window.selectedItems = [];
                updateSelectedCount();
                updateBatchButtons();
                $('#select-all').prop('checked', false);
            } else {
                showMessage(data.message || 'Ett fel inträffade', 'danger');
                $('#inventory-body').html(`<tr><td colspan="10" class="text-center text-danger">${data.message || 'Ett fel inträffade'}</td></tr>`);
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Ett fel inträffade vid hämtning av data';
            if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet att komma åt denna data';
            } else if (xhr.status >= 500) {
                errorMessage = 'Serverfel - försök igen senare';
            }
            
            showMessage(errorMessage, 'danger');
            $('#inventory-body').html(`<tr><td colspan="10" class="text-center text-danger">${errorMessage}</td></tr>`);
        }
    });
}

function renderListsProducts(products) {
    let html = '';
    
    products.forEach(item => {
        const statusClass = parseInt(item.status) === 1 ? 'text-success' : 'text-danger';
        const formattedPrice = item.formatted_price || (item.price ? `${parseFloat(item.price).toFixed(2).replace('.', ',')} €` : 'Inget pris');
        
        let markings = '';
        if (item.special_price == 1) {
            markings += '<span class="badge bg-danger me-1">Rea</span>';
        }
        if (item.rare == 1) {
            markings += '<span class="badge bg-warning text-dark me-1">Sällsynt</span>';
        }
        if (item.recommended == 1) {
            markings += '<span class="badge bg-primary me-1">Rekommenderas</span>';
        }
        
        const productId = parseInt(item.prod_id);
        const isChecked = window.selectedItems.includes(productId);
        
        html += `
        <tr>
            <td><input type="checkbox" name="list-item" value="${item.prod_id}" ${isChecked ? 'checked' : ''}></td>
            <td>${escapeHtml(item.title || '')}</td>
            <td>${escapeHtml(item.author_name || '')}</td>
            <td>${escapeHtml(item.category_name || '')}</td>
            <td>${escapeHtml(item.shelf_name || '')}</td>
            <td>${escapeHtml(item.condition_name || '')}</td>
            <td>${formattedPrice}</td>
            <td class="${statusClass}">${escapeHtml(item.status_name || '')}</td>
            <td>${markings}</td>
            <td>${escapeHtml(item.formatted_date || item.date_added || '')}</td>
        </tr>`;
    });
    
    $('#inventory-body').html(html);
    updateSelectAllCheckbox();
    updateSelectedCount();
    updateBatchButtons();
}

function updateListsPagination(pagination, searchTerm, category, limit) {
    $('#showing-start').text(pagination.firstRecord || 0);
    $('#showing-end').text(pagination.lastRecord || 0);
    $('#total-items').text(pagination.totalItems || 0);
    $('#page-size-selector').val(pagination.itemsPerPage || limit);
    
    if (pagination.totalPages > 0) {
        let html = '';
        
        html += `
            <li class="page-item ${pagination.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;
        
        const startPage = Math.max(1, pagination.currentPage - 2);
        const endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === pagination.currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        html += `
            <li class="page-item ${pagination.currentPage >= pagination.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;
        
        $('#pagination-links').html(html);
        
        $('#pagination-links .page-link').off('click').on('click', function(e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'), 10);
            if (!isNaN(page)) {
                const searchTerm = $('#search-term').val();
                const category = $('#category-filter').val();
                const limit = $('#page-size-selector').val();
                loadListsProducts(searchTerm, category, page, limit);
            }
        });
    } else {
        $('#pagination-links').html('');
    }
}

function attachListsEventHandlers() {
    // Remove existing handlers to prevent duplicates
    $('#apply-filters').off('click');
    $('#lists-search-form').off('submit');
    $('#clear-all-filters').off('click');
    $('#list-special-price, #list-rare, #list-recommended, #list-no-price, #list-poor-condition').off('click');
    $('#shelf-selector').off('change');
    $('#year-threshold').off('change keyup');
    $('#filter-header').off('click');
    
    // Remove automatic filter changes - only apply on button click
    $('#category-filter, #list-genre, #list-condition, #list-status, #shelf-filter, #price-min, #price-max, #date-min, #date-max').off('change');
    $('#search-term').off('keyup');
    
    // Apply filters button - ONLY way to apply advanced filters
    $('#apply-filters').on('click', function(e) {
        e.preventDefault();
        const searchTerm = $('#search-term').val();
        const category = $('#category-filter').val();
        loadListsProducts(searchTerm, category, 1, $('#page-size-selector').val());
    });
    
    // Search form submit
    $('#lists-search-form').on('submit', function(e) {
        e.preventDefault();
        const searchTerm = $('#search-term').val();
        const category = $('#category-filter').val();
        loadListsProducts(searchTerm, category);
    });
    
    // Clear all filters
    $('#clear-all-filters').on('click', function() {
        $('#lists-search-form')[0].reset();
        $('#shelf-selector').val('');
        $('#year-threshold').val('');
        $('#price-min').val('');
        $('#price-max').val('');
        $('#date-min').val('');
        $('#date-max').val('');
        $('#list-status').val('');
        
        window.selectedItems = [];
        $('input[name="list-item"]').prop('checked', false);
        $('#select-all').prop('checked', false);
        updateSelectedCount();
        updateBatchButtons();
        
        loadListsProducts('', '', 1, $('#page-size-selector').val());
        showMessage('Alla filter och val har rensats', 'success');
    });
    
    // Quick filter buttons
    $('#list-special-price').on('click', function() {
        clearFormFilters();
        loadListsProductsWithSpecialFilter('special_price', 1);
    });
    
    $('#list-rare').on('click', function() {
        clearFormFilters();
        loadListsProductsWithSpecialFilter('rare', 1);
    });
    
    $('#list-recommended').on('click', function() {
        clearFormFilters();
        loadListsProductsWithSpecialFilter('recommended', 1);
    });
    
    $('#list-no-price').on('click', function() {
        clearFormFilters();
        loadListsProductsWithSpecialFilter('no_price', true);
    });
    
    $('#list-poor-condition').on('click', function() {
        clearFormFilters();
        loadListsProductsWithSpecialFilter('poor_condition', true);
    });
    
    // Shelf selector
    $('#shelf-selector').on('change', function() {
        const shelfName = $(this).val();
        if (shelfName) {
            clearFormFilters();
            $('#shelf-filter').val(shelfName);
            loadListsProducts('', '', 1, $('#page-size-selector').val());
        }
    });
    
    // Year threshold
    $('#year-threshold').on('change keyup', function(e) {
        if (e.type === 'change' || e.keyCode === 13) {
            const yearThreshold = $(this).val();
            if (yearThreshold) {
                clearFormFilters();
                $(this).val(yearThreshold);
                loadListsProductsWithSpecialFilter('year_threshold', yearThreshold);
            }
        }
    });
    
    // Toggle filter section
    $('#filter-header').on('click', function() {
        const filterBody = $('#filter-body');
        const toggleIcon = $('.toggle-icon');
        
        if (filterBody.is(':visible')) {
            filterBody.slideUp();
            toggleIcon.addClass('rotated');
        } else {
            filterBody.slideDown();
            toggleIcon.removeClass('rotated');
        }
    });
}

// CONSOLIDATED CHECKBOX HANDLING
function attachCheckboxHandlers() {
    // Remove existing handlers
    $(document).off('change', '#select-all');
    $(document).off('change', 'input[name="list-item"]');
    
    // Select all checkbox
    $(document).on('change', '#select-all', function() {
        const isChecked = this.checked;
        
        if (isChecked) {
            // Select all items across all pages that match current filters
            $.ajax({
                url: BASE_URL + '/admin/get_products.php',
                type: 'GET',
                data: {
                    ...window.currentFilters,
                    page: 1,
                    limit: 10000,
                    view_type: 'lists'
                },
                dataType: 'json',
                success: function(data) {
                    if (data.success && data.items) {
                        window.selectedItems = data.items.map(item => parseInt(item.prod_id));
                        $('input[name="list-item"]').prop('checked', true);
                        updateSelectedCount();
                        updateBatchButtons();
                        showMessage(`${window.selectedItems.length} produkter valda (alla som matchar filtren)`, 'info');
                    }
                },
                error: function(xhr) {
                    showMessage('Kunde inte hämta alla produkter för markering', 'warning');
                    // Fallback: select only visible items
                    window.selectedItems = [];
                    $('input[name="list-item"]').each(function() {
                        window.selectedItems.push(parseInt(this.value));
                        this.checked = true;
                    });
                    updateSelectedCount();
                    updateBatchButtons();
                }
            });
        } else {
            window.selectedItems = [];
            $('input[name="list-item"]').prop('checked', false);
            updateSelectedCount();
            updateBatchButtons();
        }
    });
    
    // Individual checkboxes
    $(document).on('change', 'input[name="list-item"]', function() {
        const productId = parseInt(this.value);
        const isChecked = this.checked;
        
        if (isChecked) {
            if (!window.selectedItems.includes(productId)) {
                window.selectedItems.push(productId);
            }
        } else {
            const index = window.selectedItems.indexOf(productId);
            if (index !== -1) {
                window.selectedItems.splice(index, 1);
            }
        }
        
        updateSelectAllCheckbox();
        updateSelectedCount();
        updateBatchButtons();
    });
}

// CONSOLIDATED BATCH OPERATIONS
function attachBatchOperationHandlers() {
    // Remove existing handlers
    const batchButtons = [
        '#batch-update-price', '#batch-update-status', '#batch-move-shelf',
        '#batch-toggle-sale', '#batch-toggle-rare', '#batch-toggle-recommended', 
        '#batch-delete', '#export-csv-btn', '#print-list-btn'
    ];
    
    batchButtons.forEach(selector => {
        $(document).off('click', selector);
    });
    
    // Modal confirmation buttons
    const confirmButtons = [
        '#confirm-update-price', '#confirm-update-status', '#confirm-move-shelf',
        '#confirm-toggle-special-price', '#confirm-toggle-rare', '#confirm-toggle-recommended',
        '#confirm-delete'
    ];
    
    confirmButtons.forEach(selector => {
        $(document).off('click', selector);
    });
    
    // Batch operation buttons
    $(document).on('click', '#batch-update-price', function() {
        if (hasValidSelection()) {
            $('#updatePriceModal').modal('show');
            setTimeout(() => $('#new-price').focus(), 500);
        }
    });
    
    $(document).on('click', '#batch-update-status', function() {
        if (hasValidSelection()) {
            $('#updateStatusModal').modal('show');
        }
    });
    
    $(document).on('click', '#batch-move-shelf', function() {
        if (hasValidSelection()) {
            $('#moveShelfModal').modal('show');
        }
    });
    
    $(document).on('click', '#batch-toggle-sale', function() {
        if (hasValidSelection()) {
            $('#special-price-count').text(getSelectionCount());
            $('#toggleSpecialPriceModal').modal('show');
        }
    });
    
    $(document).on('click', '#batch-toggle-rare', function() {
        if (hasValidSelection()) {
            $('#rare-count').text(getSelectionCount());
            $('#toggleRareModal').modal('show');
        }
    });
    
    $(document).on('click', '#batch-toggle-recommended', function() {
        if (hasValidSelection()) {
            $('#recommended-count').text(getSelectionCount());
            $('#toggleRecommendedModal').modal('show');
        }
    });
    
    $(document).on('click', '#batch-delete', function() {
        if (hasValidSelection()) {
            $('#delete-count').text(getSelectionCount());
            $('#deleteConfirmModal').modal('show');
        }
    });
    
    $(document).on('click', '#export-csv-btn', function() {
        exportData('csv');
    });
    
    $(document).on('click', '#print-list-btn', function() {
        printList();
    });
    
    // Modal confirmation handlers
    $(document).on('click', '#confirm-update-price', function() {
        const newPrice = $('#new-price').val().trim();
        if (newPrice && parseFloat(newPrice) > 0) {
            performBatchAction('update_price', { new_price: newPrice });
        } else {
            showMessage('Vänligen ange ett giltigt pris större än 0', 'warning');
        }
    });
    
    $(document).on('click', '#confirm-update-status', function() {
        const newStatus = $('#new-status').val();
        if (newStatus) {
            performBatchAction('update_status', { new_status: newStatus });
        } else {
            showMessage('Vänligen välj en status', 'warning');
        }
    });
    
    $(document).on('click', '#confirm-move-shelf', function() {
        const newShelf = $('#new-shelf').val();
        if (newShelf) {
            performBatchAction('move_shelf', { new_shelf: newShelf });
        } else {
            showMessage('Vänligen välj en hylla', 'warning');
        }
    });
    
    $(document).on('click', '#confirm-toggle-special-price', function() {
        const action = $('#special-price-action').val();
        performBatchAction('set_special_price', { special_price_value: action });
    });
    
    $(document).on('click', '#confirm-toggle-rare', function() {
        const action = $('#rare-action').val();
        performBatchAction('set_rare', { rare_value: action });
    });
    
    $(document).on('click', '#confirm-toggle-recommended', function() {
        const action = $('#recommended-action').val();
        performBatchAction('set_recommended', { recommended_value: action });
    });
    
    $(document).on('click', '#confirm-delete', function() {
        performBatchAction('delete');
    });
}

// SECURITY-ENHANCED BATCH OPERATIONS
function performBatchAction(action, params = {}) {
    if (!hasValidSelection()) {
        showMessage('Inga produkter valda.', 'warning');
        return;
    }
    
    // Show loading overlay
    const loadingDiv = $('<div class="loading-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;z-index:9999;"><div class="spinner-border text-light" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div></div>');
    $('body').append(loadingDiv);
    
    // Store current state
    const selectedBeforeOperation = [...window.selectedItems];
    
    let requestData = {
        action: 'batch_action',
        batch_action: action,
        product_ids: JSON.stringify(window.selectedItems),
        ...params
    };
    
    // Enhanced security: Add timestamp and action validation
    requestData.timestamp = Date.now();
    requestData.client_validation = btoa(action + '_' + window.selectedItems.length);
    
    // CSRF token will be automatically added by admin_header.php
    $.ajax({
        url: BASE_URL + '/admin/list_ajax_handler.php',
        type: 'POST',
        data: requestData,
        dataType: 'json',
        timeout: 30000, // 30 second timeout
        success: function(response) {
            loadingDiv.remove();
            $('.modal').modal('hide');
            
            if (response.success) {
                showMessage(response.message, 'success');
                
                // Handle selection state after operation
                if (action === 'delete') {
                    window.selectedItems = [];
                } else {
                    window.selectedItems = selectedBeforeOperation;
                }
                
                // Reload with current filters (don't clear them)
                const searchTerm = $('#search-term').val();
                const category = $('#category-filter').val(); 
                const currentPage = parseInt($('.page-item.active .page-link').text()) || 1;
                const limit = $('#page-size-selector').val();
                
                loadListsProducts(searchTerm, category, currentPage, limit);
            } else {
                showMessage(response.message || 'Ett fel inträffade vid batch-operationen.', 'danger');
            }
        },
        error: function(xhr, status, error) {
            loadingDiv.remove();
            $('.modal').modal('hide');
            
            let errorMessage = 'Ett fel inträffade';
            
            if (xhr.status === 403) {
                errorMessage = 'Du har inte behörighet att utföra denna åtgärd';
            } else if (xhr.status === 419) {
                errorMessage = 'Säkerhetstoken har gått ut. Sidan kommer att laddas om.';
                setTimeout(() => window.location.reload(), 2000);
            } else if (xhr.status === 422) {
                errorMessage = 'Ogiltiga data skickade till servern';
            } else if (xhr.status >= 500) {
                errorMessage = 'Serverfel - försök igen senare';
            } else if (status === 'timeout') {
                errorMessage = 'Operationen tog för lång tid - försök igen';
            }
            
            showMessage(errorMessage, 'danger');
        }
    });
}

// UTILITY FUNCTIONS
function clearFormFilters() {
    $('#search-term').val('');
    $('#category-filter').val('');
    $('#list-genre').val('');
    $('#list-condition').val('');
    $('#list-status').val('');
    $('#shelf-filter').val('');
    $('#price-min').val('');
    $('#price-max').val('');
    $('#date-min').val('');
    $('#date-max').val('');
}

function updateSelectAllCheckbox() {
    const allCheckboxes = document.querySelectorAll('input[name="list-item"]');
    const checkedCount = document.querySelectorAll('input[name="list-item"]:checked').length;
    
    if (allCheckboxes.length === 0) {
        $('#select-all').prop('checked', false);
        $('#select-all').prop('indeterminate', false);
    } else if (checkedCount === allCheckboxes.length) {
        $('#select-all').prop('checked', true);
        $('#select-all').prop('indeterminate', false);
    } else if (checkedCount > 0) {
        $('#select-all').prop('checked', false);
        $('#select-all').prop('indeterminate', true);
    } else {
        $('#select-all').prop('checked', false);
        $('#select-all').prop('indeterminate', false);
    }
}

function updateSelectedCount() {
    $('#selected-count').text(window.selectedItems.length);
}

function updateBatchButtons() {
    const hasSelection = window.selectedItems && window.selectedItems.length > 0;
    const batchButtons = [
        'batch-update-price', 'batch-update-status', 'batch-move-shelf',
        'batch-toggle-sale', 'batch-toggle-rare', 'batch-toggle-recommended', 'batch-delete'
    ];
    
    batchButtons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (btn) {
            btn.disabled = !hasSelection;
        }
    });
}

function hasValidSelection() {
    const hasSelection = window.selectedItems && window.selectedItems.length > 0;
    if (!hasSelection) {
        showMessage('Inga produkter valda. Välj produkter genom att markera checkboxarna.', 'warning');
    }
    return hasSelection;
}

function getSelectionCount() {
    return window.selectedItems.length;
}

// EXPORT AND PRINT FUNCTIONS
function exportData(format) {
    const form = $('<form method="GET" target="_blank"></form>');
    form.attr('action', BASE_URL + '/admin/export.php');
    
    form.append(`<input type="hidden" name="format" value="${escapeHtml(format)}">`);
    
    // Add all current filter values
    const filterFields = [
        'search-term', 'category-filter', 'list-genre', 'list-condition', 
        'list-status', 'shelf-filter', 'price-min', 'price-max', 'date-min', 'date-max'
    ];
    
    const filterMapping = {
        'search-term': 'search',
        'category-filter': 'category',
        'list-genre': 'genre',
        'list-condition': 'condition',
        'list-status': 'status',
        'shelf-filter': 'shelf',
        'price-min': 'price_min',
        'price-max': 'price_max',
        'date-min': 'date_min',
        'date-max': 'date_max'
    };
    
    filterFields.forEach(fieldId => {
        const value = $(`#${fieldId}`).val();
        if (value) {
            const paramName = filterMapping[fieldId] || fieldId;
            form.append(`<input type="hidden" name="${paramName}" value="${escapeHtml(value)}">`);
        }
    });
    
    // Add selected items if any
    if (window.selectedItems.length > 0) {
        form.append(`<input type="hidden" name="selected_items" value="${escapeHtml(JSON.stringify(window.selectedItems))}">`);
    }
    
    $('body').append(form);
    form.submit();
    form.remove();
}

function printList() {
    if (window.selectedItems.length > 0) {
        // Print ONLY selected items
        $.ajax({
            url: BASE_URL + '/admin/get_products.php',
            type: 'GET',
            data: {
                // Use a special parameter to get only selected items
                product_ids: JSON.stringify(window.selectedItems),
                page: 1,
                limit: 10000,
                view_type: 'lists'
            },
            dataType: 'json',
            success: function(data) {
                if (data.success && data.items) {
                    // Filter the returned items to only include selected ones
                    const selectedProductIds = window.selectedItems.map(id => parseInt(id));
                    const filteredItems = data.items.filter(item => 
                        selectedProductIds.includes(parseInt(item.prod_id))
                    );
                    
                    if (filteredItems.length > 0) {
                        createPrintWindow(filteredItems);
                    } else {
                        showMessage('Kunde inte hitta de valda produkterna för utskrift.', 'warning');
                    }
                } else {
                    showMessage('Kunde inte hämta valda produkter för utskrift.', 'warning');
                }
            },
            error: function() {
                showMessage('Ett fel inträffade vid hämtning av produktdata.', 'danger');
            }
        });
    } else {
        // Print all visible products (current page/filter results)
        const visibleProducts = [];
        $('#inventory-body tr').each(function() {
            const cells = $(this).find('td');
            if (cells.length >= 9) { // Make sure it's a data row
                visibleProducts.push({
                    title: cells.eq(1).text(),
                    author_name: cells.eq(2).text(),
                    category_name: cells.eq(3).text(),
                    shelf_name: cells.eq(4).text(),
                    condition_name: cells.eq(5).text(),
                    formatted_price: cells.eq(6).text(),
                    status_name: cells.eq(7).text(),
                    markings: cells.eq(8).text(),
                    formatted_date: cells.eq(9).text()
                });
            }
        });
        
        if (visibleProducts.length > 0) {
            createPrintWindow(visibleProducts);
        } else {
            showMessage('Ingen data att skriva ut.', 'warning');
        }
    }
}

function createPrintWindow(products) {
    let tableHTML = `
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Titel</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Författare</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Kategori</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Hylla</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Skick</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px; width: 80px;">Pris</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Status</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Märkning</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">Tillagd datum</th>
                    <th style="border: 1px solid #ddd; padding: 4px; font-size: 10px; width: 30px;"></th>
                </tr>
            </thead>
            <tbody>
    `;
    
    products.forEach((product, index) => {
        const statusClass = parseInt(product.status) === 1 ? 'color: green;' : 'color: red;';
        const formattedPrice = product.price ? product.formatted_price : 'Inget pris';
        
        let markings = '';
        if (product.special_price == 1) markings += 'Rea ';
        if (product.rare == 1) markings += 'Sällsynt ';
        if (product.recommended == 1) markings += 'Rekommenderas ';
        
        const rowStyle = index % 2 === 0 ? 'background-color: #f9f9f9;' : '';
        
        tableHTML += `
            <tr style="${rowStyle}">
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(product.title || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(product.author_name || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(product.category_name || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(product.shelf_name || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(product.condition_name || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(formattedPrice)}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px; ${statusClass}">${escapeHtml(product.status_name || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(markings)}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px;">${escapeHtml(product.formatted_date || '')}</td>
                <td style="border: 1px solid #ddd; padding: 4px; font-size: 10px; width: 80px;"></td>
            </tr>
        `;
    });
    
    tableHTML += '</tbody></table>';
    
    printTableContent(tableHTML);
}

function printTableContent(tableHTML) {
    const printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Karis Antikvariat - Produktlista</title>
            <meta charset="utf-8">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                html, body {
                    height: 100%;
                    width: 100%;
                }
                body { 
                    font-family: Arial, sans-serif; 
                    font-size: 11px;
                    padding: 15mm;
                }
                h1 { 
                    text-align: center; 
                    margin-bottom: 20px; 
                    font-size: 18px;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 10px; 
                }
                th, td { 
                    border: 1px solid #ddd; 
                    padding: 4px; 
                    text-align: left; 
                    font-size: 10px; 
                    word-wrap: break-word;
                }
                th { 
                    background-color: #f2f2f2; 
                    font-weight: bold; 
                }
                tr:nth-child(even) { 
                    background-color: #f9f9f9; 
                }
                .print-header { 
                    text-align: center; 
                    margin-bottom: 20px; 
                }
                .no-print { 
                    display: none; 
                }
                @media print {
                    @page {
                        size: landscape;
                        margin: 0;
                    }
                    html, body {
                        width: 100%;
                        height: 100%;
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    body {
                        padding: 15mm !important;
                    }
                    .no-print { 
                        display: none !important; 
                    }
                }
            </style>
        </head>
        <body>
            <div class="print-header">
                <h1>Karis Antikvariat - Produktlista</h1>
                <p>Utskriven: ${new Date().toLocaleDateString('sv-SE')} ${new Date().toLocaleTimeString('sv-SE')}</p>
                <p class="no-print">
                    <button onclick="window.print()">Skriv ut</button>
                    <button onclick="window.close()">Stäng</button>
                </p>
            </div>
            ${tableHTML}
            <script>
                window.onload = function() { 
                    setTimeout(() => window.print(), 500); 
                };
            </script>
        </body>
        </html>
    `;
    
    const dataUrl = 'data:text/html;charset=utf-8,' + encodeURIComponent(printContent);
    const printWindow = window.open(dataUrl, '_blank');
    
    if (!printWindow) {
        showMessage('Popup-blockering upptäckt. Tillåt popups för att skriva ut.', 'warning');
    }
}

// SECURITY AND UTILITY FUNCTIONS
function escapeHtml(text) {
    if (typeof text !== 'string') {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

