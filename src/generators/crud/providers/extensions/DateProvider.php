<?php

namespace schmunk42\giiant\generators\crud\providers\extensions;

class DateProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
        if (isset($this->generator->getTableSchema()->columns[$attribute])) {
            $column = $this->generator->getTableSchema()->columns[$attribute];
        } else {
            return;
        }

        switch ($column->type) {
            case 'date':
                $this->generator->requires[] = 'zhuravljov/yii2-datetime-widgets';

                return <<<EOS
\$form->field(\$model, '{$column->name}')->widget(\zhuravljov\widgets\DatePicker::className(), [
    'options' => ['class' => 'form-control'],
    'clientOptions' => [
        'autoclose' => true,
        'todayHighlight' => true,
        'format' => 'yyyy-mm-dd',
        'language' => \Yii::\$app->language,
    ],
])
EOS;
                break;
            default:
                return;
        }
    }
}
