#!/bin/bash
#
# description: install nginx
#
# author: Lancer He <lancer.he@gmail.com>

DIRNAME=$(cd `dirname $0`; pwd)

function start() {
        /usr/local/bin/php $DIRNAME/application/Server.php
}

function stop() {
        ps -ef | grep "php ${DIRNAME}" | grep -v "grep" | awk '{print $2}' | xargs kill -9
}

case "$1" in
        start)
                start
        ;;
        stop)
                stop
        ;;
        restart)
                stop
                start
        ;;
        *)
                echo "Usage: $0 {start|stop|restart}"
                exit 1
        ;;
esac
