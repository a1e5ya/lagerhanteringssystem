/**
 * pagination.js - Frontend pagination handler
 * A standard JavaScript module for handling pagination and sorting in tables
 * 
 * @package    KarisAntikvariat
 * @subpackage Frontend
 * @author     Axxell
 * @version    1.1
 */

const TablePagination = {
    /**
     * Configuration options
     */
    options: {
        containerId: '',
        apiEndpoint: '',
        entity: 'products',
        viewType: 'public',
        pageSize: 20,
        currentPage: 1,
        sortColumn: '',
        sortDirection: 'asc',
        filterParams: {},
        onBeforeLoad: null,
        onAfterLoad: null,
        selectors: {
            table: '.table-paginated-table',
            content: '.table-paginated-content',
            pageLinks: '.table-pagination-links',
            pageSizeSelector: '.table-page-size-selector',
            pageSizeContainer: '.table-page-size',
            paginationInfo: '.table-pagination-info',
            showingStart: '.table-showing-start',
            showingEnd: '.table-showing-end',
            totalItems: '.table-total-items',
            sortHeaders: '[data-sort]'
        }
    },

    /**
     * Initialize pagination on a container
     * 
     * @param {string|Element} container - ID of container or container element
     * @param {object} options - Configuration options
     */
    init: function(container, options = {}) {
        // Get container element
        this.container = typeof container === 'string' ? document.getElementById(container) : container;
        if (!this.container) {
            console.error(`Container not found: ${container}`);
            return this;
        }
        
        // Merge options with defaults and data attributes
        this.options = {...this.options, ...options};
        
        // Get options from data attributes
        const dataAttrs = this.container.dataset;
        if (dataAttrs.endpoint) this.options.apiEndpoint = dataAttrs.endpoint;
        if (dataAttrs.entity) this.options.entity = dataAttrs.entity;
        if (dataAttrs.view) this.options.viewType = dataAttrs.view;
        if (dataAttrs.page) this.options.currentPage = parseInt(dataAttrs.page, 10) || 1;
        if (dataAttrs.limit) this.options.pageSize = parseInt(dataAttrs.limit, 10) || 20;
        if (dataAttrs.sort) this.options.sortColumn = dataAttrs.sort;
        if (dataAttrs.order) this.options.sortDirection = dataAttrs.order;
        
        // Initialize DOM elements
        this.initElements();
        
        // Set up event handlers
        this.setupEventHandlers();
        
        // Load initial data
        this.loadPage(this.options.currentPage, this.options.sortColumn, this.options.sortDirection);
        
        return this;
    },
    
    /**
     * Initialize DOM elements
     */
    initElements: function() {
        const s = this.options.selectors;
        
        this.table = this.container.querySelector(s.table);
        this.content = this.container.querySelector(s.content);
        this.pageLinks = this.container.querySelector(s.pageLinks);
        this.pageSizeSelector = this.container.querySelector(s.pageSizeSelector);
        this.pageSizeContainer = this.container.querySelector(s.pageSizeContainer);
        this.paginationInfo = this.container.querySelector(s.paginationInfo);
        this.showingStart = this.container.querySelector(s.showingStart);
        this.showingEnd = this.container.querySelector(s.showingEnd);
        this.totalItems = this.container.querySelector(s.totalItems);
        this.sortHeaders = this.table ? this.table.querySelectorAll(s.sortHeaders) : [];
    },
    
    /**
     * Set up event handlers
     */
    setupEventHandlers: function() {
        // Handle pagination clicks
        if (this.pageLinks) {
            this.pageLinks.addEventListener('click', (e) => {
                this.handlePaginationClick(e);
            });
        }
        
        // Handle sort header clicks
        if (this.sortHeaders) {
            this.sortHeaders.forEach(header => {
                header.addEventListener('click', (e) => {
                    this.handleSortClick(e);
                });
            });
        }
        
        // Handle page size change
        if (this.pageSizeSelector) {
            this.pageSizeSelector.addEventListener('change', (e) => {
                this.options.pageSize = parseInt(e.target.value, 10);
                this.loadPage(1); // Reset to page 1 when changing page size
            });
        }
    },
    
    /**
     * Load page data via AJAX
     * 
     * @param {number} page - Page number to load
     * @param {string} sort - Column to sort by
     * @param {string} direction - Sort direction ('asc' or 'desc')
     */
    loadPage: function(page = 1, sort = null, direction = null) {
        // Update current page
        this.options.currentPage = page;
        
        // Update sort parameters if provided
        if (sort !== null) {
            this.options.sortColumn = sort;
        }
        if (direction !== null) {
            this.options.sortDirection = direction;
        }
        
        // Show loading indicator
        this.showLoading();
        
        // Call onBeforeLoad callback if provided
        if (typeof this.options.onBeforeLoad === 'function') {
            this.options.onBeforeLoad(this);
        }
        
        // Get form data for filtering (if present)
        const filterForm = document.getElementById('search-form');
        let formData = {};
        
        if (filterForm instanceof HTMLFormElement) {
            const formElements = filterForm.elements;
            for (let i = 0; i < formElements.length; i++) {
                const element = formElements[i];
                if (element.name && element.value && element.type !== 'submit') {
                    formData[element.name] = element.value;
                }
            }
        }
        
        // Prepare request data
        const data = {
            entity: this.options.entity,
            action: 'list',
            page: this.options.currentPage,
            limit: this.options.pageSize,
            view_type: this.options.viewType,
            render_html: true, // Request HTML rendering from server
            ...formData,      // Add form data (search, category, etc.)
            ...this.options.filterParams // Add any additional filter parameters
        };
        
        // Add sort parameters if available
        if (this.options.sortColumn) {
            data.sort = this.options.sortColumn;
            data.order = this.options.sortDirection;
        }
        
        // Use the existing AJAX utility if available
        if (typeof InventoryAjax !== 'undefined') {
            InventoryAjax.post(this.options.apiEndpoint, data, 
                (response) => {
                    this.updateUI(response);
                    
                    // Call onAfterLoad callback if provided
                    if (typeof this.options.onAfterLoad === 'function') {
                        this.options.onAfterLoad(this, response);
                    }
                },
                (error) => {
                    this.showError(error.message || 'Error loading data');
                }
            );
        } else {
            // Fallback to standard fetch if InventoryAjax not available
            fetch(this.options.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update UI with response data
                this.updateUI(data);
                
                // Call onAfterLoad callback if provided
                if (typeof this.options.onAfterLoad === 'function') {
                    this.options.onAfterLoad(this, data);
                }
            })
            .catch(error => {
                console.error('Error loading page data:', error);
                this.showError('Ett fel inträffade vid hämtning av data. Försök igen senare.');
            });
        }
    },
    
