/**
 * batch-operations.js - Batch operations functionality for Karis Antikvariat
 * FIXED VERSION with proper CSRF handling and modal focus management
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
        const buttons = document.querySelectorAll('#batch-update-price, #batch-update-status, #batch-move-shelf, #batch-delete, #batch-toggle-sale, #batch-toggle-rare, #batch-toggle-recommended');
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
        
        // Toggle Special Price
        const batchToggleSaleBtn = document.getElementById('batch-toggle-sale');
        const confirmToggleSpecialPriceBtn = document.getElementById('confirm-toggle-special-price');
        
        if (batchToggleSaleBtn) {
            batchToggleSaleBtn.addEventListener('click', function() {
                if (window.selectedItems.length > 0) {
                    // Update count in modal
                    const specialPriceCountElement = document.getElementById('special-price-count');
                    if (specialPriceCountElement) {
                        specialPriceCountElement.textContent = window.selectedItems.length;
                    }
                    
                    // Show modal
                    const toggleSpecialPriceModal = document.getElementById('toggleSpecialPriceModal');
                    if (toggleSpecialPriceModal) {
                        try {
                            const modal = new bootstrap.Modal(toggleSpecialPriceModal);
                            modal.show();
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Toggle special price modal not found');
                    }
                }
            });
        }
        
        if (confirmToggleSpecialPriceBtn) {
            confirmToggleSpecialPriceBtn.addEventListener('click', function() {
                const specialPriceActionSelect = document.getElementById('special-price-action');
                const action = specialPriceActionSelect ? specialPriceActionSelect.value : '';
                
                if (action === '') {
                    alert('Vänligen välj en åtgärd.');
                    return;
                }
                
                performBatchAction('set_special_price', {special_price_value: action});
            });
        }
        
        // Toggle Rare
        const batchToggleRareBtn = document.getElementById('batch-toggle-rare');
        const confirmToggleRareBtn = document.getElementById('confirm-toggle-rare');
        
        if (batchToggleRareBtn) {
            batchToggleRareBtn.addEventListener('click', function() {
                if (window.selectedItems.length > 0) {
                    // Update count in modal
                    const rareCountElement = document.getElementById('rare-count');
                    if (rareCountElement) {
                        rareCountElement.textContent = window.selectedItems.length;
                    }
                    
                    // Show modal
                    const toggleRareModal = document.getElementById('toggleRareModal');
                    if (toggleRareModal) {
                        try {
                            const modal = new bootstrap.Modal(toggleRareModal);
                            modal.show();
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Toggle rare modal not found');
                    }
                }
            });
        }
        
        if (confirmToggleRareBtn) {
            confirmToggleRareBtn.addEventListener('click', function() {
                const rareActionSelect = document.getElementById('rare-action');
                const action = rareActionSelect ? rareActionSelect.value : '';
                
                if (action === '') {
                    alert('Vänligen välj en åtgärd.');
                    return;
                }
                
                performBatchAction('set_rare', {rare_value: action});
            });
        }
        
        // Toggle Recommended
        const batchToggleRecommendedBtn = document.getElementById('batch-toggle-recommended');
        const confirmToggleRecommendedBtn = document.getElementById('confirm-toggle-recommended');
        
        if (batchToggleRecommendedBtn) {
            batchToggleRecommendedBtn.addEventListener('click', function() {
                if (window.selectedItems.length > 0) {
                    // Update count in modal
                    const recommendedCountElement = document.getElementById('recommended-count');
                    if (recommendedCountElement) {
                        recommendedCountElement.textContent = window.selectedItems.length;
                    }
                    
                    // Show modal
                    const toggleRecommendedModal = document.getElementById('toggleRecommendedModal');
                    if (toggleRecommendedModal) {
                        try {
                            const modal = new bootstrap.Modal(toggleRecommendedModal);
                            modal.show();
                        } catch (err) {
                            alert('Error showing modal: ' + err.message);
                        }
                    } else {
                        alert('Toggle recommended modal not found');
                    }
                }
            });
        }
        
        if (confirmToggleRecommendedBtn) {
            confirmToggleRecommendedBtn.addEventListener('click', function() {
                const recommendedActionSelect = document.getElementById('recommended-action');
                const action = recommendedActionSelect ? recommendedActionSelect.value : '';
                
                if (action === '') {
                    alert('Vänligen välj en åtgärd.');
                    return;
                }
                
                performBatchAction('set_recommended', {recommended_value: action});
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
    
    // FIXED: Perform batch action via AJAX with proper focus management and CSRF protection
    function performBatchAction(action, params = {}) {
        if (window.selectedItems.length === 0) {
            alert('Inga produkter valda.');
            return;
        }
        
        // CRITICAL FIX: Remove focus from any active element before hiding modals
        if (document.activeElement) {
            document.activeElement.blur();
        }
        
        // Create request data with CSRF token
        const requestData = {
            action: 'batch_action',
            batch_action: action,
            product_ids: JSON.stringify(window.selectedItems),
            csrf_token: window.CSRF_TOKEN // <-- CRITICAL: Include CSRF token
        };
        
        // Add additional parameters
        Object.assign(requestData, params);
        
        console.log('CSRF Token being sent:', window.CSRF_TOKEN); // Debug logging
        console.log('Request Data being sent:', requestData);    // Debug logging

        // Send AJAX request using modern fetch API for better error handling
        fetch(BASE_URL + '/admin/list_ajax_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': window.CSRF_TOKEN
            },
            body: new URLSearchParams(requestData)
        })
        .then(response => {
            // Hide modals AFTER request is sent but before processing response
            try {
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            } catch (err) {
                console.warn('Error hiding modals:', err);
            }
            
            // Parse JSON response
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                alert(data.message);
                
                // Reload products
                if (typeof loadProducts === 'function') {
                    loadProducts();
                } else if (typeof loadListsProducts === 'function') {
                    // For lists.js integration
                    loadListsProducts();
                } else {
                    window.location.reload();
                }
                
                // Reset selected items for delete operations
                if (action === 'delete') {
                    window.selectedItems = [];
                    updateSelectedCount();
                    updateBatchButtons();
                }
            } else {
                // Show error message
                alert(data.message || 'Ett fel inträffade');
            }
        })
        .catch(error => {
            console.error('Batch operation error:', error);
            
            // Provide more specific error messages
            let errorMessage = 'Ett fel inträffade. Försök igen senare.';
            
            if (error.message.includes('403')) {
                errorMessage = 'Åtkomst nekad. Kontrollera dina behörigheter.';
            } else if (error.message.includes('400')) {
                errorMessage = 'Felaktig begäran. Kontrollera formulärdata.';
            } else if (error.message.includes('500')) {
                errorMessage = 'Serverfel. Kontakta administratören.';
            }
            
            alert(errorMessage);
        });
    }
    
    // Set up observer to watch for dynamic content changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && 
                (mutation.target.id === 'lists-body' || 
                 mutation.target.querySelector('#lists-body'))) {
                setupCheckboxes();
                if (typeof makeRowsClickable === 'function') {
                    makeRowsClickable();
                }
            }
        });
    });
    
    // Start observing the lists container for changes
    const listsContainer = document.getElementById('lists');
    if (listsContainer) {
        observer.observe(listsContainer, { childList: true, subtree: true });
    }
});