# API Resources pour 3INNOV

Ce répertoire contient les ressources API exposées via API Platform.

## Ressources disponibles

### TicketResource

Représente les tickets du système de support technique.

- **Endpoints**:
  - `GET /api/ticket_resources` : Liste tous les tickets
  - `GET /api/ticket_resources/{id}` : Récupère un ticket spécifique
  - `POST /api/ticket_resources` : Crée un nouveau ticket
  - `PUT /api/ticket_resources/{id}` : Met à jour un ticket existant
  - `DELETE /api/ticket_resources/{id}` : Supprime un ticket

- **Filtres**:
  - Recherche par titre, statut, priorité, utilisateur, technicien, catégorie
  - Tri par ID, date de création, priorité
  - Filtrage par date de création

### UtilisateurResource

Représente les utilisateurs du système.

- **Endpoints**:
  - `GET /api/utilisateur_resources` : Liste tous les utilisateurs
  - `GET /api/utilisateur_resources/{id}` : Récupère un utilisateur spécifique
  - `POST /api/utilisateur_resources` : Crée un nouvel utilisateur
  - `PUT /api/utilisateur_resources/{id}` : Met à jour un utilisateur existant
  - `DELETE /api/utilisateur_resources/{id}` : Supprime un utilisateur

- **Filtres**:
  - Recherche par nom, email
  - Tri par ID, nom, email

### TechnicienResource

Représente les techniciens du système.

- **Endpoints**:
  - `GET /api/technicien_resources` : Liste tous les techniciens
  - `GET /api/technicien_resources/{id}` : Récupère un technicien spécifique
  - `POST /api/technicien_resources` : Crée un nouveau technicien
  - `PUT /api/technicien_resources/{id}` : Met à jour un technicien existant
  - `DELETE /api/technicien_resources/{id}` : Supprime un technicien

- **Filtres**:
  - Recherche par nom, email, spécialité
  - Tri par ID, nom, email, spécialité

### CategorieResource

Représente les catégories de tickets.

- **Endpoints**:
  - `GET /api/categorie_resources` : Liste toutes les catégories
  - `GET /api/categorie_resources/{id}` : Récupère une catégorie spécifique
  - `POST /api/categorie_resources` : Crée une nouvelle catégorie
  - `PUT /api/categorie_resources/{id}` : Met à jour une catégorie existante
  - `DELETE /api/categorie_resources/{id}` : Supprime une catégorie

- **Filtres**:
  - Recherche par nom, description
  - Tri par ID, nom

### CommentaireResource

Représente les commentaires sur les tickets.

- **Endpoints**:
  - `GET /api/commentaire_resources` : Liste tous les commentaires
  - `GET /api/commentaire_resources/{id}` : Récupère un commentaire spécifique
  - `POST /api/commentaire_resources` : Crée un nouveau commentaire
  - `PUT /api/commentaire_resources/{id}` : Met à jour un commentaire existant
  - `DELETE /api/commentaire_resources/{id}` : Supprime un commentaire

- **Filtres**:
  - Recherche par contenu, ticket, auteur
  - Tri par ID, date de création
  - Filtrage par date de création

### ChatboxResource

Représente les boîtes de discussion.

- **Endpoints**:
  - `GET /api/chatbox_resources` : Liste toutes les chatbox
  - `GET /api/chatbox_resources/{id}` : Récupère une chatbox spécifique
  - `POST /api/chatbox_resources` : Crée une nouvelle chatbox
  - `PUT /api/chatbox_resources/{id}` : Met à jour une chatbox existante
  - `DELETE /api/chatbox_resources/{id}` : Supprime une chatbox

- **Filtres**:
  - Recherche par ticket, IA, statut temporaire
  - Tri par ID, date de création
  - Filtrage par date de création

### MessageResource

Représente les messages dans les chatbox.

- **Endpoints**:
  - `GET /api/message_resources` : Liste tous les messages
  - `GET /api/message_resources/{id}` : Récupère un message spécifique
  - `POST /api/message_resources` : Crée un nouveau message
  - `PUT /api/message_resources/{id}` : Met à jour un message existant
  - `DELETE /api/message_resources/{id}` : Supprime un message

- **Filtres**:
  - Recherche par contenu, chatbox, type, statut de lecture
  - Tri par ID, horodatage
  - Filtrage par horodatage

### NotificationResource

Représente les notifications système.

- **Endpoints**:
  - `GET /api/notification_resources` : Liste toutes les notifications
  - `GET /api/notification_resources/{id}` : Récupère une notification spécifique
  - `POST /api/notification_resources` : Crée une nouvelle notification
  - `PUT /api/notification_resources/{id}` : Met à jour une notification existante
  - `DELETE /api/notification_resources/{id}` : Supprime une notification

- **Filtres**:
  - Recherche par titre, message, type, utilisateur, ticket
  - Tri par ID, date de création
  - Filtrage par date de création et statut de lecture

### RapportResource

Représente les rapports d'intervention et statistiques.

- **Endpoints**:
  - `GET /api/rapport_resources` : Liste tous les rapports
  - `GET /api/rapport_resources/{id}` : Récupère un rapport spécifique
  - `POST /api/rapport_resources` : Crée un nouveau rapport
  - `PUT /api/rapport_resources/{id}` : Met à jour un rapport existant
  - `DELETE /api/rapport_resources/{id}` : Supprime un rapport

- **Filtres**:
  - Recherche par titre, contenu, type, période, service
  - Tri par ID, date de création
  - Filtrage par date de création

## Utilisation

1. Accédez à `/api/doc` pour explorer la documentation interactive de l'API
2. Utilisez les endpoints avec les formats JSON-LD, JSON ou HTML

## Sécurité

L'API est configurée pour utiliser l'authentification JWT. Assurez-vous d'inclure votre token JWT dans l'en-tête `Authorization`.

## Pagination

La pagination est activée par défaut avec 10 éléments par page. Vous pouvez modifier le nombre d'éléments par page avec le paramètre `itemsPerPage`.

Exemple : `/api/ticket_resources?itemsPerPage=20`
