/*
 * Application principale
 */
import 'bootstrap';
import '../styles/app.scss';

// Initialiser l'application
document.addEventListener('DOMContentLoaded', function() {
    // Setup CSRF token pour les requêtes AJAX
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token && typeof $ !== 'undefined') {
        $.ajaxSetup({
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token.content);
            }
        });
    }
    
    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialiser les popovers Bootstrap
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    const popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Initialiser les dropdowns Bootstrap (important pour le navbar)
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    const dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Fonctionnalité pour la sidebar responsive
    // Toggle pour la sidebar sur mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Fermer la sidebar en cliquant à l'extérieur
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && event.target !== sidebarToggle) {
                if (window.innerWidth < 992 && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
    
    // Toast notifications
    const toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl);
        
        // Auto-show pour les toasts avec data-autohide="true"
        if (toastEl.dataset.autohide === 'true') {
            toast.show();
        }
    });
    
    // Gestion des messages flash Symfony
    const flashMessages = document.querySelectorAll('.alert-dismissible');
    flashMessages.forEach(alert => {
        // Ajouter la classe d'animation
        alert.classList.add('animate__animated', 'animate__fadeIn');
        
        // Auto-hide après 5 secondes
        setTimeout(() => {
            alert.classList.replace('animate__fadeIn', 'animate__fadeOut');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Les tooltips et popovers sont déjà initialisés plus haut dans le code
    
    // Fonction pour afficher des notifications
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type} animate__animated animate__fadeInRight`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 
                              type === 'danger' ? 'exclamation-triangle' : 
                              type === 'warning' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            ${message}
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
            setTimeout(() => notification.remove(), 500);
        }, 5000);
    };
});
