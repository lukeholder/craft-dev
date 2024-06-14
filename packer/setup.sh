#!/usr/bin/env bash

set -euo pipefail

echo "Sleeping for 30s..."
for i in $(seq 30) ; do
    sleep 1
    echo -n .
done
echo ""

export DEBIAN_FRONTEND=noninteractive

echo "Creating teentixsysadmin user with password..."
sudo adduser --disabled-password --gecos "" teentixsysadmin
sudo adduser teentixsysadmin sudo
sudo chpasswd <<<'teentixsysadmin:$y$j9T$COlLBZgODOHg4/70ZA8.Y.$UP.pQSBXlNGOGaHU3ASyfE4YV0XxuPbQPwg8uPmzMv6'

echo "Adding current hostname to /etc/hosts to avoid sudo errors"
echo "127.0.0.1 $(hostname)" | sudo tee -a /etc/hosts

echo "Running apt-get update..."
sudo apt-get update

echo "Installing packages..."
sudo apt-get install -y \
    haproxy \
    resolvconf \
    wireguard

sudo mkdir -pv /etc/haproxy && \
    sudo mv -v /tmp/haproxy.cfg /etc/haproxy/haproxy.cfg
sudo mkdir -pv /etc/wireguard && \
    sudo mv -v /tmp/wg0.conf /etc/wireguard/wg0.conf

echo "Enabling and starting wireguard..."
sudo systemctl stop wg-quick@wg0
sudo systemctl enable wg-quick@wg0
sudo systemctl start wg-quick@wg0

echo "Enabling and starting haproxy..."
sudo systemctl stop haproxy
sudo systemctl enable haproxy
sudo systemctl start haproxy
