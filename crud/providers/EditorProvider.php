<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace schmunk42\giiant\crud\providers;

use yii\db\ColumnSchema;
use yii\db\Schema;

class EditorProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($attribute)
    {
        if (!isset($this->generator->getTableSchema()->columns[$attribute])) {
            return null;
        }

        $column = $this->generator->getTableSchema()->columns[$attribute];

        switch ($column->type) {
            case Schema::TYPE_TEXT:
                $msg = 'yiidoc/yii2-redactor (<b>Attention!</b> Configuration changes required. <small><a href="https://github.com/yiidoc/yii2-redactor">More info</a></small>)';

                if (!in_array($msg, $this->generator->requires)) {
                    $this->generator->requires[] = $msg;
                }

                return "\$form->field(\$model, '{$attribute}')->widget(\\yii\\redactor\\widgets\\Redactor::className())";
            default:
                return null;
        }
    }
} 