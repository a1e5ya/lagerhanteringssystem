/**
 * Newsletter subscription handling with modal support and reCAPTCHA
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize newsletter functionality
    initNewsletterModal();
    initFooterNewsletterForm();
    checkUrlStatus();
});

/**
 * Initialize newsletter modal form
 */
function initNewsletterModal() {
    const modalForm = document.getElementById('newsletter-form-modal');
    
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleNewsletterSubmission(modalForm, true);
        });
    }
}

/**
 * Initialize footer newsletter form (if still used)
 */
function initFooterNewsletterForm() {
    const footerForm = document.getElementById('newsletter-form');
    
    if (footerForm) {
        footerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleNewsletterSubmission(footerForm, false);
        });
    }
}

/**
 * Handle newsletter form submission
 * @param {HTMLFormElement} form - The form element
 * @param {boolean} isModal - Whether this is from modal or footer
 */
function handleNewsletterSubmission(form, isModal = true) {
    // Get form data
    const formData = new FormData(form);
    const email = formData.get('email')?.trim();
    const name = formData.get('name')?.trim() || null;
    const language = formData.get('language') || detectCurrentLanguage();
    
    // Basic validation
    if (!email || !isValidEmail(email)) {
        showValidationError('Vänligen ange en giltig e-postadress', isModal);
        return;
    }
    
    // reCAPTCHA validation (only for modal and if reCAPTCHA is loaded)
    if (isModal && typeof grecaptcha !== 'undefined') {
        const recaptchaResponse = grecaptcha.getResponse();
        if (!recaptchaResponse) {
            showRecaptchaError();
            return;
        }
        formData.append('recaptcha_response', recaptchaResponse);
    }
    
    // Add detected language
    formData.set('language', language);
    
    // Show loading state
    setLoadingState(isModal, true);
    
    // Send AJAX request
    submitNewsletterData(formData, isModal);
}

/**
 * Submit newsletter data via AJAX
 * @param {FormData} formData - The form data
 * @param {boolean} isModal - Whether this is from modal
 */
function submitNewsletterData(formData, isModal) {
    console.log('Submitting newsletter data...'); // Debug log
    
    // Use fetch API for better error handling
    fetch('newsletter.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug log
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug log
        setLoadingState(isModal, false);
        
        if (data.status === 'success') {
            console.log('Handling success response'); // Debug log
            handleSuccessResponse(data, isModal);
        } else if (data.status === 'info') {
            console.log('Handling info response - showing as success modal'); // Debug log
            // Show info messages the same way as success messages
            handleSuccessResponse(data, isModal);
        } else {
            console.log('Handling error response:', data.status, data.message); // Debug log
            showMessage(data.message, data.status, isModal);
        }
    })
    .catch(error => {
        console.error('Newsletter subscription error:', error);
        setLoadingState(isModal, false);
        
        const errorMessage = getCurrentLanguage() === 'fi' ? 
            'Virhe tapahtui. Yritä uudelleen myöhemmin.' :
            'Ett fel uppstod. Försök igen senare.';
        
        showMessage(errorMessage, 'error', isModal);
    });
}

/**
 * Handle successful subscription
 * @param {Object} data - Response data
 * @param {boolean} isModal - Whether this is from modal
 */
function handleSuccessResponse(data, isModal) {
    console.log('Success response:', data); // Debug log
    
    if (isModal) {
        // Reset form first
        const modalForm = document.getElementById('newsletter-form-modal');
        if (modalForm) {
            modalForm.reset();
        }
        
        // Reset reCAPTCHA if available
        if (typeof grecaptcha !== 'undefined') {
            try {
                grecaptcha.reset();
            } catch (e) {
                console.log('reCAPTCHA reset failed:', e);
            }
        }
        
        // Hide newsletter modal with a slight delay
        const newsletterModal = bootstrap.Modal.getInstance(document.getElementById('newsletterModal'));
        if (newsletterModal) {
            newsletterModal.hide();
        }
        
        // Show success modal after a brief delay
        setTimeout(() => {
            showSuccessModal(data.message);
        }, 300);
        
    } else {
        // For footer form, show inline message
        showMessage(data.message, 'success', false);
        const footerForm = document.getElementById('newsletter-form');
        if (footerForm) {
            footerForm.reset();
        }
    }
}

