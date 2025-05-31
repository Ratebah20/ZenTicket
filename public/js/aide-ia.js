/**
 * Fonctions d'aide pour le chat IA
 */

// S'assurer que jQuery est disponible globalement
(function() {
    // Vérifier si jQuery est déjà chargé
    if (typeof $ === 'undefined') {
        console.error('jQuery n\'est pas chargé, certaines fonctionnalités IA peuvent ne pas fonctionner correctement');
        return;
    }

    console.log('Aide IA initialisée, jQuery version: ' + $.fn.jquery);
    
    // Fonctions utilitaires pour le chat IA
    window.aideIA = {
        // Formatter les messages de l'IA
        formatMessage: function(content) {
            return content.replace(/\n/g, '<br>');
        },
        
        // Détecter les actions à effectuer automatiquement
        detectActions: function(message) {
            // Exemple: détection de création de ticket
            if (message.toLowerCase().includes('créer un ticket')) {
                console.log('Action détectée: création de ticket');
                return true;
            }
            return false;
        },
        
        // Transformer les liens en éléments cliquables
        linkify: function(text) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, function(url) {
                return '<a href="' + url + '" target="_blank">' + url + '</a>';
            });
        }
    };
})();
