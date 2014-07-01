<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
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

$this->title = '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?> View ' . $model-><?= $generator->getNameAttribute() ?> . '';
$this->params['breadcrumbs'][] = ['label' => '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = 'View';
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

	<p class='pull-left'>
		<?= "<?= " ?>Html::a('Edit', ['update', <?= $urlParams ?>], ['class' => 'btn btn-info']) ?>
        <?= "<?= " ?>Html::a('New', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php
    echo "    <p class='pull-right'>\n";
    echo "        <?= Html::a('List', ['index'], ['class'=>'btn btn-default']) ?>\n";
    echo "    </p><div class='clearfix'></div> \n";
    ?>



    <?php $label = StringHelper::basename($generator->modelClass); ?>

    <?php
    echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>\n";
    ?>

	<?= "<?php " ?>echo DetailView::widget([
		'model' => $model,
		'attributes' => [
<?php
foreach ($generator->getTableSchema()->columns as $column) {
    #$name = $generator->generateColumnName($column);
    $format = $generator->generateColumnFormat($column);
    if($relation = $generator->getRelationByColumn($column)) {
        #echo "\t\t\t'" . $column->name . ($format === 'link' ? "" : ":" . $format) . "',\n";
        echo "['format'=>'raw','attribute'=>'$column->name', 'value'=> Html::a(\$model->{$column->name}, ['".$generator->pathPrefix.Inflector::camel2id(StringHelper::basename($relation->modelClass))."/view', 'id'=>\$model->{$column->name}])],";
    } else {
        echo "\t\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }

}
?>
		],
	]); ?>
    <?php echo "<?php \$this->endBlock(); ?>\n\n"; ?>

    <?php
    $items = <<<EOS
[
    'label'   => '$label',
    'content' => \$this->blocks['{$generator->modelClass}'],
    'active'  => true,
],
EOS;

    foreach ($generator->getModelRelations() as $name => $relation) {

        # TODO: make tab selection more flexible
        #if (!$relation->via) continue; // ignore pivot tables in CRUD
        if (!$relation->multiple) continue;

        echo "\n<?php \$this->beginBlock('$name'); ?>\n";

        echo "<?php Pjax::begin() ?>\n";
        echo $generator->generateRelationGrid([$relation, $name])."\n";
        echo "<?php Pjax::end() ?>\n";

        echo "<p class='pull-right'>\n";
        echo "  <?= \\yii\\helpers\\Html::a('List ".Inflector::camel2words($name)."', ['".$generator->pathPrefix.Inflector::camel2id($generator->generateRelationTo($relation))."/index'], ['class'=>'btn btn-default btn-xs']) ?>\n";
        echo "  <?= \\yii\\helpers\\Html::a('Create ".Inflector::singularize(Inflector::camel2words($name))."', ['".$generator->pathPrefix.Inflector::camel2id($generator->generateRelationTo($relation))."/create'], ['class'=>'btn btn-success btn-xs']) ?>\n";
        echo "</p><div class='clearfix'></div>\n";

        echo "<?php \$this->endBlock() ?>\n\n";

        $label = Inflector::camel2words($name);
        $items .= <<<EOS
[
    'label'   => '<small>$label</small>',
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

    <hr/>

    <?= "<?php " ?>echo Html::a('Delete', ['delete', <?= $urlParams ?>], [
    'class' => 'btn btn-danger',
    'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
    'data-method' => 'post',
    ]); ?>
</div>