/**
     * Update UI elements with new data
     * 
     * @param {object} data - Response data from the server
     */
updateUI: function(data) {
    if (!data.success) {
        this.showError(data.message || 'Ett fel inträffade');
        return;
    }
    
    // Update table content
    if (data.html) {
        if (this.content) {
            this.content.innerHTML = data.html;
        }
    } else if (data.items) {
        this.renderItems(data.items);
    } else {
        if (this.content) {
            this.content.innerHTML = '<tr><td colspan="100" class="text-center py-3">Inga resultat hittades</td></tr>';
        }
    }
    
    // Update pagination info
    if (data.pagination) {
        this.updatePaginationInfo(data.pagination);
    }
    
    // Update sort indicators
    this.updateSortIndicators();
    
    // Initialize dynamic elements (clickable rows, etc.)
    this.initDynamicElements();
    
    // Hide loading indicator
    this.hideLoading();
    
    // Update URL parameters if needed
    if (this.options.updateUrl) {
        this.updateUrlParams();
    }
},

/**
 * Render items in the table
 * 
 * @param {Array} items - Array of items to render
 */
renderItems: function(items) {
    if (!this.content || !items || !items.length) {
        if (this.content) {
            this.content.innerHTML = '<tr><td colspan="100" class="text-center py-3">Inga resultat hittades</td></tr>';
        }
        return;
    }
    
    // Get column definitions from table headers
    const columns = [];
    this.sortHeaders.forEach(header => {
        const field = header.dataset.sort || '';
        columns.push({
            field: field,
            title: header.textContent.trim()
        });
    });
    
    // Add a column for actions if needed
    if (columns.length > 0 && !columns[columns.length - 1].field) {
        columns[columns.length - 1].field = 'actions';
    }
    
    // Build HTML for items
    let html = '';
    items.forEach(item => {
        // Add row classes
        let rowClasses = '';
        if (item.href || item.prod_id) {
            rowClasses = 'clickable-row';
        }
        
        let rowAttributes = '';
        if (item.href) {
            rowAttributes += ` data-href="${item.href}"`;
        } else if (item.prod_id) {
            const viewType = this.options.viewType;
            const productUrl = (viewType === 'admin') ? 
                `admin/adminsingleproduct.php?id=${item.prod_id}` : 
                `singleproduct.php?id=${item.prod_id}`;
            rowAttributes += ` data-href="${productUrl}"`;
        }
        
        html += `<tr class="${rowClasses}"${rowAttributes}>`;
        
        // Add cells for each column
        columns.forEach(column => {
            if (column.field === 'actions') {
                // Special case for action buttons
                html += this.renderActionButtons(item);
            } else {
                // Regular data cell
                const value = item[column.field] !== undefined ? item[column.field] : '';
                
                // Add special formatting for some types
                if (column.field === 'price') {
                    html += `<td>${item.formatted_price || value}</td>`;
                } else if (column.field === 'date_added') {
                    html += `<td>${item.formatted_date || value}</td>`;
                } else {
                    html += `<td>${value}</td>`;
                }
            }
        });
        
        html += '</tr>';
    });
    
    this.content.innerHTML = html;
},

