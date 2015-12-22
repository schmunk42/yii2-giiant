#!/usr/bin/env bash

set -e

export GIIANT_TEST_DB=${GIIANT_TEST_DB-sakila}
export CI_APP_VOLUME=${CI_APP_VOLUME-..}
DOCKER_COMPOSE="docker-compose --x-networking"

docker exec tests_php_1 sh -c "\
    codecept clean -c /app/vendor/schmunk42/yii2-giiant/codeception.yml && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli prod/base && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli prod/${GIIANT_TEST_DB} && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml functional ${GIIANT_TEST_DB} && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml acceptance ${GIIANT_TEST_DB}
    "
