<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:01
 */

namespace schmunk42\giiant\crud\providers;

class RangeProvider extends \schmunk42\giiant\base\Provider
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
\$form->field(\$model, '{$column->name}')->widget(\kartik\widgets\RangeInput::classname(), [
    'options' => ['placeholder' => 'Rate (0 - 5)...'],
    'html5Options' => ['min' => 0, 'max' => 5],
    'addon' => ['append' => ['content' => '<i class="glyphicon glyphicon-star"></i>']]
]);
EOS;
            default:
                return null;
        }
    }
} 