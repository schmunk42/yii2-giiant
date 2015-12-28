#!/usr/bin/env bash

set -e

. ./env.sh

${DOCKER_COMPOSE} run --rm php bash
