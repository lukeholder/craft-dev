#!/usr/bin/env bash
set -eo pipefail
env="${1}"
flyToml="fly.${env}.toml"
machineId=$(fly m list -c "${flyToml}" --json | jq -r '.[] | select(.config.metadata.fly_process_group == "app") | .id')
set -x
fly m start "${machineId}"
fly ssh console -c "${flyToml}" --machine "${machineId}"
{ set +x; } 2>/dev/null
