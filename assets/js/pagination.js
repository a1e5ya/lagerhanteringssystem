/**
 * pagination.js - Frontend pagination handler
 * A standard JavaScript module for handling pagination and sorting in tables
 * 
 * @package    KarisAntikvariat
 * @subpackage Frontend
 * @author     Axxell
 * @version    1.0
 */

const TablePagination = {
    /**
     * Configuration options
     */
    options: {
        containerId: '',
        apiEndpoint: '',
        pageSize: 10,
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
     * @param {string} containerId - ID of the container element
     * @param {object} options - Configuration options
     */
    init: function(containerId, options = {}) {
        // Merge options with defaults
        this.options = {...this.options, ...options, containerId};
        
        // Cache DOM elements
        this.container = document.getElementById(containerId);
        if (!this.container) {
            console.error(`Container with ID "${containerId}" not found`);
            return;
        }
        
        // Get API endpoint from data attribute if not provided
        if (!this.options.apiEndpoint) {
            this.options.apiEndpoint = this.container.dataset.endpoint || '';
        }
        
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
        this.sortHeaders = this.container.querySelectorAll(s.sortHeaders);
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
        
        // Prepare request data
        const data = {
            page: this.options.currentPage,
            limit: this.options.pageSize,
            ...this.options.filterParams
        };
        
        // Add sort parameters if available
        if (this.options.sortColumn) {
            data.sort = this.options.sortColumn;
            data.order = this.options.sortDirection;
        }
        
        // Send AJAX request
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
            this.content.innerHTML = data.html;
        } else if (data.items) {
            this.renderItems(data.items);
        }
        
        // Update pagination info
        if (data.pagination) {
            this.updatePaginationInfo(data.pagination);
        }
        
        // Update sort indicators
        this.updateSortIndicators();
        
        // Hide loading indicator
        this.hideLoading();
    },
    
    /**
     * Show loading indicator
     */
    showLoading: function() {
        if (this.content) {
            // Create loading overlay
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'table-loading-overlay';
            loadingOverlay.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Laddar...</span>
                </div>
            `;
            
            // Add loading overlay
            this.content.style.opacity = '0.5';
            this.table.appendChild(loadingOverlay);
        }
    },
    
    /**
     * Hide loading indicator
     */
    hideLoading: function() {
        if (this.content) {
            // Remove loading overlay
            const loadingOverlay = this.table.querySelector('.table-loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
            
            this.content.style.opacity = '1';
        }
    },
    
    /**
     * Show error message
     * 
     * @param {string} message - Error message to display
     */
    showError: function(message) {
        // Hide loading indicator
        this.hideLoading();
        
        // Show error message
        if (this.content) {
            const colSpan = this.getColumnCount();
            this.content.innerHTML = `
                <tr>
                    <td colspan="${colSpan}" class="text-center text-danger py-3">
                        <i class="fas fa-exclamation-circle me-2"></i>${message}
                    </td>
                </tr>
            `;
        }
    },
    
    /**
     * Get the number of columns in the table
     * 
     * @return {number} Number of columns
     */
    getColumnCount: function() {
        if (this.table) {
            const headerRow = this.table.querySelector('thead tr');
            return headerRow ? headerRow.children.length : 1;
        }
        return 1;
    },
    
    /**
     * Render items in the table
     * 
     * @param {Array} items - Array of items to render
     */
    renderItems: function(items) {
        if (!this.content || !items || !items.length) {
            this.content.innerHTML = `
                <tr>
                    <td colspan="${this.getColumnCount()}" class="text-center py-3">
                        Inga resultat hittades
                    </td>
                </tr>
            `;
            return;
        }
        
        // Get column names from headers
        const columns = Array.from(this.table.querySelectorAll('thead th')).map(th => {
            return th.dataset.field || '';
        });
        
        // Build HTML for items
        let html = '';
        items.forEach(item => {
            let rowHtml = '<tr>';
            
            columns.forEach(column => {
                if (column) {
                    const value = item[column] !== undefined ? item[column] : '';
                    rowHtml += `<td>${value}</td>`;
                } else {
                    rowHtml += '<td></td>';
                }
            });
            
            rowHtml += '</tr>';
            html += rowHtml;
        });
        
        this.content.innerHTML = html;
    },
    
    /**
     * Update pagination information
     * 
     * @param {object} pagination - Pagination data
     */
    updatePaginationInfo: function(pagination) {
        const {currentPage, totalPages, totalItems, firstRecord, lastRecord} = pagination;
        
        // Update pagination links
        if (this.pageLinks) {
            this.pageLinks.innerHTML = this.generatePaginationLinks(currentPage, totalPages);
        }
        
        // Update showing range
        if (this.showingStart) {
            this.showingStart.textContent = firstRecord;
        }
        if (this.showingEnd) {
            this.showingEnd.textContent = lastRecord;
        }
        
        // Update total items
        if (this.totalItems) {
            this.totalItems.textContent = totalItems;
        }
        
        // Show/hide pagination info based on total pages
        if (this.paginationInfo) {
            this.paginationInfo.style.display = totalPages > 0 ? 'block' : 'none';
        }
    },
    
    /**
     * Generate HTML for pagination links
     * 
     * @param {number} currentPage - Current page number
     * @param {number} totalPages - Total number of pages
     * @return {string} HTML for pagination links
     */
    generatePaginationLinks: function(currentPage, totalPages) {
        if (totalPages <= 1) {
            return '';
        }
        
        let html = '';
        
        // Previous page link
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
            
            const sortIcon = header.querySelector('.sort-icon');
            if (sortIcon) {
                sortIcon.innerHTML = '';
            }
            
            // Add indicator for current sort column
            if (header.dataset.sort === this.options.sortColumn) {
                header.classList.add(`sort-${this.options.sortDirection}`);
                
                // Create or update sort icon
                if (!sortIcon) {
                    const icon = document.createElement('span');
                    icon.className = 'sort-icon ms-1';
                    header.appendChild(icon);
                    
                    if (this.options.sortDirection === 'asc') {
                        icon.innerHTML = '&#9650;'; // Up arrow
                    } else {
                        icon.innerHTML = '&#9660;'; // Down arrow
                    }
                } else {
                    sortIcon.innerHTML = this.options.sortDirection === 'asc' ? '&#9650;' : '&#9660;';
                }
            }
        });
    },
    
    /**
     * Handle pagination link clicks
     * 
     * @param {Event} e - Click event
     */
    handlePaginationClick: function(e) {
        e.preventDefault();
        
        const target = e.target.closest('a[data-page]');
        if (!target) return;
        
        const page = parseInt(target.dataset.page, 10);
        if (isNaN(page)) return;
        
        this.loadPage(page);
    },
    
    /**
     * Handle sort header clicks
     * 
     * @param {Event} e - Click event
     */
    handleSortClick: function(e) {
        e.preventDefault();
        
        const header = e.target.closest('[data-sort]');
        if (!header) return;
        
        const column = header.dataset.sort;
        let direction = 'asc';
        
        // Toggle direction if already sorting by this column
        if (column === this.options.sortColumn) {
            direction = this.options.sortDirection === 'asc' ? 'desc' : 'asc';
        }
        
        this.loadPage(1, column, direction);
    },
    
    /**
     * Add or update filter parameters
     * 
     * @param {object} filters - Filter parameters to add/update
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
     * Refresh the current page
     */
    refresh: function() {
        this.loadPage(this.options.currentPage);
        return this;
    },
    
    /**
     * Get current pagination state
     * 
     * @return {object} Current pagination state
     */
    getState: function() {
        return {
            currentPage: this.options.currentPage,
            pageSize: this.options.pageSize,
            sortColumn: this.options.sortColumn,
            sortDirection: this.options.sortDirection,
            filterParams: {...this.options.filterParams}
        };
    }
};

// Export the TablePagination object for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = TablePagination;
} else {
    window.TablePagination = TablePagination;
}