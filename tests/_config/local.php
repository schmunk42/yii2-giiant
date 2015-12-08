<?php

switch (getenv('GIIANT_TEST_DB')) {
    case 'sakila':
        $giiantTestModule = [
            'sakila' => [
                'class' => 'app\modules\sakila\Module',
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
}


return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => getenv('DATABASE_DSN_BASE') . ';dbname=' . getenv('GIIANT_TEST_DB'), // DATABASE_DSN_DB
            'username' => getenv('DATABASE_USER'),
            'password' => getenv('DATABASE_PASSWORD'),
            'charset' => 'utf8',
            'tablePrefix' => getenv('DATABASE_TABLE_PREFIX'),
        ],
    ],
    'modules' => (php_sapi_name() == 'cli') ? [] : $giiantTestModule
];