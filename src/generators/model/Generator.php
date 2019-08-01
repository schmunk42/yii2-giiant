<?php
/**
 * @link http://www.phundament.com
 *
 * @copyright Copyright (c) 2014 herzog kommunikation GmbH
 * @license http://www.phundament.com/license/
 */
namespace schmunk42\giiant\generators\model;

use schmunk42\giiant\helpers\SaveForm;
use Yii;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 *
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
     * @var bool whether or not to use BlameableBehavior
     */
    public $useBlameableBehavior = true;

    /**
     * @var string the name of the column where the user who created the entry is stored
     */
    public $createdByColumn = 'created_by';

    /**
     * @var string the name of the column where the user who updated the entry is stored
     */
    public $updatedByColumn = 'updated_by';

    /**
     * @var bool whether or not to use TimestampBehavior
     */
    public $useTimestampBehavior = true;

    /**
     * @var string support user custom TimestampBehavior class
     */
    public $timestampBehaviorClass = 'yii\behaviors\TimestampBehavior';

    /**
     * @var string the name of the column where the user who updated the entry is stored
     */
    public $createdAtColumn = 'created_at';

    /**
     * @var string the name of the column where the user who updated the entry is stored
     */
    public $updatedAtColumn = 'updated_at';

    /**
     * @var bool whether or not to use 2amigos/yii2-translateable-behavior
     */
    public $useTranslatableBehavior = true;

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
     * @var string prefix to prepend to the base model, setting "Base" will result in a model named "BasePost"
     */
    public $baseClassPrefix = '';

    /**
     * @var string suffix to append to the base model, setting "Base" will result in a model named "PostBase"
     */
    public $baseClassSuffix = '';

    /**
     * @var array key-value pairs for mapping a table-name to class-name, eg. 'prefix_FOObar' => 'FooBar'
     */
    public $tableNameMap = [];
    public $singularEntities = false;

    public $removeDuplicateRelations = false;

    /**
     * @var bool This indicates whether the generator should generate attribute hints by using the comments of the corresponding DB columns
     */
    public $generateHintsFromComments = true;

    /**
     * @var string form field for selecting and loading saved gii forms
     */
    public $savedForm;

    public $messageCategory = 'models';

    protected $classNames2;

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant Model';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generates an ActiveRecord class and base class for the specified database table.';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [[
                    'generateModelClass',
                    'useTranslatableBehavior',
                    'generateHintsFromComments',
                    'useBlameableBehavior',
                    'useTimestampBehavior',
                    'singularEntities',
                    ], 'boolean'],
                [['languageTableName', 'languageCodeColumn', 'createdByColumn', 'updatedByColumn', 'createdAtColumn', 'updatedAtColumn', 'savedForm', 'timestampBehaviorClass'], 'string'],
                [['tablePrefix'], 'safe'],
            ]
        );
    }

    /**
     * all form fields for saving in saved forms.
     *
     * @return array
     */
    public function formAttributes()
    {
        return [
            'tableName',
            'tablePrefix',
            'modelClass',
            'ns',
            'baseClass',
            'db',
            'generateRelations',
            //'generateRelationsFromCurrentSchema',
            'generateLabelsFromComments',
            'generateHintsFromComments',
            'generateModelClass',
            'generateQuery',
            'queryNs',
            'queryClass',
            'queryBaseClass',
            'enableI18N',
            'singularEntities',
            'messageCategory',
            'useTranslatableBehavior',
            'languageTableName',
            'languageCodeColumn',
            'useBlameableBehavior',
            'createdByColumn',
            'updatedByColumn',
            'useTimestampBehavior',
            'createdAtColumn',
            'updatedAtColumn',
            'timestampBehaviorClass',
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'generateModelClass' => 'Generate Model Class',
                'generateHintsFromComments' => 'Generate Hints from DB Comments',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hints()
    {
        return array_merge(
            parent::hints(),
            [
                'generateModelClass' => 'This indicates whether the generator should generate the model class, this should usually be done only once. The model-base class is always generated.',
                'tablePrefix' => 'Custom table prefix, eg <code>app_</code>.<br/><b>Note!</b> overrides <code>yii\db\Connection</code> prefix!',
                'useTranslatableBehavior' => 'Use <code>2amigos/yii2-translateable-behavior</code> for tables with a relation to a translation table.',
                'languageTableName' => 'The name of the table containing the translations. <code>{{table}}</code> will be replaced with the value in "Table Name" field.',
                'languageCodeColumn' => 'The column name where the language code is stored.',
                'generateHintsFromComments' => 'This indicates whether the generator should generate attribute hints
                    by using the comments of the corresponding DB columns.',
                'useTimestampBehavior' => 'Use <code>TimestampBehavior</code> for tables with column(s) for created at and/or updated at timestamps.',
                'timestampBehaviorClass' => 'Use custom TimestampBehavior class.',
                'createdAtColumn' => 'The column name where the created at timestamp is stored.',
                'updatedAtColumn' => 'The column name where the updated at timestamp is stored.',
                'useBlameableBehavior' => 'Use <code>BlameableBehavior</code> for tables with column(s) for created by and/or updated by user IDs.',
                'createdByColumn' => "The column name where the record creator's user ID is stored.",
                'updatedByColumn' => "The column name where the record updater's user ID is stored.",
            ],
            SaveForm::hint()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['model.php', 'model-extended.php'];
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();

        foreach ($this->getTableNames() as $tableName) {
            list($relations, $translations) = array_values($this->extractTranslations($tableName, $relations));
//var_dump($relations,$tableName);exit;
            $className = $this->modelClass === '' || php_sapi_name() === 'cli'
                ? $this->generateClassName($tableName)
                : $this->modelClass;

            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($className) : false;
            $tableSchema = $db->getTableSchema($tableName);

            $params = [
                'tableName' => $tableName,
                'className' => $className,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'hints' => $this->generateHints($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'ns' => $this->ns,
                'enum' => $this->getEnum($tableSchema->columns),
            ];

            if (!empty($translations)) {
                $params['translation'] = $translations;
            }

            $params['blameable'] = $this->generateBlameable($tableSchema);
            $params['timestamp'] = $this->generateTimestamp($tableSchema);

            $files[] = new CodeFile(
                Yii::getAlias(
                    '@' . str_replace('\\', '/', $this->ns)
                ) . '/base/' . $this->baseClassPrefix . $className . $this->baseClassSuffix . '.php',
                $this->render('model.php', $params)
            );

            $modelClassFile = Yii::getAlias('@'.str_replace('\\', '/', $this->ns)).'/'.$className.'.php';
            if ($this->generateModelClass || !is_file($modelClassFile)) {
                $files[] = new CodeFile(
                    $modelClassFile,
                    $this->render('model-extended.php', $params)
                );
            }

            if ($queryClassName) {
                $queryClassFile = Yii::getAlias(
                        '@'.str_replace('\\', '/', $this->queryNs)
                    ).'/'.$queryClassName.'.php';
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

            /*
             * create gii/[name]GiiantModel.json with actual form data
             */
            $suffix = str_replace(' ', '', $this->getName());
            $formDataDir = Yii::getAlias('@'.str_replace('\\', '/', $this->ns));
            $formDataFile = StringHelper::dirname($formDataDir)
                    .'/gii'
                    .'/'.$tableName.$suffix.'.json';
            $generatorForm = (clone $this);
            $generatorForm->tableName = $tableName;
			$generatorForm->modelClass = $className;
            $formData = json_encode(SaveForm::getFormAttributesValues($generatorForm, $this->formAttributes()));
            $files[] = new CodeFile($formDataFile, $formData);
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

        //Yii::trace("Generating class name for '{$tableName}'...", __METHOD__);
        if (isset($this->classNames2[$tableName])) {
            //Yii::trace("Using '{$this->classNames2[$tableName]}' for '{$tableName}' from classNames2.", __METHOD__);
            return $this->classNames2[$tableName];
        }

        if (isset($this->tableNameMap[$tableName])) {
            Yii::trace("Converted '{$tableName}' from tableNameMap.", __METHOD__);

            return $this->classNames2[$tableName] = $this->tableNameMap[$tableName];
        }

        if (($pos = strrpos($tableName, '.')) !== false) {
            $tableName = substr($tableName, $pos + 1);
        }

        $db = $this->getDbConnection();
        $patterns = [];
        $patterns[] = "/^{$this->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$this->tablePrefix}$/";
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";

        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^'.str_replace('*', '(\w+)', $pattern).'$/';
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
        if ($this->singularEntities) {
            $returnName = Inflector::singularize($returnName);
        }

        Yii::trace("Converted '{$tableName}' to '{$returnName}'.", __METHOD__);

        return $this->classNames2[$tableName] = $returnName;
    }

    /**
     * Generates the attribute hints for the specified table.
     *
     * @param \yii\db\TableSchema $table the table schema
     *
     * @return array the generated attribute hints (name => hint)
     *               or an empty array if $this->generateHintsFromComments is false
     */
    public function generateHints($table)
    {
        $hints = [];

        if ($this->generateHintsFromComments) {
            foreach ($table->columns as $column) {
                if (!empty($column->comment)) {
                    $hints[$column->name] = $column->comment;
                }
            }
        }

        return $hints;
    }

    /**
     * {@inheritdoc}
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
        foreach ($relations as $model => $relInfo) {
            foreach ($relInfo as $relName => $relData) {

                // removed duplicated relations, eg. klientai, klientai0
                if ($this->removeDuplicateRelations && is_numeric(substr($relName, -1))) {
                    unset($relations[$model][$relName]);
                    continue;
                }

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
     * prepare ENUM field values.
     *
     * @param array $columns
     *
     * @return array
     */
    public function getEnum($columns)
    {
        $enum = [];
        foreach ($columns as $column) {
            if (!$this->isEnum($column)) {
                continue;
            }

            $column_camel_name = str_replace(' ', '', ucwords(implode(' ', explode('_', $column->name))));
            $enum[$column->name]['func_opts_name'] = 'opts'.$column_camel_name;
            $enum[$column->name]['func_get_label_name'] = 'get'.$column_camel_name.'ValueLabel';
            $enum[$column->name]['values'] = [];

            $enum_values = explode(',', substr($column->dbType, 4, strlen($column->dbType) - 1));

            foreach ($enum_values as $value) {
                $value = trim($value, "()'");

                $const_name = strtoupper($column->name.'_'.$value);
                $const_name = preg_replace('/\s+/', '_', $const_name);
                $const_name = str_replace(['-', '_', ' '], '_', $const_name);
                $const_name = preg_replace('/[^A-Z0-9_]/', '', $const_name);

                $label = Inflector::camel2words($value);

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
     * validate is ENUM.
     *
     * @param  $column table column
     *
     * @return type
     */
    public function isEnum($column)
    {
        return substr(strtoupper($column->dbType), 0, 4) == 'ENUM';
    }

    /**
     * Generates validation rules for the specified table and add enum value validation.
     *
     * @param \yii\db\TableSchema $table the table schema
     *
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $columns = [];
        foreach ($table->columns as $index => $column) {
            $isBlameableCol = ($column->name === $this->createdByColumn || $column->name === $this->updatedByColumn);
            $isTimestampCol = ($column->name === $this->createdAtColumn || $column->name === $this->updatedAtColumn);
            $removeCol = ($this->useBlameableBehavior && $isBlameableCol)
                || ($this->useTimestampBehavior && $isTimestampCol);
            if ($removeCol) {
                $columns[$index] = $column;
                unset($table->columns[$index]);
            }
        }

        $rules = [];

        //for enum fields create rules "in range" for all enum values
        $enum = $this->getEnum($table->columns);
        foreach ($enum as $field_name => $field_details) {
            $ea = array();
            foreach ($field_details['values'] as $field_enum_values) {
                $ea[] = 'self::'.$field_enum_values['const_name'];
            }
            $rules[] = "['".$field_name."', 'in', 'range' => [\n                    ".implode(
                    ",\n                    ",
                    $ea
                ).",\n                ]\n            ]";
        }

        // inject namespace for targetClass
        $parentRules = parent::generateRules($table);
        $ns = "\\{$this->ns}\\";
        $match = "'targetClass' => ";
        $replace = $match.$ns;
        foreach ($parentRules as $k => $parentRule) {
            $parentRules[$k] = str_replace($match, $replace, $parentRule);
        }

        $rules = array_merge($parentRules, $rules);
        $table->columns = array_merge($table->columns, $columns);

        return $rules;
    }

    /**
     * @return \yii\db\Connection the DB connection from the DI container or as application component specified by [[db]]
     */
    protected function getDbConnection()
    {
        if (Yii::$container->has($this->db)) {
            return Yii::$container->get($this->db);
        } else {
            return Yii::$app->get($this->db);
        }
    }

    /**
     * Validates the [[db]] attribute.
     */
    public function validateDb()
    {
        if (Yii::$container->has($this->db)) {
            return true;
        } else {
            return parent::validateDb();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTableNames()
    {
        return parent::getTableNames();
    }

    /**
     * @param $relations all database's relations
     *
     * @return array associative array containing the extracted relations and the modified translations
     */
    protected function extractTranslations($tableName, $relations)
    {
        $langTableName = str_replace('{{table}}', $tableName, $this->languageTableName);

        if ($this->useTranslatableBehavior and isset($relations[$langTableName], $relations[$tableName])) {
            $db = $this->getDbConnection();
            $langTableSchema = $db->getTableSchema($langTableName);
            $langTableColumns = $langTableSchema->getColumnNames();
            $langTableKeys = array_merge(
                $langTableSchema->primaryKey,
                array_map(
                    function ($fk) {
                        return array_keys($fk)[1];
                    },
                    $langTableSchema->foreignKeys
                )
            );
            $langClassName = $this->generateClassName($langTableName);

            foreach ($relations[$tableName] as $relationName => $relation) {
                list($code, $referencedClassName) = $relation;

                if ($referencedClassName === $langClassName) {
                    // found relation from model to modelLang.

                    // collect fields which are not PK, FK nor language code
                    $fields = [];
                    foreach ($langTableColumns as $columnName) {
                        if (!in_array($columnName, $langTableKeys) and strcasecmp(
                                $columnName,
                                $this->languageCodeColumn
                            ) !== 0
                        ) {
                            $fields[] = $columnName;
                        }
                    }

                    unset($relations[$tableName][$relationName]);

                    return [
                        'relations' => $relations,
                        'translations' => [
                            'fields' => $fields,
                            'code' => $code,
                            'language_table' => $langTableName,
                            'language_table_pk' => $langTableSchema->primaryKey,
                        ],
                    ];
                }
            }
        }

        return [
            'relations' => $relations,
            'translations' => [],
        ];
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     *
     * @return string[]
     */
    protected function generateBlameable($table)
    {
        $createdBy = $table->getColumn($this->createdByColumn) !== null ? $this->createdByColumn : false;
        $updatedBy = $table->getColumn($this->updatedByColumn) !== null ? $this->updatedByColumn : false;

        if ($this->useBlameableBehavior && ($createdBy || $updatedBy)) {
            return [
                'createdByAttribute' => $createdBy,
                'updatedByAttribute' => $updatedBy,
            ];
        }

        return [];
    }

    /**
     * @param \yii\db\TableSchema $table the table schema
     *
     * @return string[]
     */
    protected function generateTimestamp($table)
    {
        $createdAt = $table->getColumn($this->createdAtColumn) !== null ? $this->createdAtColumn : false;
        $updatedAt = $table->getColumn($this->updatedAtColumn) !== null ? $this->updatedAtColumn : false;

        if ($this->useTimestampBehavior && ($createdAt || $updatedAt)) {
            return [
                'createdAtAttribute'     => $createdAt,
                'updatedAtAttribute'     => $updatedAt,
                'timestampBehaviorClass' => $this->timestampBehaviorClass,
            ];
        }

        return [];
    }
}

