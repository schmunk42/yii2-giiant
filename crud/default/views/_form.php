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

        <?php foreach ($generator->getModelRelations(['has_many','many_many']) as $name => $relation) {



           /* #var_dump($name,$relation->primaryModel->tableSchema->primaryKey);
            echo "<pre>";
            $relatedModel = Yii::createObject($relation->modelClass);
            echo \yii\helpers\VarDumper::dump($name);
            #echo \yii\helpers\VarDumper::dump($relatedModel->tableSchema->foreignKeys);
            echo "</pre>";

            echo "<pre>";
            echo \yii\helpers\VarDumper::dump($relation->via);
            #echo \yii\helpers\VarDumper::dump($relation->primaryModel->tableSchema->name);
            echo "</pre><hr/>";*/

            // ignore pivot tables and belongs to relations
            if (!$relation->multiple) {
                #continue; # TODO
            }
            if (!$relation->via || !$relation->multiple) {
                #continue; # TODO
            }

            echo "<?php \$this->beginBlock('$name'); ?>\n";

            # TODO
            echo "<?php echo " . $generator->generateRelationField([$relation, $name]) . " ?>";
            echo $generator->generateRelationGrid([$relation, $name], $model->isNewRecord)."\n";
            echo "<?php \$this->endBlock(); ?>";

            $items .= <<<EOS
[
    'label'   => '$name',
    'content' => \$this->blocks['$name'],
    'active'  => false,
],
EOS;
        } ?>

        <?=
        "<?=
    \yii\bootstrap\Tabs::widget(
                 [
                     'items' => [ $items ]
                 ]
    );
    ?>";
        ?>

        <hr/>

        <div class="form-group">
            <?= "<?= " ?>Html::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => $model->isNewRecord ?
            'btn btn-primary' : 'btn btn-primary']) ?>
        </div>

        <?= "<?php " ?>ActiveForm::end(); ?>

    </div>
