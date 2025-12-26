// SweetAlert2 Helper Functions

// Confirm delete action
window.confirmDelete = function(message = 'Are you sure you want to delete this?') {
    return Swal.fire({
        title: 'Confirm Delete',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        return result.isConfirmed;
    });
};

// Confirm action
window.confirmAction = function(title, message, confirmText = 'Yes') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        return result.isConfirmed;
    });
};

// Success message
window.showSuccess = function(message, title = 'Success!') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'success',
        confirmButtonColor: '#3b82f6'
    });
};

// Error message
window.showError = function(message, title = 'Error!') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'error',
        confirmButtonColor: '#3b82f6'
    });
};

// Warning message
window.showWarning = function(message, title = 'Warning!') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'warning',
        confirmButtonColor: '#3b82f6'
    });
};

// Info message
window.showInfo = function(message, title = 'Info') {
    return Swal.fire({
        title: title,
        text: message,
        icon: 'info',
        confirmButtonColor: '#3b82f6'
    });
};
