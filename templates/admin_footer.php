<?php
/**
 * Admin Footer Template (Updated with JS Loader)
 * 
 * Contains:
 * - Simple admin footer
 * - Centralized JavaScript loading for admin functionality
 */
?>

<!-- Footer for Admin Pages -->
<footer class="footer text-white py-4 mt-auto">
    <div class="container">
        <div class="text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Axxell. Alla rättigheter förbehållna.</p>
        </div>
    </div>
</footer>


<script>
// Fix Bootstrap modal accessibility issues with focus management
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle all existing and future modals
    document.addEventListener('show.bs.modal', function(event) {
        const modal = event.target;
        // Remove aria-hidden when showing
        modal.removeAttribute('aria-hidden');
    });
    
    document.addEventListener('hide.bs.modal', function(event) {
        const modal = event.target;
        // Remove focus from any focused elements inside the modal
        const focusedElement = modal.querySelector(':focus');
        if (focusedElement) {
            focusedElement.blur();
        }
    });
    
    document.addEventListener('hidden.bs.modal', function(event) {
        const modal = event.target;
        // Ensure aria-hidden is properly set after modal is hidden
        modal.setAttribute('aria-hidden', 'true');
        
        // Double-check no elements inside have focus
        const anyFocused = modal.querySelector(':focus');
        if (anyFocused) {
            anyFocused.blur();
        }
    });
    
    // Also handle any modals that might already be open
    const openModals = document.querySelectorAll('.modal.show');
    openModals.forEach(modal => {
        modal.removeAttribute('aria-hidden');
    });
    
});

// Additional fix for specific modal operations
function hideModalSafely(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        // Remove focus first
        const focusedElement = modal.querySelector(':focus');
        if (focusedElement) {
            focusedElement.blur();
        }
        
        // Then hide the modal
        const bootstrapModal = bootstrap.Modal.getInstance(modal);
        if (bootstrapModal) {
            bootstrapModal.hide();
        }
    }
}

// Override any existing modal hide functions
if (typeof hideModal === 'function') {
    const originalHideModal = hideModal;
    window.hideModal = function(modalId) {
        hideModalSafely(modalId);
    };
}
</script>
</body>
</html>