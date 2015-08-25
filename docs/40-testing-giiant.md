## Testing giiant

**Note! This is a concept/prototype.**

### Phundament "Potemkin"-testing

*Giiant* uses a *[Phundament 4](https://github.com/phundament/app)* docker-stack which is "wrapped around" the extension directory.
The basic concept is to start an [application stack](https://github.com/schmunk42/yii2-giiant/blob/feature/tests/tests/docker-compose.yml) with `docker-compose` and "over-mount" the extension directly into `/vendor/schmunk42/yii2-giiant`.

> You should be able to use this approach for any *Yii 2.0 Framework* extension. Note: You need to install the extension in the application. This is a current limitation. 

In `tests/` resides a `docker-compose.yml` file, which contains an full-featured `phundament/app` stack, with pre-installed a *Codeception* test-framework on the `appcli`-container.
There are suites for CLI, unit, function and acceptance testing right - ready to use, right out of the box.
 
In addition the the above, the `init` script starts a MariaDB (MySQL) Docker image with a huge dataset (all MySQL sample databases) for testing.

> Note: You may need to run `init.sh` twice due to setup timeout on the very first run of the `xdb` container.
 
The stack also contains *Selenium* containers for acceptance testing with screenshots - *Firefox* or *Chrome*.

The tests run Sakila CRUD generation as CLI-tests and then use a browser-based acceptance test to access the freshly created crud.

Finally it creates a screenshot of that in `tests/_output`. 


### Requirements

- [docker](https://docs.docker.com)
- [docker-compose](https://docs.docker.com/compose/) (>=1.2)


### Setup

Go to the `tests` directory

    cd tests

Initialize *Potemkin*
    
    sh init.sh

### Usage

Run the test suites    
    
    sh run.sh

Your output should look similar to [this](https://ci.hrzg.de/projects/24/builds/2685).

### Debug and development

Enter the CLI container

    docker-compose run appcli bash

Go to the mounted project directory in the container 

    cd vendor/schmunk42/yii2-giiant

Run *Codeception* from there   
    
    codecept run cli -v
    
    
    
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


