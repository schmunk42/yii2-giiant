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
     * @var bool whether to use or not 2amigos/yii2-translateable-behavior
     */
    public $useTranslatableBehavior = true;

    /**
     * @var bool whether to use yii\behaviors\TimestampBehavior in models
     */
    public $useTimestampBehavior = true;

    /**
     * @var string the name of the table containing the translations. {{table}} will be replaced with the value in
     *             "Table Name" field
     */
    public $languageTableName = '{{table}}_lang';

    /**
     * @var string the column name where the language code is stored
     */
    public $languageCodeColumn = 'language';

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
     * @var
     */
    public $modelRemoveDuplicateRelations = false;

    /**
     * @var bool whether the strings will be generated using `Yii::t()` or normal strings
     */
    public $enableI18N = true;

    /**
     * @var bool whether the entity names will be singular or the same as the table name
     */
    public $singularEntities = true;

    /**
     * @var string the message category for models used by `Yii::t()` when `$enableI18N` is `true`.
     *             Defaults to `app`
     */
    public $modelMessageCategory = 'models';

    /**
     * @var string the message category for CRUDs used by `Yii::t()` when `$enableI18N` is `true`.
     *             Defaults to `app`
     */
    public $crudMessageCategory = 'cruds';

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
     * @var bool whether to add accessFilter in behavior
     */
    public $crudAccessFilter;

    public $crudBaseTraits;

    public $crudTemplate = 'default';

    public $crudIndexWidgetType = 'grid';

    public $crudIndexGridClass = 'yii\\grid\\GridView';

    /**
     * @var bool indicates whether to generate ActiveQuery for the ActiveRecord class
     */
    public $modelGenerateQuery = true;

    /**
     * @var bool whether to tidy generated code
     */
    public $crudTidyOutput = true;

    /**
     * @var string the namespace of the ActiveQuery class to be generated
     */
    public $modelQueryNamespace = 'app\models\query';

    /**
     * @var string the base class of the new ActiveQuery class
     */
    public $modelQueryBaseClass = 'yii\db\ActiveQuery';

    /**
     * @var bool This indicates whether the generator should generate attribute labels by using the comments of the corresponding DB columns
     */
    public $modelGenerateLabelsFromComments = false;

    /**
     * @var bool This indicates whether the generator should generate attribute hints by using the comments of the corresponding DB columns
     */
    public $modelGenerateHintsFromComments = true;
    /**
     * @var array application configuration for creating temporary applications
     */
    protected $appConfig;

    /**
     * @var instance of class schmunk42\giiant\generators\model\Generator
     */
    protected $modelGenerator;

    /**
     * {@inheritdoc}
     */
    public function options($id)
    {
        return array_merge(
            parent::options($id),
            [
                'template',
                'overwrite',
                'useTranslatableBehavior',
                'useTimestampBehavior',
                'languageTableName',
                'languageCodeColumn',
                'extendedModels',
                'enableI18N',
                'messageCategory',
                'singularEntities',
                'tables',
                'tablePrefix',
                'modelDb',
                'modelNamespace',
                'modelBaseClass',
                'modelBaseTraits',
                'modelBaseClassSuffix',
                'modelRemoveDuplicateRelations',
                'modelGenerateQuery',
                'modelQueryNamespace',
                'modelQueryBaseClass',
                'modelGenerateLabelsFromComments',
                'modelGenerateHintsFromComments',
                'crudTidyOutput',
                'crudControllerNamespace',
                'crudSearchModelNamespace',
                'crudSearchModelSuffix',
                'crudViewPath',
                'crudPathPrefix',
                'crudProviders',
                'crudSkipRelations',
                'crudBaseControllerClass',
                'crudAccessFilter',
                'crudTemplate',
            ]
        );
    }

    /**
     * Loads application configuration and checks tables parameter.
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        $this->appConfig = $this->getYiiConfiguration();
        $this->appConfig['id'] = 'temp';
        $this->modelGenerator = new ModelGenerator(['db' => $this->modelDb]);

        if (!$this->tables) {
            $this->modelGenerator->tableName = '*';
            $this->tables = $this->modelGenerator->getTableNames();
            $tableList = implode("\n\t- ", $this->tables);
            $msg = "Are you sure that you want to run action \"{$action->id}\" for the following tables?\n\t- {$tableList}\n\n";
            if (!$this->confirm($msg)) {
                return false;
            }
        }

        return parent::beforeAction($action);
    }

    /**
     * Run batch process to generate models and CRUDs for all given tables.
     *
     * @param string $message the message to be echoed
     */
    public function actionIndex()
    {
        echo "Running full giiant batch...\n";
        $this->actionModels();
        $this->actionCruds();
    }

    /**
     * Run batch process to generate models all given tables.
     *
     * @throws \yii\console\Exception
     */
    public function actionModels()
    {
        // create models
        foreach ($this->tables as $table) {
            //var_dump($this->tableNameMap, $table);exit;
            $params = [
                'interactive' => $this->interactive,
                'overwrite' => $this->overwrite,
                'useTranslatableBehavior' => $this->useTranslatableBehavior,
                'useTimestampBehavior' => $this->useTimestampBehavior,
                'languageTableName' => $this->languageTableName,
                'languageCodeColumn' => $this->languageCodeColumn,
                'template' => $this->template,
                'ns' => $this->modelNamespace,
                'db' => $this->modelDb,
                'tableName' => $table,
                'tablePrefix' => $this->tablePrefix,
                'enableI18N' => $this->enableI18N,
                'singularEntities' => $this->singularEntities,
                'messageCategory' => $this->modelMessageCategory,
                'generateModelClass' => $this->extendedModels,
                'baseClassSuffix' => $this->modelBaseClassSuffix,
                'modelClass' => isset($this->tableNameMap[$table]) ?
                    $this->tableNameMap[$table] :
                    Inflector::camelize($table),
                'baseClass' => $this->modelBaseClass,
                'baseTraits' => $this->modelBaseTraits,
                'removeDuplicateRelations' => $this->modelRemoveDuplicateRelations,
                'tableNameMap' => $this->tableNameMap,
                'generateQuery' => $this->modelGenerateQuery,
                'queryNs' => $this->modelQueryNamespace,
                'queryBaseClass' => $this->modelQueryBaseClass,
                'generateLabelsFromComments' => $this->modelGenerateLabelsFromComments,
                'generateHintsFromComments' => $this->modelGenerateHintsFromComments,
            ];
            $route = 'gii/giiant-model';

            $app = \Yii::$app;
            $temp = new \yii\console\Application($this->appConfig);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
            \Yii::$app->log->logger->flush(true);
        }
    }

    /**
     * Run batch process to generate CRUDs all given tables.
     *
     * @throws \yii\console\Exception
     */
    public function actionCruds()
    {
        // create CRUDs
        $providers = ArrayHelper::merge($this->crudProviders, Generator::getCoreProviders());

        // create folders
        $this->createDirectoryFromNamespace($this->crudControllerNamespace);
        $this->createDirectoryFromNamespace($this->crudSearchModelNamespace);

        foreach ($this->tables as $table) {
            $table = str_replace($this->tablePrefix, '', $table);
            $name = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                $this->modelGenerator->generateClassName($table);
            $params = [
                'interactive' => $this->interactive,
                'overwrite' => $this->overwrite,
                'template' => $this->template,
                'modelClass' => $this->modelNamespace.'\\'.$name,
                'searchModelClass' => $this->crudSearchModelNamespace.'\\'.$name.$this->crudSearchModelSuffix,
                'controllerNs' => $this->crudControllerNamespace,
                'controllerClass' => $this->crudControllerNamespace.'\\'.$name.'Controller',
                'viewPath' => $this->crudViewPath,
                'pathPrefix' => $this->crudPathPrefix,
                'tablePrefix' => $this->tablePrefix,
                'enableI18N' => $this->enableI18N,
                'singularEntities' => $this->singularEntities,
                'messageCategory' => $this->crudMessageCategory,
                'modelMessageCategory' => $this->modelMessageCategory,
                'actionButtonClass' => 'yii\\grid\\ActionColumn',
                'baseControllerClass' => $this->crudBaseControllerClass,
                'providerList' => $providers,
                'skipRelations' => $this->crudSkipRelations,
                'accessFilter' => $this->crudAccessFilter,
                'baseTraits' => $this->crudBaseTraits,
                'tidyOutput' => $this->crudTidyOutput,
                'template' => $this->crudTemplate,
                'indexWidgetType' => $this->crudIndexWidgetType,
                'indexGridClass' => $this->crudIndexGridClass,
            ];
            $route = 'gii/giiant-crud';
            $app = \Yii::$app;
            $temp = new \yii\console\Application($this->appConfig);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
            \Yii::$app->log->logger->flush(true);
        }
    }

    /**
     * Returns Yii's initial configuration array.
     *
     * @todo should be removed, if this issue is closed -> https://github.com/yiisoft/yii2/pull/5687
     *
     * @return array
     */
    protected function getYiiConfiguration()
    {
        if (isset($GLOBALS['config'])) {
            $config = $GLOBALS['config'];
        } else {
            $config = \yii\helpers\ArrayHelper::merge(
                require(\Yii::getAlias('@app').'/../common/config/main.php'),
                (is_file(\Yii::getAlias('@app').'/../common/config/main-local.php')) ?
                    require(\Yii::getAlias('@app').'/../common/config/main-local.php')
                    : [],
                require(\Yii::getAlias('@app').'/../console/config/main.php'),
                (is_file(\Yii::getAlias('@app').'/../console/config/main-local.php')) ?
                    require(\Yii::getAlias('@app').'/../console/config/main-local.php')
                    : []
            );
        }

        return $config;
    }

    /**
     * Helper function to create.
     *
     * @param $ns Namespace
     */
    private function createDirectoryFromNamespace($ns)
    {
        echo \Yii::getRootAlias($ns);
        $dir = \Yii::getAlias('@'.str_replace('\\', '/', ltrim($ns, '\\')));
        @mkdir($dir);
    }
}
