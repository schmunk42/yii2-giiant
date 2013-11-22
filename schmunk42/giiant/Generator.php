<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace schmunk42\giiant;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\web\Controller;

/**
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
	public function getName()
	{
		return 'Giiant';
	}

	public function getDescription()
	{
		return 'This generator generates an extended version of CRUDs.';
	}
	
	public function getModelRelations(){
        $reflector = new \ReflectionClass($this->modelClass);
        $model = new $this->modelClass;
        $stack = array();
        foreach ($reflector->getMethods() AS $method) {
            if (substr($method->name,0,3) !== 'get') continue;
            if ($method->name === 'getRelation') continue; 
            if ($method->name === 'getBehavior') continue; 
            if ($method->name === 'getFirstError') continue; 
            if ($method->name === 'getAttribute') continue; 
            if ($method->name === 'getAttributeLabel') continue;             
            if ($method->name === 'getOldAttribute') continue; 
            
            $relation = call_user_func(array($model,$method->name));       
            if($relation instanceof yii\db\ActiveRelation) {
                $stack[] = $relation;
            }
        }
        return $stack;
    }
}
