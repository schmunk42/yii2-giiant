## Testing giiant


### Requirements

- [docker](https://docs.docker.com) **>=1.10-rc1**
- [docker-compose](https://docs.docker.com/compose/) **>=1.6**


### Setup

Go to the `tests` directory

    cd tests

Select the database you want to test

    export GIIANT_TEST_DB=sakila
    cp docker-compose.override-dist.yml docker-compose.override.yml

Initialize *Potemkin*
    
    docker-compose up -d
    docker-compose run --rm -e YII_ENV=test phpfpm setup.sh


### Usage
   
#### Debug and development

Start a bash

    docker-compose run --rm -e YII_ENV=test phpfpm bash

Initialize database and application    
    
    $ setup.sh

Run cli test suite, group *mandatory*
    
    $ codecept run -g mandatory cli

Your output should look similar to [this](https://ci.hrzg.de/projects/24/builds/2685).


#### CI one-liner

Via compose run
    
    docker-compose run --rm -e YII_ENV=test phpfpm codecept run -g mandatory -g ${GIIANT_TEST_DB} cli,unit,functional,acceptance
