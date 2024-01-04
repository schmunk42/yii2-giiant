<?php

use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 * @var \yii\db\ActiveRecord $model
 * @var array $safeAttributes
 */

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\StringHelper;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
* @var yii\widgets\ActiveForm $form
*/

?>

<div class="<?= \yii\helpers\Inflector::camel2id(
    StringHelper::basename($generator->modelClass),
    '-',
    true
) ?>-form">

    <?= '<?php ' ?>$form = ActiveForm::begin([
    'id' => '<?= $model->formName() ?>',
    'layout' => '<?= $generator->formLayout ?>',
    'enableClientValidation' => true,
    'errorSummaryCssClass' => 'error-summary alert alert-danger',
    'fieldConfig' => [
             'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
             'horizontalCssClasses' => [
                 'label' => 'col-sm-2',
                 'wrapper' => 'col-sm-8',
                 'error' => '',
                 'hint' => '',
             ],
         ],
    ]
    );
    ?>

        <?php
        $label = $generator->generateString(substr(strrchr($model::class, '\\'), 1));
        echo "<?=
    Tabs::widget(
                 [
                    'encodeLabels' => false,
                    'items' => [ 
                        [
                            'label'   => $label,
                            'content' => \$this->render('_form-fields', ['form' => \$form, 'model' => \$model]),
                            'active'  => true,
                        ]
                    ]
                 ]
    );
    ?>";
        ?>

        <hr/>

        <?= '<?php ' ?>echo $form->errorSummary($model); ?>

        <?= '<?= ' ?>Html::submitButton(
        '<span class="glyphicon glyphicon-check"></span> ' .
        ($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Save') ?>),
        [
        'id' => 'save-' . $model->formName(),
        'class' => 'btn btn-success'
        ]
        );
        ?>

        <?= '<?php ' ?>ActiveForm::end(); ?>

</div>

