FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libicu-dev \
    zlib1g-dev \
    default-mysql-client

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml zip intl opcache

# Install APCu
RUN pecl install apcu && docker-php-ext-enable apcu

# Configure opcache
RUN docker-php-ext-enable opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Set working directory
WORKDIR /var/www/symfony

# Set permissions for Symfony
RUN chown -R www-data:www-data /var/www
RUN mkdir -p /var/www/symfony/var/cache /var/www/symfony/var/log
RUN chmod -R 777 /var/www/symfony/var

# Set timezone
ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Expose port 9000 (PHP-FPM)
EXPOSE 9000

# MODIFICATION : Script d'installation automatique intégré
RUN echo '#!/bin/bash\n\
set -e\n\
\n\
# Attendre que les fichiers soient montés\n\
while [ ! -f "/var/www/symfony/composer.json" ]; do\n\
    echo "Attente du montage des fichiers..."\n\
    sleep 2\n\
done\n\
\n\
# Installer Composer si vendor n existe pas\n\
if [ ! -d "/var/www/symfony/vendor" ] || [ ! -f "/var/www/symfony/vendor/autoload.php" ]; then\n\
    echo "Installation des dépendances Composer..."\n\
    cd /var/www/symfony && composer install --no-interaction --optimize-autoloader\n\
else\n\
    echo "Dépendances Composer déjà installées"\n\
fi\n\
\n\
# Attendre MySQL et créer la base\n\
if [ ! -z "$DATABASE_URL" ]; then\n\
    echo "Attente de MySQL..."\n\
    while ! mysql -h database -u root -proot -e "SELECT 1" > /dev/null 2>&1; do\n\
        sleep 3\n\
    done\n\
    echo "MySQL prêt!"\n\
    \n\
    # Créer la base de données\n\
    cd /var/www/symfony\n\
    php bin/console doctrine:database:create --if-not-exists || true\n\
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration || true\n\
fi\n\
\n\
# Permissions\n\
chmod -R 777 /var/www/symfony/var 2>/dev/null || true\n\
chmod -R 777 /var/www/symfony/public 2>/dev/null || true\n\
\n\
# Cache\n\
cd /var/www/symfony && php bin/console cache:clear 2>/dev/null || true\n\
\n\
echo "Application prête!"\n\
\n\
# Lancer PHP-FPM\n\
exec php-fpm' > /usr/local/bin/docker-php-entrypoint-custom && \
    chmod +x /usr/local/bin/docker-php-entrypoint-custom

# Utiliser notre script comme entrypoint
ENTRYPOINT ["/usr/local/bin/docker-php-entrypoint-custom"]