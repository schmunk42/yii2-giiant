<?php
/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\gii\generators\form\Generator $generator
 */

echo $form->field($generator, 'tableName');
echo $form->field($generator, 'tablePrefix');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'db');
echo $form->field($generator, 'generateRelations')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
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
