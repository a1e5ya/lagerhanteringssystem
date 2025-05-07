/**
 * main.js - Common JavaScript functionality for Karis Antikvariat
 * Contains AJAX search functionality for both public and admin pages
 */

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize search functionality based on which page we're on
    initializeSearch();
    
    // Make clickable rows work
    makeRowsClickable();
});

/**
 * Initialize search functionality based on current page
 */
function initializeSearch() {
    // Detect which page we're on
    const isAdminPage = (document.getElementById('inventory-tabs') !== null);
    const isIndexPage = (document.getElementById('public-search') !== null);
    const isListPage = (document.querySelector('.tab-pane[id="lists"]') !== null);
    
    // Initialize the appropriate search functionality
    if (isIndexPage) {
        initializePublicSearch();
    }
    
    if (isAdminPage) {
        // Check which tab is active
        const activeTab = document.querySelector('.nav-link.active');
        if (activeTab) {
            const tabName = activeTab.getAttribute('data-tab');
            
            if (tabName === 'search') {
                initializeAdminSearch();
            } else if (tabName === 'lists') {
                initializeListsSearch();
            }
            
            // Add tab change listener to initialize search when tabs change
            document.querySelectorAll('.nav-link').forEach(tab => {
                tab.addEventListener('click', function() {
                    const newTabName = this.getAttribute('data-tab');
                    if (newTabName === 'search') {
                        setTimeout(initializeAdminSearch, 300); // Short delay to ensure content is loaded
                    } else if (newTabName === 'lists') {
                        setTimeout(initializeListsSearch, 300); // Short delay to ensure content is loaded
                    }
                });
            });
        }
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
        performPublicSearch();
    });
    
    // Add change event listener to category dropdown
    categorySelect.addEventListener('change', function() {
        performPublicSearch();
    });
    
    // Function to perform search
    function performPublicSearch() {
        const targetElem = document.getElementById('public-inventory-body');
        const searchParams = {
            search: searchInput.value,
            category: categorySelect.value
        };
        
        // Perform AJAX search
        ajaxSearch('/prog23/lagerhanteringssystem/admin/search.php', 'public', searchParams, targetElem, function() {
            // Success callback
            makeRowsClickable();
            // Scroll to search results
            document.getElementById('browse').scrollIntoView({ behavior: 'smooth' });
            
            // Update URL without reloading (for bookmark/history purposes)
            updateUrlParams(searchParams);
        });
    }
}

/**
 * Initialize admin search on admin.php search tab
 */
function initializeAdminSearch() {
    const adminSearchForm = document.getElementById('admin-search-form');
    const searchTermInput = document.getElementById('search-term');
    const categoryFilterSelect = document.getElementById('category-filter');
    
    // Skip if elements don't exist
    if (!adminSearchForm || !searchTermInput || !categoryFilterSelect) return;
    
    // Handle search form submission
    adminSearchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performAdminSearch();
    });
    
    // Add change event listener to category dropdown
    categoryFilterSelect.addEventListener('change', function() {
        performAdminSearch();
    });
    
    // Function to perform search
    function performAdminSearch() {
        const targetElem = document.getElementById('inventory-body');
        const searchParams = {
            search: searchTermInput.value,
            category: categoryFilterSelect.value
        };
        
        // Perform AJAX search
        ajaxSearch('../admin/search.php', 'admin', searchParams, targetElem, function() {
            // Success callback
            attachActionListeners();
            
            // Update URL without reloading (for bookmark/history purposes)
            updateUrlParams(Object.assign({}, searchParams, { tab: 'search' }));
        });
    }
}

/**
 * Initialize lists search on admin.php lists tab
 */
function initializeListsSearch() {
    const listsSearchForm = document.getElementById('lists-search-form');
    const listsSearchInput = document.getElementById('lists-search-term');
    const listsCategorySelect = document.getElementById('lists-category');
    
    // Skip if elements don't exist
    if (!listsSearchForm || !listsSearchInput || !listsCategorySelect) return;
    
    // Handle search form submission
    listsSearchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performListsSearch();
    });
    
    // Add change event listener to category dropdown
    listsCategorySelect.addEventListener('change', function() {
        performListsSearch();
    });
    
    // Function to perform search
    function performListsSearch() {
        const targetElem = document.getElementById('lists-body');
        const searchParams = {
            search: listsSearchInput.value,
            category: listsCategorySelect.value
        };
        
        // Perform AJAX search
        ajaxSearch('admin/lists.php', 'lists', searchParams, targetElem, function() {
            // Success callback
            attachListsActionListeners();
            
            // Update URL without reloading (for bookmark/history purposes)
            updateUrlParams(Object.assign({}, searchParams, { tab: 'lists' }));
        });
    }
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
 * Make rows clickable (for public inventory table)
 */
