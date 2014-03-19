<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View                      $this
 * @var yii\gii\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass;
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ?
        'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <div class="row">
        <div class="col-md-7">
            <?php foreach ($safeAttributes as $attribute) {
                echo "\t\t<?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
            } ?>
        </div>
        <div class="col-md-3">
            <?php foreach ($generator->getModelRelations() as $name => $relation) {
                echo "<h3><?= \\yii\\helpers\\Html::a('$name', ['".$generator->generateRelationTo($relation)."/index']) ?></h3>";
            } ?>
        </div>
    </div>

    <div class="form-group">
        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ?
        'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
