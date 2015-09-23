<?php
/**
 * @link      http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */

namespace schmunk42\giiant\generators\crud;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\ColumnSchema;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\FileHelper;
use \schmunk42\giiant\generators\model\Generator as ModelGenerator;

/**
 * This generator generates an extended version of CRUDs.
 * @author Tobais Munk <schmunk@usrbin.de>
 * @since 1.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    /**
     * @var null comma separated list of provider classes
     */
    public $providerList = null;
    /**
     * @todo review
     * @var string
     */
    public $actionButtonClass = 'schmunk42\giiant\components\grid\ActionColumn';
    /**
     * @var array relations to be excluded in UI rendering
     */
    public $skipRelations = [];
    /**
     * @var string default view path
     */
    public $viewPath = '@backend/views';

    public $tablePrefix = null;
    public $pathPrefix = null;
    public $formLayout = 'horizontal';
    /**
     * @var string translation catalogue
     */
    public $messageCatalog = 'app';
    /**
     * @var int maximum number of columns to show in grid
     */
    public $gridMaxColumns = 8;
    /**
     * @var int maximum number of columns to show in grid
     */
    public $gridRelationMaxColumns = 8;
    /**
     * @var array array of composer packages (only to show information to the developer in the web UI)
     */
    public $requires = [];
    public $singularEntities = false;
    public $accessFilter = false;

    private $_p = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->providerList = self::getCoreProviders();

        return parent::init();
    }

    /**
     * @return array Class names of the providers declared directly under crud/providers folder.
     */
    static public function getCoreProviders()
    {
        $files = FileHelper::findFiles(__DIR__ . DIRECTORY_SEPARATOR . 'providers', [
            'only' => ['*.php'],
            'recursive' => false
        ]);

        foreach ($files as $file) {
            require_once($file);
        }

        return array_filter(get_declared_classes(), function($a){
            return (stripos($a, __NAMESPACE__ . '\providers') !== false);
        });
    }

    /**
     * @return array List of providers. Keys and values contain the same strings.
     */
    public function generateProviderCheckboxListData()
    {
        $coreProviders = self::getCoreProviders();

        return array_combine($coreProviders, $coreProviders);
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

    protected function initializeProviders()
    {
        // TODO: this is a hotfix for an already initialized provider queue on action re-entry
        if ($this->_p !== []) {
            return;
        }
        if ($this->providerList) {
            foreach($this->providerList as $class) {
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
        \Yii::trace("CRUD providers initialized for model '{$this->modelClass}'", __METHOD__);

    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'providerList' => 'Choose the providers to be used.',
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
                [['providerList', 'actionButtonClass', 'viewPath', 'pathPrefix'], 'safe'],
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
        // TODO: cleanup, get-label-methods, move to config
        if ($model->hasMethod('get_label')) {
            return '_label';
        }
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
        $returnName = str_replace($this->tablePrefix, '', $name);
        $returnName = Inflector::id2camel($returnName, '_');
        if ($this->singularEntities) $returnName = Inflector::singularize($returnName);
        return $returnName;
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
        if ($this->singularEntities) $class = Inflector::singularize($class);
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
            if ($relation->link && reset($relation->link) == $column->name) {
                return $relation;
            }
        }
        return null;
    }

    /**
     * Finds relations of a model class
     *
     * return values can be filtered by types 'belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'
     *
     * @param ActiveRecord $modelClass
     * @param array $types
     *
     * @return array
     */
    public function getModelRelations($modelClass, $types = ['belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'])
    {
        $reflector = new \ReflectionClass($modelClass);
        $model     = new $modelClass;
        $stack     = [];
        $modelGenerator = new ModelGenerator;
        foreach ($reflector->getMethods() AS $method) {
            if (in_array(substr($method->name, 3), $this->skipRelations)) {
                continue;
            }
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
            try {
                $relation = @call_user_func(array($model, $method->name));
                if ($relation instanceof \yii\db\ActiveQuery) {
                    #var_dump($relation->primaryModel->primaryKey);
                    if ($relation->multiple === false) {
                        $relationType = 'belongs_to';
                    } elseif ($this->isPivotRelation($relation)) { # TODO: detecttion
                        $relationType = 'pivot';
                    } else {
                        $relationType = 'has_many';
                    }

                    if (in_array($relationType, $types)) {
                        $name = $modelGenerator->generateRelationName([$relation], $model->getTableSchema(), substr($method->name, 3), $relation->multiple);
                        $stack[$name] = $relation;
                    }
                }
            } catch (Exception $e) {
                Yii::error("Error: " . $e->getMessage(), __METHOD__);
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
     * @param ColumnSchema $column
     * @param null $model
     *
     * @return mixed|string
     */
    public function activeField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
            return $code;
        } else {
            $column = $this->getColumnByAttribute($attribute);
            if (!$column) {
                return null;
            } else {
                return parent::generateActiveField($attribute);
            }
        }
    }

    public function prependActiveField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        }
        return $code;
    }

    public function appendActiveField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        }
        return $code;
    }

    public function columnFormat($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        } else {
            $code = $this->shorthandAttributeFormat($attribute, $model);
            Yii::trace("using standard formatting for '{$attribute}'", __METHOD__);
        }
        return $code;
    }

    public function attributeFormat($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
            return $code;
        }

        $column = $this->getColumnByAttribute($attribute);
        if (!$column) {
            return null;
        } else {
            return $this->shorthandAttributeFormat($attribute, $model);
        }
        // don't call parent anymore
    }

    public function relationGrid($name, $relation, $showAllRecords = false)
    {
        Yii::trace("calling provider queue for '$name'", __METHOD__);
        return $this->callProviderQueue(__FUNCTION__, $name, $relation, $showAllRecords);
    }

    /**
     * @inheritdoc
     */
    public function generateActionParams()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks   = $class::primaryKey();
        if (count($pks) === 1) {
            return '$' . $pks[0]; // fix for non-id columns
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * @inheritdoc
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $class = $this->modelClass;
        $pks   = $class::primaryKey();
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $' . $pks[0]];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
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
        $pks   = $class::primaryKey();
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

    protected function callProviderQueue($func, $args, $generator)
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

    protected function shorthandAttributeFormat($attribute, $model)
    {
        $column = $this->getColumnByAttribute($attribute, $model);
        if (!$column) {
            Yii::trace("No column for '{$attribute}' found", __METHOD__);
            return null;
        } else {
            Yii::trace("Table column detected for '{$attribute}'", __METHOD__);
        }
        if ($column->phpType === 'boolean') {
            $format = 'boolean';
        } elseif ($column->type === 'text') {
            $format = 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            $format = 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            $format = 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            $format = 'url';
        } else {
            $format = 'text';
        }

        return "        '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "'";
    }

    public function getColumnByAttribute($attribute, $model = null){
        if (is_string($model)) {
            $model = new $model;
        }
        if ($model === null) {
            $model = $this;
        }
        return $model->getTableSchema()->getColumn($attribute);
    }

    public function generate()
    {
        if ($this->singularEntities) {
            $this->modelClass = Inflector::singularize($this->modelClass);
            $this->controllerClass = Inflector::singularize(substr($this->controllerClass, 0, strlen($this->controllerClass) - 10)) . "Controller";
            $this->searchModelClass = Inflector::singularize($this->searchModelClass);
        }
        return parent::generate();
    }

    public function validateClass($attribute, $params)
    {
        if ($this->singularEntities) {
            $this->$attribute = Inflector::singularize($this->$attribute);
        }
        parent::validateClass($attribute, $params);
    }
}