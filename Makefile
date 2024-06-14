MAKEFLAGS = --no-print-directory

FLY := flyctl
FLY_DEPLOY_FLAGS =

default: help

## Display this help message
help:
	@awk '/^##.*$$/,/[a-zA-Z_-]+:/' $(MAKEFILE_LIST) | awk '!(NR%2){print $$0p}{p=$$0}' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' | sort

## Run site locally using docker-compose (add DOCKER_COMPOSE_FLAGS=--build to force images to be rebuilt)
run-local:
	$(MAKE) -C docker run-local

## Build containers and run site locally using docker-compose
build-and-run-local:
	composer install --no-interaction --prefer-dist && \
	$(MAKE) run-local DOCKER_COMPOSE_FLAGS+=--build

## Clear local dev db, next start up will re-import any database dumps in docker/db_init_scripts
clear-local-dev-db:
	./docker/clear_local_dev_db.bash

## Deploy dev.teentix.org, dev-admin.teentix.org, etc
deploy-dev:
	$(FLY) deploy -c fly.dev.toml $(FLY_DEPLOY_FLAGS)

## SSH to dev fly environment
ssh-dev:
	./infra-scripts/ssh.bash dev

## Deploy staging.teentix.org, staging-admin.teentix.org, etc
deploy-staging:
	$(FLY) deploy -c fly.staging.toml $(FLY_DEPLOY_FLAGS)

## SSH to staging fly environment
ssh-staging:
	./infra-scripts/ssh.bash staging

## Deploy www.teentix.org, admin.teentix.org, etc
deploy-production:
	$(FLY) deploy -c fly.production.toml $(FLY_DEPLOY_FLAGS)

## SSH to production fly environment
ssh-production:
	./infra-scripts/ssh.bash production

## Deploy teentix.org apex redirect
deploy-apex-redirect:
	make -C apex-redirect deploy-fly
