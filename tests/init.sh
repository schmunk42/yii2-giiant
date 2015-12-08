#!/usr/bin/env bash

set -e

export CI_APP_VOLUME=..
DOCKER_COMPOSE="docker-compose --x-networking"

#${DOCKER_COMPOSE} pull
${DOCKER_COMPOSE} kill
${DOCKER_COMPOSE} rm -fv
${DOCKER_COMPOSE} up -d & wait
${DOCKER_COMPOSE} ps

${DOCKER_COMPOSE} run --rm php yii app/create-mysql-db ${GIIANT_TEST_DB}
${DOCKER_COMPOSE} run --rm php sh /app/src/init.sh

# TODO hotfix, lookup
${DOCKER_COMPOSE} run --rm php yii migrate --interactive=0 --migrationLookup=/app/vendor/schmunk42/yii2-giiant/tests/_migrations