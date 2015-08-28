docker-compose run --rm appcli \
    sh -c './yii gii/module --moduleID=crud --moduleClass=app\\modules\\sakila\\Module'

docker-compose run --rm appcli \
    sh -c './yii gii/module --moduleID=crud --moduleClass=app\\modules\\employees\\Module'
