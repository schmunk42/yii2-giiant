<?php
namespace schmunk42\giiant\crud\providers;

use yii\db\ColumnSchema;

/**
 * Class OptsProvider
 * @package schmunk42\giiant\crud\providers
 * @author Christopher Stebe <c.stebe@herzogkommunikation.de>
 */
class OptsProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField(ColumnSchema $attribute)
    {
        $column     = $this->generator->getTableSchema()->columns[$attribute->name];
        $modelClass = $this->generator->modelClass;
        $func       = 'opts' . str_replace("_", "", $column->name);

        if (method_exists($modelClass::className(), $func)) {
            $mode = isset($this->columnNames[$attribute->name]) ? $this->columnNames[$attribute->name] : null;
        } else {
            return null;
        }

        switch ($mode) {
            case 'radio':
                return <<<EOS
                    \$form->field(\$model, '{$column->name}')->radioList(
                        {$modelClass}::{$func}()
                    );
EOS;
                break;

            case 'select2':
                return <<<EOS
                    \$form->field(\$model, '{$column->name}')->widget(\kartik\select2\Select2::classname(), [
                        'name' => 'class_name',
                        'model' => \$model,
                        'attribute' => '{$column->name}',
                        'data' => {$modelClass}::{$func}(),
                        'options' => [
                            'placeholder' => {$this->generator->generateString('Type to autocomplete')},
                            'multiple' => false,
                        ]
                    ]);
EOS;
                break;

            default:
                // Render a dropdown list if the model has a method optsColumn().
                return <<<EOS
                        \$form->field(\$model, '{$column->name}')->dropDownList(
                            {$modelClass}::{$func}()
                        );
EOS;

        }

        return null;

    }
}
