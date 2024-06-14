#!/usr/bin/env sh

set -eo pipefail

me="${0}"

if [ "${1}" = "queue-runner" ]; then
    echo "[${me}] Starting queue_runner.sh"
    exec /queue_runner.sh
fi

echo "[${me}] Running /nginx_config.sh"
/nginx_config.sh

echo "[${me}] Running /craftcms_setup.sh"
/craftcms_setup.sh

logdir=/var/log/supervisor
mkdir -pv $logdir
chown -R www-data:www-data $logdir
echo "[${me}] supervisor logging dir: ${logdir}"

echo "[${me}] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisor.conf
