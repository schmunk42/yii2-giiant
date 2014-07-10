<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:01
 */

namespace schmunk42\giiant\crud\providers;

class CallbackProvider extends \schmunk42\giiant\base\Provider
{
    public $activeFields;
    public $attributeFormats;
    public $columnFormats;

    public function generateActiveField($attribute)
    {
        if (isset($this->activeFields[$this->getModelKey($attribute)])) {
            return $this->activeFields[$this->getModelKey($attribute)]($attribute, $this->generator);
        }
    }

    private function getModelKey($attribute)
    {
        return $this->generator->modelClass . '.' . $attribute;
    }

    public function attributeFormat($attribute)
    {
        if (isset($this->columnFormats[$this->getModelKey($attribute)])) {
            return $this->columnFormats[$this->getModelKey($attribute)]($attribute, $this->generator);
        }
    }

    public function generateColumnFormat($column)
    {
        if (isset($this->columnFormats[$this->getModelKey($column->name)])) {
            return $this->columnFormats[$this->getModelKey($column->name)]($column->name, $this->generator);
        }
    }

}