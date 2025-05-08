/**
 * admin.js - Core admin functionality
 * Contains tab management, initial page setup, and core UI functionality
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
        case "tabledatamanagement":
            url = "tabledatamanagement.php";
            break;
        case "lists":
            url = "lists.php";
            break;
        default:
            return; // Exit if no valid tab
    }

    // Load the content via AJAX
    $("#tabs-content").load(
      "/prog23/lagerhanteringssystem/admin/" + url,
      function(response, status, xhr) {
          if (status == "error") {
              console.log("Error loading content: " + xhr.status + " " + xhr.statusText);
          } else {
              // After content is loaded, initialize specific functionality
              if (tab === "search") {
                  // Only attach event handlers to the search form, don't reinitialize the entire search
                  attachSearchEventHandlers();
                  attachActionListeners();
                  makeRowsClickable();
              } else if (tab === "addproduct") {
                  // Your existing code for addproduct tab
                  setupAutocomplete("author-first", "suggest-author-first", "authorFirst");
                  setupAutocomplete("author-last", "suggest-author-last", "authorLast");
                  setupAutocomplete("item-publisher", "suggest-publisher", "publisher");
                  // Set up image preview
                  setupImagePreview();
              } else if (tab === "lists") {
                  // Your existing code for lists tab
                  initializeLists();
              } else if (tab === "tabledatamanagement") {
                  // Any initialization for table data management
              } else if (tab === "addauthor") {
                  // Any initialization for add author
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
    
    // Ctrl+1 to Ctrl+5 for tabs
    if (e.ctrlKey && e.key >= '1' && e.key <= '5') {
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
    
    // Check if error is due to session timeout
    if (jqXHR.status === 401) {
      alert('Din session har gått ut. Du kommer att omdirigeras till inloggningssidan.');
      window.location.href = '/prog23/lagerhanteringssystem/login.php';
    }
  });

  // Main document ready event handlers
  document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the addproduct tab
    const addProductForm = document.getElementById('add-item-form');
    if (addProductForm) {
      console.log('Add product form detected, setting up autocomplete');
      setupAutocomplete('author-first', 'suggest-author-first', 'authorFirst');
      setupAutocomplete('author-last', 'suggest-author-last', 'authorLast');
      setupAutocomplete('item-publisher', 'suggest-publisher', 'publisher');
      
      // Initialize authors management
      initializeAuthorsManagement();
    }
    
    // Check if we're on the search tab
    const searchForm = document.getElementById('admin-search-form');
    if (searchForm) {
      initializeAdminSearch();
      makeRowsClickable();
    }
    
    // Check if we're on the lists tab
    const listsForm = document.getElementById('lists-search-form');
    if (listsForm) {
      initializeLists();
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

  // Add custom CSS for clickable rows
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
  `;
  document.head.appendChild(style);
});