/**
 * Render action buttons for a row
 * 
 * @param {object} item - The item data
 * @return {string} HTML for action buttons
 */
renderActionButtons: function(item) {
    let html = '<td>';
    
    // Add special tags first
    if (item.special_price) {
        html += '<span class="badge bg-danger me-1">Rea</span>';
    }
    if (item.is_rare || item.rare) {
        html += '<span class="badge bg-warning text-dark me-1">Sällsynt</span>';
    }
    if (item.is_recommended || item.recommended) {
        html += '<span class="badge bg-info me-1">Rekommenderas</span>';
    }
    
    // Add action buttons based on view type
    if (this.options.viewType === 'admin') {
        html += '<div class="btn-group btn-group-sm">';
        
        // Status-dependent buttons
        if (item.status == 1) { // Available
            html += `<button class="btn btn-outline-success quick-sell" data-id="${item.prod_id}" title="Markera som såld">
                <i class="fas fa-shopping-cart"></i>
            </button>`;
        } else if (item.status == 2) { // Sold
            html += `<button class="btn btn-outline-warning quick-return" data-id="${item.prod_id}" title="Återställ till tillgänglig">
                <i class="fas fa-undo"></i>
            </button>`;
        }
        
        // Always add edit button
        html += `<a href="admin/adminsingleproduct.php?id=${item.prod_id}" class="btn btn-outline-primary" title="Redigera">
            <i class="fas fa-edit"></i>
        </a>`;
        
        html += '</div>';
    } else if (this.options.viewType === 'public') {
        // Add "View details" button for mobile
        html += `<a class="btn btn-success d-block d-md-none" href="singleproduct.php?id=${item.prod_id}">Visa detaljer</a>`;
    }
    
    html += '</td>';
    return html;
},

/**
 * Update pagination information
 * 
 * @param {object} pagination - Pagination data
 */
updatePaginationInfo: function(pagination) {
    // Update showing range (first and last record)
    if (this.showingStart) {
        this.showingStart.textContent = pagination.firstRecord;
    }
    if (this.showingEnd) {
        this.showingEnd.textContent = pagination.lastRecord;
    }
    
    // Update total items
    if (this.totalItems) {
        this.totalItems.textContent = pagination.totalItems;
    }
    
    // Update pagination links
    if (this.pageLinks) {
        this.pageLinks.innerHTML = this.generatePaginationLinks(pagination);
    }
    
    // Update page size selector
    if (this.pageSizeSelector && pagination.pageSizeOptions) {
        let sizeHtml = '';
        pagination.pageSizeOptions.forEach(size => {
            const selected = (size == pagination.itemsPerPage) ? ' selected' : '';
            sizeHtml += `<option value="${size}"${selected}>${size}</option>`;
        });
        this.pageSizeSelector.innerHTML = sizeHtml;
    }
},

/**
 * Generate HTML for pagination links
 * 
 * @param {object} pagination - Pagination data
 * @return {string} HTML for pagination links
 */
