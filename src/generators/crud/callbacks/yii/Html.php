<?php

namespace schmunk42\giiant\generators\crud\callbacks\yii;

class Html
{

    static public function column()
    {
        // render HTML in grid columns
        return function ($attribute) {
            return <<<FORMAT
[
    'format' => 'html',
    'attribute' => '{$attribute}',
    'value'=> function(\$model){
        return html_entity_decode(\$model->{$attribute});
    }
]
FORMAT;
        };
    }

    static public function attribute()
    {
        // render HTML in detail view
        return function ($attribute) {
            return <<<FORMAT
[
    'format' => 'html',
    'attribute' => '{$attribute}',
    'value' => html_entity_decode(\$model->{$attribute})
]
FORMAT;
        };
    }

}