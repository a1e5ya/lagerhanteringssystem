/**
 * forms.js - Form handling functionality
 * Contains form submission handling, validation, and data processing
 */

// Handle form submission via AJAX
$(document)
  .off('submit', '#add-item-form')
  .on('submit', '#add-item-form', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const form = $(this);
      
      // If we have a single author in the input fields and none in our list
      // add the current author to the list
      if (window.authors && window.authors.length === 0) {
          const firstName = $('#author-first').val().trim();
          const lastName = $('#author-last').val().trim();
          
          if (firstName || lastName) {
              window.authors.push({
                  first_name: firstName,
                  last_name: lastName
              });
              
              // Update hidden field with JSON data
              $('#authors-json').val(JSON.stringify(window.authors));
          }
      }

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
                      
                      // Clear authors list
                      if (window.authors) {
                          window.authors = [];
                          $('#authors-list').empty();
                          $('#authors-json').val('');
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

// Initialize authors management for add product
function initializeAuthorsManagement() {
    // Store for multiple authors
    window.authors = [];
    
    // Add author to the list
    $(document).on('click', '#add-author-to-list', function(e) {
        e.preventDefault();
        
        const firstName = $('#author-first').val().trim();
        const lastName = $('#author-last').val().trim();
        
        // Basic validation
        if (!firstName && !lastName) {
            alert('Please enter at least first or last name for the author');
            return;
        }
        
        // Add to array
        window.authors.push({
            first_name: firstName,
            last_name: lastName
        });
        
        // Add to visual list
        const authorElement = $(`
            <div class="author-item mb-2 d-flex align-items-center">
                <span class="me-2">${firstName} ${lastName}</span>
                <button type="button" class="btn btn-sm btn-outline-danger remove-author" data-index="${window.authors.length - 1}">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        `);
        
        $('#authors-list').append(authorElement);
        
        // Clear inputs
        $('#author-first').val('');
        $('#author-last').val('');
        
        // Update hidden field with JSON data
        $('#authors-json').val(JSON.stringify(window.authors));
    });
    
    // Remove author from the list
    $(document).on('click', '.remove-author', function() {
        const index = $(this).data('index');
        
        // Remove from array
        window.authors.splice(index, 1);
        
        // Re-render entire list (to handle indices correctly)
        $('#authors-list').empty();
        window.authors.forEach((author, idx) => {
            const authorElement = $(`
                <div class="author-item mb-2 d-flex align-items-center">
                    <span class="me-2">${author.first_name} ${author.last_name}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-author" data-index="${idx}">
                        <i class="bi bi-trash"></i> Remove
                    </button>
                </div>
            `);
            $('#authors-list').append(authorElement);
        });
        
        // Update hidden field with JSON data
        $('#authors-json').val(JSON.stringify(window.authors));
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
    
    // Initialize authors management
    initializeAuthorsManagement();
}

// Initialize add author page
function initializeAddAuthor() {
    // Set up author form submission
    const authorForm = document.getElementById('add-author-form');
    if (authorForm) {
        $(authorForm).off('submit').on('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const form = $(this);
            
            $.ajax({
                type: 'POST',
                url: '/prog23/lagerhanteringssystem/admin/addauthor.php',
                data: form.serialize(),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    try {
                        const data = typeof response === 'object' ? response : JSON.parse(response);
                        
                        if (data.success) {
                            $('#author-message-container').html(`<div class='alert alert-success'>${data.message}</div>`);
                            $('#author-message-container').show();
                            form[0].reset();
                        } else {
                            $('#author-message-container').html(`<div class='alert alert-danger'>${data.message}</div>`);
                            $('#author-message-container').show();
                        }
                    } catch (e) {
                        console.error('Error:', e);
                        $('#author-message-container').html(`<div class='alert alert-danger'>Error processing the response.</div>`);
                        $('#author-message-container').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    $('#author-message-container').html(`<div class='alert alert-danger'>An error occurred.</div>`);
                    $('#author-message-container').show();
                }
            });
        });
    }
}

// Helper function to show messages
function showMessage(message, type) {
    // First try specific message containers
    let container = $('#message-container');
    
    // If not found, try author message container
    if (container.length === 0) {
        container = $('#author-message-container');
    }
    
    // If still not found, create a general message container
    if (container.length === 0) {
        container = $('<div id="message-container"></div>');
        container.prependTo('#tabs-content');
    }
    
    container.html(`<div class="alert alert-${type}">${message}</div>`);
    container.show();
    
    // Scroll to message
    $('html, body').animate({
        scrollTop: container.offset().top - 100
    }, 200);
    
    // Auto hide after 5 seconds
    setTimeout(function() {
        container.fadeOut(500);
    }, 5000);
}