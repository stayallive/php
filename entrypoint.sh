#!/bin/bash

# Exit on error
set -e

# Default to use UTC as timezone
if [[ -z "$TIMEZONE" ]]; then
  $TIMEZONE="UTC"
else
  echo "$TIMEZONE" >/etc/timezone
  dpkg-reconfigure -f noninteractive tzdata
fi

# Configure the PHP timezone
echo "date.timezone=$TIMEZONE" >/usr/local/etc/php/conf.d/timezone.ini

# Configure GitHub token if provided
if [[ ! -z "$COMPOSER_GITHUB" ]]; then
  composer config --global github-oauth.github.com "$COMPOSER_GITHUB"
fi

# Enable Xdebug extension when requested
if [[ ! -z "$WITH_XDEBUG" ]]; then
  docker-php-ext-enable xdebug
fi

exec "$@"
