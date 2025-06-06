/**
 * forms.js - Minimal form handling functionality
 * Only handles forms that are NOT user management forms (to avoid conflicts)
 * User management forms are handled by usermanagement.php's inline JavaScript
 */

(function() {
    'use strict';

    let formsJsInitialized = false;

    /**
     * Initialize forms functionality (non-user-management forms only)
     */
    function initializeForms() {
        if (formsJsInitialized) {
            return;
        }
        formsJsInitialized = true;

        
    }

    /**
     * Initialize non-user-management form handlers
     */
    function initializeNonUserManagementForms() {
        // Handle add author form (separate from user management forms)
        const addAuthorForm = document.getElementById('add-author-form');
        if (addAuthorForm) {
            addAuthorForm.addEventListener('submit', handleAddAuthorSubmission);
        }

        // Handle newsletter forms
        const newsletterForms = document.querySelectorAll('.newsletter-form');
        newsletterForms.forEach(form => {
            form.addEventListener('submit', handleNewsletterSubmission);
        });

        // Handle any form with class 'ajax-form' (but NOT user management forms)
        const ajaxForms = document.querySelectorAll('form.ajax-form');
        ajaxForms.forEach(form => {
            // Skip if it's a user management form
            if (form.classList.contains('user-management-form') || 
                form.id === 'add-item-form' || 
                form.id === 'edit-product-form') {
                return;
            }
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const action = this.getAttribute('action') || window.location.href;
                
                submitFormAjax(this, action)
                    .then(data => {
                        if (data.success) {
                            if (window.messageSystem) {
                                window.messageSystem.success(data.message || 'Operation framgångsrik');
                            } else {
                                showMessage(data.message || 'Operation framgångsrik', 'success');
                            }
                        } else {
                            if (window.messageSystem) {
                                window.messageSystem.error(data.message || 'Ett fel inträffade');
                            } else {
                                showMessage(data.message || 'Ett fel inträffade', 'danger');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Generic form submission error:', error);
                        if (window.messageSystem) {
                            window.messageSystem.error('Ett fel inträffade');
                        } else {
                            showMessage('Ett fel inträffade', 'danger');
                        }
                    });
            });
        });

        // Initialize basic autocomplete for non-user-management forms
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
            if (window.messageSystem) {
                window.messageSystem.warning('Vänligen fyll i författarens namn.');
            } else {
                showMessage('Vänligen fyll i författarens namn.', 'warning');
            }
            if (authorName) authorName.focus();
            return;
        }


        submitFormAjax(form, 'admin/addauthor.php')
            .then(data => {
                if (data.success) {
                    if (window.messageSystem) {
                        window.messageSystem.success(data.message);
                    } else {
                        showMessage(data.message, 'success');
                    }
                    form.reset();
                    
                    // Refresh the authors table if function exists
                    if (typeof loadAuthors === 'function') {
                        loadAuthors();
                    }
                } else {
                    if (window.messageSystem) {
                        window.messageSystem.error(data.message);
                    } else {
                        showMessage(data.message, 'danger');
                    }
                }
            })
            .catch(error => {
                console.error('Author form submission error:', error);
                if (window.messageSystem) {
                    window.messageSystem.error('Ett fel inträffade vid tillägg av författare');
                } else {
                    showMessage('Ett fel inträffade vid tillägg av författare', 'danger');
                }
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
            if (window.messageSystem) {
                window.messageSystem.warning('Vänligen ange en e-postadress');
            } else {
                showMessage('Vänligen ange en e-postadress', 'warning');
            }
            return;
        }
        
        if (!isValidEmail(email.value)) {
            if (window.messageSystem) {
                window.messageSystem.warning('Vänligen ange en giltig e-postadress');
            } else {
                showMessage('Vänligen ange en giltig e-postadress', 'warning');
            }
            return;
        }
        
        submitFormAjax(form, form.action)
            .then(data => {
                if (data.success) {
                    if (window.messageSystem) {
                        window.messageSystem.success(data.message || 'Prenumeration tillagd');
                    } else {
                        showMessage(data.message || 'Prenumeration tillagd', 'success');
                    }
                    form.reset();
                } else {
                    if (window.messageSystem) {
                        window.messageSystem.error(data.message || 'Ett fel inträffade');
                    } else {
                        showMessage(data.message || 'Ett fel inträffade', 'danger');
                    }
                }
            })
            .catch(error => {
                console.error('Newsletter form error:', error);
                if (window.messageSystem) {
                    window.messageSystem.error('Ett fel inträffade');
                } else {
                    showMessage('Ett fel inträffade', 'danger');
                }
            });
    }

    /**
     * Submit form via AJAX
     */
    function submitFormAjax(form, url) {
        const formData = new FormData(form);
        
        // Ensure CSRF token is included
        if (window.CSRF_TOKEN && !formData.has('csrf_token')) {
            formData.append('csrf_token', window.CSRF_TOKEN);
        }
        
        return fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': window.CSRF_TOKEN || ''
            }
        })
        .then(response => {
            if (response.status === 419) {
                // CSRF token expired
                throw new Error('Säkerhetstoken har gått ut. Sidan kommer att laddas om.');
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // If there's a redirect in the response, handle it
            if (data.success && data.redirect) {
                window.location.href = data.redirect;
                return data;
            }
            return data;
        })
        .catch(error => {
            if (error.message.includes('Säkerhetstoken')) {
                // Reload page for CSRF token issues
                setTimeout(() => window.location.reload(), 1000);
            }
            console.error('AJAX submission error:', error);
            throw error;
        });
    }

    /**
     * Initialize basic autocomplete for non-user-management forms
     */
    function initializeBasicAutocomplete() {
        // Only setup autocomplete for forms that are NOT user management forms
        const nonUserManagementAutocompleteFields = [
            { inputId: 'search-author', suggestionId: 'suggest-search-author', type: 'author' },
            { inputId: 'filter-publisher', suggestionId: 'suggest-filter-publisher', type: 'publisher' }
        ];

        nonUserManagementAutocompleteFields.forEach(field => {
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
     * Refresh authors table (utility function)
     */
    function refreshAuthorsTable() {
        if (typeof loadAuthors === 'function') {
            loadAuthors();
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeForms();
    });

    // Also initialize when content is loaded via AJAX
    document.addEventListener('contentLoaded', function() {
        formsJsInitialized = false; // Reset flag to allow reinitialization
        initializeForms();
    });

    // Expose utility functions globally
    window.formsUtils = {
        showMessage: window.showMessage,
        refreshAuthorsTable: refreshAuthorsTable,
        submitFormAjax: submitFormAjax,
        reinitialize: function() {
            formsJsInitialized = false;
            initializeForms();
        }
    };


})();