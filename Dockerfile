FROM php:7.4-fpm-alpine

EXPOSE 4000
WORKDIR /app

# idk why this is necessary but it makes the trash software work
RUN set -xe && \
    apk add --update \
        imap-dev \
        openssl-dev && \
    apk add --no-cache --virtual .php-deps \
        make && \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        krb5-dev && \
    (docker-php-ext-configure imap --with-kerberos --with-imap-ssl) && \
    (docker-php-ext-install imap > /dev/null) && \
    apk del .build-deps

RUN set -xe && \
    apk add --update && \
    apk add libzip-dev && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

CMD ["php", "-S", "0.0.0.0:4000"]
