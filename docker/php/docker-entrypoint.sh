#!/bin/sh
set -e

# Installer les dépendances de Composer s'il n'existe pas déjà
if [ ! -f /var/www/symfony/vendor/autoload.php ]; then
    echo "🔄 Installation des dépendances Composer..."
    cd /var/www/symfony
    composer install --no-interaction --optimize-autoloader
fi

# Créer la base de données si elle n'existe pas
php /var/www/symfony/bin/console doctrine:database:create --if-not-exists --no-interaction

# Exécuter les migrations
php /var/www/symfony/bin/console doctrine:migrations:migrate --no-interaction

# Nettoyer le cache
php /var/www/symfony/bin/console cache:clear

# Permissions sur le dossier var
chown -R www-data:www-data /var/www/symfony/var

# Premier argument est la commande à exécuter (php-fpm par défaut)
exec "$@"
