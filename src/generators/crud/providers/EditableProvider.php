<?php

namespace schmunk42\giiant\generators\crud\providers;

use yii\db\ColumnSchema;

/**
 * Class OptsProvider.
 *
 * @author Christopher Stebe <c.stebe@herzogkommunikation.de>
 */
class EditableProvider extends \schmunk42\giiant\base\Provider
{

    public function addUse($param)
    {
        return "
use kartik\editable\Editable;
";        
    }

        /**
     * 
     *
     * @param $column ColumnSchema
     *
     * @return null|string
     */
    public function attributeFormat($attribute)
    {
       
        $this->generator->requires[] = '"kartik-v/yii2-editable": "@dev"';
        $primaryKey = implode('_',$this->generator->getTableSchema()->primaryKey);
        $column = $this->generator->getTableSchema()->columns[$attribute];
        
        switch ($column->type){
            case 'integer':
            case 'char':
            case 'string':
                $inputType = 'Editable::INPUT_TEXT';
                break;
            case 'date':
            case 'datetime':
                $inputType = 'Editable::INPUT_TEXT';
                break;
            
        }
        return <<<EOS
            [
                'attribute'=>'{$attribute}',
                'format' => 'raw',
                'value' => Editable::widget([
                    'name' => 'name',
                    'asPopover' => true,
                    'value' => \$model->{$attribute},
                    'header' => \$model->getAttributeLabel('{$attribute}'),
                    'inputType' => {$inputType},
                    'size' => 'md',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'Enter ...'
                    ],
                    'ajaxSettings' => [
                        'url' => Url::to(['editable', '{$primaryKey}' => \$model->primaryKey]),
                    ],
                ]),
                
            ]
EOS;
    }


}
