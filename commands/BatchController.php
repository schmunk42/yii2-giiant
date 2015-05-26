<?php

namespace schmunk42\giiant\commands;

use schmunk42\giiant\crud\Generator;
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
     * @var string database application component
     */
    public $modelDb = 'db';

    /**
     * @var string base class for the generated models
     */
    public $modelBaseClass = 'yii\db\ActiveRecord';

    /**
     * @var boolean whether the strings will be generated using `Yii::t()` or normal strings.
     */
    public $enableI18N = true;

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
     * @var application configuration for creating temporary applications
     */
    private $appConfig;

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
                'messageCategory',
                'tables',
                'tablePrefix',
                'modelDb',
                'modelNamespace',
                'modelBaseClass',
                'crudControllerNamespace',
                'crudSearchModelNamespace',
                'crudViewPath',
                'crudPathPrefix',
                'crudProviders',
                'crudSkipRelations',
                'crudBaseControllerClass'
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

        if (!$this->tables) {
            echo "No tables specified.";
            return false;
            exit;
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
                'messageCategory'    => $this->messageCategory,
                'generateModelClass' => $this->extendedModels,
                'modelClass'         => isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                    Inflector::camelize($table), // TODO: setting is not recognized in giiant
                'baseClass'          => $this->modelBaseClass,
                'tableNameMap'       => $this->tableNameMap
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
            $name   = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] : Inflector::camelize($table);
            $params = [
                'interactive'         => $this->interactive,
                'overwrite'           => $this->overwrite,
                'template'            => $this->template,
                'modelClass'          => $this->modelNamespace . '\\' . $name,
                'searchModelClass'    => $this->crudSearchModelNamespace . '\\' . $name,
                'controllerClass'     => $this->crudControllerNamespace . '\\' . $name . 'Controller',
                'viewPath'            => $this->crudViewPath,
                'pathPrefix'          => $this->crudPathPrefix,
                'tablePrefix'         => $this->tablePrefix,
                'enableI18N'          => $this->enableI18N,
                'messageCategory'     => $this->messageCategory,
                'actionButtonClass'   => 'yii\\grid\\ActionColumn',
                'baseControllerClass' => $this->crudBaseControllerClass,
                'providerList'        => implode(',', $providers),
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
