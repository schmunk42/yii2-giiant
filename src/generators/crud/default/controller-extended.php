<?php
/**
 * Customizable controller class.
 */
echo "<?php\n";
?>

namespace <?= \yii\helpers\StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

/**
* This is the class for controller "<?= $controllerClassName ?>".
*/
class <?= $controllerClassName ?> extends \<?= $generator->controllerNs.'\base\\'.$controllerClassName."\n" ?>
{

}
