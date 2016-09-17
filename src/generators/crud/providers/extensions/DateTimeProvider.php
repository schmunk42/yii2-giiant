<?php

namespace schmunk42\giiant\generators\crud\providers\extensions;

class DateTimeProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
        switch (true) {
            case in_array($attribute, $this->columnNames):
                $this->generator->requires[] = 'zhuravljov/yii2-datetime-widgets';

                return <<<EOS
\$form->field(\$model, '{$attribute}')->widget(\zhuravljov\widgets\DateTimePicker::className(), [
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
        'autoclose' => true,
        'todayHighlight' => true,
        'format' => 'yyyy-mm-dd hh:ii',
    ],
])
EOS;
                break;
            default:
                return null;
        }
    }
}
