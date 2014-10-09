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
     * @var bool whether to generate and overwrite all files
     */
    public $generate = false;

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
    public $tableNameMap = [];

    public $modelNamespace = 'common\\models';
    public $modelDb = 'db';
    public $modelBaseClass = 'yii\db\ActiveRecord';


    public $crudControllerNamespace = 'backend\\controllers\\crud';
    public $crudViewPath = '@backend/views/crud';
    public $crudPathPrefix = 'crud/';
    public $crudProviders = [];
    public $crudBaseControllerClass = 'yii\web\Controller';

    /**
     * @inheritdoc
     */
    public function options($id)
    {
        return array_merge(
            parent::options($id),
            [
                'generate',
                'extendedModels',
                'tables',
                'tablePrefix',
                'modelDb',
                'modelNamespace',
                'modelBaseClass',
                'crudControllerNamespace',
                'crudViewPath',
                'crudProviders',
                'crudBaseControllerClass'
            ]
        );
    }

    /**
     * This command echoes what you have entered as the message.
     *
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        echo "Running batch...\n";

        $config       = $this->getYiiConfiguration();
        $config['id'] = 'temp';

        // create models
        foreach ($this->tables AS $table) {
            #var_dump($this->tableNameMap, $table);exit;
            $params = [
                'template'           => 'default',
                'ns'                 => $this->modelNamespace,
                'db'                 => $this->modelDb,
                'tableName'          => $table,
                'tablePrefix'        => $this->tablePrefix,
                'generateModelClass' => $this->extendedModels,
                'modelClass'         => isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] :
                        Inflector::camelize($table), // TODO: setting is not recognized in giiant
                'baseClass'          => $this->modelBaseClass,
                'tableNameMap'       => $this->tableNameMap
            ];
            $route   = 'gii/giiant-model';

            $app  = \Yii::$app;
            $temp = new \yii\console\Application($config);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
        }


        // create CRUDs
        $providers = ArrayHelper::merge($this->crudProviders, Generator::getCoreProviders());
        foreach ($this->tables AS $table) {
            $name   = isset($this->tableNameMap[$table]) ? $this->tableNameMap[$table] : Inflector::camelize($table);
            $params = [
                'template'            => 'default',
                'modelClass'          => $this->modelNamespace . '\\' . $name,
                'searchModelClass'    => $this->modelNamespace . '\\search\\' . $name . 'Search',
                'controllerClass'     => $this->crudControllerNamespace . '\\' . $name . 'Controller',
                'viewPath'            => $this->crudViewPath,
                'pathPrefix'          => $this->crudPathPrefix,
                'actionButtonClass'   => 'yii\\grid\\ActionColumn',
                'baseControllerClass' => $this->crudBaseControllerClass,
                'providerList'        => implode(',', $providers),
            ];
            $route  = 'gii/giiant-crud';
            $app  = \Yii::$app;
            $temp = new \yii\console\Application($config);
            $temp->runAction(ltrim($route, '/'), $params);
            unset($temp);
            \Yii::$app = $app;
        }
    }

    protected function getYiiConfiguration()
    {
        $config = \yii\helpers\ArrayHelper::merge(
            require(\Yii::getAlias('@app') . '/../common/config/main.php'),
            require(\Yii::getAlias('@app') . '/../common/config/main-local.php'),
            require(\Yii::getAlias('@app') . '/../console/config/main.php'),
            require(\Yii::getAlias('@app') . '/../console/config/main-local.php')
        );
        return $config;
    }
}