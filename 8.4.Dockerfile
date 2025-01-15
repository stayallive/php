FROM php:8.4

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# https://getcomposer.org/doc/03-cli.md#composer-no-interaction
ENV COMPOSER_NO_INTERACTION=1

# Install additional packages
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
        locales \
    " \
    && echo "force-unsafe-io" > /etc/dpkg/dpkg.cfg.d/02apt-speedup \
    && echo "Acquire::http {No-Cache=True;};" > /etc/apt/apt.conf.d/no-cache \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends $additionalPackages \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PHP extensions
RUN extensions="amqp apcu ast bcmath bitset brotli bz2 calendar csv dba decimal ds enchant ev event excimer exif ffi ftp gd gearman geos geospatial gettext gmp gnupg grpc http igbinary imagick imap inotify intl json_post jsonpath ldap luasandbox lz4 lzf mailparse maxminddb md4c memcache memcached memprof mongodb msgpack mysqli oauth odbc opcache opentelemetry parle pcntl pcov pdo_dblib pdo_firebird pdo_mysql pdo_odbc pdo_pgsql pdo_sqlsrv pgsql php_trie phpy pkcs11 pq protobuf pspell psr raphf rdkafka redis saxon seasclick shmop simdjson smbclient snappy snmp snuffleupagus soap sockets solr spx sqlsrv ssh2 stomp sync sysvmsg sysvsem sysvshm tideways tidy timezonedb uploadprogress uuid uv vips wikidiff2 xdebug xhprof xlswriter xmldiff xmlrpc xpass xsl yac yaml yar zephir_parser zip zmq zookeeper zstd" \
    && curl -sSLf -o /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions $extensions \
    && for ext in $extensions; do rm -f /usr/local/etc/php/conf.d/*php-ext-$ext.ini; done

# Configure locales (make en_US and nl_NL available)
RUN sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen && \
    sed -i -e 's/# nl_NL.UTF-8 UTF-8/nl_NL.UTF-8 UTF-8/' /etc/locale.gen && \
    dpkg-reconfigure --frontend=noninteractive locales

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
RUN curl -fsSL https://raw.githubusercontent.com/tj/n/master/bin/n | bash -s lts \
    && npm install -g n \
    && npm install -g yarn

COPY msmtprc /etc/
COPY entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "-a"]
