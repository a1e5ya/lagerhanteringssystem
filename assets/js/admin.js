$(document).ready(function() {
    // Load the initial content based on the URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab') || 'search';
    loadTabContent(initialTab);

    // Handle tab clicks
    $('.nav-link').on('click', function(e) {
        e.preventDefault(); // Prevent default anchor behavior
        const tab = $(this).data('tab'); // Get the tab name
        loadTabContent(tab); // Load corresponding content
    });

    function loadTabContent(tab) {
        let url = '';

        switch (tab) {
            case 'search':
                url = 'search.php';
                break;
            case 'addproduct':
                url = 'addproduct.php';
                break;
            case 'addauthor':
                url = 'addauthor.php';
                break;
            case 'tabledatamanagement':
                url = 'tabledatamanagement.php';
                break;
            case 'lists':
                url = 'lists.php';
                break;
            default:
                return; // Exit if no valid tab
        }

        // Load the content via AJAX
        $('#tabs-content').load('/prog23/lagerhanteringssystem/admin/' + url, function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading content: " + xhr.status + " " + xhr.statusText);
            }
        });

        // Update active class for tabs
        $('.nav-link').removeClass('active');
        $('.nav-link[data-tab="' + tab + '"]').addClass('active');

        // Update the URL to reflect the current tab
        window.history.pushState(null, '', `?tab=${tab}`);
    }

//  form submission via AJAX for add product
$(document).off('submit', '#add-item-form').on('submit', '#add-item-form', function(e) {
    e.preventDefault();
    e.stopPropagation(); // Stop the event from bubbling up
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
                    $('#message-container').html(`<div class='alert alert-success'>${data.message}</div>`);
                    $('#message-container').show();
                    
                    // Clear the form
                    form[0].reset();
                    
                    // Reset image preview if it exists
                    if ($('#new-item-image').length) {
                        $('#new-item-image').attr('src', 'assets/images/src-book.webp');
                    }
                } else {
                    // Display error message
                    $('#message-container').html(`<div class='alert alert-danger'>${data.message}</div>`);
                    $('#message-container').show();
                }
            } catch (e) {
                console.error("Error parsing response:", e);
                console.error("Raw response:", response);
                $('#message-container').html(`<div class='alert alert-danger'>Error processing the server response. Check console for details.</div>`);
                $('#message-container').show();
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.error("Response Text:", xhr.responseText);
            $('#message-container').html(`<div class='alert alert-danger'>An error occurred. Please try again.</div>`);
            $('#message-container').show();
        }
    });
});
});


//  delete button functionality

// Single event handler for delete functionality
$(document).off('click', '.delete-item').on('click', '.delete-item', function(e) {
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
                    $('#message-container').html(`<div class="alert alert-success">${response.message}</div>`);
                    $('#message-container').show();
                    
                    // Remove the row from the table
                    $button.closest('tr').fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    // Show error message
                    $('#message-container').html(`<div class="alert alert-danger">${response.message}</div>`);
                    $('#message-container').show();
                    
                    // Re-enable the button
                    $button.prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                $('#message-container').html('<div class="alert alert-danger">An error occurred during deletion.</div>');
                $('#message-container').show();
                
                // Re-enable the button
                $button.prop('disabled', false);
            }
        });
    }
});

// Modal

// Handle edit link clicks
$(document).on('click', '.edit-item', function(e) {
    e.preventDefault();
    
    // Get data attributes
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
                $('#message-container').html(`<div class="alert alert-success">${response.message}</div>`);
                $('#message-container').show();
                
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


// author form submission via AJAX
$(document).off('submit', '#add-author-form').on('submit', '#add-author-form', function(e) {
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
                // Check if response is already an object
                const data = typeof response === 'object' ? response : JSON.parse(response);
                
                if (data.success) {
                    // Display success message
                    $('#message-container').html(`<div class='alert alert-success'>${data.message}</div>`);
                    $('#message-container').show();
                    
                    // Clear the form
                    form[0].reset();
                } else {
                    // Display error message
                    $('#message-container').html(`<div class='alert alert-danger'>${data.message}</div>`);
                    $('#message-container').show();
                }
            } catch (e) {
                console.error("Error parsing response:", e);
                console.error("Raw response:", response);
                $('#message-container').html(`<div class='alert alert-danger'>Error processing the server response. Check console for details.</div>`);
                $('#message-container').show();
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.error("Response Text:", xhr.responseText);
            $('#message-container').html(`<div class='alert alert-danger'>An error occurred. Please try again.</div>`);
            $('#message-container').show();
        }
    });
});