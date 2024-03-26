<?php

/**
 * @var yii\gii\generators\crud\Generator $generator
 * @var ActiveRecord $model
 * @var array $safeAttributes
 */

use yii\db\ActiveRecord;

echo "<?php\n";
?>
/**
 * @var yii\web\View $this
 * @var <?= ltrim($generator->modelClass, '\\') ?> $model
 * @var yii\widgets\ActiveForm $form
 */
?>
<?php
foreach ($safeAttributes as $attribute) {
    echo "\n\n<!-- attribute $attribute -->\n";
    $prepend = $generator->prependActiveField($attribute, $model);
    $field = $generator->activeField($attribute, $model);
    $append = $generator->appendActiveField($attribute, $model);

    if ($prepend) {
        echo $prepend;
    }
    if ($field) {
        echo "<?php echo " . $field . ' ?>';
    }
    if ($append) {
        echo $append;
    }
}
