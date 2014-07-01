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
    public function generateActiveField($attribute)
    {
        if(!isset($this->generator->getTableSchema()->columns[$attribute])){
            return \Yii::$app->log->logger->log($attribute.' is not defined',10,'not-exist-attribute');
        }
        $column = $this->generator->getTableSchema()->columns[$attribute];

        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = 'zhuravljov\yii2-datetime-widgets';
                return <<<EOS
\$form->field(\$model, '{$column->name}')->widget(\zhuravljov\widgets\DateTimePicker::className(), [
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
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