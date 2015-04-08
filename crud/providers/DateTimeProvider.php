<?php

namespace schmunk42\giiant\crud\providers;

class DateTimeProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
        $column = $this->generator->getTableSchema()->columns[$attribute->name];

        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = 'zhuravljov/yii2-datetime-widgets';
                return <<<EOS
\$form->field(\$model, '{$column->name}')->widget(\zhuravljov\widgets\DateTimePicker::className(), [
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
        'autoclose' => true,
        'todayHighlight' => true,
    ],
])
EOS;
                break;
            default:
                return null;
        }
    }
} 
