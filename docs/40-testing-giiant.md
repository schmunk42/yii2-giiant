## Testing giiant


### Requirements

- [docker](https://docs.docker.com) **>=1.10.2**
- [docker-compose](https://docs.docker.com/compose/) **>=1.7**


### Setup

Go to the `tests` directory

    cd tests

Start *phd-potemkin-stack*
    
    docker-compose up -d

### Usage
   
#### Debug and development

Start a bash

    docker-compose run --rm -e YII_ENV=test phpfpm bash

Initialize database and application    
    
    $ setup.sh

Run cli test suite, group *mandatory*
    
    $ codecept run -g mandatory cli,acceptance --steps
    
Your output should look similar to [this](https://ci.hrzg.de/projects/24/builds/2685).


#### CI one-liner

Via compose run
    
    docker-compose run --rm -e YII_ENV=test phpfpm codecept run -g mandatory -g ${GIIANT_TEST_DB} cli,unit,functional,acceptance
