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

    \schmunk42\giiant\generators\crud\providers\EditorProvider,
    \schmunk42\giiant\generators\crud\providers\SelectProvider,
    \schmunk42\giiant\generators\crud\providers\OptsProvider,


And configure the settings of the provider, add setting via dependecy injection this to your application config, eg. in `console/config/bootstrap.php`:

    \Yii::$container->set(
        'schmunk42\giiant\generators\crud\providers\EditorProvider',
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
    'schmunk42\giiant\generators\crud\providers\CallbackProvider',
    [
        'activeFields'  => $activeFields,
        'columnFormats' => $columnFormats,
        'attributeFormats' => $attributeFormats,
    ]
);
```

[More providers...](docs/callback-provider-examples.md)

----------------------------

# Using Giiant with Providers

The following code should be added to the bootstrap of your yii application.  If you would like to keep it in it's own file (recommended) create a `config/giiant.php`, then include it from your `config/bootstrap.php`.


## DateTimeProvider

Use a DateTimePicker for the `scheduled_at` field:

```php
\Yii::$container->set('schmunk42\giiant\generators\crud\providers\DateTimeProvider', [
    'columnNames' => ['scheduled_at'],
]);
```


## Callback Provider

The callback provider allows you to override the values of model attributes during code generation.  There are 3 sections this applies to:

- `columnFormats` - used in `GridView` on the `index` view.
- `attributeFormats` - used in `DetailView` on the `view` view.
- `activeFields`, `prependActiveFields` and `appendActiveFields` - used in `ActiveForm` on the `_form` view.


### Example Model

Below is an example of the model we are working with.  The methods required are:

- `optsStatus` - return dropdown data in the `ActiveForm` on the `form` view.
- `getStatusLabel` - return html string to be used in `GridView` and `DetailView` on the `index` and `view` pages.

```php
<?php
namespace app\models;
use Yii;

class Job extends base\Job
{
    const STATUS_REQUESTED = 'requested';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_DELIVERED = 'delivered';

    public static function optsStatus($type = null)
    {
        return [
            null => '',
            self::STATUS_REQUESTED => self::STATUS_REQUESTED,
            self::STATUS_SCHEDULED => self::STATUS_SCHEDULED,
            self::STATUS_DELIVERED => self::STATUS_DELIVERED,
        ];
    }

    public function getStatusLabel()
    {
        return '<span class="label label-status-' . $this->status . '">' . $this->status . '</span>';
    }

}
```

### columnFormats

Display a column using a callback to `Job::getStatusLabel()`:

```php
$columnJobStatus = function ($attribute, $generator) {
return <<<INPUT
[
    'attribute' => '{$attribute->name}',
    'format' => 'raw',
    'value' => function (\$model) {
        return \$model->getStatusLabel();
    },
]
INPUT;
};
```

```php
$columnMultiLine = function ($attribute, $generator) {
return <<<INPUT
[
    'attribute' => '{$attribute->name}',
    'format' => 'ntext',
]
INPUT;
};
```

Join them into `$columnFormats`, using the key as a regex to the model and attribute:

```php
$columnFormats = [
    'Job.status'              => $columnJobStatus,
    '.*\.address$|.*\.notes$' => $columnMultiLine,
];
```

### attributeFormats

Display an attribute using a callback to `Job::getStatusLabel()`:

```php
$attributeJobStatus = function ($attribute, $generator) {
return <<<INPUT
    [
        'format' => 'html',
        'attribute' => '{$attribute->name}',
        'value' => \$model->getStatusLabel(),
    ]
INPUT;
};
```

Display multi-line attributes with `ntext` format:

```php
$attributeMultiLine = function ($attribute, $generator) {
return <<<INPUT
    [
        'format' => 'ntext',
        'attribute' => '{$attribute->name}',
    ]
INPUT;
};
```

Join them into `$attributeFormats`, using the key as a regex to the model and attribute:

```php
$attributeFormats = [
    'Job.status'              => $attributeJobStatus,
    '.*\.address$|.*\.notes$' => $attributeMultiLine,
];
```


### activeFields

Use a radio list field:

```php
$fieldJobType = function ($attribute, $generator) {
    $data = \yii\helpers\VarDumper::export(['pickup' => Yii::t('app', 'Pickup'), 'delivery' => Yii::t('app', 'Delivery')]);
    return <<<INPUT
\$form->field(\$model, '{$attribute->name}')->radioList({$data});
INPUT;
},
```

Customise the select field (the generator will already create this, only needed if you want to tweak it):

```php
$fieldJobStatus = function ($attribute, $generator) {
    return <<<INPUT
$form->field($model, '{$attribute->name}')->dropDownList(Job::optsStatus());
INPUT;
},
```

Use a text area for multi-line attributes:

```php
$fieldMultiLine = function ($attribute, $generator) {
return <<<INPUT
    \$form->field(\$model, '{$attribute->name}')->textarea();
INPUT;
},
```

Join them into `$activeFields`, using the key as a regex to the model and attribute:

```php
$activeFields = [
    'Job.type' => $fieldJobType,
    'Job.status' => $fieldJobStatus,
    '.*\.address$|.*\.notes$' => $fieldMultiLine,
];
```

### prependActiveFields and appendActiveFields

If we want to output something before or after the `ActiveField`, we can use `prependActiveFields` and `appendActiveFields`.

For example we only want to show the fields that are safe:

```php
$prependFieldSafe = function ($attribute, $generator) {
    return <<<INPUT
if(\$model->isAttributeSafe('{$attribute->name}')) {
INPUT;
};
$appendFieldSafe = function ($attribute, $generator) {
    return <<<INPUT
}
INPUT;
};
```

Join them into `$prependActiveFields` and `$appendActiveFields`, using the key as a regex to the model and attribute:

```php
$prependActiveFields = [
    '.*' => $prependFieldSafe,
];
$appendActiveFields = [
    '.*' => $appendFieldSafe,
];
```

### Join it all together

To invoke the callback provider we pass in our callbacks as follows:

```php
\Yii::$container->set('schmunk42\giiant\generators\crud\providers\CallbackProvider', [
    'columnFormats' => $columnFormats,
    'attributeFormats' => $attributeFormats,
    'activeFields' => $activeFields,
    'prependActiveFields' => $prependActiveFields,
    'appendActiveFields' => $appendActiveFields,
]);
```
