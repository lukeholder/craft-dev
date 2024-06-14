#!/usr/bin/env sh
set -eo pipefail
mysql -h "${DB_SERVER}" -u "${DB_USER}" -p"${DB_PASSWORD}" "${DB_DATABASE}"
