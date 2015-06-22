#!/usr/bin/env bash

docker-compose run --rm appcli \
    codecept run -c vendor/schmunk42/yii2-giiant unit,cli,functional,acceptance
