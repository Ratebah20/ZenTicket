# Variables
DOCKER_COMPOSE = docker-compose
DOCKER = docker
PHP_CONTAINER = zenticket_php
NODE_CONTAINER = zenticket_node

# Couleurs
GREEN = \033[0;32m
YELLOW = \033[0;33m
RED = \033[0;31m
NC = \033[0m

## —— 🎵 Makefile pour ZenTicket 🎵 ———————————————————————————————————————————
help: ## Affiche l'aide
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Construire les conteneurs
	@echo "$(GREEN)Construction des conteneurs...$(NC)"
	$(DOCKER_COMPOSE) build

up: ## Démarrer les conteneurs
	@echo "$(GREEN)Démarrage des conteneurs...$(NC)"
	$(DOCKER_COMPOSE) up -d

down: ## Arrêter les conteneurs
	@echo "$(YELLOW)Arrêt des conteneurs...$(NC)"
	$(DOCKER_COMPOSE) down

restart: down up ## Redémarrer les conteneurs

logs: ## Voir les logs
	$(DOCKER_COMPOSE) logs -f

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
install: ## Installer le projet
	@echo "$(GREEN)Installation du projet...$(NC)"
	$(MAKE) build
	$(MAKE) up
	$(MAKE) composer-install
	$(MAKE) npm-install
	$(MAKE) database-create
	$(MAKE) migrations
	$(MAKE) cache-clear
	$(MAKE) npm-build
	@echo "$(GREEN)✅ Installation terminée !$(NC)"
	@echo "$(GREEN)🌐 Application : http://localhost:8080$(NC)"
	@echo "$(GREEN)📊 phpMyAdmin : http://localhost:8081$(NC)"

composer-install: ## Installer les dépendances PHP
	@echo "$(GREEN)Installation des dépendances Composer...$(NC)"
	$(DOCKER) exec $(PHP_CONTAINER) composer install

npm-install: ## Installer les dépendances NPM
	@echo "$(GREEN)Installation des dépendances NPM...$(NC)"
	$(DOCKER) exec $(NODE_CONTAINER) npm install

npm-dev: ## Compiler les assets en mode dev
	@echo "$(GREEN)Compilation des assets (dev)...$(NC)"
	$(DOCKER) exec $(NODE_CONTAINER) npm run dev

npm-build: ## Compiler les assets en mode production
	@echo "$(GREEN)Compilation des assets (production)...$(NC)"
	$(DOCKER) exec $(NODE_CONTAINER) npm run build

npm-watch: ## Compiler les assets en mode watch
	$(DOCKER) exec $(NODE_CONTAINER) npm run watch

## —— Base de données 🗄️ ———————————————————————————————————————————————————————
database-create: ## Créer la base de données
	@echo "$(GREEN)Création de la base de données...$(NC)"
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:database:create --if-not-exists

database-drop: ## Supprimer la base de données
	@echo "$(RED)Suppression de la base de données...$(NC)"
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:database:drop --if-exists --force

migrations: ## Exécuter les migrations
	@echo "$(GREEN)Exécution des migrations...$(NC)"
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

migration-diff: ## Créer une nouvelle migration
	@echo "$(GREEN)Création d'une nouvelle migration...$(NC)"
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console make:migration

reset-db: database-drop database-create migrations ## Réinitialiser la base de données

## —— Outils 🛠️ ————————————————————————————————————————————————————————————————
cache-clear: ## Vider le cache
	@echo "$(GREEN)Vidage du cache...$(NC)"
	$(DOCKER) exec $(PHP_CONTAINER) php bin/console cache:clear

console: ## Accéder à la console Symfony
	$(DOCKER) exec -it $(PHP_CONTAINER) bash

mysql: ## Accéder à MySQL
	$(DOCKER) exec -it zenticket_mysql mysql -u root -proot

.PHONY: help build up down restart logs install composer-install npm-install npm-dev npm-build npm-watch database-create database-drop migrations migration-diff reset-db cache-clear console mysql
