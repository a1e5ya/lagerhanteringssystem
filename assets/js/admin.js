/**
 * admin.js - Core admin functionality
 * Contains tab management, initial page setup, and core UI functionality
 * Clean version without client-side access control - relies on server-side authentication
 */

$(document).ready(function() {
  // Load the initial content based on the URL parameter
  const urlParams = new URLSearchParams(window.location.search);
  const initialTab = urlParams.get("tab") || "search";
  loadTabContent(initialTab);

  // Handle tab clicks
  $(".nav-link").on("click", function(e) {
      e.preventDefault(); // Prevent default anchor behavior
      const tab = $(this).data("tab"); // Get the tab name
      loadTabContent(tab);
  });

  function loadTabContent(tab) {
    let url = "";

    switch (tab) {
        case "search":
            url = "search.php";
            break;
        case "addproduct":
            url = "addproduct.php";
            break;
        case "addauthor":
            url = "addauthor.php";
            break;
        case "lists":
            url = "lists.php";
            break;
        case "productlog":
            url = "productlog.php";
            break;
        case "tabledatamanagement":
            url = "tabledatamanagement.php";
            break;
        case "newsletter":
            url = "subscribers.php";
            break;
        default:
            return; // Exit if no valid tab
    }

    // Load the content via AJAX
    $("#tabs-content").load(
      BASE_URL + "/admin/" + url,
      function(response, status, xhr) {
          if (status == "error") {
              console.log("Error loading content: " + xhr.status + " " + xhr.statusText);
              
              // Handle specific error cases
              if (xhr.status === 403) {
                  $("#tabs-content").html('<div class="alert alert-danger">Du har inte behörighet att komma åt denna funktion.</div>');
              } else if (xhr.status === 401) {
                  alert('Din session har gått ut. Du kommer att omdirigeras till inloggningssidan.');
                  window.location.href = BASE_URL + '/login.php';
              } else {
                  $("#tabs-content").html('<div class="alert alert-danger">Ett fel inträffade vid laddning av innehållet.</div>');
              }
          } else {
              // After content is loaded, initialize specific functionality
              if (tab === "search") {
                  // Initialize search functionality
                  attachSearchEventHandlers();
                  attachActionListeners();
                  makeRowsClickable();
              } else if (tab === "addproduct") {
                  // Initialize add product functionality
                  setupAutocomplete("author-name", "suggest-author", "author");
                  setupAutocomplete("item-publisher", "suggest-publisher", "publisher");
                  setupImagePreview();
              } else if (tab === "productlog") {
                  // Initialize product log functionality
                  console.log('Product log tab loaded and initialized');
                  
                  // The productlog.php contains its own initialization script,
                  // but we can add any additional setup here if needed
                  
                  // Ensure any jQuery event handlers are properly attached
                  if (typeof loadProductLog === 'function') {
                      // If the function exists, call it to load initial data
                      loadProductLog();
                  }
                  
                  // Make sure any dynamically loaded content has proper event handlers
                  setTimeout(function() {
                      // Re-attach any global event handlers that might be needed
                      if (typeof attachActionListeners === 'function') {
                          attachActionListeners();
                      }
                  }, 100);
                  
              } else if (tab === "newsletter") {
                  // Initialize newsletter subscribers functionality
                  console.log('Newsletter subscribers tab loaded and initialized');
                  
                  // The newsletter.php contains its own initialization script
                  
                  // Ensure any jQuery event handlers are properly attached
                  if (typeof loadSubscribers === 'function') {
                      // If the function exists, call it to load initial data
                      loadSubscribers();
                  }
                  
                  if (typeof loadSubscriberStats === 'function') {
                      // Load subscriber statistics
                      loadSubscriberStats();
                  }
                  
                  // Make sure any dynamically loaded content has proper event handlers
                  setTimeout(function() {
                      // Re-attach any global event handlers that might be needed
                      if (typeof attachActionListeners === 'function') {
                          attachActionListeners();
                      }
                  }, 100);
                  
              } else if (tab === "lists") {
                  
              } else if (tab === "tabledatamanagement") {
                  
              } else if (tab === "addauthor") {
                  
              }
          }
      }
    );

    // Update active class for tabs
    $(".nav-link").removeClass("active");
    $('.nav-link[data-tab="' + tab + '"]').addClass("active");

    // Update the URL to reflect the current tab
    window.history.pushState(null, "", `?tab=${tab}`);
  }

  // Handle keyboard shortcuts for tabs
  document.addEventListener('keydown', function(e) {
    // Only handle keyboard shortcuts when not in input fields
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
      return;
    }
    
    // Ctrl+1 to Ctrl+7 for tabs (updated to include newsletter)
    if (e.ctrlKey && e.key >= '1' && e.key <= '7') {
      e.preventDefault();
      const tabIndex = parseInt(e.key) - 1;
      const tabs = document.querySelectorAll('.nav-link');
      
      if (tabs[tabIndex]) {
        tabs[tabIndex].click();
      }
    }
  });

  // Set up global error handler for AJAX requests
  $(document).ajaxError(function(event, jqXHR, settings, thrownError) {
    console.error('Global AJAX error:', thrownError || jqXHR.statusText);
    
    // Check if error is due to session timeout or permissions
    if (jqXHR.status === 401) {
      alert('Din session har gått ut. Du kommer att omdirigeras till inloggningssidan.');
      window.location.href = BASE_URL + '/login.php';
    } else if (jqXHR.status === 403) {
      showMessage('Du har inte behörighet att utföra denna åtgärd.', 'danger');
    }
  });

  // Main document ready event handlers
  document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the addproduct tab
    const addProductForm = document.getElementById('add-item-form');
    if (addProductForm) {
      console.log('Add product form detected, setting up autocomplete');
      setupAutocomplete("author-name", "suggest-author", "author");
      setupAutocomplete("item-publisher", "suggest-publisher", "publisher");
      
      // Initialize authors management
      if (typeof initializeAuthorsManagement === 'function') {
        initializeAuthorsManagement();
      }
    }
    
    // Check if we're on the search tab
    const searchForm = document.getElementById('admin-search-form');
    if (searchForm) {
      if (typeof initializeAdminSearch === 'function') {
        initializeAdminSearch();
      }
      makeRowsClickable();
    }
    
    // Check if we're on the product log tab
    const productLogForm = document.getElementById('log-filter-form');
    if (productLogForm) {
      console.log('Product log form detected, ensuring initialization');
      
      // Make sure the product log functionality is properly initialized
      if (typeof loadProductLog === 'function') {
        setTimeout(function() {
          loadProductLog();
        }, 100);
      }
    }
    
    // Check if we're on the newsletter subscribers tab
    const newsletterForm = document.getElementById('subscriber-filter-form');
    if (newsletterForm) {
      console.log('Newsletter subscribers form detected, ensuring initialization');
      
      // Make sure the newsletter functionality is properly initialized
      if (typeof loadSubscribers === 'function') {
        setTimeout(function() {
          loadSubscribers();
        }, 100);
      }
      
      if (typeof loadSubscriberStats === 'function') {
        setTimeout(function() {
          loadSubscriberStats();
        }, 100);
      }
    }
    
    // Handle image upload preview for all image upload fields
    document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
      input.addEventListener('change', function() {
        // Find the associated preview element
        const previewId = this.getAttribute('data-preview');
        if (!previewId) return;
        
        const preview = document.getElementById(previewId);
        if (!preview) return;
        
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.src = e.target.result;
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
    });
  });

  // Add custom CSS for clickable rows and other UI elements
  const style = document.createElement('style');
  style.textContent = `
    .clickable-row {
      cursor: pointer;
      transition: background-color 0.2s;
    }
    .clickable-row:hover {
      background-color: rgba(0, 123, 255, 0.1);
    }
    .suggest-box {
      position: absolute;
      background: white;
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      z-index: 1000;
      width: 100%;
      max-height: 200px;
      overflow-y: auto;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .author-item {
      padding: 0.5rem;
      background-color: #f8f9fa;
      border-radius: 0.25rem;
    }
    .backup-hidden {
      opacity: 0.5;
      text-decoration: line-through;
    }
    .toggle-icon {
      transition: transform 0.3s;
      font-size: 1.2rem;
    }
    .toggle-icon.rotated {
      transform: rotate(180deg);
    }
  `;
  document.head.appendChild(style);

  // Helper function to show messages (if not already defined)
  if (typeof showMessage === 'undefined') {
    window.showMessage = function(message, type = 'info') {
      const messageContainer = $('#message-container');
      if (messageContainer.length) {
        const alert = $('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                       '</div>');
        
        messageContainer.append(alert);
        messageContainer.show();
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
          alert.alert('close');
        }, 5000);
      } else {
        // Fallback to alert if no message container
        alert(message);
      }
    };
  }

  // Helper function to make table rows clickable (if not already defined)
  if (typeof makeRowsClickable === 'undefined') {
    window.makeRowsClickable = function() {
      $(document).off('click', '.clickable-row');
      $(document).on('click', '.clickable-row', function() {
        const productId = $(this).data('product-id');
        if (productId) {
          window.open(BASE_URL + '/admin/adminsingleproduct.php?id=' + productId, '_blank');
        }
      });
    };
  }
});