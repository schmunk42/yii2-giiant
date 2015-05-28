<?php

use yii\helpers\ArrayHelper;
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
use \dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php $label = StringHelper::basename($generator->modelClass); ?>
        <?= "<?= \$model->" . $generator->getModelNameAttribute($generator->modelClass) . " ?>" ?>
    </div>

    <div class="panel-body">

        <div class="<?= \yii\helpers\Inflector::camel2id(
            StringHelper::basename($generator->modelClass),
            '-',
            true
        ) ?>-form">

            <?= "<?php " ?>$form = ActiveForm::begin([
            'id' => '<?= $model->formName() ?>',
            'layout' => '<?= $generator->formLayout ?>',
            'enableClientValidation' => false,
            ]
            );
            ?>

            <div class="">
                <?= "<?php " ?>echo $form->errorSummary($model); ?>
                <?php echo "<?php \$this->beginBlock('main'); ?>\n"; ?>

                <p>
                    <?php foreach ($safeAttributes as $attribute) {
                        $column = ArrayHelper::getValue($generator->getTableSchema()->columns, $attribute);

                        if ($column === null) {
                            continue;
                        }

                        $prepend = $generator->prependActiveField($column, $model);
                        $field   = $generator->activeField($column, $model);
                        $append  = $generator->appendActiveField($column, $model);

                        if ($prepend) {
                            echo "\n\t\t\t<?php " . $prepend . " ?>";
                        }
                        if ($field) {
                            echo "\n\t\t\t<?= " . $field . " ?>";
                        }
                        if ($append) {
                            echo "\n\t\t\t<?php " . $append . " ?>";
                        }
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
    Tabs::widget(
                 [
                   'encodeLabels' => false,
                     'items' => [ $items ]
                 ]
    );
    ?>";
                ?>

                <hr/>

                <?= "<?= " ?>Html::submitButton(
                '<span class="glyphicon glyphicon-check"></span> ' . ($model->isNewRecord
                ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Save') ?>),
                [
                'id' => 'save-' . $model->formName(),
                'class' => 'btn btn-success'
                ]
                );
                ?>

                <?= "<?php " ?>ActiveForm::end(); ?>

            </div>

        </div>

    </div>

</div>
