#!/usr/bin/env bash

echo "Setting ENV variables..."

export DOCKER_COMPOSE="docker-compose"
export COMPOSE_PROJECT_NAME=${COMPOSE_PROJECT_NAME-giiant}
export CI_APP_VOLUME=${CI_APP_VOLUME-..}
export GIIANT_TEST_DB=${GIIANT_TEST_DB-sakila}

# replace the stack name in the yml configuration for isolated CI stacks
sed -i.bak s/giiant_/${COMPOSE_PROJECT_NAME}_/ acceptance.suite.yml
