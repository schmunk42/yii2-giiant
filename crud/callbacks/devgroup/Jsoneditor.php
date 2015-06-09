<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 09.06.15
 * Time: 22:40
 */

namespace schmunk42\giiant\crud\callbacks\devgroup;


use yii\bootstrap\Collapse;

class Jsoneditor
{
    static public function field()
    {
        return function ($attribute) {
            $collapse = Collapse::className();
            $editor = \devgroup\jsoneditor\Jsoneditor::className();
            return <<<FORMAT
'<div class="field-widget-{$attribute}">'.
{$editor}::widget(
    [
        'editorOptions' => [
            'modes' => ['code', 'form', 'text', 'tree', 'view'], // available modes
            'mode'  => 'tree', // current mode
        ],
        'model'         => \$model,
        'attribute'     => '{$attribute}',
        'options'       => [
            'id'    => 'widget-{$attribute}',
            'class' => 'form-control',
        ],
    ]
).
'</div>'
FORMAT;
        };
    }

    static public function attribute()
    {
        return function ($attribute, $generator) {
            $formattter = StringFormatter::className();
            return <<<FORMAT
[
    'format' => 'html',
    #'label'=>'FOOFOO',
    'attribute' => '{$attribute}',
    'value'=> {$formattter}::contentJsonToHtml(\$model->{$attribute})

]
FORMAT;
        };
    }
}