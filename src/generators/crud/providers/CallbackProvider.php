<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 19.03.14
 * Time: 01:01
 */

namespace schmunk42\giiant\generators\crud\providers;

class CallbackProvider extends \schmunk42\giiant\base\Provider
{
    public $activeFields = [];
    public $prependActiveFields = [];
    public $appendActiveFields = [];
    public $attributeFormats = [];
    public $columnFormats = [];


    public function activeField($attribute, $model, $generator)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->activeFields);
        if ($key) {
            return $this->activeFields[$key]($attribute, $model, $generator);
        }
    }

    public function prependActiveField($attribute, $model, $generator)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->prependActiveFields);
        if ($key) {
            return $this->prependActiveFields[$key]($attribute, $model, $generator);
        }
    }

    public function appendActiveField($attribute, $model, $generator)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->appendActiveFields);
        if ($key) {
            return $this->appendActiveFields[$key]($attribute, $model, $generator);
        }
    }


    public function attributeFormat($attribute, $model, $generator)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->attributeFormats);
        if ($key) {
            return $this->attributeFormats[$key]($attribute, $model, $generator);
        }
    }

    public function columnFormat($attribute, $model, $generator)
    {
        $key = $this->findValue($this->getModelKey($attribute, $model), $this->columnFormats);
        if ($key) {
            return $this->columnFormats[$key]($attribute, $model, $generator);
        }
    }

    private function getModelKey($attribute, $model)
    {
        return $model::className() . '.' . $attribute;
    }

    private function findValue($subject, $array)
    {
        foreach ($array AS $key => $value) {
            if (preg_match('/' . $key . '/', $subject)) {
                return $key;
            }
        }
    }

}