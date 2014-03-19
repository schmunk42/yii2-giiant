<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace schmunk42\giiant\crud\providers;

use yii\log\Logger;

class EditorProvider extends \schmunk42\giiant\base\Provider
{
    #public $columnNames = [''];

    public function generateActiveField($attribute)
    {
        $column = $this->generator->getTableSchema()->columns[$attribute];
        switch (true) {
            case (in_array($column->name, $this->columnNames)):
                $this->generator->requires[] = '2amigos/yii2-ckeditor-widget';
                return <<<EOS
\$form->field(\$model, '$attribute')->widget(
    \dosamigos\ckeditor\CKEditor::className(),
    [
        'options' => ['rows' => 6],
        'preset' => 'basic'
    ])
EOS;
            default:
                return null;
        }
    }
} 