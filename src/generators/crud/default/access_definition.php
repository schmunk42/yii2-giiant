<?php
/**
 * define action permissions and roles
 * use in controller access control, access migration and for role translation
 */
use yii\helpers\Inflector;

/**
 * action list
 */
$actions = ['index','view','create','update','delete'];

/**
 * permissions - create name and descriptions
 */
$permissions = [];
foreach ($actions as $k => $action){
    $name = $this->getModuleId() 
            . '_' . $this->getControllerID() 
            . '_' . $action;
    $description = $this->getModuleId() 
            . '/' . $this->getControllerID() 
            . '/' . $action;
    $permissions[$action] = [
        'name' => $name,
        'description' => $description,
        ];
}

/**
 * roles dependencies
 */
$roles = [
        'Full' => ['index','view','create','update','delete'],
        'View' => ['index','view'],
        'Edit' => ['update','create','delete'],
        ]; 

/**
 * create roles name
 */
foreach($roles as $role => $roleActons){
    unset($roles[$role]);
    $roleName = Inflector::camelize($this->getModuleId())
            .Inflector::camelize($this->getControllerID())
            .$role;
    $roles[$roleName] = $roleActons;
}

return [
    'permissions' => $permissions,
    'roles' => $roles,
];
