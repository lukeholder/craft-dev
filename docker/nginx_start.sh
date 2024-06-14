#!/usr/bin/env sh

set -euo pipefail

me="${0}"
expected_nginx_config_file=/etc/nginx/conf.d/nginx_teentix_site.conf

iterations=0
while [ ! -f "${expected_nginx_config_file}" ] && [ "${iterations}" -lt "5" ] ; do
    echo "[${me}] waiting 1 second for nginx config file: ${expected_nginx_config_file}"
    sleep 1
    iterations=$(( $iterations + 1 ))
done
if [ ! -f "${expected_nginx_config_file}" ] ; then
    echo "[${me}] ERROR expected config file does not exist at: ${expected_nginx_config_file}"
    exit 1
fi

exec nginx -g 'daemon off;'
