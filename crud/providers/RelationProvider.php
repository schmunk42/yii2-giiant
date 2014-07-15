<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace schmunk42\giiant\crud\providers;

use yii\helpers\Inflector;

class RelationProvider extends \schmunk42\giiant\base\Provider
{
    public function activeField($column)
    {
        $relation = $this->generator->getRelationByColumn($this->generator->modelClass, $column);
        if ($relation) {
            switch (true) {
                case (!$relation->multiple):
                    $pk   = key($relation->link);
                    $name = $this->generator->getModelNameAttribute($relation->modelClass);
                    $code = <<<EOS
\$form->field(\$model, '{$column->name}')->dropDownList(
    \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(),'{$pk}','{$name}'),
    ['prompt'=>'Choose...']    // active field
);
EOS;
                    return $code;
                default:
                    return null;

            }
        }
    }

    public function attributeFormat($column)
    {
        $relation = $this->generator->getRelationByColumn($this->generator->modelClass, $column);
        if ($relation) {
            if ($relation->multiple) {
                return null;
            }
            $title          = $this->generator->getModelNameAttribute($relation->modelClass);
            $route          = $this->generator->createRelationRoute($relation, 'view');
            $relationGetter = 'get' . Inflector::id2camel(
                    str_replace('_id', '', $column->name),
                    '_'
                ) . '()'; // TODO: improve detection
            $code           = <<<EOS
[
    'format'=>'html',
    'attribute'=>'$column->name',
    'value' => Html::a(\$model->{$relationGetter}->one()?\$model->{$relationGetter}->one()->{$title}:'', ['{$route}', 'id' => \$model->{$column->name}]),
]
EOS;
            return $code;
        }
    }

    public function columnFormat($column, $model)
    {
        $relation = $this->generator->getRelationByColumn($model, $column);
        if ($relation) {
            if ($relation->multiple) {
                return null;
            }
            $title          = $this->generator->getModelNameAttribute($relation->modelClass);
            $route          = $this->generator->createRelationRoute($relation, 'view');
            $relationGetter = 'get' . Inflector::id2camel(
                    str_replace('_id', '', $column->name),
                    '_'
                ) . '()'; // TODO: improve detection

            $pk   = key($relation->link);
            $code = <<<EOS
[
            "class" => yii\\grid\\DataColumn::className(),
            "attribute" => "{$column->name}",
            "value" => function(\$model){
                if (\$rel = \$model->{$relationGetter}->one()) {
                    return yii\helpers\Html::a(\$rel->{$title},["{$route}","id" => \$rel->{$pk}],["data-pjax"=>0]);
                } else {
                    return '';
                }
            },
            "format" => "raw",
]
EOS;
            return $code;
        }
    }


    // TODO: params is an array, because we need the name, improve params
    public function relationGrid($data)
    {
        $name           = $data[1];
        $relation       = $data[0];
        $showAllRecords = isset($data[2]) ? $data[2] : false;
        $model          = new $relation->modelClass;
        $counter        = 0;
        $columns        = '';

        foreach ($model->attributes AS $attr => $value) {
            if ($counter > 8) {
                continue;
            }
            if (!isset($model->tableSchema->columns[$attr])) {
                continue; // virtual attributes
            }

            $code = $this->generator->columnFormat($model->tableSchema->columns[$attr], $model);
            if ($code == false) {
                continue;
            }
            $columns .= $code . ",\n";
            $counter++;
        }

        $reflection = new \ReflectionClass($relation->modelClass);
        if (!$this->generator->isPivotRelation($relation)) {
            $template          = '{view} {update}';
            $deleteButtonPivot = '';
        } else {
            $template          = '{view} {delete}';
            $deleteButtonPivot = <<<EOS
'delete' => function (\$url, \$model) {
                return Html::a('<span class="glyphicon glyphicon-remove"></span>', \$url, [
                    'class' => 'text-danger',
                    'title' => Yii::t('yii', 'Remove'),
                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete the related item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]);
            },
EOS;
        }

        $controller   = $this->generator->pathPrefix . Inflector::camel2id($reflection->getShortName(), '-', true);
        $actionColumn = <<<EOS
[
    'class'      => 'yii\grid\ActionColumn',
    'template'   => '$template',
    'buttons'    => [
        $deleteButtonPivot
    ],
    'controller' => '$controller'
]
EOS;
        $columns .= $actionColumn . ",";

        $query = $showAllRecords ?
            "'query' => \\{$relation->modelClass}::find()" :
            "'query' => \$model->get{$name}()";
        $code  = '';
        $code .= <<<EOS
\\yii\\grid\\GridView::widget([
    'dataProvider' => new \\yii\\data\\ActiveDataProvider([{$query}, 'pagination' => ['pageSize' => 10]]),
    'columns' => [$columns]
]);
EOS;
        return $code;
    }


}