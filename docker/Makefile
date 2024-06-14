MAKEFLAGS = --no-print-directory

ENV ?= dev

ifeq ($(ENV),dev)
DOCKER_FILE_EXT=dev
else ifeq ($(ENV),staging)
DOCKER_FILE_EXT=production
else ifeq ($(ENV),production)
DOCKER_FILE_EXT=production
else
error(Unrecognized environment $(ENV))
endif

DOCKER_BUILD_PATH = ..
DOCKER_FILE = Dockerfile
IMAGE_NAME = teentix-site
GIT_BRANCH = $(shell git branch --show-current)
GIT_COMMIT = $(shell git log -1 --pretty=format:"%H")
IMAGE_TAG = git.$(GIT_COMMIT).$(GIT_BRANCH)
IMAGE_NAME_AND_TAG = $(IMAGE_NAME):$(IMAGE_TAG)
COMPOSE_PROJECT_NAME = teentix-site-local
DOCKER_COMPOSE_FLAGS =

default: help

## Display this help message
help:
	@awk '/^##.*$$/,/[a-zA-Z_-]+:/' $(MAKEFILE_LIST) | awk '!(NR%2){print $$0p}{p=$$0}' | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}' | sort

## Build docker image
build:
	docker build --tag $(IMAGE_NAME_AND_TAG) --file $(DOCKER_FILE) --platform linux/x86_64 $(DOCKER_BUILD_PATH)

## Run website locally in docker containers (add DOCKER_COMPOSE_FLAGS=--build to force images to be rebuilt)
run-local:
	COMPOSE_PROJECT_NAME=$(COMPOSE_PROJECT_NAME) docker compose up $(DOCKER_COMPOSE_FLAGS)

## Delete stopped docker containers
clean-docker-containers:
	COMPOSE_PROJECT_NAME=$(COMPOSE_PROJECT_NAME) docker compose rm

## Delete docker volumes for database and uploaded media
clean-docker-volumes:
	docker volume $(COMPOSE_PROJECT_NAME)_db_data
