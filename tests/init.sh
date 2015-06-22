#!/usr/bin/env bash

set +e
docker run --name runner-example-databases -e MARIADB_PASS=veryveryverysecretadmin schmunk42/mariadb-example-databases
set -e
docker start runner-example-databases

# cleanup
docker-compose kill
#docker-compose rm -f

# default inititalization of large databases hit timeout when running `./yii app/create-mysql-db`, so bring up the database first and sleep a while
# TODO: update command with timeout ENV variable or param
#docker-compose up -d mariadb
#sleep 45

# start test stack
docker-compose run --rm appcli \
    sh -c './yii app/create-mysql-db && ./yii migrate'
docker-compose up -d