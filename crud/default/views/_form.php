<?php

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

<div class="<?= \yii\helpers\Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin(['layout' => 'horizontal', 'enableClientValidation' => false]); ?>

    <div class="">
        <?php echo "<?php \$this->beginBlock('main'); ?>\n"; ?>
        <p>
            <?php foreach ($safeAttributes as $attribute) {
                $column   = $generator->getTableSchema()->columns[$attribute];
                $field = $generator->activeField($column, $model);
                if (!$field) {
                    continue;
                }
                echo "\n\t\t\t<?= " . $field . " ?>";
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

        <?= "<?= " ?>Html::submitButton('<span class="glyphicon glyphicon-check"></span> '.($model->isNewRecord ? 'Create' : 'Save'), ['class' => $model->isNewRecord ?
        'btn btn-primary' : 'btn btn-primary']) ?>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>

</div>
