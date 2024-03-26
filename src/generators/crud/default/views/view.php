<?php
/**
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\crud\Generator $generator
 * @var \yii\db\ActiveRecord $model
 */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

## TODO: move to generator (?); cleanup
$model = new $generator->modelClass();
if (array_key_exists('crud-view', $model->scenarios())) {
    $model->setScenario('crud-view');
} else {
    $model->setScenario('crud');
}
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $model->setScenario('default');
    $safeAttributes = $model->safeAttributes();
}
if (empty($safeAttributes)) {
    $safeAttributes = $model::getTableSchema()->columnNames;
}

$className = $model::class;
$modelName = Inflector::camel2words(StringHelper::basename($className));
$urlParams = $generator->generateUrlParams();
$permissions = $accessDefinitions['permissions'];

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use yii\bootstrap\Tabs;

/**
* @var yii\web\View $this
* @var <?= ltrim($generator->modelClass, '\\') ?> $model
*/

$this->title = Yii::t('<?= $generator->modelMessageCategory ?>', '<?= $modelName ?>');
$this->params['breadcrumbs'][] = ['label' => Yii::t('<?= $generator->modelMessageCategory ?>.plural', '<?= $modelName ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string)$model-><?= $generator::getModelNameAttribute($model)?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('View') ?>;
?>
<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-view">

    <h1>
        <?= '<?= Html::encode($model->' . $generator::getModelNameAttribute($generator->modelClass) . ") ?>\n" ?>
        <small><?= '<?= ' . $generator->generateString($modelName) . ' ?>' ?></small>
    </h1>

    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>

            <?php
                if ($generator->accessFilter) {
                  echo '<?php if(\Yii::$app->getUser()->can(\'' . $permissions['update']['name'] . '\')): ?>';
                }
            ?>
            <?= '<?php ' . PHP_EOL . ' echo ' ?>Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . <?= $generator->generateString('Edit ' . $modelName) ?>,
            [ 'update', <?= $urlParams ?>],
            ['class' => 'btn btn-info'])
            ?>
            <?php
            if ($generator->accessFilter) {
                echo '<?php endif ?>';
            }
            ?>

            <?php if ($generator->enableCopy): ?>
            <?php
            if ($generator->accessFilter) {
                echo '<?php if(\Yii::$app->getUser()->can(\'' . $permissions['update']['name'] . '\')): ?>';
            }
            ?>
            <?= '<?php ' . PHP_EOL . ' echo ' ?>Html::a(
            '<span class="glyphicon glyphicon-copy"></span> ' . <?= $generator->generateString('Copy ' . $modelName) ?>,
            ['create', <?= $urlParams ?>, '<?= StringHelper::basename($generator->modelClass) ?>'=> $model->hasMethod('getCopyParams') ? $model->getCopyParams() : $model->attributes],
            ['class' => 'btn btn-success'])
            ?>
            <?php
            if ($generator->accessFilter) {
                echo '<?php endif ?>';
            }
            ?>
            <?php endif ?>

            <?php
            if ($generator->accessFilter) {
                echo '<?php if(\Yii::$app->getUser()->can(\'' . $permissions['create']['name'] . '\')): ?>';
            }
            ?>
            <?= '<?php ' . PHP_EOL . ' echo ' ?>Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . <?= $generator->generateString('New ' . $modelName) ?>,
            ['create'],
            ['class' => 'btn btn-success'])
            ?>
            <?php
            if ($generator->accessFilter) {
                echo '<?php endif ?>';
            }
            ?>
        </div>

        <div class="pull-right">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-list"></span> '
            . <?= $generator->generateString('Full list') ?>, ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr/>

    <?php
    echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
    ?>

    <?= $generator->partialView('detail_prepend', $model); ?>

    <?= '<?php ' . PHP_EOL . ' echo ' ?>DetailView::widget([
    'model' => $model,
    'attributes' => [
    <?php
    foreach ($safeAttributes as $attribute) {
        $format = $generator->attributeFormat($attribute);
        if (!$format) {
            continue;
        } else {
            echo $format . ",\n";
        }
    }
    ?>
    ],
    ]);
    ?>

    <?= $generator->partialView('detail_append', $model); ?>

    <hr/>

    <?php
    if ($generator->accessFilter) {
        echo '<?php if(\Yii::$app->getUser()->can(\'' . $permissions['delete']['name'] . '\')): ?>';
    }
    ?>
    <?= '<?php ' . PHP_EOL . ' echo ' ?>Html::a('<span class="glyphicon glyphicon-trash"></span> '
    . <?= $generator->generateString(
        'Delete ' . $modelName
    ) ?>, ['delete', <?= $urlParams ?>],
    [
    'class' => 'btn btn-danger',
    'data-confirm' => '' . <?= $generator->generateString('Are you sure to delete this item?') ?> . '',
    'data-method' => 'post',
    ]);
    ?>
    <?php
    if ($generator->accessFilter) {
        echo '<?php endif ?>';
    }
    ?>
    <?= "<?php \$this->endBlock(); ?>\n\n"; ?>

    <?php

    // get relation info $ prepare add button
    $model = new $generator->modelClass();

    $items = <<<EOS
