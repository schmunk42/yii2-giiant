<?php
/**
 * Customizable controller class.
 */
echo "<?php\n";
?>

namespace <?= \yii\helpers\StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?php echo ltrim(isset($generator->controllerNs) ? '\\'.$generator->controllerNs.'\\' : '', '\\') .'base\\'.$controllerClassName?> as Base<?php echo $controllerClassName ?>;

/**
 * This is the class for controller "<?= $controllerClassName ?>".
 */
class <?= $controllerClassName ?> extends Base<?php echo $controllerClassName . PHP_EOL ?>
{

}
