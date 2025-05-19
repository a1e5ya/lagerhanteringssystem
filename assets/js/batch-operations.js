/**
 * batch-operations.js - Batch operations functionality for Karis Antikvariat
 */

document.addEventListener('DOMContentLoaded', function() {
    // Store selected product IDs globally
    window.selectedItems = [];
    
    // Initialize event listeners
    initBatchOperations();
    
    // Main initialization function
    function initBatchOperations() {
        // Set up checkboxes
        setupCheckboxes();
        
        // Set up batch operation buttons
        setupBatchButtons();
    }
    
    // Set up checkbox behavior
    function setupCheckboxes() {
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            // Remove any existing listeners
            const newCheckbox = selectAllCheckbox.cloneNode(true);
            selectAllCheckbox.parentNode.replaceChild(newCheckbox, selectAllCheckbox);
            
            newCheckbox.addEventListener('change', function(e) {
                const isChecked = this.checked;
                const checkboxes = document.querySelectorAll('input[name="list-item"]');
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                // Update selected items array
                window.selectedItems = isChecked 
                    ? Array.from(checkboxes).map(checkbox => parseInt(checkbox.value))
                    : [];
                
                updateSelectedCount();
                updateBatchButtons();
            });
        }
        
        // Individual checkboxes (using event delegation for dynamic content)
        const listsBody = document.getElementById('lists-body');
        if (listsBody) {
            // Use event delegation to handle checkbox changes
            listsBody.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'list-item') {
                    const productId = parseInt(e.target.value);
                    const index = window.selectedItems.indexOf(productId);
                    
                    if (e.target.checked && index === -1) {
                        window.selectedItems.push(productId);
                    } else if (!e.target.checked && index !== -1) {
                        window.selectedItems.splice(index, 1);
                    }
                    
                    updateSelectedCount();
                    updateBatchButtons();
                    
                    // Update select all checkbox
                    const selectAllCheckbox = document.getElementById('select-all');
                    const allCheckboxes = document.querySelectorAll('input[name="list-item"]');
                    
                    if (selectAllCheckbox && allCheckboxes.length > 0) {
                        selectAllCheckbox.checked = window.selectedItems.length === allCheckboxes.length;
                    }
                }
            });
        }
        
        // Directly attach event handlers to each checkbox for debugging
        const checkboxes = document.querySelectorAll('input[name="list-item"]');
        checkboxes.forEach(checkbox => {
            checkbox.onclick = function() {
                // Checkbox click detected
            };
        });
    }
    
    // Update selected count display
    function updateSelectedCount() {
        const selectedCountElement = document.getElementById('selected-count');
        if (selectedCountElement) {
            selectedCountElement.textContent = window.selectedItems.length;
        }
    }
    
    // Update batch buttons enabled/disabled state
    function updateBatchButtons() {
        const hasSelection = window.selectedItems.length > 0;
        
        // Enable/disable batch action buttons
        const buttons = document.querySelectorAll('#batch-update-price, #batch-update-status, #batch-move-shelf, #batch-delete');
        buttons.forEach(button => {
            if (button) {
                button.disabled = !hasSelection;
            }
        });
    }

    
    // Set up batch operation buttons
    function setupBatchButtons() {
        // Update Price
        const batchUpdatePriceBtn = document.getElementById('batch-update-price');
        const confirmUpdatePriceBtn = document.getElementById('confirm-update-price');
        
        if (batchUpdatePriceBtn) {
            batchUpdatePriceBtn.addEventListener('click', function(e) {
                if (window.selectedItems.length > 0) {
                    // Show modal
                    const updatePriceModal = document.getElementById('updatePriceModal');
                    if (updatePriceModal) {
                        try {
                            const modal = new bootstrap.Modal(updatePriceModal);
                            modal.show();
                            
                            // Pre-focus the input field
                            setTimeout(() => {
                                const priceInput = document.getElementById('new-price');
                                if (priceInput) {
                                    priceInput.focus();
                                }
                            }, 500);
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Update price modal not found');
                    }
                }
            });
        }
        
        if (confirmUpdatePriceBtn) {
            confirmUpdatePriceBtn.addEventListener('click', function() {
                const newPriceInput = document.getElementById('new-price');
                const newPrice = newPriceInput ? newPriceInput.value : '';
                
                if (!newPrice || parseFloat(newPrice) <= 0) {
                    alert('Vänligen ange ett giltigt pris.');
                    return;
                }
                
                performBatchAction('update_price', {new_price: newPrice});
            });
        }
        
        // Update Status
        const batchUpdateStatusBtn = document.getElementById('batch-update-status');
        const confirmUpdateStatusBtn = document.getElementById('confirm-update-status');
        
        if (batchUpdateStatusBtn) {
            batchUpdateStatusBtn.addEventListener('click', function() {
                if (window.selectedItems.length > 0) {
                    // Show modal
                    const updateStatusModal = document.getElementById('updateStatusModal');
                    if (updateStatusModal) {
                        try {
                            const modal = new bootstrap.Modal(updateStatusModal);
                            modal.show();
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Update status modal not found');
                    }
                }
            });
        }
        
        if (confirmUpdateStatusBtn) {
            confirmUpdateStatusBtn.addEventListener('click', function() {
                const newStatusSelect = document.getElementById('new-status');
                const newStatus = newStatusSelect ? newStatusSelect.value : '';
                
                if (!newStatus) {
                    alert('Vänligen välj en status.');
                    return;
                }
                
                performBatchAction('update_status', {new_status: newStatus});
            });
        }
        
        // Move Shelf
        const batchMoveShelfBtn = document.getElementById('batch-move-shelf');
        const confirmMoveShelfBtn = document.getElementById('confirm-move-shelf');
        
        if (batchMoveShelfBtn) {
            batchMoveShelfBtn.addEventListener('click', function() {
                if (window.selectedItems.length > 0) {
                    // Show modal
                    const moveShelfModal = document.getElementById('moveShelfModal');
                    if (moveShelfModal) {
                        try {
                            const modal = new bootstrap.Modal(moveShelfModal);
                            modal.show();
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Move shelf modal not found');
                    }
                }
            });
        }
        
        if (confirmMoveShelfBtn) {
            confirmMoveShelfBtn.addEventListener('click', function() {
                const newShelfSelect = document.getElementById('new-shelf');
                const newShelf = newShelfSelect ? newShelfSelect.value : '';
                
                if (!newShelf) {
                    alert('Vänligen välj en hylla.');
                    return;
                }
                
                performBatchAction('move_shelf', {new_shelf: newShelf});
            });
        }
        
        // Delete
        const batchDeleteBtn = document.getElementById('batch-delete');
        const confirmDeleteBtn = document.getElementById('confirm-delete');
        
        if (batchDeleteBtn) {
            batchDeleteBtn.addEventListener('click', function() {
                if (window.selectedItems.length > 0) {
                    // Update delete count in modal
                    const deleteCountElement = document.getElementById('delete-count');
                    if (deleteCountElement) {
                        deleteCountElement.textContent = window.selectedItems.length;
                    }
                    
                    // Show modal
                    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
                    if (deleteConfirmModal) {
                        try {
                            const modal = new bootstrap.Modal(deleteConfirmModal);
                            modal.show();
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Delete confirm modal not found');
                    }
                }
            });
        }
        
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                performBatchAction('delete');
            });
        }
    }
    
    // Perform batch action via AJAX
    function performBatchAction(action, params = {}) {
        if (window.selectedItems.length === 0) {
            alert('Inga produkter valda.');
            return;
        }
        
        // Create request data
        const requestData = {
            action: 'batch_action',
            batch_action: action,
            product_ids: JSON.stringify(window.selectedItems)
        };
        
        // Add additional parameters
        Object.assign(requestData, params);
        
        // Send AJAX request
        $.ajax({
            url: BASE_URL + '/admin/list_ajax_handler.php',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                // Hide modals
                try {
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modal => {
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });
                } catch (err) {
                    // Error hiding modals
                }
                
                if (response.success) {
                    // Show success message
                    alert(response.message);
                    
                    // Reload products
                    if (typeof loadProducts === 'function') {
                        loadProducts();
                    } else {
                        window.location.reload();
                    }
                    
                    // Reset selected items
                    window.selectedItems = [];
                    updateSelectedCount();
                    updateBatchButtons();
                } else {
                    // Show error message
                    alert(response.message || 'Ett fel inträffade');
                }
            },
            error: function(xhr, status, error) {
                alert('Ett fel inträffade. Försök igen senare.');
            }
        });
    }
    
    // Set up observer to watch for dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && 
                (mutation.target.id === 'lists-body' || 
                 mutation.target.querySelector('#lists-body'))) {
                setupCheckboxes();
                makeRowsClickable();
            }
        });
    });
    
    // Start observing the lists container for changes
    const listsContainer = document.getElementById('lists');
    if (listsContainer) {
        observer.observe(listsContainer, { childList: true, subtree: true });
    }
});