FROM alpine:3.5

ENV LANG="en_US.UTF-8" \
    LC_ALL="en_US.UTF-8" \
    LANGUAGE="en_US.UTF-8" \
    TERM="xterm"

COPY /conf/run.sh /usr/local/bin/run.sh

# Bundle app source
COPY . .

RUN echo "http://dl-4.alpinelinux.org/alpine/v3.5/main" >> /etc/apk/repositories && \
    apk --update add \
        curl \
        git \
        nginx \
        php7 \
        php7-curl \
        php7-ctype \
        php7-dom \
        php7-fpm \
        php7-gd \
        php7-iconv \
        php7-intl \
        php7-json \
        php7-mbstring \
        php7-mcrypt \
        php7-openssl \
        php7-pdo \
        php7-pdo_pgsql \
        php7-phar \
        php7-session \
        php7-xdebug \
        php7-xml \
        php7-zip \
    && rm -rf /var/cache/apk/* \
    && ln -s /usr/bin/php7 /usr/bin/php \
    && ln -s /usr/sbin/php-fpm7 /usr/bin/php-fpm \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    && mkdir -p /app/cache/prod \
    && mkdir -p /app/logs \
    && ln -s /root/.composer/vendor/bin/phpunit /usr/local/bin/phpunit \
    && chmod a+x /usr/local/bin/run.sh

# Install app dependencies
RUN composer install --no-interaction

RUN chmod 777 /app/cache/prod
RUN chmod 777 /app/logs

COPY /conf/php.ini /etc/php7/conf.d/50-setting.ini
COPY /conf/www.conf /etc/php7/php-fpm.d/www.conf
COPY /conf/nginx.conf /etc/nginx/nginx.conf

EXPOSE 8080

CMD ["/usr/local/bin/run.sh"]
