PHP				?= php
WEB				?= nginx

DOCKER_HOST_IP  ?= $(shell echo $(DOCKER_HOST) | sed 's/tcp:\/\///' | sed 's/:[0-9.]*//')
DOCKER_COMPOSE  ?= docker-compose

export COMPOSE_FILE  = tests/docker-compose.yml
export CI_APP_VOLUME   ?= ..

.PHONY: open bash test

default: help

all: clean init test open bash

up:      ##@docker start application
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) ps

open:	 ##@docker open application web service in browser
	open http://$(DOCKER_HOST_IP):`$(DOCKER_COMPOSE) port $(WEB) 80 | sed 's/[0-9.]*://'`

bash:	##@docker open application shell in container
	$(DOCKER_COMPOSE) run $(PHP) bash

init:
	sh tests/init.sh

test:
	sh tests/run.sh

clean:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) rm -fv


# Help based on https://gist.github.com/prwhite/8168133 thanks to @nowox and @prwhite
# And add help text after each target name starting with '\#\#'
# A category can be added with @category

HELP_FUN = \
		%help; \
		while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([\w-]+)\s*:.*\#\#(?:@([\w-]+))?\s(.*)$$/ }; \
		print "\nusage: make [target]\n\n"; \
	for (keys %help) { \
		print "$$_:\n"; \
		for (@{$$help{$$_}}) { \
			$$sep = "." x (25 - length $$_->[0]); \
			print "  $$_->[0]$$sep$$_->[1]\n"; \
		} \
		print "\n"; }

help:				##@base Show this help
	#
	# General targets
	#
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)