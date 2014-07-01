<?php

namespace schmunk42\giiant\helpers;

/**
 * @inheritdoc
 *
 * @author Tobias Munk <tobias@diemeisterei.de>
 */
class Inflector extends \yii\helpers\BaseInflector
{
    /**
     * Converts a PHP class name into an ID in lowercase.
     * Words in the ID may be concatenated using the specified character (defaults to '-').
     * For example, 'PostXTag' will be converted to 'post-x-tag'.
     * @param string $name the string to be converted
     * @param string $separator the character used to concatenate the words in the ID
     * @return string the resulting ID
     */
    public static function class2id($name, $separator = '-')
    {
        if ($separator === '_') {
            return trim(strtolower(preg_replace('/([A-Z])/', '_\0', $name)), '_');
        } else {
            return trim(strtolower(str_replace('_', $separator, preg_replace('/([A-Z])/', $separator . '\0', $name))), $separator);
        }
    }
}