[
    'label'   => '<b>' . \Yii::t('{$generator->messageCategory}', '# {primaryKey}', ['primaryKey' => Html::encode(\$model->{$model->primaryKey()[0]})]) . '</b>',
    'content' => \$this->blocks['{$generator->modelClass}'],
    'active'  => true,
],

EOS;

    foreach ($generator->getModelRelations($generator->modelClass, ['has_many', 'has_one']) as $name => $relation) {
        echo "\n<?php \$this->beginBlock('$name'); ?>\n";

        $showAllRecords = false;
        if ($relation->via !== null) {
            $modelName = $generator->getModelByTableName($relation->via->from[0]);
            if ($generator->disablePluralization) {
                $pivotName = $name;
            } else {
                $pivotName = Inflector::pluralize($modelName);
            }
            $label = Inflector::camel2words($pivotName);
            $pivotRelation = $model->{'get' . $pivotName}();
            $pivotPk = key($pivotRelation->link);

            $addButton = "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> ' . " . $generator->generateString('Attach ' . $label) .
                ", ['" . $generator->createRelationRoute($pivotRelation, 'create') . "', '" .
                $modelName . "'=>['" . key(
                    $pivotRelation->link
                ) . "'=>\$model->{$model->primaryKey()[0]}]],
            ['class'=>'btn btn-info btn-xs']
        ) ?>\n";
        } else {
            $addButton = '';
            $label = Inflector::camel2words($name);
        }

        // relation list, add, create buttons
        echo "<div style='float:right;'>\n";

        echo "  <?php
        echo Html::a(
            '<span class=\"glyphicon glyphicon-list\"></span> ' . " . $generator->generateString('List All ' . $label) . ",
            ['" . $generator->createRelationRoute($relation, 'index') . "'],
            ['class'=>'btn text-muted btn-xs']
        ) ?>\n";
        // TODO: support multiple PKs

        // pivot check
        if ($relation->via !== null) {
            $url = "['" . $generator->createRelationRoute($relation, 'create') . "']";
        } else {
            $url = "['" . $generator->createRelationRoute($relation, 'create') . "', '" .
                Inflector::id2camel($generator->generateRelationTo($relation),
                    '-',
                    true) . "' => ['" . key($relation->link) . "' => \$model->" . $model->primaryKey()[0] . "]]";
        }

        echo "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-plus\"></span> ' . " . $generator->generateString('New ' . $label) . ",
             {$url},
            ['class'=>'btn btn-success btn-xs']
        ); ?>\n";

        echo $addButton;

        echo "</div>\n<div class='clearfix'></div>";
        // render pivot grid
        if ($relation->via !== null) {
            $pjaxId = "pjax-{$pivotName}";
            $gridRelation = $pivotRelation;
            $gridName = $pivotName;
        } else {
            $pjaxId = "pjax-{$name}";
            $gridRelation = $relation;
            $gridName = $name;
        }

        $output = $generator->relationGrid($gridName, $gridRelation, $showAllRecords);

        // render relation grid
        if (!empty($output)):
            echo "<?php Pjax::begin(['id'=>'pjax-{$name}', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-{$name} ul.pagination a, th a']) ?>\n";
            echo "<?=\n " . $output . "\n?>\n";
            echo "<?php Pjax::end() ?>\n";
        endif;

        echo "<?php \$this->endBlock() ?>\n\n";
        // build tab items
        $itemLabel = $generator->generateString($label);
        $items .= <<<EOS
[
    'content' => \$this->blocks['$name'],
    'label'   => '<small>' . $itemLabel .' <span class="badge badge-default">'. \$model->get{$name}()->count() . '</span></small>',
    'active'  => false,
],\n
EOS;
    }
    ?>

    <?=
    // render tabs
    "<?php 
        echo Tabs::widget(
                 [
                     'id' => 'relation-tabs',
                     'encodeLabels' => false,
                     'items' => [\n $items ]
                 ]
    );
    ?>";
    ?>

</div>
