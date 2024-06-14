MAKEFLAGS = --no-print-directory

default: help

## Display this help message
help:
	@awk '/^##.*$$/,/[a-zA-Z_-]+:/' $(MAKEFILE_LIST) | awk '!(NR%2){print $$0p}{p=$$0}' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' | sort

## Build wireguard-proxy VM image with Packer
build-vm-image: wg0.conf
	packer build wireguard-proxy.pkr.hcl

wg0.conf:
	fly wireguard create teentix sea teentix-wireguard-proxy-in-azure
	$(info ACTION REQUIRED: a new wg0.conf was just created. Make sure to update the DB_SERVER env var and haproxy.cfg bind address with the new `Interface > Address`.)

## Initialize the packer environment
init-packer:
	packer init .
