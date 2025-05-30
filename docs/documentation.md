# Documentation ZenTicket

## Présentation générale

ZenTicket est une application web de gestion de tickets d'assistance technique développée avec le framework Symfony et Twig. L'application permet aux utilisateurs de créer des tickets pour signaler des problèmes, aux techniciens de les traiter, et aux administrateurs de gérer l'ensemble du système.

## Architecture de l'application

L'application est structurée selon le modèle MVC (Modèle-Vue-Contrôleur) de Symfony :
- **Modèles** : Entités Symfony représentant les données (Ticket, Personne, Commentaire, etc.)
- **Vues** : Templates Twig pour l'affichage
- **Contrôleurs** : Classes PHP gérant la logique métier et les requêtes

## Rôles utilisateurs

L'application distingue trois rôles principaux :
- **Utilisateur (ROLE_USER)** : Peut créer et suivre ses tickets
- **Technicien (ROLE_TECHNICIEN)** : Peut traiter les tickets et créer des rapports d'intervention
- **Administrateur (ROLE_ADMIN)** : Peut gérer les utilisateurs, techniciens et l'ensemble du système

## Fonctionnalités principales

### 1. Gestion des tickets

Les tickets sont au cœur de l'application. Chaque ticket possède :
- Un titre et une description
- Une catégorie
- Une priorité (basse, moyenne, haute, urgente)
- Un statut (nouveau, en cours, résolu, clôturé)
- Un utilisateur créateur
- Un technicien assigné (optionnel)
- Des commentaires

Les utilisateurs peuvent créer des tickets, y ajouter des commentaires et suivre leur progression. Les techniciens peuvent modifier le statut des tickets, y répondre et les résoudre.

### 2. Système de chat

L'application intègre deux systèmes de chat :

#### Chat standard
- Permet la communication entre utilisateurs et techniciens
- Associé à un ticket spécifique
- Utilise Mercure pour les communications en temps réel
- Supporte les notifications de frappe et les indicateurs de lecture

#### Chat IA
- Permet aux utilisateurs d'interagir avec un assistant IA avant de créer un ticket
- Utilise l'API OpenAI pour générer des réponses
- Chaque utilisateur a une chatbox unique pour conserver l'historique des conversations
- Permet de créer un ticket si l'IA ne résout pas le problème

### 3. Système d'authentification et sécurité

- Inscription et connexion des utilisateurs
- Protection CSRF pour les formulaires et requêtes AJAX
- Vérification des droits d'accès aux ressources
- Gestion des tokens pour les API

### 4. Administration

Les administrateurs disposent d'un tableau de bord permettant de :
- Gérer les techniciens (ajout, modification, suppression)
- Suivre tous les tickets
- Générer des rapports statistiques
- Configurer les paramètres de l'application

### 5. Rapports et statistiques

L'application permet de générer deux types de rapports :
- Rapports d'intervention (par les techniciens)
- Rapports statistiques (par les administrateurs)

## Spécificités techniques

### Authentification et sécurité

- Les tokens CSRF doivent être envoyés dans l'en-tête HTTP 'X-CSRF-TOKEN' pour les requêtes AJAX
- Les données JSON envoyées au serveur utilisent la clé 'content' pour le contenu des messages

### Chat IA

- Utilise l'API OpenAI pour générer des réponses
- La clé API OpenAI est stockée dans la base de données
- Une commande Symfony `app:update-ia-api-key` permet de mettre à jour la clé API
- Le service ChatAIService gère les appels à l'API OpenAI
- Deux types de chatbox : temporaire (avant création de ticket) et permanente (après création)

### Sécurité des chatbox

- Chaque chatbox est normalement accessible uniquement par son propriétaire
- Des vérifications de sécurité assouplies permettent l'accès aux chatbox temporaires

## Interface utilisateur

L'interface utilisateur est construite avec :
- Bootstrap 5 pour la mise en page et les composants
- Font Awesome pour les icônes
- jQuery pour les interactions JavaScript
- CSS personnalisé pour les styles spécifiques

## Flux d'utilisation typiques

### Pour un utilisateur standard
1. Inscription/Connexion
2. Consultation de l'assistant IA pour tenter de résoudre un problème
3. Création d'un ticket si nécessaire
4. Suivi du ticket et échanges via le chat
5. Résolution du problème

### Pour un technicien
1. Connexion
2. Consultation du tableau de bord technicien
3. Traitement des tickets assignés
4. Communication avec les utilisateurs via le chat
5. Résolution des tickets et création de rapports d'intervention

### Pour un administrateur
1. Connexion
2. Gestion des utilisateurs et techniciens
3. Supervision globale des tickets
4. Génération de rapports statistiques
5. Configuration du système

## Améliorations et maintenance

### Configuration de l'API OpenAI
Pour mettre à jour la clé API OpenAI, utilisez la commande Symfony :
```
php bin/console app:update-ia-api-key VOTRE_CLE_API
```

Une clé API OpenAI valide commence par "sk-" et fait environ 51 caractères.

### Personnalisation des réponses IA
Le service ChatAIService peut être modifié pour personnaliser les réponses de l'IA ou pour changer le modèle utilisé.

## Conclusion

ZenTicket est une application complète de gestion de tickets d'assistance qui combine des fonctionnalités traditionnelles avec des technologies modernes comme l'IA conversationnelle. Son architecture modulaire permet d'ajouter facilement de nouvelles fonctionnalités et de maintenir le code existant.
