#!/bin/bash
echo "Installing php-redis"
pecl install redis

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

