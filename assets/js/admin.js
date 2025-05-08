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
                // Initialize tab-specific functionality
                if (tab === "search") {
                    initializeAdminSearch();
                    makeRowsClickable(); 
                } else if (tab === "addproduct") {
                    initializeAddProduct();
                } else if (tab === "lists") {
                    initializeLists();
                }
            }
        }
    );

      // Update active class for tabs
      $(".nav-link").removeClass("active");
      $('.nav-link[data-tab="' + tab + '"]').addClass("active");

      // Update the URL to reflect the current tab
      updateUrlParams({ tab: tab });
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
  
  // Add change event listener to category dropdown
  categoryFilterSelect.addEventListener('change', function() {
      performAdminSearch();
  });
  
  // Attach action listeners to buttons
  attachActionListeners();
  
  // Load all products by default (if no search params are set)
  if (!searchTermInput.value && (categoryFilterSelect.value === 'all' || !categoryFilterSelect.value)) {
    // Get all products with pagination
    const searchParams = {
        search: '',
        category: 'all',
        page: 1,
        limit: 20
    };
    
    // Fix the URL path here
    ajaxSearch('/prog23/lagerhanteringssystem/admin/search.php', 'admin', searchParams, document.getElementById('inventory-body'), function() {
        attachActionListeners();
        makeRowsClickable();
    });
  } else if (searchTermInput.value || categoryFilterSelect.value !== 'all') {
      // If search parameters exist, perform search
      performAdminSearch();
  }
}

  // Perform admin search
function performAdminSearch() {
  const targetElem = document.getElementById('inventory-body');
  
  // Get the current page from URL or default to 1
  const urlParams = new URLSearchParams(window.location.search);
  const currentPage = urlParams.get('page') || 1;
  
  const searchParams = {
      search: document.getElementById('search-term').value,
      category: document.getElementById('category-filter').value,
      page: currentPage,
      limit: 20 // Always limit to 20 items per page
  };
  
  ajaxSearch('/prog23/lagerhanteringssystem/admin/search.php', 'admin', searchParams, targetElem, function() {
      // Success callback
      attachActionListeners();
      makeRowsClickable();
      
      // Update pagination UI after search
      updatePaginationUI(currentPage);
      
      // Update URL without reloading
      updateUrlParams(Object.assign({}, searchParams, { tab: 'search' }));
  });
}

// Add this function to update the pagination links after AJAX load
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

  // Initialize add product page
  function initializeAddProduct() {
      setupAutocomplete('author-first', 'suggest-author-first', 'authorFirst');
      setupAutocomplete('author-last', 'suggest-author-last', 'authorLast');
      setupAutocomplete('item-publisher', 'suggest-publisher', 'publisher');
      
      // Set up image preview
      const imageUpload = document.getElementById('item-image-upload');
      const imagePreview = document.getElementById('new-item-image');
      
      if (imageUpload && imagePreview) {
          imageUpload.addEventListener('change', function() {
              if (this.files && this.files[0]) {
                  const reader = new FileReader();
                  reader.onload = function(e) {
                      imagePreview.src = e.target.result;
                  };
                  reader.readAsDataURL(this.files[0]);
              }
          });
      }
  }

  // Attach action listeners to buttons in admin search results
