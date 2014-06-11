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
                    $this->generator->requires[] = '2amigos/yii2-selectize-widget';
                    return <<<EOS
'<label>{$column->name}</label>'.\dosamigos\selectize\Selectize::widget([
    'model' => \$model,
    'attribute' => '$column->name',
    'clientOptions' => [
        'delimiter' => ',',
        'plugins' => ['remove_button'],
        'persist' => false,
        'create' => new \yii\web\JsExpression('function(input){
            return {value: input, text: input};
        }'),
    ]
])
EOS;
                default:
                    return null;

            }
        }
    }

    // TODO: params
    public function generateRelationField($data)
    {
        #$column = $this->generator->getTableSchema()->columns[$attribute];
        switch (true) {
            case ($data[0]->multiple && $data[0]->via):
            #case (true):
                $this->generator->requires[] = '2amigos/yii2-selectize-widget';
                $attribute = reset($data[0]->link);
                $relatedClass = lcfirst($data[1]);
                return <<<EOS
'<label>Relation</label>'.\dosamigos\selectize\Selectize::widget([
    #'model' => \$model->{$relatedClass},
    'name' => '{$attribute}',
    'clientOptions' => [
        'delimiter' => ',',
        'plugins' => ['remove_button'],
        'persist' => false,
        'create' => new \yii\web\JsExpression('function(input){
            return {value: input, text: input};
        }'),
    ]
])
EOS;
                break;
            default:
                return "''";
            break;

        }
    }

    // TODO: params is an array, because we need the name
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
            'controller' => Inflector::slug($reflection->getShortName())
        ];
        $c          = var_export($columns, true);

        $code = <<<EOS
<?php
\$provider = new \\yii\\data\\ActiveDataProvider([
    'query' => \$model->get{$name}(),
    'pagination' => [
        'pageSize' => 10,
    ],
]);
?>
<?php if(\$provider->count != 0): ?>
    <?= \\yii\\grid\\GridView::widget([
            'dataProvider' => \$provider,
            'columns' => $c
        ]); ?>
<?php endif; ?>
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