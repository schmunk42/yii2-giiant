yii2-giiant
===========

Extended models and CRUDs for Gii, the code generator of Yii2 Framework

**PROJECT IS IN BETA STAGE!**


What is it?
-----------

Giiant provides templates for model and CRUD generation with relation support and a sophisticated UI.
A main project goal is porting many features and learnings from gtc, giix, awecrud and others into one solution.


Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

    composer.phar require schmunk42/yii2-giiant:"*"

The generators are registered automatically in the application bootstrap process, if the Gii module is enabled

Usage
-----

Visit your application's Gii (eg. `index.php?r=gii` and choose one of the generators from the main menu screen.

For basic usage instructions see the [Yii2 Guide section for Gii](http://www.yiiframework.com/doc-2.0/guide-tool-gii.html).

### Command Line Batches

You can run batches of base-model and CRUD generation with the build in batch command:

    ./yii giiant-batch --tables=profile,social_account,user,token

It will process the given tables, for more details see `./yii help giiant-batch`. See the [Sakila example](docs/generate-sakila-backend.md) for a detailed example.


Features
--------

### Model generator

- generates separate model classes to customize and base models classes to regenerate
- table prefixes can be stipped off model class names (not bound to db connection setting)

### CRUD generator

- model, view and controller locations can be customized to use subfolders
- horizontal and vertical form layout
- action button class customization (Select "App Class" option on the  Action Button Class option on CRUD generator to customize)
- input, attribute, column and relation customization with provider queue
- callback provider to inject any kind of code for inputs, attributes and columns via dependency injection

Providers
---------

- *CallbackProvider* universal provider to modify any input, attribute or column with highly flexible callback functions
- *RelationProvider* renders code for relations (eg. links, dropdowns)
- *EditorProvider* renders RTE, like `Ckeditor` as input widget
- *DateTimeProvider* renders date inputs
- *OptsProvider* render a populated dropdown, if the model contains and `optsColumnName()` method.


### Customization with providers

In many cases you want to exchange i.e. some inputs with a customized version for your project.
Examples for this use-case are editors, file-uploads or choosers, complex input widget with a modal screen, getting
data via AJAX and so on.

With Giiant Providers you can create a queue of instances which may provide custom code depending on more complex
rules. Take a look at some existing [giiant providers](https://github.com/schmunk42/yii2-giiant/tree/master/crud/providers).

Configure providers, add this to your provider list in the form:

    \schmunk42\giiant\crud\providers\EditorProvider,
    \schmunk42\giiant\crud\providers\SelectProvider,
    \schmunk42\giiant\crud\providers\OptsProvider,
    

And configure the settings of the provider, add setting via dependecy injection this to your application config, eg. in `console/config/bootstrap.php`:

    \Yii::$container->set(
        'schmunk42\giiant\crud\providers\EditorProvider',
        [
            'columnNames' => ['description']
        ]
    );

This will render a Ckeditor widget for every column named `description`.

    <?= $form->field($model, 'description')->widget(
    \dosamigos\ckeditor\CKEditor::className(),
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>

**NOTE** The OptsProvider matches every model with opts methods for a field, i.e. method `optsMembers` matches for model attribute `members`.

#### Using "prompt" in dropdown lists

Set the first entry in your `getColumnName()` method to value `null`.  

	null => \Yii::t('app', 'Select'),

To ensure that the correct value is written to the database you should add a validation rule in the model.  

    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [
                    ['field_name'],
                    'default',
                    'value' => null
                ]
            ]
        );
    }


### Universal `CallbackProvider`

This provider has three properties `activeFields` (form), `columnFormats` (index) and `attributeFormats` (view) which all take an array of callback as input. The keys are evaluated as a regular expression the match the class and attribute name.
While the callback function takes the current attribute and generator as input parameters.

The configuration can be done via the dependency injection container of Yii2.

Define callbacks for input fields in `_form` view

```
$activeFields = [

   /**
    * Generate a checkbox for specific column (model attribute)
    */
   'models\\\\Foo.isAvailable' => function ($attribute, $generator) {
       $data = \yii\helpers\VarDumper::export([0 => 'Nein', 1 => 'Ja']);
       return <<<INPUT
\$form->field(\$model, '{$attribute->name}')->checkbox({$data});
INPUT;
   },
   
];
```

Define callbacks for grid columns in `index` view

```
columnFormats = [

   // generate custom HTML in column
   'common\models\Foo.html' => function ($attribute, $generator) {
       return <<<FORMAT
[
    'format' => 'html',
    'label'=>'FOOFOO',
    'attribute' => 'item_id',
    'value'=> function(\$model){
        return \yii\helpers\Html::a(\$model->bar,['/crud/item/view', 'id' => \$model->link_id]);
    }
]
FORMAT;
   },

    // hide all text fields in grid
    '.+' => function ($column, $model) {
            if ($column->dbType == 'text') {
                return false;
            }
    },
    
    // hide system fields in grid
    'created_at$|updated_at$' => function () {
           return false;
    },
    
];
```

Detail `view` attributes

```
$attributeFormats = [

    // usa a static helper function for all columns ending with `_json`
    '_json$' => function ($attribute, $generator) {
        $formattter = StringFormatter::className();
        return <<<FORMAT
[
    'format' => 'html',
    #'label'=>'FOOFOO',
    'attribute' => '{$attribute->name}',
    'value'=> {$formattter}::contentJsonToHtml(\$model->{$attribute->name})

]
FORMAT;

    },
];
```

Finally add the configuration via DI container

```
\Yii::$container->set(
    'schmunk42\giiant\crud\providers\CallbackProvider',
    [
        'activeFields'  => $activeFields,
        'columnFormats' => $columnFormats,
        'attributeFormats' => $attributeFormats,
    ]
);
```

[More providers...](docs/callback-provider-examples.md)


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
            'class'     => 'schmunk42\giiant\model\Generator',
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

Extras
------

A detailed description how to use MySQL workbench for model updates and migration see [here](docs/using-mysql-workbench.md).

Special thanks to [motin](https://github.com/motin), [thyseus](https://github.com/thyseus), [uldisn](https://github.com/uldisn) and [rcoelho](https://github.com/rcoelho) for their work, inspirations and feedback.


Troubleshooting
---------------

You can also add 
    
    "repositories": [
      {
        "type": "vcs",
        "url": "https://github.com/schmunk42/yii2-giiant.git"
      }
    ],
    "require": {
        .....(your required modules)....
        "schmunk42/yii2-giiant":"dev-master"
    },

to your *** composer.json ***  file and run
    
    composer update
    
if you are having trouble with "Not found" errors using the preferred method. 

Screenshots
-----------

![giiant-0 2-screen-1](https://cloud.githubusercontent.com/assets/649031/5692432/c93fd82c-98f5-11e4-8b52-8f35df52986f.png)
![giiant-0 2-screen-2](https://cloud.githubusercontent.com/assets/649031/5692429/c9189492-98f5-11e4-969f-02a302ca6974.png)

Links
-----

- [Phundament.com](http://phundament.com)
- [GitHub](https://github.com/schmunk42/yii2-giiant)
- [Packagist](https://packagist.org/packages/schmunk42/yii2-giiant)
- [Yii Extensions](http://www.yiiframework.com/extension/yii2-giiant/)
