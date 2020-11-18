FROM php:7.4-rc

EXPOSE 4000

# idk why this is necessary but it makes the trash software work
RUN set -eux; \
	apt-get update; \
	apt-get install -y --no-install-recommends \
		libc-client-dev \
		libkrb5-dev \
	; \
	rm -rf /var/lib/apt/lists/*
RUN set -eux; \
	PHP_OPENSSL=yes docker-php-ext-configure imap --with-kerberos --with-imap-ssl; \
	docker-php-ext-install imap

#RUN docker-php-ext-configure imap --with-imap-ssl && \
#    docker-php-ext-enable imap

#RUN docker-php-ext-enable curl

#RUN apt-get update && apt-get install -y \
#    php7.2-mbstring \
#    php7.2-curl \
#    php7.2-imap

CMD ["php", "-S", "0.0.0.0:4000"]
