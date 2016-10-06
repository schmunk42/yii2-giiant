<?php

namespace schmunk42\giiant\generators\crud;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use schmunk42\giiant\generators\model\Generator as ModelGenerator;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;

trait ModelTrait
{
    public function getModelNameAttribute($modelClass)
    {
        $model = new $modelClass();
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
                case 'ns'://name short
                case 'nl'://name long
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
        if ($this->singularEntities) {
            $returnName = Inflector::singularize($returnName);
        }

        return $returnName;
    }

    /**
     * Finds relations of a model class.
     *
     * return values can be filtered by types 'belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'
     *
     * @param ActiveRecord $modelClass
     * @param array        $types
     *
     * @return array
     */
    public function getModelRelations($modelClass, $types = ['belongs_to', 'many_many', 'has_many', 'has_one', 'pivot'])
    {
        $reflector = new \ReflectionClass($modelClass);
        $model = new $modelClass();
        $stack = [];
        $modelGenerator = new ModelGenerator();
        foreach ($reflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
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
                'getOldAttribute',
            ];
            if (in_array($method->name, $skipMethods)) {
                continue;
            }
            // check for relation
            try {
                $relation = @call_user_func(array($model, $method->name));
                if ($relation instanceof \yii\db\ActiveQuery) {
                    //var_dump($relation->primaryModel->primaryKey);
                    if ($relation->multiple === false) {
                        $relationType = 'belongs_to';
                    } elseif ($this->isPivotRelation($relation)) { // TODO: detecttion
                        $relationType = 'pivot';
                    } else {
                        $relationType = 'has_many';
                    }

                    if (in_array($relationType, $types)) {
                        $name = $modelGenerator->generateRelationName(
                            [$relation],
                            $model->getTableSchema(),
                            substr($method->name, 3),
                            $relation->multiple
                        );
                        $stack[$name] = $relation;
                    }
                }
            } catch (Exception $e) {
                Yii::error('Error: '.$e->getMessage(), __METHOD__);
            }
        }

        return $stack;
    }

    public function getColumnByAttribute($attribute, $model = null)
    {
        if (is_string($model)) {
            $model = new $model();
        }
        if ($model === null) {
            $model = $this;
        }

        return $model->getTableSchema()->getColumn($attribute);
    }

    /**
     * @param $column
     *
     * @return null|\yii\db\ActiveQuery
     */
    public function getRelationByColumn($model, $column)
    {
        $relations = $this->getModelRelations($model);
        foreach ($relations as $relation) {
            // TODO: check multiple link(s)
            if ($relation->link && reset($relation->link) == $column->name) {
                return $relation;
            }
        }

        return;
    }

    public function createRelationRoute($relation, $action)
    {
        $route = $this->pathPrefix.Inflector::camel2id(
                $this->generateRelationTo($relation),
                '-',
                true
            ).'/'.$action;

        return $route;
    }

    public function generateRelationTo($relation)
    {
        $class = new \ReflectionClass($relation->modelClass);
        $route = Inflector::variablize($class->getShortName());

        return $route;
    }

    public function isPivotRelation(ActiveQuery $relation)
    {
        $model = new $relation->modelClass();
        $table = $model->tableSchema;
        $pk = $table->primaryKey;
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
}
