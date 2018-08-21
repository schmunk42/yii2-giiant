yii2-giiant
===========

> "Giiant is huge!"

**PROJECT IS IN BETA STAGE!**

[![Build Status](https://travis-ci.org/schmunk42/yii2-giiant.svg?branch=master)](https://travis-ci.org/schmunk42/yii2-giiant)

What is this?
-------------

Giiant is an extended code-generator for models and CRUDs, based upon *Gii* (Yii 2.0 Framework).

It creates prototypes for database models and backends including relations defined by foreign-key constrains in no-time.

To provide a highly flexible configuration system it features a *callback-provider-queue* to define rendering of customized inputs, columns or attribute values.

A main project goal is porting many features and learnings from *gtc*, *giix*, *awecrud* and other code-generators into one solution.


Resources
---------

- [Documentation](docs/README.md)
- [Upgrading instructions](UPGRADING.md)
- [Project Source-Code](https://github.com/schmunk42/yii2-giiant)
- [Packagist](https://packagist.org/packages/schmunk42/yii2-giiant)
- [Yii Extensions](http://www.yiiframework.com/extension/yii2-giiant/)


Features
--------

### Batch command

- `yii batch` creates all models and/or CRUDs for a set of tables sequentially with a single command

### Model generator

- generates separate model classes to customize and base models classes which can be regenerated on schema changes
- table prefixes can be stripped off model class names (not bound to `db` connection settings from Yii 2.0)

### CRUD generator

- input, attribute, column and relation customization with provider queues
- callback provider to inject any kind of code for inputs, attributes and columns via dependency injection
- virtual-relation support (non-foreign key relations)
- model, view and controller locations can be customized to use subfolders
- horizontal and vertical form layout
- options for tidying generated code
- action button class customization (Select "App Class" option on the  Action Button Class option on CRUD generator to customize)


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Using a stable version

    composer require schmunk42/yii2-giiant:"@stable"

Using latest master

    composer require schmunk42/yii2-giiant:"@dev"

The generators are registered automatically in the application bootstrap process, if *Gii* module is enabled.

> You can try giiant via [phd](http://phundament.com) (dockerized PHP application template).


Configuration
-------------

It's recommended to configure a customized `batch` command in your application CLI configuration.

    'controllerMap' => [
        'batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'overwrite' => true,
            'modelNamespace' => 'app\\modules\\crud\\models',
            'crudTidyOutput' => true,
        ]
    ],

> Note: `yii giiant-batch` is an alias for the default configuration of `BatchController` registered by this extension.

You can add the giiant specific configuration `config/giiant.php`, and include this from your `config/main.php`.

See the [batches](docs/20-batches.md) section for configuration details.


Usage
-----

To create a full-featured database backend, run the CLI batch command

    yii batch

You can still override the settings from the configuration, like selecting specific tables

    yii batch --tables=a,list,of,tables


### Core commands

Show help for gii

    yii help gii

Create application-module for giiant CRUDs
    
    yii gii/giiant-module

The commands for generating models and CRUD, there are usually run via the batch command above.

    yii gii/giiant-model
    yii gii/giiant-crud


Advanced
--------

### Provider usage and configuration via dependency injection 

See [docs](docs/30-using-providers.md) for details.

### Using callbacks to provide code-snippets

See [docs](docs/31-callback-provider-examples.md) for details.

### Troubleshooting

See [docs](docs/60-troubleshooting.md) for known-issues, platform specific usage, quirks, faq, ...


Extras
------

Special thanks to [motin](https://github.com/motin), [thyseus](https://github.com/thyseus), [uldisn](https://github.com/uldisn) and [rcoelho](https://github.com/rcoelho) for their work, inspirations and feedback.


Screenshots
-----------

![giiant-0 2-screen-1](https://cloud.githubusercontent.com/assets/649031/5692432/c93fd82c-98f5-11e4-8b52-8f35df52986f.png)
![giiant-0 2-screen-2](https://cloud.githubusercontent.com/assets/649031/5692429/c9189492-98f5-11e4-969f-02a302ca6974.png)

---

Built by [dmstr](http://diemeisterei.de)
