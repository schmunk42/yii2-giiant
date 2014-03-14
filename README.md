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


### Customization with providers

In many cases you want to exchange i.e. some inputs with a customized version for your project.
Examples for this use-case are editors, file-uploads or choosers, complex input widget with a modal screen, getting
data via AJAX and so on.

With Giiant Providers you can create a queue of instances which may provide custom code depending on more complex
rules.

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

This will render a Ckeditor widget for every field which is a text-field (see provider code) and has the name
`description`.

    <?= $form->field($model, 'description')->widget(
    \dosamigos\ckeditor\CKEditor::className(),
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>

Feature
-------

* Shows relations on index page

Screenshots
-----------

### Ckeditor and Selectize Input

![](https://lh3.googleusercontent.com/-4cFNRsSLPWk/UyMz00Gz4cI/AAAAAAAAAE0/C2kukUnDCL0/w703-h604-no/Bildschirmfoto+2014-03-14+um+17.15.21.png)


### Relation Links

![](https://lh5.googleusercontent.com/-kmeGiuJZEoQ/UyMz055TdHI/AAAAAAAAAE4/swHY85UMSwQ/w846-h581-no/Bildschirmfoto+2014-03-14+um+17.35.34.png)



Roadmap
-------

tbd