generatePaginationLinks: function(pagination) {
    const {currentPage, totalPages} = pagination;
    
    if (totalPages <= 1) {
        return ''; // No pagination needed for a single page
    }
    
    let html = '';
    
    // Previous page button
    html += `
        <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" ${currentPage <= 1 ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
    `;
    
    // Calculate range of page numbers to display
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    // First page link if not in range
    if (startPage > 1) {
        html += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="1">1</a>
            </li>
        `;
        
        // Add ellipsis if needed
        if (startPage > 2) {
            html += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }
    }
    
    // Page number links
    for (let i = startPage; i <= endPage; i++) {
        html += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `;
    }
    
    // Last page link if not in range
    if (endPage < totalPages) {
        // Add ellipsis if needed
        if (endPage < totalPages - 1) {
            html += `
                <li class="page-item disabled">
                    <span class="page-link">...</span>
                </li>
            `;
        }
        
        html += `
            <li class="page-item">
                <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
            </li>
        `;
    }
    
    // Next page link
    html += `
        <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" ${currentPage >= totalPages ? 'tabindex="-1" aria-disabled="true"' : ''}>
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    `;
    
    return html;
},

/**
 * Update sort indicators in the table headers
 */
updateSortIndicators: function() {
    if (!this.sortHeaders) return;
    
    this.sortHeaders.forEach(header => {
        // Remove existing sort indicators
        header.classList.remove('sort-asc', 'sort-desc');
        
        // Find or create sort icon element
        let sortIcon = header.querySelector('.sort-icon');
        if (!sortIcon) {
            sortIcon = document.createElement('span');
            sortIcon.className = 'sort-icon ms-1';
            header.appendChild(sortIcon);
        }
        
        // Get sort column from data attribute
        const column = header.dataset.sort;
        if (!column) return;
        
        // Set appropriate icon based on sort state
        if (column === this.options.sortColumn) {
            header.classList.add(`sort-${this.options.sortDirection}`);
            
            if (this.options.sortDirection === 'asc') {
                sortIcon.innerHTML = '<i class="fas fa-sort-up"></i>';
            } else {
                sortIcon.innerHTML = '<i class="fas fa-sort-down"></i>';
            }
        } else {
            sortIcon.innerHTML = '<i class="fas fa-sort text-muted"></i>';
        }
    });
},

/**
 * Initialize dynamic elements (clickable rows, etc.)
 */
initDynamicElements: function() {
    // Make rows clickable
    const clickableRows = this.content.querySelectorAll('.clickable-row');
    clickableRows.forEach(row => {
        row.addEventListener('click', (e) => {
            // Only navigate if not clicking on a control
            if (!e.target.closest('button, a, input, select, .no-click')) {
                const href = row.dataset.href;
                if (href) {
                    window.location.href = href;
                }
            }
        });
    });
    
    // Add event handlers for action buttons
    const quickSellButtons = this.content.querySelectorAll('.quick-sell');
    quickSellButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent row click
            
            const productId = button.dataset.id;
            this.changeProductStatus(productId, 2); // 2 = Sold
        });
    });
    
    const quickReturnButtons = this.content.querySelectorAll('.quick-return');
    quickReturnButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent row click
            
            const productId = button.dataset.id;
            this.changeProductStatus(productId, 1); // 1 = Available
        });
    });
},

/**
 * Change product status and refresh
 * 
 * @param {number} productId - Product ID
 * @param {number} newStatus - New status (1=Available, 2=Sold)
 */
changeProductStatus: function(productId, newStatus) {
    // Create form data
    const formData = new FormData();
    formData.append('action', 'change_status');
    formData.append('product_id', productId);
    formData.append('status', newStatus);
    
    // Show loading
    this.showLoading();
    
    // Send request
    fetch('admin/search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload current page
            this.loadPage(this.options.currentPage);
        } else {
            this.showError(data.message || 'Ett fel inträffade. Försök igen senare.');
            this.hideLoading();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        this.showError('Ett fel inträffade. Försök igen senare.');
        this.hideLoading();
    });
},

/**
 * Handle pagination link clicks
 * 
 * @param {Event} e - Click event
 */
handlePaginationClick: function(e) {
    e.preventDefault();
    
    // Find the clicked link
    const link = e.target.closest('a[data-page]');
    if (!link) return;
    
    // Get page number
    const page = parseInt(link.dataset.page, 10);
    if (isNaN(page)) return;
    
    // Load the page
    this.loadPage(page);
},

/**
 * Handle sort header clicks
 * 
 * @param {Event} e - Click event
 */
