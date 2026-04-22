FROM trafex/php-nginx:latest

USER root

RUN apk add --no-cache \
    php85-pdo \
    php85-pdo_mysql \
    php85-tokenizer \
    php85-dom \
    php85-xml \
    php85-xmlwriter \
    php85-fileinfo \
    php85-ctype \
    php85-iconv \
    php85-mbstring \
    php85-session \
    php85-openssl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar configuración de NGINX
COPY nginx.conf /etc/nginx/conf.d/default.conf

USER nobody

COPY --chown=nobody:nobody . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

USER root
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]