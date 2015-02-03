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
use \dmstr\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/

// Cut off returnUrl from request url for only save record option
$actionUrl = Yii::$app->request->url;
if (strpos($actionUrl, 'returnUrl') !== false) {
    $actionUrl = urldecode(substr($actionUrl, 0, strpos($actionUrl, 'returnUrl') - 1));
}
?>

<div class="<?= \yii\helpers\Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
                        'id'     => '<?= $model->formName() ?>',
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
                $column   = $generator->getTableSchema()->columns[$attribute];

                $prepend = $generator->prependActiveField($column, $model);
                $field = $generator->activeField($column, $model);
                $append = $generator->appendActiveField($column, $model);

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
                    'id'    => 'save-' . $model->formName(),
                    'class' => 'btn btn-success'
                ]
            );
        ?>
        <?= "<?= " ?>(!$model->isNewRecord && \Yii::$app->request->getQueryParam('returnUrl') !== null) ? Html::submitButton(
                '<span class="glyphicon glyphicon-fast-backward"></span> ' .
                    <?= $generator->generateString('Save and go back') ?> . '',
                    ['class' => 'btn btn-primary']
                ) : null;
        ?>


        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>

</div>

<?php
    echo "<?php\n";
?>
$js = <<<JS
// get the form id and set the action url
$('#save-{$model->formName()}').on('click', function(e) {
    $('form#{$model->formName()}').attr("action","{$actionUrl}");
});
JS;
$this->registerJs($js);