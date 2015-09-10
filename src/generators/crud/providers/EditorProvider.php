<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace schmunk42\giiant\generators\crud\providers;

class EditorProvider extends \schmunk42\giiant\base\Provider
{
    public $widget = 'ckeditor';

    public function activeField($attribute)
    {
        if (!isset($this->generator->getTableSchema()->columns[$attribute])) {
            return null;
        }

        $column = $this->generator->getTableSchema()->columns[$attribute];
        if (in_array($column->name, $this->columnNames)) {
            switch ($this->widget) {
                case 'redactor':
                    $this->generator->requires[] = 'yiidoc/yii2-redactor';
                    return "\$form->field(\$model, '{$attribute}')->widget(\\yii\\redactor\\widgets\\Redactor::className())";
                    break;
                default:
                    $this->generator->requires[] = '2amigos/yii2-ckeditor-widget';
                    return <<<EOS
\$form->field(\$model, '{$attribute}')->widget(
    \dosamigos\ckeditor\CKEditor::className(),
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ]
)
EOS;
            }
        }
    }
} 