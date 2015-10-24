<?php
namespace schmunk42\giiant\helpers;

use yii\faker;

class GiiantFaker extends \Faker\Factory{

    private static $fakerFactory;

    const TYPE_STRING = "string";
    const TYPE_INTEGER = "integer";
    const TYPE_NUMBER = "number";
    const TYPE_BOOLEAN = "boolean";
    const TYPE_DATE = "date";
    const TYPE_TIME = "time";
    const TYPE_DATETIME = "datetime";
    const TYPE_TIMESTAMP = "timestamp";

    const FORMAT_DATE = "Y-m-d";
    const FORMAT_TIME = "H:i:s";
    const FORMAT_DATETIME = "Y-m-d H:i:s";
    const FORMAT_TIMESTAMP = "Y-m-d H:i:s";

    public static function create(){
        if(self::$fakerFactory == null){
            self::$fakerFactory = parent::create();
        }
        return self::$fakerFactory;
    }

    public static function value($type = self::TYPE_STRING, $format=NULL){
        self::create();
        switch($type):
            case self::TYPE_INTEGER:
                return self::$fakerFactory->randomNumber;
            case self::TYPE_NUMBER:
                return self::$fakerFactory->randomFloat;
            case self::TYPE_BOOLEAN:
                return self::$fakerFactory->boolean;
            case self::TYPE_DATE:
                $format = ($format === NULL)? self::FORMAT_DATE:$format;
                return self::$fakerFactory->date()->format($format);
            case self::TYPE_TIME:
                $format = ($format === NULL)? self::FORMAT_TIME:$format;
                return self::$fakerFactory->time()->format($format);
            case self::TYPE_DATETIME:
                $format = ($format === NULL)? self::FORMAT_DATETIME:$format;
                return self::$fakerFactory->dateTime()->format($format);
            case self::TYPE_TIMESTAMP:
                $format = ($format === NULL)? self::FORMAT_TIMESTAMP:$format;
                return self::$fakerFactory->dateTime()->format($format);
            default:
                return self::$fakerFactory->word;
        endswitch;
    }

    public static function string(){
        return self::value(self::TYPE_STRING);
    }

    public static function integer(){
        return self::value(self::TYPE_INTEGER);
    }

    public static function number(){
        return self::value(self::TYPE_NUMBER);
    }

    public static function boolean(){
        return self::value(self::TYPE_BOOLEAN);
    }

    public static function date($format = NULL){
        return self::value(self::TYPE_DATE, $format);
    }

    public static function time($format = NULL){
        return self::value(self::TYPE_TIME, $format);
    }

    public static function datetime($format = NULL){
        return self::value(self::TYPE_DATETIME, $format);
    }

    public static function timestamp($format = NULL){
        return self::value(self::TYPE_TIMESTAMP, $format);
    }
}