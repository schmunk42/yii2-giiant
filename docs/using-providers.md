# Using Giiant with Providers

The following code should be added to the bootstrap of your yii application.  If you would like to keep it in it's own file (recommended) create a `config/giiant.php`, then include it from your `config/bootstrap.php`.


## DateTimeProvider

Use a DateTimePicker for the `scheduled_at` field:

```php
\Yii::$container->set('schmunk42\giiant\crud\providers\DateTimeProvider', [
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
\Yii::$container->set('schmunk42\giiant\crud\providers\CallbackProvider', [
    'columnFormats' => $columnFormats,
    'attributeFormats' => $attributeFormats,
    'activeFields' => $activeFields,
    'prependActiveFields' => $prependActiveFields,
    'appendActiveFields' => $appendActiveFields,
]);
```
