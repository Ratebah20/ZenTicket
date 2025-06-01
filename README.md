# ZenTicket - Système de Gestion de Tickets
## Projet 3Innov

## Table des Matières
1. [Présentation du Projet](#présentation-du-projet)
2. [Gestion de Projet Agile](#gestion-de-projet-agile)
3. [Architecture Technique](#architecture-technique)
4. [Fonctionnalités](#fonctionnalités)
5. [Installation et Déploiement](#installation-et-déploiement)
6. [Aspects Techniques Avancés](#aspects-techniques-avancés)
7. [Flux d'Utilisation](#flux-dutilisation)

---

## Présentation du Projet

ZenTicket est une application web complète de gestion de tickets d'assistance technique développée dans le cadre du projet pédagogique 3Innov. Cette solution permet aux utilisateurs de signaler des problèmes techniques, aux techniciens de les traiter efficacement, et aux administrateurs de superviser l'ensemble du système.

L'application se distingue par l'intégration d'un assistant IA basé sur l'API OpenAI, offrant une première ligne d'assistance automatisée avant la création éventuelle d'un ticket humain.

### Contexte Pédagogique

Ce projet a été développé en utilisant la méthodologie Agile Scrum avec une équipe pluridisciplinaire composée d'un chef de projet développeur et de spécialistes réseau/cybersécurité. L'approche méthodologique adoptée a permis de livrer une solution fonctionnelle répondant aux besoins identifiés.

---

## Gestion de Projet Agile

### Méthodologie Appliquée

Le développement de ZenTicket s'est appuyé sur la méthodologie Agile Scrum, adaptée à la configuration spécifique de l'équipe. Cette approche a permis d'assurer une gestion optimale et flexible du développement.

#### Analyse Préalable

Une phase d'analyse approfondie a été organisée pour comprendre les besoins précis du projet :
- Identification des exigences d'une solution de ticketing moderne
- Étude de marché des solutions existantes
- Élaboration collective des spécifications fonctionnelles
- Définition d'un cahier des charges clair et priorisé

#### Principes Agiles Appliqués

**Livraison Continue de Valeur**
- Découpage en sprints de 1 à 2 semaines
- Objectifs fonctionnels minimaux viables pour chaque sprint
- Démonstrations systématiques en fin de cycle
- Développement itératif des fonctionnalités principales

**Optimisation des Compétences**
- Répartition des tâches selon l'expertise de chaque membre
- Auto-attribution des tâches via Trello
- Responsabilités claires valorisant les compétences individuelles

#### Organisation de l'Équipe

**Rôles et Responsabilités**
- **Chef de projet** : Product Owner, développement, coordination générale
- **Équipe réseau/cybersécurité** : analyse des besoins, tests fonctionnels, documentation

**Outils et Processus**
- **Trello** : gestion des tâches et transparence de l'avancement
- **Daily Scrum** : synchronisation quotidienne de 10-15 minutes
- **Sprint Reviews** : démonstrations et validation fonctionnelle
- **Rétrospectives** : amélioration continue des processus

#### Résultats de l'Approche Agile

L'application des principes Agile a permis :
- Livraison régulière de fonctionnalités utilisables
- Implication efficace de chaque membre selon ses compétences
- Adaptation continue grâce aux feedbacks réguliers
- Maintien d'une forte motivation d'équipe
- Adéquation entre le produit final et les attentes utilisateurs

---

## Architecture Technique

### Stack Technologique

- **Backend**: PHP 8.2 avec Symfony 6.4
- **Base de données**: MySQL 8.0
- **Frontend**: Twig, Bootstrap 5, jQuery
- **Communication temps réel**: Mercure
- **Intelligence artificielle**: API OpenAI (GPT-3.5 Turbo)
- **Serveur web**: Nginx (en environnement Docker)
- **Gestion des assets**: Symfony Encore

### Structure du Projet

L'application suit l'architecture MVC (Modèle-Vue-Contrôleur) de Symfony :

- **Modèles** : Entités Symfony représentant les données (Ticket, Personne, Chatbox, Message, IA etc)
- **Vues** : Templates Twig pour l'affichage
- **Contrôleurs** : Classes PHP gérant la logique métier et les requêtes
- **Services** : Encapsulation de la logique métier complexe (notamment ChatAIService)
- **Repositories** : Classes gérant l'accès aux données

---

## Fonctionnalités

### 1. Système de Tickets

Le cœur de l'application est un système complet de gestion de tickets permettant :

- Création de tickets avec titre, description, catégorie et priorité
- Suivi des tickets via différents statuts (nouveau, en cours, résolu, clôturé)
- Assignation de tickets aux techniciens
- Ajout de commentaires et pièces jointes
- Notification des changements de statut

### 2. Chat en Temps Réel

L'application intègre deux systèmes de chat distincts :

#### Chat Standard
- Communication entre utilisateurs et techniciens dans le contexte d'un ticket
- Utilisation de Mercure pour les communications en temps réel
- Indicateurs de frappe et de lecture des messages
- Historique des conversations

#### Chat IA
- Assistant IA basé sur l'API OpenAI (GPT-3.5 Turbo)
- Chatbox unique par utilisateur pour conserver l'historique
- Possibilité de créer un ticket si l'IA ne résout pas le problème
- Gestion asynchrone des messages avec accusé de réception immédiat

### 3. Rôles Utilisateurs

L'application distingue trois rôles principaux avec des permissions spécifiques :

#### Utilisateur (ROLE_USER)
- Création et suivi de tickets personnels
- Interaction avec l'assistant IA
- Communication avec les techniciens via le chat
- Consultation de l'historique des tickets

#### Technicien (ROLE_TECHNICIEN)
- Traitement des tickets assignés
- Communication avec les utilisateurs
- Modification du statut des tickets
- Création de rapports d'intervention

#### Administrateur (ROLE_ADMIN)
- Gestion complète des utilisateurs et techniciens
- Supervision de tous les tickets
- Génération de rapports statistiques
- Configuration des paramètres système

### 4. Administration et Rapports

Le système offre des fonctionnalités avancées d'administration :

- **Tableau de bord administrateur** avec vue d'ensemble des tickets et activités
- **Gestion des utilisateurs** (création, modification, suppression)
- **Rapports statistiques** sur l'activité et les performances

---

## Installation et Déploiement

**Voir le fichier README_DOCKER.md pour plus d'informations sur l'installation avec Docker**

## Aspects Techniques Avancés

### 1. Intégration OpenAI

Le chat IA est implémenté via une intégration avec l'API OpenAI :

- Utilisation du modèle **GPT-3.5 Turbo** pour la génération de réponses
- Clé API stockée dans `.env.local` et accessible via l'entité `IA`
- Service `ChatAIService` gérant les appels API, les retry en cas d'échec et les limites de taux
- Gestion du contexte de conversation pour des réponses cohérentes
- Traitement asynchrone des messages pour une expérience utilisateur fluide

### 2. Communication Temps Réel avec Mercure

Les communications en temps réel sont gérées via Mercure :

- Hub Mercure pour la publication et l'abonnement aux événements
- Authentification JWT pour sécuriser les communications
- Notification des nouveaux messages et des indicateurs de frappe
- Configuration CORS pour permettre les communications cross-origin

### 3. Sécurité

L'application implémente plusieurs niveaux de sécurité :

- Protection CSRF pour les formulaires et requêtes AJAX (token dans l'en-tête HTTP 'X-CSRF-TOKEN')
- Contrôle d'accès basé sur les rôles utilisateurs
- Vérification des propriétaires des chatbox (avec exceptions pour les chatbox temporaires)
- Validation des données côté serveur et client

---

## Flux d'Utilisation

### Parcours Utilisateur Standard

1. **Inscription/Connexion** sur la plateforme ZenTicket
2. **Consultation de l'assistant IA** pour tenter de résoudre un problème
3. **Création d'un ticket** si l'IA ne résout pas le problème
4. **Suivi du ticket** et échanges avec le technicien via le chat
5. **Résolution du problème** et clôture du ticket

### Parcours Technicien

1. **Connexion au tableau de bord technicien**
2. **Consultation des tickets assignés** par ordre de priorité
3. **Traitement des tickets** avec communication utilisateur
4. **Résolution des tickets** et création de rapports d'intervention

### Parcours Administrateur

1. **Supervision générale** via le tableau de bord administrateur
2. **Gestion des utilisateurs** et attribution des rôles
3. **Analyse des rapports statistiques** pour optimiser le service
4. **Configuration système** et paramétrage des outils

---

## Développement et Contribution

### Standards de Développement

Le projet suit les standards Symfony :
- Architecture MVC respectée
- Services pour la logique métier complexe
- Tests unitaires et fonctionnels (en développement)
- Documentation du code selon les standards PSR

---

## Support et Documentation



### Contact et Support

Ce projet a été développé dans le cadre du programme pédagogique 3Innov. Pour toute question technique ou méthodologique, consulter la documentation du projet ou contacter l'équipe de développement : 

- rateb.ahmadi@estiam.com (Développeur)
- tomy.hombourger@estiam.com (Cybersécurité - Réseau)
- soulaymane.kadi@estiam.com (Cybersécurité - Réseau)
- romain.figaroli@estiam.com (Cybersécurité - Réseau)
- mohamed.chaouay-tissir@estiam.com (Cybersécurité - Réseau)
- jonathan.bernard@estiam.com (Cybersécurité - Réseau)

---

## Conclusion

ZenTicket représente une solution complète et moderne de gestion de tickets d'assistance qui combine des fonctionnalités traditionnelles avec des technologies avancées comme l'IA conversationnelle. 

L'approche méthodologique Agile adoptée a permis de démontrer qu'une équipe pluridisciplinaire peut efficacement développer une application complexe en optimisant les compétences de chacun. La combinaison d'un système de tickets classique avec un assistant IA offre une expérience utilisateur améliorée et permet de réduire la charge de travail des techniciens en résolvant automatiquement les problèmes simples.

Son architecture modulaire et sa base technique solide permettent d'envisager facilement de nouvelles fonctionnalités et évolutions futures.