handleSortClick: function(e) {
    e.preventDefault();
    
    // Find the clicked header
    const header = e.target.closest('[data-sort]');
    if (!header) return;
    
    // Get sort column
    const column = header.dataset.sort;
    if (!column) return;
    
    // Determine direction (toggle if already sorting by this column)
    let direction = 'asc';
    if (column === this.options.sortColumn) {
        direction = (this.options.sortDirection === 'asc') ? 'desc' : 'asc';
    }
    
    // Load the first page with new sort
    this.loadPage(1, column, direction);
},

/**
 * Show loading indicator
 */
showLoading: function() {
    // Check if loading indicator already exists
    let loader = this.container.querySelector('.table-loading-overlay');
    if (!loader) {
        // Create loading overlay
        loader = document.createElement('div');
        loader.className = 'table-loading-overlay';
        loader.innerHTML = `
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Laddar...</span>
            </div>
        `;
        
        // Set overlay styles
        loader.style.position = 'absolute';
        loader.style.top = '0';
        loader.style.left = '0';
        loader.style.width = '100%';
        loader.style.height = '100%';
        loader.style.display = 'flex';
        loader.style.alignItems = 'center';
        loader.style.justifyContent = 'center';
        loader.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
        loader.style.zIndex = '1000';
        
        // Make sure container has position relative
        if (getComputedStyle(this.container).position === 'static') {
            this.container.style.position = 'relative';
        }
        
        // Add to container
        this.container.appendChild(loader);
    } else {
        loader.style.display = 'flex';
    }
},

/**
 * Hide loading indicator
 */
hideLoading: function() {
    const loader = this.container.querySelector('.table-loading-overlay');
    if (loader) {
        loader.style.display = 'none';
    }
},

/**
 * Show error message
 * 
 * @param {string} message - Error message
 */
showError: function(message) {
    // Hide loading
    this.hideLoading();
    
    // Show error in content area
    if (this.content) {
        const colspan = this.getColumnCount();
        this.content.innerHTML = `
            <tr>
                <td colspan="${colspan}" class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-circle me-2"></i>${message}
                </td>
            </tr>
        `;
    }
    
    // Also show toast message if possible
    if (typeof window.showMessage === 'function') {
        window.showMessage(message, 'danger');
    }
},

/**
 * Get column count from table headers
 * 
 * @return {number} Column count
 */
getColumnCount: function() {
    if (this.table) {
        const headers = this.table.querySelectorAll('thead th');
        return headers.length || 7; // Default to 7 for product tables
    }
    return 7; // Default
},

/**
 * Update URL parameters to reflect current state
 */
updateUrlParams: function() {
    // Don't update URL if not in browser or if option is disabled
    if (typeof window === 'undefined' || !this.options.updateUrl) {
        return;
    }
    
    // Create URL object
    const url = new URL(window.location.href);
    
    // Update pagination parameters
    url.searchParams.set('page', this.options.currentPage);
    url.searchParams.set('limit', this.options.pageSize);
    
    // Update sort parameters if present
    if (this.options.sortColumn) {
        url.searchParams.set('sort', this.options.sortColumn);
        url.searchParams.set('order', this.options.sortDirection);
    } else {
        url.searchParams.delete('sort');
        url.searchParams.delete('order');
    }
    
    // Update browser history without reloading
    window.history.pushState({}, '', url);
},

/**
 * Set filter parameters
 * 
 * @param {object} filters - Filter parameters
 */
setFilters: function(filters) {
    this.options.filterParams = {...this.options.filterParams, ...filters};
    return this;
},

/**
 * Clear all filters
 */
clearFilters: function() {
    this.options.filterParams = {};
    return this;
},

/**
 * Get current state
 * 
 * @return {object} Current state
 */
getState: function() {
    return {
        currentPage: this.options.currentPage,
        pageSize: this.options.pageSize,
        sortColumn: this.options.sortColumn,
        sortDirection: this.options.sortDirection,
        filters: {...this.options.filterParams}
    };
}
};

// Auto-initialize all paginated tables when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
const paginatedTables = document.querySelectorAll('.table-pagination-container');
paginatedTables.forEach((container, index) => {
    // Generate an ID if none exists
    if (!container.id) {
        container.id = `paginated-table-${index}`;
    }
    
    // Initialize pagination
    TablePagination.init(container, {
        updateUrl: container.dataset.updateUrl === 'true'
    });
});
});