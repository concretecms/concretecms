#!/bin/bash
echo "Installing php-redis"
PECL = "pecl$TRAVIS_PHP_VERSION-sp"
echo  "$PECL for $TRAVIS_PHP_VERSION";
if [\("$TRAVIS_PHP_VERSION" = "5.5"\) -o \("$TRAVIS_PHP_VERSION" = "5.6"\) ]: then
  $PECL install redis-2.2.8 <<< ""
else
  pecl install redis <<< ""
fi

PORTS="6379 6380"

function start_node() {
	P=$1
	CONFIG_FILE=`tempfile`
	cat > $CONFIG_FILE << CONFIG
port $P
requirepass 'randomredis'
CONFIG
  echo "Starting Redis server on port: $P"
	redis-server $CONFIG_FILE > /dev/null 2>/dev/null &
	sleep 1
	rm -f $CONFIG_FILE
}



for P in $PORTS; do
		start_node $P
	done

