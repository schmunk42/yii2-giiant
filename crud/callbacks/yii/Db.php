<?php
/**
 * Created by PhpStorm.
 * User: tobias
 * Date: 09.06.15
 * Time: 23:26
 */

namespace schmunk42\giiant\crud\callbacks\yii;


class Db
{
    static public function falseIfText()
    {
        // hide text columns (dbType: text)
        return function ($attribute) {
            if ($attribute->dbType == 'text') {
                return false;
            }
        };
    }
}