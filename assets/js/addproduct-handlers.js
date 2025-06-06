/**
 * addproduct-handlers.js - Fixed version with proper image preview reset
 * Handles multiple authors, genres, image preview, and AJAX form submission
 * Now properly supports both add and edit product forms with image preview reset
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
            return;
        }
        
        
        // Check if we're on a product form page
        const productForm = document.getElementById('add-item-form') || document.getElementById('edit-product-form');
        if (!productForm) {
            return;
        }
        
        // Initialize all components
        initializeAuthorsManagement();
        initializeGenresManagement();
        initializeImagePreview();
        initializeAutocomplete();
        initializeFormSubmission();
        initializeResetHandlers(); // NEW: Initialize reset handlers
        
        isInitialized = true;
    }

    /**
     * NEW: Initialize reset button handlers
     */
    function initializeResetHandlers() {
        
        // Handle reset buttons
        document.addEventListener('click', function(e) {
            // Check if clicked element is a reset button
            if (e.target && e.target.type === 'reset') {
                const form = e.target.closest('form');
                if (form && (form.id === 'add-item-form' || form.id === 'edit-product-form')) {
                    // Prevent default reset to handle it manually
                    e.preventDefault();
                    
                    resetAllFormFields(form);
                }
            }
        });
        
        // Also handle the native form reset event as a backup
        document.addEventListener('reset', function(e) {
            const form = e.target;
            if (form && (form.id === 'add-item-form' || form.id === 'edit-product-form')) {
                
                // Use setTimeout to let the native reset complete first
                setTimeout(() => {
                    resetImagePreview(form);
                    renderSelectedAuthors();
                    renderSelectedGenres();
                }, 10);
            }
        });
    }

    /**
     * Initialize authors management
     */
    function initializeAuthorsManagement() {
        
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
        }
    }

    /**
     * Initialize genres management
     */
    function initializeGenresManagement() {
        
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
        }
    }

    /**
     * Initialize image preview functionality
     */
    function initializeImagePreview() {
        if (imageHandlerInitialized) {
            return;
        }
        
        
        // These are defined in the outer scope of initializeImagePreview,
        // making them accessible to nested functions like renderImagePreviews and removeImage.
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


        fileInput.addEventListener('change', function(event) {
            const newFiles = Array.from(event.target.files);

            newFiles.forEach(file => {
                const isDuplicate = productImageFiles.some(existingFile =>
                    existingFile.name === file.name && existingFile.size === file.size
                );

                if (productImageFiles.length < MAX_IMAGES && !isDuplicate) {
                    productImageFiles.push(file);
                } else if (productImageFiles.length >= MAX_IMAGES) {
                    showMessage('Du kan ladda upp högst ' + MAX_IMAGES + ' bilder.', 'warning');
                    return; // Exit forEach early if max is reached
                }
            });

            fileInput.value = ''; // Allow re-selecting the same file if removed
            updateFileInputFiles();
            renderImagePreviews(); // Calls the updated renderImagePreviews
        });

        imageHandlerInitialized = true;

        /**
         * Render image previews
         */
        function renderImagePreviews() {
            // previewContainer and defaultImage are accessible from the outer scope
            if (!previewContainer) return;

            const isNewImageContainer = previewContainer.id === 'item-image-previews'; 

            if (isNewImageContainer) {
                previewContainer.innerHTML = ''; 
            }

            if (productImageFiles.length > 0) {
                productImageFiles.forEach((file, index) => {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const imgContainer = document.createElement('div');
                        imgContainer.classList.add(
                            'd-flex',             // Use Bootstrap flexbox for side-by-side layout
                            'align-items-center', // Vertically align items (image and button) in the middle
                            'mb-3',               // Margin bottom for spacing between image rows
                            'p-2',                // Padding inside the container
                            'rounded'             // Rounded corners for the container
                        );

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = `Produktbild förhandsgranskning ${index + 1}`;
                        img.classList.add('rounded'); // Apply rounded corners to the image itself
                        img.style.maxWidth = '150px';
                        img.style.maxHeight = '150px';
                        img.style.objectFit = 'cover';    // Ensure the image covers the dimensions without distortion
                        img.style.marginRight = '1rem';   // Add space between the image and the button

                        const removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className = 'btn btn-sm btn-danger'; 
                        removeButton.innerHTML = 'Ta bort';
                        removeButton.title = 'Ta bort bild från urval';

                        removeButton.onclick = (event) => {
                            event.stopPropagation(); 
                            if (confirm('Vill du ta bort denna bild från förhandsgranskningen? Den är ännu inte uppladdad.')) {
                                removeImage(index); 
                            }
                        };

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeButton);
                        
                        if (previewContainer && isNewImageContainer) { 
                           previewContainer.appendChild(imgContainer);
                        }
                    };

                    reader.onerror = function() {
                        showMessage('Fel vid läsning av bild: ' + file.name, 'danger');
                    };

                    reader.readAsDataURL(file);
                });
            } else {
                if (defaultImage && isNewImageContainer) {
                    previewContainer.appendChild(defaultImage); 
                    defaultImage.style.display = 'block'; 
                }
            }
        }

        /**
         * Remove image from preview
         */
        function removeImage(indexToRemove) {
            // previewContainer and defaultImage are accessible from the outer scope
            if (indexToRemove >= 0 && indexToRemove < productImageFiles.length) {
                const removedFile = productImageFiles[indexToRemove];
                productImageFiles.splice(indexToRemove, 1);
                updateFileInputFiles();
                renderImagePreviews(); // This will re-render based on the current state
                
            }
        }

        /**
         * Update file input with current files
         */
        function updateFileInputFiles() {
            // fileInput is accessible from the outer scope
            if (!fileInput) return;
            
            try {
                const dataTransfer = new DataTransfer();
                productImageFiles.forEach(file => {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
                
            } catch (error) {
                console.error('Error updating file input:', error);
            }
        }
    }

    /**
     * NEW: Reset image preview to default state
     */
    function resetImagePreview(form) {
        
        // Clear the global image files array
        productImageFiles = [];
        
        // Clear the file input
        const fileInput = form ? form.querySelector('#item-image-upload') : document.getElementById('item-image-upload');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Reset the preview container
        const previewContainer = form ? form.querySelector('#item-image-previews') : document.getElementById('item-image-previews');
        const defaultImage = form ? form.querySelector('#default-image-preview') : document.getElementById('default-image-preview');
        
        if (previewContainer) {
            previewContainer.innerHTML = '';
            if (defaultImage) {
                // Clone the default image to avoid moving the original
                const defaultClone = defaultImage.cloneNode(true);
                previewContainer.appendChild(defaultClone);
                defaultClone.style.display = 'block';
            } else {
                // If no default image element found, create one
                const newDefaultImage = document.createElement('img');
                newDefaultImage.src = 'assets/images/default_antiqe_image.webp';
                newDefaultImage.alt = 'Standard produktbild';
                newDefaultImage.className = 'img-fluid rounded shadow default-preview';
                newDefaultImage.id = 'default-image-preview';
                previewContainer.appendChild(newDefaultImage);
            }
        }
        
    }

    /**
     * Initialize autocomplete functionality
     */
    function initializeAutocomplete() {
        
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
                suggestionDiv.innerHTML = '';
            });
    }

    /**
     * Initialize form submission with AJAX
     */
    function initializeFormSubmission() {
        
        const forms = [
            document.getElementById('add-item-form'),
            document.getElementById('edit-product-form')
        ].filter(form => form !== null);
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                
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
                
                ;
                
                // Show loading state
                toggleSubmitButton(form, true); // Pass form context

                submitFormAjax(form);
            });
        });
    }

    /**
     * Submit form via AJAX
     */
    function submitFormAjax(form) {
        const formData = new FormData(form);
        
        for (let [key, value] of formData.entries()) {
            // For File objects, log the name, not the object itself, for brevity.
            if (value instanceof File) {
                console.log(key, value.name);
            } else {
                console.log(key, value);
            }
        }
        
        let actionUrl = form.getAttribute('action') || window.location.href;
        
        const isEditForm = form.id === 'edit-product-form';
        if (typeof BASE_URL !== 'undefined') {
            if (isEditForm) {
                if (!actionUrl.includes('adminsingleproduct.php')) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const productId = urlParams.get('id');
                    if (productId) {
                        actionUrl = BASE_URL + '/admin/adminsingleproduct.php?id=' + productId;
                    }
                }
            } else { // add-item-form
                actionUrl = BASE_URL + '/admin/addproduct.php';
            }
        }
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            const contentType = response.headers.get('content-type');
            
            if (!response.ok) {
                 return response.text().then(text => {
                    throw new Error(`HTTP error ${response.status}: ${text.substring(0, 200)}`);
                });
            }
            
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.warn('Non-JSON response received:', text.substring(0, 500));
                    if (response.ok && text.toLowerCase().includes("success")) {
                         return { success: true, message: "Operation successful (non-JSON response)." };
                    }
                    throw new Error('Server returned non-JSON content. Check PHP errors or response type.');
                });
            }
            
            return response.json();
        })
        .then(data => {
    
    if (data.success) {
        showMessage(data.message || 'Åtgärden lyckades!', 'success');
        if (form.id === 'add-item-form') {
            resetAllFormFields(form);
        }
        // Scroll to top to show the success message
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        showMessage(data.message || 'Ett fel inträffade.', 'danger');
        // Also scroll to top for error messages
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
})
        .catch(error => {
            showMessage('Ett allvarligt fel inträffade vid kommunikation med servern: ' + error.message, 'danger');
        })
        .finally(() => {
            toggleSubmitButton(form, false);
        });
    }

    /**
     * Toggle submit button loading state
     */
    function toggleSubmitButton(form, loading) {
        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton) return;

        const submitText = submitButton.querySelector('.submit-text');
        const submitSpinner = submitButton.querySelector('.submit-spinner');
        
        if (loading) {
            submitButton.disabled = true;
            if (submitText) submitText.classList.add('d-none');
            if (submitSpinner) submitSpinner.classList.remove('d-none');
        } else {
            submitButton.disabled = false;
            if (submitText) submitText.classList.remove('d-none');
            if (submitSpinner) submitSpinner.classList.add('d-none');
        }
    }

    
    /**
     * UPDATED: Reset all form fields, selections, and image previews for the given form.
     */
    function resetAllFormFields(form) {
        if (!form) return;
        
        
        // Reset native form fields first
        form.reset();

        // Reset authors and genres
        window.productAuthors = [];
        window.productGenres = [];
        renderSelectedAuthors();
        renderSelectedGenres();
        updateAuthorsJson();
        updateGenresJson();

        // Reset image preview
        resetImagePreview(form);
        
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProductHandlers);
    } else {
        initializeProductHandlers();
    }

    // Also initialize when content is loaded via AJAX (if you use a custom 'contentLoaded' event)
    document.addEventListener('contentLoaded', function() {
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
        resetImagePreview: resetImagePreview,
        resetAll: function() {
            const form = document.getElementById('add-item-form') || document.getElementById('edit-product-form');
            if (form) resetAllFormFields(form);
        },
        reinitialize: function() {
            isInitialized = false;
            imageHandlerInitialized = false;
            initializeProductHandlers();
        }
    };


})();