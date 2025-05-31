# Guide Docker pour ZenTicket

Ce document explique comment déployer et utiliser ZenTicket avec Docker.

## Prérequis

- Docker Desktop installé et fonctionnel sur votre machine
- Docker Compose installé (généralement inclus dans Docker Desktop)

## Services disponibles

Le déploiement Docker de ZenTicket comprend les services suivants :

1. **Base de données MySQL** (Port 3308)
   - Accessible via phpMyAdmin ou directement via un client MySQL
   - Utilisateur: root, Mot de passe: root

2. **PHP-FPM 8.2**
   - Inclut toutes les extensions PHP nécessaires pour Symfony
   - Composer et Symfony CLI préinstallés

3. **Nginx** (Port 8080)
   - Serveur web configuré pour Symfony
   - L'application est accessible à l'adresse http://localhost:8080

4. **phpMyAdmin** (Port 8081)
   - Interface d'administration pour MySQL
   - Accessible à l'adresse http://localhost:8081

5. **Node.js**
   - Pour la compilation des assets avec Webpack Encore

6. **Mercure** (Port 3000)
   - Serveur pour les notifications en temps réel
   - Hub accessible à http://localhost:3000/.well-known/mercure

## Installation et démarrage

### Option 1 : Utiliser Docker Compose directement

```bash
# Construire les images
docker-compose build

# Démarrer les services
docker-compose up -d
```

### Option 2 : Utiliser le Makefile (recommandé)

```bash
# Installer et démarrer tous les services
make install

# Ou étape par étape
make build
make up
make composer-install
make npm-install
make database-create
make migrations
make npm-build
```

## Configuration de l'environnement

Le fichier `.env.local` a été configuré pour fonctionner avec les services Docker. Les paramètres importants incluent :

- La connexion à la base de données utilise le nom du conteneur Docker `database` comme hôte
- La clé API OpenAI est configurée pour le chat IA
- Les paramètres de Mercure sont configurés pour le service de notifications en temps réel

## Commandes utiles

### Gestion des conteneurs

- `make up` - Démarrer les conteneurs
- `make down` - Arrêter les conteneurs
- `make restart` - Redémarrer les conteneurs
- `make logs` - Voir les logs

### Symfony et dépendances

- `make composer-install` - Installer les dépendances PHP
- `make npm-install` - Installer les dépendances JavaScript
- `make npm-build` - Compiler les assets
- `make cache-clear` - Vider le cache Symfony

### Base de données

- `make database-create` - Créer la base de données
- `make migrations` - Exécuter les migrations
- `make reset-db` - Réinitialiser la base de données (suppression et recréation)

### Accès aux shells

- `make console` - Accéder à un shell dans le conteneur PHP
- `make mysql` - Accéder à la console MySQL

## Dépannage

### Problèmes courants

1. **Ports déjà utilisés**
   - Si les ports 8080, 8081 ou 3306 sont déjà utilisés sur votre machine, modifiez les mappings de ports dans le fichier `docker-compose.yml`

2. **Erreurs de permission**
   - Assurez-vous que les dossiers `var/cache` et `var/log` sont accessibles en écriture (chmod 777)

3. **Problèmes avec Mercure**
   - Vérifiez que les clés JWT sont correctement configurées dans `.env.local`

4. **Erreurs de base de données**
   - Vérifiez que le fichier d'initialisation SQL est présent dans `docker/mysql/init.sql`
   - Utilisez phpMyAdmin pour diagnostiquer les problèmes de base de données

## Fonctionnalités spécifiques

### Chat IA (OpenAI)

Le service de chat IA nécessite une clé API OpenAI valide. La clé doit être configurée dans le fichier `.env.local`. Utilisez la commande Symfony créée spécifiquement pour mettre à jour cette clé :

```bash
make console
php bin/console app:update-ia-api-key
```

N'oubliez pas que pour les requêtes AJAX du chat IA, le token CSRF doit être envoyé dans l'en-tête HTTP 'X-CSRF-TOKEN'.

### Mercure (Notifications en temps réel)

Le hub Mercure est configuré et accessible via Nginx. Assurez-vous que votre code JavaScript utilise l'URL publique pour se connecter au hub :

```javascript
const hubUrl = 'http://localhost:3000/.well-known/mercure';
```

## Notes de production

Pour un déploiement en production, pensez à :

1. Modifier les mots de passe par défaut
2. Configurer des clés JWT sécurisées pour Mercure
3. Utiliser des volumes montés ou nommés pour les données persistantes
4. Configurer HTTPS avec Let's Encrypt ou un autre service de certificats
5. Ajuster les paramètres PHP et MySQL pour les performances
