Batches
=======

> ### Important Notice!

> It is strongly recommended, to get familiar with the CLI usage of *Gii* and *Giiant*, since the code-generation process may be repeated several times in the inital development phase and using the CLI in conjunction with a script will save you a lot of time and also reduce your error rate!

Configuration
-------------

It's recommended to configure a customized `batch` command in your application CLI configuration, add the following code to your console application configuration.

    'controllerMap' => [
        'batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'overwrite' => true,
            'modelNamespace' => 'app\\modules\\crud\\models',
            'modelQueryNamespace' => 'app\\modules\\crud\\models\\query',
            'crudControllerNamespace' => 'app\\modules\\crud\\controllers',
            'crudSearchModelNamespace' => 'app\\modules\\crud\\models\\search',
            'crudViewPath' => '@app/modules/crud/views',
            'crudPathPrefix' => '/crud/',
            'crudTidyOutput' => true,
            'crudAccessFilter' => true,
            'crudProviders' => [
                'schmunk42\\giiant\\generators\\crud\\providers\\optsProvider',
            ],
            'tablePrefix' => 'app_',
            /*'tables' => [
                'app_profile',
            ]*/
        ]
    ],

> Note: `yii giiant-batch` is an alias for the default configuration of `BatchController` registered by this extension.

Usage
-----

### Command Line Batches

You can run batches of base-model and CRUD generation with the build in batch command:

for Linux

    ./yii batch --tables=profile,social_account,user,token

It will process the given tables, for more details see `./yii help giiant-batch`. See the [Sakila example](50-generate-sakila-backend.md) for a detailed example.



### Extended batch-command example

You can also override the batch defaults via command line options, this example is using the default configuration from `giiant-batch`

```
./yii giiant-batch \
    --interactive=0 \
    --overwrite=1 \
    --modelDb=db \
    --modelBaseClass=yii\\db\\ActiveRecord \
    --crudProviders=schmunk42\\giiant\\generators\\crud\\providers\\optsProvider \
    --tables=account,article,variation_status
```
