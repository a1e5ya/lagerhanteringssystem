/**
 * Enhanced AJAX utilities for the Karis Antikvariat Inventory Management System
 * Provides a consistent interface for all AJAX operations
 * 
 * @version 2.0
 * @author Axxell
 */



// Define the InventoryAjax namespace
const InventoryAjax = {
    /**
     * Base request function for standardizing all AJAX calls
     * 
     * @param {string} url - The endpoint URL
     * @param {string} method - HTTP method (GET, POST, PUT, DELETE)
     * @param {object|FormData} data - Request data
     * @param {Function} successCallback - Callback for successful responses
     * @param {Function} errorCallback - Callback for error responses
     * @return {Promise} - Promise for chaining
     */
    request: function(url, method, data, successCallback, errorCallback) {
        // Show global loading indicator
        this.showLoader();
        
        // Determine if we're using GET or POST
        const isGet = method.toUpperCase() === 'GET';
        const requestOptions = {
            method: method.toUpperCase(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        // Handle data differently for GET vs POST
let finalUrl = url;
// Add BASE_URL to relative URLs
if (url.indexOf('http') !== 0 && url.indexOf('/') !== 0) {
    finalUrl = BASE_URL + '/' + url;
} else if (url.indexOf('/') === 0) {
    finalUrl = BASE_URL + url;
}

if (isGet && data) {
    // For GET, convert data to query string
    const params = new URLSearchParams();
    Object.keys(data).forEach(key => {
        params.append(key, data[key]);
    });
    finalUrl = `${finalUrl}?${params.toString()}`;
} else if (!isGet && data) {
    // For POST, add data to body
    if (data instanceof FormData) {
        requestOptions.body = data;
    } else {
        requestOptions.headers['Content-Type'] = 'application/json';
        requestOptions.body = JSON.stringify(data);
    }
}
        
        // Make the request using fetch API
        return fetch(finalUrl, requestOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status: ${response.status}`);
                }
                return response.json();
            })
            .then(responseData => {
                // Hide loader on success
                this.hideLoader();
                
                // Check for success flag in standardized response
                if (responseData.success === true) {
                    if (typeof successCallback === 'function') {
                        successCallback(responseData);
                    }
                    return responseData;
                } else {
                    // Application-level error
                    const errorMsg = responseData.message || 'Unknown error occurred';
                    this.showMessage(errorMsg, 'danger');
                    
                    if (typeof errorCallback === 'function') {
                        errorCallback(responseData);
                    }
                    throw new Error(errorMsg);
                }
            })
            .catch(error => {
                // Hide loader on error
                this.hideLoader();
                
                // Show error message
                this.showMessage(error.message || 'Request failed', 'danger');
                
                if (typeof errorCallback === 'function') {
                    errorCallback(error);
                }
                
                return Promise.reject(error);
            });
    },
    
    /**
     * Convenience method for GET requests
     * 
     * @param {string} url - The endpoint URL
     * @param {object} data - Request parameters
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     * @return {Promise} - Promise for chaining
     */
    get: function(url, data, successCallback, errorCallback) {
        return this.request(url, 'GET', data, successCallback, errorCallback);
    },
    
    /**
     * Convenience method for POST requests
     * 
     * @param {string} url - The endpoint URL
     * @param {object|FormData} data - Request data
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     * @return {Promise} - Promise for chaining
     */
    post: function(url, data, successCallback, errorCallback) {
        return this.request(url, 'POST', data, successCallback, errorCallback);
    },
    
    /**
     * Load table data via AJAX
     * 
     * @param {string} tableId - ID of the table container
     * @param {string} url - Endpoint URL
     * @param {object} params - Request parameters (pagination, sorting, filters)
     * @return {Promise} - Promise for chaining
     */
    loadTable: function(tableId, url, params = {}) {
        const tableContainer = document.getElementById(tableId);
        if (!tableContainer) {
            console.error(`Table container with ID "${tableId}" not found`);
            return Promise.reject(new Error(`Table container not found: ${tableId}`));
        }
        
        const tableBody = tableContainer.querySelector('tbody');
        if (!tableBody) {
            console.error(`Table body not found in container "${tableId}"`);
            return Promise.reject(new Error(`Table body not found in container: ${tableId}`));
        }
        
        // Show table-specific loader
        this.showTableLoader(tableContainer);
        
        // Store current filters for pagination
        tableContainer.dataset.currentFilters = JSON.stringify(params);
        
        // Make the request
        return this.post(url, {
            ...params,
            render_html: true // Request HTML rendering from server
        }, 
        (response) => {
            // Success handler
            if (response.html) {
                // Use pre-rendered HTML if available
                tableBody.innerHTML = response.html;
            } else if (response.items) {
                // Otherwise, render items manually
                this.renderTableItems(tableBody, response.items, tableContainer);
            } else {
                tableBody.innerHTML = '<tr><td colspan="100" class="text-center">No results found</td></tr>';
            }
            
            // Update pagination if present
            if (response.pagination) {
                this.updatePagination(tableContainer, response.pagination);
            }
            
            // Hide table-specific loader
            this.hideTableLoader(tableContainer);
            
            // Initialize any dynamic elements in the new content
            this.initDynamicContent(tableBody);
            
            // Execute any custom callback
            if (typeof tableContainer.dataset.onLoad === 'function') {
                tableContainer.dataset.onLoad(response);
            }
        },
        (error) => {
            // Error handler
            tableBody.innerHTML = `<tr><td colspan="100" class="text-center text-danger">${error.message || 'Error loading data'}</td></tr>`;
            this.hideTableLoader(tableContainer);
        });
    },
    
    /**
     * Render table items without server-side HTML
     * 
     * @param {HTMLElement} tableBody - Table body element
     * @param {Array} items - Array of items to render
     * @param {HTMLElement} tableContainer - Table container element
     */
    renderTableItems: function(tableBody, items, tableContainer) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="100" class="text-center">No results found</td></tr>';
            return;
        }
        
        // Get column configuration from data attributes or table headers
        let columns = [];
        
        // Try to get columns from data attribute
        if (tableContainer.dataset.columns) {
            try {
                columns = JSON.parse(tableContainer.dataset.columns);
            } catch (e) {
                console.error('Error parsing columns data attribute', e);
            }
        }
        
        // If no columns defined, try to get from table headers
        if (columns.length === 0) {
            const headers = tableContainer.querySelectorAll('thead th');
            headers.forEach(header => {
                const field = header.dataset.field || '';
                if (field) {
                    columns.push({
                        field: field,
                        title: header.textContent.trim()
                    });
                }
            });
        }
        
        // Build HTML for items
        let html = '';
        items.forEach(item => {
            let rowClasses = '';
            let rowDataAttrs = '';
            
            // Add clickable-row functionality if href is available
            if (item.href) {
                rowClasses += ' clickable-row';
                rowDataAttrs += ` data-href="${item.href}"`;
            }
            
            let rowHtml = `<tr class="${rowClasses}"${rowDataAttrs}>`;
            
            // If columns are defined, use them to render cells
            if (columns.length > 0) {
                columns.forEach(column => {
                    if (column.field) {
                        const value = item[column.field] !== undefined ? item[column.field] : '';
                        const cellClasses = column.cellClass || '';
                        
                        rowHtml += `<td class="${cellClasses}">${value}</td>`;
                    } else {
                        rowHtml += '<td></td>';
                    }
                });
            } else {
                // Fallback: render all properties
                Object.values(item).forEach(value => {
                    rowHtml += `<td>${value}</td>`;
                });
            }
            
            rowHtml += '</tr>';
            html += rowHtml;
        });
        
        tableBody.innerHTML = html;
    },
    
    /**
     * Submit form via AJAX
     * 
     * @param {string|HTMLFormElement} form - Form ID or element
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     * @return {Promise} - Promise for chaining
     */
    submitForm: function(form, successCallback, errorCallback) {
        const formElement = typeof form === 'string' ? document.getElementById(form) : form;
        
        if (!formElement || !(formElement instanceof HTMLFormElement)) {
            console.error(`Form not found or invalid: ${form}`);
            return Promise.reject(new Error(`Form not found or invalid: ${form}`));
        }
        
        // Validate form (optional)
        if (!this.validateForm(formElement)) {
            return Promise.reject(new Error('Form validation failed'));
        }
        
        // Collect form data
        const formData = new FormData(formElement);
        
        // Get form action and method
        const action = formElement.getAttribute('action') || window.location.href;
        const method = formElement.getAttribute('method') || 'POST';
        
        // Show form-specific loader
        this.showFormLoader(formElement);
        
        // Submit form
        return this.request(action, method, formData, 
            (response) => {
                // Success handler
                this.hideFormLoader(formElement);
                this.showMessage(response.message || 'Operation successful', 'success');
                
                if (typeof successCallback === 'function') {
                    successCallback(response);
                }
                
                // Reset form if specified
                if (formElement.dataset.resetOnSuccess === 'true') {
                    formElement.reset();
                }
                
                // Redirect if specified
if (response.redirect) {
    if (response.redirect.indexOf('http') !== 0 && response.redirect.indexOf('/') !== 0) {
        window.location.href = BASE_URL + '/' + response.redirect;
    } else if (response.redirect.indexOf('/') === 0) {
        window.location.href = BASE_URL + response.redirect;
    } else {
        window.location.href = response.redirect;
    }
}
            },
            (error) => {
                // Error handler
                this.hideFormLoader(formElement);
                
                if (typeof errorCallback === 'function') {
                    errorCallback(error);
                }
            }
        );
    },
    
    /**
     * Perform batch operations on selected items
     * 
     * @param {string} action - Action to perform
     * @param {Array|NodeList} selectedItems - Selected item checkboxes or IDs
     * @param {string} endpoint - API endpoint
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     * @return {Promise} - Promise for chaining
     */
    batchOperation: function(action, selectedItems, endpoint, successCallback, errorCallback) {
        // Convert NodeList to Array if needed
        const items = Array.from(selectedItems);
        
        // Extract IDs from checkboxes or use directly if already IDs
        const itemIds = items.map(item => {
            if (typeof item === 'object' && item.value) {
                return item.value;
            }
            return item;
        });
        
        if (itemIds.length === 0) {
            this.showMessage('No items selected', 'warning');
            return Promise.reject(new Error('No items selected'));
        }
        
        // Show global loader
        this.showLoader();
        
        // Perform the batch operation
        return this.post(endpoint, {
            action: action,
            items: itemIds
        }, 
        (response) => {
            // Success handler
            this.hideLoader();
            this.showMessage(response.message || `Operation '${action}' completed successfully`, 'success');
            
            if (typeof successCallback === 'function') {
                successCallback(response);
            }
        },
        (error) => {
            // Error handler
            this.hideLoader();
            
            if (typeof errorCallback === 'function') {
                errorCallback(error);
            }
        });
    },
    
    /**
     * Basic form validation
     * 
     * @param {HTMLFormElement} form - Form to validate
     * @return {boolean} - True if valid, false otherwise
     */
    validateForm: function(form) {
        let isValid = true;
        
        // Check required fields
        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                
                // Add invalid feedback if not present
                let feedback = field.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'This field is required';
                    field.parentNode.insertBefore(feedback, field.nextElementSibling);
                }
                
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        // Check email fields
        form.querySelectorAll('input[type="email"]').forEach(field => {
            if (field.value.trim() && !this.validateEmail(field.value)) {
                field.classList.add('is-invalid');
                
                // Add invalid feedback if not present
                let feedback = field.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Please enter a valid email address';
                    field.parentNode.insertBefore(feedback, field.nextElementSibling);
                }
                
                isValid = false;
            } else if (field.value.trim()) {
                field.classList.remove('is-invalid');
            }
        });
        
        // Check numeric fields
        form.querySelectorAll('input[type="number"], [data-validate="number"]').forEach(field => {
            if (field.value.trim() && isNaN(parseFloat(field.value))) {
                field.classList.add('is-invalid');
                
                // Add invalid feedback if not present
                let feedback = field.nextElementSibling;
                if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = 'Please enter a valid number';
                    field.parentNode.insertBefore(feedback, field.nextElementSibling);
                }
                
                isValid = false;
            } else if (field.value.trim()) {
                field.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    },
    
    /**
     * Validate email format
     * 
     * @param {string} email - Email to validate
     * @return {boolean} - True if valid, false otherwise
     */
    validateEmail: function(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    },
    
    /**
     * Update pagination controls
     * 
     * @param {HTMLElement} container - Container element
     * @param {object} paginationData - Pagination data
     */
    updatePagination: function(container, paginationData) {
        // Find or create pagination container
        let paginationContainer = container.querySelector('.pagination-container');
        if (!paginationContainer) {
            paginationContainer = document.createElement('div');
            paginationContainer.className = 'pagination-container mt-3';
            container.appendChild(paginationContainer);
        }
        
        // Generate pagination HTML
        let paginationHtml = '<ul class="pagination justify-content-center">';
        
        // Previous button
        paginationHtml += `
            <li class="page-item ${paginationData.currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link pagination-link" href="javascript:void(0);" data-page="${paginationData.currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;
        
        // Page numbers
        const startPage = Math.max(1, paginationData.currentPage - 2);
        const endPage = Math.min(paginationData.totalPages, paginationData.currentPage + 2);
        
        // First page link if not in range
        if (startPage > 1) {
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link pagination-link" href="javascript:void(0);" data-page="1">1</a>
                </li>
            `;
            
            // Add ellipsis if needed
            if (startPage > 2) {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
        }
        
        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <li class="page-item ${i === paginationData.currentPage ? 'active' : ''}">
                    <a class="page-link pagination-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Last page link if not in range
        if (endPage < paginationData.totalPages) {
            // Add ellipsis if needed
            if (endPage < paginationData.totalPages - 1) {
                paginationHtml += `
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `;
            }
            
            paginationHtml += `
                <li class="page-item">
                    <a class="page-link pagination-link" href="javascript:void(0);" data-page="${paginationData.totalPages}">${paginationData.totalPages}</a>
                </li>
            `;
        }
        
        // Next button
        paginationHtml += `
            <li class="page-item ${paginationData.currentPage >= paginationData.totalPages ? 'disabled' : ''}">
                <a class="page-link pagination-link" href="javascript:void(0);" data-page="${paginationData.currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;
        
        paginationHtml += '</ul>';
        
        // Add pagination info text
        if (paginationData.totalItems > 0) {
            paginationHtml += `
                <div class="pagination-info text-center mt-2">
                    Showing ${paginationData.firstRecord} to ${paginationData.lastRecord} of ${paginationData.totalItems} items
                </div>
            `;
        }
        
        // Update container
        paginationContainer.innerHTML = paginationHtml;
        
        // Attach event handlers to pagination links
        const links = paginationContainer.querySelectorAll('.pagination-link');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                const page = parseInt(link.dataset.page, 10);
                if (isNaN(page)) return;
                
                // Get current filters from data attribute
                let currentFilters = {};
                try {
                    currentFilters = JSON.parse(container.dataset.currentFilters || '{}');
                } catch (err) {
                    console.error('Error parsing current filters', err);
                }
                
                // Update page number
                currentFilters.page = page;
                
                // Get endpoint from data attribute or use default
                const endpoint = container.dataset.endpoint || 'api/get_paginated_data.php';
                
                // Reload table data
                this.loadTable(container.id, endpoint, currentFilters);
                
                // Update URL if specified
                if (container.dataset.updateUrl === 'true') {
                    this.updateUrlParams({ page: page });
                }
            });
        });
    },
    
    /**
     * Initialize dynamic content elements
     * 
     * @param {HTMLElement} container - Container with new content
     */
    initDynamicContent: function(container) {
        // Make rows clickable
        container.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                // Only navigate if not clicking on a control element
                if (!e.target.closest('a, button, input, select, .no-click')) {
                    window.location.href = this.dataset.href;
                }
            });
        });
        
        // Initialize any Bootstrap components
        if (typeof bootstrap !== 'undefined') {
            // Initialize tooltips
            container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
            
            // Initialize popovers
            container.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                new bootstrap.Popover(el);
            });
        }
        
        // Initialize any custom input handlers
        container.querySelectorAll('input[data-autosubmit="true"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.form) {
                    this.form.submit();
                }
            });
        });
    },
    
    /**
     * Show global loading indicator
     */
    showLoader: function() {
        // Show global loader
        let loader = document.getElementById('global-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'global-loader';
            loader.className = 'global-loader';
            loader.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            document.body.appendChild(loader);
        }
        loader.style.display = 'flex';
    },
    
    /**
     * Hide global loading indicator
     */
    hideLoader: function() {
        // Hide global loader
        const loader = document.getElementById('global-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    },
    
    /**
     * Show table-specific loading indicator
     * 
     * @param {HTMLElement} tableContainer - Table container element
     */
    showTableLoader: function(tableContainer) {
        // Show table-specific loader
        let loader = tableContainer.querySelector('.table-loader');
        
        // Create loader if it doesn't exist
        if (!loader) {
            loader = document.createElement('div');
            loader.className = 'table-loader';
            loader.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            tableContainer.appendChild(loader);
        }
        
        loader.style.display = 'flex';
    },
    
    /**
     * Hide table-specific loading indicator
     * 
     * @param {HTMLElement} tableContainer - Table container element
     */
    hideTableLoader: function(tableContainer) {
        // Hide table-specific loader
        const loader = tableContainer.querySelector('.table-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    },
    
    /**
     * Show form-specific loading indicator
     * 
     * @param {HTMLFormElement} form - Form element
     */
    showFormLoader: function(form) {
        // Disable all form inputs and buttons
        form.querySelectorAll('input, select, textarea, button').forEach(el => {
            el.disabled = true;
        });
        
        // Create and show loader
        let loader = form.querySelector('.form-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.className = 'form-loader';
            loader.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> Processing...';
            
            // Find submit button and replace its text
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.dataset.originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = loader.innerHTML;
            } else {
                // If no submit button, append to form
                form.appendChild(loader);
            }
        } else {
            loader.style.display = 'block';
        }
    },
    
    /**
     * Hide form-specific loading indicator
     * 
     * @param {HTMLFormElement} form - Form element
     */
    hideFormLoader: function(form) {
        // Re-enable all form inputs and buttons
        form.querySelectorAll('input, select, textarea, button').forEach(el => {
            el.disabled = false;
        });
        
        // Find submit button and restore its text
        const submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn && submitBtn.dataset.originalText) {
            submitBtn.innerHTML = submitBtn.dataset.originalText;
        }
        
        // Hide standalone loader if present
        const loader = form.querySelector('.form-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    },
    
    /**
     * Show message to user
     * 
     * @param {string} message - Message text
     * @param {string} type - Message type (success, danger, warning, info)
     */
    showMessage: function(message, type = 'success') {
        // Use existing showMessage function if available
        if (typeof window.showMessage === 'function') {
            window.showMessage(message, type);
            return;
        }
        
        // Create a message container if not exists
        let messageContainer = document.getElementById('message-container');
        if (!messageContainer) {
            messageContainer = document.createElement('div');
            messageContainer.id = 'message-container';
            messageContainer.className = 'message-container';
            
            // Insert at the top of the main content
            const mainContent = document.querySelector('.container') || document.body;
            mainContent.insertBefore(messageContainer, mainContent.firstChild);
        }
        
        // Make sure container is visible
        messageContainer.style.display = 'block';
        
        // Create alert element
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to container
        messageContainer.appendChild(alert);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.classList.remove('show');
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                        
                        // Hide container if empty
                        if (messageContainer.children.length === 0) {
                            messageContainer.style.display = 'none';
                        }
                    }
                }, 150);
            }
        }, 5000);
    },
    
    /**
     * Update URL parameters without reloading the page
     * 
     * @param {object} params - Parameters to update
     */
    updateUrlParams: function(params) {
        const url = new URL(window.location);
        
        // Update or add each parameter
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined) {
                url.searchParams.set(key, params[key]);
            } else {
                url.searchParams.delete(key);
            }
        });
        
        // Update browser history without reloading page
        window.history.pushState({}, '', url);
    }
};