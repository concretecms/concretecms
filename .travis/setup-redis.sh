#!/bin/bash
echo "Installing php-redis"
if [[$(pecl install $REDIS_VERSION <<< "") =~ "pecl/redis is already installed"]]; then
echo "extension = redis.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
fi

PORTS="6379 6380"

function start_node() {
	P=$1
	CONFIG_FILE=`tempfile`
	echo "port $P" >> $CONFIG_FILE
	echo "requirepass 'randomredis'" >> $CONFIG_FILE
  echo "Starting Redis server on port: $P"
	redis-server $CONFIG_FILE > /dev/null 2>/dev/null &
	sleep 1
	rm -f $CONFIG_FILE
}



for P in $PORTS; do
		start_node $P
	done

