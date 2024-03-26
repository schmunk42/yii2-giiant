<?php
/**
 * This is the template for generating the ActiveQuery class.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

if ($generator->ns !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->ns . '\\' . $modelClassName;
} else {
    $modelFullClassName = $modelClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

use <?php echo $modelFullClassName ?>;

/**
 * This is the ActiveQuery class for [[<?= $modelClassName ?>]].
 *
 * @see <?= $modelFullClassName . "\n" ?>
 * @method <?= $modelClassName ?>[] all($db = null)
 * @method <?= $modelClassName ?> one($db = null)
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->queryBaseClass, '\\') . "\n" ?>
{

}
