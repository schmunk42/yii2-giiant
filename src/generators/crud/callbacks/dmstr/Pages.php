<?php

namespace schmunk42\giiant\generators\crud\callbacks\dmstr;

use dmstr\modules\pages\models\Tree;
use kartik\tree\TreeViewInput;

class Pages
{
    public static function dropdown()
    {
        return function () {
            $input = TreeViewInput::className();
            $tree = Tree::className();

            return <<<CODE
\$form->field(\$model, 'request_param')->widget(
    {$input}::className(),
    [
        // single query fetch to render the tree
        'query'          => {$tree}::find()->addOrderBy('root, lft'),
        'headingOptions' => ['label' => 'Pages'],
        'model'          => \$model,         // input model
        'attribute'      => 'request_param', // input attribute
        'value'          => \$model->route,
        'asDropdown'     => true,           // will render the tree input widget as a dropdown.
        'multiple'       => false,          // set to false if you do not need multiple selection
        'fontAwesome'    => true,           // render font awesome icons
        'rootOptions'    => [
            'label' => '<i class="fa fa-tree"></i>',
            'class' => 'text-success',
        ],
        'options'        => [
            #'data-route' => (\$treeNode !== null) ? \$treeNode->route : null,
        ],
    ]
);
CODE;
        };
    }
}
