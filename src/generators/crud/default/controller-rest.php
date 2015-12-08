<?php
/**
 * Customizable controller class
 *
 */

echo "<?php\n";
?>

namespace <?= $generator->controllerNs ?>\api;

/**
 * This is the class for REST controller "<?= $controllerClassName ?>".
 */
class <?= $controllerClassName ?> extends \yii\rest\ActiveController
{
    public $modelClass = '<?= $generator->modelClass ?>';
}
