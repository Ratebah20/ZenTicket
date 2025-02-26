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
        console.log('Setting up event listeners');
        
        // Utiliser une fonction fléchée pour préserver le contexte 'this'
        this.sendButton.on('click', (e) => {
            console.log('Send button clicked');
            e.preventDefault();
            this.sendMessage();
        });

        this.messageInput.on('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                console.log('Enter key pressed');
                e.preventDefault();
                this.sendMessage();
            }
        });

        this.messageInput.on('input', () => this.handleTyping());

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

        $(window).on('beforeunload', () => {
            if (this.eventSource) {
                this.eventSource.close();
            }
        });

        console.log('Event listeners setup completed');
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
        
        const connectEventSource = () => {
            console.log('Tentative de connexion à Mercure...', {
                readyState: this.eventSource?.readyState,
                attempt: this.connectionAttempts + 1
            });

            if (this.eventSource) {
                console.log('Fermeture de l\'ancienne connexion');
                this.eventSource.close();
            }

            // Construire l'URL avec le topic
            const url = new URL(this.config.mercureUrl);
            url.searchParams.append('topic', `/chat/${this.config.id}`);
            console.log('URL complète:', url.toString());

            // Configuration de EventSource
            const eventSourceInit = {
                withCredentials: false // Désactiver withCredentials pour éviter les problèmes CORS
            };

            try {
                this.eventSource = new EventSource(url.toString(), eventSourceInit);

                this.eventSource.onopen = (e) => {
                    console.log('Connexion Mercure établie', {
                        readyState: this.eventSource.readyState,
                        lastEventId: this.eventSource.lastEventId,
                        url: url.toString()
                    });
                    this.connectionAttempts = 0;

                    // Envoyer un ping pour tester la connexion
                    console.log('Envoi d\'un ping de test...');
                    fetch(url.toString(), {
                        method: 'HEAD',
                        credentials: 'omit'
                    }).then(response => {
                        console.log('Ping Mercure réussi:', response.status);
                    }).catch(error => {
                        console.error('Erreur ping Mercure:', error);
                    });
                };

                this.eventSource.onerror = (e) => {
                    console.error('Erreur Mercure:', {
                        readyState: this.eventSource?.readyState,
                        error: e,
                        url: url.toString()
                    });
                    
                    if (this.eventSource.readyState === EventSource.CLOSED) {
                        const delay = Math.min(1000 * Math.pow(2, this.connectionAttempts), 30000);
                        this.connectionAttempts++;
                        console.log(`Nouvelle tentative dans ${delay/1000} secondes...`);
                        setTimeout(connectEventSource, delay);
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
                        
                        // Vérifier si le message n'existe pas déjà
                        if (!this.messages.has(data.id)) {
                            console.log('Nouveau message Mercure détecté, traitement...');
                            this.handleMercureMessage(data);
                        } else {
                            console.log('Message déjà existant, ignoré:', data.id);
                        }
                    } catch (error) {
                        console.error('Erreur lors du traitement du message:', error);
                        console.error('Message brut reçu:', e.data);
                    }
                };
            } catch (error) {
                console.error('Erreur lors de la configuration de EventSource:', error);
            }
        };

        // Initialiser la connexion
        this.connectionAttempts = 0;
        connectEventSource();
        
        // Vérifier périodiquement l'état de la connexion
        setInterval(() => {
            if (this.eventSource && this.eventSource.readyState === EventSource.CLOSED) {
                console.log('Connexion fermée, tentative de reconnexion...');
                connectEventSource();
            }
        }, 30000);
    }

    handleMercureMessage(data) {
        console.log('Traitement du message Mercure:', {
            type: data.type,
            messageType: data.messageType,
            timestamp: new Date().toISOString(),
            data: data
        });

        if (!data) {
            console.warn('Message invalide reçu:', data);
            return;
        }

        try {
            // Gestion des messages normaux et IA
            if (data.type === 'message') {
                console.log('Nouveau message reçu via Mercure:', data);
                this.addMessage(data, true);
                this.scrollToBottom();
            }
            // Gestion des autres types de messages
            else if (data.type === 'typing') {
                console.log('Mise à jour du statut de frappe:', data);
                this.updateTypingStatus(data.userId, data.isTyping);
            }
            else if (data.type === 'reaction') {
                console.log('Mise à jour des réactions:', data);
                this.updateMessageReactions(data.messageId, data.reactions);
            }
            else if (data.type === 'read') {
                console.log('Mise à jour du statut de lecture:', data);
                this.updateMessageReadStatus(data.messageId, true);
            }
            else {
                console.warn('Type de message inconnu:', data.type);
            }
        } catch (error) {
            console.error('Erreur lors du traitement du message:', error);
            console.error('Stack trace:', error.stack);
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
            existingElement.replaceWith(this.createMessageElement(messageData));
            console.log('Message mis à jour avec succès');
            return;
        }

        console.log('Création d\'un nouveau message...');
        const messageElement = this.createMessageElement(messageData);
        this.messages.set(messageData.id, messageData);
        
        // Ajouter le message au conteneur
        if (messageData.senderId === this.config.userId) {
            console.log('Message envoyé par l\'utilisateur actuel');
            messageElement.addClass('message-sent');
        } else {
            console.log('Message reçu d\'un autre utilisateur');
            messageElement.addClass('message-received');
        }
        
        this.messageContainer.append(messageElement);
        this.scrollToBottom();
        
        // Si le message vient de Mercure et n'est pas de l'utilisateur actuel, marquer comme non lu
        if (fromMercure && messageData.senderId !== this.config.userId) {
            console.log('Marquage du message comme non lu');
            this.markMessageAsUnread(messageData.id);
        }

        console.log('=== Fin de l\'ajout du message ===');
    }

    createMessageElement(messageData) {
        console.log('Creating message element:', messageData);
        
        const isSentByMe = messageData.senderId === this.config.userId;
        const messageElement = $('<div>')
            .addClass(`message ${isSentByMe ? 'sent' : 'received'}`)
            .toggleClass('ai', messageData.messageType === 'ai');

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

    async handleTyping(isTyping = true) {
        const now = Date.now();
        if (now - this.lastTypingUpdate < 1000) return;

        this.lastTypingUpdate = now;
        
        try {
            const response = await fetch(this.config.routes.typing, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                body: JSON.stringify({ typing: isTyping })
            });

            if (!response.ok) {
                const data = await response.json();
                console.error('Error updating typing status:', data);
            }
        } catch (error) {
            console.error('Error updating typing status:', error);
        }
    }

    updateTypingStatus(userId, isTyping) {
        if (userId === this.config.userId) return;

        if (isTyping) {
            this.typingUsers.add(userId);
        } else {
            this.typingUsers.delete(userId);
        }

        this.updateTypingIndicator();
    }

    updateTypingIndicator() {
        if (this.typingUsers.size > 0) {
            this.typingText.text('Quelqu\'un est en train d\'écrire...');
            this.typingIndicator.show();
        } else {
            this.typingIndicator.hide();
        }
    }

    initEmojiPicker() {
        const commonEmojis = ['👍', '👎', '❤️', '😊', '😂', '🎉', '👏', '🤔'];
        
        this.emojiPicker.html(
            commonEmojis.map(emoji => `
                <span class="emoji" data-emoji="${emoji}">
                    ${emoji}
                </span>
            `).join('')
        );

        this.emojiPicker.on('click', '.emoji', (e) => {
            const emoji = $(e.target).data('emoji');
            const currentVal = this.messageInput.val();
            this.messageInput.val(currentVal + emoji).focus();
        });
    }

    markMessageAsUnread(messageId) {
        const messageElement = this.messages.get(messageId);
        if (messageElement) {
            messageElement.removeClass('read');
        }
    }
}
