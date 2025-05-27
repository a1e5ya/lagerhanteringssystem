/**
 * image-handler.js
 * Handles client-side image preview for multiple file uploads.
 * Allows adding multiple images one-by-one and removing them from preview by clicking the image.
 */

// Array to store all currently selected File objects for preview and eventual upload
let selectedFiles = [];
const MAX_IMAGES = 10; // Maximum number of images allowed

// Function to set up image preview
function setupImagePreview() {
    const fileInput = document.getElementById('item-image-upload');
    const previewContainer = document.getElementById('item-image-previews');
    const defaultImage = document.getElementById('default-image-preview');

    // Ensure the file input element exists and attach event listener
    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function(event) {
            // Get newly selected files
            const newFiles = Array.from(event.target.files);

            // Process new files: add to selectedFiles if not duplicate and under limit
            newFiles.forEach(file => {
                // Simple duplicate check by name and size. Can be enhanced if needed.
                const isDuplicate = selectedFiles.some(existingFile =>
                    existingFile.name === file.name && existingFile.size === file.size
                );

                if (selectedFiles.length < MAX_IMAGES && !isDuplicate) {
                    selectedFiles.push(file);
                } else if (selectedFiles.length >= MAX_IMAGES) {
                    // Optional: Show a message to the user that the limit is reached
                    if (typeof showMessage === 'function') {
                        showMessage('Du kan ladda upp högst ' + MAX_IMAGES + ' bilder.', 'warning');
                    }
                    return; // Stop adding more files if limit is hit
                }
            });

            // After adding new files, clear the input's value to allow re-selection of the same file
            // This also clears the "selected" state in the browser's UI, which is fine
            // because `selectedFiles` array is now the source of truth for our previews.
            fileInput.value = ''; 

            // Update the actual file input's `files` property so they are sent on form submission
            updateFileInputFiles();

            // Re-render all selected images in the preview area
            renderPreviews();
        });
    }

    // Function to render all images from the `selectedFiles` array into the preview container
    function renderPreviews() {
        previewContainer.innerHTML = ''; // Clear existing previews before re-rendering

        if (selectedFiles.length > 0) {
            // Hide the default image if there are selected files
            if (defaultImage) {
                defaultImage.style.display = 'none';
            }

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const imgWrapper = document.createElement('div');
                    // Use Bootstrap utility classes for layout (inline-block, margin) and relative positioning
                    imgWrapper.classList.add('position-relative', 'd-inline-block', 'me-2', 'mb-2', 'border', 'rounded', 'shadow-sm'); 
                    imgWrapper.style.maxWidth = '150px'; // Set a max width for the wrapper
                    imgWrapper.style.maxHeight = '150px'; // Set a max height for the wrapper
                    imgWrapper.style.overflow = 'hidden'; // Hide overflow if image is larger
                    imgWrapper.style.cursor = 'pointer'; // Indicate that the wrapper is clickable

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = `Product image preview ${index + 1}`;
                    // Use Bootstrap classes for responsive images, rounded corners, and shadow
                    img.classList.add('img-fluid', 'rounded'); // Removed shadow from here as wrapper has it
                    img.style.objectFit = 'cover'; // Ensure image covers the wrapper area
                    img.style.width = '100%';
                    img.style.height = '100%';

                    // Attach the remove functionality directly to the image wrapper
                    imgWrapper.onclick = () => {
                        // Confirm removal to prevent accidental deletions
                        if (confirm('Vill du ta bort den här bilden?')) { // "Do you want to remove this image?" in Swedish
                            removeImage(index);
                        }
                    };

                    imgWrapper.appendChild(img);
                    previewContainer.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file); // Read the file as a Data URL for preview
            });
        } else {
            // If no files are selected (or all were removed), show the default image
            if (defaultImage) {
                previewContainer.appendChild(defaultImage); // Re-append default image to the container
                defaultImage.style.display = 'block'; // Ensure it's visible
            }
        }
    }

    // Function to remove an image from the `selectedFiles` array by its index
    function removeImage(indexToRemove) {
        selectedFiles.splice(indexToRemove, 1); // Remove the file from the array
        updateFileInputFiles(); // Update the hidden file input's `files` property
        renderPreviews(); // Re-render the preview to reflect the change
    }

    // Crucial function to update the actual file input's `files` property
    // This uses the DataTransfer API to create a new FileList from our `selectedFiles` array,
    // which is then assigned back to the input, making them available for form submission.
    function updateFileInputFiles() {
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        fileInput.files = dataTransfer.files; // Assign the new FileList to the input
    }

    // Initial render call when the DOM is loaded
    renderPreviews();
}

// Ensure the setup function runs when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', setupImagePreview);