#!/usr/bin/env bash
set -e
id=$(docker ps -a | grep mysql | awk '{print $1}')
if [ -n "${id}" ] ; then
    set -x
    docker rm "${id}"
    { set +x; } 2>/dev/null
fi
set -x
docker volume rm teentix-site-local_db_data
