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
    public function generateActiveField($attribute)
    {
        $column   = $this->generator->getTableSchema()->columns[$attribute];
        $relation = $this->generator->getRelationByColumn($column);
        if ($relation) {
            switch (true) {
                case (!$relation->multiple):
                    // $name = $this->generator->getNameAttribute(get_class($relation->primaryModel));
                    $pk   = 'id'; // TODO - fix detection, see generateAttribute...
                    $name = 'id'; // TODO - fix line above for many many relations (crud of pivot table)
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

    public function generateAttributeFormat($column)
    {
        $relation = $this->generator->getRelationByColumn($column);
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


    // TODO: params is an array, because we need the name, improve params
    public function generateRelationGrid($data)
    {
        $name           = $data[1];
        $relation       = $data[0];
        $showAllRecords = isset($data[2]) ? $data[2] : false;
        $model          = new $relation->modelClass;
        $counter        = 0;
        $columns        = '';
        foreach ($model->attributes AS $attr => $value) {
            if ($counter > 5) {
                continue;
            }
            if (!isset($model->tableSchema->columns[$attr])) {
                continue; // virtual attributes
            }
            $code = $this->generator->generateColumnFormat($model->tableSchema->columns[$attr]);
            if ($code == false) {
                continue;
            }
            $columns .= $code . ",\n";
            $counter++;
        }
        $reflection   = new \ReflectionClass($relation->modelClass);
        $actionColumn = [
            'class'      => 'yii\grid\ActionColumn',
            'controller' => $this->generator->pathPrefix . Inflector::camel2id($reflection->getShortName(), '-', true)
        ];
        $columns .= var_export($actionColumn, true) . ",";

        # TODO: move provider generation to controller
        #$isRelation = true;
        $query = $showAllRecords ?
            "'query' => \\{$relation->modelClass}::find()" :
            "'query' => \$model->get{$name}()";

        $code = '';
        $code .= <<<EOS
<?=
\\yii\\grid\\GridView::widget([
    'dataProvider' => new \\yii\\data\\ActiveDataProvider([{$query}, 'pagination' => ['pageSize' => 5]]),
    'columns' => [$columns]
]);
?>
EOS;
        #$code .= '<div class="alert alert-info">Showing related records.</div>';
        return $code;
    }


}