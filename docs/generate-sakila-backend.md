Create a new advanced application with Giiant...

```
composer.phar create-project --prefer-dist --stability=dev yiisoft/yii2-app-advanced advanced
composer.phar require schmunk42/yii2-giiant:dev-master
```

Set it up...

```
./init
```

[Download](http://dev.mysql.com/doc/index-other.html) Sakila Demo from MySQL.

Create a database `yii2appadvanced` & import dump.

Create a database `sakila` & import dump.

Edit `common/config/main-local.php`, and adjust your standard `db` config and add:

        'dbSakila' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=sakila',
            'username' => 'dev',
            'password' => 'dev123',
            'charset' => 'utf8',
        ],

We'll use a custom base class to have Sakila in a separate database.

Create `common/models/SakilaActiveRecord.php`

```
<?php

namespace common\models;

use yii\db\ActiveRecord;

class SakilaActiveRecord extends ActiveRecord
{
    public static function getDb()
    {
        return \Yii::$app->dbSakila;
    }
}
```

To keep things clean, we'll generate the files into subfolders, prepare them with:

```
mkdir backend/controllers/crud
mkdir common/models/sakila
mkdir common/models/sakila/search
```

Finally run the batch:

```
./yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --enableI18N=1 \
    --messageCategory=app \
    --modelBaseClass=app\\modules\\sakila\\base\\SakilaActiveRecord \
    --modelNamespace=app\\modules\\sakila\\models \
    --crudControllerNamespace=app\\modules\\sakila\\controllers \
    --crudSearchModelNamespace=app\\modules\\sakila\\models\\search \
    --crudViewPath=@app/modules/sakila/views \
    --crudPathPrefix= \
    --crudProviders=schmunk42\\giiant\\crud\\providers\\DateTimeProvider \
    --tables=actor,address,category,city,country,customer,film,film_actor,film_category,film_text,inventory,language,payment,rental,staff,store
```

And open `index.php?r=crud/film` to test your fresh Sakila CRUDs generated with Giiant.
