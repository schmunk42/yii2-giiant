<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
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
use yii\bootstrap\ActiveForm;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/
?>

<div class="<?= \yii\helpers\Inflector::camel2id(StringHelper::basename($generator->modelClass), '-',true) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(['layout' => 'horizontal', 'enableClientValidation' => false]); ?>

    <div class="">
        <?php echo "<?php \$this->beginBlock('main'); ?>"; ?>
        <p>
            <?php foreach ($safeAttributes as $attribute) {
                echo "\t\t<?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
            } ?>
        </p>
        <?php echo "<?php \$this->endBlock(); ?>"; ?>

        <?php
        $label = substr(strrchr($model::className(), "\\"), 1);;

        $items = <<<EOS
[
    'label'   => '$label',
    'content' => \$this->blocks['main'],
    'active'  => true,
],
EOS;
        ?>

        <?php
        foreach ($generator->getModelRelations(['has_many', 'many_many']) as $name => $relation) {
            // render block
            echo "<?php \$this->beginBlock('$name'); ?>\n";
            echo $generator->generateRelationGrid([$relation, $name], $model->isNewRecord) . "\n";
            echo "<?php \$this->endBlock(); ?>";

            // prepare tab items with blocks
            $items .= <<<EOS
[
    'label'   => '<small>$name</small>',
    'content' => \$this->blocks['$name'],
    'active'  => false,
    // TODO: don't show tabs on create --> 'visible' => false,
],
EOS;

}
        ?>


        <?=
        "<?=
    \yii\bootstrap\Tabs::widget(
                 [
                   'encodeLabels' => false,
                     'items' => [ $items ]
                 ]
    );
    ?>";
        ?>

        <hr/>

        <?= "<?= " ?>Html::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => $model->isNewRecord ?
        'btn btn-primary' : 'btn btn-primary']) ?>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>

</div>
