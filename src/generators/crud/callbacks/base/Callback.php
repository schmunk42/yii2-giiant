<?php

namespace schmunk42\giiant\generators\crud\callbacks\base;

class Callback
{
    /**
     * @return \Closure no output, returns false to end to provider queue
     */
    static public function false()
    {
        return function () {
            return false;
        };
    }

    /**
     * @return \Closure standard attribute, without any formatting
     */
    static public function attribute()
    {
        return function ($attribute) {
            return "'$attribute'";
        };
    }
}
