#!/usr/bin/env bash

#docker-compose --x-networking pull

docker-compose --x-networking kill
docker-compose --x-networking rm -fv
docker-compose --x-networking up -d & wait
docker-compose --x-networking run php yii app/create-mysql-db ${GIIANT_TEST_DB}
docker-compose --x-networking run php sh /app/src/init.sh
docker-compose --x-networking ps