<?php

namespace schmunk42\giiant\generators\crud\providers\extensions;

use yii\db\ColumnSchema;
use yii\helpers\Inflector;

/**
 * Class EditableProvider.
 *
 * @author Uldis Nelsons
 */
class EditableProvider extends \schmunk42\giiant\base\Provider
{
    public $skipVirtualAttributes = false;

    /**
     * @param $column ColumnSchema
     *
     * @return null|string
     */
    public function attributeEditable($attribute)
    {
        $this->generator->requires[] = '"kartik-v/yii2-editable": "@dev"';
        $primaryKey = implode('_', $this->generator->getTableSchema()->primaryKey);
        $column = $this->generator->getTableSchema()->columns[$attribute];

        /*
         * search opts... method
         */
        $modelClass = $this->generator->modelClass;
        $optsFunc = 'opts'.str_replace('_', '', $attribute);
        $optsCamelFunc = 'opts'.str_replace(' ', '', ucwords(implode(' ', explode('_', $attribute))));

        $useOptsFunc = false;
        if (method_exists($modelClass::className(), $optsFunc)) {
            $useOptsFunc = $optsFunc;
        } elseif (method_exists($modelClass::className(), $optsCamelFunc)) {
            $useOptsFunc = $optsCamelFunc;
        }

        $inputType = $this->getInputType($column);
        $relation = $this->generator->getRelationByColumn($this->generator->modelClass, $column);
        if ($relation) {
            $relModelStatic = $relation->modelClass.'Static';
        }
        if ($relation && !$relation->multiple && method_exists($relModelStatic, 'getListData')) {
            $relPk = key($relation->link);
            $relName = $this->generator->getModelNameAttribute($relation->modelClass);

            return <<<EOS
                [
                    'attribute' => '{$attribute}',
                    'format' => 'raw',
                    'value' => Editable::widget([
                        'name' => '{$attribute}',
                        'asPopover' => true,
                        'value' => \$model->{$attribute},
                        'header' => \$model->getAttributeLabel('{$attribute}'),
                        'inputType' => Editable::INPUT_LIST_BOX,
                        'size' => 'md',
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Enter ...'
                        ],
                        'ajaxSettings' => [
                            'url' => Url::to(['editable', '{$primaryKey}' => \$model->primaryKey]),
                        ],
                        'data' => {$relation->modelClass}Static::getListData(),
                        'displayValueConfig' => {$relation->modelClass}Static::getListData(true),                        
                    ]),

                ]
EOS;
        } elseif ($relation && !$relation->multiple) {
            $relPk = key($relation->link);
            $relName = $this->generator->getModelNameAttribute($relation->modelClass);

            return <<<EOS
                [
                    'attribute' => '{$attribute}',
                    'format' => 'raw',
                    'value' => Editable::widget([
                        'name' => '{$attribute}',
                        'asPopover' => true,
                        'value' => \$model->{$attribute},
                        'header' => \$model->getAttributeLabel('{$attribute}'),
                        'inputType' => Editable::INPUT_LIST_BOX,
                        'size' => 'md',
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Enter ...'
                        ],
                        'ajaxSettings' => [
                            'url' => Url::to(['editable', '{$primaryKey}' => \$model->primaryKey]),
                        ],
                        'data' => \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(), '{$relPk}', '{$relName}'),
                        'displayValueConfig' => \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(), '{$relPk}', '{$relName}'),                            
                    ]),

                ]
EOS;
        } elseif ($useOptsFunc) {
            return <<<EOS
                [
                    'attribute' => '{$attribute}',
                    'format' => 'raw',
                    'value' => Editable::widget([
                        'name' => '{$attribute}',
                        'asPopover' => true,
                        'value' => \$model->{$attribute},
                        'header' => \$model->getAttributeLabel('{$attribute}'),
                        'inputType' => Editable::INPUT_LIST_BOX,
                        'size' => 'md',
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Select ...'
                        ],
                        'ajaxSettings' => [
                            'url' => Url::to(['editable', '{$primaryKey}' => \$model->primaryKey]),
                        ],
                        'data' => {$modelClass}::{$useOptsFunc}(),
                        'displayValueConfig' => {$modelClass}::{$useOptsFunc}(),                            
                    ]),

                ]
EOS;
        } else {
            return <<<EOS
                [
                    'attribute' => '{$attribute}',
                    'format' => 'raw',
                    'value' => Editable::widget([
                        'name' => '{$attribute}',
                        'asPopover' => true,
                        'value' => \$model->{$attribute},
                        'header' => \$model->getAttributeLabel('{$attribute}'),
                        'inputType' => {$inputType},
                        'size' => 'md',
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => 'Select ...'
                        ],
                        'ajaxSettings' => [
                            'url' => Url::to(['editable', '{$primaryKey}' => \$model->primaryKey]),
                        ],
                    ]),

                ]
