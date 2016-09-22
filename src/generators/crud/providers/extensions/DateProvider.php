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

    /**
     * Formatter for detail view attributes, who have get[..]ValueLabel function.
     *
     * @param $column ColumnSchema
     * @param $model ActiveRecord
     *
     * @return null|string
     */
    public function columnFormat($attribute, $model)
    {
        if (isset($this->generator->getTableSchema()->columns[$attribute])) {
            $column = $this->generator->getTableSchema()->columns[$attribute];
        } else {
            return;
        }

        if ($column->type != 'date') {
            return;
        }

        return <<<EOS
[
    'attribute'=>'{$attribute}',
    'format'=>'date',
    'filter' => '<div class="input-group drp-container">'
        .DateRangePicker::widget([
            'model' => \$searchModel,
            'attribute' => '{$attribute}_range',
            'presetDropdown'=>true,
            'convertFormat'=>true,
            'pluginOptions'=>[
                'locale'=>['format' => 'Y-m-d'],
                'showDropdowns'=>true
            ]
        ]).'</div>',                
]        
EOS;
    }
}
