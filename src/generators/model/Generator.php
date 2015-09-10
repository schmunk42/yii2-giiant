<?php
/**
 * @link http://www.phundament.com
 * @copyright Copyright (c) 2014 herzog kommunikation GmbH
 * @license http://www.phundament.com/license/
 */

namespace schmunk42\giiant\generators\model;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\Inflector;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 * @since 0.0.1
 */
class Generator extends \yii\gii\generators\model\Generator
{
    /**
     * @var bool whether to overwrite (extended) model classes, will be always created, if file does not exist
     */
    public $generateModelClass = false;

    /**
     * @var string base-traits
     */
    public $baseTraits = null;


    /**
     * @var null string for the table prefix, which is ignored in generated class name
     */
    public $tablePrefix = null;

    /**
     * @var bool whether to use or not 2amigos/yii2-translateable-behavior
     */
    public $useTranslatableBehavior = true;

    /**
     * @var string the name of the table containing the translations. {{table}} will be replaced with the value in
     * "Table Name" field.
     */
    public $languageTableName = "{{table}}_lang";

    /**
     * @var string the column name where the language code is stored.
     */
    public $languageCodeColumn = "language";

    /**
     * @var string suffix to append to the base model, setting "Base" will result in a model named "PostBase"
     */
    public $baseClassSuffix = '';

    /**
     * @var array key-value pairs for mapping a table-name to class-name, eg. 'prefix_FOObar' => 'FooBar'
     */
    public $tableNameMap = [];
    protected $classNames2;
    public $singularEntities = false;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Giiant Model';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['generateModelClass', 'useTranslatableBehavior'], 'boolean'],
                [['languageTableName', 'languageCodeColumn'], 'string'],
                [['tablePrefix'], 'safe'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'generateModelClass' => 'Generate Model Class',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'generateModelClass' => 'This indicates whether the generator should generate the model class, this should usually be done only once. The model-base class is always generated.',
                'tablePrefix'        => 'Custom table prefix, eg <code>app_</code>.<br/><b>Note!</b> overrides <code>yii\db\Connection</code> prefix!',
                'useTranslatableBehavior' => 'Use <code>2amigos/yii2-translateable-behavior</code> for tables with a relation to a translation table.',
                'languageTableName' => 'The name of the table containing the translations. <code>{{table}}</code> will be replaced with the value in "Table Name" field.',
                'languageCodeColumn' => 'The column name where the language code is stored.',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['model.php', 'model-extended.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files     = [];
        $relations = $this->generateRelations();
        $db        = $this->getDbConnection();

        foreach ($this->getTableNames() as $tableName) {

            list($relations, $translations) = array_values($this->extractTranslations($tableName, $relations));

            $className = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($className) : false;
            $tableSchema = $db->getTableSchema($tableName);

            $params      = [
                'tableName'      => $tableName,
                'className'      => $className,
                'queryClassName' => $queryClassName,
                'tableSchema'    => $tableSchema,
                'labels'         => $this->generateLabels($tableSchema),
                'rules'          => $this->generateRules($tableSchema),
                'relations'      => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'ns'             => $this->ns,
                'enum'           => $this->getEnum($tableSchema->columns),
            ];

            if (!empty($translations)) {
                $params['translation'] = $translations;
            }

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/base/' . $className . $this->baseClassSuffix . '.php',
                $this->render('model.php', $params)
            );

            $modelClassFile = Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $className . '.php';
            if ($this->generateModelClass || !is_file($modelClassFile)) {
                $files[] = new CodeFile(
                    $modelClassFile,
                    $this->render('model-extended.php', $params)
                );
            }

            if ($queryClassName) {
                $queryClassFile = Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php';
                if ($this->generateModelClass || !is_file($queryClassFile)) {
                    $params = [
                        'className' => $queryClassName,
                        'modelClassName' => $className,
                    ];
                    $files[] = new CodeFile(
                        $queryClassFile,
                        $this->render('query.php', $params)
                    );
                }
            }

        }
        return $files;
    }

