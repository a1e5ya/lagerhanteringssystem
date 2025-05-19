/**
 * data-operations.js - Data Operations Functionality
 * Contains CRUD functions for products, authors, etc. and state management
 */

// Update product sale status via AJAX
function changeProductSaleStatus(productId, newStatus, callback) {
    // Create form data for the request
    const formData = new FormData();
    formData.append('action', 'change_status');
    formData.append('product_id', productId);
    formData.append('status', newStatus);
    
    // Send request
    fetch(BASE_URL + '/admin/search.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof callback === 'function') {
                callback(true);
            }
        } else {
            // Show error message
            showMessage('Error: ' + data.message, 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
    });
  }
  
  // Delete product via AJAX
  function deleteProduct(productId, callback) {
    if (!confirm('Är du säker på att du vill ta bort denna produkt? Denna åtgärd kan inte ångras!')) {
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('delete-item', '1');
    
    // Send request
    fetch(BASE_URL + '/admin/adminsingleproduct.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            if (typeof callback === 'function') {
                callback(true);
            }
            // Redirect to admin page if needed
            window.location.href = BASE_URL + '/admin.php?tab=search';
        } else {
            showMessage(data.message || 'Ett fel inträffade', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
    });
  }
  
  // Edit product by ID via AJAX
  function editProduct(productId, formData, callback) {
    // Validate required fields
    if (!formData.get('edit-title')) {
        showMessage('Titel är ett obligatoriskt fält.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Append action identifier
    formData.append('save-item', '1');
    
    // Send request
    fetch(BASE_URL + '/admin/adminsingleproduct.php?id=' + productId, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            if (typeof callback === 'function') {
                callback(true, data);
            }
        } else {
            showMessage(data.message || 'Ett fel inträffade', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
    });
  }
  
  // Add new product via AJAX
  function addProduct(formData, callback) {
    // Validate required fields
    if (!formData.get('title')) {
        showMessage('Titel är ett obligatoriskt fält.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Send request
    fetch(BASE_URL + '/admin/addproduct.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            if (typeof callback === 'function') {
                callback(true, data);
            }
        } else {
            showMessage(data.message || 'Ett fel inträffade', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
    });
  }
  
  // Get product by ID via AJAX
  function getProductById(productId, callback) {
    fetch(BASE_URL + '/admin/get_item.php?type=product&id=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof callback === 'function') {
                    callback(true, data.item);
                }
            } else {
                showMessage('Kunde inte hämta produkten: ' + (data.message || 'Ett fel inträffade'), 'danger');
                if (typeof callback === 'function') {
                    callback(false);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        });
  }
  
  // Add a new author via AJAX
  function addAuthor(firstName, lastName, callback) {
    // Validate input
    if (!firstName && !lastName) {
        showMessage('Förnamn eller efternamn krävs', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    
    // Send request
    fetch(BASE_URL + '/admin/addauthor.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            if (typeof callback === 'function') {
                callback(true, data);
            }
        } else {
            showMessage(data.message || 'Ett fel inträffade', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
    });
  }
  
  // Edit an author via AJAX
  function editAuthor(authorId, firstName, lastName, callback) {
    // Validate input
    if (!firstName && !lastName) {
        showMessage('Förnamn eller efternamn krävs', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Create data object
    const data = {
        id: authorId,
        type: 'author',
        first_name: firstName,
        last_name: lastName
    };
    
    // Send request
    $.ajax({
        type: 'POST',
        url: BASE_URL + '/admin/edit_item.php',
        data: data,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showMessage(response.message, 'success');
                if (typeof callback === 'function') {
                    callback(true, response);
                }
            } else {
                showMessage(response.message || 'Ett fel inträffade', 'danger');
                if (typeof callback === 'function') {
                    callback(false);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    });
  }
  
  // Delete an item (author, category, shelf, etc.) via AJAX
  function deleteItem(id, type, callback) {
    if (!confirm(`Är du säker på att du vill ta bort denna ${type}?`)) {
        return;
    }
    
    // Send request
    $.ajax({
        type: 'POST',
        url: BASE_URL + '/admin/delete_item.php',
        data: {
            id: id,
            type: type
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showMessage(response.message, 'success');
                if (typeof callback === 'function') {
                    callback(true, response);
                }
            } else {
                showMessage(response.message || 'Ett fel inträffade', 'danger');
                if (typeof callback === 'function') {
                    callback(false);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            showMessage('Ett fel inträffade. Försök igen senare.', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
        }
    });
  }