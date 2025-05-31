/**
 * Sidebar JS - Gestion de la sidebar responsive pour ZenTicket
 */

document.addEventListener('DOMContentLoaded', function() {
    // Éléments DOM
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Fonction pour basculer l'état de la sidebar sur mobile
    function toggleSidebar() {
        sidebar.classList.toggle('show');
    }
    
    // Ajouter l'écouteur d'événement pour le bouton de bascule
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    // Fermer la sidebar lors d'un clic à l'extérieur sur mobile
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar && sidebar.contains(event.target);
        const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
        
        if (!isClickInsideSidebar && !isClickOnToggle && sidebar && sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });
    
    // Gestion de la taille de l'écran
    function handleResize() {
        if (window.innerWidth > 991 && sidebar) {
            sidebar.classList.remove('show');
        }
    }
    
    // Écouter les changements de taille d'écran
    window.addEventListener('resize', handleResize);
    
    // Initialiser
    handleResize();
});
