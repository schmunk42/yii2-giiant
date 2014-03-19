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

    // TODO: params
    public function generateRelationGrid($data)
    {
        $name = $data[1];
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
    <?= \\yii\\grid\\GridView::widget(['dataProvider' => \$provider,]); ?>
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