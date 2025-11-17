FROM dunglas/frankenphp:1.9-php8.4-alpine

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN install-php-extensions \
    ctype \
    curl \
    dom \
    fileinfo \
    filter \
    hash \
    mbstring \
    openssl \
    pcre \
    pdo \
    session \
    tokenizer \
    xml \
    pdo_mysql \
    intl \
    opcache \
    @composer

ENV USER_ID=1000
ENV USER=application

RUN adduser -u ${USER_ID} -D -h /home/${USER} ${USER} && \
    chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy

RUN mkdir -p /app && chown -R ${USER}:${USER} /app && chmod -R 775 /app
WORKDIR /app
USER 1000