    /**
     * Generates a class name from the specified table name.
     *
     * @param string $tableName the table name (which may contain schema prefix)
     *
     * @return string the generated class name
     */
    public function generateClassName($tableName, $useSchemaName = null)
    {

        #Yii::trace("Generating class name for '{$tableName}'...", __METHOD__);
        if (isset($this->classNames2[$tableName])) {
            #Yii::trace("Using '{$this->classNames2[$tableName]}' for '{$tableName}' from classNames2.", __METHOD__);
            return $this->classNames2[$tableName];
        }

        if (isset($this->tableNameMap[$tableName])) {
            Yii::trace("Converted '{$tableName}' from tableNameMap.", __METHOD__);
            return $this->classNames2[$tableName] = $this->tableNameMap[$tableName];
        }

        if (($pos = strrpos($tableName, '.')) !== false) {
            $tableName = substr($tableName, $pos + 1);
        }

        $db         = $this->getDbConnection();
        $patterns   = [];
        $patterns[] = "/^{$this->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$this->tablePrefix}$/";
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";

        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }

        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                Yii::trace("Mapping '{$tableName}' to '{$className}' from pattern '{$pattern}'.", __METHOD__);
                break;
            }
        }

        $returnName = Inflector::id2camel($className, '_');
        if ($this->singularEntities) $returnName = Inflector::singularize($returnName);

        Yii::trace("Converted '{$tableName}' to '{$returnName}'.", __METHOD__);
        return $this->classNames2[$tableName] = $returnName;
    }

    /**
     * @inheritdoc
     */
    public function generateRelationName($relations, $table, $key, $multiple)
    {
        return parent::generateRelationName($relations, $table, $key, $multiple);
    }

    protected function generateRelations()
    {
        $relations = parent::generateRelations();

        // inject namespace
        $ns = "\\{$this->ns}\\";
        foreach ($relations AS $model => $relInfo) {
            foreach ($relInfo AS $relName => $relData) {

                $relations[$model][$relName][0] = preg_replace(
                    '/(has[A-Za-z0-9]+\()([a-zA-Z0-9]+::)/',
                    '$1__NS__$2',
                    $relations[$model][$relName][0]
                );
                $relations[$model][$relName][0] = str_replace('__NS__', $ns, $relations[$model][$relName][0]);
            }
        }
        return $relations;
    }

    /**
     * prepare ENUM field values
     * @param array $columns
     * @return array
     */
    public function getEnum($columns){

        $enum = [];
        foreach ($columns as $column) {
            if (!$this->isEnum($column)) {
                continue;
            }

            $column_camel_name = str_replace(' ', '', ucwords(implode(' ', explode('_', $column->name))));
            $enum[$column->name]['func_opts_name'] = 'opts' . $column_camel_name;
            $enum[$column->name]['func_get_label_name'] = 'get' . $column_camel_name.'ValueLabel';
            $enum[$column->name]['values'] = [];

            $enum_values = explode(',', substr($column->dbType, 4, strlen($column->dbType) - 1));

            foreach ($enum_values as $value) {

                $value = trim($value, "()'");

                $const_name = strtoupper($column->name . '_' . $value);
                $const_name = preg_replace('/\s+/','_',$const_name);
                $const_name = str_replace(['-','_',' '],'_',$const_name);
				$const_name=preg_replace('/[^A-Z0-9_]/', '', $const_name);

                $label = ucwords(trim(strtolower(str_replace(['-', '_'], ' ', preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $value)))));
                $label = preg_replace('/\s+/', ' ', $label);

                $enum[$column->name]['values'][] = [
                    'value' => $value,
                    'const_name' => $const_name,
                    'label' => $label,
                    ];

            }
        }
        return $enum;

    }

    /**
     * validate is ENUM
     * @param  $column table column
     * @return type
     */
    public function isEnum($column){
        return substr(strtoupper($column->dbType), 0, 4) == 'ENUM';
    }


    /**
     * Generates validation rules for the specified table and add enum value validation.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $rules = [];

        //for enum fields create rules "in range" for all enum values
        $enum = $this->getEnum($table->columns);
        foreach($enum as $field_name => $field_details){
            $ea = array();
            foreach($field_details['values'] as $field_enum_values){
                $ea[] = 'self::'.$field_enum_values['const_name'];
            }
            $rules[] = "['" .$field_name . "', 'in', 'range' => [\n                    " . implode(",\n                    ",$ea) . ",\n                ]\n            ]";
        }

        return array_merge(parent::generateRules($table),$rules);
    }

    /**
     * @inheritdoc
     */
    public function getTableNames()
    {
        return parent::getTableNames();
    }

    /**
     * @param $relations all database's relations.
     * @return array associative array containing the extracted relations and the modified translations.
     */
    protected function extractTranslations($tableName, $relations)
    {
        $langTableName = str_replace("{{table}}", $tableName, $this->languageTableName);

        if ($this->useTranslatableBehavior and isset($relations[$langTableName], $relations[$tableName])) {
            $db = $this->getDbConnection();
            $langTableSchema = $db->getTableSchema($langTableName);
            $langTableColumns = $langTableSchema->getColumnNames();
            $langTableKeys = array_merge(
                $langTableSchema->primaryKey,
                array_map(function($fk){
                    return array_keys($fk)[1];
                }, $langTableSchema->foreignKeys)
            );
            $langClassName = $this->generateClassName($langTableName);

            foreach ($relations[$tableName] as $relationName => $relation) {

                list($code, $referencedClassName) = $relation;

                if ($referencedClassName === $langClassName) {
                    // found relation from model to modelLang.

                    // collect fields which are not PK, FK nor language code
                    $fields = [];
                    foreach ($langTableColumns as $columnName) {
                        if (!in_array($columnName, $langTableKeys) and strcasecmp($columnName, $this->languageCodeColumn) !== 0) {
                            $fields[] = $columnName;
                        }
                    }

                    unset($relations[$tableName][$relationName]);
                    return [
                        'relations' => $relations,
                        'translations' => [
                            'fields' => $fields,
                            'code' => $code
                        ]
                    ];

                }
            }
        }

        return [
            'relations' => $relations,
            'translations' => []
        ];

    }

}
