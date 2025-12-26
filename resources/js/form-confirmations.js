// Handle form confirmations with SweetAlert2

document.addEventListener('DOMContentLoaded', function() {
    // Handle all forms with data-confirm attribute
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const message = this.getAttribute('data-confirm');
            const type = this.getAttribute('data-confirm-type') || 'delete';

            let result;
            if (type === 'delete') {
                result = await confirmDelete(message);
            } else {
                result = await confirmAction('Confirm', message);
            }

            if (result) {
                this.submit();
            }
        });
    });

    // Handle delete buttons with data-confirm
    document.querySelectorAll('button[data-confirm], a[data-confirm]').forEach(element => {
        element.addEventListener('click', async function(e) {
            e.preventDefault();

            const message = this.getAttribute('data-confirm');
            const result = await confirmDelete(message);

            if (result) {
                // If it's inside a form, submit the form
                const form = this.closest('form');
                if (form) {
                    form.submit();
                } else {
                    // If it's a link, navigate to the href
                    const href = this.getAttribute('href');
                    if (href) {
                        window.location.href = href;
                    }
                }
            }
        });
    });
});
