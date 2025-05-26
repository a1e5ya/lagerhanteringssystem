/**
 * ui-components.js - UI Component Functionality
 * Contains autocomplete, image preview, modal dialog, and table interactions
 */

// Function to set up image preview
function setupImagePreview() {
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
fetch(BASE_URL + `/admin/autocomplete.php?type=${type}&query=${encodeURIComponent(query)}`)              .then((response) => {
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
  

    
  // Modal edit functionality for non-author items only
  $(document).on('click', '.edit-item:not([data-type="author"])', function(e) {
      e.preventDefault();
      
      const id = $(this).data('id');
      const type = $(this).data('type');
      const name = $(this).data('name');
      
      $('#edit-item-id').val(id);
      $('#edit-item-type').val(type);
      $('#edit-item-name').val(name);
      
      // Capitalize the first letter of the type (category, publisher, etc.)
      $('#editItemModalLabel').text('Redigera ' + type.charAt(0).toUpperCase() + type.slice(1));
      
      $('#editItemModal').modal('show');
  });
  
  // Handle save button click for non-author items only
  $(document).on('click', '#save-edit', function() {
      const id = $('#edit-item-id').val();
      const type = $('#edit-item-type').val();
      const name = $('#edit-item-name').val().trim();
      
      if (!name) {
          alert('Ange ett namn.');
          return;
      }
      
      const postData = {
          id: id,
          type: type,
          action: 'edit',
          name: name
      };
      
      $.ajax({
          type: 'POST',
          url: BASE_URL + '/admin/database_handler.php',
          data: postData,
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                  showMessage(response.message, 'success');
                  $('#editItemModal').modal('hide');
                  
                  // Update the table row
                  const rowSelector = `a.edit-item[data-id="${id}"][data-type="${type}"]`;
                  const row = $(rowSelector).closest('tr');
                  
                  row.find('td:nth-child(2)').text(name);
                  $(rowSelector).data('name', name);
              } else {
                  alert(`Fel: ${response.message}`);
              }
          },
          error: function(xhr) {
              console.error('Error:', xhr.responseText);
              alert('Ett fel inträffade. Försök igen.');
          }
      });
  });

  // Author-specific edit functionality
  $(document).on('click', '.edit-item[data-type="author"]', function(e) {
      e.preventDefault();
      
      const id = $(this).data('id');
      const authorName = $(this).data('author-name');
      
      $('#edit-author-id').val(id);
      $('#edit-author-name').val(authorName);
      
      $('#editAuthorModal').modal('show');
  });

  // Handle save button click for author
  $(document).on('click', '#save-author-edit', function() {
      const id = $('#edit-author-id').val();
      const authorName = $('#edit-author-name').val().trim();
      
      if (!authorName) {
          alert('Författarnamn krävs.');
          return;
      }
      
      const postData = {
          id: id,
          type: 'author',
          action: 'edit',
          author_name: authorName
      };
      
      $.ajax({
          type: 'POST',
          url: BASE_URL + '/admin/database_handler.php',
          data: postData,
          dataType: 'json',
          success: function(response) {
              if (response.success) {
                  showMessage(response.message, 'success');
                  $('#editAuthorModal').modal('hide');
                  
                  // Update the table row
                  const rowSelector = `a.edit-item[data-id="${id}"][data-type="author"]`;
                  const row = $(rowSelector).closest('tr');
                  
                  row.find('td:nth-child(2)').text(authorName);
                  $(rowSelector).data('author-name', authorName);
              } else {
                  alert(`Fel: ${response.message}`);
              }
          },
          error: function(xhr) {
              console.error('Error:', xhr.responseText);
              alert('Ett fel inträffade. Försök igen.');
          }
      });
  });
  
  // Attach action listeners to buttons in admin search results
  function attachActionListeners() {
    // Quick sell button click
    document.querySelectorAll('.quick-sell').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent row click event from firing
            const productId = this.getAttribute('data-id');
            
            changeProductStatus(productId, 2, function(success) {
                if (success) performAdminSearch();
            }); // 2 = Sold
        });
    });
    
    // Quick return button click
    document.querySelectorAll('.quick-return').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent row click event from firing
            const productId = this.getAttribute('data-id');
            
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
  
  // Delete button functionality - separate handlers for authors and other items
  // Author-specific delete functionality
  $(document).on('click', '.delete-item[data-type="author"]', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const id = $(this).data('id');
      const $button = $(this);

      if (confirm('Är du säker på att du vill ta bort denna författare?')) {
          $button.prop('disabled', true);

          $.ajax({
              type: 'POST',
              url: BASE_URL + '/admin/database_handler.php',
              data: {
                  id: id,
                  type: 'author',
                  action: 'delete'
              },
              dataType: 'json',
              success: function(response) {
                  if (response.success) {
                      showMessage(response.message, 'success');
                      $button.closest('tr').fadeOut(300, function() {
                          $(this).remove();
                      });
                  } else {
                      showMessage(response.message, 'danger');
                      $button.prop('disabled', false);
                  }
              },
              error: function(xhr) {
                  console.error('Error:', xhr.responseText);
                  showMessage('Ett fel inträffade vid borttagning.', 'danger');
                  $button.prop('disabled', false);
              }
          });
      }
  });

  // Non-author delete functionality
  $(document)
      .off('click', '.delete-item:not([data-type="author"])')
      .on('click', '.delete-item:not([data-type="author"])', function(e) {
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
  
              $.ajax({
                  type: 'POST',
                  url: BASE_URL + '/admin/database_handler.php',
                  data: {
                      id: id,
                      type: type,
                      action: 'delete' // Required for database_handler.php
                  },
                  dataType: 'json',
                  success: function(response) {
                      if (response.success) {
                          showMessage(response.message, 'success');
  
                          // Remove the row from the table
                          $button.closest('tr').fadeOut(300, function() {
                              $(this).remove();
                          });
                      } else {
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