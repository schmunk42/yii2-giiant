#!/usr/bin/env bash

# clean (kill and remove containers in stack)
docker-compose kill
docker-compose rm -f

# default inititalization of large databases hit timeout when running `./yii app/create-mysql-db`, so bring up the database first
docker-compose up -d mariadb

# start test stack
docker-compose up -d