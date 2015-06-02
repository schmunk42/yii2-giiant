<?php

\Yii::$container->set(
    'schmunk42\giiant\crud\providers\CallbackProvider',
    [
        'columnFormats'    => [
            // hide system fields, but not the ID
            'created_at$|updated_at$' => $remove,
            '.*'                      => $removeIfText,
            'file_id$'                => $attrAsImage,
            '_html$'                  => $columnAsHtml,
        ],
        'activeFields'     => [
            'id$|created_at$|updated_at$' => $remove,
        ],
        'attributeFormats' => [
            '_html$' => $attrAsHtml,
        ],

    ]
);
