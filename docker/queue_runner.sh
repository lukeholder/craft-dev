#!/usr/bin/env sh
set -eo pipefail
set -x
export CRAFT_ALLOW_SUPERUSER=1
exec php craft queue/listen --verbose
