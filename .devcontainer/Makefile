MAKEFLAGS = --no-print-directory

IMG := ghcr.io/teentix/teentix-site/devcontainer:latest

default: help

## Display this help message
help:
	@awk '/^##.*$$/,/[a-zA-Z_-]+:/' $(MAKEFILE_LIST) | awk '!(NR%2){print $$0p}{p=$$0}' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' | sort

## Push the docker image to the ghcr.io registry
push-docker-image:
	docker build --platform linux/amd64 -t $(IMG) . && \
	docker push $(IMG)