/**
 * Show success modal
 * @param {string} message - Success message
 */
function showSuccessModal(message) {
    // Update success message
    const successMessageEl = document.getElementById('success-message');
    if (successMessageEl) {
        successMessageEl.textContent = message;
    }
    
    // Show success modal
    const successModal = new bootstrap.Modal(document.getElementById('newsletterSuccessModal'));
    successModal.show();
}

/**
 * Set loading state for form
 * @param {boolean} isModal - Whether this is from modal
 * @param {boolean} loading - Loading state
 */
function setLoadingState(isModal, loading) {
    const submitBtn = isModal ? 
        document.getElementById('newsletter-submit-btn') : 
        document.querySelector('#newsletter-form button[type="submit"]');
    
    const spinner = document.getElementById('newsletter-spinner');
    
    if (submitBtn) {
        submitBtn.disabled = loading;
        
        if (spinner) {
            spinner.style.display = loading ? 'inline-block' : 'none';
        }
        
        // Update button text
        const buttonText = submitBtn.querySelector('#newsletter-submit-text') || submitBtn;
        if (loading) {
            buttonText.textContent = getCurrentLanguage() === 'fi' ? 'Lähetetään...' : 'Skickar...';
        } else {
            buttonText.textContent = getCurrentLanguage() === 'fi' ? 'Tilaa' : 'Prenumerera';
        }
    }
}

/**
 * Show validation error
 * @param {string} message - Error message
 * @param {boolean} isModal - Whether to show in modal context
 */
