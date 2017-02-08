<?php

namespace schmunk42\giiant\generators\crud;

/*
 * @link http://www.diemeisterei.de/
 * @copyright Copyright (c) 2015 diemeisterei GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Json;

trait ProviderTrait
{
    /**
     * @return array Class names of the providers declared directly under crud/providers folder
     */
    public static function getCoreProviders()
    {
        $files = FileHelper::findFiles(
            __DIR__.DIRECTORY_SEPARATOR.'providers/core',
            [
                'only' => ['*.php'],
                'recursive' => false,
            ]
        );

        foreach ($files as $file) {
            require_once $file;
        }

        return array_filter(
            get_declared_classes(),
            function ($a) {
                return stripos($a, __NAMESPACE__.'\providers') !== false;
            }
        );
    }

    /**
     * @return array List of providers. Keys and values contain the same strings
     */
    public function generateProviderCheckboxListData()
    {
        $coreProviders = self::getCoreProviders();

        return array_combine($coreProviders, $coreProviders);
    }

    protected function initializeProviders()
    {
        // TODO: this is a hotfix for an already initialized provider queue on action re-entry
        if ($this->_p !== []) {
            return;
        }

        if ($this->providerList) {
            foreach ($this->providerList as $class) {
                $class = trim($class);
                if (!$class) {
                    continue;
                }
                $obj = \Yii::createObject(['class' => $class]);
                $obj->generator = $this;
                $this->_p[] = $obj;
                //\Yii::trace("Initialized provider '{$class}'", __METHOD__);
            }
        }

        \Yii::trace("CRUD providers initialized for model '{$this->modelClass}'", __METHOD__);
    }

    /**
     * Generates code for active field by using the provider queue.
     *
     * @param ColumnSchema $column
     * @param null         $model
     *
     * @return mixed|string
     */
    public function activeField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);

            return $code;
        } else {
            $column = $this->getColumnByAttribute($attribute);
            if (!$column) {
                return;
            } else {
                return parent::generateActiveField($attribute);
            }
        }
    }

    public function prependActiveField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        }

        return $code;
    }

    public function appendActiveField($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        }

        return $code;
    }

    public function columnFormat($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);
        } else {
            $code = $this->shorthandAttributeFormat($attribute, $model);
            Yii::trace("using standard formatting for '{$attribute}'", __METHOD__);
        }

        return $code;
    }

    public function attributeFormat($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);

            return $code;
        }

        $column = $this->getColumnByAttribute($attribute);
        if (!$column) {
            return;
        } else {
            return $this->shorthandAttributeFormat($attribute, $model);
        }
        // don't call parent anymore
    }

    public function attributeEditable($attribute, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $attribute, $model, $this);
        if ($code !== null) {
            Yii::trace("found provider for '{$attribute}'", __METHOD__);

            return $code;
        }

        $column = $this->getColumnByAttribute($attribute);
        if (!$column) {
            return;
        } else {
            return $this->shorthandAttributeFormat($attribute, $model);
        }
        // don't call parent anymore
    }

    public function partialView($name, $model = null)
    {
        if ($model === null) {
            $model = $this->modelClass;
        }
        $code = $this->callProviderQueue(__FUNCTION__, $name, $model, $this);
        if ($code) {
            Yii::trace("found provider for partial view '{name}'", __METHOD__);
        }

        return $code;
    }

    public function relationGrid($name, $relation, $showAllRecords = false)
    {
        Yii::trace("calling provider queue for '$name'", __METHOD__);

        return $this->callProviderQueue(__FUNCTION__, $name, $relation, $showAllRecords);
    }

    public function relationGridEditable($name, $relation, $showAllRecords = false)
    {
        Yii::trace("calling provider queue for '$name'", __METHOD__);

        return $this->callProviderQueue(__FUNCTION__, $name, $relation, $showAllRecords);
    }

    protected function shorthandAttributeFormat($attribute, $model)
    {
        // TODO: cleanup
        if (is_object($model) && (!method_exists($model,'getTableSchema') || !$model->getTableSchema())){
            return;
        }

        $column = $this->getColumnByAttribute($attribute, $model);
        if (!$column) {
            Yii::trace("No column for '{$attribute}' found", __METHOD__);

            return;
        } else {
            Yii::trace("Table column detected for '{$attribute}'", __METHOD__);
        }
        if ($column->phpType === 'boolean') {
            $format = 'boolean';
        } elseif ($column->type === 'text') {
            $format = 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            $format = 'datetime';
        } elseif (stripos($column->name, 'email') !== false) {
            $format = 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            $format = 'url';
        } else {
            $format = 'text';
        }

        return "        '".$column->name.($format === 'text' ? '' : ':'.$format)."'";
    }

    protected function callProviderQueue($func, $args, $generator)
    {
        // TODO: should be done on init, but providerList is empty
        $this->initializeProviders();

        $args = func_get_args();
        unset($args[0]);
        // walk through providers
        foreach ($this->_p as $obj) {
            if (method_exists($obj, $func)) {
                $c = call_user_func_array(array(&$obj, $func), $args);
                // until a provider returns not null
                if ($c !== null) {
                    if (is_object($args)) {
                        $argsString = get_class($args);
                    } elseif (is_array($args)) {
                        $argsString = Json::encode($args);
                    } else {
                        $argsString = $args;
                    }
                    $msg = 'Using provider '.get_class($obj).'::'.$func.' '.$argsString;
                    Yii::trace($msg, __METHOD__);

                    return $c;
                }
            }
        }
    }
}
