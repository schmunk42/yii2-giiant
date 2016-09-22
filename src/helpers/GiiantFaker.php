<?php

namespace schmunk42\giiant\helpers;

use yii\faker;

class GiiantFaker extends \Faker\Factory
{
    private static $fakerFactory;

    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_NUMBER = 'number';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_DATE = 'date';
    const TYPE_TIME = 'time';
    const TYPE_DATETIME = 'datetime';
    const TYPE_TIMESTAMP = 'timestamp';

    const FORMAT_DATE = 'Y-m-d';
    const FORMAT_TIME = 'H:i:s';
    const FORMAT_DATETIME = 'Y-m-d H:i:s';
    const FORMAT_TIMESTAMP = 'Y-m-d H:i:s';

    /**
     * Returns new instance of Faker generator class.
     *
     * @return \Faker\Generator
     */
    public static function create()
    {
        if (self::$fakerFactory == null) {
            self::$fakerFactory = parent::create();
        }

        return self::$fakerFactory;
    }

    /**
     * Returns Faker value.
     *
     * @param string $type       model's attribute type
     * @param string $methodName model's attribute type trying to match Faker's method name
     * @param null   $format     custom data format used in Faker
     *
     * @return mixed
     */
    public static function value($type = self::TYPE_STRING, $methodName = '', $format = null)
    {
        self::create();

        $preferredValue = self::provider($type, $methodName);
        if ($preferredValue !== null) {
            return $preferredValue;
        }
        switch ($type):
            case self::TYPE_INTEGER:
                return self::$fakerFactory->randomNumber;
        case self::TYPE_NUMBER:
                return self::$fakerFactory->randomFloat;
        case self::TYPE_BOOLEAN:
                return self::$fakerFactory->boolean;
        case self::TYPE_DATE:
                $format = ($format === null) ? self::FORMAT_DATE : $format;

        return self::$fakerFactory->date($format);
        case self::TYPE_TIME:
                $format = ($format === null) ? self::FORMAT_TIME : $format;

        return self::$fakerFactory->time($format);
        case self::TYPE_DATETIME:
                $format = ($format === null) ? self::FORMAT_DATETIME : $format;

        return self::$fakerFactory->dateTime()->format($format);
        case self::TYPE_TIMESTAMP:
                $format = ($format === null) ? self::FORMAT_TIMESTAMP : $format;

        return self::$fakerFactory->dateTime()->format($format);
        default:
                return self::$fakerFactory->word;
        endswitch;
    }

    /**
     * Tries to execute Faker's provider methods like email, address, title etc, if method is found.
     *
     * @param string $type       model's attribute type
     * @param string $methodName model's attribute type trying to match Faker's method name
     * @param null   $format     custom data format used in Faker
     */
    public static function provider($type = self::TYPE_STRING, $methodName = '', $format = null)
    {
        $fakerValue = null;
        try {
            $fakerValue = self::$fakerFactory->$methodName;
            switch ($type):
                case self::TYPE_INTEGER:
                    $fakerValue = (int) $fakerValue;
            break;
            case self::TYPE_NUMBER:
                    $fakerValue = (float) $fakerValue;
            break;
            default:
                    $fakerValue = (string) $fakerValue;
            break;
            endswitch;
        } catch (\Exception $e) {
            $fakerValue = null;
        }

        return $fakerValue;
    }

    /**
     * @param string $methodName model's attribute type trying to match Faker's method name
     *
     * @return mixed
     */
    public static function string($methodName = '')
    {
        $val = self::provider(self::TYPE_STRING, $methodName);

        return ($val === null) ? self::value(self::TYPE_STRING) : $val;
    }

    /**
     * @param string $methodName model's attribute type trying to match Faker's method name
     *
     * @return mixed
     */
    public static function integer($methodName = '')
    {
        $val = self::provider(self::TYPE_INTEGER, $methodName);

        return ($val === null) ? self::value(self::TYPE_INTEGER) : $val;
    }

    /**
     * @param string $methodName model's attribute type trying to match Faker's method name
     *
     * @return mixed
     */
    public static function number($methodName = '')
    {
        $val = self::provider(self::TYPE_NUMBER, $methodName);

        return ($val === null) ? self::value(self::TYPE_NUMBER) : $val;
    }

    /**
     * @return mixed
     */
    public static function boolean()
    {
        return self::value(self::TYPE_BOOLEAN);
    }

    /**
     * @param null $format - Custom format
     *
     * @return mixed
     */
    public static function date($format = null)
    {
        return self::value(self::TYPE_DATE, $format);
    }

    /**
     * @param null $format - Custom format
     *
     * @return mixed
     */
    public static function time($format = null)
    {
        return self::value(self::TYPE_TIME, $format);
    }

    /**
     * @param null $format - Custom format
     *
     * @return mixed
     */
    public static function datetime($format = null)
    {
        return self::value(self::TYPE_DATETIME, $format);
    }

    /**
     * @param null $format - Custom format
     *
     * @return mixed
     */
    public static function timestamp($format = null)
    {
        return self::value(self::TYPE_TIMESTAMP, $format);
    }
}
