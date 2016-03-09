Customizations
==============

Models
------

### Custom database-connection via base-class

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

Use custom generators, model and crud templates
-----------------------------------------------

```
$config['modules']['gii'] = [
    'class'      => 'yii\gii\Module',
    'allowedIPs' => ['127.0.0.1'],
    'generators' => [
        // generator name
        'giiant-model' => [
            //generator class
            'class'     => 'schmunk42\giiant\generators\model\Generator',
            //setting for out templates
            'templates' => [
                // template name => path to template
                'mymodel' =>
                    '@app/giiTemplates/model/default',
            ]
        ]
    ],
];
```