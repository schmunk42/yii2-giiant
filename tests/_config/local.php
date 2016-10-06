<?php

switch (getenv('GIIANT_TEST_DB')) {
    case 'sakila':
        $giiantTestModule = [
            'sakila' => [
                'class' => 'app\modules\sakila\Module',
                'layout' => '@admin-views/layouts/main',
            ],
            'backend2' => [
                'class' => 'app\modules\backend\Module',
                'layout' => '@admin-views/layouts/main',
            ]
        ];
        break;
    case 'employees':
        $giiantTestModule = [
            'employees' => [
                'class' => 'app\modules\employees\Module',
                'layout' => '@admin-views/layouts/main',
            ]
        ];
        break;

    default:
        $giiantTestModule = [
            getenv('GIIANT_TEST_DB') => [
                'class' => 'app\modules\\'.getenv('GIIANT_TEST_DB').'\Module',
                'layout' => '@admin-views/layouts/main',
            ]
        ];

}

// TODO: add note to dependencies for CRUDs to docs
$giiantTestModule['gridview'] = ['class' => 'kartik\grid\Module'];

return [
    'aliases' => [
        '@tests' => '@vendor/schmunk42/yii2-giiant/tests',
        '@common' => '@app/common',
        '@backend' => '@app/modules/backend'
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\ApcCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DATABASE_DSN_BASE').';dbname='.getenv('GIIANT_TEST_DB'), // DATABASE_DSN_DB
            'username' => getenv('DATABASE_USER'),
            'password' => getenv('DATABASE_PASSWORD'),
            'charset' => 'utf8',
            'tablePrefix' => getenv('DATABASE_TABLE_PREFIX'),
            'enableSchemaCache' => true,
        ],
    ],
    'modules' => (php_sapi_name() == 'cli') ? [] : $giiantTestModule,
    'params' => [
        'yii.migrations' => [
            '@vendor/schmunk42/yii2-giiant/tests/_migrations'
        ]
    ]
];