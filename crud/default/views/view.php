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
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */

$this->title = '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?> <small>View ' . $model-><?= $generator->getNameAttribute() ?> . '</small>';
$this->params['breadcrumbs'][] = ['label' => '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

	<h1><?= "<?= " ?>$this->title ?></h1>

	<p>
		<?= "<?= " ?>Html::a('Edit', ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary']) ?>
		<?= "<?php " ?>echo Html::a('Delete', ['delete', <?= $urlParams ?>], [
			'class' => 'btn btn-danger',
			'data-confirm' => Yii::t('app', 'Are you sure to delete this item?'),
			'data-method' => 'post',
		]); ?>
	</p>

    <?php $label = StringHelper::basename($generator->modelClass); ?>

    <?php echo "<?php \$this->beginBlock('{$generator->modelClass}'); ?>"; ?>

    <?php echo "<p class='pull-right'><?= \\yii\\helpers\\Html::a('$label', ['".lcfirst($label)."/index'], ['class'=>'btn btn-primary']) ?></p>"; ?>

	<?= "<?php " ?>echo DetailView::widget([
		'model' => $model,
		'attributes' => [
<?php
foreach ($generator->getTableSchema()->columns as $column) {
    #$name = $generator->generateColumnName($column);
    $format = $generator->generateColumnFormat($column);

    if($relation = $generator->getRelationByColumn($column)) {
        #echo "\t\t\t'" . $column->name . ($format === 'link' ? "" : ":" . $format) . "',\n";
        echo "['format'=>'raw','attribute'=>'$column->name', 'value'=> \\yii\\helpers\\Html::a(\$model->{$column->name}, ['".lcfirst(StringHelper::basename($relation->modelClass))."/view', 'id'=>\$model->{$column->name}])],";
    } else {
        echo "\t\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
    }

}
?>
		],
	]); ?>
    <?php echo "<?php \$this->endBlock(); ?>"; ?>

    <?php
    $items = <<<EOS
[
    'label'   => '$label',
    'content' => \$this->blocks['{$generator->modelClass}'],
    'active'  => true,
],
EOS;

    foreach ($generator->getModelRelations() as $name => $relation) {
        if (!$relation->multiple) continue;
        echo "<?php \$this->beginBlock('$name'); ?>";
        echo "<?php \\yii\\widgets\\Pjax::begin() ?>";
        echo "<p class='pull-right'><?= \\yii\\helpers\\Html::a('$name', ['".$generator->generateRelationTo($relation)."/index'], ['class'=>'btn btn-primary']) ?></p>";

        echo $generator->generateRelationGrid([$relation, $name]);

        echo "<?php \\yii\\widgets\\Pjax::end() ?>";
        echo "<?php \$this->endBlock() ?>";
        $items .= <<<EOS
[
    'label'   => '$name',
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
                     'items' => [ $items ]
                 ]
    );
    ?>";
    ?>
</div>
