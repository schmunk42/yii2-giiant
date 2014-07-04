<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 14.03.14
 * Time: 10:21
 */

namespace schmunk42\giiant\crud\providers;

use yii\helpers\Inflector;
use yii\log\Logger;

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
                    $pk = 'id'; // TODO - fix detection
                    $name = 'id'; // TODO - fix line above for many many relations (crud of pivot table)
                    $code                        = <<<EOS
\$form->field(\$model, '{$column->name}')->dropDownList(
    \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(),'{$pk}','{$name}'),
    ['prompt'=>'Choose...']    // active field
);
EOS;
                    $code .= "echo '".print_r($relation)."'";
                    return $code;
                default:
                    return null;

            }
        }
    }

    // TODO: params
    public function generateRelationField($data)
    {
        switch (true) {
            case ($data[0]->multiple && $data[0]->via):
                $relation                    = $data[0];
                $attribute                         = key($data[0]->link);
                $code                        = <<<EOS
\$form->field(\$model, '{$attribute}')->listBox(
    \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(),'id', '{$this->generator->getNameAttribute(get_class($relation->primaryModel))}'),
    ['prompt'=>'Choose...', 'options'=>['multiple'=>true]]    // relation field
);
EOS;

                return <<<EOS
'<!--<div class="alert alert-notice">Select field not implemented yet.</div>-->'
EOS;
                break;
            default:
                return "''";
                break;

        }
    }

    // TODO: params is an array, because we need the name, improve params
    public function generateRelationGrid($data)
    {
        $name     = $data[1];
        $relation = $data[0];
        $model    = new $relation->modelClass;
        $counter  = 0;
        foreach ($model->attributes AS $attr => $value) {
            if ($counter > 5) {
                continue;
            }
            switch ($attr) {
                case 'last_update':
                    continue 2;
                    break;
                default:
                    $columns[] = $attr;
                    break;
            }

            $counter++;
        }
        $reflection = new \ReflectionClass($relation->modelClass);
        $columns[]  = [
            'class'      => 'yii\grid\ActionColumn',
            'controller' => $this->generator->pathPrefix . Inflector::camel2id($reflection->getShortName())
        ];
        $c          = var_export($columns, true);

        # TODO: move provider generation to controller
        $isRelation = true;
        $query = $isRelation?"'query' => \$model->get{$name}(),":"'query' => \\{$relation->modelClass}::find(),";

        $code = '';
        $code .= <<<EOS
<?php
\$provider = new \\yii\\data\\ActiveDataProvider([
    $query
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>
    <?= \\yii\\grid\\GridView::widget([
            'dataProvider' => \$provider,
            'columns' => $c
        ]); ?>
EOS;
        $code .= '<div class="alert alert-info">Showing related records.</div>';
        return $code;
    }

    public function generateRelationTo($relation)
    {
        $class = new \ReflectionClass($relation->modelClass);
        $route = Inflector::variablize($class->getShortName());
        return $route;
    }

}