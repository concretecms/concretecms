#!/bin/bash
echo "Installing php-redis"
pecl install $REDIS_VERSION <<< ""
echo "extension = redis.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini

PORTS="6379 6380"

function start_node() {
	P=$1
	CONFIG_FILE=`tempfile`
	printf "port $P\nrequirepass 'randomredis'" > $CONFIG_FILE << CONFIG
CONFIG
  echo "Starting Redis server on port: $P"
	redis-server $CONFIG_FILE > /dev/null 2>/dev/null &
	sleep 1
	rm -f $CONFIG_FILE
}



for P in $PORTS; do
		start_node $P
	done

