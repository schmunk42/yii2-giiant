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
                    $code                        = <<<EOS
\$form->field(\$model, '{$column->name}')->dropDownList(
    \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(),'id','{$this->generator->getNameAttribute()}'),
    ['prompt'=>'Choose...']    // options
);
EOS;
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
    \yii\helpers\ArrayHelper::map({$relation->modelClass}::find()->all(),'id', '{$this->generator->getNameAttribute()}'),
    ['prompt'=>'Choose...', 'options'=>['multiple'=>true]]    // options
);
EOS;

                return <<<EOS
'<div class="alert alert-warning">Select field not implemented yet.</div>'
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
                // TODO: move to closure?
                case ($attr == 'customer_id' && $relation->modelClass != 'schmunk42\sakila\models\Customer'):
                    $columns[] = 'customer.last_name';
                    break;
                case ($attr == 'inventory_id' && $relation->modelClass != 'schmunk42\sakila\models\Inventory'):
                    $columns[] = 'inventory.film.title';
                    break;
                case ($attr == 'film_id' && $relation->modelClass != 'schmunk42\sakila\models\Film'):
                    $columns[] = 'film.title';
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
        $code = '<div class="alert alert-info">Showing related records.</div>';
        #
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
        return $code;
    }

    public function generateRelationTo($relation)
    {
        $class = new \ReflectionClass($relation->modelClass);
        $route = Inflector::variablize($class->getShortName());
        return $route;
    }

}