#!/usr/bin/env bash

set -e

. ./env.sh

# replace the stack name in the yml configuration for isolated CI stacks
sed -i.bak s/giiant_/${COMPOSE_PROJECT_NAME}_/ acceptance.suite.yml

docker exec ${COMPOSE_PROJECT_NAME}_php_1 sh -c "\
    codecept clean -c /app/vendor/schmunk42/yii2-giiant/codeception.yml && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli base && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli ${GIIANT_TEST_DB} && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml acceptance ${GIIANT_TEST_DB} && \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml functional ${GIIANT_TEST_DB}
    "
