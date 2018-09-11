<?php

namespace schmunk42\giiant\generators\test;

use Yii;
use yii\gii\CodeFile;
use yii\db\Schema;

/**
 * This generator generates unit tests for crud operations.
 *
 * @author Github: gradosevic
 */
class Generator extends \schmunk42\giiant\generators\model\Generator
{
    /**
     * @var string
     */
    public $ns = 'app\tests\codeception\unit';

    /**
     * @var string Codeception's root path
     */
    public $codeceptionPath = '/tests/codeception/';

    /**
     * @var string Controller's class name
     */
    public $controllerClass = '';

    /**
     * @var string Model's class name
     */
    public $modelClass = '';

    public $modelNs = '';

    /**
     * @var string Search model's class name
     */
    public $searchModelClass = '';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Giiant Test';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'This generator generates unit tests for specified model';
    }

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        return ['unit.php'];
    }

    /**
     * @param $table Table schema
     *
     * @return array Attributes containing all required model's information for test generator
     */
    public function generateAttributes($table)
    {
        $labels = $this->generateLabels($table);
        $attributes = [];
        foreach ($table->columns as $column) {
            $label = $column->name;
            if (isset($labels[$column->name])) {
                $label = $labels[$column->name];
            }
            $attribute = [];
            $attribute['name'] = $column->name;
            $attribute['null'] = $column->allowNull;
            $attribute['size'] = $column->size;
            $attribute['primary'] = $column->isPrimaryKey;
            $attribute['label'] = $label;
            if ($column->autoIncrement) {
                $attribute['autoincrement'] = 'true';
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $attribute['required'] = 'true';
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $attribute['type'] = 'integer';
                    break;
                case Schema::TYPE_BOOLEAN:
                    $attribute['type'] = 'boolean';
                    break;
                case Schema::TYPE_FLOAT:
                case 'double': // Schema::TYPE_DOUBLE, which is available since Yii 2.0.3
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $attribute['type'] = 'number';
                    break;
                case Schema::TYPE_DATE:
                    $attribute['type'] = 'date';
                case Schema::TYPE_TIME:
                    $attribute['type'] = 'time';
                case Schema::TYPE_DATETIME:
                    $attribute['type'] = 'datetime';
                case Schema::TYPE_TIMESTAMP:
                    $attribute['type'] = 'timestamp';
                    break;
                default: // strings
                    $attribute['type'] = 'string';
            }
            $attributes[] = $attribute;
        }

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        $files = [];
       // $relations = $this->generateRelations();
        $db = $this->getDbConnection();

        $class = $this->modelNs.$this->modelClass;
        $classTableNameMethod = 'tableName';
        $this->tableName = $class::$classTableNameMethod();

        //TODO: Add unit tests for search model
        //if($this->searchModelClass !=="")
        //{
        //}

        foreach ($this->getTableNames() as $tableName) {
            $className = $this->generateClassName($tableName);
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $className,
                'modelClass' => $this->modelClass,
                'controllerClass' => $this->controllerClass,
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'attributes' => $this->generateAttributes($tableSchema),
                //TODO: Add unit tests for relations
                //'relations'      => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'ns' => $this->ns,
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@app/..'.$this->codeceptionPath.str_replace('\\', '/', $this->ns)).'/'.$this->baseClassPrefix.$className.$this->baseClassSuffix.'UnitTest.php',
                $this->render('unit.php', $params)
            );
        }

        return $files;
    }
}

