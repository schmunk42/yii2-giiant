<?php

namespace schmunk42\giiant\commands;

use schmunk42\giiant\generators\crud\Generator;
use schmunk42\giiant\generators\model\Generator as ModelGenerator;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * @author Tobias Munk <schmunk@usrbin.de>
 */
class BatchController extends Controller
{
    /**
     * @var string the generator template name
     */
    public $template = 'default';

    /**
     * @var bool whether to generate and overwrite all files
     */
    public $overwrite = false;

    /**
     * @var bool whether to overwrite extended models (from ModelBase)
     */
    public $extendedModels = false;

    /**
     * @var array table names for generating models and CRUDs
     */
    public $tables = [];

    /**
     * @var string eg. `app_`
     */
    public $tablePrefix = '';

    /**
     * @var array mapping for table name to model class names
     */
    public $tableNameMap = [];

    /**
     * @var string namespace path for model classes
     */
    public $modelNamespace = 'common\\models';

    /**
     * @var string suffix to append to the base model, setting "Base" will result in a model named "PostBase"
     */
    public $modelBaseClassSuffix = '';

    /**
     * @var string database application component
     */
    public $modelDb = 'db';

    /**
     * @var string base class for the generated models
     */
    public $modelBaseClass = 'yii\db\ActiveRecord';

    /**
     * @var string traits for base-models
     */
    public $modelBaseTraits = null;

    /**
     * @var boolean whether the strings will be generated using `Yii::t()` or normal strings.
     */
    public $enableI18N = true;

    /**
     * @var boolean whether the entity names will be singular or the same as the table name.
     */
    public $singularEntities = true;

    /**
     * @var string the message category used by `Yii::t()` when `$enableI18N` is `true`.
     * Defaults to `app`.
     */
    public $messageCategory = 'app';

    /**
     * @var string namespace path for crud controller
     */
    public $crudControllerNamespace = 'backend\\controllers\\crud';

    /**
     * @var string namespace path for crud search models
     */
    public $crudSearchModelNamespace = 'backend\\models\\search';
    
    /**
     * @var string suffix to append to the search model, setting "Search" will result in a model named "PostSearch"
     */
    public $crudSearchModelSuffix = '';

    /**
     * @var string namespace path for crud views
     */
    public $crudViewPath = '@backend/views/crud';

    /**
     * @var string route prefix for crud controller actions
     */
    public $crudPathPrefix = 'crud/';

    /**
     * @var array list of code provider classes (fully namespaced path required)
     */
    public $crudProviders = [];

    /**
     * @var string base class for crud controllers
     */
    public $crudBaseControllerClass = 'yii\web\Controller';

    /**
     * @var array list of relations to skip, when generating `view`-views
     */
    public $crudSkipRelations = [];

    /**
     * @var bool indicates whether to generate ActiveQuery for the ActiveRecord class
     */
    public $modelGenerateQuery = true;

    /**
     * @var string the namespace of the ActiveQuery class to be generated
     */
    public $modelQueryNamespace = 'app\models\query';

    /**
     * @var string the base class of the new ActiveQuery class
     */
    public $modelQueryBaseClass = 'yii\db\ActiveQuery';

    /**
     * @var array application configuration for creating temporary applications
     */
    protected $appConfig;

    /**
     * @var instance of class schmunk42\giiant\generators\model\Generator
     */
    protected $modelGenerator;

    /**
     * @inheritdoc
     */
    public function options($id)
    {
        return array_merge(
            parent::options($id),
            [
                'template',
                'overwrite',
                'extendedModels',
                'enableI18N',
                'singularEntities',
                'messageCategory',
                'tables',
                'tablePrefix',
                'modelDb',
                'modelNamespace',
                'modelBaseClass',
                'modelBaseTraits',
                'modelBaseClassSuffix',
                'crudControllerNamespace',
                'crudSearchModelNamespace',
                'crudSearchModelSuffix',
                'crudViewPath',
                'crudPathPrefix',
                'crudProviders',
                'crudSkipRelations',
                'crudBaseControllerClass',
                'modelGenerateQuery',
                'modelQueryNamespace',
                'modelQueryBaseClass',
            ]
        );
    }

