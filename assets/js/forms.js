/**
 * forms.js - Minimal form handling functionality
 * Only handles forms that are NOT product forms (to avoid conflicts)
 * Product forms are handled by addproduct-handlers.js
 */

(function() {
    'use strict';

    let formsJsInitialized = false;

    /**
     * Initialize forms functionality (non-product forms only)
     */
    function initializeForms() {
        if (formsJsInitialized) {
            return;
        }
        formsJsInitialized = true;

        console.log('Initializing minimal forms.js functionality...');
        
        // Only initialize for non-product forms
        initializeNonProductForms();
        
        console.log('Minimal forms.js initialized successfully');
    }

    /**
     * Initialize non-product form handlers
     */
    function initializeNonProductForms() {
        // Handle add author form (separate from product forms)
        const addAuthorForm = document.getElementById('add-author-form');
        if (addAuthorForm) {
            console.log('Setting up add author form handler');
            addAuthorForm.addEventListener('submit', handleAddAuthorSubmission);
        }

        // Handle newsletter forms
        const newsletterForms = document.querySelectorAll('.newsletter-form');
        newsletterForms.forEach(form => {
            form.addEventListener('submit', handleNewsletterSubmission);
        });

        // Handle user management forms
        const userManagementForms = document.querySelectorAll('.user-management-form');
        userManagementForms.forEach(form => {
            form.addEventListener('submit', handleUserManagementSubmission);
        });

        // Handle any form with class 'ajax-form' (but NOT product forms)
        const ajaxForms = document.querySelectorAll('form.ajax-form');
        ajaxForms.forEach(form => {
            // Skip if it's a product form
            if (form.id === 'add-item-form' || form.id === 'edit-product-form') {
                return;
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const action = this.getAttribute('action') || window.location.href;
                console.log('Submitting generic AJAX form to:', action);
                
                submitFormAjax(this, action)
                    .then(data => {
                        if (data.success) {
                            showMessage(data.message || 'Operation framgångsrik', 'success');
                        } else {
                            showMessage(data.message || 'Ett fel inträffade', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Generic form submission error:', error);
                        showMessage('Ett fel inträffade', 'danger');
                    });
            });
        });

        // Initialize basic autocomplete for non-product forms
        initializeBasicAutocomplete();
    }

    /**
     * Handle add author form submission
     */
    function handleAddAuthorSubmission(e) {
        e.preventDefault();
        e.stopPropagation();

        const form = e.target;
        const authorName = form.querySelector('[name="author_name"]');

        if (!authorName || !authorName.value.trim()) {
            showMessage('Vänligen fyll i författarens namn.', 'warning');
            if (authorName) authorName.focus();
            return;
        }

        console.log('Submitting add author form');

        submitFormAjax(form, 'admin/addauthor.php')
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    
                    // Refresh the authors table if function exists
                    if (typeof loadAuthors === 'function') {
                        loadAuthors();
                    }
                } else {
                    showMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Author form submission error:', error);
                showMessage('Ett fel inträffade vid tillägg av författare', 'danger');
            });
    }

    /**
     * Handle newsletter form submission
     */
    function handleNewsletterSubmission(e) {
        e.preventDefault();
        
        const form = e.target;
        const email = form.querySelector('[name="email"]');
        
        if (!email || !email.value.trim()) {
            showMessage('Vänligen ange en e-postadress', 'warning');
            return;
        }
        
        if (!isValidEmail(email.value)) {
            showMessage('Vänligen ange en giltig e-postadress', 'warning');
            return;
        }
        
        submitFormAjax(form, form.action)
            .then(data => {
                if (data.success) {
                    showMessage(data.message || 'Prenumeration tillagd', 'success');
                    form.reset();
                } else {
                    showMessage(data.message || 'Ett fel inträffade', 'danger');
                }
            })
            .catch(error => {
                console.error('Newsletter form error:', error);
                showMessage('Ett fel inträffade', 'danger');
            });
    }

    /**
     * Handle user management form submission
     */
    function handleUserManagementSubmission(e) {
        e.preventDefault();
        
        const form = e.target;
        
        submitFormAjax(form, form.action)
            .then(data => {
                if (data.success) {
                    showMessage(data.message || 'Operation framgångsrik', 'success');
                    
                    // Refresh user list if function exists
                    if (typeof loadUsers === 'function') {
                        loadUsers();
                    }
                } else {
                    showMessage(data.message || 'Ett fel inträffade', 'danger');
                }
            })
            .catch(error => {
                console.error('User management form error:', error);
                showMessage('Ett fel inträffade', 'danger');
            });
    }

    /**
     * Submit form via AJAX
     */
    function submitFormAjax(form, url) {
        const formData = new FormData(form);
        
        return fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('AJAX submission error:', error);
            throw error;
        });
    }

    /**
     * Initialize basic autocomplete for non-product forms
     */
    function initializeBasicAutocomplete() {
        // Only setup autocomplete for forms that are NOT product forms
        const nonProductAutocompleteFields = [
            { inputId: 'search-author', suggestionId: 'suggest-search-author', type: 'author' },
            { inputId: 'filter-publisher', suggestionId: 'suggest-filter-publisher', type: 'publisher' }
        ];

        nonProductAutocompleteFields.forEach(field => {
            setupAutocompleteField(field.inputId, field.suggestionId, field.type);
        });
    }

    /**
     * Setup autocomplete for a specific field
     */
    function setupAutocompleteField(inputId, suggestionId, type) {
        const input = document.getElementById(inputId);
        const suggestionDiv = document.getElementById(suggestionId);
        
        if (!input || !suggestionDiv) {
            return;
        }

        console.log(`Setting up autocomplete for ${inputId}`);

        input.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                suggestionDiv.innerHTML = '';
                return;
            }

            const baseUrl = window.location.pathname.includes('/admin/') 
                ? 'autocomplete.php' 
                : 'admin/autocomplete.php';
            const url = `${baseUrl}?type=${type}&query=${encodeURIComponent(query)}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    suggestionDiv.innerHTML = '';
                    
                    if (data && data.length > 0) {
                        data.forEach(item => {
                            const suggestion = document.createElement('div');
                            suggestion.className = 'list-group-item list-group-item-action';
                            suggestion.textContent = item;
                            suggestion.style.cursor = 'pointer';
                            
                            suggestion.addEventListener('click', function() {
                                input.value = item;
                                suggestionDiv.innerHTML = '';
                                
                                // Trigger input event for any additional processing
                                input.dispatchEvent(new Event('input'));
                            });
                            
                            suggestionDiv.appendChild(suggestion);
                        });
                    }
                })
                .catch(error => {
                    console.error('Autocomplete error:', error);
                    suggestionDiv.innerHTML = '';
                });
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !suggestionDiv.contains(e.target)) {
                suggestionDiv.innerHTML = '';
            }
        });
    }

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show message to user
     */
    function showMessage(message, type = 'info') {
        console.log('Showing message:', { message, type });

        // Try to use existing showMessage function first
        if (typeof window.showMessage === 'function') {
            window.showMessage(message, type);
            return;
        }

        // Look for message containers
        let messageContainer = document.getElementById('message-container');
        
        if (!messageContainer) {
            messageContainer = document.getElementById('author-message-container');
        }

        if (!messageContainer) {
            // Create a message container
            messageContainer = document.createElement('div');
            messageContainer.id = 'forms-message-container';
            messageContainer.style.position = 'fixed';
            messageContainer.style.top = '20px';
            messageContainer.style.right = '20px';
            messageContainer.style.zIndex = '9999';
            messageContainer.style.maxWidth = '400px';
            document.body.appendChild(messageContainer);
        }

        // Clear previous messages in this container
        messageContainer.innerHTML = '';

        // Create alert element
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close"></button>
        `;

        messageContainer.appendChild(alert);
        messageContainer.style.display = 'block';

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
                
                // Hide container if empty
                if (messageContainer.children.length === 0) {
                    messageContainer.style.display = 'none';
                }
            }
        }, 5000);
    }

    /**
     * Refresh authors table (utility function)
     */
    function refreshAuthorsTable() {
        if (typeof loadAuthors === 'function') {
            loadAuthors();
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing minimal forms.js');
        initializeForms();
    });

    // Also initialize when content is loaded via AJAX
    document.addEventListener('contentLoaded', function() {
        console.log('Content loaded, reinitializing minimal forms.js');
        formsJsInitialized = false; // Reset flag to allow reinitialization
        initializeForms();
    });

    // Expose utility functions globally
    window.formsUtils = {
        showMessage: showMessage,
        refreshAuthorsTable: refreshAuthorsTable,
        submitFormAjax: submitFormAjax,
        reinitialize: function() {
            formsJsInitialized = false;
            initializeForms();
        }
    };

    console.log('Minimal forms.js script loaded');

})();