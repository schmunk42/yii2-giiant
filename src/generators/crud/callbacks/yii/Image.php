<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 09.06.15
 * Time: 23:23.
 */
namespace schmunk42\giiant\generators\crud\callbacks\yii;

class Image
{
    public static function attribute()
    {
        // render image tag
        return function ($attribute) {
            return <<<FORMAT
[
    'format' => 'html',
    'attribute' => '{$attribute}',
    'value'=> function(\$model){
        return yii\helpers\Html::img(\Yii::getAlias("@web") . "/" . \$model->{$attribute});
    }
]
FORMAT;
        };
    }
}
