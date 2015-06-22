## Testing giiant

### Requirements

- docker
- docker-compose (>=1.2)

### CLI

Go to the `tests` directory

    cd tests

Initialize *Potemkin*
    
    sh init.sh

Run the test suites    
    
    sh run.sh


### Debug and Development

Enter the CLI container

    docker-compose run appcli bash

Go to the mounted project directory in the container 

    cd vendor/schmunk42/yii2-giiant

Run *Codeception* from there   
    
    codecept run cli -v
    
    
    
    
### Example CI script
    
    
    git submodule update --init
    ls -la
    
    set -e
    
    cd tests
    #sh init.sh
    sh run.sh
    
    docker-compose kill
    docker-compose rm -f
    
    exit 0