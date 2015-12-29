#!/usr/bin/env bash

set -e

. ./env.sh

# Cleanup
${DOCKER_COMPOSE} kill
${DOCKER_COMPOSE} rm -fv

# Start
${DOCKER_COMPOSE} up -d
${DOCKER_COMPOSE} run --rm php sh src/setup.sh

# Informational messages
${DOCKER_COMPOSE} ps
${DOCKER_COMPOSE} port nginx 80