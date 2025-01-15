# CI PHP Docker images

PHP Docker images for continuous integration and running tests. These images were created for using with Gitlab CI but should work on other platforms too.

These images are built with the [official PHP Docker images](https://registry.hub.docker.com/_/php/) as their base.

_This repository started as a fork of [TetraWeb/docker](https://github.com/TetraWeb/docker), huge thanks to them for the initial work._

## Features

- [Most extensions](#available-extensions) are compiled and ready for loading with `docker-php-ext-enable`
- Git client from official debian repo
- Latest binaries of Composer, PHPUnit and PHP Code Sniffer (`phpcs` and `phpcbf`)
- Node.js v12, v14 or v20 by default using the [`n`](https://github.com/tj/n) Node version manager, with `npm`, `yarn` and `n` pre-installed
- sendmail command via msmtp, configured as relay to localhost. Check `/etc/msmtprc` to setup relay server

## Image Registries

Images are available on the following registries:

- [GitHub Container Registry](https://github.com/stayallive/php/pkgs/container/php) (pull using `ghcr.io/stayallive/php:8.4`)
- [GitLab Container Registry](https://gitlab.com/stayallive/php/container_registry/3036570) (pull using `registry.gitlab.com/stayallive/php:8.4`)
- [Docker Hub](https://hub.docker.com/r/stayallive/php) (pull using `stayallive/php:8.4`)

They are listed as `stayallive/php` and tagged by PHP version for both `linux/amd64` & `linux/arm64`.

[![Built with Depot](https://depot.dev/badges/built-with-depot.svg)](https://depot.dev/?utm_source=stayallive)

## PHP versions

- [`stayallive/php:8.4` (*8.4.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.4.Dockerfile) (Node LTS)
- [`stayallive/php:8.3` (*8.3.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.3.Dockerfile) (Node 20 & LTS)
- [`stayallive/php:8.2` (*8.2.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.2.Dockerfile) (Node 14 & LTS)
- [`stayallive/php:8.1` (*8.1.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.1.Dockerfile) (Node 14 & LTS)
- [`stayallive/php:8.0` (*8.0.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.0.Dockerfile) (Node 14 & LTS)
- [`stayallive/php:7.4` (*7.4.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.4.Dockerfile) (Node 14, no longer being built)
- [`stayallive/php:7.3` (*7.3.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.3.Dockerfile) (Node 12, no longer being built)
- [`stayallive/php:7.2` (*7.2.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.2.Dockerfile) (Node 12, no longer being built)
- [`stayallive/php:7.1` (*7.1.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.1.Dockerfile) (Node 12, no longer being built)
- [`stayallive/php:7.0` (*7.0.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.0.Dockerfile) (Node 12, no longer being built)

_Keep in mind that although there might be a tag available it doesn't mean the PHP version is still supported, see [supported PHP versions](https://www.php.net/supported-versions.php) for more information._

## Node versions

Starting with the PHP 8 images the [`n`](https://github.com/tj/n) Node version manager is installed.

Pre-cached is Node `14`/`20` and `LTS` with `14`/`20` set as the default (see PHP versions list which Node version is the default). To use the `LTS` version run `n lts` in for example your `.gitlab-ci.yml` `before_script` section:

```yaml
before_script:
  - n lts
```

Note: Starting with PHP 8.4 only the LTS version of Node is pre-cached.

You can use `n` to install any other Node version you need, but they will be downloaded each run and will not be pre-cached inside the image.

## Available extensions

Some extensions are enabled by default (compiled-in) and some you have to enable when needed.

You can enable extensions by adding the environment variable `WITH_EXTENSIONS` with a space separated list of extensions you want to enable.

```bash
WITH_EXTENSIONS=imagick redis
```

Alternatively you can enable the extensions by calling `docker-php-ext-enable` in for example your `.gitlab-ci.yml` `before_script` section:

```yaml
before_script:
  # We enable bcmath, gd and intl here for example but you
  # can enable any extension or PECL extension listed below
  - docker-php-ext-enable bcmath gd intl
```

<details>
<summary>Available extensions</summary>

- amqp
- apcu
- ast
- bcmath
- bitset
- brotli
- bz2
- calendar
- cassandra
- csv
- dba
- ddtrace
- decimal
- ds
- enchant
- ev
- event
- excimer
- exif
- ffi
- ftp
- gd
- gearman
- geos
- geospatial
- gettext
- gmagick
- gmp
- gnupg
- grpc
- http
- igbinary
- imagick
- imap
- inotify
- intl
- ion
- json_post
- jsonpath
- ldap
- luasandbox
- lz4
- lzf
- mailparse
- maxminddb
- md4c
- memcache
- memcached
- memprof
- mongodb
- msgpack
- mysqli
- oauth
- oci8
- odbc
- opcache
- opentelemetry
- parallel
- parle
- pcntl
- pcov
- pdo_dblib
- pdo_firebird
- pdo_mysql
- pdo_oci
- pdo_odbc
- pdo_pgsql
- pdo_sqlsrv
- pgsql
- php_trie
- phpy
- pkcs11
- pq
- protobuf
- pspell
- psr
- raphf
- rdkafka
- redis
- relay
- saxon
- seasclick
- shmop
- simdjson
- smbclient
- snappy
- snmp
- snuffleupagus
- soap
- sockets
- solr
- spx
- sqlsrv
- ssh2
- stomp
- swoole
- sync
- sysvmsg
- sysvsem
- sysvshm
- tideways
- tidy
- timezonedb
- uploadprogress
- uuid
- uv
- vips
- wikidiff2
- xdebug
- xdiff
- xhprof
- xlswriter
- xmldiff
- xmlrpc
- xpass
- xsl
- yac
- yaml
- yar
- zephir_parser
- zip
- zmq
- zookeeper
- zstd

</details>

## Environment variables

### `WITH_XDEBUG=1`

Enables xdebug extension (disabled by default)

### `WITH_EXTENSIONS=imagick redis`

Enable extensions, separated by space

### `TIMEZONE=Europe/Amsterdam`

Set system and `php.ini` timezone. You can also set timezone in .gitlab-ci.yml - check [Example](https://github.com/TetraWeb/docker/blob/master/examples/purephp/.gitlab-ci.yml)

### `COMPOSER_GITHUB=<YOUR_GITHUB_TOKEN>`

Adds GitHub OAuth token for Composer which allows composer to get unlimited repositories from GitHub without blocking non-interactive mode with request for authorization.

You can obtain your token at [https://github.com/settings/tokens](https://github.com/settings/tokens) (no scopes are required).

See: [Composer documentation about GitHub API rate limits](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth)

## Custom `php.ini` values

Easiest way is to add your php.ini directives to `/usr/local/etc/php/conf.d/[anyname].ini`.

Another way is to mount your local php.ini on container start like `docker run ... -v /home/user/php.ini:/usr/local/php/etc/php.ini ...`.
