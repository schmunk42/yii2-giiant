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
use yii\web\Controller;

/**
 * @author Tobais Munk <schmunk@usrbin.de>
 * @since  2.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    #public $codeModel;
    public $providerList = null;
    public $requires = [];
    public $_p = [];

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
     * Instanciates
     *
     * @param array $data
     * @param null  $formName
     *
     * @return bool|void
     */
    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
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
                #[['providerList'], 'required'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['providerList']);
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
            if (substr($method->name, 0, 3) !== 'get') {
                continue;
            }
            /*echo $method->name."
";*/
            if ($method->name === 'getRelation') {
                continue;
            }
            if ($method->name === 'getBehavior') {
                continue;
            }
            if ($method->name === 'getFirstError') {
                continue;
            }
            if ($method->name === 'getAttribute') {
                continue;
            }
            if ($method->name === 'getAttributeLabel') {
                continue;
            }
            if ($method->name === 'getOldAttribute') {
                continue;
            }
            $relation = call_user_func(array($model, $method->name));
            if ($relation instanceof yii\db\ActiveQuery) {
                $stack[] = $relation;
            }
            #var_dump($stack);exit;
        }
        return $stack;
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

    private function callProviderQueue($func, $args)
    {
        // walk through providers
        \Yii::$app->log->log('Provider queue...', 'schmunk42/packaii');

        foreach ($this->_p AS $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), [$args]);
                // until a provider returns not null
                if ($c !== null) {
                    #echo 'Provider: '.$class."\n";
                    return $c;
                }
            }
        }
    }
}
