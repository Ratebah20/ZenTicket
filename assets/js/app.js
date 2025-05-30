/*
 * Application principale
 */
import 'bootstrap';

// Importer le CSS depuis les fichiers JS n'est pas nécessaire car nous utilisons addStyleEntry dans webpack

// Fonctionnalité pour la sidebar responsive
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Initialisation des tooltips Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    
    // Initialisation des popovers Bootstrap
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    
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
