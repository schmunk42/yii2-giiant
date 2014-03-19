<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:01
 */

namespace schmunk42\giiant\crud\providers;

class DateTimeProvider extends \schmunk42\giiant\base\Provider
{
    public $columnNames = [''];

    public function generateActiveField($attribute)
    {
        $column = $this->generator->getTableSchema()->columns[$attribute];

        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = 'zhuravljov\yii2-datetime-widgets';
                return <<<EOS
\$form->field(\$model, '{$column->name}')->widget(\zhuravljov\widgets\DateTimePicker::className(), [
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
        #'format' => 'dd.mm.yyyy',
        #'language' => 'ru',
        'autoclose' => true,
        'todayHighlight' => true,
    ],
])
EOS;
            default:
                return null;
        }
    }
} 