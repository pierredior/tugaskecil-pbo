// assets/js/main.js

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initTooltips();
    initModals();
    initForms();
    initTables();
});

// Initialize tooltips
function initTooltips() {
    // Add tooltip functionality if needed
    // This is a placeholder for future tooltip implementation
}

// Initialize modal functionality
function initModals() {
    // Handle modal close buttons
    const closeButtons = document.querySelectorAll('.modal-close');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            modal.classList.remove('modal-open');
        });
    });

    // Handle backdrop clicks to close modals
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('modal-open');
            }
        });
    });
}

// Initialize form functionality
function initForms() {
    // Add form validation and submission handling
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
            }
        });
    });

    // Add real-time validation
    const inputs = document.querySelectorAll('input[data-validate], textarea[data-validate], select[data-validate]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
}

// Validate a single form field
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.getAttribute('name');
    const validations = field.getAttribute('data-validate').split('|');
    
    for (let validation of validations) {
        if (validation.startsWith('required') && !value) {
            showError(field, `${getFieldLabel(field)} is required`);
            return false;
        }
        
        if (validation.startsWith('email') && value && !isValidEmail(value)) {
            showError(field, 'Please enter a valid email address');
            return false;
        }
        
        if (validation.startsWith('min:') && value) {
            const minVal = validation.split(':')[1];
            if (value.length < minVal) {
                showError(field, `${getFieldLabel(field)} must be at least ${minVal} characters`);
                return false;
            }
        }
    }
    
    // If all validations pass, clear error
    clearError(field);
    return true;
}

// Validate entire form
function validateForm(form) {
    const fields = form.querySelectorAll('input[data-validate], textarea[data-validate], select[data-validate]');
    let isValid = true;
    
    fields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// Helper function to get field label
function getFieldLabel(field) {
    const label = document.querySelector(`label[for="${field.getAttribute('id')}"]`);
    return label ? label.textContent : field.getAttribute('name') || 'Field';
}

// Show error message for a field
function showError(field, message) {
    // Remove existing error message
    clearError(field);
    
    // Add error class to field
    field.classList.add('input-error');
    
    // Create error message element
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-error text-sm mt-1';
    errorDiv.textContent = message;
    errorDiv.setAttribute('data-error', 'true');
    
    // Insert after the field
    field.parentNode.insertBefore(errorDiv, field.nextSibling);
}

// Clear error message for a field
function clearError(field) {
    field.classList.remove('input-error');
    
    // Remove existing error message if present
    const existingError = field.parentNode.querySelector('[data-error="true"]');
    if (existingError) {
        existingError.remove();
    }
}

// Validate email
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Initialize table functionality (for responsive tables on mobile)
function initTables() {
    const tables = document.querySelectorAll('table');
    tables.forEach(table => {
        // Wrap table in a div for horizontal scrolling on mobile
        if (!table.closest('.table-container')) {
            const container = document.createElement('div');
            container.className = 'table-container overflow-x-auto';
            table.parentNode.insertBefore(container, table);
            container.appendChild(table);
        }
    });
}

// Confirm delete action
function confirmDelete(message, callback) {
    if (confirm(message || 'Are you sure you want to delete this item?')) {
        callback();
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} fixed top-4 right-4 z-50 max-w-sm`;
    notification.innerHTML = `
        <i data-lucide="${type === 'error' ? 'x-circle' : type === 'success' ? 'check-circle' : 'info'}" class="w-5 h-5"></i>
        <span>${message}</span>
        <button class="btn btn-sm btn-circle btn-ghost ml-auto" onclick="this.closest('.alert').remove()">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Password strength indicator
function initPasswordStrength() {
    const passwordFields = document.querySelectorAll('input[type="password"][data-strength]');
    
    passwordFields.forEach(field => {
        field.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            updatePasswordStrengthIndicator(this, strength);
        });
    });
}

// Calculate password strength
function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength += 25;
    if (/[A-Z]/.test(password)) strength += 25;
    if (/[0-9]/.test(password)) strength += 25;
    if (/[^A-Za-z0-9]/.test(password)) strength += 25;
    
    if (strength < 50) return { level: 'weak', label: 'Weak', color: 'error' };
    if (strength < 75) return { level: 'medium', label: 'Medium', color: 'warning' };
    return { level: 'strong', label: 'Strong', color: 'success' };
}

// Update password strength indicator
function updatePasswordStrengthIndicator(field, strength) {
    // Remove existing indicator
    const existingIndicator = field.parentNode.querySelector('.password-strength');
    if (existingIndicator) existingIndicator.remove();
    
    // Create new indicator
    const indicator = document.createElement('div');
    indicator.className = `password-strength mt-2 h-1 w-full bg-base-300 rounded-full overflow-hidden`;
    
    const strengthBar = document.createElement('div');
    strengthBar.className = `h-full bg-${strength.color}`;
    strengthBar.style.width = `${strength.level === 'weak' ? '33%' : strength.level === 'medium' ? '66%' : '100%'}`;
    
    indicator.appendChild(strengthBar);
    
    const strengthLabel = document.createElement('div');
    strengthLabel.className = `text-xs text-${strength.color} mt-1`;
    strengthLabel.textContent = `Password strength: ${strength.label}`;
    
    field.parentNode.appendChild(indicator);
    field.parentNode.appendChild(strengthLabel);
}

// Initialize password strength on DOM load
document.addEventListener('DOMContentLoaded', initPasswordStrength);