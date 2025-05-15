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
// Add this to main.js - Initialize public search with pagination
function initializePublicSearch() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('public-search');
    const categorySelect = document.getElementById('public-category');
    
    // Skip if elements don't exist
    if (!searchForm || !searchInput || !categorySelect) return;
    
    // Handle search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performPublicSearch(1); // Start with page 1 on new search
    });
    
    // Add change event listener to category dropdown
    categorySelect.addEventListener('change', function() {
        performPublicSearch(1); // Start with page 1 on category change
    });
    

    
    // Initial load of products
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 1;
    
    if (!searchInput.value && categorySelect.value === 'all') {
        // If no search filters, load default products with pagination
        performPublicSearch(currentPage);
    } else {
        // If there are search filters, use them
        performPublicSearch(currentPage);
    }
}

// Function to perform public search with pagination
function performPublicSearch(page = 1) {
    const targetElem = document.getElementById('public-inventory-body');
    const searchParams = {
        search: document.getElementById('public-search').value,
        category: document.getElementById('public-category').value,
        page: page,
        limit: 20
    };
    
    // Perform AJAX search
    ajaxSearch('/prog23/lagerhanteringssystem/admin/search.php', 'public', searchParams, targetElem, function() {

        
        
        // Scroll to search results
        document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
        
        // Update URL without reloading (for bookmark/history purposes)
        updateUrlParams(Object.assign({}, searchParams));
    });
}





function makeRowsClickable() {
    const clickableRows = document.querySelectorAll('.clickable-row');
    
    clickableRows.forEach(row => {
        // Remove any existing click event listeners first to prevent duplicates
        row.removeEventListener('click', rowClickHandler);
        
        // Add the click event listener
        row.addEventListener('click', rowClickHandler);
    });
}

// Separate the handler function to avoid duplicating anonymous functions
function rowClickHandler(event) {
    // Only navigate if click wasn't on a button or link
    if (!event.target.closest('a') && !event.target.closest('button')) {
        window.location.href = this.dataset.href;
    }
}

/**
 * Generic AJAX search function (used by both admin.js and main.js)
 * 
 * @param {string} url - URL to send the request to
 * @param {string} type - Type of search ('public', 'admin', 'lists')
 * @param {object} params - Search parameters
 * @param {HTMLElement} targetElem - Element to update with results
 * @param {Function} successCallback - Callback to run on success
 */
// Revised AJAX search function in main.js
function ajaxSearch(url, type, params, targetElem, successCallback) {
    // Check if we have a valid target element
    if (!targetElem) {
        console.error('Target element not found');
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
            console.error('Error response:', xhr.responseText);
        }
    };
    
    xhr.onerror = function() {
        targetElem.innerHTML = `<tr><td colspan="${cols}" class="text-center text-danger">Ett fel inträffade. Kontrollera din internetanslutning.</td></tr>`;
        console.error('Network error occurred');
    };
    
    xhr.send();
}

/**
 * Change product status (sell/return) - shared function
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
    fetch('admin/search.php', {
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
                    url: 'admin/search.php',
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
        console.error('Error:', error);
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
            console.error('Cannot find suitable parent for message container');
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
            alert.remove();
            
            // If no alerts left, hide container
            if (messageContainer.children.length === 0) {
                messageContainer.style.display = 'none';
            }
        }, 150);
    }, 5000);
}



