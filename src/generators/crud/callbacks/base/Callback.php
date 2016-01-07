<?php

namespace schmunk42\giiant\generators\crud\callbacks\base;

class Callback
{
    /**
     * @return \Closure no output, returns false to end to provider queue
     */
    public static function false()
    {
        return function () {
            return false;
        };
    }

    /**
     * @return \Closure standard attribute, without any formatting
     */
    public static function attribute()
    {
        return function ($attribute) {
            return "'$attribute'";
        };
    }
}
