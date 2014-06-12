<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace schmunk42\giiant\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\log\Logger;
use yii\web\Controller;

/**
 * This generator generates an extended version of CRUDs.
 * @author Tobais Munk <schmunk@usrbin.de>
 * @since  2.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    #public $codeModel;
    public $actionButtonClass = null;
    public $providerList = null;
    public $viewPath = null;
    public $pathPrefix = null;
    public $requires = [];
    private $_p = [];

    public function getName()
    {
        return 'Giiant CRUD';
    }

    public function getDescription()
    {
        return 'This generator generates an extended version of CRUDs.';
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        $return = 'The code has been generated successfully. Please require the following packages with composer:';
        $return .= '<br/><code>' . implode('<br/>', $this->requires) . '</code>';
        return $return;
    }

    /**
     * Instanciates providers
     *
     * @param array $data
     * @param null $formName
     *
     * @return bool|void
     */
    public function init()
    {
        parent::init();
        // initialize provider objects
        if ($this->providerList) {
            foreach (explode(',', $this->providerList) AS $class) {
                $class = trim($class);
                if (!$class) {
                    continue;
                }
                $obj            = \Yii::createObject(['class' => $class]);
                $obj->generator = $this;
                $this->_p[]     = $obj;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'providerList' => 'Comma separated list of provider class names, make sure you are using the full namespaced path <code>app\providers\CustomProvider1,<br/>app\providers\CustomProvider2</code>.',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['providerList'], 'filter', 'filter' => 'trim'],
                [['actionButtonClass', 'viewPath', 'pathPrefix'], 'safe'],
                #[['providerList'], 'required'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['providerList', 'actionButtonClass']);
    }

    /**
     * @todo docs
     * @return array
     */
    public function getModelRelations()
    {
        $reflector = new \ReflectionClass($this->modelClass);
        $model     = new $this->modelClass;
        $stack     = array();
        foreach ($reflector->getMethods() AS $method) {
            // look for getters
            if (substr($method->name, 0, 3) !== 'get') {
                continue;
            }
            // skip class specific getters
            $skipMethods = [
                'getRelation',
                'getBehavior',
                'getFirstError',
                'getAttribute',
                'getAttributeLabel',
                'getOldAttribute'
            ];
            if (in_array($method->name, $skipMethods)) {
                continue;
            }
            // check for relation
            $relation = call_user_func(array($model, $method->name));
            if ($relation instanceof yii\db\ActiveQuery) {
                $stack[substr($method->name, 3)] = $relation;
            }
        }
        return $stack;
    }

    public function getRelationByColumn($column)
    {
        if ($column->isPrimaryKey) {
            return false;
        }
        $relations = $this->getModelRelations();
        foreach ($relations AS $relation) {
            // TODO: check multiple link(s)
            #var_dump($relation,$column);
            if (reset($relation->link) == $column->name) {
                return $relation;
            }
        }
        return false;
    }

    /**
     * @return string the action view file path
     */
    public function getViewPath()
    {
        if ($this->viewPath !== null) {
            return \Yii::getAlias($this->viewPath) . '/' . $this->getControllerID();
        } else {
            return parent::getViewPath();
        }

    }

    /**
     * Generates code for active field by using the provider queue
     *
     * @param string $attribute
     *
     * @return string
     */
    public function generateActiveField($attribute)
    {
        $code = $this->callProviderQueue(__FUNCTION__, $attribute);
        if ($code !== null) {
            return $code;
        } else {
            return parent::generateActiveField($attribute);
        };
    }

    public function generateColumnFormat($attribute)
    {
        $code = $this->callProviderQueue(__FUNCTION__, $attribute);
        if ($code !== null) {
            return $code;
        } else {
            return parent::generateColumnFormat($attribute);
        };
    }

    public function generateRelationTo($attribute)
    {
        return $this->callProviderQueue(__FUNCTION__, $attribute);
    }

    public function generateRelationField($relation)
    {
        return $this->callProviderQueue(__FUNCTION__, $relation);
    }

    public function generateRelationGrid($attribute)
    {
        return $this->callProviderQueue(__FUNCTION__, $attribute);
    }


    private function callProviderQueue($func, $args)
    {
        // walk through providers
        foreach ($this->_p AS $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), [$args]);
                // until a provider returns not null
                if ($c !== null) {
                    /*\Yii::$app->log->log(
                                   'Using ' . get_class($obj) . '::' . $func, // TODO: get a string? . ' for ' . print_r($args),
                                       Logger::LEVEL_INFO,
                                       __NAMESPACE__
                    );*/
                    return $c;
                }
            }
        }
    }
}
