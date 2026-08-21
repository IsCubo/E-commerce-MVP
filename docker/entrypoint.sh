#!/bin/bash
set -e

export PORT="${PORT:-80}"

# Nginx debe escuchar en el puerto que el hosting inyecte (Render, etc.)
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

# Si el certificado CA de Aiven se pasó como variable de entorno en base64
# (útil cuando el hosting no soporta "secret files" montados en disco),
# lo escribe a la ruta que apunte MYSQL_ATTR_SSL_CA antes de conectar.
if [ -n "$AIVEN_CA_CERT_BASE64" ] && [ -n "$MYSQL_ATTR_SSL_CA" ]; then
    mkdir -p "$(dirname "$MYSQL_ATTR_SSL_CA")"
    echo "$AIVEN_CA_CERT_BASE64" | base64 -d > "$MYSQL_ATTR_SSL_CA"
fi

# Cachea config/rutas/vistas con las env vars reales de este contenedor
# (nunca en build time, porque ahí todavía no existen los secrets/env de producción).
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

# Migraciones opcionales al arrancar (útil para una sola instancia; si vas
# a correr varias réplicas, mejor migrar como paso aparte del deploy y
# dejar RUN_MIGRATIONS sin definir).
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
