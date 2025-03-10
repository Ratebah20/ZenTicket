class Chat {
    constructor(config) {
        console.log('Initialisation du chat avec la configuration:', config);
        
        this.config = {
            ...config,
            conversationId: config.id // Ajouter conversationId basé sur l'id existant
        };
        
        this.messageContainer = $('#messages');
        this.messageForm = $('.message-input');
        this.messageInput = $('#messageInput');
        this.sendButton = $('#sendButton');
        this.emojiButton = $('#emoji-button');
        this.emojiPicker = $('#emoji-picker');
        this.typingIndicator = $('#typing-indicator');
        
        this.messages = new Map();
        this.processedMessageIds = new Set();
        this.pendingAIMessages = [];
        this.pendingAIMessagesByUserMessage = new Map();
        this.pendingAIMessagesByTempUserMessage = new Map();
        this.pendingMessageTimeouts = new Map();
        this.hasMoreMessages = true;
        this.isLoading = false;
        this.processingQueue = false;
        this.lastUserMessageTimestamp = null;
        this.messageElements = new Map();

        // Nouvel attribut pour suivre l'ID du dernier message utilisateur
        this.lastUserMessageId = null;
        
        // Nouvel attribut pour indiquer si le traitement des messages IA est temporairement suspendu
        this.pauseAIMessageProcessing = false;

        this.currentPage = 1;
        this.isInitialized = false;
        
        this.init();
    }

    async init() {
        console.log('Initialisation du chat');
        
        // Vérifier que les éléments DOM existent
        if (!this.messageContainer.length) {
            console.error('Conteneur de messages non trouvé');
            return;
        }
        if (!this.messageForm.length) {
            console.error('Formulaire de message non trouvé');
            return;
        }
        if (!this.messageInput.length) {
            console.error('Champ de saisie non trouvé');
            return;
        }
        if (!this.sendButton.length) {
            console.error('Bouton d\'envoi non trouvé');
            return;
        }
        
        // Configurer les écouteurs d'événements
        this.setupEventListeners();
        
        // Initialiser le sélecteur d'emoji
        this.initEmojiPicker();
        
        // Ajouter un bouton de test Mercure dans l'interface
        this.addMercureTestButton();
        
        // Charger les messages initiaux
        this.loadInitialMessages();
        
        // Configurer la source d'événements pour les messages en temps réel
        this.setupEventSource();
        
        console.log('Chat initialisé avec succès');
        this.isInitialized = true;
    }

    setupEventListeners() {
        console.log('Configuration des écouteurs d\'événements');
        
        this.sendButton.on('click', () => {
            const content = this.messageInput.val().trim();
            this.sendMessage(content);
        });
        
        this.messageInput.on('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const content = this.messageInput.val().trim();
                this.sendMessage(content);
            }
        });
        
        this.messageInput.on('input', () => {
            this.sendTypingStatus();
        });
        
        //this.reconnectButton.on('click', () => {
            //console.log('Bouton de reconnexion cliqué');
            //this.setupEventSource();
        //});
        
        // Ajouter un écouteur pour le bouton de test Mercure
        $('#test-mercure-button').on('click', () => {
            console.log('Test de la connexion Mercure');
            this.testMercureConnection();
            
            // Afficher les informations de diagnostic
            const diagnosticInfo = {
                readyState: this.eventSource ? this.eventSource.readyState : 'null',
                readyStateText: this.eventSource ? 
                    (this.eventSource.readyState === EventSource.CONNECTING ? 'CONNECTING' :
                     this.eventSource.readyState === EventSource.OPEN ? 'OPEN' :
                     this.eventSource.readyState === EventSource.CLOSED ? 'CLOSED' : 'UNKNOWN') : 'null',
                connectionAttempts: this.connectionAttempts,
                tokenAvailable: !!this.config.subscriberToken,
                tokenLength: this.config.subscriberToken ? this.config.subscriberToken.length : 0,
                mercureUrl: this.config.mercureUrl,
                topic: `/chat/${this.config.id}`,
                eventSourcePolyfillAvailable: typeof EventSourcePolyfill !== 'undefined'
            };
            
            console.log('Informations de diagnostic:', diagnosticInfo);
            
            // Si la connexion est fermée, tenter une reconnexion
            if (!this.eventSource || this.eventSource.readyState === EventSource.CLOSED) {
                console.log('Tentative de reconnexion...');
                this.setupEventSource();
            }
        });
        
        // Initialiser le sélecteur d'emoji
        this.initEmojiPicker();
    }

    // Nouvelle méthode pour ajouter un bouton de test Mercure
    addMercureTestButton() {
        const chatHeader = $('.chat-header');
        if (chatHeader.length) {
            const statusButton = $('<button>')
                .addClass('btn btn-sm btn-outline-info ms-2')
                .html('<i class="fas fa-wifi"></i> Test Mercure')
                .on('click', () => {
                    this.testMercureConnection();
                });
            chatHeader.append(statusButton);
            
            console.log('Bouton de test Mercure ajouté');
        } else {
            console.warn('Élément .chat-header non trouvé, impossible d\'ajouter le bouton de test Mercure');
        }
    }

    // Nouvelle méthode pour tester la connexion Mercure
    testMercureConnection() {
        console.log('Test de la connexion Mercure');
        
        // Afficher les informations de connexion
        const status = {
            status: this.eventSource ? 
                    (this.eventSource.readyState === EventSource.CONNECTING ? 'CONNECTING' :
                     this.eventSource.readyState === EventSource.OPEN ? 'OPEN' :
                     this.eventSource.readyState === EventSource.CLOSED ? 'CLOSED' : 'UNKNOWN') : 'null',
            reconnectAttempts: this.reconnectAttempts || 0,
            mercureUrl: this.config.mercureUrl,
            topic: `/chat/${this.config.id}`,
            tokenPresent: !!this.config.subscriberToken,
            eventSourcePolyfillAvailable: typeof EventSourcePolyfill !== 'undefined'
        };
        
        console.log('État de la connexion Mercure:', status);
        
        // Tenter de reconnecter si nécessaire
        if (!this.eventSource || this.eventSource.readyState === EventSource.CLOSED) {
            console.log('Pas de connexion EventSource, reconnexion...');
            this.setupEventSource();
        }
        
        // Afficher un message à l'utilisateur
        alert(`État de la connexion Mercure:
- Statut: ${status.status}
- Tentatives de reconnexion: ${status.reconnectAttempts}
- URL Mercure: ${status.mercureUrl}
- Topic: ${status.topic}
- Token présent: ${status.tokenPresent ? 'Oui' : 'Non'}
- EventSourcePolyfill disponible: ${status.eventSourcePolyfillAvailable ? 'Oui' : 'Non'}`);
    }

    sendMessage(content, type = 'text') {
        console.log('=== Envoi d\'un message ===');
        console.log('Content:', content);
        console.log('Type:', type);
        
        // Vérifier si le contenu est vide
        if (!content || content.trim() === '') {
            console.log('Contenu vide, annulation de l\'envoi');
            return;
        }
        
        // Désactiver le bouton d'envoi pendant l'envoi
        this.disableSendButton();
        
        // Créer un ID temporaire pour le message
        const tempId = `temp-${Date.now()}`;
        console.log(`ID temporaire créé: ${tempId}`);
        
        // Créer un message temporaire pour l'affichage immédiat
        const tempMessage = {
            id: tempId,
            content: content,
            senderId: this.config.userId,
            senderName: this.config.userName,
            senderAvatar: this.config.userAvatar,
            timestamp: new Date().toISOString(),
            messageType: 'user',
            isRead: false,
            reactions: []
        };
        
        // Ajouter le message temporaire à l'interface
        console.log('Ajout du message temporaire à l\'interface');
        const messageElement = this.addMessage(tempMessage);
        
        // Mettre à jour la variable pauseAIMessageProcessing pour indiquer qu'un message est en cours d'envoi
        this.pauseAIMessageProcessing = true;
        
        // Enregistrer l'heure d'envoi du message
        this.lastUserMessageTimestamp = new Date();
        
        // Envoyer le message au serveur
        console.log('Envoi du message au serveur');
        $.ajax({
            url: this.config.routes.send,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.config.csrfToken
            },
            data: JSON.stringify({
                content: content,
                type: type
            }),
            contentType: 'application/json',
            success: (response) => {
                console.log('Réponse du serveur:', response);
                
                if (response.success) {
                    console.log('Message envoyé avec succès');
                    
                    // Mettre à jour l'ID du message temporaire avec l'ID réel
                    const realId = response.messageId;
                    console.log(`ID réel du message: ${realId}`);
                    
                    // Mettre à jour l'élément du message avec l'ID réel
                    $(messageElement).attr('data-message-id', realId);
                    
                    // Mettre à jour les Maps
                    this.messageElements.set(realId, messageElement);
                    this.messageElements.delete(tempId);
                    
                    // Mettre à jour le message dans la Map des messages
                    tempMessage.id = realId;
                    this.messages.set(realId, tempMessage);
                    this.messages.delete(tempId);
                    
                    // Mettre à jour l'ID du dernier message utilisateur
                    this.lastUserMessageId = realId;
                    console.log(`ID du dernier message utilisateur mis à jour: ${this.lastUserMessageId}`);
                    
                    // NOUVEAU: Vérifier s'il y a des messages IA en attente pour le message temporaire
                    const pendingAIMessages = this.pendingAIMessagesByTempUserMessage.get(tempId);
                    if (pendingAIMessages && pendingAIMessages.length > 0) {
                        console.log(`Transfert de ${pendingAIMessages.length} messages IA en attente du message temporaire ${tempId} vers le message réel ${realId}`);
                        
                        // Copier les messages en attente pour éviter les problèmes de modification pendant l'itération
                        const messagesToProcess = [...pendingAIMessages];
                        
                        // Vider la liste des messages en attente pour le message temporaire
                        this.pendingAIMessagesByTempUserMessage.delete(tempId);
                        
                        // Attendre un court instant pour s'assurer que le message utilisateur est bien affiché
                        setTimeout(() => {
                            messagesToProcess.forEach(msg => {
                                console.log('Transfert du message IA:', msg);
                                // Mettre à jour le userMessageId avec l'ID réel
                                msg.userMessageId = realId;
                                // Ajouter le message IA à l'interface
                                this.addMessage(msg, true);
                            });
                        }, 100);
                    }
                    
                    // Autoriser à nouveau le traitement des messages IA
                    this.pauseAIMessageProcessing = false;
                    
                    // NOUVEAU: Traiter les messages IA en attente pour le message réel
                    this.processPendingAIMessages(realId);
                } else {
                    console.error('Erreur lors de l\'envoi du message:', response.error);
                    
                    // Afficher un message d'erreur
                    this.showErrorMessage('Erreur lors de l\'envoi du message: ' + response.error);
                    
                    // Supprimer le message temporaire
                    $(messageElement).remove();
                    this.messageElements.delete(tempId);
                    this.messages.delete(tempId);
                    
                    // Autoriser à nouveau le traitement des messages IA
                    this.pauseAIMessageProcessing = false;
                }
                
                // Réactiver le bouton d'envoi
                this.enableSendButton();
            },
            error: (xhr, status, error) => {
                console.error('Erreur AJAX lors de l\'envoi du message:', error);
                
                // Afficher un message d'erreur
                this.showErrorMessage('Erreur lors de l\'envoi du message: ' + error);
                
                // Supprimer le message temporaire
                $(messageElement).remove();
                this.messageElements.delete(tempId);
                this.messages.delete(tempId);
                
                // Autoriser à nouveau le traitement des messages IA
                this.pauseAIMessageProcessing = false;
                
                // Réactiver le bouton d'envoi
                this.enableSendButton();
            }
        });
        
        // Vider le champ de saisie
        this.messageInput.val('');
        
        // Réinitialiser la hauteur du champ de saisie
        this.resetInputHeight();
        
        // Défiler vers le bas
        this.scrollToBottom();
    }

    setupEventSource() {
        console.log('Configuration de la source d\'événements Mercure');
        if (!this.config.mercureUrl) {
            console.error('URL Mercure non configurée');
            return;
        }
    
        try {
            // Construire l'URL avec les topics
            const url = new URL(this.config.mercureUrl);
    
            // Exemple : un topic /chat/78 (selon l'ID de votre conversation)
            url.searchParams.append('topic', `/chat/${this.config.id}`);
    
            // Vérifier si EventSourcePolyfill est disponible
            if (typeof EventSourcePolyfill !== 'undefined') {
                console.log('Utilisation de EventSourcePolyfill avec en-tête Authorization');
                
                // Créer la source d'événements avec l'en-tête Authorization
                this.eventSource = new EventSourcePolyfill(url.toString(), {
                    headers: {
                        'Authorization': `Bearer ${this.config.subscriberToken}`
                    }
                });
            } else {
                console.log('Fallback sur EventSource standard avec withCredentials');
                
                // Ajouter le token dans l'URL (si votre hub accepte ce mode)
                if (this.config.subscriberToken) {
                    url.searchParams.append('token', this.config.subscriberToken);
                }
                
                // Créer la source d'événements en autorisant les credentials (CORS)
                this.eventSource = new EventSource(url.toString(), { withCredentials: true });
            }
    
            this.eventSource.onopen = (event) => {
                console.log('Connexion Mercure établie !', event);
                this.connectionStatus = 'OPEN';
                
                // Afficher un message de succès dans l'interface
                this.showSuccessMessage('Connexion en temps réel établie', 3000);
            };
    
            this.eventSource.onmessage = (event) => {
                console.log('Message Mercure reçu:', event);
                this.handleMercureMessage(event);
            };
    
            this.eventSource.onerror = (event) => {
                console.error('Erreur de connexion Mercure:', event);
                this.connectionStatus = 'ERROR';
                
                // Fermer la connexion actuelle
                this.eventSource.close();
                
                // Stratégie de reconnexion avec backoff exponentiel
                const reconnectDelay = Math.min(30000, Math.pow(2, this.reconnectAttempts) * 1000);
                console.log(`Tentative de reconnexion dans ${reconnectDelay}ms (tentative ${this.reconnectAttempts + 1})`);
                
                setTimeout(() => {
                    this.reconnectAttempts++;
                    this.setupEventSource();
                }, reconnectDelay);
                
                this.showErrorMessage(
                  'Erreur de connexion au serveur de messages en temps réel. ' +
                  'Tentative de reconnexion automatique...'
                );
            };
    
            console.log('Source d\'événements Mercure configurée avec succès');
        } catch (error) {
            console.error('Erreur lors de la configuration de la source d\'événements Mercure:', error);
            this.showErrorMessage(
              'Erreur de connexion au serveur de messages en temps réel: ' + error.message
            );
        }
    }

    handleMercureMessage(event) {
        try {
            console.log('=== Réception d\'un message Mercure ===');
            console.log('Event data brut:', event.data);
            
            const data = JSON.parse(event.data);
            console.log('Données Mercure parsées:', data);
            
            // Vérifier si le message est destiné à cette conversation
            if (data.conversationId && data.conversationId != this.config.id) {
                console.log(`Message pour une autre conversation (${data.conversationId} vs ${this.config.id}), ignoré`);
                return;
            }
            
            console.log('Message pour cette conversation, traitement en cours...');
            
            // Traiter différents types de messages
            switch (data.type) {
                case 'read_status':
                    console.log('Mise à jour du statut de lecture reçue');
                    this.updateMessageReadStatus(data.messageId, true);
                    break;
                    
                case 'reaction':
                    console.log('Réaction reçue');
                    this.updateMessageReactions(data.messageId, data.reactions);
                    break;
                    
                case 'delete':
                    console.log('Suppression de message reçue');
                    this.deleteMessage(data.messageId);
                    break;
                    
                case 'typing':
                    console.log('Événement de frappe reçu');
                    this.handleTypingEvent(data);
                    break;
                    
                case 'message':
                    console.log('Message reçu via Mercure');
                    
                    // Récupérer les données du message (nouveau format ou ancien)
                    const messageData = data.message || data;
                    
                    // Logs détaillés pour le débogage
                    console.log('Message data:', messageData);
                    console.log('Type de message:', messageData.messageType);
                    console.log('ID du message:', messageData.id);
                    console.log('ID du message utilisateur associé:', messageData.userMessageId);
                    
                    // Vérifier si le message existe déjà
                    if (messageData.id && this.messages.has(messageData.id)) {
                        console.log(`Message ${messageData.id} déjà dans la liste, ignoré`);
                        return;
                    }
                    
                    // Si c'est un message IA, vérifier s'il correspond à un message temporaire
                    if (messageData.messageType === 'ai' && messageData.userMessageId) {
                        // Vérifier si un message temporaire existe pour ce message utilisateur
                        const tempUserMessageId = `temp-${messageData.userMessageId}`;
                        if (this.messageElements.has(tempUserMessageId)) {
                            console.log(`Message temporaire trouvé pour le message utilisateur ${messageData.userMessageId}`);
                            
                            // Mettre à jour le statut de traitement du message temporaire
                            this.updateMessageProcessingStatus(tempUserMessageId, false);
                        }
                    }
                    
                    // Forcer l'affichage du message dans l'interface
                    this.displayMercureMessage(messageData);
                    
                    // Si c'est un message IA, mettre à jour le statut de traitement
                    if (messageData.messageType === 'ai' && messageData.userMessageId) {
                        console.log(`Message IA reçu pour le message utilisateur ${messageData.userMessageId}`);
                        this.updateMessageProcessingStatus(messageData.userMessageId, false);
                    }
                    break;
                    
                default:
                    console.log(`Type de message inconnu: ${data.type}`);
            }
        } catch (error) {
            console.error('Erreur lors du traitement du message Mercure:', error);
        }
    }
    
    // Nouvelle méthode pour afficher les messages reçus via Mercure
    displayMercureMessage(messageData) {
        console.log('Affichage du message Mercure:', messageData);
        
        // Ajouter le message à la Map des messages
        if (messageData.id) {
            this.messages.set(messageData.id, messageData);
        }
        
        // Créer l'élément de message
        const messageElement = this.createMessageElement(messageData);
        
        // Insérer le message dans le DOM
        if (messageData.messageType === 'ai' && messageData.userMessageId) {
            // Pour les messages IA, essayer d'abord de trouver le message utilisateur correspondant
            let userMessageElement = this.messageElements.get(messageData.userMessageId);
            
            // Si le message utilisateur n'est pas trouvé, essayer avec l'ID temporaire
            const tempUserMessageId = `temp-${messageData.userMessageId}`;
            if (!userMessageElement && this.messageElements.has(tempUserMessageId)) {
                userMessageElement = this.messageElements.get(tempUserMessageId);
                console.log(`Message utilisateur temporaire trouvé (${tempUserMessageId}), utilisation pour l'insertion du message IA`);
            }
            
            if (userMessageElement && userMessageElement.length) {
                console.log(`Insertion du message IA après le message utilisateur ${userMessageElement.attr('data-message-id')}`);
                userMessageElement.after(messageElement);
            } else {
                // Si le message utilisateur n'est pas trouvé, insérer par timestamp
                console.log('Message utilisateur non trouvé, insertion par timestamp');
                this.insertMessageByTimestamp(messageElement, messageData);
            }
        } else {
            // Pour les autres types de messages, insérer par timestamp
            this.insertMessageByTimestamp(messageElement, messageData);
        }
        
        // Ajouter à la Map des éléments de message
        if (messageData.id) {
            this.messageElements.set(messageData.id, messageElement);
            
            // Si c'est un message utilisateur, mettre à jour l'ID du dernier message utilisateur
            if (messageData.messageType === 'user') {
                this.lastUserMessageId = messageData.id;
                console.log(`ID du dernier message utilisateur mis à jour: ${this.lastUserMessageId}`);
                
                // Si ce message remplace un message temporaire, supprimer l'entrée temporaire
                const tempId = `temp-${messageData.id}`;
                if (this.messageElements.has(tempId)) {
                    console.log(`Suppression de l'entrée temporaire ${tempId} remplacée par le message réel ${messageData.id}`);
                    this.messageElements.delete(tempId);
                }
            }
        }
        
        // Défiler vers le bas si nécessaire
        if (this.isScrolledToBottom()) {
            this.scrollToBottom();
        }
        
        // Jouer un son de notification pour les nouveaux messages
        this.playNotificationSound();
    }
    
    // Méthode pour jouer un son de notification
    playNotificationSound() {
        try {
            // Vérifier si la notification sonore est activée
            if (this.config.enableSoundNotifications !== false) {
                // Vérifier si le fichier existe avant de tenter de le jouer
                const audioPath = '/sounds/notification.mp3';
                
                // Utiliser fetch pour vérifier si le fichier existe
                fetch(audioPath, { method: 'HEAD' })
                    .then(response => {
                        if (response.ok) {
                            const audio = new Audio(audioPath);
                            audio.volume = 0.5;
                            audio.play().catch(error => {
                                console.warn('Impossible de jouer le son de notification:', error);
                            });
                        } else {
                            console.warn(`Le fichier audio ${audioPath} n'existe pas`);
                        }
                    })
                    .catch(error => {
                        console.warn('Erreur lors de la vérification du fichier audio:', error);
                    });
            }
        } catch (error) {
            console.warn('Erreur lors de la lecture du son de notification:', error);
        }
    }
    
    // Fonction utilitaire pour obtenir le timestamp d'un message
    getMessageTimestamp(messageId) {
        const message = this.messages.get(messageId);
        if (message && message.timestamp) {
            return new Date(message.timestamp);
        }
        
        // Essayer de trouver le message dans le DOM
        const element = this.messageElements.get(messageId);
        if (element) {
            const timestamp = element.attr('data-timestamp');
            if (timestamp) {
                return new Date(timestamp);
            }
        }
        
        return null;
    }

    addMessageToUI(data) {
        console.log('Ajout du message à l\'interface UI:', data);
        
        // Mettre à jour le dernier ID de message
        if (data.id && (!this.lastMessageId || parseInt(data.id) > parseInt(this.lastMessageId))) {
            this.lastMessageId = data.id;
            console.log(`Mise à jour du dernier ID de message: ${this.lastMessageId}`);
        }
        
        // Ajouter le message à l'interface
        this.addMessage(data, true);
        
        // Marquer la conversation comme non lue si nécessaire
        if (data.messageType === 'ai' && !this.isActive) {
            console.log('Marquage de la conversation comme non lue (message IA reçu pendant inactivité)');
            this.markConversationAsUnread();
        }
        
        // Défiler vers le bas
        console.log('Défilement vers le bas après ajout du message');
        this.scrollToBottom();
    }

    handleTypingEvent(data) {
        const userId = data.userId;
        const isTyping = data.isTyping;
        
        // Ignorer nos propres événements de frappe
        if (userId === this.config.userId) {
            return;
        }
        
        if (isTyping) {
            this.typingUsers.add(userId);
        } else {
            this.typingUsers.delete(userId);
        }
        
        this.updateTypingIndicator();
    }
    
    updateTypingIndicator() {
        if (this.typingUsers.size > 0) {
            const text = this.typingUsers.size === 1 
                ? 'Quelqu\'un est en train d\'écrire...' 
                : 'Plusieurs personnes sont en train d\'écrire...';
            
            this.typingIndicator.show();
            this.typingText.text(text);
        } else {
            this.typingIndicator.hide();
        }
    }

    async loadMessages() {
        console.log('Loading messages...');
        try {
            const response = await fetch(this.config.routes.messages);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (Array.isArray(data.messages)) {
                this.messageContainer.empty();
                data.messages.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp));
                data.messages.forEach(message => {
                    this.addMessage(message, false);
                });
                this.scrollToBottom();
                console.log('Messages loaded successfully');
            } else {
                console.error('Invalid messages data:', data);
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            throw error;
        }
    }

    loadInitialMessages() {
        console.log('Chargement des messages initiaux');
        
        if (this.isLoading) {
            console.log('Chargement déjà en cours, annulation');
            return;
        }
        
        this.isLoading = true;
        
        // Afficher un indicateur de chargement
        const loadingIndicator = $('<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div></div>');
        this.messageContainer.prepend(loadingIndicator);
        
        // Charger les messages depuis le serveur
        $.ajax({
            url: this.config.routes.messages,
            method: 'GET',
            data: {
                page: this.currentPage
            },
            headers: {
                'X-CSRF-TOKEN': this.config.csrfToken
            },
            success: (response) => {
                console.log('Messages initiaux chargés:', response);
                
                // Supprimer l'indicateur de chargement
                loadingIndicator.remove();
                
                if (response.success) {
                    // Ajouter les messages à l'interface
                    const messages = response.messages;
                    
                    if (messages.length === 0) {
                        console.log('Aucun message à charger');
                        this.hasMoreMessages = false;
                    } else {
                        console.log(`${messages.length} messages chargés`);
                        
                        // Trier les messages par date de création
                        messages.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
                        
                        // Ajouter les messages à l'interface
                        messages.forEach(message => {
                            this.addMessage(message);
                        });
                        
                        // Mettre à jour l'ID du dernier message utilisateur
                        const userMessages = messages.filter(msg => msg.messageType === 'user');
                        if (userMessages.length > 0) {
                            this.lastUserMessageId = userMessages[userMessages.length - 1].id;
                            console.log(`ID du dernier message utilisateur mis à jour: ${this.lastUserMessageId}`);
                        }
                        
                        // Défiler vers le bas
                        this.scrollToBottom();
                    }
                } else {
                    console.error('Erreur lors du chargement des messages:', response.error || 'Erreur inconnue');
                    this.showErrorMessage('Erreur lors du chargement des messages: ' + (response.error || 'Erreur inconnue'));
                }
                
                this.isLoading = false;
            },
            error: (xhr, status, error) => {
                console.error('Erreur AJAX lors du chargement des messages:', error);
                console.error('Statut de la réponse:', xhr.status);
                console.error('Réponse texte:', xhr.responseText);
                
                // Supprimer l'indicateur de chargement
                loadingIndicator.remove();
                
                // Afficher un message d'erreur
                this.showErrorMessage('Erreur lors du chargement des messages: ' + error);
                
                this.isLoading = false;
            }
        });
    }

    loadMoreMessages() {
        console.log('Chargement de messages supplémentaires');
        
        if (this.isLoading || !this.hasMoreMessages) {
            console.log('Chargement déjà en cours ou plus de messages à charger, annulation');
            return;
        }
        
        this.isLoading = true;
        
        // Afficher un indicateur de chargement
        const loadingIndicator = $('<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div></div>');
        this.messageContainer.prepend(loadingIndicator);
        
        // Incrémenter le numéro de page
        this.currentPage++;
        
        // Charger les messages depuis le serveur
        $.ajax({
            url: this.config.routes.messages,
            method: 'GET',
            data: {
                page: this.currentPage
            },
            headers: {
                'X-CSRF-TOKEN': this.config.csrfToken
            },
            success: (response) => {
                console.log('Messages supplémentaires chargés:', response);
                
                // Supprimer l'indicateur de chargement
                loadingIndicator.remove();
                
                if (response.success) {
                    // Ajouter les messages à l'interface
                    const messages = response.messages;
                    
                    if (messages.length === 0) {
                        console.log('Aucun message supplémentaire à charger');
                        this.hasMoreMessages = false;
                    } else {
                        console.log(`${messages.length} messages supplémentaires chargés`);
                        
                        // Trier les messages par date de création
                        messages.sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));
                        
                        // Sauvegarder la position de défilement actuelle
                        const firstMessage = this.messageContainer.children('.message').first();
                        const firstMessageOffset = firstMessage.length ? firstMessage.offset().top : 0;
                        
                        // Ajouter les messages à l'interface
                        messages.forEach(message => {
                            // Créer l'élément de message
                            const messageElement = this.createMessageElement(message);
                            
                            // Ajouter l'élément au début du conteneur
                            this.messageContainer.prepend(messageElement);
                            
                            // Ajouter à la Map des éléments de message
                            if (message.id) {
                                this.messageElements.set(message.id, messageElement);
                            }
                            
                            // Ajouter le message à la Map des messages
                            if (message.id) {
                                this.messages.set(message.id, message);
                            }
                        });
                        
                        // Restaurer la position de défilement
                        if (firstMessage.length) {
                            const newOffset = firstMessage.offset().top;
                            const scrollDiff = newOffset - firstMessageOffset;
                            this.messageContainer.scrollTop(this.messageContainer.scrollTop() + scrollDiff);
                        }
                    }
                } else {
                    console.error('Erreur lors du chargement des messages supplémentaires:', response.error);
                    this.showErrorMessage('Erreur lors du chargement des messages: ' + response.error);
                }
                
                this.isLoading = false;
            },
            error: (xhr, status, error) => {
                console.error('Erreur AJAX lors du chargement des messages supplémentaires:', error);
                
                // Supprimer l'indicateur de chargement
                loadingIndicator.remove();
                
                // Afficher un message d'erreur
                this.showErrorMessage('Erreur lors du chargement des messages: ' + error);
                
                this.isLoading = false;
            }
        });
    }

    addMessage(messageData, fromMercure = false) {
        console.log('=== Ajout d\'un message ===');
        console.log('Message data:', messageData);
        console.log('From Mercure:', fromMercure);

        // Ajouter le message à la Map des messages
        if (messageData.id) {
            this.messages.set(messageData.id, messageData);
        }

        // Vérifier si l'élément existe déjà (pour éviter les doublons)
        if (messageData.id && this.messageElements.has(messageData.id)) {
            console.log(`Message ${messageData.id} déjà affiché, mise à jour si nécessaire`);
            
            // Si le message existe déjà mais qu'il vient de Mercure, ne pas le mettre à jour
            // car la version HTTP est plus complète
            if (fromMercure) {
                console.log(`Message ${messageData.id} reçu via Mercure mais déjà affiché, ignoré`);
                return this.messageElements.get(messageData.id);
            }
            
            console.log('Message existant trouvé, mise à jour...');
            
            // Mettre à jour le contenu du message existant
            const existingElement = this.messageElements.get(messageData.id);
            const contentElement = existingElement.find('.message-content');
            if (contentElement.length > 0) {
                contentElement.html(this.formatMessageContent(messageData.content));
            }
            
            // Mettre à jour les réactions si présentes
            if (messageData.reactions && messageData.reactions.length > 0) {
                this.updateMessageReactions(messageData.id, messageData.reactions);
            }
            
            // Mettre à jour le statut de lecture si présent
            if (messageData.isRead !== undefined) {
                this.updateMessageReadStatus(messageData.id, messageData.isRead);
            }
            
            console.log('Message mis à jour avec succès');
            return existingElement;
        }

        // Vérifier si c'est un message réel qui remplace un message temporaire
        if (!fromMercure && messageData.id && !String(messageData.id).startsWith('temp-')) {
            const tempId = `temp-${messageData.id}`;
            if (this.messageElements.has(tempId)) {
                console.log(`Message temporaire ${tempId} trouvé, remplacement par le message réel ${messageData.id}`);
                
                // Mettre à jour l'élément temporaire avec les données du message réel
                const tempElement = this.messageElements.get(tempId);
                tempElement.attr('data-message-id', messageData.id);
                
                // Mettre à jour le contenu si nécessaire
                const contentElement = tempElement.find('.message-content');
                if (contentElement.length > 0 && messageData.content) {
                    contentElement.html(this.formatMessageContent(messageData.content));
                }
                
                // Ajouter l'élément à la Map avec le nouvel ID
                this.messageElements.set(messageData.id, tempElement);
                
                // Supprimer l'entrée temporaire
                this.messageElements.delete(tempId);
                
                console.log(`Message temporaire ${tempId} remplacé avec succès par le message réel ${messageData.id}`);
                
                // Si c'est un message utilisateur, vérifier s'il y a des messages IA en attente
                if (messageData.messageType === 'user') {
                    this.lastUserMessageId = messageData.id;
                    console.log(`ID du dernier message utilisateur mis à jour: ${this.lastUserMessageId}`);
                    
                    // Vérifier s'il y a des messages IA en attente pour ce message utilisateur
                    this.processPendingAIMessages(messageData.id, tempId);
                }
                
                return tempElement;
            }
        }

        console.log('Création d\'un nouveau message...');
        
        // Créer l'élément de message
        const messageElement = this.createMessageElement(messageData);
        
        // Insérer le message dans le DOM
        if (messageData.messageType === 'ai' && messageData.userMessageId) {
            // Pour les messages IA, insérer après le message utilisateur correspondant
            let userMessageElement = this.messageElements.get(messageData.userMessageId);
            
            // Si le message utilisateur n'est pas trouvé, essayer avec l'ID temporaire
            const tempUserMessageId = `temp-${messageData.userMessageId}`;
            if (!userMessageElement && this.messageElements.has(tempUserMessageId)) {
                userMessageElement = this.messageElements.get(tempUserMessageId);
                console.log(`Message utilisateur temporaire trouvé (${tempUserMessageId}), utilisation pour l'insertion du message IA`);
            }
            
            if (userMessageElement && userMessageElement.length) {
                console.log(`Insertion du message IA après le message utilisateur ${userMessageElement.attr('data-message-id')}`);
                userMessageElement.after(messageElement);
            } else {
                // Si le message utilisateur n'est pas trouvé, mettre le message IA en attente
                console.log(`Message utilisateur ${messageData.userMessageId} non trouvé, mise en attente du message IA`);
                const pendingMessages = this.pendingAIMessagesByUserMessage.get(messageData.userMessageId) || [];
                pendingMessages.push(messageData);
                this.pendingAIMessagesByUserMessage.set(messageData.userMessageId, pendingMessages);
                
                // Ne pas ajouter le message IA à l'interface pour l'instant
                return null;
            }
        } else {
            // Pour les autres types de messages, utiliser la logique standard
            this.insertMessageByTimestamp(messageElement, messageData);
        }
        
        // Ajouter à la Map des éléments de message
        if (messageData.id) {
            this.messageElements.set(messageData.id, messageElement);
            
            // Si c'est un message utilisateur, vérifier s'il y a des messages IA en attente
            if (messageData.messageType === 'user') {
                // Enregistrer l'ID du dernier message utilisateur
                this.lastUserMessageId = messageData.id;
                console.log(`ID du dernier message utilisateur mis à jour: ${this.lastUserMessageId}`);
                
                // Vérifier s'il y a des messages IA en attente pour ce message utilisateur
                this.processPendingAIMessages(messageData.id);
            }
        }
        
        // Si c'est un message de l'utilisateur actuel, défiler vers le bas
        if (messageData.senderId === this.config.userId) {
            console.log('Message envoyé par l\'utilisateur actuel');
            this.scrollToBottom();
        } else {
            console.log('Message reçu d\'un autre utilisateur');
            // Défiler vers le bas uniquement si l'utilisateur était déjà en bas
            if (this.isScrolledToBottom()) {
                this.scrollToBottom();
            }
        }
        
        return messageElement;
    }
    
    // Nouvelle méthode pour traiter les messages IA en attente
    processPendingAIMessages(userMessageId, tempUserMessageId = null) {
        console.log(`Traitement des messages IA en attente pour le message utilisateur ${userMessageId}`);
        
        // Vérifier s'il y a des messages IA en attente pour ce message utilisateur
        const pendingAIMessages = this.pendingAIMessagesByUserMessage.get(userMessageId) || [];
        
        // Vérifier également s'il y a des messages en attente pour l'ID temporaire
        if (tempUserMessageId) {
            const tempPendingMessages = this.pendingAIMessagesByTempUserMessage.get(tempUserMessageId) || [];
            if (tempPendingMessages.length > 0) {
                console.log(`${tempPendingMessages.length} messages IA en attente trouvés pour l'ID temporaire ${tempUserMessageId}`);
                pendingAIMessages.push(...tempPendingMessages);
                this.pendingAIMessagesByTempUserMessage.set(tempUserMessageId, []);
            }
        }
        
        if (pendingAIMessages.length === 0) {
            console.log(`Aucun message IA en attente pour le message utilisateur ${userMessageId}`);
            return;
        }
        
        console.log(`Traitement de ${pendingAIMessages.length} messages IA en attente`);
        
        // Copier les messages en attente pour éviter les problèmes de modification pendant l'itération
        const messagesToProcess = [...pendingAIMessages];
        
        // Vider la liste des messages en attente
        this.pendingAIMessagesByUserMessage.set(userMessageId, []);
        
        // Attendre un court instant pour s'assurer que le message utilisateur est bien affiché
        setTimeout(() => {
            messagesToProcess.forEach(msg => {
                console.log('Traitement du message IA en attente:', msg);
                // Utiliser addMessage directement pour bénéficier de la logique d'insertion après le message utilisateur
                this.addMessage(msg, true);
            });
        }, 100);
    }
    
    // NOUVELLE MÉTHODE: Extraire la logique d'insertion par timestamp dans une méthode séparée
    insertMessageByTimestamp(messageElement, messageData) {
        console.log('Insertion du message par timestamp');
        
        // Obtenir le timestamp du message
        const messageTimestamp = new Date(messageData.timestamp || messageData.createdAt);
        
        // Parcourir tous les messages existants pour trouver la bonne position
        let inserted = false;
        const messages = this.messageContainer.children('.message');
        
        if (messages.length === 0) {
            // Si le conteneur est vide, ajouter simplement le message
            console.log('Conteneur vide, ajout du message');
            this.messageContainer.append(messageElement);
            return;
        }
        
        // Parcourir les messages du plus récent au plus ancien
        for (let i = messages.length - 1; i >= 0; i--) {
            const existingMessage = messages.eq(i);
            const existingTimestamp = new Date(existingMessage.attr('data-timestamp'));
            
            // Si le message existant est plus ancien que le nouveau message, insérer après
            if (messageTimestamp > existingTimestamp) {
                console.log(`Message inséré après le message ${existingMessage.attr('data-message-id')}`);
                existingMessage.after(messageElement);
                inserted = true;
                break;
            }
        }
        
        // Si aucune position n'a été trouvée, ajouter au début
        if (!inserted) {
            console.log('Message inséré au début');
            this.messageContainer.prepend(messageElement);
        }
    }
    
    // NOUVELLE MÉTHODE: Vérifier si l'utilisateur est défilé jusqu'en bas
    isScrolledToBottom() {
        const container = this.messageContainer[0];
        return container.scrollHeight - container.clientHeight <= container.scrollTop + 50; // 50px de marge
    }

    createMessageElement(messageData) {
        console.log('Creating message element:', messageData);
        
        // Déterminer si le message est de l'utilisateur actuel
        const isCurrentUser = messageData.senderId === this.config.userId;
        
        // Créer l'élément de message
        const messageElement = $('<div class="message"></div>');
        
        // Ajouter les attributs de données
        messageElement.attr('data-message-id', messageData.id);
        messageElement.attr('data-sender-id', messageData.senderId);
        messageElement.attr('data-timestamp', messageData.timestamp || messageData.createdAt);
        
        // Ajouter les classes en fonction du type de message
        if (messageData.messageType === 'user') {
            messageElement.addClass('user-message');
        } else if (messageData.messageType === 'ai') {
            messageElement.addClass('ai-message');
        } else if (messageData.messageType === 'system') {
            messageElement.addClass('system-message');
        }
        
        // Ajouter la classe pour l'utilisateur actuel
        if (isCurrentUser) {
            messageElement.addClass('current-user');
        }
        
        // Créer l'en-tête du message
        const messageHeader = $('<div class="message-header"></div>');
        
        // Ajouter l'avatar
        if (messageData.senderAvatar) {
            const avatarElement = $('<div class="message-avatar"></div>');
            const avatarImage = $('<img>');
            avatarImage.attr('src', messageData.senderAvatar);
            avatarImage.attr('alt', messageData.senderName || 'Avatar');
            avatarElement.append(avatarImage);
            messageHeader.append(avatarElement);
        }
        
        // Ajouter le nom de l'expéditeur
        if (messageData.senderName) {
            const senderNameElement = $('<div class="message-sender-name"></div>');
            senderNameElement.text(messageData.senderName);
            messageHeader.append(senderNameElement);
        }
        
        // Ajouter l'horodatage
        const timestampElement = $('<div class="message-timestamp"></div>');
        const timestamp = new Date(messageData.timestamp || messageData.createdAt);
        timestampElement.text(this.formatTimestamp(timestamp));
        messageHeader.append(timestampElement);
        
        // Ajouter l'en-tête au message
        messageElement.append(messageHeader);
        
        // Créer le contenu du message
        const contentElement = $('<div class="message-content"></div>');
        contentElement.html(this.formatMessageContent(messageData.content));
        messageElement.append(contentElement);
        
        // Créer le pied de page du message
        const messageFooter = $('<div class="message-footer"></div>');
        
        // Ajouter le statut de lecture
        if (messageData.isRead !== undefined) {
            const readStatusElement = $('<div class="message-read-status"></div>');
            if (messageData.isRead) {
                readStatusElement.addClass('read');
                readStatusElement.html('<i class="fas fa-check-double"></i>');
            } else {
                readStatusElement.addClass('unread');
                readStatusElement.html('<i class="fas fa-check"></i>');
            }
            messageFooter.append(readStatusElement);
        }
        
        // Ajouter les réactions
        if (messageData.reactions && messageData.reactions.length > 0) {
            const reactionsElement = $('<div class="message-reactions"></div>');
            messageData.reactions.forEach(reaction => {
                const reactionElement = $('<span class="reaction"></span>');
                reactionElement.text(reaction.emoji);
                reactionElement.attr('data-reaction-id', reaction.id);
                reactionElement.attr('data-user-id', reaction.userId);
                reactionsElement.append(reactionElement);
            });
            messageFooter.append(reactionsElement);
        }
        
        // Ajouter le pied de page au message
        messageElement.append(messageFooter);
        
        return messageElement;
    }

    formatMessageContent(content) {
        if (!content) {
            return '';
        }
        
        // Convertir les URL en liens cliquables
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        let formattedContent = content.replace(urlRegex, url => {
            return `<a href="${url}" target="_blank" rel="noopener noreferrer">${url}</a>`;
        });
        
        // Convertir les sauts de ligne en balises <br>
        formattedContent = formattedContent.replace(/\n/g, '<br>');
        
        // Convertir les mentions @utilisateur en liens
        const mentionRegex = /@(\w+)/g;
        formattedContent = formattedContent.replace(mentionRegex, (match, username) => {
            return `<span class="mention">@${username}</span>`;
        });
        
        // Convertir les hashtags en liens
        const hashtagRegex = /#(\w+)/g;
        formattedContent = formattedContent.replace(hashtagRegex, (match, hashtag) => {
            return `<span class="hashtag">#${hashtag}</span>`;
        });
        
        // Convertir les emojis (si nécessaire)
        // Note: les navigateurs modernes prennent en charge les emojis Unicode nativement
        
        return formattedContent;
    }
    
    formatTimestamp(timestamp) {
        if (!timestamp) {
            return '';
        }
        
        // Convertir en objet Date si c'est une chaîne
        const date = typeof timestamp === 'string' ? new Date(timestamp) : timestamp;
        
        // Vérifier si la date est valide
        if (isNaN(date.getTime())) {
            console.error('Date invalide:', timestamp);
            return '';
        }
        
        // Obtenir la date actuelle
        const now = new Date();
        
        // Calculer la différence en jours
        const diffDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
        
        // Options de formatage pour différents cas
        if (diffDays === 0) {
            // Aujourd'hui: afficher l'heure
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } else if (diffDays === 1) {
            // Hier: afficher "Hier" et l'heure
            return `Hier ${date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
        } else if (diffDays < 7) {
            // Cette semaine: afficher le jour de la semaine et l'heure
            const days = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
            return `${days[date.getDay()]} ${date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
        } else {
            // Plus d'une semaine: afficher la date complète
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }
    
    createReactionsElement(reactions) {
        const reactionsElement = $('<div class="message-reactions"></div>');
        
        Object.entries(reactions).forEach(([emoji, count]) => {
            const reactionElement = $('<span class="reaction"></span>');
            reactionElement.text(`${emoji} <span class="reaction-count">${count}</span>`);
            reactionsElement.append(reactionElement);
        });
        
        return reactionsElement;
    }
    
    updateMessageReactions(messageId, reactions) {
        console.log(`Mise à jour des réactions pour le message ${messageId}:`, reactions);
        
        const messageElement = this.messageElements.get(messageId);
        if (!messageElement) {
            console.warn(`Message ${messageId} non trouvé pour la mise à jour des réactions`);
            return;
        }
        
        // Supprimer les réactions existantes
        messageElement.find('.message-reactions').remove();
        
        // Ajouter les nouvelles réactions
        if (reactions && Object.keys(reactions).length > 0) {
            const reactionsElement = this.createReactionsElement(reactions);
            messageElement.append(reactionsElement);
        }
    }
    
    updateMessageReadStatus(messageId, isRead) {
        console.log(`Mise à jour du statut de lecture pour le message ${messageId}: ${isRead}`);
        
        const messageElement = this.messageElements.get(messageId);
        if (!messageElement) {
            console.warn(`Message ${messageId} non trouvé pour la mise à jour du statut de lecture`);
            return;
        }
        
        const readStatusElement = messageElement.find('.message-read-status');
        if (readStatusElement.length > 0) {
            if (isRead) {
                readStatusElement.addClass('read').html('<i class="fas fa-check-double"></i>');
            } else {
                readStatusElement.removeClass('read').html('<i class="fas fa-check"></i>');
            }
        }
        
        // Mettre à jour l'objet message dans la Map
        const message = this.messages.get(messageId);
        if (message) {
            message.isRead = isRead;
        }
    }
    
    // Nouvelle méthode pour mettre à jour le statut de traitement d'un message
    updateMessageProcessingStatus(messageId, isProcessing) {
        console.log(`Mise à jour du statut de traitement pour le message ${messageId}: ${isProcessing ? 'en cours' : 'terminé'}`);
        
        // Vérifier si le messageId est un ID temporaire
        const isTempId = String(messageId).startsWith('temp-');
        const realMessageId = isTempId ? messageId.replace('temp-', '') : messageId;
        
        // Vérifier si le message existe dans la Map des messages
        if (!this.messages.has(messageId) && !this.messages.has(realMessageId)) {
            console.log(`Message ${messageId} non trouvé dans la Map des messages, impossible de mettre à jour le statut de traitement`);
            return;
        }
        
        // Trouver l'élément du message (essayer avec l'ID original et l'ID réel)
        let messageElement = this.messageElements.get(messageId);
        if (!messageElement && realMessageId !== messageId) {
            messageElement = this.messageElements.get(realMessageId);
        }
        
        if (!messageElement) {
            console.log(`Élément de message ${messageId} non trouvé dans le DOM, mise à jour ignorée`);
            return;
        }
        
        // Mettre à jour l'interface
        const processingIndicator = messageElement.find('.processing-indicator');
        if (processingIndicator.length) {
            if (isProcessing) {
                processingIndicator.show();
            } else {
                processingIndicator.hide();
            }
            console.log(`Indicateur de traitement ${isProcessing ? 'affiché' : 'masqué'} pour le message ${messageId}`);
        } else {
            console.log(`Indicateur de traitement non trouvé pour le message ${messageId}`);
        }
        
        // Mettre à jour les données du message
        const message = this.messages.get(messageId) || this.messages.get(realMessageId);
        if (message) {
            message.isProcessing = isProcessing;
        }
    }
    
    escapeHtml(text) {
        if (!text) return '';
        
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    scrollToBottom() {
        this.messageContainer[0].scrollTop = this.messageContainer[0].scrollHeight;
    }

    sendTypingStatus() {
        // Éviter d'envoyer trop de mises à jour
        const now = Date.now();
        if (now - this.lastTypingUpdate < 2000) {
            return;
        }
        
        this.lastTypingUpdate = now;
        
        // Vérifier que la route typing est définie
        if (!this.config.routes.typing) {
            console.warn('Route typing non configurée, impossible d\'envoyer le statut de frappe');
            return;
        }
        
        fetch(this.config.routes.typing, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            },
            body: JSON.stringify({
                isTyping: true
            })
        })
        .then(response => {
            if (!response.ok) {
                console.error('Erreur lors de l\'envoi du statut de frappe', response);
            }
        })
        .catch(error => {
            console.error('Erreur réseau lors de l\'envoi du statut de frappe', error);
        });
        
        // Annuler le statut de frappe après un délai
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }
        
        this.typingTimeout = setTimeout(() => {
            // Vérifier que la route typing est définie
            if (!this.config.routes.typing) {
                console.warn('Route typing non configurée, impossible d\'annuler le statut de frappe');
                return;
            }
            
            fetch(this.config.routes.typing, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                body: JSON.stringify({
                    isTyping: false
                })
            });
        }, 5000);
    }

    async handleTyping(isTyping = true) {
        const now = Date.now();
        if (now - this.lastTypingUpdate < 1000) return;
        
        this.lastTypingUpdate = now;
        
        if (isTyping) {
            // Vérifier que la route typing est définie
            if (!this.config.routes.typing) {
                console.warn('Route typing non configurée, impossible de gérer le statut de frappe');
                return;
            }
            
            this.sendTypingStatus();
        }
    }

    markMessageAsUnread(messageId) {
        const messageElement = this.messages.get(messageId);
        if (messageElement) {
            messageElement.removeClass('read');
        }
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    initEmojiPicker() {
        // Ajouter la gestion du clic sur le bouton emoji
        this.emojiButton.on('click', (e) => {
            e.preventDefault();
            this.emojiPicker.toggle();
        });
        
        // Fermer le sélecteur d'emoji quand on clique ailleurs
        $(document).on('click', (e) => {
            if (!$(e.target).closest('#emojiButton, #emojiPicker').length) {
                this.emojiPicker.hide();
            }
        });
        
        // Créer la grille d'emojis
        const emojis = ['😀', '😂', '😊', '❤️', '👍', '👎', '😢', '😡', '🎉', '🤔'];
        const emojiGrid = $('<div class="emoji-grid"></div>');
        
        emojis.forEach(emoji => {
            const button = $(`<button class="emoji-btn">${emoji}</button>`);
            button.on('click', () => {
                this.messageInput.val(this.messageInput.val() + emoji);
                this.emojiPicker.hide();
                this.messageInput.focus();
            });
            emojiGrid.append(button);
        });
        
        this.emojiPicker.empty().append(emojiGrid);
    }

    disableSendButton() {
        const sendButton = this.sendButton;
        sendButton.prop('disabled', true);
        sendButton.addClass('disabled');
    }
    
    enableSendButton() {
        const sendButton = this.sendButton;
        sendButton.prop('disabled', false);
        sendButton.removeClass('disabled');
    }
    
    showErrorMessage(message) {
        console.error('Erreur:', message);
        
        // Créer un élément d'alerte
        const alertElement = $('<div class="alert alert-danger alert-dismissible fade show" role="alert"></div>');
        alertElement.text(message);
        
        // Ajouter un bouton de fermeture
        const closeButton = $('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        alertElement.append(closeButton);
        
        // Ajouter l'alerte au conteneur de messages
        this.messageContainer.append(alertElement);
        
        // Défiler vers le bas pour montrer l'alerte
        this.scrollToBottom();
        
        // Supprimer l'alerte après 5 secondes
        setTimeout(() => {
            alertElement.alert('close');
        }, 5000);
    }
    
    showSuccessMessage(message, timeout = 5000) {
        console.log('Succès:', message);
        
        // Créer un élément d'alerte
        const alertElement = $('<div class="alert alert-success alert-dismissible fade show" role="alert"></div>');
        alertElement.text(message);
        
        // Ajouter un bouton de fermeture
        const closeButton = $('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        alertElement.append(closeButton);
        
        // Ajouter l'alerte au conteneur de messages
        this.messageContainer.append(alertElement);
        
        // Défiler vers le bas pour montrer l'alerte
        this.scrollToBottom();
        
        // Masquer automatiquement après le délai spécifié
        if (timeout > 0) {
            setTimeout(() => {
                alertElement.alert('close');
            }, timeout);
        }
    }

    resetInputHeight() {
        this.messageInput.css('height', 'auto');
    }

    deleteMessage(messageId) {
        console.log(`Suppression du message ${messageId}`);
        
        // Vérifier si le message existe
        if (!this.messageElements.has(messageId)) {
            console.log(`Message ${messageId} non trouvé, impossible de le supprimer`);
            return false;
        }
        
        // Récupérer l'élément du message
        const messageElement = this.messageElements.get(messageId);
        
        // Supprimer l'élément du DOM
        $(messageElement).fadeOut(300, function() {
            $(this).remove();
        });
        
        // Supprimer le message des Maps
        this.messageElements.delete(messageId);
        this.messages.delete(messageId);
        
        console.log(`Message ${messageId} supprimé avec succès`);
        return true;
    }

    setPendingMessageTimeout(messageData, userMessageId, timeout = 5000) {
        console.log(`Définition d'un timeout de ${timeout}ms pour le message IA en attente du message utilisateur ${userMessageId}`);
        
        // Créer un identifiant unique pour ce timeout
        const timeoutId = `timeout-${messageData.id || Math.random().toString(36).substring(2, 15)}`;
        
        // Enregistrer le timeout
        this.pendingMessageTimeouts.set(timeoutId, setTimeout(() => {
            console.log(`Timeout expiré pour le message IA en attente du message utilisateur ${userMessageId}`);
            
            // Vérifier si le message utilisateur est arrivé entre-temps
            if (this.messageElements.has(userMessageId)) {
                console.log(`Message utilisateur ${userMessageId} arrivé entre-temps, traitement normal`);
                return;
            }
            
            // Si le message utilisateur n'est toujours pas arrivé, afficher le message IA quand même
            console.log(`Message utilisateur ${userMessageId} toujours absent après ${timeout}ms, affichage forcé du message IA`);
            
            // Récupérer le message IA en attente
            let pendingMessages;
            if (userMessageId.startsWith('temp-')) {
                pendingMessages = this.pendingAIMessagesByTempUserMessage.get(userMessageId) || [];
                this.pendingAIMessagesByTempUserMessage.set(userMessageId, []);
            } else {
                pendingMessages = this.pendingAIMessagesByUserMessage.get(userMessageId) || [];
                this.pendingAIMessagesByUserMessage.set(userMessageId, []);
            }
            
            // Afficher tous les messages IA en attente
            pendingMessages.forEach(msg => {
                console.log('Affichage forcé du message IA:', msg);
                
                // Associer le message IA au dernier message utilisateur connu, s'il existe
                if (this.lastUserMessageId) {
                    console.log(`Association forcée du message IA au dernier message utilisateur connu: ${this.lastUserMessageId}`);
                    msg.userMessageId = this.lastUserMessageId;
                }
                
                // Ajouter le message IA à l'interface
                this.addMessage(msg, true);
            });
            
            // Supprimer le timeout
            this.pendingMessageTimeouts.delete(timeoutId);
        }, timeout));
    }

    processPendingAIMessages(userMessageId) {
        console.log(`Traitement des messages IA en attente pour le message utilisateur ${userMessageId}`);
        
        // Récupérer les messages IA en attente pour ce message utilisateur
        const pendingMessages = this.pendingAIMessagesByUserMessage.get(userMessageId);
        if (!pendingMessages || pendingMessages.length === 0) {
            console.log(`Aucun message IA en attente pour le message utilisateur ${userMessageId}`);
            return;
        }
        
        console.log(`${pendingMessages.length} messages IA en attente trouvés pour le message utilisateur ${userMessageId}`);
        
        // Copier les messages en attente pour éviter les problèmes de modification pendant l'itération
        const messagesToProcess = [...pendingMessages];
        
        // Vider la liste des messages en attente
        this.pendingAIMessagesByUserMessage.set(userMessageId, []);
        
        // Attendre un court instant pour s'assurer que le message utilisateur est bien affiché
        setTimeout(() => {
            messagesToProcess.forEach(msg => {
                console.log('Traitement du message IA en attente:', msg);
                // Utiliser addMessage directement pour bénéficier de la logique d'insertion après le message utilisateur
                this.addMessage(msg, true);
            });
        }, 100);
    }
}
