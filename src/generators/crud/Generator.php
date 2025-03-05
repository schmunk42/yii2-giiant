<?php
/**
 * @link      http://www.yiiframework.com/
 *
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license   http://www.yiiframework.com/license/
 */
namespace schmunk42\giiant\generators\crud;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use schmunk42\giiant\helpers\SaveForm;
use yii\helpers\VarDumper;

/**
 * This generator generates an extended version of CRUDs.
 *
 * @author Tobais Munk <schmunk@usrbin.de>
 *
 * @since 1.0
 */
class Generator extends \yii\gii\generators\crud\Generator
{
    use ParamTrait, ModelTrait, ProviderTrait;

    /**
     * @var null comma separated list of provider classes
     */
    public $providerList = null;
    /**
     * @todo review
     *
     * @var string
     */
    public $actionButtonClass = 'yii\grid\ActionColumn';
    /**
     * @var array relations to be excluded in UI rendering
     */
    public $skipRelations = [];
    /**
     * @var string default view path
     */
    public $viewPath = '@backend/views';

    /**
     * @var string table prefix to be removed from class names when auto-detecting model names, eg. `app_` converts table `app_foo` into `Foo`
     */
    public $tablePrefix = null;

    /**
     * @var string prefix for controller route, eg. when generating controllers into subfolders
     */
    public $pathPrefix = null;

    /**
     * @var string Bootstrap CSS-class for form-layout
     */
    public $formLayout = 'horizontal';

    /**
     * @var string translation catalogue
     */
    public $messageCategory = 'cruds';

    /**
     * @var string translation catalogue for model related translations
     */
    public $modelMessageCategory = 'models';

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

    /**
     * @var bool whether to convert controller name to singular
     */
    public $singularEntities = false;

    /**
     * @var bool whether to add an access filter to controllers
     */
    public $accessFilter = false;

    public $generateAccessFilterMigrations = false;

    public $baseTraits;

    /**
     * @var string controller base namespace
     */
    public $controllerNs;

    /**
     * @var bool whether to overwrite extended controller classes
     */
    public $overwriteControllerClass = false;

    /**
     * @var bool whether to overwrite rest/api controller classes
     */
    public $overwriteRestControllerClass = false;

    /**
     * @var bool whether to overwrite search classes
     */
    public $overwriteSearchModelClass = false;

    /**
     * @var bool whether to use phptidy on renderer files before saving
     */
    public $tidyOutput = false;

    /**
     * @var string command-line options for phptidy command
     */
    public $tidyOptions = '';

    /**
     * @var bool whether to use php-cs-fixer to generate PSR compatible output
     */
    public $fixOutput = false;

    /**
     * @var string command-line options for php-cs-fixer command
     */
    public $fixOptions = '';

    /**
     * @var bool whether to enable or disable the pluralization of the models name
     */
    public $disablePluralization = false;

    /**
     * @var string form field for selecting and loading saved gii forms
     */
    public $savedForm;

    public $moduleNs;

    public $migrationClass;

    public $indexGridClass = 'yii\\grid\\GridView';

    /**
     * @var string position of action column in gridview 'left' or 'right'
     */
    public $actionButtonColumnPosition = 'left';

    public $giiInfoPath = '.gii';

    private $_p = [];

    public $translateRelations = ['translation', 'translation_meta'];

