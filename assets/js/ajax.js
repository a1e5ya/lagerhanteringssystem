/**
 * ajax.js - AJAX functionality for Karis Antikvariat
 * Contains all AJAX search functionality, data fetching, and response handling
 */

// Function to handle search form event handlers
function attachSearchEventHandlers() {
    const adminSearchForm = document.getElementById('admin-search-form');
    const categoryFilterSelect = document.getElementById('category-filter');
    
    // First, remove any existing event listeners to prevent duplicates
    if (adminSearchForm) {
        const newForm = adminSearchForm.cloneNode(true);
        adminSearchForm.parentNode.replaceChild(newForm, adminSearchForm);
        
        newForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performAdminSearch();
        });
    }
    
    // Handle category dropdown change
    if (categoryFilterSelect) {
        // Replace with a clone to remove all existing event listeners
        const newSelect = categoryFilterSelect.cloneNode(true);
        categoryFilterSelect.parentNode.replaceChild(newSelect, categoryFilterSelect);
        
        // Add change event that performs search immediately
        newSelect.addEventListener('change', function(e) {
            e.preventDefault();
            // Perform search immediately when dropdown value changes
            performAdminSearch();
        });
    }
  }
  
  // Initialize admin search functionality
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
    
    // Add this new section: Add change event listener to category dropdown 
    // to make it trigger search immediately
    categoryFilterSelect.addEventListener('change', function() {
        performAdminSearch();
    });
  }
  
  // Perform admin search
  function performAdminSearch(page = 1) {
    const targetElem = document.getElementById('inventory-body');
    if (!targetElem) {
        console.error('Target element not found');
        return;
    }
    
    const searchTermInput = document.getElementById('search-term');
    const categoryFilterSelect = document.getElementById('category-filter');
    
    if (!searchTermInput || !categoryFilterSelect) {
        console.error('Search form elements not found');
        return;
    }
    
    const searchParams = {
        search: searchTermInput.value,
        category: categoryFilterSelect.value,
        ajax: 'admin', // Specify that this is an admin search
        page: page || 1
    };
    
    // Show loading spinner
    targetElem.innerHTML = '<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    
    // Perform the AJAX request
    fetch(`/prog23/lagerhanteringssystem/admin/search.php?${new URLSearchParams(searchParams)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            // Update the table contents
            targetElem.innerHTML = html;
            
            // Explicitly make rows clickable first (important order)
            makeRowsClickable();
            
            // Then attach action listeners to buttons
            attachActionListeners();
            
            // Update URL without reloading
            updateUrlParams(Object.assign({}, searchParams, { tab: 'search' }));
        })
        .catch(error => {
            console.error('Error:', error);
            targetElem.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>';
        });
  }
  
  // Function to update pagination UI after AJAX load
  function updatePaginationUI(currentPage) {
    // Find all pagination links and update their click events
    document.querySelectorAll('#pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get page number from the link
            const hrefParams = new URLSearchParams(this.getAttribute('href').split('?')[1]);
            const pageNum = hrefParams.get('page');
            
            // Perform search with new page number
            const searchParams = {
                search: document.getElementById('search-term').value,
                category: document.getElementById('category-filter').value,
                page: pageNum,
                limit: 20
            };
            
            // Update URL
            updateUrlParams(Object.assign({}, searchParams, { tab: 'search' }));
            
            // Execute search with the new page
            ajaxSearch('/prog23/lagerhanteringssystem/admin/search.php', 'admin', searchParams, 
                document.getElementById('inventory-body'), function() {
                    attachActionListeners();
                    makeRowsClickable();
                    updatePaginationUI(pageNum);
                });
        });
    });
  }
  
  // Initialize lists tab
  function initializeLists() {
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
      
      // Attach list-specific action listeners
      attachListsActionListeners();
  }
  
  // Perform lists search
  function performListsSearch() {
      const targetElem = document.getElementById('lists-body');
      const searchParams = {
          search: document.getElementById('lists-search-term').value,
          category: document.getElementById('lists-category').value
      };
      
      // Perform AJAX search
      ajaxSearch('admin/search.php', 'lists', searchParams, targetElem, function() {
          // Success callback
          attachListsActionListeners();
          
          // Update URL without reloading
          updateUrlParams(Object.assign({}, searchParams, { tab: 'lists' }));
      });
  }
  
  // AJAX search function
  function ajaxSearch(url, type, params, targetElement, callback) {
      // Show loading indicator
      $(targetElement).html('<tr><td colspan="100%" class="text-center"><div class="spinner-border text-primary"></div></td></tr>');
      
      // Create form data
      const formData = new FormData();
      formData.append('action', 'search');
      formData.append('type', type);
      
      // Add all params
      Object.keys(params).forEach(key => {
          formData.append(key, params[key]);
      });
      
      // Send request
      fetch(url, {
          method: 'POST',
          body: formData
      })
      .then(response => {
          if (!response.ok) {
              throw new Error('Network response was not ok');
          }
          return response.text();
      })
      .then(html => {
          // Update the target element with the response HTML
          $(targetElement).html(html);
          
          // Call the callback function if provided
          if (typeof callback === 'function') {
              callback();
          }
      })
      .catch(error => {
          console.error('Error:', error);
          $(targetElement).html(`<tr><td colspan="100%" class="text-center text-danger">Ett fel inträffade vid sökning. Försök igen senare.</td></tr>`);
      });
  }
  
  // Update refreshTableContent to ensure rows are clickable after refresh
  function refreshTableContent() {
    const targetElem = document.getElementById('inventory-body');
    if (!targetElem) {
        console.error('Target element not found');
        return;
    }
    
    const searchTermInput = document.getElementById('search-term');
    const categoryFilterSelect = document.getElementById('category-filter');
    
    if (!searchTermInput || !categoryFilterSelect) {
        console.error('Search form elements not found');
        return;
    }
    
    const searchParams = {
        search: searchTermInput.value,
        category: categoryFilterSelect.value,
        ajax: 'admin',
        table_only: 'true' // Request only the table content
    };
    
    // Show loading spinner
    targetElem.innerHTML = '<tr><td colspan="8" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>';
    
    // Perform the AJAX request
    fetch(`/prog23/lagerhanteringssystem/admin/search.php?${new URLSearchParams(searchParams)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            // Only update the table contents, not the form
            targetElem.innerHTML = html;
            
            // Explicitly make rows clickable first
            makeRowsClickable();
            
            // Then attach action listeners to buttons
            attachActionListeners();
        })
        .catch(error => {
            console.error('Error:', error);
            targetElem.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Ett fel inträffade. Försök igen senare.</td></tr>';
        });
  }
  
  // Change product status (for quick actions)
  function changeProductStatus(productId, newStatus, callback) {
    // Create form data for the request
    const formData = new FormData();
    formData.append('action', 'change_status');
    formData.append('product_id', productId);
    formData.append('status', newStatus);
    
    // Show a loading indicator for the specific row
    const row = document.querySelector(`tr.clickable-row [data-id="${productId}"]`).closest('tr');
    if (row) {
        row.style.opacity = '0.5';
    }
    
    // Send request
    fetch('/prog23/lagerhanteringssystem/admin/search.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest' // Mark as AJAX request
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Don't show success message anymore
            
            // Just refresh the table content
            refreshTableContent();
            
            // Call the callback function if provided
            if (typeof callback === 'function') {
                callback(true);
            }
        } else {
            // Still show error message if there's a problem
            if (data.message) {
                showMessage('Error: ' + data.message, 'danger');
            }
            
            // Reset opacity of the row
            if (row) {
                row.style.opacity = '1';
            }
            
            // Call the callback function with false if provided
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        
        // Reset opacity of the row
        if (row) {
            row.style.opacity = '1';
        }
        
        // Call the callback function with false if provided
        if (typeof callback === 'function') {
            callback(false);
        }
    });
  }
  
  // Update URL parameters without reloading the page
  function updateUrlParams(params) {
      const url = new URL(window.location);
      
      // Remove all existing parameters
      [...url.searchParams.keys()].forEach(key => {
          url.searchParams.delete(key);
      });
      
      // Add new parameters
      Object.keys(params).forEach(key => {
          if (params[key]) url.searchParams.set(key, params[key]);
      });
      
      // Update URL without reload
      window.history.pushState({}, '', url);
  }
  
  // Perform batch action on selected items in lists
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
              performListsSearch();
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