#!/usr/bin/env bash

docker-compose --x-networking run php sh -c "\
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli prod/base; \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml cli prod/${GIIANT_TEST_DB}; \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml acceptance; \
    codecept run -c /app/vendor/schmunk42/yii2-giiant/codeception.yml functional
    "