    public $enableCopy = true;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $this->providerList = self::getCoreProviders();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant CRUD';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generates an extended version of CRUDs.';
    }

    /**
     * {@inheritdoc}
     */
    public function successMessage()
    {
        $return = 'The code has been generated successfully. Please require the following packages with composer:';
        $return .= '<br/><code>'.implode('<br/>', $this->requires).'</code>';

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'providerList' => 'Choose the providers to be used.',
                'viewPath' => 'Output path for view files, eg. <code>@backend/views/crud</code>.',
                'pathPrefix' => 'Customized route/subfolder for controllers and views eg. <code>crud/</code>. <b>Note!</b> Should correspond to <code>viewPath</code>.',
                'modelMessageCategory' => 'Model message categry.',
            ],
            SaveForm::hint()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
                parent::rules(), [
            [
                [
                    'providerList',
                    'actionButtonClass',
                    'viewPath',
                    'pathPrefix',
                    'savedForm',
                    'formLayout',
                    'accessFilter',
                    'generateAccessFilterMigrations',
                    'singularEntities',
                    'modelMessageCategory',
                    'enableCopy'
                ],
                'safe',
            ],
            [['viewPath'], 'required'],
                ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), ['providerList', 'actionButtonClass', 'viewPath', 'pathPrefix']);
    }

    /**
     * all form fields for saving in saved forms.
     *
     * @return array
     */
    public function formAttributes()
    {
        return [
            'modelClass',
            'searchModelClass',
            'controllerClass',
            'baseControllerClass',
            'viewPath',
            'pathPrefix',
            'enableI18N',
            'singularEntities',
            'indexWidgetType',
            'formLayout',
            'actionButtonClass',
            'providerList',
            'template',
            'accessFilter',
            'singularEntities',
            'modelMessageCategory',
            'enableCopy'
            ];
    }

    /**
     * @return string the action view file path
     */
    public function getViewPath()
    {
        if ($this->viewPath !== null) {
            return \Yii::getAlias($this->viewPath).'/'.$this->getControllerID();
        } else {
            return parent::getViewPath();
        }
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);
        if ($this->singularEntities) {
            $class = Inflector::singularize($class);
        }

        return Inflector::camel2id($class, '-', true);
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getModuleId()
    {
        if (!$this->moduleNs) {
            $controllerNs = StringHelper::dirname(ltrim($this->controllerClass, '\\'));
            $this->moduleNs = StringHelper::dirname(ltrim($controllerNs, '\\'));
        }

        return StringHelper::basename($this->moduleNs);
    }

    public function generate()
    {
        $accessDefinitions = require $this->getTemplatePath().'/access_definition.php';

        $this->controllerNs = StringHelper::dirname(ltrim($this->controllerClass, '\\'));
        $this->moduleNs = StringHelper::dirname(ltrim($this->controllerNs, '\\'));
        $controllerName = substr(StringHelper::basename($this->controllerClass), 0, -10);

        if ($this->singularEntities) {
            $this->modelClass = Inflector::singularize($this->modelClass);
            $this->controllerClass = Inflector::singularize(
                    substr($this->controllerClass, 0, strlen($this->controllerClass) - 10)
                ).'Controller';
            $this->searchModelClass = Inflector::singularize($this->searchModelClass);
        }

        $controllerFile = Yii::getAlias('@'.str_replace('\\', '/', ltrim($this->controllerClass, '\\')).'.php');
        $baseControllerFile = StringHelper::dirname($controllerFile).'/base/'.StringHelper::basename($controllerFile);
        $restControllerFile = StringHelper::dirname($controllerFile).'/api/'.StringHelper::basename($controllerFile);

        /*
         * search generated migration and overwrite it or create new
         */
        $migrationDir = StringHelper::dirname(StringHelper::dirname($controllerFile))
                    .'/migrations';

        if (file_exists($migrationDir) && $migrationDirFiles = glob($migrationDir.'/m*_'.$controllerName.'00_access.php')) {
            $this->migrationClass = pathinfo($migrationDirFiles[0], PATHINFO_FILENAME);
        } else {
            $this->migrationClass = 'm'.date('ymd_Hi').'00_'.$controllerName.'_access';
        }

        $files[] = new CodeFile($baseControllerFile, $this->render('controller.php', ['accessDefinitions' => $accessDefinitions]));
        $params['controllerClassName'] = StringHelper::basename($this->controllerClass);

        if ($this->overwriteControllerClass || !is_file($controllerFile)) {
            $files[] = new CodeFile($controllerFile, $this->render('controller-extended.php', $params));
        }

        if ($this->overwriteRestControllerClass || !is_file($restControllerFile)) {
            $files[] = new CodeFile($restControllerFile, $this->render('controller-rest.php', $params));
        }

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@'.str_replace('\\', '/', ltrim($this->searchModelClass, '\\').'.php'));
            if ($this->overwriteSearchModelClass || !is_file($searchModel)) {
                $files[] = new CodeFile($searchModel, $this->render('search.php'));
            }
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath().'/views';

        $model = Yii::createObject($this->modelClass);
        if (array_key_exists('crud-form', $model->scenarios())) {
            $model->setScenario('crud-form');
        } else {
            $model->setScenario('crud');
        }

        $safeAttributes = $model->safeAttributes();
        if (empty($safeAttributes)) {
            $model->setScenario('default');
            $safeAttributes = $model->safeAttributes();
        }
        if (empty($safeAttributes)) {
            $safeAttributes = $model::getTableSchema()->columnNames;
        }

        foreach (scandir($templatePath) as $file) {
            if ($file === '_search.php' && !$this->getRenderWithSearch()) {
                continue;
            }
            if (is_file($templatePath.'/'.$file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file", [
                    'model' => $model,
                    'safeAttributes' => $safeAttributes,
                    'accessDefinitions' => $accessDefinitions
                ]));
            }
        }

        if ($this->generateAccessFilterMigrations) {

            /*
             * access migration
             */
            $migrationFile = $migrationDir.'/'.$this->migrationClass.'.php';

            if (class_exists('dmstr\rbacMigration\Migration')) {
                $files[] = new CodeFile($migrationFile, $this->render('hrzg-rbac-migration-access.php'));
            } else {
                $files[] = new CodeFile($migrationFile, $this->render('migration_access.php', ['accessDefinitions' => $accessDefinitions]));
            }

            /*
             * access roles translation
             */
            $forRoleTranslationFile = StringHelper::dirname(StringHelper::dirname($controllerFile))
                    .'/messages/for-translation/'
                    .$controllerName.'.php';
            $files[] = new CodeFile($forRoleTranslationFile, $this->render('roles-translation.php', ['accessDefinitions' => $accessDefinitions]));
        }

        /*
         * create gii/[name]GiantCRUD.json with actual form data
         */
        $suffix = str_replace(' ', '', $this->getName());
        $controllerFileinfo = pathinfo($controllerFile);
        $formDataFile = StringHelper::dirname(StringHelper::dirname($controllerFile))
                .'/'.$this->giiInfoPath.'/'
                .str_replace('Controller', $suffix, $controllerFileinfo['filename']).'.json';
        $formData = json_encode(SaveForm::getFormAttributesValues($this, $this->formAttributes()), JSON_PRETTY_PRINT);
        $files[] = new CodeFile($formDataFile, $formData);

        return $files;
    }

    public function render($template, $params = [])
    {
        $code = parent::render($template, $params);

        // create temp file for code formatting
        $tmpDir = Yii::getAlias('@runtime/giiant');
        FileHelper::createDirectory($tmpDir);
        $tmpFile = $tmpDir.'/'.md5($template);
        file_put_contents($tmpFile, $code);

        if ($this->tidyOutput) {
            $command = Yii::getAlias('@vendor/bin/phptidy.php').' replace '.$this->tidyOptions.' '.$tmpFile;
            shell_exec($command);
            $code = file_get_contents($tmpFile);
        }

        if ($this->fixOutput) {
            $command = Yii::getAlias('@vendor/bin/php-cs-fixer').' fix '.$this->fixOptions.' '.$tmpFile;
            shell_exec($command);
            $code = file_get_contents($tmpFile);
        }

        unlink($tmpFile);

        return $code;
    }

    public function validateClass($attribute, $params)
    {
        if ($this->singularEntities) {
            $this->$attribute = Inflector::singularize($this->$attribute);
        }
        parent::validateClass($attribute, $params);
    }

    public function var_export54($var, $indent = '')
    {
        return VarDumper::export($var);
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function generateSearchRules()
    {

        $rules = parent::generateSearchRules();
        $model = \Yii::createObject($this->modelClass);
        foreach ($model->behaviors() as $key => $behavior) {
            if (!empty($behavior['translationAttributes'])) {
                $rules[] = "[['" . implode("', '", $behavior['translationAttributes']) . "'], 'safe']";
            }
        }
        return $rules;
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function generateSearchConditions()
    {

        $searchConditions = parent::generateSearchConditions();
        $model = \Yii::createObject($this->modelClass);
        foreach ($model->behaviors() as $key => $behavior) {
            if (!empty($behavior['translationAttributes'])) {
                foreach ($behavior['translationAttributes'] as $translationAttribute) {
                    $searchConditions[] = "\$query->andFilterWhere(['like','{$translationAttribute}', \$this->$translationAttribute]);";
                }
            }
        }
        return $searchConditions;
    }


    /**
     * @return array
     */
    public function getTranslationRelationModels()
    {
        $translationRelationModels = [];
        foreach ($this->translateRelations as $translateRelation) {
            $translationRelationModels[] = $this->modelClass . Inflector::camelize($translateRelation);
        }
        return $translationRelationModels;
    }

    /**
     * @return string
     */
    public function getTranslationModelClass() {
        return '\\' . $this->modelClass . Inflector::camelize('translation');
    }

    /**
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function getHasTranslationRelation() {
        return isset(\Yii::createObject($this->modelClass)->behaviors()['translation']);
    }

    public function getRenderWithSearch()
    {
        return $this->indexWidgetType !== 'grid' && $this->searchModelClass !== '';
    }
}
