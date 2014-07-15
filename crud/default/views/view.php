<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var schmunk42\giiant\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = '<?=
Inflector::camel2words(
    StringHelper::basename($generator->modelClass)
) ?> View ' . $model-><?= $generator->getNameAttribute() ?> . '';
$this->params['breadcrumbs'][] = ['label' => '<?=
Inflector::pluralize(
    Inflector::camel2words(StringHelper::basename($generator->modelClass))
) ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?=
$generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = 'View';
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-view">

    <p class='pull-left'>
        <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-pencil"></span> Edit', ['update', <?= $urlParams ?>],
        ['class' => 'btn btn-info']) ?>
        <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-plus"></span> New', ['create'], ['class' => 'btn
        btn-success']) ?>
    </p>

    <?php
    echo "    <p class='pull-right'>\n";
    echo "        <?= Html::a('<span class=\"glyphicon glyphicon-list\"></span> List', ['index'], ['class'=>'btn btn-default']) ?>\n";
    echo "    </p><div class='clearfix'></div> \n";
    ?>

    <?php $label = StringHelper::basename($generator->modelClass); ?>

    <?php
    echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
    ?>

    <h3>
        <?= "<?= \$model->" . $generator->getModelNameAttribute($generator->modelClass) . " ?>" ?>
    </h3>

    <?= "<?php " ?>echo DetailView::widget([
    'model' => $model,
    'attributes' => [
    <?php
    foreach ($generator->getTableSchema()->columns as $column) {
        $format = trim($generator->attributeFormat($column));
        if ($format === false) {
            continue;
        }
        echo $format . ",\n";
    }
    ?>
    ],
    ]); ?>

    <hr/>

    <?= "<?php " ?>echo Html::a('<span class="glyphicon glyphicon-trash"></span> Delete', ['delete', <?= $urlParams ?>],
    [
    'class' => 'btn btn-danger',
    'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
    'data-method' => 'post',
    ]); ?>

    <?php echo "<?php \$this->endBlock(); ?>\n\n"; ?>

    <?php
    $items = <<<EOS
[
    'label'   => '<span class="glyphicon glyphicon-asterisk"></span> $label',
    'content' => \$this->blocks['{$generator->modelClass}'],
    'active'  => true,
],
EOS;

    foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {

        echo "\n<?php \$this->beginBlock('$name'); ?>\n";

        // get relation info $ prepare add button
        $model          = new $generator->modelClass;
        $showAllRecords = false;
        if ($relation->via !== null) {
            $pivotName     = Inflector::pluralize(
                Inflector::id2camel(str_replace('app_', '', $relation->via->from[0]), '_')
            );
            $pivotRelation = $model->{'get' . $pivotName}();
            $pivotPk       = key($pivotRelation->link);

            $addButton = "  <?= \\yii\\helpers\\Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> Attach " .
                Inflector::singularize(Inflector::camel2words($name)) .
                "', ['" . $generator->createRelationRoute($pivotRelation, 'create') . "', '" .
                Inflector::singularize($pivotName) . "'=>['" . key($pivotRelation->link) . "'=>\$model->{$model->primaryKey()[0]}]],
            ['class'=>'btn btn-primary btn-xs']
        ) ?>\n";
        } else {
            $addButton = '';
        }

        echo "<p class='pull-left'>\n";
        echo $addButton;
        echo "</p>\n";


        // relation list, add, create buttons
        echo "<p class='pull-right'>\n";

        // TODO: support multiple PKs, VarDumper?
        echo "  <?= \\yii\\helpers\\Html::a(
            'New " . Inflector::singularize(Inflector::camel2words($name)) . "',
            ['" . $generator->createRelationRoute($relation, 'create') . "', '" .
            Inflector::singularize($name) . "'=>['" . key($relation->link) . "'=>\$model->" . $model->primaryKey()[0] . "]],
            ['class'=>'btn btn-default btn-xs']
        ) ?>\n";
        echo "  <?= \\yii\\helpers\\Html::a(
            'List All " . Inflector::camel2words($name) . "',
            ['" . $generator->createRelationRoute($relation, 'index') . "'],
            ['class'=>'btn btn-default btn-xs']
        ) ?>\n";

        echo "</p><div class='clearfix'></div>\n";

        // render pivot grid
        if ($relation->via !== null) {
            echo "<?php Pjax::begin() ?>\n";
            echo "<?=" . $generator->relationGrid([$pivotRelation, $pivotName, $showAllRecords]) . "?>\n";
            $showAllRecords = true;
            echo "<?php Pjax::end() ?>\n";
        } else {
            echo "<?php Pjax::begin() ?>\n"; // TODO add linkSelector for PJAX (pagination only)
            // render relation grid
            echo "<?= " . $generator->relationGrid([$relation, $name, $showAllRecords]) . "?>\n";
            echo "<?php Pjax::end() ?>\n";
        }

        echo "<?php \$this->endBlock() ?>\n\n";

        $label = Inflector::camel2words($name);
        $items .= <<<EOS
[
    'label'   => '<small><span class="glyphicon glyphicon-paperclip"></span> $label</small>',
    'content' => \$this->blocks['$name'],
    'active'  => false,
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
</div>

