<?php

use schmunk42\giiant\generators\model\Generator;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator yii\gii\generators\form\Generator */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'tablePrefix');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'generateRelations')->dropDownList([
    Generator::RELATIONS_NONE => Yii::t('yii', 'No relations'),
    Generator::RELATIONS_ALL => Yii::t('yii', 'All relations'),
    Generator::RELATIONS_ALL_INVERSE => Yii::t('yii', 'All relations with inverse'),
]);
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'generateHintsFromComments')->checkbox();
echo $form->field($generator, 'generateModelClass')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'singularEntities')->checkbox();
echo $form->field($generator, 'messageCategory');

?>

<div class="panel panel-default">
    <div class="panel-heading">Translatable Behavior</div>
    <div class="panel-body">
        <?php
        echo $form->field($generator, 'useTranslatableBehavior')->checkbox();
        echo $form->field($generator, 'languageTableName');
        echo $form->field($generator, 'languageCodeColumn');
        ?>
        <div class="alert alert-warning" role="alert">
            <h4>Attention!</h4>

            <p>
                You must run <code>php composer.phar require 2amigos/yii2-translateable-behavior "*"</code> to
                install this package.
            </p>
        </div>
    </div>
</div>
