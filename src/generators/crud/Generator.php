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
    public $actionButtonClass = 'yii\web\grid\ActionColumn';
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

    /**
     * @var bool whether to convert controller name to singular
     */
    public $singularEntities = false;

    /**
     * @var bool whether to add an access filter to controllers
     */
    public $accessFilter = false;

    /**
     * @var sting controller base namespace
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
     * @var array whether to use phptidy on renderer files before saving
     */
    public $tidyOutput;

    /**
     * @var string form field for selecting and loading saved gii forms 
     */
    public $savedForm;

    /** 
     * @var array all saved crud forms list found in module gii directories 
     */
    private $savedFormList;
    
    private $_p = [];

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
                'savedForm' => 'Choose saved form ad load it data to form.',
                'providerList' => 'Choose the providers to be used.',
                'viewPath' => 'Output path for view files, eg. <code>@backend/views/crud</code>.',
                'pathPrefix' => 'Customized route/subfolder for controllers and views eg. <code>crud/</code>. <b>Note!</b> Should correspond to <code>viewPath</code>.',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['providerList', 'actionButtonClass', 'viewPath', 'pathPrefix','savedForm','formLayout'], 'safe'],
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
     * all form fields for saving in saved forms
     * @return array
     */
    public function formAttributes()
    {
        return ['modelClass','searchModelClass','controllerClass',
            'baseControllerClass','viewPath','pathPrefix','enableI18N',
            'singularEntities','indexWidgetType','formLayout',
            'actionButtonClass', 'providerList'];
    }

    /**
     * get form attributes values.
     */
    public function getFormAttributesValues()
    {
        $attributes = $this->formAttributes();
        $values = [];
        foreach ($attributes as $name) {
            $values[strtolower($name)] = [
                'value' => $this->$name,
                'name' => $name,
                ];
        }
        
        return $values;
    }    
    
    /**
     * walk througt all modules gii directories and collect Giant crud generator saved forms
     * 
     * @return array
     */
    public function loadSavedForms(){
        
        if($this->savedFormList){
            return $this->savedFormList;
        }        
        
        foreach(Yii::$app->modules as $moduleId =>$module){
        
            /**
             * get module base path
             */
            if (method_exists($module, 'getBasePath')){
                $basePath = $module->getBasePath();
            }else{
                $reflector = new \ReflectionClass($module['class']);
                $basePath = StringHelper::dirname($reflector->getFileName());
            }
            $basePath .= '/gii';

            /**
             * search in module gii directory all controller forms json files 
             */
            if (!file_exists($basePath)){
                continue;
            }
            $files = scandir($basePath);
            foreach($files as $file){
                if(!preg_match('#Controller\.json$#',$file)){
                    continue;
                }
                $name=preg_replace('#Controller\.json$#','',$file);
                $forms[$moduleId.$name] =[ 
                    'jsonData' => file_get_contents($basePath . '/' . $file),
                    'label' => $moduleId . ' - ' . $name,
                    ];
            }
        }

        return $this->savedFormList = $forms;
    }
    
    /**
     * get array for form field "Saved form" data
     * @return array
     */
    public function getSavedFormsListbox(){
        $r = ['0'=>' - '];
        foreach($this->loadSavedForms() as $k => $row){
            $r[$k] =  $row['label'];
        }
        return $r;
    }

    /**
     * creata js statement for seting to variable savedFormas array with all forms and it data in json format
     * @return string
     */
    public function getSavedFormsJs(){
        $js = [];
        
        foreach($this->loadSavedForms() as $k => $row){
            $js[] =  $k . ":'" . $row['jsonData'] . "'";
        }
        
        return "var savedForms = {" . str_replace('\\','\\\\',implode(',',$js)) . "};";
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

    public function generate()
    {
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

        $files[] = new CodeFile($baseControllerFile, $this->render('controller.php'));
        $params['controllerClassName'] = \yii\helpers\StringHelper::basename($this->controllerClass);

        if ($this->overwriteControllerClass || !is_file($controllerFile)) {
            $files[] = new CodeFile($controllerFile, $this->render('controller-extended.php', $params));
        }

        if ($this->overwriteRestControllerClass || !is_file($restControllerFile)) {
            $files[] = new CodeFile($restControllerFile, $this->render('controller-rest.php', $params));
        }

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@'.str_replace('\\', '/', ltrim($this->searchModelClass, '\\').'.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath().'/views';
        foreach (scandir($templatePath) as $file) {
            if (empty($this->searchModelClass) && $file === '_search.php') {
                continue;
            }
            if (is_file($templatePath.'/'.$file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        /**
         * create gii/[name]Controller.json with actual form data
         */
        $controllerFileinfo = pathinfo($controllerFile);
        $formDataFile = StringHelper::dirname(StringHelper::dirname($controllerFile)) 
                . '/gii/'
                . $controllerFileinfo['filename'].'.json' ;
        $formData = json_encode($this->getFormAttributesValues());
        $files[] = new CodeFile($formDataFile, $formData);
        
        $this->loadSavedForms();
        
        return $files;
    }

    public function render($template, $params = [])
    {
        $code = parent::render($template, $params);
        if ($this->tidyOutput) {
            $tmpDir = Yii::getAlias('@runtime/giiant');
            FileHelper::createDirectory($tmpDir);
            $tmpFile = $tmpDir.'/'.md5($template);
            file_put_contents($tmpFile, $code);

            shell_exec('vendor'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'phptidy replace '.$tmpFile);

            return file_get_contents($tmpFile);
        } else {
            return $code;
        }
    }

    public function validateClass($attribute, $params)
    {
        if ($this->singularEntities) {
            $this->$attribute = Inflector::singularize($this->$attribute);
        }
        parent::validateClass($attribute, $params);
    }
}