function attachActionListeners() {
  // Quick sell button click
  document.querySelectorAll('.quick-sell').forEach(button => {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          const productId = this.getAttribute('data-id');
          
          // Remove confirmation - just proceed with sale
          changeProductStatus(productId, 2, function(success) {
              if (success) performAdminSearch();
          }); // 2 = Sold
      });
  });
  
  // Quick return button click
  document.querySelectorAll('.quick-return').forEach(button => {
      button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          const productId = this.getAttribute('data-id');
          
          // Remove confirmation - just proceed with return
          changeProductStatus(productId, 1, function(success) {
              if (success) performAdminSearch();
          }); // 1 = Available
      });
  });
}

  // Attach action listeners for lists tab
  function attachListsActionListeners() {
      // Select all checkbox
      const selectAllCheckbox = document.getElementById('select-all');
      if (selectAllCheckbox) {
          selectAllCheckbox.addEventListener('change', function() {
              const isChecked = this.checked;
              document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                  checkbox.checked = isChecked;
              });
          });
      }
      
      // Batch action buttons
      document.querySelectorAll('.batch-action').forEach(button => {
          button.addEventListener('click', function(e) {
              e.preventDefault();
              const action = this.getAttribute('data-action');
              performBatchAction(action);
          });
      });
  }

  // Handle form submission via AJAX
  $(document)
      .off('submit', '#add-item-form')
      .on('submit', '#add-item-form', function(e) {
          e.preventDefault();
          e.stopPropagation();
          const form = $(this);

          $.ajax({
              type: 'POST',
              url: '/prog23/lagerhanteringssystem/admin/addproduct.php',
              data: new FormData(form[0]),
              processData: false,
              contentType: false,
              headers: {
                  'X-Requested-With': 'XMLHttpRequest'
              },
              success: function(response) {
                  try {
                      // Check if response is already an object
                      const data = typeof response === 'object' ? response : JSON.parse(response);

                      if (data.success) {
                          // Display success message
                          showMessage(data.message, 'success');

                          // Clear the form
                          form[0].reset();

                          // Reset image preview if it exists
                          if ($('#new-item-image').length) {
                              $('#new-item-image').attr('src', 'assets/images/src-book.webp');
                          }
                      } else {
                          // Display error message
                          showMessage(data.message, 'danger');
                      }
                  } catch (e) {
                      console.error('Error parsing response:', e);
                      console.error('Raw response:', response);
                      showMessage('Error processing the server response. Check console for details.', 'danger');
                  }
              },
              error: function(xhr, status, error) {
                  console.error('AJAX Error:', status, error);
                  console.error('Response Text:', xhr.responseText);
                  showMessage('An error occurred. Please try again.', 'danger');
              }
          });
      });

  // Delete button functionality
  $(document)
      .off('click', '.delete-item')
      .on('click', '.delete-item', function(e) {
          e.preventDefault();
          e.stopPropagation();

          // Get data from data attributes
          const id = $(this).data('id');
          const type = $(this).data('type');

          // Show confirmation
          if (confirm(`Are you sure you want to delete this ${type}?`)) {
              // Temporarily disable the button to prevent double-clicks
              const $button = $(this);
              $button.prop('disabled', true);

              // Send AJAX request
              $.ajax({
                  type: 'POST',
                  url: '/prog23/lagerhanteringssystem/admin/delete_item.php',
                  data: {
                      id: id,
                      type: type
                  },
                  dataType: 'json',
                  success: function(response) {
                      if (response.success) {
                          // Show success message
                          showMessage(response.message, 'success');

                          // Remove the row from the table
                          $button.closest('tr').fadeOut(300, function() {
                              $(this).remove();
                          });
                      } else {
                          // Show error message
                          showMessage(response.message, 'danger');

                          // Re-enable the button
                          $button.prop('disabled', false);
                      }
                  },
                  error: function(xhr) {
                      console.error('Error:', xhr.responseText);
                      showMessage('An error occurred during deletion.', 'danger');

                      // Re-enable the button
                      $button.prop('disabled', false);
                  }
              });
          }
      });

  // Modal edit functionality
  $(document).on('click', '.edit-item', function(e) {
      e.preventDefault();

      // Get data from data attributes
      const id = $(this).data('id');
      const type = $(this).data('type');
      const name = $(this).data('name');

      // Set modal title
      $('#editItemModalLabel').text('Edit ' + type.charAt(0).toUpperCase() + type.slice(1));

      // Fill form fields
      $('#edit-item-id').val(id);
      $('#edit-item-type').val(type);
      $('#edit-item-name').val(name);

      // Show the modal
      $('#editItemModal').modal('show');
  });

  // Handle save button click
  $(document).on('click', '#save-edit', function() {
      // Get form data
      const id = $('#edit-item-id').val();
      const type = $('#edit-item-type').val();
      const name = $('#edit-item-name').val();

      // Validate form
      if (!name.trim()) {
          alert('Please enter a name');
          return;
      }

      // Send AJAX request
      $.ajax({
          type: 'POST',
          url: '/prog23/lagerhanteringssystem/admin/edit_item.php',
          data: {
              id: id,
              type: type,
              name: name
          },
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                  // Show success message
                  showMessage(response.message, 'success');

                  // Update the row in the table
                  const rowSelector = `a.edit-item[data-id="${id}"][data-type="${type}"]`;
                  $(rowSelector).closest('tr').find('td:nth-child(2)').text(name);
                  $(rowSelector).data('name', name);

                  // Close the modal
                  $('#editItemModal').modal('hide');
              } else {
                  // Show error message in the modal
                  alert(`Error: ${response.message}`);
              }
          },
          error: function(xhr) {
              console.error('Error:', xhr.responseText);
              alert('An error occurred. Please try again.');
          }
      });
  });

  // Autocomplete functionality
  function setupAutocomplete(inputId, suggestBoxId, type) {
      const input = document.getElementById(inputId);
      const suggestBox = document.getElementById(suggestBoxId);

      if (!input || !suggestBox) {
          console.error(`Element not found: ${inputId} or ${suggestBoxId}`);
          return;
      }

      input.addEventListener("input", function() {
          const query = input.value.trim();
          if (query.length < 2) {
              suggestBox.innerHTML = "";
              suggestBox.style.display = "none";
              return;
          }

          // Use fetch API to get suggestions
          fetch(`admin/autocomplete.php?type=${type}&query=${encodeURIComponent(query)}`)
              .then((response) => {
                  if (!response.ok) {
                      throw new Error("Network response was not ok");
                  }
                  return response.json();
              })
              .then((data) => {
                  suggestBox.innerHTML = "";

                  if (data.length === 0) {
                      suggestBox.style.display = "none";
                      return;
                  }

                  // Create suggestion items
                  data.forEach((item) => {
                      const div = document.createElement("div");
                      div.textContent = item;
                      div.classList.add("list-group-item", "list-group-item-action");
                      div.style.cursor = "pointer";
                      div.style.padding = "0.5rem 1rem";
                      div.addEventListener("click", function() {
                          input.value = item;
                          suggestBox.innerHTML = "";
                          suggestBox.style.display = "none";
                      });
                      suggestBox.appendChild(div);
                  });

                  // Show the suggestion box
                  suggestBox.style.display = "block";
              })
              .catch((error) => {
                  console.error("Error fetching autocomplete data:", error);
                  suggestBox.style.display = "none";
              });
      });

      // Close suggestions when clicking outside
      document.addEventListener("click", function(e) {
          if (suggestBox && !suggestBox.contains(e.target) && e.target !== input) {
              suggestBox.innerHTML = "";
              suggestBox.style.display = "none";
          }
      });
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
});