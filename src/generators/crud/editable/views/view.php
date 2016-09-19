<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/*
 * @var yii\web\View $this
 * @var schmunk42\giiant\generators\crud\Generator $generator
 */

/** @var \yii\db\ActiveRecord $model */
$model = new $generator->modelClass();
$model->setScenario('crud');
$modelName = StringHelper::basename($model::className());

$className = $model::className();

$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->getTableSchema()->columnNames;
}

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use dmstr\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\Pjax;
use kartik\editable\Editable;
use kartik\grid\GridView;
use kartik\grid\EditableColumn;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */
$copyParams = $model->attributes;

$this->title = Yii::t('<?= $generator->messageCategory ?>', '<?= StringHelper::basename($className) ?>');
$this->params['breadcrumbs'][] = ['label' => Yii::t('<?= $generator->messageCategory ?>', '<?=Inflector::pluralize(StringHelper::basename($className)) ?>'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => (string) $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('View') ?>;
?>
<div class="giiant-crud <?= Inflector::camel2id(StringHelper::basename($generator->modelClass), '-', true) ?>-view">

    <!-- flash message -->
    <?= "<?php if (\\Yii::\$app->session->getFlash('deleteError') !== null) : ?>
        <span class=\"alert alert-info alert-dismissible\" role=\"alert\">
            <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
            <span aria-hidden=\"true\">&times;</span></button>
            <?= \\Yii::\$app->session->getFlash('deleteError') ?>
        </span>
    <?php endif; ?>" ?>


    <h1>
        <?= "<?= Yii::t('{$generator->messageCategory}', '{$modelName}') ?>" ?>
        <small>
            <?= '<?= $model->'.$generator->getModelNameAttribute($generator->modelClass).' ?>' ?>
        </small>
    </h1>


    <div class="clearfix crud-navigation">

        <!-- menu buttons -->
        <div class='pull-left'>
            <?= '<?= ' ?>Html::a(
            '<span class="glyphicon glyphicon-pencil"></span> ' . <?= $generator->generateString('Edit') ?>,
            [ 'update', <?= $urlParams ?>],
            ['class' => 'btn btn-info']) ?>

            <?= '<?= ' ?>Html::a(
            '<span class="glyphicon glyphicon-copy"></span> ' . <?= $generator->generateString('Copy') ?>,
            ['create', <?= $urlParams ?>, '<?= StringHelper::basename($generator->modelClass) ?>'=>$copyParams],
            ['class' => 'btn btn-success']) ?>

            <?= '<?= ' ?>Html::a(
            '<span class="glyphicon glyphicon-plus"></span> ' . <?= $generator->generateString('New') ?>,
            ['create'],
            ['class' => 'btn btn-success']) ?>
        </div>

        <div class="pull-right">
            <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-list"></span> '
            . <?= $generator->generateString('Full list') ?>, ['index'], ['class'=>'btn btn-default']) ?>
        </div>

    </div>

    <hr />

    <?php
    echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
    ?>

    <?= $generator->partialView('detail_prepend', $model); ?>

    <?= '<?= ' ?>DetailView::widget([
    'model' => $model,
    'attributes' => [
    <?php
    foreach ($safeAttributes as $attribute) {
        
        //skip primeary key
        if($model->isPrimaryKey([$attribute])){
            continue;
        }        
        
        $format = $generator->attributeEditable($attribute);
        if (!$format) {
            continue;
        } else {
            echo $format.",\n";
        }
    }
    ?>
    ],
    ]); ?>

    <?= $generator->partialView('detail_append', $model); ?>

    <hr/>

    <?= '<?= ' ?>Html::a('<span class="glyphicon glyphicon-trash"></span> ' . <?= $generator->generateString(
        'Delete'
    ) ?>, ['delete', <?= $urlParams ?>],
    [
    'class' => 'btn btn-danger',
    'data-confirm' => '' . <?= $generator->generateString('Are you sure to delete this item?') ?> . '',
    'data-method' => 'post',
    ]); ?>
    <?= "<?php \$this->endBlock(); ?>\n\n"; ?>

    <?php

    // get relation info $ prepare add button
    $model = new $generator->modelClass();

    $relItems = [];
    foreach ($generator->getModelRelations($generator->modelClass, ['has_many']) as $name => $relation) {
        echo "\n<?php \$this->beginBlock('$name'); ?>\n";

        // render pivot grid
        if ($relation->via !== null) {
            // TODO: $pivotName
            $pivotName = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
            $pivotRelation = $model->{'get'.$pivotName}();
            $pjaxId = "pjax-{$pivotName}";
            $gridRelation = $pivotRelation;
            $gridName = $pivotName;
        } else {
            $pjaxId = "pjax-{$name}";
            $gridRelation = $relation;
            $gridName = $name;
        }        
        
        $gridModel = new $gridRelation->modelClass();        
        
        $showAllRecords = false;

        if ($relation->via !== null) {
            $pivotName = Inflector::pluralize($generator->getModelByTableName($relation->via->from[0]));
            $pivotRelation = $model->{'get'.$pivotName}();
            $pivotPk = key($pivotRelation->link);

            $addButton = "  <?= Html::a(
            '<span class=\"glyphicon glyphicon-link\"></span> ' . ".$generator->generateString('Attach')." . ' ".
                Inflector::singularize(Inflector::camel2words($name)).
                "', ['".$generator->createRelationRoute($pivotRelation, 'create')."', '".
                Inflector::singularize($pivotName)."'=>['".key(
                    $pivotRelation->link
                )."'=>\$model->{$model->primaryKey()[0]}]],
            ['class'=>'btn btn-info btn-xs']
        ) ?>\n";
        } else {
            $addButton = '';
        }

        // HEADER, relation list, add, create buttons
        $headerLabel = Inflector::camel2words($name);        
        echo "
            <div class=\"clearfix crud-navigation\">
                <div class=\"pull-left\">
                    <h2>".$headerLabel."</h2>
                </div>        
                <div class=\"pull-right\">
                     <div class=\"btn-group\">
                        <?= Html::a(
                        '<span class=\"glyphicon glyphicon-list\"></span> ' . ".$generator->generateString('List All')." . ' ".
                        Inflector::camel2words($name)."',
                        ['".$generator->createRelationRoute($relation, 'index')."'],
                        ['class'=>'btn text-muted btn-xs']
                        ) ?>
                        <?= Html::a(
                            '<span class=\"glyphicon glyphicon-plus\"></span> ' . ".$generator->generateString('New').",
                            ['".$generator->createRelationRoute($relation, 'create')."', '".
                            $gridModel->formName()."' => ['".key($relation->link)."' => \$model->".$model->primaryKey()[0]."]],
                            ['class'=>'btn btn-success btn-xs']
                        ); 
                        ?>
                        <?= Html::a(
                            '<span class=\"glyphicon glyphicon-plus\"></span> ' . ".$generator->generateString('Add row').",
                            ['".$generator->createRelationRoute($relation, 'create-for-rel')."', '".
                            $gridModel->formName()."' => ['".key($relation->link)."' => \$model->".$model->primaryKey()[0]."]],
                            ['class'=>'btn btn-success btn-xs']
                        );?>
                        " . $addButton . " 
                    </div>
                </div>
            </div>\n";

        $output = $generator->relationGridEditable($gridName, $gridRelation, $showAllRecords);

        // render relation grid
        if (!empty($output)):
            echo "<?php Pjax::begin(['id'=>'pjax-{$name}', 'enableReplaceState'=> false, 'linkSelector'=>'#pjax-{$name} ul.pagination a, th a', 'clientOptions' => ['pjax:success'=>'function(){alert(\"yo\")}']]) ?>\n";
            echo '    <div class="table-responsive">'.PHP_EOL;    
            echo '        <?php '.$output."?>\n";
            echo '    </div>'.PHP_EOL;
            echo "<?php Pjax::end() ?>\n";
        endif;

        echo "<?php \$this->endBlock() ?>\n\n";

        $relItems[] = [
            'block_name' => $name,
        ];
    }
    ?>
    <div class="row">
        <div class="col-md-4">
            <?='<?'?>=$this->blocks['<?=$generator->modelClass?>']?>
        </div>
<?php 
    foreach($relItems as $item){
?>
        <div class="col-md-8">
            <?='<?'?>=$this->blocks['<?=$item['block_name']?>']?>
        </div>
<?php
    }
?>
    </div>    
</div>
