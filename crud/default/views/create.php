<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

echo "<?php\n";
?>

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = 'Create';
$this->params['breadcrumbs'][] = ['label' => '<?= Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-create">

    <p class="pull-left">
        <?= "<?= " ?>Html::a('Cancel', \yii\helpers\Url::previous(), ['class' => 'btn btn-default']) ?>
    </p>
    <div class="clearfix"></div>

    <?= "<?php " ?>echo $this->render('_form', [
    'model' => $model,
    ]); ?>

</div>
