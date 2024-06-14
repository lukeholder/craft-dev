#!/usr/bin/env sh

set -eo pipefail

me="${0}"

if [ "${ENVIRONMENT}" = "production" ] ; then
    echo "[${me}] production environment, removing basic auth requirement"
    rm -v /etc/nginx/auth/*
fi
if [ "${TEENTIX_LOCAL_DEV}" = "true" ] ; then
    echo "[${me}] localdev environment, removing basic auth requirement"
    rm -v /etc/nginx/auth/* ||:
fi

nginx_template_file=/etc/nginx/template/nginx_teentix_site.conf.template
nginx_config_file=/etc/nginx/conf.d/nginx_teentix_site.conf
env_file=/app/.env
if [ -f "${env_file}" ] ; then
    echo "[${me}] Using .env file found at: ${env_file}"
    source "${env_file}"
fi

echo "[${me}] AZURE_BLOB_IMAGES_URL='${AZURE_BLOB_IMAGES_URL:?}'"
envsubst '${AZURE_BLOB_IMAGES_URL}' < "${nginx_template_file}" > "${nginx_config_file}"
echo "[${me}] Wrote nginx config to: ${nginx_config_file}"
