// Import styles
import './styles/app.scss';

// Import Bootstrap JS
import { Dropdown, Modal, Collapse, Toast, Alert } from 'bootstrap';

// Import jQuery
import $ from 'jquery';
global.$ = global.jQuery = $;

// Import custom scripts
import './js/sidebar';
import './js/charts';
// Ne pas importer le chat ici car il est chargé directement depuis public/js

// Initialize Bootstrap components
document.addEventListener('DOMContentLoaded', () => {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Initialize dropdowns (important for navbar)
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
    
    // Setup CSRF token for AJAX requests
    const token = document.querySelector('meta[name="csrf-token"]');
    
    if (token) {
        $.ajaxSetup({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token.content);
            }
        });
    }
});
