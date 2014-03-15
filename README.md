yii2-giiant
===========

Extended CRUDs for Yii2

**PROJECT IS IN DEVELOPMENT STAGE!**


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
    php composer.phar require schmunk42/yii2-giiant @dev

Edit the application config...
    
    edit config/web.php

... and add the giiant generator in the `YII_ENV_DEV` section

    $config['modules']['gii'] = array();	
    $config['modules']['gii']['class'] = 'yii\gii\Module';
    $config['modules']['gii']['generators'] = ['giiant' => ['class' => 'schmunk42\giiant\crud\Generator']];
    
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

Features
--------

* Shows relations on index page
* Customize inputs with provider queue

### SelectProvider

- Renders a `Selectize` widget, more to come...

### EditorProvider

- Renders a `Ckeditor` widget, more to come...


Customization with providers
----------------------------

In many cases you want to exchange i.e. some inputs with a customized version for your project.
Examples for this use-case are editors, file-uploads or choosers, complex input widget with a modal screen, getting
data via AJAX and so on.

With Giiant Providers you can create a queue of instances which may provide custom code depending on more complex
rules. Take a look at some existing [giiant providers](https://github.com/schmunk42/yii2-giiant/tree/develop/crud/providers).

Configure providers, add this to your provider list in the form:

    \schmunk42\giiant\crud\providers\EditorProvider,
    \schmunk42\giiant\crud\providers\SelectProvider,

And configure the settings of the provider:

    \Yii::$objectConfig = [
        // giiant provider configuration
        'schmunk42\giiant\crud\providers\EditorProvider' => [
            'columnNames' => ['description']
        ]
    ];

This will render a Ckeditor widget for every column named `description`.

    <?= $form->field($model, 'description')->widget(
    \dosamigos\ckeditor\CKEditor::className(),
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>

Screenshots
-----------

### Form with provider list

![](https://lh4.googleusercontent.com/-IEhBUQmnXxE/UyM2Wru_xsI/AAAAAAAAAF0/P7B-bLBv8N4/w1382-h1214-no/Bildschirmfoto+2014-03-14+um+18.00.57.png)

### Composer hints after generation

![](https://lh4.googleusercontent.com/-NC4tVJL1V-w/UyM2WsRkWJI/AAAAAAAAAFw/Zsi9V0zE7MQ/w1440-h334-no/Bildschirmfoto+2014-03-14+um+18.01.18.png)

### Ckeditor and Selectize input

![](https://lh3.googleusercontent.com/-4cFNRsSLPWk/UyMz00Gz4cI/AAAAAAAAAE0/C2kukUnDCL0/w703-h604-no/Bildschirmfoto+2014-03-14+um+17.15.21.png)


### Relation links

![](https://lh5.googleusercontent.com/-kmeGiuJZEoQ/UyMz055TdHI/AAAAAAAAAE4/swHY85UMSwQ/w846-h581-no/Bildschirmfoto+2014-03-14+um+17.35.34.png)



Roadmap
-------

tbd
