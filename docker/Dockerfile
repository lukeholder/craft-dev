ARG CRAFTCMS_VERSION=8.2

FROM composer:2 as vendor
RUN apk add \
    jpeg-dev \
    libpng-dev \
    zlib-dev \
    ;
RUN docker-php-ext-install \
    bcmath \
    gd \
    ;
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install --ignore-platform-reqs --no-interaction --prefer-dist  --no-suggest

FROM node:20 AS scripts
WORKDIR /app
COPY package*.json .
COPY assets assets
COPY html html
COPY gulpfile.js .
RUN npm ci && \
    npm run build

FROM craftcms/nginx:${CRAFTCMS_VERSION}

USER root

RUN export NR_AGENT_VERSION=$(curl -sS 'https://download.newrelic.com/php_agent/release/' | grep 'linux-musl.tar.gz' | sed 's/^.*php5-//' | sed 's/-linux-musl.tar.gz.*$/-linux-musl/') && \
    echo "newrelic agent version: ${NR_AGENT_VERSION}" && \
    curl -sSL https://download.newrelic.com/php_agent/release/newrelic-php5-${NR_AGENT_VERSION}.tar.gz | tar -C /tmp -zx && \
    export NR_INSTALL_USE_CP_NOT_LN=1 && \
    export NR_INSTALL_SILENT=1 && \
    /tmp/newrelic-php5-${NR_AGENT_VERSION}/newrelic-install install && \
    rm -rf /tmp/newrelic-php5-${NR_AGENT_VERSION} /tmp/nrinstall*

RUN apk add --no-cache --virtual .gettext gettext \
    && apk upgrade musl \
    && mv /usr/bin/envsubst /tmp/ \
    \
    && runDeps="$( \
        scanelf --needed --nobanner /tmp/envsubst \
            | awk '{ gsub(/,/, "\nso:", $2); print "so:" $2 }' \
            | sort -u \
            | xargs -r apk info --installed \
            | sort -u \
    )" \
    && apk add --no-cache $runDeps \
    && apk del .gettext \
    && mv /tmp/envsubst /usr/local/bin/

RUN apk add --no-cache \
    mysql-client \
    su-exec

COPY nginx/nginx.conf /etc/nginx/nginx.conf
RUN rm -v /etc/nginx/conf.d/*.conf
COPY nginx/nginx_teentix_site.conf.template /etc/nginx/template/nginx_teentix_site.conf.template
RUN rm -v -rf /etc/nginx/craftcms/
RUN mkdir -pv /etc/nginx/auth/
COPY nginx/auth /etc/nginx/auth

COPY docker/supervisor.conf /etc/supervisor/conf.d/supervisor.conf
COPY docker/nginx_config.sh /nginx_config.sh
COPY docker/entrypoint.sh /entrypoint.sh
COPY docker/queue_runner.sh /queue_runner.sh
COPY docker/craftcms_setup.sh /craftcms_setup.sh
COPY docker/newrelic.ini /usr/local/etc/php/conf.d/newrelic.ini
COPY docker/xdebug_local.ini /usr/local/etc/php/conf.d/xdebug_local.ini
COPY docker/connect-to-db.sh /usr/local/bin/
COPY php/conf.d/* /usr/local/etc/php/conf.d/
COPY scripts /app/scripts

USER www-data

COPY --chown=www-data:www-data --from=vendor /app/vendor/ /app/vendor/
COPY --chown=www-data:www-data bootstrap.php bootstrap.php
COPY --chown=www-data:www-data docker/BaltimoreCyberTrustRoot.crt.pem ./
COPY --chown=www-data:www-data config config
COPY --chown=www-data:www-data craft craft
COPY --chown=www-data:www-data html html
COPY --chown=www-data:www-data --from=scripts /app/html/assets/scripts/scripts.js* /app/html/assets/scripts/
COPY --chown=www-data:www-data modules modules
COPY --chown=www-data:www-data templates templates

RUN rm -rf \
    html/cpresources \
    html/image \
    .env \
    storage

USER root

RUN mkdir -pv storage && \
    chown www-data:www-data storage && \
    adduser www-data tty

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