EOS;
        }
    }

    /**
     * Renders a grid view for a given relation.
     *
     * @param string $name grid name
     * @param $relation grid relation
     * @param bool $showAllRecords
     *
     * @return mixed|string
     */
    public function relationGridEditable($name, $relation, $showAllRecords = false)
    {
        $model = new $relation->modelClass();

        // column counter
        $counter = 0;
        $columns = '';

        if (!$this->generator->isPivotRelation($relation)) {
            // hasMany relations
            $template = '{view} {update}';
            $deleteButtonPivot = '';
        } else {
            // manyMany relations
            $template = '{view} {delete}';
            $deleteButtonPivot = <<<EOS
'delete' => function (\$url, \$model) {
                return Html::a('<span class="glyphicon glyphicon-remove"></span>', \$url, [
                    'class' => 'text-danger',
                    'title'         => {$this->generator->generateString('Remove')},
                    'data-confirm'  => {$this->generator->generateString(
                            'Are you sure you want to delete the related item?'
                    )},
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
            },
'view' => function (\$url, \$model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-cog"></span>',
                    \$url,
                    [
                        'data-title'  => {$this->generator->generateString('View Pivot Record')},
                        'data-toggle' => 'tooltip',
                        'data-pjax'   => '0',
                        'class'       => 'text-muted',
                    ]
                );
            },
EOS;
        }

        $reflection = new \ReflectionClass($relation->modelClass);
        $controller = $this->generator->pathPrefix.Inflector::camel2id($reflection->getShortName(), '-', true);
//        $actionColumn = <<<EOS
//[
//    'class'      => '{$this->generator->actionButtonClass}',
//    'template'   => '$template',
//    'contentOptions' => ['nowrap'=>'nowrap'],
//    'urlCreator' => function (\$action, \$model, \$key, \$index) {
//        // using the column name as key, not mapping to 'id' like the standard generator
//        \$params = is_array(\$key) ? \$key : [\$model->primaryKey()[0] => (string) \$key];
//        \$params[0] = '$controller' . '/' . \$action;
//        return \$params;
//    },
//    'buttons'    => [
//        $deleteButtonPivot
//    ],
//    'controller' => '$controller'
//]
//EOS;

//        // add action column
//        $columns .= $actionColumn.",\n";
        // prepare grid column formatters
        $model->setScenario('crud');
        $safeAttributes = $model->safeAttributes();
        if (empty($safeAttributes)) {
            $safeAttributes = $model->getTableSchema()->columnNames;
        }
        $hasDate = false;
        foreach ($safeAttributes as $attribute) {

            // max seven columns
            if ($counter > $this->generator->gridRelationMaxColumns) {
                continue;
            }

            //skip primeary key
            if ($model->isPrimaryKey([$attribute])) {
                continue;
            }

            // skip virtual attributes
            if ($this->skipVirtualAttributes && !isset($model->tableSchema->columns[$attribute])) {
                continue;
            }
            // don't show current model
            if (key($relation->link) == $attribute) {
                continue;
            }

            /*
             * search opts... method
             */
            $optsFunc = 'opts'.str_replace('_', '', $attribute);
            $optsCamelFunc = 'opts'.str_replace(' ', '', ucwords(implode(' ', explode('_', $attribute))));

            $useOptsFunc = false;
            if (method_exists($model::className(), $optsFunc)) {
                $useOptsFunc = $optsFunc;
            } elseif (method_exists($model::className(), $optsCamelFunc)) {
                $useOptsFunc = $optsCamelFunc;
            }

            $tableColumn = $this->generator->getColumnByAttribute($attribute, $model);
            $inputType = $this->getInputType($tableColumn);
            $relRelation = $this->generator->getRelationByColumn($model->ClassName(), $tableColumn);
            if ($relRelation) {
                $relModelStatic = $relRelation->modelClass.'Static';
            }

            if ($tableColumn->type == 'date') {
                $hasDate = true;
                $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attribute}',
            'format' => 'date',                
            'editableOptions' => [
                'inputType' => \kartik\\editable\Editable::INPUT_WIDGET,
                'widgetClass' => 'kartik\datecontrol\DateControl',
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ],                            
                'options' => [
                    'type' => \kartik\datecontrol\DateControl::FORMAT_DATE,
                    'displayFormat' => \$datePattern,
                    'saveFormat' => 'php:Y-m-d',
                    'options' => [
                        'pluginOptions' => [
                            'autoclose' => true
                        ]
                    ]
                ]
            ]
        ],

        ]";
            } elseif ($relRelation && !$relRelation->multiple && method_exists($relModelStatic, 'getListData')) {
                $hasParameterForValue = false;
                $r = new \ReflectionMethod($relModelStatic, 'getListData');
                $params = $r->getParameters();
                foreach ($params as $param) {
                    if ($hasParameterForValue = ($param->getName() == 'forValue')) {
                        break;
                    }
                }
                if ($hasParameterForValue) {
                    $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attribute}',
            'editableOptions' => [
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ],
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => {$relRelation->modelClass}Static::getListData(),
                'displayValueConfig' => {$relRelation->modelClass}Static::getListData(true),
            ]
        ]";
                } else {
                    $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attribute}',
            'editableOptions' => [
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ],
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => {$relRelation->modelClass}Static::getListData(),
                'displayValueConfig' => {$relRelation->modelClass}Static::getListData(),
            ]
        ]";
                }
            } elseif ($relRelation && !$relRelation->multiple) {
                $relPk = key($relRelation->link);
                $relName = $this->generator->getModelNameAttribute($relRelation->modelClass);

                $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attribute}',
            'editableOptions' => [
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ],
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => \yii\helpers\ArrayHelper::map({$relRelation->modelClass}::find()->all(), '{$relPk}', '{$relName}'),
                'displayValueConfig' => \yii\helpers\ArrayHelper::map({$relRelation->modelClass}::find()->all(), '{$relPk}', '{$relName}'),
            ]
        ]";
            } elseif ($useOptsFunc) {
                $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attribute}',
            'editableOptions' => [
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ],
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => {$model->ClassName()}::{$useOptsFunc}(),
                'displayValueConfig' => {$model->ClassName()}::{$useOptsFunc}(),                            
            ]
        ]";
            } else {
                $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attribute}',
            'editableOptions' => [
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ],
                'inputType' => ".$inputType.'
            ]
        ]';
            }
            //$code = $this->generator->columnFormat($attr, $model);
            if ($code == false) {
                continue;
            }
            $columns .= $code.",\n";
            ++$counter;
        }

        // action column
        $columns .= "  
        [
            'class' => '\kartik\grid\ActionColumn',
            'template' => '{view} {update} {delete}',
            'urlCreator' =>  
                function(\$action, \$model, \$key, \$index) {
                    \$params = is_array(\$key) ? \$key : ['id' => (string) \$key];
                    \$params[0] = '{$controller}/' . \$action;
                    \$params['{$model->formName()}'] = ['".key($relation->link)."' => \$model->primaryKey()[0]];
                    return Url::toRoute(\$params);            
                },
        ]            
                 ";
        $query = $showAllRecords ?
                "'query' => \\{$relation->modelClass}::find()" :
                "'query' => \$model->get{$name}()";
        $pageParam = Inflector::slug("page-{$name}");
        $firstPageLabel = $this->generator->generateString('First');
        $lastPageLabel = $this->generator->generateString('Last');
        $code = '';
        if ($hasDate) {
            $code .= <<<EOS
            \$formatter = new IntlDateFormatter(\Yii::\$app->language,IntlDateFormatter::SHORT, IntlDateFormatter::NONE);            
            \$datePattern = \$formatter->getPattern();


EOS;
        }
        $code .= <<<EOS
            echo GridView::widget([
                'layout' => '{items}{pager}',
                'export' => false,                
                'dataProvider' => new \\yii\\data\\ActiveDataProvider([{$query}, 'pagination' => ['pageSize' => 20, 'pageParam'=>'{$pageParam}']]),
                'export' => false,
                'tableOptions' => [
                    'class' => 'table table-striped table-success'
                ],               

            //    'pager'        => [
            //        'class'          => yii\widgets\LinkPager::className(),
            //        'firstPageLabel' => {$firstPageLabel},
            //        'lastPageLabel'  => {$lastPageLabel}
            //    ],
                'columns' => [$columns]
            ]);
EOS;

        return $code;
    }

    public function getInputType($column)
    {
        switch ($column->type) {
            case 'double':
            case 'integer':
            case 'bigint':
            case 'smallint':
            case 'decimal':
            case 'char':
            case 'string':
                $inputType = 'Editable::INPUT_TEXT';
                break;
            case 'text':
                $inputType = 'Editable::INPUT_TEXTAREA ';
            case 'date':
            case 'datetime':
            case 'timestamp':
                $inputType = 'Editable::INPUT_TEXT';
                break;
        }
        if (!isset($inputType)) {
            return false;
            throw new \Exception('No Defined column type: '.$column->type);
        }

        return $inputType;
    }
}
