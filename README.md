# 🎫 ZenTicket - Système de Gestion de Tickets

![ZenTicket Logo](public/images/zenlogo.png)

## 📋 Présentation

ZenTicket est une solution de gestion de tickets de support avec chat intégré en temps réel, développée avec Symfony 6.4. Cette application permet de centraliser et rationaliser les demandes d'assistance technique en offrant une communication instantanée entre utilisateurs, techniciens et intelligence artificielle.

> 📌 **Pour l'installation**, consultez [le guide d'installation Docker](docs/readme-simple-docker.md) ou la section Installation en bas de ce document.

## 🌟 Fonctionnalités principales

### Gestion des tickets
- Création, assignation et suivi de tickets
- Système de priorités et catégories
- Filtrage et recherche avancés
- Historique complet et traçabilité des modifications

### Communication en temps réel
- Chat intégré entre utilisateurs et techniciens
- Notifications instantanées via Mercure
- Statut de frappe en temps réel
- Historique de conversation persistent

### Assistant IA
- Chatbot intégré propulsé par OpenAI
- Assistance technique automatisée
- Réponses personnalisées aux questions fréquentes

### Tableaux de bord et rapports
- Statistiques détaillées et graphiques
- Suivi de performance des techniciens
- Rapports d'intervention automatisés
- Métriques temporelles (temps de résolution, etc.)

### Sécurité
- Authentification avec rôles différenciés
- Protection CSRF dans les formulaires et API
- Validation stricte des entrées
- Chiffrement des données sensibles

### Interface utilisateur
- Design responsive avec Bootstrap 5
- Thème sombre par défaut
- Interface intuitive et accessible
- Indicateurs visuels de statut et priorité

## 🔧 Architecture technique

### Backend

#### 🏗️ Framework et Langages
- **PHP 8.3**: Langage principal
- **Symfony 6.4**: Framework MVC
- **Doctrine ORM**: Couche d'accès aux données
- **Twig**: Moteur de templates

#### 🔄 Bibliothèques et Services
- **Mercure**: Hub pour communications temps réel
- **OpenAI API**: Intelligence artificielle
- **Symfony Messenger**: File de messages asynchrones
- **Symfony Mailer**: Envoi d'emails

#### 🔍 Structure du Code Backend

```php
// Structure des controllers principaux
src/Controller/
  - TicketController.php     # Gestion des tickets
  - ChatController.php       # Système de chat
  - TechnicienController.php # Dashboard technicien
  - AdminController.php      # Administration
  - IAController.php         # Communication avec l'IA
  - etc...
```

### Frontend

#### 🎨 Technologies et Frameworks
- **JavaScript ES6+**: Interactivité
- **jQuery**: Manipulation DOM et AJAX
- **Bootstrap 5**: Framework CSS
- **Webpack Encore**: Bundling et optimisation
- **SCSS**: Styles préprocessés
- **ChartJS**: Visualisation de données

#### 📱 Interface utilisateur
- Design responsive adapté à tous les appareils
- Thème sombre pour réduction de la fatigue oculaire
- Icônes Font Awesome 5
- Notifications toast pour les alertes

## 📊 Modèle de données

### Entités principales

#### User
- Système multi-rôles: Admin, Technicien, Utilisateur
- Profils personnalisables
- Historique d'activité

#### Ticket
- Statuts multiples (Nouveau, En cours, Résolu, etc.)
- Système de priorités (Basse, Moyenne, Haute, Critique)
- Catégorisation par type de problème
- Assignation aux techniciens

#### ChatBox
- Liaison avec tickets ou assistance IA
- Historique complet des messages
- Support de fichiers attachés

#### Rapport
- Rapport d'intervention
- Métriques de résolution
- Documentation des solutions appliquées

### Relations

```
User (1) <----> (*) Ticket (créé par)
User (1) <----> (*) Ticket (assigné à)
Ticket (1) <----> (1) ChatBox
User (1) <----> (*) Message
ChatBox (1) <----> (*) Message
Ticket (1) <----> (*) Rapport
```

## 🔌 Intégrations externes

### OpenAI
- Utilisation de l'API GPT pour l'assistant IA
- Traitement en langage naturel des demandes


### Système d'emails
- Notifications par email
- Confirmation de création/résolution de tickets
- Rappels automatiques

### Mercure
- Communication bidirectionnelle en temps réel
- Publication et abonnement aux événements
- Sécurisation par JWT

## 🔐 Sécurité

### Authentification et Autorisation
- Système de rôles hiérarchiques
- Sessions sécurisées
- Protection contre la force brute

### Sécurisation des données
- Validation CSRF sur tous les formulaires et API
- Échappement des données dans les templates
- Sanitisation des entrées utilisateur

### Communications
- HTTPS pour toutes les communications
- Chiffrement des données sensibles
- Tokens JWT pour l'API et Mercure

## 🧠 Logique métier

### Cycle de vie d'un ticket

1. **Création**: Un utilisateur soumet un ticket via le formulaire
2. **Notification**: Les techniciens reçoivent une alerte
3. **Assignation**: Un technicien prend en charge le ticket
4. **Résolution**: Le technicien résout le problème
5. **Rapport**: Génération d'un rapport d'intervention
6. **Feedback**: L'utilisateur peut évaluer la résolution


### Métriques et KPIs

- Temps moyen de résolution
- Satisfaction utilisateur
- Volume de tickets par catégorie
- Performance des techniciens

## 📦 Structure du projet

```
ZenTicket/
├── assets/              # Ressources frontend
│   ├── controllers/     # Contrôleurs Stimulus
│   ├── styles/          # Fichiers SCSS
│   └── js/              # JavaScript
├── bin/                 # Exécutables
├── config/              # Configuration Symfony
│   ├── packages/        # Config des bundles
│   ├── routes/          # Définition des routes
│   └── services.yaml    # Définition des services
├── docker/              # Configuration Docker
│   ├── mysql/           # Config MySQL et dumps
│   ├── nginx/           # Config serveur web
│   └── php/             # Dockerfile PHP
├── migrations/          # Migrations de BDD
├── public/              # Fichiers publics
│   ├── build/           # Assets compilés (Webpack)
│   ├── images/          # Images statiques
│   └── js/              # JavaScript statique
├── src/                 # Code source PHP
│   ├── Controller/      # Contrôleurs Symfony
│   ├── Entity/          # Entités Doctrine
│   ├── Repository/      # Repositories
│   ├── Service/         # Services métier
│   ├── Security/        # Classes de sécurité
│   └── Twig/            # Extensions Twig
├── templates/           # Templates Twig
├── tests/               # Tests automatisés
├── translations/        # Traductions i18n
├── docker-compose.yml   # Config Docker Compose
├── composer.json        # Dépendances PHP
└── package.json         # Dépendances NPM
```

## 🛠️ Développement


### Architecture Mercure

Topics utilisés :
- `/chat/{id}` - Messages de chat
- `/ticket/new` - Notifications de nouveaux tickets
- `/user/{id}/notifications` - Notifications utilisateur

## Installation

Pour les instructions d'installation complètes, référez-vous au [Guide d'installation Docker](docs/readme-simple-docker.md).

## 👥 Équipe ZenTicket

Développé par l'équipe ZenTicket dans le cadre d'un projet 3Innov avancé.

## 📞 Support et Contact

 Contact : 
 Ratib132@gmail.com
 tomy@3innov.fr
 Romel@3innov.fr
 Souleymane@3innov.fr
 jonathan@3innov.fr
 Mohamed@3innov.fr

---
