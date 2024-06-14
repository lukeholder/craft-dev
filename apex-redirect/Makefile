
GIT_SHA1 := $(shell git rev-parse --short HEAD)
VERSION := 2.0.$(GIT_SHA1)
IMAGE := teentix.org-apex-redirect
FLY := flyctl
FLY_DEPLOY_FLAGS =

default: help

## Display this help message
help:
	@awk '/^##.*$$/,/[a-zA-Z_-]+:/' $(MAKEFILE_LIST) | awk '!(NR%2){print $$0p}{p=$$0}' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' | sort

## Build docker image
docker-build:
	docker build --platform linux/amd64 -t $(IMAGE):$(VERSION) .

## Run docker container locally
run-local: docker-build
	docker run -p 8080:8080 --rm --platform linux/amd64 -it $(IMAGE):$(VERSION)

## Deploy teentix.org apex redirect
deploy-fly:
	$(FLY) deploy $(FLY_DEPLOY_FLAGS)
