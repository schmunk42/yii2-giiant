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
        $column = $this->generator->getTableSchema()->columns[$attribute->name];

        // Render a dropdown list if the model has a method optsColumn().
        $modelClass = $this->generator->modelClass;
        $func       = 'opts' . str_replace("_", "", $column->name);

        if (method_exists($modelClass::className(), $func)) {
            return <<<EOS
\$form->field(\$model, '{$column->name}')->dropDownList(
    {$modelClass}::{$func}(),
    ['prompt' => {$this->generator->generateString('Select')}]
);
EOS;

        } else {
            return null;
        }
    }
}
