yii2-giiant
===========

Extended CRUDs for Yii2


Project Goal
------------

Create a sophisticated and modular CRUD template with relations for Yii 2.
Port (all) the features and learnings from gtc, giix, awecrud and others into one solution.


Installation
------------ 

Create a basic yii application

    php composer.phar create-project --stability=dev yiisoft/yii2-app-basic yii-basic
    
Go to the project directory and install `giiant`
    
    cd yii-basic
    php composer.phar require schmunk42/giiant @dev

Edit the application config...
    
    edit config/web.php

... and add the giiant generator in the `YII_ENV_DEV` section

	$config['modules']['gii'] = array();	
	$config['modules']['gii']['class'] = 'yii\gii\Module';
    $config['modules']['gii']['generators'] = ['giiant' => ['class' => 'schmunk42\giiant\Generator']];
    
Don't forget to setup a database application component, eg.

    'db' => [
         'class' => 'yii\db\Connection',
         'dsn' => 'mysql:host=localhost;dbname=sakila', // MySQL, MariaDB
         'username' => 'test',
         'password' => 'test',
         'charset' => 'utf8',
    ],
    
> Note: You can use the MySQL sakila demo database for testing.

Open Gii...

    http://localhost/index.php?r=gii
    
...and select the **Giiant** template, you may need to create some models in advance.


Feature
-------

* Shows relations on index page

Roadmap
-------

tbd
