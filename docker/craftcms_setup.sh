#!/usr/bin/env sh
set -euo pipefail
me="${0}"
echo "[${me}] Running: su-exec www-data:www-data php craft migrate/all --interactive=0"
su-exec www-data:www-data php craft migrate/all --interactive=0
echo "[${me}] Running: su-exec www-data:www-data php craft project-config/diff"
su-exec www-data:www-data php craft project-config/diff
echo "[${me}] Running: su-exec www-data:www-data php craft project-config/apply --interactive=0"
su-exec www-data:www-data php craft project-config/apply --interactive=0
echo "[${me}] Running: su-exec www-data:www-data php craft project-config/diff"
su-exec www-data:www-data php craft project-config/diff
