#!/usr/bin/env bash

set -e

export GIIANT_TEST_DB=${GIIANT_TEST_DB-sakila}
export CI_APP_VOLUME=${CI_APP_VOLUME-..}
export COMPOSE_PROJECT_NAME=${COMPOSE_PROJECT_NAME-giiant}
DOCKER_COMPOSE="docker-compose --x-networking"

# replace the stack name in the yml configuration for isolated CI stacks
sed -i.bak s/giiant_/${COMPOSE_PROJECT_NAME}_/ acceptance.suite.yml

docker exec ${COMPOSE_PROJECT_NAME}_php_1 sh -c "\
    codecept clean -c /app/vendor/schmunk42/yii2-giiant/codeception.yml && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli prod/base && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli prod/${GIIANT_TEST_DB} && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml acceptance ${GIIANT_TEST_DB} && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml functional ${GIIANT_TEST_DB}
    "
