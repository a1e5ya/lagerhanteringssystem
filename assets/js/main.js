/**
 * main.js - Common JavaScript functionality for Karis Antikvariat
 * Contains shared functionality used by both public and admin pages
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality based on which page we're on
    initializeSearch();
    
    // Make clickable rows work
    $(document).on('change', '#category-filter', function() {
        $('#admin-search-form').submit();
    });
});

/**
 * Initialize search functionality based on current page
 */
function initializeSearch() {
    // Detect which page we're on
    const isAdminPage = (document.getElementById('inventory-tabs') !== null);
    const isIndexPage = (document.getElementById('public-search') !== null);
    
    // Initialize the appropriate search functionality
    if (isIndexPage) {
        initializePublicSearch();
    }
}

/**
 * Initialize public search on index.php
 */
function initializePublicSearch() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('public-search');
    const categorySelect = document.getElementById('public-category');
    
    // Skip if elements don't exist
    if (!searchForm || !searchInput || !categorySelect) return;
    
    // Handle search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // If TablePagination is available, use it
        if (typeof TablePagination !== 'undefined') {
            const container = document.querySelector('.table-pagination-container');
            if (container) {
                TablePagination.setFilters({
                    search: searchInput.value,
                    category: categorySelect.value
                }).loadPage(1);
                
                // Scroll to search results
                document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
            } else {
                // Fallback if container not found
                performPublicSearch(1);
            }
        } else {
            // Fallback if TablePagination not available
            performPublicSearch(1);
        }
    });
    
    // Add change event listener to category dropdown
    categorySelect.addEventListener('change', function() {
        // Trigger form submission to update results
        searchForm.dispatchEvent(new Event('submit'));
    });
    
    // Initialize rows as clickable
    makeRowsClickable();
}

/**
 * Legacy function to perform public search
 * @param {number} page Page number
 * @param {boolean} randomSamples Whether to load random samples
 */
function performPublicSearch(page = 1, randomSamples = false) {
    const targetElem = document.getElementById('public-inventory-body');
    const searchParams = {
        search: document.getElementById('public-search').value || '',
        category: document.getElementById('public-category').value || 'all',
        page: page,
        limit: parseInt(document.getElementById('page-size-selector')?.value || 20)
    };
    
    // Use ajaxPublicSearch with random samples flag
    ajaxPublicSearch(
        searchParams.search, 
        searchParams.category, 
        searchParams.page, 
        searchParams.limit, 
        targetElem, 
        function() {
            // Scroll to search results
            document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
            
            // Update URL without reloading (for bookmark/history purposes)
            updateUrlParams(Object.assign({}, searchParams, {random_samples: randomSamples ? 'true' : null}));
        },
        randomSamples
    );
}

/**
 * Make table rows clickable
 */
function makeRowsClickable() {
    console.log('Making rows clickable - testing with modified URL');
    
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Only navigate if not clicking on a control element
            if (!e.target.closest('a, button, input, select, .no-click')) {
                const href = this.dataset.href;
                if (href) {
                    // Add a dummy parameter to prevent caching or interception
                    const modifiedHref = href + (href.includes('?') ? '&' : '?') + '_=' + Date.now();
                    console.log('Navigating to modified URL:', modifiedHref);
                    window.location.href = modifiedHref;
                }
            }
        });
    });
}

/**
 * Row click handler
 * @param {Event} event Click event
 */
function rowClickHandler(event) {
    console.log('Row clicked!', event.target, this.dataset.href);
    
    // Only navigate if click wasn't on a button, link, or other interactive element
    if (!event.target.closest('a, button, input, select, .no-click')) {
        const href = this.dataset.href;
        if (href) {
            console.log('Navigating to:', href);
            window.location.href = href;
        } else {
            console.log('No href found on this row');
        }
    } else {
        console.log('Click was on an interactive element, not navigating');
    }
}

/**
 * AJAX search function for public products
 * 
 * @param {string} searchTerm - Search term
 * @param {string} category - Category ID or 'all'
 * @param {number} page - Page number
 * @param {number} limit - Items per page
 * @param {HTMLElement} targetElem - Target element to update
 * @param {Function} successCallback - Callback on success
 * @param {boolean} randomSamples - Whether to load random samples mode
 */
