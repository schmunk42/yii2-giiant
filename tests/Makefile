DOCKER_COMPOSE=docker-compose

UNAME_S := $(shell uname -s)
ifeq ($(UNAME_S), Darwin)
	OPEN_CMD        ?= open
	DOCKER_HOST_IP  ?= $(shell echo $(DOCKER_HOST) | sed 's/tcp:\/\///' | sed 's/:[0-9.]*//')
else
	OPEN_CMD        ?= xdg-open
	DOCKER_HOST_IP  ?= 127.0.0.1
endif

open:	 ##@docker open application web service in browser
	$(OPEN_CMD) http://$(DOCKER_HOST_IP):$(shell $(DOCKER_COMPOSE) port nginx 80 | sed 's/[0-9.]*://')

open-db:	 ##@docker open application web service in browser
	$(OPEN_CMD) mysql://admin:secretadmin@$(DOCKER_HOST_IP):$(shell $(DOCKER_COMPOSE) port mariadb 3306 | sed 's/[0-9.]*://')

bash:	##@docker open application shell in container
	$(DOCKER_COMPOSE) run --rm phpfpm bash

up:	##@docker start application stack
	$(DOCKER_COMPOSE) up -d

setup:	##@docker setup application
	$(DOCKER_COMPOSE) run phpfpm setup.sh

test: up setup
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=dev -e GIIANT_TEST_DB=sakila phpfpm bash -c "codecept run --steps --html=_report.html -g mandatory -g sakila cli,unit,acceptance"
