<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/*
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$model->setScenario('crud');
$modelName = StringHelper::basename($model::className());


echo "<?php\n";
?>

use yii\helpers\Html;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var string $relAttributes relation fields names for disabling
*/
if(!isset($relAttributes)){
    $relAttributes = false;
}

$this->title = <?= $generator->generateString('Create') ?>;
$this->params['breadcrumbs'][] = ['label' => Yii::t('<?= $generator->messageCategory ?>', '<?=Inflector::pluralize(StringHelper::basename($model::className())) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-create">

    <h1>
        <?= "<?= Yii::t('{$generator->messageCategory}', '{$modelName}') ?>" ?>
        <small>
            <?php $label = StringHelper::basename($generator->modelClass); ?>
            <?= '<?= $model->'.$generator->getModelNameAttribute($generator->modelClass).' ?>' ?>
        </small>
    </h1>

    <div class="clearfix crud-navigation">
        <div class="pull-left">
            <?= '<?= ' ?>
            Html::a(
            <?= $generator->generateString('Cancel') ?>,
            \yii\helpers\Url::previous(),
            ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <hr />

    <?= '<?= ' ?>$this->render('_form', [
        'model' => $model,
        'relAttributes' => $relAttributes,
    ]); ?>

</div>
