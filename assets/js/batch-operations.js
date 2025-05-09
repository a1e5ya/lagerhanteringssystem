/**
 * batch-operations.js - Batch operations functionality for Karis Antikvariat
 * With additional debugging logs
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 batch-operations.js loaded');
    
    // Store selected product IDs globally
    window.selectedItems = [];
    
    // Initialize event listeners
    initBatchOperations();
    
    // Main initialization function
    function initBatchOperations() {
        console.log('🔍 Initializing batch operations');
        // Set up checkboxes
        setupCheckboxes();
        
        // Set up batch operation buttons
        setupBatchButtons();
        
        // Make rows clickable for product details
        makeRowsClickable();
        
        // Log initial state
        logElementsState();
    }
    
    // Log the state of important elements
    function logElementsState() {
        console.log('🔍 Checking elements state:');
        
        // Check select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        console.log('Select all checkbox:', selectAllCheckbox ? 'Found' : 'NOT FOUND');
        
        // Check individual checkboxes
        const checkboxes = document.querySelectorAll('input[name="list-item"]');
        console.log('Individual checkboxes:', checkboxes.length, 'found');
        
        // Check batch buttons
        const batchUpdatePriceBtn = document.getElementById('batch-update-price');
        console.log('Update price button:', batchUpdatePriceBtn ? 'Found' : 'NOT FOUND');
        
        const batchUpdateStatusBtn = document.getElementById('batch-update-status');
        console.log('Update status button:', batchUpdateStatusBtn ? 'Found' : 'NOT FOUND');
        
        const batchMoveShelfBtn = document.getElementById('batch-move-shelf');
        console.log('Move shelf button:', batchMoveShelfBtn ? 'Found' : 'NOT FOUND');
        
        const batchDeleteBtn = document.getElementById('batch-delete');
        console.log('Delete button:', batchDeleteBtn ? 'Found' : 'NOT FOUND');
        
        // Check modal elements
        const updatePriceModal = document.getElementById('updatePriceModal');
        console.log('Update price modal:', updatePriceModal ? 'Found' : 'NOT FOUND');
        
        const updateStatusModal = document.getElementById('updateStatusModal');
        console.log('Update status modal:', updateStatusModal ? 'Found' : 'NOT FOUND');
        
        const moveShelfModal = document.getElementById('moveShelfModal');
        console.log('Move shelf modal:', moveShelfModal ? 'Found' : 'NOT FOUND');
        
        const deleteConfirmModal = document.getElementById('deleteConfirmModal');
        console.log('Delete confirm modal:', deleteConfirmModal ? 'Found' : 'NOT FOUND');
    }
    
    // Set up checkbox behavior
    function setupCheckboxes() {
        console.log('🔍 Setting up checkboxes');
        
        // Select all checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            console.log('🔍 Found select-all checkbox, attaching event listener');
            
            // Remove any existing listeners
            const newCheckbox = selectAllCheckbox.cloneNode(true);
            selectAllCheckbox.parentNode.replaceChild(newCheckbox, selectAllCheckbox);
            
            newCheckbox.addEventListener('change', function(e) {
                console.log('🔍 Select all checkbox changed:', this.checked);
                const isChecked = this.checked;
                const checkboxes = document.querySelectorAll('input[name="list-item"]');
                console.log('🔍 Found', checkboxes.length, 'individual checkboxes to update');
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                
                // Update selected items array
                window.selectedItems = isChecked 
                    ? Array.from(checkboxes).map(checkbox => parseInt(checkbox.value))
                    : [];
                
                console.log('🔍 Updated selectedItems:', window.selectedItems);
                updateSelectedCount();
                updateBatchButtons();
            });
        } else {
            console.warn('⚠️ Select all checkbox not found!');
        }
        
        // Individual checkboxes (using event delegation for dynamic content)
        const listsBody = document.getElementById('lists-body');
        if (listsBody) {
            console.log('🔍 Found lists-body, attaching event delegation for checkboxes');
            
            // Use event delegation to handle checkbox changes
            listsBody.addEventListener('change', function(e) {
                if (e.target && e.target.name === 'list-item') {
                    console.log('🔍 Individual checkbox changed:', e.target.value, e.target.checked);
                    const productId = parseInt(e.target.value);
                    const index = window.selectedItems.indexOf(productId);
                    
                    if (e.target.checked && index === -1) {
                        window.selectedItems.push(productId);
                    } else if (!e.target.checked && index !== -1) {
                        window.selectedItems.splice(index, 1);
                    }
                    
                    console.log('🔍 Updated selectedItems:', window.selectedItems);
                    updateSelectedCount();
                    updateBatchButtons();
                    
                    // Update select all checkbox
                    const selectAllCheckbox = document.getElementById('select-all');
                    const allCheckboxes = document.querySelectorAll('input[name="list-item"]');
                    
                    if (selectAllCheckbox && allCheckboxes.length > 0) {
                        selectAllCheckbox.checked = window.selectedItems.length === allCheckboxes.length;
                        console.log('🔍 Updated select all checkbox:', selectAllCheckbox.checked);
                    }
                }
            });
        } else {
            console.warn('⚠️ Lists body not found!');
        }
        
        // Directly attach event handlers to each checkbox for debugging
        const checkboxes = document.querySelectorAll('input[name="list-item"]');
        checkboxes.forEach(checkbox => {
            checkbox.onclick = function() {
                console.log('🔍 Direct checkbox click detected:', this.value, this.checked);
            };
        });
    }
    
    // Update selected count display
    function updateSelectedCount() {
        const selectedCountElement = document.getElementById('selected-count');
        if (selectedCountElement) {
            selectedCountElement.textContent = window.selectedItems.length;
            console.log('🔍 Updated selected count:', window.selectedItems.length);
        } else {
            console.warn('⚠️ Selected count element not found!');
        }
    }
    
    // Update batch buttons enabled/disabled state
    function updateBatchButtons() {
        const hasSelection = window.selectedItems.length > 0;
        console.log('🔍 Updating batch buttons, hasSelection:', hasSelection);
        
        // Enable/disable batch action buttons
        const buttons = document.querySelectorAll('#batch-update-price, #batch-update-status, #batch-move-shelf, #batch-delete');
        buttons.forEach(button => {
            if (button) {
                button.disabled = !hasSelection;
                console.log('🔍 Updated button:', button.id, 'disabled:', button.disabled);
            }
        });
    }
    
    // Make product rows clickable (for viewing details)
    function makeRowsClickable() {
        console.log('🔍 Making rows clickable');
        const rows = document.querySelectorAll('#lists-body tr');
        console.log('🔍 Found', rows.length, 'rows to make clickable');
        
        rows.forEach(row => {
            row.style.cursor = 'pointer';
            
            // Use event delegation to avoid multiple event bindings
            row.addEventListener('click', function(e) {
                console.log('🔍 Row clicked:', this);
                
                // Don't navigate if clicking on checkbox or button
                if (e.target.type === 'checkbox' || e.target.tagName === 'BUTTON' || 
                    e.target.closest('input[type="checkbox"]') || e.target.closest('button')) {
                    console.log('🔍 Click was on checkbox or button, not navigating');
                    return;
                }
                
                // Find checkbox to get product ID
                const checkbox = this.querySelector('input[name="list-item"]');
                if (checkbox) {
                    const productId = checkbox.value;
                    console.log('🔍 Navigating to product:', productId);
                    window.location.href = `admin/adminsingleproduct.php?id=${productId}`;
                } else {
                    console.warn('⚠️ Could not find checkbox in this row');
                }
            });
        });
    }
    
    // Set up batch operation buttons
    function setupBatchButtons() {
        console.log('🔍 Setting up batch operation buttons');
        
        // Update Price
        const batchUpdatePriceBtn = document.getElementById('batch-update-price');
        const confirmUpdatePriceBtn = document.getElementById('confirm-update-price');
        
        if (batchUpdatePriceBtn) {
            console.log('🔍 Found update price button, attaching event listener');
            
            batchUpdatePriceBtn.addEventListener('click', function(e) {
                console.log('🔍 Update price button clicked');
                console.log('🔍 Selected items:', window.selectedItems);
                
                if (window.selectedItems.length > 0) {
                    console.log('🔍 Showing update price modal');
                    
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
                                    console.log('🔍 Focused on price input');
                                } else {
                                    console.warn('⚠️ Price input not found');
                                }
                            }, 500);
                        } catch (err) {
                            console.error('❌ Error showing modal:', err);
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        console.warn('⚠️ Update price modal not found!');
                        alert('Update price modal not found');
                    }
                } else {
                    console.log('🔍 No items selected, not showing modal');
                }
            });
        } else {
            console.warn('⚠️ Update price button not found!');
        }
        
        if (confirmUpdatePriceBtn) {
            console.log('🔍 Found confirm update price button, attaching event listener');
            
            confirmUpdatePriceBtn.addEventListener('click', function() {
                console.log('🔍 Confirm update price button clicked');
                
                const newPriceInput = document.getElementById('new-price');
                const newPrice = newPriceInput ? newPriceInput.value : '';
                
                console.log('🔍 New price value:', newPrice);
                
                if (!newPrice || parseFloat(newPrice) <= 0) {
                    console.warn('⚠️ Invalid price value');
                    alert('Vänligen ange ett giltigt pris.');
                    return;
                }
                
                performBatchAction('update_price', {new_price: newPrice});
            });
        } else {
            console.warn('⚠️ Confirm update price button not found!');
        }
        
        // Update Status
        const batchUpdateStatusBtn = document.getElementById('batch-update-status');
        const confirmUpdateStatusBtn = document.getElementById('confirm-update-status');
        
        if (batchUpdateStatusBtn) {
            console.log('🔍 Found update status button, attaching event listener');
            
            batchUpdateStatusBtn.addEventListener('click', function() {
                console.log('🔍 Update status button clicked');
                console.log('🔍 Selected items:', window.selectedItems);
                
                if (window.selectedItems.length > 0) {
                    console.log('🔍 Showing update status modal');
                    
                    // Show modal
                    const updateStatusModal = document.getElementById('updateStatusModal');
                    if (updateStatusModal) {
                        try {
                            const modal = new bootstrap.Modal(updateStatusModal);
                            modal.show();
                        } catch (err) {
                            console.error('❌ Error showing modal:', err);
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        console.warn('⚠️ Update status modal not found!');
                        alert('Update status modal not found');
                    }
                }
            });
        } else {
            console.warn('⚠️ Update status button not found!');
        }
        
        if (confirmUpdateStatusBtn) {
            console.log('🔍 Found confirm update status button, attaching event listener');
            
            confirmUpdateStatusBtn.addEventListener('click', function() {
                console.log('🔍 Confirm update status button clicked');
                
                const newStatusSelect = document.getElementById('new-status');
                const newStatus = newStatusSelect ? newStatusSelect.value : '';
                
                console.log('🔍 New status value:', newStatus);
                
                if (!newStatus) {
                    console.warn('⚠️ Invalid status value');
                    alert('Vänligen välj en status.');
                    return;
                }
                
                performBatchAction('update_status', {new_status: newStatus});
            });
        } else {
            console.warn('⚠️ Confirm update status button not found!');
        }
        
        // Move Shelf
        const batchMoveShelfBtn = document.getElementById('batch-move-shelf');
        const confirmMoveShelfBtn = document.getElementById('confirm-move-shelf');
        
        if (batchMoveShelfBtn) {
            console.log('🔍 Found move shelf button, attaching event listener');
            
            batchMoveShelfBtn.addEventListener('click', function() {
                console.log('🔍 Move shelf button clicked');
                console.log('🔍 Selected items:', window.selectedItems);
                
                if (window.selectedItems.length > 0) {
                    console.log('🔍 Showing move shelf modal');
                    
                    // Show modal
                    const moveShelfModal = document.getElementById('moveShelfModal');
                    if (moveShelfModal) {
                        try {
                            const modal = new bootstrap.Modal(moveShelfModal);
                            modal.show();
                        } catch (err) {
                            console.error('❌ Error showing modal:', err);
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        console.warn('⚠️ Move shelf modal not found!');
                        alert('Move shelf modal not found');
                    }
                }
            });
        } else {
            console.warn('⚠️ Move shelf button not found!');
        }
        
        if (confirmMoveShelfBtn) {
            console.log('🔍 Found confirm move shelf button, attaching event listener');
            
            confirmMoveShelfBtn.addEventListener('click', function() {
                console.log('🔍 Confirm move shelf button clicked');
                
                const newShelfSelect = document.getElementById('new-shelf');
                const newShelf = newShelfSelect ? newShelfSelect.value : '';
                
                console.log('🔍 New shelf value:', newShelf);
                
                if (!newShelf) {
                    console.warn('⚠️ Invalid shelf value');
                    alert('Vänligen välj en hylla.');
                    return;
                }
                
                performBatchAction('move_shelf', {new_shelf: newShelf});
            });
        } else {
            console.warn('⚠️ Confirm move shelf button not found!');
        }
        
        // Delete
        const batchDeleteBtn = document.getElementById('batch-delete');
        const confirmDeleteBtn = document.getElementById('confirm-delete');
        
        if (batchDeleteBtn) {
            console.log('🔍 Found delete button, attaching event listener');
            
            batchDeleteBtn.addEventListener('click', function() {
                console.log('🔍 Delete button clicked');
                console.log('🔍 Selected items:', window.selectedItems);
                
                if (window.selectedItems.length > 0) {
                    console.log('🔍 Showing delete confirmation modal');
                    
                    // Update delete count in modal
                    const deleteCountElement = document.getElementById('delete-count');
                    if (deleteCountElement) {
                        deleteCountElement.textContent = window.selectedItems.length;
                        console.log('🔍 Updated delete count:', window.selectedItems.length);
                    } else {
                        console.warn('⚠️ Delete count element not found!');
                    }
                    
                    // Show modal
                    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
                    if (deleteConfirmModal) {
                        try {
                            const modal = new bootstrap.Modal(deleteConfirmModal);
                            modal.show();
                        } catch (err) {
                            console.error('❌ Error showing modal:', err);
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        console.warn('⚠️ Delete confirm modal not found!');
                        alert('Delete confirm modal not found');
                    }
                }
            });
        } else {
            console.warn('⚠️ Delete button not found!');
        }
        
        if (confirmDeleteBtn) {
            console.log('🔍 Found confirm delete button, attaching event listener');
            
            confirmDeleteBtn.addEventListener('click', function() {
                console.log('🔍 Confirm delete button clicked');
                performBatchAction('delete');
            });
        } else {
            console.warn('⚠️ Confirm delete button not found!');
        }
    }
    
    // Perform batch action via AJAX
    function performBatchAction(action, params = {}) {
        console.log('🔍 Performing batch action:', action, 'params:', params);
        console.log('🔍 Selected items:', window.selectedItems);
        
        if (window.selectedItems.length === 0) {
            console.warn('⚠️ No items selected!');
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
        
        console.log('🔍 Request data:', requestData);
        
        // Send AJAX request
        $.ajax({
            url: 'admin/list_ajax_handler.php',
            type: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                console.log('🔍 AJAX success response:', response);
                
                // Hide modals
                try {
                    const modals = document.querySelectorAll('.modal');
                    modals.forEach(modal => {
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                            console.log('🔍 Modal hidden:', modal.id);
                        }
                    });
                } catch (err) {
                    console.error('❌ Error hiding modals:', err);
                }
                
                if (response.success) {
                    // Show success message
                    console.log('🔍 Operation successful:', response.message);
                    alert(response.message);
                    
                    // Reload products
                    if (typeof loadProducts === 'function') {
                        console.log('🔍 Calling loadProducts function');
                        loadProducts();
                    } else {
                        console.warn('⚠️ loadProducts function not found, reloading page');
                        window.location.reload();
                    }
                    
                    // Reset selected items
                    window.selectedItems = [];
                    updateSelectedCount();
                    updateBatchButtons();
                } else {
                    // Show error message
                    console.error('❌ Operation failed:', response.message);
                    alert(response.message || 'Ett fel inträffade');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', error);
                console.error('❌ Response:', xhr.responseText);
                alert('Ett fel inträffade. Försök igen senare.');
            }
        });
    }
    
    // Set up observer to watch for dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        console.log('🔍 DOM mutations detected, checking if relevant');
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && 
                (mutation.target.id === 'lists-body' || 
                 mutation.target.querySelector('#lists-body'))) {
                console.log('🔍 Lists body content changed, re-initializing');
                setupCheckboxes();
                makeRowsClickable();
            }
        });
    });
    
    // Start observing the lists container for changes
    const listsContainer = document.getElementById('lists');
    if (listsContainer) {
        console.log('🔍 Starting mutation observer on lists container');
        observer.observe(listsContainer, { childList: true, subtree: true });
    } else {
        console.warn('⚠️ Lists container not found, cannot set up mutation observer');
    }
    
    // Log that initialization is complete
    console.log('🔍 batch-operations.js initialization complete');
});