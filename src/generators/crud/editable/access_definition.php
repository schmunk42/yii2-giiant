<?php
/**
 * define action permissions and roles
 * use in controller access control, access migration and for role translation
 */
use yii\helpers\Inflector;

/**
 * action list
 */
$actions = [
    'index',
    'view',
    'create',
    'update',
    'delete',
    'editable',
    'editable-column-update',
    'create-for-rel',
    ];

/**
 * permissions - create name and descriptions
 */
$permisions = [];
foreach ($actions as $k => $action){
    $name = $this->getModuleId() 
            . '_' . $this->getControllerID() 
            . '_' . $action;
    $description = $this->getModuleId() 
            . '/' . $this->getControllerID() 
            . '/' . $action;
    $permisions[$action] = [
        'name' => $name,
        'description' => $description,
        ];
}

/**
 * roles dependencies
 */
$roles = [
        'Full' => [
            'index',
            'view',
            'create',
            'update',
            'delete',
            'editable',
            'editable-column-update',
            'create-for-rel',
            ],
        'View' => [
            'index',
            'view',
            ],
        'Edit' => [
            'update',
            'create',
            'delete',
            'editable',
            'editable-column-update',
            'create-for-rel',
            ],
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
    'permisions' => $permisions,
    'roles' => $roles,
];