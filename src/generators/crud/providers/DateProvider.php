<?php

namespace schmunk42\giiant\generators\crud\providers;

class DateProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
	    if (isset($this->generator->getTableSchema()->columns[$attribute])) {
            $column = $this->generator->getTableSchema()->columns[$attribute];
        } else {
            return null;
        }

        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = 'zhuravljov/yii2-datetime-widgets';
                return <<<EOS
\$form->field(\$model, '{$column->name}')->widget(\zhuravljov\widgets\DatePicker::className(), [
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
        'autoclose' => true,
        'todayHighlight' => true,
        'format' => 'yyyy-mm-dd',
    ],
])
EOS;
                break;
            default:
                return null;
        }
    }
} 
