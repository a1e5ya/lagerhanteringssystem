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
            initializeLists();
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
      window.location.href = BASE_URL + '/login.php';
    }
  });

  function initializeLists() {
    console.log("Initializing lists functionality");
  
    // Attach handlers to quick filter buttons
    $(document).on('click', '#list-no-price', function() {
      console.log("No price filter button clicked");
      
      try {
        // Direct AJAX for quick filter
        $.ajax({
          url: BASE_URL + '/admin/list_ajax_handler.php',
          type: 'POST',
          data: {
            action: 'get_filtered_products',
            no_price: true,
            page: 1,
            limit: 15
          },
          dataType: 'json',
          success: function(data) {
            if (data.success) {
              $('#lists-body').html(data.html);
              
              // Update pagination info
              $('#current-page').text(data.pagination.currentPage);
              $('#total-pages').text(data.pagination.totalPages);
              $('#total-count').text(data.pagination.totalResults);
              
              // Enable/disable pagination buttons
              $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
              $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
            }
          }
        });
        
        // Reset form fields
        $('#list-categories').val('');
        $('#list-genre').val('');
        $('#list-condition').val('');
        $('#list-status').val('all');
        $('#price-min').val('');
        $('#price-max').val('');
        $('#date-min').val('');
        $('#date-max').val('');
        $('#list-search').val('');
      } catch (error) {
        console.error("Error handling no price filter:", error);
      }
    });
    
    $(document).on('click', '#list-poor-condition', function() {
      console.log("Poor condition button clicked");
      
      try {
        // Direct AJAX for quick filter
        $.ajax({
          url: 'admin/list_ajax_handler.php',
          type: 'POST',
          data: {
            action: 'get_filtered_products',
            poor_condition: true,
            page: 1,
            limit: 15
          },
          dataType: 'json',
          success: function(data) {
            if (data.success) {
              $('#lists-body').html(data.html);
              
              // Update pagination info
              $('#current-page').text(data.pagination.currentPage);
              $('#total-pages').text(data.pagination.totalPages);
              $('#total-count').text(data.pagination.totalResults);
              
              // Enable/disable pagination buttons
              $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
              $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
            }
          }
        });
        
        // Reset form fields
        $('#list-categories').val('');
        $('#list-genre').val('');
        $('#list-condition').val('');
        $('#list-status').val('all');
        $('#price-min').val('');
        $('#price-max').val('');
        $('#date-min').val('');
        $('#date-max').val('');
        $('#list-search').val('');
      } catch (error) {
        console.error("Error handling poor condition filter:", error);
      }
    });
    
    $(document).on('change', '#shelf-selector', function() {
      console.log("Shelf selector changed");
      const shelfName = $(this).val();
      if (shelfName) {
        try {
          // Direct AJAX for shelf filter
          $.ajax({
            url: 'admin/list_ajax_handler.php',
            type: 'POST',
            data: {
              action: 'get_filtered_products',
              shelf: shelfName,
              page: 1,
              limit: 15
            },
            dataType: 'json',
            success: function(data) {
              if (data.success) {
                $('#lists-body').html(data.html);
                
                // Update pagination info
                $('#current-page').text(data.pagination.currentPage);
                $('#total-pages').text(data.pagination.totalPages);
                $('#total-count').text(data.pagination.totalResults);
                
                // Enable/disable pagination buttons
                $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
                $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
              }
            }
          });
          
          // Reset form fields
          $('#list-categories').val('');
          $('#list-genre').val('');
          $('#list-condition').val('');
          $('#list-status').val('all');
          $('#price-min').val('');
          $('#price-max').val('');
          $('#date-min').val('');
          $('#date-max').val('');
          $('#list-search').val('');
        } catch (error) {
          console.error("Error handling shelf filter:", error);
        }
      }
    });
    
    $(document).on('change keyup', '#year-threshold', function(event) {
      if (event.type === 'change' || event.keyCode === 13) {
        console.log("Year threshold entered");
        const yearThreshold = $(this).val();
        if (yearThreshold) {
          try {
            // Direct AJAX for year threshold filter
            $.ajax({
              url: 'admin/list_ajax_handler.php',
              type: 'POST',
              data: {
                action: 'get_filtered_products',
                year_threshold: yearThreshold,
                page: 1,
                limit: 15
              },
              dataType: 'json',
              success: function(data) {
                if (data.success) {
                  $('#lists-body').html(data.html);
                  
                  // Update pagination info
                  $('#current-page').text(data.pagination.currentPage);
                  $('#total-pages').text(data.pagination.totalPages);
                  $('#total-count').text(data.pagination.totalResults);
                  
                  // Enable/disable pagination buttons
                  $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
                  $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
                }
              }
            });
            
            // Reset form fields
            $('#list-categories').val('');
            $('#list-genre').val('');
            $('#list-condition').val('');
            $('#list-status').val('all');
            $('#price-min').val('');
            $('#price-max').val('');
            $('#date-min').val('');
            $('#date-max').val('');
            $('#list-search').val('');
          } catch (error) {
            console.error("Error handling year threshold filter:", error);
          }
        }
      }
    });
    
    // Fix for advanced filter button - direct implementation instead of calling potentially problematic function
    $(document).on('click', '#apply-filter-btn', function() {
      console.log("Advanced filter button clicked");
      
      try {
        // Get filter values directly from the form
        const filters = {
          category: $('#list-categories').val(),
          genre: $('#list-genre').val(),
          condition: $('#list-condition').val(),
          status: $('#list-status').val() || 'all',
          min_price: $('#price-min').val(),
          max_price: $('#price-max').val(),
          min_date: $('#date-min').val(),
          max_date: $('#date-max').val(),
          search: $('#list-search').val(),
          page: 1,
          limit: 15
        };
        
        console.log("Advanced filters:", filters);
        
        // Make the AJAX request directly
        $.ajax({
          url: 'admin/list_ajax_handler.php',
          type: 'POST',
          data: {
            action: 'get_filtered_products',
            ...filters
          },
          dataType: 'json',
          success: function(data) {
            console.log("Advanced filter response:", data);
            if (data.success) {
              $('#lists-body').html(data.html);
              
              // Update pagination info
              $('#current-page').text(data.pagination.currentPage);
              $('#total-pages').text(data.pagination.totalPages);
              $('#total-count').text(data.pagination.totalResults);
              
              // Enable/disable pagination buttons
              $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
              $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
            }
          },
          error: function(xhr, status, error) {
            console.error("Advanced filter AJAX error:", error);
            console.error("Response:", xhr.responseText);
          }
        });
      } catch (error) {
        console.error("Error in advanced filtering:", error);
      }
    });
    
    // Add pagination handlers
    $(document).on('click', '#prev-page-btn', function() {
      if (!$(this).prop('disabled')) {
        const currentPage = parseInt($('#current-page').text()) || 1;
        if (currentPage > 1) {
          // Make the AJAX request for previous page
          $.ajax({
            url: 'admin/list_ajax_handler.php',
            type: 'POST',
            data: {
              action: 'get_filtered_products',
              page: currentPage - 1,
              limit: 15
            },
            dataType: 'json',
            success: function(data) {
              if (data.success) {
                $('#lists-body').html(data.html);
                
                // Update pagination info
                $('#current-page').text(data.pagination.currentPage);
                $('#total-pages').text(data.pagination.totalPages);
                $('#total-count').text(data.pagination.totalResults);
                
                // Enable/disable pagination buttons
                $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
                $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
              }
            }
          });
        }
      }
    });
    
    $(document).on('click', '#next-page-btn', function() {
      if (!$(this).prop('disabled')) {
        const currentPage = parseInt($('#current-page').text()) || 1;
        const totalPages = parseInt($('#total-pages').text()) || 1;
        if (currentPage < totalPages) {
          // Make the AJAX request for next page
          $.ajax({
            url: 'admin/list_ajax_handler.php',
            type: 'POST',
            data: {
              action: 'get_filtered_products',
              page: currentPage + 1,
              limit: 15
            },
            dataType: 'json',
            success: function(data) {
              if (data.success) {
                $('#lists-body').html(data.html);
                
                // Update pagination info
                $('#current-page').text(data.pagination.currentPage);
                $('#total-pages').text(data.pagination.totalPages);
                $('#total-count').text(data.pagination.totalResults);
                
                // Enable/disable pagination buttons
                $('#prev-page-btn').prop('disabled', data.pagination.currentPage <= 1);
                $('#next-page-btn').prop('disabled', data.pagination.currentPage >= data.pagination.totalPages);
              }
            }
          });
        }
      }
    });
    
    console.log("Lists initialization complete");
  }

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