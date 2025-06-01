# ZenTicket - Système de Gestion de Tickets

Application de gestion de tickets avec chat en temps réel, développée avec Symfony 6.4.

# Guide d'installation Docker pour ZenTicket

**Remarque :**
L'installation est entièrement automatisée. Veuillez patienter au moins 5 minutes lors du premier lancement.
Notez également que la communication avec l'IA dans l’environnement Docker est limitée à 3 requêtes maximum. Au-delà, vous devrez redémarrer docker-compose pour réinitialiser le compteur. Dans certains cas, un simple rechargement de la page peut suffire à rétablir le fonctionnement.

## Prérequis

- **Docker Desktop** installé et fonctionnel
  - [Windows/Mac](https://www.docker.com/products/docker-desktop)
  - Linux : Docker Engine + Docker Compose
- Git (pour cloner le dépôt) ou (téléchargement du zip)

## Installation avec Docker

### 1. Récupération du projet

```bash
# Cloner le projet
git clone https://github.com/Ratebah20/ZenTicket.git
cd ZenTicket
```

### 2. Configuration

```bash
# Copier les fichiers d'environnement
ajouter le fichier .env.local dans le dossier source 

# Modifier les variables d'environnement selon vos besoins dans .env.local
# Particulièrement DATABASE_URL et MERCURE_PUBLIC_URL
```

### 2.1 Installation des dépendances

```bash
# Installer les dépendances
docker-compose build
```

### 3. Démarrage des conteneurs Docker

```bash
# Lancer l'environnement Docker
docker-compose up -d
```

**L'installation complète se fait automatiquement :**
- ✅ Serveur web Nginx
- ✅ PHP-FPM avec toutes les extensions requises
- ✅ MySQL avec initialisation de la base de données
- ✅ Mercure pour les communications en temps réel
- ✅ PHPMyAdmin pour la gestion de base de données

**important :** Temps d'installation estimé** : 3-5 minutes (première installation)

## Accès à l'application

- **Application Web** : http://localhost:8080
- **PHPMyAdmin** : http://localhost:8081 ( login : root / password : root)
- **Hub Mercure** : http://localhost:3001/.well-known/mercure

## Commandes Docker utiles

```bash
# Démarrer les conteneurs
docker-compose up -d

# Arrêter les conteneurs
docker-compose down

# Voir les logs
docker-compose logs -f

# Exécuter une commande dans le conteneur PHP
docker-compose exec php bash

# Exécuter une commande Symfony
docker-compose exec php bin/console [commande]
```

## Configuration avancée

### Personnalisation des ports

Si vous avez besoin de modifier les ports, éditez le fichier `docker-compose.yml` :

```yaml
# Pour changer le port de l'application web (8080 par défaut)
nginx:
  ports:
    - "8080:80"

# Pour changer le port de Mercure (3001 par défaut)
mercure:
  ports:
    - "3001:80"
```

### Persistance des données

Les données sont stockées dans des volumes Docker :
- `db_data` : Données MySQL
- `mercure_data` : Données Mercure


Une fois les conteneurs démarrés, accédez à :

| Service | URL | Identifiants |
|---------|-----|--------------|
| **Application** | http://localhost:8080 | ADMIN : admin@3innov.fr / admin123 | TECHNICIEN : tech.bdd@3innov.fr / tech123 | USER : crée votre compte utilisateur
| **phpMyAdmin** | http://localhost:8081 | root / root |
| **Emails** | http://localhost:8025 | - |
| **Mercure** | http://localhost:3001/.well-known/mercure | - | (résultat page avec "unauthorised")

## Commandes utiles

### Gestion de Docker

```bash
# Voir l'état des conteneurs
docker-compose ps

# Voir les logs
docker-compose logs -f

# Arrêter l'application
docker-compose down

# Redémarrer l'application
docker-compose restart
```

### Accès aux conteneurs (si nécessaire)

```bash
# Console PHP
docker exec -it zenticket_php bash

# Base de données MySQL
docker exec -it zenticket_mysql mysql -u root -proot
```

## Dépannage

### L'application ne se lance pas ?

1. **Vérifiez que Docker est lancé**
   ```bash
   docker --version
   ```

2. **Vérifiez les logs**
   ```bash
   docker-compose logs
   ```

3. **Relancez l'installation**
   ```bash
   docker-compose down -v
   docker-compose up -d
   ```

### Page blanche ou erreur 500 ?

Les permissions se corrigent automatiquement, mais si besoin :
```bash
docker exec zenticket_php chmod -R 777 var/ public/
```

### Port déjà utilisé ?

Modifiez les ports dans `docker-compose.yml` :
```yaml
services:
  nginx:
    ports:
      - "8090:80"  # Changez 8080 en 8090
```

### Page 502 Bad Gateway

**Si vous avez une page 502 Bad Gateway, veuillez attendre quelques minutes en plus**


### problème avec chat en temps réel

**Si vous avez un problème avec le chat en temps réel, vous devez recharger votre page web**



## Architecture

- **PHP 8.2** avec Symfony 6.4
- **MySQL 8.0** pour la base de données
- **Nginx** comme serveur web
- **Mercure** pour le temps réel
- **Node.js** pour la compilation des assets

## Support

En cas de problème :
1. Vérifiez les logs : `docker-compose logs`
2. Redémarrez : `docker-compose restart`
3. Réinstallez : `docker-compose down -v && docker-compose up -d`

---

