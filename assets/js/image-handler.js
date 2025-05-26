/**
 * image-handler.js
 * Handles client-side image preview for multiple file uploads.
 */

// Function to set up image preview
function setupImagePreview() {
    const fileInput = document.getElementById('item-image-upload');
    const previewContainer = document.getElementById('item-image-previews');
    const defaultImage = document.getElementById('default-image-preview');

    if (fileInput && previewContainer) {
        fileInput.addEventListener('change', function(event) {
            previewContainer.innerHTML = ''; // Clear previous images

            if (event.target.files && event.target.files.length > 0) {
                // Hide the default image if new images are selected
                if (defaultImage) {
                    defaultImage.style.display = 'none';
                }

                for (let i = 0; i < event.target.files.length; i++) {
                    const file = event.target.files[i];
                    // Only process image files
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = "Product image preview " + (i + 1);
                            img.classList.add('img-fluid', 'rounded', 'shadow');
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                }
            } else {
                // If no files are selected, or all files were removed, show the default image
                if (defaultImage) {
                    previewContainer.appendChild(defaultImage); // Re-append default image
                    defaultImage.style.display = 'block';
                }
            }
        });
    }
}