function ajaxPublicSearch(searchTerm, category, page, limit, targetElem, successCallback, randomSamples = false) {
    // Check if we have a valid target element
    if (!targetElem) {
        console.error('Target element not specified for ajaxPublicSearch');
        return;
    }
    
    // Show loading indicator
    const cols = 7; // Public table has 7 columns
    targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;
    
    // Safety limit - never load more than 200 products
    const safeLimit = Math.min(limit || 20, 200);
    
    // Build query string
    const queryParams = new URLSearchParams({
        search: searchTerm || '',
        category: category !== 'all' ? category : '',
        page: page || 1,
        limit: safeLimit
    });
    
    // Add random_samples parameter if specified
    if (randomSamples) {
        queryParams.append('random_samples', 'true');
    }
    
    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `${BASE_URL}/api/get_public_products.php?${queryParams}`, true);
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    // Update table with products
                    if (response.html) {
                        targetElem.innerHTML = response.html;
                    } else {
                        targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center">Inga produkter hittades</td></tr>`;
                    }
                    
                    // Make rows clickable for public search results
                    makeRowsClickable();
                    
                    // Update pagination info if available
                    if (response.pagination && typeof updatePaginationInfo === 'function') {
                        updatePaginationInfo(response.pagination, randomSamples);
                    }
                    
                    // Execute callback if provided
                    if (typeof successCallback === 'function') {
                        successCallback(response);
                    }
                } else {
                    // Show error message
                    targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">${response.message || 'Ett fel inträffade'}</td></tr>`;
                }
            } catch (e) {
                console.error('Error parsing JSON response:', e);
                targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Ett fel inträffade vid tolkning av svaret</td></tr>`;
            }
        } else {
            targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Ett fel inträffade. Status: ${xhr.status}</td></tr>`;
        }
    };
    
    xhr.onerror = function() {
        targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Ett fel inträffade. Kontrollera din internetanslutning.</td></tr>`;
    };
    
    xhr.send();
}

/**
 * Generic AJAX search function
 * 
 * @param {string} url - URL to send the request to
 * @param {string} type - Type of search ('public', 'admin', 'lists')
 * @param {object} params - Search parameters
 * @param {HTMLElement} targetElem - Element to update with results
 * @param {Function} successCallback - Callback to run on success
 */
function ajaxSearch(url, type, params, targetElem, successCallback) {
    // Check if we have a valid target element
    if (!targetElem) {
        return;
    }
    
    // Show loading indicator
    const cols = (type === 'public') ? 7 : 8;
    targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>`;
    
    // Build query string
    let queryParams = `ajax=${type}`;
    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            queryParams += `&${key}=${encodeURIComponent(params[key])}`;
        }
    }
    
    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `${url}?${queryParams}`, true);
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            targetElem.innerHTML = xhr.responseText;
            
            // Make rows clickable for public search results
            if (type === 'public') {
                makeRowsClickable(); // Ensure clickable rows are initialized after content update
            }
            
            if (typeof successCallback === 'function') {
                successCallback();
            }
        } else {
            targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Ett fel inträffade. Försök igen senare. Status: ${xhr.status}</td></tr>`;
        }
    };
    
    xhr.onerror = function() {
        targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Ett fel inträffade. Kontrollera din internetanslutning.</td></tr>`;
    };
    
    xhr.send();
}

/**
 * Change product status (sell/return)
 * 
 * @param {number} productId - Product ID
 * @param {number} newStatus - New status (1=Available, 2=Sold)
 * @param {Function} callback - Optional callback function
 */
function changeProductStatus(productId, newStatus) {
    // Create form data
    const formData = new FormData();
    formData.append('action', 'change_status');
    formData.append('product_id', productId);
    formData.append('status', newStatus);
    
    // Send request
    fetch(BASE_URL + '/admin/search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Instead of submitting the form, manually get the search parameters
            if (document.getElementById('search-term')) {
                const searchTerm = document.getElementById('search-term').value;
                const categoryFilter = document.getElementById('category-filter').value;
                
                // Use AJAX to refresh just the table without page reload
                $.ajax({
                    url: BASE_URL + '/admin/search.php',
                    data: {
                        ajax: 'admin',
                        search: searchTerm, 
                        category: categoryFilter
                    },
                    type: 'GET',
                    success: function(data) {
                        // Replace table content
                        $('#inventory-body').html(data);
                        
                        // Reattach event listeners
                        attachActionListeners();
                    }
                });
            }
        } else {
            // Show error message
            showMessage('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
    });
}

/**
 * Update URL parameters without reloading the page
 * 
 * @param {object} params - Parameters to update
 */
function updateUrlParams(params) {
    const url = new URL(window.location.href);
    
    // Update or add each parameter
    for (const key in params) {
        if (params.hasOwnProperty(key)) {
            url.searchParams.set(key, params[key]);
        }
    }
    
    // Update browser history without reloading
    window.history.pushState({}, '', url);
}

/**
 * Display message to user
 * 
 * @param {string} message - Message to display
 * @param {string} type - Message type ('success', 'danger', 'warning', 'info')
 * @param {string} containerId - ID of message container
 */
function showMessage(message, type, containerId = 'message-container') {
    // Find message container or create it
    let messageContainer = document.getElementById(containerId);
    
    if (!messageContainer) {
        // Look for a good place to add the message container
        const possibleParents = [
            document.getElementById('admin-search-form'),
            document.getElementById('lists-search-form'),
            document.getElementById('search-form'),
            document.querySelector('.container')
        ];
        
        let parent = null;
        for (const element of possibleParents) {
            if (element) {
                parent = element;
                break;
            }
        }
        
        if (!parent) {
            return;
        }
        
        // Create message container
        messageContainer = document.createElement('div');
        messageContainer.id = containerId;
        messageContainer.className = 'alert-container mt-3';
        parent.insertAdjacentElement('beforebegin', messageContainer);
    }
    
    // Make sure container is visible
    messageContainer.style.display = 'block';
    
    // Create alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to container
    messageContainer.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
            
            // If no alerts left, hide container
            if (messageContainer.children.length === 0) {
                messageContainer.style.display = 'none';
            }
        }, 150);
    }, 5000);
}