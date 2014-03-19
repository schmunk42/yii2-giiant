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

	<?= "<?php " ?>echo DetailView::widget([
		'model' => $model,
		'attributes' => [
<?php
foreach ($generator->getTableSchema()->columns as $column) {
	$format = $generator->generateColumnFormat($column);
	echo "\t\t\t'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
}
?>
		],
	]); ?>

    <?php
    $items = "";
    foreach ($generator->getModelRelations() as $name => $relation) {
        echo "<?php \$this->beginBlock('$name'); ?>";
        echo "<?php \\yii\\widgets\\Pjax::begin() ?>";
        echo "<p class='pull-right'><?= \\yii\\helpers\\Html::a('Go to $name', ['".$generator->generateRelationTo($relation)."/index'], ['class'=>'btn btn-primary']) ?></p>";
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
