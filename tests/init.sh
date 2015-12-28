#!/usr/bin/env bash

set -e

. "env.sh"

${DOCKER_COMPOSE} kill
${DOCKER_COMPOSE} rm -fv
${DOCKER_COMPOSE} up -d
${DOCKER_COMPOSE} ps

${DOCKER_COMPOSE} run --rm php yii db/create ${GIIANT_TEST_DB}
${DOCKER_COMPOSE} run --rm php sh src/setup.sh
