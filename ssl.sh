#!/bin/bash

# Запрос имени домена
read -p "Введите имя нового домена (например, example.com): " DOMAIN
read -p "Введите ваш Email для Let's Encrypt: " EMAIL

# Путь к файлам конфигурации
NGINX_SSL_CONF="nginx-ssl.conf"
DOCKER_COMPOSE_FILE="docker-compose.yml"

# Проверка существования файла конфигурации
if [ ! -f "$NGINX_SSL_CONF" ]; then
    echo "Файл $NGINX_SSL_CONF не найден."
    exit 1
fi

# Добавление конфигурации сервера в nginx-ssl.conf
cat <<EOF >>"$NGINX_SSL_CONF"

server {
    listen 443 ssl;
    server_name $DOMAIN;

    ssl_certificate /etc/letsencrypt/live/$DOMAIN/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/$DOMAIN/privkey.pem;

    location / {
        proxy_pass http://yourls:80;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
    }
}
EOF

# Выпуск SSL-сертификата для нового домена
docker-compose -f "$DOCKER_COMPOSE_FILE" exec nginx certbot certonly --webroot -w /var/www/certbot -d $DOMAIN --email $EMAIL --agree-tos --no-eff-email --keep-until-expiring --quiet

# Перезапуск Nginx для применения изменений
docker-compose -f "$DOCKER_COMPOSE_FILE" restart nginx

echo "Конфигурация для $DOMAIN добавлена и Nginx перезапущен."
