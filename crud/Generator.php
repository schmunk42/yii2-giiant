<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace schmunk42\giiant\crud;

use Yii;
use yii\helpers\Inflector;

/**
 * This generator generates an extended version of CRUDs.
 * @author Tobais Munk <schmunk@usrbin.de>
 * @since  2.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    #public $codeModel;
    public $actionButtonClass = 'common\helpers\ActionColumn';
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
                'viewPath'     => 'Output path for view files, eg. <code>@backend/views/crud</code>.',
                'pathPrefix'   => 'Customized route/subfolder for controllers and views eg. <code>crud/</code>. <b>Note!</b> Should correspond to <code>viewPath</code>.',
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
        return array_merge(parent::stickyAttributes(), ['providerList', 'actionButtonClass', 'viewPath', 'pathPrefix']);
    }


    public function getModelNameAttribute($model){
        foreach ($model::getTableSchema()->getColumnNames() as $name){
            switch (strtolower($name)) {
                case 'name':
                case 'title':
                case 'name_id':
                case 'default_title':
                case 'default_name':
                    return $name;
                    break;
                default:
                    continue;
                    break;
            }

        }

        return $model::primaryKey()[0];
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
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos   = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class, '-', true);
    }


    /**
     * @param $column
     *
     * @return null|\yii\db\ActiveQuery
     */
    public function getRelationByColumn($column)
    {
        #if ($column->isPrimaryKey) {
        #    return false;
        #}
        $relations = $this->getModelRelations();
        foreach ($relations AS $relation) {
            // TODO: check multiple link(s)
            #var_dump($relation,$column);
            if (reset($relation->link) == $column->name) {
                return $relation;
            }
        }
        return null;
    }

    /**
     * @todo docs
     * @return array
     */
    public function getModelRelations($types = ['belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'])
    {
        $reflector = new \ReflectionClass($this->modelClass);
        $model     = new $this->modelClass;
        $stack     = [];
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
                #var_dump($relation->primaryModel->primaryKey);
                if ($relation->multiple === false) {
                    $relationType = 'belongs_to';
                } elseif (strstr($relation->modelClass, "X")) { # TODO: detecttion
                    $relationType = 'pivot';
                } else {
                    $relationType = 'has_many';
                }

                if (in_array($relationType, $types)) {
                    $stack[substr($method->name, 3)] = $relation;
                }
            }
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

    // TODO: naming -_> columnFormat();
    public function generateColumnFormat($attribute)
    {
        $code = $this->callProviderQueue(__FUNCTION__, $attribute);
        if ($code !== null) {
            return $code;
        } else {
            return $this->shorthandAttributeFormat($attribute);
        };
    }

    public function generateAttributeFormat($attribute)
    {

        if ($code = $this->callProviderQueue(__FUNCTION__, $attribute)) {
            return $code;
        }
        return $this->shorthandAttributeFormat($attribute);
        // don't call parent anymore
    }

    public function generateRelationGrid($attribute)
    {
        return $this->callProviderQueue(__FUNCTION__, $attribute);
    }


    public function createRelationRoute($relation, $action)
    {
        $route = $this->pathPrefix . Inflector::camel2id(
                $this->generateRelationTo($relation),
                '-',
                true
            ) . "/" . $action;
        return $route;
    }

    public function generateRelationTo($relation)
    {
        $class = new \ReflectionClass($relation->modelClass);
        $route = Inflector::variablize($class->getShortName());
        return $route;
    }

    private function callProviderQueue($func, $args)
    {
        // walk through providers
        foreach ($this->_p AS $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), [$args]);
                // until a provider returns not null
                if ($c !== null) {
                    $msg = 'Using ' . get_class($obj) . '::' . $func;
                    Yii::trace($msg, __NAMESPACE__);
                    return $c;#.'/*provider*/';
                }
            }
        }
    }

    private function shorthandAttributeFormat($attribute)
    {
        if ($attribute->phpType === 'boolean') {
            $format = 'boolean';
        } elseif ($attribute->type === 'text') {
            $format = 'ntext';
        } elseif (stripos($attribute->name, 'time') !== false && $attribute->phpType === 'integer') {
            $format = 'datetime';
        } elseif (stripos($attribute->name, 'email') !== false) {
            $format = 'email';
        } elseif (stripos($attribute->name, 'url') !== false) {
            $format = 'url';
        } else {
            $format = 'text';
        }

        return "\t\t\t'" . $attribute->name . ($format === 'text' ? "" : ":" . $format) . "'\n";
    }
}
