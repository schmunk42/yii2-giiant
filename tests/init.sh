#!/usr/bin/env bash

export DOCKER_CLIENT_TIMEOUT=120

# default inititalization of large databases hit timeout when running `./yii app/create-mysql-db`, so bring up the database first
set +e
    # don't throw an error here
    # TODO: remove tlsverify=0 (Docker 1.7 issue)
    docker $DEBUG_OPTS run -d -p 3306 --name example-databases -e MARIADB_PASS=secretadmin schmunk42/mariadb-example-databases
set -e
docker $DEBUG_OPTS start example-databases

# cleanup
docker-compose kill
docker-compose rm -f

# start test stack
docker-compose run --rm appcli \
    sh -c './yii app/create-mysql-db sakila && ./yii app/create-mysql-db employees'
docker-compose run --rm appcli \
    sh -c './yii app/create-mysql-db && ./yii migrate --interactive=0'
docker-compose up -d