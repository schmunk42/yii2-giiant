<?php

use app\components\EditorIdentity;
use yii\gii\Module;
use yii\rbac\PhpManager;
use yii\web\Application;

$testVendorPath = '/repo/tests/_app/vendor';

require($testVendorPath.'/autoload.php');

switch (getenv('GIIANT_TEST_DB')) {
    case 'sakila':
        $giiantTestModule = [
            'sakila' => [
                'class' => 'app\modules\sakila\Module',
                #'layout' => '@admin-views/layouts/main',
            ],
            'backend2' => [
                'class' => 'app\modules\backend\Module',
                #'layout' => '@admin-views/layouts/main',
            ],
        ];
        break;
    case 'employees':
        $giiantTestModule = [
            'employees' => [
                'class' => 'app\modules\employees\Module',
                #'layout' => '@admin-views/layouts/main',
            ],
        ];
        break;

    default:
        $giiantTestModule = [
            getenv('GIIANT_TEST_DB') => [
                'class' => 'app\modules\\'.getenv('GIIANT_TEST_DB').'\Module',
                #'layout' => '@admin-views/layouts/main',
            ],
        ];

}

// TODO: add note to dependencies for CRUDs to docs
#$giiantTestModule['gridview'] = ['class' => 'kartik\grid\Module'];

if (php_sapi_name() != 'cli') {
    $modules = $giiantTestModule;
}

$modules['gii'] = [
    'class' => Module::class,
    'allowedIPs' => ['*'],
];

return [
    'vendorPath' => $testVendorPath,
    'aliases' => [
        '@tests' => '@vendor/schmunk42/yii2-giiant/tests',
        '@common' => '@app/common',
        '@backend' => '@app/modules/backend',
    ],
    'bootstrap' => [
        'gii',
    ],
    'on '.Application::EVENT_BEFORE_REQUEST => function (){
        if (php_sapi_name() != 'cli') {
            Yii::$app->user->login(new EditorIdentity());
        }
    },
    'components' => [
        'authManager' => [
            'class' => PhpManager::class
        ],
        /*'cache' => [
            'class' => 'yii\caching\ApcCache',
        ],*/
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.getenv('DB_PORT_3306_TCP_ADDR').';dbname='.getenv('GIIANT_TEST_DB'),
            // DATABASE_DSN_DB
            'username' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'charset' => 'utf8',
            'tablePrefix' => getenv('DATABASE_TABLE_PREFIX'),
            'enableSchemaCache' => true,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
        # TODO: bug in `Tabs`, see also https://github.com/dmstr/yii2-bootstrap/issues/4
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'user' => [
            'class' => 'dmstr\web\User',
            'identityClass' => 'app\components\EditorIdentity',
        ],
    ],
    'modules' => $modules,
    'params' => [
        'yii.migrations' => [
            '@app/src/migrations/test',
        ],
    ],
];