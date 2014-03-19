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
    public function generateRelationField($attribute)
    {
        $column = $this->generator->getTableSchema()->columns[$attribute];
        switch (true) {
            default:
                return null;
        }
    }

    // TODO: params is an array, because we need the name
    public function generateRelationGrid($data)
    {
        $name = $data[1];
        $relation = $data[0];
        $model = new $relation->modelClass;
        $counter = 0;
        foreach($model->attributes AS $attr => $value){
            if ($counter > 5) continue;
            switch($attr){
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
        $columns[] = [
            'class' => 'yii\grid\ActionColumn',
            'controller' => Inflector::slug($reflection->getShortName())
        ];
        $c = var_export($columns, true);

        $code = <<<EOS
<?php
\$provider = new \\yii\\data\\ActiveDataProvider([
    'query' => \$model->get{$name}(),
    'pagination' => [
        'pageSize' => 5,
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