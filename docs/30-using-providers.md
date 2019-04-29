# Using Giiant with Providers

The following code should be added to the bootstrap of your yii application.  


Customization with providers
----------------------------

- *CallbackProvider* universal provider to modify any input, attribute or column with highly flexible callback functions
- *RelationProvider* renders code for relations (eg. links, dropdowns)
- *EditorProvider* renders RTE, like `Ckeditor` as input widget
- *DateTimeProvider* renders date inputs
- *OptsProvider* render a populated dropdown, if the model contains and `optsColumnName()` method.


## Usage

In many cases you want to exchange i.e. some inputs with a customized version for your project.
Examples for this use-case are editors, file-uploads or choosers, complex input widget with a modal screen, getting
data via AJAX and so on.

With Giiant Providers you can create a queue of instances which may provide custom code depending on more complex
rules. Take a look at some existing [giiant providers](https://github.com/schmunk42/yii2-giiant/tree/master/src/generators/crud/providers).

To configure providers, add this to your provider list in the form or command configuration:

    \schmunk42\giiant\generators\crud\providers\extensions\EditorProvider,
    \schmunk42\giiant\generators\crud\providers\core\OptsProvider,


And to configure the settings of the provider, add settings via dependecy injection to your application config, e.g. in `console/config/bootstrap.php`:

    \Yii::$container->set(
        \schmunk42\giiant\generators\crud\providers\extensions\EditorProvider::class,
        [
            'columnNames' => ['description']
        ]
    );

This will render a Ckeditor widget for every column named `description`.

    <?= $form->field($model, 'description')->widget(
    \dosamigos\ckeditor\CKEditor::class,
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]) ?>


Configuration
-------------

### Universal `CallbackProvider`


The callback provider allows you to override the values of model attributes during code generation.  There are 3 sections this applies to:

- `columnFormats` - used in `GridView` on the `index` view.
- `attributeFormats` - used in `DetailView` on the `view` view.
- `activeFields`, `prependActiveFields` and `appendActiveFields` - used in `ActiveForm` on the `_form` view.

These three properties `activeFields` (form), `columnFormats` (index) and `attributeFormats` (view) take an array of callbacks as input. 
The keys are evaluated as a regular expression to match the namespaced class and attribute name.

While the callback function takes the current `attribute`, `model` and `generator` as input parameters.

The configuration can be done via the dependency injection container of Yii2.

Define callbacks for input fields, which should be generated in `_form` view

#### Shorthand function

    $checkboxField = function ($attribute, $model, $generator) {
        return "\$form->field(\$model, '{$attribute}')->checkbox()->label('active')";
    };

#### Generate a checkbox for specific column (model attribute)

```
$activeFields = [
   'models\\\\Foo.isAvailable' => $checkboxField,
];
```

Finally add the configuration via DI container

```
\Yii::$container->set(
    \schmunk42\giiant\generators\crud\providers\core\CallbackProvider::class,
    [
        'activeFields'  => $activeFields,
        'columnFormats' => $columnFormats,
        'attributeFormats' => $attributeFormats,
    ]
);
```

**[More examples for this provider...](31-callback-provider-examples.md)**

To generate dropdown list in GridView filed search filter set configuration via DI container

```php
\Yii::$container->set(
    \schmunk42\giiant\generators\crud\providers\core\RelationProvider::class,
    [
        'gridFilterDropdownRelation' => true,
    ]
);
``` 


### Specialized providers

#### `DateTimeProvider`

Use a DateTimePicker for the `scheduled_at` field:

```php
\Yii::$container->set('schmunk42\giiant\generators\crud\providers\extensions\DateTimeProvider', [
    'columnNames' => ['scheduled_at'],
]);
```


#### `OptsProvider`

**NOTE** The OptsProvider matches every model with opts methods for a field, i.e. method `optsMembers` matches for model attribute `members`.

##### Example Model

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
