### Universal `CallbackProvider` examples

### attributeFormats

Display an attribute using a callback to `Job::getStatusLabel()`:

```php
$attributeJobStatus = function ($attribute, $generator) {
return <<<INPUT
    [
        'format' => 'html',
        'attribute' => '{$attribute}',
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
        'attribute' => '{$attribute}',
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
    'attribute' => '{$attribute}',
    'value'=> {$formattter}::contentJsonToHtml(\$model->{$attribute})

]
FORMAT;

    },
];
```




### columnFormats

Display a column using a callback to `Job::getStatusLabel()`:

```php
$columnJobStatus = function ($attribute, $generator) {
return <<<INPUT
[
    'attribute' => '{$attribute}',
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
    'attribute' => '{$attribute}',
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

Renders a color column

```php
$colorColumn = function ($attribute, $model, $generator) {
    return <<<FORMAT
[
    'attribute' => '{$attribute}',
    'format' => 'raw',
    'value' => function (\$model) {
        return \yii\helpers\Html::tag('div', '', ['style' => 'background-color: '.\$model->{$attribute}.'; width: 30px; height: 30px;']);
    },
]
FORMAT;
};
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





### activeFields

Use a radio list field:

```php
$fieldJobType = function ($attribute, $generator) {
    $data = \yii\helpers\VarDumper::export(['pickup' => Yii::t('app', 'Pickup'), 'delivery' => Yii::t('app', 'Delivery')]);
    return <<<INPUT
\$form->field(\$model, '{$attribute}')->radioList({$data});
INPUT;
},
```

Customise the select field (the generator will already create this, only needed if you want to tweak it):

```php
$fieldJobStatus = function ($attribute, $generator) {
    return <<<INPUT
$form->field($model, '{$attribute}')->dropDownList(Job::optsStatus());
INPUT;
},
```

Use a text area for multi-line attributes:

```php
$fieldMultiLine = function ($attribute, $generator) {
return <<<INPUT
    \$form->field(\$model, '{$attribute}')->textarea();
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
if(\$model->isAttributeSafe('{$attribute}')) {
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



Join it all together
--------------------

To invoke the callback provider we pass in our callbacks as follows:

```php
\Yii::$container->set('schmunk42\giiant\generators\crud\providers\core\CallbackProvider', [
    'columnFormats' => $columnFormats,
    'attributeFormats' => $attributeFormats,
    'activeFields' => $activeFields,
    'prependActiveFields' => $prependActiveFields,
    'appendActiveFields' => $appendActiveFields,
]);
```


----------


## Alternative syntax

### Configuration with static callbacks 

```
<?php
namespace temp;

use schmunk42\giiant\generators\crud\callbacks\base\Callback;
use schmunk42\giiant\generators\crud\callbacks\yii\Db;
use schmunk42\giiant\generators\crud\callbacks\yii\Html;

\Yii::$container->set(
    'schmunk42\giiant\generators\crud\providers\core\CallbackProvider',
    [
        'columnFormats'    => [
            // hide system fields, but not ID in table
            'created_at$|updated_at$' => Callback::false(),
            // hide all TEXT or TINYTEXT columns
            '.*'                      => Db::falseIfText(),
        ],
        'activeFields'     => [
            // hide system fields in form
            'id$'                         => Db::falseIfAutoIncrement(),
            'id$|created_at$|updated_at$' => Callback::false(),
        ],
        'attributeFormats' => [
            // render HTML output
            '_html$' => Html::attribute(),
        ]
    ]
);
```
