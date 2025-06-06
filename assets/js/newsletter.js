/**
 * Newsletter subscription handling - Restored Working Version
 * Based on your original working code with minimal reCAPTCHA addition
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize newsletter functionality
    initNewsletterModal();
    initFooterNewsletterForm();
    checkUrlStatus();
    
    // Initialize reCAPTCHA v3 if available
    if (typeof grecaptcha !== 'undefined') {
        grecaptcha.ready(function() {

        });
    }
});

/**
 * Initialize newsletter modal form
 */
function initNewsletterModal() {
    const modalForm = document.getElementById('newsletter-form-modal');
    const emailInput = document.getElementById('newsletter-email-modal');
    
    if (modalForm) {
        // Update button state on email input
        if (emailInput) {
            emailInput.addEventListener('input', updateSubmitButton);
            emailInput.addEventListener('blur', updateSubmitButton);
            updateSubmitButton(); // Initial check
        }
        
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleNewsletterSubmission(modalForm, true);
        });
    }
}

/**
 * Update submit button state
 */
function updateSubmitButton() {
    const submitBtn = document.getElementById('newsletter-submit-btn');
    const emailInput = document.getElementById('newsletter-email-modal');
    
    if (!submitBtn || !emailInput) return;
    
    const emailValid = emailInput.value.trim() !== '' && emailInput.checkValidity();
    submitBtn.disabled = !emailValid;
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
async function handleNewsletterSubmission(form, isModal = true) {
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
    
    // Add detected language
    formData.set('language', language);
    
    // Try to get reCAPTCHA token if available
    if (typeof grecaptcha !== 'undefined') {
        try {
            const siteKey = document.querySelector('script[src*="render="]')?.src.match(/render=([^&]+)/)?.[1];
            if (siteKey) {
                const token = await new Promise((resolve, reject) => {
                    grecaptcha.ready(() => {
                        grecaptcha.execute(siteKey, { action: 'newsletter_subscribe' })
                            .then(resolve)
                            .catch(reject);
                    });
                });
                formData.set('g-recaptcha-response', token);
            }
        } catch (error) {
            console.warn('reCAPTCHA token failed, continuing without it:', error);
        }
    }
    
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
    
    // Use fetch API for better error handling
    fetch('newsletter.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        setLoadingState(isModal, false);
        
        if (data.status === 'success') {
            handleSuccessResponse(data, isModal);
        } else if (data.status === 'info') {
            // Show info messages the same way as success messages
            handleSuccessResponse(data, isModal);
        } else {
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
    
    if (isModal) {
        // Reset form first
        const modalForm = document.getElementById('newsletter-form-modal');
        if (modalForm) {
            modalForm.reset();
            updateSubmitButton();
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
        const alertContainer = document.getElementById('newsletter-alert');
        if (alertContainer) {
            alertContainer.className = 'alert alert-danger';
            alertContainer.textContent = message;
            alertContainer.classList.remove('d-none');
            
            // Hide alert after 5 seconds
            setTimeout(() => {
                alertContainer.classList.add('d-none');
            }, 5000);
        }
    } else {
        showMessage(message, 'error', false);
    }
}

/**
 * Show message (placeholder - implement based on your message system)
 * @param {string} message - Message to show
 * @param {string} type - Message type (success, error, info)
 * @param {boolean} isModal - Whether in modal context
 */
function showMessage(message, type, isModal) {
    // Implement based on your existing message system
    
    if (isModal) {
        const alertContainer = document.getElementById('newsletter-alert');
        if (alertContainer) {
            alertContainer.className = `alert alert-${type === 'error' ? 'danger' : type === 'info' ? 'info' : 'success'}`;
            alertContainer.textContent = message;
            alertContainer.classList.remove('d-none');
            
            setTimeout(() => {
                alertContainer.classList.add('d-none');
            }, 5000);
        }
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