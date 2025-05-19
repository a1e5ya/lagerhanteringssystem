/**
 * Newsletter subscription handling
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the newsletter form
    const form = document.getElementById('newsletter-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get the email input
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput.value.trim();
            
            // Basic validation
            if (!email || !isValidEmail(email)) {
                showMessage('Vänligen ange en giltig e-postadress', 'error');
                return;
            }
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Send AJAX request
            submitNewsletter(formData);
        });
    }
    
    // Check for newsletter status in URL and show message
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('newsletter')) {
        const status = urlParams.get('newsletter');
        
        if (status === 'success') {
            showMessage('Tack för din prenumeration på vårt nyhetsbrev!', 'success');
        } else if (status === 'info') {
            showMessage('Du prenumererar redan på vårt nyhetsbrev', 'info');
        } else if (status === 'error') {
            showMessage('Ett fel uppstod. Försök igen senare.', 'error');
        }
    }
});

/**
 * Submit newsletter form via AJAX
 */
function submitNewsletter(formData) {
    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'newsletter.php', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    // Handle response
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                // Show message based on status
                showMessage(response.message, response.status);
                
                // Clear form on success
                if (response.status === 'success') {
                    document.getElementById('newsletter-form').reset();
                }
            } catch (e) {
                showMessage('Ett oväntat fel uppstod', 'error');
            }
        } else {
            showMessage('Ett fel uppstod. Försök igen senare.', 'error');
        }
    };
    
    // Handle network errors
    xhr.onerror = function() {
        showMessage('Kunde inte ansluta till servern. Kontrollera din internetanslutning.', 'error');
    };
    
    // Send the request
    xhr.send(formData);
}

/**
 * Show message to the user
 */
function showMessage(message, type) {
    // Find existing message container or create a new one
    let messageContainer = document.getElementById('newsletter-message');
    
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.id = 'newsletter-message';
        
        // Insert after the form
        const form = document.getElementById('newsletter-form');
        form.parentNode.insertBefore(messageContainer, form.nextSibling);
    }
    
    // Set message content and style
    messageContainer.textContent = message;
    messageContainer.className = 'alert alert-' + 
        (type === 'success' ? 'success' : 
         type === 'info' ? 'info' : 'danger');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageContainer.style.display = 'none';
    }, 5000);
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}