#!/usr/bin/env bash

docker-compose run --rm appcli \
    codecept run -v -c vendor/schmunk42/yii2-giiant cli prod

docker-compose run --rm appcli \
    codecept run -v -c vendor/schmunk42/yii2-giiant unit,functional,acceptance
