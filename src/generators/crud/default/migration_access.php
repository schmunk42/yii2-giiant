<?="<?php\n"?>

use yii\db\Migration;
use yii\db\Schema;
use <?=$generator->moduleNs?>\Module;

class <?=$generator->migrationClass?> extends Migration
{
    public $actions = ['index','view','create','update','delete'];
    
    public function up()
    {
        $descriptionPrefix = '<?=$generator->getModelID()?>_<?=$report->getControllerID()?>_';

        $auth = Yii::$app->authManager;

        foreach($this->actions as $action){
            $name = $this->getNamePrefix() . $action;
            $authItem = $auth->createPermission($name);
            $authItem->description = $descriptionPrefix . $action;
            $auth->add($authItem);
        }
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        foreach($this->actions as $action){
            $name = $this->getNamePrefix() . $action;
            $authItem = $auth->createPermission($name);
            $auth->removeItem($authItem);
        }    
        
    }
    
    public function getNamePrefix()
    {
        $module = new Module();
        return '_<?=$report->getControllerID()?>_';
    }
}
