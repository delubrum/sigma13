# --- STAGE 1: Base & Extensions ---
# Motor PHP 8.5 real para el entorno de 2026
FROM dunglas/frankenphp:php8.5

# Directorio de trabajo estándar de FrankenPHP (evita conflictos de rutas en LMDE)
WORKDIR /var/www/html

# Extensiones industriales: Postgres para la DB y Redis/Valkey para cache/sessions
RUN install-php-extensions \
    pcntl \
    pgsql \
    pdo_pgsql \
    intl \
    bcmath \
    zip \
    gd \
    igbinary \
    redis \
    opcache

# Composer 2.8+ integrado
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# --- STAGE 2: Dependencies ---
# Cacheamos dependencias: si el composer.json no cambia, no se reinstala nada
COPY composer.json composer.lock ./

# Instalación "headless": sin scripts y sin autoloader (se genera en el Stage 3)
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# --- STAGE 3: Application Code ---
# Copiamos todo el proyecto (asegúrate de tener un .dockerignore para no subir el vendor local)
COPY . .

# --- STAGE 4: Optimization & Namespace Fix ---
# 1. Generamos el Classmap (esto soluciona el "Unable to detect application namespace")
# 2. Limpiamos basura de caché del host de Medellín
# 3. Cacheamos configuración usando drivers nulos para que no falle el build si no hay DB
RUN composer dump-autoload --optimize --no-dev && \
    php artisan octane:install --server=frankenphp --force && \
    rm -rf bootstrap/cache/*.php && \
    php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# --- STAGE 5: Permissions & Ownership ---
# Alineamos los permisos con www-data (el usuario de FrankenPHP)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


# --- STAGE 6: Runtime Configuration ---
# Soporte HTTP/3 (UDP 443) y puertos estándar
EXPOSE 80 443 443/udp

# Configuración de Octane/FrankenPHP para modo Worker (Arquitectura ADR de alto rendimiento)
ENTRYPOINT []
CMD ["php", "-d", "variables_order=EGPCS", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=80", "--workers=1", "--max-requests=1"]
