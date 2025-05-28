/**
 * addproduct-handlers.js - Fixed version with proper edit form support
 * Handles multiple authors, genres, image preview, and AJAX form submission
 * Now properly supports both add and edit product forms
 */

(function() {
    'use strict';

    // Global arrays to store selected items
    window.productAuthors = window.productAuthors || [];
    window.productGenres = window.productGenres || [];
    
    // Global array to store selected image files
    let productImageFiles = [];
    const MAX_IMAGES = 10;
    
    // Initialization flags to prevent multiple setups
    let isInitialized = false;
    let imageHandlerInitialized = false;

    /**
     * Main initialization function
     */
    function initializeProductHandlers() {
        if (isInitialized) {
            console.log('Product handlers already initialized, skipping...');
            return;
        }
        
        console.log('Initializing product handlers...');
        
        // Check if we're on a product form page
        const productForm = document.getElementById('add-item-form') || document.getElementById('edit-product-form');
        if (!productForm) {
            console.log('No product form found, skipping initialization');
            return;
        }
        
        // Initialize all components
        initializeAuthorsManagement();
        initializeGenresManagement();
        initializeImagePreview();
        initializeAutocomplete();
        initializeFormSubmission();
        
        isInitialized = true;
        console.log('Product handlers initialized successfully');
    }

    /**
     * Initialize authors management
     */
    function initializeAuthorsManagement() {
        console.log('Setting up authors management...');
        
        // Set up event delegation for author buttons
        document.addEventListener('click', handleAuthorActions);
        document.addEventListener('keypress', handleAuthorKeypress);
        
        // Initialize existing authors (for edit mode)
        const selectedAuthorsDiv = document.querySelector('.selected-authors');
        if (selectedAuthorsDiv && selectedAuthorsDiv.dataset.authors) {
            try {
                const existingAuthors = JSON.parse(selectedAuthorsDiv.dataset.authors);
                window.productAuthors = [];
                existingAuthors.forEach(author => {
                    if (author.name && !window.productAuthors.find(a => a.name === author.name)) {
                        window.productAuthors.push({
                            id: author.id || null,
                            name: author.name
                        });
                    }
                });
                renderSelectedAuthors();
                updateAuthorsJson();
            } catch (e) {
                console.log('No existing authors data or invalid JSON');
            }
        }
    }

    /**
     * Handle author-related click events
     */
    function handleAuthorActions(e) {
        // Handle add author button
        if (e.target && (e.target.id === 'add-author-btn' || e.target.closest('#add-author-btn'))) {
            e.preventDefault();
            addAuthorFromInput();
            return;
        }
        
        // Handle remove author clicks
        if (e.target && e.target.closest('.selected-author')) {
            const authorDiv = e.target.closest('.selected-author');
            if (authorDiv && authorDiv.classList.contains('selected-author')) {
                e.preventDefault();
                const authorName = authorDiv.dataset.name || authorDiv.textContent.trim().replace('×', '').trim();
                removeAuthor(authorName);
            }
        }
    }

    /**
     * Handle Enter key in author input
     */
    function handleAuthorKeypress(e) {
        if (e.target && e.target.id === 'author-name' && e.key === 'Enter') {
            e.preventDefault();
            addAuthorFromInput();
        }
    }

    /**
     * Add author from input field
     */
    function addAuthorFromInput() {
        const authorInput = document.getElementById('author-name');
        if (!authorInput) return;
        
        const authorName = authorInput.value.trim();
        if (authorName && !window.productAuthors.find(a => a.name === authorName)) {
            window.productAuthors.push({
                id: null,
                name: authorName
            });
            
            authorInput.value = '';
            renderSelectedAuthors();
            updateAuthorsJson();
            
            console.log('Added author:', authorName);
            
            // Hide autocomplete suggestions
            const suggestDiv = document.getElementById('suggest-author');
            if (suggestDiv) suggestDiv.innerHTML = '';
        } else if (window.productAuthors.find(a => a.name === authorName)) {
            showMessage('Författaren är redan tillagd', 'warning');
        }
    }

    /**
     * Remove author
     */
    function removeAuthor(authorName) {
        const index = window.productAuthors.findIndex(a => a.name === authorName);
        if (index > -1) {
            window.productAuthors.splice(index, 1);
            renderSelectedAuthors();
            updateAuthorsJson();
            console.log('Removed author:', authorName);
        }
    }

    /**
     * Render selected authors
     */
    function renderSelectedAuthors() {
        const selectedAuthorsDiv = document.querySelector('.selected-authors');
        if (!selectedAuthorsDiv) return;
        
        selectedAuthorsDiv.innerHTML = '';
        
        if (window.productAuthors.length === 0) {
            selectedAuthorsDiv.innerHTML = '<em class="text-muted">Ingen författare vald</em>';
            return;
        }
        
        window.productAuthors.forEach(author => {
            const authorBadge = document.createElement('div');
            authorBadge.className = 'selected-author badge bg-secondary p-2 me-2 mb-2';
            authorBadge.style.cursor = 'pointer';
            authorBadge.dataset.name = author.name;
            authorBadge.innerHTML = `${author.name} <span class="ms-1">×</span>`;
            authorBadge.title = 'Klicka för att ta bort';
            
            selectedAuthorsDiv.appendChild(authorBadge);
        });
    }

    /**
     * Update authors JSON hidden field
     */
    function updateAuthorsJson() {
        const authorsJsonInput = document.getElementById('authors-json');
        if (authorsJsonInput) {
            const authorNames = window.productAuthors.map(a => a.name);
            authorsJsonInput.value = JSON.stringify(authorNames);
            console.log('Updated authors JSON:', authorNames);
        }
    }

    /**
     * Initialize genres management
     */
    function initializeGenresManagement() {
        console.log('Setting up genres management...');
        
        // Set up event delegation for genre buttons
        document.addEventListener('click', handleGenreActions);
        
        // Initialize existing genres (for edit mode)
        const selectedGenresDiv = document.querySelector('.selected-genres');
        if (selectedGenresDiv && selectedGenresDiv.dataset.genres) {
            try {
                const existingGenres = JSON.parse(selectedGenresDiv.dataset.genres);
                window.productGenres = [];
                existingGenres.forEach(genre => {
                    if (genre.name && !window.productGenres.find(g => g.id === genre.id)) {
                        window.productGenres.push({
                            id: genre.id,
                            name: genre.name
                        });
                    }
                });
                renderSelectedGenres();
                updateGenresJson();
            } catch (e) {
                console.log('No existing genres data or invalid JSON');
            }
        }
    }

    /**
     * Handle genre-related click events
     */
    function handleGenreActions(e) {
        // Handle add genre button
        if (e.target && (e.target.id === 'add-genre-btn' || e.target.closest('#add-genre-btn'))) {
            e.preventDefault();
            addGenreFromSelect();
            return;
        }
        
        // Handle remove genre clicks
        if (e.target && e.target.closest('.selected-genre')) {
            const genreDiv = e.target.closest('.selected-genre');
            if (genreDiv && genreDiv.classList.contains('selected-genre')) {
                e.preventDefault();
                const genreId = parseInt(genreDiv.dataset.genreId);
                removeGenre(genreId);
            }
        }
    }

    /**
     * Add genre from select dropdown
     */
    function addGenreFromSelect() {
        const genreSelect = document.getElementById('item-genre');
        if (!genreSelect || !genreSelect.value) return;
        
        const genreId = parseInt(genreSelect.value);
        const genreName = genreSelect.options[genreSelect.selectedIndex].text;
        
        if (genreId && genreName && !window.productGenres.find(g => g.id === genreId)) {
            window.productGenres.push({
                id: genreId,
                name: genreName
            });
            
            genreSelect.value = '';
            renderSelectedGenres();
            updateGenresJson();
            
            console.log('Added genre:', genreName);
        } else if (window.productGenres.find(g => g.id === genreId)) {
            showMessage('Genren är redan tillagd', 'warning');
        }
    }

    /**
     * Remove genre
     */
    function removeGenre(genreId) {
        const index = window.productGenres.findIndex(g => g.id === genreId);
        if (index > -1) {
            const removedGenre = window.productGenres[index];
            window.productGenres.splice(index, 1);
            renderSelectedGenres();
            updateGenresJson();
            console.log('Removed genre:', removedGenre.name);
        }
    }

    /**
     * Render selected genres
     */
    function renderSelectedGenres() {
        const selectedGenresDiv = document.querySelector('.selected-genres');
        if (!selectedGenresDiv) return;
        
        selectedGenresDiv.innerHTML = '';
        
        if (window.productGenres.length === 0) {
            selectedGenresDiv.innerHTML = '<em class="text-muted">Ingen genre vald</em>';
            return;
        }
        
        window.productGenres.forEach(genre => {
            const genreBadge = document.createElement('div');
            genreBadge.className = 'selected-genre badge bg-secondary p-2 me-2 mb-2';
            genreBadge.style.cursor = 'pointer';
            genreBadge.dataset.genreId = genre.id;
            genreBadge.innerHTML = `${genre.name} <span class="ms-1">×</span>`;
            genreBadge.title = 'Klicka för att ta bort';
            
            selectedGenresDiv.appendChild(genreBadge);
        });
    }

    /**
     * Update genres JSON hidden field
     */
    function updateGenresJson() {
        const genresJsonInput = document.getElementById('genres-json');
        if (genresJsonInput) {
            const genreIds = window.productGenres.map(g => g.id);
            genresJsonInput.value = JSON.stringify(genreIds);
            console.log('Updated genres JSON:', genreIds);
        }
    }

    /**
     * Initialize image preview functionality
     */
    function initializeImagePreview() {
        if (imageHandlerInitialized) {
            console.log('Image handler already initialized, skipping...');
            return;
        }
        
        console.log('Setting up image preview...');
        
        const fileInput = document.getElementById('item-image-upload');
        const previewContainer = document.getElementById('item-image-previews');
        const defaultImage = document.getElementById('default-image-preview');

        if (!fileInput || !previewContainer) {
            console.log('Image elements not found:', {
                fileInput: !!fileInput,
                previewContainer: !!previewContainer
            });
            return;
        }

        console.log('Image preview elements found, setting up event listeners...');

        fileInput.addEventListener('change', function(event) {
            const newFiles = Array.from(event.target.files);
            console.log('Files selected:', newFiles.length);

            newFiles.forEach(file => {
                const isDuplicate = productImageFiles.some(existingFile =>
                    existingFile.name === file.name && existingFile.size === file.size
                );

                if (productImageFiles.length < MAX_IMAGES && !isDuplicate) {
                    productImageFiles.push(file);
                } else if (productImageFiles.length >= MAX_IMAGES) {
                    showMessage('Du kan ladda upp högst ' + MAX_IMAGES + ' bilder.', 'warning');
                    return;
                }
            });

            fileInput.value = '';
            updateFileInputFiles();
            renderImagePreviews();
        });

        imageHandlerInitialized = true;
        console.log('Image preview setup completed');

        /**
         * Render image previews
         */
        function renderImagePreviews() {
            if (!previewContainer) return;

            // Only clear and manage default image if this is the new images preview container
            const isNewImageContainer = previewContainer.id === 'item-image-previews';
            
            if (isNewImageContainer) {
                previewContainer.innerHTML = '';
            }

            if (productImageFiles.length > 0) {
                if (defaultImage && isNewImageContainer) {
                    defaultImage.style.display = 'none';
                }

                productImageFiles.forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgWrapper = document.createElement('div');
                        imgWrapper.classList.add('position-relative', 'd-inline-block', 'me-2', 'mb-2', 'border', 'rounded', 'shadow-sm');
                        imgWrapper.style.maxWidth = '150px';
                        imgWrapper.style.maxHeight = '150px';
                        imgWrapper.style.overflow = 'hidden';
                        imgWrapper.style.cursor = 'pointer';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = `Produktbild förhandsgranskning ${index + 1}`;
                        img.classList.add('img-fluid', 'rounded');
                        img.style.objectFit = 'cover';
                        img.style.width = '100%';
                        img.style.height = '100%';

                        const removeBtn = document.createElement('div');
                        removeBtn.className = 'position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center';
                        removeBtn.style.width = '25px';
                        removeBtn.style.height = '25px';
                        removeBtn.style.fontSize = '14px';
                        removeBtn.style.cursor = 'pointer';
                        removeBtn.style.transform = 'translate(50%, -50%)';
                        removeBtn.innerHTML = '×';
                        removeBtn.title = 'Ta bort denna bild';

                        removeBtn.onclick = (e) => {
                            e.stopPropagation();
                            if (confirm('Vill du ta bort den här bilden?')) {
                                removeImage(index);
                            }
                        };

                        imgWrapper.appendChild(img);
                        imgWrapper.appendChild(removeBtn);
                        previewContainer.appendChild(imgWrapper);
                    };

                    reader.onerror = function() {
                        console.error('Error reading file:', file.name);
                        showMessage('Fel vid läsning av bild: ' + file.name, 'danger');
                    };

                    reader.readAsDataURL(file);
                });
            } else {
                // Only show default image if we're on add product page or edit page with no existing images
                const existingImagesContainer = document.getElementById('existing-images');
                const hasExistingImages = existingImagesContainer && existingImagesContainer.children.length > 0;
                
                if (defaultImage && isNewImageContainer && !hasExistingImages) {
                    previewContainer.appendChild(defaultImage);
                    defaultImage.style.display = 'block';
                }
            }
        }

        /**
         * Remove image from preview
         */
        function removeImage(indexToRemove) {
            if (indexToRemove >= 0 && indexToRemove < productImageFiles.length) {
                const removedFile = productImageFiles[indexToRemove];
                productImageFiles.splice(indexToRemove, 1);
                updateFileInputFiles();
                renderImagePreviews();
                
                console.log('Removed image:', removedFile.name);
            }
        }

        /**
         * Update file input with current files
         */
        function updateFileInputFiles() {
            if (!fileInput) return;
            
            try {
                const dataTransfer = new DataTransfer();
                productImageFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
                
                console.log('Updated file input with', productImageFiles.length, 'files');
            } catch (error) {
                console.error('Error updating file input:', error);
            }
        }
    }

    /**
     * Initialize autocomplete functionality
     */
    function initializeAutocomplete() {
        console.log('Setting up autocomplete...');
        
        // Author autocomplete
        const authorInput = document.getElementById('author-name');
        const authorSuggestDiv = document.getElementById('suggest-author');
        
        if (authorInput && authorSuggestDiv) {
            authorInput.addEventListener('input', function() {
                handleAutocomplete(this.value, 'author', authorSuggestDiv, authorInput);
            });
        }
        
        // Publisher autocomplete
        const publisherInput = document.getElementById('item-publisher');
        const publisherSuggestDiv = document.getElementById('suggest-publisher');
        
        if (publisherInput && publisherSuggestDiv) {
            publisherInput.addEventListener('input', function() {
                handleAutocomplete(this.value, 'publisher', publisherSuggestDiv, publisherInput);
            });
        }

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (authorSuggestDiv && !authorInput?.contains(e.target) && !authorSuggestDiv.contains(e.target)) {
                authorSuggestDiv.innerHTML = '';
            }
            if (publisherSuggestDiv && !publisherInput?.contains(e.target) && !publisherSuggestDiv.contains(e.target)) {
                publisherSuggestDiv.innerHTML = '';
            }
        });
    }

    /**
     * Handle autocomplete requests
     */
    function handleAutocomplete(query, type, suggestionDiv, inputElement) {
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
                            inputElement.value = item;
                            if (type === 'author') {
                                addAuthorFromInput();
                            }
                            suggestionDiv.innerHTML = '';
                        });
                        
                        suggestionDiv.appendChild(suggestion);
                    });
                }
            })
            .catch(error => {
                console.error('Autocomplete error:', error);
                suggestionDiv.innerHTML = '';
            });
    }

    /**
     * Initialize form submission with AJAX
     */
    function initializeFormSubmission() {
        console.log('Setting up form submission...');
        
        const forms = [
            document.getElementById('add-item-form'),
            document.getElementById('edit-product-form')
        ].filter(form => form !== null);
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                console.log('Form submission intercepted');
                
                // Update JSON fields before submission
                updateAuthorsJson();
                updateGenresJson();
                
                // Basic validation
                const title = form.querySelector('#item-title');
                const category = form.querySelector('#item-category');
                
                if (!title || !title.value.trim()) {
                    showMessage('Titel är obligatorisk', 'danger');
                    if (title) title.focus();
                    return false;
                }
                
                if (!category || !category.value) {
                    showMessage('Kategori är obligatorisk', 'danger');
                    if (category) category.focus();
                    return false;
                }
                
                console.log('Form validation passed', {
                    authors: window.productAuthors,
                    genres: window.productGenres,
                    images: productImageFiles.length
                });
                
                // Show loading state
                toggleSubmitButton(true);
                showMessage('Sparar produkt...', 'info');
                
                // Submit form via AJAX
                submitFormAjax(form);
            });
        });
    }

    /**
     * Submit form via AJAX
     */
    function submitFormAjax(form) {
        const formData = new FormData(form);
        
        // Debug: log form data
        console.log('Submitting form data:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }
        
        // Get the correct URL for the request
        let actionUrl = form.action || window.location.href;
        
        // Determine if this is add or edit form based on form ID
        const isEditForm = form.id === 'edit-product-form';
        
        if (isEditForm) {
            // For edit form, use the current URL (should already include the product ID)
            if (!actionUrl.includes('adminsingleproduct.php')) {
                // Fallback: construct the URL with current product ID
                const urlParams = new URLSearchParams(window.location.search);
                const productId = urlParams.get('id');
                if (productId) {
                    actionUrl = BASE_URL + '/admin/adminsingleproduct.php?id=' + productId;
                }
            }
        } else {
            // For add form, use addproduct.php
            actionUrl = BASE_URL + '/admin/addproduct.php';
        }
        
        console.log('Submitting to URL:', actionUrl);
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Check if response is actually JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.log('Non-JSON response received:', text.substring(0, 500));
                    throw new Error('Server returned HTML instead of JSON. Check PHP errors.');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Server response:', data);
            
            if (data.success) {
                showMessage(data.message, 'success');
                
                // If it's add product form, reset the form
                if (form.id === 'add-item-form') {
                    resetAll();
                    form.reset();
                }
            } else {
                showMessage(data.message || 'Ett fel inträffade', 'danger');
            }
        })
        .catch(error => {
            console.error('AJAX submission error:', error);
            showMessage('Ett fel inträffade vid sparande: ' + error.message, 'danger');
        })
        .finally(() => {
            // Hide loading state
            toggleSubmitButton(false);
        });
    }

    /**
     * Toggle submit button loading state
     */
    function toggleSubmitButton(loading) {
        const submitButtons = document.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            const submitText = button.querySelector('.submit-text');
            const submitSpinner = button.querySelector('.submit-spinner');
            
            if (loading) {
                button.disabled = true;
                if (submitText) submitText.classList.add('d-none');
                if (submitSpinner) submitSpinner.classList.remove('d-none');
            } else {
                button.disabled = false;
                if (submitText) submitText.classList.remove('d-none');
                if (submitSpinner) submitSpinner.classList.add('d-none');
            }
        });
    }

    /**
     * Show message to user (only warnings, errors, and save confirmations)
     */
    function showMessage(message, type = 'info') {
        console.log(`Message (${type}):`, message);
        
        // Try to use existing showMessage function
        if (typeof window.showMessage === 'function') {
            window.showMessage(message, type);
            return;
        }
        
        // Create message display
        let messageContainer = document.getElementById('message-container');
        if (!messageContainer) {
            messageContainer = document.createElement('div');
            messageContainer.id = 'message-container';
            messageContainer.style.position = 'fixed';
            messageContainer.style.top = '20px';
            messageContainer.style.right = '20px';
            messageContainer.style.zIndex = '9999';
            messageContainer.style.maxWidth = '400px';
            document.body.appendChild(messageContainer);
        }
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        messageContainer.appendChild(alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    /**
     * Reset all selections and image preview
     */
    function resetAll() {
        window.productAuthors = [];
        window.productGenres = [];
        productImageFiles = [];
        
        renderSelectedAuthors();
        renderSelectedGenres();
        updateAuthorsJson();
        updateGenresJson();
        
        const fileInput = document.getElementById('item-image-upload');
        if (fileInput) {
            fileInput.value = '';
        }
        
        const previewContainer = document.getElementById('item-image-previews');
        const defaultImage = document.getElementById('default-image-preview');
        
        // Only show default image if we're on add product page or no existing images
        const existingImagesContainer = document.getElementById('existing-images');
        const hasExistingImages = existingImagesContainer && existingImagesContainer.children.length > 0;
        
        if (previewContainer) {
            previewContainer.innerHTML = '';
            if (defaultImage && !hasExistingImages) {
                previewContainer.appendChild(defaultImage);
                defaultImage.style.display = 'block';
            }
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProductHandlers);
    } else {
        // DOM is already ready
        initializeProductHandlers();
    }

    // Also initialize when content is loaded via AJAX
    document.addEventListener('contentLoaded', function() {
        console.log('Content loaded via AJAX, reinitializing...');
        isInitialized = false;
        imageHandlerInitialized = false;
        initializeProductHandlers();
    });

    // Expose utility functions globally
    window.addProductHandlers = {
        addAuthor: addAuthorFromInput,
        removeAuthor: removeAuthor,
        addGenre: addGenreFromSelect,
        removeGenre: removeGenre,
        updateAuthorsJson: updateAuthorsJson,
        updateGenresJson: updateGenresJson,
        resetAll: resetAll,
        reinitialize: function() {
            isInitialized = false;
            imageHandlerInitialized = false;
            initializeProductHandlers();
        }
    };

    console.log('Product handlers script loaded');

})();