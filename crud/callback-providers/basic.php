<?php

// Callback snippets for giiant CallbackProvider
// ---------------------------------------------
//
//

// hide field
$remove = function () {
    return false;
};

// hide text columns (dbType: text)
$removeIfText = function ($attribute) {
    if ($attribute->dbType == 'text') {
        return false;
    }
};

// render image tag
$attrAsImage = function ($attribute) {
    return <<<FORMAT
[
    'format' => 'html',
    'attribute' => '{$attribute->name}',
    'value'=> function(\$model){
        return yii\helpers\Html::img(\Yii::getAlias("@web") . "/" . \$model->{$attribute->name});
    }
]
FORMAT;
};

// render HTML in grid columns
$columnAsHtml = function ($attribute) {
    return <<<FORMAT
[
    'format' => 'html',

    'attribute' => '{$attribute->name}',
    'value'=> function(\$model){
        return html_entity_decode(\$model->{$attribute->name});
    }
]
FORMAT;
};


// render HTML in detail view
$attrAsHtml = function ($attribute) {
    return <<<FORMAT
[
    'format'    => 'html',
    'attribute' => '{$attribute->name}',
    'value'=> html_entity_decode(\$model->{$attribute->name})
]
FORMAT;
};


// See also https://github.com/schmunk42/yii2-giiant/blob/master/crud/providers/CallbackProvider.php
