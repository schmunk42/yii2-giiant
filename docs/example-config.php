<?php

namespace app\config;

use schmunk42\giiant\generators\crud\callbacks\base\Callback;
use schmunk42\giiant\generators\crud\callbacks\yii\Db;
use schmunk42\giiant\generators\crud\callbacks\yii\Html;

$aceEditorField = function ($attribute, $model, $generator) {
    return "\$form->field(\$model, '{$attribute}')->widget(\\trntv\\aceeditor\\AceEditor::className())";
};

\Yii::$container->set(
    \schmunk42\giiant\generators\crud\providers\core\CallbackProvider::class,
    [
        'columnFormats' => [
            // hide system fields, but not ID in table
            'created_at$|updated_at$' => Callback::false(),
            // hide all TEXT or TINYTEXT columns
            '.*' => Db::falseIfText(),
        ],
        'activeFields' => [
            // hide system fields in form
            'id$' => Db::falseIfAutoIncrement(),
            'id$|created_at$|updated_at$' => Callback::false(),
            'value' => $aceEditorField,
        ],
        'attributeFormats' => [
            // render HTML output
            '_html$' => Html::attribute(),
        ],
    ]
);

return [
    'controllerMap' => [
        'batch' => [
            'class' => 'schmunk42\giiant\commands\BatchController',
            'overwrite' => true,
            'modelNamespace' => $crudNs . '\models',
            'modelQueryNamespace' => $crudNs . '\models\query',
            'crudControllerNamespace' => $crudNs . '\controllers',
            'crudSearchModelNamespace' => $crudNs . '\models\search',
            'crudViewPath' => '@project/modules/crud/views',
            'crudPathPrefix' => '/crud/',
            'crudTidyOutput' => true,
            'crudAccessFilter' => false,
            'crudProviders' => [
                \schmunk42\giiant\generators\crud\providers\core\OptsProvider::class
            ],
            'tablePrefix' => 'project_',
            'skipTables' => [
                'app_audit_data',
                'app_migration',
                'app_audit_entry',
                'app_audit_error',
                'app_audit_javascript',
                'app_audit_mail',
                'app_audit_trail',
                'app_auth_assignment',
                'app_auth_item',
                'app_auth_item_child',
                'app_auth_rule',
                'app_dmstr_contact_log',
                'app_dmstr_page',
                'app_dmstr_page_translation',
                'app_hrzg_widget_content',
                'app_hrzg_widget_content_translation',
                'app_hrzg_widget_template',
                'app_html',
                'app_language',
                'app_language_source',
                'app_language_translate',
                'app_less',
                'app_profile',
                'app_session',
                'app_settings',
                'app_social_account',
                'app_token',
                'app_twig',
                'app_user',
                'dmstr_redirect',
                'filefly_hashmap',
            ],
        ]
    ],
];
