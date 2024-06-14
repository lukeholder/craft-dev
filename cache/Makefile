
FLY := flyctl
FLY_DEPLOY_FLAGS =

default: help

## Display this help message
help:
	@awk '/^##.*$$/,/[a-zA-Z_-]+:/' $(MAKEFILE_LIST) | awk '!(NR%2){print $$0p}{p=$$0}' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' | sort

## Deploy dev cache
deploy-dev:
	$(FLY) deploy -c fly.dev.toml $(FLY_DEPLOY_FLAGS)

## SSH to dev fly environment
ssh-dev:
	$(FLY) ssh console -c fly.dev.toml

## Deploy staging cache
deploy-staging:
	$(FLY) deploy -c fly.staging.toml $(FLY_DEPLOY_FLAGS)

## SSH to staging fly environment
ssh-staging:
	$(FLY) ssh console -c fly.staging.toml

## Deploy production cache
deploy-production:
	$(FLY) deploy -c fly.production.toml $(FLY_DEPLOY_FLAGS)

## SSH to production fly environment
ssh-production:
	$(FLY) ssh console -c fly.production.toml
