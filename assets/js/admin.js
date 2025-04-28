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

// Handle form submission via AJAX
$(document).off('submit', '#add-item-form').on('submit', '#add-item-form', function(e) {
    e.preventDefault();
    e.stopPropagation(); // Stop the event from bubbling up
    const form = $(this);
    
    $.ajax({
        type: 'POST',
        url: '/prog23/lagerhanteringssystem/admin/addproduct.php', // Use the full path
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


// Intercept delete links
$(document).on('click', 'a[href*="action=delete"]', function(e) {
    e.preventDefault(); // Stop the default link behavior
    
    // Extract data from the href attribute
    const href = $(this).attr('href');
    const urlParams = new URLSearchParams(href.split('?')[1]);
    const id = urlParams.get('id');
    const type = urlParams.get('type');
    
    // Skip confirmation if the link already has an onclick attribute
    if ($(this).attr('onclick')) {
        // Use the original confirmation dialog result
        const originalConfirm = $(this).attr('onclick');
        // Extract the confirmation action instead of adding our own
        
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
                    $(`a[href*="id=${id}"][href*="type=${type}"]`).closest('tr').remove();
                } else {
                    // Show error message
                    $('#message-container').html(`<div class="alert alert-danger">${response.message}</div>`);
                    $('#message-container').show();
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr.responseText);
                $('#message-container').html('<div class="alert alert-danger">An error occurred during deletion.</div>');
                $('#message-container').show();
            }
        });
    }
});