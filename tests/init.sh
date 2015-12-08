#!/usr/bin/env bash

set -e

export CI_APP_VOLUME=..
DOCKER_COMPOSE="docker-compose --x-networking"

#${DOCKER_COMPOSE} pull
${DOCKER_COMPOSE} kill
${DOCKER_COMPOSE} rm -fv
${DOCKER_COMPOSE} up -d & wait
${DOCKER_COMPOSE} ps