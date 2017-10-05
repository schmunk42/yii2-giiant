.PHONY: open bash test

DOCKER_COMPOSE  ?= docker-compose

PHP				?= phpfpm
WEB				?= phpfpm

UNAME_S := $(shell uname -s)
ifeq ($(UNAME_S), Darwin)
	OPEN_CMD        ?= open
else
	OPEN_CMD        ?= xdg-open
endif

ifdef ($(DOCKER_HOST))
	DOCKER_HOST_IP  ?= $(shell echo $(DOCKER_HOST) | sed 's/tcp:\/\///' | sed 's/:[0-9.]*//')
else
	DOCKER_HOST_IP  ?= 127.0.0.1
endif

default: help


all: build up setup

open:	 ##@docker open application web service in browser
	$(OPEN_CMD) http://$(DOCKER_HOST_IP):`$(DOCKER_COMPOSE) port $(WEB) 80 | sed 's/[0-9.]*://'`

open-db:	 ##@docker open application web service in browser
	$(OPEN_CMD) mysql://admin:secretadmin@$(DOCKER_HOST_IP):$(shell $(DOCKER_COMPOSE) port mariadb 3306 | sed 's/[0-9.]*://')

open-vnc:	 ##@test open application database service in browser
	$(OPEN_CMD) vnc://x:secret@$(DOCKER_HOST_IP):$(shell $(DOCKER_COMPOSE) port seleniumfirefox 5900 | sed 's/[0-9.]*://')

build:	##@docker build images
	$(DOCKER_COMPOSE) build

setup:	##@docker prepare test-application stack
	$(DOCKER_COMPOSE) run $(PHP) bash -c "cd _app && composer install"

up:	##@docker start test-application stack
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) exec mariadb /init-example-databases.sh

test:    ##@docker run tests
	$(DOCKER_COMPOSE) run $(PHP) bash -c "while ! curl mariadb:3306; do ((c++)) && ((c==30)) && break; sleep 2; done"
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=dev -e GIIANT_TEST_DB=sakila $(PHP) bash -c "codecept run --steps --html=_report.html -g mandatory -g sakila -g onlyCrud cli,unit,acceptance"

bash:	##@docker open application shell in container
	$(DOCKER_COMPOSE) run --rm $(PHP) bash

clean:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) rm -fv

lint:
	mkdir -p _artifacts/lint && chmod -R 777 _artifacts/lint
	docker run --rm -v "${PWD}/..:/project" jolicode/phaudit php-cs-fixer fix --format=txt -v --dry-run src || export ERROR=1; \
	docker run --rm -v "${PWD}/..:/project" jolicode/phaudit phpmetrics --report-html=_artifacts/lint/metrics.html src/ || ERROR=1; \
	docker run --rm -v "${PWD}/..:/project" jolicode/phaudit phpmd src html cleancode,codesize,controversial,design,unusedcode > _artifacts/lint/mess.html || ERROR=1; \
	exit ${ERROR}

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