    /**
     * Loads application configuration and checks tables parameter
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->appConfig       = $this->getYiiConfiguration();
        $this->appConfig['id'] = 'temp';
        $this->modelGenerator  = new ModelGenerator(['db' => $this->modelDb]);

        if (!$this->tables) {
            $this->modelGenerator->tableName = '*';
            $this->tables                    = $this->modelGenerator->getTableNames();
            $msg                             = "Are you sure that you want to run action \"{$action->id}\" for the following tables?\n\t- " . implode(
                    "\n\t- ",
                    $this->tables
                ) . "\n\n";
            if (!$this->confirm($msg)) {
                return false;
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Run batch process to generate models and CRUDs for all given tables
     *
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        echo "Running full giiant batch...\n";
        $this->actionModels();
        $this->actionCruds();
    }

    /**
     * Run batch process to generate models all given tables
     * @throws \yii\console\Exception
     */
    public function actionModels()
    {
        // create models
        foreach ($this->tables AS $table) {
            #var_dump($this->tableNameMap, $table);exit;
            $params = [
                'interactive'        => $this->interactive,
                'overwrite'          => $this->overwrite,
                'template'           => $this->template,
                'ns'                 => $this->modelNamespace,
                'db'                 => $this->modelDb,
                'tableName'          => $table,
                'tablePrefix'        => $this->tablePrefix,
                'enableI18N'         => $this->enableI18N,
                'singularEntities'   => $this->singularEntities,
                'messageCategory'    => $this->messageCategory,
                'generateModelClass' => $this->extendedModels,
                'baseClassSuffix'    => $this->modelBaseClassSuffix,
                'modelClass'         => isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                    Inflector::camelize($table), // TODO: setting is not recognized in giiant
                'baseClass'          => $this->modelBaseClass,
                'baseTraits'         => $this->modelBaseTraits,
                'tableNameMap'       => $this->tableNameMap,
                'generateQuery'      => $this->modelGenerateQuery,
                'queryNs'            => $this->modelQueryNamespace,
                'queryBaseClass'     => $this->modelQueryBaseClass,
            ];
            $route  = 'gii/giiant-model';

            $app  = \Yii::$app;
            $temp = new \yii\console\Application($this->appConfig);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
        }

    }

    /**
     * Run batch process to generate CRUDs all given tables
     * @throws \yii\console\Exception
     */
    public function actionCruds()
    {
        // create CRUDs
        $providers = ArrayHelper::merge($this->crudProviders, Generator::getCoreProviders());

        foreach ($this->tables AS $table) {
            $table  = str_replace($this->tablePrefix, '', $table);
            $name   = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                $this->modelGenerator->generateClassName($table);
            $params = [
                'interactive'         => $this->interactive,
                'overwrite'           => $this->overwrite,
                'template'            => $this->template,
                'modelClass'          => $this->modelNamespace . '\\' . $name,
                'searchModelClass'    => $this->crudSearchModelNamespace . '\\' . $name . $this->crudSearchModelSuffix,
                'controllerClass'     => $this->crudControllerNamespace . '\\' . $name . 'Controller',
                'viewPath'            => $this->crudViewPath,
                'pathPrefix'          => $this->crudPathPrefix,
                'tablePrefix'         => $this->tablePrefix,
                'enableI18N'          => $this->enableI18N,
                'singularEntities'    => $this->singularEntities,
                'messageCategory'     => $this->messageCategory,
                'actionButtonClass'   => 'yii\\grid\\ActionColumn',
                'baseControllerClass' => $this->crudBaseControllerClass,
                'providerList'        => $providers,
                'skipRelations'       => $this->crudSkipRelations,
            ];
            $route  = 'gii/giiant-crud';
            $app    = \Yii::$app;
            $temp   = new \yii\console\Application($this->appConfig);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
        }
    }

    /**
     * Returns Yii's initial configuration array
     *
     * @todo should be removed, if this issue is closed -> https://github.com/yiisoft/yii2/pull/5687
     * @return array
     */
    protected function getYiiConfiguration()
    {
        if (isset($GLOBALS['config'])) {
            $config = $GLOBALS['config'];
        } else {
            $config = \yii\helpers\ArrayHelper::merge(
                require(\Yii::getAlias('@app') . '/../common/config/main.php'),
                (is_file(\Yii::getAlias('@app') . '/../common/config/main-local.php')) ?
                    require(\Yii::getAlias('@app') . '/../common/config/main-local.php')
                    : [],
                require(\Yii::getAlias('@app') . '/../console/config/main.php'),
                (is_file(\Yii::getAlias('@app') . '/../console/config/main-local.php')) ?
                    require(\Yii::getAlias('@app') . '/../console/config/main-local.php')
                    : []
            );
        }
        return $config;
    }
}
