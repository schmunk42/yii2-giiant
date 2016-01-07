#!/usr/bin/env bash

set -e

export GIIANT_TEST_DB=${GIIANT_TEST_DB-sakila}
export CI_APP_VOLUME=${CI_APP_VOLUME-..}
export COMPOSE_PROJECT_NAME=${COMPOSE_PROJECT_NAME-giiant}
DOCKER_COMPOSE="docker-compose --x-networking"

# replace the stack name in the yml configuration for isolated CI stacks
sed -i.bak s/giiant_/${COMPOSE_PROJECT_NAME}_/ acceptance.suite.yml

${DOCKER_COMPOSE} kill
${DOCKER_COMPOSE} rm -fv
${DOCKER_COMPOSE} up -d
${DOCKER_COMPOSE} ps

${DOCKER_COMPOSE} run --rm php yii db/create ${GIIANT_TEST_DB}
${DOCKER_COMPOSE} run --rm php sh src/setup.sh
