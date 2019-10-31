#!/bin/bash
pecl uninstall redis
echo "Installing php-redis"
INSTALL_REDIS=$(pecl install $REDIS_VERSION <<< '')
if [[ "$INSTALL_REDIS" =~ "redis is already installed" ]]; then
  echo "extension = redis.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
fi

PORTS="6379 6380"

function start_node() {
	P=$1
  echo "Starting Redis server on port: $P"
	redis-server --daemonize yes --port $P --requirepass "randomredis"
	sleep 1
}



for P in $PORTS; do
		start_node $P
	done

