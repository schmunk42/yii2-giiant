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
        <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-plus"></span> New <?=
        Inflector::camel2words(
            StringHelper::basename($generator->modelClass)
        ) ?>', ['create'], ['class' => 'btn
        btn-success']) ?>
    </p>

    <?php
    echo "    <p class='pull-right'>\n";
    echo "        <?= Html::a('<span class=\"glyphicon glyphicon-list\"></span> List', ['index'], ['class'=>'btn btn-default']) ?>\n";
    echo "    </p><div class='clearfix'></div> \n";
    ?>

    <?php $label = StringHelper::basename($generator->modelClass); ?>

    <h3>
        <?= "<?= \$model->" . $generator->getModelNameAttribute($generator->modelClass) . " ?>" ?>
    </h3>


    <?php
    echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
    ?>

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
            $pivotName     = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
            $pivotRelation = $model->{'get' . $pivotName}();
            $pivotPk       = key($pivotRelation->link);

            $addButton = "  <?= \\yii\\helpers\\Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> Attach " .
                Inflector::singularize(Inflector::camel2words($name)) .
                "', ['" . $generator->createRelationRoute($pivotRelation, 'create') . "', '" .
                Inflector::singularize($pivotName) . "'=>['" . key(
                    $pivotRelation->link
                ) . "'=>\$model->{$model->primaryKey()[0]}]],
            ['class'=>'btn btn-info btn-xs']
        ) ?>\n";
        } else {
            $addButton = '';
        }

        // relation list, add, create buttons
        echo "<p class='pull-right'>\n";

        echo "  <?= \\yii\\helpers\\Html::a(
            '<span class=\"glyphicon glyphicon-list\"></span> List All " .
            Inflector::camel2words($name) . "',
            ['" . $generator->createRelationRoute($relation, 'index') . "'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>\n";
        // TODO: support multiple PKs, VarDumper?
        echo "  <?= \\yii\\helpers\\Html::a(
            '<span class=\"glyphicon glyphicon-plus\"></span> New " .
            Inflector::singularize(Inflector::camel2words($name)) . "',
            ['" . $generator->createRelationRoute($relation, 'create') . "', '" .
            Inflector::singularize($name) . "'=>['" . key($relation->link) . "'=>\$model->" . $model->primaryKey()[0] . "]],
            ['class'=>'btn btn-success btn-xs']
        ) ?>\n";
        echo $addButton;

        echo "</p><div class='clearfix'></div>\n";

        // render pivot grid
        if ($relation->via !== null) {
            $pjaxId       = "pjax-{$pivotName}";
            $gridRelation = $pivotRelation;
            $gridName     = $pivotName;
        } else {
            $pjaxId       = "pjax-{$name}";
            $gridRelation = $relation;
            $gridName     = $name;
        }

        // render relation grid
        echo "<?php Pjax::begin(['id'=>'pjax-{$name}','linkSelector'=>'#pjax-{$name} ul.pagination a']) ?>\n";
        echo "<?= " . $generator->relationGrid([$gridRelation, $gridName, $showAllRecords]) . "?>\n";
        echo "<?php Pjax::end() ?>\n";

        echo "<?php \$this->endBlock() ?>\n\n";

        // build tab items
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
    // render tabs
    "<?=
    \yii\bootstrap\Tabs::widget(
                 [
                     'id' => 'relation-tabs',
                     'encodeLabels' => false,
                     'items' => [ $items ]
                 ]
    );
    ?>";
    ?>
</div>
