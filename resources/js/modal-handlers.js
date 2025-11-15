import * as bootstrap from 'bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    console.log('modal-handlers.js: DOM Content Loaded. Attaching event listeners to edit buttons.');

    const statusEditModalElement = document.getElementById('statusEditModal');
    if (!statusEditModalElement) {
        console.error('modal-handlers.js: #statusEditModal element not found.');
        return;
    }

    const statusEditModal = new bootstrap.Modal(statusEditModalElement);
    const statusUpdateForm = document.getElementById('statusUpdateForm');
    const statusSelect = document.getElementById('statusSelect');

    // Add event listeners for modal lifecycle for debugging
    statusEditModalElement.addEventListener('show.bs.modal', function () {
        console.log('modal-handlers.js: Generic Modal is about to be shown.');
    });
    statusEditModalElement.addEventListener('shown.bs.modal', function () {
        console.log('modal-handlers.js: Generic Modal is now fully visible.');
    });
    statusEditModalElement.addEventListener('hide.bs.modal', function () {
        console.log('modal-handlers.js: Generic Modal is about to be hidden.');
    });
    statusEditModalElement.addEventListener('hidden.bs.modal', function () {
        console.log('modal-handlers.js: Generic Modal is now fully hidden.');
    });

    document.querySelectorAll('.open-status-modal-btn').forEach(button => {
        button.addEventListener('click', function() {
            const nominationId = this.dataset.nominationId;
            const currentStatus = this.dataset.currentStatus;
            console.log('modal-handlers.js: Edit button clicked for nomination ID:', nominationId, 'Current Status:', currentStatus);

            // Update the form action URL
            const updateUrl = `/career-guidance/nominations/${nominationId}/status`; // Adjust route as necessary
            statusUpdateForm.action = updateUrl;
            console.log('modal-handlers.js: Form action set to:', statusUpdateForm.action);

            // Set the selected option in the dropdown
            if (statusSelect) {
                statusSelect.value = currentStatus;
                console.log('modal-handlers.js: Status select value set to:', currentStatus);
            }

            // Show the modal
            statusEditModal.show();
        });
    });
});