function makeRowsClickable() {
    const clickableRows = document.querySelectorAll('.clickable-row');
    clickableRows.forEach(row => {
        row.addEventListener('click', function(event) {
            if (!event.target.closest('a') && !event.target.closest('button')) {
                window.location.href = this.dataset.href;
            }
        });
    });
}

/**
 * Attach event listeners to action buttons in admin search
 */
function attachActionListeners() {
    // Quick sell button click
    document.querySelectorAll('.quick-sell').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            if (confirm('Är du säker på att du vill markera denna produkt som såld?')) {
                changeProductStatus(productId, 2); // 2 = Sold
            }
        });
    });
    
    // Quick return button click
    document.querySelectorAll('.quick-return').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const productId = this.getAttribute('data-id');
            if (confirm('Är du säker på att du vill återställa denna produkt till tillgänglig?')) {
                changeProductStatus(productId, 1); // 1 = Available
            }
        });
    });
}

/**
 * Attach event listeners to action buttons in lists tab
 */
function attachListsActionListeners() {
    // Handle batch operations
    document.querySelectorAll('.batch-action').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            performBatchAction(action);
        });
    });
    
    // Handle "select all" checkbox
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }
}

/**
 * Change product status (sell/return)
 * 
 * @param {number} productId - Product ID
 * @param {number} newStatus - New status (1=Available, 2=Sold)
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
            // Show success message
            showMessage(data.message, 'success');
            
            // Reload search results
            if (document.getElementById('search-term')) {
                // We're on the admin search tab
                document.getElementById('admin-search-form').dispatchEvent(new Event('submit'));
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
 * Perform batch action on selected items in lists tab
 * 
 * @param {string} action - Action to perform
 */
function performBatchAction(action) {
    // Get all selected checkboxes
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    
    if (checkboxes.length === 0) {
        showMessage('Välj minst en produkt för att utföra denna åtgärd.', 'warning');
        return;
    }
    
    // Confirm action
    let confirmMessage = 'Är du säker på att du vill utföra denna åtgärd på de valda produkterna?';
    switch (action) {
        case 'sell':
            confirmMessage = 'Är du säker på att du vill markera de valda produkterna som sålda?';
            break;
        case 'return':
            confirmMessage = 'Är du säker på att du vill återställa de valda produkterna till tillgängliga?';
            break;
        case 'delete':
            confirmMessage = 'Är du säker på att du vill ta bort de valda produkterna? Denna åtgärd kan inte ångras!';
            break;
    }
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    // Get selected product IDs
    const productIds = Array.from(checkboxes).map(checkbox => checkbox.value);
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'batch_action');
    formData.append('batch_action', action);
    formData.append('product_ids', JSON.stringify(productIds));
    
    // Send request
    fetch('admin/lists.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showMessage(data.message, 'success');
            
            // Reload lists results
            if (document.getElementById('lists-search-form')) {
                document.getElementById('lists-search-form').dispatchEvent(new Event('submit'));
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
 * Display message to user
 * 
 * @param {string} message - Message to display
 * @param {string} type - Message type ('success', 'danger', 'warning', 'info')
 */
function showMessage(message, type) {
    // Find message container or create it
    let messageContainer = document.getElementById('message-container');
    
    if (!messageContainer) {
        // Look for a good place to add the message container
        const possibleParents = [
            document.getElementById('admin-search-form'),
            document.getElementById('lists-search-form'),
            document.getElementById('search-form')
        ];
        
        let parent = null;
        for (const element of possibleParents) {
            if (element) {
                parent = element;
                break;
            }
        }
        
        if (!parent) {
            // If no suitable parent, use the first container div
            parent = document.querySelector('.container');
        }
        
        if (!parent) {
            console.error('Cannot find suitable parent for message container');
            return;
        }
        
        // Create message container
        messageContainer = document.createElement('div');
        messageContainer.id = 'message-container';
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