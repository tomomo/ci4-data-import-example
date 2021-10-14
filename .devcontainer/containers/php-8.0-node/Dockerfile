FROM node:lts-alpine as node
FROM php:8.0-fpm-alpine

RUN \
  apk add --no-cache --virtual .build-deps autoconf build-base postgresql-dev tzdata && \
  apk add --no-cache \
    shadow \
    bzip2-dev \
    gettext-dev \
    postgresql-libs \
    gmp gmp-dev \
    icu-dev \
    imap-dev \
    imagemagick-dev libpng-dev libjpeg-turbo-dev \
    tidyhtml-dev \
    libxslt-dev \
    libzip-dev \
    aspell-dev \
    bash \
    git git-lfs git-flow \
    msmtp mailx ssmtp \
    unzip \
  && \
  cp /usr/share/zoneinfo/Asia/Tokyo /etc/localtime \
  && \
  pecl install imagick redis && \
  docker-php-ext-enable imagick redis \
  && \
  NPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) && \
  docker-php-ext-install -j${NPROC} \
    bcmath \
    bz2 \
    calendar \
    exif \
    gd \
    tidy \
    gettext \
    gmp \
    intl \
    imap \
    mysqli pdo_mysql \
    pgsql pdo_pgsql \
    sockets \
    pspell \
    opcache \
    sysvmsg \
    sysvsem \
    sysvshm \
    xsl \
    zip \
  && \
  apk del --purge .build-deps

COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN { \
    echo ''; \
    echo 'security.limit_extensions = .php .html'; \
  } >> /usr/local/etc/php-fpm.d/www.conf

COPY --from=node /usr/local/bin/node /usr/local/bin/
COPY --from=node /usr/local/lib/node_modules/ /usr/local/lib/node_modules/
COPY --from=node /opt/yarn* /opt/yarn
RUN \
  ln -s /usr/local/bin/node /usr/local/bin/nodejs && \
  ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm && \
  ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npx && \
  ln -s /opt/yarn/bin/yarn /usr/local/bin/yarn && \
  ln -s /opt/yarn/bin/yarnpkg /usr/local/bin/yarnpkg

RUN usermod -s /bin/ash www-data \
  && \
  { \
    echo 'export PS1="\[\e[0;36m\][\u@\h \W]\\$ \[\e[m\]"'; \
  } >> '/home/www-data/.bashrc'

RUN cat /usr/local/etc/php/php.ini-production > /usr/local/etc/php/php.ini && \
  sed -r -e 's/^memory_limit( )*=[0-9 M]+$/memory_limit = 128M/g' -i /usr/local/etc/php/php.ini && \
  sed -r -e 's/^post_max_size( )*=[0-9 M]+$/post_max_size = 16M/g' -i /usr/local/etc/php/php.ini && \
  sed -r -e 's/^upload_max_filesize( )*=[0-9 M]+$/upload_max_filesize = 16M/g' -i /usr/local/etc/php/php.ini

# ARG UID=33
# RUN usermod -u 100 www-data
# USER www-data
