<?php

namespace schmunk42\giiant\generators\crud\providers;

use yii\db\ColumnSchema;
use yii\helpers\Inflector;

/**
 * Class OptsProvider.
 *
 * @author Christopher Stebe <c.stebe@herzogkommunikation.de>
 */
class EditableProvider extends \schmunk42\giiant\base\Provider
{

    public $skipVirtualAttributes = false;

    /**
     * 
     *
     * @param $column ColumnSchema
     *
     * @return null|string
     */
    public function attributeEditable($attribute)
    {
       
        $this->generator->requires[] = '"kartik-v/yii2-editable": "@dev"';
        $primaryKey = implode('_',$this->generator->getTableSchema()->primaryKey);
        $column = $this->generator->getTableSchema()->columns[$attribute];
        
        switch ($column->type){
            case 'integer':
            case 'char':
            case 'string':
                $inputType = 'Editable::INPUT_TEXT';
                break;
            case 'date':
            case 'datetime':
                $inputType = 'Editable::INPUT_TEXT';
                break;
            
        }
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
                        'placeholder' => 'Enter ...'
                    ],
                    'ajaxSettings' => [
                        'url' => Url::to(['editable', '{$primaryKey}' => \$model->primaryKey]),
                    ],
                ]),
                
            ]
EOS;
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
        $controller = $this->generator->pathPrefix.  Inflector::camel2id($reflection->getShortName(), '-', true);
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
//
//        // add action column
//        $columns .= $actionColumn.",\n";

        // prepare grid column formatters
        $model->setScenario('crud');
        $safeAttributes = $model->safeAttributes();
        if (empty($safeAttributes)) {
            $safeAttributes = $model->getTableSchema()->columnNames;
        }
        foreach ($safeAttributes as $attr) {

            // max seven columns
            if ($counter > $this->generator->gridRelationMaxColumns) {
                continue;
            }

            //skip primeary key
            if($model->isPrimaryKey([$attr])){
                continue;
            }
            
            // skip virtual attributes
            if ($this->skipVirtualAttributes && !isset($model->tableSchema->columns[$attr])) {
                continue;
            }
            // don't show current model
            if (key($relation->link) == $attr) {
                continue;
            }

            $code = "
        [
            'class' => '\kartik\grid\EditableColumn',
            'attribute' => '{$attr}',
            'editableOptions' => [
                'formOptions' => [
                    'action' => [
                        '{$controller}/editable-column-update'
                    ]
                ]
            ]
        ]";
            
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
            'urlCreator' =>  
                function(\$action, \$model, \$key, \$index) {
                    \$params = is_array(\$key) ? \$key : ['id' => (string) \$key];
                    \$params[0] = '{$controller}/' . \$action;
                    \$params['{$model->formName()}'] = ['".key($relation->link)."' => \$model->id];
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
        $code = <<<EOS
GridView::widget([
//    'layout' => '{summary}{pager}<br/>{items}{pager}',
    'dataProvider' => new \\yii\\data\\ActiveDataProvider([{$query}, 'pagination' => ['pageSize' => 20, 'pageParam'=>'{$pageParam}']]),
//    'pager'        => [
//        'class'          => yii\widgets\LinkPager::className(),
//        'firstPageLabel' => {$firstPageLabel},
//        'lastPageLabel'  => {$lastPageLabel}
//    ],
    'columns' => [$columns]
])
EOS;

        return $code;
    }    


}
