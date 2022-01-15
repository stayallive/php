FROM php:7.4

# Workaround for PhantomJS
ENV QT_QPA_PLATFORM offscreen

ENV XDEBUG_VERSION 2.9.8

ARG NODE_VERSION=14
ARG INSTALL_PHANTOM=0

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# https://getcomposer.org/doc/03-cli.md#composer-no-interaction
ENV COMPOSER_NO_INTERACTION=1

RUN additionalPackages=" \
        apt-transport-https \
        git \
        wget \
        gnupg \
        msmtp-mta \
        subversion \
        mariadb-client \
        openssh-client \
        rsync \
        unzip \
    " \
    buildDeps=" \
        freetds-dev \
        freetds-bin \
        libbz2-dev \
        libc-client-dev \
        libffi-dev \
        libfreetype6-dev \
        libgmp3-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libkrb5-dev \
        libldap2-dev \
        libmcrypt-dev \
        libpng-dev \
        libpq-dev \
        libpspell-dev \
        libonig-dev \
        librabbitmq-dev \
        libsasl2-dev \
        libsnmp-dev \
        libssl-dev \
        libtidy-dev \
        libxml2-dev \
        libxpm-dev \
        libxslt1-dev \
        zlib1g-dev \
        libzip-dev \
    " \
    && runDeps=" \
        libc-client2007e \
        libfreetype6 \
        libicu-dev \
        libjpeg62-turbo \
        libpq5 \
        libsybdb5 \
        libtidy-dev \
        libx11-dev \
        libxpm4 \
        libxslt1.1 \
        libzip4 \
        snmp \
    " \
    && phpModules=" \
        bcmath \
        bz2 \
        calendar \
        dba \
        exif \
        ffi \
        ftp \
        gd \
        gettext \
        gmp \
        imap \
        intl \
        ldap \
        mysqli \
        opcache \
        pcntl \
        pdo \
        pdo_mysql \
        pspell \
        shmop \
        snmp \
        soap \
        sockets \
        sysvmsg \
        sysvsem \
        sysvshm \
        tidy \
        xmlrpc \
        xsl \
        zip \
        xdebug \
    " \
    && echo "force-unsafe-io" > /etc/dpkg/dpkg.cfg.d/02apt-speedup \
    && echo "Acquire::http {No-Cache=True;};" > /etc/apt/apt.conf.d/no-cache \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends $additionalPackages $buildDeps $runDeps \
    && docker-php-source extract \
    && cd /usr/src/php/ext/ \
    && curl -L http://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz | tar -zxf - \
    && mv xdebug-$XDEBUG_VERSION xdebug \
    && ls -alh /usr/lib/*linux* \
    && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap_r.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap_r.a /usr/lib/libldap_r.a \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure imap --with-imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-configure ldap --with-ldap-sasl \
    && docker-php-ext-install $phpModules \
    && docker-php-ext-enable pdo_mysql bcmath calendar gd intl pcntl soap sockets xmlrpc zip xsl mongodb \
    && pecl install igbinary \
    && pecl install mongodb \
    && pecl install redis \
    && for ext in $phpModules; do \
           rm -f /usr/local/etc/php/conf.d/docker-php-ext-$ext.ini; \
       done \
    && docker-php-source delete \
    && apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false -o APT::AutoRemove::SuggestsImportant=false $buildDeps \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install composer and put binary into $PATH
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

# Install phpunit and put binary into $PATH
RUN curl -sSLo phpunit.phar https://phar.phpunit.de/phpunit.phar \
    && chmod 755 phpunit.phar \
    && mv phpunit.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/phpunit.phar /usr/local/bin/phpunit

# Install PHP Code sniffer
RUN curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar \
    && chmod 755 phpcs.phar \
    && mv phpcs.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/phpcs.phar /usr/local/bin/phpcs \
    && curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar \
    && chmod 755 phpcbf.phar \
    && mv phpcbf.phar /usr/local/bin/ \
    && ln -s /usr/local/bin/phpcbf.phar /usr/local/bin/phpcbf

# Install Node.js & Yarn
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && curl -sL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs build-essential yarn \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN if [[ "${INSTALL_PHANTOM}" -gt 0 ]]; then \
    apt update && apt install python2 libqt5core5a libqt5gui5 libqt5network5 libqt5printsupport5 libqt5webkit5 libqt5widgets5 xvfb libxcb-util1 -y \
              wget http://ports.ubuntu.com/pool/universe/p/phantomjs/phantomjs_2.1.1+dfsg-2_arm64.deb -O phantom-2.1.1.deb \
              dpkg -i phantom-2.1.1.deb \
    fi

COPY msmtprc /etc/
COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "-a"]