function showValidationError(message, isModal) {
    if (isModal) {
        const emailInput = document.getElementById('newsletter-email-modal');
        if (emailInput) {
            emailInput.classList.add('is-invalid');
            
            // Remove existing feedback
            const existingFeedback = emailInput.parentNode.querySelector('.invalid-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            // Add new feedback
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            emailInput.parentNode.appendChild(feedback);
            
            // Remove error after user starts typing
            emailInput.addEventListener('input', function() {
                emailInput.classList.remove('is-invalid');
                const feedback = emailInput.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }, { once: true });
        }
    } else {
        showMessage(message, 'error', false);
    }
}

/**
 * Show reCAPTCHA error
 */
function showRecaptchaError() {
    const recaptchaError = document.getElementById('recaptcha-error');
    if (recaptchaError) {
        recaptchaError.style.display = 'block';
        
        // Hide error when reCAPTCHA is completed
        const originalCallback = window.recaptchaCallback;
        window.recaptchaCallback = function() {
            recaptchaError.style.display = 'none';
            if (originalCallback) {
                originalCallback();
            }
        };
    }
}

/**
 * Show message to the user
 * @param {string} message - Message text
 * @param {string} type - Message type (success, error, info)
 * @param {boolean} isModal - Whether to show in modal context
 */
function showMessage(message, type, isModal = false) {
    console.log('Showing message:', message, 'Type:', type, 'Is modal:', isModal); // Debug log
    
    try {
        if (isModal) {
            // For modal, we'll use a toast or alert within the modal
            const modalBody = document.querySelector('#newsletterModal .modal-body');
            console.log('Modal body found:', modalBody); // Debug log
            
            if (modalBody) {
                // Remove existing alerts
                const existingAlert = modalBody.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Create new alert
                const alertClass = type === 'success' ? 'success' : 
                                  type === 'info' ? 'info' : 
                                  type === 'warning' ? 'warning' : 'danger';
                
                const alert = document.createElement('div');
                alert.className = `alert alert-${alertClass} alert-dismissible fade show`;
                alert.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                modalBody.insertBefore(alert, modalBody.firstChild);
                console.log('Alert added to modal'); // Debug log
                
                // Auto-hide info messages after 4 seconds
                if (type === 'info') {
                    setTimeout(() => {
                        if (alert && alert.parentNode) {
                            alert.remove();
                        }
                    }, 4000);
                }
            } else {
                console.error('Modal body not found!');
            }
        } else {
            // For footer form, create a message container
            let messageContainer = document.getElementById('newsletter-message');
            
            if (!messageContainer) {
                messageContainer = document.createElement('div');
                messageContainer.id = 'newsletter-message';
                messageContainer.className = 'mt-3';
                
                const form = document.getElementById('newsletter-form');
                if (form) {
                    form.parentNode.appendChild(messageContainer);
                }
            }
            
            // Set message content and style
            const alertClass = type === 'success' ? 'success' : 
                              type === 'info' ? 'info' : 
                              type === 'warning' ? 'warning' : 'danger';
            
            messageContainer.innerHTML = `
                <div class="alert alert-${alertClass} alert-dismissible fade show">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                if (messageContainer) {
                    messageContainer.style.display = 'none';
                }
            }, 5000);
        }
    } catch (error) {
        console.error('Error in showMessage:', error);
        // Fallback: simple alert
        alert(message);
    }
}

/**
 * Detect current page language
 * @returns {string} Language code (sv or fi)
 */
function detectCurrentLanguage() {
    // Check various ways the language might be set
    const htmlLang = document.documentElement.lang;
    const urlParams = new URLSearchParams(window.location.search);
    const urlLang = urlParams.get('lang');
    const bodyClass = document.body.className;
    
    // Check URL parameter first
    if (urlLang === 'fi') return 'fi';
    if (urlLang === 'sv') return 'sv';
    
    // Check HTML lang attribute
    if (htmlLang && htmlLang.startsWith('fi')) return 'fi';
    if (htmlLang && htmlLang.startsWith('sv')) return 'sv';
    
    // Check body class
    if (bodyClass.includes('lang-fi')) return 'fi';
    if (bodyClass.includes('lang-sv')) return 'sv';
    
    // Check session or other indicators
    // If the page contains Finnish text elements, it's probably Finnish
    const finnishElements = document.querySelector('[data-lang="fi"], .lang-fi');
    if (finnishElements) return 'fi';
    
    // Default to Swedish
    return 'sv';
}

/**
 * Get current language for UI messages
 * @returns {string} Language code
 */
function getCurrentLanguage() {
    return detectCurrentLanguage();
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} Whether email is valid
 */
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

/**
 * Check URL for newsletter status and show message
 */
function checkUrlStatus() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('newsletter')) {
        const status = urlParams.get('newsletter');
        const language = getCurrentLanguage();
        
        let message = '';
        switch (status) {
            case 'success':
                message = language === 'fi' ? 
                    'Kiitos uutiskirjeen tilauksesta!' : 
                    'Tack för din prenumeration på vårt nyhetsbrev!';
                showMessage(message, 'success', false);
                break;
            case 'info':
                message = language === 'fi' ? 
                    'Tilaat jo uutiskirjettämme' : 
                    'Du prenumererar redan på vårt nyhetsbrev';
                showMessage(message, 'info', false);
                break;
            case 'error':
                message = language === 'fi' ? 
                    'Virhe tapahtui. Yritä uudelleen myöhemmin.' : 
                    'Ett fel uppstod. Försök igen senare.';
                showMessage(message, 'error', false);
                break;
        }
        
        // Clean URL
        const newUrl = window.location.href.split('?')[0];
        window.history.replaceState({}, document.title, newUrl);
    }
}

/**
 * reCAPTCHA callback (global function for reCAPTCHA)
 */
window.recaptchaCallback = function() {
    const recaptchaError = document.getElementById('recaptcha-error');
    if (recaptchaError) {
        recaptchaError.style.display = 'none';
    }
};