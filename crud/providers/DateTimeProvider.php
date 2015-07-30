<?php

namespace schmunk42\giiant\crud\providers;

use yii\db\Schema;

class DateTimeProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
        if (!isset($this->generator->getTableSchema()->columns[$attribute])) {
            return null;
        }

        $column = $this->generator->getTableSchema()->columns[$attribute];

        switch ($column->type) {
            case Schema::TYPE_DATE:
            case Schema::TYPE_TIME:
            case Schema::TYPE_DATETIME:
            case Schema::TYPE_TIMESTAMP:
                $msg = '2amigos/yii2-date-time-picker-widget';

                if (!in_array($msg, $this->generator->requires)) {
                    $this->generator->requires[] = $msg;
                }

                return "\$form->field(\$model, '{$column->name}')->widget(dosamigos\\datetimepicker\\DateTimePicker::className(), [
    'options' => ['class' => 'form-control'],
    'pickButtonIcon' => 'glyphicon glyphicon-calendar',
    'clientOptions' => [
        // more options @ http://www.malot.fr/bootstrap-datetimepicker
        'autoclose' => true,
        'todayHighlight' => true,
        'weekStart' => {$this->generator->weekStart},
        " . ($column->type == "datetime" ?
                "'format' => '{$this->generator->dateFormat} {$this->generator->timeFormat}', // or 'dd.mm.yyyy' to display only the date" :
                "'format' => '{$this->generator->dateFormat}',"
            ) . "
    ],
])";
                break;
            default:
                return null;
        }
    }
} 
