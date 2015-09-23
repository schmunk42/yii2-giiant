docker-compose run --rm appsrc \
    sh -c 'composer require schmunk42/yii2-giiant:dev-master'

docker-compose run --rm appcli \
    sh -c './yii gii/module --moduleID=crud --moduleClass=app\\modules\\sakila\\Module'

docker-compose run --rm appcli \
    sh -c './yii gii/module --moduleID=crud --moduleClass=app\\modules\\employees\\Module'
