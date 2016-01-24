## Testing giiant


### Requirements

- [docker](https://docs.docker.com)
- [docker-compose](https://docs.docker.com/compose/) **>=1.6**


### Setup

Go to the `tests` directory

    cd tests

Select the database you want to test

    export GIIANT_TEST_DB=sakila

Initialize *Potemkin*
    
    sh init.sh

### Usage

Run the test suites    
    
    sh run.sh

Your output should look similar to [this](https://ci.hrzg.de/projects/24/builds/2685).

### Debug and development

    export CI_APP_VOLUME=..
    export GIIANT_TEST_DB=sakila

Enter the CLI container

    docker-compose --x-networking run php bash

Go to the mounted project directory in the container 

    cd vendor/schmunk42/yii2-giiant

Run *Codeception* from there   
    
    codecept run -v cli prod
    codecept run -v functional sakila
    codecept run -v acceptance sakila
    
    
    
### Example CI script
    

```
set -e
export COMPOSE_FILE=ci.yml

cd tests
sh init.sh
sh run.sh

docker-compose kill

exit 0
```


