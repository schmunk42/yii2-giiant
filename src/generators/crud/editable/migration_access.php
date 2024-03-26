<?php
use yii\helpers\Inflector;
/**
 * Migration for controller access
 */
echo "<?php\n";
?>

use yii\db\Migration;

class <?=$generator->migrationClass?> extends Migration
{
    /**
     * @var array controller all actions
     */
    public $permissions = <?=$generator->var_export54($accessDefinitions['permissions'],'    ')?>;
    
    /**
     * @var array roles and maping to actions/permissions
     */
    public $roles = <?=$generator->var_export54($accessDefinitions['roles'],'    ')?>;
    
    public function up()
    {
        
        $permissions = [];
        $auth = \Yii::$app->authManager;

        /**
         * create permissions for each controller action
         */
        foreach ($this->permissions as $action => $permission) {
            $permissions[$action] = $auth->createPermission($permission['name']);
            $permissions[$action]->description = $permission['description'];
            $auth->add($permissions[$action]);
        }

        /**
         *  create roles
         */
        foreach ($this->roles as $roleName => $actions) {
            $role = $auth->createRole($roleName);
            $auth->add($role);

            /**
             *  to role assign permissions
             */
            foreach ($actions as $action) {
                $auth->addChild($role, $permissions[$action]);
            }
        }
    }

    public function down() {
        $auth = Yii::$app->authManager;

        foreach ($this->roles as $roleName => $actions) {
            $role = $auth->createRole($roleName);
            $auth->remove($role);
        }

        foreach ($this->permissions as $permission) {
            $authItem = $auth->createPermission($permission['name']);
            $auth->remove($authItem);
        }
    }
}
