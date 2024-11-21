<?php
/**
 * @var \schmunk42\giiant\generators\config\Generator $generator
 * @var int $levels
 */

echo '<?php' . PHP_EOL;
echo PHP_EOL;
echo 'namespace ' . $generator->ns . ';' . PHP_EOL;
echo PHP_EOL;
echo <<<PHP
use schmunk42\giiant\generators\crud\callbacks\\base\Callback;
use schmunk42\giiant\generators\crud\providers\core\CallbackProvider;
use schmunk42\giiant\generators\crud\providers\core\OptsProvider;
use schmunk42\giiant\generators\crud\providers\core\RelationProvider;
use Yii;

\$config = require dirname(__DIR__, $levels) . '/config/main.php';

Yii::\$container->set(
    CallbackProvider::class,
    [
        // Form
        'activeFields' => [
            'created_at|updated_at' => Callback::false()
        ],
        // Index
        'columnFormats' => [
            'created_at|updated_at' => fn(\$attribute) => "'\$attribute:datetime'"
        ],
        // View
        'attributeFormats' => [
            'created_at|updated_at' => fn(\$attribute) => "'\$attribute:datetime'"
        ]
    ]
);

\$config['controllerMap']['project-batch'] = [
    'class' => 'schmunk42\giiant\commands\BatchController',
    'overwrite' => true,
    'interactive' => false,
    'modelNamespace' => __NAMESPACE__ . '\\models',
    'modelBaseClass' => __NAMESPACE__ . '\\models\\ActiveRecord',
    'modelQueryNamespace' => __NAMESPACE__ . '\\models\\query',
    'crudControllerNamespace' => __NAMESPACE__ . '\\controllers',
    'crudSearchModelNamespace' => __NAMESPACE__ . '\\models\\search',
    'crudViewPath' => '@' . str_replace('\\\\', '/', __NAMESPACE__) . '/views',
    'crudPathPrefix' => '',
    'crudTidyOutput' => false,
    'crudFixOutput' => false,
    'useTimestampBehavior' => false,
    'useBlameableBehavior' => false,
    'tablePrefix' => '',
    'crudMessageCategory' => '$generator->messageCategory',
    'modelMessageCategory' => '$generator->messageCategory',
    'crudEnableCopy' => false,
    'tables' => [],
    'crudProviders' => [
        CallbackProvider::class,
        OptsProvider::class,
        RelationProvider::class
    ]
];

return \$config;
PHP;
echo PHP_EOL;
