#!/usr/bin/env bash

# cleanup
docker-compose kill
#docker-compose rm -f

# default inititalization of large databases hit timeout when running `./yii app/create-mysql-db`, so bring up the database first and sleep a while
# TODO: update command with timeout ENV variable or param
docker-compose up -d mariadb
sleep 45

# start test stack
docker-compose run --rm appcli \
    sh -c './yii app/create-mysql-db && ./yii migrate'
docker-compose up -d