/**
 * data-operations.js - Data Operations Functionality
 * Contains CRUD functions for products, authors, etc. and state management
 * Updated to use the unified database_handler.php for database operations
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

// NEW FUNCTIONS USING THE UNIFIED DATABASE_HANDLER.PHP

// Add a new database item (category, shelf, genre, language, condition)
function addDatabaseItem(type, data, callback) {
    // Validate input based on type
    if (!data || (type !== 'condition' && (!data.sv_name || !data.fi_name))) {
        showMessage('Required fields are missing', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('action', `add_${type}`);
    
    // Add appropriate fields based on type
    switch(type) {
        case 'category':
            formData.append('category_sv_name', data.sv_name);
            formData.append('category_fi_name', data.fi_name);
            break;
        case 'shelf':
            formData.append('shelf_sv_name', data.sv_name);
            formData.append('shelf_fi_name', data.fi_name);
            break;
        case 'genre':
            formData.append('genre_sv_name', data.sv_name);
            formData.append('genre_fi_name', data.fi_name);
            break;
        case 'language':
            formData.append('language_sv_name', data.sv_name);
            formData.append('language_fi_name', data.fi_name);
            break;
        case 'condition':
            formData.append('condition_sv_name', data.sv_name);
            formData.append('condition_fi_name', data.fi_name);
            formData.append('condition_code', data.code || '');
            formData.append('condition_description', data.description || '');
            break;
        default:
            showMessage('Invalid item type', 'danger');
            if (typeof callback === 'function') {
                callback(false);
            }
            return;
    }
    
    // Send request
    fetch(BASE_URL + '/admin/database_handler.php', {
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

// Edit a database item (category, shelf, genre, language, condition)
function editDatabaseItem(id, type, data, callback) {
    // Validate input
    if (!id || !type || !data || !data.sv_name || !data.fi_name) {
        showMessage('Required fields are missing', 'danger');
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'edit');
    formData.append('id', id);
    formData.append('type', type);
    formData.append('sv_name', data.sv_name);
    formData.append('fi_name', data.fi_name);
    
    // Add condition-specific fields if needed
    if (type === 'condition' && data.code) {
        formData.append('code', data.code);
        formData.append('description', data.description || '');
    }
    
    // Send request
    fetch(BASE_URL + '/admin/database_handler.php', {
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

// Delete a database item (category, shelf, genre, language, condition)
function deleteDatabaseItem(id, type, callback) {
    // Get user confirmation
    let typeName = '';
    switch(type) {
        case 'category': typeName = 'kategori'; break;
        case 'shelf': typeName = 'hyllplats'; break;
        case 'genre': typeName = 'genre'; break;
        case 'language': typeName = 'språk'; break;
        case 'condition': typeName = 'skick'; break;
        default: typeName = 'objekt';
    }
    
    if (!confirm(`Är du säker på att du vill ta bort denna ${typeName}?`)) {
        if (typeof callback === 'function') {
            callback(false);
        }
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    formData.append('type', type);
    
    // Send request
    fetch(BASE_URL + '/admin/database_handler.php', {
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

// Get a database item by ID (category, shelf, genre, language, condition)
function getDatabaseItem(id, type, callback) {
    // Create form data
    const formData = new FormData();
    formData.append('action', 'get');
    formData.append('id', id);
    formData.append('type', type);
    
    // Send request
    fetch(BASE_URL + '/admin/database_handler.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (typeof callback === 'function') {
                callback(true, data.item);
            }
        } else {
            showMessage(data.message || 'Kunde inte hämta data', 'danger');
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