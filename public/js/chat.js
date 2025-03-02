class Chat {
    constructor(config) {
        console.log('Chat constructor called with config:', config);
        if (!config) {
            console.error('No config provided to Chat constructor');
            return;
        }

        this.config = config;
        this.messageContainer = $('#messages');
        this.messageInput = $('#messageInput');
        this.sendButton = $('#sendButton');
        this.emojiButton = $('#emojiButton');
        this.emojiPicker = $('#emojiPicker');
        this.typingIndicator = $('.typing-indicator');
        this.typingText = $('.typing-text');
        this.typingTimeout = null;
        this.lastTypingUpdate = 0;
        this.messages = new Map();
        this.typingUsers = new Set();
        this.eventSource = null;
        this.connectionAttempts = 0;
        this.maxReconnectAttempts = 10;
        this.baseReconnectDelay = 1000;
        this.lastMessageId = null;
        this.processedMessageIds = new Set();
        this.reconnectButton = $('#reconnect-button');

        this.currentPage = 1;
        this.isInitialized = false;

        // Vérifier que les éléments DOM existent
        if (!this.messageContainer.length) {
            console.error('Message container not found');
            return;
        }
        if (!this.messageInput.length) {
            console.error('Message input not found');
            return;
        }
        if (!this.sendButton.length) {
            console.error('Send button not found');
            return;
        }

        this.init();
    }

    async init() {
        console.log('Initializing chat...');
        try {
            await this.loadMessages();
            this.setupEventListeners();
            this.initEmojiPicker();
            this.setupEventSource();
            this.isInitialized = true;
            console.log('Chat initialized successfully');
        } catch (error) {
            console.error('Error initializing chat:', error);
        }
    }

    setupEventListeners() {
        console.log('Configuration des écouteurs d\'événements');
        
        this.sendButton.on('click', () => {
            this.sendMessage();
        });
        
        this.messageInput.on('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        this.messageInput.on('input', () => {
            this.sendTypingStatus();
        });
        
        this.reconnectButton.on('click', () => {
            console.log('Bouton de reconnexion cliqué');
            this.setupEventSource();
        });
        
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

    async sendMessage() {
        const content = this.messageInput.val().trim();
        console.log('=== Début de l\'envoi du message ===');
        console.log('Message à envoyer:', content);
        console.log('Configuration:', {
            routes: this.config.routes,
            csrfToken: this.config.csrfToken ? 'présent' : 'manquant'
        });
        
        if (!content) {
            console.log('Message vide, annulation de l\'envoi');
            return;
        }

        if (!this.config.routes.send) {
            console.error('Route d\'envoi non configurée');
            return;
        }

        if (!this.config.csrfToken) {
            console.error('Token CSRF manquant');
            return;
        }
        
        // Vérifier la connexion Mercure avant d'envoyer
        this.testMercureConnection();

        try {
            console.log('Envoi de la requête à:', this.config.routes.send);
            console.log('Payload:', { message: content });
            console.log('En-têtes:', {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken
            });
            
            const response = await fetch(this.config.routes.send, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                body: JSON.stringify({ message: content })
            });

            console.log('Réponse brute:', response);
            console.log('Statut de la réponse:', response.status);
            console.log('En-têtes de la réponse:', {
                type: response.headers.get('content-type'),
                status: response.statusText
            });

            const responseText = await response.text();
            console.log('Texte de la réponse:', responseText);

            let data;
            try {
                data = JSON.parse(responseText);
                console.log('Données de la réponse (JSON):', data);
            } catch (e) {
                console.error('Erreur lors du parsing JSON:', e);
                console.log('Réponse non-JSON reçue:', responseText);
                throw new Error('Réponse invalide du serveur');
            }

            if (response.ok) {
                console.log('Message envoyé avec succès');
                this.messageInput.val('');
                this.handleTyping(false);
                
                console.log('Ajout du message à l\'interface');
                this.addMessage(data, false);

                console.log('En attente de la réponse de l\'IA...');
            } else {
                console.error('Erreur lors de l\'envoi:', data);
                if (data.error) {
                    const errorMessage = data.error + (data.details ? '\n' + data.details : '');
                    console.error('Message d\'erreur:', errorMessage);
                    alert(errorMessage);
                } else {
                    console.error('Erreur générique');
                    alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');
                }
            }
        } catch (error) {
            console.error('Erreur technique lors de l\'envoi:', error);
            console.error('Stack trace:', error.stack);
            alert('Erreur de connexion. Veuillez réessayer.');
        }
        
        console.log('=== Fin de l\'envoi du message ===');
    }

    setupEventSource() {
        console.log('=== Configuration de la connexion Mercure ===');
        console.log('Topic URL:', this.config.mercureUrl);
        console.log('Subscriber Token:', this.config.subscriberToken ? 'présent' : 'manquant');
        
        let reconnectTimeout = null;
        
        const connectEventSource = () => {
            console.log('Tentative de connexion à Mercure...', {
                readyState: this.eventSource?.readyState,
                attempt: this.connectionAttempts + 1
            });

            if (this.eventSource) {
                console.log('Fermeture de l\'ancienne connexion');
                this.eventSource.close();
            }

            // Construire l'URL avec le topic uniquement
            const url = new URL(this.config.mercureUrl);
            url.searchParams.append('topic', `/chat/${this.config.id}`);
            
            console.log('URL complète:', url.toString());

            try {
                console.log('Création de l\'objet EventSource...');
                
                // Créer une instance de EventSourcePolyfill qui supporte les en-têtes personnalisés
                const headers = {};
                
                // Ajouter le token dans l'en-tête Authorization
                if (this.config.subscriberToken) {
                    headers['Authorization'] = `Bearer ${this.config.subscriberToken}`;
                }
                
                // Utiliser un polyfill EventSource qui prend en charge les en-têtes personnalisés
                // Si EventSourcePolyfill n'est pas disponible, nous utilisons une solution de secours
                if (typeof EventSourcePolyfill !== 'undefined') {
                    console.log('Utilisation de EventSourcePolyfill avec en-têtes personnalisés');
                    this.eventSource = new EventSourcePolyfill(url.toString(), {
                        headers: headers
                    });
                } else {
                    // Solution de secours: utiliser le cookie pour l'authentification
                    console.log('EventSourcePolyfill non disponible, utilisation de cookies');
                    document.cookie = `mercureAuthorization=Bearer ${this.config.subscriberToken}; path=/; SameSite=Lax`;
                    this.eventSource = new EventSource(url.toString(), { withCredentials: true });
                }
                
                console.log('EventSource créé avec succès, état initial:', this.eventSource.readyState);

                this.eventSource.onopen = (e) => {
                    console.log('Connexion Mercure établie', {
                        readyState: this.eventSource.readyState,
                        lastEventId: this.eventSource.lastEventId,
                        url: url.toString()
                    });
                    this.connectionAttempts = 0;
                    
                    // Effacer tout timeout de reconnexion existant
                    if (reconnectTimeout) {
                        clearTimeout(reconnectTimeout);
                        reconnectTimeout = null;
                    }
                    
                    // Indiquer visuellement que la connexion est établie
                    console.log('Connexion WebSocket active');
                    
                    // Vérifier si des messages sont en attente
                    console.log('Vérification des messages en attente...');
                    this.loadMessages();
                };

                this.eventSource.onerror = (event) => {
                    console.error('Erreur EventSource:', event);
                    
                    // Afficher plus d'informations sur l'erreur
                    if (event.target && event.target.readyState) {
                        console.error('État de la connexion lors de l\'erreur:', 
                            event.target.readyState === EventSource.CONNECTING ? 'CONNECTING' :
                            event.target.readyState === EventSource.OPEN ? 'OPEN' :
                            event.target.readyState === EventSource.CLOSED ? 'CLOSED' : 'UNKNOWN'
                        );
                    }
                    
                    // Vérifier si l'erreur est liée à CORS
                    if (event.target && event.target.url) {
                        console.error('URL qui a causé l\'erreur:', event.target.url);
                    }
                    
                    // Afficher le bouton de reconnexion
                    this.reconnectButton.show();
                    
                    if (this.eventSource.readyState === EventSource.CLOSED) {
                        console.log('Connexion fermée, tentative de reconnexion...');
                        this.connectionAttempts++;
                        
                        if (this.connectionAttempts < this.maxReconnectAttempts) {
                            const delay = Math.min(
                                this.baseReconnectDelay * Math.pow(1.5, this.connectionAttempts),
                                30000
                            );
                            console.log(`Reconnexion dans ${delay}ms (tentative ${this.connectionAttempts}/${this.maxReconnectAttempts})`);
                            
                            if (reconnectTimeout) {
                                clearTimeout(reconnectTimeout);
                            }
                            
                            reconnectTimeout = setTimeout(() => {
                                connectEventSource();
                            }, delay);
                        } else {
                            console.error(`Nombre maximum de tentatives de reconnexion atteint (${this.maxReconnectAttempts})`);
                        }
                    }
                };

                this.eventSource.onmessage = (e) => {
                    console.log('Message Mercure reçu :', {
                        data: e.data,
                        lastEventId: e.lastEventId,
                        origin: e.origin,
                        eventType: e.type
                    });

                    try {
                        const data = JSON.parse(e.data);
                        console.log('Message parsé :', data);
                        
                        this.handleMercureMessage(data);
                    } catch (error) {
                        console.error('Erreur lors du traitement du message:', error);
                        console.error('Message brut reçu:', e.data);
                    }
                };
                
                // Vérifier l'état de la connexion toutes les 30 secondes
                setInterval(() => {
                    if (this.eventSource) {
                        const state = this.eventSource.readyState;
                        console.log('État actuel de la connexion EventSource:', 
                            state === EventSource.CONNECTING ? 'CONNECTING' :
                            state === EventSource.OPEN ? 'OPEN' :
                            state === EventSource.CLOSED ? 'CLOSED' : 'UNKNOWN'
                        );
                        
                        // Si la connexion est fermée, afficher le bouton de reconnexion
                        if (state === EventSource.CLOSED) {
                            this.reconnectButton.show();
                        } else if (state === EventSource.OPEN) {
                            this.reconnectButton.hide();
                        }
                    }
                }, 30000);
                
                console.log('EventSource configuré avec succès');
            } catch (error) {
                console.error('Erreur lors de la création de EventSource:', error);
                this.reconnectButton.show();
            }
        };
        
        // Réinitialiser le compteur de tentatives et démarrer la connexion
        this.connectionAttempts = 0;
        connectEventSource();
        
        // Vérifier périodiquement l'état de la connexion
        this.connectionCheckInterval = setInterval(() => {
            if (!this.eventSource || this.eventSource.readyState === EventSource.CLOSED) {
                console.log('Connexion fermée ou inexistante, tentative de reconnexion...');
                connectEventSource();
            } else if (this.eventSource.readyState === EventSource.CONNECTING) {
                console.log('Connexion en cours...');
            } else if (this.eventSource.readyState === EventSource.OPEN) {
                console.log('Connexion active');
            }
        }, 30000);
    }

    testMercureConnection() {
        console.log('Test de la connexion Mercure');
        
        if (!this.eventSource) {
            console.log('Pas de connexion EventSource, reconnexion...');
            this.setupEventSource();
            return false;
        }
        
        if (this.eventSource.readyState === EventSource.CLOSED) {
            console.log('Connexion fermée, reconnexion...');
            this.setupEventSource();
            return false;
        }
        
        if (this.eventSource.readyState === EventSource.CONNECTING) {
            console.log('Connexion en cours...');
            return false;
        }
        
        console.log('Connexion Mercure active');
        return true;
    }

    handleMercureMessage(data) {
        console.log('Message Mercure reçu:', data);
        
        // Gérer les différents types de messages
        if (data.type === 'typing') {
            this.handleTypingEvent(data);
            return;
        } else if (data.type === 'reaction') {
            if (data.messageId && data.reactions) {
                this.updateMessageReactions(data.messageId, data.reactions);
            }
            return;
        } else if (data.type === 'read_status') {
            if (data.messageId && data.isRead !== undefined) {
                this.updateMessageReadStatus(data.messageId, data.isRead);
            }
            return;
        } else if (data.type === 'message' || !data.type) {
            // Si c'est un message normal ou si le type n'est pas spécifié (compatibilité)
            if (data.id) {
                // Vérifier si on a déjà traité ce message
                if (this.processedMessageIds.has(data.id)) {
                    console.log(`Message ${data.id} déjà traité, ignoré`);
                    return;
                }
                
                this.processedMessageIds.add(data.id);
                
                // Mettre à jour le dernier ID de message
                this.lastMessageId = data.id;
                
                // Ajouter le message à l'interface
                this.addMessageToUI(data);
            }
        } else {
            console.log(`Type de message inconnu: ${data.type}`, data);
        }
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

    addMessageToUI(messageData) {
        console.log('Ajout du message à l\'interface:', messageData);
        
        // Ajouter le message à l'interface
        this.addMessage(messageData, true);
        
        // Défiler vers le bas
        this.scrollToBottom();
    }

    addMessage(messageData, fromMercure = false) {
        console.log('=== Ajout d\'un message ===');
        console.log('Message data:', messageData);
        console.log('From Mercure:', fromMercure);
        
        if (!messageData || !messageData.id) {
            console.error('Données de message invalides:', messageData);
            return;
        }

        // Si le message existe déjà, le mettre à jour
        const existingElement = $(`[data-message-id="${messageData.id}"]`);
        if (existingElement.length) {
            console.log('Message existant trouvé, mise à jour...');
            
            // Pour les messages IA, toujours forcer la mise à jour
            if (messageData.messageType === 'ai' && fromMercure) {
                console.log('Mise à jour forcée du message IA existant');
                const messageContent = existingElement.find('.message-content');
                messageContent.html(this.escapeHtml(messageData.content));
                
                // Mettre à jour les données du message
                this.messages.set(messageData.id, messageData);
            } else {
                const newElement = this.createMessageElement(messageData);
                existingElement.replaceWith(newElement);
                this.messages.set(messageData.id, newElement);
            }
            
            console.log('Message mis à jour avec succès');
            return existingElement;
        }

        console.log('Création d\'un nouveau message...');
        const messageElement = this.createMessageElement(messageData);
        
        // Ajouter le message au conteneur
        if (messageData.senderId === this.config.userId) {
            console.log('Message envoyé par l\'utilisateur actuel');
            messageElement.addClass('message-sent');
        } else {
            console.log('Message reçu d\'un autre utilisateur');
            messageElement.addClass('message-received');
        }
        
        this.messageContainer.append(messageElement);
        this.messages.set(messageData.id, messageElement);
        this.scrollToBottom();
        
        // Si le message vient de Mercure et n'est pas de l'utilisateur actuel, marquer comme non lu
        if (fromMercure && messageData.senderId !== this.config.userId) {
            console.log('Marquage du message comme non lu');
            this.markMessageAsUnread(messageData.id);
        }

        console.log('=== Fin de l\'ajout du message ===');
        return messageElement;
    }

    createMessageElement(messageData) {
        console.log('Creating message element:', messageData);
        
        const isSentByMe = messageData.senderId === this.config.userId;
        const messageElement = $('<div>')
            .addClass(`message ${isSentByMe ? 'sent' : 'received'}`)
            .toggleClass('ai', messageData.messageType === 'ai')
            .attr('data-message-id', messageData.id);

        const timestamp = new Date(messageData.timestamp);
        const timeString = timestamp.toLocaleTimeString();

        messageElement.html(`
            <div class="message-content">${this.escapeHtml(messageData.content)}</div>
            <div class="message-meta">
                <small class="text-muted">${timeString}</small>
                ${messageData.isRead ? '<span class="read-status"><i class="fas fa-check"></i></span>' : ''}
            </div>
            <div class="reactions" data-message-id="${messageData.id}">
                ${this.renderReactions(messageData.reactions)}
            </div>
        `);

        return messageElement;
    }

    updateMessageReactions(messageId, reactions) {
        const messageElement = this.messages.get(messageId);
        if (messageElement) {
            messageElement.find('.reactions').html(this.renderReactions(reactions));
        }
    }

    updateMessageReadStatus(messageId, isRead) {
        const messageElement = this.messages.get(messageId);
        if (messageElement) {
            messageElement.toggleClass('read', isRead);
        }
    }

    renderReactions(reactions) {
        if (!reactions || Object.keys(reactions).length === 0) {
            return '';
        }

        return Object.entries(reactions)
            .map(([emoji, count]) => `
                <span class="reaction" data-emoji="${emoji}">
                    ${emoji} <span class="count">${count}</span>
                </span>
            `).join('');
    }

    scrollToBottom() {
        this.messageContainer[0].scrollTop = this.messageContainer[0].scrollHeight;
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    sendTypingStatus() {
        // Éviter d'envoyer trop de mises à jour
        const now = Date.now();
        if (now - this.lastTypingUpdate < 2000) {
            return;
        }
        
        this.lastTypingUpdate = now;
        
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
}
