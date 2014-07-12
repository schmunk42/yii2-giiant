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

/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 */

$this->title = '<?= Inflector::camel2words(StringHelper::basename($generator->modelClass)) ?> Update ' . $model-><?= $generator->getNameAttribute() ?> . '';
$this->params['breadcrumbs'][] = ['label' => '<?= Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass),'-', true) ?>-update">

    <p>
        <?= "<?= " ?>Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', ['view', <?= $urlParams ?>], ['class' => 'btn btn-info']) ?>
    </p>

	<?= "<?php " ?>echo $this->render('_form', [
		'model' => $model,
	]); ?>

</div>
