<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace schmunk42\giiant\crud;

use schmunk42\giiant\crud\providers\CallbackProvider;
use schmunk42\giiant\crud\providers\DateTimeProvider;
use schmunk42\giiant\crud\providers\EditorProvider;
use schmunk42\giiant\crud\providers\RelationProvider;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ColumnSchema;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * This generator generates an extended version of CRUDs.
 * @author Tobais Munk <schmunk@usrbin.de>
 * @since 1.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    #public $codeModel;
    public $actionButtonClass = 'yii\grid\ActionColumn';
    public $providerList = null;
    public $viewPath = '@backend/views';
    public $tablePrefix = null;
    public $pathPrefix = null;
    public $formLayout = 'horizontal';
    public $requires = [];
    private $_p = [];

    static public function getCoreProviders()
    {
        return [
            CallbackProvider::className(),
            EditorProvider::className(),
            DateTimeProvider::className(),
            RelationProvider::className()
        ];
    }

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
     * Prepare providers
     *
     * @param array $data
     * @param null $formName
     *
     * @return bool|void
     */
    public function init()
    {
        \Yii::trace("Initializing giiant CRUD generator for model '{$this->modelClass}'", __METHOD__);
        parent::init();
    }

    private function initializeProviders(){
        // TODO: this is a hotfix for an already initialized provider queue on action re-entry
        if ($this->_p !== []) {
            return;
        }
        if ($this->providerList) {
            foreach (explode(',', $this->providerList) AS $class) {
                $class = trim($class);
                if (!$class) {
                    continue;
                }
                $obj            = \Yii::createObject(['class' => $class]);
                $obj->generator = $this;
                $this->_p[]     = $obj;
                #\Yii::trace("Initialized provider '{$class}'", __METHOD__);
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
                [['viewPath'], 'required'],
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


    public function getModelNameAttribute($modelClass)
    {
        $model = new $modelClass;
        if ($model->hasMethod('getLabel')) {
            return 'label';
        }
        foreach ($modelClass::getTableSchema()->getColumnNames() as $name) {
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

        return $modelClass::primaryKey()[0];
    }

    public function getModelByTableName($name)
    {
        return Inflector::id2camel(str_replace($this->tablePrefix, '', $name), '_');
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
    public function getRelationByColumn($model, $column)
    {
        $relations = $this->getModelRelations($model);
        foreach ($relations AS $relation) {
            // TODO: check multiple link(s)
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
    public function getModelRelations($modelClass, $types = ['belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'])
    {
        $reflector = new \ReflectionClass($modelClass);
        $model     = new $modelClass;
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
                } elseif ($this->isPivotRelation($relation)) { # TODO: detecttion
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

    /**
     * Generates code for active field by using the provider queue
     *
     * @param string $attribute
     *
     * @return string
     */
    public function activeField(ColumnSchema $column, $model = null)
    {
        Yii::trace("Rendering activeField for '{$column->name}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $column, $model);
        if ($code !== null) {
            return $code;
        } else {
            return parent::generateActiveField($column->name);
        };
    }

    public function prependActiveField(ColumnSchema $column, $model = null)
    {
        Yii::trace("Rendering activeField for '{$column->name}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        return $this->callProviderQueue(__FUNCTION__, $column, $model);
    }

    public function appendActiveField(ColumnSchema $column, $model = null)
    {
        Yii::trace("Rendering activeField for '{$column->name}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        return $this->callProviderQueue(__FUNCTION__, $column, $model);
    }

    public function columnFormat(ColumnSchema $column, $model = null)
    {
        Yii::trace("Rendering columnFormat for '{$column->name}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $column, $model);
        if ($code !== null) {
            return $code;
        } else {
            return $this->shorthandAttributeFormat($column);
        };
    }


    public function attributeFormat(ColumnSchema $column, $model = null)
    {
        Yii::trace("Rendering attributeFormat for '{$column->name}'", __METHOD__);
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $column, $model);
        if ($code !== null) {
            return $code;
        }
        return $this->shorthandAttributeFormat($column);
        // don't call parent anymore
    }

    public function relationGrid($attribute)
    {
        Yii::trace("Rendering relationGrid", __METHOD__);
        return $this->callProviderQueue(__FUNCTION__, $attribute);
    }

    public function generateActionParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            return '$'.$pks[0]; // fix for non-id columns
         } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks = $class::primaryKey();
        if (count($pks) === 1) {
            if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'{$pks[0]}' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($class, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    public function isPivotRelation(ActiveQuery $relation)
    {
        $model = new $relation->modelClass;
        $table = $model->tableSchema;
        $pk    = $table->primaryKey;
        if (count($pk) !== 2) {
            return false;
        }
        $fks = [];
        foreach ($table->foreignKeys as $refs) {
            if (count($refs) === 2) {
                if (isset($refs[$pk[0]])) {
                    $fks[$pk[0]] = [$refs[0], $refs[$pk[0]]];
                } elseif (isset($refs[$pk[1]])) {
                    $fks[$pk[1]] = [$refs[0], $refs[$pk[1]]];
                }
            }
        }
        if (count($fks) === 2 && $fks[$pk[0]][0] !== $fks[$pk[1]][0]) {
            return $fks;
        } else {
            return false;
        }
    }

    private function callProviderQueue($func, $args)
    {
        $this->initializeProviders(); // TODO: should be done on init, but providerList is empty
        //var_dump($this->_p);exit;
        $args = func_get_args();
        unset($args[0]);
        // walk through providers
        foreach ($this->_p AS $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), $args);
                // until a provider returns not null
                if ($c !== null) {
                    if (is_object($args)) {
                        $argsString = get_class($args);
                    } elseif (is_array($args)) {
                        $argsString = Json::encode($args);
                    } else {
                        $argsString = $args;
                    }
                    $msg = 'Using provider ' . get_class($obj) . '::' . $func . ' ' . $argsString;
                    Yii::trace($msg, __METHOD__);
                    return $c;
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

        return "\t\t\t'" . $attribute->name . ($format === 'text' ? "" : ":" . $format) . "'";
    }
}
