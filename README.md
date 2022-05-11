# CI PHP Docker images

PHP Docker images for continuous integration and running tests. These images were created for using with Gitlab CI but should work on other platforms too.

These images are built from [official PHP Docker images](https://registry.hub.docker.com/_/php/), and additionally include:

- [Most extensions](#available-extensions) are compiled and ready for loading with `docker-php-ext-enable`
- Git client from official debian repo
- Latest binaries of Composer, PHPUnit and PHP Code Sniffer (`phpcs` and `phpcbf`)
- Node.js v12 from official Node.js debian repositories
- sendmail command via msmtp, configured as relay to localhost. Check `/etc/msmtprc` to setup relay server

_This repository started as a fork of [TetraWeb/docker](https://github.com/TetraWeb/docker), huge thanks to them for the initial work._

## Supported PHP versions

Images are hosted on [Docker Hub](https://hub.docker.com/r/stayallive/php) as `stayallive/php` and tagged by PHP version.

- [`stayallive/php:8.1` (*8.1.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.1.Dockerfile)
- [`stayallive/php:8.0` (*8.0.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/8.0.Dockerfile)
- [`stayallive/php:7.4` (*7.4.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.4.Dockerfile)
- [`stayallive/php:7.3` (*7.3.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.3.Dockerfile)
- [`stayallive/php:7.2` (*7.2.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.2.Dockerfile)
- [`stayallive/php:7.1` (*7.1.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.1.Dockerfile)
- [`stayallive/php:7.0` (*7.0.Dockerfile*)](https://github.com/stayallive/php-docker/blob/master/7.0.Dockerfile)

Keep in mind that although there is a image for the version that doesn't mean it's supported by PHP, see [supported PHP versions](https://www.php.net/supported-versions.php) for more information.

## Available extensions

Some extensions are enabled by default (compiled-in) and some you have to enable when needed.

<details>
<summary>List of compiled-in extensions (enabled by default)</summary>

- ctype
- curl
- date
- dom
- fileinfo
- filter
- ftp
- hash
- iconv
- json
- libxml
- mbstring
- mysqlnd
- openssl
- pcre
- pdo
- pdo_sqlite
- phar
- posix
- readline
- reflection
- session
- simplexml
- sodium
- spl
- sqlite3
- standard
- tokenizer
- xml
- xmlreader
- xmlwriter
- zlib

</details>

Enable the extensions below by calling `docker-php-ext-enable` in for example your `.gitlab-ci.yml` `before_script` section:

```yaml
before_script:
  # We enable bcmath, gd and intl here for example but you
  # can enable any extension or PECL extension listed below
  - docker-php-ext-enable bcmath gd intl
```

<details>
<summary>Available extensions</summary>

- bcmath
- bz2
- calendar
- dba
- exif
- ffi
- ftp
- gd
- gettext
- gmp
- imap
- intl
- ldap
- mysqli
- opcache
- pcntl
- pdo
- pdo_dblib
- pdo_mysql
- pdo_pgsql
- pgsql
- pspell
- shmop
- snmp
- soap
- sockets
- sysvmsg
- sysvsem
- sysvshm
- tidy
- xsl
- zip

</details>

<details>
<summary>Available PECL extensions</summary>

- igbinary 
- imagick
- mongodb
- redis
- xdebug

</details>

## Environment variables

### `WITH_XDEBUG=1`

Enables xdebug extension (disabled by default)

### `TIMEZONE=Europe/Amsterdam`

Set system and `php.ini` timezone. You can also set timezone in .gitlab-ci.yml - check [Example](https://github.com/TetraWeb/docker/blob/master/examples/purephp/.gitlab-ci.yml)

### `COMPOSER_GITHUB=<YOUR_GITHUB_TOKEN>`

Adds GitHub OAuth token for Composer which allows composer to get unlimited repositories from GitHub without blocking non-interactive mode with request for authorization.

You can obtain your token at [https://github.com/settings/tokens](https://github.com/settings/tokens) (no scopes are required).

See: [Composer documentation about GitHub API rate limits](https://getcomposer.org/doc/articles/authentication-for-private-packages.md#github-oauth)

## Custom `php.ini` values

Easiest way is to add your php.ini directives to `/usr/local/etc/php/conf.d/[anyname].ini`.

Another way is to mount your local php.ini on container start like `docker run ... -v /home/user/php.ini:/usr/local/php/etc/php.ini ...`.
