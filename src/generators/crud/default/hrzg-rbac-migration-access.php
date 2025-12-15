<?php
/**
 * @var \schmunk42\giiant\generators\crud\Generator $generator
 */

use dmstr\rbacMigration\Migration;
use yii\db\Expression;
use yii\helpers\Inflector;
use yii\rbac\Item;

$assigments = [
    'Full' => ['index', 'view', 'create', 'update', 'delete'],
    'View' => ['index', 'view'],
    'Edit' => ['update', 'create', 'delete'],
];

$moduleId = $generator->getModuleId();
$controllerId = $generator->getControllerID();

$roleNamePrefix = Inflector::camelize($moduleId) . Inflector::camelize($controllerId);
echo '<?php' . PHP_EOL;
?>

use dmstr\rbacMigration\Migration;
use yii\rbac\Item;

class <?php echo $generator->migrationClass ?> extends Migration
{
    public $privileges = [
    <?php foreach ($assigments as $roleNameSuffix => $actionsIds):
        $roleName = $roleNamePrefix . $roleNameSuffix;
        ?>
        [
            'type' => Item::TYPE_ROLE,
            'name' => '<?php echo $roleName ?>',
            'ensure' => Migration::PRESENT,
            'children' => [
        <?php foreach ($actionsIds as $actionId):
            $permissionName = $moduleId . '_' . $controllerId . '_' . $actionId;
            ?>
        [
                    'type' => Item::TYPE_PERMISSION,
                    'name' => '<?php echo $permissionName ?>',
                    'ensure' => Migration::PRESENT
                ],
        <?php endforeach; ?>
        ]
        ],
    <?php endforeach; ?>
    ];
}
